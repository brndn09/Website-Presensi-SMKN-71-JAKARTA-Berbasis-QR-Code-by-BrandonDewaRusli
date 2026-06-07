<?php

namespace App\Controllers;

use CodeIgniter\I18n\Time;
use App\Models\GuruModel;
use App\Models\SiswaModel;
use App\Models\PresensiGuruModel;
use App\Models\PresensiSiswaModel;
use App\Libraries\enums\TipeUser;

class Scan extends BaseController
{
   private bool $WANotificationEnabled;

   protected SiswaModel $siswaModel;
   protected GuruModel $guruModel;
   protected PresensiSiswaModel $presensiSiswaModel;

   // Menyamakan batas waktu dengan list-absen-siswa.php
   private string $batasTerlambatMasuk = "06:31:00";
   
   // Batas waktu untuk keterlambatan absen pulang (5 sore)
   private string $batasTerlambatPulang = "15:30:00";

   public function __construct()
   {
      $this->WANotificationEnabled = getenv('WA_NOTIFICATION') === 'true' ? true : false;

      $this->siswaModel = new SiswaModel();
      $this->guruModel = new GuruModel();
      $this->presensiSiswaModel = new PresensiSiswaModel();
   }

   public function index($t = 'Masuk')
   {
      $data = ['waktu' => $t, 'title' => 'Presensi Siswa/Siswi SMKN 71 Jakarta'];
      return view('scan/scan', $data);
   }

   public function cekKode()
   {
      // Ambil variabel POST
      $uniqueCode = $this->request->getVar('unique_code');
      $waktuAbsen = $this->request->getVar('waktu');

      $status = false;
      $type = TipeUser::Siswa;

      // Cek data siswa di database
      $result = $this->siswaModel->cekSiswa($uniqueCode);

      if (empty($result)) {
         // Jika cek siswa gagal, cek data guru
         $result = $this->guruModel->cekGuru($uniqueCode);

         if (!empty($result)) {
            $status = true;
            $type = TipeUser::Guru;
         } else {
            $status = false;
            $result = NULL;
         }
      } else {
         $status = true;
      }

      if (!$status) {
         return $this->showErrorView('Data tidak ditemukan');
      }

      // KUNCI PERBAIKAN: Format nama menjadi Title Case (Kapital di awal kata) agar konsisten
      if (isset($result['nama_siswa'])) {
          $result['nama_siswa'] = ucwords(strtolower(trim($result['nama_siswa'])));
      } elseif (isset($result['nama_guru'])) {
          $result['nama_guru'] = ucwords(strtolower(trim($result['nama_guru'])));
      }

      // Jalankan fungsi berdasarkan waktu absen
      switch ($waktuAbsen) {
         case 'masuk':
            return $this->absenMasuk($type, $result);
         case 'pulang':
            return $this->absenPulang($type, $result);
         default:
            return $this->showErrorView('Cek lagi data QR Code nya, sudah sesuai belum.');
      }
   }

   public function absenMasuk($type, $result)
   {
      $data['data'] = $result;
      $data['waktu'] = 'masuk';

      $date = Time::today()->toDateString();
      $time = Time::now()->toTimeString();
      
      switch ($type) {
         case TipeUser::Siswa:
            $idSiswa = $result['id_siswa'];
            $idKelas = $result['id_kelas'];
            $data['type'] = TipeUser::Siswa;

            // Cek apakah sudah absen hari ini
            $sudahAbsen = $this->presensiSiswaModel->cekAbsen($idSiswa, $date);

            if ($sudahAbsen) {
               $data['presensi'] = $this->presensiSiswaModel->getPresensiById($sudahAbsen);
               return $this->showErrorView('Siswa sudah melakukan presensi masuk hari ini.', $data);
            }

            // Simpan data presensi ke database (Tetap berjalan normal)
            $this->presensiSiswaModel->absenMasuk($idSiswa, $date, $time, $idKelas);
            $data['presensi'] = $this->presensiSiswaModel->getPresensiByIdSiswaTanggal($idSiswa, $date);

            // --- LOGIKA KALKULASI TERLAMBAT MASUK (JAM, MENIT, DETIK) ---
            $masukTimestamp = strtotime($time);
            $batasTimestamp = strtotime($this->batasTerlambatMasuk);
            
            // JIKA TERLAMBAT: Susun pesan dan kirim WhatsApp
            if ($masukTimestamp > $batasTimestamp) {
                $selisihDetik = $masukTimestamp - $batasTimestamp;
                
                $jamTelat   = floor($selisihDetik / 3600);
                $menitTelat = floor(($selisihDetik % 3600) / 60);
                $detikTelat = $selisihDetik % 60;
                
                $statusTeks = "Terlambat";
                
                // Menyusun teks keterangan terlambat dengan dinamis
                $keteranganTerlambat = " (";
                if ($jamTelat > 0) {
                    $keteranganTerlambat .= $jamTelat . " jam ";
                }
                if ($menitTelat > 0 || $jamTelat > 0) {
                    $keteranganTerlambat .= $menitTelat . " menit ";
                }
                $keteranganTerlambat .= $detikTelat . " detik)";

                // Ambil data kelas asli dari database
                $kelasAsli = $result['nama_kelas'] ?? $result['kelas'] ?? '-';
                
                // PANGGIL FUNGSI KONVERSI ROMAWI KE ANGKA DI SINI
                $kelas = $this->konversiRomawiKeAngka($kelasAsli);

                // Susun Pesan WhatsApp khusus Terlambat Masuk
                $messageString = "Yth. Bapak/Ibu Orang Tua/Wali,\n\n";
                $messageString .= "Menginfokan bahwa siswa berikut telah melakukan *Presensi Masuk* namun tercatat *TERLAMBAT*:\n\n";
                $messageString .= "NIS: {$result['nis']}\n";
                $messageString .= "Nama: *{$result['nama_siswa']}*\n";
                $messageString .= "Kelas: {$kelas}\n"; 
                $messageString .= "Waktu: {$date} | {$time}\n";
                $messageString .= "Status: *{$statusTeks}*{$keteranganTerlambat}\n\n";
                $messageString .= "Mohon perhatiannya agar anak dapat hadir tepat waktu ke sekolah. Terima kasih.";

                // Kirim Notifikasi WA (Hanya dipicu jika masuk blok IF terlambat ini)
                if ($this->WANotificationEnabled && !empty($result['no_hp'])) {
                    $this->kirimNotifikasiWhatsApp($result['no_hp'], $messageString);
                }
            }

            break;

         default:
            return $this->showErrorView('Tipe pengguna tidak valid');
      }

      return view('scan/scan-result', $data);
   }

   public function absenPulang($type, $result)
   {
      $data['data'] = $result;
      $data['waktu'] = 'pulang';

      $date = Time::today()->toDateString();
      $time = Time::now()->toTimeString();

      switch ($type) {
         case TipeUser::Siswa:
            $idSiswa = $result['id_siswa'];
            $data['type'] = TipeUser::Siswa;

            // 1. Cek dulu record absen masuknya
            $sudahAbsen = $this->presensiSiswaModel->cekAbsen($idSiswa, $date);

            if (!$sudahAbsen) {
               return $this->showErrorView('Siswa belum melakukan presensi masuk hari ini.', $data);
            }

            // 2. KUNCI UTAMA: Ambil detail data absensi yang ditemukan hari ini
            $presensiHariIni = $this->presensiSiswaModel->getPresensiById($sudahAbsen);

            // 3. Jika kolom jam_keluar tidak kosong dan nilainya bukan '-', artinya dia sudah scan pulang
            if (!empty($presensiHariIni['jam_keluar']) && $presensiHariIni['jam_keluar'] !== '-') {
               $data['presensi'] = $presensiHariIni;
               return $this->showErrorView('Siswa sudah melakukan presensi pulang hari ini.', $data);
            }

            // Update jam keluar di database jika lolos validasi di atas
            $this->presensiSiswaModel->absenKeluar($sudahAbsen, $time);
            $data['presensi'] = $this->presensiSiswaModel->getPresensiById($sudahAbsen);

            // --- LOGIKA KALKULASI TERLAMBAT PULANG (LEBIH DARI JAM 15:30:00) ---
            $pulangTimestamp = strtotime($time);
            $batasPulangTimestamp = strtotime($this->batasTerlambatPulang);

            // JIKA PULANG MELEBIHI JAM 15:30:00: Susun pesan dan kirim WhatsApp
            if ($pulangTimestamp > $batasPulangTimestamp) {
                $selisihDetik = $pulangTimestamp - $batasPulangTimestamp;

                $jamTelat   = floor($selisihDetik / 3600);
                $menitTelat = floor(($selisihDetik % 3600) / 60);
                $detikTelat = $selisihDetik % 60;

                $statusTeks = "Terlambat Scan Pulang";

                // Menyusun teks keterangan keterlambatan pulang secara dinamis
                $keteranganTerlambat = " (Lebih ";
                if ($jamTelat > 0) {
                    $keteranganTerlambat .= $jamTelat . " jam ";
                }
                if ($menitTelat > 0 || $jamTelat > 0) {
                    $keteranganTerlambat .= $menitTelat . " menit ";
                }
                $keteranganTerlambat .= $detikTelat . " detik)";

                // Ambil data kelas asli dari database
                $kelasAsli = $result['nama_kelas'] ?? $result['kelas'] ?? '-';
                
                // Konversi Romawi ke Angka
                $kelas = $this->konversiRomawiKeAngka($kelasAsli);

                // Susun Pesan WhatsApp khusus Terlambat Pulang
                $messageString = "Yth. Bapak/Ibu Orang Tua/Wali,\n\n";
                $messageString .= "Menginfokan bahwa siswa berikut telah melakukan *Presensi Pulang* namun tercatat *MELEBIHI BATAS PRESENSI PULANG* (15:30):\n\n";
                $messageString .= "NIS: {$result['nis']}\n";
                $messageString .= "Nama: *{$result['nama_siswa']}*\n";
                $messageString .= "Kelas: {$kelas}\n"; 
                $messageString .= "Waktu Pulang: {$date} | {$time}\n";
                $messageString .= "Keterangan: *{$statusTeks}*{$keteranganTerlambat}\n\n";
                $messageString .= "Mohon konfirmasinya kepada anak Anda guna memastikan keamanan dan keselamatan selama perjalanan pulang ke rumah. Terima kasih.";

                // Kirim Notifikasi WA jika diaktifkan dan nomor HP tersedia
                if ($this->WANotificationEnabled && !empty($result['no_hp'])) {
                    $this->kirimNotifikasiWhatsApp($result['no_hp'], $messageString);
                }
            }

            break;
            
         default:
            return $this->showErrorView('Tipe pengguna tidak valid');
      }

      return view('scan/scan-result', $data);
   }

   /**
    * Fungsi Helper untuk mengubah teks Romawi di awal string kelas menjadi Angka Biasa
    */
   private function konversiRomawiKeAngka($namaKelas)
   {
       $part = explode(' ', $namaKelas);
       
       if (!empty($part)) {
           $romawi = strtoupper($part[0]);
           
           $daftarRomawi = [
               'XII' => '12',
               'XI'  => '11',
               'X'   => '10'
           ];
           
           if (array_key_exists($romawi, $daftarRomawi)) {
               $part[0] = $daftarRomawi[$romawi];
           }
       }
       
       return implode(' ', $part);
   }

   /**
    * Fungsi Helper untuk mengirim notifikasi
    */
   private function kirimNotifikasiWhatsApp($no_hp, $msg)
   {
      $payload = [
         'destination' => $no_hp,
         'message' => $msg,
         'delay' => 0
      ];
      try {
         $this->sendNotification($payload);
      } catch (\Exception $e) {
         log_message('error', 'Gagal mengirim notifikasi WA: ' . $e->getMessage());
      }
   }

   public function showErrorView(string $msg = 'no error message', $data = NULL)
   {
      $errdata = $data ?? [];
      $errdata['msg'] = $msg;
      return view('scan/error-scan-result', $errdata);
   }

   protected function sendNotification($message)
   {
      $token = getenv('WHATSAPP_TOKEN');
      $provider = getenv('WHATSAPP_PROVIDER');

         if (empty($provider) || empty($token)) return;

         switch ($provider) {
            case 'Fonnte':
               $whatsapp = new \App\Libraries\Whatsapp\Fonnte\Fonnte($token);
               break;
            default:
               return;
         }
         $whatsapp->sendMessage($message);
   }
}