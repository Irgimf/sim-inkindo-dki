<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4 mt-2">
    <div>
        <h3 class="fw-bold mb-1" style="color: var(--inkindo-blue);"><?= esc($title); ?></h3>
        <p class="text-muted small mb-0">Kelola logo, pengajuan tanggal berakhir kemitraan, profil perusahaan, dan katalog produk Mitra Kerja INKINDO DKI Jakarta.</p>
    </div>
    <?php if(session()->get('role') === 'admin' || empty($mitraList)): ?>
        <a href="/mitra/create" class="btn btn-inkindo px-4 py-2 rounded-pill fw-bold">
            <i class="bi bi-plus-lg me-1"></i> <?= session()->get('role') === 'admin' ? 'Tambah Mitra Kerja' : 'Ajukan Mitra Kerja' ?>
        </a>
    <?php endif; ?>
</div>

<?php if(session()->getFlashdata('pesan')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= session()->getFlashdata('pesan'); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if(session()->get('role') !== 'admin'): ?>
    <?php foreach($mitraList as $um): ?>
        <?php if(($um['status_kemitraan'] ?? '') === 'rejected'): ?>
            <div class="alert alert-danger border-danger shadow-sm mb-4 p-4" style="border-radius: 16px;">
                <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
                    <div>
                        <h6 class="fw-bold text-danger mb-1">
                            <i class="bi bi-exclamation-triangle-fill me-1"></i> Pengajuan Kemitraan Anda Ditolak oleh Admin
                        </h6>
                        <p class="mb-2 small text-dark">Berikut adalah catatan / alasan penolakan dari Admin:</p>
                        <div class="p-3 rounded-3 bg-white border border-danger-subtle text-dark fw-semibold small">
                            "<?= nl2br(esc($um['alasan_penolakan'] ?: 'Pengajuan belum memenuhi persyaratan kemitraan.')); ?>"
                        </div>
                    </div>
                    <a href="/mitra/edit/<?= $um['id']; ?>" class="btn btn-sm btn-danger rounded-pill px-4 py-2 fw-bold">
                        <i class="bi bi-pencil-square me-1"></i> Perbaiki &amp; Ajukan Ulang
                    </a>
                </div>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>
<?php endif; ?>

<?php if(session()->get('role') === 'admin' && !empty($pendingList)): ?>
<div class="card border-warning mb-4 shadow-sm" style="border-radius: 16px; background: #fffbeb;">
    <div class="card-body p-4">
        <div class="d-flex align-items-center gap-2 mb-3">
            <span class="badge bg-warning text-dark rounded-pill px-3 py-2 fw-bold">
                <i class="bi bi-bell-fill me-1"></i> <?= count($pendingList); ?> Pengajuan Mitra Kerja Menunggu Persetujuan
            </span>
        </div>
        <div class="table-responsive">
            <table class="table table-sm table-bordered bg-white align-middle mb-0" style="border-radius: 10px; overflow: hidden;">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">Nama Perusahaan</th>
                        <th>Kategori</th>
                        <th class="text-center">Batas Waktu Diajukan (s/d Tanggal)</th>
                        <th>Kontak / Email</th>
                        <th class="text-center" width="260">Keputusan Admin</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($pendingList as $pm): ?>
                    <?php $pmExp = $pm['expired_at'] ? date('d-m-Y', strtotime($pm['expired_at'])) : '-'; ?>
                    <tr>
                        <td class="ps-3 fw-bold text-dark"><?= esc($pm['nama_perusahaan']); ?></td>
                        <td class="small text-muted"><?= esc($pm['kategori_bisnis']); ?></td>
                        <td class="text-center">
                            <span class="badge bg-primary rounded-pill px-3 py-1">
                                <i class="bi bi-calendar-event me-1"></i> s/d <?= esc($pmExp); ?>
                            </span>
                        </td>
                        <td class="small"><?= esc($pm['telepon']); ?> / <?= esc($pm['email']); ?></td>
                        <td class="text-center py-2">
                            <div class="d-flex justify-content-center gap-2">
                                <form action="/mitra/approve/<?= $pm['id']; ?>" method="post" class="d-inline">
                                    <input type="hidden" name="expired_at" value="<?= esc($pm['expired_at'] ?? ''); ?>">
                                    <button type="submit" class="btn btn-sm btn-success rounded-pill px-3 fw-semibold">
                                        <i class="bi bi-check-circle me-1"></i> Setujui
                                    </button>
                                </form>
                                <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-3 fw-semibold"
                                    onclick="openRejectModal(<?= (int) $pm['id']; ?>, '<?= esc(addslashes($pm['nama_perusahaan'])); ?>')">
                                    Tolak
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php endif; ?>

<div class="table-custom-wrap">
    <div class="table-responsive" style="overflow-x: visible;">
        <table class="table-custom text-center align-middle">
            <thead>
                <tr>
                    <th width="4%">No</th>
                    <th width="11%">Logo</th>
                    <th class="text-start" width="22%">Nama Perusahaan Mitra</th>
                    <th width="18%">Status &amp; Masa Berlaku</th>
                    <th width="10%">Produk</th>
                    <th width="10%">Pesan Masuk</th>
                    <th width="25%">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $i = 1; ?>
                <?php foreach($mitraList as $m): ?>
                <?php
                    $status = $m['status_kemitraan'] ?? 'active';
                    $expiredAt = $m['expired_at'] ?? null;
                    $isExpired = ($status === 'expired') || ($status === 'active' && $expiredAt && strtotime($expiredAt) < strtotime(date('Y-m-d')));
                    if ($isExpired) {
                        $status = 'expired';
                    }
                ?>
                <tr>
                    <td class="text-muted"><?= $i++; ?></td>
                    <td>
                        <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 6px 10px; display: inline-flex; align-items: center; justify-content: center; width: 92px; height: 50px;">
                            <img src="/<?= esc($m['logo'] ?: 'logo-inkindo.png'); ?>" alt="<?= esc($m['nama_perusahaan']); ?>" style="max-height: 36px; max-width: 78px; object-fit: contain;">
                        </div>
                    </td>
                    <td class="text-start">
                        <div class="fw-bold text-dark"><?= esc($m['nama_perusahaan']); ?></div>
                        <div class="small text-muted mb-1"><?= esc($m['kategori_bisnis']); ?></div>
                        <?php if($status === 'active'): ?>
                            <a href="http://localhost:8000/mitra-kerja/<?= esc($m['slug']); ?>" target="_blank" class="small text-decoration-none" style="color: var(--inkindo-blue);">
                                <i class="bi bi-box-arrow-up-right"></i> /mitra-kerja/<?= esc($m['slug']); ?>
                            </a>
                        <?php else: ?>
                            <span class="small text-muted"><i class="bi bi-slash-circle"></i> Halaman Web Nonaktif</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if($status === 'pending'): ?>
                            <span class="badge rounded-pill badge-soft-warning px-3 py-2 d-inline-block mb-1">
                                <i class="bi bi-hourglass-split me-1"></i> Menunggu Persetujuan
                            </span>
                            <div class="small text-muted">
                                Diajukan s/d: <strong><?= $expiredAt ? date('d-m-Y', strtotime($expiredAt)) : '-'; ?></strong>
                            </div>
                        <?php elseif($status === 'active'): ?>
                            <span class="badge rounded-pill badge-soft-success px-3 py-2 d-inline-block mb-1">
                                <i class="bi bi-check-circle-fill me-1"></i> Aktif
                            </span>
                            <div class="small text-muted">
                                Berlaku s/d: <strong><?= $expiredAt ? date('d-m-Y', strtotime($expiredAt)) : '-'; ?></strong>
                            </div>
                        <?php elseif($status === 'expired'): ?>
                            <span class="badge rounded-pill badge-soft-danger px-3 py-2 d-inline-block mb-1">
                                <i class="bi bi-exclamation-octagon-fill me-1"></i> Expired (Mati)
                            </span>
                            <div class="small text-danger">
                                Berakhir: <?= $expiredAt ? date('d-m-Y', strtotime($expiredAt)) : '-'; ?>
                            </div>
                        <?php else: ?>
                            <span class="badge rounded-pill badge-soft-danger px-3 py-2 d-inline-block mb-1">
                                <i class="bi bi-x-circle-fill me-1"></i> Ditolak
                            </span>
                            <?php if(!empty($m['alasan_penolakan'])): ?>
                                <div class="small text-danger mt-1" style="max-width: 220px; margin: 0 auto; line-height: 1.35;">
                                    <strong>Alasan:</strong> <?= esc($m['alasan_penolakan']); ?>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="/mitra/products/<?= $m['id']; ?>" class="badge rounded-pill badge-soft-primary px-3 py-2 text-decoration-none">
                            <i class="bi bi-box-seam me-1"></i> <?= (int) $m['product_count']; ?> Produk
                        </a>
                    </td>
                    <td>
                        <a href="/mitra/messages" class="badge rounded-pill <?= ((int)$m['unread_msg_count'] > 0) ? 'badge-soft-danger' : 'badge-soft-secondary'; ?> px-3 py-2 text-decoration-none">
                            <i class="bi bi-envelope-fill me-1"></i> <?= (int) $m['unread_msg_count']; ?> Baru
                        </a>
                    </td>
                    <td>
                        <div class="d-flex justify-content-center gap-1 flex-wrap">
                            <?php if(session()->get('role') === 'admin' && $status !== 'active'): ?>
                                <form action="/mitra/approve/<?= $m['id']; ?>" method="post" class="d-inline">
                                    <input type="hidden" name="expired_at" value="<?= esc($expiredAt ?? ''); ?>">
                                    <button type="submit" class="btn btn-sm btn-success px-3 rounded-pill fw-semibold" title="Setujui & Aktifkan Mitra">
                                        <i class="bi bi-check-lg"></i> Setujui
                                    </button>
                                </form>
                            <?php endif; ?>
                            <?php if(session()->get('role') === 'admin' && $status === 'pending'): ?>
                                <button type="button" class="btn btn-sm btn-outline-danger px-3 rounded-pill fw-semibold"
                                    onclick="openRejectModal(<?= (int) $m['id']; ?>, '<?= esc(addslashes($m['nama_perusahaan'])); ?>')">
                                    Tolak
                                </button>
                            <?php endif; ?>
                            <a href="/mitra/products/<?= $m['id']; ?>" class="btn btn-sm btn-warning px-3 rounded-pill fw-semibold text-dark" title="Kelola Detail Produk">
                                <i class="bi bi-box-seam"></i> Produk
                            </a>
                            <a href="/mitra/edit/<?= $m['id']; ?>" class="btn btn-sm px-3 rounded-pill" style="background-color: var(--inkindo-blue); color: white; font-weight: 500;">
                                <?= ($status === 'expired' || $status === 'rejected') && session()->get('role') !== 'admin' ? 'Perpanjang / Ajukan Ulang' : 'Edit'; ?>
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
            <p class="text-muted mb-4">Anda belum mengajukan profil Perusahaan Mitra Kerja.</p>
            <a href="/mitra/create" class="btn btn-inkindo px-4 py-2 rounded-pill fw-bold">
                <i class="bi bi-send-check me-1"></i> Ajukan Mitra Kerja Sekarang
            </a>
        </div>
    <?php endif; ?>
</div>

<?php if(session()->get('role') === 'admin'): ?>
<!-- Modal Pop-up Alasan Penolakan Mitra Kerja -->
<div class="modal fade" id="rejectMitraModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-x-octagon-fill me-2"></i>Tolak Pengajuan Mitra Kerja
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="rejectMitraForm" action="" method="post">
                <div class="modal-body p-4">
                    <p class="mb-3 text-dark">
                        Anda akan menolak pengajuan kemitraan dari <strong id="rejectMitraName">-</strong>.
                    </p>
                    <div class="mb-2">
                        <label for="alasanPenolakanInput" class="form-label fw-semibold text-dark">
                            Alasan Penolakan <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control" id="alasanPenolakanInput" name="alasan_penolakan" rows="4"
                            placeholder="Tuliskan alasan penolakan secara jelas (contoh: Dokumen profil belum lengkap, logo tidak sesuai, atau masa berlaku yang diajukan terlalu panjang)..." required></textarea>
                        <small class="text-muted d-block mt-1">Alasan ini akan ditampilkan langsung pada dashboard akun Mitra Kerja yang bersangkutan.</small>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger rounded-pill px-4 fw-semibold">
                        <i class="bi bi-send-fill me-1"></i> Kirim Penolakan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openRejectModal(mitraId, mitraName) {
    const form = document.getElementById('rejectMitraForm');
    const nameEl = document.getElementById('rejectMitraName');
    const textarea = document.getElementById('alasanPenolakanInput');
    form.action = '/mitra/reject/' + mitraId;
    nameEl.textContent = mitraName;
    textarea.value = '';
    const modal = new bootstrap.Modal(document.getElementById('rejectMitraModal'));
    modal.show();
    setTimeout(() => textarea.focus(), 300);
}
</script>
<?php endif; ?>

<?= $this->endSection() ?>
