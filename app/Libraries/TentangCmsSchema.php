<?php

namespace App\Libraries;

use Config\Database;

/**
 * Ensures Tentang Kami CMS tables exist in MySQL and seeds initial content from inkindo-dki-web.
 */
class TentangCmsSchema
{
    public static function getDefaultProfileData(): array
    {
        return [
            'hero_title'       => 'Tentang Kami',
            'hero_subtitle'    => 'Mengenal lebih dekat profil organisasi, sejarah panjang, visi & misi, kode etik kehormatan profesi, serta struktur kepengurusan DPP INKINDO DKI Jakarta.',
            'hero_image'       => 'assets/jakarta3.jpg',
            'sejarah_heading'  => 'Sejarah INKINDO DKI Jakarta',
            'sejarah_content'  => implode("\n\n", [
                'INKINDO (Ikatan Nasional Konsultan Indonesia) merupakan asosiasi perusahaan konsultan independen yang didirikan pada tanggal 29 Juni 1979, sebagai hasil fusi antara IKINDO (Ikatan Konsultan Indonesia) dan PKTPI (Persatuan Konsultan Teknik Pembangunan Indonesia). Anggota INKINDO tersebar di 33 provinsi di seluruh Indonesia dan total memiliki anggota 7000 perusahaan konsultan.',
                'INKINDO DKI Jakarta terletak di Provinsi Daerah Khusus Ibukota Jakarta, sekarang memiliki anggota aktif 900 perusahaan, merupakan INKINDO provinsi terbesar diantara 33 provinsi di Indonesia. Anggota INKINDO DKI Jakarta dikelompokan dalam 3 (tiga) kualifikasi : Besar (20%), Menengah (45%), dan Kecil (35%). Beberapa anggota INKINDO DKI Jakarta telah menangani banyak proyek-proyek terkemuka, di Indonesia dan di manca negara.',
            ]),
            'sejarah_image'    => 'assets/hero-bg.jpg',
            'visimisi_heading' => 'Visi & Misi',
            'visimisi_bg_image'=> 'assets/jakarta3.jpg',
            'visi_title'       => 'Visi',
            'visi_text'        => 'Gerak Cepat Memajukan Anggota yang Berkelanjutan.',
            'misi_title'       => 'Misi',
            'misi_items_json'  => json_encode([
                'Meningkatkan pelayanan yang proaktif, cepat, mudah, dan terjangkau.',
                'Memperkuat kapasitas dan kolaborasi antara Anggota (pelatihan, akses informasi dan permodalan, SDM).',
                'Memperkuat peran sebagai mitra strategis pemerintah, swasta dan sinergi dengan DPN INKINDO, DPP provinsi lain, mitra kerja, serta asosiasi.',
                'Membina Anggota dalam menghadapi dinamika regulasi.',
                'Menjadikan INKINDO DKI sebagai organisasi yang inovatif, adaptif, transparan dan akuntabel.',
            ], JSON_UNESCAPED_UNICODE),
            'kode_etik_heading'  => 'Kode Etik',
            'kode_etik_subtitle' => 'Dengan menjunjung tinggi Etika Ikatan Nasional Konsultan Indonesia sebagai dasar yang dinamis untuk melayani sesama manusia, maka tiap Anggota Ikatan Nasional Konsultan Indonesia wajib untuk:',
            'kode_etik_items_json' => json_encode([
                'Menjunjung tinggi kehormatan, kemuliaan, dan nama baik profesi konsultan dalam hubungan kerja dengan pemberi tugas sesama rekan konsultan dan masyarakat.',
                'Bertindak jujur, tidak memihak, serta penuh dedikasi melayani pemberi tugas dan masyarakat.',
                'Tukar menukar pengetahuan bidang keahlian secara wajar dengan rekan konsultan dan kelompok profesi serta meningkatkan pengetahuan masyarakat terhadap profesi konsultan sehingga dapat lebih menghayati karya konsultan.',
                'Menghormati prinsip pemberian imbalan jasa yang layak dan memadai bagi konsultan, sehingga dapat dipertanggungjawabkan secara profesional dan moral yang menjamin dapat dilaksanakannya tugas yang dipercayakan memenuhi semua persyaratan yang terkait dengan keahlian, kompetensi, dan integritas tinggi.',
                'Menghargai dan menghormati reputasi profesional rekan konsultan dan setiap perjanjian kerja yang berhubungan dengan profesinya.',
                'Mendapatkan tugas terutama berdasarkan standar keahlian profesional tanpa melalui cara-cara persaingan yang tidak sehat.',
                'Bekerjasama sebagai konsultan hanya dengan rekan konsultan atau tenaga ahli lain yang memiliki integritas tinggi.',
                'Menjalankan asas pembangunan berkelanjutan dalam semua aspek pelayanan jasa konsultan sebagai bagian integral dari tanggungjawabnya terhadap sesama, lingkungan kehidupan yang luas, dan generasi yang akan datang.',
            ], JSON_UNESCAPED_UNICODE),
        ];
    }

    public static function getDefaultUnits(): array
    {
        return [
            [
                'slug'       => 'dpph',
                'nama'       => 'DPPH',
                'deskripsi'  => 'Dewan Pengurus Provinsi Harian DPP INKINDO DKI Jakarta',
                'intro_text' => null,
                'icon'       => 'account_balance',
                'urutan'     => 1,
            ],
            [
                'slug'       => 'dppl',
                'nama'       => 'DPPL',
                'deskripsi'  => 'Dewan Pengurus Provinsi Lengkap DPP INKINDO DKI Jakarta',
                'intro_text' => null,
                'icon'       => 'account_tree',
                'urutan'     => 2,
            ],
            [
                'slug'       => 'badan',
                'nama'       => 'Badan - Badan',
                'deskripsi'  => 'Struktur Badan-Badan Kerja DPP INKINDO DKI Jakarta',
                'intro_text' => null,
                'icon'       => 'corporate_fare',
                'urutan'     => 3,
            ],
            [
                'slug'       => 'komite',
                'nama'       => 'Komite',
                'deskripsi'  => 'Komite-Komite Kerja Strategis DPP INKINDO DKI Jakarta',
                'intro_text' => null,
                'icon'       => 'groups_3',
                'urutan'     => 4,
            ],
            [
                'slug'       => 'dkp',
                'nama'       => 'DKP',
                'deskripsi'  => 'Dewan Kehormatan Provinsi DPP INKINDO DKI Jakarta',
                'intro_text' => 'Dewan Kehormatan Provinsi (DKP) bertugas menjaga keluhuran martabat, mengawasi penerapan Kode Etik dan Anggaran Dasar/Anggaran Rumah Tangga (AD/ART), serta memberikan putusan mediasi terhadap persoalan etika profesi antar-anggota.',
                'icon'       => 'gavel',
                'urutan'     => 5,
            ],
            [
                'slug'       => 'dpop',
                'nama'       => 'DPOP',
                'deskripsi'  => 'Dewan Pertimbangan Organisasi Provinsi DPP INKINDO DKI Jakarta',
                'intro_text' => 'Dewan Pertimbangan Organisasi Provinsi (DPOP) beranggotakan para tokoh senior dan pimpinan purnatugas yang bertugas memberikan saran, nasihat, dan pertimbangan strategis kepada Dewan Pengurus Provinsi demi kesinambungan visi asosiasi.',
                'icon'       => 'manage_accounts',
                'urutan'     => 6,
            ],
            [
                'slug'       => 'setprov',
                'nama'       => 'Setprov',
                'deskripsi'  => 'Sekretariat Pelayanan Eksekutif DPP INKINDO DKI Jakarta',
                'intro_text' => null,
                'icon'       => 'admin_panel_settings',
                'urutan'     => 7,
            ],
        ];
    }

    public static function ensureInitialized(): void
    {
        try {
            $db = Database::connect();

            if (!$db->tableExists('cms_tentang_profile')) {
                $db->query("
                    CREATE TABLE IF NOT EXISTS `cms_tentang_profile` (
                        `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                        `hero_title` VARCHAR(255) NOT NULL DEFAULT 'Tentang Kami',
                        `hero_subtitle` TEXT NULL,
                        `hero_image` VARCHAR(255) NOT NULL DEFAULT 'assets/jakarta3.jpg',
                        `sejarah_heading` VARCHAR(255) NOT NULL DEFAULT 'Sejarah INKINDO DKI Jakarta',
                        `sejarah_content` MEDIUMTEXT NULL,
                        `sejarah_image` VARCHAR(255) NOT NULL DEFAULT 'assets/hero-bg.jpg',
                        `visimisi_heading` VARCHAR(255) NOT NULL DEFAULT 'Visi & Misi',
                        `visimisi_bg_image` VARCHAR(255) NOT NULL DEFAULT 'assets/jakarta3.jpg',
                        `visi_title` VARCHAR(100) NOT NULL DEFAULT 'Visi',
                        `visi_text` TEXT NULL,
                        `misi_title` VARCHAR(100) NOT NULL DEFAULT 'Misi',
                        `misi_items_json` MEDIUMTEXT NULL,
                        `kode_etik_heading` VARCHAR(255) NOT NULL DEFAULT 'Kode Etik',
                        `kode_etik_subtitle` TEXT NULL,
                        `kode_etik_items_json` MEDIUMTEXT NULL,
                        `updated_at` DATETIME NULL DEFAULT NULL,
                        PRIMARY KEY (`id`)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
                ");
            }

            if ((int) $db->table('cms_tentang_profile')->countAllResults() === 0) {
                $profile = self::getDefaultProfileData();
                $profile['updated_at'] = date('Y-m-d H:i:s');
                $db->table('cms_tentang_profile')->insert($profile);
            }

            if (!$db->tableExists('cms_org_units')) {
                $db->query("
                    CREATE TABLE IF NOT EXISTS `cms_org_units` (
                        `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                        `slug` VARCHAR(50) NOT NULL,
                        `nama` VARCHAR(100) NOT NULL,
                        `deskripsi` VARCHAR(255) NULL DEFAULT NULL,
                        `intro_text` TEXT NULL,
                        `icon` VARCHAR(100) NOT NULL DEFAULT 'account_balance',
                        `urutan` INT NOT NULL DEFAULT 1,
                        `updated_at` DATETIME NULL DEFAULT NULL,
                        PRIMARY KEY (`id`),
                        UNIQUE KEY `uniq_slug` (`slug`)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
                ");
            }

            if (!$db->tableExists('cms_org_groups')) {
                $db->query("
                    CREATE TABLE IF NOT EXISTS `cms_org_groups` (
                        `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                        `unit_slug` VARCHAR(50) NOT NULL,
                        `nama_kelompok` VARCHAR(255) NOT NULL,
                        `kode_badge` VARCHAR(30) NULL DEFAULT NULL,
                        `tipe_tampilan` VARCHAR(30) NOT NULL DEFAULT 'officers',
                        `section_label` VARCHAR(100) NULL DEFAULT NULL,
                        `is_full_width` TINYINT(1) NOT NULL DEFAULT 0,
                        `urutan` INT NOT NULL DEFAULT 1,
                        `is_active` TINYINT(1) NOT NULL DEFAULT 1,
                        `created_at` DATETIME NULL DEFAULT NULL,
                        `updated_at` DATETIME NULL DEFAULT NULL,
                        PRIMARY KEY (`id`),
                        KEY `idx_unit_slug` (`unit_slug`)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
                ");
            }

            if (!$db->tableExists('cms_org_members')) {
                $db->query("
                    CREATE TABLE IF NOT EXISTS `cms_org_members` (
                        `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                        `unit_slug` VARCHAR(50) NOT NULL,
                        `group_id` INT UNSIGNED NULL DEFAULT NULL,
                        `jabatan` VARCHAR(255) NOT NULL,
                        `nama` VARCHAR(255) NOT NULL,
                        `keterangan_badge` VARCHAR(100) NULL DEFAULT NULL,
                        `is_highlight` TINYINT(1) NOT NULL DEFAULT 0,
                        `urutan` INT NOT NULL DEFAULT 1,
                        `is_active` TINYINT(1) NOT NULL DEFAULT 1,
                        `created_at` DATETIME NULL DEFAULT NULL,
                        `updated_at` DATETIME NULL DEFAULT NULL,
                        PRIMARY KEY (`id`),
                        KEY `idx_unit_group` (`unit_slug`, `group_id`)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
                ");
            }

            if ((int) $db->table('cms_org_units')->countAllResults() === 0) {
                $now = date('Y-m-d H:i:s');
                $units = self::getDefaultUnits();
                foreach ($units as &$u) {
                    $u['updated_at'] = $now;
                }
                unset($u);
                $db->table('cms_org_units')->insertBatch($units);

                // Seed initial groups and personnel once on initial creation.
                self::seedDefaultOrganizationData($db, $now);
            }
        } catch (\Throwable $e) {
            // Ignore DB initialization errors gracefully
        }
    }

    private static function seedDefaultOrganizationData($db, string $now): void
    {
        $insertGroupAndMembers = function (
            string $unitSlug,
            string $groupName,
            ?string $kodeBadge,
            string $tipeTampilan,
            ?string $sectionLabel,
            int $isFullWidth,
            int $groupOrder,
            array $members
        ) use ($db, $now): void {
            $db->table('cms_org_groups')->insert([
                'unit_slug'     => $unitSlug,
                'nama_kelompok' => $groupName,
                'kode_badge'    => $kodeBadge,
                'tipe_tampilan' => $tipeTampilan,
                'section_label' => $sectionLabel,
                'is_full_width' => $isFullWidth,
                'urutan'        => $groupOrder,
                'is_active'     => 1,
                'created_at'    => $now,
                'updated_at'    => $now,
            ]);
            $groupId = (int) $db->insertID();

            $rows = [];
            foreach ($members as $idx => $m) {
                $rows[] = [
                    'unit_slug'        => $unitSlug,
                    'group_id'         => $groupId,
                    'jabatan'          => $m['jabatan'],
                    'nama'             => $m['nama'],
                    'keterangan_badge' => $m['keterangan_badge'] ?? null,
                    'is_highlight'     => $m['is_highlight'] ?? 0,
                    'urutan'           => $idx + 1,
                    'is_active'        => 1,
                    'created_at'       => $now,
                    'updated_at'       => $now,
                ];
            }
            if (!empty($rows)) {
                $db->table('cms_org_members')->insertBatch($rows);
            }
        };

        // 1. DPPH
        $insertGroupAndMembers('dpph', 'Pimpinan Harian', null, 'leader', null, 0, 1, [
            [
                'jabatan'          => 'Ketua DPP INKINDO DKI Jakarta',
                'nama'             => 'Afsdyah Eky Vitalina, SP., M.Si.',
                'keterangan_badge' => 'Pimpinan Harian',
                'is_highlight'     => 1,
            ],
        ]);

        $insertGroupAndMembers('dpph', 'Kesekretariatan & Kebendaharaan', '1', 'officers', null, 0, 2, [
            ['jabatan' => 'Sekretaris',       'nama' => 'Aditya Prakarsa Yasin, BSc, M.M'],
            ['jabatan' => 'Wakil Sekretaris', 'nama' => 'Jufriady Tanjung, S.T., M.T'],
            ['jabatan' => 'Bendahara',        'nama' => 'Tri Murdiatmoko, S.E.'],
            ['jabatan' => 'Wakil Bendahara',  'nama' => 'Andina Larasati, S.Hum., M.M.'],
        ]);

        $bidangList = [
            ['1) Pranata Usaha',                       'Ir. M. Saifullah, S.T., M.M., IPM.', 'M. Aufa Ibadur Rahman, S.E., PMP'],
            ['2) Pelayanan & Pemberdayaan Anggota',    'Pensong Benny, S.E., M.Si',          'Toni Haryanto, S.T.'],
            ['3) Penguatan Organisasi',                'Fakhrudin, M.Si, Psikolog',          'Hendri Prasetyo, S.IP'],
            ['4) Pengembangan Jasa Konstruksi',        'Ir. Mangiring Purba',                'Yanwar Anang Prabowo'],
            ['5) Pengembangan Non Jasa Konstruksi',    'Ir. Chandra Rambey, M.Sc',           'Diana Harmaini Syafei, S.T'],
            ['6) Perencanaan Strategis & Monitoring',  'Ir. Rachmadi',                       'Taufan Setia Nusantara, S.E.'],
            ['7) Kerjasama Antar Lembaga',             'Ir. Agus Sudjatmiko',                'Ir. Irza Ratnasari'],
            ['8) Hukum & Perlindungan Anggota',        'Dewisari, M.M',                      'Dr. Ery Sunandar, S.Si., M.Si., SH'],
            ['9) Peluang Pasar Internasional',         'Mohamad Imam Machfudin, S.T.',       'Herdiansa Putra, S.P.'],
            ['10) Digitalisasi & Humas',               'Munif Sofian, S.Kom',                'Aditya Tubagus Maulana, S.T.'],
            ['11) ESG & Keberlanjutan',                'Dr. Zefri, M.Si',                    'Achmad Chairul Wafa, S.I.Kom'],
            ['12) Pasar Swasta & BUMN',                'Ir. Setyo Triyono, IPM., ACPE.',     'Ar. Doti Windajani, M.T., IAI.'],
        ];
        foreach ($bidangList as $bIdx => $b) {
            $insertGroupAndMembers('dpph', $b[0], '2', 'bidang', 'Bidang Kerja DPPH', 0, 3 + $bIdx, [
                ['jabatan' => 'Wakil Ketua',      'nama' => $b[1]],
                ['jabatan' => 'Wakil Sekretaris', 'nama' => $b[2]],
            ]);
        }

        // 2. Badan - Badan
        $badanList = [
            [
                'nama' => 'Badan Sertifikasi Anggota Provinsi (BSAP)',
                'kode' => 'I',
                'full' => 0,
                'rows' => [
                    ['jabatan' => 'Ketua',      'nama' => 'Ir. Mangiring Purba'],
                    ['jabatan' => 'Sekretaris', 'nama' => 'Munif Sofian, S.Kom'],
                    ['jabatan' => 'Bendahara',  'nama' => 'Andina Larasati, S.Hum., M.M.'],
                    ['jabatan' => 'Anggota',    'nama' => 'Baharun Nazir, S.E.'],
                    ['jabatan' => 'Anggota',    'nama' => 'Rizky Agung, S.E'],
                ],
            ],
            [
                'nama' => 'Badan Advokasi dan Mediasi Anggota (BAMA)',
                'kode' => 'II',
                'full' => 0,
                'rows' => [
                    ['jabatan' => 'Ketua',       'nama' => 'Dr. Ery Sunandar, S.Si., M.Si., S.H., M.H.'],
                    ['jabatan' => 'Wakil Ketua', 'nama' => 'Denok Wahyuni, S.H.'],
                    ['jabatan' => 'Sekretaris',  'nama' => 'Ismail Yusuf, S.E., M.M.'],
                    ['jabatan' => 'Anggota',     'nama' => 'Warwo, S.T | Abel Kautsar'],
                    ['jabatan' => 'Anggota',     'nama' => 'Achmad Fahrur Rozi, S.Sos | Ir. Reinhart T.P.S'],
                ],
            ],
            [
                'nama' => 'Badan Riset dan Bakti Profesi (BRBP)',
                'kode' => 'III',
                'full' => 0,
                'rows' => [
                    ['jabatan' => 'Ketua',       'nama' => 'Ir. Halomoan Tambun'],
                    ['jabatan' => 'Wakil Ketua', 'nama' => 'Achmad Henrawan, S.T.'],
                    ['jabatan' => 'Anggota',     'nama' => 'Bayu Sulistianto, S.I. | Fahriza, S.P., M.P.'],
                    ['jabatan' => 'Anggota',     'nama' => 'Ir. Budhianto | Ishari Thaib, S.E.'],
                    ['jabatan' => 'Anggota',     'nama' => 'DR. Ing. Ir. I Gede Oka Sindhu Pribadi, M.Sc'],
                ],
            ],
            [
                'nama' => 'Badan Pengembangan Kapasitas Anggota (BPKA)',
                'kode' => 'IV',
                'full' => 0,
                'rows' => [
                    ['jabatan' => 'Ketua',       'nama' => 'Derry P Sitorus, AT., S.E.'],
                    ['jabatan' => 'Wakil Ketua', 'nama' => 'Siti Aisyah TW, S.E., M.M'],
                    ['jabatan' => 'Sekretaris',  'nama' => 'Ir. Zaenal Mustofa Djawahir'],
                    ['jabatan' => 'Anggota',     'nama' => 'Ir. Syaripin | Harsyaf Nurman D., S.T.'],
                    ['jabatan' => 'Anggota',     'nama' => 'Steven Valerian B. | Gerrits S.B.C. Udjung'],
                ],
            ],
            [
                'nama' => 'Badan Pengkajian Kebijakan & Regulasi',
                'kode' => 'V',
                'full' => 1,
                'rows' => [
                    ['jabatan' => 'Ketua',       'nama' => 'Islana Jati Waluya, S.E., M.I.Kom'],
                    ['jabatan' => 'Wakil Ketua', 'nama' => 'Ir. Ratna Ningrum, M.B.A'],
                    ['jabatan' => 'Sekretaris',  'nama' => 'Fahmi Anshari'],
                    ['jabatan' => 'Anggota',     'nama' => 'Rahyono, S.Kom | Satrio Rama W., M.Sc.'],
                    ['jabatan' => 'Anggota',     'nama' => 'Rimong Rukmiarta, MT. | Ir. Lila Andari'],
                    ['jabatan' => 'Anggota',     'nama' => 'Ir. Tati Lanniari Pulungan | Adung Kurnaen'],
                ],
            ],
        ];
        foreach ($badanList as $bdIdx => $bd) {
            $insertGroupAndMembers('badan', $bd['nama'], $bd['kode'], 'badan', null, $bd['full'], $bdIdx + 1, $bd['rows']);
        }

        // 3. DKP
        $insertGroupAndMembers('dkp', 'Daftar Pengurus DKP', null, 'officers', null, 0, 1, [
            ['jabatan' => 'Ketua merangkap Anggota',       'nama' => 'Ir. Bambang Soendaroe, M.T.',           'is_highlight' => 1],
            ['jabatan' => 'Wakil Ketua merangkap Anggota', 'nama' => 'Ir. Mukhsin Moechtar, IPU., ACPE.',     'is_highlight' => 1],
            ['jabatan' => 'Sekretaris merangkap Anggota',  'nama' => 'Ir. Novia Damayanti, M.B.A.',           'is_highlight' => 1],
            ['jabatan' => 'Anggota',                       'nama' => 'Ir. Istanto Oerip, IPU.'],
            ['jabatan' => 'Anggota',                       'nama' => 'Ir. Jumadi S. Witopawiro, M.S.'],
            ['jabatan' => 'Anggota',                       'nama' => 'Ir. Stefanus Hindarko'],
            ['jabatan' => 'Anggota',                       'nama' => 'Dr. Ir. John P. Pantouw, M.S.'],
            ['jabatan' => 'Anggota',                       'nama' => 'Ir. Jasman, S.T., M.T.'],
            ['jabatan' => 'Anggota',                       'nama' => 'Ir. Yulianto'],
            ['jabatan' => 'Anggota',                       'nama' => 'Ir. Siti Rochamayanti, M.M.'],
            ['jabatan' => 'Anggota',                       'nama' => 'Ir. Yudi Setiabudi'],
        ]);

        // 4. DPOP
        $insertGroupAndMembers('dpop', 'Daftar Pengurus DPOP', null, 'officers', null, 0, 1, [
            ['jabatan' => 'Ketua',      'nama' => 'Imam Hartawan, S.T., M.T.',    'is_highlight' => 1],
            ['jabatan' => 'Sekretaris', 'nama' => 'Ir. Anton Gerbono',            'is_highlight' => 1],
            ['jabatan' => 'Anggota',    'nama' => 'Drs. Arry Budiyanto, M.Dev.Plg'],
            ['jabatan' => 'Anggota',    'nama' => 'Ir. Suwendi, M.Sc.'],
            ['jabatan' => 'Anggota',    'nama' => 'Ir. Endang Nurzaman'],
            ['jabatan' => 'Anggota',    'nama' => 'Jusarwanto, S.E., Ak.'],
            ['jabatan' => 'Anggota',    'nama' => 'Dr. Ir. Lamidi, MTP, M.M.'],
        ]);
    }
}

