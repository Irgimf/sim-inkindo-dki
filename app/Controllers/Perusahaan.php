<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

use App\Models\CompanyModel;

class Perusahaan extends BaseController
{
    protected $companyModel;

    public function __construct()
    {
        $this->companyModel = new CompanyModel();
    }

    public function index()
    {
        $role = session()->get('role');
        $userId = session()->get('id');
        
        if ($role === 'admin') {
            $companies = $this->companyModel->findAll();
        } else {
            $companies = $this->companyModel->where('user_id', $userId)->findAll();
        }

        $data = [
            'title' => 'Data Perusahaan',
            'companies' => $companies
        ];
        return view('perusahaan/index', $data);
    }

    public function onboarding()
    {
        $role = session()->get('role');
        $userId = session()->get('id');

        if ($role === 'user') {
            $existing = $this->companyModel->where('user_id', $userId)->first();
            if ($existing) {
                return redirect()->to('/perusahaan');
            }
        }

        $userModel = new \App\Models\UserModel();
        $user = $userId ? $userModel->find($userId) : null;

        return view('perusahaan/onboarding', [
            'title'     => 'Lengkapi Data Perusahaan',
            'userEmail' => $user['email'] ?? session()->get('username') ?? '',
        ]);
    }

    public function create()
    {
        $role = session()->get('role');
        $userId = session()->get('id');
        
        if ($role === 'user') {
            $existing = $this->companyModel->where('user_id', $userId)->first();
            if ($existing) {
                session()->setFlashdata('pesan', 'Anda sudah memiliki data perusahaan. Satu akun hanya untuk satu perusahaan.');
                return redirect()->to('/perusahaan');
            }
        }

        $data = [
            'title' => 'Tambah Data Perusahaan'
        ];
        return view('perusahaan/create', $data);
    }

    public function store()
    {
        $role   = session()->get('role');
        $userId = session()->get('id');

        if ($role === 'user') {
            $existing = $this->companyModel->where('user_id', $userId)->first();
            if ($existing) {
                session()->setFlashdata('pesan', 'Anda sudah memiliki data perusahaan. Satu akun hanya untuk satu perusahaan.');
                return redirect()->to('/perusahaan');
            }
        }

        $rules = [
            'nama_pt'      => 'required|max_length[255]',
            'no_kta'       => 'required|max_length[100]',
            'kualifikasi'  => 'required|in_list[Besar,Menengah,Kecil]',
            'kategori_sbu' => 'required|max_length[100]',
            'alamat_pt'    => 'required|max_length[1000]',
        ];
        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', implode(' ', $this->validator->getErrors()));
        }

        $this->companyModel->save([
            'user_id'      => $userId,
            'nama_pt'      => trim((string) $this->request->getVar('nama_pt')),
            'nama_pjbu'    => trim((string) $this->request->getVar('nama_pjbu')) ?: '-',
            'no_kta'       => trim((string) $this->request->getVar('no_kta')),
            'status'       => trim((string) $this->request->getVar('status')) ?: 'Penuh',
            'kualifikasi'  => trim((string) $this->request->getVar('kualifikasi')),
            'wilayah'      => trim((string) $this->request->getVar('wilayah')) ?: 'Jakarta Selatan',
            'kategori_sbu' => trim((string) $this->request->getVar('kategori_sbu')),
            'alamat_pt'    => trim((string) $this->request->getVar('alamat_pt')),
            'alamat_surat' => trim((string) $this->request->getVar('alamat_surat')) ?: trim((string) $this->request->getVar('alamat_pt')),
            'telepon'      => trim((string) $this->request->getVar('telepon')) ?: '-',
            'fax'          => trim((string) $this->request->getVar('fax')) ?: '-',
            'email'        => trim((string) $this->request->getVar('email')) ?: '-',
            'website'      => trim((string) $this->request->getVar('website')) ?: '-',
        ]);

        $newCompanyId = (int) $this->companyModel->getInsertID();
        if ($newCompanyId > 0) {
            $this->companyModel->syncCompanyToMember($newCompanyId);
        }

        session()->setFlashdata('pesan', 'Data berhasil ditambahkan dan disinkronkan ke Direktori Anggota.');
        return redirect()->to('/perusahaan');
    }

    public function edit($id)
    {
        $company = $this->companyModel->find($id);
        if (! $company) {
            return redirect()->to('/perusahaan');
        }
        
        if (session()->get('role') === 'user' && $company['user_id'] != session()->get('id')) {
            return redirect()->to('/perusahaan');
        }

        $data = [
            'title' => 'Edit Data Perusahaan',
            'company' => $company
        ];
        return view('perusahaan/edit', $data);
    }

    public function update($id)
    {
        $company = $this->companyModel->find($id);
        if (! $company) {
            return redirect()->to('/perusahaan');
        }
        if (session()->get('role') === 'user' && $company['user_id'] != session()->get('id')) {
            return redirect()->to('/perusahaan');
        }

        $rules = [
            'nama_pt'      => 'required|max_length[255]',
            'no_kta'       => 'required|max_length[100]',
            'kualifikasi'  => 'required|in_list[Besar,Menengah,Kecil]',
            'kategori_sbu' => 'required|max_length[100]',
            'alamat_pt'    => 'required|max_length[1000]',
        ];
        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', implode(' ', $this->validator->getErrors()));
        }

        $this->companyModel->update($id, [
            'nama_pt'      => trim((string) $this->request->getVar('nama_pt')),
            'nama_pjbu'    => trim((string) $this->request->getVar('nama_pjbu')) ?: '-',
            'no_kta'       => trim((string) $this->request->getVar('no_kta')),
            'status'       => trim((string) $this->request->getVar('status')) ?: ($company['status'] ?? 'Penuh'),
            'kualifikasi'  => trim((string) $this->request->getVar('kualifikasi')),
            'wilayah'      => trim((string) $this->request->getVar('wilayah')) ?: ($company['wilayah'] ?? 'Jakarta Selatan'),
            'kategori_sbu' => trim((string) $this->request->getVar('kategori_sbu')),
            'alamat_pt'    => trim((string) $this->request->getVar('alamat_pt')),
            'alamat_surat' => trim((string) $this->request->getVar('alamat_surat')) ?: trim((string) $this->request->getVar('alamat_pt')),
            'telepon'      => trim((string) $this->request->getVar('telepon')) ?: '-',
            'fax'          => trim((string) $this->request->getVar('fax')) ?: '-',
            'email'        => trim((string) $this->request->getVar('email')) ?: '-',
            'website'      => trim((string) $this->request->getVar('website')) ?: '-',
        ]);

        $this->companyModel->syncCompanyToMember((int) $id);

        session()->setFlashdata('pesan', 'Data berhasil diubah dan disinkronkan ke Direktori Anggota.');
        return redirect()->to('/perusahaan');
    }

    public function delete($id)
    {
        $company = $this->companyModel->find($id);
        if (! $company) {
            return redirect()->to('/perusahaan');
        }
        if (session()->get('role') === 'user' && $company['user_id'] != session()->get('id')) {
            return redirect()->to('/perusahaan');
        }

        $this->companyModel->deleteCompanyAndMember((int) $id);
        session()->setFlashdata('pesan', 'Data berhasil dihapus.');
        return redirect()->to('/perusahaan');
    }
}
