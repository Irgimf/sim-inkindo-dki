<?php

namespace App\Controllers;

use App\Models\CmsPageBackgroundModel;

class CmsBackground extends BaseController
{
    protected CmsPageBackgroundModel $bgModel;

    public function __construct()
    {
        $this->ensureTableAndSeed();
        $this->bgModel = new CmsPageBackgroundModel();
    }

    private function ensureTableAndSeed(): void
    {
        $db  = \Config\Database::connect();
        $now = date('Y-m-d H:i:s');

        if (! $db->tableExists('cms_page_backgrounds')) {
            $db->query("
                CREATE TABLE IF NOT EXISTS `cms_page_backgrounds` (
                    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                    `page_key` VARCHAR(80) NOT NULL,
                    `page_name` VARCHAR(120) NOT NULL,
                    `section_label` VARCHAR(150) NOT NULL,
                    `image_path` VARCHAR(255) NOT NULL,
                    `default_path` VARCHAR(255) NOT NULL,
                    `updated_at` DATETIME NULL DEFAULT NULL,
                    PRIMARY KEY (`id`),
                    UNIQUE KEY `uniq_cms_page_bg_key` (`page_key`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
            ");

            $defaults = [
                [
                    'page_key'      => 'tentang_hero',
                    'page_name'     => 'Tentang Kami',
                    'section_label' => 'Background Hero Banner',
                    'image_path'    => 'assets/jakarta3.jpg',
                    'default_path'  => 'assets/jakarta3.jpg',
                    'updated_at'    => $now,
                ],
                [
                    'page_key'      => 'regulasi_hero',
                    'page_name'     => 'Regulasi',
                    'section_label' => 'Background Hero Banner',
                    'image_path'    => 'assets/jakarta.jpg',
                    'default_path'  => 'assets/jakarta.jpg',
                    'updated_at'    => $now,
                ],
                [
                    'page_key'      => 'berita_hero',
                    'page_name'     => 'Berita & Informasi',
                    'section_label' => 'Background Hero Banner',
                    'image_path'    => 'assets/jakarta2.jpg',
                    'default_path'  => 'assets/jakarta2.jpg',
                    'updated_at'    => $now,
                ],
                [
                    'page_key'      => 'berita_detail_hero',
                    'page_name'     => 'Detail Berita',
                    'section_label' => 'Background Header Halaman',
                    'image_path'    => 'assets/jakarta2.jpg',
                    'default_path'  => 'assets/jakarta2.jpg',
                    'updated_at'    => $now,
                ],
                [
                    'page_key'      => 'magazine_hero',
                    'page_name'     => 'Baca e-Magazine',
                    'section_label' => 'Background Header Halaman',
                    'image_path'    => 'assets/jakarta2.jpg',
                    'default_path'  => 'assets/jakarta2.jpg',
                    'updated_at'    => $now,
                ],
                [
                    'page_key'      => 'anggota_hero',
                    'page_name'     => 'Anggota Terdaftar',
                    'section_label' => 'Background Hero Banner',
                    'image_path'    => 'assets/jakarta8.jpg',
                    'default_path'  => 'assets/jakarta8.jpg',
                    'updated_at'    => $now,
                ],
                [
                    'page_key'      => 'ketentuan_mitra_hero',
                    'page_name'     => 'Ketentuan Mitra Kerja',
                    'section_label' => 'Background Hero Banner',
                    'image_path'    => 'assets/jakarta7.jpg',
                    'default_path'  => 'assets/jakarta7.jpg',
                    'updated_at'    => $now,
                ],
                [
                    'page_key'      => 'ketentuan_mitra_section',
                    'page_name'     => 'Ketentuan Mitra Kerja',
                    'section_label' => 'Background Bagian Ketentuan',
                    'image_path'    => 'assets/jakarta3.jpg',
                    'default_path'  => 'assets/jakarta3.jpg',
                    'updated_at'    => $now,
                ],
                [
                    'page_key'      => 'mitra_detail_hero',
                    'page_name'     => 'Detail Profil Mitra Kerja',
                    'section_label' => 'Background Hero Profil Mitra',
                    'image_path'    => 'assets/jakarta7.jpg',
                    'default_path'  => 'assets/jakarta7.jpg',
                    'updated_at'    => $now,
                ],
                [
                    'page_key'      => 'lelang_hero',
                    'page_name'     => 'Lelang Lainnya',
                    'section_label' => 'Background Hero Banner',
                    'image_path'    => 'assets/jakarta4.jpg',
                    'default_path'  => 'assets/jakarta4.jpg',
                    'updated_at'    => $now,
                ],
                [
                    'page_key'      => 'lelang_peluang_section',
                    'page_name'     => 'Lelang Lainnya',
                    'section_label' => 'Background Bagian Peluang Usaha',
                    'image_path'    => 'assets/jakarta4.jpg',
                    'default_path'  => 'assets/jakarta4.jpg',
                    'updated_at'    => $now,
                ],
            ];

            $db->table('cms_page_backgrounds')->insertBatch($defaults);
        }
    }

    private function removeCustomImage(?string $relativePath): void
    {
        $clean = ltrim(str_replace('\\', '/', (string) $relativePath), '/');
        if ($clean === '' || strpos($clean, 'cms_uploads/backgrounds/') !== 0) {
            return;
        }

        $localFile = FCPATH . str_replace('/', DIRECTORY_SEPARATOR, $clean);
        if (is_file($localFile)) {
            @unlink($localFile);
        }

        $webFile = $this->getInkindoWebPublicPath() . str_replace('/', DIRECTORY_SEPARATOR, $clean);
        if (is_file($webFile)) {
            @unlink($webFile);
        }
    }

    public function index()
    {
        if ($redirect = $this->ensureAdmin()) {
            return $redirect;
        }

        $items = $this->bgModel->orderBy('id', 'ASC')->findAll();

        return view('cms/backgrounds', [
            'title' => 'Admin CMS - Background & Gambar Halaman',
            'items' => $items,
        ]);
    }

    public function save()
    {
        if ($redirect = $this->ensureAdmin()) {
            return $redirect;
        }

        $id       = (int) ($this->request->getPost('id') ?? 0);
        $existing = $this->bgModel->find($id);
        if (! $existing) {
            return redirect()->to('/cms/backgrounds')->with('error', 'Data background halaman tidak ditemukan.');
        }

        $file = $this->request->getFile('image_file');
        if (! $file || ! $file->isValid() || $file->hasMoved()) {
            return redirect()->to('/cms/backgrounds')->with('error', 'Pilih file gambar yang valid terlebih dahulu.');
        }

        $ext = strtolower($file->getExtension());
        if (! in_array($ext, ['jpg', 'jpeg', 'png', 'webp'], true)) {
            return redirect()->to('/cms/backgrounds')->with('error', 'Format gambar harus JPG, PNG, atau WEBP.');
        }

        $uploadedPath = $this->handleSingleUpload('image_file', ['jpg', 'jpeg', 'png', 'webp'], 'cms_uploads/backgrounds', 'bg_' . $existing['page_key'] . '_');
        if (! $uploadedPath) {
            return redirect()->to('/cms/backgrounds')->with('error', 'Gagal mengunggah gambar background.');
        }

        $this->removeCustomImage($existing['image_path'] ?? '');

        $this->bgModel->update($id, [
            'image_path' => $uploadedPath,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to('/cms/backgrounds')->with('pesan', 'Background "' . $existing['page_name'] . ' — ' . $existing['section_label'] . '" berhasil diperbarui.');
    }

    public function reset($id)
    {
        if ($redirect = $this->ensureAdmin()) {
            return $redirect;
        }

        $existing = $this->bgModel->find((int) $id);
        if (! $existing) {
            return redirect()->to('/cms/backgrounds')->with('error', 'Data background halaman tidak ditemukan.');
        }

        $this->removeCustomImage($existing['image_path'] ?? '');

        $this->bgModel->update((int) $id, [
            'image_path' => $existing['default_path'],
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to('/cms/backgrounds')->with('pesan', 'Background "' . $existing['page_name'] . '" dikembalikan ke gambar bawaan.');
    }
}

