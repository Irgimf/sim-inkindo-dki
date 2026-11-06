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

        <?php
            // Kelompokkan allItems berdasarkan tipe kategori untuk dropdown yang rapi
            $groupedForDropdown = [];
            foreach ($allItems as $row) {
                $t = $row['tipe'] ?? 'rilis';
                $groupedForDropdown[$t][] = $row;
            }

            $renderCustomDropdown = function($slotNum, $slotItem, $name) use ($groupedForDropdown, $tipeMap) {
        $currentId = (int)($slotItem['id'] ?? 0);
        $currentLabel = '-- Pilih Konten (Kosongkan Slot) --';
        $currentFullTitle = $currentLabel;
        if ($slotItem) {
            $tgl = !empty($slotItem['tanggal_label']) ? '[' . $slotItem['tanggal_label'] . '] ' : '';
            $currentFullTitle = $tgl . $slotItem['judul'];
            $currentLabel = $tgl . mb_strimwidth($slotItem['judul'], 0, 42, '...');
        }

        ob_start();
        ?>
        <div class="custom-slot-dropdown position-relative mb-2" data-slot="<?= $slotNum; ?>">
            <input type="hidden" name="<?= esc($name); ?>" id="input_slot_<?= $slotNum; ?>" value="<?= $currentId; ?>">
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
                       href="javascript:void(0)" data-id="0" data-label="-- Pilih Konten (Kosongkan Slot) --" title="-- Kosongkan Slot Ini --">
                        <i class="bi bi-x-circle me-1"></i> -- Kosongkan Slot Ini --
                    </a>
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
        ?>

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

<style>
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
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.custom-slot-dropdown').forEach(function(wrap) {
        var input = wrap.querySelector('input[type="hidden"]');
        var label = wrap.querySelector('.slot-display-label');
        var triggerBtn = wrap.querySelector('.slot-trigger-btn');
        var searchInput = wrap.querySelector('.slot-filter-input');
        var options = wrap.querySelectorAll('.slot-option-item');
        var noResults = wrap.querySelector('.slot-no-results');
        var groupBlocks = wrap.querySelectorAll('.slot-group-block');

        // Selection
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

                // Close bootstrap dropdown
                var bsDropdown = bootstrap.Dropdown.getInstance(triggerBtn);
                if (bsDropdown) {
                    bsDropdown.hide();
                }
            });
        });

        // Search filter inside dropdown
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

            // Prevent dropdown from closing on input interaction
            ['click', 'keydown', 'keyup', 'focus'].forEach(function(evName) {
                searchInput.addEventListener(evName, function(e) {
                    e.stopPropagation();
                });
            });
        }

        // Auto focus search on dropdown open
        wrap.addEventListener('shown.bs.dropdown', function() {
            if (searchInput) {
                searchInput.focus();
            }
        });

        // Reset filter when dropdown closes
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

