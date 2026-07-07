<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSubmissionRequest;
use App\Models\Category;
use App\Models\Submission;
use App\Services\ActivityLogger;
use App\Services\ApprovalRoutingService;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SubmissionController extends Controller
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
        $query = Submission::with('category', 'attachments')
            ->where('user_id', Auth::id());

        if ($request->filled('status')) {
            $query->where('current_status', $request->status);
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

        return view('staff.submissions.index', compact('submissions', 'categories'));
    }

    public function create()
    {
        $categories = Category::where('is_active', true)->get();
        return view('staff.submissions.create', compact('categories'));
    }

    public function store(StoreSubmissionRequest $request)
    {
        $validated = $request->validated();

        $submission = DB::transaction(function () use ($validated, $request) {
            $submission = Submission::create([
                'submission_number' => $this->generateSubmissionNumber(),
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

        $this->activityLogger->submissionCreated($submission);

        return redirect()
            ->route('staff.submissions.show', $submission)
            ->with('success', 'Pengajuan berhasil dibuat.');
    }

    public function show(Submission $submission)
    {
        if ($submission->user_id !== Auth::id()) {
            abort(403);
        }

        $submission->load('category', 'attachments', 'approvals.approver', 'approvals.role');

        return view('staff.submissions.show', compact('submission'));
    }

    public function edit(Submission $submission)
    {
        if ($submission->user_id !== Auth::id() || $submission->current_status !== 'draft') {
            abort(403);
        }

        $categories = Category::where('is_active', true)->get();
        return view('staff.submissions.edit', compact('submission', 'categories'));
    }

    public function update(StoreSubmissionRequest $request, Submission $submission)
    {
        if ($submission->user_id !== Auth::id() || $submission->current_status !== 'draft') {
            abort(403);
        }

        $validated = $request->validated();

        $oldData = $submission->toArray();

        DB::transaction(function () use ($validated, $request, $submission) {
            $submission->update([
                'category_id' => $validated['category_id'],
                'submission_date' => $validated['submission_date'],
                'amount' => $validated['amount'],
                'description' => $validated['description'],
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
        });

        $this->activityLogger->submissionUpdated($submission, $oldData, $submission->fresh()->toArray());

        return redirect()
            ->route('staff.submissions.show', $submission)
            ->with('success', 'Pengajuan berhasil diperbarui.');
    }

    public function destroy(Submission $submission)
    {
        if ($submission->user_id !== Auth::id() || $submission->current_status !== 'draft') {
            abort(403);
        }

        $this->activityLogger->submissionDeleted($submission);

        DB::transaction(function () use ($submission) {
            foreach ($submission->attachments as $attachment) {
                Storage::disk('public')->delete($attachment->file_path);
            }
            $submission->attachments()->delete();
            $submission->delete();
        });

        return redirect()
            ->route('staff.submissions.index')
            ->with('success', 'Pengajuan berhasil dihapus.');
    }

    public function submit(Submission $submission)
    {
        if ($submission->user_id !== Auth::id() || $submission->current_status !== 'draft') {
            abort(403);
        }

        DB::transaction(function () use ($submission) {
            $submission->update(['current_status' => 'submitted']);
            $this->approvalRouting->initiateRouting($submission->fresh());
        });

        $this->activityLogger->submissionSubmitted($submission);
        $this->notificationService->submissionSubmitted($submission);

        return redirect()
            ->route('staff.submissions.show', $submission)
            ->with('success', 'Pengajuan berhasil dikirim.');
    }

    private function generateSubmissionNumber(): string
    {
        $prefix = 'PGJ';
        $period = now()->format('Y-m');
        $dateStr = now()->format('Ym');

        $lastSubmission = Submission::where('submission_number', 'like', "{$prefix}/{$dateStr}/%")
            ->orderBy('id', 'desc')
            ->first();

        if ($lastSubmission) {
            $lastNumber = (int) explode('/', $lastSubmission->submission_number)[2];
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return sprintf('%s/%s/%04d', $prefix, $dateStr, $newNumber);
    }
}
