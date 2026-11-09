<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<!-- Cropper.js CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.css">

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
        if (!$path) return '/cms/webAsset/assets/gambar.png';
        if (strpos($path, 'http://') === 0 || strpos($path, 'https://') === 0) return $path;
        if (strpos($path, 'cms_uploads/') === 0) return '/' . ltrim($path, '/');
        return '/cms/webAsset/' . ltrim($path, '/');
    };

    $groupedForDropdown = [];
    foreach ($allItems as $row) {
        $t = $row['tipe'] ?? 'rilis';
        $groupedForDropdown[$t][] = $row;
    }

    $renderCustomDropdown = function($slotNum, $slotItem, $name, ?string $prioritizeType = null, string $emptyLabel = '-- Pilih Konten (Kosongkan Slot) --') use ($groupedForDropdown, $tipeMap) {
        $currentId = (int)($slotItem['id'] ?? 0);
        $currentLabel = $emptyLabel;
        $currentFullTitle = $currentLabel;
        if ($slotItem) {
            $tgl = !empty($slotItem['tanggal_label']) ? '[' . $slotItem['tanggal_label'] . '] ' : '';
            $currentFullTitle = $tgl . $slotItem['judul'];
            $currentLabel = $tgl . mb_strimwidth($slotItem['judul'], 0, 42, '...');
        }

        $orderedTipeMap = $tipeMap;
        if ($prioritizeType && isset($orderedTipeMap[$prioritizeType])) {
            $prioritized = [$prioritizeType => $orderedTipeMap[$prioritizeType]];
            unset($orderedTipeMap[$prioritizeType]);
            $orderedTipeMap = $prioritized + $orderedTipeMap;
        }

        ob_start();
        ?>
        <div class="custom-slot-dropdown position-relative mb-2" data-slot="<?= esc((string)$slotNum); ?>">
            <input type="hidden" name="<?= esc($name); ?>" id="input_<?= esc($name); ?>" value="<?= $currentId; ?>">
            <button type="button" class="btn btn-sm w-100 bg-white border text-start d-flex justify-content-between align-items-center py-2 px-3 rounded-2 shadow-none slot-trigger-btn"
                    data-bs-toggle="dropdown" data-bs-display="static" aria-expanded="false"
                    title="<?= esc($currentFullTitle); ?>" style="border-color: #cbd5e1 !important;">
                <span class="text-truncate slot-display-label small fw-semibold text-dark" style="max-width: calc(100% - 20px);">
                    <?= esc($currentLabel); ?>
                </span>
                <i class="bi bi-chevron-down text-muted small ms-1"></i>
            </button>
            <div class="dropdown-menu dropdown-menu-start w-100 p-2 shadow-lg border rounded-3 mt-1"
                 style="top: 100% !important; left: 0 !important; right: 0 !important; width: 100% !important; max-height: 290px; overflow-y: auto; z-index: 1055; border-color: #cbd5e1 !important; box-shadow: 0 12px 28px -4px rgba(15, 23, 42, 0.16), 0 6px 12px -2px rgba(15, 23, 42, 0.08) !important;">
                <div class="p-1 mb-2 sticky-top bg-white border-bottom pb-2" style="top: -8px; margin-top: -8px; padding-top: 8px !important; z-index: 5;">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" class="form-control border-start-0 slot-filter-input" placeholder="Cari judul atau tanggal...">
                    </div>
                </div>
                <div class="slot-items-container">
                    <a class="dropdown-item rounded-2 py-1.5 px-2 small fw-semibold text-danger slot-option-item <?= $currentId === 0 ? 'active bg-danger text-white' : ''; ?>"
                       href="javascript:void(0)" data-id="0" data-label="<?= esc($emptyLabel); ?>" title="<?= esc($emptyLabel); ?>">
                        <i class="bi bi-x-circle me-1"></i> <?= esc($emptyLabel); ?>
                    </a>
                    <?php foreach ($orderedTipeMap as $typeKey => $meta): ?>
                        <?php $list = $groupedForDropdown[$typeKey] ?? []; ?>
                        <?php if (empty($list)) continue; ?>
                        <div class="slot-group-block mb-1">
                            <div class="slot-group-header px-2 py-1 small fw-bold text-uppercase text-muted my-1 rounded"
                                 style="font-size: 10.5px; letter-spacing: 0.5px; background: #f1f5f9; border: 1px solid #e2e8f0;">
                                <i class="bi <?= $meta['icon']; ?> me-1 text-primary"></i> <?= esc($meta['label']); ?> (<?= count($list); ?>)
                            </div>
                            <?php foreach ($list as $opt): ?>
                                <?php
                                    $optId = (int)$opt['id'];
                                    $isSelected = ($optId === $currentId);
                                    $tgl = !empty($opt['tanggal_label']) ? '[' . $opt['tanggal_label'] . '] ' : '';
                                    $fullTitle = $tgl . $opt['judul'];
                                    $displayLabel = $tgl . mb_strimwidth($opt['judul'], 0, 42, '...');
                                ?>
                                <a class="dropdown-item rounded-2 py-1.5 px-2 small slot-option-item <?= $isSelected ? 'active bg-primary text-white' : ''; ?>"
                                   href="javascript:void(0)"
                                   data-id="<?= $optId; ?>"
                                   data-label="<?= esc($displayLabel); ?>"
                                   data-search="<?= esc(strtolower($fullTitle)); ?>"
                                   title="<?= esc($fullTitle); ?>">
                                    <div class="fw-semibold text-truncate" style="font-size: 12px;"><?= esc($fullTitle); ?></div>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endforeach; ?>
                    <div class="slot-no-results text-center py-3 text-muted small" style="display: none;">
                        <i class="bi bi-search text-secondary d-block mb-1" style="font-size: 1.2rem;"></i>
                        Tidak ada konten yang cocok
                    </div>
                </div>
            </div>
        </div>
        <?php
        return ob_get_clean();
    };

    $pubSlots = $pubSlots ?? array_fill(1, 8, null);
    $usedPubIds = [];
    foreach ($pubSlots as $ps) {
        if ($ps && isset($ps['id'])) {
            $usedPubIds[(int)$ps['id']] = true;
        }
    }
    $pubFallbackPool = [];
    foreach (($groupedForDropdown['publikasi'] ?? []) as $pubRow) {
        if (!isset($usedPubIds[(int)$pubRow['id']])) {
            $pubFallbackPool[] = $pubRow;
        }
    }
    $effectivePubSlots = [];
    $fbIdx = 0;
    for ($s = 1; $s <= 8; $s++) {
        if (!empty($pubSlots[$s])) {
            $effectivePubSlots[$s] = ['item' => $pubSlots[$s], 'is_manual' => true];
        } elseif (isset($pubFallbackPool[$fbIdx])) {
            $effectivePubSlots[$s] = ['item' => $pubFallbackPool[$fbIdx++], 'is_manual' => false];
        } else {
            $effectivePubSlots[$s] = ['item' => null, 'is_manual' => false];
        }
    }
?>

<div class="d-flex justify-content-between align-items-center mb-4 mt-2 flex-wrap gap-3">
    <div>
        <h3 class="fw-bold mb-1" style="color: var(--inkindo-blue);">Admin CMS — Halaman Utama (Beranda)</h3>
        <p class="text-muted small mb-0">Kelola <strong>Teks Bergerak</strong>, <strong>Banner Utama (Hero Carousel)</strong>, <strong>Gambar Background Utama</strong>, <strong>3 Berita Pilihan</strong>, serta <strong>8 Publikasi Beranda (Grid 4×2)</strong> di WebProfile (`index.html`).</p>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <a href="http://localhost:8000/index.html" target="_blank" class="btn btn-outline-primary rounded-pill px-3 fw-semibold">
            <i class="bi bi-box-arrow-up-right me-1"></i> Pratinjau Halaman Utama
        </a>
    </div>
</div>

<?php if(session()->getFlashdata('pesan')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle-fill me-1"></i> <?= session()->getFlashdata('pesan'); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if(session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-1"></i> <?= session()->getFlashdata('error'); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<!-- ================= SEKSI 1: TEKS BERGERAK DI ATAS BANNER UTAMA ================= -->
<div class="card border-0 shadow-sm mb-4" style="border-radius: 16px; background: #ffffff; border-left: 5px solid var(--inkindo-blue) !important;">
    <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3 pb-2 border-bottom">
            <div>
                <h5 class="fw-bold mb-1 text-dark">
                    <i class="bi bi-input-cursor-text text-primary me-2"></i>1. Pengaturan Teks Bergerak di Atas Banner Utama (Animasi Mengetik)
                </h5>
                <p class="text-muted small mb-0">
                    Kelola daftar kalimat sambutan interaktif yang muncul bergantian dengan efek mengetik tepat di atas kotak Banner Utama pada beranda.
                </p>
            </div>
            <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 fw-semibold" style="background-color: var(--inkindo-blue); border: none;" onclick="openPhraseModal()">
                <i class="bi bi-plus-circle me-1"></i> Tambah Teks Bergerak
            </button>
        </div>

        <!-- Pratinjau Live Simulasi Animasi Mengetik -->
        <div class="p-3 rounded-3 mb-3 d-flex align-items-center justify-content-between flex-wrap gap-2" style="background: linear-gradient(135deg, #1e3a5f 0%, #0f172a 100%);">
            <div style="border-bottom: 1.5px solid rgba(255,255,255,0.65); padding-bottom: 6px; min-width: 280px; flex: 1;">
                <span class="badge bg-white bg-opacity-10 text-white-50 mb-1" style="font-size: 10px;">PRATINJAU EFEK MENGETIK DI ATAS BANNER</span>
                <div class="text-white fw-bold" style="font-size: 22px; letter-spacing: -0.3px; text-shadow: 0 2px 8px rgba(0,0,0,0.5);">
                    <span id="cms_live_typing_preview">Selamat Datang</span><span class="text-info ms-1" style="animation: cmsBlink 0.9s infinite;">|</span>
                </div>
            </div>
            <span class="badge bg-light text-dark rounded-pill px-3 py-1.5">
                Total: <?= count($phrases ?? []); ?> Kalimat
            </span>
        </div>

        <div class="row g-2">
            <?php foreach (($phrases ?? []) as $idx => $p): ?>
                <?php
                    $pActive = (int)($p['is_active'] ?? 1) === 1;
                    $pJson = htmlspecialchars(json_encode([
                        'id'        => (int)$p['id'],
                        'teks'      => (string)($p['teks'] ?? ''),
                        'urutan'    => (int)($p['urutan'] ?? ($idx + 1)),
                        'is_active' => $pActive ? 1 : 0,
                    ]), ENT_QUOTES, 'UTF-8');
                ?>
                <div class="col-md-6 col-lg-4">
                    <div class="p-2.5 px-3 rounded-3 border d-flex align-items-center justify-content-between gap-2 h-100" style="background: #f8fafc; border-color: #e2e8f0 !important; <?= !$pActive ? 'opacity: 0.6;' : ''; ?>">
                        <div class="d-flex align-items-center gap-2 text-truncate">
                            <span class="badge bg-primary rounded-pill">#<?= (int)($p['urutan'] ?? ($idx + 1)); ?></span>
                            <div class="text-truncate">
                                <div class="fw-bold text-dark small text-truncate" title="<?= esc($p['teks']); ?>"><?= esc($p['teks']); ?></div>
                                <small class="<?= $pActive ? 'text-success' : 'text-muted'; ?>" style="font-size: 11px;">
                                    <i class="bi <?= $pActive ? 'bi-check-circle-fill' : 'bi-eye-slash'; ?>"></i> <?= $pActive ? 'Aktif' : 'Nonaktif'; ?>
                                </small>
                            </div>
                        </div>
                        <div class="d-flex gap-1 flex-shrink-0">
                            <button type="button" class="btn btn-sm btn-warning text-white rounded-pill px-2.5 py-1" onclick='openPhraseModal(<?= $pJson; ?>)' title="Edit Teks">
                                <i class="bi bi-pencil-square"></i>
                            </button>
                            <a href="/cms/phrase/toggle/<?= (int)$p['id']; ?>" class="btn btn-sm btn-outline-secondary rounded-pill px-2 py-1" title="<?= $pActive ? 'Nonaktifkan' : 'Aktifkan'; ?>">
                                <i class="bi <?= $pActive ? 'bi-eye-slash' : 'bi-eye'; ?>"></i>
                            </a>
                            <a href="/cms/phrase/delete/<?= (int)$p['id']; ?>" class="btn btn-sm btn-outline-danger rounded-pill px-2 py-1" onclick="return confirm('Hapus teks bergerak ini?');" title="Hapus Teks">
                                <i class="bi bi-trash"></i>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- ================= SEKSI 2: BANNER UTAMA (HERO CAROUSEL) ================= -->
<div class="card border-0 shadow-sm mb-4" style="border-radius: 16px; background: #ffffff; border-left: 5px solid var(--inkindo-blue) !important;">
    <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3 pb-2 border-bottom">
            <div>
                <h5 class="fw-bold mb-1 text-dark">
                    <i class="bi bi-images text-primary me-2"></i>2. Pengaturan Banner Utama (Hero Carousel)
                </h5>
                <p class="text-muted small mb-0">
                    Kelola slide banner yang tampil pada kotak utama di bagian atas beranda (`index.html`). Anda dapat <strong>mengunggah gambar &amp; menulis teks sendiri</strong> (dilengkapi fitur <strong>Crop / Penyesuaian Posisi Gambar</strong>) atau <strong>mengambil dari konten Berita &amp; Informasi</strong> yang sudah ada.
                </p>
            </div>
            <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 fw-semibold" style="background-color: var(--inkindo-blue); border: none;" onclick="openBannerModal()">
                <i class="bi bi-plus-circle me-1"></i> Tambah Slide Banner
            </button>
        </div>

        <div class="row g-3">
            <?php foreach ($banners as $idx => $b): ?>
                <?php
                    $bImgUrl = $resolveImgUrl($b['gambar']);
                    $isActive = (int)($b['is_active'] ?? 1) === 1;
                    $isFromBerita = ($b['mode_sumber'] ?? 'custom') === 'berita';
                    $bannerJson = htmlspecialchars(json_encode([
                        'id'          => (int)$b['id'],
                        'mode_sumber' => $b['mode_sumber'] ?? 'custom',
                        'berita_id'   => (int)($b['berita_id'] ?? 0),
                        'subjudul'    => (string)($b['subjudul'] ?? ''),
                        'judul'       => (string)($b['judul'] ?? ''),
                        'gambar'      => (string)($b['gambar'] ?? ''),
                        'gambar_url'  => $bImgUrl,
                        'target_url'  => (string)($b['target_url'] ?? '#berita'),
                        'urutan'      => (int)($b['urutan'] ?? ($idx + 1)),
                        'is_active'   => $isActive ? 1 : 0,
                    ]), ENT_QUOTES, 'UTF-8');
                ?>
                <div class="col-md-6">
                    <div class="p-3 rounded-3 h-100 d-flex flex-column justify-content-between" style="background: #f8fafc; border: 1px solid #e2e8f0; <?= !$isActive ? 'opacity: 0.65;' : ''; ?>">
                        <div>
                            <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-1">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-dark rounded-pill px-2.5 py-1">Slide #<?= (int)($b['urutan'] ?? ($idx + 1)); ?></span>
                                    <?php if ($isFromBerita): ?>
                                        <span class="badge bg-info text-dark rounded-pill px-2.5 py-1"><i class="bi bi-newspaper me-1"></i>Dari Berita &amp; Informasi</span>
                                    <?php else: ?>
                                        <span class="badge bg-primary rounded-pill px-2.5 py-1"><i class="bi bi-upload me-1"></i>Upload &amp; Teks Sendiri</span>
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <?php if ($isActive): ?>
                                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-1">
                                            <i class="bi bi-check-circle-fill me-1"></i>Aktif Tampil
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary-subtle text-secondary border rounded-pill px-2 py-1">
                                            <i class="bi bi-eye-slash me-1"></i>Nonaktif
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Pratinjau Mini Persis Tampilan Banner Utama di WebProfile -->
                            <div class="hero-mini-preview position-relative rounded-3 overflow-hidden mb-2 shadow-sm" style="aspect-ratio: 740 / 280; background: #0f172a;">
                                <img src="<?= esc($bImgUrl); ?>" alt="<?= esc($b['judul']); ?>" style="width: 100%; height: 100%; object-fit: cover; display: block;">
                                <div class="position-absolute top-0 start-0 bottom-0 d-flex flex-column justify-content-center px-4 py-3" style="max-width: 72%; z-index: 2; pointer-events: none;">
                                    <?php if (!empty($b['subjudul'])): ?>
                                        <div class="text-white fw-medium mb-1" style="font-size: 12px; text-shadow: 0 2px 8px rgba(0,0,0,0.85), 0 1px 3px rgba(0,0,0,0.9);">
                                            <?= esc($b['subjudul']); ?>
                                        </div>
                                    <?php endif; ?>
                                    <div class="text-white fw-bold" style="font-size: 17px; line-height: 1.25; text-shadow: 0 2px 10px rgba(0,0,0,0.9), 0 1px 4px rgba(0,0,0,0.9);">
                                        <?= esc($b['judul']); ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center pt-2 border-top mt-1 flex-wrap gap-2">
                            <small class="text-muted text-truncate" style="max-width: 220px;" title="Link Tujuan: <?= esc($b['target_url']); ?>">
                                <i class="bi bi-link-45deg"></i> <?= esc($b['target_url'] ?: '#berita'); ?>
                            </small>
                            <div class="d-flex gap-1">
                                <button type="button" class="btn btn-sm btn-warning text-white rounded-pill px-3 fw-semibold" onclick='openBannerModal(<?= $bannerJson; ?>)'>
                                    <i class="bi bi-crop me-1"></i> Edit &amp; Sesuaikan
                                </button>
                                <a href="/cms/banner/toggle/<?= (int)$b['id']; ?>" class="btn btn-sm btn-outline-secondary rounded-pill px-2.5" title="<?= $isActive ? 'Sembunyikan Slide' : 'Tampilkan Slide'; ?>">
                                    <i class="bi <?= $isActive ? 'bi-eye-slash' : 'bi-eye'; ?>"></i>
                                </a>
                                <a href="/cms/banner/delete/<?= (int)$b['id']; ?>" class="btn btn-sm btn-outline-danger rounded-pill px-2.5" onclick="return confirm('Hapus slide banner utama ini?');" title="Hapus Slide">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- ================= SEKSI 3: GAMBAR BACKGROUND UTAMA WEB (HERO BACKGROUND CRUD) ================= -->
<div class="card border-0 shadow-sm mb-4" style="border-radius: 16px; background: #ffffff; border-left: 5px solid var(--inkindo-blue) !important;">
    <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3 pb-2 border-bottom">
            <div>
                <h5 class="fw-bold mb-1 text-dark">
                    <i class="bi bi-aspect-ratio text-primary me-2"></i>3. Pengaturan Gambar Background Utama Beranda (Hero Background)
                </h5>
                <p class="text-muted small mb-0">
                    Kelola daftar gambar latar belakang penuh (<em>full-screen background</em>) di halaman utama WebProfile yang otomatis berganti secara berkala di belakang Banner Utama.
                </p>
            </div>
            <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 fw-semibold" style="background-color: var(--inkindo-blue); border: none;" onclick="openBgModal()">
                <i class="bi bi-plus-circle me-1"></i> Tambah Gambar Background
            </button>
        </div>

        <div class="row g-3">
            <?php foreach (($backgrounds ?? []) as $idx => $bg): ?>
                <?php
                    $bgImgUrl = $resolveImgUrl($bg['gambar']);
                    $bgActive = (int)($bg['is_active'] ?? 1) === 1;
                    $bgJson = htmlspecialchars(json_encode([
                        'id'         => (int)$bg['id'],
                        'judul'      => (string)($bg['judul'] ?? ''),
                        'gambar'     => (string)($bg['gambar'] ?? ''),
                        'gambar_url' => $bgImgUrl,
                        'urutan'     => (int)($bg['urutan'] ?? ($idx + 1)),
                        'is_active'  => $bgActive ? 1 : 0,
                    ]), ENT_QUOTES, 'UTF-8');
                ?>
                <div class="col-md-6 col-lg-4">
                    <div class="p-3 rounded-3 h-100 d-flex flex-column justify-content-between" style="background: #f8fafc; border: 1px solid #e2e8f0; <?= !$bgActive ? 'opacity: 0.65;' : ''; ?>">
                        <div>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="badge bg-dark rounded-pill px-2.5 py-1">Background #<?= (int)($bg['urutan'] ?? ($idx + 1)); ?></span>
                                <?php if ($bgActive): ?>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-1">
                                        <i class="bi bi-check-circle-fill me-1"></i>Aktif
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-secondary-subtle text-secondary border rounded-pill px-2 py-1">
                                        <i class="bi bi-eye-slash me-1"></i>Nonaktif
                                    </span>
                                <?php endif; ?>
                            </div>

                            <div class="position-relative rounded-3 overflow-hidden mb-2 shadow-sm" style="aspect-ratio: 16 / 9; background: #0f172a;">
                                <img src="<?= esc($bgImgUrl); ?>" alt="<?= esc($bg['judul']); ?>" style="width: 100%; height: 100%; object-fit: cover; display: block;">
                                <div class="position-absolute inset-0" style="inset: 0; background: linear-gradient(180deg, rgba(15,23,42,0.35) 0%, rgba(15,23,42,0.65) 100%);"></div>
                                <div class="position-absolute bottom-0 start-0 end-0 p-2 px-3 text-white">
                                    <div class="fw-bold small text-truncate"><?= esc($bg['judul']); ?></div>
                                    <div class="text-white-50" style="font-size: 10.5px;"><?= esc($bg['gambar']); ?></div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-1 pt-2 border-top mt-1">
                            <button type="button" class="btn btn-sm btn-warning text-white rounded-pill px-3 fw-semibold" onclick='openBgModal(<?= $bgJson; ?>)'>
                                <i class="bi bi-crop me-1"></i> Edit &amp; Crop
                            </button>
                            <a href="/cms/background/toggle/<?= (int)$bg['id']; ?>" class="btn btn-sm btn-outline-secondary rounded-pill px-2.5" title="<?= $bgActive ? 'Nonaktifkan' : 'Aktifkan'; ?>">
                                <i class="bi <?= $bgActive ? 'bi-eye-slash' : 'bi-eye'; ?>"></i>
                            </a>
                            <a href="/cms/background/delete/<?= (int)$bg['id']; ?>" class="btn btn-sm btn-outline-danger rounded-pill px-2.5" onclick="return confirm('Hapus gambar background utama ini?');" title="Hapus Background">
                                <i class="bi bi-trash"></i>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- ================= SEKSI 4: 3 BERITA PILIHAN DI HALAMAN UTAMA ================= -->
<div class="card border-0 shadow-sm mb-4" style="border-radius: 16px; background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%); border-left: 5px solid var(--inkindo-blue) !important;">
    <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3 pb-2 border-bottom">
            <div>
                <h5 class="fw-bold mb-1 text-dark">
                    <i class="bi bi-layout-wtf text-primary me-2"></i>4. Pengaturan Berita Pilihan di Halaman Utama (3 Konten Utama)
                </h5>
                <p class="text-muted small mb-0">Pilih 3 konten dari Berita &amp; Informasi yang akan ditampilkan pada seksi <strong>Berita &amp; Informasi</strong> di halaman utama (`index.html`). Setiap kartu di beranda otomatis dapat diklik menuju halaman detailnya.</p>
            </div>
            <a href="http://localhost:8000/index.html#berita" target="_blank" class="btn btn-sm btn-light border rounded-pill px-3 fw-semibold text-primary">
                <i class="bi bi-eye me-1"></i> Pratinjau Seksi Berita Beranda
            </a>
        </div>

        <form action="/cms/updateHomepageSlots" method="post">
            <div class="row g-3 align-items-stretch">
                <!-- Slot 1: Kiri Besar -->
                <div class="col-lg-4">
                    <div class="p-3 rounded-3 h-100" style="background: #ffffff; border: 1px solid #e2e8f0;">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="badge bg-primary rounded-pill px-3 py-1">Slot 1 • Utama (Kiri Besar)</span>
                            <?php if($slot1): ?>
                                <small class="text-success fw-semibold"><i class="bi bi-check-circle-fill"></i> Aktif</small>
                            <?php endif; ?>
                        </div>
                        <?= $renderCustomDropdown(1, $slot1, 'slot_1'); ?>
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
                        <?= $renderCustomDropdown(2, $slot2, 'slot_2'); ?>
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
                        <?= $renderCustomDropdown(3, $slot3, 'slot_3'); ?>
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
                    <i class="bi bi-check2-circle me-1"></i> Simpan Berita Pilihan Beranda
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ================= SEKSI 5: 8 PUBLIKASI DI HALAMAN UTAMA (GRID 4x2) ================= -->
<div class="card border-0 shadow-sm mb-4" style="border-radius: 16px; background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%); border-left: 5px solid var(--inkindo-blue) !important;">
    <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3 pb-2 border-bottom">
            <div>
                <h5 class="fw-bold mb-1 text-dark">
                    <i class="bi bi-grid-3x2-gap-fill text-primary me-2"></i>5. Pengaturan Publikasi di Halaman Utama (Layout 4×2 — 8 Konten)
                </h5>
                <p class="text-muted small mb-0">
                    Pilih hingga 8 konten yang akan ditampilkan pada seksi <strong>Publikasi</strong> (4 ke samping × 2 ke bawah) tepat di bawah Berita &amp; Informasi di halaman utama (`index.html`). Slot yang dibiarkan kosong akan otomatis terisi dengan Publikasi terbaru.
                </p>
            </div>
            <a href="http://localhost:8000/index.html#publikasi-beranda" target="_blank" class="btn btn-sm btn-light border rounded-pill px-3 fw-semibold text-primary">
                <i class="bi bi-eye me-1"></i> Pratinjau Seksi Publikasi Beranda
            </a>
        </div>

        <form action="/cms/updatePublikasiSlots" method="post">
            <div class="row g-3 align-items-stretch">
                <?php for ($p = 1; $p <= 8; $p++): ?>
                    <?php
                        $manualItem = $pubSlots[$p] ?? null;
                        $effData = $effectivePubSlots[$p] ?? ['item' => null, 'is_manual' => false];
                        $previewItem = $effData['item'];
                        $isManual = $effData['is_manual'];
                        $baris = ($p <= 4) ? 1 : 2;
                        $kolom = (($p - 1) % 4) + 1;
                    ?>
                    <div class="col-md-6 col-xl-3">
                        <div class="p-3 rounded-3 h-100 d-flex flex-column justify-content-between" style="background: #ffffff; border: 1px solid #e2e8f0;">
                            <div>
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="badge rounded-pill px-2.5 py-1" style="background: <?= $baris === 1 ? 'var(--inkindo-blue)' : '#0284c7'; ?>; color: #fff; font-size: 11px;">
                                        Slot <?= $p; ?> • Baris <?= $baris; ?> Kolom <?= $kolom; ?>
                                    </span>
                                    <?php if ($isManual): ?>
                                        <small class="text-success fw-semibold" style="font-size: 11px;"><i class="bi bi-pin-angle-fill"></i> Dipilih</small>
                                    <?php else: ?>
                                        <small class="text-muted fw-semibold" style="font-size: 11px;"><i class="bi bi-magic"></i> Otomatis</small>
                                    <?php endif; ?>
                                </div>

                                <?= $renderCustomDropdown('pub_' . $p, $manualItem, 'pub_slot_' . $p, 'publikasi', '-- Otomatis (Publikasi Terbaru) --'); ?>
                            </div>

                            <?php if ($previewItem): ?>
                                <div class="d-flex align-items-center gap-2 mt-2 pt-2 border-top">
                                    <img src="<?= esc($resolveImgUrl($previewItem['gambar'])); ?>" alt="" style="width: 48px; height: 36px; object-fit: cover; border-radius: 6px; flex-shrink: 0;">
                                    <div class="small text-truncate" style="min-width: 0;">
                                        <div class="fw-bold text-dark text-truncate" title="<?= esc($previewItem['judul']); ?>"><?= esc($previewItem['judul']); ?></div>
                                        <div class="text-muted text-truncate" style="font-size: 10.5px;">
                                            <?= esc($previewItem['tanggal_label']); ?> | <?= esc($previewItem['kategori_label']); ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endfor; ?>
            </div>

            <div class="mt-3 text-end">
                <button type="submit" class="btn btn-primary rounded-pill px-4 fw-semibold" style="background-color: var(--inkindo-blue); border: none;">
                    <i class="bi bi-check2-circle me-1"></i> Simpan Publikasi Beranda (4×2)
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ================= MODAL 1: TAMBAH / EDIT TEKS BERGERAK ================= -->
<div class="modal fade" id="phraseModal" tabindex="-1" aria-labelledby="phraseModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="/cms/phrase/save" method="post" class="modal-content border-0 shadow-lg modal-scrollable-form" style="border-radius: 16px;">
            <div class="modal-header border-bottom px-4 py-3" style="background: #f8fafc;">
                <div>
                    <h5 class="modal-title fw-bold text-dark mb-0" id="phraseModalLabel">
                        <i class="bi bi-input-cursor-text text-primary me-2"></i>Tambah Teks Bergerak
                    </h5>
                    <small class="text-muted">Kalimat ini akan ditampilkan dengan efek mengetik di atas Banner Utama.</small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <input type="hidden" name="phrase_id" id="pm_phrase_id" value="0">
                <div class="mb-3">
                    <label class="form-label fw-semibold small text-dark mb-1">Kalimat / Teks Bergerak <span class="text-danger">*</span></label>
                    <input type="text" name="teks" id="pm_teks" class="form-control" placeholder="Contoh: Solusi Jasa Konsultansi Terpercaya" required>
                </div>
                <div class="row g-3">
                    <div class="col-6">
                        <label class="form-label fw-semibold small text-dark mb-1">Urutan Tampil</label>
                        <input type="number" name="urutan" id="pm_urutan" class="form-control" min="1" max="50" value="1">
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-semibold small text-dark mb-1">Status</label>
                        <select name="is_active" id="pm_is_active" class="form-select">
                            <option value="1">Aktif Tampil</option>
                            <option value="0">Nonaktif</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top px-4 py-3" style="background: #f8fafc;">
                <button type="button" class="btn btn-light border rounded-pill px-4 fw-semibold" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary rounded-pill px-4 fw-semibold" style="background-color: var(--inkindo-blue); border: none;">
                    <i class="bi bi-check2-circle me-1"></i> Simpan Teks Bergerak
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ================= MODAL 2: TAMBAH / EDIT BANNER UTAMA + CROP GAMBAR (SCROLLABLE PENUH) ================= -->
<div class="modal fade" id="bannerModal" tabindex="-1" aria-labelledby="bannerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <form action="/cms/banner/save" method="post" enctype="multipart/form-data" id="bannerForm" class="modal-content border-0 shadow-lg modal-scrollable-form" style="border-radius: 16px;">
            <div class="modal-header border-bottom px-4 py-3" style="background: #f8fafc;">
                <div>
                    <h5 class="modal-title fw-bold text-dark mb-0" id="bannerModalLabel">
                        <i class="bi bi-sliders text-primary me-2"></i>Pengaturan Slide Banner Utama
                    </h5>
                    <small class="text-muted">Pilih metode pengisian banner dan sesuaikan posisi/crop gambar agar tampil proporsional di Beranda.</small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body p-4">
                <input type="hidden" name="banner_id" id="bm_banner_id" value="0">
                <input type="hidden" name="existing_gambar" id="bm_existing_gambar" value="assets/hero-bg.jpg">
                <input type="hidden" name="cropped_image_data" id="bm_cropped_image_data" value="">
                <input type="hidden" name="use_berita_image" id="bm_use_berita_image" value="0">

                <!-- Pilihan 2 Opsi Mode Sumber Banner -->
                <div class="mb-4">
                    <label class="form-label fw-bold text-dark small text-uppercase mb-2">Pilih Opsi Pengisian Banner Utama:</label>
                    <div class="row g-2">
                        <div class="col-md-6">
                            <label class="w-100 p-3 rounded-3 border d-flex align-items-start gap-3 mode-card-option" id="card_mode_custom" style="cursor: pointer;">
                                <input type="radio" name="mode_sumber" value="custom" id="radio_mode_custom" class="form-check-input mt-1" checked onchange="switchBannerMode('custom')">
                                <div>
                                    <div class="fw-bold text-dark"><i class="bi bi-cloud-arrow-up text-primary me-1"></i> Opsi 1: Upload Gambar &amp; Edit Teks Sendiri</div>
                                    <div class="small text-muted">Unggah foto/banner baru dari perangkat Anda, sesuaikan/crop posisinya, dan tulis teks judul sesuai keinginan.</div>
                                </div>
                            </label>
                        </div>
                        <div class="col-md-6">
                            <label class="w-100 p-3 rounded-3 border d-flex align-items-start gap-3 mode-card-option" id="card_mode_berita" style="cursor: pointer;">
                                <input type="radio" name="mode_sumber" value="berita" id="radio_mode_berita" class="form-check-input mt-1" onchange="switchBannerMode('berita')">
                                <div>
                                    <div class="fw-bold text-dark"><i class="bi bi-newspaper text-primary me-1"></i> Opsi 2: Sesuaikan dari Isi Berita &amp; Informasi</div>
                                    <div class="small text-muted">Pilih dari daftar konten Berita &amp; Informasi yang sudah ada. Gambar, judul, dan tautan otomatis menyesuaikan.</div>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="row g-4">
                    <!-- Kolom Kiri: Input Data & Pilih Berita / Upload -->
                    <div class="col-lg-5">
                        <!-- Box Pilih Berita (Tampil saat Opsi 2 dipilih) -->
                        <div id="section_pilih_berita" class="p-3 rounded-3 mb-3 border" style="background: #f0f9ff; border-color: #bae6fd !important; display: none;">
                            <label class="form-label fw-bold text-dark small mb-1">
                                <i class="bi bi-journal-check text-primary me-1"></i> Pilih Konten Berita &amp; Informasi
                            </label>
                            <div class="custom-slot-dropdown position-relative" id="banner_berita_dropdown">
                                <input type="hidden" name="berita_id" id="bm_berita_id" value="0">
                                <button type="button" class="btn btn-sm w-100 bg-white border text-start d-flex justify-content-between align-items-center py-2 px-3 rounded-2 shadow-none slot-trigger-btn"
                                        data-bs-toggle="dropdown" data-bs-display="static" aria-expanded="false">
                                    <span class="text-truncate slot-display-label small fw-semibold text-dark" id="bm_berita_label" style="max-width: calc(100% - 20px);">
                                        -- Pilih Konten Berita &amp; Informasi --
                                    </span>
                                    <i class="bi bi-chevron-down text-muted small ms-1"></i>
                                </button>
                                <div class="dropdown-menu dropdown-menu-start w-100 p-2 shadow-lg border rounded-3 mt-1"
                                     style="max-height: 260px; overflow-y: auto;">
                                    <div class="p-1 mb-2 sticky-top bg-white border-bottom pb-2" style="top: -8px; margin-top: -8px; padding-top: 8px !important; z-index: 5;">
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                                            <input type="text" class="form-control border-start-0 slot-filter-input" placeholder="Cari judul atau tanggal berita...">
                                        </div>
                                    </div>
                                    <div class="slot-items-container">
                                        <?php foreach ($tipeMap as $typeKey => $meta): ?>
                                            <?php $list = $groupedForDropdown[$typeKey] ?? []; ?>
                                            <?php if (empty($list)) continue; ?>
                                            <div class="slot-group-block mb-1">
                                                <div class="slot-group-header px-2 py-1 small fw-bold text-uppercase text-muted my-1 rounded"
                                                     style="font-size: 10.5px; letter-spacing: 0.5px; background: #f1f5f9; border: 1px solid #e2e8f0;">
                                                    <i class="bi <?= $meta['icon']; ?> me-1 text-primary"></i> <?= esc($meta['label']); ?> (<?= count($list); ?>)
                                                </div>
                                                <?php foreach ($list as $opt): ?>
                                                    <?php
                                                        $optId = (int)$opt['id'];
                                                        $tgl = !empty($opt['tanggal_label']) ? '[' . $opt['tanggal_label'] . '] ' : '';
                                                        $fullTitle = $tgl . $opt['judul'];
                                                        $displayLabel = $tgl . mb_strimwidth($opt['judul'], 0, 42, '...');
                                                        $optImgUrl = $resolveImgUrl($opt['gambar']);
                                                        $subDefault = ($meta['label'] ?? 'Informasi') . (!empty($opt['tanggal_label']) ? ' • ' . $opt['tanggal_label'] : '');
                                                    ?>
                                                    <a class="dropdown-item rounded-2 py-1.5 px-2 small slot-option-item banner-berita-option"
                                                       href="javascript:void(0)"
                                                       data-id="<?= $optId; ?>"
                                                       data-label="<?= esc($displayLabel); ?>"
                                                       data-search="<?= esc(strtolower($fullTitle)); ?>"
                                                       data-judul="<?= esc($opt['judul']); ?>"
                                                       data-subjudul="<?= esc($subDefault); ?>"
                                                       data-gambar="<?= esc($opt['gambar']); ?>"
                                                       data-gambar-url="<?= esc($optImgUrl); ?>"
                                                       data-target-url="<?= esc($opt['target_url'] ?? '#berita'); ?>"
                                                       title="<?= esc($fullTitle); ?>">
                                                        <div class="fw-semibold text-truncate" style="font-size: 12px;"><?= esc($fullTitle); ?></div>
                                                    </a>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php endforeach; ?>
                                        <div class="slot-no-results text-center py-3 text-muted small" style="display: none;">
                                            Tidak ada konten yang cocok
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <small class="text-muted d-block mt-1" style="font-size: 11px;">
                                * Saat berita dipilih, gambar, subjudul, judul, dan link akan terisi otomatis. Anda tetap dapat menyesuaikan teks maupun memotong (crop) posisi gambarnya.
                            </small>
                        </div>

                        <!-- Input Upload Gambar -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold small text-dark mb-1">
                                <i class="bi bi-image text-primary me-1"></i> Upload Gambar Banner Baru <span class="text-muted fw-normal">(Opsional jika ingin ganti foto)</span>
                            </label>
                            <input type="file" name="gambar_file" id="bm_gambar_file" class="form-control form-control-sm" accept="image/jpeg,image/png,image/webp">
                            <div class="d-flex justify-content-between align-items-center mt-1">
                                <small class="text-muted" style="font-size: 11px;">Format: JPG, PNG, WEBP. Rasio ideal: 740 x 280 px.</small>
                                <button type="button" class="btn btn-link btn-sm p-0 text-decoration-none fw-semibold" onclick="activateCropperOnCurrentImage()">
                                    <i class="bi bi-crop"></i> Sesuaikan / Crop Gambar Saat Ini
                                </button>
                            </div>
                        </div>

                        <!-- Teks Subjudul (Intro) -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold small text-dark mb-1">Teks Label Atas / Subjudul Banner</label>
                            <input type="text" name="subjudul" id="bm_subjudul" class="form-control form-control-sm" placeholder="Contoh: DPP Masa Bakti 2026–2030 / Agenda Mendatang" oninput="updateLiveBannerText()">
                        </div>

                        <!-- Teks Judul Utama -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold small text-dark mb-1">Teks Judul Utama Banner <span class="text-danger">*</span></label>
                            <textarea name="judul" id="bm_judul" rows="2" class="form-control form-control-sm" placeholder="Contoh: Dewan Pengurus Provinsi INKINDO DKI Jakarta" required oninput="updateLiveBannerText()"></textarea>
                        </div>

                        <!-- Link Tujuan & Urutan -->
                        <div class="row g-2 mb-2">
                            <div class="col-6">
                                <label class="form-label fw-semibold small text-dark mb-1">Tautan Tujuan Saat Diklik</label>
                                <input type="text" name="target_url" id="bm_target_url" class="form-control form-control-sm" placeholder="#berita atau berita-detail.html?..." value="#berita">
                            </div>
                            <div class="col-3">
                                <label class="form-label fw-semibold small text-dark mb-1">Urutan Slide</label>
                                <input type="number" name="urutan" id="bm_urutan" class="form-control form-control-sm" min="1" max="20" value="1">
                            </div>
                            <div class="col-3">
                                <label class="form-label fw-semibold small text-dark mb-1">Status</label>
                                <select name="is_active" id="bm_is_active" class="form-select form-select-sm">
                                    <option value="1">Aktif</option>
                                    <option value="0">Nonaktif</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Kolom Kanan: Editor Crop Gambar & Pratinjau Real-Time Banner Utama -->
                    <div class="col-lg-7">
                        <!-- Panel Editor / Crop Gambar -->
                        <div id="cropper_workspace_card" class="p-3 rounded-3 border mb-3" style="background: #f8fafc; display: none;">
                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2">
                                <span class="fw-bold small text-dark">
                                    <i class="bi bi-bounding-box-circles text-primary me-1"></i> Penyesuaian &amp; Crop Posisi Gambar
                                </span>
                                <div class="btn-group btn-group-sm" role="group">
                                    <button type="button" class="btn btn-outline-primary active" id="btn_ratio_banner" onclick="setCropperRatio(740/280, this)">Rasio Banner (Presisi)</button>
                                    <button type="button" class="btn btn-outline-primary" id="btn_ratio_free" onclick="setCropperRatio(NaN, this)">Rasio Bebas</button>
                                </div>
                            </div>

                            <div class="rounded-2 overflow-hidden bg-dark mb-2" style="height: 240px; max-height: 240px;">
                                <img id="cropper_target_img" src="" alt="Crop Target" style="max-width: 100%; display: block;">
                            </div>

                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                <div class="btn-group btn-group-sm">
                                    <button type="button" class="btn btn-light border" onclick="cropperZoom(0.1)" title="Perbesar (Zoom In)"><i class="bi bi-zoom-in"></i></button>
                                    <button type="button" class="btn btn-light border" onclick="cropperZoom(-0.1)" title="Perkecil (Zoom Out)"><i class="bi bi-zoom-out"></i></button>
                                    <button type="button" class="btn btn-light border" onclick="cropperMove(0, -10)" title="Geser Atas"><i class="bi bi-arrow-up"></i></button>
                                    <button type="button" class="btn btn-light border" onclick="cropperMove(0, 10)" title="Geser Bawah"><i class="bi bi-arrow-down"></i></button>
                                    <button type="button" class="btn btn-light border" onclick="cropperMove(-10, 0)" title="Geser Kiri"><i class="bi bi-arrow-left"></i></button>
                                    <button type="button" class="btn btn-light border" onclick="cropperMove(10, 0)" title="Geser Kanan"><i class="bi bi-arrow-right"></i></button>
                                    <button type="button" class="btn btn-light border" onclick="cropperRotate(-90)" title="Putar Kiri"><i class="bi bi-arrow-counterclockwise"></i></button>
                                    <button type="button" class="btn btn-light border" onclick="cropperRotate(90)" title="Putar Kanan"><i class="bi bi-arrow-clockwise"></i></button>
                                    <button type="button" class="btn btn-light border" onclick="cropperReset()" title="Reset Posisi"><i class="bi bi-arrow-repeat"></i> Reset</button>
                                </div>
                                <div class="d-flex gap-1">
                                    <button type="button" class="btn btn-sm btn-success rounded-pill px-3 fw-semibold" onclick="applyCropToPreview()">
                                        <i class="bi bi-check2-circle me-1"></i> Terapkan Penyesuaian
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-2.5" onclick="closeCropperWorkspace()">
                                        Tutup Editor
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Live Preview Persis Ukuran Proporsional Banner Utama WebProfile -->
                        <div class="p-3 rounded-3 border" style="background: #ffffff;">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="fw-bold small text-dark">
                                    <i class="bi bi-eye text-primary me-1"></i> Pratinjau Tampilan Banner Utama di Beranda WebProfile
                                </span>
                                <span class="badge bg-light text-secondary border" id="crop_status_badge">Tampilan Proporsional (740 × 280)</span>
                            </div>

                            <div class="position-relative rounded-3 overflow-hidden shadow" style="aspect-ratio: 740 / 280; background: #0f172a;">
                                <img id="bm_live_preview_img" src="/cms/webAsset/assets/hero-bg.jpg" alt="Live Preview Banner" style="width: 100%; height: 100%; object-fit: cover; display: block;">
                                <div class="position-absolute top-50 start-0 translate-middle-y ms-2 d-flex align-items-center justify-content-center rounded-circle text-white" style="width: 28px; height: 28px; background: rgba(255,255,255,0.2); backdrop-filter: blur(4px);">
                                    <i class="bi bi-chevron-left small"></i>
                                </div>
                                <div class="position-absolute top-50 end-0 translate-middle-y me-2 d-flex align-items-center justify-content-center rounded-circle text-white" style="width: 28px; height: 28px; background: rgba(255,255,255,0.2); backdrop-filter: blur(4px);">
                                    <i class="bi bi-chevron-right small"></i>
                                </div>
                                <div class="position-absolute top-0 start-0 bottom-0 d-flex flex-column justify-content-center px-5 py-3" style="max-width: 72%; z-index: 2; pointer-events: none;">
                                    <div id="bm_live_preview_subjudul" class="text-white fw-medium mb-1" style="font-size: 13.5px; text-shadow: 0 2px 8px rgba(0,0,0,0.85), 0 1px 3px rgba(0,0,0,0.9);">
                                        DPP Masa Bakti 2026–2030
                                    </div>
                                    <div id="bm_live_preview_judul" class="text-white fw-bold" style="font-size: 21px; line-height: 1.25; text-shadow: 0 2px 10px rgba(0,0,0,0.9), 0 1px 4px rgba(0,0,0,0.9);">
                                        Dewan Pengurus Provinsi INKINDO DKI Jakarta
                                    </div>
                                </div>
                            </div>
                            <small class="text-muted d-block mt-2" style="font-size: 11.5px;">
                                <i class="bi bi-info-circle me-1 text-primary"></i> Tips: Jika bagian atas/bawah foto terlihat terpotong pada pratinjau di atas, klik tombol <strong>"Sesuaikan / Crop Gambar Saat Ini"</strong> untuk menggeser atau mengatur zoom foto.
                            </small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer border-top px-4 py-3" style="background: #f8fafc;">
                <button type="button" class="btn btn-light border rounded-pill px-4 fw-semibold" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary rounded-pill px-4 fw-semibold" style="background-color: var(--inkindo-blue); border: none;">
                    <i class="bi bi-check2-circle me-1"></i> Simpan Slide Banner Utama
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ================= MODAL 3: TAMBAH / EDIT GAMBAR BACKGROUND UTAMA + CROP ================= -->
<div class="modal fade" id="bgModal" tabindex="-1" aria-labelledby="bgModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form action="/cms/background/save" method="post" enctype="multipart/form-data" id="bgForm" class="modal-content border-0 shadow-lg modal-scrollable-form" style="border-radius: 16px;">
            <div class="modal-header border-bottom px-4 py-3" style="background: #f8fafc;">
                <div>
                    <h5 class="modal-title fw-bold text-dark mb-0" id="bgModalLabel">
                        <i class="bi bi-aspect-ratio text-primary me-2"></i>Tambah Gambar Background Utama
                    </h5>
                    <small class="text-muted">Unggah atau sesuaikan gambar latar belakang penuh (Hero Background) di halaman utama.</small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body p-4">
                <input type="hidden" name="bg_id" id="bgm_id" value="0">
                <input type="hidden" name="existing_gambar" id="bgm_existing_gambar" value="assets/jakartahome1.jpg">
                <input type="hidden" name="cropped_bg_data" id="bgm_cropped_data" value="">

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small text-dark mb-1">Nama / Keterangan Background <span class="text-danger">*</span></label>
                        <input type="text" name="judul" id="bgm_judul" class="form-control form-control-sm" placeholder="Contoh: Panorama Kota DKI Jakarta" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold small text-dark mb-1">Urutan Tampil</label>
                        <input type="number" name="urutan" id="bgm_urutan" class="form-control form-control-sm" min="1" max="30" value="1">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold small text-dark mb-1">Status</label>
                        <select name="is_active" id="bgm_is_active" class="form-select form-select-sm">
                            <option value="1">Aktif Tampil</option>
                            <option value="0">Nonaktif</option>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold small text-dark mb-1">
                        <i class="bi bi-upload text-primary me-1"></i> Upload Gambar Background Baru
                    </label>
                    <input type="file" name="gambar_file" id="bgm_file" class="form-control form-control-sm" accept="image/jpeg,image/png,image/webp">
                    <div class="d-flex justify-content-between align-items-center mt-1">
                        <small class="text-muted" style="font-size: 11px;">Format: JPG, PNG, WEBP. Resolusi lanskap layar lebar (16:9).</small>
                        <button type="button" class="btn btn-link btn-sm p-0 text-decoration-none fw-semibold" onclick="activateBgCropper()">
                            <i class="bi bi-crop"></i> Sesuaikan / Crop Gambar Background
                        </button>
                    </div>
                </div>

                <!-- Area Cropper Background -->
                <div id="bg_cropper_card" class="p-3 rounded-3 border mb-3" style="background: #f8fafc; display: none;">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="fw-bold small text-dark"><i class="bi bi-crop text-primary me-1"></i>Editor Crop Background Utama (16:9)</span>
                        <div class="d-flex gap-1">
                            <button type="button" class="btn btn-sm btn-success rounded-pill px-3 fw-semibold" onclick="applyBgCrop()">
                                <i class="bi bi-check2-circle me-1"></i> Terapkan Crop
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-2.5" onclick="closeBgCropper()">
                                Tutup
                            </button>
                        </div>
                    </div>
                    <div class="rounded-2 overflow-hidden bg-dark" style="height: 240px; max-height: 240px;">
                        <img id="bg_cropper_img" src="" alt="Crop BG" style="max-width: 100%; display: block;">
                    </div>
                </div>

                <!-- Pratinjau Background -->
                <div class="p-3 rounded-3 border" style="background: #ffffff;">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="fw-bold small text-dark"><i class="bi bi-eye text-primary me-1"></i>Pratinjau Gambar Background Utama</span>
                        <span class="badge bg-light text-secondary border" id="bg_crop_badge">Lanskap 16:9</span>
                    </div>
                    <div class="position-relative rounded-3 overflow-hidden shadow-sm" style="aspect-ratio: 16 / 9; max-height: 240px; width: 100%; background: #0f172a;">
                        <img id="bgm_preview_img" src="/cms/webAsset/assets/jakartahome1.jpg" alt="Preview Background" style="width: 100%; height: 100%; object-fit: cover; display: block;">
                    </div>
                </div>
            </div>

            <div class="modal-footer border-top px-4 py-3" style="background: #f8fafc;">
                <button type="button" class="btn btn-light border rounded-pill px-4 fw-semibold" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary rounded-pill px-4 fw-semibold" style="background-color: var(--inkindo-blue); border: none;">
                    <i class="bi bi-check2-circle me-1"></i> Simpan Gambar Background
                </button>
            </div>
        </form>
    </div>
</div>

<style>
@keyframes cmsBlink {
    0%, 100% { opacity: 1; }
    50% { opacity: 0; }
}
/* Perbaikan Modal Scroll agar Pop-up selalu dapat di-scroll dengan mulus ke bawah */
.modal-scrollable-form {
    max-height: 90vh;
    display: flex !important;
    flex-direction: column !important;
    overflow: hidden;
}
.modal-scrollable-form .modal-header,
.modal-scrollable-form .modal-footer {
    flex-shrink: 0;
}
.modal-scrollable-form .modal-body {
    overflow-y: auto !important;
    flex: 1 1 auto;
    max-height: calc(90vh - 135px);
}
.custom-slot-dropdown {
    position: relative !important;
}
.custom-slot-dropdown .dropdown-menu {
    position: absolute !important;
    top: 100% !important;
    bottom: auto !important;
    left: 0 !important;
    right: 0 !important;
    width: 100% !important;
    transform: none !important;
    margin-top: 6px !important;
    border-radius: 10px !important;
    border: 1px solid #cbd5e1 !important;
    box-shadow: 0 12px 28px -4px rgba(15, 23, 42, 0.16), 0 6px 12px -2px rgba(15, 23, 42, 0.08) !important;
    background-color: #ffffff !important;
    z-index: 1055 !important;
}
.custom-slot-dropdown .slot-trigger-btn {
    transition: all 0.2s ease;
    border-color: #cbd5e1 !important;
}
.custom-slot-dropdown .slot-trigger-btn:hover,
.custom-slot-dropdown .slot-trigger-btn:focus,
.custom-slot-dropdown .slot-trigger-btn[aria-expanded="true"] {
    border-color: var(--inkindo-blue) !important;
    box-shadow: 0 0 0 3px rgba(10, 100, 164, 0.15) !important;
}
.custom-slot-dropdown .slot-option-item {
    transition: background-color 0.15s ease, color 0.15s ease;
    cursor: pointer;
    text-decoration: none;
    color: #334155;
}
.custom-slot-dropdown .slot-option-item:hover {
    background-color: #f1f5f9;
    color: var(--inkindo-blue) !important;
}
.custom-slot-dropdown .slot-option-item.active {
    background-color: var(--inkindo-blue) !important;
    color: #ffffff !important;
}
.mode-card-option {
    transition: all 0.2s ease;
    background: #ffffff;
}
.mode-card-option.is-selected {
    background: #f0f9ff;
    border-color: var(--inkindo-blue) !important;
    box-shadow: 0 0 0 3px rgba(10, 100, 164, 0.12);
}
</style>

<!-- Cropper.js Script -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.js"></script>

<script>
var bannerCropper = null;
var bgCropper = null;
var currentRawImageSrc = '/cms/webAsset/assets/hero-bg.jpg';
var currentBgRawSrc = '/cms/webAsset/assets/jakartahome1.jpg';

/* ---------- Simulasi Live Typing di Halaman CMS ---------- */
(function() {
    var cmsPhrases = <?= json_encode(array_values(array_map(fn($r) => $r['teks'], array_filter($phrases ?? [], fn($r) => (int)($r['is_active'] ?? 1) === 1)))); ?>;
    if (!cmsPhrases || !cmsPhrases.length) {
        cmsPhrases = ['Selamat Datang', 'DPP INKINDO DKI Jakarta'];
    }
    var el = document.getElementById('cms_live_typing_preview');
    if (!el) return;
    var pIdx = 0;
    var cIdx = cmsPhrases[0].length;
    var isDel = true;
    el.textContent = cmsPhrases[0];

    function stepType() {
        var word = cmsPhrases[pIdx] || '';
        el.textContent = word.slice(0, cIdx);
        var delay = isDel ? 40 : 80;
        if (!isDel && cIdx === word.length) {
            delay = 2000;
            isDel = true;
        } else if (isDel && cIdx === 0) {
            isDel = false;
            pIdx = (pIdx + 1) % cmsPhrases.length;
            delay = 400;
        } else {
            cIdx += isDel ? -1 : 1;
        }
        setTimeout(stepType, delay);
    }
    setTimeout(stepType, 1800);
})();

/* ---------- Modal 1: Teks Bergerak ---------- */
function openPhraseModal(data) {
    if (data && data.id) {
        document.getElementById('phraseModalLabel').innerHTML = '<i class="bi bi-pencil-square text-primary me-2"></i>Edit Teks Bergerak #' + data.urutan;
        document.getElementById('pm_phrase_id').value = data.id;
        document.getElementById('pm_teks').value = data.teks || '';
        document.getElementById('pm_urutan').value = data.urutan || 1;
        document.getElementById('pm_is_active').value = String(data.is_active ?? 1);
    } else {
        document.getElementById('phraseModalLabel').innerHTML = '<i class="bi bi-plus-circle text-primary me-2"></i>Tambah Teks Bergerak Baru';
        document.getElementById('pm_phrase_id').value = '0';
        document.getElementById('pm_teks').value = '';
        document.getElementById('pm_urutan').value = '<?= count($phrases ?? []) + 1; ?>';
        document.getElementById('pm_is_active').value = '1';
    }
    bootstrap.Modal.getOrCreateInstance(document.getElementById('phraseModal')).show();
}

/* ---------- Modal 2: Banner Utama ---------- */
function switchBannerMode(mode) {
    var radioCustom = document.getElementById('radio_mode_custom');
    var radioBerita = document.getElementById('radio_mode_berita');
    var cardCustom = document.getElementById('card_mode_custom');
    var cardBerita = document.getElementById('card_mode_berita');
    var secBerita = document.getElementById('section_pilih_berita');

    if (mode === 'berita') {
        radioBerita.checked = true;
        cardBerita.classList.add('is-selected');
        cardCustom.classList.remove('is-selected');
        secBerita.style.display = 'block';
    } else {
        radioCustom.checked = true;
        cardCustom.classList.add('is-selected');
        cardBerita.classList.remove('is-selected');
        secBerita.style.display = 'none';
    }
}

function updateLiveBannerText() {
    var sub = document.getElementById('bm_subjudul').value.trim();
    var jud = document.getElementById('bm_judul').value.trim();
    var prevSub = document.getElementById('bm_live_preview_subjudul');
    var prevJud = document.getElementById('bm_live_preview_judul');

    prevSub.textContent = sub;
    prevSub.style.display = sub ? 'block' : 'none';
    prevJud.textContent = jud || 'Judul Banner Utama...';
}

function initCropperWithSrc(srcUrl) {
    var workspace = document.getElementById('cropper_workspace_card');
    var targetImg = document.getElementById('cropper_target_img');
    workspace.style.display = 'block';

    if (bannerCropper) {
        bannerCropper.destroy();
        bannerCropper = null;
    }

    targetImg.src = srcUrl;
    targetImg.onload = function() {
        if (bannerCropper) {
            bannerCropper.destroy();
        }
        bannerCropper = new Cropper(targetImg, {
            aspectRatio: 740 / 280,
            viewMode: 1,
            dragMode: 'move',
            autoCropArea: 1,
            restore: false,
            guides: true,
            center: true,
            highlight: false,
            cropBoxMovable: true,
            cropBoxResizable: true,
            toggleDragModeOnDblclick: false,
        });
    };
}

function activateCropperOnCurrentImage() {
    initCropperWithSrc(currentRawImageSrc);
}

function setCropperRatio(ratio, btnEl) {
    document.getElementById('btn_ratio_banner').classList.remove('active');
    document.getElementById('btn_ratio_free').classList.remove('active');
    if (btnEl) btnEl.classList.add('active');
    if (bannerCropper) {
        bannerCropper.setAspectRatio(ratio);
    }
}

function cropperZoom(delta) {
    if (bannerCropper) bannerCropper.zoom(delta);
}

function cropperMove(dx, dy) {
    if (bannerCropper) bannerCropper.move(dx, dy);
}

function cropperRotate(deg) {
    if (bannerCropper) bannerCropper.rotate(deg);
}

function cropperReset() {
    if (bannerCropper) bannerCropper.reset();
}

function applyCropToPreview() {
    if (!bannerCropper) return;
    var canvas = bannerCropper.getCroppedCanvas({
        width: 1110,
        height: 420,
        imageSmoothingEnabled: true,
        imageSmoothingQuality: 'high'
    });
    if (!canvas) return;
    var dataUrl = canvas.toDataURL('image/jpeg', 0.92);
    document.getElementById('bm_cropped_image_data').value = dataUrl;
    document.getElementById('bm_live_preview_img').src = dataUrl;
    var badge = document.getElementById('crop_status_badge');
    badge.className = 'badge bg-success-subtle text-success border border-success-subtle';
    badge.innerHTML = '<i class="bi bi-check-circle-fill me-1"></i>Hasil Crop Diterapkan';
    closeCropperWorkspace();
}

function closeCropperWorkspace() {
    var workspace = document.getElementById('cropper_workspace_card');
    workspace.style.display = 'none';
    if (bannerCropper) {
        bannerCropper.destroy();
        bannerCropper = null;
    }
}

function openBannerModal(data) {
    closeCropperWorkspace();
    document.getElementById('bm_gambar_file').value = '';
    document.getElementById('bm_cropped_image_data').value = '';
    document.getElementById('bm_use_berita_image').value = '0';

    var badge = document.getElementById('crop_status_badge');
    badge.className = 'badge bg-light text-secondary border';
    badge.textContent = 'Tampilan Proporsional (740 × 280)';

    if (data && data.id) {
        document.getElementById('bannerModalLabel').innerHTML = '<i class="bi bi-pencil-square text-primary me-2"></i>Edit &amp; Sesuaikan Slide Banner #' + data.urutan;
        document.getElementById('bm_banner_id').value = data.id;
        document.getElementById('bm_existing_gambar').value = data.gambar || 'assets/hero-bg.jpg';
        document.getElementById('bm_subjudul').value = data.subjudul || '';
        document.getElementById('bm_judul').value = data.judul || '';
        document.getElementById('bm_target_url').value = data.target_url || '#berita';
        document.getElementById('bm_urutan').value = data.urutan || 1;
        document.getElementById('bm_is_active').value = String(data.is_active ?? 1);
        document.getElementById('bm_berita_id').value = data.berita_id || 0;

        currentRawImageSrc = data.gambar_url || '/cms/webAsset/assets/hero-bg.jpg';
        document.getElementById('bm_live_preview_img').src = currentRawImageSrc;

        switchBannerMode(data.mode_sumber === 'berita' ? 'berita' : 'custom');
    } else {
        document.getElementById('bannerModalLabel').innerHTML = '<i class="bi bi-plus-circle text-primary me-2"></i>Tambah Slide Banner Utama Baru';
        document.getElementById('bm_banner_id').value = '0';
        document.getElementById('bm_existing_gambar').value = 'assets/hero-bg.jpg';
        document.getElementById('bm_subjudul').value = 'Informasi Utama';
        document.getElementById('bm_judul').value = '';
        document.getElementById('bm_target_url').value = '#berita';
        document.getElementById('bm_urutan').value = '<?= count($banners) + 1; ?>';
        document.getElementById('bm_is_active').value = '1';
        document.getElementById('bm_berita_id').value = '0';
        document.getElementById('bm_berita_label').textContent = '-- Pilih Konten Berita & Informasi --';

        currentRawImageSrc = '/cms/webAsset/assets/hero-bg.jpg';
        document.getElementById('bm_live_preview_img').src = currentRawImageSrc;

        switchBannerMode('custom');
    }

    updateLiveBannerText();
    var modalEl = document.getElementById('bannerModal');
    var modal = bootstrap.Modal.getOrCreateInstance(modalEl);
    modal.show();
}

/* ---------- Modal 3: Gambar Background Utama ---------- */
function activateBgCropper() {
    var card = document.getElementById('bg_cropper_card');
    var img = document.getElementById('bg_cropper_img');
    card.style.display = 'block';
    if (bgCropper) {
        bgCropper.destroy();
        bgCropper = null;
    }
    img.src = currentBgRawSrc;
    img.onload = function() {
        if (bgCropper) bgCropper.destroy();
        bgCropper = new Cropper(img, {
            aspectRatio: 16 / 9,
            viewMode: 1,
            dragMode: 'move',
            autoCropArea: 1,
        });
    };
}

function applyBgCrop() {
    if (!bgCropper) return;
    var canvas = bgCropper.getCroppedCanvas({
        width: 1600,
        height: 900,
        imageSmoothingEnabled: true,
        imageSmoothingQuality: 'high'
    });
    if (!canvas) return;
    var dataUrl = canvas.toDataURL('image/jpeg', 0.9);
    document.getElementById('bgm_cropped_data').value = dataUrl;
    document.getElementById('bgm_preview_img').src = dataUrl;
    var badge = document.getElementById('bg_crop_badge');
    badge.className = 'badge bg-success-subtle text-success border border-success-subtle';
    badge.innerHTML = '<i class="bi bi-check-circle-fill me-1"></i>Hasil Crop Diterapkan';
    closeBgCropper();
}

function closeBgCropper() {
    document.getElementById('bg_cropper_card').style.display = 'none';
    if (bgCropper) {
        bgCropper.destroy();
        bgCropper = null;
    }
}

function openBgModal(data) {
    closeBgCropper();
    document.getElementById('bgm_file').value = '';
    document.getElementById('bgm_cropped_data').value = '';
    var badge = document.getElementById('bg_crop_badge');
    badge.className = 'badge bg-light text-secondary border';
    badge.textContent = 'Lanskap 16:9';

    if (data && data.id) {
        document.getElementById('bgModalLabel').innerHTML = '<i class="bi bi-pencil-square text-primary me-2"></i>Edit Gambar Background #' + data.urutan;
        document.getElementById('bgm_id').value = data.id;
        document.getElementById('bgm_judul').value = data.judul || '';
        document.getElementById('bgm_existing_gambar').value = data.gambar || 'assets/jakartahome1.jpg';
        document.getElementById('bgm_urutan').value = data.urutan || 1;
        document.getElementById('bgm_is_active').value = String(data.is_active ?? 1);
        currentBgRawSrc = data.gambar_url || '/cms/webAsset/assets/jakartahome1.jpg';
        document.getElementById('bgm_preview_img').src = currentBgRawSrc;
    } else {
        document.getElementById('bgModalLabel').innerHTML = '<i class="bi bi-plus-circle text-primary me-2"></i>Tambah Gambar Background Utama Baru';
        document.getElementById('bgm_id').value = '0';
        document.getElementById('bgm_judul').value = '';
        document.getElementById('bgm_existing_gambar').value = 'assets/jakartahome1.jpg';
        document.getElementById('bgm_urutan').value = '<?= count($backgrounds ?? []) + 1; ?>';
        document.getElementById('bgm_is_active').value = '1';
        currentBgRawSrc = '/cms/webAsset/assets/jakartahome1.jpg';
        document.getElementById('bgm_preview_img').src = currentBgRawSrc;
    }

    bootstrap.Modal.getOrCreateInstance(document.getElementById('bgModal')).show();
}

document.addEventListener('DOMContentLoaded', function() {
    var fileInput = document.getElementById('bm_gambar_file');
    if (fileInput) {
        fileInput.addEventListener('change', function(e) {
            var file = e.target.files && e.target.files[0];
            if (!file) return;
            var reader = new FileReader();
            reader.onload = function(ev) {
                currentRawImageSrc = ev.target.result;
                document.getElementById('bm_live_preview_img').src = currentRawImageSrc;
                initCropperWithSrc(currentRawImageSrc);
            };
            reader.readAsDataURL(file);
        });
    }

    var bgFileInput = document.getElementById('bgm_file');
    if (bgFileInput) {
        bgFileInput.addEventListener('change', function(e) {
            var file = e.target.files && e.target.files[0];
            if (!file) return;
            var reader = new FileReader();
            reader.onload = function(ev) {
                currentBgRawSrc = ev.target.result;
                document.getElementById('bgm_preview_img').src = currentBgRawSrc;
                activateBgCropper();
            };
            reader.readAsDataURL(file);
        });
    }

    var bannerForm = document.getElementById('bannerForm');
    if (bannerForm) {
        bannerForm.addEventListener('submit', function() {
            if (bannerCropper) {
                applyCropToPreview();
            }
        });
    }

    var bgForm = document.getElementById('bgForm');
    if (bgForm) {
        bgForm.addEventListener('submit', function() {
            if (bgCropper) {
                applyBgCrop();
            }
        });
    }

    document.querySelectorAll('.custom-slot-dropdown').forEach(function(wrap) {
        var input = wrap.querySelector('input[type="hidden"]');
        var label = wrap.querySelector('.slot-display-label');
        var triggerBtn = wrap.querySelector('.slot-trigger-btn');
        var searchInput = wrap.querySelector('.slot-filter-input');
        var options = wrap.querySelectorAll('.slot-option-item');
        var noResults = wrap.querySelector('.slot-no-results');
        var groupBlocks = wrap.querySelectorAll('.slot-group-block');
        var isBannerBeritaDropdown = (wrap.id === 'banner_berita_dropdown');

        options.forEach(function(item) {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                var id = this.getAttribute('data-id');
                var text = this.getAttribute('data-label');
                var full = this.getAttribute('title') || text;
                input.value = id;
                label.textContent = text;
                triggerBtn.setAttribute('title', full);

                options.forEach(function(opt) {
                    opt.classList.remove('active', 'bg-primary', 'bg-danger', 'text-white');
                });
                if (id !== '0') {
                    this.classList.add('active', 'bg-primary', 'text-white');
                } else {
                    this.classList.add('active', 'bg-danger', 'text-white');
                }

                if (isBannerBeritaDropdown && id !== '0') {
                    var bJudul = this.getAttribute('data-judul') || '';
                    var bSub = this.getAttribute('data-subjudul') || '';
                    var bGambar = this.getAttribute('data-gambar') || '';
                    var bGambarUrl = this.getAttribute('data-gambar-url') || '';
                    var bTargetUrl = this.getAttribute('data-target-url') || '#berita';

                    document.getElementById('bm_subjudul').value = bSub;
                    document.getElementById('bm_judul').value = bJudul;
                    document.getElementById('bm_target_url').value = bTargetUrl;
                    document.getElementById('bm_existing_gambar').value = bGambar;
                    document.getElementById('bm_use_berita_image').value = '1';
                    document.getElementById('bm_cropped_image_data').value = '';

                    if (bGambarUrl) {
                        currentRawImageSrc = bGambarUrl;
                        document.getElementById('bm_live_preview_img').src = bGambarUrl;
                    }
                    updateLiveBannerText();
                }

                var bsDropdown = bootstrap.Dropdown.getInstance(triggerBtn);
                if (bsDropdown) {
                    bsDropdown.hide();
                }
            });
        });

        if (searchInput) {
            searchInput.addEventListener('input', function() {
                var q = this.value.toLowerCase().trim();
                var totalMatches = 0;

                groupBlocks.forEach(function(group) {
                    var items = group.querySelectorAll('.slot-option-item');
                    var groupMatches = 0;
                    items.forEach(function(opt) {
                        var s = (opt.getAttribute('data-search') || '').toLowerCase();
                        if (!q || s.indexOf(q) !== -1) {
                            opt.style.display = '';
                            groupMatches++;
                            totalMatches++;
                        } else {
                            opt.style.display = 'none';
                        }
                    });
                    group.style.display = (groupMatches > 0) ? '' : 'none';
                });

                if (noResults) {
                    noResults.style.display = (totalMatches === 0 && q !== '') ? 'block' : 'none';
                }
            });

            ['click', 'keydown', 'keyup', 'focus'].forEach(function(evName) {
                searchInput.addEventListener(evName, function(e) {
                    e.stopPropagation();
                });
            });
        }

        wrap.addEventListener('shown.bs.dropdown', function() {
            if (searchInput) {
                searchInput.focus();
            }
        });

        wrap.addEventListener('hidden.bs.dropdown', function() {
            if (searchInput && searchInput.value) {
                searchInput.value = '';
                groupBlocks.forEach(function(group) {
                    group.style.display = '';
                    group.querySelectorAll('.slot-option-item').forEach(function(opt) {
                        opt.style.display = '';
                    });
                });
                if (noResults) noResults.style.display = 'none';
            }
        });
    });
});
</script>
<?= $this->endSection() ?>
