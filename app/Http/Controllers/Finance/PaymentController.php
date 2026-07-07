<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Http\Requests\PaymentRequest;
use App\Models\CashBalance;
use App\Models\Payment;
use App\Models\Submission;
use App\Services\ActivityLogger;
use App\Services\ApprovalRoutingService;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
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

    public function index()
    {
        $submissions = Submission::with('category', 'user')
            ->where('current_status', 'waiting_finance')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $cashBalance = CashBalance::first();

        return view('finance.index', compact('submissions', 'cashBalance'));
    }

    public function show(Submission $submission)
    {
        if ($submission->current_status !== 'waiting_finance') {
            abort(403, 'Pengajuan tidak dalam status menunggu pembayaran.');
        }

        $submission->load('category', 'user', 'attachments', 'approvals.approver', 'approvals.role');
        $cashBalance = CashBalance::first();

        return view('finance.show', compact('submission', 'cashBalance'));
    }

    public function process(PaymentRequest $request, Submission $submission)
    {
        if ($submission->current_status !== 'waiting_finance') {
            abort(403);
        }

        DB::transaction(function () use ($request, $submission) {
            $cashBalance = CashBalance::lockForUpdate()->firstOrFail();

            if ($request->decision === 'paid') {
                if ($cashBalance->balance < $submission->amount) {
                    $status = 'insufficient_balance';
                    $submission->update([
                        'current_status' => 'rejected',
                        'rejection_reason' => 'Saldo kas tidak mencukupi.',
                    ]);
                    $this->approvalRouting->refundBudget($submission);
                } else {
                    $balanceBefore = $cashBalance->balance;
                    $cashBalance->decrement('balance', $submission->amount);

                    $status = 'paid';
                    $submission->update(['current_status' => 'paid']);
                }
            } else {
                $status = 'rejected_by_finance';
                $submission->update([
                    'current_status' => 'rejected',
                    'rejection_reason' => $request->notes ?? 'Ditolak oleh Finance.',
                ]);
                $this->approvalRouting->refundBudget($submission);
            }

            Payment::create([
                'submission_id' => $submission->id,
                'processed_by' => Auth::id(),
                'amount' => $submission->amount,
                'balance_before' => $cashBalance->balance + ($status === 'paid' ? $submission->amount : 0),
                'balance_after' => $cashBalance->balance,
                'status' => $status,
                'paid_at' => $status === 'paid' ? now() : null,
            ]);
        });

        if ($request->decision === 'paid') {
            $this->activityLogger->paymentPaid($submission);
            $this->notificationService->paymentProcessed($submission, 'paid');
        } else {
            $this->activityLogger->paymentRejected($submission, $request->notes ?? 'Ditolak oleh Finance');
            $this->notificationService->paymentProcessed($submission, 'rejected');
        }

        $message = $request->decision === 'paid'
            ? 'Pembayaran berhasil diproses.'
            : 'Pengajuan ditolak.';

        return redirect()->route('finance.index')->with('success', $message);
    }
}
