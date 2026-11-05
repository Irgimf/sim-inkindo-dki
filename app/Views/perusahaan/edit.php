<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center mt-3">
    <div class="col-md-8">
        <div class="card card-custom p-4">
            <h4 class="mb-4 fw-bold" style="color: var(--inkindo-blue);"><?= $title; ?></h4>
            <form action="/perusahaan/update/<?= $company['id']; ?>" method="post">
                <div class="mb-3 row">
                    <label class="col-sm-3 col-form-label fw-semibold">Nama PT</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" name="nama_pt" value="<?= esc($company['nama_pt']); ?>" required>
                    </div>
                </div>
                <div class="mb-3 row">
                    <label class="col-sm-3 col-form-label fw-semibold">Nama PJBU</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" name="nama_pjbu" value="<?= esc($company['nama_pjbu']); ?>" required>
                    </div>
                </div>
                <div class="mb-3 row">
                    <label class="col-sm-3 col-form-label fw-semibold">No KTA / NPA</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" name="no_kta" value="<?= esc($company['no_kta']); ?>" required>
                    </div>
                </div>
                <div class="mb-3 row">
                    <label class="col-sm-3 col-form-label fw-semibold">Status Keanggotaan</label>
                    <div class="col-sm-9">
                        <?php $st = $company['status'] ?? 'Penuh'; ?>
                        <select class="form-select" name="status" required>
                            <option value="Penuh" <?= ($st == 'Penuh') ? 'selected' : ''; ?>>Penuh</option>
                            <option value="Biasa" <?= ($st == 'Biasa') ? 'selected' : ''; ?>>Biasa</option>
                            <option value="Afiliasi" <?= ($st == 'Afiliasi') ? 'selected' : ''; ?>>Afiliasi</option>
                        </select>
                    </div>
                </div>
                <div class="mb-3 row">
                    <label class="col-sm-3 col-form-label fw-semibold">Kualifikasi</label>
                    <div class="col-sm-9">
                        <select class="form-select" name="kualifikasi" required>
                            <option value="Kecil" <?= ($company['kualifikasi'] == 'Kecil' || strtolower($company['kualifikasi']) == 'kecil') ? 'selected' : ''; ?>>Kecil</option>
                            <option value="Menengah" <?= ($company['kualifikasi'] == 'Menengah' || strtolower($company['kualifikasi']) == 'menengah') ? 'selected' : ''; ?>>Menengah</option>
                            <option value="Besar" <?= ($company['kualifikasi'] == 'Besar' || strtolower($company['kualifikasi']) == 'besar') ? 'selected' : ''; ?>>Besar</option>
                        </select>
                    </div>
                </div>
                <div class="mb-3 row">
                    <label class="col-sm-3 col-form-label fw-semibold">Wilayah DKI Jakarta</label>
                    <div class="col-sm-9">
                        <?php $wil = $company['wilayah'] ?? 'Jakarta Selatan'; ?>
                        <select class="form-select" name="wilayah" required>
                            <option value="Jakarta Selatan" <?= ($wil == 'Jakarta Selatan') ? 'selected' : ''; ?>>Jakarta Selatan</option>
                            <option value="Jakarta Pusat" <?= ($wil == 'Jakarta Pusat') ? 'selected' : ''; ?>>Jakarta Pusat</option>
                            <option value="Jakarta Barat" <?= ($wil == 'Jakarta Barat') ? 'selected' : ''; ?>>Jakarta Barat</option>
                            <option value="Jakarta Timur" <?= ($wil == 'Jakarta Timur') ? 'selected' : ''; ?>>Jakarta Timur</option>
                            <option value="Jakarta Utara" <?= ($wil == 'Jakarta Utara') ? 'selected' : ''; ?>>Jakarta Utara</option>
                        </select>
                    </div>
                </div>
                <div class="mb-3 row">
                    <label class="col-sm-3 col-form-label fw-semibold">Kategori SBU</label>
                    <div class="col-sm-9">
                        <select class="form-select" name="kategori_sbu" required>
                            <option value="Non Jasa Konstruksi" <?= (isset($company['kategori_sbu']) && $company['kategori_sbu'] == 'Non Jasa Konstruksi') ? 'selected' : ''; ?>>Non Jasa Konstruksi</option>
                            <option value="Jasa Konstruksi" <?= (isset($company['kategori_sbu']) && $company['kategori_sbu'] == 'Jasa Konstruksi') ? 'selected' : ''; ?>>Jasa Konstruksi</option>
                            <option value="NJK dan JK" <?= (isset($company['kategori_sbu']) && ($company['kategori_sbu'] == 'NJK dan JK' || $company['kategori_sbu'] == 'Jasa Konstruksi dan Non Jasa Konstruksi')) ? 'selected' : ''; ?>>NJK dan JK (Keduanya)</option>
                            <option value="Tidak Punya NJK dan JK" <?= (isset($company['kategori_sbu']) && ($company['kategori_sbu'] == 'Tidak Punya NJK dan JK' || $company['kategori_sbu'] == 'Tidak Punya Keduanya')) ? 'selected' : ''; ?>>Tidak Punya NJK dan JK</option>
                        </select>
                    </div>
                </div>
                <div class="mb-3 row">
                    <label class="col-sm-3 col-form-label fw-semibold">Alamat Domisili PT</label>
                    <div class="col-sm-9">
                        <textarea class="form-control" name="alamat_pt" rows="2" required><?= esc($company['alamat_pt']); ?></textarea>
                    </div>
                </div>
                <div class="mb-3 row">
                    <label class="col-sm-3 col-form-label fw-semibold">Alamat Surat-menyurat</label>
                    <div class="col-sm-9">
                        <textarea class="form-control" name="alamat_surat" rows="2"><?= esc($company['alamat_surat'] ?? $company['alamat_pt']); ?></textarea>
                    </div>
                </div>
                <div class="mb-3 row">
                    <label class="col-sm-3 col-form-label fw-semibold">No. Telepon & Fax</label>
                    <div class="col-sm-5 mb-2 mb-sm-0">
                        <input type="text" class="form-control" name="telepon" value="<?= esc(($company['telepon'] ?? '-') !== '-' ? $company['telepon'] : ''); ?>" placeholder="No. Telepon">
                    </div>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" name="fax" value="<?= esc(($company['fax'] ?? '-') !== '-' ? $company['fax'] : ''); ?>" placeholder="No. Fax">
                    </div>
                </div>
                <div class="mb-4 row">
                    <label class="col-sm-3 col-form-label fw-semibold">Email & Website</label>
                    <div class="col-sm-5 mb-2 mb-sm-0">
                        <input type="email" class="form-control" name="email" value="<?= esc(($company['email'] ?? '-') !== '-' ? $company['email'] : ''); ?>" placeholder="Email Perusahaan">
                    </div>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" name="website" value="<?= esc(($company['website'] ?? '-') !== '-' ? $company['website'] : ''); ?>" placeholder="Website">
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-9 offset-sm-3">
                        <button type="submit" class="btn btn-inkindo px-4">Update Data</button>
                        <a href="/perusahaan" class="btn btn-secondary ms-2">Batal</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
