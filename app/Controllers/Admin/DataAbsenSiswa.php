<?php

namespace App\Controllers\Admin;

use App\Models\KelasModel;
use App\Models\SiswaModel;
use App\Controllers\BaseController;
use App\Models\KehadiranModel;
use App\Models\PresensiSiswaModel;
use CodeIgniter\I18n\Time;

class DataAbsenSiswa extends BaseController
{
   protected KelasModel $kelasModel;
   protected SiswaModel $siswaModel;
   protected KehadiranModel $kehadiranModel;
   protected PresensiSiswaModel $presensiSiswa;
   protected string $currentDate;

   public function __construct()
   {
      $this->currentDate = Time::today()->toDateString();
      $this->siswaModel = new SiswaModel();
      $this->kehadiranModel = new KehadiranModel();
      $this->kelasModel = new KelasModel();
      $this->presensiSiswa = new PresensiSiswaModel();
   }

   public function index()
   {
      $kelas = $this->kelasModel->getDataKelas();

      $data = [
         'title' => 'Presensi Siswa',
         'ctx' => 'absen-siswa',
         'kelas' => $kelas
      ];

      return view('admin/absen/absen-siswa', $data);
   }

   public function ambilDataSiswa()
   {
      // ambil variabel POST
      $kelas = $this->request->getVar('kelas');
      $idKelas = $this->request->getVar('id_kelas');
      $tanggal = $this->request->getVar('tanggal');

      $lewat = Time::parse($tanggal)->isAfter(Time::today());

      // Gunakan query dasar yang sudah terbukti 100% SUKSES dan aman dari Error 500
      if ($idKelas === 'all') {
         $result = $this->presensiSiswa->getPresensiSemuaKelas($tanggal);
         
         // KUNCI UTAMA: Inject nama kelas asli ke masing-masing baris siswa secara real-time via KelasModel
         if (!empty($result)) {
            // Ambil semua daftar kelas yang valid dari database untuk mapping key-value
            $daftarKelasRaw = $this->kelasModel->getDataKelas();
            $mapKelas = [];
            foreach ($daftarKelasRaw as $k) {
                $mapKelas[$k['id_kelas']] = $k['kelas'];
            }

            // Pasangkan nama kelas ke setiap siswa berdasarkan id_kelas mereka
            foreach ($result as $key => $siswa) {
                $idKelasSiswa = $siswa['id_kelas'] ?? null;
                $result[$key]['nama_kelas'] = $mapKelas[$idKelasSiswa] ?? 'Tanpa Kelas';
            }
         }
      } else {
         $result = $this->presensiSiswa->getPresensiByKelasTanggal($idKelas, $tanggal);
      }

      $data = [
         'kelas' => $kelas,
         'data' => $result,
         'listKehadiran' => $this->kehadiranModel->getAllKehadiran(),
         'lewat' => $lewat
      ];

      return view('admin/absen/list-absen-siswa', $data);
   }

   public function ambilKehadiran()
   {
      $idPresensi = $this->request->getVar('id_presensi');
      $idSiswa = $this->request->getVar('id_siswa');

      $data = [
         'presensi' => $this->presensiSiswa->getPresensiById($idPresensi),
         'listKehadiran' => $this->kehadiranModel->getAllKehadiran(),
         'data' => $this->siswaModel->getSiswaById($idSiswa)
      ];

      return view('admin/absen/ubah-kehadiran-modal', $data);
   }

   public function ubahKehadiran()
   {
      // Ambil variabel POST
      $idKehadiran = $this->request->getVar('id_kehadiran');
      $idSiswa = $this->request->getVar('id_siswa');
      $idKelas = $this->request->getVar('id_kelas');
      $tanggal = $this->request->getVar('tanggal');
      $jamMasuk = $this->request->getVar('jam_masuk');
      $jamKeluar = $this->request->getVar('jam_keluar');
      $keterangan = $this->request->getVar('keterangan');

      // --- KUNCI PERBAIKAN: NORMALISASI INPUT DATA UNTUK PKL (ID: 6) ---
      if (empty($jamMasuk) || trim($jamMasuk) === '' || $idKehadiran == 6) {
          $valJamMasuk = null;
      } else {
          $valJamMasuk = $jamMasuk;
      }

      if (empty($jamKeluar) || trim($jamKeluar) === '' || $idKehadiran == 6) {
          $valJamKeluar = null;
      } else {
          $valJamKeluar = $jamKeluar;
      }

      // Berikan keterangan otomatis statis jika user memilih PKL dan catatan kosong
      if ($idKehadiran == 6 && empty(trim($keterangan))) {
          $keterangan = "Melaksanakan Praktik Kerja Lapangan (PKL)";
      }

      $cek = $this->presensiSiswa->cekAbsen($idSiswa, $tanggal);

      // Eksekusi Update data dengan parameter yang sudah dinormalisasi aman dari SQL Error 500
      $result = $this->presensiSiswa->updatePresensi(
         $cek == false ? NULL : $cek,
         $idSiswa,
         $idKelas,
         $tanggal,
         $idKehadiran,
         $valJamMasuk,
         $valJamKeluar,
         $keterangan
      );

      $siswa = $this->siswaModel->getSiswaById($idSiswa);
      $response['nama_siswa'] = $siswa['nama_siswa'] ?? 'Siswa';

      if ($result) {
         $response['status'] = TRUE;
      } else {
         $response['status'] = FALSE;
      }

      return $this->response->setJSON($response);
   }

   /**
    * METODE BARU: Menyimpan Status PKL Massal Untuk Satu Kelas Berdasarkan Rentang Tanggal
    */
   public function simpanPklMassal()
   {
      // Validasi input request POST via AJAX
      $idKelas = $this->request->getPost('id_kelas');
      $tanggalMulaiRaw = $this->request->getPost('tanggal_mulai');
      $tanggalSelesaiRaw = $this->request->getPost('tanggal_selesai');

      if (empty($idKelas) || empty($tanggalMulaiRaw) || empty($tanggalSelesaiRaw)) {
          return $this->response->setJSON([
              'status'  => false,
              'message' => 'Lengkapi seluruh data form kelas dan rentang tanggal!'
          ]);
      }

      // Konversi data string tanggal menjadi objek timestamp waktu
      $tsMulai = strtotime($tanggalMulaiRaw);
      $tsSelesai = strtotime($tanggalSelesaiRaw);

      if ($tsMulai > $tsSelesai) {
          return $this->response->setJSON([
              'status'  => false,
              'message' => 'Tanggal mulai tidak boleh melebihi tanggal selesai!'
          ]);
      }

      // Ambil daftar seluruh siswa yang berada di dalam kelas target
      // Catatan: sesuaikan nama method di SiswaModel Anda jika berbeda (misal: getSiswaByKelas)
      $daftarSiswa = $this->siswaModel->where('id_kelas', $idKelas)->findAll();

      if (empty($daftarSiswa)) {
          return $this->response->setJSON([
              'status'  => false,
              'message' => 'Tidak ditemukan data siswa aktif di dalam kelas tersebut!'
          ]);
      }

      $idKehadiranPkl = 6; // ID Enumerasi konstan untuk status PKL
      $keteranganPkl = "Melaksanakan Praktik Kerja Lapangan (PKL)";
      $suksesCounter = 0;

      // Loop hari demi hari (dari tanggal mulai sampai tanggal selesai)
      for ($currentTs = $tsMulai; $currentTs <= $tsSelesai; $currentTs = strtotime('+1 day', $currentTs)) {
          $tanggalTarget = date('Y-m-d', $currentTs);

          // Lakukan insert/update status PKL untuk setiap siswa di kelas tersebut
          foreach ($daftarSiswa as $siswa) {
              $idSiswa = $siswa['id_siswa'];

              // Cek apakah data log absensi siswa bersangkutan pada tanggal target sudah terwujud di database
              $idPresensiEksis = $this->presensiSiswa->cekAbsen($idSiswa, $tanggalTarget);

              $proses = $this->presensiSiswa->updatePresensi(
                  $idPresensiEksis == false ? NULL : $idPresensiEksis,
                  $idSiswa,
                  $idKelas,
                  $tanggalTarget,
                  $idKehadiranPkl,
                  null, // jam_masuk dipaksa NULL untuk status PKL
                  null, // jam_keluar dipaksa NULL untuk status PKL
                  $keteranganPkl
              );

              if ($proses) {
                  $suksesCounter++;
              }
          }
      }

      if ($suksesCounter > 0) {
          return $this->response->setJSON([
              'status'  => true,
              'message' => 'Berhasil menyetel status PKL massal untuk rentang tanggal tersebut.'
          ]);
      }

      return $this->response->setJSON([
          'status'  => false,
          'message' => 'Gagal memproses perubahan data absensi massal.'
      ]);
   }

   // ... Kode program method simpanPklMassal() Anda yang sebelumnya ...

   /**
    * METODE BARU: Cetak Data Siswa Berdasarkan Hasil Filter Real-Time Halaman Depan
    */
   public function cetakPresensiFilter()
   {
      $tanggal = $this->request->getPost('tanggal_cetak');
      $namaKelas = $this->request->getPost('nama_kelas');
      $filterTitle = $this->request->getPost('filter_title');
      $idSiswaList = $this->request->getPost('id_siswa_list');

      if (empty($idSiswaList) || !is_array($idSiswaList)) {
          return "<script>alert('Gagal mencetak, tidak ada data siswa yang terpilih!'); window.close();</script>";
      }

      // Ambil data mentah presensi menyeluruh untuk tanggal terpilih
      $allPresensi = ($namaKelas === 'Semua Kelas') ? 
                     $this->presensiSiswa->getPresensiSemuaKelas($tanggal) : 
                     $this->presensiSiswa->getPresensiSemuaKelas($tanggal); 
                     // Menggunakan query dasar semua kelas agar mapping data id_siswa fleksibel

      // Saring data database hanya untuk id_siswa yang dikirimkan oleh filter client view
      $filteredData = [];
      
      // Ambil mapping kelas untuk nama kelas jika mode 'Semua Kelas' aktif
      $daftarKelasRaw = $this->kelasModel->getDataKelas();
      $mapKelas = [];
      foreach ($daftarKelasRaw as $k) {
          $mapKelas[$k['id_kelas']] = $k['kelas'];
      }

      foreach ($allPresensi as $row) {
          if (in_array($row['id_siswa'], $idSiswaList)) {
              $idKelasSiswa = $row['id_kelas'] ?? null;
              $row['nama_kelas'] = $mapKelas[$idKelasSiswa] ?? $namaKelas;
              $filteredData[] = $row;
          }
      }

      // Mengirimkan variabel ke view cetak khusus
      $data = [
          'tanggal' => $tanggal,
          'nama_kelas' => $namaKelas,
          'filter_title' => $filterTitle,
          'data' => $filteredData
      ];

      return view('admin/absen/cetak-absen-filter', $data);
   }
} // Akhir dari Baris Class Controller
