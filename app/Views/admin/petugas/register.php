<?= $this->extend('templates/admin_page_layout') ?>

<?= $this->section('content') ?>
<div class="content">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-lg-11">
                <div class="card shadow border-0">
                    <!-- Header dengan desain yang lebih solid -->
                    <div class="card-header card-header-info" style="margin-top: -20px; border-radius: 8px;">
                        <div class="d-flex align-items-center p-2">
                            <div class="mr-3" style="background: rgba(255,255,255,0.2); padding: 10px; border-radius: 10px;">
                                <i class="material-icons" style="font-size: 32px;">person_add</i>
                            </div>
                            <div>
                                <h4 class="card-title mb-0 font-weight-bold"><?= lang('Auth.register') ?></h4>
                                <p class="card-category text-white" style="opacity: 0.9;">Formulir Pendaftaran Akun Petugas Baru</p>
                            </div>
                        </div>
                    </div>

                    <div class="card-body mt-4 px-4 pb-5">
                        <!-- Flash Message -->
                        <?= view('Myth\Auth\Views\_message_block') ?>

                        <form action="<?= base_url('admin/petugas/register') ?>" method="post">
                            <?= csrf_field() ?>

                            <div class="row">
                                <!-- Bagian Kiri: Akun -->
                                <div class="col-md-7 pr-md-5 border-right">
                                    <h5 class="font-weight-bold mb-4 text-info">
                                        <i class="material-icons align-middle mr-1">vpn_key</i> Informasi Akun
                                    </h5>
                                    
                                    <div class="form-group mb-4">
                                        <label class="text-dark font-weight-bold mb-1"><?= lang('Auth.email') ?></label>
                                        <input type="email" name="email" 
                                               class="form-control border px-3 <?php if (session('errors.email')): ?>is-invalid<?php endif ?>" 
                                               placeholder="contoh: petugas@email.com" value="<?= old('email') ?>">
                                        <?php if (session('errors.email')): ?>
                                            <div class="invalid-feedback"><?= session('errors.email') ?></div>
                                        <?php endif ?>
                                    </div>

                                    <div class="form-group mb-4">
                                        <label class="text-dark font-weight-bold mb-1"><?= lang('Auth.username') ?> / ID Petugas</label>
                                        <input type="text" name="username" 
                                               class="form-control border px-3 <?php if (session('errors.username')): ?>is-invalid<?php endif ?>" 
                                               placeholder="Masukkan NUPTK atau ID Guru" value="<?= old('username') ?>">
                                        <div class="invalid-feedback"><?= session('errors.username') ?></div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group mb-4">
                                                <label class="text-dark font-weight-bold mb-1"><?= lang('Auth.password') ?></label>
                                                <input type="password" name="password" class="form-control border px-3 <?php if (session('errors.password')): ?>is-invalid<?php endif ?>" placeholder="••••••••">
                                                <div class="invalid-feedback"><?= session('errors.password') ?></div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group mb-4">
                                                <label class="text-dark font-weight-bold mb-1">Konfirmasi Password</label>
                                                <input type="password" name="pass_confirm" class="form-control border px-3 <?php if (session('errors.pass_confirm')): ?>is-invalid<?php endif ?>" placeholder="••••••••">
                                                <div class="invalid-feedback"><?= session('errors.pass_confirm') ?></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Bagian Kanan: Peran -->
                                <div class="col-md-5 pl-md-5">
                                    <h5 class="font-weight-bold mb-4 text-info">
                                        <i class="material-icons align-middle mr-1">assignment_ind</i> Otoritas & Profil
                                    </h5>

                                    <div class="form-group mb-4">
                                        <label class="text-dark font-weight-bold mb-1">Role / Hak Akses</label>
                                        <select class="custom-select border <?php if (session('errors.role')): ?>is-invalid<?php endif ?>" name="role" style="height: 45px;">
                                            <option value="" disabled selected>-- Pilih Hak Akses --</option>
                                            <?php foreach ($roles as $role): ?>
                                                <option value="<?= $role->value ?>" <?= old('role') == (string) $role->value ? 'selected' : ''; ?>>
                                                    <?= strtoupper($role->label()) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <div class="invalid-feedback"><?= session('errors.role') ?></div>
                                    </div>

                                    <div class="form-group mb-4">
                                        <label class="text-dark font-weight-bold mb-1">Hubungkan ke Data Guru <span class="text-muted small font-weight-normal">(Opsional)</span></label>
                                        <select class="custom-select border" name="id_guru" style="height: 45px;">
                                            <option value="">-- Cari Nama Guru --</option>
                                            <?php foreach ($guru as $g): ?>
                                                <option value="<?= $g['id_guru']; ?>" <?= old('id_guru') == $g['id_guru'] ? 'selected' : ''; ?>>
                                                    <?= $g['nama_guru']; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <small class="form-text text-muted mt-2">Pilih nama guru jika akun ini milik petugas yang sudah terdaftar di data guru.</small>
                                    </div>
                                </div>
                            </div>

                            <hr class="my-4">

                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted small">
                                    <i class="material-icons align-middle" style="font-size: 14px;">info</i> 
                                    Pastikan email dan username belum pernah digunakan sebelumnya.
                                </span>
                                <div class="text-right">
                                    <button type="reset" class="btn btn-link text-muted mr-3">Batal</button>
                                    <button type="submit" class="btn btn-info btn-lg px-5 shadow-sm">
                                        <i class="material-icons mr-2">save</i> SIMPAN DATA PETUGAS
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Mengatasi konflik Material Dashboard */
    .form-group label {
        position: static !important; /* Menghapus posisi floating yang berantakan */
        transform: none !important;
        margin-bottom: 5px !important;
    }
    .form-control {
        background-image: none !important; /* Menghapus garis bawah animasi yang sering glitch */
        border: 1px solid #d2d2d2 !important;
        border-radius: 4px !important;
        padding: 10px 15px !important;
    }
    .form-control:focus {
        border-color: #00acc1 !important;
        box-shadow: 0 0 0 0.2rem rgba(0, 172, 193, 0.15) !important;
    }
    .custom-select {
        border: 1px solid #d2d2d2 !important;
    }
    .border-right {
        border-right: 1px solid #eee !important;
    }
    @media (max-width: 768px) {
        .border-right { border-right: none !important; }
    }
</style>
<?= $this->endSection() ?>