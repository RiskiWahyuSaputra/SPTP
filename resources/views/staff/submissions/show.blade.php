<x-app-layout>
    <div class="row g-3">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-file-text me-2 text-primary"></i>Detail Pengajuan</span>
                    @php
                        $map = ['draft'=>'secondary','submitted'=>'info','waiting_spv'=>'warning','waiting_manager'=>'warning','waiting_director'=>'warning','waiting_finance'=>'primary','paid'=>'success','rejected'=>'danger'];
                    @endphp
                    <span class="badge bg-{{ $map[$submission->current_status] ?? 'secondary' }} fs-6">
                        {{ str_replace('_', ' ', ucfirst($submission->current_status)) }}
                    </span>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr><th class="text-muted fw-normal" width="180">No. Pengajuan</th><td class="fw-semibold">{{ $submission->submission_number }}</td></tr>
                        <tr><th class="text-muted fw-normal">Tanggal</th><td>{{ $submission->submission_date->format('d/m/Y') }}</td></tr>
                        <tr><th class="text-muted fw-normal">Kategori</th><td>{{ $submission->category->name }}</td></tr>
                        <tr><th class="text-muted fw-normal">Nilai</th><td class="fw-bold fs-5" style="color: var(--color-primary);">Rp {{ number_format($submission->amount, 0, ',', '.') }}</td></tr>
                        <tr><th class="text-muted fw-normal">Deskripsi</th><td>{{ $submission->description }}</td></tr>
                        @if ($submission->rejection_reason)
                            <tr><th class="text-muted fw-normal">Alasan Ditolak</th><td class="text-danger">{{ $submission->rejection_reason }}</td></tr>
                        @endif
                        <tr><th class="text-muted fw-normal">Dibuat</th><td class="text-muted small">{{ $submission->created_at->format('d/m/Y H:i') }}</td></tr>
                    </table>

                    @if ($submission->attachments->count() > 0)
                        <h6 class="fw-semibold mt-3 mb-2"><i class="bi bi-paperclip me-1"></i> Lampiran</h6>
                        <div class="row g-2">
                            @foreach ($submission->attachments as $attachment)
                                <div class="col-md-4 col-6">
                                    <div class="border rounded p-2 text-center bg-light">
                                        @if (in_array($attachment->file_type, ['jpg', 'jpeg', 'png']))
                                            <img src="{{ route('attachment.serve', $attachment->id) }}" class="img-fluid rounded mb-1" style="max-height: 100px; object-fit: cover;">
                                        @else
                                            <i class="bi bi-file-pdf fs-1 text-danger"></i>
                                        @endif
                                        <div class="small text-truncate mt-1">{{ $attachment->file_name }}</div>
                                        <a href="{{ route('attachment.serve', $attachment->id) }}" data-attachment data-attachment-name="{{ $attachment->file_name }}" data-attachment-type="{{ $attachment->file_type }}" class="btn btn-sm btn-outline-primary mt-1">
                                            <i class="bi bi-download"></i>
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            @if ($submission->current_status === 'draft')
                <div class="d-flex gap-2">
                    <a href="{{ route('staff.submissions.edit', $submission) }}" class="btn btn-warning">
                        <i class="bi bi-pencil me-1"></i> Edit
                    </a>
                    <form method="POST" action="{{ route('staff.submissions.submit', $submission) }}">
                        @csrf
                        <button type="submit" class="btn btn-success" data-confirm="Kirim pengajuan? Data tidak bisa diubah lagi." data-confirm-title="Kirim Pengajuan">
                            <i class="bi bi-send me-1"></i> Kirim Pengajuan
                        </button>
                    </form>
                    <form method="POST" action="{{ route('staff.submissions.destroy', $submission) }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger" data-confirm="Hapus pengajuan ini? Data akan dihapus permanen." data-confirm-title="Hapus Pengajuan">
                            <i class="bi bi-trash me-1"></i> Hapus
                        </button>
                    </form>
                </div>
            @endif
        </div>

        <div class="col-lg-4">
            @if ($submission->approvals->count() > 0)
                <div class="card shadow-sm">
                    <div class="card-header">
                        <i class="bi bi-timeline me-2 text-muted"></i>Timeline Approval
                    </div>
                    <div class="card-body p-0">
                        @foreach ($submission->approvals->sortBy('sequence') as $approval)
                            <div class="d-flex align-items-start gap-3 p-3 border-bottom">
                                <div class="d-flex flex-column align-items-center" style="width: 24px;">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-white" style="width: 24px; height: 24px; font-size: .65rem; background: {{ $approval->decision === 'approved' ? 'var(--color-success)' : ($approval->decision === 'rejected' ? 'var(--color-danger)' : 'var(--color-border)') }};">
                                        {{ $approval->sequence }}
                                    </div>
                                    @if(!$loop->last)<div style="width: 2px; height: 24px; background: var(--color-border);"></div>@endif
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
                                    @if($approval->approver)<div class="small text-muted">{{ $approval->approver->name }}</div>@endif
                                    @if($approval->notes)<div class="small mt-1 text-muted">"{{ $approval->notes }}"</div>@endif
                                    @if($approval->decided_at)<div class="small text-muted mt-1">{{ $approval->decided_at->format('d/m/Y H:i') }}</div>@endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
