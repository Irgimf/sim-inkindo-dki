<div class="form-container sign-up">
    <form action="/auth/processRegister" method="post" id="registerAnggotaForm">
<?= csrf_field() ?>
        <a href="<?= esc(rtrim(config('App')->webProfileURL, '/') . '/') ?>" title="Kembali ke Beranda INKINDO DKI Jakarta" style="display: inline-block; line-height: 0; margin: 0 auto;">
            <img src="/logo-inkindo-auth.png" onerror="this.onerror=null;this.src='/logo-inkindo.png';" alt="INKINDO DKI Jakarta" class="auth-brand-logo">
        </a>
        <h1 style="color: var(--inkindo-blue);">Buat Akun</h1>
        <p>Daftar akun SIM-INKINDO DKI menggunakan email aktif perusahaan Anda.</p>

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

        <input type="text" name="website_hp" value="" tabindex="-1" autocomplete="new-password" readonly class="auth-hp-field" aria-hidden="true">
        <input type="hidden" name="captcha" value="">
        <input type="email" name="email" placeholder="Masukkan Email" required value="<?= set_value('email') ?>" autocomplete="email">
        <div class="auth-input-group">
            <i class="bi bi-lock auth-input-icon"></i>
            <input type="password" name="password" id="regPasswordInput" placeholder="Buat Password (min. 6 karakter)" minlength="6" required autocomplete="new-password">
            <button type="button" class="auth-eye-btn" onclick="togglePasswordVisibility('regPasswordInput', this)" aria-label="Tampilkan password">
                <i class="bi bi-eye-slash"></i>
            </button>
        </div>
        <div class="auth-input-group">
            <i class="bi bi-shield-lock auth-input-icon"></i>
            <input type="password" name="confpassword" id="regConfPasswordInput" placeholder="Konfirmasi Password" required autocomplete="new-password">
            <button type="button" class="auth-eye-btn" onclick="togglePasswordVisibility('regConfPasswordInput', this)" aria-label="Tampilkan konfirmasi password">
                <i class="bi bi-eye-slash"></i>
            </button>
        </div>
        <div id="regPasswordMatchMsg" style="width: 100%; text-align: left; font-size: 12px; font-weight: 600; min-height: 18px; margin: 2px 0 4px;"></div>
        <button type="submit" id="regSubmitBtn">Daftar</button>
        <div class="mobile-auth-switch">
            <span>Sudah punya akun?</span>
            <button type="button" class="mobile-switch-btn" id="mobileLoginBtn">Masuk di sini</button>
        </div>
    </form>
</div>
