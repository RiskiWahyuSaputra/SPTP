<x-app-layout>
    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Detail Pengajuan</h5>
                    <span class="badge bg-{{ match($submission->current_status) {
                        'draft' => 'secondary', 'submitted' => 'info', 'waiting_spv' => 'warning',
                        'waiting_manager' => 'warning', 'waiting_director' => 'warning',
                        'waiting_finance' => 'primary', 'paid' => 'success', 'rejected' => 'danger',
                        default => 'secondary'
                    } }} fs-6">
                        {{ str_replace('_', ' ', ucfirst($submission->current_status)) }}
                    </span>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <th width="180">No. Pengajuan</th>
                            <td>{{ $submission->submission_number }}</td>
                        </tr>
                        <tr>
                            <th>Tanggal</th>
                            <td>{{ $submission->submission_date->format('d/m/Y') }}</td>
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
                        @if ($submission->rejection_reason)
                            <tr>
                                <th>Alasan Ditolak</th>
                                <td class="text-danger">{{ $submission->rejection_reason }}</td>
                            </tr>
                        @endif
                        <tr>
                            <th>Dibuat</th>
                            <td>{{ $submission->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            @if ($submission->attachments->count() > 0)
                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="mb-0">Lampiran ({{ $submission->attachments->count() }})</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-2">
                            @foreach ($submission->attachments as $attachment)
                                <div class="col-md-4">
                                    <div class="border rounded p-2 text-center">
                                        @if (in_array($attachment->file_type, ['jpg', 'jpeg', 'png']))
                                            <img src="{{ Storage::url($attachment->file_path) }}" class="img-fluid rounded mb-1" style="max-height: 120px;">
                                        @else
                                            <i class="bi bi-file-pdf fs-1 text-danger"></i>
                                        @endif
                                        <div class="small text-truncate">{{ $attachment->file_name }}</div>
                                        <a href="{{ Storage::url($attachment->file_path) }}" class="btn btn-sm btn-outline-primary" target="_blank">
                                            <i class="bi bi-download"></i>
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            @if ($submission->current_status === 'draft')
                <div class="d-flex gap-2 mb-3">
                    <a href="{{ route('staff.submissions.edit', $submission) }}" class="btn btn-warning">Edit</a>
                    <form method="POST" action="{{ route('staff.submissions.submit', $submission) }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-success"
                            onclick="return confirm('Kirim pengajuan ini? Data tidak bisa diedit setelah dikirim.')">
                            Kirim Pengajuan
                        </button>
                    </form>
                    <form method="POST" action="{{ route('staff.submissions.destroy', $submission) }}" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" onclick="return confirm('Hapus pengajuan ini?')">Hapus</button>
                    </form>
                </div>
            @endif
        </div>

        <div class="col-lg-4">
            @if ($submission->approvals->count() > 0)
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Timeline Approval</h5>
                    </div>
                    <div class="card-body p-0">
                        <ul class="list-group list-group-flush">
                            @foreach ($submission->approvals->sortBy('sequence') as $approval)
                                <li class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <strong>{{ $approval->role->name }}</strong>
                                        @if ($approval->decision === 'approved')
                                            <span class="badge bg-success">Disetujui</span>
                                        @elseif ($approval->decision === 'rejected')
                                            <span class="badge bg-danger">Ditolak</span>
                                        @else
                                            <span class="badge bg-secondary">Menunggu</span>
                                        @endif
                                    </div>
                                    @if ($approval->approver)
                                        <div class="small text-muted">{{ $approval->approver->name }}</div>
                                    @endif
                                    @if ($approval->notes)
                                        <div class="small mt-1">{{ $approval->notes }}</div>
                                    @endif
                                    @if ($approval->decided_at)
                                        <div class="small text-muted">{{ $approval->decided_at->format('d/m/Y H:i') }}</div>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
