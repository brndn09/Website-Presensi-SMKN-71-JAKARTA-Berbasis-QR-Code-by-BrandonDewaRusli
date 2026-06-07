<?php

namespace App\Models;

use CodeIgniter\Model;

class GuruModel extends Model
{
    // 1. Sesuaikan allowedFields agar hanya kolom yang digunakan yang bisa diisi
    protected $allowedFields = [
        'nuptk',
        'nama_guru',
        'jenis_kelamin',
        'unique_code'
    ];

    protected $table = 'tb_guru';

    protected $primaryKey = 'id_guru';

    /**
     * Mengambil semua data guru urut berdasarkan nama
     */
    public function getAllGuru()
    {
        return $this->orderBy('nama_guru', 'ASC')->findAll();
    }

    /**
     * Mengambil satu data guru berdasarkan ID
     */
    public function getGuruById($id)
    {
        return $this->where([$this->primaryKey => $id])->first();
    }

    /**
     * Menambah data guru baru
     * Parameter disederhanakan: hanya nuptk, nama, dan jenisKelamin
     */
    public function createGuru($nuptk, $nama, $jenisKelamin)
    {
        return $this->save([
            'nuptk'         => $nuptk,
            'nama_guru'     => $nama,
            'jenis_kelamin' => $jenisKelamin,
            // Generate unique_code sederhana karena no_hp sudah dihapus
            'unique_code'   => sha1($nama . md5($nuptk . time())) . substr(sha1($nuptk . rand(0, 100)), 0, 10),
        ]);
    }

    /**
     * Mengupdate data guru
     * Parameter disederhanakan agar sesuai dengan panggilan di Controller
     */
    public function updateGuru($id, $nuptk, $nama, $jenisKelamin)
    {
        return $this->update($id, [
            'nuptk'         => $nuptk,
            'nama_guru'     => $nama,
            'jenis_kelamin' => $jenisKelamin,
        ]);
    }

    /**
     * Method cekGuru disesuaikan karena rfid_code sudah tidak digunakan
     */
    public function cekGuru(string $unique_code)
    {
        return $this->where(['unique_code' => $unique_code])
                    ->first();
    }
}