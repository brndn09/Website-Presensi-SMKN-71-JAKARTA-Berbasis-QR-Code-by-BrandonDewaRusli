# Website Presensi SMKN 71 JAKARTA Berbasis QR Code



Aplikasi presensi (absensi) siswa berbasis web yang dirancang khusus untuk SMKN 71 Jakarta. Sistem ini memanfaatkan teknologi QR Code untuk mempermudah, mempercepat, dan meningkatkan akurasi proses pencatatan kehadiran siswa secara *real-time*.



---



## 🚀 Fitur Utama



*   **Generasi QR Code Dinamis:** Membuat QR Code otomatis untuk setiap sesi presensi.

*   **Scanner QR Code Terintegrasi:** sistem dapat memindai QR Code siswa langsung menggunakan kamera perangkat.

*   **Multi-Role Authentication:** Akses masuk terpisah untuk Superadmin, Guru Piket, WaliKelas.

*   **Laporan Real-Time:** Rekapitulasi data kehadiran harian dan bulanan yang dapat dipantau langsung.

*   **Manajemen Data:** Kemudahan pengelolaan data siswa, data presensi siswa, data guru, monitoring presensi.



## 🛠️ Teknologi yang Digunakan



Aplikasi ini dibangun menggunakan ekosistem teknologi berikut:



*   **Backend & Framework:** PHP / CodeIgniter4 

*   **Frontend:** HTML5, CSS3, JavaScript, Bootstrap / Tailwind CSS

*   **Database:** MySQL

*   **Library Scanner:** Html5-qrcode



---



## 💻 Cara Instalasi dan Menjalankan Proyek



### Prasyarat

Sebelum memulai, pastikan kamu sudah menginstal:

*   [Git](https://git-scm.com/)

*   PHP (versi 8.x) & Composer 

*   MySQL



### Langkah-Langkah



1.  **Clone Repositori**

```bash

    git clone [https://github.com/brndn09/Website-Presensi-SMKN-71-JAKARTA-Berbasis-QR-Code-by-BrandonDewaRusli.git]([https://github.com/brndn09/PresensiSMKN71JakartaBerbasisQRCode.git](https://github.com/brndn09/Website-Presensi-SMKN-71-JAKARTA-Berbasis-QR-Code-by-BrandonDewaRusli.git))

    cd PresensiSMKN71JakartaBerbasisQRCode

```



2.  **Konfigurasi Environment**

    Salin file `.env.example` menjadi `.env` dan sesuaikan pengaturan database Anda.

```bash

    cp .env.example .env

    di setting dulu .env nya

```




3.  **Menjalankan Secara Lokal**

```bash

    composer install

    mysql -u root -p

    create database db_absensi;

    php spark migrate --all

    php spark db:seed DatabaseSeeder

    php artisan serve

```

    Buka `http://localhost:8000` di browser kamu.



---





## 👥 Kontributor



*   **Brandon Dewa Rusli** - *Lead Developer* - [@github](https://github.com/brndn09)



---



## 📄 Lisensi



Proyek ini dilindungi di bawah lisensi **MIT License** - lihat file [LICENSE](LICENSE) untuk informasi lebih lanjut.
