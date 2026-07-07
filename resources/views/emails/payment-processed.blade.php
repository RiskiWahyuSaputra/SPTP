@component('mail::message')
# Pembayaran {{ $status === 'paid' ? 'Selesai' : 'Ditolak' }}

Halo **{{ $submission->user->name }}**,

Pembayaran untuk pengajuan Anda telah **{{ $status === 'paid' ? 'diproses' : 'ditolak' }}**.

**Detail Pengajuan:**
- **Nomor:** {{ $submission->submission_number }}
- **Kategori:** {{ $submission->category->name }}
- **Nilai:** Rp {{ number_format($submission->amount, 0, ',', '.') }}
- **Status Akhir:** {{ $status === 'paid' ? '✅ Dibayar' : '❌ ' . ($submission->rejection_reason ?? 'Ditolak') }}

@component('mail::button', ['url' => route('dashboard')])
Lihat Dashboard
@endcomponent

Terima kasih,<br>
{{ config('app.name') }}
@endcomponent
