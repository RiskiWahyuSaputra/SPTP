<x-app-layout>
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold mb-0" style="font-size: 1.1rem;">
            <i class="bi bi-file-earmark-text me-2 text-primary"></i>Daftar Pengajuan
        </h5>
        <a href="{{ route('staff.submissions.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i> Buat Pengajuan
        </a>
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
                            <th>Kategori</th>
                            <th>Tanggal</th>
                            <th>Nilai</th>
                            <th>Status</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($submissions as $submission)
                            <tr>
                                <td class="fw-semibold">{{ $submission->submission_number }}</td>
                                <td>{{ $submission->category->name }}</td>
                                <td class="text-muted small">{{ $submission->submission_date->format('d/m/Y') }}</td>
                                <td>Rp {{ number_format($submission->amount, 0, ',', '.') }}</td>
                                <td>
                                    @php
                                        $map = ['draft'=>'secondary','submitted'=>'info','waiting_spv'=>'warning','waiting_manager'=>'warning','waiting_director'=>'warning','waiting_finance'=>'primary','paid'=>'success','rejected'=>'danger'];
                                    @endphp
                                    <span class="badge bg-{{ $map[$submission->current_status] ?? 'secondary' }}">
                                        @switch($submission->current_status)
                                            @case('draft') <i class="bi bi-pencil me-1"></i> @break
                                            @case('submitted') <i class="bi bi-send me-1"></i> @break
                                            @case('waiting_spv') @case('waiting_manager') @case('waiting_director') <i class="bi bi-hourglass me-1"></i> @break
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
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
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

    <div class="mt-3">
        {{ $submissions->links() }}
    </div>
</x-app-layout>
