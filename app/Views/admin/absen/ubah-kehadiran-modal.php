<div class="modal-body px-4 pt-4">
    <div class="container-fluid p-0">
        <div class="d-flex align-items-center mb-4 p-3 bg-light" style="border-radius: 12px;">
            <div class="icon-box bg-soft-primary mr-3" style="width: 40px; height: 40px;">
                <i class="material-icons" style="font-size: 20px;">person</i>
            </div>
            <div>
                <?php 
                    // Standardisasi Kapitalisasi Nama agar Rapi (Brandon Dewa Rusli)
                    $namaSiswaClean = ucwords(strtolower(trim($data['nama_siswa'] ?? 'Nama Siswa')));
                ?>
                <h6 class="mb-0 font-weight-bold text-dark"><?= htmlspecialchars($namaSiswaClean); ?></h6>
                <p class="text-xxs text-muted mb-0">Atur status kehadiran untuk tanggal terpilih</p>
            </div>
        </div>

        <form id="formUbah">
            <input type="hidden" name="id_siswa" value="<?= $data['id_siswa'] ?? ''; ?>">
            <input type="hidden" name="id_guru" value="<?= $data['id_guru'] ?? ''; ?>">
            <input type="hidden" name="id_kelas" value="<?= $data['id_kelas'] ?? ''; ?>">
            <input type="hidden" name="id_presensi" value="<?= $presensi['id_presensi'] ?? ''; ?>">

            <label class="stat-label mb-3">Pilih Status Kehadiran</label>
            <div class="attendance-options mb-4">
                <?php foreach ($listKehadiran as $value2) : ?>
                    <?php 
                        if ($value2['id_kehadiran'] == 5) continue; // Skip 'Belum Tersedia' dari pilihan
                        $kehadiran = kehadiran($value2['id_kehadiran']); 
                        $isSelected = ($value2['id_kehadiran'] == ($presensi['id_kehadiran'] ?? '4'));
                    ?>
                    <div class="custom-attendance-card mb-2">
                        <input class="d-none" type="radio" name="id_kehadiran" 
                               id="k<?= $value2['id_kehadiran']; ?>" 
                               value="<?= $value2['id_kehadiran']; ?>" 
                               onchange="toggleTimeInputs(this.value)"
                               <?= $isSelected ? 'checked' : ''; ?>>
                        <label class="attendance-label d-flex align-items-center p-3 border rounded-lg cursor-pointer transition-all" 
                               for="k<?= $value2['id_kehadiran']; ?>" 
                               style="border-radius: 12px; cursor: pointer; border: 1.5px solid #edf2f7;">
                            <div class="custom-radio-circle mr-3"></div>
                            <span class="font-weight-bold text-sm text-<?= $kehadiran['color']; ?>">
                                <?= $kehadiran['text']; ?>
                            </span>
                        </label>
                    </div>
                <?php endforeach; ?>

            <div class="row mb-4" id="timeFieldsContainer">
                <div class="col-6">
                    <label class="stat-label mb-2">Jam Masuk</label>
                    <input class="form-control form-control-modern" type="time" name="jam_masuk" 
                           id="jamMasuk" value="<?= $presensi['jam_masuk'] ?? ''; ?>">
                </div>
                <div class="col-6">
                    <label class="stat-label mb-2">Jam Keluar</label>
                    <input class="form-control form-control-modern" type="time" name="jam_keluar" 
                           id="jamKeluar" value="<?= $presensi['jam_keluar'] ?? ''; ?>">
                </div>
            </div>

            <div class="form-group mb-0">
                <label class="stat-label mb-2">Catatan / Keterangan</label>
                <textarea id="keterangan" name="keterangan" 
                          class="form-control form-control-modern" 
                          placeholder="Contoh: Sakit demam, PKL di PT Indonesia Sejahtera..." 
                          style="min-height: 100px; padding-top: 12px;"><?= trim($presensi['keterangan'] ?? ''); ?></textarea>
            </div>
        </form>
    </div>
</div>

<div class="modal-footer border-0 px-4 pb-4 pt-0">
    <button type="button" class="btn btn-light font-weight-bold px-4 py-2" 
            data-dismiss="modal" style="border-radius: 10px;">Batal</button>
    <button type="button" onclick="ubahKehadiran()" 
            class="btn btn-primary font-weight-bold px-4 py-2 shadow-sm" 
            style="border-radius: 10px; background: #4361ee;">Simpan Perubahan</button>
</div>

<style>
    /* Styling khusus Form Kehadiran */
    .attendance-label {
        transition: all 0.2s ease;
        background: #fff;
    }

    .attendance-label:hover {
        background: #f8fafc;
        border-color: #cbd5e1 !important;
    }

    /* Logic checked radio */
    input[type="radio"]:checked + .attendance-label {
        background: rgba(67, 97, 238, 0.05);
        border-color: #4361ee !important;
    }

    input[type="radio"]:checked + .attendance-label .custom-radio-circle {
        border: 5px solid #4361ee;
        background: #fff;
    }
    
    /* Checked Khusus Opsi PKL */
    input[type="radio"][value="6"]:checked + .attendance-label {
        background: rgba(111, 66, 193, 0.05);
        border-color: #6f42c1 !important;
    }
    input[type="radio"][value="6"]:checked + .attendance-label .custom-radio-circle {
        border: 5px solid #6f42c1;
        background: #fff;
    }

    .custom-radio-circle {
        width: 18px;
        height: 18px;
        border-radius: 50%;
        border: 2px solid #cbd5e1;
        transition: all 0.2s;
    }

    .cursor-pointer { cursor: pointer; }
    .text-xxs { font-size: 0.7rem; }
    .text-sm { font-size: 0.9rem; }
    
    /* Custom utility warna text untuk PKL (Indigo) */
    .text-indigo-custom { color: #6f42c1 !important; }

    .form-control-modern {
        border-radius: 10px;
        border: 1.5px solid #edf2f7;
        background-color: #fbfcfe;
    }

    .form-control-modern:focus {
        background-color: #fff;
        border-color: #4361ee;
        box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.1);
    }
</style>

<script>
    /**
     * Manajemen statis form log waktu: Mengosongkan field input jam
     * jika opsi yang dipilih adalah status PKL (ID: 6)
     */
    function toggleTimeInputs(idKehadiran) {
        const timeContainer = document.getElementById('timeFieldsContainer');
        const jamMasuk = document.getElementById('jamMasuk');
        const jamKeluar = document.getElementById('jamKeluar');
        
        if (parseInt(idKehadiran) === 6) {
            // Beri visual transparan, nonaktifkan field, dan kosongkan value jam agar filter statis berfungsi
            timeContainer.style.opacity = '0.5';
            jamMasuk.value = '';
            jamKeluar.value = '';
            jamMasuk.setAttribute('disabled', 'disabled');
            jamKeluar.setAttribute('disabled', 'disabled');
        } else {
            timeContainer.style.opacity = '1';
            jamMasuk.removeAttribute('disabled');
            jamKeluar.removeAttribute('disabled');
        }
    }
    
    // Trigger pengecekan saat inisiasi muat modal pertama kali terbuka
    setTimeout(function() {
        let currentSelected = $('input[type="radio"][name="id_kehadiran"]:checked').val();
        if (currentSelected) toggleTimeInputs(currentSelected);
    }, 200);
</script>

<?php
function kehadiran($kehadiran): array
{
    $text = '';
    $color = '';
    switch ($kehadiran) {
        case 1: $color = 'success'; $text = 'Hadir'; break;
        case 2: $color = 'warning'; $text = 'Sakit'; break;
        case 3: $color = 'info'; $text = 'Izin'; break;
        case 4: $color = 'danger'; $text = 'Alfa'; break;
        case 6: $color = 'indigo-custom'; $text = 'Sedang PKL'; break;
        case 5:
        default: $color = 'secondary'; $text = 'Belum tersedia'; break;
    }
    return ['color' => $color, 'text' => $text];
}
?>