<?php
/**
 * View: list_absen_siswa.php (Teacher/Wali Kelas)
 * Menampilkan tabel absensi siswa dengan Ringkasan Statistik Lengkap dan Filter Interaktif.
 */

// Inisialisasi Counter Statistik Murni Data Awal
$countHadir = 0;
$countSakit = 0;
$countIzin  = 0;
$countAlfa  = 0;
$countBelum = 0;
$countTerlambat = 0;
$countTerlambatPulang = 0;
$countBelumPulang = 0;
$countPkl = 0;

$batas_terlambat = "06:30:00";
$batas_terlambat_pulang = "15:30:00";

if (!empty($data)) {
    foreach ($data as $row) {
        $status_id = (int)($row['id_kehadiran'] ?? 5);
        $jam_masuk_check = ($row['jam_masuk'] == '00:00:00') ? null : ($row['jam_masuk'] ?? null);
        $jam_pulang_check = ($row['jam_keluar'] == '00:00:00' || $row['jam_keluar'] == '-') ? null : ($row['jam_keluar'] ?? null);

        switch ($status_id) {
            case 1: 
                $countHadir++; 
                // 1. Validasi Terlambat Masuk
                if ($jam_masuk_check && strtotime($jam_masuk_check) > strtotime($batas_terlambat)) {
                    $countTerlambat++;
                }
                // 2. Validasi Terlambat Scan Pulang
                if ($jam_pulang_check && strtotime($jam_pulang_check) > strtotime($batas_terlambat_pulang)) {
                    $countTerlambatPulang++;
                }
                // 3. Validasi Belum Scan Pulang (Sudah masuk, tapi jam pulang kosong)
                if ($jam_masuk_check && empty($jam_pulang_check)) {
                    $countBelumPulang++;
                }
                break;
            case 2: $countSakit++; break;
            case 3: $countIzin++; break;
            case 4: $countAlfa++; break;
            case 6: $countPkl++; break; // Menghitung siswa yang sedang PKL
            default: 
                if ($status_id != 6) {
                    $countBelum++; 
                }
                break;
        }
    }
}
$totalSiswa = count($data);
?>

<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm" style="border-radius: 15px; background: #f8f9fa;">
            <div class="card-body p-3">
                
                <div class="d-flex flex-wrap align-items-stretch justify-content-between text-center" style="gap: 12px;">
                    
                    <div class="bg-white p-2 flex-fill rounded shadow-sm border-left" style="border-left: 4px solid #2ec4b6 !important; min-width: 100px;">
                        <small class="text-muted d-block uppercase font-weight-bold" style="font-size: 10px;">HADIR</small>
                        <h4 class="mb-0 font-weight-bold text-success" id="substat-hadir"><?= $countHadir; ?></h4>
                    </div>

                    <div class="bg-white p-2 flex-fill rounded shadow-sm border-left" style="border-left: 4px solid #ff9f43 !important; min-width: 100px;">
                        <small class="text-muted d-block uppercase font-weight-bold" style="font-size: 10px;">SAKIT</small>
                        <h4 class="mb-0 font-weight-bold text-warning" id="substat-sakit"><?= $countSakit; ?></h4>
                    </div>

                    <div class="bg-white p-2 flex-fill rounded shadow-sm border-left" style="border-left: 4px solid #3a86ff !important; min-width: 100px;">
                        <small class="text-muted d-block uppercase font-weight-bold" style="font-size: 10px;">IZIN</small>
                        <h4 class="mb-0 font-weight-bold text-info" id="substat-izin"><?= $countIzin; ?></h4>
                    </div>

                    <div class="bg-white p-2 flex-fill rounded shadow-sm border-left" style="border-left: 4px solid #e63946 !important; min-width: 100px;">
                        <small class="text-muted d-block uppercase font-weight-bold" style="font-size: 10px;">ALFA</small>
                        <h4 class="mb-0 font-weight-bold text-danger" id="substat-alfa"><?= $countAlfa; ?></h4>
                    </div>

                    <div class="bg-white p-2 flex-fill rounded shadow-sm border-left" style="border-left: 4px solid #6f42c1 !important; min-width: 100px;">
                        <small class="text-muted d-block uppercase font-weight-bold" style="font-size: 10px; color: #6f42c1 !important;">SEDANG PKL</small>
                        <h4 class="mb-0 font-weight-bold" style="color: #6f42c1 !important;" id="substat-pkl"><?= $countPkl; ?></h4>
                    </div>

                    <div class="bg-white p-2 flex-fill rounded shadow-sm border-left" style="border-left: 4px solid #e01e37 !important; min-width: 110px;">
                        <small class="text-muted d-block uppercase font-weight-bold" style="font-size: 10px; color: #e01e37 !important;">TERLAMBAT MASUK</small>
                        <h4 class="mb-0 font-weight-bold" style="color: #e01e37 !important;" id="substat-terlambat"><?= $countTerlambat; ?></h4>
                    </div>

                    <div class="bg-white p-2 flex-fill rounded shadow-sm border-left" style="border-left: 4px solid #b7094c !important; min-width: 110px;">
                        <small class="text-muted d-block uppercase font-weight-bold" style="font-size: 10px; color: #b7094c !important;">TERLAMBAT PULANG</small>
                        <h4 class="mb-0 font-weight-bold" style="color: #b7094c !important;" id="substat-terlambat-pulang"><?= $countTerlambatPulang; ?></h4>
                    </div>

                    <div class="bg-white p-2 flex-fill rounded shadow-sm border-left" style="border-left: 4px solid #f77f00 !important; min-width: 110px;">
                        <small class="text-muted d-block uppercase font-weight-bold" style="font-size: 10px; color: #f77f00 !important;">BELUM SCAN PULANG</small>
                        <h4 class="mb-0 font-weight-bold" style="color: #f77f00 !important;" id="substat-belum-pulang"><?= $countBelumPulang; ?></h4>
                    </div>

                    <div class="bg-white p-2 flex-fill rounded shadow-sm border-left" style="border-left: 4px solid #94a3b8 !important; min-width: 110px;">
                        <small class="text-muted d-block uppercase font-weight-bold" style="font-size: 10px;">BELUM PRESENSI MASUK</small>
                        <h4 class="mb-0 font-weight-bold text-secondary" id="substat-belum"><?= $countBelum; ?></h4>
                    </div>

                    <div class="p-2 flex-fill rounded shadow-sm text-white" style="background: #4361ee; min-width: 100px;">
                        <small class="text-white d-block uppercase font-weight-bold" style="font-size: 10px; opacity: 0.9;">TOTAL SISWA</small>
                        <h4 class="mb-0 font-weight-bold text-white" id="substat-total"><?= $totalSiswa; ?></h4>
                    </div>

                </div>

                <div class="d-flex flex-wrap align-items-center justify-content-start mt-3" style="gap: 8px; border-top: 1px solid #edf2f7; padding-top: 12px;">
                    <span class="small font-weight-bold text-muted uppercase mr-2" style="font-size: 10px; letter-spacing: 0.5px;">Filter Cepat Status:</span>
                    <button onclick="jalankanFilterSiswa('ALL', this)" class="btn btn-filter-tab active-semua" style="color: #0b0b0b;">SEMUA STATUS</button>
                    <button onclick="jalankanFilterSiswa('TEPAT_WAKTU', this)" class="btn btn-filter-tab" style="color: #2ec4b6;">HADIR TEPAT WAKTU</button>
                    <button onclick="jalankanFilterSiswa('SAKIT', this)" class="btn btn-filter-tab" style="color: #ff9f43;">SAKIT</button>
                    <button onclick="jalankanFilterSiswa('IZIN', this)" class="btn btn-filter-tab" style="color: #3a86ff;">IZIN</button>
                    <button onclick="jalankanFilterSiswa('ALFA', this)" class="btn btn-filter-tab" style="color: #e63946;">ALFA</button>
                    <button onclick="jalankanFilterSiswa('PKL', this)" class="btn btn-filter-tab" style="color: #6f42c1;">SEDANG PKL</button>
                    <button onclick="jalankanFilterSiswa('TERLAMBAT', this)" class="btn btn-filter-tab" style="color: #e01e37;">TERLAMBAT MASUK</button>
                    <button onclick="jalankanFilterSiswa('TERLAMBAT_PULANG', this)" class="btn btn-filter-tab" style="color: #b7094c;">TERLAMBAT SCAN PULANG</button>
                    <button onclick="jalankanFilterSiswa('BELUM_PULANG', this)" class="btn btn-filter-tab" style="color: #f77f00;">BELUM PRESENSI PULANG</button>
                    <button onclick="jalankanFilterSiswa('BELUM_PRESENSI', this)" class="btn btn-filter-tab" style="color: #94a3b8;">BELUM PRESENSI MASUK</button>
                </div>

            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-lg" style="border-radius: 15px; overflow: hidden;">
    <div class="card-header bg-white py-4 px-4 border-0">
        <div class="row align-items-center">
            <div class="col-md-5">
                <h4 class="mb-1 font-weight-bold text-dark">
                    <i class="material-icons align-middle mr-2 text-primary">assignment</i>
                    Data Presensi Kelas
                </h4>
                <p class="text-muted small mb-0">
                    <i class="material-icons align-middle" style="font-size: 14px;">info</i>
                    Menampilkan status kehadiran siswa berdasarkan data murni database.
                </p>
            </div>
            <div class="col-md-7 text-md-right d-flex flex-wrap justify-content-md-end align-items-center mt-3 mt-md-0" style="gap: 12px;">
                
                <button type="button" class="btn btn-danger btn-sm px-4 py-2" onclick="pemicuAksiCetakFilter()" style="border-radius: 20px; background-color: #e63946; border: none; font-weight: 700; display: inline-flex; align-items: center; color: white;">
                    <i class="material-icons mr-1" style="font-size: 18px;">print</i>
                    Cetak Hasil Filter
                </button>

                <div class="d-inline-block px-4 py-2 rounded-pill bg-light border">
                    <span class="text-muted small uppercase font-weight-bold">Kelas:</span>
                    <h5 class="d-inline mb-0 ml-2 font-weight-bold text-primary">
                        <?= htmlspecialchars($kelas); ?>
                    </h5>
                </div>
            </div>
        </div>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <?php if (!empty($data)): ?>
                <table class="table table-hover mb-0 align-middle" id="tabelPresensiUtama">
                    <thead class="bg-light text-primary">
                        <tr>
                            <th class="px-4 py-3 border-0 text-center" width="5%">No.</th>
                            <th class="py-3 border-0">Identitas Siswa</th>
                            <th class="py-3 border-0 text-center" width="15%">Status Kehadiran</th>
                            <th class="py-3 border-0 text-center" width="25%">Waktu Log</th>
                            <th class="py-3 border-0 px-4" width="30%">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        foreach ($data as $value): 
                            $jam_masuk = ($value['jam_masuk'] == '00:00:00') ? null : ($value['jam_masuk'] ?? null);
                            $jam_pulang = ($value['jam_keluar'] == '00:00:00' || $value['jam_keluar'] == '-') ? null : ($value['jam_keluar'] ?? null);
                            $keterangan_db = $value['keterangan'] ?? '';
                            
                            $status_id = (int)($value['id_kehadiran'] ?? 5);
                            $flag_terlambat_masuk = false;
                            $flag_terlambat_pulang = false;
                            $flag_belum_pulang = false;
                            $arrayKeteranganHTML = [];

                            if ($status_id == 1) {
                                // 1. Kalkulasi Terlambat Masuk
                                if ($jam_masuk) {
                                    if (strtotime($jam_masuk) > strtotime($batas_terlambat)) {
                                        $flag_terlambat_masuk = true;
                                        $selisihM = strtotime($jam_masuk) - strtotime($batas_terlambat);
                                        $jam_telatM = floor($selisihM / 3600);
                                        $menit_telatM = floor(($selisihM % 3600) / 60);
                                        
                                        $teksM = '<span class="text-danger font-weight-bold">Terlambat Masuk';
                                        if ($jam_telatM > 0) $teksM .= " " . $jam_telatM . " jam";
                                        $teksM .= " " . $menit_telatM . " menit</span>";
                                        $arrayKeteranganHTML[] = $teksM;
                                    }
                                }

                                // 2. Kalkulasi Terlambat Scan Pulang atau Belum Scan Pulang
                                if ($jam_pulang) {
                                    if (strtotime($jam_pulang) > strtotime($batas_terlambat_pulang)) {
                                        $flag_terlambat_pulang = true;
                                        $selisihP = strtotime($jam_pulang) - strtotime($batas_terlambat_pulang);
                                        $jam_telatP = floor($selisihP / 3600);
                                        $menit_telatP = floor(($selisihP % 3600) / 60);
                                        
                                        $teksP = '<span class="style-pulang-danger font-weight-bold">Terlambat Scan Pulang';
                                        if ($jam_telatP > 0) $teksP .= " " . $jam_telatP . " jam";
                                        $teksP .= " " . $menit_telatP . " menit</span>";
                                        $arrayKeteranganHTML[] = $teksP;
                                    }
                                } else {
                                    if ($jam_masuk) {
                                        $flag_belum_pulang = true;
                                        $arrayKeteranganHTML[] = '<span class="style-belum-pulang font-weight-bold">Belum Presensi Pulang</span>';
                                    }
                                }
                            } elseif ($status_id == 6) {
                                $arrayKeteranganHTML[] = '<span class="style-text-pkl font-weight-bold">Melaksanakan Praktik Kerja Lapangan (PKL)</span>';
                            }

                            $status = getStatusKehadiranVisual($status_id);
                            $namaSiswaClean = ucwords(strtolower(trim($value['nama_siswa'])));
                        ?>
                            <tr data-id-siswa="<?= $value['id_siswa']; ?>"
                                data-status-kehadiran="<?= $status['text']; ?>" 
                                data-terlambat-masuk="<?= $flag_terlambat_masuk ? 'YES' : 'NO'; ?>" 
                                data-terlambat-pulang="<?= $flag_terlambat_pulang ? 'YES' : 'NO'; ?>"
                                data-belum-pulang="<?= $flag_belum_pulang ? 'YES' : 'NO'; ?>">
                                
                                <td class="px-4 text-center text-muted font-weight-bold indeks-nomor-tabel"></td>
                                <td>
                                    <div class="font-weight-bold text-dark" style="font-size: 14px;"><?= htmlspecialchars($namaSiswaClean); ?></div>
                                    <div class="small text-muted">NIS: <?= htmlspecialchars($value['nis']); ?></div>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-pill badge-<?= $status['color']; ?> px-3 py-2 shadow-sm uppercase font-weight-bold badge-status-text" style="min-width: 115px; font-size: 10px;">
                                        <?= ($status['text'] == 'BELUM ABSEN') ? 'BELUM PRESENSI' : $status['text']; ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center align-items-center">
                                        <div class="mx-2 text-center">
                                            <small class="text-muted d-block uppercase mb-1" style="font-size: 9px;">Masuk</small>
                                            <span class="font-weight-bold <?= $jam_masuk ? ($flag_terlambat_masuk ? 'text-danger' : 'text-primary') : ($status_id == 6 ? 'style-text-pkl' : 'text-light') ?>" style="font-size: 13px;">
                                                <?= ($status_id == 6) ? 'PKL' : ($jam_masuk ?: '--:--'); ?>
                                            </span>
                                        </div>
                                        <div class="border-left h-25 mx-2" style="border-color: #eee !important; height: 25px;"></div>
                                        <div class="mx-2 text-center">
                                            <small class="text-muted d-block uppercase mb-1" style="font-size: 9px;">Pulang</small>
                                            <span class="font-weight-bold <?= $jam_pulang ? ($flag_terlambat_pulang ? 'style-pulang-danger' : 'text-success') : ($flag_belum_pulang ? 'style-belum-pulang' : ($status_id == 6 ? 'style-text-pkl' : 'text-light')) ?>" style="font-size: 13px;">
                                                <?= ($status_id == 6) ? 'PKL' : ($jam_pulang ?: '--:--'); ?>
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 kolom-keterangan-row">
                                    <div class="text-xs mb-0" style="line-height: 1.5; font-size: 13px;">
                                        <?php if (!empty($arrayKeteranganHTML)): ?>
                                            <?= implode('<br>', $arrayKeteranganHTML); ?>
                                            <?php if (!empty($keterangan_db)): ?>
                                                <div class="text-muted small mt-1">(<?= htmlspecialchars($keterangan_db); ?>)</div>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span class="text-muted"><?= ($status_id == 1) ? '<span class="badge-soft-success uppercase font-weight-bold" style="font-size:10px;">Tepat Waktu</span>' : '-'; ?></span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="material-icons text-light" style="font-size: 80px;">folder_open</i>
                    <h5 class="text-muted mt-3 font-weight-light">Data Presensi tidak ditemukan.</h5>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<form id="formCetakPresensiHidden" method="POST" action="<?= base_url('/admin/absen-siswa/cetak'); ?>" target="_blank" style="display:none;">
    <?= csrf_field() ?>
    <input type="hidden" name="tanggal_cetak" id="hiddenTanggalCetak">
    <input type="hidden" name="nama_kelas" id="hiddenNamaKelas">
    <input type="hidden" name="filter_title" id="hiddenFilterTitle">
    <div id="hiddenIdsContainer"></div>
</form>

<style>
    .uppercase { text-transform: uppercase; }
    .italic { font-style: italic; }
    
    .table thead th { 
        font-size: 11px; 
        letter-spacing: 1px; 
        text-transform: uppercase; 
        font-weight: 800; 
        border: none !important; 
    }
    .table td { 
        border-top: 1px solid #f8f9fa; 
        vertical-align: middle !important; 
        padding: 1.1rem 0.75rem; 
    }
    .table tbody tr:hover { background-color: rgba(67, 97, 238, 0.02); }

    .badge-pill { border-radius: 50px; }
    
    /* Warna Badge Status */
    .badge-success { background-color: #2ec4b6; color: white; }
    .badge-warning { background-color: #ff9f43; color: white; }
    .badge-info { background-color: #3a86ff; color: white; }
    .badge-danger { background-color: #e63946; color: white; }
    .badge-secondary { background-color: #94a3b8; color: white; }
    .badge-pkl { background-color: #6f42c1; color: white; }
    
    /* Soft Badges */
    .badge-soft-danger { background-color: rgba(230, 57, 70, 0.1); color: #e63946; padding: 4px 10px; border-radius: 6px; font-size: 10.5px; }
    .badge-soft-success { background-color: rgba(46, 196, 182, 0.1); color: #2ec4b6; padding: 4px 10px; border-radius: 6px; font-size: 10.5px; }

    .text-primary { color: #4361ee !important; }
    .text-light { color: #e2e8f0 !important; }

    /* Custom Color Utilities */
    .border-left { border-left-width: 4px !important; }
    .style-pulang-danger { color: #b7094c !important; }
    .style-belum-pulang { color: #f77f00 !important; }
    .style-text-pkl { color: #6f42c1 !important; }

    /* Custom Styling Tombol Filter */
    .btn-filter-tab {
        border-radius: 24px;
        padding: 8px 20px;
        font-size: 0.8rem;
        font-weight: 700;
        border: 1px solid #edf2f7;
        background-color: #ffffff;
        transition: all 0.25s ease-in-out;
        box-shadow: 0 2px 4px rgba(0,0,0,0.02);
    }

    .btn-filter-tab:hover {
        box-shadow: 0 4px 6px rgba(0,0,0,0.06);
        transform: translateY(-1px);
    }

    /* State Filter Aktif Elemen */
    .btn-filter-tab.active-semua { background-color: #8338ec !important; color: #ffffff !important; box-shadow: 0 4px 12px rgba(131, 56, 236, 0.3) !important; border-color: transparent; }
    .btn-filter-tab.active-hadir { background-color: #2ec4b6 !important; color: #ffffff !important; box-shadow: 0 4px 12px rgba(46, 196, 182, 0.3) !important; border-color: transparent; }
    .btn-filter-tab.active-sakit { background-color: #ff9f43 !important; color: #ffffff !important; box-shadow: 0 4px 12px rgba(255, 159, 67, 0.3) !important; border-color: transparent; }
    .btn-filter-tab.active-izin { background-color: #3a86ff !important; color: #ffffff !important; box-shadow: 0 4px 12px rgba(58, 134, 255, 0.3) !important; border-color: transparent; }
    .btn-filter-tab.active-alfa { background-color: #e63946 !important; color: #ffffff !important; box-shadow: 0 4px 12px rgba(230, 57, 70, 0.3) !important; border-color: transparent; }
    .btn-filter-tab.active-pkl { background-color: #6f42c1 !important; color: #ffffff !important; box-shadow: 0 4px 12px rgba(111, 66, 193, 0.3) !important; border-color: transparent; }
    .btn-filter-tab.active-terlambat { background-color: #e01e37 !important; color: #ffffff !important; box-shadow: 0 4px 12px rgba(224, 30, 55, 0.3) !important; border-color: transparent; }
    .btn-filter-tab.active-terlambat-pulang { background-color: #b7094c !important; color: #ffffff !important; box-shadow: 0 4px 12px rgba(183, 9, 76, 0.3) !important; border-color: transparent; }
    .btn-filter-tab.active-belum-pulang { background-color: #f77f00 !important; color: #ffffff !important; box-shadow: 0 4px 12px rgba(247, 127, 0, 0.3) !important; border-color: transparent; }
    .btn-filter-tab.active-belum-masuk { background-color: #94a3b8 !important; color: #ffffff !important; box-shadow: 0 4px 12px rgba(148, 163, 184, 0.3) !important; border-color: transparent; }
</style>

<script type="text/javascript">
// Pastikan fungsi berjalan saat AJAX memuat dokumen pertama kali
$(document).ready(function() {
    urutkanUlangNomorTabel();
});

// Fungsi mandiri untuk mengurutkan nomor baris yang terlihat saja dari angka 1
function urutkanUlangNomorTabel() {
    let internalCounter = 1;
    $('#tabelPresensiUtama tbody tr').each(function() {
        if ($(this).is(':visible')) {
            $(this).find('.indeks-nomor-tabel').text(internalCounter++);
        }
    });
}

function jalankanFilterSiswa(kategori, element) {
    // 1. Reset Semua Class State Aktif Sebelumnya
    $('.btn-filter-tab').removeClass(function(index, className) {
        return (className.match(/(^|\s)active-\S+/g) || []).join(' ');
    });
    
    // 2. Suntikkan Class Active Sesuai Target Supaya Teks Menjadi Putih dan Berbayang
    if (kategori === 'ALL') $(element).addClass('active-semua');
    else if (kategori === 'TEPAT_WAKTU') $(element).addClass('active-hadir');
    else if (kategori === 'SAKIT') $(element).addClass('active-sakit');
    else if (kategori === 'IZIN') $(element).addClass('active-izin');
    else if (kategori === 'ALFA') $(element).addClass('active-alfa');
    else if (kategori === 'PKL') $(element).addClass('active-pkl');
    else if (kategori === 'TERLAMBAT') $(element).addClass('active-terlambat');
    else if (kategori === 'TERLAMBAT_PULANG') $(element).addClass('active-terlambat-pulang');
    else if (kategori === 'BELUM_PULANG') $(element).addClass('active-belum-pulang');
    else if (kategori === 'BELUM_PRESENSI') $(element).addClass('active-belum-masuk');

    // 3. Filter Baris Data Tabel
    $('#tabelPresensiUtama tbody tr').each(function() {
        let baris = $(this);
        let statusSiswa = baris.attr('data-status-kehadiran');
        let statusTelatMasuk = baris.attr('data-terlambat-masuk');
        let statusTelatPulang = baris.attr('data-terlambat-pulang');
        let statusBelumPulang = baris.attr('data-belum-pulang');

        let tampilkan = false;

        if (kategori === 'ALL') {
            tampilkan = true;
        } else if (kategori === 'TEPAT_WAKTU') {
            if (statusSiswa === 'HADIR' && statusTelatMasuk === 'NO') tampilkan = true;
        } else if (kategori === 'TERLAMBAT') {
            if (statusSiswa === 'HADIR' && statusTelatMasuk === 'YES') tampilkan = true;
        } else if (kategori === 'TERLAMBAT_PULANG') {
            if (statusSiswa === 'HADIR' && statusTelatPulang === 'YES') tampilkan = true;
        } else if (kategori === 'BELUM_PULANG') {
            if (statusSiswa === 'HADIR' && statusBelumPulang === 'YES') tampilkan = true;
        } else if (kategori === 'PKL') {
            if (statusSiswa === 'PKL') tampilkan = true;
        } else if (kategori === 'SAKIT') {
            if (statusSiswa === 'SAKIT') tampilkan = true;
        } else if (kategori === 'IZIN') {
            if (statusSiswa === 'IZIN') tampilkan = true;
        } else if (kategori === 'ALFA') {
            if (statusSiswa === 'ALFA') tampilkan = true;
        } else if (kategori === 'BELUM_PRESENSI') {
            if (statusSiswa === 'BELUM ABSEN') tampilkan = true;
        }

        if (tampilkan) {
            baris.show();
        } else {
            baris.hide();
        }
    });

    // Pemicu penyusunan ulang nomor urut agar tidak melompat setelah filter berjalan
    urutkanUlangNomorTabel();
}

// Fungsi pengumpul data ID Siswa terfilter untuk proses kirim form cetak
function pemicuAksiCetakFilter() {
    var tanggal = $('#tanggal').val() || '<?= date("Y-m-d"); ?>';
    var kelasText = '<?= htmlspecialchars($kelas); ?>';
    var filterTerpilihText = $('.btn-filter-tab.active-semua, .btn-filter-tab.active-hadir, .btn-filter-tab.active-sakit, .btn-filter-tab.active-izin, .btn-filter-tab.active-alfa, .btn-filter-tab.active-pkl, .btn-filter-tab.active-terlambat, .btn-filter-tab.active-terlambat-pulang, .btn-filter-tab.active-belum-pulang, .btn-filter-tab.active-belum-masuk').first().text().trim();

    var listIdSiswa = [];
    
    // Ambil data-id-siswa hanya dari baris yang terlihat di layar saat ini
    $('#tabelPresensiUtama tbody tr:visible').each(function() {
        var idSiswa = $(this).attr('data-id-siswa');
        if (idSiswa) {
            listIdSiswa.push(idSiswa);
        }
    });

    if (listIdSiswa.length === 0) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({ icon: 'warning', title: 'Data Kosong', text: 'Tidak ada baris data siswa yang tampil untuk dicetak!' });
        } else {
            alert('Tidak ada baris data siswa yang tampil pada filter saat ini untuk dicetak!');
        }
        return;
    }

    // Set nilai ke input form tersembunyi
    $('#hiddenTanggalCetak').val(tanggal);
    $('#hiddenNamaKelas').val(kelasText);
    $('#hiddenFilterTitle').val(filterTerpilihText);
    
    // Kosongkan container lama dan bangun list token input baru
    $('#hiddenIdsContainer').empty();
    listIdSiswa.forEach(function(id) {
        $('#hiddenIdsContainer').append('<input type="hidden" name="id_siswa_list[]" value="' + id + '">');
    });

    // Jalankan submit post target blank browser
    $('#formCetakPresensiHidden').submit();
}
</script>

<?php
function getStatusKehadiranVisual($id): array
{
    switch ($id) {
        case 1: return ['color' => 'success', 'text' => 'HADIR'];
        case 2: return ['color' => 'warning', 'text' => 'SAKIT'];
        case 3: return ['color' => 'info', 'text' => 'IZIN'];
        case 4: return ['color' => 'danger', 'text' => 'ALFA'];
        case 6: return ['color' => 'pkl', 'text' => 'PKL'];
        default: return ['color' => 'secondary', 'text' => 'BELUM ABSEN'];
    }
}
?>