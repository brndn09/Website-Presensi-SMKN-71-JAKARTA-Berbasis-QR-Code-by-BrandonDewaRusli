<?= $this->extend('templates/laporan') ?>

<?= $this->section('content') ?>
<style>
    .main-table { width: 100%; border-collapse: collapse; margin-top: 20px; font-size: 10pt; }
    .main-table th, .main-table td { border: 1px solid black; padding: 8px; }
    .main-table th { background-color: #f2f2f2; text-align: center; }
    .text-center { text-align: center !important; }
    .text-red { color: red; font-weight: bold; }
</style>

<div class="text-center">
    <h2 style="margin-bottom: 5px;">LAPORAN KETERLAMBATAN SISWA</h2>
    <?php $kelasNama = is_array($kelas) ? $kelas['kelas'] : $kelas->kelas; ?>
    <h4 style="margin: 0;">KELAS: <?= strtoupper($kelasNama); ?> | PERIODE: <?= strtoupper($bulan . ' ' . $tahun); ?></h4>
    <p style="margin-top: 5px;">Batas Toleransi: <strong><?= $batas; ?> WIB</strong></p>
</div>

<table class="main-table">
    <thead>
        <tr>
            <th width="5%">No</th>
            <th width="15%">Tanggal</th>
            <th>Nama Siswa</th>
            <th width="12%">NIS</th>
            <th width="12%">Jam Masuk</th>
            <th width="15%">Keterlambatan</th>
        </tr>
    </thead>
    <tbody>
        <?php if(empty($rekapTerlambat)): ?>
            <tr><td colspan="6" class="text-center">Tidak ada data keterlambatan pada hari kerja di periode ini.</td></tr>
        <?php else: ?>
            <?php $no = 1; foreach($rekapTerlambat as $row): ?>
            <tr>
                <td class="text-center"><?= $no++; ?></td>
                <td class="text-center"><?= date('d-m-Y', strtotime($row['tanggal'])); ?></td>
                <td><?= strtoupper($row['nama_siswa']); ?></td>
                <td class="text-center"><?= $row['nis']; ?></td>
                <td class="text-center"><?= $row['jam_masuk']; ?></td>
                <td class="text-center text-red">
                    <?php 
                        $diff = strtotime($row['jam_masuk']) - strtotime($batas);
                        echo floor($diff / 60) . " Menit";
                    ?>
                </td>
            </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>
<?= $this->endSection() ?>