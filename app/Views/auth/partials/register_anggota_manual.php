<div class="form-container sign-up">
    <form action="/auth/processRegister" method="post" id="registerOtpForm">
<?= csrf_field() ?>
        <a href="<?= esc(rtrim(config('App')->webProfileURL, '/') . '/') ?>" title="Kembali ke Beranda INKINDO DKI Jakarta" style="display: inline-block; line-height: 0; margin: 0 auto;">
            <img src="/logo-inkindo-auth.png" onerror="this.onerror=null;this.src='/logo-inkindo.png';" alt="INKINDO DKI Jakarta" class="auth-brand-logo">
        </a>
        <h1 style="color: var(--inkindo-blue);">Buat Akun</h1>
        <p style="margin: 6px 0 10px;">Daftar akun menggunakan email aktif perusahaan Anda.</p>

        <div class="otp-steps" id="regOtpStepPills">
            <span class="otp-step is-active" data-step="1">1. Email</span>
            <span class="otp-step" data-step="2">2. OTP</span>
            <span class="otp-step" data-step="3">3. Password</span>
        </div>

        <?php if (isset($registerError)): ?>
            <div class="alert alert-danger"><?= esc($registerError) ?></div>
        <?php endif; ?>
        <?php if (isset($validation)): ?>
            <div class="alert alert-danger">
                <ul class="list-unstyled mb-0">
                <?php foreach ($validation->getErrors() as $error): ?>
                    <li><?= esc($error) ?></li>
                <?php endforeach ?>
                </ul>
            </div>
        <?php endif; ?>

        <div id="regOtpAlert" class="otp-alert" style="display: none;"></div>

        <input type="text" name="website_hp" id="regWebsiteHp" value="" tabindex="-1" autocomplete="new-password" readonly class="auth-hp-field" aria-hidden="true">

        <!-- Step 1: Cek Email & Kirim OTP -->
        <div id="regOtpStep1" class="otp-step-panel">
            <div class="auth-input-group">
                <i class="bi bi-envelope auth-input-icon"></i>
                <input type="email" id="regEmailInput" placeholder="Masukkan Email Perusahaan" value="<?= set_value('email') ?>" autocomplete="email">
            </div>
            <button type="button" id="regSendOtpBtn" onclick="regSendOtp(false)">Daftar</button>
        </div>

        <!-- Step 2: Masukkan Kode OTP 6 Digit -->
        <div id="regOtpStep2" class="otp-step-panel" style="display: none;">
            <div class="otp-masked-box">
                <i class="bi bi-envelope-check-fill"></i>
                <span>Kode OTP dikirim ke <strong id="regMaskedEmailText">***</strong></span>
            </div>
            <input type="text" id="regOtpCodeInput" class="otp-code-input" placeholder="••••••" maxlength="6" inputmode="numeric" pattern="[0-9]*" autocomplete="one-time-code">
            <div class="otp-resend-row">
                <button type="button" class="otp-link-btn" onclick="goRegStep(1)">Ganti Email</button>
                <button type="button" class="otp-link-btn" id="regResendOtpBtn" onclick="regSendOtp(true)">Kirim Ulang OTP</button>
            </div>
            <button type="button" id="regVerifyOtpBtn" onclick="regVerifyOtp()">
                Verifikasi OTP <i class="bi bi-check-circle ms-1"></i>
            </button>
        </div>

        <!-- Step 3: Buat Password Baru & Lanjut Isi Data PT -->
        <div id="regOtpStep3" class="otp-step-panel" style="display: none;">
            <input type="hidden" name="email" id="regVerifiedEmailHidden" value="">
            <div class="otp-badge-verified">
                <i class="bi bi-patch-check-fill"></i>
                <span>Email terverifikasi: <strong id="regVerifiedEmailText"></strong></span>
            </div>
            <div class="auth-input-group">
                <i class="bi bi-lock auth-input-icon"></i>
                <input type="password" name="password" id="regOtpPasswordInput" placeholder="Buat Password (min. 8 karakter)" minlength="8" required autocomplete="new-password">
                <button type="button" class="auth-eye-btn" onclick="togglePasswordVisibility('regOtpPasswordInput', this)" aria-label="Tampilkan password">
                    <i class="bi bi-eye-slash"></i>
                </button>
            </div>
            <div class="auth-input-group">
                <i class="bi bi-shield-lock auth-input-icon"></i>
                <input type="password" name="confpassword" id="regOtpConfPasswordInput" placeholder="Konfirmasi Password" minlength="8" required autocomplete="new-password">
                <button type="button" class="auth-eye-btn" onclick="togglePasswordVisibility('regOtpConfPasswordInput', this)" aria-label="Tampilkan konfirmasi password">
                    <i class="bi bi-eye-slash"></i>
                </button>
            </div>
            <div id="regOtpPassMatchMsg" class="otp-match-msg"></div>
            <button type="submit" id="regSubmitBtn">
                Daftar &amp; Isi Data PT <i class="bi bi-arrow-right ms-1"></i>
            </button>
        </div>

        <div class="mobile-auth-switch">
            <span>Sudah punya akun?</span>
            <button type="button" class="mobile-switch-btn" id="mobileLoginBtn">Masuk di sini</button>
        </div>
    </form>
</div>
