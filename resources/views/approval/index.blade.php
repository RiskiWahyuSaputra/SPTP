<x-app-layout>
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold mb-0" style="font-size: 1.1rem;">
            <i class="bi bi-inbox me-2 text-primary"></i>Daftar Pengajuan — 
            <span class="badge bg-{{ match($roleSlug) { 'spv' => 'warning', 'manager' => 'info', 'direktur' => 'danger', default => 'secondary' } }} ms-1">
                {{ ucfirst($roleSlug) }}
            </span>
        </h5>
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
                            <th>Status</th>
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
                                <td>
                                    @php
                                        $m = ['submitted'=>'info','waiting_spv'=>'warning','waiting_manager'=>'warning','waiting_director'=>'warning'];
                                    @endphp
                                    <span class="badge bg-{{ $m[$submission->current_status] ?? 'secondary' }}">
                                        {{ str_replace('_', ' ', ucfirst($submission->current_status)) }}
                                    </span>
                                </td>
                                <td class="text-muted small">{{ $submission->created_at->format('d/m/Y') }}</td>
                                <td class="text-end">
                                    <a href="/approval/detail/{{ $submission->id }}" class="btn btn-sm btn-primary">
                                        <i class="bi bi-check2-circle me-1"></i> Proses
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="bi bi-check2-all fs-1 d-block mb-2 text-success"></i>
                                    Tidak ada pengajuan yang menunggu approval Anda.
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
