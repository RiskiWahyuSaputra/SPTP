<x-app-layout>
    <div class="row">
        <div class="col-lg-7">
            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Detail Pengajuan</h5>
                    <span class="badge bg-primary fs-6">Waiting Finance</span>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <th width="160">No. Pengajuan</th>
                            <td>{{ $submission->submission_number }}</td>
                        </tr>
                        <tr>
                            <th>Pengaju</th>
                            <td>{{ $submission->user->name }}</td>
                        </tr>
                        <tr>
                            <th>Kategori</th>
                            <td>{{ $submission->category->name }}</td>
                        </tr>
                        <tr>
                            <th>Nilai</th>
                            <td>Rp {{ number_format($submission->amount, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <th>Deskripsi</th>
                            <td>{{ $submission->description }}</td>
                        </tr>
                    </table>

                    @if ($submission->attachments->count() > 0)
                        <h6 class="mt-3">Lampiran</h6>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach ($submission->attachments as $attachment)
                                <a href="{{ Storage::url($attachment->file_path) }}" target="_blank" class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-paperclip"></i> {{ $attachment->file_name }}
                                </a>
                            @endforeach
                        </div>
                    @endif

                    <h6 class="mt-3">Riwayat Approval</h6>
                    <ul class="list-group">
                        @foreach ($submission->approvals->sortBy('sequence') as $approval)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>{{ $approval->role->name }}</strong>
                                    @if ($approval->approver)
                                        <div class="small text-muted">{{ $approval->approver->name }}</div>
                                    @endif
                                    @if ($approval->notes)
                                        <div class="small mt-1">{{ $approval->notes }}</div>
                                    @endif
                                </div>
                                @if ($approval->decision === 'approved')
                                    <span class="badge bg-success">Disetujui</span>
                                @else
                                    <span class="badge bg-secondary">{{ ucfirst($approval->decision) }}</span>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="mb-0">Proses Pembayaran</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <strong>Saldo Kas Tersedia:</strong>
                        <span class="fs-5 {{ $cashBalance && $cashBalance->balance >= $submission->amount ? 'text-success' : 'text-danger' }}">
                            Rp {{ number_format($cashBalance?->balance ?? 0, 0, ',', '.') }}
                        </span>
                        @if ($cashBalance && $cashBalance->balance < $submission->amount)
                            <div class="small mt-1 text-danger">
                                <i class="bi bi-exclamation-triangle"></i> Saldo tidak mencukupi!
                            </div>
                        @endif
                    </div>

                    <form method="POST" action="{{ route('finance.process', $submission) }}">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">Keputusan <span class="text-danger">*</span></label>
                            <div class="d-flex gap-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="decision" id="decision_paid" value="paid"
                                        {{ $cashBalance && $cashBalance->balance < $submission->amount ? 'disabled' : '' }} required>
                                    <label class="form-check-label text-success fw-semibold" for="decision_paid">
                                        <i class="bi bi-check-circle"></i> Bayar
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="decision" id="decision_rejected" value="rejected" required>
                                    <label class="form-check-label text-danger fw-semibold" for="decision_rejected">
                                        <i class="bi bi-x-circle"></i> Tolak
                                    </label>
                                </div>
                            </div>
                            @error('decision')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Catatan</label>
                            <textarea id="notes" name="notes" class="form-control @error('notes') is-invalid @enderror" rows="4" placeholder="Catatan (opsional)">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex gap-2">
                            <a href="{{ route('finance.index') }}" class="btn btn-secondary">Kembali</a>
                            <button type="submit" class="btn btn-primary" onclick="return confirm('Konfirmasi keputusan pembayaran?')">
                                <i class="bi bi-send"></i> Proses
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
