<x-app-layout>
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header">
                    <i class="bi bi-pencil me-2 text-warning"></i>Edit Pengajuan
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('staff.submissions.update', $submission) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="category_id" class="form-label">Kategori <span class="text-danger">*</span></label>
                                <select id="category_id" name="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                                    <option value="">-- Pilih --</option>
                                    @foreach ($categories as $cat)
                                        <option value="{{ $cat->id }}" {{ old('category_id', $submission->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                                @error('category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label for="submission_date" class="form-label">Tanggal <span class="text-danger">*</span></label>
                                <input id="submission_date" type="date" name="submission_date" class="form-control @error('submission_date') is-invalid @enderror" value="{{ old('submission_date', $submission->submission_date->format('Y-m-d')) }}" required>
                                @error('submission_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label for="amount_display" class="form-label">Nilai (Rp) <span class="text-danger">*</span></label>
                                <input type="text" id="amount_display" class="form-control @error('amount') is-invalid @enderror" value="{{ old('amount') ? number_format(old('amount'), 0, ',', '.') : number_format($submission->amount, 0, ',', '.') }}" placeholder="Rp 0" required>
                                <input type="hidden" name="amount" id="amount" value="{{ old('amount', $submission->amount) }}">
                                @error('amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12">
                                <label for="description" class="form-label">Deskripsi <span class="text-danger">*</span></label>
                                <textarea id="description" name="description" class="form-control @error('description') is-invalid @enderror" rows="4" required>{{ old('description', $submission->description) }}</textarea>
                                @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label">Tambah Lampiran Baru <span class="text-muted small fw-normal">(opsional)</span></label>
                                <input type="file" name="attachments[]" class="form-control @error('attachments.*') is-invalid @enderror" multiple accept=".pdf,.jpg,.jpeg,.png">
                                @error('attachments.*')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            @if ($submission->attachments->count() > 0)
                                <div class="col-12">
                                    <label class="form-label">Lampiran Saat Ini</label>
                                    <ul class="list-group">
                                        @foreach ($submission->attachments as $attachment)
                                            <li class="list-group-item d-flex justify-content-between align-items-center py-2">
                                                <span><i class="bi bi-paperclip me-2"></i>{{ $attachment->file_name }}</span>
                                                <span class="badge bg-secondary">{{ $attachment->file_size }} KB</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('staff.submissions.show', $submission) }}" class="btn btn-outline-secondary">Batal</a>
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="bi bi-save me-1"></i> Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('amount_display')?.addEventListener('input', function() {
            let v = this.value.replace(/\./g, '').replace(/[^0-9]/g, '');
            this.value = v ? new Intl.NumberFormat('id-ID').format(v) : '';
            document.getElementById('amount').value = v;
        });
    </script>
</x-app-layout>
