<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="mb-4 mt-2">
    <a href="/mitra" class="btn btn-outline-secondary btn-sm mb-3"><i class="bi bi-arrow-left"></i> Kembali ke Daftar Mitra Kerja</a>
    <h3 class="fw-bold" style="color: var(--inkindo-blue);"><?= esc($title); ?></h3>
</div>

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

            <div class="col-md-6">
                <label class="form-label fw-semibold">Nama Perusahaan Mitra Kerja <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="nama_perusahaan" value="<?= esc($mitra['nama_perusahaan']); ?>" required>
            </div>

            <div class="col-md-6">
                <label class="form-label fw-semibold">Kategori / Bidang Kemitraan</label>
                <input type="text" class="form-control" name="kategori_bisnis" value="<?= esc($mitra['kategori_bisnis']); ?>">
            </div>

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
        </div>

        <div class="mt-4 pt-2 d-flex gap-2">
            <button type="submit" class="btn btn-inkindo px-4 py-2">Update Data Mitra</button>
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

