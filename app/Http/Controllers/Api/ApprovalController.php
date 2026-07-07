<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Approval;
use App\Models\Role;
use App\Models\Submission;
use App\Services\ApprovalRoutingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ApprovalController extends Controller
{
    protected ApprovalRoutingService $approvalRouting;

    public function __construct(ApprovalRoutingService $approvalRouting)
    {
        $this->approvalRouting = $approvalRouting;
    }

    public function index(Request $request): JsonResponse
    {
        $user = Auth::user();
        $role = Role::where('slug', $user->role->slug)->firstOrFail();

        $query = Submission::with('category', 'user', 'approvals.role')
            ->whereHas('approvals', function ($q) use ($role) {
                $q->where('role_id', $role->id);
            });

        if ($request->filled('status')) {
            if ($request->status === 'pending') {
                $query->whereHas('approvals', function ($q) use ($role) {
                    $q->where('role_id', $role->id)->where('decision', 'pending');
                });
            } elseif ($request->status === 'approved') {
                $query->whereHas('approvals', function ($q) use ($role) {
                    $q->where('role_id', $role->id)->where('decision', 'approved')
                      ->where('approver_id', $user->id);
                });
            } elseif ($request->status === 'rejected') {
                $query->whereHas('approvals', function ($q) use ($role) {
                    $q->where('role_id', $role->id)->where('decision', 'rejected')
                      ->where('approver_id', $user->id);
                });
            }
        } else {
            $query->whereHas('approvals', function ($q) use ($role) {
                $q->where('role_id', $role->id)->where('decision', 'pending');
            });
        }

        $submissions = $query->orderBy('created_at', 'desc')->paginate($request->per_page ?? 15);

        return response()->json($submissions);
    }

    public function process(Request $request, Submission $submission): JsonResponse
    {
        $user = Auth::user();
        $roleSlug = $user->role->slug;
        $role = Role::where('slug', $roleSlug)->firstOrFail();

        $request->validate([
            'decision' => 'required|in:approved,rejected',
            'notes' => 'nullable|string|max:500',
        ]);

        $hasPending = $submission->approvals()
            ->where('role_id', $role->id)
            ->where('decision', 'pending')
            ->exists();

        if (!$hasPending) {
            return response()->json(['message' => 'Tidak ada approval pending untuk role ini.'], 403);
        }

        DB::transaction(function () use ($request, $submission, $user, $role, $roleSlug) {
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

        return response()->json([
            'message' => $request->decision === 'approved'
                ? 'Pengajuan berhasil disetujui.'
                : 'Pengajuan ditolak.',
            'submission' => $submission->fresh()->load('category', 'user', 'approvals.role'),
        ]);
    }
}
