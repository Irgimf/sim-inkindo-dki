<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<?php
    $iconPresets = [
        'balance'             => 'Neraca / Prinsip',
        'campaign'            => 'Megafon / Fasilitas',
        'assignment_turned_in'=> 'Checklist / Kewajiban',
        'calendar_clock'      => 'Kalender / Masa Berlaku',
        'security'            => 'Perisai / Integritas',
        'cancel'              => 'Silang / Pengakhiran',
        'gavel'               => 'Palu / Regulasi',
        'verified_user'       => 'Verifikasi / Legalitas',
        'handshake'           => 'Kemitraan',
    ];
?>

<div class="d-flex justify-content-between align-items-center mb-4 mt-2 flex-wrap gap-3">
    <div>
        <h3 class="fw-bold mb-1" style="color: var(--inkindo-blue);">Admin CMS — Ketentuan Mitra Kerja</h3>
        <p class="text-muted small mb-0">Kelola konten halaman <code>ketentuan-mitra-kerja.html</code> (Hero, Maksud &amp; Tujuan, Syarat, dan Ketentuan Mitra Kerja).</p>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <a href="<?= esc(rtrim(config('App')->webProfileURL, '/')); ?>/ketentuan-mitra-kerja.html" target="_blank" class="btn btn-outline-primary rounded-pill px-3 fw-semibold">
            <i class="bi bi-box-arrow-up-right me-1"></i> Pratinjau Halaman
        </a>
        <?php if ($activeTab === 'syarat'): ?>
            <button type="button" class="btn btn-primary rounded-pill px-4 fw-semibold shadow-sm" style="background-color: var(--inkindo-blue); border: none;" onclick="openMkItemModal('syarat')">
                <i class="bi bi-plus-lg me-1"></i> Tambah Syarat
            </button>
        <?php elseif ($activeTab === 'ketentuan'): ?>
            <button type="button" class="btn btn-primary rounded-pill px-4 fw-semibold shadow-sm" style="background-color: var(--inkindo-blue); border: none;" onclick="openMkItemModal('ketentuan')">
                <i class="bi bi-plus-lg me-1"></i> Tambah Ketentuan
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

<!-- Tab Navigasi -->
<div class="d-flex flex-wrap gap-2 mb-4">
    <a href="/cms/ketentuan-mitra?tab=syarat" class="btn btn-sm rounded-pill px-4 fw-semibold <?= $activeTab === 'syarat' ? 'btn-primary' : 'btn-outline-secondary' ?>" <?= $activeTab === 'syarat' ? 'style="background-color: var(--inkindo-blue); border-color: var(--inkindo-blue);"' : '' ?>>
        <i class="bi bi-list-check me-1"></i> Syarat Mitra Kerja (<?= count($syaratItems); ?>)
    </a>
    <a href="/cms/ketentuan-mitra?tab=ketentuan" class="btn btn-sm rounded-pill px-4 fw-semibold <?= $activeTab === 'ketentuan' ? 'btn-primary' : 'btn-outline-secondary' ?>" <?= $activeTab === 'ketentuan' ? 'style="background-color: var(--inkindo-blue); border-color: var(--inkindo-blue);"' : '' ?>>
        <i class="bi bi-shield-check me-1"></i> Ketentuan Mitra Kerja (<?= count($ketentuanItems); ?>)
    </a>
    <a href="/cms/ketentuan-mitra?tab=hero" class="btn btn-sm rounded-pill px-4 fw-semibold <?= $activeTab === 'hero' ? 'btn-primary' : 'btn-outline-secondary' ?>" <?= $activeTab === 'hero' ? 'style="background-color: var(--inkindo-blue); border-color: var(--inkindo-blue);"' : '' ?>>
        <i class="bi bi-card-heading me-1"></i> Hero &amp; Maksud Tujuan
    </a>
</div>

<?php if ($activeTab === 'syarat' || $activeTab === 'ketentuan'): ?>
    <?php
        $isSyarat = $activeTab === 'syarat';
        $rows     = $isSyarat ? $syaratItems : $ketentuanItems;
    ?>

    <!-- Form Header Bagian -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <form action="/cms/ketentuan-mitra/page/save" method="post">
                <input type="hidden" name="section" value="<?= esc($activeTab); ?>">
                <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                    <h6 class="fw-bold mb-0" style="color: var(--inkindo-blue);">
                        Pengantar Bagian <?= $isSyarat ? 'Syarat Mitra Kerja' : 'Ketentuan Mitra Kerja'; ?>
                    </h6>
                    <button type="submit" class="btn btn-sm btn-primary rounded-pill px-4 fw-semibold" style="background-color: var(--inkindo-blue); border: none;">
                        <i class="bi bi-save me-1"></i> Simpan Pengantar
                    </button>
                </div>

                <?php if ($isSyarat): ?>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small text-dark">Label Atas</label>
                            <input type="text" class="form-control" name="syarat_label" value="<?= esc($page['syarat_label'] ?? ''); ?>" required>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label fw-semibold small text-dark">Judul Bagian</label>
                            <input type="text" class="form-control" name="syarat_judul" value="<?= esc($page['syarat_judul'] ?? ''); ?>" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold small text-dark">Deskripsi Pengantar</label>
                            <textarea class="form-control" name="syarat_deskripsi" rows="2" required><?= esc($page['syarat_deskripsi'] ?? ''); ?></textarea>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small text-dark">Judul Kotak Info</label>
                            <input type="text" class="form-control" name="syarat_info_judul" value="<?= esc($page['syarat_info_judul'] ?? ''); ?>" required>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label fw-semibold small text-dark">Isi Kotak Info (Siapa yang Dapat Bermitra)</label>
                            <textarea class="form-control" name="syarat_info_isi" rows="2" required><?= esc($page['syarat_info_isi'] ?? ''); ?></textarea>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small text-dark">Label Atas</label>
                            <input type="text" class="form-control" name="ketentuan_label" value="<?= esc($page['ketentuan_label'] ?? ''); ?>" required>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label fw-semibold small text-dark">Judul Bagian</label>
                            <input type="text" class="form-control" name="ketentuan_judul" value="<?= esc($page['ketentuan_judul'] ?? ''); ?>" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold small text-dark">Deskripsi Pengantar</label>
                            <textarea class="form-control" name="ketentuan_deskripsi" rows="2" required><?= esc($page['ketentuan_deskripsi'] ?? ''); ?></textarea>
                        </div>
                    </div>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <!-- Tabel Daftar Item -->
    <div class="table-custom-wrap">
        <div class="table-responsive" style="overflow-x: visible;">
            <table class="table table-custom align-middle">
                <thead>
                    <tr>
                        <th width="50">No</th>
                        <th>Judul &amp; Uraian <?= $isSyarat ? 'Syarat' : 'Ketentuan'; ?></th>
                        <th width="90" class="text-center">Urutan</th>
                        <th width="130" class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; foreach ($rows as $row): ?>
                    <tr>
                        <td class="fw-bold text-muted"><?= $no++; ?></td>
                        <td>
                            <?php if (! $isSyarat && ! empty($row['ikon'])): ?>
                                <span class="badge bg-light text-secondary border rounded-pill mb-1" style="font-size: 11px;">
                                    ikon: <code><?= esc($row['ikon']); ?></code>
                                </span>
                            <?php endif; ?>
                            <div class="fw-bold text-dark mb-1"><?= esc($row['judul']); ?></div>
                            <div class="text-muted small" style="line-height: 1.45;">
                                <?= esc($row['deskripsi']); ?>
                            </div>
                        </td>
                        <td class="text-center fw-bold text-muted"><?= (int) $row['urutan']; ?></td>
                        <td class="text-end">
                            <div class="d-inline-flex gap-1">
                                <button type="button"
                                        class="btn btn-sm btn-outline-primary rounded-circle"
                                        title="Edit"
                                        style="width: 34px; height: 34px;"
                                        onclick='openMkItemModal(<?= json_encode($activeTab); ?>, <?= json_encode([
                                            'id'        => (int) $row['id'],
                                            'jenis'     => (string) $row['jenis'],
                                            'ikon'      => (string) ($row['ikon'] ?? 'gavel'),
                                            'judul'     => (string) $row['judul'],
                                            'deskripsi' => (string) ($row['deskripsi'] ?? ''),
                                            'urutan'    => (int) ($row['urutan'] ?? 1),
                                        ], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT); ?>)'>
                                    <i class="bi bi-pencil-fill"></i>
                                </button>
                                <a href="/cms/ketentuan-mitra/item/delete/<?= (int) $row['id']; ?>"
                                   class="btn btn-sm btn-outline-danger rounded-circle"
                                   title="Hapus"
                                   style="width: 34px; height: 34px; display: inline-flex; align-items: center; justify-content: center;"
                                   onclick="return confirm('Yakin ingin menghapus item ini?');">
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

    <!-- Modal Tambah / Edit Item Syarat / Ketentuan -->
    <div class="modal fade" id="mkItemModal" tabindex="-1" aria-labelledby="mkItemModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form action="/cms/ketentuan-mitra/item/save" method="post" class="modal-content border-0 shadow-lg" style="border-radius: 18px;">
                <input type="hidden" name="id" id="mk_id" value="0">
                <input type="hidden" name="jenis" id="mk_jenis" value="<?= esc($activeTab); ?>">

                <div class="modal-header border-0 pb-0 pt-4 px-4">
                    <h5 class="modal-title fw-bold" id="mkItemModalLabel" style="color: var(--inkindo-blue);">Tambah Item</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>

                <div class="modal-body px-4 py-3">
                    <div class="row g-3">
                        <div class="col-12" id="mk_ikon_wrap" style="display: none;">
                            <label for="mk_ikon" class="form-label fw-semibold small text-dark">Ikon Kartu Ketentuan</label>
                            <select class="form-select" name="ikon" id="mk_ikon">
                                <?php foreach ($iconPresets as $iKey => $iLabel): ?>
                                    <option value="<?= esc($iKey); ?>"><?= esc($iLabel); ?> (<?= esc($iKey); ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-9">
                            <label for="mk_judul" class="form-label fw-semibold small text-dark">Judul <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="judul" id="mk_judul" required>
                        </div>
                        <div class="col-md-3">
                            <label for="mk_urutan" class="form-label fw-semibold small text-dark">Urutan</label>
                            <input type="number" class="form-control" name="urutan" id="mk_urutan" min="1" value="1">
                        </div>

                        <div class="col-12">
                            <label for="mk_deskripsi" class="form-label fw-semibold small text-dark">Deskripsi / Penjelasan <span class="text-danger">*</span></label>
                            <textarea class="form-control" name="deskripsi" id="mk_deskripsi" rows="4" required></textarea>
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-0 pt-0 pb-4 px-4">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-semibold" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-semibold" style="background-color: var(--inkindo-blue); border: none;">
                        <i class="bi bi-save me-1"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openMkItemModal(jenis, item = null) {
            const modalEl = document.getElementById('mkItemModal');
            const titleEl = document.getElementById('mkItemModalLabel');
            const ikonWrap = document.getElementById('mk_ikon_wrap');
            const isKetentuan = jenis === 'ketentuan';

            document.getElementById('mk_jenis').value = jenis;
            ikonWrap.style.display = isKetentuan ? 'block' : 'none';

            if (item) {
                titleEl.textContent = isKetentuan ? 'Edit Ketentuan Mitra Kerja' : 'Edit Syarat Mitra Kerja';
                document.getElementById('mk_id').value = item.id;
                document.getElementById('mk_ikon').value = item.ikon || 'gavel';
                document.getElementById('mk_judul').value = item.judul || '';
                document.getElementById('mk_urutan').value = item.urutan || 1;
                document.getElementById('mk_deskripsi').value = item.deskripsi || '';
            } else {
                titleEl.textContent = isKetentuan ? 'Tambah Ketentuan Mitra Kerja' : 'Tambah Syarat Mitra Kerja';
                document.getElementById('mk_id').value = '0';
                document.getElementById('mk_ikon').value = 'gavel';
                document.getElementById('mk_judul').value = '';
                document.getElementById('mk_urutan').value = '<?= count($rows) + 1; ?>';
                document.getElementById('mk_deskripsi').value = '';
            }

            bootstrap.Modal.getOrCreateInstance(modalEl).show();
        }
    </script>

<?php else: ?>
    <!-- TAB 3: HERO & MAKSUD TUJUAN -->
    <form action="/cms/ketentuan-mitra/page/save" method="post">
        <input type="hidden" name="section" value="hero">

        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-3" style="color: var(--inkindo-blue);">1. Bagian Hero Halaman</h5>
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label fw-semibold small text-dark">Judul Utama (Hero)</label>
                        <input type="text" class="form-control" name="hero_judul" value="<?= esc($page['hero_judul'] ?? ''); ?>" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold small text-dark">Paragraf Utama</label>
                        <textarea class="form-control" name="hero_paragraf_1" rows="2" required><?= esc($page['hero_paragraf_1'] ?? ''); ?></textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold small text-dark">Paragraf Catatan (Garis Biru Kiri)</label>
                        <textarea class="form-control" name="hero_paragraf_2" rows="2"><?= esc($page['hero_paragraf_2'] ?? ''); ?></textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-3" style="color: var(--inkindo-blue);">2. Bagian Maksud &amp; Tujuan Mitra Kerja</h5>
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold small text-dark">Label Atas</label>
                        <input type="text" class="form-control" name="tujuan_label" value="<?= esc($page['tujuan_label'] ?? ''); ?>" required>
                    </div>
                    <div class="col-md-8">
                        <label class="form-label fw-semibold small text-dark">Judul Bagian</label>
                        <input type="text" class="form-control" name="tujuan_judul" value="<?= esc($page['tujuan_judul'] ?? ''); ?>" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold small text-dark">Deskripsi Pengantar</label>
                        <textarea class="form-control" name="tujuan_deskripsi" rows="3" required><?= esc($page['tujuan_deskripsi'] ?? ''); ?></textarea>
                    </div>
                </div>

                <h6 class="fw-bold text-dark mb-3">3 Kartu Manfaat (Anggota, Mitra Kerja, Masyarakat)</h6>
                <div class="row g-3">
                    <?php foreach ($cards as $cIdx => $card): ?>
                        <div class="col-lg-4">
                            <div class="border rounded-3 p-3 h-100 bg-light-subtle">
                                <div class="mb-2">
                                    <label class="form-label fw-semibold small text-dark">Judul Kartu <?= $cIdx + 1; ?></label>
                                    <input type="text" class="form-control form-control-sm" name="card_judul[]" value="<?= esc($card['judul'] ?? ''); ?>" required>
                                </div>
                                <div class="mb-2">
                                    <label class="form-label fw-semibold small text-dark">Deskripsi Singkat</label>
                                    <textarea class="form-control form-control-sm" name="card_deskripsi[]" rows="3" required><?= esc($card['deskripsi'] ?? ''); ?></textarea>
                                </div>
                                <div>
                                    <label class="form-label fw-semibold small text-dark">Poin Manfaat (1 poin per baris)</label>
                                    <textarea class="form-control form-control-sm" name="card_poin[]" rows="4"><?= esc(implode("\n", $card['poin'] ?? [])); ?></textarea>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end mb-4">
            <button type="submit" class="btn btn-primary rounded-pill px-5 py-2 fw-semibold shadow-sm" style="background-color: var(--inkindo-blue); border: none;">
                <i class="bi bi-save me-1"></i> Simpan Hero &amp; Maksud Tujuan
            </button>
        </div>
    </form>
<?php endif; ?>
<?= $this->endSection() ?>

