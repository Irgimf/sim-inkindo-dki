<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\CompanyModel;

class Dashboard extends BaseController
{
    public function index()
    {
        if (session()->get('role') === 'user') {
            return redirect()->to('/perusahaan');
        }

        $companyModel = new CompanyModel();
        $companies = $companyModel->findAll();

        $non_jk = [];
        $jk = [];
        $jk_dan_non_jk = [];
        $tidak_punya = [];

        foreach ($companies as $c) {
            if ($c['kategori_sbu'] == 'Non Jasa Konstruksi') {
                $non_jk[] = $c;
            } elseif ($c['kategori_sbu'] == 'Jasa Konstruksi') {
                $jk[] = $c;
            } elseif ($c['kategori_sbu'] == 'NJK dan JK' || $c['kategori_sbu'] == 'Jasa Konstruksi dan Non Jasa Konstruksi') {
                $jk_dan_non_jk[] = $c;
            } else {
                $tidak_punya[] = $c;
            }
        }

        $data = [
            'title' => 'Dashboard',
            'non_jk' => $non_jk,
            'jk' => $jk,
            'jk_dan_non_jk' => $jk_dan_non_jk,
            'tidak_punya' => $tidak_punya
        ];
        return view('dashboard/welcome', $data);
    }
}
