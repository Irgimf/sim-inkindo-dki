<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SIM-INKINDO DKI (Sistem Informasi & Manajemen INKINDO DKI)</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --inkindo-blue: #0A64A4;
            --inkindo-blue-hover: #085287;
            --inkindo-green: #62B845;
            --inkindo-bg: #F4F7FA;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Outfit', sans-serif;
        }

        body {
            background-color: var(--inkindo-bg);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            height: 100vh;
        }

        .auth-wrapper {
            background-color: #fff;
            border-radius: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            position: relative;
            overflow: hidden;
            width: 850px;
            max-width: 95%;
            min-height: 560px;
        }

        .auth-captcha-row {
            width: 100%;
            display: flex;
            align-items: center;
            gap: 8px;
            margin: 8px 0;
        }

        .auth-captcha-box {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            background: #f1f5f9;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 2px 6px 2px 2px;
            flex-shrink: 0;
        }

        .auth-captcha-box img {
            height: 40px;
            width: auto;
            display: block;
            border-radius: 8px;
        }

        .auth-captcha-refresh {
            background: transparent !important;
            border: none !important;
            box-shadow: none !important;
            color: var(--inkindo-blue) !important;
            padding: 4px 6px !important;
            margin: 0 !important;
            font-size: 15px !important;
            cursor: pointer;
            line-height: 1;
        }

        .auth-captcha-refresh:hover {
            background: rgba(10, 100, 164, 0.08) !important;
            transform: none !important;
        }

        .auth-captcha-input {
            margin: 0 !important;
            text-transform: uppercase;
            letter-spacing: 2px;
            font-weight: 600;
            text-align: center;
            min-width: 0;
        }

        .auth-hp-field {
            position: absolute;
            left: -9999px;
            width: 1px;
            height: 1px;
            opacity: 0;
            pointer-events: none;
        }

        .auth-brand-logo {
            height: 88px;
            width: auto;
            max-width: 240px;
            object-fit: contain;
            margin-bottom: 10px;
            display: block;
        }

        .auth-wrapper p {
            font-size: 14px;
            line-height: 20px;
            letter-spacing: 0.3px;
            margin: 20px 0;
            color: #555;
        }

        .auth-wrapper span {
            font-size: 13px;
            color: #777;
        }

        .auth-wrapper a {
            color: var(--inkindo-blue);
            font-size: 13px;
            text-decoration: none;
            margin: 15px 0 10px;
            font-weight: 600;
        }

        .auth-wrapper button {
            background-color: var(--inkindo-blue);
            color: #fff;
            font-size: 14px;
            padding: 12px 45px;
            border: 1px solid transparent;
            border-radius: 50px;
            font-weight: 600;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            margin-top: 20px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 10px rgba(10, 100, 164, 0.3);
        }

        .auth-wrapper button:hover {
            background-color: var(--inkindo-blue-hover);
            transform: translateY(-2px);
        }

        .auth-wrapper button.hidden {
            background-color: transparent;
            border-color: #fff;
            box-shadow: none;
        }
        
        .auth-wrapper button.hidden:hover {
            background-color: rgba(255,255,255,0.1);
        }

        .auth-wrapper form {
            background-color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            padding: 0 50px;
            height: 100%;
            text-align: center;
        }

        .auth-wrapper input {
            background-color: #f3f4f6;
            border: 1px solid #e5e7eb;
            margin: 8px 0;
            padding: 12px 15px;
            font-size: 14px;
            border-radius: 10px;
            width: 100%;
            outline: none;
            transition: all 0.3s ease;
        }
        
        .auth-wrapper input:focus {
            border-color: var(--inkindo-blue);
            box-shadow: 0 0 0 3px rgba(10, 100, 164, 0.1);
        }

        .form-container {
            position: absolute;
            top: 0;
            height: 100%;
            transition: all 0.6s ease-in-out;
        }

        .sign-in {
            left: 0;
            width: 50%;
            z-index: 2;
        }

        .auth-wrapper.active .sign-in {
            transform: translateX(100%);
        }

        .sign-up {
            left: 0;
            width: 50%;
            opacity: 0;
            z-index: 1;
        }

        .auth-wrapper.active .sign-up {
            transform: translateX(100%);
            opacity: 1;
            z-index: 5;
            animation: move 0.6s;
        }

        @keyframes move {
            0%, 49.99% {
                opacity: 0;
                z-index: 1;
            }
            50%, 100% {
                opacity: 1;
                z-index: 5;
            }
        }

        .social-icons {
            margin: 20px 0;
        }

        .social-icons a {
            border: 1px solid #ddd;
            border-radius: 50%;
            display: inline-flex;
            justify-content: center;
            align-items: center;
            margin: 0 5px;
            width: 45px;
            height: 45px;
            color: #333;
            font-size: 18px;
            transition: all 0.3s ease;
        }
        
        .social-icons a:hover {
            color: var(--inkindo-blue);
            border-color: var(--inkindo-blue);
            background-color: rgba(10, 100, 164, 0.05);
        }

        .toggle-container {
            position: absolute;
            top: 0;
            left: 50%;
            width: 50%;
            height: 100%;
            overflow: hidden;
            transition: all 0.6s ease-in-out;
            border-radius: 150px 0 0 100px;
            z-index: 1000;
        }

        .auth-wrapper.active .toggle-container {
            transform: translateX(-100%);
            border-radius: 0 150px 100px 0;
        }

        .toggle {
            background-color: var(--inkindo-blue);
            background: linear-gradient(to right, var(--inkindo-blue-hover), var(--inkindo-blue));
            height: 100%;
            color: #fff;
            position: relative;
            left: -100%;
            height: 100%;
            width: 200%;
            transform: translateX(0);
            transition: all 0.6s ease-in-out;
        }

        .auth-wrapper.active .toggle {
            transform: translateX(50%);
        }

        .toggle-panel {
            position: absolute;
            width: 50%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            padding: 0 40px;
            text-align: center;
            top: 0;
            transform: translateX(0);
            transition: all 0.6s ease-in-out;
        }
        
        .toggle-panel h1 {
            font-weight: 700;
            margin-bottom: 10px;
        }
        
        .toggle-panel p {
            color: rgba(255,255,255,0.9);
        }

        .toggle-left {
            transform: translateX(-200%);
        }

        .auth-wrapper.active .toggle-left {
            transform: translateX(0);
        }

        .toggle-right {
            right: 0;
            transform: translateX(0);
        }
        
        .alert {
            padding: 10px 15px;
            border-radius: 8px;
            margin-bottom: 15px;
            width: 100%;
            font-size: 13px;
            text-align: left;
        }
        .alert-danger {
            background-color: #fee2e2;
            color: #991b1b;
            border: 1px solid #f87171;
        }
        .alert-success {
            background-color: #dcfce7;
            color: #166534;
            border: 1px solid #4ade80;
        }
        
        .list-unstyled {
            list-style: none;
            text-align: left;
            margin: 0;
            padding: 0;
        }

        .mobile-auth-switch {
            display: none;
        }

        .auth-wrapper button.mobile-switch-btn {
            background: transparent;
            color: var(--inkindo-blue);
            border: none;
            box-shadow: none;
            padding: 4px 8px;
            margin-top: 0;
            font-size: 13.5px;
            font-weight: 700;
            text-transform: none;
            letter-spacing: 0;
            border-radius: 8px;
        }

        .auth-wrapper button.mobile-switch-btn:hover {
            background: rgba(10, 100, 164, 0.08);
            transform: none;
        }

        .auth-bottom-links {
            margin-top: 18px;
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 10px 20px;
            align-items: center;
            font-size: 13.5px;
            text-align: center;
            padding: 0 12px;
        }

        .mobile-auth-header {
            display: none;
        }

        .auth-input-group {
            position: relative;
            width: 100%;
            margin: 7px 0;
        }

        .auth-input-group .auth-input-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #64748b;
            font-size: 16px;
            pointer-events: none;
        }

        .auth-input-group input {
            margin: 0;
            padding-left: 42px;
            padding-right: 42px;
        }

        .auth-eye-btn {
            position: absolute !important;
            right: 12px;
            top: 50%;
            transform: translateY(-50%) !important;
            background: transparent !important;
            border: none !important;
            box-shadow: none !important;
            padding: 4px !important;
            margin: 0 !important;
            color: #64748b !important;
            font-size: 16px !important;
            text-transform: none !important;
            letter-spacing: 0 !important;
            cursor: pointer;
        }

        .auth-eye-btn:hover {
            color: var(--inkindo-blue) !important;
            background: transparent !important;
            transform: translateY(-50%) !important;
        }

        .auth-options-row {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            margin: 8px 0 4px;
            font-size: 12.5px;
        }

        .auth-remember-label {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            color: #475569;
            cursor: pointer;
            user-select: none;
        }

        .auth-remember-label input[type="checkbox"] {
            width: 15px;
            height: 15px;
            margin: 0;
            padding: 0;
            accent-color: var(--inkindo-blue);
            cursor: pointer;
        }

        .auth-forgot-link {
            color: var(--inkindo-blue) !important;
            font-size: 12.5px !important;
            font-weight: 600 !important;
            margin: 0 !important;
            cursor: pointer;
        }

        .auth-forgot-modal-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.45);
            display: none;
            align-items: center;
            justify-content: center;
            padding: 20px;
            z-index: 9999;
        }

        .auth-forgot-modal-backdrop.is-open {
            display: flex;
        }

        .auth-forgot-modal-card {
            background: #ffffff;
            border-radius: 20px;
            padding: 26px 24px;
            max-width: 380px;
            width: 100%;
            text-align: center;
            box-shadow: 0 18px 40px rgba(15, 23, 42, 0.2);
        }

        @media (max-width: 767.98px) {
            body {
                height: auto;
                min-height: 100vh;
                padding: 56px 28px 36px;
                justify-content: flex-start;
                align-items: center;
                background: #ffffff;
            }
            .auth-wrapper {
                width: 100%;
                max-width: 335px;
                min-height: auto;
                border-radius: 0;
                background: transparent;
                border: none;
                box-shadow: none;
                overflow: visible;
            }
            .auth-brand-logo {
                display: block;
                height: 175px;
                width: auto;
                max-width: 200px;
                padding: 2px 4px;
                margin: 0 auto 20px;
            }
            .auth-wrapper form {
                padding: 0;
                height: auto;
                background: transparent;
            }
            .auth-wrapper h1 {
                font-size: 24px !important;
            }
            .auth-wrapper p {
                margin: 6px 0 20px;
                font-size: 13.5px;
            }
            .auth-input-group {
                background: #ffffff;
                border: 1px solid #e2e8f0;
                border-radius: 16px;
                box-shadow: 0 4px 14px rgba(10, 100, 164, 0.05);
                margin: 7px 0;
            }
            .auth-input-group input {
                border-radius: 16px;
                padding: 14px 42px;
                background: transparent;
                border: none;
            }
            .auth-wrapper .sign-up input {
                background: #ffffff;
                border: 1px solid #e2e8f0;
                border-radius: 16px;
                padding: 14px 16px;
                box-shadow: 0 4px 14px rgba(10, 100, 164, 0.05);
                margin: 7px 0;
            }
            .auth-options-row {
                margin: 12px 2px 6px;
                background: transparent;
            }
            .auth-wrapper button[type="submit"] {
                width: 100%;
                border-radius: 16px;
                padding: 14px 20px;
                margin-top: 12px;
                box-shadow: 0 8px 20px rgba(10, 100, 164, 0.24);
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 8px;
            }
            .auth-wrapper .form-container {
                position: relative;
                top: auto;
                left: auto;
                width: 100%;
                height: auto;
                transform: none !important;
                animation: none !important;
                transition: none;
                background: transparent;
            }
            .mobile-auth-switch {
                border-top: none !important;
                margin-top: 18px !important;
                padding-top: 0 !important;
            }
            .auth-wrapper .sign-in {
                display: block;
                opacity: 1;
            }
            .auth-wrapper .sign-up {
                display: none;
                opacity: 1;
            }
            .auth-wrapper.active .sign-in {
                display: none;
            }
            .auth-wrapper.active .sign-up {
                display: block;
                opacity: 1;
            }
            .toggle-container {
                display: none;
            }
            .mobile-auth-switch {
                display: flex;
                flex-wrap: wrap;
                align-items: center;
                justify-content: center;
                gap: 4px;
                margin-top: 18px;
                padding-top: 14px;
                border-top: 1px solid #e2e8f0;
                width: 100%;
                font-size: 13px;
                color: #64748b;
            }
        }

        @media (max-width: 575.98px) {
            .auth-bottom-links {
                flex-direction: column;
                gap: 8px;
            }
            .auth-link-sep {
                display: none;
            }
        }
    </style>
</head>
<body>

    <div class="auth-wrapper <?= (isset($is_register) && $is_register) ? 'active' : '' ?>" id="authWrapper">
        <!-- Sign Up Form (2 Konsep Register Anggota) -->
        <!-- 1. KONSEP SEMENTARA (Belum ada akses database KTA Online) - AKTIF -->
        <?php /* <?= view('auth/partials/register_anggota_manual', get_defined_vars()) ?> */ ?>

        <!-- 2. KONSEP INTEGRASI KTA ONLINE (Hapus comment di bawah dan comment konsep 1 di atas jika sudah ada akses database KTA Online) -->
        <?= view('auth/partials/register_anggota_kta_online', get_defined_vars()) ?>

        <!-- Sign In Form -->
        <div class="form-container sign-in">
            <form action="/auth/processLogin" method="post">
                <input type="hidden" name="next" value="<?= esc($next ?? '') ?>">
                <a href="<?= esc(rtrim(config('App')->webProfileURL, '/') . '/') ?>" title="Kembali ke Beranda INKINDO DKI Jakarta" style="display: inline-block; line-height: 0; margin: 0 auto;">
                    <img src="/logo-inkindo-auth.png" onerror="this.onerror=null;this.src='/logo-inkindo.png';" alt="INKINDO DKI Jakarta" class="auth-brand-logo">
                </a>
                <h1 style="color: var(--inkindo-blue);">Selamat Datang</h1>
                <p>Silakan masuk ke akun SIM-INKINDO DKI Anda</p>
                
                <?php if(session()->getFlashdata('msg')):?>
                    <div class="alert alert-danger"><?= session()->getFlashdata('msg') ?></div>
                <?php endif;?>
                <?php if(session()->getFlashdata('success')):?>
                    <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
                <?php endif;?>

                <input type="text" name="website_hp" value="" tabindex="-1" autocomplete="new-password" readonly class="auth-hp-field" aria-hidden="true">
                <input type="hidden" name="captcha" value="">
                <div class="auth-input-group">
                    <i class="bi bi-person auth-input-icon"></i>
                    <input type="text" name="username" placeholder="Username atau Email" required>
                </div>
                <div class="auth-input-group">
                    <i class="bi bi-lock auth-input-icon"></i>
                    <input type="password" name="password" id="loginPasswordInput" placeholder="Password" required>
                    <button type="button" class="auth-eye-btn" onclick="togglePasswordVisibility('loginPasswordInput', this)" aria-label="Tampilkan password">
                        <i class="bi bi-eye-slash"></i>
                    </button>
                </div>
                <div class="auth-options-row">
                    <label class="auth-remember-label">
                        <input type="checkbox" name="remember" value="1"> Remember Me
                    </label>
                    <a href="#" class="auth-forgot-link" onclick="openForgotModal(event)">Lupa Password?</a>
                </div>
                <button type="submit">Login <i class="bi bi-arrow-right ms-1"></i></button>
                <div class="mobile-auth-switch">
                    <span>Belum punya akun?</span>
                    <button type="button" class="mobile-switch-btn" id="mobileRegisterBtn">Daftar Akun</button>
                </div>
            </form>
        </div>

        <!-- Overlay / Toggle Container -->
        <div class="toggle-container">
            <div class="toggle">
                <!-- Left Panel (Visible when active / on Register view) -->
                <div class="toggle-panel toggle-left">
                    <h1>Sudah Punya Akun?</h1>
                    <p>Masukkan detail akun Anda untuk masuk ke portal SIM-INKINDO DKI (Sistem Informasi &amp; Manajemen INKINDO DKI).</p>
                    <button class="hidden" id="loginBtn">Masuk</button>
                </div>
                <!-- Right Panel (Visible when inactive / on Login view) -->
                <div class="toggle-panel toggle-right">
                    <h1>Belum Punya Akun?</h1>
                    <p>Belum memiliki akun? Segera daftarkan akun baru Anda untuk mulai mengelola SBU dan sertifikat.</p>
                    <button class="hidden" id="registerBtn">Daftar Akun</button>
                </div>
            </div>
        </div>
    </div>

    <div class="auth-forgot-modal-backdrop" id="captchaModal">
        <div class="auth-forgot-modal-card" style="max-width: 360px;">
            <h3 style="color: var(--inkindo-blue); margin-bottom: 6px; font-size: 19px;">Verifikasi Keamanan</h3>
            <p style="font-size: 13px; color: #64748b; line-height: 1.5; margin-bottom: 16px;">
                Masukkan kode CAPTCHA di bawah ini untuk melanjutkan.
            </p>
            <div class="auth-captcha-row" style="justify-content: center; margin-bottom: 14px;">
                <div class="auth-captcha-box">
                    <img src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" id="captchaModalImg" alt="CAPTCHA">
                    <button type="button" class="auth-captcha-refresh" onclick="refreshModalCaptcha()" title="Ganti kode CAPTCHA" aria-label="Refresh CAPTCHA">
                        <i class="bi bi-arrow-clockwise"></i>
                    </button>
                </div>
                <input type="text" id="captchaModalInput" class="auth-captcha-input" placeholder="Kode" maxlength="5" autocomplete="off" style="max-width: 130px; background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 10px; padding: 10px 12px;">
            </div>
            <div style="display: flex; gap: 10px; justify-content: center;">
                <button type="button" onclick="closeCaptchaModal()" style="background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; border-radius: 12px; padding: 10px 18px; font-weight: 600; cursor: pointer;">Batal</button>
                <button type="button" onclick="submitWithCaptcha()" style="background: var(--inkindo-blue); color: #fff; border: none; border-radius: 12px; padding: 10px 20px; font-weight: 600; cursor: pointer;">Lanjutkan</button>
            </div>
        </div>
    </div>

    <div class="auth-forgot-modal-backdrop" id="forgotModal">
        <div class="auth-forgot-modal-card">
            <h3 style="color: var(--inkindo-blue); margin-bottom: 8px; font-size: 19px;">Lupa Password?</h3>
            <p style="font-size: 13.5px; color: #475569; line-height: 1.6; margin-bottom: 18px;">
                Untuk keamanan akun anggota, silakan hubungi Admin Sekretariat DPP INKINDO DKI Jakarta di <strong>+62217942940</strong> atau email <strong>sekretariat@inkindo-dki.org</strong> untuk reset password.
            </p>
            <button type="button" onclick="closeForgotModal()" style="background: var(--inkindo-blue); color: #fff; border: none; border-radius: 12px; padding: 10px 24px; font-weight: 600; cursor: pointer;">Mengerti</button>
        </div>
    </div>

    <script>
        let pendingAuthForm = null;
        let pendingCaptchaFormKey = 'login';

        function refreshModalCaptcha() {
            const img = document.getElementById('captchaModalImg');
            if (img) {
                img.src = '/auth/captcha?form=' + encodeURIComponent(pendingCaptchaFormKey) + '&t=' + Date.now();
            }
        }

        function openCaptchaModal(formEl, formKey) {
            pendingAuthForm = formEl;
            pendingCaptchaFormKey = formKey;
            const hiddenCaptcha = formEl ? formEl.querySelector('input[name="captcha"]') : null;
            if (hiddenCaptcha) hiddenCaptcha.value = '';
            const inp = document.getElementById('captchaModalInput');
            if (inp) inp.value = '';
            refreshModalCaptcha();
            document.getElementById('captchaModal')?.classList.add('is-open');
            setTimeout(() => { if (inp) inp.focus(); }, 80);
        }

        function closeCaptchaModal() {
            document.getElementById('captchaModal')?.classList.remove('is-open');
            pendingAuthForm = null;
        }

        function submitWithCaptcha() {
            const inp = document.getElementById('captchaModalInput');
            const code = (inp ? inp.value : '').trim();
            if (!code) {
                if (inp) inp.focus();
                return;
            }
            if (pendingAuthForm) {
                const hiddenCaptcha = pendingAuthForm.querySelector('input[name="captcha"]');
                if (hiddenCaptcha) hiddenCaptcha.value = code;
                const formToSubmit = pendingAuthForm;
                closeCaptchaModal();
                formToSubmit.submit();
            }
        }

        document.getElementById('captchaModal')?.addEventListener('click', function (e) {
            if (e.target === this) closeCaptchaModal();
        });
        document.getElementById('captchaModalInput')?.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                submitWithCaptcha();
            }
        });

        document.querySelector('.sign-in form')?.addEventListener('submit', function (e) {
            const hiddenCaptcha = this.querySelector('input[name="captcha"]');
            if (hiddenCaptcha && !hiddenCaptcha.value.trim()) {
                e.preventDefault();
                openCaptchaModal(this, 'login');
            }
        });
        const regPassInput = document.getElementById('regPasswordInput');
        const regConfPassInput = document.getElementById('regConfPasswordInput');
        const regPassMsg = document.getElementById('regPasswordMatchMsg');

        function checkRegisterPasswordMatch() {
            if (!regPassInput || !regConfPassInput || !regPassMsg) return true;
            const p1 = regPassInput.value;
            const p2 = regConfPassInput.value;
            if (!p2) {
                regPassMsg.textContent = '';
                return true;
            }
            if (p1 !== p2) {
                regPassMsg.textContent = 'Password tidak sama';
                regPassMsg.style.color = '#dc2626';
                return false;
            }
            regPassMsg.textContent = 'Password sesuai';
            regPassMsg.style.color = '#16a34a';
            return true;
        }

        if (regPassInput) regPassInput.addEventListener('input', checkRegisterPasswordMatch);
        if (regConfPassInput) regConfPassInput.addEventListener('input', checkRegisterPasswordMatch);

        document.querySelector('.sign-up form')?.addEventListener('submit', function (e) {
            if (!checkRegisterPasswordMatch()) {
                e.preventDefault();
                if (regConfPassInput) regConfPassInput.focus();
                return;
            }
            const hiddenCaptcha = this.querySelector('input[name="captcha"]');
            if (hiddenCaptcha && !hiddenCaptcha.value.trim()) {
                e.preventDefault();
                openCaptchaModal(this, 'register');
            }
        });

        window.addEventListener('pageshow', function () {
            document.querySelectorAll('input[name="captcha"]').forEach((el) => {
                el.value = '';
            });
        });

        function togglePasswordVisibility(inputId, btn) {
            const inp = document.getElementById(inputId);
            if (!inp) return;
            const isPass = inp.type === 'password';
            inp.type = isPass ? 'text' : 'password';
            const icon = btn.querySelector('i');
            if (icon) {
                icon.className = isPass ? 'bi bi-eye' : 'bi bi-eye-slash';
            }
        }
        function openForgotModal(e) {
            if (e) e.preventDefault();
            document.getElementById('forgotModal')?.classList.add('is-open');
        }
        function closeForgotModal() {
            document.getElementById('forgotModal')?.classList.remove('is-open');
        }
        document.getElementById('forgotModal')?.addEventListener('click', function (e) {
            if (e.target === this) closeForgotModal();
        });
        const authWrapper = document.getElementById('authWrapper');
        const registerBtn = document.getElementById('registerBtn');
        const loginBtn = document.getElementById('loginBtn');
        const mobileRegisterBtn = document.getElementById('mobileRegisterBtn');
        const mobileLoginBtn = document.getElementById('mobileLoginBtn');

        function syncViewFromUrl() {
            if (!authWrapper) return;
            const isRegisterPath = window.location.pathname.replace(/\/+$/, '').endsWith('/register');
            authWrapper.classList.toggle('active', isRegisterPath);
        }

        function showRegister() {
            authWrapper.classList.add("active");
            if (!window.location.pathname.replace(/\/+$/, '').endsWith('/register')) {
                window.history.pushState({ view: 'register' }, "", "/auth/register" + window.location.search);
            }
        }

        function showLogin() {
            authWrapper.classList.remove("active");
            if (window.location.pathname.replace(/\/+$/, '').endsWith('/register')) {
                window.history.pushState({ view: 'login' }, "", "/auth/login" + window.location.search);
            }
        }

        window.addEventListener('popstate', syncViewFromUrl);
        window.addEventListener('pageshow', syncViewFromUrl);

        if (registerBtn) registerBtn.addEventListener('click', showRegister);
        if (mobileRegisterBtn) mobileRegisterBtn.addEventListener('click', showRegister);
        if (loginBtn) loginBtn.addEventListener('click', showLogin);
        if (mobileLoginBtn) mobileLoginBtn.addEventListener('click', showLogin);
    </script>
</body>
</html>
