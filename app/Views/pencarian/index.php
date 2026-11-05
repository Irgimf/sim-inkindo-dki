<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<style>
    @media print {
        @page {
            size: landscape;
            margin: 1.5cm;
        }
        body * {
            visibility: hidden;
        }
        #printableArea, #printableArea * {
            visibility: visible;
        }
        #printableArea {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }
        .no-print {
            display: none !important;
        }
        /* Perapihan tabel saat diprint agar tidak berhimpitan */
        #printableArea .card {
            border: none !important;
            box-shadow: none !important;
        }
        #printableArea table {
            width: 100% !important;
        }
        #printableArea th, #printableArea td {
            padding: 12px 10px !important; /* Padding lebih besar */
            font-size: 12px !important;
            border-bottom: 1px solid #ddd !important;
        }
        #printableArea th {
            background-color: #004a99 !important; /* Warna biru */
            color: white !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        .badge {
            border: 1px solid #333 !important;
        }
    }
</style>

<div class="d-flex justify-content-between align-items-center mb-4 mt-2 no-print">
    <h3 class="fw-bold" style="color: var(--inkindo-blue);"><?= esc($title); ?></h3>
</div>

<div class="card card-custom mb-4 no-print">
    <div class="card-body">
        <form action="/pencarian" method="get">
            <div class="row g-2 align-items-end">
                <div class="col-md col-12">
                    <label class="form-label fw-semibold" style="font-size: 14px;">Nama PT</label>
                    <input type="text" class="form-control form-control-sm" name="nama_pt" value="<?= esc($filters['nama_pt'] ?? '') ?>" placeholder="Cari Nama PT...">
                </div>
                <div class="col-md col-12">
                    <label class="form-label fw-semibold" style="font-size: 14px;">Kualifikasi</label>
                    <select class="form-select form-select-sm" name="kualifikasi">
                        <option value="">Semua</option>
                        <option value="Kecil" <?= (isset($filters['kualifikasi']) && $filters['kualifikasi'] == 'Kecil') ? 'selected' : '' ?>>Kecil</option>
                        <option value="Menengah" <?= (isset($filters['kualifikasi']) && $filters['kualifikasi'] == 'Menengah') ? 'selected' : '' ?>>Menengah</option>
                        <option value="Besar" <?= (isset($filters['kualifikasi']) && $filters['kualifikasi'] == 'Besar') ? 'selected' : '' ?>>Besar</option>
                    </select>
                </div>
                <div class="col-md col-12">
                    <label class="form-label fw-semibold" style="font-size: 14px;">Kategori SBU</label>
                    <select class="form-select form-select-sm" name="kategori_sbu">
                        <option value="">Semua</option>
                        <option value="Non Jasa Konstruksi" <?= (isset($filters['kategori_sbu']) && $filters['kategori_sbu'] == 'Non Jasa Konstruksi') ? 'selected' : '' ?>>Non Jasa Konstruksi</option>
                        <option value="Jasa Konstruksi" <?= (isset($filters['kategori_sbu']) && $filters['kategori_sbu'] == 'Jasa Konstruksi') ? 'selected' : '' ?>>Jasa Konstruksi</option>
                        <option value="NJK dan JK" <?= (isset($filters['kategori_sbu']) && $filters['kategori_sbu'] == 'NJK dan JK') ? 'selected' : '' ?>>NJK dan JK</option>
                        <option value="Tidak Punya NJK dan JK" <?= (isset($filters['kategori_sbu']) && $filters['kategori_sbu'] == 'Tidak Punya NJK dan JK') ? 'selected' : '' ?>>Tidak Punya NJK dan JK</option>
                    </select>
                </div>
                <div class="col-md col-12">
                    <label class="form-label fw-semibold" style="font-size: 14px;">Subklasifikasi</label>
                    <select class="form-select form-select-sm" name="kode_kbli">
                        <option value="">Semua</option>
                        <?php if(isset($kbli_list)): foreach($kbli_list as $kbli): ?>
                            <option value="<?= esc($kbli['kode_kbli']) ?>" <?= (isset($filters['kode_kbli']) && $filters['kode_kbli'] == $kbli['kode_kbli']) ? 'selected' : '' ?>><?= esc($kbli['kode_kbli']) ?> - <?= esc($kbli['judul_kbli']) ?></option>
                        <?php endforeach; endif; ?>
                    </select>
                </div>
                <div class="col-md-3 col-12">
                    <label class="form-label fw-semibold" style="font-size: 14px;">Rentang Nilai Proyek</label>
                    <div class="input-group input-group-sm">
                        <input type="text" class="form-control" name="nilai_proyek_min" value="<?= esc($filters['nilai_proyek_min'] ?? '') ?>" placeholder="Min">
                        <span class="input-group-text">-</span>
                        <input type="text" class="form-control" name="nilai_proyek_max" value="<?= esc($filters['nilai_proyek_max'] ?? '') ?>" placeholder="Max">
                    </div>
                </div>
                
                <div class="col-md-auto col-12">
                    <div class="d-flex gap-1">
                        <button type="submit" class="btn btn-sm btn-inkindo w-100" title="Cari"><i class="bi bi-search"></i></button>
                        <button type="button" class="btn btn-sm btn-secondary w-100" onclick="window.print()" title="Print"><i class="bi bi-printer"></i></button>
                        
                        <?php
                            $exportUrl = '/pencarian/exportExcel';
                            $queryParams = http_build_query($filters ?? []);
                            if (!empty($queryParams)) {
                                $exportUrl .= '?' . $queryParams;
                            }
                        ?>
                        <a href="<?= $exportUrl ?>" class="btn btn-sm btn-success w-100" title="Export Excel"><i class="bi bi-file-earmark-excel"></i></a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<div id="printableArea">
    <div class="table-custom-wrap">
        <div class="table-responsive" style="overflow-x: visible;">
            <table class="table-custom text-center align-middle">
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th class="text-start" width="20%">Nama PT</th>
                        <th width="10%">Kualifikasi</th>
                        <th width="12%">Kategori SBU</th>
                        <th width="10%">Subklasifikasi</th>
                        <th width="18%">Judul Subklasifikasi</th>
                        <th width="15%">Nama Pekerjaan</th>
                        <th width="10%">Nilai Proyek</th>
                    </tr>
                </thead>
                <tbody>
                        <?php $i = 1; foreach($results as $row): ?>
                        <tr>
                            <td class="text-muted"><?= $i++; ?></td>
                            <td class="text-start fw-semibold text-dark"><?= esc($row['nama_pt'] ?? '-'); ?></td>
                            <td>
                                <?php 
                                    $k = strtolower($row['c_kualifikasi'] ?? '');
                                    $bg = 'badge-soft-primary';
                                    if($k == 'kecil') $bg = 'badge-soft-success';
                                    else if($k == 'menengah') $bg = 'badge-soft-warning';
                                    else if($k == 'besar') $bg = 'badge-soft-danger';
                                ?>
                                <span class="badge rounded-pill <?= $bg; ?> px-3 py-2"><?= strtoupper($row['c_kualifikasi'] ?? '-'); ?></span>
                            </td>
                            <td><span class="badge rounded-pill badge-soft-secondary px-3 py-2"><?= esc($row['kategori_sbu'] ?? '-'); ?></span></td>
                            <td><span class="badge rounded-pill badge-soft-secondary px-3 py-2"><?= esc($row['kode_kbli'] ?? '-'); ?></span></td>
                            <td class="text-muted small"><?= esc($row['judul_kbli'] ?? '-'); ?></td>
                            <td class="text-muted small"><?= esc($row['nama_pekerjaan'] ?? '-'); ?></td>
                            <td class="fw-bold text-success">
                                <?php
                                    $n = $row['nilai_proyek'] ?? '';
                                    $n_clean = preg_replace('/[^0-9]/', '', $n);
                                    echo $n_clean !== '' ? number_format($n_clean, 0, ',', '.') : esc($n ?: '-');
                                ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
