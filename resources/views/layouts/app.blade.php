<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'SPTP') }} — {{ $header ?? 'Dashboard' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div class="d-flex flex-column min-vh-100">
        @include('layouts.navigation')

        @isset($header)
            <div class="bg-white border-bottom" style="padding: 1.25rem 0;">
                <div class="container d-flex align-items-center justify-content-between">
                    <h5 class="mb-0 fw-bold" style="font-size: 1.15rem;">{{ $header }}</h5>
                </div>
            </div>
        @endisset

        <main class="flex-grow-1" style="padding: 1.5rem 0;">
            <div class="container">
                {{ $slot }}
            </div>
        </main>

        <footer class="py-3 text-center">
            <div class="container">
                &copy; {{ date('Y') }} {{ config('app.name', 'SPTP') }}. All rights reserved.
            </div>
        </footer>
    </div>
</body>
</html>
