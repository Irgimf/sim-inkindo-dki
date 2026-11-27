<?php
    $portalKey   = ($portal ?? 'anggota') === 'mitra' ? 'mitra' : 'anggota';
    $portalTitle = $portalKey === 'mitra' ? 'Mitra Kerja' : 'Anggota';
?>
<div class="auth-forgot-modal-backdrop" id="forgotModal">
    <div class="auth-forgot-modal-card otp-modal-card">
        <div class="otp-modal-header">
            <div class="otp-modal-icon">
                <i class="bi bi-shield-lock-fill"></i>
            </div>
            <div>
                <h3>Reset Password <?= esc($portalTitle) ?></h3>
                <p>Verifikasi akun Anda menggunakan kode OTP email.</p>
            </div>
            <button type="button" class="otp-modal-close" onclick="closeForgotModal()" aria-label="Tutup">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <div class="otp-steps" id="forgotStepPills">
            <span class="otp-step is-active" data-step="1">1. Akun</span>
            <span class="otp-step" data-step="2">2. OTP</span>
            <span class="otp-step" data-step="3">3. Password</span>
        </div>

        <div id="forgotAlertBox" class="otp-alert" style="display: none;"></div>

        <!-- Step 1: Masukkan Email Terdaftar -->
        <div id="forgotStep1" class="otp-step-panel">
            <div class="auth-input-group">
                <i class="bi bi-envelope auth-input-icon"></i>
                <input type="email" id="forgotIdentifierInput" placeholder="Masukkan Email Terdaftar" autocomplete="email">
            </div>
            <div class="otp-modal-actions">
                <button type="button" class="otp-btn-secondary" onclick="closeForgotModal()">Batal</button>
                <button type="button" class="otp-btn-primary" id="forgotSendOtpBtn" onclick="forgotSendOtp()">
                    Kirim OTP <i class="bi bi-send ms-1"></i>
                </button>
            </div>
        </div>

        <!-- Step 2: Verifikasi OTP ke Email Tersensor -->
        <div id="forgotStep2" class="otp-step-panel" style="display: none;">
            <div class="otp-masked-box">
                <i class="bi bi-envelope-check-fill"></i>
                <span>OTP dikirim ke <strong id="forgotMaskedEmailText">***</strong></span>
            </div>
            <input type="text" id="forgotOtpInput" class="otp-code-input" placeholder="••••••" maxlength="6" inputmode="numeric" pattern="[0-9]*" autocomplete="one-time-code">
            <div class="otp-resend-row">
                <button type="button" class="otp-link-btn" onclick="goForgotStep(1)">Ganti Akun</button>
                <button type="button" class="otp-link-btn" id="forgotResendBtn" onclick="forgotSendOtp(true)">Kirim Ulang OTP</button>
            </div>
            <div class="otp-modal-actions">
                <button type="button" class="otp-btn-primary" style="width: 100%;" id="forgotVerifyOtpBtn" onclick="forgotVerifyOtp()">
                    Verifikasi OTP <i class="bi bi-check-circle ms-1"></i>
                </button>
            </div>
        </div>

        <!-- Step 3: Buat Password Baru -->
        <div id="forgotStep3" class="otp-step-panel" style="display: none;">
            <div class="otp-badge-verified">
                <i class="bi bi-patch-check-fill"></i>
                <span>Terverifikasi: <strong id="forgotVerifiedEmailText">***</strong></span>
            </div>
            <div class="auth-input-group">
                <i class="bi bi-lock auth-input-icon"></i>
                <input type="password" id="forgotNewPassInput" placeholder="Password baru (min. 8 karakter)" minlength="8" autocomplete="new-password">
                <button type="button" class="auth-eye-btn" onclick="togglePasswordVisibility('forgotNewPassInput', this)" aria-label="Tampilkan password">
                    <i class="bi bi-eye-slash"></i>
                </button>
            </div>
            <div class="auth-input-group">
                <i class="bi bi-shield-lock auth-input-icon"></i>
                <input type="password" id="forgotConfPassInput" placeholder="Konfirmasi password baru" minlength="8" autocomplete="new-password">
                <button type="button" class="auth-eye-btn" onclick="togglePasswordVisibility('forgotConfPassInput', this)" aria-label="Tampilkan konfirmasi password">
                    <i class="bi bi-eye-slash"></i>
                </button>
            </div>
            <div id="forgotPassMatchMsg" class="otp-match-msg"></div>
            <div class="otp-modal-actions">
                <button type="button" class="otp-btn-primary" style="width: 100%;" id="forgotSavePassBtn" onclick="forgotResetPassword()">
                    Simpan Password Baru <i class="bi bi-key ms-1"></i>
                </button>
            </div>
        </div>
    </div>
</div>
