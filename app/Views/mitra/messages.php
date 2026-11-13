<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4 mt-2">
    <div>
        <h3 class="fw-bold mb-1" style="color: var(--inkindo-blue);"><?= esc($title); ?></h3>
        <p class="text-muted small mb-0">Daftar pesan dan pertanyaan yang dikirimkan melalui tombol <strong>Kontak Kami</strong> pada halaman profil Mitra Kerja di Web Profile.</p>
    </div>
</div>

<?php if(session()->getFlashdata('pesan')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= session()->getFlashdata('pesan'); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="table-custom-wrap">
    <div class="table-responsive" style="overflow-x: visible;">
        <table class="table-custom text-center align-middle">
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th class="text-start" width="18%">Pengirim (Perusahaan &amp; PIC)</th>
                    <th class="text-start" width="18%">Kontak Pengirim</th>
                    <?php if(session()->get('role') === 'admin'): ?>
                        <th class="text-start" width="15%">Tujuan Mitra Kerja</th>
                    <?php endif; ?>
                    <th class="text-start">Isi Pesan</th>
                    <th width="12%">Waktu</th>
                    <th width="10%">Status</th>
                    <th width="13%">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $i = 1; foreach($messages as $msg): ?>
                <?php
                    $cleanSingleLine = preg_replace('/\s+/', ' ', trim((string)($msg['pesan'] ?? '')));
                    $previewPesan = mb_strimwidth($cleanSingleLine, 0, 55, '...');
                    $waktuFormatted = $msg['created_at'] ? date('d-m-Y H:i', strtotime($msg['created_at'])) : '-';
                ?>
                <tr>
                    <td class="text-muted"><?= $i++; ?></td>
                    <td class="text-start">
                        <div class="fw-bold text-dark"><?= esc($msg['nama_perusahaan']); ?></div>
                        <div class="small text-muted"><i class="bi bi-person-fill me-1"></i> PIC: <?= esc($msg['nama_pic']); ?></div>
                    </td>
                    <td class="text-start small">
                        <div><i class="bi bi-envelope-fill text-primary me-1"></i> <?= esc($msg['email_perusahaan']); ?></div>
                        <div><i class="bi bi-telephone-fill text-success me-1"></i> <?= esc($msg['no_telepon']); ?></div>
                    </td>
                    <?php if(session()->get('role') === 'admin'): ?>
                        <td class="text-start fw-semibold small text-dark">
                            <?= esc($msg['nama_mitra'] ?? '-'); ?>
                        </td>
                    <?php endif; ?>
                    <td class="text-start small text-muted">
                        <div class="text-truncate" style="max-width: 220px;" title="Klik tombol Baca untuk melihat pesan lengkap">
                            <?= esc($previewPesan); ?>
                        </div>
                    </td>
                    <td class="small text-muted">
                        <?= $waktuFormatted; ?>
                    </td>
                    <td>
                        <?php if((int)$msg['is_read'] === 0): ?>
                            <span id="msgStatusBadge-<?= $msg['id']; ?>" class="badge rounded-pill badge-soft-danger px-3 py-2">Pesan Baru</span>
                        <?php else: ?>
                            <span id="msgStatusBadge-<?= $msg['id']; ?>" class="badge rounded-pill badge-soft-success px-3 py-2">Dibaca</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div class="d-flex justify-content-center gap-1 flex-wrap">
                            <button type="button"
                                    class="btn btn-sm btn-primary rounded-pill px-3 fw-semibold"
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
                            <a href="/mitra/deleteMessage/<?= $msg['id']; ?>" class="btn btn-sm btn-danger rounded-pill px-3 fw-semibold" onclick="return confirm('Hapus pesan masuk ini?');">
                                Hapus
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php if(empty($messages)): ?>
        <div class="text-center py-5 text-muted">
            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
            Belum ada pesan masuk untuk Mitra Kerja Anda.
        </div>
    <?php endif; ?>
</div>

<!-- Modal Pop-Up Baca Pesan Masuk -->
<div class="modal fade" id="readMessageModal" tabindex="-1" aria-labelledby="readMessageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow" style="border-radius: 16px;">
            <div class="modal-header border-bottom-0 pb-0 pt-4 px-4">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 42px; height: 42px; background: rgba(10, 100, 164, 0.1); color: var(--inkindo-blue);">
                        <i class="bi bi-envelope-open-fill fs-5"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-dark mb-0" id="readMessageModalLabel">Detail Pesan Masuk</h5>
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
                        <?php if(session()->get('role') === 'admin'): ?>
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

<script>
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
                badge.className = 'badge rounded-pill badge-soft-success px-3 py-2';
                badge.textContent = 'Dibaca';
            }
            var navBadge = document.querySelector('a[href="/mitra/messages"] .badge');
            if (navBadge) {
                var currentCount = parseInt(navBadge.textContent.trim(), 10);
                if (!isNaN(currentCount) && currentCount > 1) {
                    navBadge.textContent = currentCount - 1;
                } else {
                    navBadge.remove();
                }
            }
        }).catch(function() {});
    }
}
</script>
<?= $this->endSection() ?>
