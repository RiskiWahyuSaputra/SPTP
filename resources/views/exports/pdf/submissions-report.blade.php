<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Pengajuan</title>
    <style>
        body { font-family: sans-serif; font-size: 11px; color: #333; }
        .header { border-bottom: 2px solid #A16207; padding-bottom: 10px; margin-bottom: 20px; }
        .header h1 { color: #A16207; margin: 0; font-size: 18px; }
        .header p { margin: 2px 0; color: #666; }
        table { width: 100%; border-collapse: collapse; }
        table th, table td { padding: 5px 6px; text-align: left; border: 1px solid #ddd; font-size: 10px; }
        table th { background: #f5f5f5; font-weight: 600; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .footer { margin-top: 30px; text-align: center; color: #999; font-size: 9px; border-top: 1px solid #ddd; padding-top: 10px; }
        .summary { margin-bottom: 15px; }
        .summary table { width: auto; }
        .summary table th, .summary table td { border: none; padding: 2px 10px 2px 0; }
        .summary table th { background: transparent; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Sistem Pengajuan Transaksi Pengeluaran</h1>
        <p>Laporan Pengajuan {{ $dateRange ? "- {$dateRange}" : '' }}</p>
    </div>

    @php
        $totalAmount = $submissions->sum('amount');
        $totalPaid = $submissions->where('current_status', 'paid')->sum('amount');
        $totalRejected = $submissions->where('current_status', 'rejected')->count();
    @endphp

    <div class="summary">
        <table>
            <tr><th>Total Pengajuan</th><td>{{ $submissions->count() }}</td></tr>
            <tr><th>Total Nilai</th><td>Rp {{ number_format($totalAmount, 0, ',', '.') }}</td></tr>
            <tr><th>Total Dibayar</th><td>Rp {{ number_format($totalPaid, 0, ',', '.') }}</td></tr>
            <tr><th>Total Ditolak</th><td>{{ $totalRejected }}</td></tr>
        </table>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>No. Pengajuan</th>
                <th>Tanggal</th>
                <th>Pengaju</th>
                <th>Kategori</th>
                <th class="text-right">Nilai</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($submissions as $i => $submission)
                <tr>
                    <td class="text-center">{{ $i + 1 }}</td>
                    <td>{{ $submission->submission_number }}</td>
                    <td>{{ $submission->submission_date->format('d/m/Y') }}</td>
                    <td>{{ $submission->user->name }}</td>
                    <td>{{ $submission->category->name }}</td>
                    <td class="text-right">Rp {{ number_format($submission->amount, 0, ',', '.') }}</td>
                    <td>{{ str_replace('_', ' ', ucfirst($submission->current_status)) }}</td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center">Tidak ada data</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>Dicetak pada {{ now()->format('d/m/Y H:i') }} | {{ config('app.name') }}</p>
    </div>
</body>
</html>
