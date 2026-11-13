<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<?php
    $kategoriLabels = [
        'inkindo' => 'Regulasi INKINDO',
        'jasa'    => 'Regulasi Jasa Konsultasi',
        'terkait' => 'Regulasi Terkait',
    ];
    $subLabels = [
        'kontruksi'     => 'Konstruksi',
        'non-kontruksi' => 'Non Konstruksi',
        'umum'          => 'Umum',
    ];
    $defaultFormKategori = $filterKategori !== '' ? $filterKategori : 'inkindo';
    $defaultFormSub      = ($defaultFormKategori === 'jasa') ? ($filterSub !== '' ? $filterSub : 'umum') : '';
?>

<div class="d-flex justify-content-between align-items-center mb-4 mt-2 flex-wrap gap-3">
    <div>
        <h3 class="fw-bold mb-1" style="color: var(--inkindo-blue);">Admin CMS — Regulasi</h3>
        <p class="text-muted small mb-0">Kelola dokumen <strong>Regulasi &amp; Kebijakan</strong> pada halaman <code>regulasi.html</code> di <code>inkindo-dki-web</code> (maksimal 6 data per halaman).</p>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <a href="<?= esc(rtrim(config('App')->webProfileURL, '/')); ?>/regulasi.html" target="_blank" class="btn btn-outline-primary rounded-pill px-3 fw-semibold">
            <i class="bi bi-box-arrow-up-right me-1"></i> Pratinjau Halaman Regulasi
        </a>
        <button type="button" class="btn btn-primary rounded-pill px-4 fw-semibold shadow-sm" style="background-color: var(--inkindo-blue); border: none;" onclick="openRegulasiModal()">
            <i class="bi bi-plus-lg me-1"></i> Tambah Regulasi
        </button>
    </div>
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

<!-- Filter Kategori Halaman Regulasi -->
<div class="d-flex flex-wrap gap-2 mb-3">
    <a href="/cms/regulasi" class="btn btn-sm rounded-pill px-3 fw-semibold <?= $filterKategori === '' ? 'btn-primary' : 'btn-outline-secondary' ?>" <?= $filterKategori === '' ? 'style="background-color: var(--inkindo-blue); border-color: var(--inkindo-blue);"' : '' ?>>
        Semua (<?= (int) ($counts['semua'] ?? 0); ?>)
    </a>
    <?php foreach ($kategoriLabels as $kKey => $kLabel): ?>
        <a href="/cms/regulasi?kategori=<?= esc($kKey); ?>" class="btn btn-sm rounded-pill px-3 fw-semibold <?= $filterKategori === $kKey ? 'btn-primary' : 'btn-outline-secondary' ?>" <?= $filterKategori === $kKey ? 'style="background-color: var(--inkindo-blue); border-color: var(--inkindo-blue);"' : '' ?>>
            <?= esc($kLabel); ?> (<?= (int) ($counts[$kKey] ?? 0); ?>)
        </a>
    <?php endforeach; ?>
</div>

<?php if ($filterKategori === 'jasa'): ?>
    <div class="d-flex flex-wrap gap-2 mb-4">
        <a href="/cms/regulasi?kategori=jasa" class="btn btn-xs btn-sm rounded-pill px-3 <?= $filterSub === '' ? 'btn-dark' : 'btn-light border' ?>">
            Semua Jasa Konsultasi
        </a>
        <?php foreach ($subLabels as $sKey => $sLabel): ?>
            <a href="/cms/regulasi?kategori=jasa&sub=<?= esc($sKey); ?>" class="btn btn-xs btn-sm rounded-pill px-3 <?= $filterSub === $sKey ? 'btn-dark' : 'btn-light border' ?>">
                <?= esc($sLabel); ?>
            </a>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<!-- Tabel Daftar Regulasi (Pagination DataTables max 6 per halaman) -->
<div class="table-custom-wrap">
    <div class="table-responsive" style="overflow-x: visible;">
        <table class="table table-custom align-middle">
            <thead>
                <tr>
                    <th width="50">No</th>
                    <th>Judul &amp; Deskripsi Regulasi</th>
                    <th width="210">File PDF</th>
                    <th width="140" class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; foreach ($items as $row): ?>
                <tr>
                    <td class="fw-bold text-muted"><?= $no++; ?></td>
                    <td>
                        <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                            <span class="badge badge-soft-primary rounded-pill" style="font-size: 11px;">
                                <?= esc($kategoriLabels[$row['kategori']] ?? $row['kategori']); ?>
                            </span>
                            <?php if (! empty($row['sub_kategori'])): ?>
                                <span class="badge bg-light text-secondary border rounded-pill" style="font-size: 11px;">
                                    <?= esc($subLabels[$row['sub_kategori']] ?? $row['sub_kategori']); ?>
                                </span>
                            <?php endif; ?>
                        </div>
                        <div class="fw-bold text-dark mb-1"><?= esc($row['judul']); ?></div>
                        <div class="text-muted small" style="line-height: 1.45;">
                            <?= esc(mb_strimwidth((string) ($row['deskripsi'] ?? ''), 0, 145, '...')); ?>
                        </div>
                    </td>
                    <td>
                        <?php if (! empty($row['file_pdf'])): ?>
                            <a href="/<?= esc(ltrim($row['file_pdf'], '/')); ?>" target="_blank" class="btn btn-sm btn-outline-danger rounded-pill px-3 fw-semibold d-inline-flex align-items-center gap-1" title="<?= esc($row['file_name'] ?: basename($row['file_pdf'])); ?>">
                                <i class="bi bi-file-earmark-pdf-fill"></i>
                                <span class="text-truncate" style="max-width: 130px;"><?= esc($row['file_name'] ?: 'Lihat PDF'); ?></span>
                            </a>
                        <?php else: ?>
                            <span class="badge bg-warning-subtle text-warning-emphasis rounded-pill px-3 py-2 fw-semibold">
                                <i class="bi bi-exclamation-circle me-1"></i> Belum ada PDF
                            </span>
                        <?php endif; ?>
                    </td>
                    <td class="text-end">
                        <div class="d-inline-flex gap-1">
                            <button type="button"
                                    class="btn btn-sm btn-outline-primary rounded-circle"
                                    title="Edit Regulasi"
                                    style="width: 34px; height: 34px;"
                                    onclick='openRegulasiModal(<?= json_encode([
                                        'id'           => (int) $row['id'],
                                        'kategori'     => (string) $row['kategori'],
                                        'sub_kategori' => (string) ($row['sub_kategori'] ?? ''),
                                        'judul'        => (string) $row['judul'],
                                        'deskripsi'    => (string) ($row['deskripsi'] ?? ''),
                                        'file_pdf'     => (string) ($row['file_pdf'] ?? ''),
                                        'file_name'    => (string) ($row['file_name'] ?? ''),
                                    ], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT); ?>)'>
                                <i class="bi bi-pencil-fill"></i>
                            </button>
                            <a href="/cms/regulasi/delete/<?= (int) $row['id']; ?>"
                               class="btn btn-sm btn-outline-danger rounded-circle"
                               title="Hapus Regulasi"
                               style="width: 34px; height: 34px; display: inline-flex; align-items: center; justify-content: center;"
                               onclick="return confirm('Yakin ingin menghapus regulasi ini beserta file PDF-nya?');">
                                <i class="bi bi-trash-fill"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Tambah / Edit Regulasi -->
<div class="modal fade" id="regulasiModal" tabindex="-1" aria-labelledby="regulasiModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="/cms/regulasi/save" method="post" enctype="multipart/form-data" class="modal-content border-0 shadow-lg" style="border-radius: 18px;">
            <input type="hidden" name="id" id="reg_id" value="0">
            <input type="hidden" name="return_all" value="<?= $filterKategori === '' ? '1' : '0'; ?>">

            <div class="modal-header border-0 pb-0 pt-4 px-4">
                <div>
                    <h5 class="modal-title fw-bold" id="regulasiModalLabel" style="color: var(--inkindo-blue);">Tambah Regulasi</h5>
                    <span class="badge badge-soft-primary rounded-pill mt-1" id="reg_kategori_badge" style="font-size: 11px;">
                        <?= esc($kategoriLabels[$defaultFormKategori] ?? 'Regulasi INKINDO'); ?>
                    </span>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>

            <div class="modal-body px-4 py-3">
                <div class="row g-2 mb-3">
                    <div class="col-12" id="reg_kat_col">
                        <label for="reg_kategori" class="form-label fw-semibold small text-dark">Kategori Regulasi <span class="text-danger">*</span></label>
                        <select class="form-select" name="kategori" id="reg_kategori" required onchange="syncRegulasiCategoryUI()">
                            <?php foreach ($kategoriLabels as $kKey => $kLabel): ?>
                                <option value="<?= esc($kKey); ?>"><?= esc($kLabel); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-12" id="reg_sub_wrap" style="display: none;">
                        <label for="reg_sub_kategori" class="form-label fw-semibold small text-dark">Sub Kategori Jasa Konsultasi <span class="text-danger">*</span></label>
                        <select class="form-select" name="sub_kategori" id="reg_sub_kategori" onchange="syncRegulasiCategoryUI()">
                            <?php foreach ($subLabels as $sKey => $sLabel): ?>
                                <option value="<?= esc($sKey); ?>"><?= esc($sLabel); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="reg_judul" class="form-label fw-semibold small text-dark">Judul Regulasi <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="judul" id="reg_judul" placeholder="Contoh: UU No. 2 Tahun 2017 tentang Jasa Konstruksi" required>
                </div>

                <div class="mb-3">
                    <label for="reg_deskripsi" class="form-label fw-semibold small text-dark">Deskripsi Singkat <span class="text-danger">*</span></label>
                    <textarea class="form-control" name="deskripsi" id="reg_deskripsi" rows="4" placeholder="Tuliskan ringkasan isi regulasi..." required></textarea>
                </div>

                <div class="mb-2">
                    <label for="reg_pdf_file" class="form-label fw-semibold small text-dark">Upload File PDF <span class="text-danger" id="reg_pdf_required_mark">*</span></label>
                    <input type="file" class="form-control" name="pdf_file" id="reg_pdf_file" accept=".pdf,application/pdf">
                    <div class="form-text small" id="reg_pdf_help">Format wajib <code>.pdf</code> (maks. 20 MB).</div>
                    <div class="mt-2 small" id="reg_current_pdf_wrap" style="display: none;">
                        File saat ini: <a href="#" target="_blank" id="reg_current_pdf_link" class="fw-semibold text-danger text-decoration-none"><i class="bi bi-file-earmark-pdf-fill me-1"></i><span id="reg_current_pdf_name"></span></a>
                    </div>
                </div>
            </div>

            <div class="modal-footer border-0 pt-0 pb-4 px-4">
                <button type="button" class="btn btn-light rounded-pill px-4 fw-semibold" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary rounded-pill px-4 fw-semibold" style="background-color: var(--inkindo-blue); border: none;">
                    <i class="bi bi-save me-1"></i> Simpan Regulasi
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    const REG_KATEGORI_LABELS = {
        inkindo: 'Regulasi INKINDO',
        jasa: 'Regulasi Jasa Konsultasi',
        terkait: 'Regulasi Terkait'
    };
    const REG_SUB_LABELS = {
        'kontruksi': 'Konstruksi',
        'non-kontruksi': 'Non Konstruksi',
        'umum': 'Umum'
    };

    function syncRegulasiCategoryUI() {
        const katEl = document.getElementById('reg_kategori');
        const subWrap = document.getElementById('reg_sub_wrap');
        const subEl = document.getElementById('reg_sub_kategori');
        const badgeEl = document.getElementById('reg_kategori_badge');

        const isJasa = katEl.value === 'jasa';
        subWrap.style.display = isJasa ? 'block' : 'none';
        subEl.disabled = !isJasa;
        if (isJasa && !subEl.value) {
            subEl.value = 'umum';
        }

        const kLabel = REG_KATEGORI_LABELS[katEl.value] || katEl.value;
        const sLabel = (isJasa && subEl.value && REG_SUB_LABELS[subEl.value])
            ? ' • ' + REG_SUB_LABELS[subEl.value]
            : '';
        badgeEl.textContent = kLabel + sLabel;
    }

    function openRegulasiModal(item = null) {
        const modalEl = document.getElementById('regulasiModal');
        const titleEl = document.getElementById('regulasiModalLabel');
        const idEl = document.getElementById('reg_id');
        const katEl = document.getElementById('reg_kategori');
        const subEl = document.getElementById('reg_sub_kategori');
        const judulEl = document.getElementById('reg_judul');
        const descEl = document.getElementById('reg_deskripsi');
        const pdfEl = document.getElementById('reg_pdf_file');
        const reqMark = document.getElementById('reg_pdf_required_mark');
        const helpEl = document.getElementById('reg_pdf_help');
        const curWrap = document.getElementById('reg_current_pdf_wrap');
        const curLink = document.getElementById('reg_current_pdf_link');
        const curName = document.getElementById('reg_current_pdf_name');

        pdfEl.value = '';

        if (item) {
            titleEl.textContent = 'Edit Regulasi';
            idEl.value = item.id;
            katEl.value = item.kategori || 'inkindo';
            subEl.value = item.sub_kategori || 'umum';
            judulEl.value = item.judul || '';
            descEl.value = item.deskripsi || '';

            const hasExistingPdf = Boolean(item.file_pdf);
            pdfEl.required = !hasExistingPdf;
            reqMark.style.display = hasExistingPdf ? 'none' : 'inline';
            helpEl.textContent = hasExistingPdf
                ? 'Kosongkan jika tidak ingin mengganti file PDF saat ini (maks. 20 MB).'
                : 'Format wajib .pdf (maks. 20 MB).';

            if (hasExistingPdf) {
                curWrap.style.display = 'block';
                curLink.href = '/' + String(item.file_pdf).replace(/^\/+/, '');
                curName.textContent = item.file_name || 'Dokumen PDF';
            } else {
                curWrap.style.display = 'none';
            }
        } else {
            titleEl.textContent = 'Tambah Regulasi';
            idEl.value = '0';
            katEl.value = <?= json_encode($defaultFormKategori); ?>;
            subEl.value = <?= json_encode($defaultFormSub ?: 'umum'); ?>;
            judulEl.value = '';
            descEl.value = '';
            pdfEl.required = true;
            reqMark.style.display = 'inline';
            helpEl.innerHTML = 'Format wajib <code>.pdf</code> (maks. 20 MB).';
            curWrap.style.display = 'none';
        }

        syncRegulasiCategoryUI();

        const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
        modal.show();
    }
</script>
<?= $this->endSection() ?>

