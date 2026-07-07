<div class="nav-section-label">Menu Utama</div>

<a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
    <i class="bi bi-grid-fill"></i>
    <span>Dashboard</span>
</a>

@if(Auth::user()?->hasRole('staff'))
    <a class="nav-link {{ request()->routeIs('staff.submissions.*') ? 'active' : '' }}" href="{{ route('staff.submissions.index') }}">
        <i class="bi bi-file-earmark-text"></i>
        <span>Pengajuan Saya</span>
    </a>
@endif

@if(Auth::user()?->hasRole(['spv', 'manager', 'direktur']))
    <div class="nav-section-label mt-2">Approval</div>

    <a class="nav-link {{ request()->routeIs('approval.*') ? 'active' : '' }}" href="{{ route('approval.index') }}">
        <i class="bi bi-check2-square"></i>
        <span>Approval</span>
    </a>
@endif

@if(Auth::user()?->hasRole('finance'))
    <div class="nav-section-label mt-2">Keuangan</div>

    <a class="nav-link {{ request()->routeIs('finance.*') ? 'active' : '' }}" href="{{ route('finance.index') }}">
        <i class="bi bi-cash-stack"></i>
        <span>Pembayaran</span>
    </a>
@endif

<div class="nav-section-label mt-2">Sistem</div>

<a class="nav-link {{ request()->routeIs('activity-logs.*') ? 'active' : '' }}" href="{{ route('activity-logs.index') }}">
    <i class="bi bi-clock-history"></i>
    <span>Activity Log</span>
</a>
