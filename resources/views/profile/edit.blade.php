<x-app-layout>
    <div class="row g-3">
        <div class="col-lg-6">
            <div class="card shadow-sm">
                <div class="card-header"><i class="bi bi-person me-2 text-primary"></i>Informasi Profil</div>
                <div class="card-body">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card shadow-sm mb-3">
                <div class="card-header"><i class="bi bi-key me-2 text-warning"></i>Ubah Password</div>
                <div class="card-body">
                    @include('profile.partials.update-password-form')
                </div>
            </div>
            <div class="card shadow-sm border-danger">
                <div class="card-header text-danger"><i class="bi bi-exclamation-triangle me-2"></i>Hapus Akun</div>
                <div class="card-body">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
