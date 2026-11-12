<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center align-items-center" style="min-height: 100vh;">
    <div class="col-md-5">
        <div class="card card-custom p-4">
            <div class="text-center mb-4">
                <h2 class="fw-bold" style="color: var(--inkindo-blue);">Register</h2>
                <p class="text-muted">Buat akun SIM-INKINDO DKI (Sistem Informasi &amp; Manajemen INKINDO DKI)</p>
            </div>
            
            <?php if(isset($validation)):?>
                <div class="alert alert-danger"><?= $validation->listErrors() ?></div>
            <?php endif;?>

            <form action="/auth/processRegister" method="post">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Username</label>
                    <input type="text" name="username" class="form-control" value="<?= set_value('username') ?>" required placeholder="Pilih username">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Password</label>
                    <input type="password" name="password" class="form-control" required placeholder="Minimal 6 karakter">
                </div>
                <div class="mb-4">
                    <label class="form-label fw-semibold">Konfirmasi Password</label>
                    <input type="password" name="confpassword" class="form-control" required placeholder="Ulangi password">
                </div>
                <button type="submit" class="btn btn-inkindo w-100 mb-3">Register</button>
                <div class="text-center">
                    <span class="text-muted">Sudah punya akun?</span> <a href="/auth/login" style="color: var(--inkindo-blue); font-weight: 600; text-decoration: none;">Login di sini</a>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
