<?= $this->extend('templates/admin_page_layout') ?>

<?= $this->section('content') ?>
<style>
    /* Layout Dasar */
    .content-wrapper {
        padding-top: 50px;
        background-color: #f4f7f6;
        min-height: 100vh;
        padding-bottom: 50px;
    }

    .main-card {
        border-radius: 12px !important;
        border: none !important;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08) !important;
        background: #fff;
        margin-bottom: 50px;
    }

    /* Header Styling */
    .header-custom {
        background: #4caf50;
        padding: 25px 40px !important;
        margin-top: -30px !important;
        margin-left: 20px;
        margin-right: 20px;
        border-radius: 8px !important;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15) !important;
        display: flex;
        align-items: center;
    }

    .header-icon {
        background: #fff;
        width: 45px;
        height: 45px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 20px;
    }

    .header-icon i { color: #4caf50; font-size: 28px; }
    .header-text h4 { color: #fff; margin: 0; font-weight: 600; letter-spacing: 0.5px; font-size: 1.2rem; }
    .header-text p { color: rgba(255, 255, 255, 0.8); margin: 0; font-size: 13px; }

    /* Alert Info */
    .info-alert {
        background-color: #f1f8e9;
        border-left: 5px solid #4caf50;
        border-radius: 4px;
        padding: 12px 25px;
        margin: 30px 40px 20px 40px;
        display: flex;
        align-items: center;
        text-align: left;
    }

    .info-alert i { color: #4caf50; margin-right: 15px; font-size: 22px; }
    .info-alert span { color: #333; font-size: 13.5px; }

    /* Form Container */
    .form-group-container {
        border: 1px solid #e0e0e0;
        border-radius: 12px;
        padding: 25px;
        margin: 0 40px;
    }

    .form-label-custom {
        font-weight: 500;
        color: #444;
        display: flex;
        align-items: center;
        margin-bottom: 8px;
        font-size: 14px;
    }

    .form-label-custom i { margin-right: 8px; font-size: 18px; color: #4caf50; }

    .custom-input {
        border: 1px solid #ccc !important;
        border-radius: 8px !important;
        padding: 10px 15px !important;
        height: 45px !important;
        font-size: 14px !important;
        background-color: #fff !important;
        width: 100%;
    }

    .custom-input:focus {
        border-color: #4caf50 !important;
        box-shadow: 0 0 0 0.2rem rgba(76, 175, 80, 0.15) !important;
    }
    
    /* Fokus warna khusus untuk area orange/warning */
    .border-warning-focus:focus {
        border-color: #ffa000 !important;
        box-shadow: 0 0 0 0.2rem rgba(255, 160, 0, 0.15) !important;
    }

    /* Action Buttons */
    .btn-action-container {
        padding: 30px 40px 40px 40px;
        display: flex;
        gap: 15px;
        justify-content: center;
    }

    .btn-download {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 12px 25px;
        border-radius: 8px;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 13px;
        transition: 0.3s;
        border: none;
        cursor: pointer;
        min-width: 200px;
    }

    .btn-pdf { background-color: #e91e63; color: #fff; }
    .btn-pdf:hover { background-color: #d81b60; transform: translateY(-2px); }

    .btn-excel { background-color: #2e7d32; color: #fff; }
    .btn-excel:hover { background-color: #1b5e20; transform: translateY(-2px); }

    .btn-download i { margin-right: 10px; font-size: 18px; }

    .back-link {
        color: #888;
        font-size: 14px;
        text-decoration: none;
        display: inline-block;
        margin-top: 10px;
        transition: 0.2s;
    }

    .back-link:hover { color: #4caf50; }
</style>

<div class="content-wrapper">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-11 col-lg-9">
                
                <!-- CARD 1: LAPORAN ABSENSI UMUM -->
                <div class="card main-card">
                    <div class="header-custom">
                        <div class="header-icon shadow-sm">
                            <i class="material-icons">summarize</i>
                        </div>
                        <div class="header-text text-left">
                            <h4>Laporan Presensi Kelas <?= $kelas['kelas']; ?></h4>
                            <p>Rekapitulasi kehadiran siswa (Hadir, Izin, Sakit, Alpa)</p>
                        </div>
                    </div>

                    <div class="card-body p-0 text-center">
                        <div class="info-alert">
                            <i class="material-icons">info</i>
                            <span>Laporan mencakup data presensi harian selama satu bulan penuh untuk kelas ini.</span>
                        </div>

                        <form action="<?= base_url('teacher/laporan/generate'); ?>" method="post">
                            <?= csrf_field() ?>
                            <div class="form-group-container shadow-sm">
                                <div class="row text-left">
                                    <div class="col-md-12">
                                        <label class="form-label-custom">
                                            <i class="material-icons">calendar_month</i> Pilih Periode Bulan
                                        </label>
                                        <input type="month" name="bulan" class="form-control custom-input" value="<?= date('Y-m'); ?>" required>
                                    </div>
                                </div>
                            </div>

                            <div class="btn-action-container">
                                <button type="submit" name="type" value="pdf" class="btn-download btn-pdf shadow">
                                    <i class="material-icons">picture_as_pdf</i> Unduh PDF
                                </button>
                                <button type="submit" name="type" value="xls" class="btn-download btn-excel shadow">
                                    <i class="material-icons">description</i> Unduh Excel
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- CARD 2: LAPORAN KETERLAMBATAN (REKAP PRESENSI DETAIL) -->
                <div class="card main-card">
                    <div class="header-custom" style="background: #ffa000;"> <!-- Warna Kuning/Emas untuk Keterlambatan -->
                        <div class="header-icon shadow-sm">
                            <i class="material-icons" style="color: #ffa000;">history_toggle_off</i>
                        </div>
                        <div class="header-text text-left">
                            <h4>Laporan Presensi Detail <?= $kelas['kelas']; ?></h4>
                            <p>Data siswa yang masuk jam berapa, pulang jam berapa, terlambat berapa jam/menit</p>
                        </div>
                    </div>

                    <div class="card-body p-0 text-center">
                        <div class="info-alert" style="background-color: #fff8e1; border-left-color: #ffa000;">
                            <i class="material-icons" style="color: #ffa000;">warning_amber</i>
                            <span>Laporan ini menampilkan lebih detail masuk jam berapa, pulang jam berapa, terlambat berapa jam/menit dan lain lain.</span>
                        </div>

                        <!-- Endpoint diarahkan ke fungsi laporan keterlambatan -->
                        <form action="<?= base_url('teacher/laporan/terlambat'); ?>" method="post">
                            <?= csrf_field() ?>
                            <div class="form-group-container shadow-sm">
                                <div class="row text-left">
                                    
                                    <!-- Input Pilihan Jenis Periode -->
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label-custom">
                                            <i class="material-icons" style="color: #ffa000;">date_range</i> Jenis Periode
                                        </label>
                                        <select name="jenis_periode" id="jenisPeriode" class="form-control custom-input border-warning-focus" required>
                                            <option value="bulanan" selected>Bulanan</option>
                                            <option value="mingguan">Mingguan / Kustom</option>
                                        </select>
                                    </div>

                                    <!-- Input Periode Bulanan -->
                                    <div class="col-md-8 mb-3" id="containerPeriodeBulanan">
                                        <label class="form-label-custom">
                                            <i class="material-icons" style="color: #ffa000;">event_repeat</i> Pilih Periode Bulan
                                        </label>
                                        <input type="month" name="bulan" id="inputBulan" class="form-control custom-input border-warning-focus" value="<?= date('Y-m'); ?>" required>
                                    </div>

                                    <!-- Input Tanggal Mulai (Kustom/Mingguan) -->
                                    <div class="col-md-4 mb-3 d-none" id="containerPeriodeMingguanMulai">
                                        <label class="form-label-custom">
                                            <i class="material-icons" style="color: #ffa000;">today</i> Tanggal Mulai
                                        </label>
                                        <input type="date" name="tanggal_mulai" id="inputMulai" class="form-control custom-input border-warning-focus" value="<?= date('Y-m-d'); ?>">
                                    </div>

                                    <!-- Input Tanggal Selesai (Kustom/Mingguan) -->
                                    <div class="col-md-4 mb-3 d-none" id="containerPeriodeMingguanSelesai">
                                        <label class="form-label-custom">
                                            <i class="material-icons" style="color: #ffa000;">event</i> Tanggal Selesai
                                        </label>
                                        <input type="date" name="tanggal_selesai" id="inputSelesai" class="form-control custom-input border-warning-focus" value="<?= date('Y-m-d'); ?>">
                                    </div>

                                </div>
                            </div>

                            <div class="btn-action-container">
                                <button type="submit" name="type" value="pdf" class="btn-download btn-pdf shadow">
                                    <i class="material-icons">picture_as_pdf</i> Rekap PDF
                                </button>
                                <button type="submit" name="type" value="xls" class="btn-download btn-excel shadow">
                                    <i class="material-icons">table_view</i> Rekap Excel
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
    // Logic untuk toggle Input Periode Bulanan / Mingguan secara dinamis pada Card Detail
    document.getElementById('jenisPeriode').addEventListener('change', function() {
        const bulananBox = document.getElementById('containerPeriodeBulanan');
        const mingguanMulaiBox = document.getElementById('containerPeriodeMingguanMulai');
        const mingguanSelesaiBox = document.getElementById('containerPeriodeMingguanSelesai');
        
        const inputBulan = document.getElementById('inputBulan');
        const inputMulai = document.getElementById('inputMulai');
        const inputSelesai = document.getElementById('inputSelesai');

        if (this.value === 'bulanan') {
            // Tampilkan pilihan bulanan, sembunyikan kustom tanggal
            bulananBox.classList.remove('d-none');
            mingguanMulaiBox.classList.add('d-none');
            mingguanSelesaiBox.classList.add('d-none');
            
            // Atur atribut required secara dinamis
            inputBulan.setAttribute('required', 'required');
            inputMulai.removeAttribute('required');
            inputSelesai.removeAttribute('required');
        } else {
            // Sembunyikan pilihan bulanan, tampilkan kustom tanggal
            bulananBox.classList.add('d-none');
            mingguanMulaiBox.classList.remove('d-none');
            mingguanSelesaiBox.classList.remove('d-none');
            
            // Atur atribut required secara dinamis
            inputBulan.removeAttribute('required');
            inputMulai.setAttribute('required', 'required');
            inputSelesai.setAttribute('required', 'required');
        }
    });
</script>
<?= $this->endSection() ?>