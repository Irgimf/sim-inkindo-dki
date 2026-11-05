<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="mb-4 mt-2">
    <a href="/mitra" class="btn btn-outline-secondary btn-sm mb-3"><i class="bi bi-arrow-left"></i> Kembali ke Data Mitra Kerja</a>
    <h3 class="fw-bold" style="color: var(--inkindo-blue);"><?= esc($title); ?></h3>
    <?php if(session()->get('role') === 'admin'): ?>
        <p class="text-muted small">Tambahkan Mitra Kerja baru beserta tanggal batas berlaku kemitraannya.</p>
    <?php else: ?>
        <p class="text-muted small">Isi profil perusahaan, upload logo, dan pilih tanggal batas berakhirnya kemitraan yang diajukan melalui kalender. Setelah disetujui Admin, halaman web dan logo Mitra Kerja Anda akan otomatis aktif di Web Profile INKINDO DKI Jakarta.</p>
    <?php endif; ?>
</div>

<?php if(session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= session()->getFlashdata('error'); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="card card-custom p-4 border-0 shadow-sm" style="border-radius: 18px;">
    <form action="/mitra/store" method="post" enctype="multipart/form-data">
        <div class="row g-3">
            <!-- Upload Logo -->
            <div class="col-md-12 mb-2">
                <label class="form-label fw-semibold">Upload Gambar Logo Perusahaan Mitra <span class="text-danger">*</span></label>
                <div class="d-flex align-items-center gap-4 flex-wrap p-3 rounded-3" style="background: #f8fafc; border: 1px dashed #cbd5e1;">
                    <div id="logoPreviewWrap" style="width: 140px; height: 80px; background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                        <span class="text-muted small" id="logoPreviewText">Preview Logo</span>
                        <img id="logoPreviewImg" src="" alt="Preview" style="max-height: 64px; max-width: 120px; object-fit: contain; display: none;">
                    </div>
                    <div class="flex-grow-1">
                        <input type="file" name="logo" id="logoInput" class="form-control" accept=".jpg,.jpeg,.png,.webp,.svg" required>
                        <small class="text-muted d-block mt-1">Format didukung: JPG, PNG, WEBP, SVG. Disarankan latar belakang putih atau transparan.</small>
                    </div>
                </div>
            </div>

            <div class="col-md-5">
                <label class="form-label fw-semibold">Nama Perusahaan Mitra Kerja <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="nama_perusahaan" placeholder="Contoh: PT Granitoguna Building Ceramics" required>
            </div>

            <div class="col-md-4">
                <label class="form-label fw-semibold">Kategori / Bidang Kemitraan</label>
                <input type="text" class="form-control" name="kategori_bisnis" placeholder="Contoh: Produsen Material Bangunan" value="Produsen Material & Solusi Konstruksi">
            </div>

            <div class="col-md-3">
                <label class="form-label fw-semibold">Berlaku Sampai Tanggal <span class="text-danger">*</span></label>
                <input type="date" name="expired_at" class="form-control" min="<?= date('Y-m-d', strtotime('+1 day')); ?>" value="<?= date('Y-m-d', strtotime('+1 year')); ?>" required>
                <small class="text-muted">Pilih batas tanggal berakhir kemitraan</small>
            </div>

            <div class="col-md-12">
                <label class="form-label fw-semibold">Deskripsi Profil Perusahaan Mitra Kerja <span class="text-danger">*</span></label>
                <textarea class="form-control" name="deskripsi" rows="4" placeholder="Tuliskan deskripsi profil perusahaan, keunggulan, dan layanan kemitraan bersama INKINDO DKI Jakarta..." required></textarea>
            </div>

            <div class="col-md-12">
                <label class="form-label fw-semibold">Alamat Kantor / Showroom</label>
                <textarea class="form-control" name="alamat" rows="2" placeholder="Contoh: Jl. Jenderal Sudirman Kav. 10, Jakarta Selatan"></textarea>
            </div>

            <div class="col-md-4">
                <label class="form-label fw-semibold">No. Telepon / WhatsApp</label>
                <input type="text" class="form-control" name="telepon" placeholder="Contoh: (021) 52901234">
            </div>

            <div class="col-md-4">
                <label class="form-label fw-semibold">Email Resmi</label>
                <input type="email" class="form-control" name="email" placeholder="Contoh: info@perusahaan.co.id">
            </div>

            <div class="col-md-4">
                <label class="form-label fw-semibold">Website</label>
                <input type="text" class="form-control" name="website" placeholder="Contoh: https://www.perusahaan.co.id">
            </div>
        </div>

        <div class="mt-4 pt-2 d-flex gap-2">
            <button type="submit" class="btn btn-inkindo px-4 py-2">
                <i class="bi bi-send-check me-1"></i>
                <?= session()->get('role') === 'admin' ? 'Simpan & Aktifkan Mitra Kerja' : 'Ajukan Mitra Kerja & Lanjut Isi Produk'; ?>
            </button>
            <a href="/mitra" class="btn btn-secondary px-4 py-2">Batal</a>
        </div>
    </form>
</div>

<script>
document.getElementById('logoInput').addEventListener('change', function(e) {
    const file = e.target.files[0];
    const img = document.getElementById('logoPreviewImg');
    const txt = document.getElementById('logoPreviewText');
    if (file) {
        const reader = new FileReader();
        reader.onload = function(ev) {
            img.src = ev.target.result;
            img.style.display = 'block';
            txt.style.display = 'none';
        };
        reader.readAsDataURL(file);
    }
});
</script>
<?= $this->endSection() ?>
