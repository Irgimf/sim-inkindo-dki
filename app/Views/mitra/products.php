<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="mb-4 mt-2 d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div>
        <a href="/mitra" class="btn btn-outline-secondary btn-sm mb-3"><i class="bi bi-arrow-left"></i> Kembali ke Data Mitra Kerja</a>
        <h3 class="fw-bold mb-0" style="color: var(--inkindo-blue);">Detail Mitra & Katalog Produk</h3>
    </div>
    <div class="d-flex gap-2">
        <a href="http://localhost:8000/mitra-kerja/<?= esc($mitra['slug']); ?>" target="_blank" class="btn btn-outline-primary btn-sm px-3 py-2 rounded-pill fw-semibold">
            <i class="bi bi-box-arrow-up-right me-1"></i> Lihat Halaman Profil Mitra di Web
        </a>
        <a href="/mitra/edit/<?= $mitra['id']; ?>" class="btn btn-sm btn-secondary px-3 py-2 rounded-pill fw-semibold">
            <i class="bi bi-pencil-square me-1"></i> Edit Info Perusahaan
        </a>
    </div>
</div>

<?php if(session()->getFlashdata('pesan')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= session()->getFlashdata('pesan'); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<!-- MASTER: INFORMASI PERUSAHAAN MITRA KERJA -->
<div class="card card-custom mb-4 border-0 shadow-sm" style="border-radius: 16px;">
    <div class="card-header bg-light fw-bold text-uppercase" style="color: var(--inkindo-blue);">
        Informasi Perusahaan Mitra Kerja
    </div>
    <div class="card-body">
        <div class="row align-items-center">
            <div class="col-md-3 text-center mb-3 mb-md-0">
                <div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 14px; padding: 16px; display: inline-flex; align-items: center; justify-content: center; width: 170px; height: 105px;">
                    <img src="/<?= esc($mitra['logo'] ?: 'logo-inkindo.png'); ?>" alt="<?= esc($mitra['nama_perusahaan']); ?>" style="max-height: 75px; max-width: 140px; object-fit: contain;">
                </div>
            </div>
            <div class="col-md-9 border-start">
                <h5 class="fw-bold text-dark mb-1"><?= esc($mitra['nama_perusahaan']); ?></h5>
                <span class="badge rounded-pill badge-soft-primary px-3 py-1 mb-2"><?= esc($mitra['kategori_bisnis']); ?></span>
                <p class="text-muted small mb-2"><?= nl2br(esc($mitra['deskripsi'])); ?></p>
                <div class="row small text-muted g-2 mt-1">
                    <div class="col-md-6"><i class="bi bi-geo-alt-fill text-primary me-1"></i> <?= esc($mitra['alamat'] ?: '-'); ?></div>
                    <div class="col-md-3"><i class="bi bi-telephone-fill text-primary me-1"></i> <?= esc($mitra['telepon'] ?: '-'); ?></div>
                    <div class="col-md-3"><i class="bi bi-globe text-primary me-1"></i> <?= esc($mitra['website'] ?: '-'); ?></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- DETAIL: DAFTAR PRODUK UNGGULAN MITRA KERJA -->
<div class="card card-custom border-0 shadow-sm" style="border-radius: 16px;">
    <div class="card-header bg-light d-flex justify-content-between align-items-center py-3">
        <span class="fw-bold text-uppercase" style="color: var(--inkindo-blue);">Daftar Detail Produk Mitra Kerja</span>
        <button class="btn btn-sm btn-inkindo px-3 py-2" data-bs-toggle="modal" data-bs-target="#tambahProdukModal">
            + Tambah Produk
        </button>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle text-center">
                <thead style="background-color: var(--inkindo-blue); color: white;">
                    <tr>
                        <th class="py-3" width="5%">No</th>
                        <th class="py-3" width="12%">Gambar</th>
                        <th class="py-3 text-start" width="22%">Nama Produk</th>
                        <th class="py-3 text-start" width="28%">Deskripsi Produk</th>
                        <th class="py-3 text-start" width="20%">Spesifikasi Teknis</th>
                        <th class="py-3" width="13%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $pi = 1; foreach($products as $p): ?>
                        <tr class="bg-white">
                            <td class="fw-semibold"><?= $pi++; ?></td>
                            <td>
                                <div style="width: 72px; height: 54px; margin: 0 auto; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                                    <img src="/<?= esc($p['gambar_produk'] ?: $mitra['logo']); ?>" alt="<?= esc($p['nama_produk']); ?>" style="max-height: 46px; max-width: 64px; object-fit: contain;">
                                </div>
                            </td>
                            <td class="text-start">
                                <div class="fw-bold text-dark"><?= esc($p['nama_produk']); ?></div>
                                <span class="badge bg-secondary-subtle text-secondary small"><?= esc($p['kategori_produk'] ?: 'Produk Unggulan'); ?></span>
                            </td>
                            <td class="text-start small text-muted"><?= nl2br(esc($p['deskripsi_produk'])); ?></td>
                            <td class="text-start small text-dark"><?= nl2br(esc($p['spesifikasi'] ?: '-')); ?></td>
                            <td>
                                <div class="d-flex justify-content-center gap-1">
                                    <button class="btn btn-sm btn-outline-primary px-3 rounded-pill fw-semibold" data-bs-toggle="modal" data-bs-target="#editProdukModal<?= $p['id']; ?>">
                                        Edit
                                    </button>
                                    <a href="/mitra/deleteProduct/<?= $p['id']; ?>" class="btn btn-sm btn-outline-danger px-3 rounded-pill fw-semibold" onclick="return confirm('Hapus produk ini?');">
                                        Hapus
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if(empty($products)): ?>
                        <tr class="bg-white">
                            <td colspan="6" class="text-center py-4 text-muted">Belum ada Detail Produk yang ditambahkan untuk Mitra Kerja ini. Silakan klik tombol <strong>+ Tambah Produk</strong>.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah Produk -->
<div class="modal fade" id="tambahProdukModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background-color: var(--inkindo-blue); color: white;">
                <h5 class="modal-title fw-bold">Tambah Detail Produk - <?= esc($mitra['nama_perusahaan']); ?></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="/mitra/storeProduct/<?= $mitra['id']; ?>" method="post" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-7">
                            <label class="form-label fw-semibold">Nama Produk / Layanan <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="nama_produk" placeholder="Contoh: Homogeneous Tile Granito Salsa Series 60x60" required>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label fw-semibold">Kategori / Seri Produk</label>
                            <input type="text" class="form-control" name="kategori_produk" placeholder="Contoh: Architectural Floor Tile">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Deskripsi Detail Produk <span class="text-danger">*</span></label>
                            <textarea class="form-control" name="deskripsi_produk" rows="3" placeholder="Jelaskan keunggulan, kegunaan, dan deskripsi produk..." required></textarea>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Spesifikasi Teknis (Ukuran, Material, Standar SNI/ISO, dll.)</label>
                            <textarea class="form-control" name="spesifikasi" rows="2" placeholder="Contoh: Ukuran: 600x600 mm | Ketebalan: 9.8 mm | Standar: SNI ISO 13006"></textarea>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Upload Foto / Gambar Produk (Opsional)</label>
                            <input type="file" class="form-control" name="gambar_produk" accept=".jpg,.jpeg,.png,.webp,.svg">
                            <small class="text-muted">Jika dikosongkan, akan menggunakan logo perusahaan sebagai gambar default.</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-inkindo">Simpan Produk</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Produk -->
<?php foreach($products as $p): ?>
<div class="modal fade" id="editProdukModal<?= $p['id']; ?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content text-start">
            <div class="modal-header" style="background-color: var(--inkindo-blue); color: white;">
                <h5 class="modal-title fw-bold">Edit Detail Produk</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="/mitra/updateProduct/<?= $p['id']; ?>" method="post" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-7">
                            <label class="form-label fw-semibold">Nama Produk / Layanan <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="nama_produk" value="<?= esc($p['nama_produk']); ?>" required>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label fw-semibold">Kategori / Seri Produk</label>
                            <input type="text" class="form-control" name="kategori_produk" value="<?= esc($p['kategori_produk']); ?>">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Deskripsi Detail Produk <span class="text-danger">*</span></label>
                            <textarea class="form-control" name="deskripsi_produk" rows="3" required><?= esc($p['deskripsi_produk']); ?></textarea>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Spesifikasi Teknis</label>
                            <textarea class="form-control" name="spesifikasi" rows="2"><?= esc($p['spesifikasi']); ?></textarea>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Ganti Foto / Gambar Produk (Opsional)</label>
                            <input type="file" class="form-control" name="gambar_produk" accept=".jpg,.jpeg,.png,.webp,.svg">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-inkindo">Update Produk</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endforeach; ?>

<?= $this->endSection() ?>

