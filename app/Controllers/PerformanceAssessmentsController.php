<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\PerformanceAssessmentModel;
use App\Models\EmployeeAssessmentModel;
use App\Models\BatchModel;
use App\Models\PeriodeModel;
use App\Models\PresenceModel;
use App\Models\NewPAModel;
use App\Models\BsmcModel;
use App\Models\JarumModel;
use App\Models\RossoModel;
use App\Models\EvaluationAspectModel;
use App\Models\FinalAssssmentModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;


class PerformanceAssessmentsController extends BaseController
{
    protected $role;
    protected $paModel;
    protected $eaModel;
    protected $periodeModel;
    protected $batchModel;
    protected $presenceModel;
    protected $newPAModel;
    protected $bsmcModel;
    protected $jarumModel;
    protected $evaluationAspectModel;
    protected $rossoModel;
    protected $finalAssssmentModel;

    public function __construct()
    {
        $this->paModel = new PerformanceAssessmentModel();
        $this->eaModel = new EmployeeAssessmentModel();
        $this->periodeModel = new PeriodeModel();
        $this->batchModel = new BatchModel();
        $this->presenceModel = new PresenceModel();
        $this->newPAModel = new NewPAModel();
        $this->bsmcModel = new BsmcModel();
        $this->jarumModel = new JarumModel();
        $this->rossoModel = new RossoModel();
        $this->evaluationAspectModel = new EvaluationAspectModel();
        $this->finalAssssmentModel = new FinalAssssmentModel();
        $this->role = session()->get('role');
    }

    const bobot_nilai = [
        1 => 15,
        2 => 30,
        3 => 45,
        4 => 60,
        5 => 85,
        6 => 100
    ];

    private function calculateSkor($grade)
    {
        $map = [4 => 'A', 3 => 'B', 2 => 'C', 1 => 'D'];
        return $map[$grade] ?? 0;
    }

    private function calculateGradeBatch($average)
    {
        if (!is_numeric($average)) return '-';
        if ($average >= 3.5) return 'A';
        if ($average >= 2.5) return 'B';
        if ($average >= 1.5) return 'C';
        return 'D';
    }

    public function index()
    {
        //
    }

    public function penilaianPerArea($main_factory)
    {
        $main_factory = urldecode($main_factory);
        // dd($main_factory);
        $penilaian = $this->paModel->getAssessmentsByMainFactory($main_factory);
        // dd($penilaian);
        $data = [
            'role' => session()->get('role'),
            'title' => 'Report Penilaian',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'active7' => '',
            'active8' => 'active',
            'penilaian' => $penilaian,
            'main_factory' => $main_factory
        ];
        // dd ($data);

        return view('penilaian/reportareaperarea', $data);
    }

    public function excelReportPerPeriode($main_factory, $batch_name, $periode_name)
    {
        // dd($main_factory, $batch_name, $periode_name);
        $reportbatch = $this->eaModel->getAssessmentsByPeriod($main_factory, $batch_name, $periode_name);
        $bulan = $this->periodeModel->getPeriodeByNamaBatchAndNamaPeriode($batch_name, $periode_name);
        $uniqueSheets = [];
        foreach ($reportbatch as $item) {
            $key = $item['main_job_role_name'];
            if (!in_array($key, $uniqueSheets)) {
                $uniqueSheets[] = $key;
            }
        }
        // dd($reportbatch);

        $spreadsheet = new Spreadsheet();
        $spreadsheet->removeSheetByIndex(0); // Hapus sheet default

        $gradeD = [];
        $gradeDPerBagian = []; // key = main_job_role_name

        // function getMainBagian($bagian)
        // {
        //     $bagian = strtoupper(trim($bagian));

        //     if (strpos($bagian, 'OPERATOR') === 0) {
        //         return 'OPERATOR';
        //     } elseif (strpos($bagian, 'MONTIR') === 0) {
        //         return 'MONTIR';
        //     } elseif (strpos($bagian, 'ROSSO') === 0) {
        //         return 'ROSSO';
        //     }

        //     // fallback: tetap pakai nama bagian aslinya
        //     return $bagian;
        // }

        // 1. Grup per employee_code
        $grouped = [];
        foreach ($reportbatch as $row) {
            $code = $row['employee_code'];
            if (! isset($grouped[$code])) {
                // inisialisasi data employee
                $grouped[$code] = [
                    'employee_code'      => $row['employee_code'],
                    'employee_name'      => $row['employee_name'],
                    'shift'              => $row['shift'],
                    'gender'             => $row['gender'],
                    'date_of_joining'    => $row['date_of_joining'],
                    'main_job_role_name' => $row['main_job_role_name'],
                    'nilai'              => $row['nilai'],
                    'permit'             => $row['permit'],
                    'sick'               => $row['sick'],
                    'absent'             => $row['absent'],
                    'factory_name'       => $row['factory_name'],
                    'assessments'        => [],   // tempatkan penilaian
                ];
            }
            // tambahkan assessment ke karyawan tersebut
            $grouped[$code]['assessments'][] = [
                'jobdescription' => $row['jobdescription'],
                'description'    => $row['description'],
                'score'          => $row['score'],
                'id_assessment'  => $row['id_assessment'],
            ];
        }

        // 2. Ubah jadi indexed array, bukan associative by code
        $employees = array_values($grouped);
        // dd($employees);

        foreach ($uniqueSheets as $sheetName) {
            $sheet = $spreadsheet->createSheet();
            $sheet->setTitle(substr($sheetName, 0, 31)); // Nama sheet sesuai area dan bagian
            // Pisahkan area dan nama bagian
            list($currentBagian) = explode(' ', $sheetName, 2);

            // Filter data untuk sheet ini
            $dataFiltered = array_filter($employees, function ($item) use ($currentBagian) {
                return $item['main_job_role_name'] === $currentBagian;
            });
            // dd($dataFiltered);
            // Ambil nama bulan dari end_date
            $namaBulan = isset($bulan['nama_bulan']) ? strtoupper($bulan['nama_bulan']) : '';
            // Kelompokkan berdasarkan shift
            $dataByShift = $this->groupByShift($dataFiltered);
            // Header Utama
            $sheet->mergeCells('A1:G1')->setCellValue('A1', 'REPORT PENILAIAN - ' . strtoupper($sheetName));
            $sheet->mergeCells('A2:G2')->setCellValue('A2', 'DEPARTEMEN KAOS KAKI');
            $sheet->mergeCells('A3:G3')->setCellValue('A3', '(PERIODE ' . $namaBulan . ' ' . strtoupper($batch_name) . ')');
            $sheet->getStyle('A1:A3')->applyFromArray([
                'font' => ['bold' => true, 'size' => 16, 'name' => 'Times New Roman'],
                'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            ]);

            // Header Kolom Statis
            $headers = ['NO', 'KODE KARTU', 'NAMA KARYAWAN', 'SHIFT', 'L/P', 'TGL. MASUK KERJA', 'BAGIAN', 'PREVIOUS GRADE'];
            $startCol = 1; // Kolom A
            foreach ($headers as $header) {
                $colLetter = Coordinate::stringFromColumnIndex($startCol);
                $sheet->getStyle($colLetter . '5')->getAlignment()->setWrapText(true);
                $sheet->mergeCells($colLetter . '5:' . $colLetter . '6')->setCellValue($colLetter . '5', $header);
                $sheet->getStyle($colLetter . '5:' . $colLetter . '6')->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    ],
                    'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
                ]);
                $startCol++;
            }

            // Header Dinamis untuk keterangan dan jobdesc
            $currentCol = 9; // Dimulai dari kolom I
            $jobdescGrouped = [];

            foreach ($dataFiltered as $p) {
                foreach ($p['assessments'] as $assessment) {
                    // Jika description & jobdescription adalah string JSON, decode dulu
                    // $keterangan = json_decode($assessment['description'], true);
                    // $jobdesc = json_decode($assessment['jobdescription'], true);

                    // // Jika bukan JSON, langsung pakai
                    // if (!is_array($keterangan)) {
                    //     $keterangan = [$assessment['description']];
                    // }
                    // if (!is_array($jobdesc)) {
                    //     $jobdesc = [$assessment['jobdescription']];
                    // }
                    $keterangan = [$assessment['description']];
                    $jobdesc = [$assessment['jobdescription']];

                    // Gabungkan berdasarkan indeks
                    foreach ($keterangan as $index => $ket) {
                        $job = $jobdesc[$index] ?? '';
                        $jobdescGrouped[$ket][] = $job;
                    }
                }
            }
            // dd($jobdescGrouped);

            // Hilangkan duplikasi dalam setiap keterangan
            foreach ($jobdescGrouped as $keterangan => &$jobs) {
                $jobs = array_unique($jobs);
            }
            unset($jobs); // Lepaskan referensi

            // Tulis header keterangan dan jobdesc
            foreach ($jobdescGrouped as $keterangan => $jobs) {
                $startColLetter = Coordinate::stringFromColumnIndex($currentCol);
                $endColLetter = Coordinate::stringFromColumnIndex($currentCol + count($jobs) - 1);

                // Header keterangan
                $sheet->mergeCells($startColLetter . '5:' . $endColLetter . '5');
                $sheet->setCellValue($startColLetter . '5', $keterangan);
                $sheet->getStyle($startColLetter . '5:' . $endColLetter . '5')->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    ],
                    'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
                ]);

                // Header jobdesc
                foreach ($jobs as $job) {
                    $sheet->setCellValue(Coordinate::stringFromColumnIndex($currentCol) . '6', $job);
                    $sheet->getStyle(Coordinate::stringFromColumnIndex($currentCol) . '6')->applyFromArray([
                        'font' => ['bold' => true],
                        'alignment' => [
                            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                            'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                        ],
                        'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
                    ]);
                    $currentCol++;
                }
            }

            // Header absen
            $sheet->mergeCells(Coordinate::stringFromColumnIndex($currentCol + 2) . '4:' . Coordinate::stringFromColumnIndex($currentCol + 4) . '4');
            $sheet->setCellValue(Coordinate::stringFromColumnIndex($currentCol + 2) . '4', 'KEHADIRAN');
            $sheet->getStyle(Coordinate::stringFromColumnIndex($currentCol + 2) . '4:' . Coordinate::stringFromColumnIndex($currentCol + 4) . '4')->applyFromArray([
                'font' => ['bold' => true],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
                'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
            ]);
            // Tambahkan Header SAKIT, IZIN, MANGKIR, CUTI
            $additionalHeaders = ['GRADE', 'SKOR', 'SI', 'MI', 'M', 'JML HARI TIDAK MASUK KERJA', 'PERSENTASE KEHADIRAN', 'AKUMULASI ABSENSI', 'GRADE AKHIR', 'TRACKING'];
            foreach ($additionalHeaders as $header) {
                $sheet->mergeCells(Coordinate::stringFromColumnIndex($currentCol) . '5:' . Coordinate::stringFromColumnIndex($currentCol) . '6');
                $sheet->setCellValue(Coordinate::stringFromColumnIndex($currentCol) . '5', $header);
                $sheet->getStyle(Coordinate::stringFromColumnIndex($currentCol) . '5:' . Coordinate::stringFromColumnIndex($currentCol) . '6')->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    ],
                    'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
                ]);
                $currentCol++;
            }



            // Tulis Data Karyawan Berdasarkan Shift
            $row = 7;
            foreach ($dataByShift as $shift => $karyawan) {
                // Tulis Data Karyawan
                $no = 1;
                foreach ($karyawan as $p) {
                    // dd($karyawan);
                    $sheet->setCellValue('A' . $row, $no++);
                    $sheet->setCellValue('B' . $row, $p['employee_code']);
                    $sheet->setCellValue('C' . $row, $p['employee_name']);
                    $sheet->setCellValue('D' . $row, $p['shift']);
                    $sheet->setCellValue('E' . $row, $p['gender']);
                    $sheet->setCellValue('F' . $row, $p['date_of_joining']);
                    $sheet->setCellValue('G' . $row, $p['main_job_role_name']);
                    // dd($p);
                    // $sheet->setCellValue('H' . $row, $p['previous_grade'] ?? '-');
                    // $col = 'I';
                    // if (isset($p['assessments']) && is_array($p['assessments'])) {
                    //     foreach ($p['assessments'] as $assessment) {
                    //         $score = $assessment['score'] ?? '-';
                    //         $sheet->setCellValue($col . $row, $score);
                    //         $col++;
                    //     }
                    // }
                    // Decode nilai
                    $nilai = $assessment['score'];
                    // dd($nilai);
                    $colIndex = 9; // Dimulai dari kolom I


                    // Set nilai
                    foreach ($p['assessments'] as $assessment) {
                        $score = $assessment['score'] ?? '-';
                        $sheet->setCellValue(Coordinate::stringFromColumnIndex($colIndex) . $row, $score);
                        $colIndex++;


                        // $skor = $this->calculateSkor($grade);
                        // Set job description and additional columns
                        // foreach ($nilai as $value) {
                        //     $sheet->setCellValue(Coordinate::stringFromColumnIndex($colIndex) . $row, $value);
                        //     $colIndex++;
                        // }

                        // set grade
                        // $sheet->setCellValue(Coordinate::stringFromColumnIndex($colIndex) . $row, $p['nilai'] ?? '-');
                        // $colIndex++;
                        // // set skor
                        // $sheet->setCellValue(Coordinate::stringFromColumnIndex($colIndex) . $row, $nilai);
                        // $colIndex++;
                        $skor = $assessment['nilai'] ?? 0;
                    }
                    $grade = $this->calculateGradeBatch($p['nilai'] ?? 0);

                    $sheet->setCellValue(Coordinate::stringFromColumnIndex($colIndex) . $row, $grade);
                    $colIndex++;

                    $sheet->setCellValue(Coordinate::stringFromColumnIndex($colIndex) . $row, $p['nilai'] ?? '-');
                    $colIndex++;

                    // Set absen
                    $izin = $p['permit'] ?? 0;
                    $sakit = $p['sick'] ?? 0;
                    $mangkir = $p['absent'] ?? 0;
                    $cuti = $p['leave'] ?? 0;
                    $totalAbsen = ($sakit * 1) + ($izin * 2) + ($mangkir * 3);
                    // $kehadiran = 100 - $totalAbsen;

                    //Set Persentase Kehadiran
                    $start_date = new \DateTime($bulan['start_date']);
                    $end_date = new \DateTime($bulan['end_date']);
                    $selisih = $start_date->diff($end_date);
                    $totalHari = $selisih->days + 1; // +1 untuk menyertakan hari pertama
                    $jmlLibur = $bulan['holiday'];
                    $persentaseKehadiran = (($totalHari - $jmlLibur - $totalAbsen) / ($totalHari - $jmlLibur)) * 100;

                    // =IF(BW9<0.94,"-1",IF(BW9>0.93,"0"))
                    $accumulasi = $persentaseKehadiran < 94 ? -1 : 0;


                    // hasil akhir = skor + accumulasi
                    $hasil_akhir = $skor + $accumulasi;
                    $grade_akhir = $this->calculateGradeBatch($hasil_akhir);
                    // Update grade akhir ke database
                    // $this->penilaianmodel->updateGradeAkhir($p['karyawan_id'], $p['id_periode'], $grade_akhir);

                    // $tracking = $previous_grade . $grade_akhir;

                    $sheet->setCellValue(Coordinate::stringFromColumnIndex($colIndex) . $row, $sakit);
                    $colIndex++;
                    $sheet->setCellValue(Coordinate::stringFromColumnIndex($colIndex) . $row, $izin);
                    $colIndex++;
                    $sheet->setCellValue(Coordinate::stringFromColumnIndex($colIndex) . $row, $mangkir);
                    $colIndex++;

                    //jml hari tidak masuk kerja
                    $sheet->setCellValue(Coordinate::stringFromColumnIndex($colIndex) . $row, $totalAbsen);
                    $colIndex++;
                    //persentase kehadiran
                    $sheet->setCellValue(Coordinate::stringFromColumnIndex($colIndex) . $row, round($persentaseKehadiran) . '%'); // Tambahkan , 2 jika ingin menampilkan desimal
                    $colIndex++;
                    //accumulasi absensi
                    $sheet->setCellValue(Coordinate::stringFromColumnIndex($colIndex) . $row, $accumulasi);
                    $colIndex++;
                    //grade akhir
                    $sheet->setCellValue(Coordinate::stringFromColumnIndex($colIndex) . $row, '-');
                    $colIndex++;
                    //tracking
                    $sheet->setCellValue(Coordinate::stringFromColumnIndex($colIndex) . $row, '-');
                    // $colIndex++;

                    $jobdescArr = json_decode($p['jobdesc'] ?? '[]', true);
                    if (!is_array($jobdescArr)) {
                        $jobdescArr = [$jobdescArr];
                    }
                    $nilaiArr = json_decode($p['bobot_nilai'] ?? '[]', true);
                    if (!is_array($nilaiArr)) {
                        $nilaiArr = [$nilaiArr];
                    }
                    $jobdescArr = array_values($jobdescArr);
                    $nilaiArr   = array_values($nilaiArr);

                    // filter hanya yang nilai ≤ 4
                    $failJobdesc = [];
                    $failNilai   = [];
                    foreach ($nilaiArr as $i => $val) {
                        if ($val > 0 && $val < 4) {
                            // Sekarang $jobdescArr[$i] pasti ada
                            $failJobdesc[] = $jobdescArr[$i] ?? '(tidak ada)';
                            $failNilai[]   = $val;
                        }
                    }

                    // if ($grade_akhir === 'D' && ! empty($failJobdesc)) {
                    //     $bagian = getMainBagian($p['main_job_role_name']);
                    //     if (! isset($gradeDPerBagian[$bagian])) {
                    //         $gradeDPerBagian[$bagian] = [];
                    //     }

                    //     $entry = [
                    //         'no'            => $no - 1,
                    //         'employee_code'    => $p['employee_code'],
                    //         'employee_name' => $p['employee_name'],
                    //         'izin'          => $p['izin']      ?? 0,
                    //         'sakit'         => $p['sakit']     ?? 0,
                    //         'mangkir'       => $p['mangkir']   ?? 0,
                    //         'accumulasi'    => $accumulasi,
                    //         'grade_akhir'   => $grade_akhir,
                    //         'failJobdesc'   => $failJobdesc,
                    //         'failNilai'     => $failNilai,
                    //     ];

                    //     switch ($bagian) {
                    //         case 'OPERATOR':
                    //             $entry['prod_op'] = $p['prod_op'] ?? 0;
                    //             $entry['bs_mc']   = $p['bs_mc']   ?? 0;
                    //             break;
                    //         case 'ROSSO':
                    //             $entry['prod_rosso'] = $p['prod_rosso'] ?? 0;
                    //             $entry['perb_rosso'] = $p['perb_rosso'] ?? 0;
                    //             break;
                    //         case 'MONTIR':
                    //             $entry['used_needle'] = $p['used_needle'] ?? 0;
                    //             break;
                    //     }

                    //     $gradeDPerBagian[$bagian][] = $entry;
                    // }

                    //style from array
                    $sheet->getStyle('A' . $row . ':' . Coordinate::stringFromColumnIndex($colIndex) . $row)->applyFromArray([
                        'font' => ['name' => 'Times New Roman', 'size' => 10],
                        'alignment' => [
                            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                            'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                        ],
                        'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
                    ]);
                    $row++;
                }
                // Tulis Total Karyawan
                // $sheet->setCellValue('B' . $row, 'TOTAL' . $shift);
                $sheet->setCellValue('B' . $row, 'TOTAL');
                $sheet->mergeCells('B' . $row . ':C' . $row);
                $sheet->setCellValue('D' . $row, count($karyawan));
                $sheet->getStyle('B' . $row . ':D' . $row)->applyFromArray([
                    'font' => ['bold' => false],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    ],
                    'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
                ]);
                $row++;
            }

            // Set auto-size untuk semua kolom
            foreach ($sheet->getColumnIterator() as $column) {
                $sheet->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
            }
        }

        // dd($shift, $karyawan);
        // sheet baru untuk report tracking
        $sheet = $spreadsheet->createSheet();
        $sheet->setTitle('TRACKING');

        // Header Utama
        $sheet->mergeCells('A1:G1')->setCellValue('A1', 'REPORT TRACKING - ' . strtoupper($main_factory));
        $sheet->mergeCells('A2:G2')->setCellValue('A2', 'DEPARTEMEN KAOS KAKI');
        $sheet->mergeCells('A3:G3')->setCellValue('A3', '(PERIODE ' . strtoupper($periode_name)  . ' ' . strtoupper($batch_name) . ')');
        $sheet->getStyle('A1:A3')->applyFromArray([
            'font' => ['bold' => true, 'size' => 16, 'name' => 'Times New Roman'],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
        ]);

        // Header Kolom Statis
        $headers = ['NO', 'KODE KARTU', 'NAMA KARYAWAN', 'SHIFT', 'L/P', 'TGL. MASUK KERJA', 'BAGIAN', 'AREA', 'TRACKING'];
        $startCol = 1; // Kolom A
        foreach ($headers as $header) {
            $colLetter = Coordinate::stringFromColumnIndex($startCol);
            $sheet->getStyle($colLetter . '5')->getAlignment()->setWrapText(true);
            $sheet->mergeCells($colLetter . '5:' . $colLetter . '6')->setCellValue($colLetter . '5', $header);
            $sheet->getStyle($colLetter . '5:' . $colLetter . '6')->applyFromArray([
                'font' => [
                    'name' => 'Times New Roman',
                    'size' => 10,
                    'bold' => true,
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
                'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
            ]);
            $startCol++;
        }

        // sort data by kode kartu
        $sortOrders = [
            'KKMA',
            'KKMB',
            'KKMC',
            'KKMNS',
            'KKSA',
            'KKSB',
            'KKSC',
            'KKJHA',
            'KKJHB',
            'KKJHC',
            'KK2MA',
            'KK2MB',
            'KK2MC',
            'KK2MNS',
            'KK2SA',
            'KK2SB',
            'KK2SC',
            'KK5A',
            'KK5B',
            'KK5C',
            'KK5NS',
            'KK7A',
            'KK7B',
            'KK7C',
            'KK7NS',
            'KK8MA',
            'KK8MB',
            'KK8MC',
            'KK8MNS',
            'KK8SA',
            'KK8SB',
            'KK8SC',
            'KK9A',
            'KK9B',
            'KK9C',
            'KK9NS',
            'KK10A',
            'KK10B',
            'KK10C',
            'KK10NS',
            'KK11A',
            'KK11B',
            'KK11C',
            'KK11NS'
        ];

        $sortedData = [];
        foreach ($reportbatch as $p) {
            $sortedData[] = [
                'employee_code' => $p['employee_code'],
                'employee_name' => $p['employee_name'],
                'shift' => $p['shift'],
                'gender' => $p['gender'],
                'date_of_joining' => $p['date_of_joining'],
                'main_job_role_name' => $p['main_job_role_name'],
                'factory_name' => $p['factory_name'],
                'nilai' => $p['nilai'],
                // 'grade_akhir' => $p['grade_akhir'],
                // 'previous_grade' => $p['previous_grade'],
                'description' => json_decode($p['description'], true),
                'jobdescription' => json_decode($p['jobdescription'], true),
            ];
        }

        // Sort the flattened array by employee_code
        usort($sortedData, function ($a, $b) use ($sortOrders) {
            // Ekstrak prefix kode kartu
            preg_match('/^[A-Z]+/', $a['employee_code'], $matchA);
            preg_match('/^[A-Z]+/', $b['employee_code'], $matchB);

            $prefixA = $matchA[0] ?? '';
            $prefixB = $matchB[0] ?? '';

            // Cari posisi prefix di array $sortOrders
            $posA = array_search($prefixA, $sortOrders);
            $posB = array_search($prefixB, $sortOrders);

            // Jika tidak ditemukan, posisikan di akhir
            $posA = ($posA === false) ? PHP_INT_MAX : $posA;
            $posB = ($posB === false) ? PHP_INT_MAX : $posB;

            // Bandingkan berdasarkan posisi prefix
            if ($posA !== $posB) {
                return $posA <=> $posB;
            }

            // Jika prefix sama, bandingkan berdasarkan angka di kode kartu
            preg_match('/\d+/', $a['employee_code'], $numberA);
            preg_match('/\d+/', $b['employee_code'], $numberB);

            $numA = (int)($numberA[0] ?? PHP_INT_MAX); // Default jika tidak ada angka
            $numB = (int)($numberB[0] ?? PHP_INT_MAX);

            return $numA <=> $numB;
        });

        // Reorganize sorted data back by grade
        $dataByGrade = [];
        foreach ($sortedData as $sortedEmployee) {
            $dataByGrade[$sortedEmployee['employee_code']] = $sortedEmployee;
        }
        // dd($dataByGrade);
        // Tulis Data Karyawan Berdasarkan Shift
        $row = 7;

        foreach ($dataByGrade as $p) {
            $sheet->setCellValue('A' . $row, $row - 6);
            $sheet->setCellValue('B' . $row, $p['employee_code']);
            $sheet->setCellValue('C' . $row, $p['employee_name']);
            $sheet->setCellValue('D' . $row, $p['shift']);
            $sheet->setCellValue('E' . $row, $p['gender']);
            $sheet->setCellValue('F' . $row, $p['date_of_joining']);
            $sheet->setCellValue('G' . $row, $p['main_job_role_name']);
            if ($p['factory_name']) {
                $sheet->setCellValue('H' . $row, $p['factory_name']);
            } else {
                $sheet->setCellValue('H' . $row, '-');
            }

            // set tracking
            // $tracking = $p['previous_grade'] . $p['grade_akhir'];
            // $sheet->setCellValue('I' . $row, $tracking);

            //style from array
            $sheet->getStyle('A' . $row . ':I' . $row)->applyFromArray([
                'font' => ['name' => 'Times New Roman', 'size' => 10],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
                'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
            ]);

            $row++;
        }
        // total karyawan
        $sheet->mergeCells('A' . $row . ':G' . $row);
        $sheet->setCellValue('A' . $row, 'TOTAL KARYAWAN');
        $sheet->setCellValue('H' . $row, count($dataByGrade));
        $sheet->getStyle('A' . $row . ':I' . $row)
            ->getFont()
            ->setName('Times New Roman')
            ->setBold(true)
            ->setSize(10);
        $sheet->getStyle('A' . $row . ':I' . $row)
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        // center
        $sheet->getStyle('A' . $row . ':I' . $row)
            ->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
            ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        // Set wrap text
        $sheet->getStyle('A' . $row . ':I' . $row)->getAlignment()->setWrapText(true);

        // //Sheet Grade D
        // foreach ($gradeDPerBagian as $bagian => $dataKaryawan) {
        //     // Buat sheet baru
        //     $sheet = $spreadsheet->createSheet();
        //     $sheet->setTitle(substr($bagian, 0, 31) . ' Grade D'); // Maks 31 karakter

        //     // Header
        //     $headers = ['No', 'Kode Kartu', 'Nama Karyawan', 'Izin', 'Sakit', 'Mangkir', 'Akumulasi', 'Grade Akhir', 'Jobdesc', 'Nilai', 'Produksi', 'BS', 'Used Needle'];
        //     $col = 'A';
        //     foreach ($headers as $header) {
        //         $sheet->setCellValue($col . '1', $header);
        //         $col++;
        //     }
        //     // Style header 
        //     $sheet->getStyle('A1:M1')->applyFromArray([
        //         'font' => [
        //             'bold' => true,
        //             'name' => 'Times New Roman',
        //             'size' => 11, // Sedikit lebih besar untuk header
        //         ],
        //         'borders' => [
        //             'allBorders' => [
        //                 'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
        //             ],
        //         ],
        //         'alignment' => [
        //             'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, // Biar header tengah
        //             'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
        //         ],
        //     ]);

        //     $row = 2; // Mulai dari baris ke-2
        //     $no = 1;
        //     foreach ($dataKaryawan as $karyawan) {
        //         $failJobdescs = $karyawan['failJobdesc'];
        //         $failNilais = $karyawan['failNilai'];

        //         $first = true; // Penanda untuk baris pertama karyawan
        //         foreach ($failJobdescs as $i => $jobdesc) {
        //             if ($first) {
        //                 $sheet->setCellValue('A' . $row, $no++);
        //                 $sheet->setCellValue('B' . $row, $karyawan['employee_code']);
        //                 $sheet->setCellValue('C' . $row, $karyawan['employee_name']);
        //                 $sheet->setCellValue('D' . $row, $karyawan['izin']);
        //                 $sheet->setCellValue('E' . $row, $karyawan['sakit']);
        //                 $sheet->setCellValue('F' . $row, $karyawan['mangkir']);
        //                 $sheet->setCellValue('G' . $row, $karyawan['accumulasi']);
        //                 $sheet->setCellValue('H' . $row, $karyawan['grade_akhir']);
        //                 $sheet->setCellValue('K' . $row, $karyawan['prod_op'] ?? $karyawan['prod_rosso'] ?? '');
        //                 $sheet->setCellValue('L' . $row, $karyawan['bs_mc'] ?? $karyawan['perb_rosso'] ?? '');
        //                 $sheet->setCellValue('M' . $row, $karyawan['used_needle'] ?? '');

        //                 $first = false;
        //             }

        //             // Kolom Jobdesc dan Nilai
        //             $sheet->setCellValue('I' . $row, $jobdesc);
        //             $sheet->setCellValue('J' . $row, $failNilais[$i] ?? '-');

        //             // Apply style untuk setiap baris
        //             $sheet->getStyle('A' . $row . ':M' . $row)->applyFromArray([
        //                 'font' => ['name' => 'Times New Roman', 'size' => 10],
        //                 'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
        //             ]);

        //             $row++;
        //         }
        //     }

        //     // Setelah semua data karyawan dimasukkan, baru set auto-size untuk semua kolom
        //     foreach (range('A', 'M') as $columnID) {
        //         $sheet->getColumnDimension($columnID)->setAutoSize(true);
        //     }

        //     $lastRow = $row - 1;

        //     // Kolom-kolom yang ingin di-center
        //     $columnsToCenter = ['A', 'B', 'D', 'E', 'F', 'G', 'H', 'J', 'K', 'L', 'M'];

        //     foreach ($columnsToCenter as $colID) {
        //         $sheet->getStyle("{$colID}2:{$colID}{$lastRow}")
        //             ->getAlignment()
        //             ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
        //             ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        //     }
        // }

        // Simpan file Excel
        $filename = 'Report_Penilaian-' . $main_factory . '-' . date('m-d-Y') . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit();
    }

    private function groupByShift(array $penilaian): array
    {
        $penilaianByShift = [];
        // sort by shift
        foreach ($penilaian as $p) {
            $penilaianByShift[$p['shift']][] = $p;
        }
        // Sort groups by shift key in ascending order
        ksort($penilaianByShift);

        return $penilaianByShift;
    }

    public function reportAreaperBatch($main_factory)
    {
        if ($main_factory === 'all') {
            $batch = $this->paModel->getBatchName();
        } else {
            $batch = $this->paModel->getBatchNameByMainFactory($main_factory);
        }
        $data = [
            'role' => session()->get('role'),
            'title' => 'Report Batch',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'active7' => '',
            'active8' => '',
            'active9' => 'active',
            'reportbatch' => $batch,
            'main_factory' => $main_factory
        ];

        return view('penilaian/reportareaperbatch', $data);
    }


    public function exelReportBatch($id_batch,$main_factory)
    {
        $reportbatch = $this->paModel->getReportBatch($id_batch,$main_factory);
        // get batch name
        $batch = $this->batchModel->select('batch_name')
            ->where('id_batch', $id_batch)
            ->first();
        $batch_name = $batch['batch_name'] ?? '';
        if (empty($reportbatch)) {
            session()->setFlashdata('error', 'Tidak ada data penilaian untuk periode ini.');
            return redirect()->back();
        }
        
        // dd($reportbatch);
        // Buat Spreadsheet baru
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()
            ->setCreator('BPSYSTEM by RPLTEAM')
            ->setTitle('Report Penilaian');

        // Buat sheet pertama untuk report penilaian
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('REPORT PENILAIAN');

        // Header Utama
        $sheet->mergeCells('A1:H1')->setCellValue('A1', 'REPORT PENILAIAN ' . strtoupper($main_factory) . ' - ' . strtoupper($batch_name));

        // header data
        $sheet->mergeCells('A3:A4')->setCellValue('A3', 'NO');
        $sheet->mergeCells('B3:B4')->setCellValue('B3', 'KODE KARTU');
        $sheet->mergeCells('C3:C4')->setCellValue('C3', 'NAMA KARYAWAN');
        $sheet->mergeCells('D3:D4')->setCellValue('D3', 'SHIFT');
        $sheet->mergeCells('E3:E4')->setCellValue('E3', 'L/P');
        $sheet->mergeCells('F3:F4')->setCellValue('F3', 'TGL. MASUK KERJA');
        $sheet->mergeCells('G3:G4')->setCellValue('G3', 'BAGIAN');
        
        $sheet->mergeCells('H3:J3')->setCellValue('H3', 'Bulan');
        // header bulan per batch
        $bulan = $this->batchModel->getBulanPerBatch($id_batch);
        // buat header bulan
        $currentCol = 8; // Mulai dari kolom H
        foreach ($bulan as $b) {
            $b['bulan'] = date('F', mktime(0, 0, 0, $b['bulan'], 1)); // Mengubah angka bulan menjadi nama bulan
            $sheet->setCellValue(Coordinate::stringFromColumnIndex($currentCol) . '4', $b['bulan']);
            $currentCol++;
        }   

        $filename = 'Report_Batch_' . $main_factory . '_' . date('m-d-Y') . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit();
        
    }

    public function fetchDataFinalAssesment()
    {
        $id_batch       = $this->request->getPost('id_batch');
        // dd ($id_batch);
        $main_factory   = $this->request->getPost('main_factory');
        // $aspects        = $this->evaluationAspectModel->select('department')->groupBy('department')->findAll();
        // $aspects = array_column($aspects, 'department');
        $aspects = ['ROSSO', 'MONTIR', 'OPERATOR', 'SEWING'];

        $jobdesc       = $this->jobdesc($id_batch, $main_factory);              // sudah pakai aspek di dalamnya
        $absen         = $this->scorePresence($id_batch, $main_factory);
        $productivity  = $this->productivity($id_batch, $main_factory);
        $rossoScores   = $this->getProductivityRosso($id_batch, $main_factory);
        $needleBonuses = $this->usedNeedle($id_batch, $main_factory);
        // 4. Gabungkan data
        $final = [];

        foreach ($absen as $key => $p) {
            // pastikan kunci ada di semua set
            $j   = $jobdesc[$key]       ?? ['score_jobdesc' => 0, 'scoreJobdesc6s' => 0, 'id_main_job_role' => null, 'id_periode' => null, 'main_job_role_name' => null];
            $pr  = $productivity[$key]  ?? ['score_productivity' => 0];
            $rs  = $rossoScores[$key]   ?? ['score_rosso' => 0];
            $nb  = $needleBonuses[$key] ?? ['group' => 0];

            // hanya ambil yang punya id_main_job_role (valid)
            if (empty($j['id_main_job_role'])) {
                continue;
            }

            $final[$key] = [
                'id_employee'        => $p['id_employee'],
                'id_main_job_role'   => $j['id_main_job_role'],
                'id_periode'         => $j['id_periode'],
                // 'main_job_role_name' => $j['main_job_role_name'],
                'score_presence'      => $p['score_absensi']      ?? 0,
                'score_performance_job'      => $j['score_jobdesc'],
                'score_performance_6s'     => $j['scoreJobdesc6s'],
                'score_productivity'      => ($pr['score_productivity'] ?? 0) + ($rs['score_rosso'] ?? 0) + ($nb['group'] ?? 0),
            ];
        }
        // dd ($final);
        // Setelah foreach selesai
        if (!empty($final)) {
            $finalInsert = [];
            $duplicateData = []; // <- Tambahan untuk nyimpan yang duplikat
            $uniquePeriode = array_unique(array_column($final, 'id_periode'));

            $existingData = $this->finalAssssmentModel
                ->select(['id_employee', 'id_main_job_role', 'id_periode'])
                ->whereIn('id_periode', $uniquePeriode)
                ->whereIn('id_employee', array_column($final, 'id_employee'))
                ->findAll();

            $existingKeys = [];
            foreach ($existingData as $row) {
                $key = (string)$row['id_employee'] . '_' . (string)$row['id_main_job_role'] . '_' . (string)$row['id_periode'];
                $existingKeys[$key] = true;
            }

            foreach ($final as $row) {
                $key = (string)$row['id_employee'] . '_' . (string)$row['id_main_job_role'] . '_' . (string)$row['id_periode'];

                if (!isset($existingKeys[$key])) {
                    $finalInsert[] = $row;
                } else {
                    $duplicateData[] = $key; // <- Simpan duplikat
                }
            }

            if (!empty($finalInsert)) {
                $db = \Config\Database::connect();
                $db->transStart();
                $this->finalAssssmentModel->insertBatch($finalInsert);
                $db->transComplete();

                if ($db->transStatus() === false) {
                    session()->setFlashdata('error', 'Gagal menyimpan data final assessment.');
                } else {
                    $successMsg = 'Data final assessment berhasil disimpan. Jumlah: ' . count($finalInsert);
                    if (!empty($duplicateData)) {
                        $successMsg .= '. Duplikat yang dilewati: ' . count($duplicateData);
                        // Jika ingin detailnya:
                        $successMsg .= '. Duplikat: ' . implode(', ', $duplicateData);
                    }
                    session()->setFlashdata('success', $successMsg);
                }
            } else {
                session()->setFlashdata('error', 'Semua data sudah ada, tidak ada data baru yang disimpan. Total duplikat: ' . count($duplicateData));
            }

            return redirect()->to(base_url($this->role . '/reportBatch/' . $main_factory));
        }
        session()->setFlashdata('error', 'Tidak ada data final assessment yang bisa disimpan.');
        return redirect()->to(base_url($this->role . '/reportBatch/' . $main_factory)); 
    }

    private function scorePresence($id_batch, $main_factory)
    {
        // Ambil data absensi dan total hari kerja per periode
        $absen          = $this->presenceModel->getPresenceData($id_batch, $main_factory);
        // dd ($absen);
        $ttlHariKerja   = $this->presenceModel->getTotalHariKerjaPerPeriode($id_batch, $main_factory);

        // 1) Bangun map total hari kerja berdasarkan id_periode
        $mapHariKerja = [];
        foreach ($ttlHariKerja as $row) {
            // misal $row = ['id_periode' => 4, 'periode_name' => 'Tengah', 'total_hari_kerja' => 31]
            $mapHariKerja[$row['id_periode']] = $row['total_hari_kerja'];
        }

        // 2) Iterasi setiap karyawan untuk hitung poin & nilai absensi
        $presence = [];
        foreach ($absen as $a) {
            $key            = $a['employee_code']
                . '-' . $a['employee_name']
                . '-' . $a['id_periode'];

            // ambil jumlah insiden
            $sakit          = $a['sick']   ?? 0;
            $izin           = $a['permit'] ?? 0;
            $mangkir        = $a['absent'] ?? 0;
            $cuti           = $a['leave']  ?? 0;

            // 2.a) Total poin absensi
            $totalPoin      = ($sakit * 1)
                + ($izin  * 2)
                + ($mangkir * 3)
                + ($cuti    * 0);

            // 2.b) Total hari kerja dari map
            $totalHariKerja = $mapHariKerja[$a['id_periode']] ?? 0;

            // 2.c) Hari hadir efektif
            $hariHadir          = $totalHariKerja - $totalPoin;

            // 2.d) Persentase hadir (raw)
            //    pastikan totalHariKerja > 0 untuk menghindari division by zero
            if ($totalHariKerja > 0) {
                $pctHadir       = ($hariHadir / $totalHariKerja) * 100;
            } else {
                $pctHadir       = 0;
            }

            // 2.e) Terapkan bobot absensi (30%)
            $scoreAbsensiRaw    = $pctHadir;
            $scoreAbsensi       = $pctHadir * 0.30;

            // Simpan hasilnya
            $presence[$key] = [
                'id_employee'        => $a['id_employee'],
                'employee_code'      => $a['employee_code'],
                'employee_name'      => $a['employee_name'],
                'id_periode'         => $a['id_periode'],
                'score_absensi'        => round($scoreAbsensi, 2),
            ];
        }
        return $presence;
        // Contoh dump hasil
        // dd($presence);
    }

    // private function jobdesc($id_batch, $main_factory)
    // {
    //     // definisikan tiga aspek penilaian
    //     $aspects = ['ROSSO', 'MONTIR', 'OPERATOR', 'SEWING'];
    //     // $aspects = $this->evaluationAspectModel->select('department')->groupBy('department')->findAll();
    //     // $aspects = array_column($aspects, 'department');

    //     // dd ($aspects);
    //     // Ambil data jobdesc per periode
    //     $jobdescData = $this->newPAModel->getJobdescData($id_batch, $main_factory, $aspects);
    //     // dd ($jobdescData);
    //     // hitung nilai jobdesk akhir
    //     $jobdesc = [];
    //     foreach ($jobdescData as $jd) {
    //         $key = $jd['employee_code'] . '-' . $jd['employee_name'] . '-' . $jd['id_periode'];

    //         // Ambil nilai jobdesc
    //         $nilaiJobdesc = $jd['performance_score'] ?? 0;

    //         // jika $aspects['aspect'] = $jd['main_job_role_name'] terapkan bobot sesuai persentage
    //         // Misal jika $aspects['aspect'] = 'OPERATOR' dan $jd['main_job_role_name'] = 'OPERATOR', maka ambil persentase dari $aspects
    //         $scoreJobdesc = 0;
    //         $scoreJobdesc6s = 0;
    //         if (stripos($jd['main_job_role_name'], 'ROSSO') !== false) {
    //             // Ambil bobot aspek ini (0 jika gak ketemu)
    //             $scoreJobdesc6s = $nilaiJobdesc * 0.15;
    //             // Terapkan bobot jobdesc (15%)
    //             $scoreJobdesc = $nilaiJobdesc * 0.15;
    //         } elseif (stripos($jd['main_job_role_name'], 'MONTIR') !== false) {
    //             $scoreJobdesc6s = $nilaiJobdesc * 0.15;
    //             // Terapkan bobot jobdesc (50%)
    //             $scoreJobdesc = $nilaiJobdesc * 0.50;
    //         } elseif (stripos($jd['main_job_role_name'], 'OPERATOR') !== false) {
    //             // Ambil bobot aspek ini (0 jika gak ketemu)
    //             $scoreJobdesc6s = $nilaiJobdesc * 0.15;
    //             // Terapkan bobot jobdesc (15%)
    //             $scoreJobdesc = $nilaiJobdesc * 0.15;
    //         } elseif (stripos($jd['main_job_role_name'], 'SEWING') !== false) {
    //             // Ambil bobot aspek ini (0 jika gak ketemu)
    //             $scoreJobdesc6s = $nilaiJobdesc * 0.25;
    //             // Terapkan bobot jobdesc (50%)
    //             $scoreJobdesc = $nilaiJobdesc * 0.45;
    //         } else {
    //             // Jika tidak ada department yang sesuai, set score ke 0
    //             $scoreJobdesc = 0;
    //         }

    //         // Simpan hasilnya
    //         $jobdesc[$key] = [
    //             'employee_code' => $jd['employee_code'],
    //             'employee_name' => $jd['employee_name'],
    //             'id_periode'    => $jd['id_periode'],
    //             'scoreJobdesc6s'   => round($scoreJobdesc6s ?? 0, 2), // Tambahkan scoreJobdesc6s jika ada
    //             'score_jobdesc' => round($scoreJobdesc, 2),
    //             'main_job_role_name' => $jd['main_job_role_name'],
    //         ];
    //     }
    //     return $jobdesc;
    //     // Contoh dump hasil
    //     // dd($jobdesc);
    // }


    private function jobdesc($id_batch, $main_factory)
    {
        // 1. Mapping bobot per aspek (jobdesc & jobdesc6s)
        $aspectWeights = [
            'ROSSO'    => ['jobdesc' => 0.15, 'jobdesc6s' => 0.15],
            'MONTIR'   => ['jobdesc' => 0.50, 'jobdesc6s' => 0.15],
            'OPERATOR' => ['jobdesc' => 0.15, 'jobdesc6s' => 0.15],
            'SEWING'   => ['jobdesc' => 0.45, 'jobdesc6s' => 0.25],
        ];

        // 2. Ambil data jobdesc sesuai aspek-aspek di atas
        $jobdescData = $this->newPAModel->getJobdescData(
            $id_batch,
            $main_factory,
            array_keys($aspectWeights)  // ['ROSSO','MONTIR','OPERATOR','SEWING']
        );

        $jobdesc = [];

        foreach ($jobdescData as $jd) {
            $key = $jd['employee_code'] . '-' . $jd['employee_name'] . '-' . $jd['id_periode'];
            $nilai        = $jd['performance_score'] ?? 0;
            $roleRaw      = $jd['main_job_role_name'] ?? '';
            $roleUpper    = strtoupper($roleRaw);

            // default jika tidak ada yang match
            $scoreJobdesc   = 0;
            $scoreJobdesc6s = 0;

            // 3. Loop tiap aspek, cek LIKE '%ASPECT%'
            foreach ($aspectWeights as $aspect => $w) {
                if (strpos($roleUpper, $aspect) !== false) {
                    $scoreJobdesc   = $nilai * $w['jobdesc'];
                    $scoreJobdesc6s = $nilai * $w['jobdesc6s'];
                    break;
                }
            }

            // 4. Simpan hasil dengan pembulatan 2 desimal
            $jobdesc[$key] = [
                'employee_code'      => $jd['employee_code'],
                'employee_name'      => $jd['employee_name'],
                'id_periode'         => $jd['id_periode'],
                'id_main_job_role' => $jd['id_main_job_role'] ?? '', // Tambahkan id_main_job_role jika ada
                'main_job_role_name' => $roleRaw,
                'score_jobdesc'      => round($scoreJobdesc, 2),
                'scoreJobdesc6s'     => round($scoreJobdesc6s, 2),
            ];
        }

        // dd ($jobdesc);
        return $jobdesc;
    }

    private function productivity($id_batch, $main_factory)
    {
        // Ambil data produktivitas per periode
        $productivityData = $this->bsmcModel->getProductivityData($id_batch, $main_factory);
        // dd ($productivityData);
        // hitung nilai produktivitas akhir
        $productivity = [];
        foreach ($productivityData as $pd) {
            $key = $pd['employee_code'] . '-' . $pd['employee_name'] . '-' . $pd['id_periode'];

            $prodTotal = $pd['total_produksi'] ?? 0;
            $bsTotal = $pd['total_bs'] ?? 0;

            $productivityRaw = 0;
            if ($prodTotal > 0) {
                // Hitung produktivitas raw
                $productivityRaw = ($prodTotal - $bsTotal) / $prodTotal * 100;
            } else {
                // Jika total produksi 0, set produktivitas raw ke 0
                $productivityRaw = 0;
            }

            // Terapkan bobot produktivitas (40%)
            $scoreProductivity = $productivityRaw * 0.40;

            // Simpan hasilnya
            $productivity[$key] = [
                'employee_code' => $pd['employee_code'],
                'employee_name' => $pd['employee_name'],
                'id_periode'    => $pd['id_periode'],
                'score_productivity' => round($scoreProductivity, 2),
            ];
        }
        return $productivity;
        // Contoh dump hasil
        // dd($productivity);
    }

    private function getProductivityRosso($id_batch, $main_factory)
    {
        // Ambil data rosso per periode
        $rossoData = $this->rossoModel->getRossoDataForFinal($id_batch, $main_factory);
        // dd ($rossoData);
        // Hitung nilai rosso akhir
        $rosso = [];
        foreach ($rossoData as $r) {
            $key = $r['employee_code'] . '-' . $r['employee_name'] . '-' . $r['id_periode'];

            // Ambil nilai rosso
            $produksi = $r['total_produksi'] ?? 0;
            $perbaikan = $r['total_perbaikan'] ?? 0;
            $nilaiRosso = 0;
            if ($produksi > 0) {
                // Hitung nilai rosso raw
                $nilaiRosso = ($produksi - $perbaikan) / $produksi * 100;
            } else {
                // Jika total produksi 0, set nilai rosso raw ke 0
                $nilaiRosso = 0;
            }
            // Terapkan bobot rosso (40%)
            $scoreRosso = $nilaiRosso * 0.40;

            // Simpan hasilnya
            $rosso[$key] = [
                'employee_code' => $r['employee_code'],
                'employee_name' => $r['employee_name'],
                'id_periode'    => $r['id_periode'],
                'score_rosso'  => round($scoreRosso, 2),
            ];
        }
        // dd ($rosso);
        return $rosso;
    }

    private function usedNeedle($id_batch, $main_factory)
    {
        // 1. Ambil data used needle per periode
        $usedNeedleData = $this->jarumModel
            ->getUsedNeedleData($id_batch, $main_factory);

        // 2. Hitung jumlah karyawan
        $count = count($usedNeedleData);
        if ($count === 0) {
            return [];
        }

        // 3. Urutkan descending berdasarkan total_jarum
        usort($usedNeedleData, function ($a, $b) {
            return ($b['total_jarum'] ?? 0) <=> ($a['total_jarum'] ?? 0);
        });

        // 4. Tentukan ukuran grup (kita pakai ceil agar semua masuk grup)
        $groupSize = (int) ceil($count / 5);

        $usedNeedle = [];
        foreach ($usedNeedleData as $idx => $un) {
            $key = $un['employee_code'] . '-' . $un['employee_name'] . '-' . $un['id_periode'];

            // Nilai mentah
            $raw = $un['total_jarum'] ?? 0;

            // Tentukan grup (0..4) berdasarkan posisi urut
            $groupIndex = (int) floor($idx / $groupSize);
            if ($groupIndex > 4) {
                $groupIndex = 4;
            }

            // Bobot percent: 5% untuk grup 0, 4% grup1, ..., 1% grup4
            $percent = 5 - $groupIndex;

            // Hitung skor
            // $score = $raw * ($percent / 100);
            

            $usedNeedle[$key] = [
                'id_employee'        => $un['id_employee'],
                'id_periode'         => $un['id_periode'],
                'employee_code'      => $un['employee_code'],
                'employee_name'      => $un['employee_name'],
                'total_jarum'        => $raw,
                'rank'               => $idx + 1,
                'group'              => $groupIndex + 1,      // 1..5
                'jarumBonus'            => $percent
            ];
        }

        // Contoh dump hasil
        // dd($usedNeedle);
        return $usedNeedle;
    }
}
