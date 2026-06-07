<?= $this->extend('templates/admin_page_layout') ?>

<?= $this->section('styles') ?>

<title>Data Guru</title>

<style>
    :root {
        --primary: #4361ee;
        --success-gradient: linear-gradient(60deg, #26a69a, #2ec4b6);
        --card-border: #edf2f7;
    }

    /* Memperbaiki posisi Card agar tidak terlalu mepet header */
    .content {
        padding-top: 30px !important;
    }

    /* Card Modern Restoration */
    .card-modern {
        border-radius: 15px !important;
        box-shadow: 0 4px 20px 0 rgba(0, 0, 0, 0.05) !important;
        border: none !important;
        margin-top: 30px !important;
    }

    /* Memperbaiki Card Header agar rapi (Style Material yang di-modernisasi) */
    .card-modern .card-header-modern {
        background: var(--success-gradient) !important;
        box-shadow: 0 4px 20px 0 rgba(0, 0, 0, 0.14), 0 7px 10px -5px rgba(46, 196, 182, 0.4) !important;
        border-radius: 10px !important;
        margin: -20px 15px 0 !important; /* Margin negatif untuk efek melayang Material */
        padding: 20px !important;
        position: relative;
        color: #fff;
    }

    .section-title {
        font-weight: 700 !important;
        margin-top: 0 !important;
        margin-bottom: 5px !important;
        color: #ffffff !important;
        font-size: 1.2rem !important;
    }

    .section-subtitle {
        color: rgba(255, 255, 255, 0.8) !important;
        font-size: 0.85rem !important;
        margin: 0 !important;
    }

    /* Tombol-tombol di dalam Header */
    .header-actions {
        display: flex;
        gap: 8px;
        justify-content: flex-end;
    }

    .btn-header {
        background: rgba(255, 255, 255, 0.15) !important;
        color: white !important;
        border: 1px solid rgba(255, 255, 255, 0.3) !important;
        border-radius: 8px !important;
        padding: 8px 12px !important;
        font-size: 12px !important;
        font-weight: 600 !important;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        text-transform: none !important;
        transition: all 0.2s !important;
        box-shadow: none !important;
    }

    .btn-header:hover {
        background: rgba(255, 255, 255, 0.3) !important;
        transform: translateY(-2px);
    }

    .btn-header i {
        font-size: 16px !important;
    }

    /* Container Data */
    #dataGuru {
        padding: 20px !important;
        min-height: 200px;
    }

    /* Responsive */
    @media (max-width: 991px) {
        .header-actions {
            margin-top: 15px;
            justify-content: start;
        }
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="content">
    <div class="container-fluid">
        
        <!-- Row Utama -->
        <div class="row">
            <div class="col-md-12">
                
                <?php if (session()->getFlashdata('msg')): ?>
                    <div class="alert alert-<?= session()->getFlashdata('error') == true ? 'danger' : 'success' ?> shadow-sm mb-4">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <i class="material-icons">close</i>
                        </button>
                        <span><?= session()->getFlashdata('msg') ?></span>
                    </div>
                <?php endif; ?>

                <div class="card card-modern">
                    <!-- Header Kartu -->
                    <div class="card-header-modern">
                        <div class="row align-items-center">
                            <div class="col-lg-6 col-md-12">
                                <h4 class="section-title">Daftar Guru</h4>
                                <p class="section-subtitle">Tahun Pelajaran <?= $generalSettings->school_year; ?></p>
                            </div>
                            <div class="col-lg-6 col-md-12 text-right">
                                <div class="header-actions">
                                    <a class="btn btn-header" href="<?= base_url('admin/guru/create'); ?>">
                                        <i class="material-icons">person_add</i> Tambah Data
                                    </a>
                                    <button class="btn btn-header" onclick="getDataGuru()">
                                        <i class="material-icons">refresh</i> Refresh
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Body Kartu -->
                    <div class="card-body">
                        <div id="dataGuru">
                            <div class="text-center py-5">
                                <div class="spinner-border text-teal" role="status"></div>
                                <p class="text-muted mt-2">Menyiapkan data...</p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function() {
        getDataGuru();
    });

    function getDataGuru() {
        $('#dataGuru').html(`
            <div class="text-center py-5">
                <div class="spinner-border text-info" role="status"></div>
                <p class="text-muted mt-2">Sinkronisasi database...</p>
            </div>
        `);

        $.ajax({
            url: "<?= base_url('/admin/guru'); ?>",
            type: 'POST',
            data: {
                '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
            },
            success: function (response) {
                $('#dataGuru').hide().html(response).fadeIn(400);
            },
            error: function (xhr, status, thrown) {
                $('#dataGuru').html(`
                    <div class="alert alert-danger text-center">
                        Gagal memuat data. Silahkan coba Refresh kembali.
                    </div>
                `);
            }
        });
    }
</script>
<?= $this->endSection() ?>