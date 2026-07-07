<x-app-layout>
    @if($roleSlug === 'staff')
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card text-bg-primary">
                    <div class="card-body text-center">
                        <h3 class="mb-0">{{ $totalSubmissions }}</h3>
                        <div class="small">Total Pengajuan</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-bg-warning">
                    <div class="card-body text-center">
                        <h3 class="mb-0">{{ $pendingSubmissions }}</h3>
                        <div class="small">Menunggu Proses</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-bg-success">
                    <div class="card-body text-center">
                        <h3 class="mb-0">{{ $approvedSubmissions }}</h3>
                        <div class="small">Telah Dibayar</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-bg-danger">
                    <div class="card-body text-center">
                        <h3 class="mb-0">{{ $rejectedSubmissions }}</h3>
                        <div class="small">Ditolak</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Pengajuan Terbaru</h5>
                        <a href="{{ route('staff.submissions.index') }}" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>No. Pengajuan</th>
                                    <th>Kategori</th>
                                    <th>Nilai</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($recentSubmissions as $submission)
                                    <tr>
                                        <td>{{ $submission->submission_number }}</td>
                                        <td>{{ $submission->category->name }}</td>
                                        <td>Rp {{ number_format($submission->amount, 0, ',', '.') }}</td>
                                        <td>
                                            <span class="badge bg-{{ $submission->current_status === 'draft' ? 'secondary' : (in_array($submission->current_status, ['paid']) ? 'success' : ($submission->current_status === 'rejected' ? 'danger' : 'warning')) }}">
                                                {{ str_replace('_', ' ', ucfirst($submission->current_status)) }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-3 text-muted">
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
                        <h5 class="mb-0">Aksi Cepat</h5>
                    </div>
                    <div class="card-body">
                        <a href="{{ route('staff.submissions.create') }}" class="btn btn-primary w-100 mb-2">
                            <i class="bi bi-plus-lg"></i> Buat Pengajuan Baru
                        </a>
                        @if ($lastSubmission && $lastSubmission->current_status === 'draft')
                            <a href="{{ route('staff.submissions.show', $lastSubmission) }}" class="btn btn-warning w-100">
                                <i class="bi bi-pencil"></i> Lanjutkan Draft
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>

    @elseif(in_array($roleSlug, ['spv', 'manager', 'direktur']))
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <div class="card text-bg-warning">
                    <div class="card-body text-center">
                        <h2 class="mb-0">{{ $pendingCount }}</h2>
                        <div class="small">Pengajuan Menunggu Approval</div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card text-bg-primary">
                    <div class="card-body text-center">
                        <h2 class="mb-0">{{ ucfirst($roleSlug) }}</h2>
                        <div class="small">Level Approval Anda</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Antrian Approval Terbaru</h5>
                <a href="{{ route('approval.index') }}" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>No. Pengajuan</th>
                            <th>Pengaju</th>
                            <th>Kategori</th>
                            <th>Nilai</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($pendingSubmissions as $submission)
                            <tr>
                                <td>{{ $submission->submission_number }}</td>
                                <td>{{ $submission->user->name }}</td>
                                <td>{{ $submission->category->name }}</td>
                                <td>Rp {{ number_format($submission->amount, 0, ',', '.') }}</td>
                                <td>
                                    <a href="{{ route('approval.show', $submission) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i> Proses
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-3 text-muted">
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
            <div class="col-md-3">
                <div class="card text-bg-primary">
                    <div class="card-body text-center">
                        <h3 class="mb-0">{{ $waitingFinance }}</h3>
                        <div class="small">Menunggu Pembayaran</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-bg-success">
                    <div class="card-body text-center">
                        <h3 class="mb-0">{{ $totalPaid }}</h3>
                        <div class="small">Telah Dibayar</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-bg-danger">
                    <div class="card-body text-center">
                        <h3 class="mb-0">{{ $totalRejected }}</h3>
                        <div class="small">Ditolak</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-bg-info">
                    <div class="card-body text-center">
                        <h3 class="mb-0">Rp {{ number_format($cashBalance?->balance ?? 0, 0, ',', '.') }}</h3>
                        <div class="small">Saldo Kas</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Antrian Pembayaran</h5>
                <a href="{{ route('finance.index') }}" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>No. Pengajuan</th>
                            <th>Pengaju</th>
                            <th>Kategori</th>
                            <th>Nilai</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($recentWaiting as $submission)
                            <tr>
                                <td>{{ $submission->submission_number }}</td>
                                <td>{{ $submission->user->name }}</td>
                                <td>{{ $submission->category->name }}</td>
                                <td>Rp {{ number_format($submission->amount, 0, ',', '.') }}</td>
                                <td>
                                    <a href="{{ route('finance.show', $submission) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-cash"></i> Proses
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-3 text-muted">
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
