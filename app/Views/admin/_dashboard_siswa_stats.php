<?php
/**
 * View Partial: _dashboard_siswa_stats.php
 * Digunakan untuk menampilkan ringkasan angka statistik di Dashboard
 * baik saat load pertama kali maupun saat difilter via AJAX.
 */

// Sinkronisasi variabel penampung dari data kiriman controller / AJAX filter
$belum_presensi = $belum_presensi ?? $belum_absen ?? 0;
$pkl = $pkl ?? 0;
?>

<div class="row g-3 text-center align-items-center">
    <div class="col-6 mb-2">
        <div class="p-3 rounded-lg shadow-sm border h-100 d-flex flex-column justify-content-center" 
             style="background: #f0fff4; border-color: #c6f6d5 !important;">
            <h6 class="text-success text-xxs font-weight-bolder mb-1 uppercase">HADIR</h6>
            <h3 class="mb-0 font-weight-bold text-dark"><?= $hadir; ?></h3>
        </div>
    </div>
    <div class="col-6 mb-2">
        <div class="p-3 rounded-lg shadow-sm border h-100 d-flex flex-column justify-content-center" 
             style="background: #f8fafc; border-color: #e2e8f0 !important;">
            <h6 class="text-muted text-xxs font-weight-bolder mb-1 uppercase">BELUM PRESENSI</h6>
            <h3 class="mb-0 font-weight-bold text-dark"><?= $belum_presensi; ?></h3>
        </div>
    </div>
    
    <div class="col-3">
        <div class="py-2 border rounded shadow-none bg-white" style="border-style: dashed !important; border-color: #ff9f43 !important;">
            <p class="text-warning text-xxs font-weight-bold mb-0 uppercase">SAKIT</p>
            <h5 class="mb-0 font-weight-bold text-dark"><?= $sakit; ?></h5>
        </div>
    </div>
    <div class="col-3">
        <div class="py-2 border rounded shadow-none bg-white" style="border-style: dashed !important; border-color: #3a86ff !important;">
            <p class="text-info text-xxs font-weight-bold mb-0 uppercase">IZIN</p>
            <h5 class="mb-0 font-weight-bold text-dark"><?= $izin; ?></h5>
        </div>
    </div>
    <div class="col-3">
        <div class="py-2 border rounded shadow-none bg-white" style="border-style: dashed !important; border-color: #e63946 !important;">
            <p class="text-danger text-xxs font-weight-bold mb-0 uppercase">ALFA</p>
            <h5 class="mb-0 font-weight-bold text-dark"><?= $alfa; ?></h5>
        </div>
    </div>
    <div class="col-3">
        <div class="py-2 border rounded shadow-none bg-white" style="border-style: dashed !important; border-color: #6f42c1 !important;">
            <p class="style-title-pkl text-xxs font-weight-bold mb-0 uppercase">PKL</p>
            <h5 class="mb-0 font-weight-bold text-dark"><?= $pkl; ?></h5>
        </div>
    </div>
</div>

<div class="mt-4 pt-3 border-top">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <p class="text-primary text-xxs font-weight-bolder mb-0 uppercase">Total Siswa</p>
            <small class="text-muted text-xxs"></small>
        </div>
        <div class="text-right">
            <h5 class="mb-0 font-weight-bold text-dark"><?= $totalSiswa; ?> Siswa</h5>
        </div>
    </div>
</div>

<style>
    /* Menangani Tipografi Kecil untuk Label */
    .text-xxs { 
        font-size: 0.65rem !important; 
        line-height: 1.2;
        letter-spacing: 0.05rem; 
        white-space: nowrap;
    }
    .uppercase { text-transform: uppercase; }
    
    /* Warna teks kustom ungu tua untuk judul indikator PKL */
    .style-title-pkl { color: #6f42c1 !important; }
    
    /* Responsive adjustment: mengecilkan angka jika layar sangat sempit */
    @media (max-width: 350px) {
        h3 { font-size: 1.15rem !important; }
        h5 { font-size: 0.8rem !important; }
        .p-3 { padding: 0.75rem !important; }
    }

    /* Menambahkan radius pada border dashed agar terlihat modern */
    .rounded-lg {
        border-radius: 12px !important;
    }
</style>