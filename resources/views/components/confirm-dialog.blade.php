<div class="modal fade" id="confirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 400px;">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
            <div class="modal-body text-center p-4">
                <div class="mb-3">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-warning-subtle" style="width: 56px; height: 56px;">
                        <i class="bi bi-exclamation-triangle fs-3 text-warning"></i>
                    </div>
                </div>
                <h6 class="fw-bold mb-2" id="confirmTitle" style="font-family: 'Lexend', sans-serif;">Konfirmasi</h6>
                <p class="text-muted small mb-4" id="confirmMessage">Apakah Anda yakin?</p>
                <div class="d-flex gap-2 justify-content-center">
                    <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn px-4" id="confirmSubmit" style="background: var(--color-gold); color: #fff; border-color: var(--color-gold);">Ya, Lanjutkan</button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const modal = document.getElementById('confirmModal');
        const titleEl = document.getElementById('confirmTitle');
        const msgEl = document.getElementById('confirmMessage');
        const submitBtn = document.getElementById('confirmSubmit');
        let confirmForm = null;

        document.querySelectorAll('[data-confirm]').forEach(el => {
            el.addEventListener('click', function (e) {
                e.preventDefault();
                confirmForm = this.closest('form') || null;
                const msg = this.getAttribute('data-confirm');
                const title = this.getAttribute('data-confirm-title') || 'Konfirmasi';
                titleEl.textContent = title;
                msgEl.textContent = msg;
                submitBtn.className = 'btn px-4';
                if (this.classList.contains('btn-danger') || this.classList.contains('text-danger')) {
                    submitBtn.className = 'btn btn-danger px-4';
                } else if (this.classList.contains('btn-success')) {
                    submitBtn.className = 'btn btn-success px-4';
                } else {
                    submitBtn.style.background = 'var(--color-gold)';
                    submitBtn.style.color = '#fff';
                    submitBtn.style.borderColor = 'var(--color-gold)';
                }
                const modalObj = new bootstrap.Modal(modal);
                modalObj.show();
            });
        });

        submitBtn?.addEventListener('click', function () {
            if (confirmForm) {
                confirmForm.submit();
            }
            bootstrap.Modal.getInstance(modal)?.hide();
        });
    });
</script>
@endpush
