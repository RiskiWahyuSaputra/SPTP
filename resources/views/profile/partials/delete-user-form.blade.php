<h5 class="fw-semibold mb-2 text-danger">Hapus Akun</h5>
<p class="text-muted small mb-4">Setelah akun dihapus, seluruh data akan terhapus permanen.</p>

<form method="POST" action="{{ route('profile.destroy') }}">
    @csrf
    @method('delete')

    <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required placeholder="Masukkan password untuk konfirmasi">
        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <button type="submit" class="btn btn-danger px-4">
        <i class="bi bi-trash me-1"></i> Hapus Akun
    </button>
</form>
