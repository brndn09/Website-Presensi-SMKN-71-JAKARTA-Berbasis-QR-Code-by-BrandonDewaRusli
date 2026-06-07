<?= $this->extend('templates/admin_page_layout') ?>

<?= $this->section('styles') ?>
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

    /* Card Modern Floating Style */
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

    /* Form Styling */
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
        box-shadow: none !important;
    }

    .form-control-modern:focus {
        background-color: #fff;
        border-color: var(--primary);
        box-shadow: 0 0 0 4px rgba(67, 97, 238, 0.1) !important;
        outline: none;
    }

    /* Custom Radio Gender Styling */
    .gender-box {
        display: flex;
        gap: 15px;
    }

    .gender-option {
        flex: 1;
        position: relative;
        margin-bottom: 0;
    }

    .gender-option input {
        position: absolute;
        opacity: 0;
        cursor: pointer;
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

    /* Button Styling */
    .btn-save {
        background: var(--primary-gradient);
        border: none;
        border-radius: 12px;
        padding: 14px;
        font-weight: 700;
        font-size: 1rem;
        color: #fff;
        letter-spacing: 0.5px;
        box-shadow: 0 4px 15px rgba(67, 97, 238, 0.3);
        transition: all 0.3s;
    }

    .btn-save:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(67, 97, 238, 0.4);
    }

    .invalid-feedback {
        font-weight: 600;
        font-size: 0.75rem;
        margin-top: 5px;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="content">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-9">
                
                <!-- Flash Message -->
                <?php if (session()->getFlashdata('msg')): ?>
                    <div class="alert alert-<?= session()->getFlashdata('error') == true ? 'danger' : 'success' ?> shadow-sm mb-4" style="border-radius: 12px; border: none;">
                        <div class="d-flex align-items-center">
                            <i class="material-icons mr-3"><?= session()->getFlashdata('error') == true ? 'error' : 'check_circle' ?></i>
                            <span class="font-weight-bold"><?= session()->getFlashdata('msg') ?></span>
                        </div>
                        <button type="button" class="close" data-dismiss="alert">
                            <i class="material-icons">close</i>
                        </button>
                    </div>
                <?php endif; ?>

                <div class="card card-modern">
                    <!-- Floating Header -->
                    <div class="card-header-modern">
                        <div class="d-flex align-items-center">
                            <div class="p-2 bg-white-transparent rounded-lg mr-3">
                                <i class="material-icons text-white" style="font-size: 32px;">edit_note</i>
                            </div>
                            <div>
                                <h4 class="form-title">Edit Data Siswa</h4>
                                <p class="form-subtitle mb-0">Perbarui informasi profil dan akademik siswa.</p>
                            </div>
                        </div>
                    </div>

                    <div class="card-body p-4 p-md-5">
                        <form action="<?= base_url('admin/siswa/edit'); ?>" method="post">
                            <?= csrf_field() ?>
                            <?php $validation = \Config\Services::validation(); ?>
                            <input type="hidden" name="id" value="<?= $data['id_siswa']; ?>">

                            <div class="row">
                                <!-- NIS -->
                                <div class="col-md-5">
                                    <div class="form-group mb-4">
                                        <label class="form-label-custom" for="nis">NIS (Nomor Induk Siswa)</label>
                                        <input type="text" id="nis" name="nis"
                                            class="form-control form-control-modern <?= $validation->getError('nis') ? 'is-invalid' : ''; ?>" 
                                            placeholder="1234" value="<?= old('nis') ?? $oldInput['nis'] ?? $data['nis'] ?>">
                                        <div class="invalid-feedback"><?= $validation->getError('nis'); ?></div>
                                    </div>
                                </div>

                                <!-- Nama -->
                                <div class="col-md-7">
                                    <div class="form-group mb-4">
                                        <label class="form-label-custom" for="nama">Nama Lengkap</label>
                                        <input type="text" id="nama" name="nama"
                                            class="form-control form-control-modern <?= $validation->getError('nama') ? 'is-invalid' : ''; ?>" 
                                            placeholder="Nama Siswa" value="<?= old('nama') ?? $oldInput['nama'] ?? $data['nama_siswa'] ?>">
                                        <div class="invalid-feedback"><?= $validation->getError('nama'); ?></div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <!-- Kelas -->
                                <div class="col-md-6">
                                    <div class="form-group mb-4">
                                        <label class="form-label-custom" for="kelas">Kelas / Penempatan</label>
                                        <select class="form-control form-control-modern <?= $validation->getError('id_kelas') ? 'is-invalid' : ''; ?>" id="kelas" name="id_kelas">
                                            <option value="">-- Pilih Kelas --</option>
                                            <?php foreach ($kelas as $value): ?>
                                                <option value="<?= $value['id_kelas']; ?>" <?= (old('id_kelas') == $value['id_kelas'] || (isset($oldInput['id_kelas']) && $oldInput['id_kelas'] == $value['id_kelas']) || ($value['id_kelas'] == $data['id_kelas'])) ? 'selected' : ''; ?>>
                                                    <?= $value['kelas']; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <div class="invalid-feedback"><?= $validation->getError('id_kelas'); ?></div>
                                    </div>
                                </div>

                                <!-- Jenis Kelamin -->
                                <div class="col-md-6">
                                    <div class="form-group mb-4">
                                        <label class="form-label-custom">Jenis Kelamin</label>
                                        <?php
                                            $jenisKelamin = (old('jk') ?? $oldInput['jk'] ?? $data['jenis_kelamin']);
                                            $l = ($jenisKelamin == 'Laki-laki' || $jenisKelamin == '1') ? 'checked' : '';
                                            $p = ($jenisKelamin == 'Perempuan' || $jenisKelamin == '2') ? 'checked' : '';
                                        ?>
                                        <div class="gender-box">
                                            <label class="gender-option">
                                                <input type="radio" name="jk" value="1" <?= $l; ?>>
                                                <span class="gender-card">Laki-laki</span>
                                            </label>
                                            <label class="gender-option">
                                                <input type="radio" name="jk" value="2" <?= $p; ?>>
                                                <span class="gender-card">Perempuan</span>
                                            </label>
                                        </div>
                                        <?php if($validation->getError('jk')): ?>
                                            <div class="text-danger small font-weight-bold mt-2 ml-1"><?= $validation->getError('jk'); ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <!-- No HP -->
                                <div class="col-md-6">
                                    <div class="form-group mb-4">
                                        <label class="form-label-custom" for="hp">No. WhatsApp / HP</label>
                                        <input type="number" id="hp" name="no_hp"
                                            class="form-control form-control-modern <?= $validation->getError('no_hp') ? 'is-invalid' : ''; ?>"
                                            value="<?= old('no_hp') ?? $oldInput['no_hp'] ?? $data['no_hp'] ?>" placeholder="08xxxx">
                                        <div class="invalid-feedback"><?= $validation->getError('no_hp'); ?></div>
                                    </div>
                                </div>

                                <!-- RFID -->
                                <div class="col-md-6">
                                    <div class="form-group mb-4">
                                        <label class="form-label-custom" for="rfid">NIS Card Code</label>
                                        <input type="text" id="rfid" name="rfid"
                                            class="form-control form-control-modern <?= $validation->getError('rfid') ? 'is-invalid' : ''; ?>"
                                            value="<?= old('rfid') ?? $oldInput['rfid'] ?? $data['rfid_code'] ?? '' ?>"
                                            placeholder="Tap Kartu RFID...">
                                        <div class="invalid-feedback"><?= $validation->getError('rfid'); ?></div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-4">
                                <div class="col-md-6 mb-2">
                                    <a href="<?= base_url('admin/siswa') ?>" class="btn btn-light btn-block py-3 font-weight-bold text-muted" style="border-radius: 12px;">
                                        Batal & Kembali
                                    </a>
                                </div>
                                <div class="col-md-6">
                                    <button type="submit" class="btn btn-primary btn-block btn-save">
                                        <i class="material-icons align-middle mr-1">update</i> Perbarui Data
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