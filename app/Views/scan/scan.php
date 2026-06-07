<?= $this->extend('templates/starting_page_layout'); ?>

<?= $this->section('navaction') ?>
<a href="<?= base_url('/admin'); ?> " class="btn btn-primary pull-right pl-3 shadow-sm">
    <i class="material-icons mr-2">dashboard</i>
    Dashboard
</a>

<a href="<?= base_url('/logout'); ?> " class="btn btn-danger pull-right pl-3 shadow-sm">
    <i class="material-icons mr-2">exit_to_app</i>
    Logout
</a>
<?= $this->endSection() ?>

<?= $this->section('content'); ?>
<?php
// Logika penentuan tombol lawan (Opposite Button)
$oppBtn = ($waktu == 'Masuk') ? 'pulang' : 'masuk';
?>

<style>
    /* Global Styling & Background */
    body {
        background: linear-gradient(rgba(255, 255, 255, 0.85), rgba(255, 255, 255, 0.85)), 
                    url('<?= base_url('assets/img/bg-sekolah.jpg') ?>'); 
        background-size: cover;
        background-attachment: fixed;
        font-family: 'Poppins', sans-serif;
    }

    /* Card Utama dengan efek Glassmorphism */
    .card-main {
        border: none;
        border-radius: 20px;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(10px);
        margin-bottom: 30px;
    }

    /* Floating Header */
    .card-header-floating {
        background: linear-gradient(45deg, #9c27b0, #e91e63) !important;
        border-radius: 15px !important;
        margin-top: -30px;
        padding: 20px !important;
        box-shadow: 0 8px 20px rgba(156, 39, 176, 0.4);
    }

    /* Scanner Styling */
    .preview-container {
        border: 5px solid #fff;
        border-radius: 20px;
        overflow: hidden;
        background: #000;
        box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        position: relative;
    }

    /* PERBAIKAN UTAMA: Membalik video secara horizontal agar menghasilkan efek cermin (Mirror) */
    #previewKamera {
        width: 100%;
        max-height: 320px;
        object-fit: cover;
        transform: scaleX(-1) !important;
        -webkit-transform: scaleX(-1) !important;
    }

    /* Toggle Switch Area */
    .mode-selector {
        background: #f0f2f5;
        padding: 10px 20px;
        border-radius: 50px;
        display: inline-flex;
        gap: 20px;
        box-shadow: inset 0 2px 4px rgba(0,0,0,0.05);
    }

    /* Side Info Cards */
    .info-card {
        border: none;
        border-radius: 15px;
        background: rgba(255, 255, 255, 0.8);
        transition: all 0.3s ease;
    }
    
    .info-card:hover {
        transform: translateY(-5px);
        background: #fff;
    }

    .border-left-primary { border-left: 5px solid #9c27b0; }
    .border-left-info { border-left: 5px solid #00bcd4; }

    /* Custom Input RFID */
    #rfidInput {
        border: 2px dashed #9c27b0;
        border-radius: 15px;
        font-size: 1.5rem;
        font-weight: bold;
        letter-spacing: 5px;
        transition: all 0.3s ease;
    }

    #rfidInput:focus {
        border-style: solid;
        box-shadow: 0 0 15px rgba(156, 39, 176, 0.2);
    }

    /* Placeholder untuk hasil scan jika belum ada data */
    .hasil-scan-placeholder {
        border: 2px dashed #ccc;
        border-radius: 15px;
        height: 100%;
        min-height: 250px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #aaa;
        flex-direction: column;
    }

    /* Memperbesar ukuran teks informasi hasil scan presensi siswa */
    .hasil-scan-placeholder p {
        font-size: 1.25rem !important;
        font-weight: 600 !important;
        color: #718096 !important;
    }

    #hasilScan {
        font-size: 1.25rem !important;
    }

    #hasilScan .alert, 
    #hasilScan text, 
    #hasilScan h4, 
    #hasilScan p, 
    #hasilScan span {
        font-size: 1.3rem !important;
        font-weight: 700 !important;
        line-height: 1.5 !important;
    }
</style>

<div class="main-panel">
    <div class="content pt-5">
        <div class="container-fluid px-md-4">
            <div class="row justify-content-center">
                
                <div class="col-lg-2 order-2 order-lg-1">
                    <div class="card info-card border-left-primary shadow-sm mb-4">
                        <div class="card-body">
                            <div class="font-weight-bold text-primary" style="font-size: 1rem;">
                                <i class="material-icons align-middle mr-1">lightbulb</i> Tips Scan
                            </div>
                            <hr>
                            <ul class="pl-3 small text-muted" style="line-height: 1.6;">
                                <li>Pastikan cahaya ruangan cukup agar QR Code terbaca jelas.</li>
                                <li>Posisikan QR Code di dalam bingkai kamera.</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col-lg-8 order-1 order-lg-2">
                    <div class="card card-main">
                        <div class="col-11 mx-auto card-header card-header-floating text-white">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <h4 class="card-title mb-0"><b>Presensi <?= $waktu; ?></b></h4>
                                    <p class="card-category mb-0 opacity-8">Silahkan Scan QR atau Masukan NIS</p>
                                </div>
                                <div class="col-md-4 text-md-right mt-2 mt-md-0">
                                    <a href canvas="" class="d-none"></a>
                                    <a href="<?= base_url("scan/$oppBtn"); ?>" class="btn btn-light btn-sm font-weight-bold text-dark px-3 shadow-sm">
                                        PINDAH KE <?= strtoupper($oppBtn); ?>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="card-body text-center px-4 mt-4">
                            <div class="mode-selector mb-4">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="toggleKamera" checked>
                                    <label class="custom-control-label font-weight-bold" for="toggleKamera">Mode Kamera</label>
                                </div>
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="toggleRFID">
                                    <label class="custom-control-label font-weight-bold" for="toggleRFID">Mode RFID / NIS</label>
                                </div>
                            </div>

                            <div class="row text-left">
                                
                                <div class="col-md-6 mb-4">
                                    <div id="cameraSection">
                                        <div class="d-flex align-items-center justify-content-center mb-3">
                                            <i class="material-icons text-muted mr-2">videocam</i>
                                            <select id="pilihKamera" class="form-control form-control-sm bg-light border-0 rounded-pill w-75 shadow-sm"></select>
                                        </div>
                                        <div class="preview-container shadow-lg mb-2">
                                            <div id="searching" class="text-center d-none p-5">
                                                <div class="spinner-grow text-primary" role="status"></div>
                                                <h5 class="mt-3 font-weight-bold">Memproses Data...</h5>
                                            </div>
                                            <video id="previewKamera" autoplay muted playsinline></video>
                                        </div>
                                    </div>

                                    <div id="rfidSection" style="display: none;" class="py-2">
                                        <div class="mb-4 text-center">
                                            <div id="rfidStatus" class="mb-3">
                                                <span class="badge badge-pill badge-secondary py-2 px-4 shadow-sm" id="statusBadge">
                                                    <i class="material-icons align-middle mr-1" style="font-size: 18px;">usb_off</i>
                                                    <span id="statusText">RFID Reader Standby</span>
                                                </span>
                                            </div>
                                            <input type="text" id="rfidInput" class="form-control text-center py-4 mx-auto w-100" 
                                                   placeholder="MASUKAN NIS" autocomplete="off">
                                            <small class="text-muted d-block mt-3">Masukan NIS Jika Tidak Membawa QR Code.</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6 mb-4">
                                    <div class="font-weight-bold text-muted mb-2"><i class="material-icons align-middle mr-1">info</i> Informasi Hasil Scan:</div>
                                    <div id="hasilScan">
                                        <div class="hasil-scan-placeholder">
                                            <i class="material-icons" style="font-size: 48px; color: #ccc;">qr_code_scanner</i>
                                            <p class="mt-2 small text-center px-3">Menunggu data presensi siswa...</p>
                                        </div>
                                    </div>
                                </div>

                            </div> 
                        </div>
                    </div>
                </div>

                <div class="col-lg-2 order-3">
                    <div class="card info-card border-left-info shadow-sm mb-4">
                        <div class="card-body">
                            <div class="font-weight-bold text-info" style="font-size: 1rem;">
                                <i class="material-icons align-middle mr-1">help_outline</i> Panduan
                            </div>
                            <hr>
                            <p class="small text-muted mb-2">1. Pilih mode <b>Kamera</b> atau <b>Masukan No NIS</b>.</p>
                            <p class="small text-muted mb-2">2. Jika data valid, suara <b>"Beep"</b> akan terdengar.</p>
                            <p class="small text-muted mb-0">3. Klik <b>Dashboard</b> untuk rekap harian.</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<script type="text/javascript" src="<?= base_url('assets/js/plugins/zxing/zxing.min.js') ?>"></script>
<script src="<?= base_url('assets/js/core/jquery-3.5.1.min.js') ?>"></script>

<script type="text/javascript">
    let selectedDeviceId = null;
    let audio = new Audio("<?= base_url('assets/audio/beep.mp3'); ?>");
    const codeReader = new ZXing.BrowserMultiFormatReader();
    const sourceSelect = $('#pilihKamera');

    // Inisialisasi Scanner
    function initScanner() {
        if (!$('#toggleKamera').is(':checked')) return;

        codeReader.listVideoInputDevices()
            .then(videoInputDevices => {
                if (videoInputDevices.length < 1) {
                    alert("Kamera tidak ditemukan!");
                    return;
                }

                if (selectedDeviceId == null) {
                    selectedDeviceId = videoInputDevices.length > 1 ? videoInputDevices[1].deviceId : videoInputDevices[0].deviceId;
                }

                sourceSelect.html('');
                videoInputDevices.forEach((element) => {
                    const sourceOption = `<option value="${element.deviceId}" ${element.deviceId == selectedDeviceId ? 'selected' : ''}>${element.label}</option>`;
                    sourceSelect.append(sourceOption);
                });

                startScan();
            })
            .catch(err => console.error(err));
    }

    function startScan() {
        // Memastikan elemen video kembali terlihat saat scanning ulang dimulai
        $('#previewKamera').removeClass('d-none');
        $('#searching').addClass('d-none');

        codeReader.decodeFromVideoDevice(selectedDeviceId, 'previewKamera', (result, err) => {
            if (result) {
                $('#previewKamera').addClass('d-none');
                $('#searching').removeClass('d-none');
                
                cekData(result.text);

                codeReader.reset();
                setTimeout(() => {
                    if($('#toggleKamera').is(':checked')) startScan();
                }, 2500);
            }
        });
    }

    // Ajax Kirim Data ke Controller
    function cekData(code) {
        $.ajax({
            url: "<?= base_url('scan/cek'); ?>",
            type: 'POST',
            data: {
                'unique_code': code,
                'waktu': '<?= strtolower($waktu); ?>',
                '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
            },
            success: function (response) {
                audio.play();
                $('#hasilScan').hide().html(response).fadeIn();
            },
            error: function (xhr) {
                $('#hasilScan').html(`<div class="alert alert-danger" style="font-size: 1.3rem !important; font-weight: bold;">Terjadi kesalahan sistem.</div>`);
            }
        });
    }

    $(document).ready(function () {
        initScanner();

        // Listener Toggle Mode
        $('#toggleKamera').on('change', function () {
            if (this.checked) {
                $('#cameraSection').slideDown();
                initScanner();
            } else {
                codeReader.reset();
                $('#cameraSection').slideUp();
            }
        });

        // Toggle RFID / Manual NIS Input
        $('#toggleRFID').on('change', function () {
            if (this.checked) {
                $('#rfidSection').slideDown();
                setTimeout(() => $('#rfidInput').focus(), 300);
            } else {
                $('#rfidSection').slideUp();
            }
        });

        sourceSelect.on('change', function () {
            selectedDeviceId = $(this).val();
            codeReader.reset();
            startScan();
        });

        // Input RFID / Enter manual NIS
        $('#rfidInput').on('keypress', function (e) {
            if (e.which == 13) {
                let code = $(this).val().trim();
                if (code !== "") {
                    cekData(code);
                    $(this).val('');
                }
            }
        });

        // Auto Focus RFID (Memastikan input field pembaca selalu siaga)
        setInterval(() => {
            if ($('#toggleRFID').is(':checked') && !$('input, select').is(':focus')) {
                $('#rfidInput').focus();
            }
        }, 3000);

        $('#rfidInput').on('focus', function() {
            $('#statusBadge').removeClass('badge-secondary').addClass('badge-success');
            $('#statusText').text('RFID Reader: Aktif');
        }).on('blur', function() {
            $('#statusBadge').removeClass('badge-success').addClass('badge-secondary');
            $('#statusText').text('RFID Reader: Tidak Fokus (Klik Disini)');
        });
    });
</script>
<?= $this->endSection(); ?>