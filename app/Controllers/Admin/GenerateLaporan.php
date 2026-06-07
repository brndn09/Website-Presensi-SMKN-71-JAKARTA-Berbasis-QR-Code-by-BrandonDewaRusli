<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use CodeIgniter\I18n\Time;
use DateTime;
use DateInterval;
use DatePeriod;

// Library PhpSpreadsheet
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

use App\Models\GuruModel;
use App\Models\KelasModel;
use App\Models\SiswaModel;
use App\Models\PresensiSiswaModel;

class GenerateLaporan extends BaseController
{
    protected SiswaModel $siswaModel;
    protected KelasModel $kelasModel;
    protected GuruModel $guruModel;
    protected PresensiSiswaModel $presensiSiswaModel;

    public function __construct()
    {
        $this->siswaModel = new SiswaModel();
        $this->kelasModel = new KelasModel();
        $this->guruModel = new GuruModel();
        $this->presensiSiswaModel = new PresensiSiswaModel();
    }

    public function index()
    {
        $kelas = $this->kelasModel->getDataKelas();
        $guru = $this->guruModel->getAllGuru();
        $siswaPerKelas = [];

        foreach ($kelas as $value) {
            $siswaPerKelas[] = $this->siswaModel->getSiswaByKelas($value['id_kelas']);
        }

        $data = [
            'title'         => 'Generate Laporan',
            'ctx'           => 'laporan',
            'siswaPerKelas' => $siswaPerKelas,
            'kelas'         => $kelas,
            'guru'          => $guru
        ];

        return view('admin/generate-laporan/generate-laporan', $data);
    }

    /**
     * FUNGSI 1: LAPORAN ABSENSI SISWA (H, S, I, A)
     */
    public function generateLaporanSiswa()
    {
        $idKelas = $this->request->getVar('kelas');
        $type    = $this->request->getVar('type');
        $bulan   = $this->request->getVar('tanggalSiswa'); 

        $siswa = $this->siswaModel->getSiswaByKelas($idKelas);

        if (empty($siswa)) {
            session()->setFlashdata(['msg' => 'Data siswa pada kelas ini kosong!', 'error' => true]);
            return redirect()->to('/admin/laporan');
        }

        $kelasInfo = (array) $this->kelasModel->getKelas($idKelas);
        $begin     = new Time($bulan, locale: 'id');
        $end       = (new DateTime($begin->format('Y-m-t')))->modify('+1 day');
        $interval  = DateInterval::createFromDateString('1 day');
        $period    = new DatePeriod($begin, $interval, $end);

        $arrayTanggal = [];
        $dataAbsen    = [];

        foreach ($period as $dt) {
            if (!in_array($dt->format('D'), ['Sat', 'Sun'])) {
                $lewat = Time::parse($dt->format('Y-m-d'))->isAfter(Time::today());
                $absen = $this->presensiSiswaModel->getPresensiByKelasTanggal($idKelas, $dt->format('Y-m-d'));
                $absen['lewat'] = $lewat;
                $dataAbsen[]    = $absen;
                $arrayTanggal[] = Time::createFromInstance($dt, locale: 'id');
            }
        }

        $laki = 0;
        foreach ($siswa as $s) {
            if ($s['jenis_kelamin'] != 'Perempuan') { $laki++; }
        }

        $data = [
            'title'      => 'Laporan Presensi Siswa',
            'tanggal'    => $arrayTanggal,
            'bulan'      => $begin->toLocalizedString('MMMM'),
            'tahun'      => $begin->format('Y'),
            'listAbsen'  => $dataAbsen,
            'listSiswa'  => $siswa,
            'rekapSiswa' => ['laki' => $laki, 'perempuan' => count($siswa) - $laki],
            'kelas'      => $kelasInfo,
            'grup'       => "Kelas " . ($kelasInfo['kelas'] ?? ''),
        ];

        if ($type == 'xls') return $this->exportToExcel($data);
        return view('admin/generate-laporan/laporan-siswa', $data) . view('admin/generate-laporan/topdf');
    }

    /**
     * FUNGSI 2: REKAP ABSENSI DETAIL
     */
    public function generateLaporanTerlambat()
    {
        $idKelas      = $this->request->getVar('kelas');
        $type         = $this->request->getVar('type');
        $jenisPeriode = $this->request->getVar('jenis_periode');
        $batasMasuk   = "06:31:00"; 

        $siswa = $this->siswaModel->getSiswaByKelas($idKelas);
        if (empty($siswa)) {
            session()->setFlashdata(['msg' => 'Data siswa kosong!', 'error' => true]);
            return redirect()->to('/admin/laporan');
        }

        $kelasInfo = (array) $this->kelasModel->getKelas($idKelas);

        if ($jenisPeriode === 'mingguan') {
            $tglMulaiInput   = $this->request->getVar('tanggal_mulai');
            $tglSelesaiInput = $this->request->getVar('tanggal_selesai');

            $begin = new Time($tglMulaiInput, locale: 'id');
            $end   = (new DateTime($tglSelesaiInput))->modify('+1 day');
            
            $infoPeriode = "PERIODE: " . $begin->toLocalizedString('dd MMMM Y') . ' s/d ' . Time::parse($tglSelesaiInput, locale: 'id')->toLocalizedString('dd MMMM Y');
        } else {
            $bulanInput = $this->request->getVar('tanggalSiswa'); 
            $begin      = new Time($bulanInput . '-01', locale: 'id');
            $end        = (new DateTime($begin->format('Y-m-t')))->modify('+1 day');
            
            $infoPeriode = "BULAN: " . strtoupper($begin->toLocalizedString('MMMM') . ' ' . $begin->format('Y'));
        }

        $interval = DateInterval::createFromDateString('1 day');
        $period   = new DatePeriod($begin, $interval, $end);

        $listTanggal = [];
        foreach ($period as $dt) {
            if (!in_array($dt->format('D'), ['Sat', 'Sun'])) {
                $listTanggal[] = [
                    'date' => $dt->format('Y-m-d'),
                    'day'  => Time::parse($dt->format('Y-m-d'), locale: 'id')->toLocalizedString('EEE'),
                    'num'  => $dt->format('d')
                ];
            }
        }

        if (empty($listTanggal)) {
            session()->setFlashdata(['msg' => 'Tidak ada hari efektif (Senin-Jumat) dalam periode yang dipilih!', 'error' => true]);
            return redirect()->to('/admin/laporan');
        }

        $rekapSiswa = [];
        foreach ($siswa as $s) {
            $dataHarian = [];
            $stats = ['tepat' => 0, 'terlambat' => 0];

            foreach ($listTanggal as $tgl) {
                $p = $this->presensiSiswaModel->where(['id_siswa' => $s['id_siswa'], 'tanggal' => $tgl['date']])->first();
                $status = '-'; $jamMasuk = '-'; $jamPulang = '-'; $lateText = '';

                if ($p && $p['id_kehadiran'] == 1) { 
                    $jamMasuk = $p['jam_masuk'] ?? '-';
                    $jamPulang = $p['jam_keluar'] ?? '-';
                    if (strtotime($jamMasuk) <= strtotime($batasMasuk)) {
                        $status = 'H'; $stats['tepat']++;
                    } else {
                        $status = 'T'; $stats['terlambat']++;
                        $diff = strtotime($jamMasuk) - strtotime($batasMasuk);
                        $jam = floor($diff / 3600);
                        $menit = floor(($diff / 60) % 60);
                        $lateText = ($jam > 0) ? $jam . "j " . $menit . "m" : $menit . "m";
                    }
                }
                $dataHarian[$tgl['date']] = ['status' => $status, 'masuk' => $jamMasuk, 'pulang' => $jamPulang, 'late' => $lateText];
            }
            $rekapSiswa[] = ['nama' => $s['nama_siswa'], 'nis' => $s['nis'], 'detail' => $dataHarian, 'total' => $stats];
        }

        $data = [
            'title'        => 'Rekap Presensi Detail',
            'listTanggal'  => $listTanggal,
            'rekap'        => $rekapSiswa,
            'kelas'        => $kelasInfo,
            'infoPeriode'  => $infoPeriode,
            'bulan'        => $begin->toLocalizedString('MMMM'), 
            'tahun'        => $begin->format('Y'),
            'batas'        => $batasMasuk,
            'grup'         => "Kelas " . ($kelasInfo['kelas'] ?? '')
        ];

        if ($type == 'xls') return $this->exportRekapExcel($data);
        return view('admin/generate-laporan/laporan-rekap-absensi-pdf', $data) . view('admin/generate-laporan/topdf');
    }

    /**
     * PERBAIKAN FUNGSI 2: EXCEL REKAP DETAIL + KOP RESMI DAN DUA LOGO
     */
    private function exportRekapExcel($data)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Tentukan kolom terakhir data
        $lastColNum = count($data['listTanggal']) + 4;
        $lastColLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($lastColNum);

        // --- 1. PROSES DUA LOGO (Kiri & Kanan) ---
        $pathLogoDki = ROOTPATH . 'public/assets/img/logodkijakarta.jpg'; 
        $pathLogoSekolah = ROOTPATH . 'public/assets/img/logosekolah.jpg';

        // --- LOGO KIRI (DKI) ---
        if (file_exists($pathLogoDki)) {
            $drawingLeft = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
            $drawingLeft->setName('Logo DKI');
            $drawingLeft->setDescription('Logo Provinsi DKI Jakarta');
            $drawingLeft->setPath($pathLogoDki);
            $drawingLeft->setHeight(85); 
            $drawingLeft->setCoordinates('A2'); 
            $drawingLeft->setOffsetX(10);
            $drawingLeft->setWorksheet($sheet);
        }

        // --- LOGO KANAN (SEKOLAH) ---
        if (file_exists($pathLogoSekolah)) {
            $drawingRight = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
            $drawingRight->setName('Logo Sekolah');
            $drawingRight->setDescription('Logo SMKN 71');
            $drawingRight->setPath($pathLogoSekolah);
            $drawingRight->setHeight(85); 
            $drawingRight->setCoordinates($lastColLetter . '2'); 
            $drawingRight->setOffsetX(-40);
            $drawingRight->setWorksheet($sheet);
        }

        // --- 2. TEKS KOP SURAT ---
        $endKopLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($lastColNum - 1);

        $sheet->setCellValue('B1', 'PEMERINTAH PROVINSI DAERAH KHUSUS IBUKOTA JAKARTA');
        $sheet->setCellValue('B2', 'DINAS PENDIDIKAN');
        $sheet->setCellValue('B3', 'SEKOLAH MENENGAH KEJURUAN NEGERI 71 JAKARTA');
        $sheet->setCellValue('B4', 'BIDANG KEAHLIAN : 1. PENGEMBANGAN PERANGKAT LUNAK DAN GIM | 2. SENI DAN INDUSTRI KREATIF');
        $sheet->setCellValue('B5', 'Jl. Radjiman Widyodiningrat Pulo Jahe, Cakung, Jakarta Timur');
        $sheet->setCellValue('B6', 'E-mail: smkntujuh1jakarta@gmail.com Website: http://smkn71jakarta.sch.id/ Kode Pos : 13930');

        for ($r = 1; $r <= 6; $r++) {
            $sheet->mergeCells("B{$r}:{$endKopLetter}{$r}");
            $sheet->getStyle("B{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }

        $sheet->getStyle("B1:B2")->getFont()->setBold(true)->setSize(11);
        $sheet->getStyle("B3")->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle("B4:B6")->getFont()->setSize(9);

        // Membuat Garis Double Tebal di bawah Kop Surat
        $sheet->getStyle("A7:{$lastColLetter}7")->getBorders()->getBottom()->setBorderStyle(Border::BORDER_DOUBLE);

        // --- 3. INFO HEADER LAPORAN ---
        $sheet->setCellValue('A9', 'REKAPITULASI KEDISIPLINAN PRESENSI SISWA');
        $sheet->mergeCells("A9:{$lastColLetter}9");
        $sheet->getStyle("A9")->getFont()->setBold(true)->setSize(12);

        $sheet->setCellValue('A11', 'KELAS: ' . strtoupper($data['kelas']['kelas'] ?? '-'));
        $sheet->setCellValue('A12', $data['infoPeriode']); 
        $sheet->setCellValue('A13', 'BATAS TOLERANSI MASUK: ' . $data['batas'] . ' WIB');
        $sheet->getStyle("A11:A13")->getFont()->setBold(true);

        // --- 4. HEADER TABEL ---
        $sheet->setCellValue('A15', 'No');
        $sheet->setCellValue('B15', 'Nama Siswa');
        
        $col = 3;
        foreach ($data['listTanggal'] as $tgl) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col);
            $sheet->setCellValue($colLetter . '15', $tgl['day'] . "\n" . $tgl['num']);
            $sheet->getStyle($colLetter . '15')->getAlignment()->setWrapText(true);
            $sheet->getColumnDimension($colLetter)->setWidth(10);
            $col++;
        }
        
        $colH = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col++);
        $colT = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col);
        $sheet->setCellValue($colH . '15', 'H');
        $sheet->setCellValue($colT . '15', 'T');
        
        $sheet->getStyle("A15:{$colT}15")->getFont()->setBold(true);
        $sheet->getStyle("A15:{$colT}15")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle("A15:{$colT}15")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        $sheet->getStyle("A15:{$colT}15")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('F2F2F2');

        // --- 5. ISI DATA ---
        $row = 16;
        foreach ($data['rekap'] as $idx => $s) {
            $sheet->setCellValue('A' . $row, $idx + 1);
            $sheet->setCellValue('B' . $row, strtoupper($s['nama']));
            
            $colIdx = 3;
            foreach ($data['listTanggal'] as $tgl) {
                $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIdx);
                $info = $s['detail'][$tgl['date']];
                
                if (in_array($info['status'], ['H', 'T'])) {
                    $val = $info['status'] . "\nM:" . $info['masuk'] . "\nP:" . $info['pulang'];
                    if ($info['status'] == 'T') $val .= "\nL:" . $info['late'];
                    
                    $sheet->setCellValue($colLetter . $row, $val);
                    $sheet->getStyle($colLetter . $row)->getAlignment()->setWrapText(true);
                    $sheet->getStyle($colLetter . $row)->getFont()->setSize(7);
                    
                    if ($info['status'] == 'H') {
                        $sheet->getStyle($colLetter . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('C6EFCE');
                    } else {
                        $sheet->getStyle($colLetter . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('FFEB9C');
                    }
                } else {
                    $sheet->setCellValue($colLetter . $row, '');
                }
                $sheet->getStyle($colLetter . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $colIdx++;
            }

            $sheet->setCellValue($colH . $row, $s['total']['tepat']);
            $sheet->setCellValue($colT . $row, $s['total']['terlambat']);
            
            $sheet->getStyle("A{$row}:{$colT}{$row}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            $row++;
        }

        $sheet->getColumnDimension('B')->setAutoSize(true);
        $sheet->getColumnDimension('A')->setWidth(5);
        
        $filename = 'Rekap_Kedisiplinan_' . str_replace(' ', '_', $data['kelas']['kelas'] ?? 'Siswa') . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$filename.'"');
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
    
    /**
     * PERBAIKAN SAMBUNGAN FUNGSI 1: EXCEL ABSENSI UMUM (H, S, I, A) + KOP RESMI DAN DUA LOGO
     */
    private function exportToExcel($data)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $periode = strtoupper($data['bulan'] . ' ' . $data['tahun']);

        // Tentukan kolom terakhir data (Jumlah tanggal + No, Nama, H, S, I, A)
        $lastColNum = count($data['tanggal']) + 6; 
        $lastColLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($lastColNum);

        // --- 1. PROSES DUA LOGO (Kiri & Kanan) ---
        $pathLogoDki = ROOTPATH . 'public/assets/img/logodkijakarta.jpg';
        $pathLogoSekolah = ROOTPATH . 'public/assets/img/logosekolah.jpg';

        // --- LOGO KIRI (DKI) ---
        if (file_exists($pathLogoDki)) {
            $drawingLeft = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
            $drawingLeft->setName('Logo DKI');
            $drawingLeft->setPath($pathLogoDki);
            $drawingLeft->setHeight(85);
            $drawingLeft->setCoordinates('A2');
            $drawingLeft->setOffsetX(10);
            $drawingLeft->setWorksheet($sheet);
        }

        // --- LOGO KANAN (SEKOLAH) ---
        if (file_exists($pathLogoSekolah)) {
            $drawingRight = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
            $drawingRight->setName('Logo Sekolah');
            $drawingRight->setPath($pathLogoSekolah);
            $drawingRight->setHeight(85);
            $drawingRight->setCoordinates($lastColLetter . '2');
            $drawingRight->setOffsetX(-40);
            $drawingRight->setWorksheet($sheet);
        }

        // --- 2. TEKS KOP SURAT ---
        $endKopLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($lastColNum - 1);

        $sheet->setCellValue('B1', 'PEMERINTAH PROVINSI DAERAH KHUSUS IBUKOTA JAKARTA');
        $sheet->setCellValue('B2', 'DINAS PENDIDIKAN');
        $sheet->setCellValue('B3', 'SEKOLAH MENENGAH KEJURUAN NEGERI 71 JAKARTA');
        $sheet->setCellValue('B4', 'BIDANG KEAHLIAN : 1. PENGEMBANGAN PERANGKAT LUNAK DAN GIM | 2. SENI DAN INDUSTRI KREATIF');
        $sheet->setCellValue('B5', 'Jl. Radjiman Widyodiningrat Pulo Jahe, Cakung, Jakarta Timur');
        $sheet->setCellValue('B6', 'E-mail: smkntujuh1jakarta@gmail.com Website: http://smkn71jakarta.sch.id/ Kode Pos : 13930');

        for ($r = 1; $r <= 6; $r++) {
            $sheet->mergeCells("B{$r}:{$endKopLetter}{$r}");
            $sheet->getStyle("B{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }
        $sheet->getStyle("B1:B2")->getFont()->setBold(true)->setSize(11);
        $sheet->getStyle("B3")->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle("B4:B6")->getFont()->setSize(9);

        // Garis Double Kop
        $sheet->getStyle("A7:{$lastColLetter}7")->getBorders()->getBottom()->setBorderStyle(Border::BORDER_DOUBLE);

        // --- 3. INFO HEADER LAPORAN ---
        $sheet->setCellValue('A9', 'LAPORAN PRESENSI UMUM SISWA - KELAS ' . strtoupper($data['kelas']['kelas'] ?? ''));
        $sheet->setCellValue('A10', 'PERIODE BULAN: ' . $periode);
        $sheet->getStyle("A9:A10")->getFont()->setBold(true)->setSize(11);

        // --- 4. HEADER TABEL (Baris 12) ---
        $sheet->setCellValue('A12', 'No');
        $sheet->setCellValue('B12', 'Nama Siswa');
        
        $col = 'C';
        foreach ($data['tanggal'] as $tgl) {
            $sheet->setCellValue($col . '12', $tgl->format('d'));
            $sheet->getColumnDimension($col)->setWidth(4);
            $col++;
        }

        // Kolom Rekapitulasi (H, S, I, A)
        $colH = $col++; $sheet->setCellValue($colH . '12', 'H');
        $colS = $col++; $sheet->setCellValue($colS . '12', 'S');
        $colI = $col++; $sheet->setCellValue($colI . '12', 'I');
        $colA = $col++; $sheet->setCellValue($colA . '12', 'A');
        $lastCol = $colA;

        $headerStyle = [
            'font' => ['bold' => true],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E9E9E9']
            ],
        ];
        $sheet->getStyle("A12:{$lastCol}12")->applyFromArray($headerStyle);

        // --- 5. PENGISIAN DATA ---
        $row = 13;
        foreach ($data['listSiswa'] as $siswaIdx => $s) {
            $sheet->setCellValue('A' . $row, $siswaIdx + 1);
            $sheet->setCellValue('B' . $row, strtoupper($s['nama_siswa']));
            
            $colData = 'C';
            $h = 0; $s_t = 0; $i_t = 0; $a_t = 0;

            foreach ($data['listAbsen'] as $dayAbsen) {
                $status = $dayAbsen[$siswaIdx]['id_kehadiran'] ?? null;
                $val = '';

                if ($status == 1) { 
                    $val = 'H'; $h++; 
                } elseif ($status == 2) { 
                    $val = 'S'; $s_t++; 
                } elseif ($status == 3) { 
                    $val = 'I'; $i_t++; 
                } elseif ($status == 4) { 
                    $val = 'A'; $a_t++; 
                } else { 
                    if (!$dayAbsen['lewat']) {
                        $val = 'A'; $a_t++;
                    }
                }

                $sheet->setCellValue($colData . $row, $val);
                if ($val == 'A') {
                    $sheet->getStyle($colData . $row)->getFont()->getColor()->setRGB('FF0000');
                }
                $sheet->getStyle($colData . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $colData++;
            }

            $sheet->setCellValue($colH . $row, $h);
            $sheet->setCellValue($colS . $row, $s_t);
            $sheet->setCellValue($colI . $row, $i_t);
            $sheet->setCellValue($colA . $row, $a_t);

            $sheet->getStyle("A{$row}:{$lastCol}{$row}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle($colH . $row . ":{$colA}" . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $row++;
        }

        $sheet->getColumnDimension('B')->setAutoSize(true);
        $sheet->getColumnDimension('A')->setWidth(5);

        $filename = 'Laporan_Absen_' . str_replace(' ', '_', $data['kelas']['kelas'] ?? 'Siswa') . '_' . $data['bulan'] . '.xlsx';
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}