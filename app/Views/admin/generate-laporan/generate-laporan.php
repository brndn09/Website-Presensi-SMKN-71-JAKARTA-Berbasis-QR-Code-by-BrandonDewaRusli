<?= $this->extend('templates/admin_page_layout') ?>

<?= $this->section('content') ?>
<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-10 mx-auto">
                
                <?php if (session()->getFlashdata('msg')): ?>
                    <div class="alert alert-<?= session()->getFlashdata('error') == true ? 'danger' : 'success' ?> alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                        <span class="ml-2"><?= session()->getFlashdata('msg') ?></span>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                <?php endif; ?>

                <div class="card shadow-sm border-0 main-container mb-5">
                    <div class="custom-card-header shadow">
                        <div class="d-flex align-items-center">
                            <div class="header-icon-container mr-3">
                                <i class="material-icons text-info" style="font-size: 20px;">assessment</i>
                            </div>
                            <div>
                                <h5 class="m-0 font-weight-bold text-white">Laporan Presensi Siswa</h5>
                                <small class="text-white-50">laporan Presensi keseluruhan (Hadir, Izin, Sakit, Alpa)</small>
                            </div>
                        </div>
                    </div>

                    <div class="card-body p-5">
                        <form action="<?= base_url('admin/laporan/siswa'); ?>" method="post" class="formPelaporan">
                            <?= csrf_field(); ?>
                            <div class="row px-md-4">
                                <div class="col-md-6 mb-4">
                                    <label class="d-flex align-items-center text-muted mb-2">
                                        <i class="material-icons mr-1" style="font-size: 18px;">calendar_today</i>
                                        <span style="font-size: 13px;">Periode Laporan</span>
                                    </label>
                                    <input type="month" name="tanggalSiswa" class="form-control custom-input" value="<?= date('Y-m'); ?>" required>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <label class="d-flex align-items-center text-muted mb-2">
                                        <i class="material-icons mr-1" style="font-size: 18px;">class</i>
                                        <span style="font-size: 13px;">Pilih Kelas</span>
                                    </label>
                                    <select name="kelas" class="form-control custom-input select-kelas" required>
                                        <option value="" disabled selected>-- Pilih Kelas --</option>
                                        <?php foreach ($kelas as $key => $value): ?>
                                            <option value="<?= $value['id_kelas']; ?>">Kelas <?= $value['kelas'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="info-dashed-box mt-3 mb-5 mx-md-4">
                                <div class="d-flex align-items-center justify-content-center py-3 px-2 text-center">
                                    <i class="material-icons text-primary mr-2" style="font-size: 20px;">info_outline</i>
                                    <span class="text-primary" style="font-size: 12px;">Data mencakup seluruh status kehadiran selama hari efektif sekolah.</span>
                                </div>
                            </div>

                            <div class="row justify-content-center px-md-4">
                                <div class="col-md-5 mb-3">
                                    <button type="submit" name="type" value="pdf" class="btn btn-export-pdf btn-block shadow-sm">
                                        <i class="material-icons mr-2" style="font-size: 18px;">picture_as_pdf</i><b>EKSPOR PDF</b>
                                    </button>
                                </div>
                                <div class="col-md-5 mb-3">
                                    <button type="submit" name="type" value="xls" class="btn btn-export-excel btn-block shadow-sm">
                                        <i class="material-icons mr-2" style="font-size: 18px;">description</i><b>EKSPOR EXCEL</b>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card shadow-sm border-0 main-container">
                    <div class="custom-card-header shadow bg-warning-gradient">
                        <div class="d-flex align-items-center">
                            <div class="header-icon-container mr-3">
                                <i class="material-icons text-warning" style="font-size: 20px;">history_toggle_off</i>
                            </div>
                            <div>
                                <h5 class="m-0 font-weight-bold text-white">Laporan Rekap Presensi Detail</h5>
                                <small class="text-white-50">Laporan rekap data siswa hadir jam berapa, pulang jam berapa, telat berapa jam/menit, dll.</small>
                            </div>
                        </div>
                    </div>

                    <div class="card-body p-5">
                        <form action="<?= base_url('admin/laporan/siswa_terlambat'); ?>" method="post" class="formPelaporan">
                            <?= csrf_field(); ?>
                            
                            <div class="row px-md-4">
                                <div class="col-md-4 mb-4">
                                    <label class="d-flex align-items-center text-muted mb-2">
                                        <i class="material-icons mr-1" style="font-size: 18px;">date_range</i>
                                        <span style="font-size: 13px;">Jenis Periode</span>
                                    </label>
                                    <select name="jenis_periode" id="jenisPeriode" class="form-control custom-input" required>
                                        <option value="bulanan" selected>Bulanan</option>
                                        <option value="mingguan">Mingguan / Kustom</option>
                                    </select>
                                </div>

                                <div class="col-md-4 mb-4" id="containerPeriodeBulanan">
                                    <label class="d-flex align-items-center text-muted mb-2">
                                        <i class="material-icons mr-1" style="font-size: 18px;">calendar_month</i>
                                        <span style="font-size: 13px;">Periode Bulan</span>
                                    </label>
                                    <input type="month" name="tanggalSiswa" id="inputBulan" class="form-control custom-input" value="<?= date('Y-m'); ?>">
                                </div>

                                <div class="col-md-4 mb-4 d-none" id="containerPeriodeMingguanMulai">
                                    <label class="d-flex align-items-center text-muted mb-2">
                                        <i class="material-icons mr-1" style="font-size: 18px;">today</i>
                                        <span style="font-size: 13px;">Tanggal Mulai</span>
                                    </label>
                                    <input type="date" name="tanggal_mulai" id="inputMulai" class="form-control custom-input" value="<?= date('Y-m-d'); ?>">
                                </div>

                                <div class="col-md-4 mb-4 d-none" id="containerPeriodeMingguanSelesai">
                                    <label class="d-flex align-items-center text-muted mb-2">
                                        <i class="material-icons mr-1" style="font-size: 18px;">event</i>
                                        <span style="font-size: 13px;">Tanggal Selesai</span>
                                    </label>
                                    <input type="date" name="tanggal_selesai" id="inputSelesai" class="form-control custom-input" value="<?= date('Y-m-d'); ?>">
                                </div>

                                <div class="col-md-4 mb-4">
                                    <label class="d-flex align-items-center text-muted mb-2">
                                        <i class="material-icons mr-1" style="font-size: 18px;">groups</i>
                                        <span style="font-size: 13px;">Pilih Kelas</span>
                                    </label>
                                    <select name="kelas" class="form-control custom-input select-kelas" required>
                                        <option value="" disabled selected>-- Pilih Kelas --</option>
                                        <?php foreach ($kelas as $key => $value): ?>
                                            <option value="<?= $value['id_kelas']; ?>">Kelas <?= $value['kelas'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="info-dashed-box-warning mt-3 mb-5 mx-md-4">
                                <div class="d-flex align-items-center justify-content-center py-3 px-2 text-center">
                                    <i class="material-icons text-warning mr-2" style="font-size: 20px;">warning_amber</i>
                                    <span class="text-warning" style="font-size: 12px;">Hanya menampilkan data siswa hadir masuk jam berapa, pulang jam berapa dan terlambat berapa jam/menit.</span>
                                </div>
                            </div>

                            <div class="row justify-content-center px-md-4">
                                <div class="col-md-5 mb-3">
                                    <button type="submit" name="type" value="pdf" class="btn btn-export-pdf btn-block shadow-sm">
                                        <i class="material-icons mr-2" style="font-size: 18px;">picture_as_pdf</i><b>REKAP PDF</b>
                                    </button>
                                </div>
                                <div class="col-md-5 mb-3">
                                    <button type="submit" name="type" value="xls" class="btn btn-export-excel btn-block shadow-sm">
                                        <i class="material-icons mr-2" style="font-size: 18px;">table_rows</i><b>REKAP EXCEL</b>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div id="errMsg" class="text-center mt-4"></div>

            </div>
        </div>
    </div>
</div>

<style>
    .main-container { border-radius: 12px !important; background-color: #ffffff; margin-top: 50px; }
    
    /* Header Absensi Umum (Biru Toska) */
    .custom-card-header { background: #00bcd4; padding: 25px 30px; border-radius: 10px; margin: -30px 20px 0 20px; position: relative; }
    
    /* Header Terlambat (Orange/Kuning) */
    .bg-warning-gradient { background: linear-gradient(60deg, #ffa726, #fb8c00) !important; }

    .header-icon-container { background: #ffffff; width: 38px; height: 38px; border-radius: 50%; display: flex; align-items: center; justify-content: center; }
    .custom-input { background-color: #f8f9fa !important; border: 1px solid #e9ecef !important; border-radius: 10px !important; height: 50px !important; padding-left: 15px !important; font-size: 14px !important; }
    .custom-input:focus { border-color: #00bcd4 !important; box-shadow: none !important; background-color: #fff !important; }

    /* Info Boxes */
    .info-dashed-box { border: 1.5px dashed #00bcd4; border-radius: 30px; }
    .info-dashed-box-warning { border: 1.5px dashed #ffa726; border-radius: 30px; }

    /* Buttons */
    .btn-export-pdf { background-color: #f85b6b !important; color: white !important; border-radius: 10px !important; height: 55px !important; border: none; transition: 0.3s; }
    .btn-export-excel { background-color: #2dce89 !important; color: white !important; border-radius: 10px !important; height: 55px !important; border: none; transition: 0.3s; }
    .btn-export-pdf:hover, .btn-export-excel:hover { filter: brightness(90%); transform: translateY(-2px); }

    .card .card-body { padding: 0; }
    @media (max-width: 768px) { .custom-card-header { margin: 0; border-radius: 12px 12px 0 0; } }
</style>

<script>
    // Logic untuk toggle Input Periode Bulanan / Mingguan secara dinamis
    document.getElementById('jenisPeriode').addEventListener('change', function() {
        const bulananBox = document.getElementById('containerPeriodeBulanan');
        const mingguanMulaiBox = document.getElementById('containerPeriodeMingguanMulai');
        const mingguanSelesaiBox = document.getElementById('containerPeriodeMingguanSelesai');
        
        const inputBulan = document.getElementById('inputBulan');
        const inputMulai = document.getElementById('inputMulai');
        const inputSelesai = document.getElementById('inputSelesai');

        if (this.value === 'bulanan') {
            bulananBox.classList.remove('d-none');
            mingguanMulaiBox.classList.add('d-none');
            mingguanSelesaiBox.classList.add('d-none');
            
            inputBulan.setAttribute('required', 'required');
            inputMulai.removeAttribute('required');
            inputSelesai.removeAttribute('required');
        } else {
            bulananBox.classList.add('d-none');
            mingguanMulaiBox.classList.remove('d-none');
            mingguanSelesaiBox.classList.remove('d-none');
            
            inputBulan.removeAttribute('required');
            inputMulai.setAttribute('required', 'required');
            inputSelesai.setAttribute('required', 'required');
        }
    });

    // Script validasi untuk semua form pelaporan sebelum submit
    document.querySelectorAll('.formPelaporan').forEach(form => {
        form.onsubmit = function(e) {
            const kelas = form.querySelector('.select-kelas').value;
            const errMsg = document.getElementById('errMsg');

            if (!kelas) {
                e.preventDefault();
                errMsg.innerHTML = `
                    <div class='alert alert-warning border-0 p-2 shadow-sm d-inline-block' style='border-radius: 8px;'>
                        <small><i class='material-icons align-middle mr-1' style='font-size:16px;'>warning</i> Pilih kelas terlebih dahulu pada form yang bersangkutan!</small>
                    </div>`;
                window.scrollTo({ top: document.body.scrollHeight, behavior: 'smooth' });
            } else {
                errMsg.innerHTML = "";
            }
        };
    });
</script>
<?= $this->endSection() ?>