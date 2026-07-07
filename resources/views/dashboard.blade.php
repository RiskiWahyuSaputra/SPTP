<x-app-layout>
    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            @if($roleSlug === 'staff' && $monthlyData->isNotEmpty())
                const staffCtx = document.getElementById('staffChart');
                if (staffCtx) {
                    new Chart(staffCtx, {
                        type: 'bar',
                        data: {
                            labels: @json($monthlyData->pluck('month')->map(fn($m) => \Carbon\Carbon::parse($m . '-01')->translatedFormat('M Y'))),
                            datasets: [{
                                label: 'Jumlah Pengajuan',
                                data: @json($monthlyData->pluck('total')),
                                backgroundColor: 'rgba(161, 98, 7, 0.2)',
                                borderColor: '#A16207',
                                borderWidth: 2,
                                borderRadius: 6,
                                tension: 0.3,
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: { legend: { display: false } },
                            scales: {
                                y: { beginAtZero: true, ticks: { stepSize: 1, color: '#64748B' }, grid: { color: '#E2E8F0' } },
                                x: { ticks: { color: '#64748B' }, grid: { display: false } }
                            }
                        }
                    });
                }
            @endif

            @if($roleSlug === 'finance' && $monthlyPaid->isNotEmpty())
                const financeCtx = document.getElementById('financeChart');
                if (financeCtx) {
                    new Chart(financeCtx, {
                        type: 'line',
                        data: {
                            labels: @json($monthlyPaid->pluck('month')->map(fn($m) => \Carbon\Carbon::parse($m . '-01')->translatedFormat('M Y'))),
                            datasets: [{
                                label: 'Total Dibayar (Rp)',
                                data: @json($monthlyPaid->pluck('amount')),
                                backgroundColor: 'rgba(34, 197, 94, 0.1)',
                                borderColor: '#22C55E',
                                borderWidth: 2,
                                fill: true,
                                tension: 0.4,
                                pointRadius: 4,
                                pointBackgroundColor: '#22C55E',
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { display: false },
                                tooltip: {
                                    callbacks: {
                                        label: ctx => 'Rp ' + Number(ctx.raw).toLocaleString('id-ID')
                                    }
                                }
                            },
                            scales: {
                                y: { beginAtZero: true, ticks: { callback: v => 'Rp' + (v/1e6).toFixed(0) + 'jt', color: '#64748B' }, grid: { color: '#E2E8F0' } },
                                x: { ticks: { color: '#64748B' }, grid: { display: false } }
                            }
                        }
                    });
                }
            @endif
        });
    </script>
    @endpush

    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1" style="font-family: 'Lexend', sans-serif; color: var(--color-primary);">
                @switch($roleSlug)
                    @case('staff') Dashboard Staff @break
                    @case('spv') Dashboard SPV @break
                    @case('manager') Dashboard Manager @break
                    @case('direktur') Dashboard Direktur @break
                    @case('finance') Dashboard Finance @break
                    @default Dashboard
                @endswitch
            </h4>
            <p class="text-muted small mb-0">Selamat datang, {{ Auth::user()->name }} — {{ now()->translatedFormat('l, d F Y') }}</p>
        </div>
    </div>

    {{-- ==================== STAFF DASHBOARD ==================== --}}
    @if($roleSlug === 'staff')
        {{-- Stat Cards --}}
        <div class="row g-3 mb-4">
            <div class="col-6 col-xl-3">
                <div class="card border-0 shadow-sm h-100" style="border-radius: 12px; border-left: 4px solid var(--color-gold) !important;">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="d-flex align-items-center justify-content-center rounded" style="width: 44px; height: 44px; background: var(--color-gold-light); color: var(--color-gold);">
                                <i class="bi bi-file-text fs-5"></i>
                            </div>
                            <div class="flex-grow-1 min-w-0">
                                <p class="text-muted small text-uppercase fw-semibold mb-0" style="font-size: 11px; letter-spacing: 0.5px;">Total Pengajuan</p>
                                <h3 class="fw-bold mb-0" style="font-family: 'Lexend', sans-serif; color: var(--color-primary);">{{ $totalSubmissions }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-xl-3">
                <div class="card border-0 shadow-sm h-100" style="border-radius: 12px; border-left: 4px solid var(--color-gold) !important;">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="d-flex align-items-center justify-content-center rounded" style="width: 44px; height: 44px; background: var(--color-gold-light); color: var(--color-gold);">
                                <i class="bi bi-hourglass-split fs-5"></i>
                            </div>
                            <div class="flex-grow-1 min-w-0">
                                <p class="text-muted small text-uppercase fw-semibold mb-0" style="font-size: 11px; letter-spacing: 0.5px;">Menunggu</p>
                                <h3 class="fw-bold mb-0" style="font-family: 'Lexend', sans-serif; color: var(--color-primary);">{{ $pending }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-xl-3">
                <div class="card border-0 shadow-sm h-100" style="border-radius: 12px; border-left: 4px solid var(--color-gold) !important;">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="d-flex align-items-center justify-content-center rounded" style="width: 44px; height: 44px; background: var(--color-gold-light); color: var(--color-gold);">
                                <i class="bi bi-check-circle fs-5"></i>
                            </div>
                            <div class="flex-grow-1 min-w-0">
                                <p class="text-muted small text-uppercase fw-semibold mb-0" style="font-size: 11px; letter-spacing: 0.5px;">Dibayar</p>
                                <h3 class="fw-bold mb-0" style="font-family: 'Lexend', sans-serif; color: var(--color-primary);">{{ $paid }}</h3>
                                <small class="text-muted">Rp {{ number_format($paidAmount, 0, ',', '.') }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-xl-3">
                <div class="card border-0 shadow-sm h-100" style="border-radius: 12px; border-left: 4px solid var(--color-gold) !important;">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="d-flex align-items-center justify-content-center rounded" style="width: 44px; height: 44px; background: var(--color-gold-light); color: var(--color-gold);">
                                <i class="bi bi-x-circle fs-5"></i>
                            </div>
                            <div class="flex-grow-1 min-w-0">
                                <p class="text-muted small text-uppercase fw-semibold mb-0" style="font-size: 11px; letter-spacing: 0.5px;">Ditolak</p>
                                <h3 class="fw-bold mb-0" style="font-family: 'Lexend', sans-serif; color: var(--color-primary);">{{ $rejected }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Chart + Recent + Quick Actions --}}
        <div class="row g-3">
            <div class="col-xl-7">
                <div class="card border-0 shadow-sm" style="border-radius: 16px;">
                    <div class="card-header bg-transparent border-bottom d-flex justify-content-between align-items-center px-4 py-3">
                        <span class="fw-semibold" style="color: var(--color-primary);"><i class="bi bi-bar-chart-fill me-2 text-gold"></i>Tren Pengajuan (6 Bulan)</span>
                    </div>
                    @if($monthlyData->isNotEmpty())
                        <div class="card-body px-4 py-3">
                            <div style="height: 220px;">
                                <canvas id="staffChart"></canvas>
                            </div>
                        </div>
                    @else
                        <div class="card-body text-center py-4 text-muted small">
                            <i class="bi bi-bar-chart fs-2 d-block mb-2 text-muted"></i>
                            Belum ada data pengajuan dalam 6 bulan terakhir.
                        </div>
                    @endif
                </div>
            </div>
            <div class="col-xl-5">
                {{-- Quick Action --}}
                <div class="card border-0 shadow-sm mb-3" style="border-radius: 12px; border-left: 4px solid var(--color-gold) !important;">
                    <div class="card-body p-4">
                        <h6 class="fw-bold mb-2" style="font-family: 'Lexend', sans-serif; color: var(--color-primary);"><i class="bi bi-lightning-fill me-2 text-gold"></i>Aksi Cepat</h6>
                        <p class="text-muted small mb-3">Buat pengajuan baru atau lanjutkan draft yang tersimpan.</p>
                        <div class="d-flex gap-2">
                            <a href="{{ route('staff.submissions.create') }}" class="btn text-white fw-semibold flex-fill" style="background: var(--color-gold); border-color: var(--color-gold); border-radius: 8px;">
                                <i class="bi bi-plus-lg me-1"></i> Pengajuan Baru
                            </a>
                            @if($lastDraft)
                                <a href="{{ route('staff.submissions.show', $lastDraft) }}" class="btn btn-outline-gold flex-fill" style="border-radius: 8px;">
                                    <i class="bi bi-pencil me-1"></i> Draft
                                </a>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Ringkasan --}}
                <div class="card border-0 shadow-sm" style="border-radius: 16px;">
                    <div class="card-header bg-transparent border-bottom px-4 py-3">
                        <span class="fw-semibold" style="color: var(--color-primary);"><i class="bi bi-pie-chart-fill me-2 text-gold"></i>Ringkasan</span>
                    </div>
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="text-muted small">Total Nilai Pengajuan</span>
                            <span class="fw-bold" style="color: var(--color-primary);">Rp {{ number_format($totalAmount, 0, ',', '.') }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="text-muted small">Tingkat Persetujuan</span>
                            <span class="fw-bold" style="color: var(--color-success);">
                                {{ $totalSubmissions > 0 ? round(($paid / $totalSubmissions) * 100) : 0 }}%
                            </span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted small">Tingkat Penolakan</span>
                            <span class="fw-bold" style="color: var(--color-danger);">
                                {{ $totalSubmissions > 0 ? round(($rejected / $totalSubmissions) * 100) : 0 }}%
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Recent Submissions --}}
        <div class="card border-0 shadow-sm mt-3" style="border-radius: 16px;">
            <div class="card-header bg-transparent border-bottom d-flex justify-content-between align-items-center px-4 py-3">
                <span class="fw-semibold" style="color: var(--color-primary);"><i class="bi bi-clock-history me-2 text-gold"></i>Pengajuan Terbaru</span>
                <a href="{{ route('staff.submissions.index') }}" class="btn btn-sm btn-outline-gold">Lihat Semua <i class="bi bi-arrow-right ms-1"></i></a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4 text-muted small fw-semibold">No. Pengajuan</th>
                                <th class="text-muted small fw-semibold">Kategori</th>
                                <th class="text-muted small fw-semibold">Nilai</th>
                                <th class="text-muted small fw-semibold">Tanggal</th>
                                <th class="text-muted small fw-semibold">Status</th>
                                <th class="text-end pe-4 text-muted small fw-semibold">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($recentSubmissions as $submission)
                                <tr>
                                    <td class="ps-4 fw-semibold small">{{ $submission->submission_number }}</td>
                                    <td><span class="badge bg-gold-subtle text-gold fw-normal">{{ $submission->category->name }}</span></td>
                                    <td class="fw-semibold">Rp {{ number_format($submission->amount, 0, ',', '.') }}</td>
                                    <td class="text-muted small">{{ $submission->created_at->format('d/m/Y') }}</td>
                                    <td>
                                        @php
                                            $s = $submission->current_status;
                                            $badgeMap = [
                                                'draft' => ['bg' => 'bg-secondary', 'icon' => 'bi-pencil'],
                                                'submitted' => ['bg' => 'bg-info', 'icon' => 'bi-send'],
                                                'waiting_spv' => ['bg' => 'bg-warning text-dark', 'icon' => 'bi-hourglass'],
                                                'waiting_manager' => ['bg' => 'bg-warning text-dark', 'icon' => 'bi-hourglass'],
                                                'waiting_director' => ['bg' => 'bg-warning text-dark', 'icon' => 'bi-hourglass'],
                                                'waiting_finance' => ['bg' => 'bg-primary', 'icon' => 'bi-cash'],
                                                'paid' => ['bg' => 'bg-success', 'icon' => 'bi-check-circle'],
                                                'rejected' => ['bg' => 'bg-danger', 'icon' => 'bi-x-circle'],
                                            ];
                                            $b = $badgeMap[$s] ?? ['bg' => 'bg-secondary', 'icon' => 'bi-question'];
                                        @endphp
                                        <span class="badge {{ $b['bg'] }} rounded-pill px-3 py-1 fw-normal">
                                            <i class="bi {{ $b['icon'] }} me-1"></i> {{ str_replace('_', ' ', ucfirst($s)) }}
                                        </span>
                                    </td>
                                    <td class="text-end pe-4">
                                        <a href="{{ route('staff.submissions.show', $submission) }}" class="btn btn-sm btn-outline-gold rounded-pill px-3">
                                            <i class="bi bi-eye me-1"></i> Detail
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">
                                        <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                                        Belum ada pengajuan. <a href="{{ route('staff.submissions.create') }}">Buat sekarang</a>.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    {{-- ==================== APPROVER DASHBOARD ==================== --}}
    @elseif(in_array($roleSlug, ['spv', 'manager', 'direktur']))
        {{-- Stat Cards --}}
        <div class="row g-3 mb-4">
            <div class="col-6 col-xl-3">
                <div class="card border-0 shadow-sm h-100" style="border-radius: 12px; border-left: 4px solid var(--color-gold) !important;">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="d-flex align-items-center justify-content-center rounded" style="width: 44px; height: 44px; background: var(--color-gold-light); color: var(--color-gold);">
                                <i class="bi bi-inbox fs-5"></i>
                            </div>
                            <div class="flex-grow-1 min-w-0">
                                <p class="text-muted small text-uppercase fw-semibold mb-0" style="font-size: 11px; letter-spacing: 0.5px;">Menunggu Approval</p>
                                <h3 class="fw-bold mb-0" style="font-family: 'Lexend', sans-serif; color: var(--color-primary);">{{ $pendingCount }}</h3>
                                <small class="text-muted">Rp {{ number_format($pendingAmount, 0, ',', '.') }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-xl-3">
                <div class="card border-0 shadow-sm h-100" style="border-radius: 12px; border-left: 4px solid var(--color-gold) !important;">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="d-flex align-items-center justify-content-center rounded" style="width: 44px; height: 44px; background: var(--color-gold-light); color: var(--color-gold);">
                                <i class="bi bi-check2-all fs-5"></i>
                            </div>
                            <div class="flex-grow-1 min-w-0">
                                <p class="text-muted small text-uppercase fw-semibold mb-0" style="font-size: 11px; letter-spacing: 0.5px;">Disetujui</p>
                                <h3 class="fw-bold mb-0" style="font-family: 'Lexend', sans-serif; color: var(--color-primary);">{{ $approvedCount }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-xl-3">
                <div class="card border-0 shadow-sm h-100" style="border-radius: 12px; border-left: 4px solid var(--color-gold) !important;">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="d-flex align-items-center justify-content-center rounded" style="width: 44px; height: 44px; background: var(--color-gold-light); color: var(--color-gold);">
                                <i class="bi bi-x-circle fs-5"></i>
                            </div>
                            <div class="flex-grow-1 min-w-0">
                                <p class="text-muted small text-uppercase fw-semibold mb-0" style="font-size: 11px; letter-spacing: 0.5px;">Ditolak</p>
                                <h3 class="fw-bold mb-0" style="font-family: 'Lexend', sans-serif; color: var(--color-primary);">{{ $rejectedCount }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-xl-3">
                <div class="card border-0 shadow-sm h-100" style="border-radius: 12px; border-left: 4px solid var(--color-gold) !important;">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="d-flex align-items-center justify-content-center rounded" style="width: 44px; height: 44px; background: var(--color-gold-light); color: var(--color-gold);">
                                <i class="bi bi-shield-check fs-5"></i>
                            </div>
                            <div class="flex-grow-1 min-w-0">
                                <p class="text-muted small text-uppercase fw-semibold mb-0" style="font-size: 11px; letter-spacing: 0.5px;">Level Saya</p>
                                <h3 class="fw-bold mb-0" style="font-family: 'Lexend', sans-serif; color: var(--color-primary); text-transform: capitalize;">{{ $roleSlug }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Pending Approval Queue --}}
        <div class="card border-0 shadow-sm" style="border-radius: 16px;">
            <div class="card-header bg-transparent border-bottom d-flex justify-content-between align-items-center px-4 py-3">
                <span class="fw-semibold" style="color: var(--color-primary);"><i class="bi bi-inbox me-2 text-warning"></i>Antrian Approval</span>
                @if($pendingCount > 0)
                    <span class="badge bg-warning text-dark rounded-pill px-3">{{ $pendingCount }} menunggu</span>
                @endif
                <a href="{{ route('approval.index') }}" class="btn btn-sm btn-outline-gold ms-auto">Lihat Semua <i class="bi bi-arrow-right ms-1"></i></a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4 text-muted small fw-semibold">No. Pengajuan</th>
                                <th class="text-muted small fw-semibold">Pengaju</th>
                                <th class="text-muted small fw-semibold">Kategori</th>
                                <th class="text-muted small fw-semibold">Nilai</th>
                                <th class="text-muted small fw-semibold">Tanggal</th>
                                <th class="text-end pe-4 text-muted small fw-semibold">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($pendingSubmissions as $submission)
                                <tr>
                                    <td class="ps-4 fw-semibold small">{{ $submission->submission_number }}</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="d-flex align-items-center justify-content-center rounded-circle text-white fw-bold" style="width: 32px; height: 32px; font-size: 12px; background: var(--color-gold);">
                                                {{ substr($submission->user->name, 0, 1) }}
                                            </div>
                                            <span>{{ $submission->user->name }}</span>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-gold-subtle text-gold fw-normal">{{ $submission->category->name }}</span></td>
                                    <td class="fw-semibold">Rp {{ number_format($submission->amount, 0, ',', '.') }}</td>
                                    <td class="text-muted small">{{ $submission->created_at->format('d/m/Y') }}</td>
                                    <td class="text-end pe-4">
                                        <a href="{{ route('approval.show', $submission) }}" class="btn btn-sm text-white fw-semibold px-3 rounded-pill" style="background: var(--color-gold); border-color: var(--color-gold);">
                                            <i class="bi bi-check2-circle me-1"></i> Proses
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">
                                        <i class="bi bi-check2-all fs-3 d-block mb-2"></i>
                                        Tidak ada antrian approval. Semua pengajuan sudah diproses.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    {{-- ==================== FINANCE DASHBOARD ==================== --}}
    @elseif($roleSlug === 'finance')
        {{-- Stat Cards --}}
        <div class="row g-3 mb-4">
            <div class="col-6 col-xl-3">
                <div class="card border-0 shadow-sm h-100" style="border-radius: 12px; border-left: 4px solid var(--color-gold) !important;">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="d-flex align-items-center justify-content-center rounded" style="width: 44px; height: 44px; background: var(--color-gold-light); color: var(--color-gold);">
                                <i class="bi bi-clock fs-5"></i>
                            </div>
                            <div class="flex-grow-1 min-w-0">
                                <p class="text-muted small text-uppercase fw-semibold mb-0" style="font-size: 11px; letter-spacing: 0.5px;">Menunggu Pembayaran</p>
                                <h3 class="fw-bold mb-0" style="font-family: 'Lexend', sans-serif; color: var(--color-primary);">{{ $waitingFinance }}</h3>
                                <small class="text-muted">Rp {{ number_format($waitingAmount, 0, ',', '.') }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-xl-3">
                <div class="card border-0 shadow-sm h-100" style="border-radius: 12px; border-left: 4px solid var(--color-gold) !important;">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="d-flex align-items-center justify-content-center rounded" style="width: 44px; height: 44px; background: var(--color-gold-light); color: var(--color-gold);">
                                <i class="bi bi-credit-card-2-front fs-5"></i>
                            </div>
                            <div class="flex-grow-1 min-w-0">
                                <p class="text-muted small text-uppercase fw-semibold mb-0" style="font-size: 11px; letter-spacing: 0.5px;">Total Dibayar</p>
                                <h3 class="fw-bold mb-0" style="font-family: 'Lexend', sans-serif; color: var(--color-primary);">{{ $totalPaid }}</h3>
                                <small class="text-muted">Rp {{ number_format($totalPaidAmount, 0, ',', '.') }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-xl-3">
                <div class="card border-0 shadow-sm h-100" style="border-radius: 12px; border-left: 4px solid var(--color-gold) !important;">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="d-flex align-items-center justify-content-center rounded" style="width: 44px; height: 44px; background: var(--color-gold-light); color: var(--color-gold);">
                                <i class="bi bi-x-circle fs-5"></i>
                            </div>
                            <div class="flex-grow-1 min-w-0">
                                <p class="text-muted small text-uppercase fw-semibold mb-0" style="font-size: 11px; letter-spacing: 0.5px;">Ditolak</p>
                                <h3 class="fw-bold mb-0" style="font-family: 'Lexend', sans-serif; color: var(--color-primary);">{{ $totalRejected }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-xl-3">
                <div class="card border-0 shadow-sm h-100" style="border-radius: 12px; border-left: 4px solid var(--color-gold) !important;">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="d-flex align-items-center justify-content-center rounded" style="width: 44px; height: 44px; background: var(--color-gold-light); color: var(--color-gold);">
                                <i class="bi bi-wallet2 fs-5"></i>
                            </div>
                            <div class="flex-grow-1 min-w-0">
                                <p class="text-muted small text-uppercase fw-semibold mb-0" style="font-size: 11px; letter-spacing: 0.5px;">Saldo Kas</p>
                                <h4 class="fw-bold mb-0" style="font-family: 'Lexend', sans-serif; color: var(--color-primary);">
                                    Rp {{ number_format($cashBalance?->balance ?? 0, 0, ',', '.') }}
                                </h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-xl-7">
                {{-- Payment Queue --}}
                <div class="card border-0 shadow-sm" style="border-radius: 16px;">
                    <div class="card-header bg-transparent border-bottom d-flex justify-content-between align-items-center px-4 py-3">
                        <span class="fw-semibold" style="color: var(--color-primary);"><i class="bi bi-cash-stack me-2 text-success"></i>Antrian Pembayaran</span>
                        <a href="{{ route('finance.index') }}" class="btn btn-sm btn-outline-gold">Lihat Semua <i class="bi bi-arrow-right ms-1"></i></a>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0 align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-4 text-muted small fw-semibold">No. Pengajuan</th>
                                        <th class="text-muted small fw-semibold">Pengaju</th>
                                        <th class="text-muted small fw-semibold">Kategori</th>
                                        <th class="text-muted small fw-semibold">Nilai</th>
                                        <th class="text-end pe-4 text-muted small fw-semibold">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($recentWaiting as $submission)
                                        <tr>
                                            <td class="ps-4 fw-semibold small">{{ $submission->submission_number }}</td>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="d-flex align-items-center justify-content-center rounded-circle text-white fw-bold" style="width: 32px; height: 32px; font-size: 12px; background: var(--color-gold);">
                                                        {{ substr($submission->user->name, 0, 1) }}
                                                    </div>
                                                    <span>{{ $submission->user->name }}</span>
                                                </div>
                                            </td>
                                            <td><span class="badge bg-gold-subtle text-gold fw-normal">{{ $submission->category->name }}</span></td>
                                            <td class="fw-semibold">Rp {{ number_format($submission->amount, 0, ',', '.') }}</td>
                                            <td class="text-end pe-4">
                                                <a href="{{ route('finance.show', $submission) }}" class="btn btn-sm btn-success rounded-pill px-3">
                                                    <i class="bi bi-cash me-1"></i> Bayar
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-4 text-muted">
                                                <i class="bi bi-credit-card fs-3 d-block mb-2"></i>
                                                Tidak ada antrian pembayaran.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-5">
                {{-- Chart --}}
                <div class="card border-0 shadow-sm h-100" style="border-radius: 16px;">
                    <div class="card-header bg-transparent border-bottom px-4 py-3">
                        <span class="fw-semibold" style="color: var(--color-primary);"><i class="bi bi-graph-up me-2 text-success"></i>Tren Pembayaran (6 Bulan)</span>
                    </div>
                    @if($monthlyPaid->isNotEmpty())
                        <div class="card-body px-4 py-3">
                            <div style="height: 220px;">
                                <canvas id="financeChart"></canvas>
                            </div>
                        </div>
                    @else
                        <div class="card-body text-center py-4 text-muted small">
                            <i class="bi bi-graph-up fs-2 d-block mb-2 text-muted"></i>
                            Belum ada data pembayaran dalam 6 bulan terakhir.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif
</x-app-layout>
