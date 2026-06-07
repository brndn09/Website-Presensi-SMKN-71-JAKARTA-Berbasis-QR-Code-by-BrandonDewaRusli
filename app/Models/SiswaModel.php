<?php

namespace App\Models;

use CodeIgniter\Model;

class SiswaModel extends Model
{
    protected $table = 'tb_siswa';
    protected $primaryKey = 'id_siswa';

    protected function initialize()
    {
        $this->allowedFields = [
            'nis',
            'nama_siswa',
            'id_kelas',
            'jenis_kelamin',
            'no_hp',
            'unique_code',
            'rfid_code'
        ];
    }

    /**
     * Mengecek data siswa berdasarkan unique_code atau rfid_code (untuk scan/absensi)
     */
    public function cekSiswa(string $unique_code)
    {
        $this->select('tb_siswa.*, tb_kelas.tingkat, tb_kelas.index_kelas, tb_jurusan.jurusan, CONCAT(tb_kelas.tingkat, " ", tb_jurusan.jurusan, " ", tb_kelas.index_kelas) as kelas')
            ->join(
                'tb_kelas',
                'tb_kelas.id_kelas = tb_siswa.id_kelas',
                'LEFT'
            )->join(
                'tb_jurusan',
                'tb_jurusan.id = tb_kelas.id_jurusan',
                'LEFT'
            );
        return $this->where(['unique_code' => $unique_code])
            ->orWhere(['rfid_code' => $unique_code])
            ->first();
    }

    /**
     * Mendapatkan data siswa berdasarkan ID
     */
    public function getSiswaById($id)
    {
        return $this->where([$this->primaryKey => $id])->first();
    }

    /**
     * Mendapatkan semua data siswa dengan join kelas dan jurusan, serta filter pencarian
     */
    public function getAllSiswaWithKelas($kelas = null, $jurusan = null, $index = null, $keyword = null)
    {
        $query = $this->select('tb_siswa.*, tb_kelas.tingkat, tb_kelas.index_kelas, tb_jurusan.jurusan, CONCAT(tb_kelas.tingkat, " ", tb_jurusan.jurusan, " ", tb_kelas.index_kelas) as kelas')
            ->join(
                'tb_kelas',
                'tb_kelas.id_kelas = tb_siswa.id_kelas',
                'LEFT'
            )->join(
                'tb_jurusan',
                'tb_kelas.id_jurusan = tb_jurusan.id',
                'LEFT'
            );

        if (!empty($keyword)) {
            $query->groupStart()
                ->like('tb_siswa.nama_siswa', $keyword)
                ->orLike('tb_siswa.nis', $keyword)
                ->groupEnd();
        }

        if (!empty($kelas)) {
            $query->where('tb_kelas.tingkat', $kelas);
        }
        if (!empty($jurusan)) {
            $query->where('tb_jurusan.jurusan', $jurusan);
        }
        if (!empty($index)) {
            $query->where('tb_kelas.index_kelas', $index);
        }

        return $query->orderBy('nama_siswa')->findAll();
    }

    /**
     * Mendapatkan data siswa per kelas tertentu
     */
    public function getSiswaByKelas($id_kelas)
    {
        return $this->select('tb_siswa.*, tb_kelas.tingkat, tb_kelas.index_kelas, tb_jurusan.jurusan, CONCAT(tb_kelas.tingkat, " ", tb_jurusan.jurusan, " ", tb_kelas.index_kelas) as kelas')
            ->join(
                'tb_kelas',
                'tb_kelas.id_kelas = tb_siswa.id_kelas',
                'LEFT'
            )
            ->join('tb_jurusan', 'tb_kelas.id_jurusan = tb_jurusan.id', 'left')
            ->where(['tb_siswa.id_kelas' => $id_kelas])
            ->orderBy('nama_siswa')
            ->findAll();
    }

    /**
     * Membuat data siswa baru secara manual
     */
    public function createSiswa($nis, $nama, $idKelas, $jenisKelamin, $noHp, $rfid = null)
    {
        return $this->save([
            'nis' => $nis,
            'nama_siswa' => $nama,
            'id_kelas' => $idKelas,
            'jenis_kelamin' => $jenisKelamin,
            'no_hp' => $noHp,
            'unique_code' => generateToken(),
            'rfid_code' => $rfid
        ]);
    }

    /**
     * Mengupdate data siswa
     */
    public function updateSiswa($id, $nis, $nama, $idKelas, $jenisKelamin, $noHp, $rfid = null)
    {
        return $this->save([
            $this->primaryKey => $id,
            'nis' => $nis,
            'nama_siswa' => $nama,
            'id_kelas' => $idKelas,
            'jenis_kelamin' => $jenisKelamin,
            'no_hp' => $noHp,
            'rfid_code' => $rfid
        ]);
    }

    /**
     * Menghitung jumlah siswa per kelas
     */
    public function getSiswaCountByKelas($kelasId = null)
    {
        if (empty($kelasId)) {
            return $this->countAllResults();
        }

        $tree = array();
        $kelasId = cleanNumber($kelasId);
        if (!empty($kelasId)) {
            array_push($tree, $kelasId);
        }

        $kelasIds = $tree;
        if (count($kelasIds) < 1) {
            return 0;
        }

        return $this->whereIn('tb_siswa.id_kelas', $kelasIds, false)->countAllResults();
    }

    /**
     * FITUR IMPORT: Tahap 1 - Mengambil file CSV dan mengubahnya menjadi serialized array (TXT)
     * Ditambahkan fitur Auto-Detect Delimiter (Koma atau Titik Koma)
     */
    public function generateCSVObject($filePath)
    {
        $array = array();
        $fields = array();
        $txtName = uniqid() . '.txt';
        $i = 0;
        $handle = fopen($filePath, 'r');
        
        if ($handle) {
            // DETEKSI OTOMATIS: Ambil baris pertama untuk menentukan separator (; atau ,)
            $firstLine = fgets($handle);
            $separator = (strpos($firstLine, ';') !== false) ? ';' : ',';
            rewind($handle); // Kembali ke awal file

            while (($row = fgetcsv($handle, 0, $separator)) !== false) {
                if (empty($fields)) {
                    // Bersihkan karakter UTF-8 BOM jika ada agar header "nis" terbaca bersih
                    $row[0] = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $row[0]);
                    $fields = $row;
                    continue;
                }
                foreach ($row as $k => $value) {
                    // Hanya masukkan data jika kolom header ada
                    if (isset($fields[$k])) {
                        $array[$i][$fields[$k]] = trim($value);
                    }
                }
                $i++;
            }
            
            if (!feof($handle)) {
                fclose($handle);
                return false;
            }
            fclose($handle);

            if (!empty($array)) {
                $dir = FCPATH . 'uploads/tmp/';
                if (!is_dir($dir)) mkdir($dir, 0777, true);

                $txtFile = fopen($dir . $txtName, 'w');
                fwrite($txtFile, serialize($array));
                fclose($txtFile);

                $obj = new \stdClass();
                $obj->numberOfItems = count($array);
                $obj->txtFileName = $txtName;
                @unlink($filePath);
                return $obj;
            }
        }
        return false;
    }

    /**
     * FITUR IMPORT: Tahap 2 - Import data per baris ke database
     */
    public function importCSVItem($txtFileName, $index)
    {
        $filePath = FCPATH . 'uploads/tmp/' . $txtFileName;
        if (!file_exists($filePath)) return false;

        $content = file_get_contents($filePath);
        $array = @unserialize($content);

        if (!empty($array)) {
            // Index dari frontend dimulai dari 1, array dimulai dari 0
            $itemIndex = $index - 1;

            if (isset($array[$itemIndex])) {
                $item = $array[$itemIndex];
                
                $data = array();
                $data['nis']           = getCSVInputValue($item, 'nis', 'int');
                $data['nama_siswa']    = getCSVInputValue($item, 'nama_siswa');
                $data['id_kelas']      = getCSVInputValue($item, 'id_kelas', 'int');
                $data['jenis_kelamin'] = getCSVInputValue($item, 'jenis_kelamin');
                $data['no_hp']         = getCSVInputValue($item, 'no_hp');
                $data['unique_code']   = generateToken(true);
                $data['rfid_code']     = null; // Inisialisasi rfid kosong saat import

                if ($this->insert($data)) {
                    return $data;
                }
            }
        }
        return false;
    }

    /**
     * Mendapatkan record tunggal baris data siswa (Object)
     */
    public function getSiswa($id)
    {
        return $this->where('id_siswa', cleanNumber($id))->get()->getRow();
    }

    /**
     * Menghapus satu data siswa
     */
    public function deleteSiswa($id)
    {
        $siswa = $this->getSiswa($id);
        if (!empty($siswa)) {
            return $this->where('id_siswa', $siswa->id_siswa)->delete();
        }
        return false;
    }

    /**
     * Menghapus banyak siswa berdasarkan array ID (Bulk Delete)
     */
    public function deleteMultiSelected($siswaIds)
    {
        if (!empty($siswaIds) && is_array($siswaIds)) {
            return $this->whereIn('id_siswa', $siswaIds)->delete();
        }
        return false;
    }

    /**
     * FITUR BARU: PROSES KELULUSAN & KENAIKAN KELAS MASSAL
     * Menggunakan Database Transaction untuk keamanan data
     */
    public function prosesKenaikanKelas()
    {
        $db = \Config\Database::connect();
        $db->transStart(); 

        // 1. Ambil ID Kelas XII lalu hapus siswa (Lulus)
        $kelas12 = $db->table('tb_kelas')->select('id_kelas')->where('tingkat', 'XII')->get()->getResultArray();
        $idKelas12 = array_column($kelas12, 'id_kelas');
        
        if (!empty($idKelas12)) {
            $db->table('tb_siswa')->whereIn('id_kelas', $idKelas12)->delete();
        }

        // 2. Naikkan siswa Kelas XI ke XII (Berdasarkan kesamaan Jurusan dan Index)
        $db->query("
            UPDATE tb_siswa s
            JOIN tb_kelas k_asal ON s.id_kelas = k_asal.id_kelas
            JOIN tb_kelas k_tujuan ON k_asal.id_jurusan = k_tujuan.id_jurusan 
                AND k_asal.index_kelas = k_tujuan.index_kelas
            SET s.id_kelas = k_tujuan.id_kelas
            WHERE k_asal.tingkat = 'XI' AND k_tujuan.tingkat = 'XII'
        ");

        // 3. Naikkan siswa Kelas X ke XI
        $db->query("
            UPDATE tb_siswa s
            JOIN tb_kelas k_asal ON s.id_kelas = k_asal.id_kelas
            JOIN tb_kelas k_tujuan ON k_asal.id_jurusan = k_tujuan.id_jurusan 
                AND k_asal.index_kelas = k_tujuan.index_kelas
            SET s.id_kelas = k_tujuan.id_kelas
            WHERE k_asal.tingkat = 'X' AND k_tujuan.tingkat = 'XI'
        ");

        $db->transComplete();

        return $db->transStatus(); 
    }
}