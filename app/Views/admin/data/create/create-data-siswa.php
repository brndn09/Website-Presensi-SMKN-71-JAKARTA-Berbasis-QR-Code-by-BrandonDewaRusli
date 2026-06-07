<?= $this->extend('templates/admin_page_layout') ?>

<?= $this->section('styles') ?>
<title>Tambah Data Siswa</title>
<style>
    :root {
        --primary: #4361ee;
        --primary-gradient: linear-gradient(60deg, #4361ee, #3a86ff);
        --bg-body: #f8f9fc;
        --card-border: #edf2f7;
    }

    body {
        background-color: var(--bg-body);
        font-family: 'Inter', sans-serif;
    }

    .content {
        padding-top: 30px !important;
    }

    .card-modern {
        border: none;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        background: #ffffff;
        margin-top: 30px;
        overflow: visible;
    }

    .card-header-modern {
        background: var(--primary-gradient) !important;
        box-shadow: 0 4px 20px 0 rgba(0, 0, 0, 0.14), 0 7px 10px -5px rgba(67, 97, 238, 0.4) !important;
        border-radius: 12px !important;
        margin: -20px 20px 0 !important;
        padding: 20px !important;
        color: #fff;
    }

    .form-title {
        font-weight: 700;
        color: #ffffff;
        letter-spacing: -0.5px;
        margin-bottom: 0;
    }

    .form-subtitle {
        color: rgba(255, 255, 255, 0.8);
        font-size: 0.85rem;
    }

    .form-label-custom {
        font-size: 0.75rem;
        font-weight: 700;
        color: #4a5568;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        margin-bottom: 8px;
        display: block;
    }

    .form-control-modern {
        border-radius: 12px;
        border: 1.5px solid var(--card-border);
        padding: 12px 16px;
        height: auto;
        font-weight: 500;
        color: #2d3748;
        background-color: #fbfcfe;
        transition: all 0.2s ease;
    }

    .form-control-modern:focus {
        background-color: #fff;
        border-color: var(--primary);
        box-shadow: 0 0 0 4px rgba(67, 97, 238, 0.1) !important;
        outline: none;
    }

    .gender-box {
        display: flex;
        gap: 15px;
    }

    .gender-option {
        flex: 1;
        position: relative;
    }

    .gender-option input {
        position: absolute;
        opacity: 0;
        cursor: pointer;
        width: 100%;
        height: 100%;
    }

    .gender-card {
        display: block;
        padding: 12px;
        text-align: center;
        border: 1.5px solid var(--card-border);
        border-radius: 12px;
        cursor: pointer;
        transition: all 0.2s;
        background: #fbfcfe;
        font-weight: 600;
        color: #718096;
    }

    .gender-option input:checked ~ .gender-card {
        border-color: var(--primary);
        background: rgba(67, 97, 238, 0.05);
        color: var(--primary);
    }

    .btn-save {
        background: var(--primary-gradient);
        border: none;
        border-radius: 12px;
        padding: 14px;
        font-weight: 700;
        color: #fff;
        box-shadow: 0 4px 15px rgba(67, 97, 238, 0.3);
        cursor: pointer;
        transition: opacity 0.2s;
        width: 100%;
    }
    
    .btn-save:hover {
        opacity: 0.9;
        color: #fff;
    }

    .btn-back {
        border-radius: 12px;
        padding: 14px;
        font-weight: 600;
        color: #718096;
        background-color: #edf2f7;
        display: flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
    }

    .bg-white-transparent {
        background: rgba(255, 255, 255, 0.2);
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="content">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-9">
                
                <?php 
                    $msg = session()->getFlashdata('msg');
                    $isError = session()->getFlashdata('error');
                    if ($msg): 
                ?>
                    <div class="alert alert-<?= $isError ? 'danger' : 'success' ?> alert-dismissible fade show shadow-sm mb-4" style="border-radius: 12px; border: none;">
                        <div class="d-flex align-items-center">
                            <i class="material-icons mr-3"><?= $isError ? 'error' : 'check_circle' ?></i>
                            <div class="font-weight-bold">
                                <?php if (is_array($msg)): ?>
                                    <ul class="mb-0 pl-3">
                                        <?php foreach ($msg as $m) : ?>
                                            <li><?= esc($m) ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php else: ?>
                                    <?= esc($msg) ?>
                                <?php endif; ?>
                            </div>
                        </div>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                <?php endif; ?>

                <div class="card card-modern mb-5">
                    <div class="card-header-modern">
                        <div class="d-flex align-items-center">
                            <div class="p-2 bg-white-transparent rounded-lg mr-3">
                                <i class="material-icons text-white" style="font-size: 32px;">person_add</i>
                            </div>
                            <div>
                                <h4 class="form-title">Registrasi Siswa Baru</h4>
                                <p class="form-subtitle mb-0">Input data akademis dan biodata siswa secara akurat.</p>
                            </div>
                        </div>
                    </div>

                    <div class="card-body p-4 p-md-5">
                        <form action="<?= base_url('admin/siswa/create'); ?>" method="post">
                            <?= csrf_field() ?>
                            <?php $validation = \Config\Services::validation(); ?>

                            <div class="row">
                                <div class="col-md-5">
                                    <div class="form-group mb-4">
                                        <label class="form-label-custom" for="nis">Nomor Induk Siswa (NIS)</label>
                                        <input type="text" id="nis" name="nis"
                                            class="form-control form-control-modern <?= $validation->hasError('nis') ? 'is-invalid' : ''; ?>" 
                                            placeholder="Contoh: 1234" value="<?= old('nis') ?>">
                                        <div class="invalid-feedback"><?= $validation->getError('nis'); ?></div>
                                    </div>
                                </div>

                                <div class="col-md-7">
                                    <div class="form-group mb-4">
                                        <label class="form-label-custom" for="nama">Nama Lengkap Siswa</label>
                                        <input type="text" id="nama" name="nama"
                                            class="form-control form-control-modern <?= $validation->hasError('nama') ? 'is-invalid' : ''; ?>" 
                                            placeholder="Masukkan nama lengkap" value="<?= old('nama') ?>">
                                        <div class="invalid-feedback"><?= $validation->getError('nama'); ?></div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-4">
                                        <label class="form-label-custom" for="id_kelas">Penempatan Kelas</label>
                                        <select class="form-control form-control-modern <?= $validation->hasError('id_kelas') ? 'is-invalid' : ''; ?>" id="id_kelas" name="id_kelas">
                                            <option value="">-- Pilih Kelas --</option>
                                            <?php if (isset($kelas) && is_array($kelas)): ?>
                                                <?php foreach ($kelas as $value): ?>
                                                    <option value="<?= $value['id_kelas']; ?>" <?= (old('id_kelas') == $value['id_kelas']) ? 'selected' : ''; ?>>
                                                        <?= esc($value['kelas']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
                                        <div class="invalid-feedback"><?= $validation->getError('id_kelas'); ?></div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group mb-4">
                                        <label class="form-label-custom">Jenis Kelamin</label>
                                        <div class="gender-box">
                                            <label class="gender-option">
                                                <input type="radio" name="jk" value="Laki-laki" <?= (old('jk') == 'Laki-laki') ? 'checked' : ''; ?>>
                                                <span class="gender-card">Laki-laki</span>
                                            </label>
                                            <label class="gender-option">
                                                <input type="radio" name="jk" value="Perempuan" <?= (old('jk') == 'Perempuan') ? 'checked' : ''; ?>>
                                                <span class="gender-card">Perempuan</span>
                                            </label>
                                        </div>
                                        <?php if($validation->hasError('jk')): ?>
                                            <div class="text-danger small font-weight-bold mt-2"><?= $validation->getError('jk'); ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-4">
                                        <label class="form-label-custom" for="no_hp">No. WhatsApp Orang Tua</label>
                                        <input type="number" id="no_hp" name="no_hp"
                                            class="form-control form-control-modern <?= $validation->hasError('no_hp') ? 'is-invalid' : ''; ?>"
                                            value="<?= old('no_hp') ?>" placeholder="08xxxxxxxxxx">
                                        <div class="invalid-feedback"><?= $validation->getError('no_hp'); ?></div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group mb-4">
                                        <label class="form-label-custom" for="rfid">ID RFID (Opsional)</label>
                                        <input type="text" id="rfid" name="rfid"
                                            class="form-control form-control-modern <?= $validation->hasError('rfid') ? 'is-invalid' : ''; ?>"
                                            value="<?= old('rfid') ?>" placeholder="Scan kartu untuk input ID">
                                        <div class="invalid-feedback"><?= $validation->getError('rfid'); ?></div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-4">
                                <div class="col-md-6 mb-2">
                                    <a href="<?= base_url('admin/siswa') ?>" class="btn btn-light btn-block btn-back">
                                        Batal & Kembali
                                    </a>
                                </div>
                                <div class="col-md-6">
                                    <button type="submit" class="btn btn-primary btn-block btn-save">
                                        <i class="material-icons align-middle mr-1">save</i> Simpan Siswa
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
<?= $this->endSection() ?>