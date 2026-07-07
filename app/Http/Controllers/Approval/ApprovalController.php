<?php

namespace App\Http\Controllers\Approval;

use App\Http\Controllers\Controller;
use App\Http\Requests\ApprovalRequest;
use App\Models\Approval;
use App\Models\Category;
use App\Models\Role;
use App\Models\Submission;
use App\Services\ActivityLogger;
use App\Services\ApprovalRoutingService;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ApprovalController extends Controller
{
    protected ApprovalRoutingService $approvalRouting;
    protected ActivityLogger $activityLogger;
    protected NotificationService $notificationService;

    public function __construct(ApprovalRoutingService $approvalRouting, ActivityLogger $activityLogger, NotificationService $notificationService)
    {
        $this->approvalRouting = $approvalRouting;
        $this->activityLogger = $activityLogger;
        $this->notificationService = $notificationService;
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $roleSlug = $user->role->slug;
        $role = Role::where('slug', $roleSlug)->firstOrFail();

        $status = $request->get('status', 'pending');

        $query = Submission::with('category', 'user', 'approvals.role');

        if ($status === 'all') {
            $query->whereHas('approvals', function ($q) use ($role) {
                $q->where('role_id', $role->id);
            });
        } elseif ($status === 'approved') {
            $query->whereHas('approvals', function ($q) use ($role, $user) {
                $q->where('role_id', $role->id)
                  ->where('decision', 'approved')
                  ->where('approver_id', $user->id);
            });
        } elseif ($status === 'rejected') {
            $query->whereHas('approvals', function ($q) use ($role, $user) {
                $q->where('role_id', $role->id)
                  ->where('decision', 'rejected')
                  ->where('approver_id', $user->id);
            });
        } else {
            $query->whereHas('approvals', function ($q) use ($role) {
                $q->where('role_id', $role->id)
                  ->where('decision', 'pending');
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('submission_number', 'like', "%{$s}%")
                  ->orWhere('description', 'like', "%{$s}%");
            });
        }

        $submissions = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();
        $categories = Category::where('is_active', true)->get();

        return view('approval.index', compact('submissions', 'roleSlug', 'categories', 'status'));
    }

    public function show(Submission $submission)
    {
        $user = Auth::user();
        $roleSlug = $user->role->slug;

        $this->authorizeApproval($submission, $roleSlug);

        $submission->load('category', 'user', 'attachments', 'approvals.role', 'approvals.approver');

        return view('approval.show', compact('submission', 'roleSlug'));
    }

    public function process(ApprovalRequest $request, Submission $submission)
    {
        $user = Auth::user();
        $roleSlug = $user->role->slug;

        $this->authorizeApproval($submission, $roleSlug);

        DB::transaction(function () use ($request, $submission, $user, $roleSlug) {
            $role = Role::where('slug', $roleSlug)->firstOrFail();

            Approval::where('submission_id', $submission->id)
                ->where('role_id', $role->id)
                ->where('decision', 'pending')
                ->update([
                    'approver_id' => $user->id,
                    'decision' => $request->decision,
                    'notes' => $request->notes,
                    'decided_at' => now(),
                ]);

            $this->approvalRouting->processApproval(
                $submission->fresh(),
                $roleSlug,
                $request->decision,
                $request->notes
            );
        });

        if ($request->decision === 'approved') {
            $this->activityLogger->approved($submission, $role->name, $request->notes);
        } else {
            $this->activityLogger->rejected($submission, $role->name, $request->notes ?? 'Ditolak');
        }

        $this->notificationService->approvalProcessed($submission, $roleSlug, $request->decision);

        $message = $request->decision === 'approved'
            ? 'Pengajuan berhasil disetujui.'
            : 'Pengajuan ditolak.';

        return redirect('/approval')->with('success', $message);
    }

    private function authorizeApproval(Submission $submission, string $roleSlug): void
    {
        $role = Role::where('slug', $roleSlug)->firstOrFail();

        $hasPending = $submission->approvals()
            ->where('role_id', $role->id)
            ->where('decision', 'pending')
            ->exists();

        if (!$hasPending) {
            abort(403, 'Anda tidak memiliki akses untuk memproses pengajuan ini.');
        }
    }

    private function getPendingRoleIds(string $roleSlug): array
    {
        $role = Role::where('slug', $roleSlug)->firstOrFail();
        return [$role->id];
    }
}
