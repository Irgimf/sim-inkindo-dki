<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\MitraKerjaModel;
use App\Models\MitraProductModel;
use App\Models\MitraMessageModel;

class Mitra extends BaseController
{
    protected $mitraModel;
    protected $productModel;
    protected $messageModel;

    public function __construct()
    {
        $this->mitraModel = new MitraKerjaModel();
        $this->productModel = new MitraProductModel();
        $this->messageModel = new MitraMessageModel();
        $this->mitraModel->refreshExpiredStatuses();
    }

    private function handleUpload(string $fieldName, string $subFolder = 'mitra_logos'): ?string
    {
        $file = $this->request->getFile($fieldName);
        if (!$file || !$file->isValid() || $file->hasMoved()) {
            return null;
        }

        $ext = strtolower($file->getExtension() ?: pathinfo($file->getClientName(), PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif', 'svg'];
        if (!in_array($ext, $allowed, true)) {
            return null;
        }

        $destDir = FCPATH . str_replace('/', DIRECTORY_SEPARATOR, $subFolder);
        if (!is_dir($destDir)) {
            mkdir($destDir, 0777, true);
        }

        $newName = 'mitra_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
        $file->move($destDir, $newName);

        $relativePath = trim($subFolder, '/') . '/' . $newName;
        $this->syncFileToInkindoWeb($relativePath);

        return $relativePath;
    }

    public function index()
    {
        $role = session()->get('role');
        $userId = session()->get('id');

        if ($role === 'user') {
            return redirect()->to('/perusahaan');
        }

        if ($role === 'admin') {
            // Prioritaskan pengajuan pending di urutan paling atas bagi Admin
            $mitraList = $this->mitraModel
                ->orderBy("CASE WHEN status_kemitraan = 'pending' THEN 0 WHEN status_kemitraan = 'expired' THEN 1 ELSE 2 END", 'ASC', false)
                ->orderBy('id', 'DESC')
                ->findAll();
        } else {
            $mitraList = $this->mitraModel->where('user_id', $userId)->orderBy('id', 'DESC')->findAll();
        }

        $pendingList = [];
        foreach ($mitraList as &$m) {
            $m['product_count'] = $this->productModel->where('mitra_id', $m['id'])->countAllResults();
            $m['unread_msg_count'] = $this->messageModel->where('mitra_id', $m['id'])->where('is_read', 0)->countAllResults();
            if (($m['status_kemitraan'] ?? '') === 'pending') {
                $pendingList[] = $m;
            }
        }
        unset($m);

        $data = [
            'title'       => 'Data Mitra Kerja',
            'mitraList'   => $mitraList,
            'pendingList' => $pendingList,
        ];
        return view('mitra/index', $data);
    }

    public function create()
    {
        $role = session()->get('role');
        $userId = session()->get('id');

        if ($role === 'user') {
            return redirect()->to('/perusahaan');
        }

        if ($role === 'mitra') {
            $existing = $this->mitraModel->where('user_id', $userId)->first();
            if ($existing) {
                session()->setFlashdata('pesan', 'Akun Anda sudah memiliki data Mitra Kerja. Silakan kelola atau perpanjang melalui halaman Edit.');
                return redirect()->to('/mitra');
            }
        }

        return view('mitra/create', [
            'title' => $role === 'admin' ? 'Tambah Data Mitra Kerja' : 'Form Pengajuan Mitra Kerja',
        ]);
    }

    public function store()
    {
        $role = session()->get('role');
        $userId = session()->get('id');

        if ($role === 'user') {
            return redirect()->to('/perusahaan');
        }

        $namaPerusahaan = trim((string) $this->request->getVar('nama_perusahaan'));
        if ($namaPerusahaan === '') {
            session()->setFlashdata('error', 'Nama Perusahaan Mitra Kerja wajib diisi.');
            return redirect()->back()->withInput();
        }

        $expiredAt = trim((string) $this->request->getVar('expired_at'));
        if ($expiredAt === '') {
            $expiredAt = date('Y-m-d', strtotime('+1 year'));
        }

        $logoPath = $this->handleUpload('logo', 'mitra_logos');
        $slug = $this->mitraModel->generateUniqueSlug($namaPerusahaan);

        $isAdmin = ($role === 'admin');
        $statusKemitraan = $isAdmin ? 'active' : 'pending';
        $approvedAt = $isAdmin ? date('Y-m-d H:i:s') : null;

        $this->mitraModel->save([
            'user_id'          => $userId,
            'nama_perusahaan'  => $namaPerusahaan,
            'slug'             => $slug,
            'kategori_bisnis'  => trim((string) $this->request->getVar('kategori_bisnis')) ?: 'Mitra Strategis Konstruksi & Konsultansi',
            'logo'             => $logoPath ?: 'assets/logo-inkindo.png',
            'deskripsi'        => trim((string) $this->request->getVar('deskripsi')),
            'alamat'           => trim((string) $this->request->getVar('alamat')) ?: '-',
            'telepon'          => trim((string) $this->request->getVar('telepon')) ?: '-',
            'email'            => trim((string) $this->request->getVar('email')) ?: '-',
            'website'          => trim((string) $this->request->getVar('website')) ?: '-',
            'durasi_tahun'     => 1,
            'status_kemitraan' => $statusKemitraan,
            'approved_at'      => $approvedAt,
            'expired_at'       => $expiredAt,
        ]);

        $newId = (int) $this->mitraModel->getInsertID();
        $formattedExp = date('d-m-Y', strtotime($expiredAt));
        if ($isAdmin) {
            session()->setFlashdata('pesan', "Data Mitra Kerja berhasil disimpan dan aktif hingga {$formattedExp}.");
        } else {
            session()->setFlashdata('pesan', "Pengajuan Mitra Kerja hingga tanggal {$formattedExp} berhasil dikirim! Silakan lengkapi Detail Produk sambil menunggu persetujuan Admin.");
        }
        return redirect()->to('/mitra/products/' . $newId);
    }

    public function edit($id)
    {
        $mitra = $this->mitraModel->find($id);
        if (!$mitra) {
            return redirect()->to('/mitra');
        }

        if (session()->get('role') !== 'admin' && $mitra['user_id'] != session()->get('id')) {
            return redirect()->to('/mitra');
        }

        return view('mitra/edit', [
            'title' => 'Edit & Perpanjangan Mitra Kerja',
            'mitra' => $mitra,
        ]);
    }

    public function update($id)
    {
        $mitra = $this->mitraModel->find($id);
        if (!$mitra) {
            return redirect()->to('/mitra');
        }

        $role = session()->get('role');
        if ($role !== 'admin' && $mitra['user_id'] != session()->get('id')) {
            return redirect()->to('/mitra');
        }

        $namaPerusahaan = trim((string) $this->request->getVar('nama_perusahaan'));
        $logoPath = $this->handleUpload('logo', 'mitra_logos');
        $expiredAt = trim((string) $this->request->getVar('expired_at')) ?: ($mitra['expired_at'] ?? date('Y-m-d', strtotime('+1 year')));

        $slug = $mitra['slug'];
        if ($namaPerusahaan !== '' && $namaPerusahaan !== $mitra['nama_perusahaan']) {
            $slug = $this->mitraModel->generateUniqueSlug($namaPerusahaan, (int) $id);
        }

        $updateData = [
            'nama_perusahaan' => $namaPerusahaan ?: $mitra['nama_perusahaan'],
            'slug'            => $slug,
            'kategori_bisnis' => trim((string) $this->request->getVar('kategori_bisnis')) ?: $mitra['kategori_bisnis'],
            'deskripsi'       => trim((string) $this->request->getVar('deskripsi')),
            'alamat'          => trim((string) $this->request->getVar('alamat')) ?: '-',
            'telepon'         => trim((string) $this->request->getVar('telepon')) ?: '-',
            'email'           => trim((string) $this->request->getVar('email')) ?: '-',
            'website'         => trim((string) $this->request->getVar('website')) ?: '-',
            'expired_at'      => $expiredAt,
        ];

        if ($logoPath !== null) {
            $updateData['logo'] = $logoPath;
        }

        if ($role === 'admin') {
            $statusInput = $this->request->getVar('status_kemitraan');
            if ($statusInput) {
                $updateData['status_kemitraan'] = $statusInput;
                if ($statusInput === 'active') {
                    $updateData['alasan_penolakan'] = null;
                }
            }
            if (($updateData['status_kemitraan'] ?? $mitra['status_kemitraan']) === 'active' && empty($mitra['approved_at'])) {
                $updateData['approved_at'] = date('Y-m-d H:i:s');
            }
            session()->setFlashdata('pesan', 'Data Mitra Kerja berhasil diperbarui oleh Admin.');
        } else {
            // Jika user mitra mengajukan ulang (karena expired/rejected atau mengubah tanggal batas expired / centang ajukan)
            $ajukanPerpanjangan = (bool) $this->request->getVar('ajukan_perpanjangan');
            if ($ajukanPerpanjangan || in_array($mitra['status_kemitraan'], ['expired', 'rejected', 'pending'], true) || ($mitra['expired_at'] ?? '') !== $expiredAt) {
                $updateData['status_kemitraan'] = 'pending';
                $updateData['alasan_penolakan'] = null;
                $formattedExp = date('d-m-Y', strtotime($expiredAt));
                session()->setFlashdata('pesan', "Pengajuan aktivasi Mitra Kerja hingga tanggal {$formattedExp} telah dikirim ke Admin untuk diverifikasi.");
            } else {
                session()->setFlashdata('pesan', 'Profil Mitra Kerja berhasil diperbarui.');
            }
        }

        $this->mitraModel->update($id, $updateData);
        return redirect()->to('/mitra');
    }

    public function approve($id)
    {
        if (session()->get('role') !== 'admin') {
            return redirect()->to('/mitra');
        }

        $mitra = $this->mitraModel->find($id);
        if (!$mitra) {
            return redirect()->to('/mitra');
        }

        $expiredAt = trim((string) $this->request->getVar('expired_at')) ?: ($mitra['expired_at'] ?? date('Y-m-d', strtotime('+1 year')));
        if (strtotime($expiredAt) < strtotime(date('Y-m-d'))) {
            $expiredAt = date('Y-m-d', strtotime('+1 year'));
        }

        $this->mitraModel->update($id, [
            'status_kemitraan' => 'active',
            'alasan_penolakan' => null,
            'approved_at'      => date('Y-m-d H:i:s'),
            'expired_at'       => $expiredAt,
        ]);

        session()->setFlashdata('pesan', 'Pengajuan Mitra Kerja "' . $mitra['nama_perusahaan'] . '" disetujui (Aktif s/d ' . date('d-m-Y', strtotime($expiredAt)) . ').');
        return redirect()->to('/mitra');
    }

    public function reject($id)
    {
        if (session()->get('role') !== 'admin') {
            return redirect()->to('/mitra');
        }

        $mitra = $this->mitraModel->find($id);
        if ($mitra) {
            $alasan = trim((string) $this->request->getVar('alasan_penolakan'));
            $this->mitraModel->update($id, [
                'status_kemitraan' => 'rejected',
                'alasan_penolakan' => $alasan !== '' ? $alasan : 'Pengajuan belum memenuhi persyaratan kemitraan.',
            ]);
            session()->setFlashdata('pesan', 'Pengajuan Mitra Kerja "' . $mitra['nama_perusahaan'] . '" telah ditolak beserta alasannya.');
        }

        return redirect()->to('/mitra');
    }

    public function delete($id)
    {
        $mitra = $this->mitraModel->find($id);
        if (!$mitra) {
            return redirect()->to('/mitra');
        }

        if (session()->get('role') !== 'admin' && $mitra['user_id'] != session()->get('id')) {
            return redirect()->to('/mitra');
        }

        $this->productModel->where('mitra_id', $id)->delete();
        $this->messageModel->where('mitra_id', $id)->delete();
        $this->mitraModel->delete($id);

        session()->setFlashdata('pesan', 'Data Mitra Kerja beserta katalog produknya berhasil dihapus.');
        return redirect()->to('/mitra');
    }

    // ================= KELOLA DETAIL PRODUK MITRA KERJA =================
    public function products($mitraId)
    {
        $mitra = $this->mitraModel->find($mitraId);
        if (!$mitra) {
            return redirect()->to('/mitra');
        }

        if (session()->get('role') !== 'admin' && $mitra['user_id'] != session()->get('id')) {
            return redirect()->to('/mitra');
        }

        $products = $this->productModel->where('mitra_id', $mitraId)->orderBy('id', 'ASC')->findAll();

        return view('mitra/products', [
            'title'    => 'Detail & Produk Mitra Kerja - ' . $mitra['nama_perusahaan'],
            'mitra'    => $mitra,
            'products' => $products,
        ]);
    }

    public function storeProduct($mitraId)
    {
        $mitra = $this->mitraModel->find($mitraId);
        if (!$mitra) {
            return redirect()->to('/mitra');
        }

        if (session()->get('role') !== 'admin' && $mitra['user_id'] != session()->get('id')) {
            return redirect()->to('/mitra');
        }

        $gambarProduk = $this->handleUpload('gambar_produk', 'mitra_logos/products');

        $this->productModel->save([
            'mitra_id'         => $mitraId,
            'nama_produk'      => trim((string) $this->request->getVar('nama_produk')),
            'kategori_produk'  => trim((string) $this->request->getVar('kategori_produk')) ?: 'Produk Unggulan',
            'deskripsi_produk' => trim((string) $this->request->getVar('deskripsi_produk')),
            'spesifikasi'      => trim((string) $this->request->getVar('spesifikasi')),
            'gambar_produk'    => $gambarProduk ?: $mitra['logo'],
        ]);

        session()->setFlashdata('pesan', 'Detail Produk berhasil ditambahkan.');
        return redirect()->to('/mitra/products/' . $mitraId);
    }

    public function updateProduct($productId)
    {
        $product = $this->productModel->find($productId);
        if (!$product) {
            return redirect()->to('/mitra');
        }

        $mitra = $this->mitraModel->find($product['mitra_id']);
        if (!$mitra || (session()->get('role') !== 'admin' && $mitra['user_id'] != session()->get('id'))) {
            return redirect()->to('/mitra');
        }

        $gambarProduk = $this->handleUpload('gambar_produk', 'mitra_logos/products');

        $updateData = [
            'nama_produk'      => trim((string) $this->request->getVar('nama_produk')),
            'kategori_produk'  => trim((string) $this->request->getVar('kategori_produk')) ?: 'Produk Unggulan',
            'deskripsi_produk' => trim((string) $this->request->getVar('deskripsi_produk')),
            'spesifikasi'      => trim((string) $this->request->getVar('spesifikasi')),
        ];

        if ($gambarProduk !== null) {
            $updateData['gambar_produk'] = $gambarProduk;
        }

        $this->productModel->update($productId, $updateData);

        session()->setFlashdata('pesan', 'Detail Produk berhasil diperbarui.');
        return redirect()->to('/mitra/products/' . $product['mitra_id']);
    }

    public function deleteProduct($productId)
    {
        $product = $this->productModel->find($productId);
        if (!$product) {
            return redirect()->to('/mitra');
        }

        $mitra = $this->mitraModel->find($product['mitra_id']);
        if (!$mitra || (session()->get('role') !== 'admin' && $mitra['user_id'] != session()->get('id'))) {
            return redirect()->to('/mitra');
        }

        $this->productModel->delete($productId);

        session()->setFlashdata('pesan', 'Produk berhasil dihapus.');
        return redirect()->to('/mitra/products/' . $product['mitra_id']);
    }

    // ================= PESAN MASUK MITRA KERJA & KONSULTASI =================
    private function ensureKonsultasiTable($db): void
    {
        if ($db->tableExists('konsultasi_messages')) {
            return;
        }

        $db->query("
            CREATE TABLE IF NOT EXISTS `konsultasi_messages` (
                `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `kategori` VARCHAR(150) NOT NULL,
                `nama` VARCHAR(150) NOT NULL,
                `nama_perusahaan` VARCHAR(255) NOT NULL,
                `no_telepon` VARCHAR(100) NOT NULL,
                `email` VARCHAR(150) NOT NULL,
                `subject` VARCHAR(255) NOT NULL,
                `pesan` TEXT NOT NULL,
                `is_read` TINYINT(1) NOT NULL DEFAULT 0,
                `created_at` DATETIME NULL DEFAULT NULL,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    }

    public function messages()
    {
        $role = session()->get('role');
        $userId = session()->get('id');

        if ($role === 'user') {
            return redirect()->to('/perusahaan');
        }

        $db = \Config\Database::connect();
        $isAdmin = ($role === 'admin');

        $activeTab = $this->request->getGet('tab') === 'konsultasi' ? 'konsultasi' : 'mitra';
        if (!$isAdmin) {
            $activeTab = 'mitra';
        }

        // 1. Pesan Mitra Kerja (pagination maks 6 per halaman via DataTables)
        $mitraBuilder = $db->table('mitra_messages m')
            ->select('m.*, k.nama_perusahaan as nama_mitra, k.slug as slug_mitra, k.user_id as mitra_user_id')
            ->join('mitra_kerja k', 'k.id = m.mitra_id', 'left')
            ->orderBy('m.id', 'DESC');
        if (!$isAdmin) {
            $mitraBuilder->where('k.user_id', $userId);
        }
        $messages = $mitraBuilder->get()->getResultArray();
        $mitraTotal = count($messages);
        $unreadMitraCount = 0;
        foreach ($messages as $m) {
            if ((int) ($m['is_read'] ?? 0) === 0) {
                $unreadMitraCount++;
            }
        }

        // 2. Pesan Konsultasi (khusus Admin, pagination maks 6 per halaman via DataTables)
        $konsultasiMessages = [];
        $konsultasiTotal = 0;
        $unreadKonsultasiCount = 0;

        if ($isAdmin) {
            $this->ensureKonsultasiTable($db);
            $konsultasiMessages = $db->table('konsultasi_messages')
                ->orderBy('id', 'DESC')
                ->get()
                ->getResultArray();
            $konsultasiTotal = count($konsultasiMessages);
            foreach ($konsultasiMessages as $km) {
                if ((int) ($km['is_read'] ?? 0) === 0) {
                    $unreadKonsultasiCount++;
                }
            }
        }

        return view('mitra/messages', [
            'title'                 => $isAdmin ? 'Pesan Masuk' : 'Pesan Masuk Mitra Kerja',
            'isAdmin'               => $isAdmin,
            'activeTab'             => $activeTab,
            'messages'              => $messages,
            'mitraTotal'            => $mitraTotal,
            'unreadMitraCount'      => $unreadMitraCount,
            'konsultasiMessages'    => $konsultasiMessages,
            'konsultasiTotal'       => $konsultasiTotal,
            'unreadKonsultasiCount' => $unreadKonsultasiCount,
        ]);
    }

    public function markMessageRead($msgId)
    {
        $msg = $this->messageModel->find($msgId);
        if ($msg) {
            $mitra = $this->mitraModel->find($msg['mitra_id']);
            if (session()->get('role') === 'admin' || ($mitra && $mitra['user_id'] == session()->get('id'))) {
                $this->messageModel->update($msgId, ['is_read' => 1]);
            }
        }
        return redirect()->to('/mitra/messages?tab=mitra');
    }

    public function deleteMessage($msgId)
    {
        $msg = $this->messageModel->find($msgId);
        if ($msg) {
            $mitra = $this->mitraModel->find($msg['mitra_id']);
            if (session()->get('role') === 'admin' || ($mitra && $mitra['user_id'] == session()->get('id'))) {
                $this->messageModel->delete($msgId);
                session()->setFlashdata('pesan', 'Pesan Mitra Kerja berhasil dihapus.');
            }
        }
        return redirect()->to('/mitra/messages?tab=mitra');
    }

    public function markKonsultasiRead($msgId)
    {
        if (session()->get('role') === 'admin') {
            $db = \Config\Database::connect();
            if ($db->tableExists('konsultasi_messages')) {
                $db->table('konsultasi_messages')->where('id', (int) $msgId)->update(['is_read' => 1]);
            }
        }
        return redirect()->to('/mitra/messages?tab=konsultasi');
    }

    public function deleteKonsultasiMessage($msgId)
    {
        if (session()->get('role') === 'admin') {
            $db = \Config\Database::connect();
            if ($db->tableExists('konsultasi_messages')) {
                $db->table('konsultasi_messages')->where('id', (int) $msgId)->delete();
                session()->setFlashdata('pesan', 'Pesan Konsultasi berhasil dihapus.');
            }
        }
        return redirect()->to('/mitra/messages?tab=konsultasi');
    }
}
