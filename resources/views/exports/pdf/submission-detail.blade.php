<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Detail Pengajuan - {{ $submission->submission_number }}</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #333; }
        .header { border-bottom: 2px solid #A16207; padding-bottom: 10px; margin-bottom: 20px; }
        .header h1 { color: #A16207; margin: 0; font-size: 18px; }
        .header p { margin: 2px 0; color: #666; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        table th, table td { padding: 6px 8px; text-align: left; border: 1px solid #ddd; }
        table th { background: #f5f5f5; font-weight: 600; width: 30%; }
        .section-title { font-size: 14px; font-weight: 700; color: #A16207; margin: 15px 0 8px; }
        .status-badge { display: inline-block; padding: 3px 10px; border-radius: 4px; font-size: 11px; font-weight: 600; }
        .status-paid { background: #d4edda; color: #155724; }
        .status-rejected { background: #f8d7da; color: #721c24; }
        .status-pending { background: #fff3cd; color: #856404; }
        .status-draft { background: #e2e3e5; color: #383d41; }
        .footer { margin-top: 30px; text-align: center; color: #999; font-size: 10px; border-top: 1px solid #ddd; padding-top: 10px; }
        .approval-step { margin: 3px 0; }
        .text-right { text-align: right; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Sistem Pengajuan Transaksi Pengeluaran</h1>
        <p>Detail Pengajuan</p>
    </div>

    <table>
        <tr><th>No. Pengajuan</th><td>{{ $submission->submission_number }}</td></tr>
        <tr><th>Tanggal</th><td>{{ $submission->submission_date->format('d/m/Y') }}</td></tr>
        <tr><th>Pengaju</th><td>{{ $submission->user->name }}</td></tr>
        <tr><th>Kategori</th><td>{{ $submission->category->name }}</td></tr>
        <tr><th>Nilai</th><td>Rp {{ number_format($submission->amount, 0, ',', '.') }}</td></tr>
        <tr><th>Status</th>
            <td>
                <span class="status-badge
                    @switch($submission->current_status)
                        @case('paid') status-paid @break
                        @case('rejected') status-rejected @break
                        @case('draft') status-draft @break
                        @default status-pending
                    @endswitch">
                    {{ str_replace('_', ' ', ucfirst($submission->current_status)) }}
                </span>
            </td>
        </tr>
        <tr><th>Deskripsi</th><td>{{ $submission->description }}</td></tr>
        @if($submission->rejection_reason)
            <tr><th>Alasan Ditolak</th><td>{{ $submission->rejection_reason }}</td></tr>
        @endif
    </table>

    @if($submission->approvals->isNotEmpty())
        <div class="section-title">Riwayat Approval</div>
        <table>
            <thead>
                <tr>
                    <th>Level</th>
                    <th>Approver</th>
                    <th>Keputusan</th>
                    <th>Catatan</th>
                    <th>Waktu</th>
                </tr>
            </thead>
            <tbody>
                @foreach($submission->approvals->sortBy('sequence') as $approval)
                    <tr>
                        <td>{{ $approval->role->name }}</td>
                        <td>{{ $approval->approver?->name ?? '-' }}</td>
                        <td>
                            @if($approval->decision === 'approved') ✅ Disetujui
                            @elseif($approval->decision === 'rejected') ❌ Ditolak
                            @else ⏳ Menunggu
                            @endif
                        </td>
                        <td>{{ $approval->notes ?? '-' }}</td>
                        <td>{{ $approval->decided_at ? $approval->decided_at->format('d/m/Y H:i') : '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    @if($submission->payment)
        <div class="section-title">Informasi Pembayaran</div>
        <table>
            <tr><th>Status</th><td>{{ $submission->payment->status === 'paid' ? '✅ Dibayar' : '❌ ' . str_replace('_', ' ', ucfirst($submission->payment->status)) }}</td></tr>
            <tr><th>Diproses Oleh</th><td>{{ $submission->payment->processedBy?->name ?? '-' }}</td></tr>
            <tr><th>Saldo Sebelum</th><td>Rp {{ number_format($submission->payment->balance_before, 0, ',', '.') }}</td></tr>
            <tr><th>Saldo Setelah</th><td>Rp {{ number_format($submission->payment->balance_after, 0, ',', '.') }}</td></tr>
            @if($submission->payment->paid_at)
                <tr><th>Waktu Bayar</th><td>{{ $submission->payment->paid_at->format('d/m/Y H:i') }}</td></tr>
            @endif
        </table>
    @endif

    <div class="footer">
        <p>Dicetak pada {{ now()->format('d/m/Y H:i') }} | {{ config('app.name') }}</p>
    </div>
</body>
</html>
