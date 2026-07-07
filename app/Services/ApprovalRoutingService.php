<?php

namespace App\Services;

use App\Models\Approval;
use App\Models\Budget;
use App\Models\Role;
use App\Models\Submission;
use Illuminate\Support\Facades\DB;

class ApprovalRoutingService
{
    public function initiateRouting(Submission $submission): void
    {
        $category = $submission->category;
        $spvRole = Role::where('slug', 'spv')->firstOrFail();

        Approval::create([
            'submission_id' => $submission->id,
            'role_id' => $spvRole->id,
            'sequence' => 1,
            'decision' => 'pending',
        ]);

        if ($category->is_po_produk) {
            $this->routeToDirector($submission);
        } elseif ($submission->amount > 5000000) {
            $managerRole = Role::where('slug', 'manager')->firstOrFail();

            Approval::create([
                'submission_id' => $submission->id,
                'role_id' => $managerRole->id,
                'sequence' => 2,
                'decision' => 'pending',
            ]);

            if ($submission->amount > 10000000) {
                $this->routeToDirector($submission);
            } else {
                $submission->update(['current_status' => 'waiting_spv']);
            }
        } else {
            if (!$this->checkBudget($submission)) {
                $this->reject($submission, 'Budget kategori tidak mencukupi.');
                return;
            }
            $submission->update(['current_status' => 'waiting_spv']);
        }
    }

    public function processApproval(Submission $submission, string $roleSlug, string $decision, ?string $notes = null): void
    {
        DB::transaction(function () use ($submission, $roleSlug, $decision, $notes) {
            $role = Role::where('slug', $roleSlug)->firstOrFail();

            $approval = Approval::where('submission_id', $submission->id)
                ->where('role_id', $role->id)
                ->where('decision', 'pending')
                ->firstOrFail();

            if ($decision === 'rejected') {
                $this->reject($submission, $notes ?? 'Ditolak oleh ' . $role->name, $approval);
                return;
            }

            $approval->update([
                'decision' => 'approved',
                'notes' => $notes,
                'decided_at' => now(),
            ]);

            if ($roleSlug === 'spv' && $submission->category->is_po_produk) {
                $submission->update(['current_status' => 'waiting_director']);
                return;
            }

            if ($roleSlug === 'spv') {
                if ($submission->amount > 5000000) {
                    $nextApproval = Approval::where('submission_id', $submission->id)
                        ->where('role_id', Role::where('slug', 'manager')->first()->id)
                        ->first();

                    if ($nextApproval && $submission->amount > 10000000) {
                        $submission->update(['current_status' => 'waiting_director']);
                    } else {
                        $submission->update(['current_status' => 'waiting_manager']);
                    }
                } else {
                    $this->deductBudget($submission);
                    $submission->update(['current_status' => 'waiting_finance']);
                }
            } elseif ($roleSlug === 'manager') {
                if ($submission->amount > 10000000) {
                    $submission->update(['current_status' => 'waiting_director']);
                } else {
                    $this->deductBudget($submission);
                    $submission->update(['current_status' => 'waiting_finance']);
                }
            } elseif ($roleSlug === 'direktur') {
                $this->deductBudget($submission);
                $submission->update(['current_status' => 'waiting_finance']);
            }
        });
    }

    public function getRoleForApprovalSequence(Submission $submission, int $sequence): ?Role
    {
        $approval = Approval::where('submission_id', $submission->id)
            ->where('sequence', $sequence)
            ->first();

        return $approval?->role;
    }

    public function checkBudget(Submission $submission): bool
    {
        $period = $submission->submission_date->format('Y-m');
        $budget = Budget::where('category_id', $submission->category_id)
            ->where('period', $period)
            ->first();

        if (!$budget) {
            return false;
        }

        return $budget->remaining() >= $submission->amount;
    }

    public function deductBudget(Submission $submission): void
    {
        $period = $submission->submission_date->format('Y-m');
        $budget = Budget::where('category_id', $submission->category_id)
            ->where('period', $period)
            ->lockForUpdate()
            ->first();

        if ($budget) {
            $budget->increment('used_amount', $submission->amount);
        }
    }

    public function refundBudget(Submission $submission): void
    {
        $period = $submission->submission_date->format('Y-m');
        $budget = Budget::where('category_id', $submission->category_id)
            ->where('period', $period)
            ->lockForUpdate()
            ->first();

        if ($budget && $budget->used_amount >= $submission->amount) {
            $budget->decrement('used_amount', $submission->amount);
        }
    }

    private function routeToDirector(Submission $submission): void
    {
        $dirRole = Role::where('slug', 'direktur')->firstOrFail();

        $existingDir = Approval::where('submission_id', $submission->id)
            ->where('role_id', $dirRole->id)
            ->first();

        if (!$existingDir) {
            Approval::create([
                'submission_id' => $submission->id,
                'role_id' => $dirRole->id,
                'sequence' => 3,
                'decision' => 'pending',
            ]);
        }

        $submission->update(['current_status' => 'waiting_director']);
    }

    private function reject(Submission $submission, string $reason, ?Approval $approval = null): void
    {
        if ($approval) {
            $approval->update([
                'decision' => 'rejected',
                'notes' => $reason,
                'decided_at' => now(),
            ]);
        }

        if (in_array($submission->current_status, ['waiting_finance', 'paid'])) {
            $this->refundBudget($submission);
        }

        $submission->update([
            'current_status' => 'rejected',
            'rejection_reason' => $reason,
        ]);
    }
}
