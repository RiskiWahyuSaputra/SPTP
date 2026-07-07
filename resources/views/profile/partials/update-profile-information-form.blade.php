<h5 class="fw-semibold mb-2">Informasi Profil</h5>
<p class="text-muted small mb-4">Perbarui informasi profil dan email akun Anda.</p>

<form method="POST" action="{{ route('profile.update') }}">
    @csrf
    @method('patch')

    <div class="mb-3">
        <label for="name" class="form-label">Nama</label>
        <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name">
        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email', $user->email) }}" required autocomplete="username">
        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="d-flex align-items-center gap-3">
        <button type="submit" class="btn btn-primary px-4">
            <i class="bi bi-save me-1"></i> Simpan
        </button>
        @if (session('status') === 'profile-updated')
            <span class="text-success small"><i class="bi bi-check-circle me-1"></i> Tersimpan.</span>
        @endif
    </div>
</form>
