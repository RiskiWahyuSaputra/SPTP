<x-app-layout>
    <h5 class="mb-3">
        Daftar Pengajuan — 
        <span class="badge bg-{{ match($roleSlug) {
            'spv' => 'info', 'manager' => 'primary', 'direktur' => 'danger', default => 'secondary'
        } }}">
            {{ ucfirst($roleSlug) }}
        </span>
    </h5>

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
                        <th>Status</th>
                        <th>Tgl. Diajukan</th>
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
                            <td>
                                @php
                                    $badge = match($submission->current_status) {
                                        'submitted' => 'info',
                                        'waiting_spv' => 'warning',
                                        'waiting_manager' => 'warning',
                                        'waiting_director' => 'warning',
                                        default => 'secondary'
                                    };
                                @endphp
                                <span class="badge bg-{{ $badge }}">
                                    {{ str_replace('_', ' ', ucfirst($submission->current_status)) }}
                                </span>
                            </td>
                            <td>{{ $submission->created_at->format('d/m/Y') }}</td>
                            <td>
                                <a href="{{ route('approval.show', $submission) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye"></i> Proses
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                Tidak ada pengajuan yang menunggu approval Anda.
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
