<?= $this->extend('templates/admin_page_layout') ?>

<?= $this->section('styles') ?>
<style>
    :root {
        --primary: #4361ee;
        --success: #2ec4b6;
        --bg-body: #f8f9fc;
        --card-border: #edf2f7;
        --text-dark: #1a202c;
        --text-muted: #718096;
        --danger-pulang: #b7094c; 
        --warning-pulang: #f77f00;
        --info-pkl: #6f42c1;
    }

    body {
        background-color: var(--bg-body);
        color: #2d3748;
    }

    /* Card Styling */
    .card-custom {
        border: 1px solid var(--card-border);
        border-radius: 16px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        background: #ffffff;
        margin-bottom: 1.5rem;
    }

    .section-title {
        font-weight: 700;
        color: var(--text-dark);
        letter-spacing: -0.5px;
        margin-bottom: 0.25rem;
    }

    /* Button Kelas Styling */
    .btn-kelas {
        border-radius: 12px;
        padding: 12px 10px;
        font-weight: 600;
        transition: all 0.3s ease;
        border: 1.5px solid transparent;
        margin-bottom: 10px;
        font-size: 0.9rem;
    }

    .btn-outline-primary-custom {
        background: rgba(67, 97, 238, 0.05);
        color: var(--primary);
        border-color: rgba(67, 97, 238, 0.1);
    }

    .btn-outline-primary-custom:hover {
        background: var(--primary);
        color: white;
    }

    .btn-success-custom {
        background: var(--success) !important;
        color: white !important;
        box-shadow: 0 4px 12px rgba(46, 196, 182, 0.3);
    }

    /* Form Modern Styling */
    .form-control-modern {
        border-radius: 10px;
        border: 1.5px solid var(--card-border);
        padding: 10px 15px;
        height: auto;
        font-weight: 500;
        transition: all 0.2s;
    }

    .form-control-modern:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.1);
    }

    /* Layouting Grid */
    .grid-kelas {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
        gap: 12px;
    }

    .uppercase { text-transform: uppercase; }

    /* Custom Style untuk Filter Kehadiran Row di Bawah */
    .btn-filter-status {
        border-radius: 24px;
        padding: 8px 20px;
        font-size: 0.8rem;
        font-weight: 700;
        border: 1px solid #edf2f7;
        background-color: #ffffff;
        transition: all 0.25s ease-in-out;
        box-shadow: 0 2px 4px rgba(0,0,0,0.02);
    }

    .btn-filter-status:hover {
        box-shadow: 0 4px 6px rgba(0,0,0,0.06);
        transform: translateY(-1px);
    }

    /* State Filter Aktif */
    .btn-filter-status.active-semua { background-color: #8338ec !important; color: #ffffff !important; box-shadow: 0 4px 12px rgba(131, 56, 236, 0.3) !important; border-color: transparent; }
    .btn-filter-status.active-hadir { background-color: #4cc9f0 !important; color: #ffffff !important; box-shadow: 0 4px 12px rgba(76, 201, 240, 0.3) !important; border-color: transparent; }
    .btn-filter-status.active-sakit { background-color: #f77f00 !important; color: #ffffff !important; box-shadow: 0 4px 12px rgba(247, 127, 0, 0.3) !important; border-color: transparent; }
    .btn-filter-status.active-izin { background-color: #00b4d8 !important; color: #ffffff !important; box-shadow: 0 4px 12px rgba(0, 180, 216, 0.3) !important; border-color: transparent; }
    .btn-filter-status.active-alfa { background-color: #e63946 !important; color: #ffffff !important; box-shadow: 0 4px 12px rgba(230, 57, 70, 0.3) !important; border-color: transparent; }
    .btn-filter-status.active-pkl { background-color: #6f42c1 !important; color: #ffffff !important; box-shadow: 0 4px 12px rgba(111, 66, 193, 0.3) !important; border-color: transparent; }
    .btn-filter-status.active-terlambat { background-color: #e01e37 !important; color: #ffffff !important; box-shadow: 0 4px 12px rgba(224, 30, 55, 0.3) !important; border-color: transparent; }
    .btn-filter-status.active-terlambat-pulang { background-color: var(--danger-pulang) !important; color: #ffffff !important; box-shadow: 0 4px 12px rgba(183, 9, 76, 0.3) !important; border-color: transparent; }
    .btn-filter-status.active-belum-pulang { background-color: var(--warning-pulang) !important; color: #ffffff !important; box-shadow: 0 4px 12px rgba(247, 127, 0, 0.3) !important; border-color: transparent; }
    .btn-filter-status.active-belum-masuk { background-color: #6c757d !important; color: #ffffff !important; box-shadow: 0 4px 12px rgba(108, 117, 125, 0.3) !important; border-color: transparent; }

    @media (max-width: 576px) {
        .grid-kelas {
            grid-template-columns: repeat(2, 1fr);
        }
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="content py-4">
    <div class="container-fluid">
        
        <div class="row mb-4">
            <div class="col-12">
                <h3 class="section-title">Manajemen Presensi Siswa</h3>
                <p class="text-muted">Kelola dan pantau kehadiran siswa berdasarkan filter kelas dan tanggal.</p>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card card-custom">
                    <div class="card-body p-4">
                        <div class="row align-items-end">
                            <div class="col-12 d-flex flex-wrap justify-content-between align-items-end mb-3">
                                <div style="min-width: 250px; max-width: 320px;" class="flex-grow-1 pr-sm-3 mb-3 mb-sm-0">
                                    <label class="small font-weight-bold text-muted uppercase mb-2 d-block">PILIH TANGGAL</label>
                                    <input class="form-control form-control-modern" type="date" name="tanggal" id="tanggal" value="<?= date('Y-m-d'); ?>" onchange="onDateChange()">
                                </div>
                                <div>
                                    <button type="button" class="btn btn-md font-weight-bold shadow-sm px-4 text-white" style="background-color: var(--info-pkl); border-radius: 10px; height: calc(2.25rem + 14px);" data-toggle="modal" data-target="#pklMassalModal">
                                        <i class="fas fa-plane-departure mr-1"></i> SETTING PKL
                                    </button>
                                </div>
                            </div>

                            <div class="col-12 mt-2 mb-2">
                                <label class="small font-weight-bold text-muted uppercase mb-3 d-block">PILIH KELAS</label>
                                <div class="grid-kelas">
                                    <button id="kelas-all" 
                                            onclick="getSiswa('all', 'Semua Kelas')" 
                                            class="btn btn-kelas btn-outline-primary-custom w-100">
                                        SEMUA
                                    </button>
                                    
                                    <?php foreach ($kelas as $value): ?>
                                        <button id="kelas-<?= $value['id_kelas']; ?>" 
                                                onclick="getSiswa(<?= $value['id_kelas']; ?>, '<?= $value['kelas']; ?>')" 
                                                class="btn btn-kelas btn-outline-primary-custom w-100">
                                            <?= $value['kelas']; ?>
                                        </button>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div id="containerStatistik" class="col-lg-12 mb-4" style="display: none;">
                <div class="card card-custom border-0 shadow-sm" style="background: #f8f9fa;">
                    <div class="card-body p-3">
                        <div class="d-flex flex-wrap align-items-stretch justify-content-between text-center" style="gap: 12px;">
                            
                            <div class="bg-white p-3 flex-fill rounded shadow-sm border-left" style="border-left: 4px solid #2ec4b6 !important; min-width: 100px;">
                                <small class="text-muted d-block uppercase font-weight-bold mb-1" style="font-size: 10px; letter-spacing: 0.5px;">HADIR</small>
                                <h3 class="mb-0 font-weight-bolder text-success" id="stat-hadir">0</h3>
                            </div>

                            <div class="bg-white p-3 flex-fill rounded shadow-sm border-left" style="border-left: 4px solid #ff9f43 !important; min-width: 100px;">
                                <small class="text-muted d-block uppercase font-weight-bold mb-1" style="font-size: 10px; letter-spacing: 0.5px;">SAKIT</small>
                                <h3 class="mb-0 font-weight-bolder text-warning" id="stat-sakit">0</h3>
                            </div>

                            <div class="bg-white p-3 flex-fill rounded shadow-sm border-left" style="border-left: 4px solid #3a86ff !important; min-width: 100px;">
                                <small class="text-muted d-block uppercase font-weight-bold mb-1" style="font-size: 10px; letter-spacing: 0.5px;">IZIN</small>
                                <h3 class="mb-0 font-weight-bolder text-info" id="stat-izin">0</h3>
                            </div>

                            <div class="bg-white p-3 flex-fill rounded shadow-sm border-left" style="border-left: 4px solid #e63946 !important; min-width: 100px;">
                                <small class="text-muted d-block uppercase font-weight-bold mb-1" style="font-size: 10px; letter-spacing: 0.5px;">ALFA</small>
                                <h3 class="mb-0 font-weight-bolder text-danger" id="stat-alfa">0</h3>
                            </div>

                            <div class="bg-white p-3 flex-fill rounded shadow-sm border-left" style="border-left: 4px solid #e01e37 !important; min-width: 115px;">
                                <small class="text-muted d-block uppercase font-weight-bold mb-1" style="font-size: 10px; letter-spacing: 0.5px; color: #e01e37 !important;">TERLAMBAT MASUK</small>
                                <h3 class="mb-0 font-weight-bolder" style="color: #e01e37 !important;" id="stat-terlambat">0</h3>
                            </div>

                            <div class="bg-white p-3 flex-fill rounded shadow-sm border-left" style="border-left: 4px solid var(--danger-pulang) !important; min-width: 115px;">
                                <small class="d-block uppercase font-weight-bold mb-1" style="font-size: 10px; letter-spacing: 0.5px; color: var(--danger-pulang) !important;">TERLAMBAT PULANG</small>
                                <h3 class="mb-0 font-weight-bolder" style="color: var(--danger-pulang) !important;" id="stat-terlambat-pulang">0</h3>
                            </div>

                            <div class="bg-white p-3 flex-fill rounded shadow-sm border-left" style="border-left: 4px solid var(--warning-pulang) !important; min-width: 115px;">
                                <small class="d-block uppercase font-weight-bold mb-1" style="font-size: 10px; letter-spacing: 0.5px; color: var(--warning-pulang) !important;">BELUM SCAN PULANG</small>
                                <h3 class="mb-0 font-weight-bolder" style="color: var(--warning-pulang) !important;" id="stat-belum-pulang">0</h3>
                            </div>

                            <div class="bg-white p-3 flex-fill rounded shadow-sm border-left" style="border-left: 4px solid #94a3b8 !important; min-width: 115px;">
                                <small class="text-muted d-block uppercase font-weight-bold mb-1" style="font-size: 10px; letter-spacing: 0.5px;">BELUM PRESENSI MASUK</small>
                                <h3 class="mb-0 font-weight-bolder text-secondary" id="stat-belum">0</h3>
                            </div>

                            <div class="p-3 flex-fill bg-primary rounded shadow-sm text-white" style="background-color: var(--primary) !important; min-width: 100px;">
                                <small class="text-white d-block uppercase font-weight-bold mb-1" style="font-size: 10px; letter-spacing: 0.5px; opacity: 0.9;">TOTAL SISWA</small>
                                <h3 class="mb-0 font-weight-bolder text-white" id="stat-total">0</h3>
                            </div>

                        </div>

                        <hr class="my-3 border-0 bg-transparent">
                        
                        <div class="d-flex flex-wrap align-items-center justify-content-start" style="gap: 8px;">
                            <span class="small font-weight-bold text-muted uppercase mr-2" style="font-size: 11px;">Filter Cepat Status:</span>
                            <button onclick="filterStatusTabel('all', this)" class="btn btn-filter-status active-semua" style="color: #070707;">SEMUA STATUS</button>
                            <button onclick="filterStatusTabel('hadir_normal', this)" class="btn btn-filter-status" style="color: #2ec4b6;">HADIR TEPAT WAKTU</button>
                            <button onclick="filterStatusTabel('sakit', this)" class="btn btn-filter-status" style="color: #f77f00;">SAKIT</button>
                            <button onclick="filterStatusTabel('izin', this)" class="btn btn-filter-status" style="color: #00b4d8;">IZIN</button>
                            <button onclick="filterStatusTabel('alfa', this)" class="btn btn-filter-status" style="color: #e63946;">ALFA</button>
                            <button onclick="filterStatusTabel('pkl', this)" class="btn btn-filter-status" style="color: var(--info-pkl);">SEDANG PKL</button>
                            <button onclick="filterStatusTabel('terlambat', this)" class="btn btn-filter-status" style="color: #e01e37;">TERLAMBAT MASUK</button>
                            <button onclick="filterStatusTabel('terlambat_pulang', this)" class="btn btn-filter-status" style="color: var(--danger-pulang);">TERLAMBAT SCAN PULANG</button>
                            <button onclick="filterStatusTabel('belum_pulang', this)" class="btn btn-filter-status" style="color: var(--warning-pulang);">BELUM PRESENSI PULANG</button>
                            <button onclick="filterStatusTabel('belum', this)" class="btn btn-filter-status" style="color: #6c757d;">BELUM PRESENSI MASUK</button>
                        </div>

                    </div>
                </div>
            </div>

            <div class="col-lg-12">
                <div id="dataSiswa">
                    <div class="card card-custom">
                        <div class="card-body text-center py-5">
                            <div class="spinner-border text-primary mb-3" role="status"></div>
                            <p class="text-muted">Menyiapkan dan memuat data presensi...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="ubahModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content shadow-lg">
                <div class="modal-header border-0">
                    <h5 class="modal-title font-weight-bold">Update Status Kehadiran</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div id="modalFormUbahSiswa" class="modal-body">
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="pklMassalModal" tabindex="-1" role="dialog" aria-labelledby="pklMassalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content shadow-lg" style="border-radius: 16px;">
                <div class="modal-header border-0 pt-4 px-4">
                    <h5 class="modal-title font-weight-bold text-dark" id="pklMassalTitle">
                        <i class="fas fa-calendar-alt text-primary mr-2"></i>Atur Jadwal PKL Per Kelas
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="formPklMassal" onsubmit="simpanPklMassal(event)">
                    <div class="modal-body px-4">
                        <p class="text-muted small">Fitur ini akan mengubah seluruh status kehadiran siswa pada kelas terpilih menjadi <strong>PKL</strong> dalam rentang waktu yang ditentukan.</p>
                        
                        <div class="form-group mb-3">
                            <label class="small font-weight-bold text-muted uppercase mb-2">Pilih Kelas</label>
                            <select class="form-control form-control-modern" name="id_kelas" required>
                                <option value="" disabled selected>-- Pilih Kelas Target --</option>
                                <?php foreach ($kelas as $value): ?>
                                    <option value="<?= $value['id_kelas']; ?>"><?= $value['kelas']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="row">
                            <div class="col-md-6 form-group mb-3">
                                <label class="small font-weight-bold text-muted uppercase mb-2">Tanggal Mulai</label>
                                <input type="date" class="form-control form-control-modern" name="tanggal_mulai" required>
                            </div>
                            <div class="col-md-6 form-group mb-3">
                                <label class="small font-weight-bold text-muted uppercase mb-2">Tanggal Selesai</label>
                                <input type="date" class="form-control form-control-modern" name="tanggal_selesai" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pb-4 px-4 d-flex justify-content-end" style="gap: 8px;">
                        <button type="button" class="btn font-weight-bold px-4" style="border-radius: 10px; background-color: #edf2f7; color: var(--text-dark);" data-dismiss="modal">BATAL</button>
                        <button type="submit" class="btn font-weight-bold px-4 text-white" style="border-radius: 10px; background-color: var(--primary);">SIMPAN JADWAL</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    var lastIdKelas;
    var lastKelas;

    $(document).ready(function() {
        getSiswa('all', 'Semua Kelas');
    });

    function onDateChange() {
        if (lastIdKelas != null && lastKelas != null) getSiswa(lastIdKelas, lastKelas);
    }

    function getSiswa(idKelas, kelas) {
        var tanggal = $('#tanggal').val();
        updateBtn(idKelas);
        
        $('#dataSiswa').html(`
            <div class="card card-custom">
                <div class="card-body text-center py-5">
                    <div class="spinner-border text-primary mb-3" role="status"></div>
                    <p class="text-muted">Memuat data siswa ${kelas}...</p>
                </div>
            </div>
        `);

        jQuery.ajax({
            url: "<?= base_url('/admin/absen-siswa'); ?>",
            type: 'post',
            data: {
                'kelas': kelas,
                'id_kelas': idKelas,
                'tanggal': tanggal
            },
            success: function (response) {
                $('#dataSiswa').hide().html(response).fadeIn(400);
                $('#containerStatistik').fadeIn();
                calculateStats();
            },
            error: function (xhr, status, thrown) {
                $('#dataSiswa').html('<div class="alert alert-danger">Gagal mengambil data: ' + thrown + '</div>');
            }
        });

        lastIdKelas = idKelas;
        lastKelas = kelas;
    }

    function updateBtn(id_btn) {
        $('.btn-kelas').removeClass('btn-success-custom').addClass('btn-outline-primary-custom');
        $('#kelas-' + id_btn).removeClass('btn-outline-primary-custom').addClass('btn-success-custom');
    }

    function calculateStats() {
        let hadir = 0, sakit = 0, izin = 0, alfa = 0, belum = 0, terlambat = 0, terlambatPulang = 0, belumPulang = 0;
        
        $('.btn-filter-status').removeClass(function(index, className) {
            return (className.match(/(^|\s)active-\S+/g) || []).join(' ');
        });
        $('.btn-filter-status').first().addClass('active-semua');

        $('#dataSiswa table tbody tr').each(function() {
            let row = $(this);
            let status = row.find('.badge-status-text').text().trim().toUpperCase();
            
            let isTelatMasuk = row.attr('data-terlambat-masuk') === 'YA';
            let isTelatPulang = row.attr('data-terlambat-pulang') === 'YA';
            let isBelumPulang = row.attr('data-belum-pulang') === 'YA';
            
            if (status === 'HADIR') {
                hadir++;
                if (isTelatMasuk) terlambat++;
                if (isTelatPulang) terlambatPulang++;
                if (isBelumPulang) belumPulang++;
            }
            else if (status === 'SAKIT') sakit++;
            else if (status === 'IZIN') izin++;
            else if (status === 'ALFA') alfa++;
            else if (status === 'BELUM PRESENSI' || status === 'BELUM ABSEN') belum++;
        });

        $('#stat-hadir').text(hadir);
        $('#stat-sakit').text(sakit);
        $('#stat-izin').text(izin);
        $('#stat-alfa').text(alfa);
        $('#stat-terlambat').text(terlambat);
        $('#stat-terlambat-pulang').text(terlambatPulang); 
        $('#stat-belum-pulang').text(belumPulang); 
        $('#stat-belum').text(belum);
        
        $('#stat-total').text($('#dataSiswa table tbody tr').length);
    }

    function filterStatusTabel(targetStatus, element) {
        $('.btn-filter-status').removeClass(function(index, className) {
            return (className.match(/(^|\s)active-\S+/g) || []).join(' ');
        });
        
        if (targetStatus === 'all') $(element).addClass('active-semua');
        else if (targetStatus === 'hadir_normal') $(element).addClass('active-hadir');
        else if (targetStatus === 'sakit') $(element).addClass('active-sakit');
        else if (targetStatus === 'izin') $(element).addClass('active-izin');
        else if (targetStatus === 'alfa') $(element).addClass('active-alfa');
        else if (targetStatus === 'pkl') $(element).addClass('active-pkl');
        else if (targetStatus === 'terlambat') $(element).addClass('active-terlambat');
        else if (targetStatus === 'terlambat_pulang') $(element).addClass('active-terlambat-pulang');
        else if (targetStatus === 'belum_pulang') $(element).addClass('active-belum-pulang');
        else if (targetStatus === 'belum') $(element).addClass('active-belum-masuk');

        $('#dataSiswa table tbody tr').each(function() {
            let row = $(this);
            let statusBadge = row.find('.badge-status-text').text().trim().toUpperCase();
            
            let isTelatMasuk = row.attr('data-terlambat-masuk') === 'YA';
            let isTelatPulang = row.attr('data-terlambat-pulang') === 'YA';
            let isBelumPulang = row.attr('data-belum-pulang') === 'YA';

            if (targetStatus === 'all') {
                row.show();
            } else if (targetStatus === 'hadir_normal') {
                if (statusBadge === 'HADIR' && !isTelatMasuk) row.show(); else row.hide();
            } else if (targetStatus === 'terlambat') {
                if (statusBadge === 'HADIR' && isTelatMasuk) row.show(); else row.hide();
            } else if (targetStatus === 'terlambat_pulang') {
                if (statusBadge === 'HADIR' && isTelatPulang) row.show(); else row.hide();
            } else if (targetStatus === 'belum_pulang') {
                if (statusBadge === 'HADIR' && isBelumPulang) row.show(); else row.hide();
            } else if (targetStatus === 'pkl') {
                if (statusBadge === 'PKL') row.show(); else row.hide();
            } else if (targetStatus === 'sakit') {
                if (statusBadge === 'SAKIT') row.show(); else row.hide();
            } else if (targetStatus === 'izin') {
                if (statusBadge === 'IZIN') row.show(); else row.hide();
            } else if (targetStatus === 'alfa') {
                if (statusBadge === 'ALFA') row.show(); else row.hide();
            } else if (targetStatus === 'belum') {
                if (statusBadge === 'BELUM PRESENSI' || statusBadge === 'BELUM ABSEN') row.show(); else row.hide();
            }
        });
    }

    // PERBAIKAN: Penyertaan Token CSRF secara dinamis pada Array Serialize
    function simpanPklMassal(e) {
        e.preventDefault();
        
        var formData = $('#formPklMassal').serializeArray();
        formData.push({ name: '<?= csrf_token() ?>', value: '<?= csrf_hash() ?>' });
        
        Swal.fire({
            title: 'Konfirmasi Jadwal',
            text: "Apakah Anda yakin ingin menyetel status PKL massal untuk kelas terpilih?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: varGetComputed('--primary'),
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Simpan!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                jQuery.ajax({
                    url: "<?= base_url('/admin/absen-siswa/pkl-massal'); ?>",
                    type: 'post',
                    data: $.param(formData),
                    dataType: 'json',
                    success: function(response) {
                        if(response.status === true || response['status'] === true) {
                            $('#pklMassalModal').modal('hide');
                            $('#formPklMassal')[0].reset();
                            
                            if (lastIdKelas != null && lastKelas != null) {
                                getSiswa(lastIdKelas, lastKelas);
                            }
                            
                            Swal.fire('Berhasil!', response.message || 'Jadwal PKL massal berhasil disimpan.', 'success');
                        } else {
                            Swal.fire('Gagal!', response.message || 'Terjadi kendala pada server.', 'error');
                        }
                    },
                    error: function(xhr) {
                        console.error("Detail Log Error Server: ", xhr.responseText);
                        Swal.fire('Error!', 'Gagal menghubungi server.', 'error');
                    }
                });
            }
        });
    }

    function varGetComputed(varName) {
        return getComputedStyle(document.documentElement).getPropertyValue(varName).trim();
    }

    function getDataKehadiran(idPresensi, idSiswa) {
        $('#modalFormUbahSiswa').html('<div class="text-center p-4"><div class="spinner-border text-primary" role="status"></div></div>');
        jQuery.ajax({
            url: "<?= base_url('/admin/absen-siswa/kehadiran'); ?>",
            type: 'post',
            data: { 
                'id_presensi': idPresensi, 
                'id_siswa': idSiswa 
            },
            success: function (response) {
                $('#modalFormUbahSiswa').html(response);
            }
        });
    }

    function ubahKehadiran() {
        var tanggal = $('#tanggal').val();
        var form = $('#formUbah').serializeArray();
        form.push({ name: 'tanggal', value: tanggal });

        jQuery.ajax({
            url: "<?= base_url('/admin/absen-siswa/edit'); ?>",
            type: 'post',
            data: form,
            success: function (response) {
                if (response['status']) {
                    $('#ubahModal').modal('hide');
                    getSiswa(lastIdKelas, lastKelas);
                    Swal.fire({
                        icon: 'success',
                        title: 'Tersimpan!',
                        text: 'Status kehadiran ' + response['nama_siswa'] + ' berhasil diperbarui.',
                        showConfirmButton: false,
                        timer: 1500
                    });
                } else {
                    Swal.fire({ 
                        icon: 'error', 
                        title: 'Gagal', 
                        text: 'Terjadi kesalahan saat menyimpan data.' 
                    });
                }
            },
            error: function() {
                Swal.fire({ icon: 'error', title: 'Error', text: 'Koneksi server terputus.' });
            }
        });
    }
</script>
<?= $this->endSection() ?>