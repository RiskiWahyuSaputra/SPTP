<?php

namespace App\Services;

class ExcelExportService
{
    public function submissionsExport($submissions): string
    {
        $path = storage_path('app/temp/exports');
        if (!is_dir($path)) {
            mkdir($path, 0755, true);
        }
        $filePath = $path . '/submissions-' . now()->timestamp . '.xlsx';

        $xml = $this->buildXlsx($submissions);
        file_put_contents($filePath, $xml);

        return $filePath;
    }

    private function buildXlsx($submissions): string
    {
        $rows = [];

        $rows[] = '<row><c t="inline" r="A1"><is><t>No</t></is></c><c t="inline" r="B1"><is><t>No. Pengajuan</t></is></c><c t="inline" r="C1"><is><t>Tanggal</t></is></c><c t="inline" r="D1"><is><t>Pengaju</t></is></c><c t="inline" r="E1"><is><t>Kategori</t></is></c><c t="inline" r="F1"><is><t>Nilai</t></is></c><c t="inline" r="G1"><is><t>Status</t></is></c><c t="inline" r="H1"><is><t>Deskripsi</t></is></c><c t="inline" r="I1"><is><t>Tgl Dibuat</t></is></c></row>';

        foreach ($submissions as $i => $s) {
            $r = $i + 2;
            $rows[] = '<row>'
                . '<c t="inline" r="A' . $r . '"><is><t>' . $this->esc($i + 1) . '</t></is></c>'
                . '<c t="inline" r="B' . $r . '"><is><t>' . $this->esc($s->submission_number) . '</t></is></c>'
                . '<c t="inline" r="C' . $r . '"><is><t>' . $this->esc($s->submission_date->format('d/m/Y')) . '</t></is></c>'
                . '<c t="inline" r="D' . $r . '"><is><t>' . $this->esc($s->user->name) . '</t></is></c>'
                . '<c t="inline" r="E' . $r . '"><is><t>' . $this->esc($s->category->name) . '</t></is></c>'
                . '<c t="inline" r="F' . $r . '"><is><t>' . $this->esc((string) $s->amount) . '</t></is></c>'
                . '<c t="inline" r="G' . $r . '"><is><t>' . $this->esc(str_replace('_', ' ', ucfirst($s->current_status))) . '</t></is></c>'
                . '<c t="inline" r="H' . $r . '"><is><t>' . $this->esc($s->description) . '</t></is></c>'
                . '<c t="inline" r="I' . $r . '"><is><t>' . $this->esc($s->created_at->format('d/m/Y H:i')) . '</t></is></c>'
                . '</row>';
        }

        $xml = '<?xml version="1.0" encoding="UTF-8"?>'
            . '<?mso-application progid="Excel.Sheet"?>'
            . '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"'
            . ' xmlns:o="urn:schemas-microsoft-com:office:office"'
            . ' xmlns:x="urn:schemas-microsoft-com:office:excel"'
            . ' xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet">'
            . '<Worksheet ss:Name="Submissions">'
            . '<Table>' . implode('', $rows) . '</Table>'
            . '</Worksheet>'
            . '</Workbook>';

        return $xml;
    }

    private function esc(string $value): string
    {
        return htmlspecialchars($value, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    }
}
