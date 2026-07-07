<x-app-layout>
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0">Daftar Pembayaran</h5>
        @if ($cashBalance)
            <div class="d-flex align-items-center gap-3">
                <span class="text-muted small">Saldo Kas:</span>
                <span class="fw-bold fs-5 {{ $cashBalance->balance > 0 ? 'text-success' : 'text-danger' }}">
                    Rp {{ number_format($cashBalance->balance, 0, ',', '.') }}
                </span>
            </div>
        @endif
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>No. Pengajuan</th>
                        <th>Pengaju</th>
                        <th>Kategori</th>
                        <th>Nilai</th>
                        <th>Tgl. Masuk</th>
                        <th width="120">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($submissions as $submission)
                        <tr>
                            <td>{{ $submission->submission_number }}</td>
                            <td>{{ $submission->user->name }}</td>
                            <td>{{ $submission->category->name }}</td>
                            <td>Rp {{ number_format($submission->amount, 0, ',', '.') }}</td>
                            <td>{{ $submission->created_at->format('d/m/Y') }}</td>
                            <td>
                                <a href="{{ route('finance.show', $submission) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-cash"></i> Proses
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                Tidak ada pengajuan yang menunggu pembayaran.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $submissions->links() }}
    </div>
</x-app-layout>
