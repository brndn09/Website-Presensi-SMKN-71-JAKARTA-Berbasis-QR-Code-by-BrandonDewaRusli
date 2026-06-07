<?= $this->extend('templates/laporan') ?>

<?= $this->section('content') ?>
<style>
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
    .header-text .bidang-keahlian { margin: 2px 0; font-size: 8.5pt; font-weight: bold; font-family: Arial, sans-serif; }
    .header-text p { margin: 2px 0 0 0; font-size: 8.5pt; }
    
    /* Menggunakan table-layout fixed agar kolom tanggal seragam lebarnya */
    .main-table { 
        width: 100%; 
        border-collapse: collapse; 
        font-size: 6.5pt; 
        table-layout: fixed; 
    }
    .main-table th, .main-table td { 
        border: 1px solid black; 
        padding: 2px 1px; 
        text-align: center; 
        vertical-align: middle;
        word-wrap: break-word;
    }
    .main-table th { 
        background-color: #f2f2f2; 
        font-weight: bold;
    }
    
    /* Mengizinkan nama siswa turun ke bawah jika terlalu panjang agar tidak terpotong */
    .text-left { 
        text-align: left !important; 
        padding-left: 4px; 
        white-space: normal; 
        word-wrap: break-word;
        line-height: 1.2;
    }
    
    /* Pewarnaan Sel */
    .bg-hadir { background-color: #c6efce; color: #006100; }
    .bg-telat { background-color: #ffeb9c; color: #9c6500; }
    
    .info-header { font-size: 9pt; font-weight: bold; margin-bottom: 5px; margin-top: 10px; }
    .info-header td { border: none; }
    
    .small-text { 
        font-size: 5pt; 
        display: block; 
        line-height: 1.1; 
        margin-top: 1px;
        font-weight: normal !important;
    }
    .total-col { font-weight: bold; background-color: #fafafa; }
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
    <h4 style="margin: 0; font-size: 11pt; font-weight: bold;">DAFTAR HADIR SISWA (REKAP KEDISIPLINAN)</h4>
    <span style="font-size: 9pt; font-weight: bold;">TAHUN PELAJARAN 2025/2026</span>
</div>

<table width="100%" class="info-header">
    <tr>
        <td width="50%">KELAS: <?= strtoupper($kelas['kelas'] ?? '-'); ?></td>
        <td width="50%" align="right">BULAN: <?= strtoupper(($bulan ?? '') . ' ' . ($tahun ?? '')); ?></td>
    </tr>
    <tr>
        <td>BATAS JAM WAKTU: <?= $batas ?? '06:31'; ?> WIB</td>
        <td align="right">TGL CETAK: <?= date('d/m/Y'); ?></td>
    </tr>
</table>

<table class="main-table">
    <thead>
        <tr>
            <th rowspan="2" width="25px">No</th>
            <th rowspan="2" width="160px">Nama Siswa</th>
            <th colspan="<?= count($listTanggal); ?>">Hari / Tanggal</th>
            <th colspan="2" width="45px">Total</th>
        </tr>
        <tr>
            <?php foreach ($listTanggal as $tgl): ?>
                <th>
                    <span class="small-text" style="font-weight: bold;"><?= $tgl['day']; ?></span>
                    <?= $tgl['num']; ?>
                </th>
            <?php endforeach; ?>
            <th class="total-col" width="22px">H</th>
            <th class="total-col" width="22px">T</th>
        </tr>
    </thead>
    <tbody>
        <?php $no = 1; foreach ($rekap as $s): ?>
            <tr>
                <td><?= $no++; ?></td>
                <td class="text-left"><?= strtoupper($s['nama']); ?></td>
                
                <?php foreach ($listTanggal as $tgl): 
                    $info = $s['detail'][$tgl['date']];
                    $class = '';
                    
                    if ($info['status'] == 'H') $class = 'bg-hadir';
                    if ($info['status'] == 'T') $class = 'bg-telat';
                ?>
                    <td class="<?= $class; ?>">
                        <span style="font-weight: bold;"><?= (in_array($info['status'], ['H', 'T'])) ? $info['status'] : ''; ?></span>
                        
                        <?php if (in_array($info['status'], ['H', 'T'])): ?>
                            <span class="small-text">M: <?= $info['masuk']; ?></span>
                            <span class="small-text">P: <?= $info['pulang']; ?></span>
                            
                            <?php if ($info['status'] == 'T' && !empty($info['late'])): ?>
                                <span class="small-text" style="color: #9c0006; font-weight: bold !important;">L: <?= $info['late']; ?></span>
                            <?php endif; ?>
                        <?php endif; ?>
                    </td>
                <?php endforeach; ?>

                <td class="bg-hadir total-col"><?= ($s['total']['tepat'] ?? 0) ?: '-'; ?></td>
                <td class="bg-telat total-col"><?= ($s['total']['terlambat'] ?? 0) ?: '-'; ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<div style="margin-top: 15px; font-size: 7.5pt;">
    <strong>Keterangan:</strong><br>
    H: Hadir Tepat | T: Terlambat | M: Jam Masuk | P: Jam Pulang | L: Lama Terlambat (Durasi)
</div>

<table width="100%" style="margin-top: 20px; font-size: 9pt;">
    <tr>
        <td width="70%"></td>
        <td width="30%" align="center">
            Jakarta, <?= date('d/m/Y') ?><br>
            Wali Kelas,<br><br><br><br><br>
            ( ____________________ )
        </td>
    </tr>
</table>
<?= $this->endSection() ?>