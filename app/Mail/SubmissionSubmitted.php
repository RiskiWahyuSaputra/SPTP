<?php

namespace App\Mail;

use App\Models\Submission;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SubmissionSubmitted extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Submission $submission,
        public string $approverName,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "[SPTP] Pengajuan Baru: {$this->submission->submission_number}",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.submission-submitted',
        );
    }
}
