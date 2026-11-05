<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center mt-3">
    <div class="col-md-8">
        <div class="card card-custom p-4">
            <h4 class="mb-4 fw-bold" style="color: var(--inkindo-blue);"><?= $title; ?></h4>
            <form action="/perusahaan/store" method="post">
                <div class="mb-3 row">
                    <label class="col-sm-3 col-form-label fw-semibold">Nama PT</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" name="nama_pt" required placeholder="Contoh: PT. Inkindo">
                    </div>
                </div>
                <div class="mb-3 row">
                    <label class="col-sm-3 col-form-label fw-semibold">Nama PJBU</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" name="nama_pjbu" required placeholder="Nama Penanggung Jawab Badan Usaha">
                    </div>
                </div>
                <div class="mb-3 row">
                    <label class="col-sm-3 col-form-label fw-semibold">No KTA / NPA</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" name="no_kta" required placeholder="Contoh: 14642/P/3046.DKI">
                    </div>
                </div>
                <div class="mb-3 row">
                    <label class="col-sm-3 col-form-label fw-semibold">Status Keanggotaan</label>
                    <div class="col-sm-9">
                        <select class="form-select" name="status" required>
                            <option value="Penuh" selected>Penuh</option>
                            <option value="Biasa">Biasa</option>
                            <option value="Afiliasi">Afiliasi</option>
                        </select>
                    </div>
                </div>
                <div class="mb-3 row">
                    <label class="col-sm-3 col-form-label fw-semibold">Kualifikasi</label>
                    <div class="col-sm-9">
                        <select class="form-select" name="kualifikasi" required>
                            <option value="">Pilih Kualifikasi</option>
                            <option value="Kecil">Kecil</option>
                            <option value="Menengah">Menengah</option>
                            <option value="Besar">Besar</option>
                        </select>
                    </div>
                </div>
                <div class="mb-3 row">
                    <label class="col-sm-3 col-form-label fw-semibold">Wilayah DKI Jakarta</label>
                    <div class="col-sm-9">
                        <select class="form-select" name="wilayah" required>
                            <option value="Jakarta Selatan">Jakarta Selatan</option>
                            <option value="Jakarta Pusat">Jakarta Pusat</option>
                            <option value="Jakarta Barat">Jakarta Barat</option>
                            <option value="Jakarta Timur">Jakarta Timur</option>
                            <option value="Jakarta Utara">Jakarta Utara</option>
                        </select>
                    </div>
                </div>
                <div class="mb-3 row">
                    <label class="col-sm-3 col-form-label fw-semibold">Kategori SBU</label>
                    <div class="col-sm-9">
                        <select class="form-select" name="kategori_sbu" required>
                            <option value="">Pilih Kategori SBU</option>
                            <option value="Non Jasa Konstruksi">Non Jasa Konstruksi</option>
                            <option value="Jasa Konstruksi">Jasa Konstruksi</option>
                            <option value="NJK dan JK">NJK dan JK (Keduanya)</option>
                            <option value="Tidak Punya NJK dan JK">Tidak Punya NJK dan JK</option>
                        </select>
                    </div>
                </div>
                <div class="mb-3 row">
                    <label class="col-sm-3 col-form-label fw-semibold">Alamat Domisili PT</label>
                    <div class="col-sm-9">
                        <textarea class="form-control" name="alamat_pt" rows="2" required placeholder="Alamat domisili lengkap perusahaan"></textarea>
                    </div>
                </div>
                <div class="mb-3 row">
                    <label class="col-sm-3 col-form-label fw-semibold">Alamat Surat-menyurat</label>
                    <div class="col-sm-9">
                        <textarea class="form-control" name="alamat_surat" rows="2" placeholder="Kosongkan jika sama dengan alamat domisili"></textarea>
                    </div>
                </div>
                <div class="mb-3 row">
                    <label class="col-sm-3 col-form-label fw-semibold">No. Telepon & Fax</label>
                    <div class="col-sm-5 mb-2 mb-sm-0">
                        <input type="text" class="form-control" name="telepon" placeholder="No. Telepon (Contoh: 021-7942940)">
                    </div>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" name="fax" placeholder="No. Fax">
                    </div>
                </div>
                <div class="mb-4 row">
                    <label class="col-sm-3 col-form-label fw-semibold">Email & Website</label>
                    <div class="col-sm-5 mb-2 mb-sm-0">
                        <input type="email" class="form-control" name="email" placeholder="email@perusahaan.co.id">
                    </div>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" name="website" placeholder="www.perusahaan.co.id">
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-9 offset-sm-3">
                        <button type="submit" class="btn btn-inkindo px-4">Simpan Data</button>
                        <a href="/perusahaan" class="btn btn-secondary ms-2">Batal</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
