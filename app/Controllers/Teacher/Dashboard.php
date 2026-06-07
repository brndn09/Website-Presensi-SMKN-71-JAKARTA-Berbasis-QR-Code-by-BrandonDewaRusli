<?php

namespace App\Controllers\Teacher;

use App\Controllers\BaseController;
use App\Models\KelasModel;
use App\Models\SiswaModel;
use App\Models\PresensiSiswaModel;
use App\Models\KehadiranModel;
use CodeIgniter\I18n\Time;

class Dashboard extends BaseController
{
    protected KelasModel $kelasModel;
    protected SiswaModel $siswaModel;
    protected PresensiSiswaModel $presensiSiswaModel;
    protected KehadiranModel $kehadiranModel;

    public function __construct()
    {
        $this->kelasModel = new KelasModel();
        $this->siswaModel = new SiswaModel();
        $this->presensiSiswaModel = new PresensiSiswaModel();
        $this->kehadiranModel = new KehadiranModel();
    }

    /**
     * Fungsi Helper untuk menghitung statistik harian Wali Kelas.
     * Logika: Menghitung data murni dari database tanpa 'Alfa Otomatis'.
     */
    private function hitungStatistikWaliKelas($idKelas)
    {
        $now = Time::now('Asia/Jakarta');
        $today = $now->toDateString();

        // 1. Ambil total populasi siswa di kelas tersebut
        $totalSiswa = $this->siswaModel->getSiswaCountByKelas($idKelas);

        // 2. Ambil data presensi murni yang sudah terinput di DB hari ini
        $db = \Config\Database::connect();
        $builder = $db->table('tb_presensi_siswa');
        $builder->where('tanggal', $today);
        $builder->where('id_kelas', $idKelas);
        $presensiHariIni = $builder->get()->getResultArray();

        $stats = [
            'hadir'       => 0,
            'sakit'       => 0,
            'izin'        => 0,
            'alfa'        => 0,
            'pkl'         => 0, // Sinkronisasi counter PKL harian untuk dashboard Guru
            'belum_absen' => 0,
            'total_siswa' => $totalSiswa
        ];

        // 3. Klasifikasi berdasarkan id_kehadiran di database
        foreach ($presensiHariIni as $p) {
            $id = (int)$p['id_kehadiran'];
            if ($id === 1) $stats['hadir']++;
            elseif ($id === 2) $stats['sakit']++;
            elseif ($id === 3) $stats['izin']++;
            elseif ($id === 4) $stats['alfa']++;
            elseif ($id === 6) $stats['pkl']++; // Akumulasi data PKL (ID: 6)
        }

        // 4. Hitung kategori BELUM ABSEN
        // Rumus: Total Siswa - (Jumlah yang sudah memiliki record presensi termasuk yang PKL)
        $jumlahSudahInput = $stats['hadir'] + $stats['sakit'] + $stats['izin'] + $stats['alfa'] + $stats['pkl'];
        $stats['belum_absen'] = max(0, $totalSiswa - $jumlahSudahInput);

        return $stats;
    }

    public function index()
    {
        $user = user(); // Mengambil data user dari library Auth (Ion Auth/Shield/Custom)
        
        if (!is_wali_kelas()) {
            return redirect()->to('admin')->with('error', 'Anda bukan Wali Kelas.');
        }

        // Ambil data kelas berdasarkan ID Guru yang login
        $kelas = $this->kelasModel->getKelasByWali($user->id_guru);

        if (empty($kelas)) {
            $data = [
                'title'    => 'Dashboard Wali Kelas',
                'ctx'      => 'dashboard',
                'no_class' => true
            ];
            return view('teacher/dashboard', $data);
        }

        $now = Time::now('Asia/Jakarta');
        
        // Mempersiapkan label untuk grafik 7 hari terakhir
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

        // Ambil statistik hari ini (Database-first logic)
        $stats = $this->hitungStatistikWaliKelas($kelas['id_kelas']);

        // Ambil trend mingguan khusus kelas ini melalui Model (Sudah terpasang tracking PKL di sisi model)
        $grafikKehadiran = $this->presensiSiswaModel->getAttendanceTrend(7, $kelas['id_kelas']);

        $data = [
            'title'   => 'Dashboard Wali Kelas',
            'ctx'     => 'dashboard',
            'kelas'   => $kelas,
            'summary' => [
                'total_siswa'          => $stats['total_siswa'],
                'hadir_hari_ini'       => $stats['hadir'],
                'sakit_hari_ini'       => $stats['sakit'],
                'izin_hari_ini'        => $stats['izin'],
                'alfa_hari_ini'        => $stats['alfa'],
                'pkl_hari_ini'         => $stats['pkl'], // Kirim parameter PKL ke view Dashboard Guru
                'belum_absen_hari_ini' => $stats['belum_absen']
            ],
            'dateRange'       => $dateRange,
            'grafikKehadiran' => $grafikKehadiran
        ];

        return view('teacher/dashboard', $data);
    }

    public function attendance()
    {
        $user = user();
        if (!is_wali_kelas()) {
            return redirect()->to('teacher/dashboard')->with('error', 'Akses ditolak.');
        }

        $kelas = $this->kelasModel->getKelasByWali($user->id_guru);
        if (empty($kelas)) {
            return redirect()->to('teacher/dashboard')->with('error', 'Anda belum ditugaskan di kelas manapun.');
        }

        $data = [
            'title' => 'Manajemen Kehadiran',
            'ctx'   => 'attendance',
            'kelas' => $kelas,
            'date'  => Time::now('Asia/Jakarta')->toDateString()
        ];

        return view('teacher/attendance', $data);
    }

    public function getAttendanceList()
    {
        $idKelas   = $this->request->getVar('id_kelas');
        $namaKelas = $this->request->getVar('kelas');
        $tanggal   = $this->request->getVar('tanggal');

        // Mengambil data presensi gabungan (Murni dari Model)
        $result = $this->presensiSiswaModel->getPresensiByKelasTanggal($idKelas, $tanggal);
        
        // Memastikan apakah tanggal yang dipilih adalah tanggal mendatang (masa depan)
        $lewat = Time::parse($tanggal, 'Asia/Jakarta')->isAfter(Time::today('Asia/Jakarta'));

        $data = [
            'data'  => $result,
            'kelas' => $namaKelas,
            'lewat' => $lewat
        ];

        return view('teacher/absen/list_absen_siswa', $data);
    }

    public function getEditModal()
    {
        $idPresensi = $this->request->getVar('id_presensi');
        $idSiswa    = $this->request->getVar('id_siswa');

        $data = [
            'presensi'      => $this->presensiSiswaModel->getPresensiById($idPresensi),
            'listKehadiran' => $this->kehadiranModel->getAllKehadiran(),
            'data'          => $this->siswaModel->getSiswaById($idSiswa)
        ];

        return view('teacher/absen/modal_ubah_kehadiran', $data);
    }

    public function updateSingleAttendance()
    {
        $idKehadiran = $this->request->getVar('id_kehadiran');
        $idSiswa     = $this->request->getVar('id_siswa');
        $idKelas     = $this->request->getVar('id_kelas');
        $tanggal     = $this->request->getVar('tanggal');
        $jamMasuk    = $this->request->getVar('jam_masuk');
        $jamKeluar   = $this->request->getVar('jam_keluar');
        $keterangan  = $this->request->getVar('keterangan');

        // Cari ID presensi murni melalui Model
        $cek = $this->presensiSiswaModel->cekAbsen($idSiswa, $tanggal);

        // --- FILTER PARAMETER LOG WAKTU UNTUK PKL (ID: 6) ---
        // Jika status PKL, paksa input log waktu jam menjadi null agar database tidak terkena crash
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

        // Keterangan teks bawaan default jika opsinya PKL namun input teks kosong
        if ($idKehadiran == 6 && empty(trim($keterangan))) {
            $keterangan = "Melaksanakan Praktik Kerja Lapangan (PKL)";
        }

        // Kirim ID jika ada untuk update, null jika insert baru
        $result = $this->presensiSiswaModel->updatePresensi(
            $cek ?: null,
            $idSiswa,
            $idKelas,
            $tanggal,
            $idKehadiran,
            $valJamMasuk,
            $valJamKeluar,
            $keterangan
        );

        $siswa = $this->siswaModel->getSiswaById($idSiswa);
        $response = [
            'nama_siswa' => $siswa['nama_siswa'] ?? 'Siswa',
            'status'     => $result ? true : false
        ];

        return $this->response->setJSON($response);
    }
}