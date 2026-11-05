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
        $userId = session()->get('id');
        
        $this->companyModel->save([
            'user_id'      => $userId,
            'nama_pt'      => $this->request->getVar('nama_pt'),
            'nama_pjbu'    => $this->request->getVar('nama_pjbu') ?: '-',
            'no_kta'       => $this->request->getVar('no_kta'),
            'status'       => $this->request->getVar('status') ?: 'Penuh',
            'kualifikasi'  => $this->request->getVar('kualifikasi'),
            'wilayah'      => $this->request->getVar('wilayah') ?: 'Jakarta Selatan',
            'kategori_sbu' => $this->request->getVar('kategori_sbu'),
            'alamat_pt'    => $this->request->getVar('alamat_pt'),
            'alamat_surat' => $this->request->getVar('alamat_surat') ?: $this->request->getVar('alamat_pt'),
            'telepon'      => $this->request->getVar('telepon') ?: '-',
            'fax'          => $this->request->getVar('fax') ?: '-',
            'email'        => $this->request->getVar('email') ?: '-',
            'website'      => $this->request->getVar('website') ?: '-',
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
        if (session()->get('role') === 'user' && $company['user_id'] != session()->get('id')) {
            return redirect()->to('/perusahaan');
        }

        $this->companyModel->update($id, [
            'nama_pt'      => $this->request->getVar('nama_pt'),
            'nama_pjbu'    => $this->request->getVar('nama_pjbu') ?: '-',
            'no_kta'       => $this->request->getVar('no_kta'),
            'status'       => $this->request->getVar('status') ?: ($company['status'] ?? 'Penuh'),
            'kualifikasi'  => $this->request->getVar('kualifikasi'),
            'wilayah'      => $this->request->getVar('wilayah') ?: ($company['wilayah'] ?? 'Jakarta Selatan'),
            'kategori_sbu' => $this->request->getVar('kategori_sbu'),
            'alamat_pt'    => $this->request->getVar('alamat_pt'),
            'alamat_surat' => $this->request->getVar('alamat_surat') ?: $this->request->getVar('alamat_pt'),
            'telepon'      => $this->request->getVar('telepon') ?: '-',
            'fax'          => $this->request->getVar('fax') ?: '-',
            'email'        => $this->request->getVar('email') ?: '-',
            'website'      => $this->request->getVar('website') ?: '-',
        ]);

        $this->companyModel->syncCompanyToMember((int) $id);

        session()->setFlashdata('pesan', 'Data berhasil diubah dan disinkronkan ke Direktori Anggota.');
        return redirect()->to('/perusahaan');
    }

    public function delete($id)
    {
        $company = $this->companyModel->find($id);
        if (session()->get('role') === 'user' && $company['user_id'] != session()->get('id')) {
            return redirect()->to('/perusahaan');
        }

        $this->companyModel->deleteCompanyAndMember((int) $id);
        session()->setFlashdata('pesan', 'Data berhasil dihapus.');
        return redirect()->to('/perusahaan');
    }
}
