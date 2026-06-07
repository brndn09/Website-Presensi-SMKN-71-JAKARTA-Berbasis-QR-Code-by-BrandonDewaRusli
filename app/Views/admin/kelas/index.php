<?= $this->extend('templates/admin_page_layout') ?>

<?= $this->section('styles') ?>
<style>
    :root {
        --primary: #4361ee;
        --primary-gradient: linear-gradient(60deg, #4361ee, #3a86ff);
        --card-border: #edf2f7;
    }

    /* Penyesuaian Ruang Konten Utama agar tidak terlalu ke atas dan dempet */
    .content-wrapper { 
        padding: 3.5rem 1.5rem 2rem 1.5rem !important; 
        background-color: #f8f9fc; 
        min-height: 100vh;
    }
    
    .page-header { 
        margin-bottom: 2rem; 
    }
    
    .page-title { 
        font-weight: 700; 
        color: #2d3436; 
        font-size: 1.5rem; 
    }
    
    /* Card Modern Restoration */
    .custom-card {
        border: none !important;
        border-radius: 15px !important;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05) !important;
        background: #ffffff;
        margin-top: 25px !important;
        margin-bottom: 2rem;
        position: relative;
    }

    /* Efek Floating Header Elegan dengan Spacing Seimbang */
    .custom-card .card-header-modern {
        background: var(--primary-gradient) !important;
        box-shadow: 0 4px 20px 0 rgba(0, 0, 0, 0.14), 0 7px 10px -5px rgba(67, 97, 238, 0.4) !important;
        border-radius: 12px !important;
        margin: -25px 15px 0 15px !important;
        padding: 1.25rem 1.5rem !important;
        position: relative;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border: none !important;
    }

    .header-info h4 { 
        margin: 0 0 4px 0 !important; 
        font-weight: 700 !important; 
        color: #ffffff !important; 
        font-size: 1.15rem !important; 
    }
    
    .header-info p { 
        margin: 0 !important; 
        font-size: 0.85rem !important; 
        color: rgba(255, 255, 255, 0.85) !important; 
    }

    .btn-action-group { 
        display: flex; 
        gap: 10px; 
    }
    
    .btn-modern {
        padding: 8px 16px;
        border-radius: 8px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 0.85rem;
        text-transform: none !important;
        transition: all 0.2s ease-in-out;
    }

    .btn-add { 
        background-color: rgba(255, 255, 255, 0.2) !important; 
        color: #ffffff !important; 
        border: 1px solid rgba(255, 255, 255, 0.3) !important; 
    }
    
    .btn-add:hover { 
        background-color: rgba(255, 255, 255, 0.35) !important; 
        transform: translateY(-2px); 
        color: #ffffff !important;
    }
    
    .btn-refresh { 
        background-color: rgba(255, 255, 255, 0.15) !important; 
        color: #ffffff !important; 
        border: 1px solid rgba(255, 255, 255, 0.25) !important; 
    }
    
    .btn-refresh:hover { 
        background-color: rgba(255, 255, 255, 0.3) !important; 
        color: #ffffff !important;
    }

    /* Penyesuaian Ruang Dalam Konten Tabel */
    .card-body-content { 
        min-height: 200px; 
        padding: 25px 20px 20px 20px !important; 
    }

    /* Shimmer Loading Animation */
    .skeleton-loader {
        width: 100%; 
        height: 15px;
        background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
        background-size: 200% 100%;
        animation: shimmer 1.5s infinite;
        border-radius: 4px;
        margin-bottom: 10px;
    }

    @keyframes shimmer {
        0% { background-position: -200% 0; }
        100% { background-position: 200% 0; }
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="content-wrapper">
    <div class="container-fluid">
        
        <div class="row">
            <div class="col-12">
                <?= view('admin/_messages'); ?>
            </div>
        </div>
        
        <div class="row">
            <div class="col-12 col-xl-6">
                <div class="card custom-card">
                    <div class="card-header-modern">
                        <div class="header-info">
                            <h4><i class="fas fa-door-open mr-2"></i> Daftar Kelas</h4>
                            <p>Angkatan <?= $generalSettings->school_year ?? '2025/2026'; ?></p>
                        </div>
                        <div class="btn-action-group">
                            <button class="btn btn-modern btn-refresh" onclick="refreshData('kelas', '#dataKelas')">
                                <i class="material-icons" style="font-size: 18px;">refresh</i>
                            </button>
                            <a href="<?= base_url('admin/kelas/tambah'); ?>" class="btn btn-modern btn-add shadow-sm">
                                <i class="material-icons" style="font-size: 18px;">add</i> <span>BARU</span>
                            </a>
                        </div>
                    </div>
                    <div class="card-body-content" id="dataKelas">
                        <div class="p-4 text-center text-muted">
                            <div class="skeleton-loader"></div>
                            <div class="skeleton-loader w-75"></div>
                            <p>Memuat data kelas...</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-xl-6">
                <div class="card custom-card">
                    <div class="card-header-modern">
                        <div class="header-info">
                            <h4><i class="fas fa-graduation-cap mr-2"></i> Daftar Jurusan</h4>
                            <p>Tahun Akademik <?= $generalSettings->school_year ?? '2025/2026'; ?></p>
                        </div>
                        <div class="btn-action-group">
                            <button class="btn btn-modern btn-refresh" onclick="refreshData('jurusan', '#dataJurusan')">
                                <i class="material-icons" style="font-size: 18px;">refresh</i>
                            </button>
                            <a href="<?= base_url('admin/jurusan/tambah'); ?>" class="btn btn-modern btn-add shadow-sm">
                                <i class="material-icons" style="font-size: 18px;">add</i> <span>BARU</span>
                            </a>
                        </div>
                    </div>
                    <div class="card-body-content" id="dataJurusan">
                        <div class="p-4 text-center text-muted">
                            <div class="skeleton-loader"></div>
                            <div class="skeleton-loader w-75"></div>
                            <p>Memuat data jurusan...</p>
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
    // State CSRF Token Global Dinamis agar sinkron dengan seluruh fungsi hapus
    let csrfTokenName = '<?= csrf_token() ?>';
    let csrfHash = '<?= csrf_hash() ?>';

    document.addEventListener('DOMContentLoaded', function () {
        // Initial Fetch data otomatis saat halaman selesai di-render
        refreshData('kelas', '#dataKelas');
        refreshData('jurusan', '#dataJurusan');
    });

    /**
     * Fungsi utama interaksi pemanggil feedback visual spinner
     */
    function refreshData(type, targetElement) {
        const container = document.querySelector(targetElement);
        
        // Tampilkan loading state spinner
        container.innerHTML = `
            <div class="p-5 text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
                <p class="mt-2 text-muted small">Menyinkronkan data...</p>
            </div>
        `;

        // Jalankan fungsi asinkronus AJAX untuk mengambil data ke backend
        if (typeof fetchKelasJurusanData === "function") {
            fetchKelasJurusanData(type, targetElement);
        } else {
            console.error("Fungsi fetchKelasJurusanData tidak ditemukan!");
            container.innerHTML = `<div class="alert alert-danger m-2 small">Gagal memuat fungsi sinkronisasi data.</div>`;
        }
    }
</script>
<?= $this->endSection() ?>