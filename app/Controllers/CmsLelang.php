<?php

namespace App\Controllers;

use App\Models\CmsLelangPortalModel;

class CmsLelang extends BaseController
{
    protected CmsLelangPortalModel $portalModel;

    public function __construct()
    {
        $this->ensureTablesAndSeed();
        $this->portalModel = new CmsLelangPortalModel();
    }

    private function ensureTablesAndSeed(): void
    {
        $db  = \Config\Database::connect();
        $now = date('Y-m-d H:i:s');

        if (! $db->tableExists('cms_lelang_portal')) {
            $db->query("
                CREATE TABLE IF NOT EXISTS `cms_lelang_portal` (
                    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                    `ikon` VARCHAR(60) NOT NULL DEFAULT 'public',
                    `kategori` VARCHAR(100) NOT NULL,
                    `judul` VARCHAR(200) NOT NULL,
                    `deskripsi` TEXT NULL,
                    `teks_tombol` VARCHAR(120) NOT NULL DEFAULT 'Buka Portal Pengadaan',
                    `url` VARCHAR(500) NOT NULL,
                    `urutan` INT NOT NULL DEFAULT 1,
                    `created_at` DATETIME NULL DEFAULT NULL,
                    `updated_at` DATETIME NULL DEFAULT NULL,
                    PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
            ");

            $defaultPortals = [
                [
                    'ikon'        => 'flight_takeoff',
                    'kategori'    => 'BUMN Kebandarudaraan',
                    'judul'       => 'Angkasa Pura',
                    'deskripsi'   => 'Portal Procurement resmi untuk pengadaan jasa konsultansi fasilitas bandara, terminal, runway, arsitektur, dan rekayasa kebandarudaraan.',
                    'teks_tombol' => 'Buka Portal Pengadaan',
                    'url'         => 'https://procurement.angkasapura2.co.id/',
                    'urutan'      => 1,
                    'created_at'  => $now,
                    'updated_at'  => $now,
                ],
                [
                    'ikon'        => 'bolt',
                    'kategori'    => 'BUMN Ketenagalistrikan',
                    'judul'       => 'PT Perusahaan Listrik Negara (Persero)',
                    'deskripsi'   => 'Sistem e-Procurement PT PLN (Persero) untuk paket studi kelayakan, jaringan transmisi, pembangkitan, dan transisi energi terbarukan.',
                    'teks_tombol' => 'Buka e-Procurement PLN',
                    'url'         => 'https://eproc.pln.co.id/',
                    'urutan'      => 2,
                    'created_at'  => $now,
                    'updated_at'  => $now,
                ],
                [
                    'ikon'        => 'train',
                    'kategori'    => 'BUMN Transportasi Rel',
                    'judul'       => 'PT Kereta Api Indonesia (Persero)',
                    'deskripsi'   => 'Pengadaan barang dan jasa konsultansi prasarana perkeretaapian, modernisasi stasiun, persinyalan, dan integrasi logistik nasional.',
                    'teks_tombol' => 'Buka e-Procurement KAI',
                    'url'         => 'https://eproc.kai.id/',
                    'urutan'      => 3,
                    'created_at'  => $now,
                    'updated_at'  => $now,
                ],
                [
                    'ikon'        => 'account_balance',
                    'kategori'    => 'Lembaga Multilateral',
                    'judul'       => 'Asian Development Bank (ADB)',
                    'deskripsi'   => 'Akses peluang tender dan paket jasa konsultansi internasional pada proyek pembiayaan infrastruktur berkelanjutan kawasan Asia-Pasifik.',
                    'teks_tombol' => 'Buka Tender ADB',
                    'url'         => 'https://www.adb.org/projects/tenders',
                    'urutan'      => 4,
                    'created_at'  => $now,
                    'updated_at'  => $now,
                ],
                [
                    'ikon'        => 'public',
                    'kategori'    => 'Lembaga Finansial Global',
                    'judul'       => 'World Bank (Bank Dunia)',
                    'deskripsi'   => 'Direktori proyek dan operasional pembangunan Bank Dunia di Indonesia dan global, mencakup paket konsultansi teknis serta kebijakan publik.',
                    'teks_tombol' => 'Buka Proyek World Bank',
                    'url'         => 'https://projects.worldbank.org/en/projects-operations/projects-list',
                    'urutan'      => 5,
                    'created_at'  => $now,
                    'updated_at'  => $now,
                ],
                [
                    'ikon'        => 'shopping_cart_checkout',
                    'kategori'    => 'Pemerintah RI (SPSE)',
                    'judul'       => 'LKPP - LPSE Nasional',
                    'deskripsi'   => 'Layanan Pengadaan Secara Elektronik Lembaga Kebijakan Pengadaan Barang/Jasa Pemerintah untuk seluruh K/L/D/I di Indonesia.',
                    'teks_tombol' => 'Buka Portal LPSE LKPP',
                    'url'         => 'https://lpse.lkpp.go.id/eproc4/lelang',
                    'urutan'      => 6,
                    'created_at'  => $now,
                    'updated_at'  => $now,
                ],
            ];
            $db->table('cms_lelang_portal')->insertBatch($defaultPortals);
        }

        if (! $db->tableExists('cms_lelang_peluang')) {
            $db->query("
                CREATE TABLE IF NOT EXISTS `cms_lelang_peluang` (
                    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                    `hero_judul` VARCHAR(255) NOT NULL,
                    `hero_deskripsi` TEXT NULL,
                    `section_badge` VARCHAR(150) NOT NULL,
                    `section_judul` VARCHAR(255) NOT NULL,
                    `section_deskripsi` TEXT NULL,
                    `surat_label` VARCHAR(150) NOT NULL,
                    `surat_judul` VARCHAR(255) NOT NULL,
                    `surat_status` VARCHAR(60) NOT NULL DEFAULT 'Terbuka',
                    `surat_kepada` VARCHAR(255) NOT NULL,
                    `surat_pengantar` TEXT NULL,
                    `kualifikasi_json` LONGTEXT NULL,
                    `surat_penutup` TEXT NULL,
                    `kontak_telp` VARCHAR(120) NOT NULL,
                    `kontak_email` VARCHAR(150) NOT NULL,
                    `updated_at` DATETIME NULL DEFAULT NULL,
                    PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
            ");

            $defaultKualifikasi = [
                'Mampu menganalisa data curah hujan secara mendalam, akurat, dan berbasis data hidrologi terkini.',
                'Mampu melakukan studi kelaikan pencegahan banjir terhadap struktur bangunan yang telah ada (existing structures).',
                'Melakukan perancangan pencegahan banjir di public area, sesuai dengan kaidah-kaidah yang berlaku: structural, arsitektural, estetika, dan lain-lain.',
                'Mampu berkoordinasi dengan kementrian teknis terkait (Kementrian PUPR, dan lain-lain), untuk mendapatkan izin terkait perancangan struktur bangunan pencegahan banjir.',
            ];

            $db->table('cms_lelang_peluang')->insert([
                'hero_judul'        => 'Lelang Lainnya & Peluang Usaha',
                'hero_deskripsi'    => 'Kanal informasi paket pengadaan jasa konsultansi alternatif dari BUMN terkemuka, lembaga pembiayaan multilateral internasional, serta peluang kerjasama proyek khusus bagi seluruh anggota DPP INKINDO DKI Jakarta.',
                'section_badge'     => 'Pemberitahuan Resmi Sekretariat DPP',
                'section_judul'     => 'Peluang Usaha Lainnya',
                'section_deskripsi' => 'Informasi permohonan keahlian konsultan perencana struktur untuk mitigasi & pencegahan banjir bagi seluruh anggota.',
                'surat_label'       => 'Surat Pemberitahuan Terbuka',
                'surat_judul'       => 'Permintaan Konsultan Perencana Struktur untuk Pencegahan Banjir',
                'surat_status'      => 'Terbuka',
                'surat_kepada'      => 'Seluruh Anggota INKINDO DKI Jakarta',
                'surat_pengantar'   => 'Dapat kami sampaikan bahwa saat ini ada permintaan untuk konsultan Perencana Struktur untuk Pencegahan Banjir dengan kualifikasi sebagai berikut:',
                'kualifikasi_json'  => json_encode($defaultKualifikasi, JSON_UNESCAPED_UNICODE),
                'surat_penutup'     => 'Apabila Bidang Usaha Bapak/Ibu sesuai kualifikasi tersebut diatas dapat menghubungi / menyampaikan ke Sekretariat INKINDO DKI Jakarta.',
                'kontak_telp'       => '+62217942940(H) / +6282112079750',
                'kontak_email'      => 'sekretariat@inkindo-dki.org',
                'updated_at'        => $now,
            ]);
        }
    }

    public function index()
    {
        if ($redirect = $this->ensureAdmin()) {
            return $redirect;
        }

        $db      = \Config\Database::connect();
        $portals = $this->portalModel->orderBy('urutan', 'ASC')->orderBy('id', 'ASC')->findAll();
        $peluang = $db->table('cms_lelang_peluang')->orderBy('id', 'ASC')->get()->getRowArray() ?? [];

        $kualifikasi = [];
        if (! empty($peluang['kualifikasi_json'])) {
            $decoded = json_decode((string) $peluang['kualifikasi_json'], true);
            if (is_array($decoded)) {
                $kualifikasi = $decoded;
            }
        }

        $activeTab = (string) ($this->request->getGet('tab') ?? 'portal');
        if (! in_array($activeTab, ['portal', 'peluang'], true)) {
            $activeTab = 'portal';
        }

        return view('cms/lelang', [
            'title'       => 'Admin CMS - Lelang Lainnya',
            'portals'     => $portals,
            'peluang'     => $peluang,
            'kualifikasi' => $kualifikasi,
            'activeTab'   => $activeTab,
        ]);
    }

    public function savePortal()
    {
        if ($redirect = $this->ensureAdmin()) {
            return $redirect;
        }

        $id         = (int) ($this->request->getPost('id') ?? 0);
        $ikon       = trim((string) ($this->request->getPost('ikon') ?? 'public'));
        $kategori   = trim((string) ($this->request->getPost('kategori') ?? ''));
        $judul      = trim((string) ($this->request->getPost('judul') ?? ''));
        $deskripsi  = trim((string) ($this->request->getPost('deskripsi') ?? ''));
        $teksTombol = trim((string) ($this->request->getPost('teks_tombol') ?? 'Buka Portal Pengadaan'));
        $url        = trim((string) ($this->request->getPost('url') ?? ''));
        $urutan     = max(1, (int) ($this->request->getPost('urutan') ?? 1));

        if ($kategori === '' || $judul === '' || $url === '') {
            return redirect()->to('/cms/lelang?tab=portal')->with('error', 'Kategori, Judul Portal, dan URL wajib diisi.');
        }

        if (! preg_match('~^https?://~i', $url)) {
            $url = 'https://' . ltrim($url, '/');
        }

        $data = [
            'ikon'        => $ikon !== '' ? $ikon : 'public',
            'kategori'    => $kategori,
            'judul'       => $judul,
            'deskripsi'   => $deskripsi,
            'teks_tombol' => $teksTombol !== '' ? $teksTombol : 'Buka Portal Pengadaan',
            'url'         => $url,
            'urutan'      => $urutan,
        ];

        if ($id > 0 && $this->portalModel->find($id)) {
            $this->portalModel->update($id, $data);
            $msg = 'Portal e-Procurement berhasil diperbarui.';
        } else {
            $this->portalModel->insert($data);
            $msg = 'Portal e-Procurement baru berhasil ditambahkan.';
        }

        return redirect()->to('/cms/lelang?tab=portal')->with('pesan', $msg);
    }

    public function deletePortal($id)
    {
        if ($redirect = $this->ensureAdmin()) {
            return $redirect;
        }

        $existing = $this->portalModel->find((int) $id);
        if (! $existing) {
            return redirect()->to('/cms/lelang?tab=portal')->with('error', 'Data portal tidak ditemukan.');
        }

        $this->portalModel->delete((int) $id);
        return redirect()->to('/cms/lelang?tab=portal')->with('pesan', 'Portal e-Procurement berhasil dihapus.');
    }

    public function savePeluang()
    {
        if ($redirect = $this->ensureAdmin()) {
            return $redirect;
        }

        $rawKualifikasi = $this->request->getPost('kualifikasi');
        $kualifikasi    = [];
        if (is_array($rawKualifikasi)) {
            foreach ($rawKualifikasi as $item) {
                $clean = trim((string) $item);
                if ($clean !== '') {
                    $kualifikasi[] = $clean;
                }
            }
        }

        $data = [
            'hero_judul'        => trim((string) ($this->request->getPost('hero_judul') ?? '')),
            'hero_deskripsi'    => trim((string) ($this->request->getPost('hero_deskripsi') ?? '')),
            'section_badge'     => trim((string) ($this->request->getPost('section_badge') ?? '')),
            'section_judul'     => trim((string) ($this->request->getPost('section_judul') ?? '')),
            'section_deskripsi' => trim((string) ($this->request->getPost('section_deskripsi') ?? '')),
            'surat_label'       => trim((string) ($this->request->getPost('surat_label') ?? '')),
            'surat_judul'       => trim((string) ($this->request->getPost('surat_judul') ?? '')),
            'surat_status'      => trim((string) ($this->request->getPost('surat_status') ?? 'Terbuka')),
            'surat_kepada'      => trim((string) ($this->request->getPost('surat_kepada') ?? '')),
            'surat_pengantar'   => trim((string) ($this->request->getPost('surat_pengantar') ?? '')),
            'kualifikasi_json'  => json_encode($kualifikasi, JSON_UNESCAPED_UNICODE),
            'surat_penutup'     => trim((string) ($this->request->getPost('surat_penutup') ?? '')),
            'kontak_telp'       => trim((string) ($this->request->getPost('kontak_telp') ?? '')),
            'kontak_email'      => trim((string) ($this->request->getPost('kontak_email') ?? '')),
            'updated_at'        => date('Y-m-d H:i:s'),
        ];

        $db       = \Config\Database::connect();
        $existing = $db->table('cms_lelang_peluang')->orderBy('id', 'ASC')->get()->getRowArray();

        if ($existing) {
            $db->table('cms_lelang_peluang')->where('id', $existing['id'])->update($data);
        } else {
            $db->table('cms_lelang_peluang')->insert($data);
        }

        return redirect()->to('/cms/lelang?tab=peluang')->with('pesan', 'Konten Peluang Usaha Lainnya berhasil diperbarui.');
    }
}

