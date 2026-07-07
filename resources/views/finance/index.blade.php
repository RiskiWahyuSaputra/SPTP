<x-app-layout>
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold mb-0" style="font-size: 1.1rem;">
            <i class="bi bi-cash-stack me-2 text-success"></i>Daftar Pembayaran
        </h5>
        @if ($cashBalance)
            <div class="d-flex align-items-center gap-2">
                <span class="text-muted small">Saldo Kas:</span>
                <span class="fw-bold @if($cashBalance->balance > 0) text-success @else text-danger @endif">
                    Rp {{ number_format($cashBalance->balance, 0, ',', '.') }}
                </span>
            </div>
        @endif
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm">{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>No. Pengajuan</th>
                            <th>Pengaju</th>
                            <th>Kategori</th>
                            <th>Nilai</th>
                            <th>Tanggal</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($submissions as $submission)
                            <tr>
                                <td class="fw-semibold">{{ $submission->submission_number }}</td>
                                <td>{{ $submission->user->name }}</td>
                                <td>{{ $submission->category->name }}</td>
                                <td>Rp {{ number_format($submission->amount, 0, ',', '.') }}</td>
                                <td class="text-muted small">{{ $submission->created_at->format('d/m/Y') }}</td>
                                <td class="text-end">
                                    <a href="{{ route('finance.show', $submission) }}" class="btn btn-sm btn-success">
                                        <i class="bi bi-cash me-1"></i> Proses
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="bi bi-credit-card fs-1 d-block mb-2"></i>
                                    Tidak ada pengajuan yang menunggu pembayaran.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-3">
        {{ $submissions->links() }}
    </div>
</x-app-layout>
