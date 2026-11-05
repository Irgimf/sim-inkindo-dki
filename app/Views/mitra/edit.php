<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="mb-4 mt-2">
    <a href="/mitra" class="btn btn-outline-secondary btn-sm mb-3"><i class="bi bi-arrow-left"></i> Kembali ke Data Mitra Kerja</a>
    <h3 class="fw-bold" style="color: var(--inkindo-blue);"><?= esc($title); ?></h3>
</div>

<?php if(($mitra['status_kemitraan'] ?? '') === 'rejected' && !empty($mitra['alasan_penolakan'])): ?>
<div class="alert alert-danger border-danger mb-4 p-3" style="border-radius: 14px;">
    <div class="fw-bold text-danger mb-1"><i class="bi bi-exclamation-triangle-fill me-1"></i> Alasan Penolakan dari Admin:</div>
    <div class="text-dark small">"<?= nl2br(esc($mitra['alasan_penolakan'])); ?>"</div>
    <div class="text-muted small mt-1">Silakan perbaiki data di bawah ini lalu klik <strong>Simpan / Ajukan Mitra Kerja</strong> untuk mengajukan kembali.</div>
</div>
<?php endif; ?>

<div class="card card-custom p-4 border-0 shadow-sm" style="border-radius: 18px;">
    <form action="/mitra/update/<?= $mitra['id']; ?>" method="post" enctype="multipart/form-data">
        <div class="row g-3">
            <!-- Upload Logo -->
            <div class="col-md-12 mb-2">
                <label class="form-label fw-semibold">Logo Perusahaan Mitra Kerja</label>
                <div class="d-flex align-items-center gap-4 flex-wrap p-3 rounded-3" style="background: #f8fafc; border: 1px dashed #cbd5e1;">
                    <div style="width: 140px; height: 80px; background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                        <img id="logoPreviewImg" src="/<?= esc($mitra['logo'] ?: 'logo-inkindo.png'); ?>" alt="Logo" style="max-height: 64px; max-width: 120px; object-fit: contain;">
                    </div>
                    <div class="flex-grow-1">
                        <input type="file" name="logo" id="logoInput" class="form-control" accept=".jpg,.jpeg,.png,.webp,.svg">
                        <small class="text-muted d-block mt-1">Biarkan kosong jika tidak ingin mengganti gambar logo saat ini.</small>
                    </div>
                </div>
            </div>

            <div class="col-md-5">
                <label class="form-label fw-semibold">Nama Perusahaan Mitra Kerja <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="nama_perusahaan" value="<?= esc($mitra['nama_perusahaan']); ?>" required>
            </div>

            <div class="col-md-4">
                <label class="form-label fw-semibold">Kategori / Bidang Kemitraan</label>
                <input type="text" class="form-control" name="kategori_bisnis" value="<?= esc($mitra['kategori_bisnis']); ?>">
            </div>

            <div class="col-md-3">
                <label class="form-label fw-semibold">Berlaku Sampai Tanggal <span class="text-danger">*</span></label>
                <input type="date" class="form-control" name="expired_at" value="<?= esc($mitra['expired_at'] ?? date('Y-m-d', strtotime('+1 year'))); ?>" required>
                <small class="text-muted">Pilih batas tanggal berakhir kemitraan</small>
            </div>

            <?php if(session()->get('role') === 'admin'): ?>
                <div class="col-md-12">
                    <label class="form-label fw-semibold text-primary">Status Kemitraan (Khusus Admin)</label>
                    <?php $curStatus = $mitra['status_kemitraan'] ?? 'active'; ?>
                    <select name="status_kemitraan" class="form-select">
                        <option value="active" <?= $curStatus === 'active' ? 'selected' : ''; ?>>Aktif (Disetujui)</option>
                        <option value="pending" <?= $curStatus === 'pending' ? 'selected' : ''; ?>>Pending (Menunggu Persetujuan)</option>
                        <option value="expired" <?= $curStatus === 'expired' ? 'selected' : ''; ?>>Expired (Masa Berlaku Habis / Mati)</option>
                        <option value="rejected" <?= $curStatus === 'rejected' ? 'selected' : ''; ?>>Ditolak</option>
                    </select>
                    <small class="text-muted">Jika tanggal batas di atas terlewati atau status diubah ke Expired, halaman web &amp; logo mitra otomatis mati di WebProfile.</small>
                </div>
            <?php endif; ?>

            <div class="col-md-12">
                <label class="form-label fw-semibold">Deskripsi Profil Perusahaan Mitra Kerja <span class="text-danger">*</span></label>
                <textarea class="form-control" name="deskripsi" rows="4" required><?= esc($mitra['deskripsi']); ?></textarea>
            </div>

            <div class="col-md-12">
                <label class="form-label fw-semibold">Alamat Kantor / Showroom</label>
                <textarea class="form-control" name="alamat" rows="2"><?= esc($mitra['alamat']); ?></textarea>
            </div>

            <div class="col-md-4">
                <label class="form-label fw-semibold">No. Telepon / WhatsApp</label>
                <input type="text" class="form-control" name="telepon" value="<?= esc($mitra['telepon']); ?>">
            </div>

            <div class="col-md-4">
                <label class="form-label fw-semibold">Email Resmi</label>
                <input type="text" class="form-control" name="email" value="<?= esc($mitra['email']); ?>">
            </div>

            <div class="col-md-4">
                <label class="form-label fw-semibold">Website</label>
                <input type="text" class="form-control" name="website" value="<?= esc($mitra['website']); ?>">
            </div>

            <?php if(session()->get('role') !== 'admin'): ?>
                <div class="col-md-12 mt-2">
                    <div class="form-check p-3 rounded-3" style="background: #fffbeb; border: 1px solid #fde68a;">
                        <input class="form-check-input ms-0 me-2" type="checkbox" value="1" id="ajukanPerpanjanganCheck" name="ajukan_perpanjangan" <?= in_array($mitra['status_kemitraan'] ?? '', ['expired', 'rejected', 'pending'], true) ? 'checked' : ''; ?>>
                        <label class="form-check-label fw-semibold text-dark" for="ajukanPerpanjanganCheck">
                            Ajukan Aktivasi / Perpanjangan Masa Kemitraan ke Admin hingga tanggal batas waktu yang dipilih di atas
                        </label>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <div class="mt-4 pt-2 d-flex gap-2 flex-wrap">
            <button type="submit" class="btn btn-inkindo px-4 py-2">
                <?= session()->get('role') === 'admin' ? 'Update Data Mitra' : 'Simpan / Ajukan Mitra Kerja'; ?>
            </button>
            <a href="/mitra/products/<?= $mitra['id']; ?>" class="btn btn-warning px-4 py-2 fw-semibold">Kelola Detail Produk</a>
            <a href="/mitra" class="btn btn-secondary px-4 py-2">Batal</a>
        </div>
    </form>
</div>

<script>
document.getElementById('logoInput').addEventListener('change', function(e) {
    const file = e.target.files[0];
    const img = document.getElementById('logoPreviewImg');
    if (file) {
        const reader = new FileReader();
        reader.onload = function(ev) {
            img.src = ev.target.result;
        };
        reader.readAsDataURL(file);
    }
});
</script>
<?= $this->endSection() ?>
