<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'SPTP') }} — {{ $header ?? 'Dashboard' }}</title>
    @vite(['resources/js/app.js'])
</head>
<body>

    {{-- Sidebar --}}
    <aside class="sidebar" id="sidebar">
        <button class="sidebar-close" id="sidebarClose" aria-label="Tutup sidebar">&times;</button>

        <div class="sidebar-brand">
            <a href="{{ route('dashboard') }}">
                <i class="bi bi-receipt-cutoff"></i>
                <span>{{ config('app.name', 'SPTP') }}</span>
            </a>
        </div>

        <nav class="sidebar-nav">
            @include('layouts.navigation')
        </nav>

        <div class="sidebar-footer">
            <div class="sidebar-user">
                <div class="avatar">{{ substr(Auth::user()->name, 0, 1) }}</div>
                <div class="user-info">
                    <div class="user-name">{{ Auth::user()->name }}</div>
                    <div class="user-role">{{ Auth::user()->role?->name ?? 'User' }}</div>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="logout-btn">
                    <i class="bi bi-box-arrow-right"></i>
                    <span>Log Out</span>
                </button>
            </form>
        </div>
    </aside>

    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    {{-- Main Content --}}
    <div class="main-content">
        <div class="topbar">
            <div class="topbar-left">
                <button class="toggle-sidebar" id="toggleSidebar" aria-label="Buka sidebar">
                    <i class="bi bi-list"></i>
                </button>
                <span class="topbar-title">{{ $header ?? 'Dashboard' }}</span>
            </div>
            <div class="topbar-right">
                <span class="text-muted small d-none d-sm-inline">{{ Auth::user()->name }}</span>
            </div>
        </div>

        <main class="page-content">
            {{ $slot }}
        </main>

        <div class="page-footer">
            &copy; {{ date('Y') }} {{ config('app.name', 'SPTP') }}. All rights reserved.
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            const toggleBtn = document.getElementById('toggleSidebar');
            const closeBtn = document.getElementById('sidebarClose');

            function openSidebar() {
                sidebar.classList.add('open');
                overlay.classList.add('show');
                document.body.style.overflow = 'hidden';
            }

            function closeSidebar() {
                sidebar.classList.remove('open');
                overlay.classList.remove('show');
                document.body.style.overflow = '';
            }

            if (toggleBtn) toggleBtn.addEventListener('click', openSidebar);
            if (closeBtn) closeBtn.addEventListener('click', closeSidebar);
            if (overlay) overlay.addEventListener('click', closeSidebar);
        });
    </script>
</body>
</html>
