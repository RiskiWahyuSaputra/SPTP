<x-guest-layout>
    <h5 class="text-center fw-bold mb-1" style="color: var(--color-primary);">Masuk</h5>
    <p class="text-center text-muted small mb-4">Masuk ke akun Anda</p>

    <x-auth-session-status class="mb-3" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <div class="input-group">
                <span class="input-group-text bg-light"><i class="bi bi-envelope"></i></span>
                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="nama@perusahaan.com">
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <div class="input-group">
                <span class="input-group-text bg-light"><i class="bi bi-lock"></i></span>
                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password" placeholder="&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;">
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="mb-3 form-check">
            <input type="checkbox" class="form-check-input" id="remember_me" name="remember">
            <label class="form-check-label small" for="remember_me">Ingat saya</label>
        </div>

        <button type="submit" class="btn btn-primary w-100 py-2 mb-3">
            <i class="bi bi-box-arrow-in-right me-2"></i> Masuk
        </button>

        @if (Route::has('password.request'))
            <div class="text-center">
                <a class="text-decoration-none small" href="{{ route('password.request') }}">
                    Lupa password?
                </a>
            </div>
        @endif
    </form>
</x-guest-layout>
