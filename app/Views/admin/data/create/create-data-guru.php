<?= $this->extend('templates/admin_page_layout') ?>

<?= $this->section('styles') ?>
<title>Tambah Data Guru</title>
<style>
    :root { --success-gradient: linear-gradient(60deg, #26a69a, #2ec4b6); --bg-body: #f8f9fc; }
    .content { padding-top: 50px !important; }
    .card-modern { border-radius: 15px !important; box-shadow: 0 1px 15px rgba(0,0,0,0.05) !important; border: none !important; margin-top: 30px !important; background: #fff !important; }
    .card-modern .card-header-modern { background: var(--success-gradient) !important; box-shadow: 0 4px 20px 0 rgba(0, 0, 0, 0.14), 0 7px 10px -5px rgba(38, 166, 154, 0.4) !important; border-radius: 10px !important; margin: -40px 20px 15px !important; padding: 25px !important; position: relative !important; z-index: 3 !important; color: #fff; }
    .form-label-custom { font-size: 0.7rem !important; font-weight: 800 !important; color: #718096 !important; text-transform: uppercase !important; letter-spacing: 0.05em !important; margin-bottom: 8px !important; display: block; }
    .form-control-modern { border-radius: 10px !important; border: 1.5px solid #edf2f7 !important; padding: 12px 16px !important; height: auto !important; font-size: 0.9rem !important; background-color: #fcfdfe !important; }
    .gender-box { display: flex; gap: 12px; }
    .gender-option { flex: 1; margin: 0; cursor: pointer; }
    .gender-option input { display: none; }
    .gender-card { display: block; padding: 12px; text-align: center; border: 1.5px solid #edf2f7; border-radius: 10px; transition: all 0.2s; font-weight: 600; color: #a0aec0; }
    .gender-option input:checked ~ .gender-card { border-color: #26a69a; background: rgba(38, 166, 154, 0.05); color: #26a69a; }
    .btn-save { background: var(--success-gradient) !important; border-radius: 10px !important; padding: 15px !important; font-weight: 700 !important; color: #fff !important; border: none !important; }
    .btn-cancel { background: #a0aec0 !important; color: white !important; border-radius: 10px !important; padding: 15px !important; font-weight: 700 !important; border: none !important; }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="content">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card card-modern">
                    <div class="card-header-modern">
                        <div class="d-flex align-items-center">
                            <i class="material-icons mr-3" style="font-size: 30px;">person_add</i>
                            <div>
                                <h4 class="m-0 font-weight-bold" style="color:white">Tambah Data Guru</h4>
                                <p class="m-0 small text-white-50">Masukkan informasi NIP dan Nama Guru.</p>
                            </div>
                        </div>
                    </div>
                    <div class="card-body px-4 py-5">
                        <form action="<?= base_url('admin/guru/create'); ?>" method="post">
                            <?= csrf_field() ?>
                            <?php $validation = \Config\Services::validation(); ?>

                            <div class="mb-4">
                                <label class="form-label-custom">NUPTK / NIP</label>
                                <input type="text" name="nuptk" class="form-control form-control-modern <?= $validation->getError('nuptk') ? 'is-invalid' : ''; ?>" placeholder="Contoh: 198203..." value="<?= old('nuptk') ?>">
                                <div class="invalid-feedback"><?= $validation->getError('nuptk'); ?></div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label-custom">NAMA LENGKAP</label>
                                <input type="text" name="nama" class="form-control form-control-modern <?= $validation->getError('nama') ? 'is-invalid' : ''; ?>" placeholder="Masukkan nama lengkap" value="<?= old('nama') ?>">
                                <div class="invalid-feedback"><?= $validation->getError('nama'); ?></div>
                            </div>

                            <div class="mb-5">
                                <label class="form-label-custom">JENIS KELAMIN</label>
                                <div class="gender-box">
                                    <label class="gender-option">
                                        <input type="radio" name="jk" value="1" <?= (old('jk') == '1') ? 'checked' : ''; ?>>
                                        <span class="gender-card">Laki-laki</span>
                                    </label>
                                    <label class="gender-option">
                                        <input type="radio" name="jk" value="2" <?= (old('jk') == '2') ? 'checked' : ''; ?>>
                                        <span class="gender-card">Perempuan</span>
                                    </label>
                                </div>
                                <div class="invalid-feedback d-block"><?= $validation->getError('jk'); ?></div>
                            </div>

                            <div class="row">
                                <div class="col-6">
                                    <a href="<?= base_url('admin/guru') ?>" class="btn btn-cancel btn-block text-center">Batal</a>
                                </div>
                                <div class="col-6">
                                    <button type="submit" class="btn btn-save btn-block">Simpan Data</button>
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