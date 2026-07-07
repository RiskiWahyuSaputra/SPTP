<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'SPTP') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div class="d-flex flex-column min-vh-100 bg-light">
        @include('layouts.navigation')

        @isset($header)
            <div class="bg-white shadow-sm">
                <div class="container py-3">
                    <h4 class="mb-0">{{ $header }}</h4>
                </div>
            </div>
        @endisset

        <main class="flex-grow-1 py-4">
            <div class="container">
                {{ $slot }}
            </div>
        </main>
    </div>
</body>
</html>
