<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4 mt-2 flex-wrap gap-3">
    <div>
        <h3 class="fw-bold mb-1" style="color: var(--inkindo-blue);">Admin CMS — Footer &amp; Contact</h3>
        <p class="text-muted small mb-0">Satu kali pengisian kontak otomatis memperbarui bagian <strong>Footer</strong> dan menu samping <strong>Hubungi Kami</strong> di seluruh halaman <code>inkindo-dki-web</code>.</p>
    </div>
    <a href="<?= esc(rtrim(config('App')->webProfileURL, '/')); ?>" target="_blank" class="btn btn-outline-primary rounded-pill px-3 fw-semibold">
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

<form action="/cms/footer-contact/save" method="post">
    <!-- 1. Kontak Bersama (Footer & Menu Samping Hubungi Kami) -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3 pb-2 border-bottom">
                <h5 class="fw-bold mb-0" style="color: var(--inkindo-blue);">
                    1. Informasi Kontak Sekretariat (Footer &amp; Hubungi Kami)
                </h5>
                <span class="badge badge-soft-primary rounded-pill px-3 py-1" style="font-size: 11px;">
                    Sinkron Otomatis 2 Tempat
                </span>
            </div>

            <div class="row g-3">
                <div class="col-12">
                    <label class="form-label fw-semibold small text-dark">Alamat Sekretariat DPP <span class="text-danger">*</span></label>
                    <textarea class="form-control" name="alamat" rows="2" required><?= esc($data['alamat'] ?? ''); ?></textarea>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold small text-dark">Telepon Kantor (Utama) <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="telepon_1" value="<?= esc($data['telepon_1'] ?? ''); ?>" placeholder="+62217942940" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold small text-dark">Nomor HP / Alternatif</label>
                    <input type="text" class="form-control" name="telepon_2" value="<?= esc($data['telepon_2'] ?? ''); ?>" placeholder="+6282112079750">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold small text-dark">Nomor Fax</label>
                    <input type="text" class="form-control" name="fax" value="<?= esc($data['fax'] ?? ''); ?>" placeholder="+62217942941">
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold small text-dark">Email Utama <span class="text-danger">*</span></label>
                    <input type="email" class="form-control" name="email_1" value="<?= esc($data['email_1'] ?? ''); ?>" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold small text-dark">Email Alternatif</label>
                    <input type="email" class="form-control" name="email_2" value="<?= esc($data['email_2'] ?? ''); ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold small text-dark">Jam Operasional <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="jam_operasional" value="<?= esc($data['jam_operasional'] ?? ''); ?>" placeholder="Senin - Jumat: 08.00 - 16.30 WIB" required>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. Bio Footer, Media Sosial & Copyright -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <h5 class="fw-bold mb-3 pb-2 border-bottom" style="color: var(--inkindo-blue);">
                2. Bio Organisasi, Media Sosial &amp; Copyright (Footer)
            </h5>

            <div class="row g-3">
                <div class="col-12">
                    <label class="form-label fw-semibold small text-dark">Deskripsi Singkat / Bio Footer</label>
                    <textarea class="form-control" name="bio" rows="3" required><?= esc($data['bio'] ?? ''); ?></textarea>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold small text-dark"><i class="bi bi-youtube text-danger me-1"></i> URL YouTube</label>
                    <input type="url" class="form-control" name="youtube_url" value="<?= esc($data['youtube_url'] ?? ''); ?>" placeholder="https://www.youtube.com/@...">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold small text-dark"><i class="bi bi-facebook text-primary me-1"></i> URL Facebook</label>
                    <input type="url" class="form-control" name="facebook_url" value="<?= esc($data['facebook_url'] ?? ''); ?>" placeholder="https://www.facebook.com/...">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold small text-dark"><i class="bi bi-twitter-x me-1"></i> URL X (Twitter)</label>
                    <input type="url" class="form-control" name="x_url" value="<?= esc($data['x_url'] ?? ''); ?>" placeholder="https://x.com/...">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold small text-dark"><i class="bi bi-instagram text-danger me-1"></i> URL Instagram</label>
                    <input type="url" class="form-control" name="instagram_url" value="<?= esc($data['instagram_url'] ?? ''); ?>" placeholder="https://www.instagram.com/...">
                </div>

                <div class="col-12">
                    <label class="form-label fw-semibold small text-dark">Teks Copyright Footer</label>
                    <input type="text" class="form-control" name="copyright_text" value="<?= esc($data['copyright_text'] ?? ''); ?>" required>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-end mb-4">
        <button type="submit" class="btn btn-primary rounded-pill px-5 py-2 fw-semibold shadow-sm" style="background-color: var(--inkindo-blue); border: none;">
            <i class="bi bi-save me-1"></i> Simpan Footer &amp; Contact
        </button>
    </div>
</form>
<?= $this->endSection() ?>

