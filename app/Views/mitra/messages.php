<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<?php
$isAdmin = !empty($isAdmin);
$activeTab = $activeTab ?? 'mitra';
$mitraTotal = (int) ($mitraTotal ?? count($messages ?? []));
$unreadMitraCount = (int) ($unreadMitraCount ?? 0);
$konsultasiTotal = (int) ($konsultasiTotal ?? 0);
$unreadKonsultasiCount = (int) ($unreadKonsultasiCount ?? 0);
?>

<style>
    .msg-table-wrap .table-custom {
        width: 100% !important;
        table-layout: auto;
    }
    .msg-table-wrap .table-custom th,
    .msg-table-wrap .table-custom td {
        padding-left: 10px;
        padding-right: 10px;
        vertical-align: middle;
    }
    .msg-wrap-text {
        white-space: normal !important;
        word-break: break-word;
        overflow-wrap: anywhere;
    }
    .msg-kategori-badge {
        display: inline-block;
        white-space: normal !important;
        text-align: left;
        line-height: 1.35;
        max-width: 210px;
        word-break: break-word;
    }
    .msg-preview {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        max-width: 180px;
        white-space: normal;
        line-height: 1.4;
    }
</style>

<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4 mt-2">
    <div>
        <h3 class="fw-bold mb-1" style="color: var(--inkindo-blue);"><?= esc($title); ?></h3>
        <?php if ($isAdmin): ?>
            <p class="text-muted small mb-0">Kelola pesan masuk yang dikelompokkan dalam 2 kategori: <strong>Pesan Mitra Kerja</strong> dan <strong>Pesan Konsultasi</strong></p>
        <?php else: ?>
            <p class="text-muted small mb-0">Daftar pesan dan pertanyaan yang dikirimkan melalui tombol <strong>Kontak Kami</strong> pada halaman profil Mitra Kerja di Web Profile.</p>
        <?php endif; ?>
    </div>
</div>

<?php if (session()->getFlashdata('pesan')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= session()->getFlashdata('pesan'); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if ($isAdmin): ?>
    <ul class="nav nav-pills mb-3 gap-2" id="pesanMasukTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-semibold px-4 py-2 rounded-pill <?= $activeTab === 'mitra' ? 'active' : 'bg-white border text-dark'; ?>"
                    id="tab-mitra-btn"
                    data-bs-toggle="pill"
                    data-bs-target="#pane-mitra"
                    type="button"
                    role="tab"
                    aria-controls="pane-mitra"
                    aria-selected="<?= $activeTab === 'mitra' ? 'true' : 'false'; ?>"
                    onclick="switchMsgTab('mitra')">
                <i class="bi bi-briefcase-fill me-1"></i> Pesan Mitra Kerja
                <span class="badge rounded-pill <?= $activeTab === 'mitra' ? 'bg-light text-primary' : 'bg-secondary bg-opacity-10 text-dark'; ?> ms-1"><?= $mitraTotal; ?></span>
                <?php if ($unreadMitraCount > 0): ?>
                    <span id="tabUnreadMitra" class="badge rounded-pill bg-danger ms-1"><?= $unreadMitraCount; ?> baru</span>
                <?php endif; ?>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-semibold px-4 py-2 rounded-pill <?= $activeTab === 'konsultasi' ? 'active' : 'bg-white border text-dark'; ?>"
                    id="tab-konsultasi-btn"
                    data-bs-toggle="pill"
                    data-bs-target="#pane-konsultasi"
                    type="button"
                    role="tab"
                    aria-controls="pane-konsultasi"
                    aria-selected="<?= $activeTab === 'konsultasi' ? 'true' : 'false'; ?>"
                    onclick="switchMsgTab('konsultasi')">
                <i class="bi bi-chat-square-dots-fill me-1"></i> Pesan Konsultasi
                <span class="badge rounded-pill <?= $activeTab === 'konsultasi' ? 'bg-light text-primary' : 'bg-secondary bg-opacity-10 text-dark'; ?> ms-1"><?= $konsultasiTotal; ?></span>
                <?php if ($unreadKonsultasiCount > 0): ?>
                    <span id="tabUnreadKonsultasi" class="badge rounded-pill bg-danger ms-1"><?= $unreadKonsultasiCount; ?> baru</span>
                <?php endif; ?>
            </button>
        </li>
    </ul>
<?php endif; ?>

<div class="tab-content" id="pesanMasukTabsContent">
    <!-- TAB 1: PESAN MITRA KERJA -->
    <div class="tab-pane fade <?= (!$isAdmin || $activeTab === 'mitra') ? 'show active' : ''; ?>" id="pane-mitra" role="tabpanel" aria-labelledby="tab-mitra-btn">
        <div class="table-custom-wrap msg-table-wrap">
            <div class="table-responsive" style="overflow-x: auto;">
                <table class="table-custom text-center align-middle w-100">
                    <thead>
                        <tr>
                            <th style="width: 44px;">No</th>
                            <th class="text-start">Pengirim</th>
                            <th class="text-start">Kontak</th>
                            <?php if ($isAdmin): ?>
                                <th class="text-start">Tujuan Mitra</th>
                            <?php endif; ?>
                            <th class="text-start">Isi Pesan</th>
                            <th style="width: 95px;">Waktu</th>
                            <th style="width: 95px;">Status</th>
                            <th style="width: 145px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i = 1; foreach ($messages as $msg): ?>
                        <?php
                            $cleanSingleLine = preg_replace('/\s+/', ' ', trim((string)($msg['pesan'] ?? '')));
                            $previewPesan = mb_strimwidth($cleanSingleLine, 0, 60, '...');
                            $waktuFormatted = $msg['created_at'] ? date('d-m-Y H:i', strtotime($msg['created_at'])) : '-';
                        ?>
                        <tr>
                            <td class="text-muted"><?= $i++; ?></td>
                            <td class="text-start msg-wrap-text">
                                <div class="fw-bold text-dark"><?= esc($msg['nama_perusahaan']); ?></div>
                                <div class="small text-muted"><i class="bi bi-person-fill me-1"></i> PIC: <?= esc($msg['nama_pic']); ?></div>
                            </td>
                            <td class="text-start small msg-wrap-text">
                                <div><i class="bi bi-envelope-fill text-primary me-1"></i> <?= esc($msg['email_perusahaan']); ?></div>
                                <div><i class="bi bi-telephone-fill text-success me-1"></i> <?= esc($msg['no_telepon']); ?></div>
                            </td>
                            <?php if ($isAdmin): ?>
                                <td class="text-start fw-semibold small text-dark msg-wrap-text">
                                    <?= esc($msg['nama_mitra'] ?? '-'); ?>
                                </td>
                            <?php endif; ?>
                            <td class="text-start small text-muted">
                                <div class="msg-preview" title="Klik tombol Baca untuk melihat pesan lengkap">
                                    <?= esc($previewPesan); ?>
                                </div>
                            </td>
                            <td class="small text-muted" style="white-space: nowrap;">
                                <?= $waktuFormatted; ?>
                            </td>
                            <td>
                                <?php if ((int)$msg['is_read'] === 0): ?>
                                    <span id="msgStatusBadge-<?= $msg['id']; ?>" class="badge rounded-pill badge-soft-danger px-2 py-2">Pesan Baru</span>
                                <?php else: ?>
                                    <span id="msgStatusBadge-<?= $msg['id']; ?>" class="badge rounded-pill badge-soft-success px-2 py-2">Dibaca</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="d-flex justify-content-center gap-1 flex-nowrap">
                                    <button type="button"
                                            class="btn btn-sm btn-primary rounded-pill px-2 fw-semibold"
                                            data-id="<?= $msg['id']; ?>"
                                            data-perusahaan="<?= esc($msg['nama_perusahaan'], 'attr'); ?>"
                                            data-pic="<?= esc($msg['nama_pic'], 'attr'); ?>"
                                            data-email="<?= esc($msg['email_perusahaan'], 'attr'); ?>"
                                            data-telepon="<?= esc($msg['no_telepon'], 'attr'); ?>"
                                            data-mitra="<?= esc($msg['nama_mitra'] ?? '-', 'attr'); ?>"
                                            data-waktu="<?= esc($waktuFormatted, 'attr'); ?>"
                                            data-pesan="<?= esc($msg['pesan'], 'attr'); ?>"
                                            data-is-read="<?= (int)$msg['is_read']; ?>"
                                            onclick="openReadMessageModal(this)"
                                            title="Baca Isi Pesan">
                                        <i class="bi bi-envelope-open me-1"></i> Baca
                                    </button>
                                    <a href="/mitra/deleteMessage/<?= $msg['id']; ?>" class="btn btn-sm btn-danger rounded-pill px-2 fw-semibold" onclick="return confirm('Hapus pesan masuk ini?');">
                                        Hapus
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <?php if ($isAdmin): ?>
    <!-- TAB 2: PESAN KONSULTASI -->
    <div class="tab-pane fade <?= $activeTab === 'konsultasi' ? 'show active' : ''; ?>" id="pane-konsultasi" role="tabpanel" aria-labelledby="tab-konsultasi-btn">
        <div class="table-custom-wrap msg-table-wrap">
            <div class="table-responsive" style="overflow-x: auto;">
                <table class="table-custom text-center align-middle w-100">
                    <thead>
                        <tr>
                            <th style="width: 44px;">No</th>
                            <th class="text-start">Pengirim</th>
                            <th class="text-start">Kontak</th>
                            <th class="text-start">Kategori &amp; Subject</th>
                            <th class="text-start">Isi Pesan</th>
                            <th style="width: 95px;">Waktu</th>
                            <th style="width: 95px;">Status</th>
                            <th style="width: 145px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $k = 1; foreach ($konsultasiMessages as $km): ?>
                        <?php
                            $cleanKonsulLine = preg_replace('/\s+/', ' ', trim((string)($km['pesan'] ?? '')));
                            $previewKonsul = mb_strimwidth($cleanKonsulLine, 0, 60, '...');
                            $waktuKonsul = $km['created_at'] ? date('d-m-Y H:i', strtotime($km['created_at'])) : '-';
                        ?>
                        <tr>
                            <td class="text-muted"><?= $k++; ?></td>
                            <td class="text-start msg-wrap-text">
                                <div class="fw-bold text-dark"><?= esc($km['nama']); ?></div>
                                <div class="small text-muted"><i class="bi bi-building me-1"></i> <?= esc($km['nama_perusahaan']); ?></div>
                            </td>
                            <td class="text-start small msg-wrap-text">
                                <div><i class="bi bi-envelope-fill text-primary me-1"></i> <?= esc($km['email']); ?></div>
                                <div><i class="bi bi-telephone-fill text-success me-1"></i> <?= esc($km['no_telepon']); ?></div>
                            </td>
                            <td class="text-start small msg-wrap-text">
                                <span class="badge bg-primary bg-opacity-10 text-primary mb-1 msg-kategori-badge"><?= esc($km['kategori']); ?></span>
                                <div class="fw-semibold text-dark"><?= esc($km['subject']); ?></div>
                            </td>
                            <td class="text-start small text-muted">
                                <div class="msg-preview" title="Klik tombol Baca untuk melihat pesan lengkap">
                                    <?= esc($previewKonsul); ?>
                                </div>
                            </td>
                            <td class="small text-muted" style="white-space: nowrap;">
                                <?= $waktuKonsul; ?>
                            </td>
                            <td>
                                <?php if ((int)$km['is_read'] === 0): ?>
                                    <span id="konsulStatusBadge-<?= $km['id']; ?>" class="badge rounded-pill badge-soft-danger px-2 py-2">Pesan Baru</span>
                                <?php else: ?>
                                    <span id="konsulStatusBadge-<?= $km['id']; ?>" class="badge rounded-pill badge-soft-success px-2 py-2">Dibaca</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="d-flex justify-content-center gap-1 flex-nowrap">
                                    <button type="button"
                                            class="btn btn-sm btn-primary rounded-pill px-2 fw-semibold"
                                            data-id="<?= $km['id']; ?>"
                                            data-kategori="<?= esc($km['kategori'], 'attr'); ?>"
                                            data-nama="<?= esc($km['nama'], 'attr'); ?>"
                                            data-perusahaan="<?= esc($km['nama_perusahaan'], 'attr'); ?>"
                                            data-email="<?= esc($km['email'], 'attr'); ?>"
                                            data-telepon="<?= esc($km['no_telepon'], 'attr'); ?>"
                                            data-subject="<?= esc($km['subject'], 'attr'); ?>"
                                            data-waktu="<?= esc($waktuKonsul, 'attr'); ?>"
                                            data-pesan="<?= esc($km['pesan'], 'attr'); ?>"
                                            data-is-read="<?= (int)$km['is_read']; ?>"
                                            onclick="openReadKonsultasiModal(this)"
                                            title="Baca Isi Pesan Konsultasi">
                                        <i class="bi bi-envelope-open me-1"></i> Baca
                                    </button>
                                    <a href="/mitra/deleteKonsultasiMessage/<?= $km['id']; ?>" class="btn btn-sm btn-danger rounded-pill px-2 fw-semibold" onclick="return confirm('Hapus pesan konsultasi ini?');">
                                        Hapus
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- Modal Pop-Up Baca Pesan Masuk Mitra Kerja -->
<div class="modal fade" id="readMessageModal" tabindex="-1" aria-labelledby="readMessageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow" style="border-radius: 16px;">
            <div class="modal-header border-bottom-0 pb-0 pt-4 px-4">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 42px; height: 42px; background: rgba(10, 100, 164, 0.1); color: var(--inkindo-blue);">
                        <i class="bi bi-envelope-open-fill fs-5"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-dark mb-0" id="readMessageModalLabel">Detail Pesan Mitra Kerja</h5>
                        <small class="text-muted" id="modalMsgWaktu">-</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body px-4 pt-3 pb-4">
                <div class="p-3 rounded-3 mb-3" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                    <div class="row g-2 small">
                        <div class="col-md-6">
                            <div class="text-muted mb-1">Perusahaan Pengirim</div>
                            <div class="fw-bold text-dark fs-6" id="modalMsgPerusahaan">-</div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted mb-1">Nama PIC</div>
                            <div class="fw-semibold text-dark" id="modalMsgPic">-</div>
                        </div>
                        <div class="col-md-6 mt-2">
                            <div class="text-muted mb-1">Email Perusahaan</div>
                            <div><i class="bi bi-envelope-fill text-primary me-1"></i> <span id="modalMsgEmail" class="fw-medium text-dark">-</span></div>
                        </div>
                        <div class="col-md-6 mt-2">
                            <div class="text-muted mb-1">No. Telepon / WhatsApp</div>
                            <div><i class="bi bi-telephone-fill text-success me-1"></i> <span id="modalMsgTelepon" class="fw-medium text-dark">-</span></div>
                        </div>
                        <?php if ($isAdmin): ?>
                        <div class="col-12 mt-2 pt-2 border-top">
                            <span class="text-muted">Tujuan Mitra Kerja:</span>
                            <span class="fw-bold text-primary ms-1" id="modalMsgMitra">-</span>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <label class="form-label fw-bold text-dark small mb-2">
                    <i class="bi bi-chat-left-text-fill text-primary me-1"></i> Isi Pesan Lengkap
                </label>
                <div id="modalMsgPesan"
                     class="p-3 rounded-3 text-dark"
                     style="background: #ffffff; border: 1px solid #cbd5e1; white-space: pre-wrap; word-break: break-word; max-height: 300px; overflow-y: auto; font-size: 14px; line-height: 1.65;"></div>
            </div>
            <div class="modal-footer border-top-0 pt-0 pb-4 px-4">
                <button type="button" class="btn btn-secondary rounded-pill px-4 fw-semibold" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<?php if ($isAdmin): ?>
<!-- Modal Pop-Up Baca Pesan Konsultasi -->
<div class="modal fade" id="readKonsultasiModal" tabindex="-1" aria-labelledby="readKonsultasiModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow" style="border-radius: 16px;">
            <div class="modal-header border-bottom-0 pb-0 pt-4 px-4">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 42px; height: 42px; background: rgba(10, 100, 164, 0.1); color: var(--inkindo-blue);">
                        <i class="bi bi-chat-square-dots-fill fs-5"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-dark mb-0" id="readKonsultasiModalLabel">Detail Pesan Klinik Konsultasi</h5>
                        <small class="text-muted" id="modalKonsulWaktu">-</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body px-4 pt-3 pb-4">
                <div class="p-3 rounded-3 mb-3" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                    <div class="row g-2 small">
                        <div class="col-md-6">
                            <div class="text-muted mb-1">Nama Pengirim</div>
                            <div class="fw-bold text-dark fs-6" id="modalKonsulNama">-</div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted mb-1">Nama Perusahaan</div>
                            <div class="fw-semibold text-dark" id="modalKonsulPerusahaan">-</div>
                        </div>
                        <div class="col-md-6 mt-2">
                            <div class="text-muted mb-1">Email</div>
                            <div><i class="bi bi-envelope-fill text-primary me-1"></i> <span id="modalKonsulEmail" class="fw-medium text-dark">-</span></div>
                        </div>
                        <div class="col-md-6 mt-2">
                            <div class="text-muted mb-1">No. Telepon / WhatsApp</div>
                            <div><i class="bi bi-telephone-fill text-success me-1"></i> <span id="modalKonsulTelepon" class="fw-medium text-dark">-</span></div>
                        </div>
                        <div class="col-md-6 mt-2 pt-2 border-top">
                            <span class="text-muted">Kategori:</span>
                            <span class="badge bg-primary bg-opacity-10 text-primary ms-1 msg-kategori-badge" id="modalKonsulKategori">-</span>
                        </div>
                        <div class="col-md-6 mt-2 pt-2 border-top">
                            <span class="text-muted">Subject:</span>
                            <span class="fw-bold text-dark ms-1" id="modalKonsulSubject">-</span>
                        </div>
                    </div>
                </div>

                <label class="form-label fw-bold text-dark small mb-2">
                    <i class="bi bi-chat-left-text-fill text-primary me-1"></i> Isi Pesan Konsultasi
                </label>
                <div id="modalKonsulPesan"
                     class="p-3 rounded-3 text-dark"
                     style="background: #ffffff; border: 1px solid #cbd5e1; white-space: pre-wrap; word-break: break-word; max-height: 300px; overflow-y: auto; font-size: 14px; line-height: 1.65;"></div>
            </div>
            <div class="modal-footer border-top-0 pt-0 pb-4 px-4">
                <button type="button" class="btn btn-secondary rounded-pill px-4 fw-semibold" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('#pesanMasukTabs [data-bs-toggle="pill"]').forEach(function (tabEl) {
        tabEl.addEventListener('shown.bs.tab', function () {
            if (window.jQuery && $.fn.dataTable) {
                $.fn.dataTable.tables({ visible: true, api: true }).columns.adjust();
            }
        });
    });
});

function decrementBadge(el) {
    if (!el) return;
    var match = el.textContent.trim().match(/\d+/);
    if (!match) return;
    var count = parseInt(match[0], 10);
    if (isNaN(count)) return;
    if (count > 1) {
        el.textContent = el.textContent.replace(/\d+/, String(count - 1));
    } else {
        el.remove();
    }
}

function switchMsgTab(tabName) {
    var mitraBtn = document.getElementById('tab-mitra-btn');
    var konsulBtn = document.getElementById('tab-konsultasi-btn');
    if (mitraBtn && konsulBtn) {
        if (tabName === 'mitra') {
            mitraBtn.classList.remove('bg-white', 'border', 'text-dark');
            konsulBtn.classList.add('bg-white', 'border', 'text-dark');
        } else {
            konsulBtn.classList.remove('bg-white', 'border', 'text-dark');
            mitraBtn.classList.add('bg-white', 'border', 'text-dark');
        }
    }
    if (window.history && window.history.replaceState) {
        var url = new URL(window.location.href);
        url.searchParams.set('tab', tabName);
        window.history.replaceState({}, '', url.toString());
    }
}

function openReadMessageModal(btn) {
    var msgId = btn.getAttribute('data-id');
    var perusahaan = btn.getAttribute('data-perusahaan') || '-';
    var pic = btn.getAttribute('data-pic') || '-';
    var email = btn.getAttribute('data-email') || '-';
    var telepon = btn.getAttribute('data-telepon') || '-';
    var mitra = btn.getAttribute('data-mitra') || '-';
    var waktu = btn.getAttribute('data-waktu') || '-';
    var pesan = btn.getAttribute('data-pesan') || '';
    var isRead = btn.getAttribute('data-is-read');

    document.getElementById('modalMsgPerusahaan').textContent = perusahaan;
    document.getElementById('modalMsgPic').textContent = pic;
    document.getElementById('modalMsgEmail').textContent = email;
    document.getElementById('modalMsgTelepon').textContent = telepon;
    document.getElementById('modalMsgWaktu').textContent = 'Dikirim pada: ' + waktu;
    document.getElementById('modalMsgPesan').textContent = pesan;

    var mitraEl = document.getElementById('modalMsgMitra');
    if (mitraEl) {
        mitraEl.textContent = mitra;
    }

    var modalEl = document.getElementById('readMessageModal');
    var modal = bootstrap.Modal.getOrCreateInstance(modalEl);
    modal.show();

    if (isRead === '0') {
        btn.setAttribute('data-is-read', '1');
        fetch('/mitra/markMessageRead/' + msgId, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        }).then(function() {
            var badge = document.getElementById('msgStatusBadge-' + msgId);
            if (badge) {
                badge.className = 'badge rounded-pill badge-soft-success px-2 py-2';
                badge.textContent = 'Dibaca';
            }
            decrementBadge(document.getElementById('tabUnreadMitra'));
            decrementBadge(document.querySelector('a[href="/mitra/messages"] .badge'));
        }).catch(function() {});
    }
}

function openReadKonsultasiModal(btn) {
    var msgId = btn.getAttribute('data-id');
    var nama = btn.getAttribute('data-nama') || '-';
    var perusahaan = btn.getAttribute('data-perusahaan') || '-';
    var email = btn.getAttribute('data-email') || '-';
    var telepon = btn.getAttribute('data-telepon') || '-';
    var kategori = btn.getAttribute('data-kategori') || '-';
    var subject = btn.getAttribute('data-subject') || '-';
    var waktu = btn.getAttribute('data-waktu') || '-';
    var pesan = btn.getAttribute('data-pesan') || '';
    var isRead = btn.getAttribute('data-is-read');

    document.getElementById('modalKonsulNama').textContent = nama;
    document.getElementById('modalKonsulPerusahaan').textContent = perusahaan;
    document.getElementById('modalKonsulEmail').textContent = email;
    document.getElementById('modalKonsulTelepon').textContent = telepon;
    document.getElementById('modalKonsulKategori').textContent = kategori;
    document.getElementById('modalKonsulSubject').textContent = subject;
    document.getElementById('modalKonsulWaktu').textContent = 'Dikirim pada: ' + waktu;
    document.getElementById('modalKonsulPesan').textContent = pesan;

    var modalEl = document.getElementById('readKonsultasiModal');
    var modal = bootstrap.Modal.getOrCreateInstance(modalEl);
    modal.show();

    if (isRead === '0') {
        btn.setAttribute('data-is-read', '1');
        fetch('/mitra/markKonsultasiRead/' + msgId, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        }).then(function() {
            var badge = document.getElementById('konsulStatusBadge-' + msgId);
            if (badge) {
                badge.className = 'badge rounded-pill badge-soft-success px-2 py-2';
                badge.textContent = 'Dibaca';
            }
            decrementBadge(document.getElementById('tabUnreadKonsultasi'));
            decrementBadge(document.querySelector('a[href="/mitra/messages"] .badge'));
        }).catch(function() {});
    }
}
</script>
<?= $this->endSection() ?>
