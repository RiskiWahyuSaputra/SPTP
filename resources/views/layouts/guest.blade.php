<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'SPTP') }} — Login</title>
    @vite(['resources/js/app.js'])
</head>
<body class="bg-light" style="background: linear-gradient(135deg, #0F172A 0%, #1E293B 50%, #0F172A 100%);">
    <div class="min-vh-100 d-flex flex-column justify-content-center align-items-center py-5 px-3">
        <div class="mb-4 text-center">
            <a href="/" class="text-decoration-none">
                <div class="d-flex align-items-center justify-content-center gap-2 mb-2">
                    <i class="bi bi-receipt-cutoff fs-1" style="color: #A16207;"></i>
                    <h3 class="text-white fw-bold mb-0" style="font-family: 'Lexend', sans-serif;">SPTP</h3>
                </div>
                <p class="text-white-50 small mb-0">Sistem Pengajuan Transaksi Pengeluaran</p>
            </a>
        </div>

        <div class="card shadow-lg border-0" style="max-width: 440px; width: 100%; border-radius: 16px;">
            <div class="card-body p-4">
                {{ $slot }}
            </div>
        </div>

        <p class="text-white-50 small mt-4">&copy; {{ date('Y') }} SPTP. All rights reserved.</p>
    </div>
</body>
</html>
