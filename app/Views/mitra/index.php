<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4 mt-2">
    <div>
        <h3 class="fw-bold mb-1" style="color: var(--inkindo-blue);"><?= esc($title); ?></h3>
        <p class="text-muted small mb-0">Kelola logo, profil perusahaan, dan katalog produk Mitra Kerja yang tampil otomatis di Web Profile INKINDO DKI Jakarta.</p>
    </div>
    <?php if(session()->get('role') === 'admin' || empty($mitraList)): ?>
        <a href="/mitra/create" class="btn btn-inkindo px-4 py-2 rounded-pill fw-bold">
            <i class="bi bi-plus-lg me-1"></i> Tambah Mitra Kerja
        </a>
    <?php endif; ?>
</div>

<?php if(session()->getFlashdata('pesan')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= session()->getFlashdata('pesan'); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="table-custom-wrap">
    <div class="table-responsive" style="overflow-x: visible;">
        <table class="table-custom text-center align-middle">
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th width="12%">Logo</th>
                    <th class="text-start" width="22%">Nama Perusahaan Mitra</th>
                    <th class="text-start" width="22%">Deskripsi Singkat</th>
                    <th width="14%">Kategori Mitra</th>
                    <th width="10%">Detail Produk</th>
                    <th width="15%">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $i = 1; ?>
                <?php foreach($mitraList as $m): ?>
                <tr>
                    <td class="text-muted"><?= $i++; ?></td>
                    <td>
                        <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 6px 10px; display: inline-flex; align-items: center; justify-content: center; width: 95px; height: 52px;">
                            <img src="/<?= esc($m['logo'] ?: 'logo-inkindo.png'); ?>" alt="<?= esc($m['nama_perusahaan']); ?>" style="max-height: 38px; max-width: 80px; object-fit: contain;">
                        </div>
                    </td>
                    <td class="text-start">
                        <div class="fw-bold text-dark"><?= esc($m['nama_perusahaan']); ?></div>
                        <a href="http://localhost:8000/mitra-kerja/<?= esc($m['slug']); ?>" target="_blank" class="small text-decoration-none" style="color: var(--inkindo-blue);">
                            <i class="bi bi-box-arrow-up-right"></i> /mitra-kerja/<?= esc($m['slug']); ?>
                        </a>
                    </td>
                    <td class="text-start text-muted small">
                        <?= esc(mb_strimwidth($m['deskripsi'] ?? '-', 0, 95, '...')); ?>
                    </td>
                    <td>
                        <span class="badge rounded-pill badge-soft-primary px-3 py-2"><?= esc($m['kategori_bisnis'] ?: 'Mitra Kerja'); ?></span>
                    </td>
                    <td>
                        <a href="/mitra/products/<?= $m['id']; ?>" class="badge rounded-pill badge-soft-success px-3 py-2 text-decoration-none">
                            <i class="bi bi-box-seam me-1"></i> <?= (int) $m['product_count']; ?> Produk
                        </a>
                    </td>
                    <td>
                        <div class="d-flex justify-content-center gap-1 flex-wrap">
                            <a href="/mitra/products/<?= $m['id']; ?>" class="btn btn-sm btn-warning px-3 rounded-pill fw-semibold text-dark" title="Kelola Detail Produk">
                                <i class="bi bi-box-seam"></i> Produk
                            </a>
                            <a href="/mitra/edit/<?= $m['id']; ?>" class="btn btn-sm px-3 rounded-pill" style="background-color: var(--inkindo-blue); color: white; font-weight: 500;">
                                Edit
                            </a>
                            <a href="/mitra/delete/<?= $m['id']; ?>" class="btn btn-sm btn-danger px-3 rounded-pill fw-semibold" onclick="return confirm('Apakah Anda yakin ingin menghapus Mitra Kerja ini beserta produknya?');">
                                Hapus
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php if(empty($mitraList)): ?>
        <div class="text-center py-5">
            <p class="text-muted mb-4">Anda belum menambahkan profil Perusahaan Mitra Kerja.</p>
            <a href="/mitra/create" class="btn btn-inkindo px-4 py-2 rounded-pill fw-bold">
                <i class="bi bi-plus-lg me-1"></i> Tambah Data Mitra Kerja Sekarang
            </a>
        </div>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>

