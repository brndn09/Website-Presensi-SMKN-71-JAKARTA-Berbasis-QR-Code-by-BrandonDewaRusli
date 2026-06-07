<?= $this->extend('templates/admin_page_layout') ?>

<?= $this->section('styles') ?>
<style>
    :root {
        --primary: #4361ee;
        --primary-gradient: linear-gradient(60deg, #4361ee, #3a86ff);
        --card-border: #edf2f7;
    }

    /* Penyesuaian Ruang Konten */
    .content {
        padding-top: 30px !important;
    }

    /* Card Modern Restoration */
    .card-modern {
        border-radius: 15px !important;
        box-shadow: 0 4px 20px 0 rgba(0, 0, 0, 0.05) !important;
        border: none !important;
        margin-top: 30px !important;
        background: #fff;
    }

    /* Floating Header Styling */
    .card-modern .card-header-modern {
        background: var(--primary-gradient) !important;
        box-shadow: 0 4px 20px 0 rgba(0, 0, 0, 0.14), 0 7px 10px -5px rgba(67, 97, 238, 0.4) !important;
        border-radius: 10px !important;
        margin: -20px 15px 0 !important;
        padding: 20px !important;
        position: relative;
        color: #fff;
    }

    .section-title {
        font-weight: 700 !important;
        margin: 0 0 5px 0 !important;
        color: #ffffff !important;
        font-size: 1.2rem !important;
        letter-spacing: -0.5px;
    }

    .section-subtitle {
        color: rgba(255, 255, 255, 0.8) !important;
        font-size: 0.85rem !important;
        margin: 0 !important;
    }

    /* Header Actions */
    .header-actions {
        display: flex;
        gap: 8px;
        justify-content: flex-end;
        flex-wrap: wrap;
    }

    .btn-header {
        background: rgba(255, 255, 255, 0.15) !important;
        color: white !important;
        border: 1px solid rgba(255, 255, 255, 0.3) !important;
        border-radius: 8px !important;
        padding: 8px 14px !important;
        font-size: 12px !important;
        font-weight: 600 !important;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        text-transform: none !important;
        transition: all 0.2s !important;
    }

    .btn-header:hover {
        background: rgba(255, 255, 255, 0.3) !important;
        transform: translateY(-2px);
    }

    .btn-header-danger {
        background: rgba(230, 57, 70, 0.2) !important;
        border-color: rgba(230, 57, 70, 0.4) !important;
    }

    .btn-header-danger:hover {
        background: #e63946 !important;
    }

    .btn-header-warning {
        background: rgba(255, 159, 67, 0.2) !important;
        border-color: rgba(255, 159, 67, 0.4) !important;
    }

    .btn-header-warning:hover {
        background: #ff9f43 !important;
    }

    /* Filter & Search Section */
    .filter-wrapper {
        padding: 30px 25px 15px !important;
        background: #fbfcfe;
        border-bottom: 1px solid var(--card-border);
    }

    .filter-label {
        font-size: 0.7rem !important;
        font-weight: 800 !important;
        color: #718096 !important;
        text-transform: uppercase !important;
        letter-spacing: 1px !important;
        margin-bottom: 8px !important;
        display: block;
    }

    .custom-input-modern {
        border-radius: 10px !important;
        border: 1.5px solid var(--card-border) !important;
        padding: 10px 15px !important;
        height: auto !important;
        font-weight: 500 !important;
        font-size: 0.9rem !important;
        background-color: #fff !important;
        transition: all 0.2s;
    }

    .custom-input-modern:focus {
        border-color: var(--primary) !important;
        box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.1) !important;
    }

    .search-inner {
        position: relative;
    }

    .search-inner i {
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: #a0aec0;
    }

    /* Table Loading */
    .loading-container {
        padding: 60px 0;
        text-align: center;
    }

    @media (max-width: 991px) {
        .header-actions {
            margin-top: 15px;
            justify-content: flex-start;
        }
        .btn-header { flex: 1; justify-content: center; }
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                
                <?php if (session()->getFlashdata('msg')): ?>
                    <div class="alert alert-<?= session()->getFlashdata('error') == true ? 'danger' : 'success' ?> shadow-sm mb-4" style="border-radius: 10px;">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <i class="material-icons">close</i>
                        </button>
                        <span><?= session()->getFlashdata('msg') ?></span>
                    </div>
                <?php endif; ?>

                <div class="card card-modern">
                    <div class="card-header-modern">
                        <div class="row align-items-center">
                            <div class="col-lg-4 col-md-12">
                                <h4 class="section-title">Daftar Data Siswa</h4>
                                <p class="section-subtitle">Tahun Pelajaran <?= $generalSettings->school_year ?? '2023/2024'; ?></p>
                            </div>
                            <div class="col-lg-8 col-md-12">
                                <div class="header-actions">
                                    <form action="<?= base_url('admin/siswa/kenaikan-kelas'); ?>" method="post" id="formKenaikanKelas" class="d-inline">
                                        <?= csrf_field(); ?>
                                        <button type="button" class="btn btn-header btn-header-warning" id="btnKenaikanKelas">
                                            <i class="material-icons">school</i> Kenaikan Kelas Massal
                                        </button>
                                    </form>

                                    <a class="btn btn-header" href="<?= base_url('admin/siswa/create'); ?>">
                                        <i class="material-icons">person_add</i> Tambah
                                    </a>
                                    <a class="btn btn-header" href="<?= base_url('admin/siswa/bulk'); ?>">
                                        <i class="material-icons">file_upload</i> Import
                                    </a>
                                    <button class="btn btn-header btn-header-danger" id="btnBulkDelete">
                                        <i class="material-icons">delete_sweep</i> Hapus Terpilih
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="filter-wrapper">
                        <div class="row">
                            <div class="col-md-12 mb-4">
                                <label class="filter-label">Cari Nama / NISN / NIS</label>
                                <div class="search-inner">
                                    <input type="text" id="searchSiswa" class="form-control custom-input-modern" placeholder="Ketik nama atau nomor induk lalu tekan Enter...">
                                    <i class="material-icons">search</i>
                                </div>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="filter-label">Tingkat / Kelas</label>
                                <select id="filterKelasSiswa" class="form-control custom-input-modern">
                                    <option value="">Semua Tingkat</option>
                                    <?php foreach ($tingkat as $value): ?>
                                        <option value="<?= $value['tingkat']; ?>">Kelas <?= $value['tingkat']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="filter-label">Program Keahlian</label>
                                <select id="filterJurusanSiswa" class="form-control custom-input-modern">
                                    <option value="">Semua Jurusan</option>
                                    <?php foreach ($jurusan as $value): ?>
                                        <option value="<?= $value['jurusan']; ?>"><?= $value['jurusan']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="filter-label">Indeks Kelas</label>
                                <select id="filterIndexSiswa" class="form-control custom-input-modern">
                                    <option value="">Semua Indeks</option>
                                    <?php foreach ($index_kelas as $value): ?>
                                        <option value="<?= $value['index_kelas']; ?>"><?= $value['index_kelas']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="card-body p-0">
                        <div id="dataSiswa">
                            <div class="loading-container">
                                <div class="spinner-border text-primary" role="status"></div>
                                <p class="text-muted mt-3 font-weight-bold">Menyiapkan Data...</p>
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Menyimpan nama dan hash CSRF token global secara dinamis
    let csrfTokenName = '<?= csrf_token() ?>';
    let csrfHash = '<?= csrf_hash() ?>';

    // State management untuk filter data siswa
    let filters = {
        search: '',
        kelas: '',
        jurusan: '',
        index: ''
    };

    $(document).ready(function() {
        // Memuat tabel data awal ketika halaman pertama dibuka
        fetchData();

        // Event: Pencarian (KeyUp dengan fungsi Debounce / Penundaan Waktu)
        let timer;
        $('#searchSiswa').on('keyup', function() {
            clearTimeout(timer);
            filters.search = $(this).val();
            timer = setTimeout(function() {
                fetchData();
            }, 500); // Menunggu jeda mengetik selama 500ms
        });

        // Event: Trigger perubahan pada elemen Dropdown Filter
        $('#filterKelasSiswa, #filterJurusanSiswa, #filterIndexSiswa').on('change', function () {
            filters.kelas = $('#filterKelasSiswa').val();
            filters.jurusan = $('#filterJurusanSiswa').val();
            filters.index = $('#filterIndexSiswa').val();
            fetchData();
        });

        // Event: Fungsionalitas Checkbox Check All (Pilih Semua Baris Tabel)
        $(document).on('click', '#checkAll', function () {
            $('.checkbox-table').prop('checked', this.checked);
        });

        // Event: Modul Dialog Kenaikan Kelas Massal
        $('#btnKenaikanKelas').on('click', function() {
            Swal.fire({
                title: 'Konfirmasi Kenaikan Kelas & Kelulusan',
                text: "Siswa kelas 12 akan DIHAPUS otomatis (Lulus). Siswa kelas 10 & 11 akan naik tingkat. Pastikan Anda sudah mem-backup data sebelum melanjutkan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ff9f43',
                cancelButtonColor: '#cbd5e1',
                confirmButtonText: 'Ya, Proses Sekarang!',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Memproses Data...',
                        text: 'Sistem sedang memperbarui data kelas siswa, mohon tunggu.',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    $('#formKenaikanKelas').submit();
                }
            });
        });

        // Event: Eksekusi Tombol Bulk Delete (Hapus Terpilih Berfungsi Penuh)
        $('#btnBulkDelete').on('click', function() {
            let selectedIds = [];
            // Melakukan looping ke seluruh checkbox baris tabel yang sedang tercentang
            $('.checkbox-table:checked').each(function() {
                selectedIds.push($(this).val());
            });

            if (selectedIds.length === 0) {
                Swal.fire({
                    icon: 'info',
                    title: 'Pemberitahuan',
                    text: 'Silahkan pilih data siswa yang ingin dihapus terlebih dahulu.',
                    confirmButtonColor: '#4361ee'
                });
                return;
            }

            Swal.fire({
                title: 'Hapus data terpilih?',
                text: 'Yakin ingin menghapus ' + selectedIds.length + ' data siswa terpilih secara permanen?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e63946',
                cancelButtonColor: '#cbd5e1',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // Eksekusi fungsi pengiriman data penghapusan massal ke server
                    deleteSelectedSiswa(selectedIds);
                }
            });
        });
    });

    /**
     * Fungsi Asinkronus Utama untuk mengambil list HTML data siswa dari Controller
     */
    function fetchData() {
        const container = $('#dataSiswa');
        
        container.html(`
            <div class="loading-container">
                <div class="spinner-border text-primary" role="status"></div>
                <p class="text-muted mt-3 font-weight-bold">Memproses Data...</p>
            </div>
        `);

        // Menyiapkan payload data POST
        let postData = {
            keyword: filters.search,
            kelas: filters.kelas,
            jurusan: filters.jurusan,
            index: filters.index
        };
        postData[csrfTokenName] = csrfHash; // Mengisi token CSRF terbaru

        $.ajax({
            url: "<?= base_url('/admin/siswa'); ?>", 
            type: 'POST',
            data: postData,
            success: function (response) {
                // Tampilkan respon HTML tabel yang di-render dari list-data-siswa
                container.hide().html(response).fadeIn(300);
            },
            error: function (xhr) {
                container.html(`
                    <div class="p-5 text-center">
                        <i class="material-icons text-danger" style="font-size: 48px;">error_outline</i>
                        <p class="mt-2 text-dark font-weight-bold">Gagal memuat data.</p>
                        <button onclick="fetchData()" class="btn btn-sm btn-primary">Refresh Halaman</button>
                    </div>
                `);
            }
        });
    }

    /**
     * Fungsi AJAX untuk Memproses Penghapusan Massal
     * Menggunakan URL Route pencarian data /admin/siswa demi menghindari pembuatan route baru
     */
    function deleteSelectedSiswa(ids) {
        Swal.fire({
            title: 'Menghapus Data...',
            text: 'Mohon tunggu sejenak, sistem sedang menghapus data terpilih.',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // Membuat objek data berisi penanda parameter 'action'
        let requestData = {
            action: 'bulk_delete', 
            ids: ids
        };
        requestData[csrfTokenName] = csrfHash; // Menyertakan token CSRF

        $.ajax({
            url: "<?= base_url('/admin/siswa'); ?>", // Tetap mengarah ke target POST controller ambilDataSiswa()
            type: 'POST',
            data: requestData,
            dataType: 'json', // Mendefinisikan format respon balik wajib berupa JSON objek
            success: function(response) {
                // Selalu perbarui hash token dari parameter kembalian controller agar tidak mismatch di request berikutnya
                if (response.csrf_hash) {
                    csrfHash = response.csrf_hash;
                }

                if (response.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: response.message || 'Data siswa terpilih berhasil dihapus.',
                        showConfirmButton: false,
                        timer: 1500
                    });
                    // Refresh data tabel secara asinkronus tanpa muat ulang halaman penuh
                    fetchData(); 
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: response.message || 'Gagal memproses penghapusan data siswa.'
                    });
                }
            },
            error: function(xhr, status, error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Kesalahan Sistem',
                    text: 'Terjadi kesalahan internal pada server saat mencoba menghapus data.'
                });
            }
        });
    }
</script>
<?= $this->endSection() ?>