<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;

class ActivityLogger
{
    public function log(
        string $type,
        string $description,
        ?Model $loggable = null,
        ?array $oldData = null,
        ?array $newData = null,
        ?int $userId = null,
    ): ActivityLog {
        return ActivityLog::create([
            'user_id' => $userId ?? auth()->id(),
            'loggable_id' => $loggable?->getKey(),
            'loggable_type' => $loggable ? get_class($loggable) : null,
            'type' => $type,
            'description' => $description,
            'old_data' => $oldData,
            'new_data' => $newData,
        ]);
    }

    public function submissionCreated(Model $submission): void
    {
        $this->log(
            'submission_created',
            "Membuat pengajuan baru: {$submission->submission_number}",
            $submission,
            null,
            $submission->toArray(),
        );
    }

    public function submissionUpdated(Model $submission, array $old, array $new): void
    {
        $this->log(
            'submission_updated',
            "Memperbarui pengajuan: {$submission->submission_number}",
            $submission,
            $old,
            $new,
        );
    }

    public function submissionSubmitted(Model $submission): void
    {
        $this->log(
            'submission_submitted',
            "Mengirim pengajuan: {$submission->submission_number}",
            $submission,
            null,
            ['current_status' => 'submitted'],
        );
    }

    public function submissionDeleted(Model $submission): void
    {
        $this->log(
            'submission_deleted',
            "Menghapus pengajuan: {$submission->submission_number}",
            $submission,
            $submission->toArray(),
            null,
        );
    }

    public function approved(Model $submission, string $roleName, ?string $notes = null): void
    {
        $desc = "Menyetujui pengajuan {$submission->submission_number} sebagai {$roleName}";
        if ($notes) {
            $desc .= " (catatan: {$notes})";
        }
        $this->log('approved', $desc, $submission);
    }

    public function rejected(Model $submission, string $roleName, string $reason): void
    {
        $this->log(
            'rejected',
            "Menolak pengajuan {$submission->submission_number} sebagai {$roleName}: {$reason}",
            $submission,
        );
    }

    public function paymentPaid(Model $submission): void
    {
        $this->log(
            'payment_paid',
            "Memproses pembayaran untuk pengajuan: {$submission->submission_number}",
            $submission,
            null,
            ['current_status' => 'paid'],
        );
    }

    public function paymentRejected(Model $submission, string $reason): void
    {
        $this->log(
            'payment_rejected',
            "Menolak pembayaran untuk pengajuan {$submission->submission_number}: {$reason}",
            $submission,
        );
    }

    public function budgetRejected(Model $submission): void
    {
        $this->log(
            'budget_rejected',
            "Pengajuan {$submission->submission_number} ditolak karena budget tidak mencukupi",
            $submission,
        );
    }
}
