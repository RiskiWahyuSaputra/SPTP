<?php

use Illuminate\Support\Facades\Route;

Route::post('/login', [App\Http\Controllers\Api\AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [App\Http\Controllers\Api\AuthController::class, 'logout']);
    Route::get('/user', [App\Http\Controllers\Api\AuthController::class, 'user']);

    Route::get('submissions', [App\Http\Controllers\Api\SubmissionController::class, 'index']);
    Route::get('submissions/{submission}', [App\Http\Controllers\Api\SubmissionController::class, 'show']);
    Route::post('submissions', [App\Http\Controllers\Api\SubmissionController::class, 'store']);
    Route::post('submissions/{submission}/submit', [App\Http\Controllers\Api\SubmissionController::class, 'submit']);

    Route::get('approvals', [App\Http\Controllers\Api\ApprovalController::class, 'index']);
    Route::post('approvals/{submission}/process', [App\Http\Controllers\Api\ApprovalController::class, 'process']);
});
