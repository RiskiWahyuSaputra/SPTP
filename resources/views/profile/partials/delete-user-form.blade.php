<h5 class="card-title text-danger">Delete Account</h5>
<p class="text-muted small">Once your account is deleted, all of its resources and data will be permanently deleted.</p>

<form method="POST" action="{{ route('profile.destroy') }}">
    @csrf
    @method('delete')

    <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required placeholder="Enter your password to confirm">
        @error('password')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <button type="submit" class="btn btn-danger">Delete Account</button>
</form>
