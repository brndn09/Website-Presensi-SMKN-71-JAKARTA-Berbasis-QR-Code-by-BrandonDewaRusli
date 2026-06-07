<?= $this->extend('templates/admin_page_layout') ?>

<?= $this->section('content') ?>
<style>
    /* Desain UI Modern */
    .card-generate {
        border-radius: 15px !important;
        transition: all 0.3s ease;
        border: none !important;
        box-shadow: 0 4px 20px 0 rgba(0,0,0,.05) !important;
    }
    
    .card-generate:hover {
        transform: translateY(-5px);
    }

    .header-gradient-siswa {
        background: linear-gradient(60deg, #ab47bc, #8e24aa) !important;
        border-radius: 10px !important;
        padding: 20px !important;
        margin-top: -30px !important;
    }

    .btn-custom {
        border-radius: 8px !important;
        padding: 12px 20px !important;
        font-weight: 600 !important;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        transition: all 0.2s;
    }

    .custom-select-modern {
        height: 45px !important;
        border-radius: 8px !important;
        border: 1px solid #e0e0e0 !important;
        padding: 0 15px !important;
    }

    .progress-container {
        background: rgba(0,0,0,0.05);
        border-radius: 10px;
        overflow: hidden;
        margin-top: 10px;
        height: 8px;
    }

    .progress-bar-fill {
        height: 100%;
        transition: width 0.4s ease;
    }

    .info-box {
        background: #f8f9fa;
        border-radius: 10px;
        padding: 20px;
        border-left: 4px solid #00acc1;
    }
</style>

<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <!-- Flash Message -->
                <?php if (session()->getFlashdata('msg')): ?>
                    <div class="alert alert-<?= session()->getFlashdata('error') == true ? 'danger' : 'success' ?> shadow-sm">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <i class="material-icons">close</i>
                        </button>
                        <div class="d-flex align-items-center">
                            <i class="material-icons mr-2"><?= session()->getFlashdata('error') == true ? 'error' : 'check_circle' ?></i>
                            <b><?= session()->getFlashdata('msg') ?></b>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <div class="col-lg-10 col-md-12 mx-auto">
                <div class="card card-generate mt-5">
                    <div class="card-header header-gradient-siswa shadow">
                        <div class="d-flex align-items-center">
                            <i class="material-icons text-white mr-3" style="font-size: 32px;">qr_code_2</i>
                            <div>
                                <h4 class="card-title text-white mb-0"><b>Generate QR Code Siswa</b></h4>
                                <p class="card-category text-white-50 mb-0">Manajemen pembuatan kode QR untuk absensi siswa</p>
                            </div>
                        </div>
                    </div>

                    <div class="card-body pt-5 px-md-5">
                        <div class="row align-items-center mb-5">
                            <div class="col-md-7">
                                <h5 class="text-muted mb-1">Total Siswa Terdaftar</h5>
                                <h2 class="font-weight-bold text-primary mb-3"><?= count($siswa); ?> Siswa</h2>
                                <p class="text-secondary" style="font-size: 14px;">
                                    Anda dapat membuat kode QR secara massal untuk seluruh siswa atau memfilternya berdasarkan kelas tertentu. 
                                    Kode QR yang dihasilkan akan digunakan oleh siswa untuk melakukan scan absensi.
                                </p>
                            </div>
                            <div class="col-md-5 text-md-right">
                                <a href="<?= base_url('admin/siswa'); ?>" class="btn btn-outline-primary btn-round">
                                    <i class="material-icons">person_search</i> Kelola Data Siswa
                                </a>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Opsi Generate Semua -->
                            <div class="col-md-6 mb-4">
                                <div class="p-4 border rounded bg-light h-100 shadow-sm">
                                    <h5 class="font-weight-bold mb-3"><i class="material-icons align-middle mr-1 text-primary">groups</i> Seluruh Siswa</h5>
                                    <div class="row no-gutters">
                                        <div class="col-6 pr-1">
                                            <button onclick="generateAllQrSiswa()" class="btn btn-primary btn-custom w-100" id="btnGenSiswa">
                                                <i class="material-icons">autorenew</i> Generate All
                                            </button>
                                        </div>
                                        <div class="col-6 pl-1">
                                            <a href="<?= base_url('admin/qr/siswa/download'); ?>" class="btn btn-info btn-custom w-100">
                                                <i class="material-icons">download_for_offline</i> Download All
                                            </a>
                                        </div>
                                    </div>

                                    <!-- Progress Bar Siswa -->
                                    <div id="progressSiswa" class="d-none mt-4 p-3 bg-white border rounded shadow-sm">
                                        <div class="d-flex justify-content-between mb-2">
                                            <span id="progressTextSiswa" class="small font-weight-bold text-primary">Menyiapkan data...</span>
                                            <i id="progressSelesaiSiswa" class="material-icons text-success d-none">verified</i>
                                        </div>
                                        <div class="progress-container">
                                            <div id="progressBarSiswa" class="progress-bar-fill bg-primary" style="width: 0%;"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Opsi Per Kelas -->
                            <div class="col-md-6 mb-4">
                                <div class="p-4 border rounded bg-light h-100 shadow-sm">
                                    <h5 class="font-weight-bold mb-3"><i class="material-icons align-middle mr-1 text-info">filter_alt</i> Berdasarkan Kelas</h5>
                                    <form action="<?= base_url('admin/qr/siswa/download'); ?>" method="get">
                                        <div class="form-group mb-3">
                                            <select name="id_kelas" id="kelasSelect" class="custom-select custom-select-modern" required>
                                                <option value="">-- Pilih Kelas --</option>
                                                <?php foreach ($kelas as $value): ?>
                                                    <option id="idKelas<?= $value['id_kelas']; ?>" value="<?= $value['id_kelas']; ?>">
                                                        Kelas <?= $value['kelas']; ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                            <small class="text-danger font-weight-bold mt-1 d-block" id="textErrorKelas"></small>
                                        </div>
                                        
                                        <div class="row no-gutters">
                                            <div class="col-6 pr-1">
                                                <button type="button" onclick="generateQrSiswaByKelas()" class="btn btn-outline-primary btn-custom w-100">
                                                    <i class="material-icons">qr_code</i> Generate
                                                </button>
                                            </div>
                                            <div class="col-6 pl-1">
                                                <button type="submit" class="btn btn-outline-info btn-custom w-100">
                                                    <i class="material-icons">download</i> Download
                                                </button>
                                            </div>
                                        </div>
                                    </form>

                                    <!-- Progress Bar Kelas -->
                                    <div id="progressKelas" class="d-none mt-4 p-3 bg-white border rounded shadow-sm">
                                        <div class="d-flex justify-content-between mb-2">
                                            <span id="progressTextKelas" class="small font-weight-bold text-info">Memproses...</span>
                                            <i id="progressSelesaiKelas" class="material-icons text-success d-none">verified</i>
                                        </div>
                                        <div class="progress-container">
                                            <div id="progressBarKelas" class="progress-bar-fill bg-info" style="width: 0%;"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="info-box mt-4">
                            <div class="d-flex align-items-start">
                                <i class="material-icons text-info mr-3" style="font-size: 28px;">info</i>
                                <div>
                                    <h6 class="font-weight-bold mb-1">Penting:</h6>
                                    <p class="m-0 text-dark" style="font-size: 13px; line-height: 1.6;">
                                        Pastikan Anda melakukan <b>Generate</b> ulang apabila terjadi perubahan data Nama atau NIS siswa agar QR Code tetap sinkron dengan sistem. 
                                        QR Code yang sudah dihasilkan akan tersimpan di sistem dan siap untuk diunduh kapan saja.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Bagian Guru di-comment/nonaktifkan sesuai permintaan -->
                        <!-- 
                        <div class="guru-section mt-5">
                            ... bagian guru ...
                        </div> 
                        -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    const dataSiswa = [
        <?php foreach ($siswa as $value): ?>
        {
            'nama': `<?= addslashes($value['nama_siswa']) ?>`,
            'unique_code': `<?= $value['unique_code'] ?>`,
            'id_kelas': `<?= $value['id_kelas'] ?>`,
            'nomor': `<?= $value['nis'] ?>`
        },
        <?php endforeach; ?>
    ];

    // Helper AJAX
    function callAjax(url, data, successCallback) {
        return jQuery.ajax({
            url: url,
            type: 'post',
            data: data,
            success: successCallback,
            error: function(err) {
                console.error("Kesalahan proses:", err);
            }
        });
    }

    function generateAllQrSiswa() {
        if (!confirm('Generate QR Code untuk ' + dataSiswa.length + ' siswa? Proses ini mungkin memakan waktu.')) return;
        
        let i = 0;
        $('#progressSiswa').removeClass('d-none');
        $('#progressSelesaiSiswa').addClass('d-none');
        $('#btnGenSiswa').attr('disabled', true).html('<span class="spinner-border spinner-border-sm mr-2"></span> Memproses...');

        dataSiswa.forEach(element => {
            callAjax("<?= base_url('admin/generate/siswa'); ?>", {
                nama: element.nama,
                unique_code: element.unique_code,
                id_kelas: element.id_kelas,
                nomor: element.nomor
            }, function (response) {
                i++;
                let percent = (i / dataSiswa.length) * 100;
                $('#progressBarSiswa').css('width', percent + '%');
                $('#progressTextSiswa').html('Progres: ' + i + ' / ' + dataSiswa.length);
                
                if (i === dataSiswa.length) {
                    $('#progressTextSiswa').html('Berhasil! ' + i + ' QR Code Siswa selesai dibuat.');
                    $('#progressSelesaiSiswa').removeClass('d-none');
                    $('#btnGenSiswa').attr('disabled', false).html('<i class="material-icons">autorenew</i> Generate All');
                }
            });
        });
    }

    function generateQrSiswaByKelas() {
        const idKelas = $('#kelasSelect').val();
        if (idKelas == '') {
            $('#textErrorKelas').html('Silakan pilih kelas terlebih dahulu!');
            return;
        }

        $('#textErrorKelas').html('');
        $('#progressKelas').removeClass('d-none');
        $('#progressSelesaiKelas').addClass('d-none');

        callAjax("<?= base_url('admin/generate/siswa-by-kelas'); ?>", { idKelas: idKelas }, function (response) {
            const dataSiswaPerKelas = response;
            if (dataSiswaPerKelas.length < 1) {
                $('#progressKelas').addClass('d-none');
                $('#textErrorKelas').html('Data siswa kelas ini tidak ditemukan.');
                return;
            }

            let i = 0;
            dataSiswaPerKelas.forEach(element => {
                callAjax("<?= base_url('admin/generate/siswa'); ?>", {
                    nama: element.nama_siswa,
                    unique_code: element.unique_code,
                    id_kelas: element.id_kelas,
                    nomor: element.nis
                }, function () {
                    i++;
                    let percent = (i / dataSiswaPerKelas.length) * 100;
                    $('#progressBarKelas').css('width', percent + '%');
                    $('#progressTextKelas').html('Memproses: ' + i + ' / ' + dataSiswaPerKelas.length);
                    
                    if (i === dataSiswaPerKelas.length) {
                        $('#progressTextKelas').html('Selesai! QR Code kelas berhasil diperbarui.');
                        $('#progressSelesaiKelas').removeClass('d-none');
                    }
                });
            });
        });
    }
</script>
<?= $this->endSection() ?>