<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\CompanyModel;
use App\Models\SbuModel;
use App\Models\MitraKerjaModel;

class Users extends BaseController
{
    protected $userModel;
    protected $companyModel;
    protected $sbuModel;
    protected $mitraModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->companyModel = new CompanyModel();
        $this->sbuModel = new SbuModel();
        $this->mitraModel = new MitraKerjaModel();
    }

    public function index()
    {
        if (session()->get('role') !== 'admin') {
            return redirect()->to('/dashboard');
        }

        $data = [
            'title' => 'Manajemen Pengguna',
            'users' => $this->userModel->orderBy('id', 'ASC')->findAll(),
        ];
        return view('users/index', $data);
    }

    public function store()
    {
        if (session()->get('role') !== 'admin') {
            return redirect()->to('/dashboard');
        }

        $rules = [
            'username' => 'required|min_length[4]|max_length[100]|is_unique[users.username]',
            'email'    => 'permit_empty|valid_email|max_length[150]',
            'name'     => 'permit_empty|max_length[150]',
            'role'     => 'required|in_list[admin,user,mitra]',
            'password' => 'required|min_length[6]|max_length[100]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->to('/users')->with('error', implode(' ', $this->validator->getErrors()));
        }

        $username = trim((string) $this->request->getPost('username'));
        $email    = trim((string) $this->request->getPost('email'));
        $name     = trim((string) $this->request->getPost('name'));
        $role     = (string) $this->request->getPost('role');
        $password = (string) $this->request->getPost('password');

        if ($name === '') {
            $name = $email !== '' ? $email : $username;
        }

        $this->userModel->save([
            'username' => $username,
            'name'     => $name,
            'email'    => $email !== '' ? $email : null,
            'role'     => $role,
            'password' => password_hash($password, PASSWORD_DEFAULT),
        ]);

        session()->setFlashdata('pesan', 'Akun pengguna baru berhasil dibuat.');
        return redirect()->to('/users');
    }

    public function updateRole($id)
    {
        if (session()->get('role') !== 'admin') {
            return redirect()->to('/dashboard');
        }

        $id = (int) $id;
        if ($id === (int) session()->get('id')) {
            return redirect()->to('/users')->with('error', 'Anda tidak dapat mengubah role akun Anda sendiri yang sedang login.');
        }

        $user = $this->userModel->find($id);
        if (! $user) {
            return redirect()->to('/users')->with('error', 'Akun pengguna tidak ditemukan.');
        }

        $newRole = (string) $this->request->getPost('role');
        if (! in_array($newRole, ['admin', 'user', 'mitra'], true)) {
            return redirect()->to('/users')->with('error', 'Role yang dipilih tidak valid.');
        }

        $this->userModel->update($id, ['role' => $newRole]);
        session()->setFlashdata('pesan', 'Role akun "' . $user['username'] . '" berhasil diperbarui menjadi ' . $newRole . '.');
        return redirect()->to('/users');
    }

    public function delete($id)
    {
        if (session()->get('role') !== 'admin') {
            return redirect()->to('/dashboard');
        }

        $id = (int) $id;
        if ($id === (int) session()->get('id')) {
            return redirect()->to('/users')->with('error', 'Anda tidak dapat menghapus akun Anda sendiri yang sedang login.');
        }

        $userCompanies = $this->companyModel->where('user_id', $id)->findAll();
        foreach ($userCompanies as $comp) {
            $this->companyModel->deleteCompanyAndMember((int) $comp['id']);
        }
        $this->sbuModel->where('user_id', $id)->delete();
        $this->mitraModel->where('user_id', $id)->delete();

        $db = \Config\Database::connect();
        if ($db->tableExists('sso_tokens')) {
            $db->table('sso_tokens')->where('user_id', $id)->delete();
        }

        $this->userModel->delete($id);

        session()->setFlashdata('pesan', 'Akun pengguna dan seluruh datanya berhasil dihapus.');
        return redirect()->to('/users');
    }
}
