# Website Presensi SMKN 71 JAKARTA Berbasis QR Code

Aplikasi presensi (absensi) siswa berbasis web yang dirancang khusus untuk SMKN 71 Jakarta. Sistem ini memanfaatkan teknologi QR Code untuk mempermudah, mempercepat, dan meningkatkan akurasi proses pencatatan kehadiran siswa secara *real-time*.

---

## 🚀 Fitur Utama

*   **Generasi QR Code Dinamis:** Membuat QR Code otomatis untuk setiap sesi presensi.
*   **Scanner QR Code Terintegrasi:** Guru atau sistem dapat memindai QR Code siswa langsung menggunakan kamera perangkat.
*   **Multi-Role Authentication:** Akses masuk terpisah untuk Admin, Guru, dan Siswa.
*   **Laporan Real-Time:** Rekapitulasi data kehadiran harian dan bulanan yang dapat dipantau langsung.
*   **Manajemen Data:** Kemudahan pengelolaan data siswa, kelas, guru, dan jadwal pelajaran.

## 🛠️ Teknologi yang Digunakan

Aplikasi ini dibangun menggunakan ekosistem teknologi berikut:

*   **Backend & Framework:** PHP / Laravel *(sesuaikan dengan framework yang kamu pakai)*
*   **Frontend:** HTML5, CSS3, JavaScript, Bootstrap / Tailwind CSS
*   **Database:** MySQL
*   **Library Scanner:** Html5-qrcode / Instascan
*   **Containerization:** Docker (terdapat konfigurasi `docker-compose.yml`)

---

## 💻 Cara Instalasi dan Menjalankan Proyek

### Prasyarat
Sebelum memulai, pastikan kamu sudah menginstal:
*   [Git](https://git-scm.com/)
*   [Docker](https://www.docker.com/) & Docker Compose *(jika menggunakan Docker)*
*   PHP (versi 8.x) & Composer *(jika dijalankan secara lokal tanpa Docker)*
*   MySQL

### Langkah-Langkah

1.  **Clone Repositori**
```bash
    git clone [https://github.com/username-kamu/PresensiSMKN71JakartaBerbasisQRCode.git](https://github.com/username-kamu/PresensiSMKN71JakartaBerbasisQRCode.git)
    cd PresensiSMKN71JakartaBerbasisQRCode
```

2.  **Konfigurasi Environment**
    Salin file `.env.example` menjadi `.env` dan sesuaikan pengaturan database Anda.
```bash
    cp .env.example .env
```

3.  **Menjalankan Menggunakan Docker (Rekomendasi)**
    Jika kamu menggunakan Docker yang sudah tertera di proyekmu, cukup jalankan:
```bash
    docker-compose up -d
    ```

4.  **Menjalankan Secara Lokal (Alternatif)**
    Jika ingin menjalankan manual tanpa Docker:
```bash
    composer install
    php artisan key:generate
    php artisan migrate --seed
    php artisan serve
    ```
    Buka `http://localhost:8000` di browser kamu.

---

## 📂 Struktur File Penting

Berdasarkan arsitektur proyek, berikut adalah beberapa file konfigurasi utama yang tersedia:
*   `docker-compose.yml` - Konfigurasi layanan container Docker.
*   `Dockerfile` - Instruksi pembuatan image Docker untuk aplikasi.
*   `phpunit.xml.dist` - Konfigurasi untuk *automated testing*.
*   `MIGRATION_GUIDE.md` - Panduan untuk migrasi database.

---

## 👥 Kontributor

*   **Brandon Dewa Rusli** - *Lead Developer* - [@github_username](https://github.com/username-kamu)

---

## 📄 Lisensi

Proyek ini dilindungi di bawah lisensi **MIT License** - lihat file [LICENSE](LICENSE) untuk informasi lebih lanjut.
