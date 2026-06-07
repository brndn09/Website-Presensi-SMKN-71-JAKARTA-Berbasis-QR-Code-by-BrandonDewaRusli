<?php

namespace App\Models;

use App\Models\PresensiInterface;
use CodeIgniter\I18n\Time;
use CodeIgniter\Model;

class PresensiSiswaModel extends Model implements PresensiInterface
{
    protected $table      = 'tb_presensi_siswa';
    protected $primaryKey = 'id_presensi';

    protected $allowedFields = [
        'id_siswa',
        'id_kelas',
        'tanggal',
        'jam_masuk',
        'jam_keluar',
        'id_kehadiran',
        'keterangan'
    ];

    public function cekAbsen(string|int $id, string|Time $date)
    {
        $result = $this->where(['id_siswa' => $id, 'tanggal' => $date])->first();
        return empty($result) ? false : $result[$this->primaryKey];
    }

    public function absenMasuk(string $id, $date, $time, $idKelas = '')
    {
        return $this->save([
            'id_siswa'     => $id,
            'id_kelas'     => $idKelas,
            'tanggal'      => $date,
            'jam_masuk'    => $time,
            'id_kehadiran' => 1, // Status Hadir
            'keterangan'   => ''
        ]);
    }

    public function absenKeluar(string $id, $time)
    {
        return $this->update($id, [
            'jam_keluar' => $time
        ]);
    }

    public function getPresensiByIdSiswaTanggal($idSiswa, $date)
    {
        return $this->where(['id_siswa' => $idSiswa, 'tanggal' => $date])->first();
    }

    public function getPresensiById(string $idPresensi)
    {
        return $this->find($idPresensi);
    }

    /**
     * Mengambil data list presensi per kelas dan tanggal.
     */
    public function getPresensiByKelasTanggal($idKelas, $tanggal): array
    {
        return $this->db->table('tb_siswa')
            ->select('tb_siswa.*, p.id_presensi, p.tanggal, p.jam_masuk, p.jam_keluar, p.id_kehadiran, p.keterangan')
            ->join('tb_presensi_siswa p', "tb_siswa.id_siswa = p.id_siswa AND p.tanggal = '$tanggal'", 'left')
            ->where("tb_siswa.id_kelas", $idKelas)
            ->orderBy("tb_siswa.nama_siswa", "ASC")
            ->get()
            ->getResultArray();
    }

    /**
     * Mengambil seluruh data presensi siswa dari semua kelas tanpa memfilter ID kelas tertentu.
     */
    public function getPresensiSemuaKelas($tanggal): array
    {
        return $this->db->table('tb_siswa')
            ->select('tb_siswa.*, tb_presensi_siswa.id_presensi, tb_presensi_siswa.tanggal, tb_presensi_siswa.jam_masuk, tb_presensi_siswa.jam_keluar, tb_presensi_siswa.id_kehadiran, tb_presensi_siswa.keterangan')
            ->join('tb_presensi_siswa', "tb_siswa.id_siswa = tb_presensi_siswa.id_siswa AND tb_presensi_siswa.tanggal = '$tanggal'", 'left')
            ->orderBy("tb_siswa.id_kelas", "ASC")
            ->orderBy("tb_siswa.nama_siswa", "ASC")
            ->get()
            ->getResultArray();
    }

    /**
     * Wajib diimplementasikan dari PresensiInterface.
     */
    public function getPresensiByKehadiran(string $idKehadiran, $tanggal, $idKelas = null)
    {
        return $this->countPresensiByStatus($idKehadiran, $tanggal, $idKelas);
    }

    /**
     * Menghitung statistik kehadiran murni dari Database.
     */
    public function countPresensiByStatus($idKehadiran, $tanggal, $idKelas = null): int
    {
        // 5 = Status Belum Absen / Belum Presensi Utama
        if ($idKehadiran == '5') {
            $subQuery = $this->db->table('tb_presensi_siswa')
                                 ->select('id_siswa')
                                 ->where('tanggal', $tanggal);
            
            $builder = $this->db->table('tb_siswa')->whereNotIn('id_siswa', $subQuery);
            if ($idKelas && $idKelas !== 'all') {
                $builder->where('id_kelas', $idKelas);
            }
            return $builder->countAllResults();
        }

        $builder = $this->db->table($this->table)
                        ->where('tanggal', $tanggal)
                        ->where('id_kehadiran', $idKehadiran);
        
        if ($idKelas && $idKelas !== 'all') {
            $builder->join('tb_siswa', 'tb_siswa.id_siswa = tb_presensi_siswa.id_siswa')
                    ->where('tb_siswa.id_kelas', $idKelas);
        }

        return $builder->countAllResults();
    }

    /**
     * Mengambil tren kehadiran untuk grafik 7 hari terakhir (Sudah mendukung tracking PKL).
     */
    public function getAttendanceTrend(int $days = 7, $idKelas = null): array
    {
        $now = Time::now('Asia/Jakarta');
        $result = [
            'hadir' => [], 'sakit' => [], 'izin' => [], 'alfa' => [], 'pkl' => [], 'belum' => []
        ];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = $now->subDays($i)->toDateString();

            $result['hadir'][] = $this->countPresensiByStatus('1', $date, $idKelas);
            $result['sakit'][] = $this->countPresensiByStatus('2', $date, $idKelas);
            $result['izin'][]  = $this->countPresensiByStatus('3', $date, $idKelas);
            $result['alfa'][]  = $this->countPresensiByStatus('4', $date, $idKelas);
            $result['pkl'][]   = $this->countPresensiByStatus('6', $date, $idKelas); // Sinkronisasi database data PKL (ID: 6)
            $result['belum'][] = $this->countPresensiByStatus('5', $date, $idKelas);
        }

        return $result;
    }

    /**
     * Update atau Insert data presensi secara manual.
     * PERBAIKAN: Menangani konversi jam kosong ke NULL, otomatis membersihkan jam jika status PKL (6).
     */
    public function updatePresensi($idPresensi, $idSiswa, $idKelas, $tanggal, $idKehadiran, $jamMasuk, $jamKeluar, $keterangan) 
    {
        // Validasi mendasar string kosong waktu
        $valJamMasuk  = (!empty($jamMasuk) && $jamMasuk !== '00:00:00') ? $jamMasuk : null;
        $valJamKeluar = (!empty($jamKeluar) && $jamKeluar !== '00:00:00') ? $jamKeluar : null;

        // KUNCI UTAMA: Jika status yang dikirim adalah PKL (6), paksa log jam masuk & pulang menjadi NULL
        if ($idKehadiran == 6) {
            $valJamMasuk  = null;
            $valJamKeluar = null;
        }

        $data = [
            'id_siswa'     => $idSiswa,
            'id_kelas'     => $idKelas,
            'tanggal'      => $tanggal,
            'id_kehadiran' => $idKehadiran,
            'keterangan'   => $keterangan ?? '',
            'jam_masuk'    => $valJamMasuk,
            'jam_keluar'   => $valJamKeluar
        ];

        // Logika tambahan: Jika status bukan Hadir (Sakit/Izin/Alfa), paksa jam_masuk jadi NULL 
        // kecuali admin mengisi jam tersebut secara spesifik.
        if ($idKehadiran != 1 && $idKehadiran != 6 && empty($jamMasuk)) {
            $data['jam_masuk'] = null;
        }

        // Simpan data (Insert jika idPresensi null/-1, Update jika idPresensi ada di row database)
        if ($idPresensi != null && $idPresensi != -1) {
            $data[$this->primaryKey] = $idPresensi;
        }

        return $this->save($data);
    }
}