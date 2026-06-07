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
        --secondary: #94a3b8; /* Warna Belum Presensi */
        --bg-body: #f8f9fc;
        --card-border: #edf2f7;
        /* Variabel warna kustom baru untuk status PKL pada grafik */
        --info-pkl: #6f42c1;
    }

    body {
        background-color: var(--bg-body);
        color: #2d3748;
    }

    /* Typography & Titles */
    .dashboard-title { font-weight: 800; letter-spacing: -0.5px; color: #1a202c; }
    .stat-label { color: #718096; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px; }
    .stat-value { font-weight: 700; color: #1a202c; font-size: 1.5rem; }

    /* Card Styling */
    .card-custom {
        border-radius: 16px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        background: #ffffff;
        border: 1px solid var(--card-border);
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
        margin-right: 15px;
    }

    .bg-soft-primary { background: rgba(67, 97, 238, 0.1); color: var(--primary); }
    .bg-soft-danger { background: rgba(230, 57, 70, 0.1); color: var(--danger); }

    /* Stat Box Minor (Responsive Friendly) */
    .stat-box-mini {
        padding: 12px 8px;
        border-radius: 12px;
        border: 1px dashed #e2e8f0;
        height: 100%;
    }

    .chart-container { position: relative; height: 320px; width: 100%; }

    .badge-date {
        background: #ffffff;
        border: 1px solid var(--card-border);
        color: #4a5568;
        padding: 8px 16px;
        border-radius: 10px;
        font-weight: 600;
        font-size: 0.85rem;
    }

    .divider-vertical {
        border-right: 2px solid #edf2f7;
        height: 40px;
        margin: 0 15px;
    }

    .btn-soft-primary { background-color: rgba(67, 97, 238, 0.1); color: #4361ee; transition: 0.2s; font-weight: 600; }
    .btn-soft-primary:hover { background-color: #4361ee; color: white; }

    /* Fix Zoom & Responsiveness */
    @media (max-width: 991px) {
        .divider-vertical { display: none; }
        .stat-value { font-size: 1.25rem; }
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="content py-4">
    <div class="container-fluid">
        
        <?php if (isset($no_class)): ?>
            <div class="row justify-content-center mt-5">
                <div class="col-md-7 text-center">
                    <div class="card card-custom p-5 border-0">
                        <div class="icon-box bg-soft-danger mx-auto mb-4" style="width: 80px; height: 80px;">
                            <i class="material-icons" style="font-size: 40px; color: var(--danger);">error_outline</i>
                        </div>
                        <h2 class="font-weight-bold">Akses Terbatas</h2>
                        <p class="text-muted">Anda belum ditugaskan sebagai <b>Wali Kelas</b>.<br>Silahkan hubungi admin untuk pembaruan data.</p>
                    </div>
                </div>
            </div>
        <?php else: ?>

            <div class="row align-items-center mb-4">
                <div class="col-md-6 col-12 mb-3 mb-md-0">
                    <h1 class="dashboard-title h3 mb-1">Dashboard Wali Kelas</h1>
                    <p class="text-muted small mb-0">Pantau kehadiran siswa kelas secara real-time.</p>
                </div>
                <div class="col-md-6 col-12 text-md-right">
                    <span class="badge-date d-inline-flex align-items-center shadow-sm">
                        <i class="material-icons mr-2" style="font-size: 18px; color: var(--primary);">calendar_today</i>
                        <?= date('d F Y'); ?>
                    </span>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-3 col-lg-4 col-12 mb-4">
                    <div class="card card-custom h-100">
                        <div class="card-body d-flex align-items-center">
                            <div class="icon-box bg-soft-primary shadow-sm">
                                <i class="material-icons">school</i>
                            </div>
                            <div>
                                <p class="stat-label mb-0">Kelas Anda</p>
                                <h3 class="stat-value mb-0">
                                    <?= $kelas['tingkat'] . ' ' . $kelas['jurusan'] . ' ' . $kelas['index_kelas']; ?>
                                </h3>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-9 col-lg-8 col-12 mb-4">
                    <div class="card card-custom h-100">
                        <div class="card-body">
                            <p class="stat-label mb-3">Kehadiran Hari Ini</p>
                            <div class="row g-2 text-center">
                                <div class="col-md col-4">
                                    <h6 class="text-success text-xxs font-weight-bold mb-1">HADIR</h6>
                                    <p class="h4 mb-0 font-weight-bold text-dark"><?= $summary['hadir_hari_ini']; ?></p>
                                </div>
                                <div class="col-md col-4">
                                    <h6 class="text-warning text-xxs font-weight-bold mb-1">SAKIT</h6>
                                    <p class="h4 mb-0 font-weight-bold text-dark"><?= $summary['sakit_hari_ini']; ?></p>
                                </div>
                                <div class="col-md col-4">
                                    <h6 class="text-info text-xxs font-weight-bold mb-1">IZIN</h6>
                                    <p class="h4 mb-0 font-weight-bold text-dark"><?= $summary['izin_hari_ini']; ?></p>
                                </div>
                                <div class="col-md col-4">
                                    <h6 class="text-danger text-xxs font-weight-bold mb-1">ALFA</h6>
                                    <p class="h4 mb-0 font-weight-bold text-dark"><?= $summary['alfa_hari_ini']; ?></p>
                                </div>
                                <div class="col-md col-4">
                                    <h6 class="text-xxs font-weight-bold mb-1" style="color: #6f42c1;">SEDANG PKL</h6>
                                    <p class="h4 mb-0 font-weight-bold text-dark"><?= $summary['pkl_hari_ini'] ?? 0; ?></p>
                                </div>
                                <div class="col-md col-4">
                                    <h6 class="text-secondary text-xxs font-weight-bold mb-1">BELUM PRESENSI</h6>
                                    <p class="h4 mb-0 font-weight-bold text-dark"><?= $summary['belum_absen_hari_ini']; ?></p>
                                </div>
                                <div class="col-md col-4 border-left d-none d-md-block">
                                    <h6 class="text-primary text-xxs font-weight-bold mb-1">TOTAL</h6>
                                    <p class="h4 mb-0 font-weight-bold text-dark"><?= $summary['total_siswa']; ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card card-custom">
                        <div class="card-header bg-white border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="m-0 font-weight-bold text-dark">Grafik Kehadiran</h6>
                                <p class="text-muted text-xxs mb-0">Tren 7 Hari Terakhir</p>
                            </div>
                            <a href="<?= base_url('teacher/laporan'); ?>" class="btn btn-sm btn-soft-primary px-3" style="border-radius: 8px;">
                                <i class="material-icons align-middle mr-1" style="font-size: 16px;">print</i> Laporan
                            </a>
                        </div>
                        <div class="card-body">
                            <div class="chart-container">
                                <canvas id="kehadiranSiswaKelas"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        <?php endif; ?>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<?php if (!isset($no_class)): ?>
    <script src="<?= base_url('assets/js/plugins/chartjs/chart.umd.min.js') ?>"></script>
    <script>
        $(document).ready(function () {
            const ctx = document.getElementById('kehadiranSiswaKelas');
            if (ctx) {
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: <?= json_encode($dateRange) ?>,
                        datasets: [
                            { label: 'Hadir', data: <?= json_encode($grafikKehadiran['hadir']) ?>, backgroundColor: '#2ec4b6', borderRadius: 4 },
                            { label: 'Sakit', data: <?= json_encode($grafikKehadiran['sakit']) ?>, backgroundColor: '#ff9f43', borderRadius: 4 },
                            { label: 'Izin', data: <?= json_encode($grafikKehadiran['izin']) ?>, backgroundColor: '#3a86ff', borderRadius: 4 },
                            { label: 'Alfa', data: <?= json_encode($grafikKehadiran['alfa']) ?>, backgroundColor: '#e63946', borderRadius: 4 },
                            // SINKRONISASI DATASET KE-5: GRAFIK TRACK DATA PKL
                            { label: 'Sedang PKL', data: <?= json_encode($grafikKehadiran['pkl'] ?? array_fill(0, count($dateRange), 0)) ?>, backgroundColor: '#6f42c1', borderRadius: 4 },
                            { label: 'Belum Presensi', data: <?= json_encode($grafikKehadiran['belum']) ?>, backgroundColor: '#94a3b8', borderRadius: 4 }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { position: 'bottom', labels: { usePointStyle: true, padding: 20, font: { size: 11, weight: '600' } } }
                        },
                        scales: {
                            y: { beginAtZero: true, ticks: { stepSize: 1 }, grid: { color: 'rgba(0, 0, 0, 0.03)' } },
                            x: { grid: { display: false } }
                        }
                    }
                });
            }
        });
    </script>
<?php endif; ?>
<?= $this->endSection() ?>