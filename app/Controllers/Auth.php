<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Libraries\EmailOtpService;
use App\Models\UserModel;

class Auth extends BaseController
{
    private function ensureSsoTable(): void
    {
        $db = \Config\Database::connect();
        if ($db->tableExists('sso_tokens')) {
            return;
        }
        $db->query("
            CREATE TABLE IF NOT EXISTS `sso_tokens` (
                `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                `user_id` BIGINT UNSIGNED NOT NULL,
                `token` VARCHAR(128) NOT NULL,
                `target_app` VARCHAR(20) NOT NULL DEFAULT 'web',
                `redirect_to` VARCHAR(255) NOT NULL DEFAULT '/',
                `expires_at` DATETIME NOT NULL,
                `used_at` DATETIME NULL DEFAULT NULL,
                `created_at` DATETIME NULL DEFAULT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `sso_tokens_token_unique` (`token`),
                KEY `sso_tokens_user_expires_idx` (`user_id`, `expires_at`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    }

    private function sanitizeRedirectPath(?string $next): string
    {
        $next = trim((string) $next);
        if ($next === '') {
            return '';
        }
        if (str_starts_with($next, '/') && !str_starts_with($next, '//')) {
            return $next;
        }
        return '';
    }

    private function createWebSsoRedirect(int $userId, string $redirectPath = '/'): \CodeIgniter\HTTP\RedirectResponse
    {
        $this->ensureSsoTable();
        $db = \Config\Database::connect();

        $plainToken = bin2hex(random_bytes(32));
        $now = date('Y-m-d H:i:s');
        $expiresAt = date('Y-m-d H:i:s', time() + 120);

        // Bersihkan token SSO yang sudah kedaluwarsa atau terpakai (> 1 jam) agar tabel tetap ringan
        $db->table('sso_tokens')
            ->groupStart()
                ->where('expires_at <', date('Y-m-d H:i:s', time() - 3600))
                ->orWhere('used_at IS NOT NULL', null, false)
            ->groupEnd()
            ->delete();

        $db->table('sso_tokens')->insert([
            'user_id'     => $userId,
            'token'       => hash('sha256', $plainToken),
            'target_app'  => 'web',
            'redirect_to' => $this->sanitizeRedirectPath($redirectPath) ?: '/',
            'expires_at'  => $expiresAt,
            'used_at'     => null,
            'created_at'  => $now,
        ]);

        $webUrl = rtrim((string) config('App')->webProfileURL, '/');
        return redirect()->to($webUrl . '/sso/callback?token=' . urlencode($plainToken));
    }

    private function redirectByRole(string $role): \CodeIgniter\HTTP\RedirectResponse
    {
        if ($role === 'mitra') {
            return redirect()->to('/mitra');
        }
        if ($role === 'user') {
            return redirect()->to('/perusahaan');
        }
        return redirect()->to('/dashboard');
    }

    public function captcha()
    {
        $form = preg_replace('/[^a-z0-9_]/', '', strtolower((string) $this->request->getGet('form'))) ?: 'login';
        // Hindari karakter ambigu (0/O, 1/I/L, 2/Z, 5/S, 8/B) agar mudah dibaca manusia
        $chars = 'ACDEFGHJKMNPQRTUVWXY34679';
        $code = '';
        for ($i = 0; $i < 5; $i++) {
            $code .= $chars[random_int(0, strlen($chars) - 1)];
        }

        session()->set('captcha_' . $form, $code);

        $width = 160;
        $height = 46;

        // Render sebagai gambar raster PNG (via GD) yang tajam & jelas dibaca manusia namun tidak bisa di-parse sebagai teks XML oleh bot
        if (function_exists('imagecreatetruecolor') && function_exists('imagepng')) {
            $img = imagecreatetruecolor($width, $height);
            $bg  = imagecolorallocate($img, 241, 245, 249);
            imagefilledrectangle($img, 0, 0, $width, $height, $bg);

            // Garis & titik noise halus di latar belakang
            for ($i = 0; $i < 4; $i++) {
                $lineColor = imagecolorallocate($img, random_int(175, 210), random_int(195, 225), random_int(215, 240));
                imageline($img, random_int(0, 30), random_int(4, $height - 4), random_int($width - 30, $width), random_int(4, $height - 4), $lineColor);
            }
            for ($i = 0; $i < 60; $i++) {
                $dotColor = imagecolorallocate($img, random_int(185, 220), random_int(195, 225), random_int(205, 235));
                imagesetpixel($img, random_int(0, $width - 1), random_int(0, $height - 1), $dotColor);
            }

            $paletteRgb = [
                [10, 100, 164],
                [6, 66, 110],
                [30, 64, 175],
                [21, 128, 61],
                [15, 23, 42],
            ];

            $ttfCandidates = [
                'C:\\Windows\\Fonts\\arialbd.ttf',
                'C:\\Windows\\Fonts\\segoeuib.ttf',
                '/usr/share/fonts/truetype/dejavu/DejaVuSans-Bold.ttf',
                '/usr/share/fonts/truetype/liberation/LiberationSans-Bold.ttf',
            ];
            $ttfFont = null;
            foreach ($ttfCandidates as $cand) {
                if (is_file($cand)) {
                    $ttfFont = $cand;
                    break;
                }
            }

            if ($ttfFont !== null && function_exists('imagettftext')) {
                for ($i = 0; $i < 5; $i++) {
                    $rgb   = $paletteRgb[random_int(0, count($paletteRgb) - 1)];
                    $col   = imagecolorallocate($img, $rgb[0], $rgb[1], $rgb[2]);
                    $angle = random_int(-10, 10);
                    $x     = 16 + ($i * 27) + random_int(-2, 2);
                    $y     = 33 + random_int(-2, 2);
                    imagettftext($img, 20, $angle, $x, $y, $col, $ttfFont, $code[$i]);
                }
            } else {
                // Fallback GD bitmap diperbesar 2x agar tetap jelas dibaca
                $smallW = 80;
                $smallH = 23;
                $small  = imagecreatetruecolor($smallW, $smallH);
                $sBg    = imagecolorallocate($small, 241, 245, 249);
                imagefilledrectangle($small, 0, 0, $smallW, $smallH, $sBg);
                for ($i = 0; $i < 5; $i++) {
                    $rgb = $paletteRgb[random_int(0, count($paletteRgb) - 1)];
                    $col = imagecolorallocate($small, $rgb[0], $rgb[1], $rgb[2]);
                    imagestring($small, 5, 7 + ($i * 14), 4 + random_int(-1, 1), $code[$i], $col);
                }
                imagecopyresampled($img, $small, 0, 0, 0, 0, $width, $height, $smallW, $smallH);
                imagedestroy($small);
            }

            ob_start();
            imagepng($img);
            $pngBinary = ob_get_clean();
            imagedestroy($img);

            return $this->response
                ->setHeader('Content-Type', 'image/png')
                ->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
                ->setBody((string) $pngBinary);
        }

        $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="' . $width . '" height="' . $height . '" viewBox="0 0 ' . $width . ' ' . $height . '">';
        $svg .= '<rect width="100%" height="100%" rx="10" fill="#f1f5f9"/>';

        for ($i = 0; $i < 4; $i++) {
            $x1 = random_int(0, 40);
            $y1 = random_int(4, $height - 4);
            $x2 = random_int($width - 40, $width);
            $y2 = random_int(4, $height - 4);
            $color = ($i % 2 === 0) ? '#93c5fd' : '#86efac';
            $svg .= '<line x1="' . $x1 . '" y1="' . $y1 . '" x2="' . $x2 . '" y2="' . $y2 . '" stroke="' . $color . '" stroke-width="1.5"/>';
        }

        $palette = ['#0A64A4', '#06426e', '#1e40af', '#15803d', '#0f172a'];
        for ($i = 0; $i < 5; $i++) {
            $x = 18 + ($i * 25) + random_int(-2, 2);
            $y = 29 + random_int(-3, 3);
            $rot = random_int(-14, 14);
            $col = $palette[random_int(0, count($palette) - 1)];
            $svg .= '<text x="' . $x . '" y="' . $y . '" fill="' . $col . '" font-family="monospace, sans-serif" font-size="22" font-weight="bold" transform="rotate(' . $rot . ' ' . $x . ' ' . $y . ')">' . $code[$i] . '</text>';
        }

        $svg .= '</svg>';

        return $this->response
            ->setHeader('Content-Type', 'image/svg+xml')
            ->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->setBody($svg);
    }

    private function verifyCaptcha(string $form): bool
    {
        // Honeypot anti-bot: jika field tersembunyi diisi bot, tolak langsung
        if (trim((string) $this->request->getPost('website_hp')) !== '') {
            return false;
        }

        $session = session();
        $expected = strtoupper(trim((string) $session->get('captcha_' . $form)));
        $input = strtoupper(trim((string) $this->request->getPost('captcha')));

        if ($expected === '' || $input === '') {
            return false;
        }

        $session->remove('captcha_' . $form);
        return hash_equals($expected, $input);
    }

    public function login()
    {
        $next = $this->sanitizeRedirectPath($this->request->getGet('next'));
        if (session()->get('logged_in')) {
            if ($next !== '') {
                return $this->createWebSsoRedirect((int) session()->get('id'), $next);
            }
            return $this->redirectByRole((string) session()->get('role'));
        }
        return view('auth/login', ['is_register' => false, 'next' => $next]);
    }

    public function processLogin()
    {
        $session = session();
        $next = $this->sanitizeRedirectPath($this->request->getVar('next'));
        $throttler = \Config\Services::throttler();
        $throttleKey = 'login_' . md5((string) $this->request->getIPAddress());
        if ($throttler->check($throttleKey, 8, MINUTE) === false) {
            $session->setFlashdata('msg', 'Terlalu banyak percobaan login. Silakan coba lagi dalam 1 menit.');
            return redirect()->to('/auth/login' . ($next !== '' ? '?next=' . urlencode($next) : ''));
        }

        if (! $this->verifyCaptcha('login')) {
            $session->setFlashdata('msg', 'Kode CAPTCHA tidak sesuai atau sudah kedaluwarsa. Silakan coba lagi.');
            return redirect()->to('/auth/login' . ($next !== '' ? '?next=' . urlencode($next) : ''));
        }

        $model = new UserModel();
        $username = trim((string) $this->request->getVar('username'));
        $password = $this->request->getVar('password');

        $data = $model->groupStart()
            ->where('username', $username)
            ->orWhere('email', $username)
            ->groupEnd()
            ->first();

        if ($data) {
            $pass = $data['password'];
            $verify_pass = password_verify($password, $pass);
            if ($verify_pass) {
                if (in_array($data['role'], ['admin', 'super_admin'], true)) {
                    $normalizedRole = 'admin';
                } elseif ($data['role'] === 'mitra') {
                    $session->setFlashdata('msg', 'Akun ini adalah akun Mitra Kerja. Silakan login melalui Portal Mitra Kerja.');
                    return redirect()->to('/auth/login' . ($next !== '' ? '?next=' . urlencode($next) : ''));
                } else {
                    $normalizedRole = 'user';
                }

                $session->regenerate(true);
                $ses_data = [
                    'id'           => $data['id'],
                    'username'     => $data['username'],
                    'role'         => $normalizedRole,
                    'login_portal' => $normalizedRole === 'mitra' ? 'mitra' : 'anggota',
                    'logged_in'    => true,
                ];
                $session->set($ses_data);

                if ($next !== '') {
                    return $this->createWebSsoRedirect((int) $data['id'], $next);
                }

                if ($normalizedRole === 'mitra') {
                    $mitraModel = new \App\Models\MitraKerjaModel();
                    $hasMitra = $mitraModel->where('user_id', $data['id'])->first();
                    if (! $hasMitra) {
                        return redirect()->to('/mitra/onboarding');
                    }
                    return redirect()->to('/mitra');
                }
                if ($normalizedRole === 'user') {
                    $companyModel = new \App\Models\CompanyModel();
                    $hasCompany = $companyModel->where('user_id', $data['id'])->first();
                    if (! $hasCompany) {
                        return redirect()->to('/perusahaan/onboarding');
                    }
                    return redirect()->to('/perusahaan');
                }
                return redirect()->to('/dashboard');
            } else {
                $session->setFlashdata('msg', 'Username/email atau password salah.');
                return redirect()->to('/auth/login' . ($next !== '' ? '?next=' . urlencode($next) : ''));
            }
        } else {
            $session->setFlashdata('msg', 'Username/email atau password salah.');
            return redirect()->to('/auth/login' . ($next !== '' ? '?next=' . urlencode($next) : ''));
        }
    }

    public function register()
    {
        if (session()->get('logged_in')) {
            return $this->redirectByRole((string) session()->get('role'));
        }
        return view('auth/login', ['is_register' => true]);
    }

    public function processRegister()
    {
        $throttler = \Config\Services::throttler();
        $throttleKey = 'reg_' . md5((string) $this->request->getIPAddress());
        if ($throttler->check($throttleKey, 8, MINUTE) === false) {
            return view('auth/login', [
                'is_register'   => true,
                'registerError' => 'Terlalu banyak percobaan pendaftaran. Silakan tunggu 1 menit.',
            ]);
        }

        if (trim((string) $this->request->getPost('website_hp')) !== '') {
            return view('auth/login', [
                'is_register'   => true,
                'registerError' => 'Permintaan tidak valid.',
            ]);
        }

        $email = strtolower(trim((string) $this->request->getVar('email')));
        $verified = session()->get('otp_reg_verified_anggota');
        if (
            ! is_array($verified)
            || ($verified['email'] ?? '') !== $email
            || (time() - (int) ($verified['verified_at'] ?? 0)) > 900
        ) {
            return view('auth/login', [
                'is_register'   => true,
                'registerError' => 'Email belum terverifikasi OTP atau sesi verifikasi telah kedaluwarsa. Silakan ulangi verifikasi OTP.',
            ]);
        }

        $rules = [
            'email'        => 'required|valid_email|max_length[150]|is_unique[users.email]|is_unique[users.username]',
            'password'     => 'required|min_length[8]|max_length[100]',
            'confpassword' => 'matches[password]',
        ];

        if ($this->validate($rules)) {
            $model = new UserModel();
            $data = [
                'username' => $email,
                'email'    => $email,
                'name'     => $email,
                'role'     => 'user',
                'password' => password_hash((string) $this->request->getVar('password'), PASSWORD_DEFAULT),
            ];
            $model->save($data);
            $newUserId = (int) $model->getInsertID();

            $session = session();
            $session->remove('otp_reg_verified_anggota');
            $session->regenerate(true);
            $session->set([
                'id'           => $newUserId,
                'username'     => $email,
                'role'         => 'user',
                'login_portal' => 'anggota',
                'logged_in'    => true,
            ]);

            return redirect()->to('/perusahaan/onboarding')->with('success', 'Akun berhasil dibuat & email terverifikasi. Silakan lengkapi data perusahaan (PT) Anda.');
        } else {
            $data['validation'] = $this->validator;
            $data['is_register'] = true;
            return view('auth/login', $data);
        }
    }

    // ================= PORTAL LOGIN & REGISTER MITRA KERJA =================
    public function mitraLogin()
    {
        $next = $this->sanitizeRedirectPath($this->request->getGet('next'));
        if (session()->get('logged_in')) {
            if ($next !== '') {
                return $this->createWebSsoRedirect((int) session()->get('id'), $next);
            }
            return $this->redirectByRole((string) session()->get('role'));
        }
        return view('auth/mitra_login', ['is_register' => false, 'next' => $next]);
    }

    public function processMitraLogin()
    {
        $session = session();
        $next = $this->sanitizeRedirectPath($this->request->getVar('next'));
        $throttler = \Config\Services::throttler();
        $throttleKey = 'mitra_login_' . md5((string) $this->request->getIPAddress());
        if ($throttler->check($throttleKey, 8, MINUTE) === false) {
            $session->setFlashdata('msg', 'Terlalu banyak percobaan login. Silakan coba lagi dalam 1 menit.');
            return redirect()->to('/mitra/login' . ($next !== '' ? '?next=' . urlencode($next) : ''));
        }

        if (! $this->verifyCaptcha('mitra_login')) {
            $session->setFlashdata('msg', 'Kode CAPTCHA tidak sesuai atau sudah kedaluwarsa. Silakan coba lagi.');
            return redirect()->to('/mitra/login' . ($next !== '' ? '?next=' . urlencode($next) : ''));
        }

        $model = new UserModel();
        $username = trim((string) $this->request->getVar('username'));
        $password = $this->request->getVar('password');

        $data = $model->groupStart()
            ->where('username', $username)
            ->orWhere('email', $username)
            ->groupEnd()
            ->first();

        if ($data) {
            $verify_pass = password_verify($password, $data['password']);
            if ($verify_pass) {
                if (in_array($data['role'], ['admin', 'super_admin'], true)) {
                    $normalizedRole = 'admin';
                } elseif ($data['role'] === 'mitra') {
                    $normalizedRole = 'mitra';
                } else {
                    $session->setFlashdata('msg', 'Akun ini adalah akun Anggota. Silakan login melalui Portal Anggota.');
                    return redirect()->to('/mitra/login' . ($next !== '' ? '?next=' . urlencode($next) : ''));
                }

                $session->regenerate(true);
                $ses_data = [
                    'id'           => $data['id'],
                    'username'     => $data['username'],
                    'role'         => $normalizedRole,
                    'login_portal' => 'mitra',
                    'logged_in'    => true,
                ];
                $session->set($ses_data);

                if ($next !== '') {
                    return $this->createWebSsoRedirect((int) $data['id'], $next);
                }

                if ($normalizedRole === 'mitra') {
                    $mitraModel = new \App\Models\MitraKerjaModel();
                    $hasMitra = $mitraModel->where('user_id', $data['id'])->first();
                    if (! $hasMitra) {
                        return redirect()->to('/mitra/onboarding');
                    }
                }

                return redirect()->to('/mitra');
            } else {
                $session->setFlashdata('msg', 'Username/email atau password salah.');
                return redirect()->to('/mitra/login' . ($next !== '' ? '?next=' . urlencode($next) : ''));
            }
        } else {
            $session->setFlashdata('msg', 'Username/email atau password salah.');
            return redirect()->to('/mitra/login' . ($next !== '' ? '?next=' . urlencode($next) : ''));
        }
    }

    public function mitraRegister()
    {
        if (session()->get('logged_in')) {
            return $this->redirectByRole((string) session()->get('role'));
        }
        return view('auth/mitra_login', ['is_register' => true]);
    }

    public function processMitraRegister()
    {
        $throttler = \Config\Services::throttler();
        $throttleKey = 'mitra_reg_' . md5((string) $this->request->getIPAddress());
        if ($throttler->check($throttleKey, 8, MINUTE) === false) {
            return view('auth/mitra_login', [
                'is_register'   => true,
                'registerError' => 'Terlalu banyak percobaan pendaftaran. Silakan tunggu 1 menit.',
            ]);
        }

        if (trim((string) $this->request->getPost('website_hp')) !== '') {
            return view('auth/mitra_login', [
                'is_register'   => true,
                'registerError' => 'Permintaan tidak valid.',
            ]);
        }

        $email = strtolower(trim((string) $this->request->getVar('email')));
        $verified = session()->get('otp_reg_verified_mitra');
        if (
            ! is_array($verified)
            || ($verified['email'] ?? '') !== $email
            || (time() - (int) ($verified['verified_at'] ?? 0)) > 900
        ) {
            return view('auth/mitra_login', [
                'is_register'   => true,
                'registerError' => 'Email belum terverifikasi OTP atau sesi verifikasi telah kedaluwarsa. Silakan ulangi verifikasi OTP.',
            ]);
        }

        $rules = [
            'email'        => 'required|valid_email|max_length[150]|is_unique[users.email]|is_unique[users.username]',
            'password'     => 'required|min_length[8]|max_length[100]',
            'confpassword' => 'matches[password]',
        ];

        if ($this->validate($rules)) {
            $model = new UserModel();
            $data = [
                'username' => $email,
                'email'    => $email,
                'name'     => $email,
                'role'     => 'mitra',
                'password' => password_hash((string) $this->request->getVar('password'), PASSWORD_DEFAULT),
            ];
            $model->save($data);
            $newUserId = (int) $model->getInsertID();

            $session = session();
            $session->remove('otp_reg_verified_mitra');
            $session->regenerate(true);
            $session->set([
                'id'           => $newUserId,
                'username'     => $email,
                'role'         => 'mitra',
                'login_portal' => 'mitra',
                'logged_in'    => true,
            ]);

            return redirect()->to('/mitra/onboarding')->with('success', 'Akun Mitra Kerja berhasil dibuat & email terverifikasi. Silakan lengkapi profil perusahaan mitra Anda.');
        } else {
            $data['validation'] = $this->validator;
            $data['is_register'] = true;
            return view('auth/mitra_login', $data);
        }
    }

    // ================= EMAIL OTP: DAFTAR AKUN & LUPA PASSWORD =================

    public function sendRegisterOtp()
    {
        if (trim((string) $this->request->getPost('website_hp')) !== '') {
            return $this->response->setStatusCode(400)->setJSON(['ok' => false, 'message' => 'Permintaan ditolak.']);
        }

        $portal = strtolower(trim((string) $this->request->getPost('portal'))) === 'mitra' ? 'mitra' : 'anggota';
        $email  = strtolower(trim((string) $this->request->getPost('email')));

        $otpService = new EmailOtpService();
        if (! $otpService->isValidEmailAddress($email)) {
            return $this->response->setStatusCode(422)->setJSON([
                'ok'        => false,
                'message'   => 'Alamat email tidak valid atau domain email tidak ditemukan. Gunakan email aktif.',
                'csrf_hash' => csrf_hash(),
            ]);
        }

        $userModel = new UserModel();
        $exists = $userModel->groupStart()
            ->where('email', $email)
            ->orWhere('username', $email)
            ->groupEnd()
            ->first();

        if ($exists) {
            return $this->response->setStatusCode(409)->setJSON([
                'ok'        => false,
                'message'   => 'Email ini sudah terdaftar. Silakan masuk atau gunakan fitur Lupa Password.',
                'csrf_hash' => csrf_hash(),
            ]);
        }

        $subject = $portal === 'mitra' ? 'Kode OTP Pendaftaran Mitra Kerja' : 'Kode OTP Pendaftaran Anggota';
        $result = $otpService->sendOtp($email, 'register_' . $portal, (string) $this->request->getIPAddress(), $subject);
        $result['csrf_hash'] = csrf_hash();

        return $this->response->setStatusCode($result['ok'] ? 200 : 429)->setJSON($result);
    }

    public function verifyRegisterOtp()
    {
        $portal = strtolower(trim((string) $this->request->getPost('portal'))) === 'mitra' ? 'mitra' : 'anggota';
        $email  = strtolower(trim((string) $this->request->getPost('email')));
        $code   = trim((string) $this->request->getPost('otp'));

        $otpService = new EmailOtpService();
        $res = $otpService->verifyOtp($email, 'register_' . $portal, $code);

        if (! $res['ok']) {
            return $this->response->setStatusCode(422)->setJSON([
                'ok'        => false,
                'message'   => $res['message'],
                'csrf_hash' => csrf_hash(),
            ]);
        }

        session()->set('otp_reg_verified_' . $portal, [
            'email'       => $email,
            'verified_at' => time(),
        ]);

        return $this->response->setJSON([
            'ok'        => true,
            'email'     => $email,
            'message'   => 'Email berhasil diverifikasi! Silakan buat password baru Anda.',
            'csrf_hash' => csrf_hash(),
        ]);
    }

    public function sendForgotOtp()
    {
        $portal = strtolower(trim((string) $this->request->getPost('portal'))) === 'mitra' ? 'mitra' : 'anggota';
        $email  = strtolower(trim((string) $this->request->getPost('identifier')));

        if ($email === '' || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->response->setStatusCode(422)->setJSON([
                'ok'        => false,
                'message'   => 'Masukkan alamat email yang valid dan terdaftar (contoh: nama@domain.com).',
                'csrf_hash' => csrf_hash(),
            ]);
        }

        $userModel = new UserModel();
        $user = $userModel->where('email', $email)->first();

        if (! $user) {
            return $this->response->setStatusCode(404)->setJSON([
                'ok'        => false,
                'message'   => 'Alamat email tersebut tidak terdaftar di database sistem.',
                'csrf_hash' => csrf_hash(),
            ]);
        }

        $role = (string) ($user['role'] ?? 'user');
        if ($portal === 'mitra' && ! in_array($role, ['mitra', 'admin', 'super_admin'], true)) {
            return $this->response->setStatusCode(403)->setJSON([
                'ok'        => false,
                'message'   => 'Email ini terdaftar sebagai akun Anggota. Silakan lakukan Lupa Password melalui Portal Anggota.',
                'csrf_hash' => csrf_hash(),
            ]);
        }
        if ($portal === 'anggota' && $role === 'mitra') {
            return $this->response->setStatusCode(403)->setJSON([
                'ok'        => false,
                'message'   => 'Email ini terdaftar sebagai akun Mitra Kerja. Silakan lakukan Lupa Password melalui Portal Mitra Kerja.',
                'csrf_hash' => csrf_hash(),
            ]);
        }

        $targetEmail = strtolower(trim((string) $user['email']));
        $otpService = new EmailOtpService();
        $result = $otpService->sendOtp($targetEmail, 'forgot_password', (string) $this->request->getIPAddress(), 'Kode OTP Reset Password');
        $result['csrf_hash'] = csrf_hash();

        if ($result['ok']) {
            session()->set('otp_forgot_pending', [
                'user_id'    => (int) $user['id'],
                'email'      => $targetEmail,
                'portal'     => $portal,
                'created_at' => time(),
            ]);
        }

        return $this->response->setStatusCode($result['ok'] ? 200 : 429)->setJSON($result);
    }

    public function verifyForgotOtp()
    {
        $pending = session()->get('otp_forgot_pending');
        if (! is_array($pending) || empty($pending['email']) || (time() - (int) ($pending['created_at'] ?? 0)) > 600) {
            return $this->response->setStatusCode(422)->setJSON([
                'ok'        => false,
                'message'   => 'Sesi reset password kedaluwarsa. Silakan kirim ulang OTP.',
                'csrf_hash' => csrf_hash(),
            ]);
        }

        $code = trim((string) $this->request->getPost('otp'));
        $otpService = new EmailOtpService();
        $res = $otpService->verifyOtp((string) $pending['email'], 'forgot_password', $code);

        if (! $res['ok']) {
            return $this->response->setStatusCode(422)->setJSON([
                'ok'        => false,
                'message'   => $res['message'],
                'csrf_hash' => csrf_hash(),
            ]);
        }

        session()->remove('otp_forgot_pending');
        session()->set('otp_forgot_verified', [
            'user_id'     => (int) $pending['user_id'],
            'email'       => (string) $pending['email'],
            'verified_at' => time(),
        ]);

        return $this->response->setJSON([
            'ok'           => true,
            'masked_email' => EmailOtpService::maskEmail((string) $pending['email']),
            'message'      => 'Kode OTP valid! Silakan buat password baru Anda.',
            'csrf_hash'    => csrf_hash(),
        ]);
    }

    public function resetForgotPassword()
    {
        $verified = session()->get('otp_forgot_verified');
        if (! is_array($verified) || empty($verified['user_id']) || (time() - (int) ($verified['verified_at'] ?? 0)) > 600) {
            return $this->response->setStatusCode(422)->setJSON([
                'ok'        => false,
                'message'   => 'Sesi verifikasi reset password telah habis. Silakan ulangi dari awal.',
                'csrf_hash' => csrf_hash(),
            ]);
        }

        $password     = (string) $this->request->getPost('password');
        $confPassword = (string) $this->request->getPost('confpassword');

        if (strlen($password) < 8 || strlen($password) > 100) {
            return $this->response->setStatusCode(422)->setJSON([
                'ok'        => false,
                'message'   => 'Password baru minimal 8 karakter.',
                'csrf_hash' => csrf_hash(),
            ]);
        }

        if ($password !== $confPassword) {
            return $this->response->setStatusCode(422)->setJSON([
                'ok'        => false,
                'message'   => 'Konfirmasi password tidak cocok.',
                'csrf_hash' => csrf_hash(),
            ]);
        }

        $userModel = new UserModel();
        $userModel->update((int) $verified['user_id'], [
            'password' => password_hash($password, PASSWORD_DEFAULT),
        ]);

        session()->remove('otp_forgot_verified');

        return $this->response->setJSON([
            'ok'        => true,
            'message'   => 'Password berhasil diperbarui! Silakan login menggunakan password baru Anda.',
            'csrf_hash' => csrf_hash(),
        ]);
    }

    public function logout()
    {
        $session = session();
        $isMitraPortal = ($session->get('login_portal') === 'mitra' || $session->get('role') === 'mitra');
        $session->destroy();

        $webUrl = rtrim((string) config('App')->webProfileURL, '/');
        if ($this->request->getGet('redirect_web') === '1') {
            return redirect()->to($webUrl . '/');
        }

        $portal = $isMitraPortal ? 'mitra' : 'anggota';
        return redirect()->to($webUrl . '/sso/logout?from=sim&portal=' . $portal);
    }

    public function switchToWeb()
    {
        $session = session();
        if (!$session->get('logged_in') || !$session->get('id')) {
            return redirect()->to('/auth/login');
        }
        return $this->createWebSsoRedirect((int) $session->get('id'), '/');
    }

    public function sso()
    {
        $this->ensureSsoTable();
        $token = trim((string) $this->request->getGet('token'));
        if ($token === '') {
            return redirect()->to('/auth/login');
        }

        $db = \Config\Database::connect();
        $now = date('Y-m-d H:i:s');
        $row = $db->table('sso_tokens')
            ->where('token', hash('sha256', $token))
            ->where('target_app', 'sim')
            ->where('used_at IS NULL', null, false)
            ->where('expires_at >=', $now)
            ->get()
            ->getRowArray();

        if (!$row) {
            return redirect()->to('/auth/login')->with('msg', 'Token sesi tidak valid atau kedaluwarsa.');
        }

        $db->table('sso_tokens')
            ->where('id', $row['id'])
            ->where('used_at IS NULL', null, false)
            ->update(['used_at' => $now]);

        if ($db->affectedRows() === 0) {
            return redirect()->to('/auth/login');
        }

        $model = new UserModel();
        $user = $model->find($row['user_id']);
        if (!$user) {
            return redirect()->to('/auth/login');
        }

        if (in_array($user['role'], ['admin', 'super_admin'], true)) {
            $normalizedRole = 'admin';
        } elseif ($user['role'] === 'mitra') {
            $normalizedRole = 'mitra';
        } else {
            $normalizedRole = 'user';
        }

        $session = session();
        $session->regenerate(true);
        $session->set([
            'id'           => $user['id'],
            'username'     => $user['username'],
            'role'         => $normalizedRole,
            'login_portal' => $normalizedRole === 'mitra' ? 'mitra' : 'anggota',
            'logged_in'    => true,
        ]);

        if ($normalizedRole === 'mitra') {
            return redirect()->to('/mitra');
        }
        if ($normalizedRole === 'user') {
            return redirect()->to('/perusahaan');
        }
        return redirect()->to('/dashboard');
    }
}
