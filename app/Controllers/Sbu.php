<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

use App\Models\SbuModel;
use App\Models\CompanyModel;
use App\Models\SbuProjectModel;

class Sbu extends BaseController
{
    protected $sbuModel;
    protected $companyModel;
    protected $sbuProjectModel;

    public function __construct()
    {
        $this->sbuModel = new SbuModel();
        $this->companyModel = new CompanyModel();
        $this->sbuProjectModel = new SbuProjectModel();
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
            'title' => 'Data SBU (Perusahaan)',
            'companies' => $companies
        ];
        return view('sbu/index', $data);
    }

    public function detail($company_id)
    {
        $role = session()->get('role');
        $currentUserId = session()->get('id');

        $company = $this->companyModel->find($company_id);
        if (!$company) {
            session()->setFlashdata('pesan', 'Data Perusahaan tidak ditemukan.');
            return redirect()->to('/sbu');
        }

        if ($role === 'user' && $company['user_id'] != $currentUserId) {
            return redirect()->to('/sbu');
        }

        // Ambil sbu berdasarkan company_id (kalau belum diupdate company_id-nya di db, maka fallback ke user_id)
        $sbus = $this->sbuModel->where('company_id', $company_id)->findAll();
        if (empty($sbus)) {
             // Fallback to user_id just in case of old data (but this might show all admin's SBUs)
             // Only fallback if the company has user_id and it's an old record.
             // For strict correctness, we should only rely on company_id now.
             // But to prevent data "disappearing" temporarily for users, let's keep it checking company_id.
             // Actually, wait, let's just use company_id. 
        }

        // Ambil semua proyek untuk dimasukkan ke view
        $allProjects = $this->sbuProjectModel->findAll();
        $projectsBySbu = [];
        foreach ($allProjects as $p) {
            $projectsBySbu[$p['sbu_id']][] = $p;
        }

        $data = [
            'title' => 'Detail SBU - ' . $company['nama_pt'],
            'company' => $company,
            'sbus' => $sbus,
            'projectsBySbu' => $projectsBySbu,
            'companyId' => $company_id
        ];
        return view('sbu/detail', $data);
    }

    public function store()
    {
        $role = session()->get('role');
        $companyId = $this->request->getVar('company_id'); // Hidden field on the form
        
        $company = $this->companyModel->find($companyId);
        if (!$company) return redirect()->to('/sbu');

        // Cek permission user
        if ($role === 'user' && $company['user_id'] != session()->get('id')) {
            return redirect()->to('/sbu');
        }
        
        $this->sbuModel->save([
            'user_id'     => $company['user_id'], // Keep user_id for backward compatibility/DB constraint
            'company_id'  => $companyId,
            'kode_kbli'   => $this->request->getVar('kode_kbli'),
            'judul_kbli'  => $this->request->getVar('judul_kbli'),
            'kualifikasi' => $this->request->getVar('kualifikasi'),
            'masa_berlaku'=> $this->request->getVar('masa_berlaku')
        ]);
        $this->companyModel->syncCompanyToMember((int) $companyId);
        session()->setFlashdata('pesan', 'Data SBU berhasil ditambahkan.');
        return redirect()->to('/sbu/detail/' . $companyId);
    }

    public function update($id)
    {
        $sbu = $this->sbuModel->find($id);
        if (!$sbu) return redirect()->to('/sbu');

        $role = session()->get('role');
        if ($role === 'user' && $sbu['user_id'] != session()->get('id')) {
            return redirect()->to('/sbu');
        }

        $this->sbuModel->update($id, [
            'kode_kbli'   => $this->request->getVar('kode_kbli'),
            'judul_kbli'  => $this->request->getVar('judul_kbli'),
            'kualifikasi' => $this->request->getVar('kualifikasi'),
            'masa_berlaku'=> $this->request->getVar('masa_berlaku')
        ]);
        $this->companyModel->syncCompanyToMember((int) $sbu['company_id']);
        session()->setFlashdata('pesan', 'Data SBU berhasil diubah.');
        return redirect()->to('/sbu/detail/' . $sbu['company_id']);
    }

    public function projects($sbu_id)
    {
        $sbu = $this->sbuModel->find($sbu_id);
        if (!$sbu) return redirect()->to('/sbu');

        if (session()->get('role') === 'user' && $sbu['user_id'] != session()->get('id')) {
            return redirect()->to('/sbu');
        }
        
        $company = $this->companyModel->find($sbu['company_id']);
        if (!$company) {
            $company = $this->companyModel->where('user_id', $sbu['user_id'])->first();
        }

        $projects = $this->sbuProjectModel->where('sbu_id', $sbu_id)->findAll();
        
        $data = [
            'title' => 'Daftar Proyek - ' . $sbu['kode_kbli'],
            'sbu' => $sbu,
            'company' => $company,
            'projects' => $projects
        ];

        return view('sbu/projects', $data);
    }

    public function delete($id)
    {
        $sbu = $this->sbuModel->find($id);
        if (!$sbu) return redirect()->to('/sbu');

        if (session()->get('role') === 'user' && $sbu['user_id'] != session()->get('id')) {
            return redirect()->to('/sbu');
        }

        $companyId = (int) $sbu['company_id'];
        // Hapus proyek terkait terlebih dahulu
        $this->sbuProjectModel->where('sbu_id', $id)->delete();
        $this->sbuModel->delete($id);
        $this->companyModel->syncCompanyToMember($companyId);
        session()->setFlashdata('pesan', 'Data SBU berhasil dihapus.');
        return redirect()->to('/sbu/detail/' . $companyId);
    }

    // --- MANAJEMEN PROYEK SBU ---

    public function storeProject($sbu_id)
    {
        $sbu = $this->sbuModel->find($sbu_id);
        if (!$sbu || (session()->get('role') === 'user' && $sbu['user_id'] != session()->get('id'))) {
            return redirect()->to('/sbu');
        }

        $this->sbuProjectModel->save([
            'sbu_id'         => $sbu_id,
            'nama_pekerjaan' => $this->request->getVar('nama_pekerjaan'),
            'nilai_proyek'   => $this->request->getVar('nilai_proyek'),
            'tahun_proyek'   => $this->request->getVar('tahun_proyek'),
            'lama_proyek'    => $this->request->getVar('lama_proyek')
        ]);
        
        session()->setFlashdata('pesan', 'Data Pekerjaan berhasil ditambahkan.');
        return redirect()->to('/sbu/projects/' . $sbu_id);
    }

    public function updateProject($id)
    {
        $project = $this->sbuProjectModel->find($id);
        if (!$project) return redirect()->to('/sbu');

        $sbu = $this->sbuModel->find($project['sbu_id']);
        if (session()->get('role') === 'user' && $sbu['user_id'] != session()->get('id')) {
            return redirect()->to('/sbu');
        }

        $this->sbuProjectModel->update($id, [
            'nama_pekerjaan' => $this->request->getVar('nama_pekerjaan'),
            'nilai_proyek'   => $this->request->getVar('nilai_proyek'),
            'tahun_proyek'   => $this->request->getVar('tahun_proyek'),
            'lama_proyek'    => $this->request->getVar('lama_proyek')
        ]);

        session()->setFlashdata('pesan', 'Data Pekerjaan berhasil diubah.');
        return redirect()->to('/sbu/projects/' . $sbu['id']);
    }

    public function deleteProject($id)
    {
        $project = $this->sbuProjectModel->find($id);
        if (!$project) return redirect()->to('/sbu');

        $sbu = $this->sbuModel->find($project['sbu_id']);
        if (session()->get('role') === 'user' && $sbu['user_id'] != session()->get('id')) {
            return redirect()->to('/sbu');
        }

        $this->sbuProjectModel->delete($id);
        session()->setFlashdata('pesan', 'Data Pekerjaan berhasil dihapus.');
        return redirect()->to('/sbu/projects/' . $sbu['id']);
    }
}
