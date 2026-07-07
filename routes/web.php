<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Staff\SubmissionController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'verified', 'role:staff'])->prefix('staff')->name('staff.')->group(function () {
    Route::resource('submissions', SubmissionController::class)->except(['show']);
    Route::get('submissions/{submission}', [SubmissionController::class, 'show'])->name('submissions.show');
    Route::post('submissions/{submission}/submit', [SubmissionController::class, 'submit'])->name('submissions.submit');
});

Route::middleware(['auth', 'verified', 'role:spv,manager,direktur'])->prefix('approval')->name('approval.')->group(function () {
    Route::get('/', [App\Http\Controllers\Approval\ApprovalController::class, 'index'])->name('index');
    Route::get('{submission}', [App\Http\Controllers\Approval\ApprovalController::class, 'show'])->name('show');
    Route::post('{submission}/process', [App\Http\Controllers\Approval\ApprovalController::class, 'process'])->name('process');
});

Route::middleware(['auth', 'verified', 'role:finance'])->prefix('finance')->name('finance.')->group(function () {
    Route::get('/', [App\Http\Controllers\Finance\PaymentController::class, 'index'])->name('index');
    Route::get('{submission}', [App\Http\Controllers\Finance\PaymentController::class, 'show'])->name('show');
    Route::post('{submission}/process', [App\Http\Controllers\Finance\PaymentController::class, 'process'])->name('process');
});

require __DIR__.'/auth.php';
