<nav class="navbar navbar-expand-md">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('dashboard') }}">
            <i class="bi bi-receipt-cutoff"></i>
            <span>{{ config('app.name', 'SPTP') }}</span>
        </a>
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" style="color: rgba(255,255,255,.75);">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                        <i class="bi bi-grid-fill me-1"></i> Dashboard
                    </a>
                </li>
                @if(Auth::user()?->hasRole('staff'))
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('staff.submissions.*') ? 'active' : '' }}" href="{{ route('staff.submissions.index') }}">
                            <i class="bi bi-file-earmark-text me-1"></i> Pengajuan Saya
                        </a>
                    </li>
                @endif
                @if(Auth::user()?->hasRole(['spv', 'manager', 'direktur']))
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('approval.*') ? 'active' : '' }}" href="{{ route('approval.index') }}">
                            <i class="bi bi-check2-square me-1"></i> Approval
                        </a>
                    </li>
                @endif
                @if(Auth::user()?->hasRole('finance'))
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('finance.*') ? 'active' : '' }}" href="{{ route('finance.index') }}">
                            <i class="bi bi-cash-stack me-1"></i> Pembayaran
                        </a>
                    </li>
                @endif
            </ul>

            <ul class="navbar-nav">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" style="height: 64px; padding: 0 .75rem!important;">
                        <span class="d-flex align-items-center justify-content-center rounded-circle bg-white text-dark fw-bold" style="width: 32px; height: 32px; font-size: .8rem;">
                            {{ substr(Auth::user()->name, 0, 1) }}
                        </span>
                        <span class="d-none d-md-inline" style="font-size: .85rem;">{{ Auth::user()->name }}</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                <i class="bi bi-person me-2"></i> Profile
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="bi bi-box-arrow-right me-2"></i> Log Out
                                </button>
                            </form>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>
