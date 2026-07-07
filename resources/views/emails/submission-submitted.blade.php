<x-mail::message>
# Pengajuan Baru

Halo **{{ $approverName }}**,

Terdapat pengajuan baru yang membutuhkan persetujuan Anda.

**Detail Pengajuan:**
- **Nomor:** {{ $submission->submission_number }}
- **Pengaju:** {{ $submission->user->name }}
- **Kategori:** {{ $submission->category->name }}
- **Nilai:** Rp {{ number_format($submission->amount, 0, ',', '.') }}
- **Tanggal:** {{ $submission->submission_date->format('d/m/Y') }}
- **Deskripsi:** {{ $submission->description }}

<x-mail::button :url="route('approval.show', $submission)">
Lihat & Proses
</x-mail::button>

Terima kasih,<br>
{{ config('app.name') }}
</x-mail::message>
