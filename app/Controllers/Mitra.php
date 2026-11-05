<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\MitraKerjaModel;
use App\Models\MitraProductModel;

class Mitra extends BaseController
{
    protected $mitraModel;
    protected $productModel;

    public function __construct()
    {
        $this->mitraModel = new MitraKerjaModel();
        $this->productModel = new MitraProductModel();
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
        $this->syncFileToProjectWeb($relativePath);

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
            $mitraList = $this->mitraModel->orderBy('id', 'DESC')->findAll();
        } else {
            $mitraList = $this->mitraModel->where('user_id', $userId)->orderBy('id', 'DESC')->findAll();
        }

        foreach ($mitraList as &$m) {
            $m['product_count'] = $this->productModel->where('mitra_id', $m['id'])->countAllResults();
        }
        unset($m);

        $data = [
            'title'     => 'Data Mitra Kerja',
            'mitraList' => $mitraList,
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
                session()->setFlashdata('pesan', 'Akun Anda sudah memiliki profil Mitra Kerja. Silakan edit data yang sudah ada atau tambahkan Detail Produk.');
                return redirect()->to('/mitra');
            }
        }

        return view('mitra/create', [
            'title' => 'Tambah Data Mitra Kerja',
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

        $logoPath = $this->handleUpload('logo', 'mitra_logos');
        $slug = $this->mitraModel->generateUniqueSlug($namaPerusahaan);

        $this->mitraModel->save([
            'user_id'         => $userId,
            'nama_perusahaan' => $namaPerusahaan,
            'slug'            => $slug,
            'kategori_bisnis' => trim((string) $this->request->getVar('kategori_bisnis')) ?: 'Mitra Strategis Konstruksi & Konsultansi',
            'logo'            => $logoPath ?: 'assets/logo-inkindo.png',
            'deskripsi'       => trim((string) $this->request->getVar('deskripsi')),
            'alamat'          => trim((string) $this->request->getVar('alamat')) ?: '-',
            'telepon'         => trim((string) $this->request->getVar('telepon')) ?: '-',
            'email'           => trim((string) $this->request->getVar('email')) ?: '-',
            'website'         => trim((string) $this->request->getVar('website')) ?: '-',
        ]);

        $newId = (int) $this->mitraModel->getInsertID();
        session()->setFlashdata('pesan', 'Data Mitra Kerja berhasil disimpan dan logo otomatis terintegrasi ke Web Profile INKINDO.');
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
            'title' => 'Edit Data Mitra Kerja',
            'mitra' => $mitra,
        ]);
    }

    public function update($id)
    {
        $mitra = $this->mitraModel->find($id);
        if (!$mitra) {
            return redirect()->to('/mitra');
        }

        if (session()->get('role') !== 'admin' && $mitra['user_id'] != session()->get('id')) {
            return redirect()->to('/mitra');
        }

        $namaPerusahaan = trim((string) $this->request->getVar('nama_perusahaan'));
        $logoPath = $this->handleUpload('logo', 'mitra_logos');

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
        ];

        if ($logoPath !== null) {
            $updateData['logo'] = $logoPath;
        }

        $this->mitraModel->update($id, $updateData);

        session()->setFlashdata('pesan', 'Data Mitra Kerja berhasil diperbarui dan disinkronkan ke Web Profile.');
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
}

