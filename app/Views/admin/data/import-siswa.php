<?= $this->extend('templates/admin_page_layout') ?>

<?= $this->section('styles') ?>
<title>Import Data Siswa</title>
<style>
    :root {
        --primary: #4361ee;
        --primary-gradient: linear-gradient(60deg, #4361ee, #3a86ff);
        --success: #2ec4b6;
        --bg-body: #f8f9fc;
        --card-border: #edf2f7;
    }

    body { background-color: var(--bg-body); font-family: 'Inter', sans-serif; }
    .content { padding-top: 30px !important; }

    /* Card Modern */
    .card-modern {
        border: none; border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        background: #ffffff; margin-top: 30px;
    }

    .card-header-modern {
        background: var(--primary-gradient) !important;
        box-shadow: 0 4px 20px 0 rgba(0, 0, 0, 0.14), 0 7px 10px -5px rgba(67, 97, 238, 0.4) !important;
        border-radius: 12px !important;
        margin: -20px 20px 0 !important;
        padding: 20px !important; color: #fff;
    }

    .section-title { font-weight: 700; letter-spacing: -0.5px; margin-bottom: 0; }
    .section-subtitle { color: rgba(255, 255, 255, 0.8); font-size: 0.85rem; }

    /* Uploader */
    .dm-uploader {
        border: 2px dashed #cbd5e1; border-radius: 20px;
        padding: 50px 20px; text-align: center;
        transition: all 0.3s ease; background: #fbfcfe;
        cursor: pointer; position: relative;
    }

    .dm-uploader:hover, .dm-uploader.active {
        border-color: var(--primary);
        background: rgba(67, 97, 238, 0.05);
    }

    .dm-upload-icon i {
        font-size: 64px; background: var(--primary-gradient);
        -webkit-background-clip: text; background-clip: text;
        -webkit-text-fill-color: transparent; margin-bottom: 15px;
    }

    .btn-choose-file {
        border-radius: 12px; padding: 10px 25px; font-weight: 700;
        box-shadow: 0 4px 12px rgba(67, 97, 238, 0.2);
    }

    /* Progress & List */
    .csv-upload-spinner {
        display: none; padding: 25px; text-align: center;
        background: #fff; border-radius: 15px;
        margin-top: 20px; border: 1px solid var(--card-border);
    }

    .csv-uploaded-files {
        max-height: 350px; overflow-y: auto;
        border-radius: 15px; margin-top: 20px;
        border: 1px solid var(--card-border); list-style: none; padding: 0;
    }

    .list-group-item {
        font-size: 0.9rem; padding: 12px 20px;
        border-bottom: 1px solid #f1f5f9; font-weight: 500;
        display: flex; align-items: center;
    }

    /* Help Sidebar */
    .btn-help {
        border-radius: 12px; padding: 14px; font-weight: 700;
        margin-bottom: 12px; display: flex; align-items: center;
        justify-content: center; gap: 12px; border: none;
        transition: all 0.2s; background: rgba(46, 196, 182, 0.08);
        color: var(--success); width: 100%;
    }

    .btn-help:hover { background: var(--success); color: white; transform: translateY(-2px); }

    .table-modern thead th {
        font-size: 0.75rem; text-transform: uppercase;
        font-weight: 800; color: #64748b; background: #f8fafc;
        padding: 15px; border: none;
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
                    <div class="col-12 col-xl-8">
                        <div class="card card-modern">
                            <div class="card-header-modern">
                                <h4 class="section-title">Import Data Siswa</h4>
                                <p class="section-subtitle">Gunakan file CSV (koma) untuk mengunggah data siswa.</p>
                            </div>
                            <div class="card-body p-4 p-md-5">
                                <div id="drag-and-drop-zone" class="dm-uploader shadow-sm">
                                    <div class="dm-upload-icon">
                                        <i class="material-icons">cloud_upload</i>
                                    </div>
                                    <h4 class="font-weight-bold text-dark mb-2">Tarik & Lepas File CSV</h4>
                                    <p class="text-muted mb-4">Pastikan Format Seperti Pada Tamplate<b></b></p>
                                    
                                    <div class="btn btn-primary btn-choose-file">
                                        <span>Pilih File Dari Komputer</span>
                                        <input type="file" title='Klik untuk menambah file' />
                                    </div>
                                </div>

                                <div id="csv_upload_spinner" class="csv-upload-spinner shadow-sm">
                                    <strong class="text-csv-importing text-primary d-block mb-3">
                                        <span class="spinner-border spinner-border-sm mr-2"></span> 
                                        Memproses: <span id="current_process_label">0</span> / <span id="total_process_label">0</span>
                                    </strong>
                                    <strong class="text-csv-import-completed text-success" style="display:none;">
                                        <i class="material-icons align-middle mr-1">check_circle</i> Import Selesai!
                                    </strong>
                                    <div class="progress mt-3" style="height: 10px; border-radius: 10px;">
                                        <div id="importProgressBar" class="progress-bar progress-bar-striped progress-bar-animated bg-primary" style="width: 0%"></div>
                                    </div>
                                </div>

                                <div class="csv-uploaded-files-container">
                                    <ul id="csv_uploaded_files" class="csv-uploaded-files shadow-sm"></ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-xl-4">
                        <div class="card card-modern">
                            <div class="card-header-modern" style="background: linear-gradient(60deg, #26a69a, #2ec4b6) !important;">
                                <h5 class="section-title">Pusat Bantuan</h5>
                                <p class="section-subtitle">Instruksi & Template</p>
                            </div>
                            <div class="card-body p-4 pt-5">
                                <button type="button" class="btn btn-help" data-toggle="modal" data-target="#modalKelas">
                                    <i class="material-icons">info_outline</i> Referensi ID Kelas
                                </button>
                                
                                <form action="<?= base_url('admin/siswa/downloadCSVFilePost'); ?>" method="post">
                                    <?= csrf_field(); ?>
                                    <button class="btn btn-help" name="submit" value="csv_siswa_template">
                                        <i class="material-icons">file_download</i> Unduh Template CSV
                                    </button>
                                </form>

                                <div class="alert alert-info mt-4 border-0" style="border-radius: 15px; background: #eff6ff; color: #1e40af;">
                                    <strong><i class="material-icons align-middle" style="font-size:18px">lightbulb</i> Tips Penting:</strong>
                                    <ul class="small mb-0 mt-2 pl-3">
                                        <li>Pemisah harus <b>Koma (,)</b> bukan Titik Koma.</li>
                                        <li>Simpan sebagai <b>CSV (Comma Delimited)</b>.</li>
                                        <li>NIS tidak boleh ganda di database.</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalKelas" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="border-radius: 20px; border: none;">
            <div class="modal-header border-0 p-4">
                <h5 class="font-weight-bold mb-0">Daftar Referensi ID Kelas</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body p-4 pt-0">
                <div class="table-responsive" style="border-radius: 15px; border: 1px solid #f1f5f9;">
                    <table class="table table-hover table-modern mb-0">
                        <thead>
                            <tr>
                                <th>ID (Gunakan ini di CSV)</th>
                                <th>Nama Kelas</th>
                                <th>Jurusan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($kelas as $value) : ?>
                                <tr>
                                    <td><span class="badge badge-light px-3 py-2" style="color: var(--primary);"><?= $value['id_kelas']; ?></span></td>
                                    <td><b><?= $value['kelas']; ?></b></td>
                                    <td class="text-muted"><?= $value['jurusan']; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    // Inisialisasi Token CSRF dari CI4
    var csrfName = '<?= csrf_token() ?>';
    var csrfHash = '<?= csrf_hash() ?>';

    $(function() {
        $('#drag-and-drop-zone').dmUploader({
            url: '<?= base_url("admin/siswa/generateCSVObjectPost"); ?>',
            multiple: false,
            extFilter: ["csv"],
            extraData: function() {
                let data = {};
                data[csrfName] = csrfHash;
                return data;
            },
            onDragEnter: function() { this.addClass('active'); },
            onDragLeave: function() { this.removeClass('active'); },
            onNewFile: function(id, file) {
                $("#csv_upload_spinner").fadeIn();
                $("#csv_upload_spinner .text-csv-importing").show();
                $("#csv_upload_spinner .text-csv-import-completed").hide();
                $("#importProgressBar").css('width', '0%').addClass('progress-bar-animated').removeClass('bg-success');
                $("#csv_uploaded_files").empty();
            },
            onUploadSuccess: function(id, response) {
                try {
                    let obj = (typeof response === 'object') ? response : JSON.parse(response);
                    
                    // Update CSRF Hash agar request berikutnya tidak ditolak
                    if (obj.token) csrfHash = obj.token;

                    if (obj.result == 1) {
                        let total = obj.numberOfItems;
                        $("#total_process_label").text(total);
                        if (total > 0) {
                            addCSVItem(total, obj.txtFileName, 1);
                        } else {
                            $("#csv_upload_spinner").hide();
                            swal("Kosong", "Tidak ada data ditemukan dalam file.", "warning");
                        }
                    } else {
                        $("#csv_upload_spinner").hide();
                        swal("Gagal", obj.message || "File tidak valid", "error");
                    }
                } catch (e) {
                    console.error("Parsing Error:", e);
                    $("#csv_upload_spinner").hide();
                }
            }
        });
    });

    function addCSVItem(total, fileName, index) {
        if (index <= total) {
            $("#current_process_label").text(index);
            
            let postData = { 'txtFileName': fileName, 'index': index };
            postData[csrfName] = csrfHash; // Sertakan CSRF terbaru

            $.ajax({
                type: "POST",
                url: '<?= base_url("admin/siswa/importCSVItemPost"); ?>',
                data: postData,
                dataType: 'json',
                success: function(res) {
                    // Update CSRF dari respon server
                    if (res.token) csrfHash = res.token;

                    let percent = Math.round((index / total) * 100);
                    $("#importProgressBar").css('width', percent + '%');

                    if (res.result == 1) {
                        $("#csv_uploaded_files").prepend(
                            '<li class="list-group-item text-success animated fadeIn">' +
                            '<i class="material-icons mr-2" style="font-size:18px">check_circle</i>' +
                            index + '. ' + res.siswa.nis + ' - ' + res.siswa.nama_siswa + ' (Berhasil)</li>'
                        );
                    } else {
                        $("#csv_uploaded_files").prepend(
                            '<li class="list-group-item text-danger animated fadeIn">' +
                            '<i class="material-icons mr-2" style="font-size:18px">error_outline</i>' +
                            'Baris ' + index + ': ' + (res.message || 'Gagal Import') + '</li>'
                        );
                    }

                    if (index == total) {
                        $("#csv_upload_spinner .text-csv-importing").hide();
                        $("#csv_upload_spinner .text-csv-import-completed").fadeIn();
                        $("#importProgressBar").removeClass('progress-bar-animated').addClass('bg-success');
                        swal("Selesai", "Proses import data telah berakhir.", "success");
                    } else {
                        addCSVItem(total, fileName, index + 1);
                    }
                },
                error: function() {
                    $("#csv_upload_spinner").hide();
                    swal("Error", "Koneksi terputus di baris ke-" + index, "error");
                }
            });
        }
    }
</script>
<?= $this->endSection() ?>