<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\GuruModel;
use App\Models\JurusanModel;
use App\Models\SiswaModel;
use App\Models\KelasModel;
use App\Models\PetugasModel;
use App\Models\PresensiSiswaModel;
use CodeIgniter\I18n\Time;
use App\Libraries\enums\UserRole;

class Dashboard extends BaseController
{
    protected SiswaModel $siswaModel;
    protected GuruModel $guruModel;
    protected KelasModel $kelasModel;
    protected JurusanModel $jurusanModel;
    protected PresensiSiswaModel $presensiSiswaModel;
    protected PetugasModel $petugasModel;

    public function __construct()
    {
        $this->siswaModel = new SiswaModel();
        $this->guruModel = new GuruModel();
        $this->kelasModel = new KelasModel();
        $this->jurusanModel = new JurusanModel();
        $this->presensiSiswaModel = new PresensiSiswaModel();
        $this->petugasModel = new PetugasModel();
    }

    /**
     * Fungsi Helper untuk menghitung statistik kehadiran.
     * Logika: Menghitung data murni dari database. 
     * Siswa yang tidak ada di tabel presensi dianggap 'Belum Absen'.
     */
    private function hitungStatistikKehadiran($idKelas = null)
    {
        $now = Time::now('Asia/Jakarta');
        $today = $now->toDateString();

        // 1. Dapatkan Total Siswa (berdasarkan filter kelas jika ada)
        $totalSiswa = $this->siswaModel->getSiswaCountByKelas($idKelas);

        // 2. Ambil data presensi yang SUDAH terinput di database hari ini
        $db = \Config\Database::connect();
        $builder = $db->table('tb_presensi_siswa');
        $builder->where('tanggal', $today);
        
        if ($idKelas) {
            $builder->join('tb_siswa', 'tb_siswa.id_siswa = tb_presensi_siswa.id_siswa');
            $builder->where('tb_siswa.id_kelas', $idKelas);
        }
        
        $presensiHariIni = $builder->get()->getResultArray();

        $stats = [
            'hadir'       => 0,
            'sakit'       => 0,
            'izin'        => 0,
            'alfa'        => 0,
            'pkl'         => 0, // Tambahan counter data PKL harian
            'belum_absen' => 0
        ];

        // 3. Hitung data berdasarkan status yang ada di database
        foreach ($presensiHariIni as $p) {
            $idKehadiran = (int)$p['id_kehadiran'];
            
            if ($idKehadiran === 1) $stats['hadir']++;
            elseif ($idKehadiran === 2) $stats['sakit']++;
            elseif ($idKehadiran === 3) $stats['izin']++;
            elseif ($idKehadiran === 4) $stats['alfa']++;
            elseif ($idKehadiran === 6) $stats['pkl']++; // Akumulasi data PKL (ID: 6)
        }

        // 4. Hitung Belum Absen
        // Rumus: Total Siswa - (Semua yang sudah punya record di tb_presensi termasuk yang PKL)
        $jumlahSudahAbsen = $stats['hadir'] + $stats['sakit'] + $stats['izin'] + $stats['alfa'] + $stats['pkl'];
        $stats['belum_absen'] = max(0, $totalSiswa - $jumlahSudahAbsen);

        return $stats;
    }

    public function index()
    {
        // Role check
        if (is_wali_kelas()) {
            return redirect()->to('teacher/dashboard');
        }

        if (user_role() === UserRole::Scanner) {
            return redirect()->to('scan');
        }

        $now = Time::now('Asia/Jakarta');
        
        // Date Range untuk label Grafik (7 hari terakhir)
        $dateRange = [];
        for ($i = 6; $i >= 0; $i--) {
            if ($i == 0) {
                $formattedDate = "Hari ini";
            } else {
                $t = $now->subDays($i);
                $formattedDate = "{$t->getDay()} " . substr($t->toFormattedDateString(), 0, 3);
            }
            array_push($dateRange, $formattedDate);
        }

        // Statistik Trend (Grafik) - Sudah dipasangi counter array PKL di Model
        $grafikKehadiranSiswa = $this->presensiSiswaModel->getAttendanceTrend();

        // Data Kelas untuk Dropdown Filter
        $kelasData = $this->kelasModel->getDataKelas();
        foreach ($kelasData as &$k) {
            $k['jumlah_siswa'] = $this->siswaModel->getSiswaCountByKelas($k['id_kelas']);
        }

        // Statistik Card (Hadir, Sakit, Izin, Alfa, PKL, Belum Absen)
        $stats = $this->hitungStatistikKehadiran();
        $totalSiswaDashboard = $this->siswaModel->getSiswaCountByKelas();

        $data = [
            'title'                => 'Dashboard',
            'ctx'                  => 'dashboard',
            'siswa'                => $this->siswaModel->getAllSiswaWithKelas(),
            'guru'                 => $this->guruModel->getAllGuru(),
            'kelas'                => $kelasData,
            'jurusan'              => $this->jurusanModel->getDataJurusan(),
            'dateRange'            => $dateRange,
            'dateNow'              => $now->toLocalizedString('d MMMM Y'),
            'grafikKehadiranSiswa' => $grafikKehadiranSiswa,
            'jumlahKehadiranSiswa' => $stats,
            'totalSiswa'           => $totalSiswaDashboard,
            'totalGuru'            => $this->guruModel->countAllResults(),
            'petugas'              => $this->petugasModel->getAllPetugas(),
        ];

        return view('admin/dashboard', $data);
    }

    public function filterData()
    {
        $idKelas = $this->request->getPost('id_kelas');
        
        // Ambil statistik berdasarkan kelas dengan logika database murni
        $stats = $this->hitungStatistikKehadiran($idKelas);

        // Grafik Tren Kehadiran khusus kelas tersebut
        $grafikKehadiranSiswa = $this->presensiSiswaModel->getAttendanceTrend(7, $idKelas ?: null);

        // Total siswa untuk kelas terpilih
        $totalSiswa = $this->siswaModel->getSiswaCountByKelas($idKelas);

        $data = [
            'hadir'       => $stats['hadir'],
            'sakit'       => $stats['sakit'],
            'izin'        => $stats['izin'],
            'alfa'        => $stats['alfa'],
            'pkl'         => $stats['pkl'], // Lempar data PKL ke parsial html view render
            'belum_absen' => $stats['belum_absen'],
            'totalSiswa'  => $totalSiswa,
        ];

        return json_encode([
            'result'      => 1,
            'htmlContent' => view('admin/_dashboard_siswa_stats', $data),
            'chartData'   => $grafikKehadiranSiswa,
            'totalSiswa'  => $totalSiswa
        ]);
    }
}