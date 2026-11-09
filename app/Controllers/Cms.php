<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\CmsBeritaModel;
use App\Models\CmsHeroBackgroundModel;
use App\Models\CmsHeroBannerModel;
use App\Models\CmsHeroPhraseModel;

class Cms extends BaseController
{
    protected $cmsModel;
    protected $bannerModel;
    protected $phraseModel;
    protected $backgroundModel;

    public function __construct()
    {
        $this->cmsModel = new CmsBeritaModel();
        $this->bannerModel = new CmsHeroBannerModel();
        $this->phraseModel = new CmsHeroPhraseModel();
        $this->backgroundModel = new CmsHeroBackgroundModel();
        $this->ensureHeroBannerTable();
    }

    private function ensureHeroBannerTable(): void
    {
        try {
            $db = \Config\Database::connect();
            if (!$db->tableExists('cms_hero_banners')) {
                $db->query("
                    CREATE TABLE IF NOT EXISTS `cms_hero_banners` (
                        `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                        `mode_sumber` VARCHAR(20) NOT NULL DEFAULT 'custom',
                        `berita_id` INT UNSIGNED NULL DEFAULT NULL,
                        `subjudul` VARCHAR(255) NULL DEFAULT NULL,
                        `judul` VARCHAR(255) NOT NULL,
                        `gambar` VARCHAR(255) NOT NULL,
                        `target_url` VARCHAR(255) NULL DEFAULT '#berita',
                        `urutan` INT NOT NULL DEFAULT 1,
                        `is_active` TINYINT(1) NOT NULL DEFAULT 1,
                        `created_at` DATETIME NULL DEFAULT NULL,
                        `updated_at` DATETIME NULL DEFAULT NULL,
                        PRIMARY KEY (`id`)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
                ");
            }

            $count = (int) $db->table('cms_hero_banners')->countAllResults();
            if ($count === 0) {
                $now = date('Y-m-d H:i:s');
                $defaultBanners = [
                    [
                        'mode_sumber' => 'custom',
                        'berita_id'   => null,
                        'subjudul'    => 'DPP Masa Bakti 2026–2030',
                        'judul'       => 'Dewan Pengurus Provinsi INKINDO DKI Jakarta',
                        'gambar'      => 'assets/hero-bg.jpg',
                        'target_url'  => '#tentang',
                        'urutan'      => 1,
                        'is_active'   => 1,
                        'created_at'  => $now,
                        'updated_at'  => $now,
                    ],
                    [
                        'mode_sumber' => 'custom',
                        'berita_id'   => null,
                        'subjudul'    => 'Kemitraan Kampus',
                        'judul'       => 'INKINDO DKI Goes to Campus',
                        'gambar'      => 'assets/gambar.png',
                        'target_url'  => '#berita',
                        'urutan'      => 2,
                        'is_active'   => 1,
                        'created_at'  => $now,
                        'updated_at'  => $now,
                    ],
                    [
                        'mode_sumber' => 'custom',
                        'berita_id'   => null,
                        'subjudul'    => 'Agenda Mendatang',
                        'judul'       => 'Pelatihan Audit Internal ISO 37001',
                        'gambar'      => 'assets/gambar2.jpg',
                        'target_url'  => '#berita',
                        'urutan'      => 3,
                        'is_active'   => 1,
                        'created_at'  => $now,
                        'updated_at'  => $now,
                    ],
                    [
                        'mode_sumber' => 'custom',
                        'berita_id'   => null,
                        'subjudul'    => 'e-Magazine Edisi 79',
                        'judul'       => 'Majalah INKINDO DKI Jakarta',
                        'gambar'      => 'assets/gambar1.jpg',
                        'target_url'  => '#berita',
                        'urutan'      => 4,
                        'is_active'   => 1,
                        'created_at'  => $now,
                        'updated_at'  => $now,
                    ],
                ];
                $db->table('cms_hero_banners')->insertBatch($defaultBanners);
            }

            if (!$db->tableExists('cms_hero_phrases')) {
                $db->query("
                    CREATE TABLE IF NOT EXISTS `cms_hero_phrases` (
                        `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                        `teks` VARCHAR(255) NOT NULL,
                        `urutan` INT NOT NULL DEFAULT 1,
                        `is_active` TINYINT(1) NOT NULL DEFAULT 1,
                        `created_at` DATETIME NULL DEFAULT NULL,
                        `updated_at` DATETIME NULL DEFAULT NULL,
                        PRIMARY KEY (`id`)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
                ");
            }

            if ((int) $db->table('cms_hero_phrases')->countAllResults() === 0) {
                $now = date('Y-m-d H:i:s');
                $defaultPhrases = [
                    ['teks' => 'Selamat Datang', 'urutan' => 1, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
                    ['teks' => 'DPP INKINDO DKI Jakarta', 'urutan' => 2, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
                    ['teks' => 'Ekosistem Konsultansi Digital', 'urutan' => 3, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
                    ['teks' => 'Solusi Jasa Konsultansi Terpercaya', 'urutan' => 4, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
                    ['teks' => 'Integritas, Inovasi & Profesionalisme', 'urutan' => 5, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
                ];
                $db->table('cms_hero_phrases')->insertBatch($defaultPhrases);
            }

            if (!$db->tableExists('cms_hero_backgrounds')) {
                $db->query("
                    CREATE TABLE IF NOT EXISTS `cms_hero_backgrounds` (
                        `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                        `judul` VARCHAR(255) NOT NULL,
                        `gambar` VARCHAR(255) NOT NULL,
                        `urutan` INT NOT NULL DEFAULT 1,
                        `is_active` TINYINT(1) NOT NULL DEFAULT 1,
                        `created_at` DATETIME NULL DEFAULT NULL,
                        `updated_at` DATETIME NULL DEFAULT NULL,
                        PRIMARY KEY (`id`)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
                ");
            }

            if ((int) $db->table('cms_hero_backgrounds')->countAllResults() === 0) {
                $now = date('Y-m-d H:i:s');
                $defaultBgs = [
                    ['judul' => 'Panorama Kota DKI Jakarta', 'gambar' => 'assets/jakartahome1.jpg', 'urutan' => 1, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
                    ['judul' => 'Jakarta International Stadium (JIS)', 'gambar' => 'assets/jakartahome3.jpg', 'urutan' => 2, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
                ];
                $db->table('cms_hero_backgrounds')->insertBatch($defaultBgs);
            }

            if ($db->tableExists('cms_berita') && !$db->fieldExists('publikasi_slot', 'cms_berita')) {
                $db->query("ALTER TABLE `cms_berita` ADD COLUMN `publikasi_slot` INT NOT NULL DEFAULT 0 AFTER `homepage_slot`");
            }
        } catch (\Throwable $e) {
            // Ignore DB init exception gracefully
        }
    }

    private function handleBase64ImageUpload(string $base64Data, string $subFolder = 'cms_uploads'): ?string
    {
        if (!preg_match('/^data:image\/(jpeg|jpg|png|webp);base64,(.+)$/i', trim($base64Data), $matches)) {
            return null;
        }

        $ext = strtolower($matches[1]);
        if ($ext === 'jpeg') {
            $ext = 'jpg';
        }
        $binary = base64_decode($matches[2], true);
        if ($binary === false || strlen($binary) < 64) {
            return null;
        }

        $destDir = FCPATH . str_replace('/', DIRECTORY_SEPARATOR, $subFolder);
        if (!is_dir($destDir)) {
            mkdir($destDir, 0777, true);
        }

        $newName = 'banner_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
        $fullPath = $destDir . DIRECTORY_SEPARATOR . $newName;
        if (file_put_contents($fullPath, $binary) === false) {
            return null;
        }

        $relativePath = trim($subFolder, '/') . '/' . $newName;
        $this->syncFileToProjectWeb($relativePath);

        return $relativePath;
    }

    private function buildTargetUrlForBerita(array $row): string
    {
        $tipe = (string) ($row['tipe'] ?? 'rilis');
        $slug = (string) ($row['slug'] ?? '');

        if (in_array($tipe, ['agenda', 'rilis', 'publikasi'], true)) {
            return 'berita-detail.html?type=' . rawurlencode($tipe) . '&id=' . rawurlencode($slug);
        }
        if ($tipe === 'emagazine') {
            return 'read-magazine.html?id=' . rawurlencode($slug);
        }
        if ($tipe === 'galeri') {
            return 'berita.html?tab=galeri&open=' . rawurlencode($slug);
        }
        if ($tipe === 'karya') {
            return 'berita.html?tab=karya&open=' . rawurlencode($slug);
        }
        return 'berita-detail.html?type=' . rawurlencode($tipe) . '&id=' . rawurlencode($slug);
    }

    private function ensureAdmin()
    {
        if (session()->get('role') !== 'admin') {
            return redirect()->to('/dashboard');
        }
        return null;
    }

    private function syncFileToProjectWeb(string $relativePath): void
    {
        $source = FCPATH . str_replace('/', DIRECTORY_SEPARATOR, $relativePath);
        $targetDir = 'D:\\magang inkindo\\ProjectWeb\\' . dirname(str_replace('/', DIRECTORY_SEPARATOR, $relativePath));
        $target = 'D:\\magang inkindo\\ProjectWeb\\' . str_replace('/', DIRECTORY_SEPARATOR, $relativePath);

        if (is_file($source)) {
            if (!is_dir($targetDir)) {
                @mkdir($targetDir, 0777, true);
            }
            @copy($source, $target);
        }
    }

    private function handleSingleUpload(string $fieldName, array $allowedExts, string $subFolder = 'cms_uploads'): ?string
    {
        $file = $this->request->getFile($fieldName);
        if (!$file || !$file->isValid() || $file->hasMoved()) {
            return null;
        }

        $ext = strtolower($file->getExtension() ?: pathinfo($file->getClientName(), PATHINFO_EXTENSION));
        if (!in_array($ext, $allowedExts, true)) {
            return null;
        }

        $destDir = FCPATH . str_replace('/', DIRECTORY_SEPARATOR, $subFolder);
        if (!is_dir($destDir)) {
            mkdir($destDir, 0777, true);
        }

        $newName = 'cms_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
        $file->move($destDir, $newName);

        $relativePath = trim($subFolder, '/') . '/' . $newName;
        $this->syncFileToProjectWeb($relativePath);

        return $relativePath;
    }

    private function handleMultipleImageUploads(string $fieldName, string $subFolder = 'cms_uploads/galeri'): array
    {
        $files = $this->request->getFileMultiple($fieldName);
        if (!$files) {
            return [];
        }

        $allowedExts = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
        $destDir = FCPATH . str_replace('/', DIRECTORY_SEPARATOR, $subFolder);
        if (!is_dir($destDir)) {
            mkdir($destDir, 0777, true);
        }

        $uploadedPaths = [];
        foreach ($files as $file) {
            if (!$file || !$file->isValid() || $file->hasMoved()) {
                continue;
            }
            $ext = strtolower($file->getExtension() ?: pathinfo($file->getClientName(), PATHINFO_EXTENSION));
            if (!in_array($ext, $allowedExts, true)) {
                continue;
            }
            $newName = 'galeri_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
            $file->move($destDir, $newName);
            $relativePath = trim($subFolder, '/') . '/' . $newName;
            $this->syncFileToProjectWeb($relativePath);
            $uploadedPaths[] = $relativePath;
        }

        return $uploadedPaths;
    }

    private function formatTanggalIndo(?string $dateStr): string
    {
        if (!$dateStr) {
            return date('d M Y');
        }
        $ts = strtotime($dateStr);
        if (!$ts) {
            return $dateStr;
        }
        $bulan = [
            1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr',
            5 => 'Mei', 6 => 'Jun', 7 => 'Jul', 8 => 'Agu',
            9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des'
        ];
        $m = (int) date('n', $ts);
        return date('d', $ts) . ' ' . ($bulan[$m] ?? date('M', $ts)) . ' ' . date('Y', $ts);
    }

    public function index()
    {
        if ($redir = $this->ensureAdmin()) {
            return $redir;
        }

        $filterTipe = trim((string) $this->request->getGet('tipe'));
        $validTypes = ['agenda', 'rilis', 'publikasi', 'galeri', 'emagazine', 'karya'];

        $allItems = $this->cmsModel->orderBy('tanggal', 'DESC')->orderBy('id', 'DESC')->findAll();
        if (in_array($filterTipe, $validTypes, true)) {
            $items = array_values(array_filter($allItems, fn($row) => $row['tipe'] === $filterTipe));
        } else {
            $filterTipe = '';
            $items = $allItems;
        }

        $slot1 = null;
        $slot2 = null;
        $slot3 = null;
        foreach ($allItems as $row) {
            $slot = (int) ($row['homepage_slot'] ?? 0);
            if ($slot === 1) $slot1 = $row;
            if ($slot === 2) $slot2 = $row;
            if ($slot === 3) $slot3 = $row;
        }

        $counts = [
            'all' => count($allItems),
            'agenda' => 0,
            'rilis' => 0,
            'publikasi' => 0,
            'galeri' => 0,
            'emagazine' => 0,
            'karya' => 0,
        ];
        foreach ($allItems as $r) {
            $t = $r['tipe'] ?? '';
            if (isset($counts[$t])) {
                $counts[$t]++;
            }
        }

        return view('cms/index', [
            'title'      => 'Admin CMS - Berita & Informasi',
            'items'      => $items,
            'allItems'   => $allItems,
            'filterTipe' => $filterTipe,
            'counts'     => $counts,
            'slot1'      => $slot1,
            'slot2'      => $slot2,
            'slot3'      => $slot3,
        ]);
    }

    public function homepage()
    {
        if ($redir = $this->ensureAdmin()) {
            return $redir;
        }

        $allItems = $this->cmsModel->orderBy('tanggal', 'DESC')->orderBy('id', 'DESC')->findAll();
        foreach ($allItems as &$itemRow) {
            $itemRow['target_url'] = $this->buildTargetUrlForBerita($itemRow);
        }
        unset($itemRow);

        $slot1 = null;
        $slot2 = null;
        $slot3 = null;
        $pubSlots = array_fill(1, 8, null);
        foreach ($allItems as $row) {
            $slot = (int) ($row['homepage_slot'] ?? 0);
            if ($slot === 1) $slot1 = $row;
            if ($slot === 2) $slot2 = $row;
            if ($slot === 3) $slot3 = $row;

            $pSlot = (int) ($row['publikasi_slot'] ?? 0);
            if ($pSlot >= 1 && $pSlot <= 8) {
                $pubSlots[$pSlot] = $row;
            }
        }

        $banners = $this->bannerModel->orderBy('urutan', 'ASC')->orderBy('id', 'ASC')->findAll();
        $phrases = $this->phraseModel->orderBy('urutan', 'ASC')->orderBy('id', 'ASC')->findAll();
        $backgrounds = $this->backgroundModel->orderBy('urutan', 'ASC')->orderBy('id', 'ASC')->findAll();

        return view('cms/homepage', [
            'title'       => 'Admin CMS - Halaman Utama',
            'allItems'    => $allItems,
            'banners'     => $banners,
            'phrases'     => $phrases,
            'backgrounds' => $backgrounds,
            'slot1'       => $slot1,
            'slot2'       => $slot2,
            'slot3'       => $slot3,
            'pubSlots'    => $pubSlots,
        ]);
    }

    public function updateHomepageSlots()
    {
        if ($redir = $this->ensureAdmin()) {
            return $redir;
        }

        $slot1Id = (int) $this->request->getPost('slot_1');
        $slot2Id = (int) $this->request->getPost('slot_2');
        $slot3Id = (int) $this->request->getPost('slot_3');

        $db = \Config\Database::connect();
        $db->table('cms_berita')->update(['homepage_slot' => 0]);

        if ($slot1Id > 0) {
            $db->table('cms_berita')->where('id', $slot1Id)->update(['homepage_slot' => 1]);
        }
        if ($slot2Id > 0 && $slot2Id !== $slot1Id) {
            $db->table('cms_berita')->where('id', $slot2Id)->update(['homepage_slot' => 2]);
        }
        if ($slot3Id > 0 && $slot3Id !== $slot1Id && $slot3Id !== $slot2Id) {
            $db->table('cms_berita')->where('id', $slot3Id)->update(['homepage_slot' => 3]);
        }

        session()->setFlashdata('pesan', 'Pengaturan 3 konten Berita Pilihan pada Halaman Utama (Beranda WebProfile) berhasil diperbarui.');
        return redirect()->to('/cms/homepage');
    }

    public function updatePublikasiSlots()
    {
        if ($redir = $this->ensureAdmin()) {
            return $redir;
        }

        $db = \Config\Database::connect();
        if (!$db->fieldExists('publikasi_slot', 'cms_berita')) {
            $db->query("ALTER TABLE `cms_berita` ADD COLUMN `publikasi_slot` INT NOT NULL DEFAULT 0 AFTER `homepage_slot`");
        }

        $db->table('cms_berita')->update(['publikasi_slot' => 0]);

        $usedIds = [];
        for ($i = 1; $i <= 8; $i++) {
            $itemId = (int) $this->request->getPost('pub_slot_' . $i);
            if ($itemId > 0 && !in_array($itemId, $usedIds, true)) {
                $db->table('cms_berita')->where('id', $itemId)->update(['publikasi_slot' => $i]);
                $usedIds[] = $itemId;
            }
        }

        session()->setFlashdata('pesan', 'Pengaturan 8 konten Publikasi (Grid 4x2) pada Halaman Utama berhasil diperbarui.');
        return redirect()->to('/cms/homepage');
    }

    public function saveHeroBanner()
    {
        if ($redir = $this->ensureAdmin()) {
            return $redir;
        }

        $bannerId    = (int) $this->request->getPost('banner_id');
        $modeSumber  = trim((string) $this->request->getPost('mode_sumber')) === 'berita' ? 'berita' : 'custom';
        $beritaId    = (int) $this->request->getPost('berita_id');
        $subjudul    = trim((string) $this->request->getPost('subjudul'));
        $judul       = trim((string) $this->request->getPost('judul'));
        $targetUrl   = trim((string) $this->request->getPost('target_url'));
        $urutan      = max(1, (int) ($this->request->getPost('urutan') ?: 1));
        $isActive    = $this->request->getPost('is_active') !== null ? (int) $this->request->getPost('is_active') : 1;
        $existingImg = trim((string) $this->request->getPost('existing_gambar'));
        $croppedData = (string) $this->request->getPost('cropped_image_data');

        $selectedBerita = null;
        if ($modeSumber === 'berita' && $beritaId > 0) {
            $selectedBerita = $this->cmsModel->find($beritaId);
        }

        if ($selectedBerita) {
            if ($judul === '') {
                $judul = (string) ($selectedBerita['judul'] ?? '');
            }
            if ($subjudul === '') {
                $subjudul = (string) ($selectedBerita['kategori_label'] ?: $selectedBerita['tanggal_label'] ?: 'Berita & Informasi');
            }
            if ($targetUrl === '' || $targetUrl === '#berita') {
                $targetUrl = $this->buildTargetUrlForBerita($selectedBerita);
            }
        }

        if ($judul === '') {
            session()->setFlashdata('error', 'Judul Banner Utama wajib diisi.');
            return redirect()->to('/cms/homepage');
        }

        if ($targetUrl === '') {
            $targetUrl = '#berita';
        }

        // Proses Gambar: Prioritas 1 = Hasil Crop/Penyesuaian, Prioritas 2 = Upload File Baru, Prioritas 3 = Gambar Berita / Existing
        $gambarPath = null;
        if ($croppedData !== '') {
            $gambarPath = $this->handleBase64ImageUpload($croppedData, 'cms_uploads');
        }
        if (!$gambarPath) {
            $gambarPath = $this->handleSingleUpload('gambar_file', ['jpg', 'jpeg', 'png', 'webp', 'gif'], 'cms_uploads');
        }
        if (!$gambarPath && $selectedBerita && ($existingImg === '' || $this->request->getPost('use_berita_image') === '1')) {
            $gambarPath = (string) ($selectedBerita['gambar'] ?: 'assets/gambar.png');
        }
        if (!$gambarPath) {
            $gambarPath = $existingImg ?: ($selectedBerita['gambar'] ?? 'assets/hero-bg.jpg');
        }

        $payload = [
            'mode_sumber' => $modeSumber,
            'berita_id'   => ($modeSumber === 'berita' && $beritaId > 0) ? $beritaId : null,
            'subjudul'    => $subjudul ?: null,
            'judul'       => $judul,
            'gambar'      => $gambarPath,
            'target_url'  => $targetUrl,
            'urutan'      => $urutan,
            'is_active'   => $isActive ? 1 : 0,
        ];

        if ($bannerId > 0 && $this->bannerModel->find($bannerId)) {
            $this->bannerModel->update($bannerId, $payload);
            session()->setFlashdata('pesan', 'Slide Banner Utama berhasil diperbarui.');
        } else {
            $this->bannerModel->insert($payload);
            session()->setFlashdata('pesan', 'Slide Banner Utama baru berhasil ditambahkan.');
        }

        return redirect()->to('/cms/homepage');
    }

    public function deleteHeroBanner($id)
    {
        if ($redir = $this->ensureAdmin()) {
            return $redir;
        }

        $banner = $this->bannerModel->find((int) $id);
        if ($banner) {
            $this->bannerModel->delete((int) $id);
            session()->setFlashdata('pesan', 'Slide Banner Utama berhasil dihapus.');
        }

        return redirect()->to('/cms/homepage');
    }

    public function toggleHeroBanner($id)
    {
        if ($redir = $this->ensureAdmin()) {
            return $redir;
        }

        $banner = $this->bannerModel->find((int) $id);
        if ($banner) {
            $newState = ((int) ($banner['is_active'] ?? 1) === 1) ? 0 : 1;
            $this->bannerModel->update((int) $id, ['is_active' => $newState]);
            session()->setFlashdata('pesan', 'Status tampil Slide Banner Utama berhasil diubah.');
        }

        return redirect()->to('/cms/homepage');
    }

    public function saveHeroPhrase()
    {
        if ($redir = $this->ensureAdmin()) {
            return $redir;
        }

        $phraseId = (int) $this->request->getPost('phrase_id');
        $teks     = trim((string) $this->request->getPost('teks'));
        $urutan   = max(1, (int) ($this->request->getPost('urutan') ?: 1));
        $isActive = $this->request->getPost('is_active') !== null ? (int) $this->request->getPost('is_active') : 1;

        if ($teks === '') {
            session()->setFlashdata('error', 'Teks bergerak tidak boleh kosong.');
            return redirect()->to('/cms/homepage');
        }

        $payload = [
            'teks'      => $teks,
            'urutan'    => $urutan,
            'is_active' => $isActive ? 1 : 0,
        ];

        if ($phraseId > 0 && $this->phraseModel->find($phraseId)) {
            $this->phraseModel->update($phraseId, $payload);
            session()->setFlashdata('pesan', 'Teks bergerak di atas Banner Utama berhasil diperbarui.');
        } else {
            $this->phraseModel->insert($payload);
            session()->setFlashdata('pesan', 'Teks bergerak baru berhasil ditambahkan.');
        }

        return redirect()->to('/cms/homepage');
    }

    public function deleteHeroPhrase($id)
    {
        if ($redir = $this->ensureAdmin()) {
            return $redir;
        }

        $phrase = $this->phraseModel->find((int) $id);
        if ($phrase) {
            $this->phraseModel->delete((int) $id);
            session()->setFlashdata('pesan', 'Teks bergerak berhasil dihapus.');
        }

        return redirect()->to('/cms/homepage');
    }

    public function toggleHeroPhrase($id)
    {
        if ($redir = $this->ensureAdmin()) {
            return $redir;
        }

        $phrase = $this->phraseModel->find((int) $id);
        if ($phrase) {
            $newState = ((int) ($phrase['is_active'] ?? 1) === 1) ? 0 : 1;
            $this->phraseModel->update((int) $id, ['is_active' => $newState]);
            session()->setFlashdata('pesan', 'Status tampil teks bergerak berhasil diubah.');
        }

        return redirect()->to('/cms/homepage');
    }

    public function saveHeroBackground()
    {
        if ($redir = $this->ensureAdmin()) {
            return $redir;
        }

        $bgId        = (int) $this->request->getPost('bg_id');
        $judul       = trim((string) $this->request->getPost('judul')) ?: 'Background Utama Beranda';
        $urutan      = max(1, (int) ($this->request->getPost('urutan') ?: 1));
        $isActive    = $this->request->getPost('is_active') !== null ? (int) $this->request->getPost('is_active') : 1;
        $existingImg = trim((string) $this->request->getPost('existing_gambar'));
        $croppedData = (string) $this->request->getPost('cropped_bg_data');

        $gambarPath = null;
        if ($croppedData !== '') {
            $gambarPath = $this->handleBase64ImageUpload($croppedData, 'cms_uploads');
        }
        if (!$gambarPath) {
            $gambarPath = $this->handleSingleUpload('gambar_file', ['jpg', 'jpeg', 'png', 'webp', 'gif'], 'cms_uploads');
        }
        if (!$gambarPath) {
            $gambarPath = $existingImg ?: 'assets/jakartahome1.jpg';
        }

        $payload = [
            'judul'     => $judul,
            'gambar'    => $gambarPath,
            'urutan'    => $urutan,
            'is_active' => $isActive ? 1 : 0,
        ];

        if ($bgId > 0 && $this->backgroundModel->find($bgId)) {
            $this->backgroundModel->update($bgId, $payload);
            session()->setFlashdata('pesan', 'Gambar Background Utama berhasil diperbarui.');
        } else {
            $this->backgroundModel->insert($payload);
            session()->setFlashdata('pesan', 'Gambar Background Utama baru berhasil ditambahkan.');
        }

        return redirect()->to('/cms/homepage');
    }

    public function deleteHeroBackground($id)
    {
        if ($redir = $this->ensureAdmin()) {
            return $redir;
        }

        $bg = $this->backgroundModel->find((int) $id);
        if ($bg) {
            $this->backgroundModel->delete((int) $id);
            session()->setFlashdata('pesan', 'Gambar Background Utama berhasil dihapus.');
        }

        return redirect()->to('/cms/homepage');
    }

    public function toggleHeroBackground($id)
    {
        if ($redir = $this->ensureAdmin()) {
            return $redir;
        }

        $bg = $this->backgroundModel->find((int) $id);
        if ($bg) {
            $newState = ((int) ($bg['is_active'] ?? 1) === 1) ? 0 : 1;
            $this->backgroundModel->update((int) $id, ['is_active' => $newState]);
            session()->setFlashdata('pesan', 'Status tampil Gambar Background Utama berhasil diubah.');
        }

        return redirect()->to('/cms/homepage');
    }

    public function webAsset(...$segments)
    {
        $relPath = implode('/', $segments);
        $relPath = ltrim(str_replace(['..', '\\'], ['', '/'], $relPath), '/');

        $candidates = [
            FCPATH . str_replace('/', DIRECTORY_SEPARATOR, $relPath),
            'D:\\magang inkindo\\ProjectWeb\\' . str_replace('/', DIRECTORY_SEPARATOR, $relPath),
        ];

        foreach ($candidates as $file) {
            if (is_file($file)) {
                $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                $mimes = [
                    'jpg'  => 'image/jpeg',
                    'jpeg' => 'image/jpeg',
                    'png'  => 'image/png',
                    'webp' => 'image/webp',
                    'gif'  => 'image/gif',
                    'svg'  => 'image/svg+xml',
                ];
                $mime = $mimes[$ext] ?? 'application/octet-stream';
                return $this->response
                    ->setHeader('Content-Type', $mime)
                    ->setHeader('Cache-Control', 'public, max-age=3600')
                    ->setBody((string) file_get_contents($file));
            }
        }

        throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
    }

    public function create()
    {
        if ($redir = $this->ensureAdmin()) {
            return $redir;
        }

        $defaultTipe = trim((string) $this->request->getGet('tipe')) ?: 'rilis';

        return view('cms/form', [
            'title'       => 'Tambah Konten Berita & Informasi',
            'mode'        => 'create',
            'item'        => null,
            'defaultTipe' => $defaultTipe,
        ]);
    }

    public function store()
    {
        if ($redir = $this->ensureAdmin()) {
            return $redir;
        }

        $tipe = trim((string) $this->request->getPost('tipe')) ?: 'rilis';
        $judul = trim((string) $this->request->getPost('judul'));
        if ($judul === '') {
            session()->setFlashdata('error', 'Judul konten wajib diisi.');
            return redirect()->back()->withInput();
        }

        $slug = CmsBeritaModel::generateSlug($judul);
        $tanggal = trim((string) $this->request->getPost('tanggal')) ?: null;
        $tanggalLabel = trim((string) $this->request->getPost('tanggal_label'));
        if ($tanggalLabel === '') {
            if ($tipe === 'karya') {
                $tanggalLabel = $tanggal ? date('Y', strtotime($tanggal)) : date('Y');
            } else {
                $tanggalLabel = $this->formatTanggalIndo($tanggal);
            }
        }

        $gambarPath = $this->handleSingleUpload('gambar_file', ['jpg', 'jpeg', 'png', 'webp', 'gif'], 'cms_uploads');
        if (!$gambarPath) {
            $gambarPath = 'assets/gambar.png';
        }

        $pdfPath = ($tipe === 'emagazine') ? $this->handleSingleUpload('pdf_file', ['pdf'], 'cms_uploads/pdf') : null;

        $galeriPhotos = $this->handleMultipleImageUploads('galeri_files', 'cms_uploads/galeri');
        if ($tipe === 'galeri' && empty($galeriPhotos) && $gambarPath) {
            $galeriPhotos = [$gambarPath];
        }

        $badge = trim((string) $this->request->getPost('badge'));
        if ($tipe === 'galeri' && $badge === '') {
            $badge = count($galeriPhotos) . ' Foto';
        }

        $lokasiMeta = trim((string) $this->request->getPost('lokasi_meta'));
        if ($lokasiMeta === '') {
            if ($tipe === 'emagazine') $lokasiMeta = 'Baca Online';
            elseif ($tipe === 'karya') $lokasiMeta = 'Lihat Deskripsi';
            elseif ($tipe === 'galeri') $lokasiMeta = 'Album Dokumentasi';
            elseif ($tipe === 'publikasi') $lokasiMeta = 'Publikasi Resmi';
        }

        $homepageSlot = (int) $this->request->getPost('homepage_slot');
        if (in_array($homepageSlot, [1, 2, 3], true)) {
            \Config\Database::connect()->table('cms_berita')
                ->where('homepage_slot', $homepageSlot)
                ->update(['homepage_slot' => 0]);
        } else {
            $homepageSlot = 0;
        }

        $this->cmsModel->insert([
            'tipe'             => $tipe,
            'judul'            => $judul,
            'slug'             => $slug,
            'kategori_label'   => trim((string) $this->request->getPost('kategori_label')) ?: 'Informasi',
            'badge'            => $badge ?: null,
            'tanggal'          => $tanggal,
            'tanggal_label'    => $tanggalLabel,
            'penulis'          => trim((string) $this->request->getPost('penulis')) ?: null,
            'lokasi_meta'      => $lokasiMeta ?: null,
            'waktu_kegiatan'   => trim((string) $this->request->getPost('waktu_kegiatan')) ?: null,
            'biaya_kegiatan'   => trim((string) $this->request->getPost('biaya_kegiatan')) ?: null,
            'kuota_kegiatan'   => trim((string) $this->request->getPost('kuota_kegiatan')) ?: null,
            'ukuran_file'      => trim((string) $this->request->getPost('ukuran_file')) ?: null,
            'jumlah_halaman'   => trim((string) $this->request->getPost('jumlah_halaman')) ?: null,
            'gambar'           => $gambarPath,
            'file_pdf'         => $pdfPath,
            'external_url'     => trim((string) $this->request->getPost('external_url')) ?: null,
            'ringkasan'        => trim((string) $this->request->getPost('ringkasan')) ?: null,
            'konten'           => trim((string) $this->request->getPost('konten')) ?: null,
            'galeri_foto_json' => !empty($galeriPhotos) ? json_encode($galeriPhotos) : null,
            'homepage_slot'    => $homepageSlot,
        ]);

        session()->setFlashdata('pesan', 'Data Berita & Informasi berhasil ditambahkan.');
        return redirect()->to('/cms?tipe=' . urlencode($tipe));
    }

    public function edit($id)
    {
        if ($redir = $this->ensureAdmin()) {
            return $redir;
        }

        $item = $this->cmsModel->find($id);
        if (!$item) {
            return redirect()->to('/cms');
        }

        return view('cms/form', [
            'title'       => 'Edit Konten Berita & Informasi',
            'mode'        => 'edit',
            'item'        => $item,
            'defaultTipe' => $item['tipe'],
        ]);
    }

    public function update($id)
    {
        if ($redir = $this->ensureAdmin()) {
            return $redir;
        }

        $item = $this->cmsModel->find($id);
        if (!$item) {
            return redirect()->to('/cms');
        }

        $tipe = trim((string) $this->request->getPost('tipe')) ?: $item['tipe'];
        $judul = trim((string) $this->request->getPost('judul'));
        if ($judul === '') {
            session()->setFlashdata('error', 'Judul konten wajib diisi.');
            return redirect()->back()->withInput();
        }

        $slug = ($judul !== $item['judul']) ? CmsBeritaModel::generateSlug($judul, (int) $id) : $item['slug'];
        $tanggal = trim((string) $this->request->getPost('tanggal')) ?: null;
        $tanggalLabel = trim((string) $this->request->getPost('tanggal_label'));
        if ($tanggalLabel === '') {
            $tanggalLabel = $tanggal ? $this->formatTanggalIndo($tanggal) : $item['tanggal_label'];
        }

        $gambarPath = $this->handleSingleUpload('gambar_file', ['jpg', 'jpeg', 'png', 'webp', 'gif'], 'cms_uploads');
        if (!$gambarPath) {
            $gambarPath = $item['gambar'];
        }

        if ($tipe === 'emagazine') {
            $pdfPath = $this->handleSingleUpload('pdf_file', ['pdf'], 'cms_uploads/pdf');
            if (!$pdfPath) {
                $pdfPath = $item['file_pdf'];
            }
        } else {
            $pdfPath = null;
        }

        $existingGaleri = [];
        if (!empty($item['galeri_foto_json'])) {
            $decoded = json_decode($item['galeri_foto_json'], true);
            if (is_array($decoded)) {
                $existingGaleri = $decoded;
            }
        }
        $keepGaleri = $this->request->getPost('keep_galeri');
        if (is_array($keepGaleri)) {
            $existingGaleri = array_values($keepGaleri);
        } elseif ($this->request->getPost('galeri_present') === '1') {
            $existingGaleri = [];
        }

        $newGaleri = $this->handleMultipleImageUploads('galeri_files', 'cms_uploads/galeri');
        $mergedGaleri = array_values(array_unique(array_merge($existingGaleri, $newGaleri)));

        $badge = trim((string) $this->request->getPost('badge'));
        if ($tipe === 'galeri' && $badge === '' && !empty($mergedGaleri)) {
            $badge = count($mergedGaleri) . ' Foto';
        }

        $homepageSlot = (int) $this->request->getPost('homepage_slot');
        if (in_array($homepageSlot, [1, 2, 3], true)) {
            \Config\Database::connect()->table('cms_berita')
                ->where('homepage_slot', $homepageSlot)
                ->where('id !=', (int) $id)
                ->update(['homepage_slot' => 0]);
        } else {
            $homepageSlot = 0;
        }

        $this->cmsModel->update($id, [
            'tipe'             => $tipe,
            'judul'            => $judul,
            'slug'             => $slug,
            'kategori_label'   => trim((string) $this->request->getPost('kategori_label')) ?: 'Informasi',
            'badge'            => $badge ?: null,
            'tanggal'          => $tanggal,
            'tanggal_label'    => $tanggalLabel,
            'penulis'          => trim((string) $this->request->getPost('penulis')) ?: null,
            'lokasi_meta'      => trim((string) $this->request->getPost('lokasi_meta')) ?: null,
            'waktu_kegiatan'   => trim((string) $this->request->getPost('waktu_kegiatan')) ?: null,
            'biaya_kegiatan'   => trim((string) $this->request->getPost('biaya_kegiatan')) ?: null,
            'kuota_kegiatan'   => trim((string) $this->request->getPost('kuota_kegiatan')) ?: null,
            'ukuran_file'      => trim((string) $this->request->getPost('ukuran_file')) ?: null,
            'jumlah_halaman'   => trim((string) $this->request->getPost('jumlah_halaman')) ?: null,
            'gambar'           => $gambarPath,
            'file_pdf'         => $pdfPath,
            'external_url'     => trim((string) $this->request->getPost('external_url')) ?: null,
            'ringkasan'        => trim((string) $this->request->getPost('ringkasan')) ?: null,
            'konten'           => trim((string) $this->request->getPost('konten')) ?: null,
            'galeri_foto_json' => !empty($mergedGaleri) ? json_encode($mergedGaleri) : null,
            'homepage_slot'    => $homepageSlot,
        ]);

        session()->setFlashdata('pesan', 'Data Berita & Informasi berhasil diperbarui.');
        return redirect()->to('/cms?tipe=' . urlencode($tipe));
    }

    public function delete($id)
    {
        if ($redir = $this->ensureAdmin()) {
            return $redir;
        }

        $item = $this->cmsModel->find($id);
        if ($item) {
            $this->cmsModel->delete($id);
            session()->setFlashdata('pesan', 'Konten Berita & Informasi berhasil dihapus.');
        }

        return redirect()->to('/cms');
    }
}

