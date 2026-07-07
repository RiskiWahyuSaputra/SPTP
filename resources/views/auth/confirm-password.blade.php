<x-guest-layout>
    <h5 class="text-center fw-bold mb-1" style="color: var(--color-primary);">Konfirmasi Password</h5>
    <p class="text-center text-muted small mb-4">Area aman. Konfirmasi password untuk melanjutkan.</p>

    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf

        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <div class="input-group">
                <span class="input-group-text bg-light"><i class="bi bi-lock"></i></span>
                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password" placeholder="Masukkan password">
                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>

        <button type="submit" class="btn btn-primary w-100 py-2">
            <i class="bi bi-check-lg me-2"></i> Konfirmasi
        </button>
    </form>
</x-guest-layout>
