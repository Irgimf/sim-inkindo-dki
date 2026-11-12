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
            min-height: 520px;
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
    </style>
</head>
<body>

    <div class="auth-wrapper <?= (isset($is_register) && $is_register) ? 'active' : '' ?>" id="authWrapper">
        <!-- Sign Up Form -->
        <div class="form-container sign-up">
            <form action="/auth/processRegister" method="post">
                <h1 style="color: var(--inkindo-blue);">Buat Akun</h1>
                <p>Daftar akun SIM-INKINDO DKI untuk mengelola data perusahaan &amp; layanan anggota.</p>
                
                <?php if(isset($validation)): ?>
                    <div class="alert alert-danger">
                        <ul class="list-unstyled">
                        <?php foreach($validation->getErrors() as $error): ?>
                            <li><?= esc($error) ?></li>
                        <?php endforeach ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <input type="text" name="username" placeholder="Buat Username" required value="<?= set_value('username') ?>">
                <input type="password" name="password" placeholder="Buat Password" required>
                <input type="password" name="confpassword" placeholder="Konfirmasi Password" required>
                <button type="submit">Daftar</button>
            </form>
        </div>

        <!-- Sign In Form -->
        <div class="form-container sign-in">
            <form action="/auth/processLogin" method="post">
                <h1 style="color: var(--inkindo-blue);">Selamat Datang</h1>
                <p>Silakan masuk ke akun SIM-INKINDO DKI Anda</p>
                
                <?php if(session()->getFlashdata('msg')):?>
                    <div class="alert alert-danger"><?= session()->getFlashdata('msg') ?></div>
                <?php endif;?>
                <?php if(session()->getFlashdata('success')):?>
                    <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
                <?php endif;?>

                <input type="text" name="username" placeholder="Username" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit">Login</button>
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

    <div style="margin-top: 18px; display: flex; gap: 20px; align-items: center; font-size: 13.5px;">
        <a href="http://localhost:8000" style="color: #64748b; text-decoration: none; font-weight: 500;">
            <i class="bi bi-arrow-left"></i> Kembali ke Web Profile INKINDO
        </a>
        <span style="color: #cbd5e1;">|</span>
        <a href="/mitra/login" style="color: var(--inkindo-blue); text-decoration: none; font-weight: 600;">
            Portal Login Mitra Kerja <i class="bi bi-arrow-right"></i>
        </a>
    </div>

    <script>
        const authWrapper = document.getElementById('authWrapper');
        const registerBtn = document.getElementById('registerBtn');
        const loginBtn = document.getElementById('loginBtn');

        registerBtn.addEventListener('click', () => {
            authWrapper.classList.add("active");
            // Optionally update URL without reloading
            window.history.pushState({}, "", "/auth/register");
        });

        loginBtn.addEventListener('click', () => {
            authWrapper.classList.remove("active");
            // Optionally update URL without reloading
            window.history.pushState({}, "", "/auth/login");
        });
    </script>
</body>
</html>
