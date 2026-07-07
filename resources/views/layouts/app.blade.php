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

    <x-confirm-dialog />

    {{-- Attachment Preview Modal --}}
    <div class="modal fade" id="attachmentModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content border-0 shadow" style="border-radius: 12px;">
                <div class="modal-header border-0 pb-0">
                    <h6 class="modal-title fw-bold" id="attachmentModalTitle" style="font-family: 'Lexend', sans-serif;">Lampiran</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body text-center p-3" id="attachmentModalBody">
                    <div class="text-muted py-5">
                        <div class="spinner-border text-gold" role="status">
                            <span class="visually-hidden">Memuat...</span>
                        </div>
                        <p class="mt-2 small">Memuat lampiran...</p>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 justify-content-between">
                    <span class="small text-muted" id="attachmentModalInfo"></span>
                    <a href="#" class="btn btn-sm btn-outline-secondary" id="attachmentDownloadBtn" download>
                        <i class="bi bi-download me-1"></i> Download
                    </a>
                </div>
            </div>
        </div>
    </div>

    @stack('scripts')

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

            // Attachment preview modal
            document.querySelectorAll('[data-attachment]').forEach(el => {
                el.addEventListener('click', function (e) {
                    e.preventDefault();
                    const url = this.getAttribute('href');
                    const name = this.getAttribute('data-attachment-name') || 'Lampiran';
                    const type = this.getAttribute('data-attachment-type') || '';
                    const isImage = ['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(type);

                    document.getElementById('attachmentModalTitle').textContent = name;
                    document.getElementById('attachmentModalInfo').textContent = name + (type ? ' (' + type.toUpperCase() + ')' : '');
                    document.getElementById('attachmentDownloadBtn').href = url;

                    const body = document.getElementById('attachmentModalBody');
                    if (isImage) {
                        body.innerHTML = '<img src="' + url + '" class="img-fluid rounded" style="max-height: 75vh; object-fit: contain;" alt="' + name + '">';
                    } else {
                        body.innerHTML = '<iframe src="' + url + '" class="w-100 border-0 rounded" style="height: 70vh;" title="' + name + '"></iframe>';
                    }

                    new bootstrap.Modal(document.getElementById('attachmentModal')).show();
                });
            });
        });
    </script>
</body>
</html>
