<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lengkapi Data Perusahaan - SIM-INKINDO DKI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --inkindo-blue: #0A64A4;
            --bg-app: #F0F3F7;
        }
        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-app);
            color: #1E293B;
        }
        .onboarding-card {
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 12px 32px rgba(15, 23, 42, 0.08);
            border: 1px solid #e2e8f0;
        }
        .btn-inkindo {
            background-color: var(--inkindo-blue);
            color: #ffffff;
            border: 1px solid var(--inkindo-blue);
            font-weight: 600;
            border-radius: 10px;
        }
        .btn-inkindo:hover {
            background-color: #085287;
            border-color: #085287;
            color: #ffffff;
        }
    </style>
</head>
<body>
    <div class="container py-4 py-md-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="onboarding-card p-4 p-md-5">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4 pb-3 border-bottom">
                        <div class="d-flex align-items-center gap-3">
                            <img src="/logo-inkindo.png" alt="INKINDO DKI" style="height: 44px; width: auto;">
                            <div>
                                <h4 class="fw-bold mb-1" style="color: var(--inkindo-blue);">Lengkapi Data Perusahaan (PT)</h4>
                                <p class="text-muted small mb-0">Langkah terakhir sebelum masuk ke Dashboard Anggota SIM-INKINDO DKI.</p>
                            </div>
                        </div>
                        <a href="/auth/logout" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                            <i class="bi bi-box-arrow-right me-1"></i> Keluar
                        </a>
                    </div>

                    <?php if (session()->getFlashdata('success')): ?>
                        <div class="alert alert-success"><?= esc((string) session()->getFlashdata('success')) ?></div>
                    <?php endif; ?>

                    <form action="/perusahaan/store" method="post">
<?= csrf_field() ?>
                        <div class="mb-3 row">
                            <label class="col-sm-3 col-form-label fw-semibold">Nama PT <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name="nama_pt" required placeholder="Contoh: PT. Inkindo">
                            </div>
                        </div>
                        <div class="mb-3 row">
                            <label class="col-sm-3 col-form-label fw-semibold">Nama PJBU <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name="nama_pjbu" required placeholder="Nama Penanggung Jawab Badan Usaha">
                            </div>
                        </div>
                        <div class="mb-3 row">
                            <label class="col-sm-3 col-form-label fw-semibold">No KTA / NPA <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name="no_kta" required placeholder="Contoh: 14642/P/3046.DKI">
                            </div>
                        </div>
                        <div class="mb-3 row">
                            <label class="col-sm-3 col-form-label fw-semibold">Status Keanggotaan <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <select class="form-select" name="status" required>
                                    <option value="Penuh" selected>Penuh</option>
                                    <option value="Biasa">Biasa</option>
                                    <option value="Afiliasi">Afiliasi</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3 row">
                            <label class="col-sm-3 col-form-label fw-semibold">Kualifikasi <span class="text-danger">*</span></label>
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
                            <label class="col-sm-3 col-form-label fw-semibold">Wilayah DKI Jakarta <span class="text-danger">*</span></label>
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
                            <label class="col-sm-3 col-form-label fw-semibold">Kategori SBU <span class="text-danger">*</span></label>
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
                            <label class="col-sm-3 col-form-label fw-semibold">Alamat Domisili PT <span class="text-danger">*</span></label>
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
                            <label class="col-sm-3 col-form-label fw-semibold">No. Telepon &amp; Fax</label>
                            <div class="col-sm-5 mb-2 mb-sm-0">
                                <input type="text" class="form-control" name="telepon" placeholder="No. Telepon (Contoh: 021-7942940)">
                            </div>
                            <div class="col-sm-4">
                                <input type="text" class="form-control" name="fax" placeholder="No. Fax">
                            </div>
                        </div>
                        <div class="mb-4 row">
                            <label class="col-sm-3 col-form-label fw-semibold">Email &amp; Website</label>
                            <div class="col-sm-5 mb-2 mb-sm-0">
                                <input type="email" class="form-control" name="email" placeholder="Email resmi perusahaan (untuk tampilan web)">
                                <div class="form-text">Ditampilkan di direktori web publik &amp; bisa diubah kapan saja (terpisah dari email login).</div>
                            </div>
                            <div class="col-sm-4">
                                <input type="text" class="form-control" name="website" placeholder="www.perusahaan.co.id">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-9 offset-sm-3">
                                <button type="submit" class="btn btn-inkindo px-4 py-2">
                                    Simpan &amp; Masuk Dashboard <i class="bi bi-arrow-right ms-1"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
