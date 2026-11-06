<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\CmsBeritaModel;

class Cms extends BaseController
{
    protected $cmsModel;

    public function __construct()
    {
        $this->cmsModel = new CmsBeritaModel();
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

        $allItems = $this->cmsModel->orderBy('id', 'DESC')->findAll();
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

        session()->setFlashdata('pesan', 'Pengaturan berita utama yang tampil pada Halaman Utama (Beranda WebProfile) berhasil diperbarui.');
        return redirect()->to('/cms');
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

