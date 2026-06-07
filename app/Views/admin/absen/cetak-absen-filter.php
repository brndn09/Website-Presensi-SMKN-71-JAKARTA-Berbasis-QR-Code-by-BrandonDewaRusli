<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Laporan Presensi Filter</title>

    <link rel="icon" type="image/jpg" href="<?= base_url('assets/img/logosekolah.jpg'); ?>">

    <style>
        /* Pengaturan Dasar & Cetak Halaman */
        body { 
            font-family: Arial, sans-serif; 
            font-size: 11px; 
            color: #333; 
            line-height: 1.4; 
            padding: 10px; 
        }
        
        /* Pengaturan Kop Laporan Resmi (Modern Flexbox) */
        .header-container { 
            width: 100%; 
            border-bottom: 3px double #000000; 
            padding-bottom: 8px; 
            margin-bottom: 15px; 
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .logo-left { 
            width: 70px; 
            height: auto; 
        }
        .logo-right { 
            width: 65px; 
            height: auto; 
        }
        .header-text { 
            text-align: center; 
            flex-grow: 1;
            padding: 0 10px;
        }
        .header-text h1 { margin: 0; font-size: 11pt; font-weight: normal; letter-spacing: 0.5px; }
        .header-text h2 { margin: 0; font-size: 11pt; font-weight: bold; }
        .header-text h3 { margin: 3px 0; font-size: 13pt; font-weight: bold; }
        .header-text .bidang-keahlian { margin: 2px 0; font-size: 8.5pt; font-weight: bold; font-family: Arial, sans-serif; }
        .header-text p { margin: 2px 0 0 0; font-size: 8pt; }

        /* Meta Informasi Laporan */
        .meta-info { width: 100%; margin-bottom: 15px; font-weight: bold; font-size: 11px; }
        .meta-info td { padding: 3px 0; }
        
        /* Tabel Data Utama */
        table.data-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table.data-table th { background-color: #f2f2f2; text-transform: uppercase; font-size: 10px; padding: 8px; border: 1px solid #111; }
        table.data-table td { padding: 7px 8px; border: 1px solid #111; vertical-align: middle; }
        
        /* Utility Classes */
        .text-center { text-align: center; }
        .text-bold { font-weight: bold; }
        .text-small { font-size: 10px; }
        .badge-status { font-weight: bold; text-transform: uppercase; font-size: 10px; }
        .text-danger { color: #c00; font-weight: bold; }
        .text-warning { color: #f77f00; font-weight: bold; }
        .text-pkl { color: #6f42c1; font-weight: bold; }
        
        /* Bagian Footer & Tanda Tangan */
        .footer-section {
            margin-top: 30px;
            width: 100%;
            display: flex;
            justify-content: flex-end;
            page-break-inside: avoid; /* Mencegah ttd terbelah di halaman baru */
        }
        .signature-box {
            text-align: center;
            width: 220px;
        }

        /* CSS khusus untuk mode print */
        @media print {
            @page { size: A4 portrait; margin: 1.2cm; }
            body { padding: 0; }
            .no-print { display: none; }
            /* Memastikan baris tabel tidak terpotong aneh di tengah-tengah kalimat */
            table.data-table tr { page-break-inside: avoid; page-break-after: auto; }
        }
    </style>
</head>
<body>

    <div class="header-container">
        <img src="<?= base_url('assets/img/logodkijakarta.jpg') ?>" class="logo-left" alt="Logo DKI">
        <div class="header-text">
            <h1>PEMERINTAH PROVINSI DAERAH KHUSUS IBUKOTA JAKARTA</h1>
            <h2>DINAS PENDIDIKAN</h2>
            <h3>SEKOLAH MENENGAH KEJURUAN NEGERI 71 JAKARTA</h3>
            <div class="bidang-keahlian">
                BIDANG KEAHLIAN : 1. PENGEMBANGAN PERANGKAT LUNAK DAN GIM<br>
                2. SENI DAN INDUSTRI KREATIF
            </div>
            <p>Jl. Radjiman Widyodiningrat Pulo Jahe, Cakung, Jakarta Timur</p>
            <p style="font-size: 7.5pt;">E-mail: <span style="color: blue; text-decoration: underline;">smkntujuh1jakarta@gmail.com</span> Website: <span style="color: blue; text-decoration: underline;">http://smkn71jakarta.sch.id/</span> Kode Pos : 13930</p>
        </div>
        <img src="<?= base_url('assets/img/logosekolah.jpg') ?>" class="logo-right" alt="Logo Sekolah">
    </div>

    <div style="text-align: center; margin-bottom: 20px;">
        <h4 style="margin: 0; font-size: 12pt; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px;">Laporan Presensi Kehadiran Siswa</h4>
    </div>

    <table class="meta-info">
        <tr>
            <td style="width: 15%;">Tanggal Presensi</td>
            <td style="width: 2%;">:</td>
            <td><?= date('d F Y', strtotime($tanggal)); ?></td>
            <td style="width: 15%; text-align: right;">Kategori Filter</td>
            <td style="width: 2%; text-align: center;">:</td>
            <td style="width: 25%; text-align: left; color: #c00;"><?= htmlspecialchars($filter_title); ?></td>
        </tr>
        <tr>
            <td>Target Kelas</td>
            <td>:</td>
            <td colspan="4"><?= htmlspecialchars($nama_kelas); ?></td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 4%;">No.</th>
                <th style="width: 12%;">NIS</th>
                <th style="width: 26%;">Nama Lengkap Siswa</th>
                <th style="width: 10%;">Kelas</th>
                <th style="width: 11%;">Status</th>
                <th style="width: 9%;" class="text-center">Masuk</th>
                <th style="width: 9%;" class="text-center">Pulang</th>
                <th style="width: 19%;">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            date_default_timezone_set('Asia/Jakarta');
            $batasTerlambatMasuk = "06:30:00"; 
            $batasTerlambatPulang = "15:30:00"; 
            
            $batasTimestampMasuk = strtotime($batasTerlambatMasuk);
            $batasTimestampPulang = strtotime($batasTerlambatPulang);

            $no = 1;
            foreach ($data as $siswa): 
                $idKehadiran = intval($siswa['id_kehadiran'] ?? 5);
                $jamMasukSiswa = $siswa['jam_masuk'] ?? null;
                $jamKeluarSiswa = $siswa['jam_keluar'] ?? null;
                $catatanKeterangan = trim($siswa['keterangan'] ?? '');

                $arrayKeteranganHTML = [];

                // --- LOGIKA PENENTUAN TEXT STATUS ---
                switch ($idKehadiran) {
                    case 1: $statusTxt = 'Hadir'; break;
                    case 2: $statusTxt = 'Sakit'; break;
                    case 3: $statusTxt = 'Izin'; break;
                    case 4: $statusTxt = 'Alfa'; break;
                    case 6: $statusTxt = 'PKL'; break;
                    default: $statusTxt = 'Belum Presensi'; break;
                }

                // --- LOGIKA NORMALISASI DATA KETERANGAN ---
                if ($idKehadiran == 1) { 
                    // 1. Cek Terlambat Masuk
                    if (!empty($jamMasukSiswa)) {
                        $masukTimestamp = strtotime($jamMasukSiswa);
                        if ($masukTimestamp > $batasTimestampMasuk) {
                            $selisihMasuk = $masukTimestamp - $batasTimestampMasuk;
                            $jamTelatM = floor($selisihMasuk / 3600);
                            $menitTelatM = floor(($selisihMasuk % 3600) / 60);
                            
                            $teksM = '<span class="text-danger">Terlambat Masuk';
                            if ($jamTelatM > 0) $teksM .= " " . $jamTelatM . " jam";
                            $teksM .= " " . $menitTelatM . " menit</span>";
                            $arrayKeteranganHTML[] = $teksM;
                        }
                    }

                    // 2. Cek Terlambat Scan Pulang atau Belum Pulang
                    if (!empty($jamKeluarSiswa) && $jamKeluarSiswa !== '-' && $jamKeluarSiswa !== '--:--') {
                        $keluarTimestamp = strtotime($jamKeluarSiswa);
                        if ($keluarTimestamp > $batasTimestampPulang) {
                            $selisihPulang = $keluarTimestamp - $batasTimestampPulang;
                            $jamTelatP = floor($selisihPulang / 3600);
                            $menitTelatP = floor(($selisihPulang % 3600) / 60);
                            
                            $teksP = '<span class="text-danger">Terlambat Scan Pulang';
                            if ($jamTelatP > 0) $teksP .= " " . $jamTelatP . " jam";
                            $teksP .= " " . $menitTelatP . " menit</span>";
                            $arrayKeteranganHTML[] = $teksP;
                        }
                    } else {
                        if (!empty($jamMasukSiswa)) {
                            $arrayKeteranganHTML[] = '<span class="text-warning">Belum Presensi Pulang</span>';
                        }
                    }
                } elseif ($idKehadiran == 6) {
                    $arrayKeteranganHTML[] = '<span class="text-pkl">Melaksanakan Praktik Kerja Lapangan (PKL)</span>';
                }

                // Tambahkan catatan manual dari database jika ada
                if (!empty($catatanKeterangan)) {
                    $arrayKeteranganHTML[] = '<span style="color: #555; font-style: italic;">(' . htmlspecialchars($catatanKeterangan) . ')</span>';
                }

                $keteranganFinal = !empty($arrayKeteranganHTML) ? implode('<br>', $arrayKeteranganHTML) : '-';
            ?>
                <tr>
                    <td class="text-center"><?= $no++; ?></td>
                    <td><?= htmlspecialchars($siswa['nis']); ?></td>
                    <td class="text-bold"><?= ucwords(strtolower(trim($siswa['nama_siswa']))); ?></td>
                    <td class="text-center"><?= htmlspecialchars($siswa['nama_kelas'] ?? $nama_kelas); ?></td>
                    <td class="text-center"><span class="badge-status"><?= $statusTxt; ?></span></td>
                    <td class="text-center"><?= ($idKehadiran == 6) ? 'PKL' : ($siswa['jam_masuk'] ?? '--:--'); ?></td>
                    <td class="text-center"><?= ($idKehadiran == 6) ? 'PKL' : ((!empty($siswa['jam_keluar']) && $siswa['jam_keluar'] !== '-') ? $siswa['jam_keluar'] : '--:--'); ?></td>
                    <td class="text-small" style="line-height: 1.3;"><?= $keteranganFinal; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="footer-section">
        <div class="signature-box">
            <p>Jakarta, <?= date('d M Y'); ?></p>
            <p style="margin-bottom: 60px;">Guru Piket,</p>
            <p>____________________</p>
        </div>
    </div>

    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>