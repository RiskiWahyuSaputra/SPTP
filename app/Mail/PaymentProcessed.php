<?php

namespace App\Mail;

use App\Models\Submission;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PaymentProcessed extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Submission $submission,
        public string $status,
    ) {}

    public function envelope(): Envelope
    {
        $subject = $this->status === 'paid'
            ? "[SPTP] Pembayaran Selesai: {$this->submission->submission_number}"
            : "[SPTP] Pembayaran Ditolak: {$this->submission->submission_number}";

        return new Envelope(
            subject: $subject,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.payment-processed',
        );
    }
}
