<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Libraries\EmailOtpService;
use App\Models\CompanyModel;
use App\Models\MitraKerjaModel;
use App\Models\SbuModel;
use App\Models\UserModel;

class Profile extends BaseController
{
    protected UserModel $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    private function ensureProfileColumns(): void
    {
        $db = \Config\Database::connect();
        try {
            $fields = array_map('strtolower', $db->getFieldNames('users'));
            if (! in_array('name', $fields, true)) {
                $db->query("ALTER TABLE `users` ADD COLUMN `name` VARCHAR(150) NULL DEFAULT NULL AFTER `username`");
            }
            if (! in_array('email', $fields, true)) {
                $db->query("ALTER TABLE `users` ADD COLUMN `email` VARCHAR(150) NULL DEFAULT NULL AFTER `name`");
            }
        } catch (\Throwable $e) {
            // Ignore if columns already exist
        }
    }

    public function index()
    {
        $role = (string) session()->get('role');
        if (! in_array($role, ['user', 'mitra'], true)) {
            return redirect()->to('/dashboard');
        }

        $this->ensureProfileColumns();
        $userId = (int) session()->get('id');
        $user = $this->userModel->find($userId);

        if (! $user) {
            return redirect()->to('/auth/logout');
        }

        return view('profile/index', [
            'title'       => 'Profil Akun',
            'user'        => $user,
            'maskedEmail' => EmailOtpService::maskEmail((string) ($user['email'] ?? $user['username'] ?? '')),
        ]);
    }

    public function update()
    {
        $role = (string) session()->get('role');
        if (! in_array($role, ['user', 'mitra'], true)) {
            return redirect()->to('/dashboard');
        }

        $this->ensureProfileColumns();
        $userId = (int) session()->get('id');
        $user = $this->userModel->find($userId);
        if (! $user) {
            return redirect()->to('/auth/logout');
        }

        $rules = [
            'name' => 'permit_empty|max_length[150]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->to('/profile')->with('error', implode(' ', $this->validator->getErrors()));
        }

        $existingEmail = trim((string) ($user['email'] ?? $user['username'] ?? ''));
        $nameInput = trim((string) $this->request->getPost('name'));

        // Email bersifat permanen (tidak diubah). Jika nama dikosongkan, default menggunakan email.
        $finalName = $nameInput !== '' ? $nameInput : $existingEmail;

        $this->userModel->update($userId, [
            'name' => $finalName,
        ]);

        session()->setFlashdata('pesan', 'Informasi profil berhasil diperbarui.');
        return redirect()->to('/profile');
    }

    public function sendPasswordOtp()
    {
        $role = (string) session()->get('role');
        if (! in_array($role, ['user', 'mitra'], true)) {
            return $this->response->setStatusCode(403)->setJSON(['ok' => false, 'message' => 'Akses ditolak.']);
        }

        $userId = (int) session()->get('id');
        $user = $this->userModel->find($userId);
        if (! $user) {
            return $this->response->setStatusCode(401)->setJSON(['ok' => false, 'message' => 'Sesi tidak valid.']);
        }

        $targetEmail = strtolower(trim((string) ($user['email'] ?? '')));
        if ($targetEmail === '' && filter_var((string) ($user['username'] ?? ''), FILTER_VALIDATE_EMAIL)) {
            $targetEmail = strtolower(trim((string) $user['username']));
        }

        if ($targetEmail === '' || ! filter_var($targetEmail, FILTER_VALIDATE_EMAIL)) {
            return $this->response->setStatusCode(422)->setJSON([
                'ok'        => false,
                'message'   => 'Harap lengkapi alamat email aktif pada Informasi Profil terlebih dahulu sebelum mengubah password.',
                'csrf_hash' => csrf_hash(),
            ]);
        }

        $otpService = new EmailOtpService();
        $result = $otpService->sendOtp($targetEmail, 'change_password', (string) $this->request->getIPAddress(), 'Kode OTP Ubah Password');
        $result['csrf_hash'] = csrf_hash();

        return $this->response->setStatusCode($result['ok'] ? 200 : 429)->setJSON($result);
    }

    public function updatePassword()
    {
        $role = (string) session()->get('role');
        if (! in_array($role, ['user', 'mitra'], true)) {
            return redirect()->to('/dashboard');
        }

        $userId = (int) session()->get('id');
        $user = $this->userModel->find($userId);
        if (! $user) {
            return redirect()->to('/auth/logout');
        }

        $rules = [
            'current_password' => 'required',
            'new_password'     => 'required|min_length[8]|max_length[100]',
            'confirm_password' => 'required|matches[new_password]',
            'otp_code'         => 'required|exact_length[6]|numeric',
        ];

        if (! $this->validate($rules)) {
            return redirect()->to('/profile')->with('error', implode(' ', $this->validator->getErrors()));
        }

        $currentPassword = (string) $this->request->getPost('current_password');
        $newPassword     = (string) $this->request->getPost('new_password');
        $otpCode         = trim((string) $this->request->getPost('otp_code'));

        if (! password_verify($currentPassword, (string) $user['password'])) {
            return redirect()->to('/profile')->with('error', 'Password lama yang Anda masukkan salah.');
        }

        $targetEmail = strtolower(trim((string) ($user['email'] ?? '')));
        if ($targetEmail === '' && filter_var((string) ($user['username'] ?? ''), FILTER_VALIDATE_EMAIL)) {
            $targetEmail = strtolower(trim((string) $user['username']));
        }

        $otpService = new EmailOtpService();
        $otpCheck = $otpService->verifyOtp($targetEmail, 'change_password', $otpCode);
        if (! $otpCheck['ok']) {
            return redirect()->to('/profile')->with('error', $otpCheck['message']);
        }

        $this->userModel->update($userId, [
            'password' => password_hash($newPassword, PASSWORD_DEFAULT),
        ]);

        session()->setFlashdata('pesan', 'Password akun berhasil diperbarui setelah verifikasi OTP.');
        return redirect()->to('/profile');
    }

    public function deleteAccount()
    {
        $role = (string) session()->get('role');
        if (! in_array($role, ['user', 'mitra'], true)) {
            return redirect()->to('/dashboard');
        }

        $userId = (int) session()->get('id');
        $user = $this->userModel->find($userId);
        if (! $user) {
            return redirect()->to('/auth/logout');
        }

        $companyModel = new CompanyModel();
        $sbuModel     = new SbuModel();
        $mitraModel   = new MitraKerjaModel();

        $userCompanies = $companyModel->where('user_id', $userId)->findAll();
        foreach ($userCompanies as $comp) {
            $companyModel->deleteCompanyAndMember((int) $comp['id']);
        }
        $sbuModel->where('user_id', $userId)->delete();
        $mitraModel->where('user_id', $userId)->delete();

        $db = \Config\Database::connect();
        if ($db->tableExists('sso_tokens')) {
            $db->table('sso_tokens')->where('user_id', $userId)->delete();
        }

        $this->userModel->delete($userId);

        $isMitraPortal = ($role === 'mitra' || session()->get('login_portal') === 'mitra');
        session()->destroy();

        $webUrl = rtrim((string) config('App')->webProfileURL, '/');
        $portal = $isMitraPortal ? 'mitra' : 'anggota';
        return redirect()->to($webUrl . '/sso/logout?from=sim&portal=' . $portal);
    }
}
