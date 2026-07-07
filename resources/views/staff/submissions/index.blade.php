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

    {{-- Filter --}}
    <div class="card border-0 shadow-sm mb-3" style="border-radius: 12px;">
        <div class="card-body p-3">
            <form method="GET" class="row g-2 gx-3 align-items-end">
                <div class="col-6 col-md-3 col-lg-2">
                    <label class="form-label text-muted small fw-semibold mb-1" style="font-size: 11px;">Status</label>
                    <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">Semua</option>
                        @foreach (['draft'=>'Draft','submitted'=>'Diajukan','waiting_spv'=>'Menunggu SPV','waiting_manager'=>'Menunggu Manager','waiting_director'=>'Menunggu Direktur','waiting_finance'=>'Menunggu Finance','paid'=>'Dibayar','rejected'=>'Ditolak'] as $v => $l)
                            <option value="{{ $v }}" {{ request('status') === $v ? 'selected' : '' }}>{{ $l }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-3 col-lg-2">
                    <label class="form-label text-muted small fw-semibold mb-1" style="font-size: 11px;">Kategori</label>
                    <select name="category_id" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">Semua</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-3 col-lg-2">
                    <label class="form-label text-muted small fw-semibold mb-1" style="font-size: 11px;">Dari</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control form-control-sm">
                </div>
                <div class="col-6 col-md-3 col-lg-2">
                    <label class="form-label text-muted small fw-semibold mb-1" style="font-size: 11px;">Sampai</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-control form-control-sm">
                </div>
                <div class="col-8 col-md-6 col-lg-3">
                    <label class="form-label text-muted small fw-semibold mb-1" style="font-size: 11px;">Cari</label>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm" placeholder="No. pengajuan / deskripsi...">
                </div>
                <div class="col-4 col-md-3 col-lg-1">
                    <button type="submit" class="btn btn-sm text-white w-100" style="background: var(--color-gold); border-color: var(--color-gold);"><i class="bi bi-search"></i></button>
                </div>
            </form>
        </div>
    </div>

    {{-- Table --}}
    <div class="card border-0 shadow-sm" style="border-radius: 12px;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4 text-muted small fw-semibold">No. Pengajuan</th>
                            <th class="text-muted small fw-semibold">Kategori</th>
                            <th class="text-muted small fw-semibold">Tanggal</th>
                            <th class="text-muted small fw-semibold">Nilai</th>
                            <th class="text-muted small fw-semibold">Status</th>
                            <th class="text-end pe-4 text-muted small fw-semibold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($submissions as $submission)
                            <tr>
                                <td class="ps-4 fw-semibold small">{{ $submission->submission_number }}</td>
                                <td><span class="badge bg-gold-subtle text-gold fw-normal">{{ $submission->category->name }}</span></td>
                                <td class="text-muted small">{{ $submission->submission_date->format('d/m/Y') }}</td>
                                <td class="fw-semibold">Rp {{ number_format($submission->amount, 0, ',', '.') }}</td>
                                <td>
                                    @php
                                        $map = ['draft'=>'secondary','submitted'=>'info','waiting_spv'=>'warning','waiting_manager'=>'warning','waiting_director'=>'warning','waiting_finance'=>'primary','paid'=>'success','rejected'=>'danger'];
                                        $ico = ['draft'=>'bi-pencil','submitted'=>'bi-send','waiting_spv'=>'bi-hourglass','waiting_manager'=>'bi-hourglass','waiting_director'=>'bi-hourglass','waiting_finance'=>'bi-cash','paid'=>'bi-check-circle','rejected'=>'bi-x-circle'];
                                    @endphp
                                    <span class="badge bg-{{ $map[$submission->current_status] ?? 'secondary' }} rounded-pill px-3 py-1 fw-normal">
                                        <i class="bi {{ $ico[$submission->current_status] ?? 'bi-question' }} me-1"></i>
                                        {{ str_replace('_', ' ', ucfirst($submission->current_status)) }}
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
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                    @if(request()->anyFilled(['status', 'category_id', 'date_from', 'date_to', 'search']))
                                        Tidak ada pengajuan yang sesuai filter.
                                        <a href="{{ route('staff.submissions.index') }}">Reset filter</a>.
                                    @else
                                        Belum ada pengajuan.
                                        <a href="{{ route('staff.submissions.create') }}">Buat sekarang</a>.
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center mt-3 px-1">
        <small class="text-muted">
            Menampilkan {{ $submissions->firstItem() ?? 0 }}–{{ $submissions->lastItem() ?? 0 }} dari {{ $submissions->total() }} pengajuan
        </small>
        <div>
            {{ $submissions->links() }}
        </div>
    </div>
</x-app-layout>
