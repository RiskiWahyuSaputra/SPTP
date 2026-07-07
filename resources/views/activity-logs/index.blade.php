<x-app-layout>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1" style="font-family: 'Lexend', sans-serif; color: var(--color-primary);">
                <i class="bi bi-clock-history me-2"></i>Activity Log
            </h4>
            <p class="text-muted small mb-0">Riwayat aktivitas sistem</p>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px;">
        <div class="card-body p-3">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-auto">
                    <select name="type" class="form-select form-select-sm" style="min-width: 180px;">
                        <option value="">Semua Tipe</option>
                        @foreach ($types as $t)
                            <option value="{{ $t }}" {{ request('type') === $t ? 'selected' : '' }}>
                                {{ str_replace('_', ' ', ucfirst($t)) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-auto">
                    <input type="date" name="date_from" class="form-control form-select-sm" value="{{ request('date_from') }}" placeholder="Dari">
                </div>
                <div class="col-auto">
                    <input type="date" name="date_to" class="form-control form-select-sm" value="{{ request('date_to') }}" placeholder="Sampai">
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-sm fw-semibold text-white px-3" style="background: var(--color-gold); border-color: var(--color-gold); border-radius: 8px;">
                        <i class="bi bi-funnel me-1"></i> Filter
                    </button>
                </div>
                <div class="col-auto">
                    <a href="{{ route('activity-logs.index') }}" class="btn btn-sm btn-outline-secondary px-3" style="border-radius: 8px;">
                        <i class="bi bi-x-circle me-1"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm" style="border-radius: 16px;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4 text-muted small fw-semibold">Waktu</th>
                            <th class="text-muted small fw-semibold">User</th>
                            <th class="text-muted small fw-semibold">Tipe</th>
                            <th class="text-muted small fw-semibold">Deskripsi</th>
                            <th class="text-end pe-4 text-muted small fw-semibold">Detail</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($logs as $log)
                            <tr>
                                <td class="ps-4 text-muted small">
                                    {{ $log->created_at->format('d/m/Y H:i') }}
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="d-flex align-items-center justify-content-center rounded-circle text-white fw-bold"
                                             style="width: 28px; height: 28px; font-size: 11px; background: var(--color-gold);">
                                            {{ $log->user ? substr($log->user->name, 0, 1) : 'S' }}
                                        </div>
                                        <span class="small">{{ $log->user?->name ?? 'System' }}</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge rounded-pill px-3 py-1 fw-normal
                                        @switch($log->type)
                                            @case('submission_created') bg-primary @break
                                            @case('submission_submitted') bg-info text-dark @break
                                            @case('approved') bg-success @break
                                            @case('rejected') bg-danger @break
                                            @case('payment_paid') bg-success @break
                                            @case('payment_rejected') bg-danger @break
                                            @default bg-secondary
                                        @endswitch">
                                        {{ str_replace('_', ' ', ucfirst($log->type)) }}
                                    </span>
                                </td>
                                <td class="small">{{ $log->description }}</td>
                                <td class="text-end pe-4">
                                    @if($log->new_data || $log->old_data)
                                        <button type="button" class="btn btn-sm btn-outline-gold rounded-pill px-3"
                                                data-bs-toggle="modal" data-bs-target="#logModal{{ $log->id }}">
                                            <i class="bi bi-eye me-1"></i> Data
                                        </button>

                                        <div class="modal fade" id="logModal{{ $log->id }}" tabindex="-1">
                                            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h6 class="modal-title fw-bold">Detail Perubahan</h6>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body text-start">
                                                        @if($log->old_data)
                                                            <h6 class="text-muted small fw-semibold mb-2">Data Lama</h6>
                                                            <pre class="bg-light p-3 rounded small" style="max-height: 200px; overflow: auto; font-size: 11px;">{{ json_encode($log->old_data, JSON_PRETTY_PRINT) }}</pre>
                                                        @endif
                                                        @if($log->new_data)
                                                            <h6 class="text-muted small fw-semibold mb-2 mt-3">Data Baru</h6>
                                                            <pre class="bg-light p-3 rounded small" style="max-height: 200px; overflow: auto; font-size: 11px;">{{ json_encode($log->new_data, JSON_PRETTY_PRINT) }}</pre>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-muted small">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">
                                    <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                                    Belum ada aktivitas.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($logs->hasPages())
            <div class="card-footer bg-transparent">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
