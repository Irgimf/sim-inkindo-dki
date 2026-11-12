<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<!-- Cropper.js CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.css">

<?php
    $resolveImgUrl = static function (?string $path, string $fallback = 'assets/jakarta3.jpg'): string {
        $p = trim((string) $path) ?: $fallback;
        if (strpos($p, 'http://') === 0 || strpos($p, 'https://') === 0) {
            return $p;
        }
        if (strpos($p, 'cms_uploads/') === 0) {
            return '/' . ltrim($p, '/');
        }
        return '/cms/webAsset/' . ltrim($p, '/');
    };

    $misiItems = json_decode((string) ($profile['misi_items_json'] ?? '[]'), true);
    if (!is_array($misiItems) || empty($misiItems)) {
        $misiItems = [''];
    }

    $kodeEtikItems = json_decode((string) ($profile['kode_etik_items_json'] ?? '[]'), true);
    if (!is_array($kodeEtikItems) || empty($kodeEtikItems)) {
        $kodeEtikItems = [''];
    }

    $currentUnitObj = $units[0] ?? ['id' => 0, 'slug' => 'dpph', 'nama' => 'DPPH', 'deskripsi' => '', 'intro_text' => ''];
    foreach ($units as $uItem) {
        if (($uItem['slug'] ?? '') === $activeUnit) {
            $currentUnitObj = $uItem;
            break;
        }
    }

    $unitGroups  = $groupsByUnit[$activeUnit] ?? [];
    $unitMembers = $membersByUnit[$activeUnit] ?? [];
?>

<style>
    .cms-tentang-tab-btn {
        border-radius: 12px;
        font-weight: 600;
        padding: 10px 20px;
        border: 1px solid #e2e8f0;
        background: #ffffff;
        color: #475569;
        transition: all 0.2s ease;
    }
    .cms-tentang-tab-btn:hover {
        background: #f8fafc;
        color: var(--inkindo-blue);
    }
    .cms-tentang-tab-btn.active {
        background: var(--inkindo-blue);
        color: #ffffff;
        border-color: var(--inkindo-blue);
        box-shadow: 0 4px 12px rgba(10, 100, 164, 0.22);
    }
    .sub-unit-pill {
        border-radius: 50px;
        padding: 8px 16px;
        font-size: 13.5px;
        font-weight: 600;
        border: 1px solid #cbd5e1;
        background: #ffffff;
        color: #334155;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s ease;
    }
    .sub-unit-pill:hover {
        border-color: var(--inkindo-blue);
        color: var(--inkindo-blue);
        background: #f0f7ff;
    }
    .sub-unit-pill.active {
        background: var(--inkindo-blue);
        color: #ffffff;
        border-color: var(--inkindo-blue);
        box-shadow: 0 3px 10px rgba(10, 100, 164, 0.25);
    }
    .cms-img-preview-box {
        width: 100%;
        height: 170px;
        border-radius: 12px;
        overflow: hidden;
        background: #0f172a;
        border: 1px solid #e2e8f0;
        position: relative;
    }
    .cms-img-preview-box img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }
    .cms-img-preview-box--contain {
        height: auto;
        min-height: 170px;
        max-height: 300px;
        background: #f8fafc;
        padding: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .cms-img-preview-box--contain img {
        width: auto;
        max-width: 100%;
        height: auto;
        max-height: 280px;
        object-fit: contain;
        border-radius: 6px;
    }
    .crud-table-input {
        border-color: #dbe3ed;
        font-size: 13px;
        transition: border-color 0.15s ease, box-shadow 0.15s ease;
    }
    .crud-table-input:focus {
        border-color: var(--inkindo-blue);
        box-shadow: 0 0 0 0.15rem rgba(10, 100, 164, 0.14);
    }
    .crud-row-highlighted {
        background-color: #eff6ff !important;
        box-shadow: inset 4px 0 0 #0a64a4;
    }
    .crud-row-inactive {
        background-color: #f8fafc !important;
        opacity: 0.65;
        box-shadow: inset 4px 0 0 #94a3b8;
    }
    .cropper-stage-wrap {
        background: #0f172a;
        border-radius: 10px;
        padding: 14px;
    }
    .cropper-stage-inner {
        width: 100%;
        height: 440px;
        max-height: 60vh;
        position: relative;
        overflow: hidden;
    }
    .group-chip {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 8px 12px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-size: 12.5px;
    }
    .modal-scrollable-form {
        max-height: calc(100vh - 2.5rem);
        overflow: hidden;
    }
    .modal-scrollable-form .modal-body {
        overflow-y: auto;
        min-height: 0;
    }
</style>

<!-- Header -->
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <h4 class="fw-bold mb-1" style="color: var(--inkindo-blue);">
            <i class="bi bi-info-square-fill me-2"></i>Admin CMS — Tentang Kami
        </h4>
        <p class="text-muted small mb-0">
            Kelola konten halaman <strong>Tentang Kami</strong> pada <code>inkindo-dki-web</code> (Profil Organisasi: Hero, Sejarah, Visi &amp; Misi, Gambar, Kode Etik serta Struktur Organisasi 7 Sub-Unit).
        </p>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <a href="http://localhost:8000/tentang.html" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-semibold">
            <i class="bi bi-box-arrow-up-right me-1"></i> Pratinjau Tentang Kami
        </a>
        <a href="http://localhost:8000/tentang.html?tab=struktur&sub=<?= esc($activeUnit); ?>" target="_blank" class="btn btn-sm btn-outline-secondary rounded-pill px-3 fw-semibold">
            <i class="bi bi-diagram-3 me-1"></i> Pratinjau <?= esc($currentUnitObj['nama'] ?? 'Struktur'); ?>
        </a>
    </div>
</div>

<?php if (session()->getFlashdata('pesan')): ?>
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i><?= esc(session()->getFlashdata('pesan')); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i><?= esc(session()->getFlashdata('error')); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<!-- Primary CMS Tabs -->
<div class="d-flex gap-2 flex-wrap mb-4">
    <a href="/cms/tentang?tab=profil"
       class="cms-tentang-tab-btn text-decoration-none <?= $activeTab === 'profil' ? 'active' : ''; ?>">
        <i class="bi bi-building-check me-2"></i>1. Profil Organisasi (Sejarah, Visi &amp; Misi, Gambar, Kode Etik)
    </a>
    <a href="/cms/tentang?tab=struktur&unit=<?= esc($activeUnit); ?>"
       class="cms-tentang-tab-btn text-decoration-none <?= $activeTab === 'struktur' ? 'active' : ''; ?>">
        <i class="bi bi-diagram-3-fill me-2"></i>2. Struktur Organisasi (DPPH, DPPL, Badan, Komite, DKP, DPOP, Setprov)
    </a>
</div>

<?php if ($activeTab === 'profil'): ?>
<!-- ================= TAB 1: PROFIL ORGANISASI ================= -->
<form action="/cms/tentang/profile/save" method="post" enctype="multipart/form-data">
    <!-- 1. Hero Banner -->
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px; border-left: 5px solid var(--inkindo-blue) !important;">
        <div class="card-body p-4">
            <h5 class="fw-bold mb-1 text-dark">
                <i class="bi bi-image-fill text-primary me-2"></i>A. Banner Hero Halaman Tentang Kami
            </h5>
            <p class="text-muted small mb-3">Mengatur judul, subjudul, dan gambar latar atas pada halaman Tentang Kami.</p>

            <div class="row g-4 align-items-start">
                <div class="col-lg-7">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-dark">Judul Banner Hero</label>
                        <input type="text" name="hero_title" class="form-control" value="<?= esc($profile['hero_title'] ?? 'Tentang Kami'); ?>" required>
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-semibold small text-dark">Subjudul Banner Hero</label>
                        <textarea name="hero_subtitle" rows="3" class="form-control"><?= esc($profile['hero_subtitle'] ?? ''); ?></textarea>
                    </div>
                </div>
                <div class="col-lg-5">
                    <?php $heroUrl = $resolveImgUrl($profile['hero_image'] ?? '', 'assets/jakarta3.jpg'); ?>
                    <label class="form-label fw-semibold small text-dark">Gambar Latar Hero</label>
                    <div class="cms-img-preview-box mb-2">
                        <img id="prev_hero_image" src="<?= esc($heroUrl); ?>" data-raw-src="<?= esc($heroUrl); ?>" alt="Hero Tentang Kami">
                    </div>
                    <input type="hidden" name="existing_hero_image" id="existing_hero_image" value="<?= esc($profile['hero_image'] ?? 'assets/jakarta3.jpg'); ?>">
                    <input type="hidden" name="cropped_hero_image" id="cropped_hero_image" value="">
                    <input type="file" name="hero_image_file" id="file_hero_image" class="form-control form-control-sm mb-2" accept="image/*"
                           onchange="handleTentangFileSelect(this, 'prev_hero_image', 'cropped_hero_image')">
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3"
                                onclick="openTentangCropper('prev_hero_image', 'cropped_hero_image', 16/5, 'Sesuaikan Gambar Latar Hero')">
                            <i class="bi bi-crop me-1"></i> Sesuaikan / Crop
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3"
                                onclick="resetTentangImage('assets/jakarta3.jpg', 'prev_hero_image', 'existing_hero_image', 'cropped_hero_image', 'file_hero_image')">
                            <i class="bi bi-arrow-counterclockwise me-1"></i> Reset Default
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. Sejarah -->
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px; border-left: 5px solid var(--inkindo-blue) !important;">
        <div class="card-body p-4">
            <h5 class="fw-bold mb-1 text-dark">
                <i class="bi bi-clock-history text-primary me-2"></i>B. Seksi Sejarah Organisasi
            </h5>
            <p class="text-muted small mb-3">Pisahkan antar paragraf dengan menekan <strong>Enter dua kali</strong> (satu baris kosong).</p>

            <div class="row g-4 align-items-start">
                <div class="col-lg-7">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-dark">Judul Seksi Sejarah</label>
                        <input type="text" name="sejarah_heading" class="form-control" value="<?= esc($profile['sejarah_heading'] ?? 'Sejarah INKINDO DKI Jakarta'); ?>" required>
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-semibold small text-dark">Teks Sejarah (Multi-Paragraf)</label>
                        <textarea name="sejarah_content" rows="8" class="form-control" required><?= esc($profile['sejarah_content'] ?? ''); ?></textarea>
                    </div>
                </div>
                <div class="col-lg-5">
                    <?php $sejarahUrl = $resolveImgUrl($profile['sejarah_image'] ?? '', 'assets/hero-bg.jpg'); ?>
                    <label class="form-label fw-semibold small text-dark">Gambar Seksi Sejarah</label>
                    <div class="cms-img-preview-box cms-img-preview-box--contain mb-2">
                        <img id="prev_sejarah_image" src="<?= esc($sejarahUrl); ?>" data-raw-src="<?= esc($sejarahUrl); ?>" alt="Gambar Sejarah">
                    </div>
                    <input type="hidden" name="existing_sejarah_image" id="existing_sejarah_image" value="<?= esc($profile['sejarah_image'] ?? 'assets/hero-bg.jpg'); ?>">
                    <input type="hidden" name="cropped_sejarah_image" id="cropped_sejarah_image" value="">
                    <input type="file" name="sejarah_image_file" id="file_sejarah_image" class="form-control form-control-sm mb-2" accept="image/*"
                           onchange="handleTentangFileSelect(this, 'prev_sejarah_image', 'cropped_sejarah_image')">
                    <div class="d-flex gap-2 flex-wrap">
                        <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3"
                                onclick="openTentangCropper('prev_sejarah_image', 'cropped_sejarah_image', null, 'Sesuaikan / Crop Gambar Seksi Sejarah (Bebas)')">
                            <i class="bi bi-crop me-1"></i> Sesuaikan / Crop (Bebas)
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3"
                                onclick="resetTentangImage('assets/hero-bg.jpg', 'prev_sejarah_image', 'existing_sejarah_image', 'cropped_sejarah_image', 'file_sejarah_image')">
                            <i class="bi bi-arrow-counterclockwise me-1"></i> Reset Default
                        </button>
                    </div>
                    <small class="text-muted d-block mt-2" style="font-size: 11.5px;">
                        <i class="bi bi-arrows-fullscreen me-1 text-primary"></i>Mode <strong>Rasio Bebas</strong>: foto tampil utuh, dan sisi kiri, kanan, atas, atau bawah dapat digeser secara bebas tanpa terkunci kotak rasio. Langsung klik <em>Simpan</em> bila ingin mengunggah utuh tanpa dipotong.
                    </small>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. Visi & Misi -->
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px; border-left: 5px solid var(--inkindo-blue) !important;">
        <div class="card-body p-4">
            <h5 class="fw-bold mb-1 text-dark">
                <i class="bi bi-bullseye text-primary me-2"></i>C. Seksi Visi &amp; Misi (Beserta Gambar Latar)
            </h5>
            <p class="text-muted small mb-3">Kelola teks Visi, daftar poin Misi yang bisa ditambah/dikurangi, serta gambar latar belakang seksi Visi &amp; Misi.</p>

            <div class="row g-4 mb-3">
                <div class="col-md-4">
                    <label class="form-label fw-semibold small text-dark">Judul Seksi Utama</label>
                    <input type="text" name="visimisi_heading" class="form-control mb-3" value="<?= esc($profile['visimisi_heading'] ?? 'Visi & Misi'); ?>" required>

                    <label class="form-label fw-semibold small text-dark">Label Judul Visi</label>
                    <input type="text" name="visi_title" class="form-control mb-3" value="<?= esc($profile['visi_title'] ?? 'Visi'); ?>">

                    <?php $visiMisiUrl = $resolveImgUrl($profile['visimisi_bg_image'] ?? '', 'assets/jakarta3.jpg'); ?>
                    <label class="form-label fw-semibold small text-dark">Gambar Latar Seksi Visi &amp; Misi</label>
                    <div class="cms-img-preview-box mb-2" style="height: 150px;">
                        <img id="prev_visimisi_bg" src="<?= esc($visiMisiUrl); ?>" data-raw-src="<?= esc($visiMisiUrl); ?>" alt="Background Visi Misi">
                    </div>
                    <input type="hidden" name="existing_visimisi_bg_image" id="existing_visimisi_bg_image" value="<?= esc($profile['visimisi_bg_image'] ?? 'assets/jakarta3.jpg'); ?>">
                    <input type="hidden" name="cropped_visimisi_bg_image" id="cropped_visimisi_bg_image" value="">
                    <input type="file" name="visimisi_bg_file" id="file_visimisi_bg" class="form-control form-control-sm mb-2" accept="image/*"
                           onchange="handleTentangFileSelect(this, 'prev_visimisi_bg', 'cropped_visimisi_bg_image')">
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3"
                                onclick="openTentangCropper('prev_visimisi_bg', 'cropped_visimisi_bg_image', 16/7, 'Sesuaikan Gambar Latar Visi & Misi')">
                            <i class="bi bi-crop me-1"></i> Crop
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3"
                                onclick="resetTentangImage('assets/jakarta3.jpg', 'prev_visimisi_bg', 'existing_visimisi_bg_image', 'cropped_visimisi_bg_image', 'file_visimisi_bg')">
                            Reset
                        </button>
                    </div>
                </div>

                <div class="col-md-8">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-dark">Isi Teks Visi</label>
                        <textarea name="visi_text" rows="3" class="form-control" required><?= esc($profile['visi_text'] ?? ''); ?></textarea>
                    </div>

                    <div class="row g-2 align-items-center mb-2">
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold small text-dark mb-0">Label &amp; Daftar Poin Misi</label>
                        </div>
                        <div class="col-sm-6 text-sm-end">
                            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-semibold" onclick="addDynamicListItem('misiListContainer', 'misi_items[]', 'Tulis poin misi...')">
                                <i class="bi bi-plus-lg me-1"></i> Tambah Poin Misi
                            </button>
                        </div>
                    </div>
                    <input type="text" name="misi_title" class="form-control form-control-sm mb-2" style="max-width: 220px;" value="<?= esc($profile['misi_title'] ?? 'Misi'); ?>" placeholder="Judul Misi">

                    <div id="misiListContainer" class="d-flex flex-column gap-2">
                        <?php foreach ($misiItems as $idx => $mText): ?>
                            <div class="input-group dynamic-list-row">
                                <span class="input-group-text bg-light fw-bold small row-num-badge"><?= $idx + 1; ?></span>
                                <textarea name="misi_items[]" rows="2" class="form-control form-control-sm" placeholder="Tulis poin misi..."><?= esc($mText); ?></textarea>
                                <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeDynamicListItem(this, 'misiListContainer')" title="Hapus Poin">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 4. Kode Etik -->
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px; border-left: 5px solid var(--inkindo-blue) !important;">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                <div>
                    <h5 class="fw-bold mb-1 text-dark">
                        <i class="bi bi-shield-check text-primary me-2"></i>D. Seksi Kode Etik
                    </h5>
                    <p class="text-muted small mb-0">Kelola judul, pengantar, dan butir-butir Kode Etik profesi INKINDO.</p>
                </div>
                <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-semibold" onclick="addDynamicListItem('kodeEtikListContainer', 'kode_etik_items[]', 'Tulis poin kode etik...')">
                    <i class="bi bi-plus-lg me-1"></i> Tambah Poin Kode Etik
                </button>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-4">
                    <label class="form-label fw-semibold small text-dark">Judul Seksi Kode Etik</label>
                    <input type="text" name="kode_etik_heading" class="form-control" value="<?= esc($profile['kode_etik_heading'] ?? 'Kode Etik'); ?>" required>
                </div>
                <div class="col-md-8">
                    <label class="form-label fw-semibold small text-dark">Pengantar Kode Etik</label>
                    <textarea name="kode_etik_subtitle" rows="2" class="form-control"><?= esc($profile['kode_etik_subtitle'] ?? ''); ?></textarea>
                </div>
            </div>

            <div id="kodeEtikListContainer" class="d-flex flex-column gap-2">
                <?php foreach ($kodeEtikItems as $idx => $keText): ?>
                    <div class="input-group dynamic-list-row">
                        <span class="input-group-text bg-light fw-bold small row-num-badge"><?= str_pad((string) ($idx + 1), 2, '0', STR_PAD_LEFT); ?></span>
                        <textarea name="kode_etik_items[]" rows="2" class="form-control form-control-sm" placeholder="Tulis poin kode etik..."><?= esc($keText); ?></textarea>
                        <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeDynamicListItem(this, 'kodeEtikListContainer', true)" title="Hapus Poin">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-end mb-4">
        <button type="submit" class="btn btn-primary btn-lg rounded-pill px-4 fw-bold shadow-sm" style="background-color: var(--inkindo-blue); border: none;">
            <i class="bi bi-check2-circle me-2"></i>Simpan Semua Perubahan Profil Organisasi
        </button>
    </div>
</form>

<?php else: ?>
<!-- ================= TAB 2: STRUKTUR ORGANISASI (7 SUB-UNIT + CRUD TABEL) ================= -->

<!-- Selector 7 Sub-Unit -->
<div class="card border-0 shadow-sm mb-4" style="border-radius: 16px;">
    <div class="card-body p-3 px-4">
        <div class="small fw-bold text-uppercase text-muted mb-2" style="letter-spacing: 0.5px;">
            <i class="bi bi-diagram-3 me-1 text-primary"></i> Pilih Sub-Unit Struktur Organisasi:
        </div>
        <div class="d-flex flex-wrap gap-2">
            <?php foreach ($units as $u): ?>
                <?php
                    $uSlug = (string) $u['slug'];
                    $uCount = (int) ($activeCountByUnit[$uSlug] ?? 0);
                ?>
                <a href="/cms/tentang?tab=struktur&unit=<?= esc($uSlug); ?>"
                   class="sub-unit-pill <?= $uSlug === $activeUnit ? 'active' : ''; ?>">
                    <span><?= esc($u['nama']); ?></span>
                    <span class="badge rounded-pill <?= $uSlug === $activeUnit ? 'bg-white text-primary' : 'bg-light text-secondary border'; ?>" style="font-size: 11px;">
                        <?= $uCount; ?>
                    </span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Info & Edit Header Sub-Unit -->
<div class="card border-0 shadow-sm mb-4" style="border-radius: 16px; border-left: 5px solid var(--inkindo-blue) !important;">
    <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
            <div>
                <h5 class="fw-bold mb-1 text-dark">
                    <i class="bi bi-collection-fill text-primary me-2"></i><?= esc($currentUnitObj['nama']); ?>
                </h5>
                <p class="text-muted small mb-0"><?= esc($currentUnitObj['deskripsi'] ?? ''); ?></p>
            </div>
            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3 fw-semibold"
                    data-bs-toggle="collapse" data-bs-target="#editUnitHeaderCollapse">
                <i class="bi bi-pencil-square me-1"></i> Edit Judul &amp; Pengantar Sub-Unit
            </button>
        </div>

        <div class="collapse mt-3 pt-3 border-top" id="editUnitHeaderCollapse">
            <form action="/cms/tentang/unit/save" method="post" class="row g-3">
                <input type="hidden" name="unit_id" value="<?= (int) ($currentUnitObj['id'] ?? 0); ?>">
                <input type="hidden" name="slug" value="<?= esc($activeUnit); ?>">
                <div class="col-md-4">
                    <label class="form-label fw-semibold small text-dark">Nama Sub-Unit</label>
                    <input type="text" name="nama" class="form-control form-control-sm" value="<?= esc($currentUnitObj['nama'] ?? ''); ?>" required>
                </div>
                <div class="col-md-8">
                    <label class="form-label fw-semibold small text-dark">Deskripsi Sub-Unit</label>
                    <input type="text" name="deskripsi" class="form-control form-control-sm" value="<?= esc($currentUnitObj['deskripsi'] ?? ''); ?>">
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold small text-dark">Teks Pengantar / Kotak Penjelasan (Opsional, seperti pada DKP &amp; DPOP)</label>
                    <textarea name="intro_text" rows="2" class="form-control form-control-sm" placeholder="Kosongkan jika tidak perlu kotak pengantar..."><?= esc($currentUnitObj['intro_text'] ?? ''); ?></textarea>
                </div>
                <div class="col-12 text-end">
                    <button type="submit" class="btn btn-sm btn-primary rounded-pill px-4 fw-semibold" style="background-color: var(--inkindo-blue); border: none;">
                        <i class="bi bi-save me-1"></i> Simpan Info Sub-Unit
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Kelompok / Bidang / Badan dalam Sub-Unit -->
<div class="card border-0 shadow-sm mb-4" style="border-radius: 16px;">
    <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
            <div>
                <h6 class="fw-bold mb-0 text-dark">
                    <i class="bi bi-folder2-open text-primary me-2"></i>Kelompok / Bidang / Badan di <?= esc($currentUnitObj['nama']); ?>
                </h6>
                <small class="text-muted">Digunakan untuk mengelompokkan pengurus (misalnya Pimpinan Harian, Bidang 1–12, Badan I–V, atau grup baru untuk DPPL/Komite/Setprov).</small>
            </div>
            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-semibold" onclick="openGroupModal()">
                <i class="bi bi-plus-circle me-1"></i> Tambah Kelompok / Bidang / Badan
            </button>
        </div>

        <?php if (empty($unitGroups)): ?>
            <div class="text-muted small fst-italic">
                Belum ada kelompok khusus pada sub-unit ini. Anda tetap dapat langsung klik <strong>+ Tambah Orang</strong> di bawah—sistem akan otomatis membuatkan kelompok default.
            </div>
        <?php else: ?>
            <div class="d-flex flex-wrap gap-2">
                <?php foreach ($unitGroups as $grp): ?>
                    <?php
                        $grpJson = htmlspecialchars(json_encode([
                            'id'            => (int) $grp['id'],
                            'unit_slug'     => (string) $grp['unit_slug'],
                            'nama_kelompok' => (string) $grp['nama_kelompok'],
                            'kode_badge'    => (string) ($grp['kode_badge'] ?? ''),
                            'tipe_tampilan' => (string) ($grp['tipe_tampilan'] ?? 'officers'),
                            'section_label' => (string) ($grp['section_label'] ?? ''),
                            'is_full_width' => (int) ($grp['is_full_width'] ?? 0),
                            'urutan'        => (int) ($grp['urutan'] ?? 1),
                            'is_active'     => (int) ($grp['is_active'] ?? 1),
                        ]), ENT_QUOTES, 'UTF-8');
                    ?>
                    <div class="group-chip">
                        <?php if (!empty($grp['kode_badge'])): ?>
                            <span class="badge bg-primary rounded-pill"><?= esc($grp['kode_badge']); ?></span>
                        <?php endif; ?>
                        <span class="fw-semibold text-dark"><?= esc($grp['nama_kelompok']); ?></span>
                        <span class="text-muted" style="font-size: 11px;">(<?= esc($grp['tipe_tampilan']); ?>)</span>
                        <button type="button" class="btn btn-sm btn-link text-warning p-0 ms-1" onclick='openGroupModal(<?= $grpJson; ?>)' title="Edit Kelompok">
                            <i class="bi bi-pencil-square"></i>
                        </button>
                        <a href="/cms/tentang/group/delete/<?= (int) $grp['id']; ?>"
                           class="text-danger ms-1"
                           onclick="return confirm('Hapus kelompok \"<?= esc($grp['nama_kelompok']); ?>\" beserta seluruh pengurus di dalamnya?');"
                           title="Hapus Kelompok">
                            <i class="bi bi-x-circle"></i>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Tabel CRUD Pengurus & Jabatan -->
<div class="card border-0 shadow-sm mb-4" style="border-radius: 16px;">
    <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-3 pb-3 border-bottom">
            <div>
                <h5 class="fw-bold mb-1 text-dark">
                    <i class="bi bi-table text-primary me-2"></i>Tabel CRUD Pengurus &amp; Jabatan — <?= esc($currentUnitObj['nama']); ?>
                </h5>
                <p class="text-muted small mb-0">
                    Tambah orang baru, hapus orang dari baris tabel, atau edit langsung <strong>teks jabatan</strong>, <strong>nama pengurus</strong>, dan urutan lalu klik <strong>Simpan Perubahan Tabel</strong>.
                </p>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 fw-semibold" style="background-color: var(--inkindo-blue); border: none;" data-bs-toggle="modal" data-bs-target="#addMemberModal">
                    <i class="bi bi-person-plus-fill me-1"></i> Tambah Orang
                </button>
                <?php if (!empty($unitMembers)): ?>
                    <button type="submit" form="crudMembersForm" class="btn btn-sm btn-success rounded-pill px-3 fw-semibold">
                        <i class="bi bi-check2-all me-1"></i> Simpan Perubahan Tabel
                    </button>
                <?php endif; ?>
            </div>
        </div>

        <?php if (empty($unitMembers)): ?>
            <div class="text-center py-5 px-3 rounded-3" style="background: #f8fafc; border: 1px dashed #cbd5e1;">
                <i class="bi bi-people text-secondary d-block mb-2" style="font-size: 2.4rem;"></i>
                <h6 class="fw-bold text-dark mb-1">Belum Ada Data Pengurus di <?= esc($currentUnitObj['nama']); ?></h6>
                <p class="text-muted small mb-3">
                    Di halaman publik <code>tentang.html</code>, sub-unit ini saat ini menampilkan status <em>Data Belum Tersedia</em>. Tambahkan pengurus untuk langsung menampilkannya.
                </p>
                <button type="button" class="btn btn-primary rounded-pill px-4 fw-semibold" style="background-color: var(--inkindo-blue); border: none;" data-bs-toggle="modal" data-bs-target="#addMemberModal">
                    <i class="bi bi-person-plus-fill me-1"></i> Tambah Orang Pertama
                </button>
            </div>
        <?php else: ?>
            <!-- Filter Cepat Tabel -->
            <div class="row g-2 mb-3">
                <div class="col-md-5">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" id="crudSearchInput" class="form-control" placeholder="Cari nama pengurus atau teks jabatan...">
                    </div>
                </div>
                <div class="col-md-4">
                    <select id="crudGroupFilter" class="form-select form-select-sm">
                        <option value="">Semua Kelompok / Bidang / Badan</option>
                        <?php foreach ($unitGroups as $grp): ?>
                            <option value="<?= (int) $grp['id']; ?>"><?= esc($grp['nama_kelompok']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3 text-md-end small text-muted align-self-center">
                    Total Baris: <strong class="text-dark" id="crudVisibleCount"><?= count($unitMembers); ?></strong>
                </div>
            </div>

            <div class="alert alert-light border d-flex flex-wrap align-items-center justify-content-between gap-2 py-2 px-3 mb-3 small">
                <div class="d-flex flex-wrap gap-3 align-items-center">
                    <span><span class="badge bg-primary-subtle text-primary border border-primary-subtle me-1">Sorot</span> Memberi sorotan kartu biru pada website publik <code>tentang.html</code></span>
                    <span><span class="badge bg-success-subtle text-success border border-success-subtle me-1">Aktif</span> Menampilkan pengurus di website publik (hilangkan centang untuk sembunyikan sementara)</span>
                </div>
                <span class="text-muted fst-italic">Setelah mengubah isi tabel atau centang, klik <strong>Simpan Perubahan Tabel</strong>.</span>
            </div>

            <form action="/cms/tentang/member/updateTable" method="post" id="crudMembersForm">
                <input type="hidden" name="unit_slug" value="<?= esc($activeUnit); ?>">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="min-width: 860px;">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 78px;">Urut</th>
                                <th style="width: 220px;">Kelompok / Bidang</th>
                                <th style="width: 240px;">Teks Jabatan (Editable)</th>
                                <th>Nama Pengurus (Editable)</th>
                                <th style="width: 175px;" class="text-center">Opsi Tampil</th>
                                <th style="width: 90px;" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="crudMembersTbody">
                            <?php foreach ($unitMembers as $m): ?>
                                <?php
                                    $mId = (int) $m['id'];
                                    $mGroupId = (int) ($m['group_id'] ?? 0);
                                    $mActive = (int) ($m['is_active'] ?? 1) === 1;
                                    $mHighlight = (int) ($m['is_highlight'] ?? 0) === 1;
                                    $searchKey = strtolower(trim(($m['jabatan'] ?? '') . ' ' . ($m['nama'] ?? '') . ' ' . ($groupMap[$mGroupId]['nama_kelompok'] ?? '')));
                                    $rowStateClass = !$mActive ? 'crud-row-inactive' : ($mHighlight ? 'crud-row-highlighted' : '');
                                ?>
                                <tr class="crud-member-row <?= $rowStateClass; ?>"
                                    data-group-id="<?= $mGroupId; ?>"
                                    data-search="<?= esc($searchKey); ?>">
                                    <td>
                                        <input type="number"
                                               name="members[<?= $mId; ?>][urutan]"
                                               value="<?= (int) ($m['urutan'] ?? 1); ?>"
                                               min="1"
                                               class="form-control form-control-sm crud-table-input text-center">
                                    </td>
                                    <td>
                                        <select name="members[<?= $mId; ?>][group_id]" class="form-select form-select-sm crud-table-input">
                                            <?php foreach ($unitGroups as $grp): ?>
                                                <option value="<?= (int) $grp['id']; ?>" <?= ((int) $grp['id'] === $mGroupId) ? 'selected' : ''; ?>>
                                                    <?= esc($grp['nama_kelompok']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <input type="hidden" name="members[<?= $mId; ?>][keterangan_badge]" value="<?= esc($m['keterangan_badge'] ?? ''); ?>">
                                    </td>
                                    <td>
                                        <input type="text"
                                               name="members[<?= $mId; ?>][jabatan]"
                                               value="<?= esc($m['jabatan']); ?>"
                                               class="form-control form-control-sm fw-semibold crud-table-input"
                                               placeholder="Jabatan..."
                                               required>
                                    </td>
                                    <td>
                                        <input type="text"
                                               name="members[<?= $mId; ?>][nama]"
                                               value="<?= esc($m['nama']); ?>"
                                               class="form-control form-control-sm crud-table-input"
                                               placeholder="Nama lengkap & gelar..."
                                               required>
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-center gap-3">
                                            <div class="form-check mb-0" title="Sorot kartu dengan aksen biru di website publik">
                                                <input class="form-check-input chk-sorot" type="checkbox" name="members[<?= $mId; ?>][is_highlight]" value="1" id="hl_<?= $mId; ?>" <?= $mHighlight ? 'checked' : ''; ?>>
                                                <label class="form-check-label small" for="hl_<?= $mId; ?>">Sorot</label>
                                            </div>
                                            <div class="form-check mb-0" title="Tampilkan atau sembunyikan sementara di website publik">
                                                <input class="form-check-input chk-aktif" type="checkbox" name="members[<?= $mId; ?>][is_active]" value="1" id="act_<?= $mId; ?>" <?= $mActive ? 'checked' : ''; ?>>
                                                <label class="form-check-label small" for="act_<?= $mId; ?>">Aktif</label>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <a href="/cms/tentang/member/delete/<?= $mId; ?>"
                                           class="btn btn-sm btn-outline-danger rounded-pill px-2.5"
                                           onclick="return confirm('Hapus pengurus \"<?= esc($m['nama']); ?>\" (<?= esc($m['jabatan']); ?>) dari tabel?');"
                                           title="Kurangi / Hapus Orang">
                                            <i class="bi bi-trash"></i> Hapus
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top flex-wrap gap-2">
                    <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-semibold" data-bs-toggle="modal" data-bs-target="#addMemberModal">
                        <i class="bi bi-person-plus-fill me-1"></i> Tambah Orang
                    </button>
                    <button type="submit" class="btn btn-success rounded-pill px-4 fw-semibold">
                        <i class="bi bi-check2-all me-1"></i> Simpan Perubahan Tabel <?= esc($currentUnitObj['nama']); ?>
                    </button>
                </div>
            </form>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>

<!-- ================= MODAL TAMBAH ORANG (STRUKTUR ORGANISASI) ================= -->
<div class="modal fade" id="addMemberModal" tabindex="-1" aria-labelledby="addMemberModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="/cms/tentang/member/add" method="post" class="modal-content border-0 shadow-lg modal-scrollable-form" style="border-radius: 16px;">
            <div class="modal-header border-bottom px-4 py-3" style="background: #f8fafc;">
                <div>
                    <h5 class="modal-title fw-bold text-dark mb-0" id="addMemberModalLabel">
                        <i class="bi bi-person-plus-fill text-primary me-2"></i>Tambah Orang ke <?= esc($currentUnitObj['nama']); ?>
                    </h5>
                    <small class="text-muted">Tambahkan pengurus beserta teks jabatan ke sub-unit ini.</small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <input type="hidden" name="unit_slug" value="<?= esc($activeUnit); ?>">

                <div class="mb-3">
                    <label class="form-label fw-semibold small text-dark mb-1">Kelompok / Bidang / Badan</label>
                    <?php if (!empty($unitGroups)): ?>
                        <select name="group_id" class="form-select">
                            <?php foreach ($unitGroups as $grp): ?>
                                <option value="<?= (int) $grp['id']; ?>"><?= esc($grp['nama_kelompok']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    <?php else: ?>
                        <input type="hidden" name="group_id" value="0">
                        <div class="form-control bg-light text-muted small">
                            Otomatis dibuatkan kelompok: <strong>Daftar Pengurus <?= esc($currentUnitObj['nama']); ?></strong>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold small text-dark mb-1">Teks Jabatan <span class="text-danger">*</span></label>
                    <input type="text" name="jabatan" class="form-control" placeholder="Contoh: Ketua, Wakil Ketua, Sekretaris, Bendahara, Anggota" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold small text-dark mb-1">Nama Pengurus &amp; Gelar <span class="text-danger">*</span></label>
                    <input type="text" name="nama" class="form-control" placeholder="Contoh: Ir. Budi Santoso, M.T." required>
                </div>

                <div class="row g-3">
                    <div class="col-4">
                        <label class="form-label fw-semibold small text-dark mb-1">Urutan</label>
                        <input type="number" name="urutan" class="form-control" value="0" min="0">
                        <small class="text-muted" style="font-size: 11px;">0 = otomatis akhir</small>
                    </div>
                    <div class="col-8">
                        <label class="form-label fw-semibold small text-dark mb-1">Badge Keterangan (Opsional)</label>
                        <input type="text" name="keterangan_badge" class="form-control" placeholder="Contoh: Pimpinan Harian">
                    </div>
                </div>

                <div class="d-flex gap-4 mt-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_highlight" value="1" id="add_hl">
                        <label class="form-check-label small fw-semibold" for="add_hl">Sorot Kartu (Highlight)</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" id="add_act" checked>
                        <label class="form-check-label small fw-semibold" for="add_act">Tampilkan (Aktif)</label>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top px-4 py-3 bg-light">
                <button type="button" class="btn btn-light border rounded-pill px-4 fw-semibold" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary rounded-pill px-4 fw-semibold" style="background-color: var(--inkindo-blue); border: none;">
                    <i class="bi bi-check2-circle me-1"></i> Tambahkan Orang
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ================= MODAL TAMBAH / EDIT KELOMPOK / BIDANG / BADAN ================= -->
<div class="modal fade" id="groupModal" tabindex="-1" aria-labelledby="groupModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="/cms/tentang/group/save" method="post" class="modal-content border-0 shadow-lg modal-scrollable-form" style="border-radius: 16px;">
            <div class="modal-header border-bottom px-4 py-3" style="background: #f8fafc;">
                <div>
                    <h5 class="modal-title fw-bold text-dark mb-0" id="groupModalLabel">
                        <i class="bi bi-folder-plus text-primary me-2"></i>Kelompok / Bidang / Badan
                    </h5>
                    <small class="text-muted">Atur bagian pengelompokan di dalam sub-unit <?= esc($currentUnitObj['nama']); ?>.</small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <input type="hidden" name="group_id" id="gm_group_id" value="0">
                <input type="hidden" name="unit_slug" value="<?= esc($activeUnit); ?>">

                <div class="mb-3">
                    <label class="form-label fw-semibold small text-dark mb-1">Nama Kelompok / Bidang / Badan <span class="text-danger">*</span></label>
                    <input type="text" name="nama_kelompok" id="gm_nama_kelompok" class="form-control" placeholder="Contoh: 13) Bidang Baru atau Komite Tetap..." required>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold small text-dark mb-1">Kode / Nomor Badge</label>
                        <input type="text" name="kode_badge" id="gm_kode_badge" class="form-control" placeholder="Cth: I, II, 1, 2">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold small text-dark mb-1">Gaya Tampilan</label>
                        <select name="tipe_tampilan" id="gm_tipe_tampilan" class="form-select">
                            <option value="officers">Daftar Kartu (Officers)</option>
                            <option value="leader">Kartu Ketua Utama (Leader)</option>
                            <option value="bidang">Kartu Grid Bidang</option>
                            <option value="badan">Kartu Badan-Badan</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold small text-dark mb-1">Urutan</label>
                        <input type="number" name="urutan" id="gm_urutan" class="form-control" value="<?= count($unitGroups) + 1; ?>" min="1">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold small text-dark mb-1">Judul Seksi Pengelompok (Opsional)</label>
                    <input type="text" name="section_label" id="gm_section_label" class="form-control" placeholder="Contoh: Bidang Kerja DPPH (kosongkan jika tidak perlu)">
                </div>

                <div class="d-flex gap-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_full_width" value="1" id="gm_is_full_width">
                        <label class="form-check-label small fw-semibold" for="gm_is_full_width">Lebar Penuh (khusus Kartu Badan)</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" id="gm_is_active" checked>
                        <label class="form-check-label small fw-semibold" for="gm_is_active">Aktif</label>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top px-4 py-3 bg-light">
                <button type="button" class="btn btn-light border rounded-pill px-4 fw-semibold" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary rounded-pill px-4 fw-semibold" style="background-color: var(--inkindo-blue); border: none;">
                    <i class="bi bi-check2-circle me-1"></i> Simpan Kelompok
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ================= MODAL CROP GAMBAR PROFIL ORGANISASI ================= -->
<div class="modal fade" id="tentangCropModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
            <div class="modal-header border-bottom px-4 py-3" style="background: #f8fafc;">
                <div>
                    <h5 class="modal-title fw-bold text-dark mb-0" id="tentangCropModalTitle">
                        <i class="bi bi-crop text-primary me-2"></i>Sesuaikan &amp; Crop Gambar
                    </h5>
                    <small class="text-muted">Pilih rasio bebas untuk menggeser tepi kiri, kanan, atas, atau bawah secara independen, atau pakai foto utuh tanpa potong.</small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                    <div class="btn-group btn-group-sm" role="group" aria-label="Preset Rasio Crop">
                        <button type="button" class="btn btn-outline-primary active" id="cropRatioFreeBtn" onclick="setTentangCropRatio(NaN, this)">
                            <i class="bi bi-arrows-fullscreen me-1"></i> Rasio Bebas (Custom)
                        </button>
                        <button type="button" class="btn btn-outline-primary" id="cropRatioPresetBtn" style="display: none;" onclick="setTentangCropRatio(activeDefaultCropRatio, this)">
                            Rasio Bawaan Seksi
                        </button>
                        <button type="button" class="btn btn-outline-primary" id="cropRatio43Btn" onclick="setTentangCropRatio(4/3, this)">4 : 3</button>
                        <button type="button" class="btn btn-outline-primary" id="cropRatio169Btn" onclick="setTentangCropRatio(16/9, this)">16 : 9</button>
                        <button type="button" class="btn btn-outline-primary" id="cropRatio11Btn" onclick="setTentangCropRatio(1, this)">1 : 1</button>
                    </div>

                    <div class="btn-group btn-group-sm" role="group" aria-label="Kontrol Gambar">
                        <button type="button" class="btn btn-light border" onclick="tentangCropAction('zoomIn')" title="Perbesar (Zoom In)"><i class="bi bi-zoom-in"></i></button>
                        <button type="button" class="btn btn-light border" onclick="tentangCropAction('zoomOut')" title="Perkecil (Zoom Out)"><i class="bi bi-zoom-out"></i></button>
                        <button type="button" class="btn btn-light border" onclick="tentangCropAction('rotateLeft')" title="Putar Kiri"><i class="bi bi-arrow-counterclockwise"></i></button>
                        <button type="button" class="btn btn-light border" onclick="tentangCropAction('rotateRight')" title="Putar Kanan"><i class="bi bi-arrow-clockwise"></i></button>
                        <button type="button" class="btn btn-light border fw-semibold" onclick="tentangCropAction('full')" title="Pilih penuh seluruh foto">
                            <i class="bi bi-aspect-ratio me-1"></i> Pilih Utuh (100%)
                        </button>
                    </div>
                </div>

                <div class="cropper-stage-wrap">
                    <div class="cropper-stage-inner">
                        <img id="tentangCropImgTarget" src="" alt="Crop Preview" style="max-width: 100%; display: block;">
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top px-4 py-3 bg-light justify-content-between flex-wrap gap-2">
                <button type="button" class="btn btn-outline-secondary rounded-pill px-3 fw-semibold" onclick="useUncroppedTentangImage()">
                    <i class="bi bi-image me-1"></i> Pakai Foto Utuh (Tanpa Crop)
                </button>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-light border rounded-pill px-4 fw-semibold" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary rounded-pill px-4 fw-semibold" style="background-color: var(--inkindo-blue); border: none;" onclick="applyTentangCrop()">
                        <i class="bi bi-check-lg me-1"></i> Gunakan Hasil Crop
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.js"></script>
<script>
    function renumberDynamicList(containerId, padZero = false) {
        const container = document.getElementById(containerId);
        if (!container) return;
        const badges = container.querySelectorAll('.row-num-badge');
        badges.forEach((b, idx) => {
            const num = idx + 1;
            b.textContent = padZero ? String(num).padStart(2, '0') : String(num);
        });
    }

    function addDynamicListItem(containerId, inputName, placeholderText) {
        const container = document.getElementById(containerId);
        if (!container) return;
        const padZero = containerId === 'kodeEtikListContainer';
        const wrapper = document.createElement('div');
        wrapper.className = 'input-group dynamic-list-row';
        wrapper.innerHTML = `
            <span class="input-group-text bg-light fw-bold small row-num-badge"></span>
            <textarea name="${inputName}" rows="2" class="form-control form-control-sm" placeholder="${placeholderText}"></textarea>
            <button type="button" class="btn btn-outline-danger btn-sm" title="Hapus Poin">
                <i class="bi bi-trash"></i>
            </button>
        `;
        const delBtn = wrapper.querySelector('button');
        delBtn.addEventListener('click', () => removeDynamicListItem(delBtn, containerId, padZero));
        container.appendChild(wrapper);
        renumberDynamicList(containerId, padZero);
    }

    function removeDynamicListItem(btnEl, containerId, padZero = false) {
        const container = document.getElementById(containerId);
        if (!container) return;
        const rows = container.querySelectorAll('.dynamic-list-row');
        if (rows.length <= 1) {
            const ta = rows[0].querySelector('textarea');
            if (ta) ta.value = '';
            return;
        }
        const row = btnEl.closest('.dynamic-list-row');
        if (row) {
            row.remove();
            renumberDynamicList(containerId, padZero);
        }
    }

    function handleTentangFileSelect(fileInput, previewImgId, croppedHiddenId) {
        const file = fileInput.files && fileInput.files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = function (e) {
            const prev = document.getElementById(previewImgId);
            if (prev) {
                prev.src = e.target.result;
                prev.setAttribute('data-raw-src', e.target.result);
            }
            const hidden = document.getElementById(croppedHiddenId);
            if (hidden) hidden.value = '';
        };
        reader.readAsDataURL(file);
    }

    function resetTentangImage(defaultRelPath, previewImgId, existingHiddenId, croppedHiddenId, fileInputId) {
        const defaultUrl = '/cms/webAsset/' + defaultRelPath;
        const prev = document.getElementById(previewImgId);
        if (prev) {
            prev.src = defaultUrl;
            prev.setAttribute('data-raw-src', defaultUrl);
        }
        const ex = document.getElementById(existingHiddenId);
        if (ex) ex.value = defaultRelPath;
        const crop = document.getElementById(croppedHiddenId);
        if (crop) crop.value = '';
        const fi = document.getElementById(fileInputId);
        if (fi) fi.value = '';
    }

    let tentangCropper = null;
    let activeCropPreviewId = null;
    let activeCropHiddenId = null;
    let activeDefaultCropRatio = NaN;

    function updateCropRatioButtons(activeBtnId) {
        ['cropRatioFreeBtn', 'cropRatioPresetBtn', 'cropRatio43Btn', 'cropRatio169Btn', 'cropRatio11Btn'].forEach((id) => {
            const btn = document.getElementById(id);
            if (btn) btn.classList.toggle('active', id === activeBtnId);
        });
    }

    function setTentangCropRatio(ratio, btnEl = null) {
        if (btnEl && btnEl.id) {
            updateCropRatioButtons(btnEl.id);
        }
        if (tentangCropper) {
            tentangCropper.setAspectRatio(Number.isFinite(ratio) && ratio > 0 ? ratio : NaN);
        }
    }

    function tentangCropAction(action) {
        if (!tentangCropper) return;
        if (action === 'zoomIn') tentangCropper.zoom(0.15);
        else if (action === 'zoomOut') tentangCropper.zoom(-0.15);
        else if (action === 'rotateLeft') tentangCropper.rotate(-90);
        else if (action === 'rotateRight') tentangCropper.rotate(90);
        else if (action === 'full') {
            setTentangCropRatio(NaN, document.getElementById('cropRatioFreeBtn'));
            tentangCropper.reset();
            const imgData = tentangCropper.getCanvasData();
            if (imgData && imgData.width && imgData.height) {
                tentangCropper.setCropBoxData({
                    left: imgData.left,
                    top: imgData.top,
                    width: imgData.width,
                    height: imgData.height,
                });
            }
        }
    }

    function useUncroppedTentangImage() {
        if (activeCropPreviewId && activeCropHiddenId) {
            const prev = document.getElementById(activeCropPreviewId);
            const hidden = document.getElementById(activeCropHiddenId);
            if (prev) {
                const rawSrc = prev.getAttribute('data-raw-src');
                if (rawSrc) prev.src = rawSrc;
            }
            if (hidden) hidden.value = '';
        }
        const modalEl = document.getElementById('tentangCropModal');
        bootstrap.Modal.getOrCreateInstance(modalEl).hide();
    }

    function openTentangCropper(previewImgId, croppedHiddenId, aspectRatio = null, titleText = 'Sesuaikan & Crop Gambar') {
        const prev = document.getElementById(previewImgId);
        const modalEl = document.getElementById('tentangCropModal');
        const targetImg = document.getElementById('tentangCropImgTarget');
        const titleEl = document.getElementById('tentangCropModalTitle');
        const presetBtn = document.getElementById('cropRatioPresetBtn');
        if (!prev || !modalEl || !targetImg) return;

        activeCropPreviewId = previewImgId;
        activeCropHiddenId = croppedHiddenId;
        activeDefaultCropRatio = (typeof aspectRatio === 'number' && Number.isFinite(aspectRatio) && aspectRatio > 0) ? aspectRatio : NaN;

        if (titleEl) {
            titleEl.innerHTML = '<i class="bi bi-crop text-primary me-2"></i>' + titleText;
        }
        if (presetBtn) {
            presetBtn.style.display = Number.isFinite(activeDefaultCropRatio) ? '' : 'none';
        }
        updateCropRatioButtons(Number.isFinite(activeDefaultCropRatio) ? 'cropRatioPresetBtn' : 'cropRatioFreeBtn');

        const rawSrc = prev.getAttribute('data-raw-src') || prev.src;
        targetImg.onload = null;
        targetImg.src = rawSrc;

        modalEl.addEventListener('hidden.bs.modal', function onHidden() {
            modalEl.removeEventListener('hidden.bs.modal', onHidden);
            targetImg.onload = null;
            if (tentangCropper) {
                tentangCropper.destroy();
                tentangCropper = null;
            }
            activeCropPreviewId = null;
            activeCropHiddenId = null;
        });

        const mountCropper = () => {
            if (tentangCropper) {
                tentangCropper.destroy();
            }
            tentangCropper = new Cropper(targetImg, {
                aspectRatio: activeDefaultCropRatio,
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

        const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
        modalEl.addEventListener('shown.bs.modal', function onShown() {
            modalEl.removeEventListener('shown.bs.modal', onShown);
            if (targetImg.complete && targetImg.naturalWidth > 0) {
                mountCropper();
            } else {
                targetImg.onload = () => {
                    targetImg.onload = null;
                    mountCropper();
                };
            }
        });
        modal.show();
    }

    function applyTentangCrop() {
        if (!tentangCropper || !activeCropPreviewId || !activeCropHiddenId) return;
        const targetImg = document.getElementById('tentangCropImgTarget');
        const srcLower = targetImg ? (targetImg.src || '').toLowerCase() : '';
        const isPng = srcLower.startsWith('data:image/png') || srcLower.endsWith('.png');
        const mime = isPng ? 'image/png' : 'image/jpeg';

        const canvas = tentangCropper.getCroppedCanvas({ maxWidth: 2000, maxHeight: 2000 });
        if (canvas) {
            const dataUrl = isPng ? canvas.toDataURL('image/png') : canvas.toDataURL(mime, 0.92);
            const prev = document.getElementById(activeCropPreviewId);
            const hidden = document.getElementById(activeCropHiddenId);
            if (prev) prev.src = dataUrl;
            if (hidden) hidden.value = dataUrl;
        }
        const modalEl = document.getElementById('tentangCropModal');
        bootstrap.Modal.getOrCreateInstance(modalEl).hide();
    }

    function openGroupModal(data = null) {
        document.getElementById('gm_group_id').value = data ? (data.id || 0) : 0;
        document.getElementById('gm_nama_kelompok').value = data ? (data.nama_kelompok || '') : '';
        document.getElementById('gm_kode_badge').value = data ? (data.kode_badge || '') : '';
        document.getElementById('gm_tipe_tampilan').value = data ? (data.tipe_tampilan || 'officers') : '<?= ($activeUnit === "badan") ? "badan" : "officers"; ?>';
        document.getElementById('gm_section_label').value = data ? (data.section_label || '') : '';
        document.getElementById('gm_urutan').value = data ? (data.urutan || 1) : <?= count($unitGroups) + 1; ?>;
        document.getElementById('gm_is_full_width').checked = data ? (Number(data.is_full_width) === 1) : false;
        document.getElementById('gm_is_active').checked = data ? (Number(data.is_active) === 1) : true;
        bootstrap.Modal.getOrCreateInstance(document.getElementById('groupModal')).show();
    }

    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('crudSearchInput');
        const groupFilter = document.getElementById('crudGroupFilter');
        const countEl     = document.getElementById('crudVisibleCount');
        const rows        = document.querySelectorAll('.crud-member-row');

        function filterCrudTable() {
            const q = (searchInput ? searchInput.value : '').toLowerCase().trim();
            const grpVal = groupFilter ? groupFilter.value : '';
            let visible = 0;

            rows.forEach((tr) => {
                const rowGrp = tr.getAttribute('data-group-id') || '';
                const jabatanInput = tr.querySelector('input[name*="[jabatan]"]');
                const namaInput    = tr.querySelector('input[name*="[nama]"]');
                const liveText = ((jabatanInput ? jabatanInput.value : '') + ' ' + (namaInput ? namaInput.value : '') + ' ' + (tr.getAttribute('data-search') || '')).toLowerCase();

                const matchGrp = !grpVal || rowGrp === grpVal;
                const matchTxt = !q || liveText.includes(q);
                const show = matchGrp && matchTxt;
                tr.style.display = show ? '' : 'none';
                if (show) visible++;
            });

            if (countEl) countEl.textContent = String(visible);
        }

        if (searchInput) searchInput.addEventListener('input', filterCrudTable);
        if (groupFilter) groupFilter.addEventListener('change', filterCrudTable);

        rows.forEach((tr) => {
            const sorotCb = tr.querySelector('.chk-sorot');
            const aktifCb = tr.querySelector('.chk-aktif');
            const syncRowVisual = () => {
                const isAct = aktifCb ? aktifCb.checked : true;
                const isSor = sorotCb ? sorotCb.checked : false;
                tr.classList.toggle('crud-row-inactive', !isAct);
                tr.classList.toggle('crud-row-highlighted', isAct && isSor);
            };
            if (sorotCb) sorotCb.addEventListener('change', syncRowVisual);
            if (aktifCb) aktifCb.addEventListener('change', syncRowVisual);
        });
    });
</script>
<?= $this->endSection() ?>
