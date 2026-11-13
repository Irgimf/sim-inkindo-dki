<?php

namespace App\Controllers;

use App\Models\CmsRegulasiModel;

class CmsRegulasi extends BaseController
{
    protected CmsRegulasiModel $regulasiModel;

    public function __construct()
    {
        $this->ensureTableAndSeed();
        $this->regulasiModel = new CmsRegulasiModel();
    }

    private function ensureTableAndSeed(): void
    {
        $db = \Config\Database::connect();
        if (! $db->tableExists('cms_regulasi')) {
            $db->query("
                CREATE TABLE IF NOT EXISTS `cms_regulasi` (
                    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                    `kategori` VARCHAR(40) NOT NULL DEFAULT 'inkindo',
                    `sub_kategori` VARCHAR(40) NULL DEFAULT NULL,
                    `judul` VARCHAR(255) NOT NULL,
                    `deskripsi` TEXT NULL,
                    `file_pdf` VARCHAR(255) NULL DEFAULT NULL,
                    `file_name` VARCHAR(255) NULL DEFAULT NULL,
                    `created_at` DATETIME NULL DEFAULT NULL,
                    `updated_at` DATETIME NULL DEFAULT NULL,
                    PRIMARY KEY (`id`),
                    KEY `idx_cms_regulasi_kategori` (`kategori`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
            ");

            $now = date('Y-m-d H:i:s');
            $defaults = [
                [
                    'kategori'     => 'inkindo',
                    'sub_kategori' => null,
                    'judul'        => 'Anggaran Dasar / Anggaran Rumah Tangga (AD/ART) INKINDO',
                    'deskripsi'    => 'Pedoman pokok keorganisasian yang memuat ketentuan kelembagaan, hak dan kewajiban anggota, tata kelola asosiasi, serta mekanisme pengambilan keputusan di lingkungan INKINDO.',
                    'file_pdf'     => null,
                    'file_name'    => null,
                    'created_at'   => $now,
                    'updated_at'   => $now,
                ],
                [
                    'kategori'     => 'inkindo',
                    'sub_kategori' => null,
                    'judul'        => 'Kode Etik Anggota INKINDO',
                    'deskripsi'    => 'Standar norma etika dan perilaku profesional yang wajib dipatuhi oleh seluruh anggota dalam menjalankan kegiatan dan praktik jasa konsultansi secara berintegritas.',
                    'file_pdf'     => null,
                    'file_name'    => null,
                    'created_at'   => $now,
                    'updated_at'   => $now,
                ],
                [
                    'kategori'     => 'inkindo',
                    'sub_kategori' => null,
                    'judul'        => 'Pedoman Organisasi Provinsi INKINDO DKI Jakarta',
                    'deskripsi'    => 'Aturan operasional dan tata kerja organisasi tingkat provinsi dalam penyelenggaraan pelayanan anggota, koordinasi kepengurusan, serta program strategis DKI Jakarta.',
                    'file_pdf'     => null,
                    'file_name'    => null,
                    'created_at'   => $now,
                    'updated_at'   => $now,
                ],
                [
                    'kategori'     => 'jasa',
                    'sub_kategori' => 'kontruksi',
                    'judul'        => 'UU No. 2 Tahun 2017 tentang Jasa Konstruksi',
                    'deskripsi'    => 'Landasan hukum utama penyelenggaraan jasa konstruksi nasional yang mengatur ketentuan perizinan usaha, standar mutu dan keamanan, serta tata kelola industri konstruksi.',
                    'file_pdf'     => null,
                    'file_name'    => null,
                    'created_at'   => $now,
                    'updated_at'   => $now,
                ],
                [
                    'kategori'     => 'jasa',
                    'sub_kategori' => 'non-kontruksi',
                    'judul'        => 'Pedoman Pengadaan Jasa Konsultansi Non Konstruksi',
                    'deskripsi'    => 'Petunjuk teknis tata cara pemilihan penyedia, penyusunan kualifikasi teknis, dan evaluasi penawaran untuk pengadaan jasa konsultansi bidang non-konstruksi.',
                    'file_pdf'     => null,
                    'file_name'    => null,
                    'created_at'   => $now,
                    'updated_at'   => $now,
                ],
                [
                    'kategori'     => 'jasa',
                    'sub_kategori' => 'umum',
                    'judul'        => 'Standar Remunerasi (Billing Rate) Jasa Konsultansi 2026',
                    'deskripsi'    => 'Pedoman acuan perhitungan remunerasi minimal tenaga ahli dan biaya langsung non-personil berbasis keahlian dalam perencanaan anggaran dan proposal lelang konsultansi.',
                    'file_pdf'     => null,
                    'file_name'    => null,
                    'created_at'   => $now,
                    'updated_at'   => $now,
                ],
                [
                    'kategori'     => 'jasa',
                    'sub_kategori' => 'kontruksi',
                    'judul'        => 'PP No. 22 Tahun 2020 tentang Peraturan Pelaksanaan UU Jasa Konstruksi',
                    'deskripsi'    => 'Peraturan pelaksanaan undang-undang jasa konstruksi yang mengatur perizinan berusaha, sertifikasi kompetensi kerja, tata kelola rantai pasok, dan pengawasan proyek.',
                    'file_pdf'     => null,
                    'file_name'    => null,
                    'created_at'   => $now,
                    'updated_at'   => $now,
                ],
                [
                    'kategori'     => 'jasa',
                    'sub_kategori' => 'umum',
                    'judul'        => 'Standar Biaya Langsung Personil & Non Personil 2026',
                    'deskripsi'    => 'Rincian komponen batas biaya langsung personil menurut jenjang pendidikan dan pengalaman kerja, serta rincian biaya penunjang operasional kegiatan konsultansi.',
                    'file_pdf'     => null,
                    'file_name'    => null,
                    'created_at'   => $now,
                    'updated_at'   => $now,
                ],
                [
                    'kategori'     => 'terkait',
                    'sub_kategori' => null,
                    'judul'        => 'Peraturan LKPP No. 12 Tahun 2021 tentang Pedoman Pengadaan Barang/Jasa Pemerintah',
                    'deskripsi'    => 'Pedoman pelaksanaan pengadaan barang dan jasa melalui penyedia, tata kelola tender jasa konsultansi, negosiasi teknis, serta penyusunan kontrak kerja pemerintah.',
                    'file_pdf'     => null,
                    'file_name'    => null,
                    'created_at'   => $now,
                    'updated_at'   => $now,
                ],
                [
                    'kategori'     => 'terkait',
                    'sub_kategori' => null,
                    'judul'        => 'Peraturan Menteri PUPR No. 10 Tahun 2021 tentang Pedoman SMKK',
                    'deskripsi'    => 'Pedoman penerapan Sistem Manajemen Keselamatan Konstruksi (SMKK) untuk menjamin pemenuhan standar keselamatan keteknikan, kesehatan kerja, dan keberlanjutan lingkungan.',
                    'file_pdf'     => null,
                    'file_name'    => null,
                    'created_at'   => $now,
                    'updated_at'   => $now,
                ],
            ];
            $db->table('cms_regulasi')->insertBatch($defaults);
        }
    }

    private function removePdfFile(?string $relativePath): void
    {
        $clean = ltrim(str_replace('\\', '/', (string) $relativePath), '/');
        if ($clean === '' || strpos($clean, 'cms_uploads/regulasi/') !== 0) {
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
        if ($redir = $this->ensureAdmin()) {
            return $redir;
        }

        $filterKategori = trim((string) $this->request->getGet('kategori'));
        if (! in_array($filterKategori, ['inkindo', 'jasa', 'terkait'], true)) {
            $filterKategori = '';
        }

        $filterSub = trim((string) $this->request->getGet('sub'));
        if ($filterKategori !== 'jasa' || ! in_array($filterSub, ['kontruksi', 'non-kontruksi', 'umum'], true)) {
            $filterSub = '';
        }

        $builder = $this->regulasiModel->orderBy('id', 'DESC');
        if ($filterKategori !== '') {
            $builder->where('kategori', $filterKategori);
            if ($filterSub !== '') {
                $builder->where('sub_kategori', $filterSub);
            }
        }

        $items = $builder->findAll();

        $allCounts = [
            'semua'   => (int) $this->regulasiModel->countAllResults(),
            'inkindo' => (int) $this->regulasiModel->where('kategori', 'inkindo')->countAllResults(),
            'jasa'    => (int) $this->regulasiModel->where('kategori', 'jasa')->countAllResults(),
            'terkait' => (int) $this->regulasiModel->where('kategori', 'terkait')->countAllResults(),
        ];

        return view('cms/regulasi', [
            'title'          => 'Admin CMS - Regulasi',
            'items'          => $items,
            'filterKategori' => $filterKategori,
            'filterSub'      => $filterSub,
            'counts'         => $allCounts,
        ]);
    }

    public function save()
    {
        if ($redir = $this->ensureAdmin()) {
            return $redir;
        }

        $id          = (int) $this->request->getPost('id');
        $kategori    = trim((string) $this->request->getPost('kategori'));
        $subKategori = trim((string) $this->request->getPost('sub_kategori'));
        $judul       = trim((string) $this->request->getPost('judul'));
        $deskripsi   = trim((string) $this->request->getPost('deskripsi'));

        if (! in_array($kategori, ['inkindo', 'jasa', 'terkait'], true)) {
            $kategori = 'inkindo';
        }
        if ($kategori !== 'jasa' || ! in_array($subKategori, ['kontruksi', 'non-kontruksi', 'umum'], true)) {
            $subKategori = $kategori === 'jasa' ? 'umum' : null;
        }

        $redirectQuery = '?kategori=' . urlencode($kategori);
        if ($kategori === 'jasa' && $subKategori !== null) {
            $redirectQuery .= '&sub=' . urlencode($subKategori);
        }
        $returnUrl = '/cms/regulasi' . ($this->request->getPost('return_all') === '1' ? '' : $redirectQuery);

        if ($judul === '' || $deskripsi === '') {
            return redirect()->to($returnUrl)->with('error', 'Judul dan deskripsi regulasi wajib diisi.');
        }

        $existing = $id > 0 ? $this->regulasiModel->find($id) : null;
        if ($id > 0 && ! $existing) {
            return redirect()->to('/cms/regulasi')->with('error', 'Data regulasi tidak ditemukan.');
        }

        $file = $this->request->getFile('pdf_file');
        $hasUploadedFile = $file && $file->getError() !== UPLOAD_ERR_NO_FILE;

        $filePdf  = $existing['file_pdf'] ?? null;
        $fileName = $existing['file_name'] ?? null;

        if ($hasUploadedFile) {
            if (! $file->isValid()) {
                return redirect()->to($returnUrl)->with('error', 'File PDF gagal diunggah.');
            }

            $ext = strtolower($file->getExtension() ?: pathinfo($file->getClientName(), PATHINFO_EXTENSION));
            $mime = strtolower((string) $file->getMimeType());
            $allowedMimes = ['application/pdf', 'application/x-pdf', 'application/acrobat', 'applications/vnd.pdf'];
            if ($ext !== 'pdf' || ($mime !== '' && ! in_array($mime, $allowedMimes, true) && strpos($mime, 'pdf') === false)) {
                return redirect()->to($returnUrl)->with('error', 'Format file harus berupa dokumen PDF (.pdf).');
            }

            if ($file->getSizeByUnit('mb') > 20) {
                return redirect()->to($returnUrl)->with('error', 'Ukuran file PDF maksimal 20 MB.');
            }

            $clientName = $file->getClientName();
            $uploadedPath = $this->handleSingleUpload('pdf_file', ['pdf'], 'cms_uploads/regulasi', 'reg_');
            if ($uploadedPath === null) {
                return redirect()->to($returnUrl)->with('error', 'Gagal menyimpan file PDF regulasi.');
            }

            if ($existing && ! empty($existing['file_pdf'])) {
                $this->removePdfFile($existing['file_pdf']);
            }

            $filePdf  = $uploadedPath;
            $fileName = $clientName;
        } elseif (! $existing) {
            return redirect()->to($returnUrl)->with('error', 'File PDF wajib diunggah saat menambahkan regulasi baru.');
        }

        $payload = [
            'kategori'     => $kategori,
            'sub_kategori' => $subKategori,
            'judul'        => $judul,
            'deskripsi'    => $deskripsi,
            'file_pdf'     => $filePdf,
            'file_name'    => $fileName,
        ];

        if ($existing) {
            $this->regulasiModel->update($id, $payload);
            return redirect()->to($returnUrl)->with('pesan', 'Data regulasi berhasil diperbarui.');
        }

        $this->regulasiModel->insert($payload);
        return redirect()->to($returnUrl)->with('pesan', 'Regulasi baru berhasil ditambahkan.');
    }

    public function delete(int $id)
    {
        if ($redir = $this->ensureAdmin()) {
            return $redir;
        }

        $item = $this->regulasiModel->find($id);
        if ($item) {
            if (! empty($item['file_pdf'])) {
                $this->removePdfFile($item['file_pdf']);
            }
            $this->regulasiModel->delete($id);
        }

        return redirect()->back()->with('pesan', 'Regulasi berhasil dihapus.');
    }
}

