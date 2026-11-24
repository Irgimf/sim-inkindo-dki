<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center mt-3">
    <div class="col-md-8">
        <div class="card card-custom p-4">
            <h4 class="mb-4 fw-bold" style="color: var(--inkindo-blue);"><?= $title; ?></h4>
            <form action="/sbu/update/<?= $sbu['id']; ?>" method="post">
<?= csrf_field() ?>
                
                <?php if(session()->get('role') === 'admin'): ?>
                <div class="mb-3 row">
                    <label class="col-sm-3 col-form-label fw-semibold">Pilih Perusahaan</label>
                    <div class="col-sm-9">
                        <select class="form-select" name="user_id" required>
                            <option value="">-- Pilih Perusahaan (PT) --</option>
                            <?php foreach($companies as $c): ?>
                                <option value="<?= $c['user_id']; ?>" <?= ($sbu['user_id'] == $c['user_id']) ? 'selected' : ''; ?>><?= $c['nama_pt']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <?php endif; ?>

                <div class="mb-3 row">
                    <label class="col-sm-3 col-form-label fw-semibold">Kode KBLI</label>
                    <div class="col-sm-9">
                        <select class="form-select" name="kode_kbli" id="kode_kbli" required>
                            <option value="">-- Pilih Kode KBLI --</option>
                            <!-- Arsitektur (AR) -->
                            <option value="AR001" data-judul="Jasa Arsitektural Bangunan Gedung dan non Hunian" <?= ($sbu['kode_kbli'] == 'AR001') ? 'selected' : ''; ?>>AR001</option>
                            <option value="AR002" data-judul="Jasa Arsitektural Lainnya" <?= ($sbu['kode_kbli'] == 'AR002') ? 'selected' : ''; ?>>AR002</option>
                            <option value="AR003" data-judul="Jasa Desain Interior Pada Bangunan Gedung dan Bangunan Sipil" <?= ($sbu['kode_kbli'] == 'AR003') ? 'selected' : ''; ?>>AR003</option>
                            <!-- Lanskap & Wilayah (AL) -->
                            <option value="AL001" data-judul="Jasa Pengembangan Pemanfaatan Ruang" <?= ($sbu['kode_kbli'] == 'AL001') ? 'selected' : ''; ?>>AL001</option>
                            <option value="AL002" data-judul="Jasa Pengembangan Wilayah" <?= ($sbu['kode_kbli'] == 'AL002') ? 'selected' : ''; ?>>AL002</option>
                            <option value="AL003" data-judul="Jasa Pengembangan" <?= ($sbu['kode_kbli'] == 'AL003') ? 'selected' : ''; ?>>AL003</option>
                            <option value="AL004" data-judul="Jasa Pengembangan Lingkungan Bangunan dan Lanskap" <?= ($sbu['kode_kbli'] == 'AL004') ? 'selected' : ''; ?>>AL004</option>
                            <!-- Perencanaan Penataan Ruang (PR) -->
                            <option value="PR101" data-judul="Jasa Perencanaan dan Perancangan Perkotaan" <?= ($sbu['kode_kbli'] == 'PR101') ? 'selected' : ''; ?>>PR101</option>
                            <option value="PR102" data-judul="Jasa Perencanaan Wilayah" <?= ($sbu['kode_kbli'] == 'PR102') ? 'selected' : ''; ?>>PR102</option>
                            <option value="PR103" data-judul="Jasa Perencanaan dan Perancangan Lingkungan Bangunan dan Lansekap" <?= ($sbu['kode_kbli'] == 'PR103') ? 'selected' : ''; ?>>PR103</option>
                            <option value="PR104" data-judul="jasa Pengembangan Pemanfaatan Ruang" <?= ($sbu['kode_kbli'] == 'PR104') ? 'selected' : ''; ?>>PR104</option>
                            <option value="PR201" data-judul="Jasa Pengawas dan Pengendali Penataan Ruang" <?= ($sbu['kode_kbli'] == 'PR201') ? 'selected' : ''; ?>>PR201</option>
                            <!-- Konsultansi Manajemen (7020) -->
                            <option value="70209" data-judul="Aktivitas Konsultasi Manajemen dan Bisnis Lainnya" <?= ($sbu['kode_kbli'] == '70209') ? 'selected' : ''; ?>>70209</option>
                            <option value="70201" data-judul="Aktivitas Konsultasi Manajemen dan Bisnis Pariwisata" <?= ($sbu['kode_kbli'] == '70201') ? 'selected' : ''; ?>>70201</option>
                            <option value="70202" data-judul="Aktivitas Konsultasi Manajemen dan Bisnis Industri" <?= ($sbu['kode_kbli'] == '70202') ? 'selected' : ''; ?>>70202</option>
                            <option value="70203" data-judul="Aktivitas Konsultasi Manajemen dan Bisnis Perdagangan" <?= ($sbu['kode_kbli'] == '70203') ? 'selected' : ''; ?>>70203</option>
                        </select>
                    </div>
                </div>
                <div class="mb-3 row">
                    <label class="col-sm-3 col-form-label fw-semibold">Judul KBLI</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control bg-light" name="judul_kbli" id="judul_kbli" value="<?= esc($sbu['judul_kbli']); ?>" required readonly placeholder="Otomatis terisi...">
                    </div>
                </div>
                <div class="mb-3 row">
                    <label class="col-sm-3 col-form-label fw-semibold">Kualifikasi</label>
                    <div class="col-sm-9">
                        <select class="form-select" name="kualifikasi" required>
                            <option value="Kecil" <?= ($sbu['kualifikasi'] == 'Kecil') ? 'selected' : ''; ?>>Kecil</option>
                            <option value="Menengah" <?= ($sbu['kualifikasi'] == 'Menengah') ? 'selected' : ''; ?>>Menengah</option>
                            <option value="Besar" <?= ($sbu['kualifikasi'] == 'Besar') ? 'selected' : ''; ?>>Besar</option>
                        </select>
                    </div>
                </div>
                <div class="mb-4 row">
                    <label class="col-sm-3 col-form-label fw-semibold">Masa Berlaku</label>
                    <div class="col-sm-9">
                        <input type="date" class="form-control" name="masa_berlaku" value="<?= esc($sbu['masa_berlaku']); ?>" required>
                    </div>
                </div>
                <div class="row mt-4">
                    <div class="col-sm-9 offset-sm-3 d-flex justify-content-between">
                        <div>
                            <button type="submit" class="btn btn-inkindo px-4">Update Data</button>
                            <a href="/sbu" class="btn btn-secondary ms-2">Batal</a>
                        </div>
                        <a href="/sbu/delete/<?= $sbu['id']; ?>" class="btn btn-outline-danger px-4" onclick="return confirm('Apakah Anda yakin ingin menghapus data SBU beserta seluruh proyeknya?');">Hapus SBU</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('kode_kbli').addEventListener('change', function() {
    var selectedOption = this.options[this.selectedIndex];
    var judul = selectedOption.getAttribute('data-judul');
    document.getElementById('judul_kbli').value = judul ? judul : '';
});
</script>
<?= $this->endSection() ?>
