<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<?php
    $roleLabel = (($user['role'] ?? '') === 'mitra') ? 'Mitra Kerja' : 'Anggota';
    $currentEmail = (string) ($user['email'] ?? '');
    $currentName  = (string) ($user['name'] ?? '');
    if ($currentName === '' && $currentEmail !== '') {
        $currentName = $currentEmail;
    }
?>

<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4 mt-2">
    <div>
        <h3 class="fw-bold mb-1" style="color: var(--inkindo-blue);">Profil Akun</h3>
        <p class="text-muted small mb-0">Kelola informasi nama, email, keamanan password, dan pengaturan akun Anda.</p>
    </div>
    <span class="badge rounded-pill px-3 py-2 fw-semibold" style="background: #eff6ff; color: var(--inkindo-blue); border: 1px solid #bfdbfe;">
        <i class="bi bi-person-badge me-1"></i> Akun <?= esc($roleLabel); ?>
    </span>
</div>

<?php if (session()->getFlashdata('pesan')): ?>
    <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i><?= esc(session()->getFlashdata('pesan')); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i><?= esc(session()->getFlashdata('error')); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="row g-4">
    <!-- Informasi Profil -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body p-4">
                <div class="d-flex align-items-center gap-3 mb-4 pb-3 border-bottom">
                    <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 48px; height: 48px; background: #eff6ff; color: var(--inkindo-blue);">
                        <i class="bi bi-person-lines-fill fs-4"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-0">Informasi Profil</h5>
                        <small class="text-muted">Perbarui nama tampilan dan alamat email Anda</small>
                    </div>
                </div>

                <form action="/profile/update" method="post">
<?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-muted">Username</label>
                        <input type="text" class="form-control bg-light" value="<?= esc((string) ($user['username'] ?? '')); ?>" readonly>
                    </div>

                    <div class="mb-3">
                        <label for="profile_name" class="form-label fw-semibold small">Nama User</label>
                        <input type="text" id="profile_name" name="name" class="form-control" value="<?= esc($currentName); ?>" placeholder="Masukkan nama lengkap / nama tampilan">
                        <div class="form-text">Jika dikosongkan, nama user otomatis menggunakan alamat email Anda.</div>
                    </div>

                    <div class="mb-4">
                        <label for="profile_email" class="form-label fw-semibold small text-muted">Email</label>
                        <div class="input-group" style="cursor: not-allowed;">
                            <span class="input-group-text" style="background-color: #cbd5e1; border-color: #cbd5e1; color: #475569;">
                                <i class="bi bi-lock-fill"></i>
                            </span>
                            <input type="email" id="profile_email" class="form-control" value="<?= esc($currentEmail); ?>" disabled tabindex="-1" aria-disabled="true" style="background-color: #e2e8f0; color: #475569; border-color: #cbd5e1; pointer-events: none; user-select: none;">
                        </div>
                        <div class="form-text">Email yang sudah terdaftar bersifat permanen dan tidak dapat diubah.</div>
                    </div>

                    <button type="submit" class="btn btn-inkindo px-4 py-2 rounded-pill fw-semibold">
                        <i class="bi bi-save me-1"></i> Simpan Profil
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Ganti Password -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body p-4">
                <div class="d-flex align-items-center gap-3 mb-3 pb-3 border-bottom">
                    <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 48px; height: 48px; background: #f0fdf4; color: #16a34a;">
                        <i class="bi bi-shield-lock-fill fs-4"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-0">Ganti Password</h5>
                        <small class="text-muted">Memerlukan password lama &amp; verifikasi kode OTP email</small>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-2 px-3 py-2 rounded-3 mb-3 small" style="background: #eff6ff; color: #1e40af; border: 1px solid #bfdbfe;">
                    <i class="bi bi-envelope-check-fill flex-shrink-0"></i>
                    <span>Kode OTP akan dikirim ke: <strong><?= esc($maskedEmail ?? '***'); ?></strong></span>
                </div>

                <div id="profileOtpAlert" class="alert py-2 px-3 small rounded-3 mb-3" style="display: none;"></div>

                <form action="/profile/password" method="post">
<?= csrf_field() ?>
                    <div class="mb-3">
                        <label for="current_password" class="form-label fw-semibold small">Password Lama <span class="text-danger">*</span></label>
                        <input type="password" id="current_password" name="current_password" class="form-control" placeholder="Masukkan password saat ini" required>
                    </div>

                    <div class="mb-3">
                        <label for="new_password" class="form-label fw-semibold small">Password Baru <span class="text-danger">*</span></label>
                        <input type="password" id="new_password" name="new_password" class="form-control" placeholder="Minimal 8 karakter" minlength="8" required>
                    </div>

                    <div class="mb-3">
                        <label for="confirm_password" class="form-label fw-semibold small">Konfirmasi Password Baru <span class="text-danger">*</span></label>
                        <input type="password" id="confirm_password" name="confirm_password" class="form-control" placeholder="Ulangi password baru" minlength="8" required>
                        <div id="profilePassMatchMsg" class="small fw-semibold mt-1" style="min-height: 18px;"></div>
                    </div>

                    <div class="mb-4">
                        <label for="profile_otp_code" class="form-label fw-semibold small">Kode OTP Email (6 Digit) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="text" id="profile_otp_code" name="otp_code" class="form-control fw-bold text-center" placeholder="6 digit OTP" maxlength="6" inputmode="numeric" pattern="[0-9]{6}" autocomplete="one-time-code" style="letter-spacing: 5px; font-family: monospace;" required>
                            <button type="button" id="profileSendOtpBtn" class="btn btn-outline-primary fw-semibold px-3" onclick="sendProfilePasswordOtp()">
                                <i class="bi bi-send me-1"></i> Kirim OTP
                            </button>
                        </div>
                        <div class="form-text">Klik <strong>Kirim OTP</strong> terlebih dahulu, lalu masukkan 6 digit kode dari email Anda.</div>
                    </div>

                    <button type="submit" class="btn btn-success px-4 py-2 rounded-pill fw-semibold">
                        <i class="bi bi-key-fill me-1"></i> Verifikasi &amp; Perbarui Password
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Hapus Akun -->
    <div class="col-12">
        <div class="card border border-danger-subtle shadow-sm rounded-4" style="background: #fffafa;">
            <div class="card-body p-4 d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div>
                    <h5 class="fw-bold text-danger mb-1">
                        <i class="bi bi-exclamation-octagon-fill me-1"></i> Hapus Akun Permanen
                    </h5>
                    <p class="text-muted small mb-0">
                        Menghapus akun akan menghapus seluruh data Perusahaan, SBU, dan profil Mitra Kerja yang terkait secara permanen dan tidak dapat dibatalkan.
                    </p>
                </div>
                <button type="button" class="btn btn-outline-danger rounded-pill px-4 py-2 fw-semibold" data-bs-toggle="modal" data-bs-target="#deleteAccountModal">
                    <i class="bi bi-trash3-fill me-1"></i> Hapus Akun
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Centered Pop-up Modal Konfirmasi Hapus Akun -->
<div class="modal fade" id="deleteAccountModal" tabindex="-1" aria-labelledby="deleteAccountModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-body p-4 text-center">
                <div class="mx-auto mb-3 d-flex align-items-center justify-content-center rounded-circle" style="width: 68px; height: 68px; background: #fef2f2; color: #dc2626;">
                    <i class="bi bi-exclamation-triangle-fill fs-2"></i>
                </div>
                <h5 class="fw-bold text-dark mb-2" id="deleteAccountModalLabel">Yakin Ingin Menghapus Akun?</h5>
                <p class="text-muted small mb-4">
                    Tindakan ini bersifat <strong>permanen</strong>. Seluruh data perusahaan, SBU, dan kemitraan yang terhubung dengan akun <strong><?= esc((string) ($user['username'] ?? '')); ?></strong> akan ikut terhapus dan Anda akan langsung keluar dari sistem.
                </p>
                <div class="d-flex justify-content-center gap-2">
                    <button type="button" class="btn btn-light border rounded-pill px-4 py-2 fw-semibold" data-bs-dismiss="modal">Batal</button>
                    <form action="/profile/delete" method="post" class="m-0">
<?= csrf_field() ?>
                        <button type="submit" class="btn btn-danger rounded-pill px-4 py-2 fw-semibold">
                            <i class="bi bi-trash3 me-1"></i> Ya, Hapus Akun
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
(function () {
    let profileOtpTimer = null;
    const p1 = document.getElementById('new_password');
    const p2 = document.getElementById('confirm_password');
    const pMsg = document.getElementById('profilePassMatchMsg');
    const otpInp = document.getElementById('profile_otp_code');

    function checkMatch() {
        if (!p1 || !p2 || !pMsg) return true;
        if (!p2.value) { pMsg.textContent = ''; return true; }
        if (p1.value !== p2.value) {
            pMsg.textContent = 'Password baru tidak sama';
            pMsg.style.color = '#dc2626';
            return false;
        }
        pMsg.textContent = 'Password baru sesuai';
        pMsg.style.color = '#16a34a';
        return true;
    }
    if (p1) p1.addEventListener('input', checkMatch);
    if (p2) p2.addEventListener('input', checkMatch);
    if (otpInp) {
        otpInp.addEventListener('input', function () {
            this.value = this.value.replace(/\D+/g, '').slice(0, 6);
        });
    }

    window.sendProfilePasswordOtp = async function () {
        const btn = document.getElementById('profileSendOtpBtn');
        const alertEl = document.getElementById('profileOtpAlert');
        const devEl = document.getElementById('profileDevOtpBox');
        const csrfNameMeta = document.querySelector('meta[name="csrf-token-name"]');
        const csrfValMeta = document.querySelector('meta[name="csrf-token-value"]');
        const tokenName = csrfNameMeta ? csrfNameMeta.getAttribute('content') : '';
        const tokenVal = csrfValMeta ? csrfValMeta.getAttribute('content') : '';

        if (alertEl) alertEl.style.display = 'none';
        if (devEl) devEl.style.display = 'none';

        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i> Mengirim...';
        }

        const fd = new FormData();
        if (tokenName && tokenVal) fd.append(tokenName, tokenVal);

        let resData = {};
        try {
            const resp = await fetch('/profile/password/send-otp', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': tokenVal
                },
                body: fd,
                credentials: 'same-origin'
            });
            resData = await resp.json();
        } catch (err) {
            resData = { ok: false, message: 'Gagal terhubung ke server. Silakan coba lagi.' };
        }

        if (resData.csrf_hash && csrfValMeta) {
            csrfValMeta.setAttribute('content', resData.csrf_hash);
            if (tokenName) {
                document.querySelectorAll('input[name="' + tokenName + '"]').forEach((i) => { i.value = resData.csrf_hash; });
            }
        }

        if (alertEl) {
            alertEl.className = 'alert py-2 px-3 small rounded-3 mb-3 ' + (resData.ok ? 'alert-success' : 'alert-danger');
            alertEl.textContent = resData.message || 'Gagal mengirim OTP.';
            alertEl.style.display = 'block';
        }

        if (resData.ok && resData.dev_otp && devEl) {
            devEl.innerHTML = 'Mode Lokal (SMTP belum aktif) — Kode OTP: <strong style="font-size:14px; letter-spacing:2px;">' + resData.dev_otp + '</strong>';
            devEl.style.display = 'block';
        }

        if (resData.ok || resData.cooldown) {
            let left = Number(resData.cooldown || 60);
            if (profileOtpTimer) clearInterval(profileOtpTimer);
            if (btn) {
                btn.disabled = true;
                btn.textContent = 'Ulangi (' + left + 'd)';
            }
            profileOtpTimer = setInterval(() => {
                left--;
                if (left <= 0) {
                    clearInterval(profileOtpTimer);
                    if (btn) {
                        btn.disabled = false;
                        btn.innerHTML = '<i class="bi bi-send me-1"></i> Kirim OTP';
                    }
                } else if (btn) {
                    btn.textContent = 'Ulangi (' + left + 'd)';
                }
            }, 1000);
            if (resData.ok && otpInp) otpInp.focus();
        } else if (btn) {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-send me-1"></i> Kirim OTP';
        }
    };
})();
</script>
<?= $this->endSection() ?>
