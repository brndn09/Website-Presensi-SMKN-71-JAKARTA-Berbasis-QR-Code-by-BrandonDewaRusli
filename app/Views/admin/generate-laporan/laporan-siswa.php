<?= $this->extend('templates/laporan') ?>

<?= $this->section('content') ?>
<style>
    /* Pengaturan Media Cetak */
    @media print {
        @page { 
            margin: 0.5cm; 
            size: landscape; 
        }
        body { margin: 1cm; }
        .no-print { display: none; }
    }

    /* Pengaturan Kop Laporan Resmi (Dua Logo) */
    .header-container { 
        width: 100%; 
        border-bottom: 3px double black; /* Garis ganda khas kop surat resmi */
        padding-bottom: 8px; 
        margin-bottom: 15px; 
        position: relative;
    }
    .logo-left { 
        width: 75px; 
        height: auto; 
        float: left; 
        margin-top: 5px;
    }
    .logo-right { 
        width: 70px; 
        height: auto; 
        float: right; 
        margin-top: 5px;
    }
    .header-text { 
        text-align: center; 
        margin-left: 85px;  /* Mengimbangi float kiri */
        margin-right: 85px; /* Mengimbangi float kanan */
    }
    .header-text h1 { margin: 0; font-size: 11pt; font-weight: normal; letter-spacing: 0.5px; }
    .header-text h2 { margin: 0; font-size: 11pt; font-weight: bold; }
    .header-text h3 { margin: 3px 0; font-size: 14pt; font-weight: bold; }
    .header-text .bidang-keahlian { margin: 2px 0; font-size: 8.5pt; font-weight: bold; font-family: Arial, sans-serif; line-height: 1.3; }
    .header-text p { margin: 2px 0 0 0; font-size: 8.5pt; }

    /* Container Informasi Kiri & Kanan */
    .info-container {
        width: 100%;
        margin-bottom: 15px;
        font-family: Arial, sans-serif;
        font-size: 12px;
        border-collapse: collapse;
    }
    .info-container td {
        padding: 2px 0;
        vertical-align: top;
    }

    /* Tabel Utama (Grid Absensi) */
    .main-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 9pt; 
        table-layout: fixed;
    }
    .main-table th, .main-table td {
        border: 1px solid black;
        padding: 4px 1px;
        text-align: center;
        overflow: hidden;
    }
    .main-table th {
        background-color: #f0f0f0 !important;
        font-weight: bold;
        -webkit-print-color-adjust: exact;
    }
    
    /* Pengaturan Lebar Kolom */
    .col-no { width: 28px; }
    .col-nama { 
        width: 190px; 
        text-align: left !important; 
        padding-left: 5px !important; 
        white-space: nowrap;
    }
    .col-tgl { width: 22px; }
    .col-rekap { width: 25px; font-weight: bold; }

    /* Indikator Warna Kehadiran */
    .bg-hadir { background-color: #c6efce !important; color: #006100; -webkit-print-color-adjust: exact; } 
    .bg-sakit-izin { background-color: #ffeb9c !important; color: #9c6500; -webkit-print-color-adjust: exact; }
    .bg-alpa { background-color: #ffcccb !important; color: #ff0000; -webkit-print-color-adjust: exact; }
    
    /* Style Tambahan Kustom Untuk Penanda Siswa Sedang PKL */
    .bg-pkl { background-color: #e2d9f3 !important; color: #6f42c1; font-weight: bold; -webkit-print-color-adjust: exact; }
    
    .rekap-footer {
        margin-top: 15px;
        font-size: 11px;
    }
</style>

<div class="header-container">
    <img src="<?= base_url('assets/img/logodkijakarta.jpg') ?>" class="logo-left" alt="Logo DKI">
    <img src="<?= base_url('assets/img/logosekolah.jpg') ?>" class="logo-right" alt="Logo Sekolah">
    <div class="header-text">
        <h1>PEMERINTAH PROVINSI DAERAH KHUSUS IBUKOTA JAKARTA</h1>
        <h2>DINAS PENDIDIKAN</h2>
        <h3>SEKOLAH MENENGAH KEJURUAN NEGERI 71 JAKARTA</h3>
        <div class="bidang-keahlian">
            BIDANG KEAHLIAN : 1. PENGEMBANGAN PERANGKAT LUNAK DAN GIM<br>
            2. SENI DAN INDUSTRI KREATIF
        </div>
        <p>Jl. Radjiman Widyodiningrat Pulo Jahe, Cakung, Jakarta Timur</p>
        <p style="font-size: 8pt;">E-mail: <span style="color: blue; text-decoration: underline;">smkntujuh1jakarta@gmail.com</span> Website: <span style="color: blue; text-decoration: underline;">http://smkn71jakarta.sch.id/</span> Kode Pos : 13930</p>
    </div>
    <div style="clear: both;"></div>
</div>

<div style="text-align: center; margin-bottom: 10px;">
    <h4 style="margin: 0; font-size: 11pt; font-weight: bold; text-transform: uppercase;">LAPORAN PRESENSI SISWA</h4>
    <span style="font-size: 9pt; font-weight: bold;">TAHUN PELAJARAN 2025/2026</span>
</div>

<table class="info-container">
    <tr>
        <td width="50%" align="left">
            <strong>Kelas:</strong> <?= $kelas['kelas'] ?? '-'; ?><br>
            <strong>Tanggal Cetak:</strong> <?= date('d/m/Y'); ?>
        </td>
        <td width="50%" align="right" style="vertical-align: bottom;">
            <strong style="font-size: 14px;">Bulan: <?= strtoupper($bulan ?? ''); ?> <?= $tahun ?? ''; ?></strong>
        </td>
    </tr>
</table>

<table class="main-table">
    <thead>
        <tr>
            <th rowspan="3" class="col-no">No</th>
            <th rowspan="3" class="col-nama">Nama Siswa</th>
            <th colspan="<?= count($tanggal); ?>">Hari / Tanggal</th>
            <th colspan="4">Total</th>
        </tr>
        <tr>
            <?php foreach ($tanggal as $tgl): ?>
                <th class="col-tgl"><?= $tgl->toLocalizedString('E'); ?></th>
            <?php endforeach; ?>
            <th rowspan="2" class="col-rekap bg-hadir">H</th>
            <th rowspan="2" class="col-rekap bg-sakit-izin">S</th>
            <th rowspan="2" class="col-rekap bg-sakit-izin">I</th>
            <th rowspan="2" class="col-rekap bg-alpa">A</th>
        </tr>
        <tr>
            <?php foreach ($tanggal as $tgl): ?>
                <th class="col-tgl"><?= $tgl->format('d'); ?></th>
            <?php endforeach; ?>
        </tr>
    </thead>
    <tbody>
        <?php $idx = 0; foreach ($listSiswa as $siswa): ?>
            <?php
            // Logika Hitung Rekap Per Siswa (Murni mengabaikan status PKL atau status diluar H,S,I,A)
            $h = count(array_filter($listAbsen, function ($a) use ($idx) {
                return (!$a['lewat'] && isset($a[$idx]['id_kehadiran']) && $a[$idx]['id_kehadiran'] == 1);
            }));
            $s = count(array_filter($listAbsen, function ($a) use ($idx) {
                return (!$a['lewat'] && isset($a[$idx]['id_kehadiran']) && $a[$idx]['id_kehadiran'] == 2);
            }));
            $i = count(array_filter($listAbsen, function ($a) use ($idx) {
                return (!$a['lewat'] && isset($a[$idx]['id_kehadiran']) && $a[$idx]['id_kehadiran'] == 3);
            }));
            $a = count(array_filter($listAbsen, function ($a) use ($idx) {
                if ($a['lewat']) return false;
                $st = $a[$idx]['id_kehadiran'] ?? null;
                return (is_null($st) || $st == 4);
            }));
            ?>
            <tr>
                <td><?= $idx + 1; ?></td>
                <td class="col-nama"><?= strtoupper($siswa['nama_siswa']); ?></td>
                
                <?php foreach ($listAbsen as $absen): ?>
                    <?php 
                        $statusId = $absen[$idx]['id_kehadiran'] ?? ($absen['lewat'] ? 5 : 4);
                        echo renderAbsensiCell($statusId);
                    ?>
                <?php endforeach; ?>

                <td class="bg-hadir"><?= $h ?: '-'; ?></td>
                <td class="bg-sakit-izin"><?= $s ?: '-'; ?></td>
                <td class="bg-sakit-izin"><?= $i ?: '-'; ?></td>
                <td class="bg-alpa"><?= $a ?: '-'; ?></td>
            </tr>
        <?php $idx++; endforeach; ?>
    </tbody>
</table>

<table width="100%" style="margin-top: 15px; border-collapse: collapse;">
    <tr>
        <td width="50%" style="vertical-align: top;">
            <div class="rekap-footer">
                <table style="border-collapse: collapse; width: 220px; border: 1px solid #ccc;">
                    <tr>
                        <td style="padding: 4px; border-bottom: 1px solid #ccc;" width="120">Total Siswa</td>
                        <td style="padding: 4px; border-bottom: 1px solid #ccc;">: <strong><?= count($listSiswa); ?></strong></td>
                    </tr>
                    <tr>
                        <td style="padding: 4px;">Laki-laki</td>
                        <td style="padding: 4px;">: <?= $rekapSiswa['laki'] ?? 0; ?></td>
                    </tr>
                    <tr>
                        <td style="padding: 4px;">Perempuan</td>
                        <td style="padding: 4px;">: <?= $rekapSiswa['perempuan'] ?? 0; ?></td>
                    </tr>
                </table>
            </div>
        </td>
        <td width="50%" align="right" style="vertical-align: top; font-family: Arial, sans-serif; font-size: 10pt;">
            <table width="200px" style="text-align: center;">
                <tr>
                    <td>
                        Jakarta, <?= date('d/m/Y') ?><br>
                        Wali Kelas,<br><br><br><br><br>
                        ( ____________________ )
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>

<?php
/**
 * Helper untuk merender sel berdasarkan status ID
 */
function renderAbsensiCell($id) {
    switch ($id) {
        case 1: return "<td class='bg-hadir'>H</td>";
        case 2: return "<td class='bg-sakit-izin'>S</td>";
        case 3: return "<td class='bg-sakit-izin'>I</td>";
        case 4: return "<td class='bg-alpa'>A</td>";
        case 6: return "<td class='bg-pkl'>PKL</td>"; // Menampilkan teks PKL berwarna khusus ungu di kalender laporan
        default: return "<td></td>";
    }
}
?>
<?= $this->endSection() ?>