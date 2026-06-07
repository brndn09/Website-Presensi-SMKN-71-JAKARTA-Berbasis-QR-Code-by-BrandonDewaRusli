<?= $this->extend('templates/admin_page_layout') ?>

<?= $this->section('styles') ?>
<style>
    /* Global Variables untuk konsistensi warna */
    :root {
        --primary: #4361ee;
        --success: #2ec4b6;
        --danger: #e63946;
        --info: #3a86ff;
        --warning: #ff9f43;
        --secondary: #94a3b8;
        --bg-body: #f8f9fc;
        --card-border: #edf2f7;
        /* Variabel warna kustom baru untuk status PKL pada grafik */
        --info-pkl: #6f42c1;
    }

    body {
        background-color: var(--bg-body);
        color: #2d3748;
    }

    /* Typography */
    .dashboard-title { font-weight: 800; letter-spacing: -0.5px; color: #1a202c; }
    .stat-label { color: #718096; font-size: 0.85rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; }
    .stat-value { font-weight: 700; color: #1a202c; font-size: 1.5rem; }

    /* Card Styling */
    .card-custom {
        border: 1px solid var(--card-border);
        border-radius: 16px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        background: #ffffff;
    }

    .card-custom:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.08);
    }

    /* Icon Box */
    .icon-box {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .bg-soft-p { background: rgba(67, 97, 238, 0.1); color: var(--primary); }
    .bg-soft-s { background: rgba(46, 196, 182, 0.1); color: var(--success); }
    .bg-soft-i { background: rgba(58, 134, 255, 0.1); color: var(--info); }
    .bg-soft-d { background: rgba(230, 57, 70, 0.1); color: var(--danger); }

    /* Chart Area */
    .chart-container { position: relative; height: 340px; width: 100%; }

    .custom-select-modern {
        border-radius: 10px;
        border: 1.5px solid var(--card-border);
        padding: 8px 12px;
        height: auto;
        font-weight: 600;
        color: #4a5568;
    }

    .badge-date {
        background: #ffffff;
        border: 1px solid var(--card-border);
        padding: 8px 16px;
        border-radius: 10px;
        font-weight: 600;
        font-size: 0.9rem;
    }

    .text-xxs { font-size: 0.65rem !important; letter-spacing: 0.05rem; }

    @media (max-width: 768px) {
        .stat-value { font-size: 1.25rem; }
        .dashboard-title { font-size: 1.5rem; }
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="content py-4">
    <div class="container-fluid">
        
        <div class="row align-items-center mb-4">
            <div class="col-md-6 col-12 mb-3 mb-md-0">
                <h1 class="dashboard-title h3 mb-1">Halaman Dashboard</h1>
                <p class="text-muted small mb-0">Monitor aktivitas dan statistik harian sekolah secara real-time.</p>
            </div>
            <div class="col-md-6 col-12 text-md-right">
                <span class="badge-date d-inline-flex align-items-center shadow-sm">
                    <i class="material-icons mr-2" style="font-size: 18px; color: var(--primary);">calendar_today</i>
                    <?= $dateNow; ?>
                </span>
            </div>
        </div>

        <div class="row">
            <div class="col-xl-3 col-sm-6 mb-4">
                <div class="card card-custom h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="icon-box bg-soft-p"><i class="material-icons">groups</i></div>
                            <span class="badge badge-pill badge-success" style="font-size: 10px; padding: 5px 10px;">AKTIF</span>
                        </div>
                        <p class="stat-label mb-1">Total Siswa</p>
                        <h2 class="stat-value mb-0"><?= number_format($totalSiswa); ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 mb-4">
                <div class="card card-custom h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="icon-box bg-soft-s"><i class="material-icons">person_4</i></div>
                        </div>
                        <p class="stat-label mb-1">Total Guru</p>
                        <h2 class="stat-value mb-0"><?= number_format($totalGuru); ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 mb-4">
                <div class="card card-custom h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="icon-box bg-soft-i"><i class="material-icons">meeting_room</i></div>
                        </div>
                        <p class="stat-label mb-1">Kelas / Jurusan</p>
                        <h2 class="stat-value mb-0"><?= count($kelas) . ' / ' . count($jurusan); ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 mb-4">
                <div class="card card-custom h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="icon-box bg-soft-d"><i class="material-icons">manage_accounts</i></div>
                        </div>
                        <p class="stat-label mb-1">Administrator</p>
                        <h2 class="stat-value mb-0"><?= count($petugas); ?></h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-xl-4 col-lg-5 col-12 mb-4">
                <div class="card card-custom border-0 h-100 shadow-sm">
                    <div class="card-header bg-white border-0 pt-4 px-4 d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="m-0 font-weight-bold text-dark">Ringkasan Kehadiran</h6>
                            <p class="text-xxs text-muted mb-0 uppercase">Data Hari Ini</p>
                        </div>
                        <div id="filterLoader" style="display: none;">
                            <div class="spinner-border spinner-border-sm text-primary"></div>
                        </div>
                    </div>
                    <div class="card-body px-4">
                        <select name="id_kelas" id="filterKelas" class="form-control custom-select-modern mb-4 shadow-sm">
                            <option value="">Semua Kelas (<?= $totalSiswa ?> siswa)</option>
                            <?php foreach ($kelas as $k): ?>
                                <option value="<?= $k['id_kelas'] ?>">Kelas <?= $k['kelas'] ?></option>
                            <?php endforeach; ?>
                        </select>
                        <div id="siswaStatsContainer">
                            <?= view('admin/_dashboard_siswa_stats', [
                                'hadir'          => $jumlahKehadiranSiswa['hadir'],
                                'sakit'          => $jumlahKehadiranSiswa['sakit'],
                                'izin'           => $jumlahKehadiranSiswa['izin'],
                                'alfa'           => $jumlahKehadiranSiswa['alfa'],
                                'pkl'            => $jumlahKehadiranSiswa['pkl'] ?? 0,
                                'belum_presensi' => $jumlahKehadiranSiswa['belum_absen'] ?? $jumlahKehadiranSiswa['belum_presensi'],
                                'totalSiswa'     => $totalSiswa
                            ]) ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-8 col-lg-7 col-12 mb-4">
                <div class="card card-custom border-0 h-100 shadow-sm">
                    <div class="card-header bg-white border-0 pt-4 px-4">
                        <h6 class="m-0 font-weight-bold text-dark">Grafik Kehadiran (7 Hari Terakhir)</h6>
                        <p class="text-xxs text-muted mb-0 uppercase">Analisis Partisipasi Harian</p>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="kehadiranSiswa"></canvas>
                        </div>
                    </div>
                    <div class="card-footer bg-white border-0 pb-4 px-4">
                        <a href="<?= base_url('admin/absen-siswa'); ?>" class="btn btn-outline-primary btn-sm btn-block py-2" style="border-radius: 10px; font-weight: 600;">
                            Lihat Detail Laporan <i class="material-icons align-middle ml-1" style="font-size: 16px;">arrow_forward</i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('assets/js/plugins/chartjs/chart.umd.min.js') ?>"></script>
<script>
    let kehadiranSiswaChart;
    const chartLabels = <?= json_encode($dateRange) ?>;

    // Konsistensi warna grafik dengan CSS variable
    const chartColors = {
        hadir: '#2ec4b6',
        sakit: '#ff9f43',
        izin: '#3a86ff',
        alfa: '#e63946',
        pkl: '#6f42c1', // Warna baru ungu untuk pkl pada grafik
        belum: '#94a3b8'
    };

    function initChart() {
        const ctx = document.getElementById('kehadiranSiswa').getContext('2d');

        kehadiranSiswaChart = new Chart(ctx, {
            type: 'bar', 
            data: {
                labels: chartLabels,
                datasets: [
                    {
                        label: 'Hadir',
                        data: <?= json_encode($grafikKehadiranSiswa['hadir']) ?>,
                        backgroundColor: chartColors.hadir,
                        borderRadius: 5
                    },
                    {
                        label: 'Sakit',
                        data: <?= json_encode($grafikKehadiranSiswa['sakit']) ?>,
                        backgroundColor: chartColors.sakit,
                        borderRadius: 5
                    },
                    {
                        label: 'Izin',
                        data: <?= json_encode($grafikKehadiranSiswa['izin']) ?>,
                        backgroundColor: chartColors.izin,
                        borderRadius: 5
                    },
                    {
                        label: 'Alfa',
                        data: <?= json_encode($grafikKehadiranSiswa['alfa']) ?>,
                        backgroundColor: chartColors.alfa,
                        borderRadius: 5
                    },
                    {
                        // SINKRONISASI DATASET KE-5 UNTUK TRAX DATA PKL (ID: 6)
                        label: 'Sedang PKL',
                        data: <?= json_encode($grafikKehadiranSiswa['pkl'] ?? array_fill(0, count($dateRange), 0)) ?>,
                        backgroundColor: chartColors.pkl,
                        borderRadius: 5
                    },
                    {
                        label: 'Belum Presensi',
                        data: <?= json_encode($grafikKehadiranSiswa['belum']) ?>,
                        backgroundColor: chartColors.belum,
                        borderRadius: 5
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { 
                            usePointStyle: true, 
                            padding: 20, 
                            font: { size: 12, weight: '600' } 
                        }
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        backgroundColor: 'rgba(26, 32, 44, 0.9)',
                        padding: 12,
                        cornerRadius: 8
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { borderDash: [8, 4], color: '#e2e8f0', drawBorder: false },
                        ticks: { stepSize: 1, font: { size: 11 } }
                    },
                    x: { grid: { display: false } }
                }
            }
        });
    }

    $(document).ready(function () {
        initChart();

        $('#filterKelas').on('change', function () {
            const idKelas = $(this).val();
            $('#filterLoader').fadeIn();

            $.ajax({
                url: '<?= base_url('admin/dashboard/filter-data') ?>',
                type: 'POST',
                data: (typeof setAjaxData === "function") ? setAjaxData({ id_kelas: idKelas }) : { id_kelas: idKelas }, 
                success: function (response) {
                    const obj = JSON.parse(response);
                    if (obj.result == 1) {
                        // 1. Update Statistik Angka via Partial View
                        $('#siswaStatsContainer').html(obj.htmlContent);
                        
                        // 2. Update Data Grafik secara dinamis real-time
                        kehadiranSiswaChart.data.datasets[0].data = obj.chartData.hadir;
                        kehadiranSiswaChart.data.datasets[1].data = obj.chartData.sakit;
                        kehadiranSiswaChart.data.datasets[2].data = obj.chartData.izin;
                        kehadiranSiswaChart.data.datasets[3].data = obj.chartData.alfa;
                        kehadiranSiswaChart.data.datasets[4].data = obj.chartData.pkl; // Inject data pkl baru ke chart
                        kehadiranSiswaChart.data.datasets[5].data = obj.chartData.belum || obj.chartData.belum_presensi;
                        
                        kehadiranSiswaChart.update();
                    }
                },
                error: function() {
                    alert("Gagal memuat data filter.");
                },
                complete: function () {
                    $('#filterLoader').fadeOut();
                }
            });
        });
    });
</script>
<?= $this->endSection() ?>