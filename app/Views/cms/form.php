<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<?php
    $isEdit = ($mode === 'edit' && !empty($item));
    $formAction = $isEdit ? '/cms/update/' . $item['id'] : '/cms/store';
    $curTipe = old('tipe', $isEdit ? $item['tipe'] : ($defaultTipe ?? 'rilis'));

    $resolveImgUrl = function (?string $path): string {
        if (!$path) return 'http://localhost:8000/assets/gambar.png';
        if (strpos($path, 'http://') === 0 || strpos($path, 'https://') === 0) return $path;
        if (strpos($path, 'cms_uploads/') === 0) return '/' . ltrim($path, '/');
        return 'http://localhost:8000/' . ltrim($path, '/');
    };

    $existingGaleri = [];
    if ($isEdit && !empty($item['galeri_foto_json'])) {
        $decoded = json_decode($item['galeri_foto_json'], true);
        if (is_array($decoded)) {
            $existingGaleri = $decoded;
        }
    }
?>

<div class="d-flex justify-content-between align-items-center mb-4 mt-2">
    <div>
        <h3 class="fw-bold mb-1" style="color: var(--inkindo-blue);"><?= esc($title); ?></h3>
        <p class="text-muted small mb-0">Isi detail konten untuk halaman <strong>Berita &amp; Informasi</strong> pada WebProfile.</p>
    </div>
    <a href="/cms<?= $curTipe ? '?tipe=' . esc($curTipe) : '' ?>" class="btn btn-outline-secondary rounded-pill px-4 fw-semibold">
        <i class="bi bi-arrow-left me-1"></i> Kembali
    </a>
</div>

<?php if(session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= session()->getFlashdata('error'); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="card border-0 shadow-sm" style="border-radius: 16px;">
    <div class="card-body p-4 p-md-5">
        <form action="<?= $formAction; ?>" method="post" enctype="multipart/form-data">
            <div class="row g-4">
                <!-- Pilihan Tab / Kategori Utama -->
                <div class="col-md-6">
                    <label class="form-label fw-bold text-dark">Tab / Kanal Utama Berita &amp; Informasi <span class="text-danger">*</span></label>
                    <select name="tipe" id="cmsTipeSelect" class="form-select" required onchange="syncCmsFieldsByType()">
                        <option value="agenda" <?= $curTipe === 'agenda' ? 'selected' : '' ?>>1. Agenda Kegiatan</option>
                        <option value="rilis" <?= $curTipe === 'rilis' ? 'selected' : '' ?>>2. Rilis Berita</option>
                        <option value="publikasi" <?= $curTipe === 'publikasi' ? 'selected' : '' ?>>3. Publikasi</option>
                        <option value="galeri" <?= $curTipe === 'galeri' ? 'selected' : '' ?>>4. Galeri (Album Dokumentasi)</option>
                        <option value="emagazine" <?= $curTipe === 'emagazine' ? 'selected' : '' ?>>5. e-Magazine</option>
                        <option value="karya" <?= $curTipe === 'karya' ? 'selected' : '' ?>>6. Karya Anggota (Buku)</option>
                    </select>
                </div>

                <!-- Pilihan Slot Halaman Utama (index.html) -->
                <div class="col-md-6">
                    <label class="form-label fw-bold text-dark">Tampilkan di Halaman Utama (`index.html`)</label>
                    <?php $curSlot = (int) old('homepage_slot', $isEdit ? ($item['homepage_slot'] ?? 0) : 0); ?>
                    <select name="homepage_slot" class="form-select">
                        <option value="0" <?= $curSlot === 0 ? 'selected' : '' ?>>Tidak Tampil di Halaman Utama</option>
                        <option value="1" <?= $curSlot === 1 ? 'selected' : '' ?>>Slot 1 — Berita Utama (Kartu Besar Kiri)</option>
                        <option value="2" <?= $curSlot === 2 ? 'selected' : '' ?>>Slot 2 — Kartu Kanan Atas</option>
                        <option value="3" <?= $curSlot === 3 ? 'selected' : '' ?>>Slot 3 — Kartu Kanan Bawah</option>
                    </select>
                    <div class="form-text">Jika dipilih, konten ini otomatis menggantikan slot yang bersangkutan di beranda.</div>
                </div>

                <!-- Judul -->
                <div class="col-12">
                    <label class="form-label fw-bold text-dark">Judul Konten / Berita / Buku <span class="text-danger">*</span></label>
                    <input type="text" name="judul" class="form-control" required
                           placeholder="Masukkan judul lengkap..."
                           value="<?= esc(old('judul', $isEdit ? $item['judul'] : '')); ?>">
                </div>

                <!-- Label Kategori (Pill) & Badge Gambar -->
                <div class="col-md-4">
                    <label class="form-label fw-bold text-dark" id="lblKategoriLabel">Label Sub-Kategori (Pill)</label>
                    <input type="text" name="kategori_label" id="inpKategoriLabel" class="form-control"
                           placeholder="Contoh: Siaran Pers / Pelatihan & Bimtek / Majalah"
                           value="<?= esc(old('kategori_label', $isEdit ? $item['kategori_label'] : '')); ?>">
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-bold text-dark" id="lblBadge">Label Badge Pojok Gambar</label>
                    <input type="text" name="badge" id="inpBadge" class="form-control"
                           placeholder="Contoh: Rilis Resmi / Hybrid Event / Edisi 79 / 2026"
                           value="<?= esc(old('badge', $isEdit ? $item['badge'] : '')); ?>">
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-bold text-dark">Tanggal Kalender</label>
                    <input type="date" name="tanggal" class="form-control"
                           value="<?= esc(old('tanggal', $isEdit ? $item['tanggal'] : date('Y-m-d'))); ?>">
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-bold text-dark" id="lblTanggalLabel">Teks Tanggal Tampil (Opsional)</label>
                    <input type="text" name="tanggal_label" class="form-control"
                           placeholder="Otomatis dari tanggal kalender jika dikosongkan (misal: 06 Nov 2026)"
                           value="<?= esc(old('tanggal_label', $isEdit ? $item['tanggal_label'] : '')); ?>">
                </div>

                <div class="col-md-4" id="wrapPenulis">
                    <label class="form-label fw-bold text-dark" id="lblPenulis">Sumber / Penulis</label>
                    <input type="text" name="penulis" id="inpPenulis" class="form-control"
                           placeholder="Contoh: Biro Komunikasi DPP / Nama Penulis Buku"
                           value="<?= esc(old('penulis', $isEdit ? $item['penulis'] : '')); ?>">
                </div>

                <div class="col-md-4" id="wrapLokasiMeta">
                    <label class="form-label fw-bold text-dark" id="lblLokasiMeta">Info Lokasi / Tag Footer Kartu</label>
                    <input type="text" name="lokasi_meta" id="inpLokasiMeta" class="form-control"
                           placeholder="Contoh: Hotel Bidakara & Zoom / Biro Komunikasi DPP"
                           value="<?= esc(old('lokasi_meta', $isEdit ? $item['lokasi_meta'] : '')); ?>">
                </div>

                <!-- KHUSUS AGENDA KEGIATAN -->
                <div class="col-12" id="sectionAgendaFields" style="display: none;">
                    <div class="p-3 rounded-3" style="background: #f8fafc; border: 1px solid #cbd5e1;">
                        <h6 class="fw-bold text-primary mb-3"><i class="bi bi-calendar-event me-1"></i> Detail Informasi Kegiatan (Khusus Agenda Kegiatan)</h6>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label small fw-bold text-dark">Waktu Pelaksanaan</label>
                                <input type="text" name="waktu_kegiatan" class="form-control form-control-sm"
                                       placeholder="Contoh: 08.30 – 16.00 WIB"
                                       value="<?= esc(old('waktu_kegiatan', $isEdit ? $item['waktu_kegiatan'] : '')); ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold text-dark">Biaya Pendaftaran</label>
                                <input type="text" name="biaya_kegiatan" class="form-control form-control-sm"
                                       placeholder="Contoh: Gratis bagi Anggota / Rp 1.500.000"
                                       value="<?= esc(old('biaya_kegiatan', $isEdit ? $item['biaya_kegiatan'] : '')); ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold text-dark">Kuota Peserta</label>
                                <input type="text" name="kuota_kegiatan" class="form-control form-control-sm"
                                       placeholder="Contoh: 100 Peserta"
                                       value="<?= esc(old('kuota_kegiatan', $isEdit ? $item['kuota_kegiatan'] : '')); ?>">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- KHUSUS E-MAGAZINE (PDF) -->
                <div class="col-12" id="sectionPdfFields" style="display: none;">
                    <div class="p-3 rounded-3" style="background: #fffbeb; border: 1px solid #fde68a;">
                        <h6 class="fw-bold text-dark mb-3"><i class="bi bi-file-earmark-pdf-fill text-danger me-1"></i> Dokumen PDF e-Magazine</h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-dark">Upload File PDF Majalah</label>
                                <input type="file" name="pdf_file" class="form-control form-control-sm" accept=".pdf">
                                <?php if($isEdit && !empty($item['file_pdf'])): ?>
                                    <div class="small text-success mt-1">
                                        <i class="bi bi-check-circle-fill"></i> File PDF saat ini: <code><?= esc($item['file_pdf']); ?></code>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-dark">Tautan PDF Eksternal / Cadangan (Opsional)</label>
                                <input type="url" name="external_url" class="form-control form-control-sm"
                                       placeholder="https://..."
                                       value="<?= esc(old('external_url', $isEdit ? $item['external_url'] : '')); ?>">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- KHUSUS GALERI (UPLOAD BANYAK FOTO ALBUM) -->
                <div class="col-12" id="sectionGaleriFields" style="display: none;">
                    <div class="p-3 rounded-3" style="background: #f0fdf4; border: 1px solid #bbf7d0;">
                        <h6 class="fw-bold text-success mb-2"><i class="bi bi-images me-1"></i> Foto-Foto Album Dokumentasi (Khusus Galeri)</h6>
                        <p class="small text-muted mb-3">Anda dapat memilih banyak foto sekaligus untuk ditampilkan pada *Lightbox* saat album galeri diklik di WebProfile.</p>
                        <input type="file" name="galeri_files[]" class="form-control" accept="image/*" multiple>
                        <input type="hidden" name="galeri_present" value="1">

                        <?php if(!empty($existingGaleri)): ?>
                            <div class="mt-3">
                                <label class="form-label small fw-bold text-dark">Foto dalam Album Saat Ini (Centang untuk tetap menyimpan):</label>
                                <div class="d-flex flex-wrap gap-3">
                                    <?php foreach($existingGaleri as $idx => $gPath): ?>
                                        <label class="border rounded-3 p-2 bg-white text-center" style="width: 120px; cursor: pointer;">
                                            <img src="<?= esc($resolveImgUrl($gPath)); ?>" alt="" style="width: 100%; height: 70px; object-fit: cover; border-radius: 6px;" class="mb-1">
                                            <div class="form-check d-flex justify-content-center mb-0">
                                                <input class="form-check-input me-1" type="checkbox" name="keep_galeri[]" value="<?= esc($gPath); ?>" checked>
                                                <span class="small text-muted" style="font-size: 11px;">Simpan</span>
                                            </div>
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Upload Gambar Sampul / Thumbnail -->
                <div class="col-12">
                    <label class="form-label fw-bold text-dark">Gambar Sampul / Thumbnail Utama</label>
                    <input type="file" name="gambar_file" class="form-control" accept="image/*">
                    <div class="form-text">Format: JPG, PNG, WEBP. Untuk e-Magazine dan Karya Anggota disarankan rasio sampul buku (3:4).</div>
                    <?php if($isEdit && !empty($item['gambar'])): ?>
                        <div class="mt-2 d-flex align-items-center gap-3">
                            <img src="<?= esc($resolveImgUrl($item['gambar'])); ?>" alt="Preview" style="width: 100px; height: 70px; object-fit: cover; border-radius: 8px; border: 1px solid #cbd5e1;">
                            <div class="small text-muted">Gambar saat ini: <code><?= esc($item['gambar']); ?></code><br>Biarkan kosong jika tidak ingin mengganti gambar sampul.</div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Ringkasan / Lead -->
                <div class="col-12">
                    <label class="form-label fw-bold text-dark">Ringkasan Singkat / Paragraf Pembuka (Lead) <span class="text-danger">*</span></label>
                    <textarea name="ringkasan" class="form-control" rows="3" required
                              placeholder="Tulis ringkasan singkat yang akan tampil pada kartu dan pembuka halaman detail..."><?= esc(old('ringkasan', $isEdit ? $item['ringkasan'] : '')); ?></textarea>
                </div>

                <!-- Konten Lengkap -->
                <div class="col-12" id="wrapKontenLengkap">
                    <label class="form-label fw-bold text-dark" id="lblKontenLengkap">Isi Berita / Deskripsi Lengkap</label>
                    <textarea name="konten" class="form-control" rows="8"
                              placeholder="Tulis isi artikel atau deskripsi lengkap di sini (pisahkan antar paragraf dengan baris baru)..."><?= esc(old('konten', $isEdit ? $item['konten'] : '')); ?></textarea>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                <a href="/cms<?= $curTipe ? '?tipe=' . esc($curTipe) : '' ?>" class="btn btn-light border rounded-pill px-4 fw-semibold">Batal</a>
                <button type="submit" class="btn btn-primary rounded-pill px-5 fw-semibold" style="background-color: var(--inkindo-blue); border: none;">
                    <i class="bi bi-save me-1"></i> <?= $isEdit ? 'Simpan Perubahan' : 'Simpan Konten'; ?>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function syncCmsFieldsByType() {
    var tipe = document.getElementById('cmsTipeSelect').value;
    var secAgenda = document.getElementById('sectionAgendaFields');
    var secPdf = document.getElementById('sectionPdfFields');
    var secGaleri = document.getElementById('sectionGaleriFields');
    var lblBadge = document.getElementById('lblBadge');
    var inpBadge = document.getElementById('inpBadge');
    var lblPenulis = document.getElementById('lblPenulis');

    secAgenda.style.display = (tipe === 'agenda') ? 'block' : 'none';
    secPdf.style.display = (tipe === 'emagazine') ? 'block' : 'none';
    secGaleri.style.display = (tipe === 'galeri') ? 'block' : 'none';

    if (tipe === 'karya') {
        lblBadge.textContent = 'Tahun Terbit Buku (Badge)';
        inpBadge.placeholder = 'Contoh: 2025';
        lblPenulis.textContent = 'Nama Penulis Buku';
    } else if (tipe === 'emagazine') {
        lblBadge.textContent = 'Nomor Edisi / Tahun (Badge)';
        inpBadge.placeholder = 'Contoh: Edisi 80';
        lblPenulis.textContent = 'Redaksi / Penerbit';
    } else if (tipe === 'galeri') {
        lblBadge.textContent = 'Label Jumlah Foto (Opsional)';
        inpBadge.placeholder = 'Otomatis terhitung jika dikosongkan (misal: 12 Foto)';
        lblPenulis.textContent = 'Fotografer / Dokumentasi';
    } else {
        lblBadge.textContent = 'Label Badge Pojok Gambar';
        inpBadge.placeholder = 'Contoh: Rilis Resmi / Hybrid Event / Pedoman 2026';
        lblPenulis.textContent = 'Sumber / Penulis Berita';
    }
}
document.addEventListener('DOMContentLoaded', syncCmsFieldsByType);
</script>
<?= $this->endSection() ?>

