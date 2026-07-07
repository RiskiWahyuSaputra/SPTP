<x-app-layout>
    <div class="row g-3">
        <div class="col-lg-7">
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-file-text me-2 text-primary"></i>Detail Pengajuan</span>
                    <span class="badge bg-primary fs-6"><i class="bi bi-cash me-1"></i> Waiting Finance</span>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr><th class="text-muted fw-normal" width="160">No. Pengajuan</th><td class="fw-semibold">{{ $submission->submission_number }}</td></tr>
                        <tr><th class="text-muted fw-normal">Pengaju</th><td>{{ $submission->user->name }}</td></tr>
                        <tr><th class="text-muted fw-normal">Kategori</th><td>{{ $submission->category->name }}</td></tr>
                        <tr><th class="text-muted fw-normal">Nilai</th><td class="fw-bold fs-5" style="color: var(--color-primary);">Rp {{ number_format($submission->amount, 0, ',', '.') }}</td></tr>
                        <tr><th class="text-muted fw-normal">Deskripsi</th><td>{{ $submission->description }}</td></tr>
                    </table>

                    @if ($submission->attachments->count() > 0)
                        <h6 class="fw-semibold mt-3 mb-2"><i class="bi bi-paperclip me-1"></i> Lampiran</h6>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach ($submission->attachments as $attachment)
                                <a href="{{ route('attachment.serve', $attachment->id) }}" data-attachment data-attachment-name="{{ $attachment->file_name }}" data-attachment-type="{{ $attachment->file_type }}" class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-file-{{ in_array($attachment->file_type, ['jpg','jpeg','png']) ? 'image' : 'pdf' }} me-1"></i>
                                    {{ $attachment->file_name }}
                                </a>
                            @endforeach
                        </div>
                    @endif

                    <h6 class="fw-semibold mt-3 mb-2"><i class="bi bi-check2-all me-1 text-success"></i> Riwayat Approval</h6>
                    <ul class="list-group">
                        @foreach ($submission->approvals->sortBy('sequence') as $approval)
                            <li class="list-group-item d-flex justify-content-between align-items-center py-2">
                                <div>
                                    <strong class="small">{{ $approval->role->name }}</strong>
                                    @if ($approval->approver)<div class="small text-muted">{{ $approval->approver->name }}</div>@endif
                                    @if ($approval->notes)<div class="small mt-1 text-muted">"{{ $approval->notes }}"</div>@endif
                                </div>
                                <span class="badge bg-success-subtle text-success">Disetujui</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card shadow-sm">
                <div class="card-header">
                    <i class="bi bi-cash-coin me-2 text-success"></i>Proses Pembayaran
                </div>
                <div class="card-body">
                    <div class="alert @if($cashBalance && $cashBalance->balance >= $submission->amount) alert-info @else alert-danger @endif d-flex align-items-center gap-2">
                        <i class="bi bi-wallet2 fs-4"></i>
                        <div>
                            <strong>Saldo Kas:</strong>
                            <span class="fw-bold">Rp {{ number_format($cashBalance?->balance ?? 0, 0, ',', '.') }}</span>
                            @if($cashBalance && $cashBalance->balance < $submission->amount)
                                <div class="small mt-1"><i class="bi bi-exclamation-triangle me-1"></i> Saldo tidak mencukupi!</div>
                            @endif
                        </div>
                    </div>

                    <form method="POST" action="{{ route('finance.process', $submission) }}">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">Keputusan <span class="text-danger">*</span></label>
                            <div class="d-flex gap-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="decision" id="paid" value="paid" {{ $cashBalance && $cashBalance->balance < $submission->amount ? 'disabled' : '' }} required style="border-color: var(--color-success);">
                                    <label class="form-check-label fw-semibold text-success" for="paid">
                                        <i class="bi bi-check-circle"></i> Bayar
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="decision" id="rejected" value="rejected" required style="border-color: var(--color-danger);">
                                    <label class="form-check-label fw-semibold text-danger" for="rejected">
                                        <i class="bi bi-x-circle"></i> Tolak
                                    </label>
                                </div>
                            </div>
                            @error('decision')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Catatan</label>
                            <textarea id="notes" name="notes" class="form-control @error('notes') is-invalid @enderror" rows="3" placeholder="Catatan (opsional)">{{ old('notes') }}</textarea>
                            @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="d-flex gap-2">
                            <a href="{{ route('finance.index') }}" class="btn btn-outline-secondary">Kembali</a>
                            <button type="submit" class="btn btn-success flex-grow-1" data-confirm="Konfirmasi keputusan pembayaran? Tindakan ini tidak bisa dibatalkan." data-confirm-title="Konfirmasi Pembayaran">
                                <i class="bi bi-send me-1"></i> Proses
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
