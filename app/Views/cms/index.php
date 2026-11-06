<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<?php
    $tipeMap = [
        'agenda'    => ['label' => 'Agenda Kegiatan', 'badge' => 'badge-soft-primary', 'icon' => 'bi-calendar-event'],
        'rilis'     => ['label' => 'Rilis Berita',    'badge' => 'badge-soft-info',    'icon' => 'bi-megaphone'],
        'publikasi' => ['label' => 'Publikasi',       'badge' => 'badge-soft-warning', 'icon' => 'bi-journal-text'],
        'galeri'    => ['label' => 'Galeri',          'badge' => 'badge-soft-success', 'icon' => 'bi-images'],
        'emagazine' => ['label' => 'e-Magazine',      'badge' => 'badge-soft-danger',  'icon' => 'bi-journal-richtext'],
        'karya'     => ['label' => 'Karya Anggota',   'badge' => 'badge-soft-secondary','icon' => 'bi-book'],
    ];

    $resolveImgUrl = function (?string $path): string {
        if (!$path) return 'http://localhost:8000/assets/gambar.png';
        if (strpos($path, 'http://') === 0 || strpos($path, 'https://') === 0) return $path;
        if (strpos($path, 'cms_uploads/') === 0) return '/' . ltrim($path, '/');
        return 'http://localhost:8000/' . ltrim($path, '/');
    };
?>

<div class="d-flex justify-content-between align-items-center mb-4 mt-2 flex-wrap gap-3">
    <div>
        <h3 class="fw-bold mb-1" style="color: var(--inkindo-blue);">Admin CMS — Berita &amp; Informasi</h3>
        <p class="text-muted small mb-0">Kelola seluruh konten halaman <strong>Berita &amp; Informasi</strong> serta pilih berita pilihan yang tampil pada <strong>Halaman Utama (Beranda WebProfile)</strong>.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="http://localhost:8000/berita.html" target="_blank" class="btn btn-outline-primary rounded-pill px-3 fw-semibold">
            <i class="bi bi-box-arrow-up-right me-1"></i> Lihat Halaman Berita
        </a>
        <a href="/cms/create<?= $filterTipe ? '?tipe=' . esc($filterTipe) : '' ?>" class="btn btn-primary rounded-pill px-4 fw-semibold shadow-sm" style="background-color: var(--inkindo-blue); border: none;">
            <i class="bi bi-plus-lg me-1"></i> Tambah Konten Baru
        </a>
    </div>
</div>

<?php if(session()->getFlashdata('pesan')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle-fill me-1"></i> <?= session()->getFlashdata('pesan'); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<!-- Pengaturan Tampilan Berita di Halaman Utama (index.html) -->
<div class="card border-0 shadow-sm mb-4" style="border-radius: 16px; background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%); border-left: 5px solid var(--inkindo-blue) !important;">
    <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
            <div>
                <h5 class="fw-bold mb-1 text-dark">
                    <i class="bi bi-layout-wtf text-primary me-2"></i>Pengaturan Berita Pilihan di Halaman Utama (Beranda)
                </h5>
                <p class="text-muted small mb-0">Pilih 3 konten dari Berita &amp; Informasi yang akan ditampilkan pada seksi <strong>Berita &amp; Informasi</strong> di halaman utama (`index.html`). Setiap kartu di beranda otomatis dapat diklik menuju halaman detailnya.</p>
            </div>
            <a href="http://localhost:8000/index.html#berita" target="_blank" class="btn btn-sm btn-light border rounded-pill px-3 fw-semibold text-primary">
                <i class="bi bi-eye me-1"></i> Pratinjau Beranda
            </a>
        </div>

        <form action="/cms/updateHomepageSlots" method="post">
            <div class="row g-3 align-items-end">
                <!-- Slot 1: Kiri Besar -->
                <div class="col-lg-4">
                    <div class="p-3 rounded-3 h-100" style="background: #ffffff; border: 1px solid #e2e8f0;">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="badge bg-primary rounded-pill px-3 py-1">Slot 1 • Utama (Kiri Besar)</span>
                            <?php if($slot1): ?>
                                <small class="text-success fw-semibold"><i class="bi bi-check-circle-fill"></i> Aktif</small>
                            <?php endif; ?>
                        </div>
                        <select name="slot_1" class="form-select form-select-sm mb-2">
                            <option value="0">-- Pilih Konten untuk Slot 1 --</option>
                            <?php foreach($allItems as $opt): ?>
                                <?php $tLabel = $tipeMap[$opt['tipe']]['label'] ?? strtoupper($opt['tipe']); ?>
                                <option value="<?= $opt['id']; ?>" <?= ((int)($opt['homepage_slot'] ?? 0) === 1) ? 'selected' : ''; ?>>
                                    [<?= esc($tLabel); ?>] <?= esc($opt['judul']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if($slot1): ?>
                            <div class="d-flex align-items-center gap-2 mt-2 pt-2 border-top">
                                <img src="<?= esc($resolveImgUrl($slot1['gambar'])); ?>" alt="" style="width: 48px; height: 36px; object-fit: cover; border-radius: 6px;">
                                <div class="small text-truncate">
                                    <div class="fw-bold text-dark text-truncate" style="max-width: 220px;"><?= esc($slot1['judul']); ?></div>
                                    <div class="text-muted" style="font-size: 11px;"><?= esc($slot1['tanggal_label']); ?> | <?= esc($slot1['kategori_label']); ?></div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Slot 2: Kanan Atas -->
                <div class="col-lg-4">
                    <div class="p-3 rounded-3 h-100" style="background: #ffffff; border: 1px solid #e2e8f0;">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="badge bg-info text-dark rounded-pill px-3 py-1">Slot 2 • Kanan Atas</span>
                            <?php if($slot2): ?>
                                <small class="text-success fw-semibold"><i class="bi bi-check-circle-fill"></i> Aktif</small>
                            <?php endif; ?>
                        </div>
                        <select name="slot_2" class="form-select form-select-sm mb-2">
                            <option value="0">-- Pilih Konten untuk Slot 2 --</option>
                            <?php foreach($allItems as $opt): ?>
                                <?php $tLabel = $tipeMap[$opt['tipe']]['label'] ?? strtoupper($opt['tipe']); ?>
                                <option value="<?= $opt['id']; ?>" <?= ((int)($opt['homepage_slot'] ?? 0) === 2) ? 'selected' : ''; ?>>
                                    [<?= esc($tLabel); ?>] <?= esc($opt['judul']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if($slot2): ?>
                            <div class="d-flex align-items-center gap-2 mt-2 pt-2 border-top">
                                <img src="<?= esc($resolveImgUrl($slot2['gambar'])); ?>" alt="" style="width: 48px; height: 36px; object-fit: cover; border-radius: 6px;">
                                <div class="small text-truncate">
                                    <div class="fw-bold text-dark text-truncate" style="max-width: 220px;"><?= esc($slot2['judul']); ?></div>
                                    <div class="text-muted" style="font-size: 11px;"><?= esc($slot2['tanggal_label']); ?> | <?= esc($slot2['kategori_label']); ?></div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Slot 3: Kanan Bawah -->
                <div class="col-lg-4">
                    <div class="p-3 rounded-3 h-100" style="background: #ffffff; border: 1px solid #e2e8f0;">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="badge bg-secondary rounded-pill px-3 py-1">Slot 3 • Kanan Bawah</span>
                            <?php if($slot3): ?>
                                <small class="text-success fw-semibold"><i class="bi bi-check-circle-fill"></i> Aktif</small>
                            <?php endif; ?>
                        </div>
                        <select name="slot_3" class="form-select form-select-sm mb-2">
                            <option value="0">-- Pilih Konten untuk Slot 3 --</option>
                            <?php foreach($allItems as $opt): ?>
                                <?php $tLabel = $tipeMap[$opt['tipe']]['label'] ?? strtoupper($opt['tipe']); ?>
                                <option value="<?= $opt['id']; ?>" <?= ((int)($opt['homepage_slot'] ?? 0) === 3) ? 'selected' : ''; ?>>
                                    [<?= esc($tLabel); ?>] <?= esc($opt['judul']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if($slot3): ?>
                            <div class="d-flex align-items-center gap-2 mt-2 pt-2 border-top">
                                <img src="<?= esc($resolveImgUrl($slot3['gambar'])); ?>" alt="" style="width: 48px; height: 36px; object-fit: cover; border-radius: 6px;">
                                <div class="small text-truncate">
                                    <div class="fw-bold text-dark text-truncate" style="max-width: 220px;"><?= esc($slot3['judul']); ?></div>
                                    <div class="text-muted" style="font-size: 11px;"><?= esc($slot3['tanggal_label']); ?> | <?= esc($slot3['kategori_label']); ?></div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="mt-3 text-end">
                <button type="submit" class="btn btn-primary rounded-pill px-4 fw-semibold" style="background-color: var(--inkindo-blue); border: none;">
                    <i class="bi bi-check2-circle me-1"></i> Simpan Tampilan Beranda
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Filter Tab Kategori Berita & Informasi -->
<div class="d-flex flex-wrap gap-2 mb-3">
    <a href="/cms" class="btn btn-sm rounded-pill px-3 fw-semibold <?= $filterTipe === '' ? 'btn-primary' : 'btn-light border text-muted' ?>">
        Semua Kategori <span class="badge bg-white text-dark ms-1"><?= $counts['all']; ?></span>
    </a>
    <?php foreach($tipeMap as $key => $meta): ?>
        <a href="/cms?tipe=<?= $key; ?>" class="btn btn-sm rounded-pill px-3 fw-semibold <?= $filterTipe === $key ? 'btn-primary' : 'btn-light border text-muted' ?>">
            <i class="bi <?= $meta['icon']; ?> me-1"></i> <?= $meta['label']; ?>
            <span class="badge bg-white text-dark ms-1"><?= $counts[$key] ?? 0; ?></span>
        </a>
    <?php endforeach; ?>
</div>

<div class="table-custom-wrap">
    <div class="table-responsive" style="overflow-x: visible;">
        <table class="table-custom text-center align-middle">
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th width="10%">Gambar</th>
                    <th class="text-start" width="32%">Judul &amp; Ringkasan</th>
                    <th width="14%">Tab / Kategori</th>
                    <th width="14%">Tanggal / Info</th>
                    <th width="12%">Tampil Beranda</th>
                    <th width="13%">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $i = 1; foreach($items as $row): ?>
                <?php
                    $tMeta = $tipeMap[$row['tipe']] ?? ['label' => ucfirst($row['tipe']), 'badge' => 'badge-soft-secondary', 'icon' => 'bi-file-text'];
                    $cleanDesc = preg_replace('/\s+/', ' ', trim((string)($row['ringkasan'] ?? '')));
                    $shortDesc = mb_strimwidth($cleanDesc, 0, 75, '...');
                    $slotNum = (int)($row['homepage_slot'] ?? 0);
                ?>
                <tr>
                    <td class="text-muted"><?= $i++; ?></td>
                    <td>
                        <img src="<?= esc($resolveImgUrl($row['gambar'])); ?>"
                             alt="<?= esc($row['judul']); ?>"
                             style="width: 72px; height: 50px; object-fit: cover; border-radius: 8px; border: 1px solid #e2e8f0;">
                    </td>
                    <td class="text-start">
                        <div class="fw-bold text-dark"><?= esc($row['judul']); ?></div>
                        <?php if($shortDesc !== ''): ?>
                            <div class="small text-muted text-truncate" style="max-width: 340px;"><?= esc($shortDesc); ?></div>
                        <?php endif; ?>
                        <?php if(!empty($row['file_pdf'])): ?>
                            <span class="badge bg-light text-danger border mt-1"><i class="bi bi-file-earmark-pdf-fill me-1"></i>PDF Tersedia</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div class="fw-semibold text-primary small"><i class="bi <?= $tMeta['icon']; ?> me-1"></i><?= esc($tMeta['label']); ?></div>
                        <span class="badge bg-light text-secondary border mt-1"><?= esc($row['kategori_label']); ?></span>
                    </td>
                    <td class="small">
                        <div class="fw-semibold text-dark"><?= esc($row['tanggal_label'] ?: '-'); ?></div>
                        <?php if(!empty($row['badge'])): ?>
                            <div class="text-muted" style="font-size: 12px;">Badge: <?= esc($row['badge']); ?></div>
                        <?php endif; ?>
                        <?php if(!empty($row['penulis'])): ?>
                            <div class="text-muted text-truncate" style="font-size: 11px; max-width: 150px; margin: 0 auto;"><?= esc($row['penulis']); ?></div>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if($slotNum === 1): ?>
                            <span class="badge rounded-pill bg-primary px-3 py-2">Slot 1 (Utama)</span>
                        <?php elseif($slotNum === 2): ?>
                            <span class="badge rounded-pill bg-info text-dark px-3 py-2">Slot 2 (Kanan Atas)</span>
                        <?php elseif($slotNum === 3): ?>
                            <span class="badge rounded-pill bg-secondary px-3 py-2">Slot 3 (Kanan Bawah)</span>
                        <?php else: ?>
                            <span class="text-muted small">-</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div class="d-flex justify-content-center gap-1 flex-wrap">
                            <a href="/cms/edit/<?= $row['id']; ?>" class="btn btn-sm btn-warning text-white rounded-pill px-3 fw-semibold">
                                Edit
                            </a>
                            <a href="/cms/delete/<?= $row['id']; ?>" class="btn btn-sm btn-danger rounded-pill px-3 fw-semibold" onclick="return confirm('Hapus konten ini dari Berita & Informasi?');">
                                Hapus
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?= $this->endSection() ?>

