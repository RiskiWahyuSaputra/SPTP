<x-guest-layout>
    <h5 class="text-center fw-bold mb-1" style="color: var(--color-primary);">Lupa Password</h5>
    <p class="text-center text-muted small mb-4">Masukkan email untuk menerima tautan reset</p>

    <x-auth-session-status class="mb-3" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <div class="input-group">
                <span class="input-group-text bg-light"><i class="bi bi-envelope"></i></span>
                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autofocus placeholder="nama@perusahaan.com">
                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>

        <button type="submit" class="btn btn-primary w-100 py-2">
            <i class="bi bi-send me-2"></i> Kirim Tautan Reset
        </button>

        <div class="text-center mt-3">
            <a href="{{ route('login') }}" class="text-decoration-none small">
                <i class="bi bi-arrow-left me-1"></i> Kembali ke Login
            </a>
        </div>
    </form>
</x-guest-layout>
