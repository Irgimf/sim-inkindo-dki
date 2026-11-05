<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<style>
    .card-clickable {
        cursor: pointer;
        transition: all 0.2s ease;
        border: none;
        border-radius: 16px;
    }
    .card-clickable:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(0,0,0,0.04);
    }
    .card-icon-wrap {
        display: flex;
        align-items: center;
        justify-content: space-between;
        width: 100%;
    }
</style>

<div class="row mt-2">
    <?php
        $dbDash = \Config\Database::connect();
        $pendingMitraDash = $dbDash->table('mitra_kerja')->where('status_kemitraan', 'pending')->get()->getResultArray();
        if (!empty($pendingMitraDash)):
    ?>
    <div class="col-12 mb-3">
        <div class="alert alert-warning border-warning d-flex justify-content-between align-items-center flex-wrap gap-2 shadow-sm mb-0" style="border-radius: 14px;">
            <div>
                <i class="bi bi-bell-fill text-warning me-2"></i>
                <strong>Notifikasi Pengajuan Mitra Kerja:</strong> Terdapat <strong><?= count($pendingMitraDash); ?> perusahaan</strong> yang mengajukan kemitraan dan menunggu persetujuan Admin.
            </div>
            <a href="/mitra" class="btn btn-sm btn-inkindo rounded-pill px-3">
                Tinjau Pengajuan <i class="bi bi-arrow-right ms-1"></i>
            </a>
        </div>
    </div>
    <?php endif; ?>
    <div class="col-md-3 mb-4">
        <div class="card p-4 card-clickable" style="background-color: #e0f2fe;" onclick="showList('list-non-jk')">
            <div class="card-icon-wrap">
                <h6 class="fw-bold mb-0 text-start" style="color: #0284c7; line-height: 1.4;">Non Jasa<br>Konstruksi</h6>
                <h2 class="mb-0 fw-bold" style="color: #0284c7;"><?= count($non_jk) ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card p-4 card-clickable" style="background-color: #dcfce7;" onclick="showList('list-jk')">
            <div class="card-icon-wrap">
                <h6 class="fw-bold mb-0 text-start" style="color: #16a34a; line-height: 1.4;">Jasa<br>Konstruksi</h6>
                <h2 class="mb-0 fw-bold" style="color: #16a34a;"><?= count($jk) ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card p-4 card-clickable" style="background-color: #fef3c7;" onclick="showList('list-jk-non-jk')">
            <div class="card-icon-wrap">
                <h6 class="fw-bold mb-0 text-start" style="color: #d97706; line-height: 1.4;">JK dan<br>Non JK</h6>
                <h2 class="mb-0 fw-bold" style="color: #d97706;"><?= count($jk_dan_non_jk) ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card p-4 card-clickable" style="background-color: #f3e8ff;" onclick="showList('list-tidak-punya')">
            <div class="card-icon-wrap">
                <h6 class="fw-bold mb-0 text-start" style="color: #9333ea; line-height: 1.4;">Tidak Punya<br>JK & Non JK</h6>
                <h2 class="mb-0 fw-bold" style="color: #9333ea;"><?= count($tidak_punya) ?></h2>
            </div>
        </div>
    </div>
</div>

<?php
$lists = [
    ['id' => 'list-non-jk', 'title' => 'Non Jasa Konstruksi', 'data' => $non_jk],
    ['id' => 'list-jk', 'title' => 'Jasa Konstruksi', 'data' => $jk],
    ['id' => 'list-jk-non-jk', 'title' => 'JK dan Non JK', 'data' => $jk_dan_non_jk],
    ['id' => 'list-tidak-punya', 'title' => 'Tidak Punya JK & Non JK', 'data' => $tidak_punya],
];
?>

<?php foreach ($lists as $list): ?>
<div id="<?= $list['id'] ?>" class="company-list mt-2" style="display: none;">
    <div class="d-flex justify-content-between align-items-center mb-4 px-2">
        <div>
            <h4 class="fw-bold mb-1 text-dark" style="font-size: 1.25rem;"><?= $list['title'] ?></h4>
            <p class="text-muted small mb-0"><?= count($list['data']) ?> data perusahaan ditemukan</p>
        </div>
    </div>
    <div class="table-responsive px-2 pb-3">
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
                <?php $i = 1; foreach($list['data'] as $c): ?>
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
                        <a href="/sbu/detail/<?= $c['id']; ?>" class="btn btn-sm px-3 rounded-pill fw-semibold" style="background-color: var(--inkindo-blue); color: white;">Detail</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endforeach; ?>

<script>
function showList(listId) {
    var targetList = document.getElementById(listId);
    var isCurrentlyVisible = targetList.style.display === 'block';

    // Sembunyikan semua list terlebih dahulu
    var lists = document.getElementsByClassName('company-list');
    for (var i = 0; i < lists.length; i++) {
        lists[i].style.display = 'none';
    }

    // Jika list yang diklik sebelumnya tidak terlihat, tampilkan. 
    // Jika sudah terlihat (dan baru saja kita hide di atas), biarkan tersembunyi (toggle off).
    if (!isCurrentlyVisible) {
        targetList.style.display = 'block';
    }
}
</script>
<?= $this->endSection() ?>
