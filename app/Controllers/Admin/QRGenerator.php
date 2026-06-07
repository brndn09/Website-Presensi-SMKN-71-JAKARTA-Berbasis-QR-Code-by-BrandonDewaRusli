<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\GuruModel;
use App\Models\KelasModel;
use App\Models\SiswaModel;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;
use Endroid\QrCode\Label\Font\Font;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Label\Label;
use Endroid\QrCode\Logo\Logo;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Writer\SvgWriter;
use Endroid\QrCode\Writer\WriterInterface;

class QRGenerator extends BaseController
{
   protected QrCode $qrCode;
   protected WriterInterface $writer;
   protected ?Logo $logo = null;
   protected Label $label;
   protected Font $labelFont;
   protected Color $foregroundColor;
   protected Color $foregroundColor2;
   protected Color $backgroundColor;

   protected string $qrCodeFilePath;

   const UPLOADS_PATH = FCPATH . 'uploads' . DIRECTORY_SEPARATOR;

   public function __construct()
   {
      $this->setQrCodeFilePath(self::UPLOADS_PATH);

      $this->writer = new PngWriter();

      // Font cadangan untuk library utama jika sewaktu-waktu dibutuhkan
      $this->labelFont = new Font(FCPATH . 'assets/fonts/Roboto-Medium.ttf', 11);

      $this->foregroundColor = new Color(44, 73, 162);
      $this->foregroundColor2 = new Color(28, 101, 90);
      $this->backgroundColor = new Color(255, 255, 255);

      if (boolval(env('QR_LOGO'))) {
         // Create logo
         $logo = (new \Config\School)::$generalSettings->logo;
         if (empty($logo) || !file_exists(FCPATH . $logo)) {
            $logo = 'assets/img/logo_sekolah.jpg';
         }
         if (file_exists(FCPATH . $logo)) {
            $fileExtension = pathinfo(FCPATH . $logo, PATHINFO_EXTENSION);
            if ($fileExtension === 'svg') {
               $this->writer = new SvgWriter();
               $this->logo = Logo::create(FCPATH . $logo)
                  ->setResizeToWidth(75)
                  ->setResizeToHeight(75);
            } else {
               $this->logo = Logo::create(FCPATH . $logo)
                  ->setResizeToWidth(75);
            }
         }
      }

      $this->label = Label::create('')
         ->setFont($this->labelFont)
         ->setTextColor($this->foregroundColor);

      // Create QR code dasar
      $this->qrCode = QrCode::create('')
         ->setEncoding(new Encoding('UTF-8'))
         ->setErrorCorrectionLevel(new ErrorCorrectionLevelHigh())
         ->setSize(300)
         ->setMargin(10)
         ->setRoundBlockSizeMode(new RoundBlockSizeModeMargin())
         ->setForegroundColor($this->foregroundColor)
         ->setBackgroundColor($this->backgroundColor);
   }

   public function setQrCodeFilePath(string $qrCodeFilePath)
   {
      $this->qrCodeFilePath = $qrCodeFilePath;
      if (!file_exists($this->qrCodeFilePath))
         mkdir($this->qrCodeFilePath, recursive: true);
   }

   /**
    * Generate QR Code Siswa (Dipanggil via AJAX / Form POST)
    */
   public function generateQrSiswa()
   {
      $idKelas = $this->request->getVar('id_kelas');
      $kelasSlug = $this->getKelasJurusanSlug($idKelas);
      if (!$kelasSlug) {
         return $this->response->setJSON(false);
      }

      // Reset base path dengan DIRECTORY_SEPARATOR yang konsisten agar string path tidak menumpuk
      $basePath = self::UPLOADS_PATH . "qr-siswa" . DIRECTORY_SEPARATOR . $kelasSlug . DIRECTORY_SEPARATOR;
      $this->setQrCodeFilePath($basePath);

      // Ambil nama asli kelas & jurusan untuk teks label (bukan versi slug)
      $kelasModel = new KelasModel();
      $kelasData = $kelasModel->getKelas($idKelas);
      $namaKelasText = $kelasData ? $kelasData->kelas : '';

      // URUTAN PARAMETER DISESUAIKAN: unique_code, nama, nomor (NIS), infoTambahan
      $this->generate(
         unique_code: (string)$this->request->getVar('unique_code'),
         nama: (string)$this->request->getVar('nama'),
         nomor: (string)$this->request->getVar('nomor'), 
         infoTambahan: $namaKelasText 
      );

      return $this->response->setJSON(true);
   }

   /**
    * Generate QR Code Guru (Dipanggil via AJAX / Form POST)
    */
   public function generateQrGuru()
   {
      $this->qrCode->setForegroundColor($this->foregroundColor2);
      $this->label->setTextColor($this->foregroundColor2);

      $basePath = self::UPLOADS_PATH . "qr-guru" . DIRECTORY_SEPARATOR;
      $this->setQrCodeFilePath($basePath);

      // URUTAN PARAMETER DISESUAIKAN: unique_code, nama, nomor (NUPTK), infoTambahan
      $this->generate(
         unique_code: (string)$this->request->getVar('unique_code'),
         nama: (string)$this->request->getVar('nama'),
         nomor: (string)$this->request->getVar('nomor'), 
         infoTambahan: 'Guru / Staff'
      );

      return $this->response->setJSON(true);
   }

   /**
    * CORE FUNCTION: Membuat QR dasar, lalu menyusun teks multi-baris menggunakan GD Library
    * Urutan parameter diubah menjadi: unique_code, nama, nomor agar sinkron dengan fungsi pemanggilnya.
    */
   public function generate(string $unique_code, string $nama, string $nomor, string $infoTambahan = ''): string
   {
      $fileExt = $this->writer instanceof SvgWriter ? 'svg' : 'png';
      $filename = url_title($nama, lowercase: true) . "_" . url_title($nomor, lowercase: true) . ".$fileExt";
      $fullPath = $this->qrCodeFilePath . $filename;

      // Set data utama di dalam QR Code
      $this->qrCode->setData($unique_code);

      // Jika menggunakan format SVG, manipulasi GD Image tidak didukung
      if ($fileExt === 'svg') {
         // Fallback menggunakan nama saja agar tidak memicu error line breaks
         $this->label->setText($nama);
         $this->writer
            ->write(qrCode: $this->qrCode, logo: $this->logo, label: $this->label)
            ->saveToFile($fullPath);
         return $fullPath;
      }

      // --- PROSES MANIPULASI GAMBAR PNG DENGAN GD LIBRARY ---

      // 1. Ambil output gambar QR Code mentah tanpa label bawaan Endroid
      $result = $this->writer->write(qrCode: $this->qrCode, logo: $this->logo);
      $qrImageString = $result->getString();
      $sourceImage = imagecreatefromstring($qrImageString);

      $qrWidth = imagesx($sourceImage);
      $qrHeight = imagesy($sourceImage);

      // Siapkan ruang tambahan tinggi canvas sebanyak 75px ke bawah untuk area teks
      $extraHeight = 75;
      $newHeight = $qrHeight + $extraHeight;

      // Buat canvas baru yang lebih tinggi
      $canvas = imagecreatetruecolor($qrWidth, $newHeight);

      // Isi background canvas dengan warna putih bersih
      $whiteColor = imagecolorallocate($canvas, 255, 255, 255);
      imagefill($canvas, 0, 0, $whiteColor);

      // Tempelkan gambar QR Code asli ke canvas baru di posisi atas (Y = 0)
      imagecopy($canvas, $sourceImage, 0, 0, 0, 0, $qrWidth, $qrHeight);

      // Tentukan warna teks secara dinamis berdasarkan warna QR Code yang sedang aktif
      $colorObj = $this->qrCode->getForegroundColor();
      $fontColor = imagecolorallocate($canvas, $colorObj->getRed(), $colorObj->getGreen(), $colorObj->getBlue());

      // Path Font TrueType (.ttf)
      $fontPath = FCPATH . 'assets/fonts/Roboto-Medium.ttf';
      if (!file_exists($fontPath)) {
         // Fallback ke font sistem default jika file ttf belum diletakkan di folder assets
         $fontPath = 'C:\Windows\Fonts\Arial.ttf'; 
      }

      $fontSize = 10; // Ukuran font proporsional untuk 3 baris teks

      // 2. Gambar teks baris demi baris secara manual (Center-Aligned)

      // Baris 1: Nomor (NIS / NUPTK)
      $bbox1 = imagettfbbox($fontSize, 0, $fontPath, $nomor);
      $textWidth1 = $bbox1[2] - $bbox1[0];
      $x1 = ($qrWidth - $textWidth1) / 2;
      imagettftext($canvas, $fontSize, 0, (int)$x1, $qrHeight + 15, $fontColor, $fontPath, $nomor);

      // Baris 2: Nama
      $bbox2 = imagettfbbox($fontSize, 0, $fontPath, $nama);
      $textWidth2 = $bbox2[2] - $bbox2[0];
      $x2 = ($qrWidth - $textWidth2) / 2;
      imagettftext($canvas, $fontSize, 0, (int)$x2, $qrHeight + 38, $fontColor, $fontPath, $nama);

      // Baris 3: Informasi Tambahan (Kelas & Jurusan / Status Jabatan)
      if (!empty($infoTambahan)) {
         $bbox3 = imagettfbbox($fontSize, 0, $fontPath, $infoTambahan);
         $textWidth3 = $bbox3[2] - $bbox3[0];
         $x3 = ($qrWidth - $textWidth3) / 2;
         imagettftext($canvas, $fontSize, 0, (int)$x3, $qrHeight + 60, $fontColor, $fontPath, $infoTambahan);
      }

      // 3. Simpan Canvas ke dalam file path tujuan
      imagepng($canvas, $fullPath);

      // Bersihkan alokasi memory resource GD agar ram tidak bocor
      imagedestroy($sourceImage);
      imagedestroy($canvas);

      return $fullPath;
   }

   /**
    * Single Download QR Siswa
    */
   public function downloadQrSiswa($idSiswa = null)
   {
      $siswa = (new SiswaModel)->find($idSiswa);
      if (!$siswa) {
         session()->setFlashdata([
            'msg' => 'Siswa tidak ditemukan',
            'error' => true
         ]);
         return redirect()->back();
      }

      try {
         $kelasSlug = $this->getKelasJurusanSlug($siswa['id_kelas']) ?? 'tmp';
         
         $basePath = self::UPLOADS_PATH . "qr-siswa" . DIRECTORY_SEPARATOR . $kelasSlug . DIRECTORY_SEPARATOR;
         $this->setQrCodeFilePath($basePath);

         // Ambil nama teks asli kelas untuk diletakkan pada Baris 3
         $kelasData = (new KelasModel)->getKelas($siswa['id_kelas']);
         $namaKelasText = $kelasData ? $kelasData->kelas : '';

         return $this->response->download(
            $this->generate(
               unique_code: $siswa['unique_code'],
               nama: $siswa['nama_siswa'],
               nomor: $siswa['nis'],
               infoTambahan: $namaKelasText
            ),
            null,
            true,
         );
      } catch (\Throwable $th) {
         session()->setFlashdata([
            'msg' => $th->getMessage(),
            'error' => true
         ]);
         return redirect()->back();
      }
   }

   /**
    * Single Download QR Guru
    */
   public function downloadQrGuru($idGuru = null)
   {
      $guru = (new GuruModel)->find($idGuru);
      if (!$guru) {
         session()->setFlashdata([
            'msg' => 'Data tidak ditemukan',
            'error' => true
         ]);
         return redirect()->back();
      }
      try {
         $this->qrCode->setForegroundColor($this->foregroundColor2);
         $this->label->setTextColor($this->foregroundColor2);

         $basePath = self::UPLOADS_PATH . "qr-guru" . DIRECTORY_SEPARATOR;
         $this->setQrCodeFilePath($basePath);

         return $this->response->download(
            $this->generate(
               unique_code: $guru['unique_code'],
               nama: $guru['nama_guru'],
               nomor: $guru['nuptk'],
               infoTambahan: 'Guru / Staff'
            ),
            null,
            true,
         );
      } catch (\Throwable $th) {
         session()->setFlashdata([
            'msg' => $th->getMessage(),
            'error' => true
         ]);
         return redirect()->back();
      }
   }

   public function downloadAllQrSiswa()
   {
      $kelas = null;
      if ($idKelas = $this->request->getVar('id_kelas')) {
         $kelas = $this->getKelasJurusanSlug($idKelas);
         if (!$kelas) {
            session()->setFlashdata([
               'msg' => 'Kelas tidak ditemukan',
               'error' => true
            ]);
            return redirect()->back();
         }
      }

      $targetFolder = self::UPLOADS_PATH . "qr-siswa" . DIRECTORY_SEPARATOR . ($kelas ? "{$kelas}" . DIRECTORY_SEPARATOR : '');

      if (!file_exists($targetFolder) || count(glob($targetFolder . '*')) === 0) {
         session()->setFlashdata([
            'msg' => 'QR Code tidak ditemukan, generate qr terlebih dahulu',
            'error' => true
         ]);
         return redirect()->back();
      }

      try {
         $output = self::UPLOADS_PATH . 'qrcode-siswa' . ($kelas ? "_{$kelas}.zip" : '.zip');

         $this->zipFolder($targetFolder, $output);

         return $this->response->download($output, null, true);
      } catch (\Throwable $th) {
         session()->setFlashdata([
            'msg' => $th->getMessage(),
            'error' => true
         ]);
         return redirect()->back();
      }
   }

   public function downloadAllQrGuru()
   {
      $targetFolder = self::UPLOADS_PATH . "qr-guru" . DIRECTORY_SEPARATOR;

      if (!file_exists($targetFolder) || count(glob($targetFolder . '*')) === 0) {
         session()->setFlashdata([
            'msg' => 'QR Code tidak ditemukan, generate qr terlebih dahulu',
            'error' => true
         ]);
         return redirect()->back();
      }

      try {
         $output = self::UPLOADS_PATH . DIRECTORY_SEPARATOR . 'qrcode-guru.zip';

         $this->zipFolder($targetFolder, $output);

         return $this->response->download($output, null, true);
      } catch (\Throwable $th) {
         session()->setFlashdata([
            'msg' => $th->getMessage(),
            'error' => true
         ]);
         return redirect()->back();
      }
   }

   private function zipFolder(string $folder, string $output)
   {
      $zip = new \ZipArchive;
      $zip->open($output, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);

      /** @var \SplFileInfo[] $files */
      $files = new \RecursiveIteratorIterator(
         new \RecursiveDirectoryIterator($folder),
         \RecursiveIteratorIterator::LEAVES_ONLY
      );

      foreach ($files as $file) {
         if (!$file->isDir()) {
            $filePath = $file->getRealPath();
            $folderLength = strlen($folder);
            if ($folder[$folderLength - 1] === DIRECTORY_SEPARATOR) {
               $relativePath = substr($filePath, $folderLength);
            } else {
               $relativePath = substr($filePath, $folderLength + 1);
            }

            $zip->addFile($filePath, $relativePath);
         }
      }
      $zip->close();
   }

   protected function kelas(string $unique_code)
   {
      return self::UPLOADS_PATH . DIRECTORY_SEPARATOR . "qr-siswa/{$unique_code}.png";
   }

   protected function getKelasJurusanSlug(string $idKelas)
   {
      $kelas = (new KelasModel)->getKelas($idKelas);
      if ($kelas) {
         return url_title($kelas->kelas, lowercase: true);
      } else {
         return false;
      }
   }
}