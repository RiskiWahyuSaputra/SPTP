<?php

namespace App\Http\Controllers\Approval;

use App\Http\Controllers\Controller;
use App\Http\Requests\ApprovalRequest;
use App\Models\Approval;
use App\Models\Role;
use App\Models\Submission;
use App\Services\ApprovalRoutingService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ApprovalController extends Controller
{
    protected ApprovalRoutingService $approvalRouting;

    public function __construct(ApprovalRoutingService $approvalRouting)
    {
        $this->approvalRouting = $approvalRouting;
    }

    public function index()
    {
        $user = Auth::user();
        $roleSlug = $user->role->slug;

        $pendingRoleIds = $this->getPendingRoleIds($roleSlug);

        $submissions = Submission::with('category', 'user', 'approvals.role')
            ->whereHas('approvals', function ($q) use ($pendingRoleIds) {
                $q->whereIn('role_id', $pendingRoleIds)
                  ->where('decision', 'pending');
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('approval.index', compact('submissions', 'roleSlug'));
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
