<h5 class="fw-semibold mb-2">Ubah Password</h5>
<p class="text-muted small mb-4">Gunakan password yang panjang dan acak untuk keamanan.</p>

<form method="POST" action="{{ route('password.update') }}">
    @csrf
    @method('put')

    <div class="mb-3">
        <label for="current_password" class="form-label">Password Saat Ini</label>
        <input id="current_password" type="password" class="form-control @error('current_password') is-invalid @enderror" name="current_password" required autocomplete="current-password">
        @error('current_password')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="mb-3">
        <label for="password" class="form-label">Password Baru</label>
        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">
        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="mb-3">
        <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
        <input id="password_confirmation" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
    </div>

    <div class="d-flex align-items-center gap-3">
        <button type="submit" class="btn btn-primary px-4">
            <i class="bi bi-save me-1"></i> Simpan
        </button>
        @if (session('status') === 'password-updated')
            <span class="text-success small"><i class="bi bi-check-circle me-1"></i> Tersimpan.</span>
        @endif
    </div>
</form>
