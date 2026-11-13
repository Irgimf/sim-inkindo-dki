<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<?php
    $webBaseUrl = rtrim(config('App')->webProfileURL, '/');
    $resolvePreview = static function (string $path) use ($webBaseUrl): string {
        $clean = ltrim(str_replace('\\', '/', $path), '/');
        if (strpos($clean, 'cms_uploads/') === 0) {
            return '/' . $clean;
        }
        return $webBaseUrl . '/' . $clean;
    };

    // Kelompokkan item berdasarkan nama halaman (mempertahankan urutan)
    $groupedPages = [];
    foreach ($items as $item) {
        $pName = (string) ($item['page_name'] ?? 'Lainnya');
        if (! isset($groupedPages[$pName])) {
            $groupedPages[$pName] = [];
        }
        $groupedPages[$pName][] = $item;
    }
?>

<div class="d-flex justify-content-between align-items-center mb-4 mt-2 flex-wrap gap-3">
    <div>
        <h3 class="fw-bold mb-1" style="color: var(--inkindo-blue);">Admin CMS — Background &amp; Gambar Halaman</h3>
        <p class="text-muted small mb-0">Kelola gambar latar belakang (<em>hero</em> &amp; <em>section</em>) untuk seluruh halaman di <code>inkindo-dki-web</code> (kecuali Halaman Utama).</p>
    </div>
    <a href="<?= esc($webBaseUrl); ?>" target="_blank" class="btn btn-outline-primary rounded-pill px-3 fw-semibold">
        <i class="bi bi-box-arrow-up-right me-1"></i> Lihat Web Publik
    </a>
</div>

<?php if (session()->getFlashdata('pesan')): ?>
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3" role="alert">
        <i class="bi bi-check-circle-fill me-1"></i> <?= esc(session()->getFlashdata('pesan')); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-1"></i> <?= esc(session()->getFlashdata('error')); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php $pageNo = 1; foreach ($groupedPages as $pageName => $pageItems): ?>
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3 pb-2 border-bottom">
                <h5 class="fw-bold mb-0" style="color: var(--inkindo-blue);">
                    <?= $pageNo++; ?>. Halaman <?= esc($pageName); ?>
                </h5>
                <span class="badge badge-soft-primary rounded-pill px-3 py-1" style="font-size: 11.5px;">
                    <?= count($pageItems); ?> Gambar
                </span>
            </div>

            <div class="row g-4">
                <?php foreach ($pageItems as $item): ?>
                    <?php
                        $previewUrl = $resolvePreview((string) $item['image_path']);
                        $isCustom   = ($item['image_path'] ?? '') !== ($item['default_path'] ?? '');
                    ?>
                    <div class="col-md-6 col-xl-4">
                        <div class="card border shadow-none rounded-4 h-100 overflow-hidden" style="border-color: #e2e8f0 !important;">
                            <div style="position: relative; height: 170px; background: #0f172a; overflow: hidden;">
                                <img src="<?= esc($previewUrl); ?>"
                                     alt="<?= esc($item['page_name']); ?> - <?= esc($item['section_label']); ?>"
                                     style="width: 100%; height: 100%; object-fit: cover; opacity: 0.94;">
                                <?php if ($isCustom): ?>
                                    <span class="badge bg-success rounded-pill px-2 py-1"
                                          style="position: absolute; top: 12px; right: 12px; font-size: 10.5px;">
                                        Custom
                                    </span>
                                <?php endif; ?>
                            </div>

                            <div class="card-body p-3 d-flex flex-column justify-content-between">
                                <div>
                                    <h6 class="fw-bold text-dark mb-1"><?= esc($item['section_label']); ?></h6>
                                    <p class="text-muted small mb-3 text-truncate" title="<?= esc($item['image_path']); ?>">
                                        File: <code><?= esc($item['image_path']); ?></code>
                                    </p>
                                </div>

                                <form action="/cms/backgrounds/save" method="post" enctype="multipart/form-data">
                                    <input type="hidden" name="id" value="<?= (int) $item['id']; ?>">
                                    <div class="mb-2">
                                        <input type="file"
                                               class="form-control form-control-sm"
                                               name="image_file"
                                               accept="image/jpeg,image/png,image/webp"
                                               required>
                                        <div class="form-text" style="font-size: 11px;">Format JPG / PNG / WEBP. Disarankan landscape HD.</div>
                                    </div>
                                    <div class="d-flex gap-2 mt-2">
                                        <button type="submit" class="btn btn-sm btn-primary rounded-pill px-3 fw-semibold flex-grow-1" style="background-color: var(--inkindo-blue); border: none;">
                                            <i class="bi bi-upload me-1"></i> Ganti Gambar
                                        </button>
                                        <?php if ($isCustom): ?>
                                            <a href="/cms/backgrounds/reset/<?= (int) $item['id']; ?>"
                                               class="btn btn-sm btn-outline-secondary rounded-pill px-3"
                                               title="Kembalikan ke gambar bawaan"
                                               onclick="return confirm('Kembalikan background ini ke gambar bawaan?');">
                                                <i class="bi bi-arrow-counterclockwise"></i>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
<?php endforeach; ?>
<?= $this->endSection() ?>
