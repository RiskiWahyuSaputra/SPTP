<x-app-layout>
    @if($roleSlug === 'staff')
        <div class="row g-3 mb-4">
            <div class="col-6 col-md-3">
                <div class="card border-0" style="border-left: 4px solid var(--color-accent) !important;">
                    <div class="card-body text-center py-3">
                        <div class="text-muted small text-uppercase fw-semibold tracking-wide">Total Pengajuan</div>
                        <div class="fw-bold" style="font-size: 2rem; color: var(--color-accent);">{{ $totalSubmissions }}</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card border-0" style="border-left: 4px solid var(--color-warning) !important;">
                    <div class="card-body text-center py-3">
                        <div class="text-muted small text-uppercase fw-semibold tracking-wide">Menunggu</div>
                        <div class="fw-bold" style="font-size: 2rem; color: var(--color-warning);">{{ $pendingSubmissions }}</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card border-0" style="border-left: 4px solid var(--color-success) !important;">
                    <div class="card-body text-center py-3">
                        <div class="text-muted small text-uppercase fw-semibold tracking-wide">Dibayar</div>
                        <div class="fw-bold" style="font-size: 2rem; color: var(--color-success);">{{ $approvedSubmissions }}</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card border-0" style="border-left: 4px solid var(--color-danger) !important;">
                    <div class="card-body text-center py-3">
                        <div class="text-muted small text-uppercase fw-semibold tracking-wide">Ditolak</div>
                        <div class="fw-bold" style="font-size: 2rem; color: var(--color-danger);">{{ $rejectedSubmissions }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-clock-history me-2 text-primary"></i>Pengajuan Terbaru</span>
                        <a href="{{ route('staff.submissions.index') }}" class="btn btn-sm btn-outline-primary">Lihat Semua <i class="bi bi-arrow-right ms-1"></i></a>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>No. Pengajuan</th>
                                    <th>Kategori</th>
                                    <th>Nilai</th>
                                    <th>Status</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($recentSubmissions as $submission)
                                    <tr>
                                        <td class="fw-semibold">{{ $submission->submission_number }}</td>
                                        <td>{{ $submission->category->name }}</td>
                                        <td>Rp {{ number_format($submission->amount, 0, ',', '.') }}</td>
                                        <td>
                                            @php
                                                $map = ['draft'=>'secondary','submitted'=>'info','waiting_spv'=>'warning','waiting_manager'=>'warning','waiting_director'=>'warning','waiting_finance'=>'primary','paid'=>'success','rejected'=>'danger'];
                                            @endphp
                                            <span class="badge bg-{{ $map[$submission->current_status] ?? 'secondary' }}">
                                                @switch($submission->current_status)
                                                    @case('draft') <i class="bi bi-pencil me-1"></i> @break
                                                    @case('submitted') <i class="bi bi-send me-1"></i> @break
                                                    @case('waiting_spv') <i class="bi bi-hourglass me-1"></i> @break
                                                    @case('waiting_manager') <i class="bi bi-hourglass me-1"></i> @break
                                                    @case('waiting_director') <i class="bi bi-hourglass me-1"></i> @break
                                                    @case('waiting_finance') <i class="bi bi-cash me-1"></i> @break
                                                    @case('paid') <i class="bi bi-check-circle me-1"></i> @break
                                                    @case('rejected') <i class="bi bi-x-circle me-1"></i> @break
                                                @endswitch
                                                {{ str_replace('_', ' ', ucfirst($submission->current_status)) }}
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <a href="{{ route('staff.submissions.show', $submission) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">
                                            <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                                            Belum ada pengajuan.
                                            <a href="{{ route('staff.submissions.create') }}">Buat sekarang</a>.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <i class="bi bi-lightning-fill me-2 text-warning"></i>Aksi Cepat
                    </div>
                    <div class="card-body">
                        <a href="{{ route('staff.submissions.create') }}" class="btn btn-primary w-100 mb-2 py-2">
                            <i class="bi bi-plus-lg me-2"></i> Buat Pengajuan Baru
                        </a>
                        @if ($lastSubmission && $lastSubmission->current_status === 'draft')
                            <a href="{{ route('staff.submissions.show', $lastSubmission) }}" class="btn btn-outline-warning w-100 py-2">
                                <i class="bi bi-pencil me-2"></i> Lanjutkan Draft
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>

    @elseif(in_array($roleSlug, ['spv', 'manager', 'direktur']))
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <div class="card border-0" style="border-left: 4px solid var(--color-warning) !important;">
                    <div class="card-body text-center py-4">
                        <div class="text-muted small text-uppercase fw-semibold tracking-wide">Menunggu Approval</div>
                        <div class="fw-bold" style="font-size: 2.5rem; color: var(--color-warning);">{{ $pendingCount }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card border-0" style="border-left: 4px solid var(--color-accent) !important;">
                    <div class="card-body text-center py-4">
                        <div class="text-muted small text-uppercase fw-semibold tracking-wide">Level Approval</div>
                        <div class="fw-bold" style="font-size: 2.5rem; color: var(--color-accent);">
                            {{ ucfirst($roleSlug) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-inbox me-2 text-warning"></i>Antrian Approval</span>
                <a href="{{ route('approval.index') }}" class="btn btn-sm btn-outline-primary">Lihat Semua <i class="bi bi-arrow-right ms-1"></i></a>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>No. Pengajuan</th>
                            <th>Pengaju</th>
                            <th>Kategori</th>
                            <th>Nilai</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($pendingSubmissions as $submission)
                            <tr>
                                <td class="fw-semibold">{{ $submission->submission_number }}</td>
                                <td>{{ $submission->user->name }}</td>
                                <td>{{ $submission->category->name }}</td>
                                <td>Rp {{ number_format($submission->amount, 0, ',', '.') }}</td>
                                <td class="text-end">
                                    <a href="{{ route('approval.show', $submission) }}" class="btn btn-sm btn-primary">
                                        <i class="bi bi-check2-circle me-1"></i> Proses
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">
                                    <i class="bi bi-check2-all fs-3 d-block mb-2"></i>
                                    Tidak ada antrian approval.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    @elseif($roleSlug === 'finance')
        <div class="row g-3 mb-4">
            <div class="col-6 col-md-3">
                <div class="card border-0" style="border-left: 4px solid var(--color-accent) !important;">
                    <div class="card-body text-center py-3">
                        <div class="text-muted small text-uppercase fw-semibold">Menunggu</div>
                        <div class="fw-bold" style="font-size: 2rem; color: var(--color-accent);">{{ $waitingFinance }}</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card border-0" style="border-left: 4px solid var(--color-success) !important;">
                    <div class="card-body text-center py-3">
                        <div class="text-muted small text-uppercase fw-semibold">Dibayar</div>
                        <div class="fw-bold" style="font-size: 2rem; color: var(--color-success);">{{ $totalPaid }}</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card border-0" style="border-left: 4px solid var(--color-danger) !important;">
                    <div class="card-body text-center py-3">
                        <div class="text-muted small text-uppercase fw-semibold">Ditolak</div>
                        <div class="fw-bold" style="font-size: 2rem; color: var(--color-danger);">{{ $totalRejected }}</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card border-0" style="border-left: 4px solid var(--color-success) !important;">
                    <div class="card-body text-center py-3">
                        <div class="text-muted small text-uppercase fw-semibold">Saldo Kas</div>
                        <div class="fw-bold @if($cashBalance && $cashBalance->balance > 0) text-success @else text-danger @endif" style="font-size: 1.2rem;">
                            Rp {{ number_format($cashBalance?->balance ?? 0, 0, ',', '.') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-cash-stack me-2 text-primary"></i>Antrian Pembayaran</span>
                <a href="{{ route('finance.index') }}" class="btn btn-sm btn-outline-primary">Lihat Semua <i class="bi bi-arrow-right ms-1"></i></a>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>No. Pengajuan</th>
                            <th>Pengaju</th>
                            <th>Kategori</th>
                            <th>Nilai</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($recentWaiting as $submission)
                            <tr>
                                <td class="fw-semibold">{{ $submission->submission_number }}</td>
                                <td>{{ $submission->user->name }}</td>
                                <td>{{ $submission->category->name }}</td>
                                <td>Rp {{ number_format($submission->amount, 0, ',', '.') }}</td>
                                <td class="text-end">
                                    <a href="{{ route('finance.show', $submission) }}" class="btn btn-sm btn-success">
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
    @endif
</x-app-layout>
