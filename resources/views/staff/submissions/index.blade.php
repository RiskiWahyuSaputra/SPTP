<x-app-layout>
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0">Daftar Pengajuan</h5>
        <a href="{{ route('staff.submissions.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Buat Pengajuan
        </a>
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
                        <th>Kategori</th>
                        <th>Tanggal</th>
                        <th>Nilai</th>
                        <th>Status</th>
                        <th width="120">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($submissions as $submission)
                        <tr>
                            <td>{{ $submission->submission_number }}</td>
                            <td>{{ $submission->category->name }}</td>
                            <td>{{ $submission->submission_date->format('d/m/Y') }}</td>
                            <td>Rp {{ number_format($submission->amount, 0, ',', '.') }}</td>
                            <td>
                                @php
                                    $badge = match($submission->current_status) {
                                        'draft' => 'secondary',
                                        'submitted' => 'info',
                                        'waiting_spv' => 'warning',
                                        'waiting_manager' => 'warning',
                                        'waiting_director' => 'warning',
                                        'waiting_finance' => 'primary',
                                        'paid' => 'success',
                                        'rejected' => 'danger',
                                        default => 'secondary'
                                    };
                                @endphp
                                <span class="badge bg-{{ $badge }}">
                                    {{ str_replace('_', ' ', ucfirst($submission->current_status)) }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('staff.submissions.show', $submission) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                Belum ada pengajuan. <a href="{{ route('staff.submissions.create') }}">Buat sekarang</a>.
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
