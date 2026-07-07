<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Submission;
use App\Services\ApprovalRoutingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SubmissionController extends Controller
{
    protected ApprovalRoutingService $approvalRouting;

    public function __construct(ApprovalRoutingService $approvalRouting)
    {
        $this->approvalRouting = $approvalRouting;
    }

    public function index(Request $request): JsonResponse
    {
        $user = Auth::user();
        $query = Submission::with('category', 'user', 'approvals.role');

        if ($user->role->slug === 'staff') {
            $query->where('user_id', $user->id);
        }

        if ($request->filled('status')) {
            $query->where('current_status', $request->status);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $submissions = $query->orderBy('created_at', 'desc')->paginate($request->per_page ?? 15);

        return response()->json($submissions);
    }

    public function show(Submission $submission): JsonResponse
    {
        $user = Auth::user();
        if ($user->role->slug === 'staff' && $submission->user_id !== $user->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $submission->load('category', 'user', 'attachments', 'approvals.approver', 'approvals.role', 'payment');

        return response()->json($submission);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'submission_date' => 'required|date',
            'amount' => 'required|numeric|min:1',
            'description' => 'required|string|min:10',
        ]);

        $submission = DB::transaction(function () use ($validated, $request) {
            $submission = Submission::create([
                'submission_number' => $this->generateNumber(),
                'user_id' => Auth::id(),
                'category_id' => $validated['category_id'],
                'submission_date' => $validated['submission_date'],
                'amount' => $validated['amount'],
                'description' => $validated['description'],
                'current_status' => 'draft',
            ]);

            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $path = $file->store('attachments/' . $submission->id, 'public');
                    $submission->attachments()->create([
                        'file_name' => $file->getClientOriginalName(),
                        'file_path' => $path,
                        'file_type' => $file->getClientOriginalExtension(),
                        'file_size' => round($file->getSize() / 1024),
                    ]);
                }
            }

            return $submission;
        });

        return response()->json($submission->load('category', 'attachments'), 201);
    }

    public function submit(Submission $submission): JsonResponse
    {
        if ($submission->user_id !== Auth::id() || $submission->current_status !== 'draft') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        DB::transaction(function () use ($submission) {
            $submission->update(['current_status' => 'submitted']);
            $this->approvalRouting->initiateRouting($submission->fresh());
        });

        return response()->json([
            'message' => 'Pengajuan berhasil dikirim.',
            'submission' => $submission->fresh()->load('category', 'approvals.role'),
        ]);
    }

    private function generateNumber(): string
    {
        $prefix = 'PGJ';
        $dateStr = now()->format('Ym');
        $last = Submission::where('submission_number', 'like', "{$prefix}/{$dateStr}/%")
            ->orderBy('id', 'desc')->first();
        $num = $last ? (int) explode('/', $last->submission_number)[2] + 1 : 1;
        return sprintf('%s/%s/%04d', $prefix, $dateStr, $num);
    }
}
