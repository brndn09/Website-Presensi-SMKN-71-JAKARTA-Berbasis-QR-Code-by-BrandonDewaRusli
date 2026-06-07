<div class="card card-custom border-0 shadow-sm">
    <div class="card-header bg-white border-0 py-4 px-4">
        <div class="row align-items-center">
            <div class="col-12 col-md-5 mb-3 mb-md-0">
                <h4 class="section-title mb-1">Daftar Presensi Siswa</h4>
                <p class="text-muted small mb-0">Menampilkan status kehadiran kelas <span class="badge badge-soft-primary px-2"><?= htmlspecialchars($kelas); ?></span></p>
            </div>
            <div class="col-12 col-md-7 text-md-right d-flex justify-content-md-end align-items-center" style="gap: 10px;">
                <button type="button" class="btn btn-danger btn-sm px-3 py-2" onclick="aksiCetakFilterAktif()" style="border-radius: 10px; background-color: #dc3545; border-color: #dc3545; border: none; color: white;">
                    <i class="material-icons align-middle mr-1" style="font-size: 18px;">print</i> 
                    <span class="align-middle font-weight-bold">Cetak Hasil Filter</span>
                </button>

                <button class="btn btn-outline-primary-custom btn-sm px-3 py-2" onclick="onDateChange()" style="border-radius: 10px;">
                    <i class="material-icons align-middle mr-1" style="font-size: 18px;">refresh</i> 
                    <span class="align-middle font-weight-bold">Refresh Data</span>
                </button>
            </div>
        </div>
    </div>

    <?php use App\Libraries\enums\UserRole; ?>
    
    <div class="card-body p-0">
        <div class="table-responsive">
            <?php if (!empty($data)): ?>
                <?php
                date_default_timezone_set('Asia/Jakarta');
                $batasTerlambatMasuk = "06:30:00"; 
                $batasTerlambatPulang = "15:30:00"; 
                
                $batasTimestampMasuk = strtotime($batasTerlambatMasuk);
                $batasTimestampPulang = strtotime($batasTerlambatPulang);

                // --- PROSES SORTING DATA BERDASARKAN ATURAN ---
                usort($data, function($a, $b) use ($batasTimestampMasuk, $batasTimestampPulang) {
                    $idA = intval($a['id_kehadiran'] ?? 5);
                    $idB = intval($b['id_kehadiran'] ?? 5);
                    
                    $jamMasukA = $a['jam_masuk'] ?? null;
                    $jamMasukB = $b['jam_masuk'] ?? null;
                    $jamKeluarA = $a['jam_keluar'] ?? null;
                    $jamKeluarB = $b['jam_keluar'] ?? null;

                    $weightA = 6; 
                    if ($idA == 1) {
                        if (!empty($jamMasukA) && strtotime($jamMasukA) > $batasTimestampMasuk) {
                            $weightA = 1; 
                        } elseif (!empty($jamKeluarA) && $jamKeluarA !== '-' && strtotime($jamKeluarA) > $batasTimestampPulang) {
                            $weightA = 2; 
                        } else {
                            $weightA = 3; 
                        }
                    } elseif ($idA == 6) {
                        $weightA = 4; 
                    } elseif ($idA == 5) {
                        $weightA = 5; 
                    }

                    $weightB = 6; 
                    if ($idB == 1) {
                        if (!empty($jamMasukB) && strtotime($jamMasukB) > $batasTimestampMasuk) {
                            $weightB = 1;
                        } elseif (!empty($jamKeluarB) && $jamKeluarB !== '-' && strtotime($jamKeluarB) > $batasTimestampPulang) {
                            $weightB = 2;
                        } else {
                            $weightB = 3;
                        }
                    } elseif ($idB == 6) {
                        $weightB = 4;
                    } elseif ($idB == 5) {
                        $weightB = 5;
                    }

                    if ($weightA !== $weightB) {
                        return $weightA <=> $weightB;
                    }

                    return strcasecmp($a['nama_siswa'], $b['nama_siswa']);
                });
                ?>

                <table id="tabelPresensiSiswa" class="table table-hover align-items-center mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="text-uppercase text-muted text-xxs font-weight-bolder opacity-7 px-4">No.</th>
                            <th class="text-uppercase text-muted text-xxs font-weight-bolder opacity-7">Identitas Siswa</th>
                            <th class="text-uppercase text-muted text-xxs font-weight-bolder opacity-7 text-center">Kelas</th>
                            <th class="text-uppercase text-muted text-xxs font-weight-bolder opacity-7 text-center">Status</th>
                            <th class="text-uppercase text-muted text-xxs font-weight-bolder opacity-7 text-center">Waktu Log</th>
                            <th class="text-uppercase text-muted text-xxs font-weight-bolder opacity-7 px-4">Keterangan</th>
                            <th class="text-uppercase text-muted text-xxs font-weight-bolder opacity-7 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($data as $value): ?>
                            <?php
                                $idKehadiran = intval($value['id_kehadiran'] ?? 5);
                                $jamMasukSiswa = $value['jam_masuk'] ?? null;
                                $jamKeluarSiswa = $value['jam_keluar'] ?? null;
                                
                                $keteranganTampil = $value['keterangan'] ?: '-';
                                $isTerlambatMasuk = false;
                                $isTerlambatPulang = false;
                                $isBelumPresensiPulang = false;
                                $arrayKeteranganHTML = [];

                                if ($idKehadiran == 1) { 
                                    if (!empty($jamMasukSiswa)) {
                                        $masukTimestamp = strtotime($jamMasukSiswa);
                                        if ($masukTimestamp > $batasTimestampMasuk) {
                                            $isTerlambatMasuk = true;
                                            $selisihMasuk = $masukTimestamp - $batasTimestampMasuk;
                                            $jamTelatM = floor($selisihMasuk / 3600);
                                            $menitTelatM = floor(($selisihMasuk % 3600) / 60);
                                            
                                            $teksM = '<span class="text-danger font-weight-bold">Terlambat Masuk';
                                            if ($jamTelatM > 0) $teksM .= " " . $jamTelatM . " jam";
                                            $teksM .= " " . $menitTelatM . " menit</span>";
                                            $arrayKeteranganHTML[] = $teksM;
                                        }
                                    }

                                    if (!empty($jamKeluarSiswa) && $jamKeluarSiswa !== '-' && $jamKeluarSiswa !== '--:--') {
                                        $keluarTimestamp = strtotime($jamKeluarSiswa);
                                        if ($keluarTimestamp > $batasTimestampPulang) {
                                            $isTerlambatPulang = true;
                                            $selisihPulang = $keluarTimestamp - $batasTimestampPulang;
                                            $jamTelatP = floor($selisihPulang / 3600);
                                            $menitTelatP = floor(($selisihPulang % 3600) / 60);
                                            
                                            $teksP = '<span class="style-pulang-danger font-weight-bold">Terlambat Scan Pulang';
                                            if ($jamTelatP > 0) $teksP .= " " . $jamTelatP . " jam";
                                            $teksP .= " " . $menitTelatP . " menit</span>";
                                            $arrayKeteranganHTML[] = $teksP;
                                        }
                                    } else {
                                        if (!empty($jamMasukSiswa)) {
                                            $isBelumPresensiPulang = true;
                                            $arrayKeteranganHTML[] = '<span class="style-belum-pulang font-weight-bold">Belum Presensi Pulang</span>';
                                        }
                                    }
                                } elseif ($idKehadiran == 6) {
                                    $arrayKeteranganHTML[] = '<span class="style-text-pkl font-weight-bold">Melaksanakan Praktik Kerja Lapangan (PKL)</span>';
                                }

                                $kehadiran = helperKehadiran($idKehadiran);
                                $kelasSiswaTampil = (!empty($value['nama_kelas'])) ? $value['nama_kelas'] : ((!empty($value['kelas'])) ? $value['kelas'] : $kelas);
                                $namaSiswaClean = ucwords(strtolower(trim($value['nama_siswa'])));
                            ?>

                            <tr data-id-siswa="<?= $value['id_siswa']; ?>"
                                data-status-hadir="<?= ($idKehadiran == 1) ? 'YA' : 'TIDAK'; ?>"
                                data-terlambat-masuk="<?= $isTerlambatMasuk ? 'YA' : 'TIDAK'; ?>"
                                data-terlambat-pulang="<?= $isTerlambatPulang ? 'YA' : 'TIDAK'; ?>"
                                data-belum-pulang="<?= $isBelumPresensiPulang ? 'YA' : 'TIDAK'; ?>">
                                
                                <td class="px-4 py-3">
                                    <span class="text-small font-weight-bold text-muted row-number"></span>
                                </td>
                                
                                <td class="py-3">
                                    <div class="d-flex flex-column">
                                        <h6 class="mb-0 text-sm font-weight-bold text-dark"><?= htmlspecialchars($namaSiswaClean); ?></h6>
                                        <p class="text-xs text-muted mb-0">NIS: <?= htmlspecialchars($value['nis']); ?></p>
                                    </div>
                                </td>
                                
                                <td class="text-center py-3">
                                    <span class="badge badge-soft-dark py-2 px-3 font-weight-bolder text-xxs shadow-none" style="border-radius: 6px; min-width: 75px;">
                                        <?= htmlspecialchars($kelasSiswaTampil); ?>
                                    </span>
                                </td>
                                
                                <td class="text-center py-3">
                                    <span class="badge badge-pill py-2 px-3 badge-soft-<?= $kehadiran['color']; ?> font-weight-bold badge-status-text mx-auto" style="font-size: 0.75rem; min-width: 110px; display: inline-block;">
                                        <?= $kehadiran['text']; ?>
                                    </span>
                                </td>
                                
                                <td class="text-center py-3">
                                    <div class="d-flex justify-content-center align-items-center">
                                        <div class="text-center px-2">
                                            <p class="text-xxs text-muted mb-0 uppercase">Masuk</p>
                                            <span class="text-xs font-weight-bold <?= $isTerlambatMasuk ? 'text-danger' : ($idKehadiran == 6 ? 'style-text-pkl' : 'text-dark'); ?>"><?= ($idKehadiran == 6) ? 'PKL' : ($value['jam_masuk'] ?? '--:--'); ?></span>
                                        </div>
                                        <div class="border-left h-25 mx-2"></div>
                                        <div class="text-center px-2">
                                            <p class="text-xxs text-muted mb-0 uppercase">Pulang</p>
                                            <span class="text-xs font-weight-bold <?= $isTerlambatPulang ? 'style-pulang-danger' : ($isBelumPresensiPulang ? 'style-belum-pulang' : ($idKehadiran == 6 ? 'style-text-pkl' : 'text-dark')); ?>"><?= ($idKehadiran == 6) ? 'PKL' : ((!empty($value['jam_keluar']) && $value['jam_keluar'] !== '-') ? $value['jam_keluar'] : '--:--'); ?></span>
                                        </div>
                                    </div>
                                </td>
                                
                                <td class="px-4 py-3">
                                    <div class="text-xs mb-0" style="line-height: 1.5;">
                                        <?php if (!empty($arrayKeteranganHTML)): ?>
                                            <?= implode('<br>', $arrayKeteranganHTML); ?>
                                            <?php if (!empty($value['keterangan'])): ?>
                                                <div class="text-muted small mt-1">(<?= htmlspecialchars($value['keterangan']); ?>)</div>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span class="text-muted"><?= htmlspecialchars($keteranganTampil); ?></span>
                                        <?php endif; ?>
                                    </div>
                                </td>                                
                                
                                <td class="text-center py-3 px-4">
                                    <?php if (!$lewat && (is_superadmin() || is_kepsek())): ?>
                                        <button data-toggle="modal" data-target="#ubahModal" 
                                                onclick="getDataKehadiran(<?= $value['id_presensi'] ?? '-1'; ?>, <?= $value['id_siswa']; ?>)" 
                                                class="btn btn-sm btn-soft-primary px-3 shadow-none border-0" 
                                                title="Ubah Status Presensi" style="border-radius: 8px;">
                                            <i class="material-icons align-middle" style="font-size: 18px;">edit_note</i>
                                            <span class="align-middle ml-1 font-weight-bold">Ubah</span>
                                        </button>
                                    <?php else: ?>
                                        <span class="text-xxs font-weight-bold text-muted">TERKUNCI</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="text-center py-5">
                    <img src="<?= base_url('assets/img/no-data.svg') ?>" style="width: 120px; opacity: 0.5;" class="mb-3">
                    <h5 class="text-muted font-weight-light">Belum ada data siswa untuk ditampilkan</h5>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php if (!empty($data)): ?>
        <div class="card-footer bg-white border-0 py-3 px-4 d-flex justify-content-between align-items-center">
            <div class="text-muted small">
                Menampilkan <span id="paginasi-info-awal" class="font-weight-bold">0</span> - <span id="paginasi-info-akhir" class="font-weight-bold">0</span> dari <span id="paginasi-info-total" class="font-weight-bold">0</span> data siswa terpilih
            </div>
            <nav aria-label="Navigasi Halaman Absensi">
                <ul class="pagination pagination-sm mb-0 justify-content-end" id="kontainer-navigasi-slide">
                    </ul>
            </nav>
        </div>
    <?php endif; ?>
</div>

<form id="formCetakPresensiHidden" method="POST" action="<?= base_url('/admin/absen-siswa/cetak'); ?>" target="_blank" style="display:none;">
    <?= csrf_field() ?>
    <input type="hidden" name="tanggal_cetak" id="hiddenTanggalCetak">
    <input type="hidden" name="nama_kelas" id="hiddenNamaKelas">
    <input type="hidden" name="filter_title" id="hiddenFilterTitle">
    <div id="hiddenIdsContainer"></div>
</form>

<script>
    // Konfigurasi Sistem Paginasi Slide Tabel
    var barisPerHalaman = 50; // Jumlah baris siswa yang tampil per slide halaman
    var halamanAktif = 1;
    var arrayBarisTerfilter = [];

    $(document).ready(function() {
        inisialisasiPaginasiSistem();
        
        // INTERSEPTOR: Integrasikan dengan fungsi filter bawaan agar slide halaman ikut ter-refresh otomatis
        if (typeof window.filterStatusTabelOriginal === 'undefined' && typeof window.filterStatusTabel === 'function') {
            window.filterStatusTabelOriginal = window.filterStatusTabel;
            window.filterStatusTabel = function(targetStatus, element) {
                window.filterStatusTabelOriginal(targetStatus, element);
                
                // Setel ulang halaman aktif ke halaman pertama setiap kali filter diubah
                halamanAktif = 1;
                inisialisasiPaginasiSistem();
            };
        }
    });

    // Fungsi utama mendeteksi baris yang lolos filter, menata nomor urut awal, dan membuat navigasi slide
    function inisialisasiPaginasiSistem() {
        arrayBarisTerfilter = [];
        var nomorUrut = 1;

        // 1. Kumpulkan baris data yang berstatus visible (lolos dari filter cepat)
        $('#tabelPresensiSiswa tbody tr').each(function() {
            var baris = $(this);
            // Cek kondisi filter yang diatur oleh fungsi view utama
            if (baris.css('display') !== 'none') {
                baris.find('.row-number').text(nomorUrut);
                arrayBarisTerfilter.push(baris);
                nomorUrut++;
            }
        });

        // 2. Tampilkan/Sembunyikan baris data berdasarkan slice halaman aktif
        renderSlideHalaman();
    }

    function renderSlideHalaman() {
        var totalData = arrayBarisTerfilter.length;
        var totalHalaman = Math.ceil(totalData / barisPerHalaman) || 1;

        if (halamanAktif > totalHalaman) halamanAktif = totalHalaman;

        var indeksMulai = (halamanAktif - 1) * barisPerHalaman;
        var indeksSelesai = indeksMulai + barisPerHalaman;

        // Sembunyikan semua baris terlebih dahulu
        arrayBarisTerfilter.forEach(function(baris) {
            baris.hide();
        });

        // Hanya tampilkan baris yang masuk ke rentang halaman aktif saat ini
        for (var i = indeksMulai; i < indeksSelesai && i < totalData; i++) {
            arrayBarisTerfilter[i].show();
        }

        // 3. Update info teks statistik footer paginasi
        var infoAwal = totalData === 0 ? 0 : indeksMulai + 1;
        var infoAkhir = indeksSelesai > totalData ? totalData : indeksSelesai;
        $('#paginasi-info-awal').text(infoAwal);
        $('#paginasi-info-akhir').text(infoAkhir);
        $('#paginasi-info-total').text(totalData);

        // 4. Bangun komponen tombol navigasi slide < 1 2 3 >
        bangunTombolNavigasi(totalHalaman);
    }

    function bangunTombolNavigasi(totalHalaman) {
        var kontainerNav = $('#kontainer-navigasi-slide');
        kontainerNav.empty();

        if (arrayBarisTerfilter.length === 0) return;

        // Tombol Back (<)
        var kelasDisabledSebelumnya = (halamanAktif === 1) ? 'disabled' : '';
        kontainerNav.append(`
            <li class="page-item ${kelasDisabledSebelumnya}">
                <a class="page-link" href="javascript:void(0)" onclick="pindahHalaman(${halamanAktif - 1})" aria-label="Sebelumnya">
                    <span aria-hidden="true">&laquo;</span>
                </a>
            </li>
        `);

        // Daftar Angka Halaman
        for (var p = 1; p <= totalHalaman; p++) {
            var kelasAktif = (p === halamanAktif) ? 'active' : '';
            kontainerNav.append(`
                <li class="page-item ${kelasAktif}">
                    <a class="page-link font-weight-bold" href="javascript:void(0)" onclick="pindahHalaman(${p})">${p}</a>
                </li>
            `);
        }

        // Tombol Next (>)
        var kelasDisabledSelanjutnya = (halamanAktif === totalHalaman) ? 'disabled' : '';
        kontainerNav.append(`
            <li class="page-item ${kelasDisabledSelanjutnya}">
                <a class="page-link" href="javascript:void(0)" onclick="pindahHalaman(${halamanAktif + 1})" aria-label="Selanjutnya">
                    <span aria-hidden="true">&raquo;</span>
                </a>
            </li>
        `);
    }

    function pindahHalaman(targetHalaman) {
        var totalHalaman = Math.ceil(arrayBarisTerfilter.length / barisPerHalaman) || 1;
        if (targetHalaman < 1 || targetHalaman > totalHalaman) return;
        
        halamanAktif = targetHalaman;
        renderSlideHalaman();
    }

    // Fungsi cetak mendeteksi seluruh data terfilter, bukan hanya halaman yang aktif dilayar
    function aksiCetakFilterAktif() {
        var tanggal = $('#tanggal').val();
        var kelasText = lastKelas || 'Semua Kelas';
        var filterTerpilihText = $('.btn-filter-status.active-semua, .btn-filter-status.active-hadir, .btn-filter-status.active-sakit, .btn-filter-status.active-izin, .btn-filter-status.active-alfa, .btn-filter-status.active-pkl, .btn-filter-status.active-terlambat, .btn-filter-status.active-terlambat-pulang, .btn-filter-status.active-belum-pulang, .btn-filter-status.active-belum-masuk').first().text().trim();

        var listIdSiswa = [];
        
        // Membaca dari array hasil filter agar semua data lintas halaman paged ikut tercetak lengkap
        arrayBarisTerfilter.forEach(function(baris) {
            var idSiswa = baris.attr('data-id-siswa');
            if (idSiswa) {
                listIdSiswa.push(idSiswa);
            }
        });

        if (listIdSiswa.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Data Kosong',
                text: 'Tidak ada data siswa yang tampil pada filter saat ini untuk dicetak!'
            });
            return;
        }

        $('#hiddenTanggalCetak').val(tanggal);
        $('#hiddenNamaKelas').val(kelasText);
        $('#hiddenFilterTitle').val(filterTerpilihText);
        
        $('#hiddenIdsContainer').empty();
        listIdSiswa.forEach(function(id) {
            $('#hiddenIdsContainer').append('<input type="hidden" name="id_siswa_list[]" value="' + id + '">');
        });

        $('#formCetakPresensiHidden').submit();
    }
</script>

<style>
    /* Styling Tambahan Komponen Navigasi Paginasi Slide */
    .pagination .page-link {
        color: #4361ee;
        border: 1px solid #edf2f7;
        padding: 6px 12px;
        transition: all 0.2s;
    }
    .pagination .page-item.active .page-link {
        background-color: #4361ee !important;
        border-color: #4361ee !important;
        color: white !important;
        box-shadow: 0 4px 10px rgba(67, 97, 238, 0.2);
    }
    .pagination .page-item.disabled .page-link {
        color: #94a3b8;
        background-color: #f8f9fa;
        border-color: #edf2f7;
    }
    .pagination .page-link:hover:not(.active) {
        background-color: rgba(67, 97, 238, 0.05);
        color: #4361ee;
    }

    .text-xxs { font-size: 0.65rem !important; letter-spacing: 0.05rem; }
    .text-xs { font-size: 0.75rem !important; }
    .text-sm { font-size: 0.875rem !important; }
    .uppercase { text-transform: uppercase; }
    
    .badge-soft-success { background-color: rgba(46, 196, 182, 0.1); color: #2ec4b6; }
    .badge-soft-warning { background-color: rgba(255, 159, 67, 0.1); color: #ff9f43; }
    .badge-soft-info { background-color: rgba(58, 134, 255, 0.1); color: #33a8ff; }
    .badge-soft-danger { background-color: rgba(230, 57, 70, 0.1); color: #e63946; }
    .badge-soft-disabled { background-color: #f1f5f9; color: #94a3b8; }
    .badge-soft-primary { background-color: rgba(67, 97, 238, 0.1); color: #4361ee; }
    .badge-soft-dark { background-color: rgba(30, 41, 59, 0.1); color: #1e293b; }
    .badge-soft-pkl { background-color: rgba(111, 66, 193, 0.1); color: #6f42c1; }

    .btn-soft-primary { background-color: rgba(67, 97, 238, 0.1); color: #4361ee; transition: 0.2s; }
    .btn-soft-primary:hover { background-color: #4361ee; color: white; }

    .table thead th { border-bottom: 1px solid #edf2f7; }
    .table td { vertical-align: middle; border-bottom: 1px solid #f8f9fc; }
    .table tbody tr:last-child td { border-bottom: none; }
    
    .style-pulang-danger { color: #b7094c !important; }
    .style-belum-pulang { color: #f77f00 !important; }
    .style-text-pkl { color: #6f42c1 !important; }
</style>

<?php
function helperKehadiran($idKehadiran): array
{
    switch ($idKehadiran) {
        case 1: return ['color' => 'success', 'text' => 'Hadir'];
        case 2: return ['color' => 'warning', 'text' => 'Sakit'];
        case 3: return ['color' => 'info', 'text' => 'Izin'];
        case 4: return ['color' => 'danger', 'text' => 'Alfa'];
        case 6: return ['color' => 'pkl', 'text' => 'PKL']; 
        case 5:
        default: return ['color' => 'disabled', 'text' => 'Belum Presensi'];
    }
}
?>