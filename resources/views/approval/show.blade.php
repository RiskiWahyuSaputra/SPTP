<x-app-layout>
    <div class="row g-3">
        <div class="col-lg-7">
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-file-text me-2 text-primary"></i>Detail Pengajuan</span>
                    @php
                        $m = ['draft'=>'secondary','submitted'=>'info','waiting_spv'=>'warning','waiting_manager'=>'warning','waiting_director'=>'warning','waiting_finance'=>'primary','paid'=>'success','rejected'=>'danger'];
                    @endphp
                    <span class="badge bg-{{ $m[$submission->current_status] ?? 'secondary' }} fs-6">
                        {{ str_replace('_', ' ', ucfirst($submission->current_status)) }}
                    </span>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr><th class="text-muted fw-normal" width="160">No. Pengajuan</th><td class="fw-semibold">{{ $submission->submission_number }}</td></tr>
                        <tr><th class="text-muted fw-normal">Pengaju</th><td>{{ $submission->user->name }}</td></tr>
                        <tr><th class="text-muted fw-normal">Tanggal</th><td>{{ $submission->submission_date->format('d/m/Y') }}</td></tr>
                        <tr><th class="text-muted fw-normal">Kategori</th><td>{{ $submission->category->name }}</td></tr>
                        <tr><th class="text-muted fw-normal">Nilai</th><td class="fw-bold">Rp {{ number_format($submission->amount, 0, ',', '.') }}</td></tr>
                        <tr><th class="text-muted fw-normal">Deskripsi</th><td>{{ $submission->description }}</td></tr>
                    </table>

                    @if ($submission->attachments->count() > 0)
                        <h6 class="fw-semibold mt-3 mb-2"><i class="bi bi-paperclip me-1"></i> Lampiran</h6>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach ($submission->attachments as $attachment)
                                <a href="{{ Storage::url($attachment->file_path) }}" target="_blank" class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-file-{{ in_array($attachment->file_type, ['jpg','jpeg','png']) ? 'image' : 'pdf' }} me-1"></i>
                                    {{ $attachment->file_name }}
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card shadow-sm mb-3">
                <div class="card-header">
                    <i class="bi bi-check2-circle me-2 text-primary"></i>Putusan Approval
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('approval.process', $submission) }}">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">Keputusan <span class="text-danger">*</span></label>
                            <div class="d-flex gap-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="decision" id="approve" value="approved" required style="border-color: var(--color-success);">
                                    <label class="form-check-label fw-semibold text-success" for="approve">
                                        <i class="bi bi-check-circle"></i> Setujui
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="decision" id="reject" value="rejected" required style="border-color: var(--color-danger);">
                                    <label class="form-check-label fw-semibold text-danger" for="reject">
                                        <i class="bi bi-x-circle"></i> Tolak
                                    </label>
                                </div>
                            </div>
                            @error('decision')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Catatan <span class="text-muted small fw-normal">(wajib jika menolak)</span></label>
                            <textarea id="notes" name="notes" class="form-control @error('notes') is-invalid @enderror" rows="4" placeholder="Tulis catatan...">{{ old('notes') }}</textarea>
                            @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="d-flex gap-2">
                            <a href="{{ route('approval.index') }}" class="btn btn-outline-secondary">Kembali</a>
                            <button type="submit" class="btn btn-primary flex-grow-1" data-confirm="Konfirmasi keputusan approval? Tindakan ini tidak bisa dibatalkan." data-confirm-title="Konfirmasi Approval">
                                <i class="bi bi-send me-1"></i> Kirim Keputusan
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header">
                    <i class="bi bi-timeline me-2 text-muted"></i>Timeline Approval
                </div>
                <div class="card-body p-0">
                    @forelse ($submission->approvals->sortBy('sequence') as $approval)
                        <div class="d-flex align-items-start gap-3 p-3 border-bottom">
                            <div class="d-flex flex-column align-items-center" style="width: 24px;">
                                <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-white" style="width: 24px; height: 24px; font-size: .65rem; background: {{ $approval->decision === 'approved' ? 'var(--color-success)' : ($approval->decision === 'rejected' ? 'var(--color-danger)' : 'var(--color-border)') }};">
                                    {{ $approval->sequence }}
                                </div>
                                @if(!$loop->last)
                                    <div style="width: 2px; height: 24px; background: var(--color-border);"></div>
                                @endif
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between">
                                    <strong class="small">{{ $approval->role->name }}</strong>
                                    @if($approval->decision === 'approved')
                                        <span class="badge bg-success-subtle text-success">Disetujui</span>
                                    @elseif($approval->decision === 'rejected')
                                        <span class="badge bg-danger-subtle text-danger">Ditolak</span>
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary">Menunggu</span>
                                    @endif
                                </div>
                                @if($approval->approver)
                                    <div class="small text-muted">{{ $approval->approver->name }}</div>
                                @endif
                                @if($approval->notes)
                                    <div class="small mt-1 text-muted">"{{ $approval->notes }}"</div>
                                @endif
                                @if($approval->decided_at)
                                    <div class="small text-muted mt-1">{{ $approval->decided_at->format('d/m/Y H:i') }}</div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="p-3 text-muted small">Belum ada riwayat approval.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
