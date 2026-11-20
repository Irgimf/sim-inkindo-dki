<div class="form-container sign-up">
    <div style="background: #ffffff; display: flex; align-items: center; justify-content: center; flex-direction: column; padding: 0 38px; height: 100%; text-align: center;">
        <a href="<?= esc(rtrim(config('App')->webProfileURL, '/') . '/') ?>" title="Kembali ke Beranda INKINDO DKI Jakarta" style="display: inline-block; line-height: 0; margin: 0 auto;">
            <img src="/logo-inkindo-auth.png" onerror="this.onerror=null;this.src='/logo-inkindo.png';" alt="INKINDO DKI Jakarta" class="auth-brand-logo">
        </a>
        <h1 style="color: var(--inkindo-blue); font-size: 24px; margin-bottom: 10px;">Pendaftaran Melalui KTA Online</h1>
        <p style="font-size: 13.5px; color: #475569; line-height: 1.65; margin: 0 0 18px;">
            Akun Anggota <strong>SIM-INKINDO DKI</strong> terintegrasi langsung dengan sistem <strong>KTA Online INKINDO</strong>.
            Silakan lakukan pendaftaran keanggotaan terlebih dahulu.
            Setelah akun KTA Online Anda aktif, Anda dapat langsung login di halaman ini.
        </p>
        <div class="mobile-auth-switch" style="margin-top: 22px;">
            <span>Sudah punya akun KTA Online?</span>
            <button type="button" class="mobile-switch-btn" id="mobileLoginBtn">Masuk di sini</button>
        </div>
    </div>
</div>
