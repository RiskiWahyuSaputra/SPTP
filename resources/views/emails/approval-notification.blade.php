@component('mail::message')
# Notifikasi Approval

Halo **{{ $approverName }}**,

Pengajuan **{{ $submission->submission_number }}** telah **{{ $action === 'approved' ? 'disetujui' : 'ditolak' }}**.

**Detail Pengajuan:**
- **Nomor:** {{ $submission->submission_number }}
- **Pengaju:** {{ $submission->user->name }}
- **Kategori:** {{ $submission->category->name }}
- **Nilai:** Rp {{ number_format($submission->amount, 0, ',', '.') }}
- **Status:** {{ $action === 'approved' ? '✅ Disetujui' : '❌ Ditolak' }}

@if($submission->rejection_reason)
**Alasan:** {{ $submission->rejection_reason }}
@endif

@component('mail::button', ['url' => route('dashboard')])
Lihat Dashboard
@endcomponent

Terima kasih,<br>
{{ config('app.name') }}
@endcomponent
