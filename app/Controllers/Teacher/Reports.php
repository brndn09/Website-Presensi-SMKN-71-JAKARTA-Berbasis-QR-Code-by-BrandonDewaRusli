<?php

namespace App\Controllers\Teacher;

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

use App\Models\KelasModel;
use App\Models\SiswaModel;
use App\Models\PresensiSiswaModel;

class Reports extends BaseController
{
    protected SiswaModel $siswaModel;
    protected KelasModel $kelasModel;
    protected PresensiSiswaModel $presensiSiswaModel;

    public function __construct()
    {
        $this->siswaModel = new SiswaModel();
        $this->kelasModel = new KelasModel();
        $this->presensiSiswaModel = new PresensiSiswaModel();
    }

    public function index()
    {
        $user = user();
        if (!is_wali_kelas()) {
            return redirect()->to('admin')->with('error', 'Anda bukan Wali Kelas.');
        }

        $kelas = $this->kelasModel->getKelasByWali($user->id_guru);

        if (empty($kelas)) {
            return redirect()->to('teacher/dashboard')->with('error', 'Kelas belum ditugaskan.');
        }

        $data = [
            'title' => 'Laporan Presensi Kelas',
            'ctx' => 'laporan-kelas',
            'kelas' => $kelas
        ];

        return view('teacher/reports', $data);
    }

    /**
     * FUNGSI 1: GENERATE REKAP BULANAN UMUM (H, S, I, A)
     */
    public function generate()
    {
        $user = user();
        $kelas = $this->kelasModel->getKelasByWali($user->id_guru);

        if (empty($kelas)) {
            return redirect()->to('teacher/dashboard');
        }

        $idKelas = $kelas['id_kelas'];
        $siswa = $this->siswaModel->getSiswaByKelas($idKelas);
        $type = $this->request->getVar('type');
        $bulan = $this->request->getVar('bulan');

        if (empty($siswa)) {
            return redirect()->back()->with('error', 'Data siswa kosong!');
        }

        $kelasData = (array) $this->kelasModel->getKelas($idKelas);

        $begin = new Time($bulan . '-01', locale: 'id');
        $end = (new DateTime($begin->format('Y-m-t')))->modify('+1 day');
        $interval = DateInterval::createFromDateString('1 day');
        $period = new DatePeriod($begin, $interval, $end);

        $arrayTanggal = [];
        $dataAbsen = [];

        foreach ($period as $value) {
            if (!in_array($value->format('D'), ['Sat', 'Sun'])) {
                $lewat = Time::parse($value->format('Y-m-d'))->isAfter(Time::today());
                $absenByTanggal = $this->presensiSiswaModel->getPresensiByKelasTanggal($idKelas, $value->format('Y-m-d'));
                $absenByTanggal['lewat'] = $lewat;
                
                $dataAbsen[] = $absenByTanggal;
                $arrayTanggal[] = Time::createFromInstance($value, locale: 'id');
            }
        }

        $laki = 0;
        foreach ($siswa as $value) {
            if ($value['jenis_kelamin'] != 'Perempuan') $laki++;
        }

        $data = [
            'title'      => 'Laporan Presensi Bulanan',
            'tanggal'    => $arrayTanggal,
            'bulan'      => $begin->toLocalizedString('MMMM'),
            'tahun'      => $begin->format('Y'),
            'listAbsen'  => $dataAbsen,
            'listSiswa'  => $siswa,
            'rekapSiswa' => [
                'laki'      => $laki,
                'perempuan' => count($siswa) - $laki
            ],
            'kelas'      => $kelasData,
            'grup'       => "Kelas " . $kelasData['kelas'],
            'batas'      => "06:31:00"
        ];

        if ($type == 'xls') return $this->exportToExcel($data);

        return view('admin/generate-laporan/laporan-siswa', $data) . view('admin/generate-laporan/topdf');
    }

    /**
     * FUNGSI 2: GENERATE REKAP DETAIL (JAM MASUK, JAM PULANG, TERLAMBAT)
     */
    public function terlambat()
    {
        $user = user();
        $kelas = $this->kelasModel->getKelasByWali($user->id_guru);
        if (empty($kelas)) return redirect()->to('teacher/dashboard');

        $idKelas = $kelas['id_kelas'];
        $type    = $this->request->getVar('type');
        $jenisPeriode = $this->request->getVar('jenis_periode');
        $batasMasuk = "06:31:00"; 

        $siswa = $this->siswaModel->getSiswaByKelas($idKelas);
        if (empty($siswa)) return redirect()->back()->with('error', 'Data siswa kosong!');

        if ($jenisPeriode === 'mingguan') {
            $tglMulai   = $this->request->getVar('tanggal_mulai');
            $tglSelesai = $this->request->getVar('tanggal_selesai');

            $begin = new Time($tglMulai, locale: 'id');
            $end   = (new DateTime($tglSelesai))->modify('+1 day');
            
            $periodeTeks = Time::parse($tglMulai, locale: 'id')->toLocalizedString('dd MMMM Y') . ' s/d ' . Time::parse($tglSelesai, locale: 'id')->toLocalizedString('dd MMMM Y');
        } else {
            $bulan = $this->request->getVar('bulan'); 
            $begin = new Time($bulan . '-01', locale: 'id');
            $end   = (new DateTime($begin->format('Y-m-t')))->modify('+1 day');
            
            $periodeTeks = strtoupper($begin->toLocalizedString('MMMM') . ' ' . $begin->format('Y'));
        }

        $interval = DateInterval::createFromDateString('1 day');
        $period = new DatePeriod($begin, $interval, $end);

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
            return redirect()->back()->with('error', 'Tidak ada hari efektif sekolah pada periode ini!');
        }

        $rekapSiswa = [];
        foreach ($siswa as $s) {
            $dataHarian = [];
            $stats = ['tepat' => 0, 'terlambat' => 0];

            foreach ($listTanggal as $tgl) {
                $p = $this->presensiSiswaModel->where(['id_siswa' => $s['id_siswa'], 'tanggal' => $tgl['date']])->first();
                
                $status = '-'; $jamM = '-'; $jamP = '-'; $lateText = '';

                if ($p && $p['id_kehadiran'] == 1) { 
                    $jamM = $p['jam_masuk'] ?? '-';
                    $jamP = $p['jam_keluar'] ?? '-';
                    
                    if (strtotime($jamM) <= strtotime($batasMasuk)) {
                        $status = 'H'; $stats['tepat']++;
                    } else {
                        $status = 'T'; $stats['terlambat']++;
                        $diff = strtotime($jamM) - strtotime($batasMasuk);
                        $jam = floor($diff / 3600);
                        $menit = floor(($diff / 60) % 60);
                        $lateText = ($jam > 0) ? $jam . "j " . $menit . "m" : $menit . "m";
                    }
                }

                $dataHarian[$tgl['date']] = [
                    'status' => $status,
                    'masuk'  => $jamM,
                    'pulang' => $jamP,
                    'late'   => $lateText
                ];
            }

            $rekapSiswa[] = [
                'nama'   => $s['nama_siswa'],
                'nis'    => $s['nis'],
                'detail' => $dataHarian,
                'total'  => $stats
            ];
        }

        $data = [
            'title'       => 'Rekap Presensi Detail',
            'listTanggal' => $listTanggal,
            'rekap'       => $rekapSiswa,
            'kelas'       => (array)$kelas,
            'periode_teks'=> $periodeTeks,
            'batas'       => $batasMasuk,
            'grup'        => "Kelas " . ($kelas['kelas'] ?? '')
        ];

        if ($type == 'xls') return $this->exportTerlambatExcel($data);

        return view('admin/generate-laporan/laporan-rekap-absensi-pdf', $data) . view('admin/generate-laporan/topdf');
    }

    /**
     * EXPORT EXCEL 1: REKAP BULANAN UMUM (H, S, I, A) + DUA LOGO & KOP RESMI
     */
    private function exportToExcel($data)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        $lastColNum = count($data['tanggal']) + 6; 
        $lastColLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($lastColNum);

        // --- 1. PROSES DUA LOGO (Kiri & Kanan) ---
        $pathLogoDki = ROOTPATH . 'public/assets/img/logodkijakarta.jpg';
        $pathLogoSekolah = ROOTPATH . 'public/assets/img/logosekolah.jpg';

        if (file_exists($pathLogoDki)) {
            $drawingLeft = new Drawing();
            $drawingLeft->setName('Logo DKI');
            $drawingLeft->setPath($pathLogoDki);
            $drawingLeft->setHeight(85);
            $drawingLeft->setCoordinates('A2');
            $drawingLeft->setOffsetX(10);
            $drawingLeft->setWorksheet($sheet);
        }

        if (file_exists($pathLogoSekolah)) {
            $drawingRight = new Drawing();
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
        $sheet->setCellValue('A9', 'LAPORAN PRESENSI UMUM SISWA - KELAS ' . strtoupper($data['kelas']['kelas']));
        $sheet->setCellValue('A10', 'PERIODE BULAN: ' . strtoupper($data['bulan'] . ' ' . $data['tahun']));
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

        $colH = $col++; $colS = $col++; $colI = $col++; $colA = $col++;
        $finalCol = $colA;

        $sheet->setCellValue($colH . '12', 'H');
        $sheet->setCellValue($colS . '12', 'S');
        $sheet->setCellValue($colI . '12', 'I');
        $sheet->setCellValue($colA . '12', 'A');

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
        $sheet->getStyle("A12:{$finalCol}12")->applyFromArray($headerStyle);

        // --- 5. PENGISIAN DATA ---
        $row = 13;
        foreach ($data['listSiswa'] as $siswaIdx => $s) {
            $sheet->setCellValue('A' . $row, $row - 12);
            $sheet->setCellValue('B' . $row, strtoupper($s['nama_siswa']));
            
            $colData = 'C';
            $h = $s_t = $i_t = $a_t = 0;

            foreach ($data['listAbsen'] as $dayAbsen) {
                $status = $dayAbsen[$siswaIdx]['id_kehadiran'] ?? null;
                $val = '';
                if ($status == 1) { $val = 'H'; $h++; }
                elseif ($status == 2) { $val = 'S'; $s_t++; }
                elseif ($status == 3) { $val = 'I'; $i_t++; }
                elseif ($status == 4) { $val = 'A'; $a_t++; }
                else { if (!$dayAbsen['lewat']) { $val = 'A'; $a_t++; } }

                $sheet->setCellValue($colData . $row, $val);
                if ($val == 'A') $sheet->getStyle($colData . $row)->getFont()->getColor()->setRGB('FF0000');
                $sheet->getStyle($colData . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $colData++;
            }

            $sheet->setCellValue($colH . $row, $h);
            $sheet->setCellValue($colS . $row, $s_t);
            $sheet->setCellValue($colI . $row, $i_t);
            $sheet->setCellValue($colA . $row, $a_t);
            
            $sheet->getStyle("A{$row}:{$finalCol}{$row}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle($colH . $row . ":{$colA}" . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $row++;
        }
        
        $sheet->getColumnDimension('B')->setAutoSize(true);
        $sheet->getColumnDimension('A')->setWidth(5);
        
        $filename = 'Laporan_Absen_' . str_replace(' ', '_', $data['kelas']['kelas']) . '_' . $data['bulan'] . '.xlsx';
        $this->downloadExcel($spreadsheet, $filename);
    }

    /**
     * EXPORT EXCEL 2: REKAP DETAIL KEDISIPLINAN + DUA LOGO & KOP RESMI
     */
    private function exportTerlambatExcel($data)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $lastColNum = count($data['listTanggal']) + 4;
        $lastColLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($lastColNum);
        
        // --- 1. PROSES DUA LOGO (Kiri & Kanan) ---
        $pathLogoDki = ROOTPATH . 'public/assets/img/logodkijakarta.jpg'; 
        $pathLogoSekolah = ROOTPATH . 'public/assets/img/logosekolah.jpg';

        if (file_exists($pathLogoDki)) {
            $drawingLeft = new Drawing();
            $drawingLeft->setName('Logo DKI');
            $drawingLeft->setPath($pathLogoDki);
            $drawingLeft->setHeight(85); 
            $drawingLeft->setCoordinates('A2'); 
            $drawingLeft->setOffsetX(10);
            $drawingLeft->setWorksheet($sheet);
        }

        if (file_exists($pathLogoSekolah)) {
            $drawingRight = new Drawing();
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

        // Membuat Garis Double Tebal di bawah Kop Surat
        $sheet->getStyle("A7:{$lastColLetter}7")->getBorders()->getBottom()->setBorderStyle(Border::BORDER_DOUBLE);

        // --- 3. INFO HEADER LAPORAN ---
        $sheet->setCellValue('A9', 'REKAPITULASI KEDISIPLINAN PRESENSI SISWA');
        $sheet->mergeCells("A9:{$lastColLetter}9");
        $sheet->getStyle("A9")->getFont()->setBold(true)->setSize(12);

        $sheet->setCellValue('A11', 'KELAS: ' . strtoupper($data['kelas']['kelas'] ?? '-'));
        $sheet->setCellValue('A12', 'PERIODE: ' . strtoupper($data['periode_teks'])); 
        $sheet->setCellValue('A13', 'BATAS TOLERANSI MASUK: ' . $data['batas'] . ' WIB');
        $sheet->getStyle("A11:A13")->getFont()->setBold(true);

        // --- 4. HEADER TABEL (Baris 15) ---
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
        
        $filename = 'Rekap_Kedisiplinan_' . str_replace(' ', '_', $data['kelas']['kelas']) . '.xlsx';
        $this->downloadExcel($spreadsheet, $filename);
    }

    private function downloadExcel($spreadsheet, $filename)
    {
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}