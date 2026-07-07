<?php

namespace App\Http\Controllers;

use App\Models\Submission;
use App\Services\ExcelExportService;
use App\Services\PdfExportService;
use Illuminate\Http\Request;

class ExportController extends Controller
{
    protected PdfExportService $pdfExport;
    protected ExcelExportService $excelExport;

    public function __construct(PdfExportService $pdfExport, ExcelExportService $excelExport)
    {
        $this->pdfExport = $pdfExport;
        $this->excelExport = $excelExport;
    }

    public function pdfSubmission(Submission $submission)
    {
        if ($submission->user_id !== auth()->id() && !auth()->user()->hasRole(['spv', 'manager', 'direktur', 'finance'])) {
            abort(403);
        }

        $dompdf = $this->pdfExport->submissionDetail($submission);

        return $dompdf->stream("pengajuan-{$submission->submission_number}.pdf");
    }

    public function pdfSubmissionsReport(Request $request)
    {
        $user = auth()->user();
        $roleSlug = $user->role->slug;

        $query = Submission::with('category', 'user');

        if ($roleSlug === 'staff') {
            $query->where('user_id', $user->id);
        }

        if ($request->filled('status')) {
            $query->where('current_status', $request->status);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $submissions = $query->orderBy('created_at', 'desc')->get();

        $dateRange = '';
        if ($request->filled('date_from') || $request->filled('date_to')) {
            $dateRange = ($request->date_from ?? '...') . ' s/d ' . ($request->date_to ?? '...');
        }

        $dompdf = $this->pdfExport->submissionsReport($submissions, $dateRange);

        return $dompdf->stream('laporan-pengajuan.pdf');
    }

    public function pdfSubmissionDownload(Submission $submission)
    {
        if ($submission->user_id !== auth()->id() && !auth()->user()->hasRole(['spv', 'manager', 'direktur', 'finance'])) {
            abort(403);
        }

        $dompdf = $this->pdfExport->submissionDetail($submission);

        return $dompdf->download("pengajuan-{$submission->submission_number}.pdf");
    }

    public function excelSubmissions(Request $request)
    {
        $user = auth()->user();
        $roleSlug = $user->role->slug;

        $query = Submission::with('category', 'user');

        if ($roleSlug === 'staff') {
            $query->where('user_id', $user->id);
        }

        if ($request->filled('status')) {
            $query->where('current_status', $request->status);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $submissions = $query->orderBy('created_at', 'desc')->get();

        $filePath = $this->excelExport->submissionsExport($submissions);

        return response()->download($filePath, 'laporan-pengajuan-' . now()->format('Ymd') . '.xlsx')
            ->deleteFileAfterSend(true);
    }
}
