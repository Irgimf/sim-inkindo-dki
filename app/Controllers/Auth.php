<?php

namespace App\Controllers;

use App\Controllers\BaseController;
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

        $model = new UserModel();
        $username = $this->request->getVar('username');
        $password = $this->request->getVar('password');

        $data = $model->where('username', $username)->first();

        if ($data) {
            $pass = $data['password'];
            $verify_pass = password_verify($password, $pass);
            if ($verify_pass) {
                if (in_array($data['role'], ['admin', 'super_admin'], true)) {
                    $normalizedRole = 'admin';
                } elseif ($data['role'] === 'mitra') {
                    $normalizedRole = 'mitra';
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
                    return redirect()->to('/mitra');
                }
                if ($normalizedRole === 'user') {
                    return redirect()->to('/perusahaan');
                }
                return redirect()->to('/dashboard');
            } else {
                $session->setFlashdata('msg', 'Password Salah');
                return redirect()->to('/auth/login' . ($next !== '' ? '?next=' . urlencode($next) : ''));
            }
        } else {
            $session->setFlashdata('msg', 'Username tidak ditemukan');
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
        $rules = [
            'username'     => 'required|min_length[4]|max_length[100]|is_unique[users.username]',
            'password'     => 'required|min_length[6]',
            'confpassword' => 'matches[password]',
        ];

        if ($this->validate($rules)) {
            $model = new UserModel();
            $username = $this->request->getVar('username');
            $data = [
                'username' => $username,
                'name'     => $username,
                'role'     => 'user',
                'password' => password_hash($this->request->getVar('password'), PASSWORD_DEFAULT),
            ];
            $model->save($data);
            return redirect()->to('/auth/login')->with('success', 'Registrasi Anggota berhasil. Silakan login.');
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

        $model = new UserModel();
        $username = $this->request->getVar('username');
        $password = $this->request->getVar('password');

        $data = $model->where('username', $username)->first();

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

                return redirect()->to('/mitra');
            } else {
                $session->setFlashdata('msg', 'Password Salah');
                return redirect()->to('/mitra/login' . ($next !== '' ? '?next=' . urlencode($next) : ''));
            }
        } else {
            $session->setFlashdata('msg', 'Username Mitra Kerja tidak ditemukan');
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
        $rules = [
            'username'     => 'required|min_length[4]|max_length[100]|is_unique[users.username]',
            'password'     => 'required|min_length[6]',
            'confpassword' => 'matches[password]',
        ];

        if ($this->validate($rules)) {
            $model = new UserModel();
            $username = $this->request->getVar('username');
            $data = [
                'username' => $username,
                'name'     => $username,
                'role'     => 'mitra',
                'password' => password_hash($this->request->getVar('password'), PASSWORD_DEFAULT),
            ];
            $model->save($data);
            return redirect()->to('/mitra/login')->with('success', 'Registrasi Mitra Kerja berhasil. Silakan login untuk mengelola profil & produk mitra Anda.');
        } else {
            $data['validation'] = $this->validator;
            $data['is_register'] = true;
            return view('auth/mitra_login', $data);
        }
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
