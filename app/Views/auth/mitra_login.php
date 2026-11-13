<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal Mitra Kerja - SIM-INKINDO DKI</title>
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

        .portal-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(10, 100, 164, 0.1);
            color: var(--inkindo-blue);
            font-size: 12px;
            font-weight: 700;
            padding: 6px 14px;
            border-radius: 99px;
            margin-bottom: 10px;
            letter-spacing: 0.4px;
            text-transform: uppercase;
        }

        .auth-wrapper {
            background-color: #fff;
            border-radius: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            position: relative;
            overflow: hidden;
            width: 850px;
            max-width: 95%;
            min-height: 520px;
        }

        .auth-wrapper p {
            font-size: 14px;
            line-height: 20px;
            letter-spacing: 0.3px;
            margin: 14px 0 18px;
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

        @media (max-width: 767.98px) {
            body {
                height: auto;
                min-height: 100vh;
                padding: 24px 14px;
            }
            .auth-wrapper {
                width: 100%;
                max-width: 430px;
                min-height: auto;
                border-radius: 22px;
            }
            .auth-wrapper form {
                padding: 32px 22px;
                height: auto;
            }
            .auth-wrapper h1 {
                font-size: 24px !important;
            }
            .auth-wrapper p {
                margin: 10px 0 14px;
                font-size: 13.5px;
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
        <!-- Sign Up Form (Mitra Kerja) -->
        <div class="form-container sign-up">
            <form action="/mitra/processRegister" method="post">
                <div class="portal-badge"><i class="bi bi-people-fill"></i> Portal Mitra Kerja</div>
                <h1 style="color: var(--inkindo-blue); font-size: 28px;">Daftar Mitra Kerja</h1>
                <p>Bergabung sebagai Mitra Kerja strategis INKINDO DKI Jakarta</p>
                
                <?php if(isset($validation)): ?>
                    <div class="alert alert-danger">
                        <ul class="list-unstyled">
                        <?php foreach($validation->getErrors() as $error): ?>
                            <li><?= esc($error) ?></li>
                        <?php endforeach ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <input type="text" name="username" placeholder="Buat Username Mitra" required value="<?= set_value('username') ?>">
                <input type="password" name="password" placeholder="Buat Password (min. 6 karakter)" required>
                <input type="password" name="confpassword" placeholder="Konfirmasi Password" required>
                <button type="submit">Daftar Mitra</button>
                <div class="mobile-auth-switch">
                    <span>Sudah terdaftar mitra?</span>
                    <button type="button" class="mobile-switch-btn" id="mobileLoginBtn">Masuk Mitra</button>
                </div>
            </form>
        </div>

        <!-- Sign In Form (Mitra Kerja) -->
        <div class="form-container sign-in">
            <form action="/mitra/processLogin" method="post">
                <div class="portal-badge"><i class="bi bi-people-fill"></i> Portal Mitra Kerja</div>
                <h1 style="color: var(--inkindo-blue); font-size: 28px;">Login Mitra Kerja</h1>
                <p>Kelola profil perusahaan, logo, dan katalog produk kemitraan Anda</p>
                
                <?php if(session()->getFlashdata('msg')):?>
                    <div class="alert alert-danger"><?= session()->getFlashdata('msg') ?></div>
                <?php endif;?>
                <?php if(session()->getFlashdata('success')):?>
                    <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
                <?php endif;?>

                <input type="text" name="username" placeholder="Username Mitra / Admin" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit">Login</button>
                <div class="mobile-auth-switch">
                    <span>Belum daftar mitra?</span>
                    <button type="button" class="mobile-switch-btn" id="mobileRegisterBtn">Daftar Mitra</button>
                </div>
            </form>
        </div>

        <!-- Overlay / Toggle Container -->
        <div class="toggle-container">
            <div class="toggle">
                <!-- Left Panel (Visible when active / on Register view) -->
                <div class="toggle-panel toggle-left">
                    <h1>Sudah Terdaftar Mitra?</h1>
                    <p>Masuk ke akun Mitra Kerja Anda untuk memperbarui profil perusahaan, logo, dan detail produk.</p>
                    <button class="hidden" id="loginBtn">Masuk Mitra</button>
                </div>
                <!-- Right Panel (Visible when inactive / on Login view) -->
                <div class="toggle-panel toggle-right">
                    <h1>Daftar Mitra Baru?</h1>
                    <p>Daftarkan perusahaan Anda sebagai Mitra Kerja resmi INKINDO DKI Jakarta untuk menampilkan logo dan katalog produk Anda.</p>
                    <button class="hidden" id="registerBtn">Daftar Mitra</button>
                </div>
            </div>
        </div>
    </div>

    <div class="auth-bottom-links">
        <a href="<?= esc(rtrim(config('App')->webProfileURL, '/')); ?>" style="color: #64748b; text-decoration: none; font-weight: 500;">
            <i class="bi bi-arrow-left"></i> Kembali ke Web Profile INKINDO
        </a>
        <span class="auth-link-sep" style="color: #cbd5e1;">|</span>
        <a href="/auth/login" style="color: var(--inkindo-blue); text-decoration: none; font-weight: 600;">
            Portal Login Anggota (PT) <i class="bi bi-arrow-right"></i>
        </a>
    </div>

    <script>
        const authWrapper = document.getElementById('authWrapper');
        const registerBtn = document.getElementById('registerBtn');
        const loginBtn = document.getElementById('loginBtn');
        const mobileRegisterBtn = document.getElementById('mobileRegisterBtn');
        const mobileLoginBtn = document.getElementById('mobileLoginBtn');

        function showRegister() {
            authWrapper.classList.add("active");
            window.history.pushState({}, '', '/mitra/register');
        }

        function showLogin() {
            authWrapper.classList.remove("active");
            window.history.pushState({}, '', '/mitra/login');
        }

        if (registerBtn) registerBtn.addEventListener('click', showRegister);
        if (mobileRegisterBtn) mobileRegisterBtn.addEventListener('click', showRegister);
        if (loginBtn) loginBtn.addEventListener('click', showLogin);
        if (mobileLoginBtn) mobileLoginBtn.addEventListener('click', showLogin);
    </script>
</body>
</html>

