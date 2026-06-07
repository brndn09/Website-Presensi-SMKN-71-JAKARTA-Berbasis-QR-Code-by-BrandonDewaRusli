<?php

namespace App\Controllers\Admin;

use App\Models\SiswaModel;
use App\Models\KelasModel;
use App\Controllers\BaseController;
use App\Models\JurusanModel;
use App\Models\UploadModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class DataSiswa extends BaseController
{
    protected SiswaModel $siswaModel;
    protected KelasModel $kelasModel;
    protected JurusanModel $jurusanModel;

    /**
     * Aturan validasi dipindahkan ke property agar mudah dikelola.
     * Rule 'is_unique' akan disuntikkan secara dinamis di method save/update.
     */
    protected $siswaValidationRules = [
        'nis' => [
            'rules'  => 'required|max_length[20]|min_length[4]',
            'errors' => [
                'required'   => 'NIS harus diisi.',
                'is_unique'  => 'NIS ini telah terdaftar.',
                'min_length' => 'Panjang NIS minimal 4 karakter'
            ]
        ],
        'nama' => [
            'rules'  => 'required|min_length[3]',
            'errors' => [
                'required' => 'Nama harus diisi'
            ]
        ],
        'id_kelas' => [
            'rules'  => 'required',
            'errors' => [
                'required' => 'Kelas harus diisi'
            ]
        ],
        'jk' => [
            'rules'  => 'required', 
            'errors' => [
                'required' => 'Jenis kelamin wajib diisi'
            ]
        ],
        'no_hp' => [
            'rules'  => 'required|numeric|max_length[20]|min_length[5]',
            'errors' => [
                'required' => 'Nomor HP harus diisi',
                'numeric'  => 'Nomor HP harus berupa angka'
            ]
        ],
        'rfid' => [
            'rules'  => 'permit_empty', 
            'errors' => [
                'is_unique' => 'Kode RFID sudah digunakan oleh siswa lain.'
            ]
        ]
    ];

    public function __construct()
    {
        $this->siswaModel = new SiswaModel();
        $this->kelasModel = new KelasModel();
        $this->jurusanModel = new JurusanModel();
        
        // Load helpers
        helper(['form', 'url', 'custom']); 
    }

    public function index()
    {
        if (!is_superadmin()) {
            return redirect()->to('admin');
        }

        $data = [
            'title'       => 'Data Siswa',
            'ctx'         => 'siswa',
            'tingkat'     => $this->kelasModel->getDistinctTingkat(),
            'jurusan'     => $this->jurusanModel->getDataJurusan(),
            'index_kelas' => $this->kelasModel->getDistinctIndexKelas()
        ];

        return view('admin/data/data-siswa', $data);
    }

    /**
     * PERBAIKAN UTAMA: Intercept Request Bulk Delete Melalui Jalur Pencarian Data (Tanpa Route Baru)
     */
    public function ambilDataSiswa()
    {
        // 1. Cek Apakah Request Merupakan Aksi Bulk Delete Terpilih
        $action = $this->request->getPost('action');
        if ($action === 'bulk_delete') {
            $siswaIds = $this->request->getPost('ids');
            
            if (!empty($siswaIds) && is_array($siswaIds)) {
                // Eksekusi penghapusan massal data siswa menggunakan Primary Key (id_siswa)
                $proses = $this->siswaModel->delete($siswaIds);

                if ($proses) {
                    return $this->response->setJSON([
                        'status'    => 'success',
                        'message'   => count($siswaIds) . ' data siswa terpilih berhasil dihapus.',
                        'csrf_hash' => csrf_hash() // Mengirim balik token terbaru agar form tidak expired
                    ]);
                }
            }

            return $this->response->setJSON([
                'status'    => 'error',
                'message'   => 'Gagal menghapus data, pastikan data siswa telah dipilih.',
                'csrf_hash' => csrf_hash()
            ]);
        }

        // 2. Jika Bukan Bulk Delete, Jalankan Logika Asli Anda (Pencarian & Ambil Data Tabel)
        $kelas   = $this->request->getVar('kelas') ?? null;
        $jurusan = $this->request->getVar('jurusan') ?? null;
        $index   = $this->request->getVar('index') ?? null;
        $keyword = $this->request->getVar('keyword') ?? null;

        $result = $this->siswaModel->getAllSiswaWithKelas($kelas, $jurusan, $index, $keyword);

        $data = [
            'data'  => $result,
            'empty' => empty($result)
        ];

        return view('admin/data/list-data-siswa', $data);
    }

    public function formTambahSiswa()
    {
        $data = [
            'ctx'   => 'siswa',
            'kelas' => $this->kelasModel->getDataKelas(),
            'title' => 'Tambah Data Siswa'
        ];

        return view('admin/data/create/create-data-siswa', $data);
    }

    public function saveSiswa()
    {
        // Set rules unik untuk tambah data baru
        $this->siswaValidationRules['nis']['rules']  .= '|is_unique[tb_siswa.nis]';
        $this->siswaValidationRules['rfid']['rules']  = 'permit_empty|is_unique[tb_siswa.rfid_code]';

        if (!$this->validate($this->siswaValidationRules)) {
            $data = [
                'ctx'        => 'siswa',
                'kelas'      => $this->kelasModel->getDataKelas(),
                'title'      => 'Tambah Data Siswa',
                'validation' => $this->validator,
                'oldInput'   => $this->request->getVar()
            ];
            return view('admin/data/create/create-data-siswa', $data);
        }

        $result = $this->siswaModel->createSiswa(
            $this->request->getVar('nis'),
            $this->request->getVar('nama'),
            intval($this->request->getVar('id_kelas')),
            $this->request->getVar('jk'),
            $this->request->getVar('no_hp'),
            $this->request->getVar('rfid')
        );

        if ($result) {
            session()->setFlashdata(['msg' => 'Tambah data berhasil', 'error' => false]);
            return redirect()->to('/admin/siswa');
        }

        session()->setFlashdata(['msg' => 'Gagal menambah data', 'error' => true]);
        return redirect()->to('/admin/siswa/create')->withInput();
    }

    public function formEditSiswa($id)
    {
        $siswa = $this->siswaModel->getSiswaById($id);
        if (empty($siswa)) {
            throw new PageNotFoundException('Data siswa dengan id ' . $id . ' tidak ditemukan');
        }

        $data = [
            'data'  => $siswa,
            'kelas' => $this->kelasModel->getDataKelas(),
            'ctx'   => 'siswa',
            'title' => 'Edit Siswa',
        ];

        return view('admin/data/edit/edit-data-siswa', $data);
    }

    public function updateSiswa()
    {
        $idSiswa   = $this->request->getVar('id');
        $siswaLama = $this->siswaModel->getSiswaById($idSiswa);

        if (empty($siswaLama)) {
             throw new PageNotFoundException('Siswa tidak ditemukan');
        }

        /**
         * Penanganan Validasi Unik saat Update:
         * Gunakan parameter ketiga is_unique untuk mengabaikan ID saat ini (ignore).
         * Format: is_unique[tabel.kolom, field_id, nilai_id]
         */
        
        // NIS Validation
        $this->siswaValidationRules['nis']['rules'] .= "|is_unique[tb_siswa.nis,id_siswa,{$idSiswa}]";
        
        // RFID Validation (Menggunakan kolom 'rfid_code' sesuai struktur database Anda)
        $this->siswaValidationRules['rfid']['rules'] = "permit_empty|is_unique[tb_siswa.rfid_code,id_siswa,{$idSiswa}]";

        if (!$this->validate($this->siswaValidationRules)) {
            $data = [
                'data'       => $siswaLama,
                'kelas'      => $this->kelasModel->getDataKelas(),
                'ctx'        => 'siswa',
                'title'      => 'Edit Siswa',
                'validation' => $this->validator,
                'oldInput'   => $this->request->getVar()
            ];
            return view('admin/data/edit/edit-data-siswa', $data);
        }

        $result = $this->siswaModel->updateSiswa(
            $idSiswa,
            $this->request->getVar('nis'),
            $this->request->getVar('nama'),
            intval($this->request->getVar('id_kelas')),
            $this->request->getVar('jk'),
            $this->request->getVar('no_hp'),
            $this->request->getVar('rfid')
        );

        if ($result) {
            session()->setFlashdata(['msg' => 'Edit data berhasil', 'error' => false]);
            return redirect()->to('/admin/siswa');
        }

        session()->setFlashdata(['msg' => 'Gagal mengubah data', 'error' => true]);
        return redirect()->to('/admin/siswa/edit/' . $idSiswa)->withInput();
    }

    public function delete($id)
    {
        $result = $this->siswaModel->delete($id);

        if ($result) {
            session()->setFlashdata(['msg' => 'Data berhasil dihapus', 'error' => false]);
        } else {
            session()->setFlashdata(['msg' => 'Gagal menghapus data', 'error' => true]);
        }
        return redirect()->to('/admin/siswa');
    }

    /**
     * Method ini bisa dibiarkan kosong atau dihapus karena fungsionalitasnya
     * sudah disatukan ke dalam method ambilDataSiswa di atas demi menghindari rute baru.
     */
    public function deleteSelectedSiswa()
    {
        return redirect()->to('/admin/siswa');
    }

    public function bulkPostSiswa()
    {
        $data = [
            'title' => 'Import Siswa',
            'ctx'   => 'siswa',
            'kelas' => $this->kelasModel->getDataKelas()
        ];

        return view('admin/data/import-siswa', $data);
    }

    public function generateCSVObjectPost()
    {
        $uploadModel = new UploadModel();
        
        // Bersihkan tmp files
        $files = glob(FCPATH . 'uploads/tmp/*.txt');
        if (!empty($files)) {
            foreach ($files as $item) {
                if (is_file($item)) @unlink($item);
            }
        }
        
        $file = $uploadModel->uploadCSVFile('file');
        if (!empty($file) && !empty($file['path'])) {
            $obj = $this->siswaModel->generateCSVObject($file['path']);
            if (!empty($obj)) {
                return $this->response->setJSON([
                    'result'        => 1,
                    'numberOfItems' => $obj->numberOfItems,
                    'txtFileName'   => $obj->txtFileName,
                ]);
            }
        }
        return $this->response->setJSON(['result' => 0]);
    }

    public function importCSVItemPost()
    {
        $txtFileName = $this->request->getPost('txtFileName');
        $index       = $this->request->getPost('index');
        $siswa       = $this->siswaModel->importCSVItem($txtFileName, $index);
        
        if (!empty($siswa)) {
            return $this->response->setJSON([
                'result' => 1,
                'siswa'  => $siswa,
                'index'  => $index
            ]);
        } 
        
        return $this->response->setJSON([
            'result' => 0,
            'index'  => $index
        ]);
    }

    public function downloadCSVFilePost()
    {
        $submit   = $this->request->getPost('submit');
        $response = \Config\Services::response();
        
        if ($submit == 'csv_siswa_template') {
            return $response->download(FCPATH . 'assets/file/csv_siswa_template.csv', null);
        } elseif ($submit == 'csv_guru_template') {
            return $response->download(FCPATH . 'assets/file/csv_guru_template.csv', null);
        }
    }

    /**
     * FITUR BARU: PROSES KELULUSAN KELAS 12 & KENAIKAN KELAS MASSAL 10-11
     * Method ini memanggil fungsi transaksi dari SiswaModel untuk:
     * 1. Menghapus data siswa kelas 12 yang sudah lulus.
     * 2. Menggeser tingkat kelas 11 ke 12, dan kelas 10 ke 11 secara massal.
     */
    public function kenaikanKelasMassal()
    {
        if (!is_superadmin()) {
            return redirect()->to('admin');
        }

        // Mengeksekusi query relasi kenaikan kelas dari SiswaModel
        $proses = $this->siswaModel->prosesKenaikanKelas();

        if ($proses) {
            session()->setFlashdata(['msg' => 'Proses Kelulusan Kelas 12 & Kenaikan Kelas Berhasil!', 'error' => false]);
        } else {
            session()->setFlashdata(['msg' => 'Gagal memproses kelulusan dan kenaikan kelas.', 'error' => true]);
        }

        return redirect()->to('/admin/siswa');
    }
}