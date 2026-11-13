<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4 mt-2">
    <h3 class="fw-bold mb-0" style="color: var(--inkindo-blue);"><?= $title; ?></h3>
    <?php if(session()->get('role') === 'admin' || empty($companies)): ?>
        <a href="/perusahaan/create" class="btn px-4 py-2 rounded-pill fw-bold" style="background-color: var(--inkindo-blue); color: white; box-shadow: 0 4px 10px rgba(10,100,164,0.2); transition: all 0.3s ease;" onmouseover="this.style.transform='translateY(-2px)';" onmouseout="this.style.transform='translateY(0)';"><i class="bi bi-plus-lg me-1"></i> Tambah Data PT</a>
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
                    <th class="text-start" width="20%">Nama PT</th>
                    <th width="15%">Nama PJBU</th>
                    <th width="12%">No KTA</th>
                    <th width="20%">Alamat</th>
                    <th width="10%">Kualifikasi</th>
                    <th width="10%">Kategori SBU</th>
                    <th width="8%">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $i = 1; ?>
                <?php foreach($companies as $c): ?>
                <tr>
                    <td class="text-muted"><?= $i++; ?></td>
                    <td class="text-start fw-semibold text-dark"><?= esc($c['nama_pt']); ?></td>
                    <td class="text-muted"><?= esc($c['nama_pjbu']); ?></td>
                    <td><span class="badge rounded-pill badge-soft-secondary px-3 py-2"><?= esc($c['no_kta']); ?></span></td>
                    <td class="text-muted small"><?= esc($c['alamat_pt']); ?></td>
                    <td>
                        <?php 
                            $k = strtolower($c['kualifikasi'] ?? '');
                            $bg = 'badge-soft-primary';
                            if($k == 'kecil') $bg = 'badge-soft-success';
                            else if($k == 'menengah') $bg = 'badge-soft-warning';
                            else if($k == 'besar') $bg = 'badge-soft-danger';
                        ?>
                        <span class="badge rounded-pill <?= $bg; ?> px-3 py-2"><?= strtoupper($c['kualifikasi'] ?? ''); ?></span>
                    </td>
                    <td>
                        <?php 
                            $kat = isset($c['kategori_sbu']) && !empty($c['kategori_sbu']) ? $c['kategori_sbu'] : '-';
                        ?>
                        <span class="badge rounded-pill badge-soft-secondary px-3 py-2"><?= esc($kat); ?></span>
                    </td>
                    <td>
                        <div class="d-flex justify-content-center gap-1">
                            <a href="/perusahaan/edit/<?= $c['id']; ?>" class="btn btn-sm px-3 rounded-pill" style="background-color: var(--inkindo-blue); color: white; font-weight: 500;">Edit</a>
                            <a href="/perusahaan/delete/<?= $c['id']; ?>" class="btn btn-sm btn-danger px-3 rounded-pill fw-semibold" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?');">Hapus</a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
        
        <?php if(empty($companies)): ?>
            <?php if(session()->get('role') === 'user'): ?>
            <div class="text-center py-5">
                <p class="text-muted mb-4">Anda belum menambahkan Data Perusahaan.</p>
                <a href="/perusahaan/create" class="btn px-4 py-2 rounded-pill fw-bold" style="background-color: var(--inkindo-blue); color: white; box-shadow: 0 4px 10px rgba(10,100,164,0.2); transition: all 0.3s ease;" onmouseover="this.style.transform='translateY(-2px)';" onmouseout="this.style.transform='translateY(0)';"><i class="bi bi-plus-lg me-1"></i> Tambah Data PT Sekarang</a>
            </div>
            <?php else: ?>
            <div class="text-center py-5 text-muted">Belum ada data PT. Silakan tambah data baru.</div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
<?= $this->endSection() ?>
