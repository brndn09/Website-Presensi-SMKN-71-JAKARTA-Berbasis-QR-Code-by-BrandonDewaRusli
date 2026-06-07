<?= $this->extend('templates/admin_page_layout') ?>

<?= $this->section('styles') ?>
<style>
    :root {
        --success-gradient: linear-gradient(60deg, #26a69a, #2ec4b6);
        --primary: #4361ee;
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
        background: var(--success-gradient) !important;
        box-shadow: 0 4px 20px 0 rgba(0, 0, 0, 0.14), 0 7px 10px -5px rgba(38, 166, 154, 0.4) !important;
        border-radius: 12px !important;
        margin: -20px 20px 0 !important;
        padding: 20px !important;
        color: #fff;
    }

    .section-title {
        font-weight: 700;
        color: #ffffff;
        letter-spacing: -0.5px;
        margin-bottom: 0;
    }

    .section-subtitle {
        color: rgba(255, 255, 255, 0.8);
        font-size: 0.85rem;
    }

    /* Modern Uploader Area */
    .dm-uploader {
        border: 2px dashed #cbd5e1;
        border-radius: 20px;
        padding: 50px 20px;
        text-align: center;
        transition: all 0.3s ease;
        background: #fbfcfe;
        cursor: pointer;
        position: relative;
    }

    .dm-uploader:hover, .dm-uploader.active {
        border-color: #26a69a;
        background: rgba(38, 166, 154, 0.05);
    }

    .dm-upload-icon i {
        font-size: 64px;
        background: var(--success-gradient);
        -webkit-background-clip: text;
        background-clip: text;
        -webkit-text-fill-color: transparent;
        margin-bottom: 15px;
    }

    .btn-choose-file {
        background: var(--success-gradient);
        border: none;
        border-radius: 12px;
        padding: 10px 25px;
        font-weight: 700;
        color: white;
        box-shadow: 0 4px 12px rgba(38, 166, 154, 0.2);
    }

    /* Progress Spinner & List */
    .csv-upload-spinner {
        display: none;
        padding: 25px;
        text-align: center;
        background: #fff;
        border-radius: 15px;
        margin-top: 20px;
        border: 1px solid var(--card-border);
    }

    .csv-uploaded-files {
        max-height: 350px;
        overflow-y: auto;
        border-radius: 15px;
        margin-top: 20px;
        border: 1px solid var(--card-border);
    }

    .list-group-item {
        font-size: 0.9rem;
        padding: 12px 20px;
        border: none;
        border-bottom: 1px solid #f1f5f9;
        font-weight: 500;
    }

    /* Help Section Buttons */
    .btn-help {
        border-radius: 12px;
        padding: 14px;
        font-weight: 700;
        margin-bottom: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 12px;
        border: none;
        transition: all 0.2s;
        background: #f0fdfa;
        color: #0d9488;
    }

    .btn-help:hover {
        background: #0d9488;
        color: white;
        transform: translateY(-2px);
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <?= view('admin/_messages'); ?>
                
                <div class="row">
                    <!-- Left: Upload Section -->
                    <div class="col-12 col-xl-8">
                        <div class="card card-modern">
                            <div class="card-header-modern">
                                <h4 class="section-title">Bulk Import Guru</h4>
                                <p class="section-subtitle">Impor data tenaga pengajar secara massal via file CSV.</p>
                            </div>
                            <div class="card-body p-4 p-md-5">
                                <div id="drag-and-drop-zone" class="dm-uploader shadow-sm">
                                    <div class="dm-upload-icon">
                                        <i class="material-icons">cloud_upload</i>
                                    </div>
                                    <h4 class="font-weight-bold text-dark mb-2">Tarik & Lepas File Di Sini</h4>
                                    <p class="text-muted mb-4 px-4">Pastikan file bertipe .csv dan mengikuti format template resmi.</p>
                                    
                                    <div class="btn btn-choose-file">
                                        <span>Buka File Browser</span>
                                        <input type="file" title='Klik untuk menambah file' />
                                    </div>
                                </div>

                                <!-- Loading Status -->
                                <div id="csv_upload_spinner" class="csv-upload-spinner shadow-sm">
                                    <strong class="text-csv-importing text-teal d-block mb-3">
                                        <span class="spinner-border spinner-border-sm mr-2"></span> Sedang mengimpor data guru...
                                    </strong>
                                    <strong class="text-csv-import-completed text-success" style="display:none;">
                                        <i class="material-icons align-middle mr-1">check_circle</i> Impor Berhasil Diselesaikan!
                                    </strong>
                                    <div class="progress mt-3" style="height: 10px; border-radius: 10px;">
                                        <div id="importProgressBar" class="progress-bar progress-bar-striped progress-bar-animated bg-success" style="width: 0%"></div>
                                    </div>
                                </div>

                                <!-- Result List -->
                                <div class="csv-uploaded-files-container">
                                    <ul id="csv_uploaded_files" class="list-group csv-uploaded-files shadow-sm"></ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right: Help Section -->
                    <div class="col-12 col-xl-4">
                        <div class="card card-modern">
                            <div class="card-header-modern">
                                <h5 class="section-title">Panduan Import</h5>
                                <p class="section-subtitle">Instruksi Penggunaan</p>
                            </div>
                            <div class="card-body p-4 pt-5">
                                <p class="text-muted small mb-4">Gunakan template di bawah ini agar data dapat diproses oleh sistem tanpa kendala.</p>
                                
                                <form action="<?= base_url('admin/guru/downloadCSVFilePost'); ?>" method="post">
                                    <?= csrf_field(); ?>
                                    <button class="btn btn-help w-100" name="submit" value="csv_guru_template">
                                        <i class="material-icons">file_download</i> Download CSV Template
                                    </button>
                                </form>

                                <div class="alert mt-4 border-0" style="border-radius: 15px; background: #fffbeb; color: #92400e; font-size: 0.85rem;">
                                    <div class="d-flex">
                                        <i class="material-icons mr-2" style="font-size: 20px;">warning</i>
                                        <div>
                                            <strong>Catatan Penting:</strong>
                                            <p class="mb-0 mt-1">Hindari penggunaan tanda kutip ganda (") dalam isi file CSV Anda karena dapat merusak struktur data.</p>
                                        </div>
                                    </div>
                                </div>
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
    $(function () {
        $('#drag-and-drop-zone').dmUploader({
            url: '<?= base_url("admin/guru/generateCSVObjectPost"); ?>',
            multiple: false,
            extFilter: ["csv"],
            extraData: function (id) {
                return { '<?= csrf_token() ?>': '<?= csrf_hash(); ?>' };
            },
            onDragEnter: function () { this.addClass('active'); },
            onDragLeave: function () { this.removeClass('active'); },
            onNewFile: function (id, file) {
                $("#csv_upload_spinner").fadeIn();
                $("#csv_upload_spinner .text-csv-importing").show();
                $("#csv_upload_spinner .text-csv-import-completed").hide();
                $("#importProgressBar").css('width', '0%');
                $("#csv_uploaded_files").empty();
            },
            onUploadSuccess: function (id, response) {
                try {
                    var obj = JSON.parse(response);
                    if (obj.result == 1) {
                        if (obj.numberOfItems > 0) {
                            addCSVItem(obj.numberOfItems, obj.txtFileName, 1);
                        } else {
                            $("#csv_upload_spinner").hide();
                        }
                    } else {
                        $("#csv_upload_spinner").hide();
                    }
                } catch (e) {
                    swal("Error", "Format file CSV tidak valid!", "error");
                    $("#csv_upload_spinner").hide();
                }
            }
        });
    });

    function addCSVItem(numberOfItems, txtFileName, index) {
        if (index <= numberOfItems) {
            $.ajax({
                type: "POST",
                url: '<?= base_url("admin/guru/importCSVItemPost"); ?>',
                data: setAjaxData({ 'txtFileName': txtFileName, 'index': index }),
                success: function (response) {
                    var objSub = JSON.parse(response);
                    var percent = Math.round((index / numberOfItems) * 100);
                    $("#importProgressBar").css('width', percent + '%');

                    if (objSub.result == 1) {
                        $("#csv_uploaded_files").prepend('<li class="list-group-item text-success animated fadeIn"><i class="material-icons align-middle mr-2" style="font-size:18px">check_circle</i>' + objSub.index + '. ' + objSub.guru.nama_guru + ' (' + objSub.guru.nuptk + ')</li>');
                    } else {
                        $("#csv_uploaded_files").prepend('<li class="list-group-item text-danger animated fadeIn"><i class="material-icons align-middle mr-2" style="font-size:18px">error_outline</i>Baris ' + objSub.index + ': Gagal diimpor</li>');
                    }
                    
                    if (objSub.index == numberOfItems) {
                        $("#csv_upload_spinner .text-csv-importing").hide();
                        $("#csv_upload_spinner .text-csv-import-completed").fadeIn();
                    } else {
                        addCSVItem(numberOfItems, txtFileName, index + 1);
                    }
                },
                error: function () {
                    swal("Gagal", "Terjadi gangguan koneksi.", "warning").then(() => location.reload());
                },
            });
        }
    }
</script>
<?= $this->endSection() ?>