<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSubmissionRequest;
use App\Models\Category;
use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SubmissionController extends Controller
{
    public function index()
    {
        $submissions = Submission::with('category', 'attachments')
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('staff.submissions.index', compact('submissions'));
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

        return redirect()
            ->route('staff.submissions.show', $submission)
            ->with('success', 'Pengajuan berhasil diperbarui.');
    }

    public function destroy(Submission $submission)
    {
        if ($submission->user_id !== Auth::id() || $submission->current_status !== 'draft') {
            abort(403);
        }

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

        $submission->update(['current_status' => 'submitted']);

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
