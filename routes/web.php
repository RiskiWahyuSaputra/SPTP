<?php

use App\Http\Controllers\ProfileController;
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

// Staff routes
Route::middleware(['auth', 'verified', 'role:staff'])->prefix('staff')->name('staff.')->group(function () {
    // Will be filled in Phase 8
});

// Approver routes (SPV, Manager, Direktur)
Route::middleware(['auth', 'verified', 'role:spv,manager,direktur'])->prefix('approval')->name('approval.')->group(function () {
    // Will be filled in Phase 10
});

// Finance routes
Route::middleware(['auth', 'verified', 'role:finance'])->prefix('finance')->name('finance.')->group(function () {
    // Will be filled in Phase 11
});

require __DIR__.'/auth.php';
