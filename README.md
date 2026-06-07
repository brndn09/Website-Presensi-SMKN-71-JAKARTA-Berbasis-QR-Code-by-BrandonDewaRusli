# 📱 Website Presensi SMKN 71 JAKARTA Berbasis QR Code
By Brandon Dewa Rusli

![GitHub repo size](https://img.shields.io/github/repo-size/brndn09/Website-Presensi-SMKN-71-JAKARTA-Berbasis-QR-Code-by-BrandonDewaRusli?style=for-the-badge)
![GitHub language count](https://img.shields.io/github/languages/count/brndn09/Website-Presensi-SMKN-71-JAKARTA-Berbasis-QR-Code-by-BrandonDewaRusli?style=for-the-badge&color=blue)
![GitHub license](https://img.shields.io/github/license/brndn09/Website-Presensi-SMKN-71-JAKARTA-Berbasis-QR-Code-by-BrandonDewaRusli?style=for-the-badge&color=green)

Aplikasi presensi (absensi) siswa berbasis web modern yang dirancang khusus untuk lingkungan **SMKN 71 Jakarta**. Sistem ini memanfaatkan teknologi **QR Code** untuk mempermudah, mempercepat, dan meningkatkan akurasi serta transparansi proses pencatatan kehadiran siswa secara *real-time*.

---

## 🚀 Fitur Utama

* ✨ **Generasi QR Code Dinamis:** Membuat QR Code otomatis dan unik untuk setiap sesi atau jam mata pelajaran guna menghindari kecurangan siswa.
* 📸 **Scanner QR Code Terintegrasi:** Guru, operator, atau sistem dapat memindai QR Code siswa langsung menggunakan kamera perangkat (Smartphone/Laptop) secara instan.
* 🔒 **Multi-Role Authentication:** Hak akses masuk yang aman dan terpisah untuk **Admin**, **Guru**, dan **Siswa**.
* 📊 **Laporan Real-Time & Rekap otomatis:** Rekapitulasi data kehadiran harian, mingguan, dan bulanan yang dapat dipantau langsung serta siap cetak.
* 🗂️ **Manajemen Data Terpusat:** Kemudahan pengelolaan data siswa, kelas, jurusan, guru, hingga jadwal pelajaran.

---

## 🛠️ Teknologi yang Digunakan

Proyek ini dibangun menggunakan kombinasi teknologi modern untuk menjamin performa dan skalabilitas:

* **Backend Framework:** ![PHP](https://img.shields.io/badge/php-%23777BB4.svg?style=flat&logo=php&logoColor=white) ![Laravel](https://img.shields.io/badge/laravel-%23FF2D20.svg?style=flat&logo=laravel&logoColor=white) *(Sesuaikan jika menggunakan framework lain)*
* **Frontend UI:** ![HTML5](https://img.shields.io/badge/html5-%23E34F26.svg?style=flat&logo=html5&logoColor=white) ![CSS3](https://img.shields.io/badge/css3-%231572B6.svg?style=flat&logo=css3&logoColor=white) ![JavaScript](https://img.shields.io/badge/javascript-%23F7DF1E.svg?style=flat&logo=javascript&logoColor=black) ![Bootstrap](https://img.shields.io/badge/bootstrap-%238511FA.svg?style=flat&logo=bootstrap&logoColor=white)
* **Database:** ![MySQL](https://img.shields.io/badge/mysql-%2300f.svg?style=flat&logo=mysql&logoColor=white)
* **Library Scanner:** `Html5-qrcode` / `Instascan` untuk pemindaian kamera yang responsif.
* **Containerization:** ![Docker](https://img.shields.io/badge/docker-%230db7ed.svg?style=flat&logo=docker&logoColor=white) (Dilengkapi konfigurasi `Dockerfile` & `docker-compose.yml`).

---

## 💻 Panduan Instalasi & Cara Menjalankan

### 📋 Prasyarat Sistem
Sebelum memulai instalasi, pastikan perangkat kamu sudah terpasang:
* [Git](https://git-scm.com/)
* [Docker & Docker Compose](https://www.docker.com/) *(Direkomendasikan)*
* [PHP (v8.x)](https://www.php.net/) & [Composer](https://getcomposer.org/) *(Jika tanpa Docker)*
* MySQL Server

### 🛠️ Langkah-Langkah Sinkronisasi

1. **Clone Repositori**
   ```bash
   git clone [https://github.com/brndn09/Website-Presensi-SMKN-71-JAKARTA-Berbasis-QR-Code-by-BrandonDewaRusli.git](https://github.com/brndn09/Website-Presensi-SMKN-71-JAKARTA-Berbasis-QR-Code-by-BrandonDewaRusli.git)
   cd Website-Presensi-SMKN-71-JAKARTA-Berbasis-QR-Code-by-BrandonDewaRusli
