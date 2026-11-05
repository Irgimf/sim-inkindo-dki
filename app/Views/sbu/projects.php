<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="mb-4 mt-2">
    <a href="/sbu/detail/<?= $company['id'] ?>" class="btn btn-outline-secondary btn-sm mb-3"><i class="bi bi-arrow-left"></i> Kembali ke Detail SBU</a>
    <h3 class="fw-bold" style="color: var(--inkindo-blue);"><?= $title; ?></h3>
</div>

<?php if(session()->getFlashdata('pesan')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= session()->getFlashdata('pesan'); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<!-- MASTER: INFORMASI SUBKLASIFIKASI & PERUSAHAAN -->
<div class="card card-custom mb-4">
    <div class="card-header bg-light fw-bold text-uppercase" style="color: var(--inkindo-blue);">
        Informasi Subklasifikasi
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <table class="table table-sm table-borderless mb-0">
                    <tr>
                        <td width="35%" class="text-muted">Subklasifikasi</td>
                        <td width="5%">:</td>
                        <td class="fw-semibold"><span class="badge bg-dark fs-6"><?= esc($sbu['kode_kbli']); ?></span></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Judul Subklasifikasi</td>
                        <td>:</td>
                        <td class="fw-semibold"><?= esc($sbu['judul_kbli']); ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Kualifikasi</td>
                        <td>:</td>
                        <td>
                            <?php 
                                $k = strtolower($sbu['kualifikasi']);
                                $bg = 'bg-primary';
                                if($k == 'kecil') $bg = 'bg-success';
                                else if($k == 'menengah') $bg = 'bg-warning text-dark';
                                else if($k == 'besar') $bg = 'bg-danger';
                            ?>
                            <span class="badge <?= $bg; ?>"><?= strtoupper($sbu['kualifikasi']); ?></span>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted">Masa Berlaku</td>
                        <td>:</td>
                        <td class="fw-semibold"><?= date('d-m-Y', strtotime($sbu['masa_berlaku'])); ?></td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6 border-start">
                <table class="table table-sm table-borderless mb-0 ms-2">
                    <tr>
                        <td width="35%" class="text-muted">Perusahaan</td>
                        <td width="5%">:</td>
                        <td class="fw-semibold"><?= esc($company['nama_pt']); ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted">No KTA</td>
                        <td>:</td>
                        <td class="fw-semibold"><?= esc($company['no_kta']); ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Kategori SBU</td>
                        <td>:</td>
                        <td>
                            <?php 
                                $kat = isset($company['kategori_sbu']) && !empty($company['kategori_sbu']) ? $company['kategori_sbu'] : '-';
                            ?>
                            <span class="badge bg-secondary fw-semibold"><?= $kat; ?></span>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- DETAIL: DAFTAR PROYEK / PEKERJAAN -->
<div class="card card-custom">
    <div class="card-header bg-light d-flex justify-content-between align-items-center">
        <span class="fw-bold text-uppercase" style="color: var(--inkindo-blue);">Daftar Proyek / Pekerjaan</span>
        <button class="btn btn-sm btn-inkindo" data-bs-toggle="modal" data-bs-target="#tambahProyekModal">
            + Tambah Proyek
        </button>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle text-center">
                <thead style="background-color: var(--inkindo-blue); color: white;">
                    <tr>
                        <th class="py-3" width="5%">No</th>
                        <th class="py-3 text-start">Pekerjaan</th>
                        <th class="py-3 text-end">Nilai Proyek</th>
                        <th class="py-3">Tahun</th>
                        <th class="py-3">Durasi</th>
                        <th class="py-3" width="10%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $pi = 1; foreach($projects as $p): ?>
                        <tr class="bg-white">
                            <td class="fw-semibold"><?= $pi++; ?></td>
                            <td class="text-start fw-semibold"><?= esc($p['nama_pekerjaan']); ?></td>
                            <td class="text-end">Rp <?= number_format($p['nilai_proyek'], 0, ',', '.'); ?></td>
                            <td><?= esc($p['tahun_proyek']); ?></td>
                            <td><?= esc($p['lama_proyek']); ?></td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary px-3 rounded-pill fw-semibold" data-bs-toggle="modal" data-bs-target="#editProyekModal<?= $p['id']; ?>">
                                    Edit
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah Proyek -->
<div class="modal fade" id="tambahProyekModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold">Tambah Pekerjaan - <?= $sbu['kode_kbli']; ?></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="/sbu/storeProject/<?= $sbu['id']; ?>" method="post">
                <div class="modal-body text-start">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Pekerjaan</label>
                        <input type="text" class="form-control" name="nama_pekerjaan" required placeholder="Contoh: Pembangunan Gedung A">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nilai Proyek (Rp)</label>
                        <input type="number" class="form-control" name="nilai_proyek" required placeholder="Hanya angka">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tahun Proyek</label>
                        <input type="number" class="form-control" name="tahun_proyek" required placeholder="Contoh: 2023">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Lama Proyek</label>
                        <input type="text" class="form-control" name="lama_proyek" required placeholder="Contoh: 6 Bulan">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary px-4">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Proyek -->
<?php foreach($projects as $p): ?>
    <div class="modal fade" id="editProyekModal<?= $p['id']; ?>" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold">Edit Pekerjaan - <?= $sbu['kode_kbli']; ?></h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="/sbu/updateProject/<?= $p['id']; ?>" method="post">
                    <div class="modal-body text-start">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nama Pekerjaan</label>
                            <input type="text" class="form-control" name="nama_pekerjaan" value="<?= esc($p['nama_pekerjaan']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nilai Proyek (Rp)</label>
                            <input type="number" class="form-control" name="nilai_proyek" value="<?= esc($p['nilai_proyek']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Tahun Proyek</label>
                            <input type="number" class="form-control" name="tahun_proyek" value="<?= esc($p['tahun_proyek']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Lama Proyek</label>
                            <input type="text" class="form-control" name="lama_proyek" value="<?= esc($p['lama_proyek']); ?>" required>
                        </div>
                    </div>
                    <div class="modal-footer d-flex justify-content-between">
                        <a href="/sbu/deleteProject/<?= $p['id']; ?>" class="btn btn-outline-danger" onclick="return confirm('Hapus pekerjaan ini?');">Hapus Proyek</a>
                        <div>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary px-4">Update Data</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php endforeach; ?>

<?= $this->endSection() ?>
