<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'SPTP') }} — {{ $title ?? 'Login' }}</title>
    @vite(['resources/js/app.js'])
</head>
<body style="background: #F8FAFC;">
    <div class="min-vh-100 d-flex flex-column justify-content-center align-items-center py-5 px-3">
        <div class="mb-4 text-center">
            <a href="/" class="text-decoration-none">
                <div class="d-flex align-items-center justify-content-center gap-2 mb-2">
                    <i class="bi bi-receipt-cutoff fs-1" style="color: #A16207;"></i>
                    <h3 class="fw-bold mb-0" style="font-family: 'Lexend', sans-serif; color: #0F172A;">SPTP</h3>
                </div>
                <p class="text-muted small mb-0">Sistem Pengajuan Transaksi Pengeluaran</p>
            </a>
        </div>

        <div class="card shadow-sm border-0" style="max-width: 440px; width: 100%; border-radius: 16px; border-top: 4px solid #A16207 !important;">
            <div class="card-body p-4">
                {{ $slot }}
            </div>
        </div>

        <p class="text-muted small mt-4">&copy; {{ date('Y') }} SPTP. All rights reserved.</p>
    </div>
</body>
</html>
