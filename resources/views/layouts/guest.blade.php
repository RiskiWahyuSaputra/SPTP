<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'SPTP') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-light">
    <div class="min-vh-100 d-flex flex-column justify-content-center align-items-center py-5">
        <div class="mb-4">
            <a href="/" class="text-decoration-none">
                <h3 class="text-primary fw-bold">{{ config('app.name', 'SPTP') }}</h3>
            </a>
        </div>

        <div class="card shadow-sm" style="max-width: 450px; width: 100%;">
            <div class="card-body p-4">
                {{ $slot }}
            </div>
        </div>
    </div>
</body>
</html>
