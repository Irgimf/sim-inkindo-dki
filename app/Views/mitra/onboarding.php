<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lengkapi Data Mitra Kerja - SIM-INKINDO DKI</title>
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
            <div class="col-lg-9">
                <div class="onboarding-card p-4 p-md-5">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4 pb-3 border-bottom">
                        <div class="d-flex align-items-center gap-3">
                            <img src="/logo-inkindo.png" alt="INKINDO DKI" style="height: 44px; width: auto;">
                            <div>
                                <h4 class="fw-bold mb-1" style="color: var(--inkindo-blue);">Lengkapi Profil Mitra Kerja</h4>
                                <p class="text-muted small mb-0">Isi data perusahaan, logo, dan layanan kemitraan Anda sebelum masuk ke Portal Mitra Kerja.</p>
                            </div>
                        </div>
                        <a href="/auth/logout" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                            <i class="bi bi-box-arrow-right me-1"></i> Keluar
                        </a>
                    </div>

                    <?php if (session()->getFlashdata('success')): ?>
                        <div class="alert alert-success"><?= esc((string) session()->getFlashdata('success')) ?></div>
                    <?php endif; ?>
                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger"><?= esc((string) session()->getFlashdata('error')) ?></div>
                    <?php endif; ?>

                    <form action="/mitra/store" method="post" enctype="multipart/form-data">
                        <div class="row g-3">
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
                                <input type="email" class="form-control" name="email" value="<?= esc((string) ($userEmail ?? '')) ?>" placeholder="Contoh: info@perusahaan.co.id">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Website</label>
                                <input type="text" class="form-control" name="website" placeholder="Contoh: https://www.perusahaan.co.id">
                            </div>
                        </div>

                        <div class="mt-4 pt-2">
                            <button type="submit" class="btn btn-inkindo px-4 py-2">
                                <i class="bi bi-send-check me-1"></i> Simpan &amp; Ajukan Mitra Kerja
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('logoInput')?.addEventListener('change', function (e) {
            const file = e.target.files[0];
            const img = document.getElementById('logoPreviewImg');
            const txt = document.getElementById('logoPreviewText');
            if (file && img && txt) {
                const reader = new FileReader();
                reader.onload = function (ev) {
                    img.src = ev.target.result;
                    img.style.display = 'block';
                    txt.style.display = 'none';
                };
                reader.readAsDataURL(file);
            }
        });
    </script>
</body>
</html>
