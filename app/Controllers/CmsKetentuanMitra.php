<?php

namespace App\Controllers;

use App\Models\CmsMitraKetentuanItemModel;

class CmsKetentuanMitra extends BaseController
{
    protected CmsMitraKetentuanItemModel $itemModel;

    public function __construct()
    {
        $this->ensureTablesAndSeed();
        $this->itemModel = new CmsMitraKetentuanItemModel();
    }

    private function ensureTablesAndSeed(): void
    {
        $db  = \Config\Database::connect();
        $now = date('Y-m-d H:i:s');

        if (! $db->tableExists('cms_mitra_ketentuan_page')) {
            $db->query("
                CREATE TABLE IF NOT EXISTS `cms_mitra_ketentuan_page` (
                    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                    `hero_judul` VARCHAR(255) NOT NULL,
                    `hero_paragraf_1` TEXT NULL,
                    `hero_paragraf_2` TEXT NULL,
                    `tujuan_label` VARCHAR(150) NOT NULL,
                    `tujuan_judul` VARCHAR(255) NOT NULL,
                    `tujuan_deskripsi` TEXT NULL,
                    `tujuan_cards_json` LONGTEXT NULL,
                    `syarat_label` VARCHAR(150) NOT NULL,
                    `syarat_judul` VARCHAR(255) NOT NULL,
                    `syarat_deskripsi` TEXT NULL,
                    `syarat_info_judul` VARCHAR(255) NOT NULL,
                    `syarat_info_isi` TEXT NULL,
                    `ketentuan_label` VARCHAR(150) NOT NULL,
                    `ketentuan_judul` VARCHAR(255) NOT NULL,
                    `ketentuan_deskripsi` TEXT NULL,
                    `updated_at` DATETIME NULL DEFAULT NULL,
                    PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
            ");

            $defaultCards = [
                [
                    'judul'     => 'Bagi Anggota INKINDO',
                    'deskripsi' => 'Mempermudah 900+ badan usaha konsultan dalam mengakses referensi material bersertifikasi, teknologi terkini, penawaran harga korporat terbaik, serta vendor pelaksana yang terpercaya.',
                    'poin'      => [
                        'Akses langsung ke katalog produk & spesifikasi teknis',
                        'Dukungan transfer keahlian & workshop material',
                        'Jaminan mutu pengadaan dalam rancang bangun',
                    ],
                ],
                [
                    'judul'     => 'Bagi Para Mitra Kerja',
                    'deskripsi' => 'Membuka jembatan interaksi promosi langsung dengan para perencana, perancang, pengawas, dan konsultan manajemen proyek di seluruh DKI Jakarta dan proyek nasional.',
                    'poin'      => [
                        'Publikasi resmi di portal direktori INKINDO DKI',
                        'Peluang business matching & sponsorship event',
                        'Sinergi penawaran pada proyek pemerintah & swasta',
                    ],
                ],
                [
                    'judul'     => 'Bagi Masyarakat Luas',
                    'deskripsi' => 'Mendorong terwujudnya hasil karya pembangunan infrastruktur dan gedung yang lebih kokoh, aman, ramah lingkungan, serta memenuhi standar kepatuhan nasional.',
                    'poin'      => [
                        'Standar konstruksi yang transparan dan akuntabel',
                        'Penerapan material ramah lingkungan (Green Building)',
                        'Hasil investasi proyek yang berdaya tahan lama',
                    ],
                ],
            ];

            $db->table('cms_mitra_ketentuan_page')->insert([
                'hero_judul'          => 'Tentang Mitra Kerja',
                'hero_paragraf_1'     => 'Halaman ini didedikasikan bagi para pihak yang bekerjasama dengan INKINDO DKI Jakarta baik untuk Kontraktor, Produsen, Suplier dan lain-lain.',
                'hero_paragraf_2'     => 'Apabila Bapak/Ibu berminat menjadi Mitra Kerja INKINDO DKI Jakarta dapat menghubungi Sekretariat INKINDO DKI Jakarta.',
                'tujuan_label'        => 'Kolaborasi & Sinergi Strategis',
                'tujuan_judul'        => 'Maksud & Tujuan Mitra Kerja',
                'tujuan_deskripsi'    => 'Dengan halaman mitra kerja ini diharapkan dapat memberikan manfaat khususnya bagi Anggota, para Mitra Kerja dan Masyarakat pada umumnya, baik dalam mencari barang/jasa maupun informasi yang dibutuhkan untuk kegiatan usaha terkait usaha jasa konsultansi.',
                'tujuan_cards_json'   => json_encode($defaultCards, JSON_UNESCAPED_UNICODE),
                'syarat_label'        => 'Kriteria & Legalitas',
                'syarat_judul'        => 'Syarat Mitra Kerja',
                'syarat_deskripsi'    => 'Program kemitraan ini terbuka bagi seluruh pelaku usaha yang berkomitmen menjalin kerjasama secara profesional, transparan, dan berintegritas tinggi bersama DPP INKINDO DKI Jakarta.',
                'syarat_info_judul'   => 'Siapa yang Dapat Bermitra?',
                'syarat_info_isi'     => 'Kontraktor Pelaksana, Produsen Material Bahan Bangunan, Suplier Peralatan & Mesin, Penyedia Solusi Software/IT Desain (BIM, CAD), Perbankan, Penjaminan/Asuransi Proyek, dan Laboratorium Pengujian Teknis.',
                'ketentuan_label'     => 'Tata Tertib & Regulasi Kerjasama',
                'ketentuan_judul'     => 'Ketentuan Mitra Kerja',
                'ketentuan_deskripsi' => 'Seluruh hubungan kemitraan diatur secara profesional untuk melindungi reputasi asosiasi serta menjamin keterbukaan hak dan kewajiban kedua belah pihak.',
                'updated_at'          => $now,
            ]);
        }

        if (! $db->tableExists('cms_mitra_ketentuan_items')) {
            $db->query("
                CREATE TABLE IF NOT EXISTS `cms_mitra_ketentuan_items` (
                    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                    `jenis` VARCHAR(20) NOT NULL DEFAULT 'syarat',
                    `ikon` VARCHAR(60) NULL DEFAULT NULL,
                    `judul` VARCHAR(255) NOT NULL,
                    `deskripsi` TEXT NULL,
                    `urutan` INT NOT NULL DEFAULT 1,
                    `created_at` DATETIME NULL DEFAULT NULL,
                    `updated_at` DATETIME NULL DEFAULT NULL,
                    PRIMARY KEY (`id`),
                    KEY `idx_cms_mk_jenis` (`jenis`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
            ");

            $defaultItems = [
                // Syarat (6)
                [
                    'jenis'      => 'syarat',
                    'ikon'       => null,
                    'judul'      => 'Badan Usaha Resmi',
                    'deskripsi'  => 'Perusahaan berbadan hukum sah di Indonesia (PT, CV, Koperasi, Distributor Resmi / Agen Tunggal) dengan domisili usaha yang jelas.',
                    'urutan'     => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'jenis'      => 'syarat',
                    'ikon'       => null,
                    'judul'      => 'Legalitas & Izin Usaha',
                    'deskripsi'  => 'Memiliki Nomor Induk Berusaha (NIB) berbasis risiko aktif, Nomor Pokok Wajib Pajak (NPWP) Badan, serta izin operasional sektor terkait.',
                    'urutan'     => 2,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'jenis'      => 'syarat',
                    'ikon'       => null,
                    'judul'      => 'Bidang Usaha yang Relevan',
                    'deskripsi'  => 'Bergerak di bidang yang menunjang aktivitas jasa konstruksi, jasa konsultansi keteknikan, dan layanan penunjang profesional lainnya.',
                    'urutan'     => 3,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'jenis'      => 'syarat',
                    'ikon'       => null,
                    'judul'      => 'Penunjukan PIC Resmi',
                    'deskripsi'  => 'Menunjuk Person in Charge (PIC) tetap sebagai narahubung resmi dan berwenang melakukan koordinasi teknis dengan INKINDO DKI Jakarta.',
                    'urutan'     => 4,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'jenis'      => 'syarat',
                    'ikon'       => null,
                    'judul'      => 'Komitmen Mutu & SMAP',
                    'deskripsi'  => 'Menyediakan produk/jasa berstandar SNI/internasional dan berkomitmen mematuhi Sistem Manajemen Anti Penyuapan (ISO 37001).',
                    'urutan'     => 5,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'jenis'      => 'syarat',
                    'ikon'       => null,
                    'judul'      => 'Registrasi Administratif',
                    'deskripsi'  => 'Mengisi formulir pendaftaran kemitraan secara daring dan menandatangani kesepahaman kemitraan bersama sekretariat.',
                    'urutan'     => 6,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                // Ketentuan (6)
                [
                    'jenis'      => 'ketentuan',
                    'ikon'       => 'balance',
                    'judul'      => '1. Prinsip & Asas Kemitraan',
                    'deskripsi'  => 'Hubungan kemitraan bersifat non-eksklusif dan berorientasi pada sinergi saling menguntungkan (mutual benefit). Kemitraan tidak membatasi independensi para konsultan anggota INKINDO dalam menentukan keputusan teknis.',
                    'urutan'     => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'jenis'      => 'ketentuan',
                    'ikon'       => 'campaign',
                    'judul'      => '2. Hak & Fasilitas Mitra',
                    'deskripsi'  => 'Mitra resmi berhak mencantumkan logo di direktori website INKINDO DKI, mengikuti agenda knowledge sharing / seminar teknis, serta berpeluang mempresentasikan inovasi produk dalam temu anggota asosiasi.',
                    'urutan'     => 2,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'jenis'      => 'ketentuan',
                    'ikon'       => 'assignment_turned_in',
                    'judul'      => '3. Kewajiban & Akuntabilitas',
                    'deskripsi'  => 'Mitra berkewajiban memberikan informasi spesifikasi material/jasa yang akurat, menjamin ketersediaan barang/layanan purnajual, serta menjaga harga yang kompetitif bagi para konsultan anggota.',
                    'urutan'     => 3,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'jenis'      => 'ketentuan',
                    'ikon'       => 'calendar_clock',
                    'judul'      => '4. Masa Berlaku & Evaluasi',
                    'deskripsi'  => 'Status kemitraan berlaku selama 1 (satu) tahun kalender sejak tanggal pengesahan registrasi. DPP INKINDO DKI Jakarta melaksanakan evaluasi berkala untuk perpanjangan masa kemitraan berikutnya.',
                    'urutan'     => 4,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'jenis'      => 'ketentuan',
                    'ikon'       => 'security',
                    'judul'      => '5. Integritas & Etika Bisnis',
                    'deskripsi'  => 'Mitra wajib menjunjung tinggi etika bisnis yang sehat, tidak melakukan praktik monopoli, gratifikasi ilegal, atau penyuapan yang bertentangan dengan standar SMAP ISO 37001 dan hukum Republik Indonesia.',
                    'urutan'     => 5,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'jenis'      => 'ketentuan',
                    'ikon'       => 'cancel',
                    'judul'      => '6. Pengakhiran Kemitraan',
                    'deskripsi'  => 'DPP INKINDO DKI Jakarta berhak menghentikan kemitraan secara sepihak apabila mitra terbukti melakukan pelanggaran hukum, wanprestasi kontrak, atau merugikan harkat dan martabat asosiasi.',
                    'urutan'     => 6,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
            ];
            $db->table('cms_mitra_ketentuan_items')->insertBatch($defaultItems);
        }
    }

    public function index()
    {
        if ($redirect = $this->ensureAdmin()) {
            return $redirect;
        }

        $db   = \Config\Database::connect();
        $page = $db->table('cms_mitra_ketentuan_page')->orderBy('id', 'ASC')->get()->getRowArray() ?? [];

        $cards = [];
        if (! empty($page['tujuan_cards_json'])) {
            $decoded = json_decode((string) $page['tujuan_cards_json'], true);
            if (is_array($decoded)) {
                $cards = $decoded;
            }
        }

        $syaratItems    = $this->itemModel->where('jenis', 'syarat')->orderBy('urutan', 'ASC')->orderBy('id', 'ASC')->findAll();
        $ketentuanItems = $this->itemModel->where('jenis', 'ketentuan')->orderBy('urutan', 'ASC')->orderBy('id', 'ASC')->findAll();

        $activeTab = (string) ($this->request->getGet('tab') ?? 'syarat');
        if (! in_array($activeTab, ['hero', 'syarat', 'ketentuan'], true)) {
            $activeTab = 'syarat';
        }

        return view('cms/ketentuan_mitra', [
            'title'          => 'Admin CMS - Ketentuan Mitra Kerja',
            'page'           => $page,
            'cards'          => $cards,
            'syaratItems'    => $syaratItems,
            'ketentuanItems' => $ketentuanItems,
            'activeTab'      => $activeTab,
        ]);
    }

    public function savePage()
    {
        if ($redirect = $this->ensureAdmin()) {
            return $redirect;
        }

        $db       = \Config\Database::connect();
        $existing = $db->table('cms_mitra_ketentuan_page')->orderBy('id', 'ASC')->get()->getRowArray() ?? [];
        $section  = (string) ($this->request->getPost('section') ?? 'hero');

        $data = ['updated_at' => date('Y-m-d H:i:s')];

        if ($section === 'hero') {
            $cardTitles = $this->request->getPost('card_judul');
            $cardDescs  = $this->request->getPost('card_deskripsi');
            $cardPoins  = $this->request->getPost('card_poin');
            $cards      = [];

            if (is_array($cardTitles)) {
                foreach ($cardTitles as $idx => $cTitle) {
                    $rawPoin = (string) ($cardPoins[$idx] ?? '');
                    $lines   = array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $rawPoin))));
                    $cards[] = [
                        'judul'     => trim((string) $cTitle),
                        'deskripsi' => trim((string) ($cardDescs[$idx] ?? '')),
                        'poin'      => $lines,
                    ];
                }
            }

            $data['hero_judul']        = trim((string) ($this->request->getPost('hero_judul') ?? ''));
            $data['hero_paragraf_1']   = trim((string) ($this->request->getPost('hero_paragraf_1') ?? ''));
            $data['hero_paragraf_2']   = trim((string) ($this->request->getPost('hero_paragraf_2') ?? ''));
            $data['tujuan_label']      = trim((string) ($this->request->getPost('tujuan_label') ?? ''));
            $data['tujuan_judul']      = trim((string) ($this->request->getPost('tujuan_judul') ?? ''));
            $data['tujuan_deskripsi']  = trim((string) ($this->request->getPost('tujuan_deskripsi') ?? ''));
            $data['tujuan_cards_json'] = json_encode($cards, JSON_UNESCAPED_UNICODE);
            $tab = 'hero';
            $msg = 'Bagian Hero & Maksud Tujuan Mitra Kerja berhasil diperbarui.';
        } elseif ($section === 'syarat') {
            $data['syarat_label']      = trim((string) ($this->request->getPost('syarat_label') ?? ''));
            $data['syarat_judul']      = trim((string) ($this->request->getPost('syarat_judul') ?? ''));
            $data['syarat_deskripsi']  = trim((string) ($this->request->getPost('syarat_deskripsi') ?? ''));
            $data['syarat_info_judul'] = trim((string) ($this->request->getPost('syarat_info_judul') ?? ''));
            $data['syarat_info_isi']   = trim((string) ($this->request->getPost('syarat_info_isi') ?? ''));
            $tab = 'syarat';
            $msg = 'Header Syarat Mitra Kerja berhasil diperbarui.';
        } else {
            $data['ketentuan_label']     = trim((string) ($this->request->getPost('ketentuan_label') ?? ''));
            $data['ketentuan_judul']     = trim((string) ($this->request->getPost('ketentuan_judul') ?? ''));
            $data['ketentuan_deskripsi'] = trim((string) ($this->request->getPost('ketentuan_deskripsi') ?? ''));
            $tab = 'ketentuan';
            $msg = 'Header Ketentuan Mitra Kerja berhasil diperbarui.';
        }

        if (! empty($existing['id'])) {
            $db->table('cms_mitra_ketentuan_page')->where('id', $existing['id'])->update($data);
        } else {
            $db->table('cms_mitra_ketentuan_page')->insert($data);
        }

        return redirect()->to('/cms/ketentuan-mitra?tab=' . $tab)->with('pesan', $msg);
    }

    public function saveItem()
    {
        if ($redirect = $this->ensureAdmin()) {
            return $redirect;
        }

        $id        = (int) ($this->request->getPost('id') ?? 0);
        $jenis     = (string) ($this->request->getPost('jenis') ?? 'syarat');
        $jenis     = in_array($jenis, ['syarat', 'ketentuan'], true) ? $jenis : 'syarat';
        $ikon      = trim((string) ($this->request->getPost('ikon') ?? ''));
        $judul     = trim((string) ($this->request->getPost('judul') ?? ''));
        $deskripsi = trim((string) ($this->request->getPost('deskripsi') ?? ''));
        $urutan    = max(1, (int) ($this->request->getPost('urutan') ?? 1));

        if ($judul === '' || $deskripsi === '') {
            return redirect()->to('/cms/ketentuan-mitra?tab=' . $jenis)->with('error', 'Judul dan Deskripsi wajib diisi.');
        }

        $data = [
            'jenis'     => $jenis,
            'ikon'      => $jenis === 'ketentuan' ? ($ikon !== '' ? $ikon : 'gavel') : null,
            'judul'     => $judul,
            'deskripsi' => $deskripsi,
            'urutan'    => $urutan,
        ];

        if ($id > 0 && $this->itemModel->find($id)) {
            $this->itemModel->update($id, $data);
            $msg = 'Item ' . ($jenis === 'syarat' ? 'Syarat' : 'Ketentuan') . ' berhasil diperbarui.';
        } else {
            $this->itemModel->insert($data);
            $msg = 'Item ' . ($jenis === 'syarat' ? 'Syarat' : 'Ketentuan') . ' baru berhasil ditambahkan.';
        }

        return redirect()->to('/cms/ketentuan-mitra?tab=' . $jenis)->with('pesan', $msg);
    }

    public function deleteItem($id)
    {
        if ($redirect = $this->ensureAdmin()) {
            return $redirect;
        }

        $existing = $this->itemModel->find((int) $id);
        if (! $existing) {
            return redirect()->to('/cms/ketentuan-mitra')->with('error', 'Data tidak ditemukan.');
        }

        $jenis = in_array($existing['jenis'] ?? '', ['syarat', 'ketentuan'], true) ? $existing['jenis'] : 'syarat';
        $this->itemModel->delete((int) $id);

        return redirect()->to('/cms/ketentuan-mitra?tab=' . $jenis)->with('pesan', 'Item berhasil dihapus.');
    }
}

