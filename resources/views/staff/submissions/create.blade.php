<x-app-layout>
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header">
                    <i class="bi bi-plus-circle me-2 text-primary"></i>Buat Pengajuan Baru
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('staff.submissions.store') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="category_id" class="form-label">Kategori <span class="text-danger">*</span></label>
                                <select id="category_id" name="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                                    <option value="">-- Pilih --</option>
                                    @foreach ($categories as $cat)
                                        <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                                @error('category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6">
                                <label for="submission_date" class="form-label">Tanggal <span class="text-danger">*</span></label>
                                <input id="submission_date" type="date" name="submission_date" class="form-control @error('submission_date') is-invalid @enderror" value="{{ old('submission_date', date('Y-m-d')) }}" required>
                                @error('submission_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6">
                                <label for="amount" class="form-label">Nilai Pengajuan (Rp) <span class="text-danger">*</span></label>
                                <input id="amount" type="number" name="amount" class="form-control @error('amount') is-invalid @enderror" value="{{ old('amount') }}" min="1" step="0.01" required>
                                @error('amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-12">
                                <label for="description" class="form-label">Deskripsi <span class="text-danger">*</span></label>
                                <textarea id="description" name="description" class="form-control @error('description') is-invalid @enderror" rows="4" required>{{ old('description') }}</textarea>
                                @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-12">
                                <label for="attachments" class="form-label">Lampiran <span class="text-muted small fw-normal">(opsional, maks 5MB/file)</span></label>
                                <input id="attachments" type="file" name="attachments[]" class="form-control @error('attachments.*') is-invalid @enderror" multiple accept=".pdf,.jpg,.jpeg,.png">
                                <div class="form-text">Format: PDF, JPG, JPEG, PNG.</div>
                                @error('attachments.*')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('staff.submissions.index') }}" class="btn btn-outline-secondary">Batal</a>
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="bi bi-save me-1"></i> Simpan sebagai Draft
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
