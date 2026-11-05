<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class Pencarian extends BaseController
{
    public function index()
    {
        if (session()->get('role') !== 'admin') {
            return redirect()->to('/dashboard');
        }

        $db = \Config\Database::connect();
        
        $nama_pt = $this->request->getGet('nama_pt');
        $kualifikasi = $this->request->getGet('kualifikasi');
        $kategori_sbu = $this->request->getGet('kategori_sbu');
        $kode_kbli = $this->request->getGet('kode_kbli');
        $nilai_proyek_min = $this->request->getGet('nilai_proyek_min');
        $nilai_proyek_max = $this->request->getGet('nilai_proyek_max');

        // Get list of KBLI for dropdown
        $kbli_list = $db->table('sbu')
            ->select('kode_kbli, judul_kbli')
            ->where('kode_kbli !=', '')
            ->where('kode_kbli IS NOT NULL')
            ->groupBy('kode_kbli')
            ->get()->getResultArray();

        $builder = $db->table('companies c');
        $builder->select('c.nama_pt, c.kualifikasi as c_kualifikasi, c.kategori_sbu, s.kode_kbli, s.judul_kbli, p.nama_pekerjaan, p.nilai_proyek');
        $builder->join('sbu s', 's.company_id = c.id', 'left');
        $builder->join('sbu_projects p', 'p.sbu_id = s.id', 'left');

        if (!empty($nama_pt)) {
            $builder->like('c.nama_pt', $nama_pt);
        }
        if (!empty($kualifikasi)) {
            $builder->where('c.kualifikasi', $kualifikasi);
        }
        if (!empty($kategori_sbu)) {
            $builder->where('c.kategori_sbu', $kategori_sbu);
        }
        if (!empty($kode_kbli)) {
            $builder->where('s.kode_kbli', $kode_kbli);
        }
        
        $results = $builder->get()->getResultArray();

        if ($nilai_proyek_min !== '' && $nilai_proyek_min !== null || $nilai_proyek_max !== '' && $nilai_proyek_max !== null) {
            $min_val = preg_replace('/[^0-9]/', '', $nilai_proyek_min ?? '');
            $max_val = preg_replace('/[^0-9]/', '', $nilai_proyek_max ?? '');
            
            $filtered = [];
            foreach ($results as $row) {
                $row_val_str = preg_replace('/[^0-9]/', '', $row['nilai_proyek'] ?? '');
                $row_val = $row_val_str !== '' ? (float) $row_val_str : 0;
                
                $passMin = ($min_val === '') || ($row_val >= (float) $min_val);
                $passMax = ($max_val === '') || ($row_val <= (float) $max_val);
                
                if ($passMin && $passMax) {
                    $filtered[] = $row;
                }
            }
            $results = $filtered;
        }

        // Sort by nilai_proyek descending
        usort($results, function($a, $b) {
            $valA = (float) preg_replace('/[^0-9.]/', '', $a['nilai_proyek'] ?? '0');
            $valB = (float) preg_replace('/[^0-9.]/', '', $b['nilai_proyek'] ?? '0');
            return $valB <=> $valA;
        });

        $data = [
            'title' => 'Pencarian Khusus',
            'results' => $results,
            'kbli_list' => $kbli_list,
            'filters' => [
                'nama_pt' => $nama_pt,
                'kualifikasi' => $kualifikasi,
                'kategori_sbu' => $kategori_sbu,
                'kode_kbli' => $kode_kbli,
                'nilai_proyek_min' => $nilai_proyek_min,
                'nilai_proyek_max' => $nilai_proyek_max
            ]
        ];

        return view('pencarian/index', $data);
    }
    
    public function exportExcel()
    {
        if (session()->get('role') !== 'admin') {
            return redirect()->to('/dashboard');
        }

        $db = \Config\Database::connect();
        
        $nama_pt = $this->request->getGet('nama_pt');
        $kualifikasi = $this->request->getGet('kualifikasi');
        $kategori_sbu = $this->request->getGet('kategori_sbu');
        $kode_kbli = $this->request->getGet('kode_kbli');
        $nilai_proyek_min = $this->request->getGet('nilai_proyek_min');
        $nilai_proyek_max = $this->request->getGet('nilai_proyek_max');

        $builder = $db->table('companies c');
        $builder->select('c.nama_pt, c.kualifikasi as c_kualifikasi, c.kategori_sbu, s.kode_kbli, s.judul_kbli, p.nama_pekerjaan, p.nilai_proyek');
        $builder->join('sbu s', 's.company_id = c.id', 'left');
        $builder->join('sbu_projects p', 'p.sbu_id = s.id', 'left');

        if (!empty($nama_pt)) {
            $builder->like('c.nama_pt', $nama_pt);
        }
        if (!empty($kualifikasi)) {
            $builder->where('c.kualifikasi', $kualifikasi);
        }
        if (!empty($kategori_sbu)) {
            $builder->where('c.kategori_sbu', $kategori_sbu);
        }
        if (!empty($kode_kbli)) {
            $builder->where('s.kode_kbli', $kode_kbli);
        }
        
        $results = $builder->get()->getResultArray();

        if ($nilai_proyek_min !== '' && $nilai_proyek_min !== null || $nilai_proyek_max !== '' && $nilai_proyek_max !== null) {
            $min_val = preg_replace('/[^0-9]/', '', $nilai_proyek_min ?? '');
            $max_val = preg_replace('/[^0-9]/', '', $nilai_proyek_max ?? '');
            
            $filtered = [];
            foreach ($results as $row) {
                $row_val_str = preg_replace('/[^0-9]/', '', $row['nilai_proyek'] ?? '');
                $row_val = $row_val_str !== '' ? (float) $row_val_str : 0;
                
                $passMin = ($min_val === '') || ($row_val >= (float) $min_val);
                $passMax = ($max_val === '') || ($row_val <= (float) $max_val);
                
                if ($passMin && $passMax) {
                    $filtered[] = $row;
                }
            }
            $results = $filtered;
        }

        usort($results, function($a, $b) {
            $valA = (float) preg_replace('/[^0-9.]/', '', $a['nilai_proyek'] ?? '0');
            $valB = (float) preg_replace('/[^0-9.]/', '', $b['nilai_proyek'] ?? '0');
            return $valB <=> $valA;
        });
        
        $filename = "Export_Data_Pencarian_" . date('Ymd_His') . ".xls";
        
        header("Content-Type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=\"$filename\"");
        
        echo "<table border='1'>";
        echo "<tr>";
        echo "<th>No</th>";
        echo "<th>Nama PT</th>";
        echo "<th>Kualifikasi</th>";
        echo "<th>Kategori SBU</th>";
        echo "<th>Kode KBLI</th>";
        echo "<th>Judul KBLI</th>";
        echo "<th>Nama Pekerjaan</th>";
        echo "<th>Nilai Proyek</th>";
        echo "</tr>";
        
        $no = 1;
        foreach ($results as $row) {
            echo "<tr>";
            echo "<td>" . $no++ . "</td>";
            echo "<td>" . htmlspecialchars($row['nama_pt'] ?? '') . "</td>";
            echo "<td>" . htmlspecialchars($row['c_kualifikasi'] ?? '') . "</td>";
            echo "<td>" . htmlspecialchars($row['kategori_sbu'] ?? '') . "</td>";
            echo "<td>" . htmlspecialchars($row['kode_kbli'] ?? '') . "</td>";
            echo "<td>" . htmlspecialchars($row['judul_kbli'] ?? '') . "</td>";
            echo "<td>" . htmlspecialchars($row['nama_pekerjaan'] ?? '') . "</td>";
            echo "<td>" . htmlspecialchars($row['nilai_proyek'] ?? '') . "</td>";
            echo "</tr>";
        }
        
        echo "</table>";
        exit;
    }
}
