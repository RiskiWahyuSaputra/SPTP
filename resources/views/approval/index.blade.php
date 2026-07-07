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

    {{-- Filter --}}
    <div class="card border-0 shadow-sm mb-3" style="border-radius: 12px;">
        <div class="card-body p-3">
            <form method="GET" class="row g-2 gx-3 align-items-end">
                <div class="col-6 col-md-3 col-lg-2">
                    <label class="form-label text-muted small fw-semibold mb-1" style="font-size: 11px;">Status</label>
                    <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Menunggu</option>
                        <option value="approved" {{ $status === 'approved' ? 'selected' : '' }}>Disetujui</option>
                        <option value="rejected" {{ $status === 'rejected' ? 'selected' : '' }}>Ditolak</option>
                        <option value="all" {{ $status === 'all' ? 'selected' : '' }}>Semua</option>
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
                            <th class="text-muted small fw-semibold">Pengaju</th>
                            <th class="text-muted small fw-semibold">Kategori</th>
                            <th class="text-muted small fw-semibold">Nilai</th>
                            <th class="text-muted small fw-semibold">Status</th>
                            <th class="text-muted small fw-semibold">Tanggal</th>
                            <th class="text-end pe-4 text-muted small fw-semibold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($submissions as $submission)
                            <tr>
                                <td class="ps-4 fw-semibold small">{{ $submission->submission_number }}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="d-flex align-items-center justify-content-center rounded-circle text-white fw-bold" style="width: 30px; height: 30px; font-size: 11px; background: var(--color-gold);">
                                            {{ substr($submission->user->name, 0, 1) }}
                                        </div>
                                        <span class="small">{{ $submission->user->name }}</span>
                                    </div>
                                </td>
                                <td><span class="badge bg-gold-subtle text-gold fw-normal">{{ $submission->category->name }}</span></td>
                                <td class="fw-semibold">Rp {{ number_format($submission->amount, 0, ',', '.') }}</td>
                                <td>
                                    @php
                                        $m = ['submitted'=>'info','waiting_spv'=>'warning','waiting_manager'=>'warning','waiting_director'=>'warning','waiting_finance'=>'primary','paid'=>'success','rejected'=>'danger','draft'=>'secondary'];
                                        $ico = ['submitted'=>'bi-send','waiting_spv'=>'bi-hourglass','waiting_manager'=>'bi-hourglass','waiting_director'=>'bi-hourglass','waiting_finance'=>'bi-cash','paid'=>'bi-check-circle','rejected'=>'bi-x-circle','draft'=>'bi-pencil'];
                                    @endphp
                                    <span class="badge bg-{{ $m[$submission->current_status] ?? 'secondary' }} rounded-pill px-3 py-1 fw-normal">
                                        <i class="bi {{ $ico[$submission->current_status] ?? 'bi-question' }} me-1"></i>
                                        {{ str_replace('_', ' ', ucfirst($submission->current_status)) }}
                                    </span>
                                </td>
                                <td class="text-muted small">{{ $submission->created_at->format('d/m/Y') }}</td>
                                <td class="text-end pe-4">
                                    <a href="/approval/detail/{{ $submission->id }}" class="btn btn-sm text-white fw-semibold px-3 rounded-pill" style="background: var(--color-gold); border-color: var(--color-gold);">
                                        <i class="bi bi-check2-circle me-1"></i> Proses
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    @if($status === 'pending')
                                        <i class="bi bi-check2-all fs-1 d-block mb-2 text-success"></i>
                                        Tidak ada pengajuan yang menunggu approval Anda.
                                    @else
                                        <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                        Tidak ada pengajuan ditemukan.
                                    @endif
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
