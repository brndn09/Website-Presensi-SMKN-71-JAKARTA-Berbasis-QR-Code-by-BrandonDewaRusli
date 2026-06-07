<?= $this->extend('templates/admin_page_layout') ?>

<?= $this->section('styles') ?>
<style>
    :root { --success-gradient: linear-gradient(60deg, #26a69a, #2ec4b6); }
    .content { padding-top: 50px !important; }
    .card-modern { border-radius: 15px !important; border: none !important; margin-top: 40px !important; background: #fff !important; box-shadow: 0 1px 15px rgba(0,0,0,0.05) !important; }
    .card-modern .card-header-modern { background: var(--success-gradient) !important; box-shadow: 0 4px 20px 0 rgba(0, 0, 0, 0.14), 0 7px 10px -5px rgba(38, 166, 154, 0.4) !important; border-radius: 12px !important; margin: -40px 20px 15px !important; padding: 25px !important; position: relative !important; z-index: 3 !important; color: #fff; }
    .form-label-custom { font-size: 0.7rem !important; font-weight: 800 !important; color: #718096 !important; text-transform: uppercase !important; letter-spacing: 0.05em !important; margin-bottom: 10px !important; display: block; }
    .form-control-modern { border-radius: 10px !important; border: 1.5px solid #edf2f7 !important; padding: 12px 16px !important; font-size: 0.9rem !important; }
    .gender-box { display: flex; gap: 12px; }
    .gender-option { flex: 1; margin: 0; cursor: pointer; }
    .gender-option input { display: none; }
    .gender-card { display: block; padding: 12px; text-align: center; border: 1.5px solid #edf2f7; border-radius: 10px; font-weight: 600; color: #a0aec0; }
    .gender-option input:checked ~ .gender-card { border-color: #26a69a; background: rgba(38, 166, 154, 0.05); color: #26a69a; }
    .btn-save-modern { flex: 1; background: var(--success-gradient) !important; border-radius: 10px !important; padding: 15px !important; font-weight: 700 !important; color: white !important; border: none !important; }
    .btn-cancel-modern { flex: 1; background: #a0aec0 !important; color: white !important; border-radius: 10px !important; padding: 15px !important; font-weight: 700 !important; text-align: center; text-decoration: none !important; }
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
                            <i class="material-icons mr-3" style="font-size: 32px;">edit</i>
                            <div>
                                <h4 class="m-0 font-weight-bold" style="color:white">Edit Data Guru</h4>
                                <span class="small text-white-50">Perbarui data NIP dan Nama Guru.</span>
                            </div>
                        </div>
                    </div>

                    <div class="card-body px-4 py-5">
                        <form action="<?= base_url('admin/guru/edit'); ?>" method="post">
                            <?= csrf_field() ?>
                            <?php $validation = \Config\Services::validation(); ?>
                            <input type="hidden" name="id" value="<?= $data['id_guru'] ?>">

                            <div class="mb-4">
                                <label class="form-label-custom">NUPTK / NIP</label>
                                <input type="text" name="nuptk" class="form-control form-control-modern <?= $validation->getError('nuptk') ? 'is-invalid' : ''; ?>" 
                                value="<?= old('nuptk') ?? $data['nuptk'] ?>">
                                <div class="invalid-feedback"><?= $validation->getError('nuptk'); ?></div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label-custom">NAMA LENGKAP</label>
                                <input type="text" name="nama" class="form-control form-control-modern <?= $validation->getError('nama') ? 'is-invalid' : ''; ?>" 
                                value="<?= old('nama') ?? $data['nama_guru'] ?>">
                                <div class="invalid-feedback"><?= $validation->getError('nama'); ?></div>
                            </div>

                            <div class="mb-5">
                                <label class="form-label-custom">JENIS KELAMIN</label>
                                <div class="gender-box">
                                    <?php
                                        $jkVal = old('jk') ?? $data['jenis_kelamin'];
                                        $isL = ($jkVal == 'Laki-laki' || $jkVal == '1');
                                        $isP = ($jkVal == 'Perempuan' || $jkVal == '2');
                                    ?>
                                    <label class="gender-option">
                                        <input type="radio" name="jk" value="1" <?= $isL ? 'checked' : ''; ?>>
                                        <span class="gender-card">Laki-laki</span>
                                    </label>
                                    <label class="gender-option">
                                        <input type="radio" name="jk" value="2" <?= $isP ? 'checked' : ''; ?>>
                                        <span class="gender-card">Perempuan</span>
                                    </label>
                                </div>
                                <div class="invalid-feedback d-block"><?= $validation->getError('jk'); ?></div>
                            </div>

                            <div class="d-flex" style="gap:15px">
                                <a href="<?= base_url('admin/guru') ?>" class="btn-cancel-modern">Batal</a>
                                <button type="submit" class="btn-save-modern">Perbarui Data</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>