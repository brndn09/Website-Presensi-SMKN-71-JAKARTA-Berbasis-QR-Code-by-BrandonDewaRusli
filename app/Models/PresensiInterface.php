<?php

namespace App\Models;

use CodeIgniter\I18n\Time;

/**
 * Interface PresensiInterface
 * * Kontrak standar untuk model presensi (Siswa maupun Guru).
 * Memastikan semua fungsi inti manajemen absensi tersedia.
 */
interface PresensiInterface
{
    /**
     * Mengecek apakah user sudah memiliki data absensi pada tanggal tertentu.
     * @return mixed ID presensi jika ditemukan, false jika tidak.
     */
    public function cekAbsen(string|int $id, string|Time $date);

    /**
     * Mencatat data absen masuk pertama kali.
     */
    public function absenMasuk(string $id, $date, $time, $idKelas = '');

    /**
     * Memperbarui data jam keluar pada record yang sudah ada.
     */
    public function absenKeluar(string $id, $time);

    /**
     * Mendapatkan satu baris data presensi berdasarkan ID Primary Key.
     */
    public function getPresensiById(string $idPresensi);

    /**
     * Mendapatkan statistik atau list data berdasarkan kategori kehadiran (Hadir, Sakit, Izin, Alfa).
     * * PERBAIKAN DOKUMENTASI: 
     * @param string $idKehadiran ID status (1-5)
     * @param string|Time $tanggal Tanggal absensi yang dicek
     * @param string|int|null $idKelas ID kelas spesifik, atau string 'all' untuk kalkulasi semua kelas.
     */
    public function getPresensiByKehadiran(string $idKehadiran, $tanggal, $idKelas = null);

    /**
     * Memperbarui data presensi secara manual (biasanya oleh Admin).
     */
    public function updatePresensi($idPresensi, $idSiswa, $idKelas, $tanggal, $idKehadiran, $jamMasuk, $jamKeluar, $keterangan);
}