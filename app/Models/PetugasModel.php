<?php

namespace App\Models;

use CodeIgniter\Model;
use Myth\Auth\Password;

class PetugasModel extends Model
{
    protected $table      = 'users';
    protected $primaryKey = 'id';
    protected $returnType = 'array';

    // Mendefinisikan kolom yang boleh dimanipulasi oleh Model
    protected $allowedFields = [
        'email',
        'username',
        'password_hash',
        'reset_hash',
        'reset_at',
        'reset_expires',
        'activate_hash',
        'status',
        'status_message',
        'active',
        'force_pass_reset',
        'is_superadmin',
        'id_guru',
        'reset_otp' // Kolom tambahan untuk fitur OTP
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Mengambil semua data petugas beserta nama guru jika terhubung.
     */
    public function getAllPetugas()
    {
        return $this->select('users.*, tb_guru.nama_guru')
            ->join('tb_guru', 'users.id_guru = tb_guru.id_guru', 'left')
            ->findAll();
    }

    /**
     * Mengambil satu data petugas berdasarkan ID.
     */
    public function getPetugasById($id)
    {
        return $this->where([$this->primaryKey => $id])->first();
    }

    /**
     * Fungsi untuk menyimpan atau mengupdate data petugas (Register/Edit Admin).
     */
    public function savePetugas($idPetugas, $email, $username, $passwordHash, $role, $id_guru = null, $active = 1)
    {
        return $this->save([
            $this->primaryKey => $idPetugas,
            'email'           => $email,
            'username'        => $username,
            'password_hash'   => $passwordHash,
            'is_superadmin'   => $role ?? '0',
            'id_guru'         => $id_guru,
            'active'          => $active
        ]);
    }

    // --- FUNGSI UNTUK IMPORT CSV ---

    /**
     * Memproses file CSV menjadi objek JSON sementara untuk antrean import.
     */
    public function generateCSVObject($filePath)
    {
        $array = array();
        $fields = array();
        $txtName = uniqid() . '.txt';
        $i = 0;
        $handle = fopen($filePath, 'r');

        if ($handle) {
            while (($row = fgetcsv($handle)) !== false) {
                if (empty($fields)) {
                    $fields = $row;
                    $bom = pack('H*', 'EFBBBF');
                    $fields[0] = preg_replace("/^$bom/", '', $fields[0]);
                    $fields = array_map('trim', $fields);
                    continue;
                }
                foreach ($row as $k => $value) {
                    $array[$i][$fields[$k]] = $value;
                }
                $i++;
            }
            if (!feof($handle)) {
                return false;
            }
            fclose($handle);

            if (!empty($array)) {
                if (!is_dir(FCPATH . 'uploads/tmp/')) {
                    mkdir(FCPATH . 'uploads/tmp/', 0777, true);
                }
                $txtFile = fopen(FCPATH . 'uploads/tmp/' . $txtName, 'w');
                fwrite($txtFile, json_encode($array));
                fclose($txtFile);

                $obj = new \stdClass();
                $obj->numberOfItems = count($array);
                $obj->txtFileName = $txtName;

                if (file_exists($filePath)) {
                    unlink($filePath);
                }

                return $obj;
            }
        }
        return false;
    }

    /**
     * Mengambil item dari JSON sementara dan memasukkannya ke Database.
     */
    public function importCSVItem($txtFileName, $index)
    {
        if (!preg_match('/^[a-zA-Z0-9_\-\.]+$/', $txtFileName) || strpos($txtFileName, '..') !== false) {
            return null;
        }

        $filePath = FCPATH . 'uploads/tmp/' . $txtFileName;
        if (!file_exists($filePath)) {
            return null;
        }

        $content = file_get_contents($filePath);
        $array = json_decode($content, true);

        if (!empty($array)) {
            $i = 1;
            foreach ($array as $item) {
                if ($i == $index) {
                    $data = [
                        'username'      => getCSVInputValue($item, 'username'),
                        'email'         => getCSVInputValue($item, 'email'),
                        'is_superadmin' => getCSVInputValue($item, 'role', 'int'),
                        'active'        => 1
                    ];

                    // Validasi Dasar
                    if (empty($data['username']) || empty($data['email'])) return null;

                    // Hash Password
                    $password = getCSVInputValue($item, 'password');
                    if (empty($password)) return null;
                    $data['password_hash'] = Password::hash($password);

                    // Cek Guru
                    $idGuru = getCSVInputValue($item, 'id_guru', 'int');
                    if (!empty($idGuru)) {
                        $guruModel = new \App\Models\GuruModel();
                        if ($guruModel->find($idGuru)) {
                            $data['id_guru'] = $idGuru;
                        }
                    }

                    // Cek Duplikasi
                    $existing = $this->where('email', $data['email'])
                                     ->orWhere('username', $data['username'])
                                     ->first();
                    if ($existing) return null;

                    $this->insert($data);
                    
                    unset($data['password_hash']);
                    return $data;
                }
                $i++;
            }
        }
        return null;
    }
}