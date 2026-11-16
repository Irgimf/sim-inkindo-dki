<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4 mt-2">
    <div>
        <h3 class="fw-bold mb-1" style="color: var(--inkindo-blue);"><?= esc($title); ?></h3>
        <p class="text-muted small mb-0">Buat akun baru, ubah role pengguna (Admin, Anggota, Mitra Kerja), atau hapus akun.</p>
    </div>
    <button type="button" class="btn btn-inkindo px-4 py-2 rounded-pill fw-bold" data-bs-toggle="modal" data-bs-target="#addUserModal">
        <i class="bi bi-person-plus-fill me-1"></i> Tambah Akun
    </button>
</div>

<?php if (session()->getFlashdata('pesan')): ?>
    <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-sm" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i><?= esc(session()->getFlashdata('pesan')); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 shadow-sm" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i><?= esc(session()->getFlashdata('error')); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="table-custom-wrap">
    <div class="table-responsive" style="overflow-x: visible;">
        <table class="table-custom text-center align-middle">
            <thead>
                <tr>
                    <th width="6%">No</th>
                    <th class="text-start" width="22%">Username</th>
                    <th class="text-start" width="24%">Nama &amp; Email</th>
                    <th width="22%">Role</th>
                    <th width="14%">Tanggal Dibuat</th>
                    <th width="12%">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $i = 1;
                    $currentAdminId = (int) session()->get('id');
                ?>
                <?php foreach ($users as $u): ?>
                <?php
                    $uid = (int) $u['id'];
                    $uRole = (string) ($u['role'] ?? 'user');
                    $isSelf = ($uid === $currentAdminId);
                    $normalizedSelectRole = in_array($uRole, ['admin', 'super_admin'], true) ? 'admin' : $uRole;
                ?>
                <tr>
                    <td class="text-muted"><?= $i++; ?></td>
                    <td class="text-start fw-semibold text-dark">
                        <?= esc((string) $u['username']); ?>
                        <?php if ($isSelf): ?>
                            <span class="badge bg-success-subtle text-success rounded-pill ms-1" style="font-size: 11px;">Anda</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-start">
                        <div class="fw-semibold text-dark small"><?= esc((string) (($u['name'] ?? '') !== '' ? $u['name'] : ($u['email'] ?? '-'))); ?></div>
                        <div class="text-muted" style="font-size: 12px;"><?= esc((string) (($u['email'] ?? '') !== '' ? $u['email'] : '-')); ?></div>
                    </td>
                    <td>
                        <?php if ($isSelf): ?>
                            <span class="badge rounded-pill px-3 py-2 fw-semibold" style="background: #eff6ff; color: var(--inkindo-blue);">
                                <?= esc($uRole); ?>
                            </span>
                        <?php else: ?>
                            <form action="/users/updateRole/<?= $uid; ?>" method="post" class="d-inline-flex align-items-center justify-content-center gap-2 m-0">
                                <select name="role" class="form-select form-select-sm rounded-pill" style="width: 135px; font-weight: 600;" onchange="this.form.submit()">
                                    <option value="admin" <?= $normalizedSelectRole === 'admin' ? 'selected' : ''; ?>>Admin</option>
                                    <option value="user" <?= $normalizedSelectRole === 'user' ? 'selected' : ''; ?>>Anggota</option>
                                    <option value="mitra" <?= $normalizedSelectRole === 'mitra' ? 'selected' : ''; ?>>Mitra Kerja</option>
                                </select>
                            </form>
                        <?php endif; ?>
                    </td>
                    <td class="text-muted small">
                        <?= ! empty($u['created_at']) ? date('d-m-Y H:i', strtotime((string) $u['created_at'])) : '-'; ?>
                    </td>
                    <td>
                        <?php if ($isSelf): ?>
                            <span class="text-muted small">-</span>
                        <?php else: ?>
                            <?php
                                $confirmText = in_array($uRole, ['admin', 'super_admin'], true)
                                    ? 'Hapus akun admin ini? Data perusahaan, SBU, dan mitra yang dibuatnya akan dialihkan ke akun admin Anda.'
                                    : 'Menghapus akun ini akan menghapus semua data Perusahaan, SBU, dan Mitra miliknya. Apakah Anda yakin?';
                            ?>
                            <a href="/users/delete/<?= $uid; ?>" class="btn btn-sm btn-danger px-3 rounded-pill fw-semibold" onclick="return confirm('<?= esc($confirmText, 'js'); ?>');">Hapus</a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($users)): ?>
                <tr>
                    <td colspan="6" class="text-center py-4 text-muted border-0">Belum ada akun user yang terdaftar.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Tambah Akun -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-bottom px-4 py-3">
                <h5 class="modal-title fw-bold" id="addUserModalLabel" style="color: var(--inkindo-blue);">
                    <i class="bi bi-person-plus-fill me-2"></i>Tambah Akun Pengguna
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="/users/store" method="post">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label for="new_username" class="form-label fw-semibold small">Username <span class="text-danger">*</span></label>
                        <input type="text" id="new_username" name="username" class="form-control" placeholder="Minimal 4 karakter" minlength="4" required>
                    </div>
                    <div class="mb-3">
                        <label for="new_name" class="form-label fw-semibold small">Nama User</label>
                        <input type="text" id="new_name" name="name" class="form-control" placeholder="Opsional (default menggunakan email/username)">
                    </div>
                    <div class="mb-3">
                        <label for="new_email" class="form-label fw-semibold small">Email</label>
                        <input type="email" id="new_email" name="email" class="form-control" placeholder="nama@perusahaan.co.id">
                    </div>
                    <div class="mb-3">
                        <label for="new_role" class="form-label fw-semibold small">Role <span class="text-danger">*</span></label>
                        <select id="new_role" name="role" class="form-select" required>
                            <option value="user">Anggota</option>
                            <option value="mitra">Mitra Kerja</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    <div class="mb-0">
                        <label for="new_password" class="form-label fw-semibold small">Password <span class="text-danger">*</span></label>
                        <input type="password" id="new_password" name="password" class="form-control" placeholder="Minimal 6 karakter" minlength="6" required>
                    </div>
                </div>
                <div class="modal-footer border-top px-4 py-3">
                    <button type="button" class="btn btn-light border rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-inkindo rounded-pill px-4 fw-semibold">Buat Akun</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
