<?php

namespace App\Services;

use App\Mail\ApprovalNotification;
use App\Mail\PaymentProcessed;
use App\Mail\SubmissionSubmitted;
use App\Models\Role;
use App\Models\Submission;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    public function submissionSubmitted(Submission $submission): void
    {
        $spvRole = Role::where('slug', 'spv')->first();
        if (!$spvRole) return;

        $spvUsers = $spvRole->users;

        foreach ($spvUsers as $spv) {
            try {
                Mail::to($spv->email)->send(
                    new SubmissionSubmitted($submission, $spv->name)
                );
            } catch (\Exception $e) {
                Log::warning("Gagal kirim email ke {$spv->email}: {$e->getMessage()}");
            }
        }
    }

    public function approvalProcessed(Submission $submission, string $roleSlug, string $decision): void
    {
        $staff = $submission->user;
        $roleName = Role::where('slug', $roleSlug)->first()?->name ?? $roleSlug;

        try {
            Mail::to($staff->email)->send(
                new ApprovalNotification($submission, $staff->name, $decision)
            );
        } catch (\Exception $e) {
            Log::warning("Gagal kirim email ke {$staff->email}: {$e->getMessage()}");
        }
    }

    public function paymentProcessed(Submission $submission, string $status): void
    {
        $staff = $submission->user;

        try {
            Mail::to($staff->email)->send(
                new PaymentProcessed($submission, $status)
            );
        } catch (\Exception $e) {
            Log::warning("Gagal kirim email ke {$staff->email}: {$e->getMessage()}");
        }
    }
}
