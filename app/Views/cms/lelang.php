<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<?php
    $iconPresets = [
        'flight_takeoff'         => 'Pesawat / Bandara',
        'bolt'                   => 'Listrik / Energi',
        'train'                  => 'Kereta / Transportasi',
        'account_balance'        => 'Bank / Lembaga',
        'public'                 => 'Global / Internasional',
        'shopping_cart_checkout' => 'Pengadaan / LPSE',
        'business'               => 'Perusahaan / BUMN',
        'engineering'            => 'Konstruksi / Teknik',
        'gavel'                  => 'Lelang / Tender',
        'work'                   => 'Peluang Usaha',
    ];
?>

<div class="d-flex justify-content-between align-items-center mb-4 mt-2 flex-wrap gap-3">
    <div>
        <h3 class="fw-bold mb-1" style="color: var(--inkindo-blue);">Admin CMS — Lelang Lainnya</h3>
        <p class="text-muted small mb-0">Kelola daftar <strong>Portal e-Procurement</strong> dan pemberitahuan <strong>Peluang Usaha Lainnya</strong> pada halaman <code>lelang-lainnya.html</code>.</p>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <a href="<?= esc(rtrim(config('App')->webProfileURL, '/')); ?>/lelang-lainnya.html" target="_blank" class="btn btn-outline-primary rounded-pill px-3 fw-semibold">
            <i class="bi bi-box-arrow-up-right me-1"></i> Pratinjau Lelang Lainnya
        </a>
        <?php if ($activeTab === 'portal'): ?>
            <button type="button" class="btn btn-primary rounded-pill px-4 fw-semibold shadow-sm" style="background-color: var(--inkindo-blue); border: none;" onclick="openPortalModal()">
                <i class="bi bi-plus-lg me-1"></i> Tambah Portal Lelang
            </button>
        <?php endif; ?>
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

<!-- Tabs CMS Lelang Lainnya -->
<div class="d-flex flex-wrap gap-2 mb-4">
    <a href="/cms/lelang?tab=portal" class="btn btn-sm rounded-pill px-4 fw-semibold <?= $activeTab === 'portal' ? 'btn-primary' : 'btn-outline-secondary' ?>" <?= $activeTab === 'portal' ? 'style="background-color: var(--inkindo-blue); border-color: var(--inkindo-blue);"' : '' ?>>
        <i class="bi bi-grid-3x3-gap me-1"></i> Portal e-Procurement (<?= count($portals); ?>)
    </a>
    <a href="/cms/lelang?tab=peluang" class="btn btn-sm rounded-pill px-4 fw-semibold <?= $activeTab === 'peluang' ? 'btn-primary' : 'btn-outline-secondary' ?>" <?= $activeTab === 'peluang' ? 'style="background-color: var(--inkindo-blue); border-color: var(--inkindo-blue);"' : '' ?>>
        <i class="bi bi-megaphone me-1"></i> Peluang Usaha &amp; Header Halaman
    </a>
</div>

<?php if ($activeTab === 'portal'): ?>
    <!-- TAB 1: PORTAL E-PROCUREMENT (CRUD) -->
    <div class="table-custom-wrap">
        <div class="table-responsive" style="overflow-x: visible;">
            <table class="table table-custom align-middle">
                <thead>
                    <tr>
                        <th width="50">No</th>
                        <th>Kategori &amp; Nama Portal</th>
                        <th width="250">URL Portal</th>
                        <th width="80" class="text-center">Urutan</th>
                        <th width="130" class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; foreach ($portals as $row): ?>
                    <tr>
                        <td class="fw-bold text-muted"><?= $no++; ?></td>
                        <td>
                            <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                                <span class="badge badge-soft-primary rounded-pill" style="font-size: 11px;">
                                    <?= esc($row['kategori']); ?>
                                </span>
                                <span class="badge bg-light text-secondary border rounded-pill" style="font-size: 11px;">
                                    ikon: <code><?= esc($row['ikon']); ?></code>
                                </span>
                            </div>
                            <div class="fw-bold text-dark mb-1"><?= esc($row['judul']); ?></div>
                            <div class="text-muted small" style="line-height: 1.45;">
                                <?= esc(mb_strimwidth((string) ($row['deskripsi'] ?? ''), 0, 140, '...')); ?>
                            </div>
                        </td>
                        <td>
                            <a href="<?= esc($row['url']); ?>" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-semibold d-inline-flex align-items-center gap-1" title="<?= esc($row['url']); ?>">
                                <i class="bi bi-box-arrow-up-right"></i>
                                <span class="text-truncate" style="max-width: 170px;"><?= esc($row['teks_tombol']); ?></span>
                            </a>
                        </td>
                        <td class="text-center fw-bold text-muted"><?= (int) $row['urutan']; ?></td>
                        <td class="text-end">
                            <div class="d-inline-flex gap-1">
                                <button type="button"
                                        class="btn btn-sm btn-outline-primary rounded-circle"
                                        title="Edit Portal"
                                        style="width: 34px; height: 34px;"
                                        onclick='openPortalModal(<?= json_encode([
                                            'id'          => (int) $row['id'],
                                            'ikon'        => (string) $row['ikon'],
                                            'kategori'    => (string) $row['kategori'],
                                            'judul'       => (string) $row['judul'],
                                            'deskripsi'   => (string) ($row['deskripsi'] ?? ''),
                                            'teks_tombol' => (string) ($row['teks_tombol'] ?? 'Buka Portal Pengadaan'),
                                            'url'         => (string) $row['url'],
                                            'urutan'      => (int) ($row['urutan'] ?? 1),
                                        ], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT); ?>)'>
                                    <i class="bi bi-pencil-fill"></i>
                                </button>
                                <a href="/cms/lelang/portal/delete/<?= (int) $row['id']; ?>"
                                   class="btn btn-sm btn-outline-danger rounded-circle"
                                   title="Hapus Portal"
                                   style="width: 34px; height: 34px; display: inline-flex; align-items: center; justify-content: center;"
                                   onclick="return confirm('Yakin ingin menghapus portal lelang ini?');">
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

    <!-- Modal Tambah / Edit Portal e-Procurement -->
    <div class="modal fade" id="portalModal" tabindex="-1" aria-labelledby="portalModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <form action="/cms/lelang/portal/save" method="post" class="modal-content border-0 shadow-lg" style="border-radius: 18px;">
                <input type="hidden" name="id" id="portal_id" value="0">

                <div class="modal-header border-0 pb-0 pt-4 px-4">
                    <h5 class="modal-title fw-bold" id="portalModalLabel" style="color: var(--inkindo-blue);">Tambah Portal e-Procurement</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>

                <div class="modal-body px-4 py-3">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="portal_kategori" class="form-label fw-semibold small text-dark">Kategori / Instansi <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="kategori" id="portal_kategori" placeholder="Contoh: BUMN Ketenagalistrikan" required>
                        </div>
                        <div class="col-md-4">
                            <label for="portal_ikon" class="form-label fw-semibold small text-dark">Ikon Kartu</label>
                            <select class="form-select" name="ikon" id="portal_ikon">
                                <?php foreach ($iconPresets as $iKey => $iLabel): ?>
                                    <option value="<?= esc($iKey); ?>"><?= esc($iLabel); ?> (<?= esc($iKey); ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="portal_urutan" class="form-label fw-semibold small text-dark">Urutan</label>
                            <input type="number" class="form-control" name="urutan" id="portal_urutan" min="1" value="1">
                        </div>

                        <div class="col-12">
                            <label for="portal_judul" class="form-label fw-semibold small text-dark">Nama Portal / Lembaga <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="judul" id="portal_judul" placeholder="Contoh: PT Perusahaan Listrik Negara (Persero)" required>
                        </div>

                        <div class="col-12">
                            <label for="portal_deskripsi" class="form-label fw-semibold small text-dark">Deskripsi Singkat</label>
                            <textarea class="form-control" name="deskripsi" id="portal_deskripsi" rows="3" placeholder="Tuliskan deskripsi singkat portal pengadaan..."></textarea>
                        </div>

                        <div class="col-md-7">
                            <label for="portal_url" class="form-label fw-semibold small text-dark">URL Portal Pengadaan <span class="text-danger">*</span></label>
                            <input type="url" class="form-control" name="url" id="portal_url" placeholder="https://eproc.pln.co.id/" required>
                        </div>
                        <div class="col-md-5">
                            <label for="portal_teks_tombol" class="form-label fw-semibold small text-dark">Teks Tombol</label>
                            <input type="text" class="form-control" name="teks_tombol" id="portal_teks_tombol" placeholder="Buka Portal Pengadaan">
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-0 pt-0 pb-4 px-4">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-semibold" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-semibold" style="background-color: var(--inkindo-blue); border: none;">
                        <i class="bi bi-save me-1"></i> Simpan Portal
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openPortalModal(item = null) {
            const modalEl = document.getElementById('portalModal');
            const titleEl = document.getElementById('portalModalLabel');

            if (item) {
                titleEl.textContent = 'Edit Portal e-Procurement';
                document.getElementById('portal_id').value = item.id;
                document.getElementById('portal_kategori').value = item.kategori || '';
                document.getElementById('portal_ikon').value = item.ikon || 'public';
                document.getElementById('portal_urutan').value = item.urutan || 1;
                document.getElementById('portal_judul').value = item.judul || '';
                document.getElementById('portal_deskripsi').value = item.deskripsi || '';
                document.getElementById('portal_url').value = item.url || '';
                document.getElementById('portal_teks_tombol').value = item.teks_tombol || 'Buka Portal Pengadaan';
            } else {
                titleEl.textContent = 'Tambah Portal e-Procurement';
                document.getElementById('portal_id').value = '0';
                document.getElementById('portal_kategori').value = '';
                document.getElementById('portal_ikon').value = 'business';
                document.getElementById('portal_urutan').value = '<?= count($portals) + 1; ?>';
                document.getElementById('portal_judul').value = '';
                document.getElementById('portal_deskripsi').value = '';
                document.getElementById('portal_url').value = '';
                document.getElementById('portal_teks_tombol').value = 'Buka Portal Pengadaan';
            }

            bootstrap.Modal.getOrCreateInstance(modalEl).show();
        }
    </script>

<?php else: ?>
    <!-- TAB 2: PELUANG USAHA LAINNYA & HERO -->
    <form action="/cms/lelang/peluang/save" method="post">
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-3" style="color: var(--inkindo-blue);">1. Header Halaman (Hero Section)</h5>
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label fw-semibold small text-dark">Judul Utama Halaman</label>
                        <input type="text" class="form-control" name="hero_judul" value="<?= esc($peluang['hero_judul'] ?? ''); ?>" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold small text-dark">Deskripsi Hero</label>
                        <textarea class="form-control" name="hero_deskripsi" rows="3" required><?= esc($peluang['hero_deskripsi'] ?? ''); ?></textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-3" style="color: var(--inkindo-blue);">2. Surat Pemberitahuan Peluang Usaha Lainnya</h5>
                <div class="row g-3">
                    <div class="col-md-5">
                        <label class="form-label fw-semibold small text-dark">Badge Bagian</label>
                        <input type="text" class="form-control" name="section_badge" value="<?= esc($peluang['section_badge'] ?? ''); ?>" required>
                    </div>
                    <div class="col-md-7">
                        <label class="form-label fw-semibold small text-dark">Judul Bagian</label>
                        <input type="text" class="form-control" name="section_judul" value="<?= esc($peluang['section_judul'] ?? ''); ?>" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold small text-dark">Subjudul Bagian</label>
                        <input type="text" class="form-control" name="section_deskripsi" value="<?= esc($peluang['section_deskripsi'] ?? ''); ?>">
                    </div>

                    <hr class="my-2">

                    <div class="col-md-4">
                        <label class="form-label fw-semibold small text-dark">Label Surat</label>
                        <input type="text" class="form-control" name="surat_label" value="<?= esc($peluang['surat_label'] ?? ''); ?>" required>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label fw-semibold small text-dark">Judul Surat Pemberitahuan</label>
                        <input type="text" class="form-control" name="surat_judul" value="<?= esc($peluang['surat_judul'] ?? ''); ?>" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold small text-dark">Status</label>
                        <?php $st = $peluang['surat_status'] ?? 'Terbuka'; ?>
                        <select class="form-select" name="surat_status">
                            <option value="Terbuka" <?= $st === 'Terbuka' ? 'selected' : ''; ?>>Terbuka</option>
                            <option value="Ditutup" <?= $st === 'Ditutup' ? 'selected' : ''; ?>>Ditutup</option>
                            <option value="Segera Hadir" <?= $st === 'Segera Hadir' ? 'selected' : ''; ?>>Segera Hadir</option>
                        </select>
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold small text-dark">Ditujukan Kepada (Kepada Yth.)</label>
                        <input type="text" class="form-control" name="surat_kepada" value="<?= esc($peluang['surat_kepada'] ?? ''); ?>" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold small text-dark">Paragraf Pengantar Surat</label>
                        <textarea class="form-control" name="surat_pengantar" rows="3" required><?= esc($peluang['surat_pengantar'] ?? ''); ?></textarea>
                    </div>

                    <div class="col-12">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="form-label fw-semibold small text-dark mb-0">Daftar Poin Kualifikasi</label>
                            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-semibold" onclick="addKualifikasiRow()">
                                <i class="bi bi-plus-lg me-1"></i> Tambah Poin
                            </button>
                        </div>
                        <div id="kualifikasi-list" class="d-grid gap-2">
                            <?php if (empty($kualifikasi)): ?>
                                <div class="input-group">
                                    <input type="text" class="form-control" name="kualifikasi[]" placeholder="Tuliskan poin kualifikasi...">
                                    <button type="button" class="btn btn-outline-danger" onclick="removeKualifikasiRow(this)"><i class="bi bi-trash"></i></button>
                                </div>
                            <?php else: ?>
                                <?php foreach ($kualifikasi as $kItem): ?>
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="kualifikasi[]" value="<?= esc($kItem); ?>" placeholder="Tuliskan poin kualifikasi...">
                                        <button type="button" class="btn btn-outline-danger" onclick="removeKualifikasiRow(this)"><i class="bi bi-trash"></i></button>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold small text-dark">Paragraf Penutup Surat</label>
                        <textarea class="form-control" name="surat_penutup" rows="2" required><?= esc($peluang['surat_penutup'] ?? ''); ?></textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-3" style="color: var(--inkindo-blue);">3. Kontak Layanan Sekretariat</h5>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small text-dark">Nomor Telepon Layanan</label>
                        <input type="text" class="form-control" name="kontak_telp" value="<?= esc($peluang['kontak_telp'] ?? ''); ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small text-dark">Email Sekretariat</label>
                        <input type="email" class="form-control" name="kontak_email" value="<?= esc($peluang['kontak_email'] ?? ''); ?>" required>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end mb-4">
            <button type="submit" class="btn btn-primary rounded-pill px-5 py-2 fw-semibold shadow-sm" style="background-color: var(--inkindo-blue); border: none;">
                <i class="bi bi-save me-1"></i> Simpan Perubahan Peluang Usaha
            </button>
        </div>
    </form>

    <script>
        function addKualifikasiRow() {
            const container = document.getElementById('kualifikasi-list');
            const row = document.createElement('div');
            row.className = 'input-group';
            row.innerHTML = '<input type="text" class="form-control" name="kualifikasi[]" placeholder="Tuliskan poin kualifikasi...">' +
                '<button type="button" class="btn btn-outline-danger" onclick="removeKualifikasiRow(this)"><i class="bi bi-trash"></i></button>';
            container.appendChild(row);
        }

        function removeKualifikasiRow(btn) {
            const container = document.getElementById('kualifikasi-list');
            if (container.children.length <= 1) {
                btn.closest('.input-group').querySelector('input').value = '';
                return;
            }
            btn.closest('.input-group').remove();
        }
    </script>
<?php endif; ?>
<?= $this->endSection() ?>

