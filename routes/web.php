<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Staff\SubmissionController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : redirect()->route('login');
});

Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('attachments/{attachment}/serve', function (App\Models\SubmissionAttachment $attachment) {
        $disk = Storage::disk('public');
        if (!$disk->exists($attachment->file_path)) {
            abort(404);
        }
        return $disk->response($attachment->file_path, $attachment->file_name);
    })->name('attachment.serve');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/activity-logs', [App\Http\Controllers\ActivityLogController::class, 'index'])->name('activity-logs.index');
});

Route::middleware(['auth', 'verified', 'role:staff'])->prefix('staff')->name('staff.')->group(function () {
    Route::resource('submissions', SubmissionController::class)->except(['show']);
    Route::get('submissions/{submission}', [SubmissionController::class, 'show'])->name('submissions.show');
    Route::post('submissions/{submission}/submit', [SubmissionController::class, 'submit'])->name('submissions.submit');
});

Route::prefix('approval')->name('approval.')->middleware(['auth', 'verified', 'role:spv,manager,direktur'])->group(function () {
    Route::get('/', [App\Http\Controllers\Approval\ApprovalController::class, 'index'])->name('index');
    Route::get('detail/{submission}', [App\Http\Controllers\Approval\ApprovalController::class, 'show'])->name('show');
    Route::post('process/{submission}', function (Illuminate\Http\Request $request, App\Models\Submission $submission) {
        $roleSlug = Auth::user()->role->slug;
        app(App\Services\ApprovalRoutingService::class)->processApproval(
            $submission,
            $roleSlug,
            $request->input('decision', 'rejected'),
            $request->input('notes')
        );
        $success = $request->input('decision') === 'approved'
            ? 'Pengajuan berhasil disetujui.'
            : 'Pengajuan ditolak.';
        return redirect('/approval')->with('success', $success);
    })->name('process');
});

Route::middleware(['auth', 'verified', 'role:finance'])->prefix('finance')->name('finance.')->group(function () {
    Route::get('/', [App\Http\Controllers\Finance\PaymentController::class, 'index'])->name('index');
    Route::get('{submission}', [App\Http\Controllers\Finance\PaymentController::class, 'show'])->name('show');
    Route::post('{submission}/process', [App\Http\Controllers\Finance\PaymentController::class, 'process'])->name('process');
});

require __DIR__.'/auth.php';
