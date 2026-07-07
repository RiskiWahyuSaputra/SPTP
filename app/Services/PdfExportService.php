<?php

namespace App\Services;

use App\Models\Submission;
use Dompdf\Dompdf;
use Dompdf\Options;

class PdfExportService
{
    public function submissionDetail(Submission $submission): \Dompdf\Dompdf
    {
        $submission->load('category', 'user', 'attachments', 'approvals.approver', 'approvals.role', 'payment');

        $html = view('exports.pdf.submission-detail', compact('submission'))->render();

        return $this->render($html, "pengajuan-{$submission->submission_number}.pdf");
    }

    public function submissionsReport($submissions, string $dateRange = ''): \Dompdf\Dompdf
    {
        $html = view('exports.pdf.submissions-report', compact('submissions', 'dateRange'))->render();

        return $this->render($html, 'laporan-pengajuan.pdf');
    }

    private function render(string $html, string $filename): \Dompdf\Dompdf
    {
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', false);
        $options->set('defaultFont', 'sans-serif');

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return $dompdf;
    }
}
