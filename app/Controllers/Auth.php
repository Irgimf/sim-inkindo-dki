<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;

class Auth extends BaseController
{
    public function login()
    {
        return view('auth/login', ['is_register' => false]);
    }

    public function processLogin()
    {
        $session = session();
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

                $ses_data = [
                    'id'           => $data['id'],
                    'username'     => $data['username'],
                    'role'         => $normalizedRole,
                    'login_portal' => $normalizedRole === 'mitra' ? 'mitra' : 'anggota',
                    'logged_in'    => true,
                ];
                $session->set($ses_data);

                if ($normalizedRole === 'mitra') {
                    return redirect()->to('/mitra');
                }
                if ($normalizedRole === 'user') {
                    return redirect()->to('/perusahaan');
                }
                return redirect()->to('/dashboard');
            } else {
                $session->setFlashdata('msg', 'Password Salah');
                return redirect()->to('/auth/login');
            }
        } else {
            $session->setFlashdata('msg', 'Username tidak ditemukan');
            return redirect()->to('/auth/login');
        }
    }

    public function register()
    {
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
        return view('auth/mitra_login', ['is_register' => false]);
    }

    public function processMitraLogin()
    {
        $session = session();
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
                    return redirect()->to('/mitra/login');
                }

                $ses_data = [
                    'id'           => $data['id'],
                    'username'     => $data['username'],
                    'role'         => $normalizedRole,
                    'login_portal' => 'mitra',
                    'logged_in'    => true,
                ];
                $session->set($ses_data);

                return redirect()->to('/mitra');
            } else {
                $session->setFlashdata('msg', 'Password Salah');
                return redirect()->to('/mitra/login');
            }
        } else {
            $session->setFlashdata('msg', 'Username Mitra Kerja tidak ditemukan');
            return redirect()->to('/mitra/login');
        }
    }

    public function mitraRegister()
    {
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
        return redirect()->to($isMitraPortal ? '/mitra/login' : '/auth/login');
    }
}
