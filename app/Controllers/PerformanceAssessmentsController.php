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
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;


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
        if ($average >= 90) return 'A';
        if ($average >= 85) return 'B';
        if ($average >= 75) return 'C';
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


        // 1. Grup per kombinasi employee_code + id_periode
        $grouped = [];
        foreach ($reportbatch as $row) {
            // pakai key unik termasuk periode
            $key = $row['employee_code'] . '_' . $row['id_periode'] . '_' . $batch_name . '_' . $row['main_job_role_name'];
            // dd ($key);
            if (! isset($grouped[$key])) {
                // inisialisasi data employee + periode
                $grouped[$key] = [
                    'employee_code'      => $row['employee_code'],
                    'employee_name'      => $row['employee_name'],
                    'shift'              => $row['shift'],
                    'gender'             => $row['gender'],
                    'date_of_joining'    => $row['date_of_joining'],
                    'main_job_role_name' => $row['main_job_role_name'],
                    'previous_performance_score' => $row['previous_performance_score'] ?? null,
                    'nilai'              => $row['performance_score'],
                    'factory_name'       => $row['factory_name'],
                    'assessments'        => [],
                    'id_periode'         => $row['id_periode'],       // simpan periode
                    'periode_name'       => $row['periode_name'],     // simpan nama periode
                ];
            }
            // tambahkan assessment ke kombinasi karyawan+periode
            $grouped[$key]['assessments'][] = [
                'jobdescription' => $row['jobdescription'],
                'description'    => $row['description'],
                'score'          => $row['score'],
                'id_assessment'  => $row['id_assessment'],
            ];
        }

        // dd ($grouped);


        // 2. Ubah jadi indexed array, bukan associative by code
        $employees = array_values($grouped);
        // dd($employees);

        foreach ($uniqueSheets as $sheetName) {
            $sheet = $spreadsheet->createSheet();
            $sheet->setTitle(substr($sheetName, 0, 31)); // Nama sheet sesuai area dan bagian
            // Gunakan nama sheet (main_job_role_name) secara penuh untuk filter
            $currentBagian = $sheetName;

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
            // dd ($dataFiltered);
            foreach ($dataFiltered as $p) {
                foreach ($p['assessments'] as $assessment) {
                    $keterangan = [$assessment['description']];
                    $jobdesc = [$assessment['jobdescription']];

                    // Gabungkan berdasarkan indeks
                    foreach ($keterangan as $index => $ket) {
                        $job = $jobdesc[$index] ?? '';
                        $jobdescGrouped[$ket][] = $job;
                    }
                }
            }
            // dd ($dataFiltered[18], $jobdescGrouped);
            // dd ($jobdescGrouped);
            // Hilangkan duplikasi dalam setiap keterangan
            foreach ($jobdescGrouped as $keterangan => &$jobs) {
                $jobs = array_unique($jobs);
            }
            unset($jobs); // Lepaskan referensi
            // dd ($jobdescGrouped);
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
                    $colLetter = Coordinate::stringFromColumnIndex($currentCol);
                    $sheet->setCellValue($colLetter . '6', $job);
                    $sheet->getStyle($colLetter . '6')->applyFromArray([
                        'font' => ['bold' => true],
                        'alignment' => [
                            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                            'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                            'textRotation' => 90, // Rotate text up
                            'wrapText' => true,    // Enable wrap text
                        ],
                        'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
                    ]);
                    // Set row height for header row 6 (taller for rotated text)
                    $sheet->getRowDimension(6)->setRowHeight(100);
                    $currentCol++;
                }
            }

            // Tambahkan Header untuk grade, skor, dan tracking
            $additionalHeaders = ['SKOR', 'GRADE', 'TRACKING'];
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
                    $sheet->setCellValue('H' . $row, $this->calculateGradeBatch($p['previous_performance_score'] ?? '-'));
                    // Decode nilai
                    $nilai = $assessment['score'];
                    // dd($nilai);
                    $colIndex = 9; // Dimulai dari kolom I


                    // Set nilai
                    foreach ($p['assessments'] as $assessment) {
                        $score = $assessment['score'] ?? '-';
                        $sheet->setCellValue(Coordinate::stringFromColumnIndex($colIndex) . $row, $score);
                        $colIndex++;

                        $skor = $assessment['nilai'] ?? 0;
                    }
                    $grade = $this->calculateGradeBatch($p['nilai'] ?? 0);

                    $sheet->setCellValue(Coordinate::stringFromColumnIndex($colIndex) . $row, $p['nilai'] ?? '-');
                    $colIndex++;

                    $sheet->setCellValue(Coordinate::stringFromColumnIndex($colIndex) . $row, $grade);
                    $colIndex++;


                    //tracking
                    $sheet->setCellValue(Coordinate::stringFromColumnIndex($colIndex) . $row, $this->calculateGradeBatch($p['previous_performance_score'] ?? '-') . $grade);


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
        // dd ($uniqueSheets, $reportbatch, $dataByShift, $dataFiltered, $grouped);

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
                'previous_performance_score' => $p['previous_performance_score'] ?? null,
                'nilai' => $p['performance_score'],
                // 'grade_akhir' => $p['grade_akhir'],
                // 'previous_grade' => $p['previous_grade'],
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
        $gradeDPerBagian = []; // key = main_job_role_name
        $dataByGrade = [];
        foreach ($grouped as $key => $p) {
            $dataByGrade[] = [
                'employee_code'    => $p['employee_code'],
                'employee_name'    => $p['employee_name'],
                'shift'            => $p['shift'],
                'main_job_role_name' => $p['main_job_role_name'],
                'score'            => $p['nilai'],
                'id_assessment'    => $p['id_periode'], // simpan id_periode
                'gender'           => $p['gender'] ?? '',
                'date_of_joining'  => $p['date_of_joining'] ?? '',
                'factory_name'     => $p['factory_name'] ?? '',
                'previous_performance_score' => $p['previous_performance_score'] ?? null,
                'nilai'            => $p['nilai'],
                'assessments'      => $p['assessments'],
                'periode_name'     => $p['periode_name'] ?? '',
            ];
        }
        // dd($dataByGrade[200]['assessments']);
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
            $tracking = $this->calculateGradeBatch($p['previous_performance_score'] ?? '-') . $this->calculateGradeBatch($p['nilai'] ?? 0);
            $sheet->setCellValue('I' . $row, $tracking);

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
        // Set auto-size untuk semua kolom
        foreach ($sheet->getColumnIterator() as $column) {
            $sheet->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
        }

        //Sheet Grade D
        // dd ($dataByGrade);
        $gradeD = [];
        foreach ($dataByGrade as $item => $p) {
            if ($p['nilai'] < 75) { // Grade D
                $gradeD[] = [
                    'kode_kartu' => $p['employee_code'],
                    'nama_karyawan' => $p['employee_name'],
                    'shift' => $p['shift'],
                    'main_job_role_name' => $p['main_job_role_name'],
                    'grade_akhir' => $this->calculateGradeBatch($p['nilai']),
                    'failJobdesc' => [],
                    'failDesc' => [],
                    'failNilai' => [],
                ];
                // Tambahkan jobdesc dan nilai yang gagal
                foreach ($p['assessments'] as $assessment) {
                    if ($assessment['score'] < 75) { // Nilai gagal
                        // jika nilai < 4 maka tambahkan ke failJobdesc dan failNilai
                        $gradeD[count($gradeD) - 1]['failJobdesc'][] = $assessment['jobdescription'] ?? '(tidak ada jobdesc)';
                        $gradeD[count($gradeD) - 1]['failDesc'][] = $assessment['description'] ?? '(tidak ada deskripsi)';
                        $gradeD[count($gradeD) - 1]['failNilai'][] = $assessment['score'] ?? '-';
                        // dd($gradeD[count($gradeD) - 1]['failJobdesc'], $gradeD[count($gradeD) - 1]['failNilai']);
                        
                    }
                }
                // Tambahkan ke gradeDPerBagian
                $bagian = $p['main_job_role_name'];
                if (!isset($gradeDPerBagian[$bagian])) {
                    $gradeDPerBagian[$bagian] = [];
                }
                $gradeDPerBagian[$bagian][] = $gradeD[count($gradeD) - 1];
            }
        }

        // dd ($gradeD, $gradeDPerBagian);
        foreach ($gradeDPerBagian as $bagian => $dataKaryawan) {
            // Buat sheet baru
            $sheet = $spreadsheet->createSheet();
            $sheet->setTitle(substr($bagian, 0, 31) . ' Grade D'); // Maks 31 karakter

            // Header
            $headers = ['No', 'Kode Kartu', 'Nama Karyawan', 'Grade Akhir', 'Deskripsi', 'Jobdesc', 'Nilai'];
            $col = 'A';
            foreach ($headers as $header) {
                $sheet->setCellValue($col . '1', $header);
                $col++;
            }
            // Style header 
            $sheet->getStyle('A1:G1')->applyFromArray([
                'font' => [
                    'bold' => true,
                    'name' => 'Times New Roman',
                    'size' => 11, // Sedikit lebih besar untuk header
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    ],
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, // Biar header tengah
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
            ]);

            $row = 2; // Mulai dari baris ke-2
            $no = 1;
            foreach ($dataKaryawan as $karyawan) {
                // Filter failJobdesc dan failNilai hanya jika failNilai < 4
                $failJobdescs = [];
                $failNilais = [];
                $failDesc = [];
                foreach ($karyawan['failNilai'] as $i => $nilai) {   
                    if ($nilai < 4) {
                        $failJobdescs[] = $karyawan['failJobdesc'][$i] ?? '';
                        $failNilais[] = $nilai;
                        $failDesc[] = $karyawan['failDesc'][$i] ?? '';
                    }
                }

                $first = true; // Penanda untuk baris pertama karyawan
                foreach ($failJobdescs as $i => $jobdesc) {
                    if ($first) {
                        // Merge cell A, B, C jika ada lebih dari satu failJobdesc
                        if (count($failJobdescs) > 1) {
                            $sheet->mergeCells('A' . $row . ':A' . ($row + count($failJobdescs) - 1));
                            $sheet->mergeCells('B' . $row . ':B' . ($row + count($failJobdescs) - 1));
                            $sheet->mergeCells('C' . $row . ':C' . ($row + count($failJobdescs) - 1));
                            $sheet->mergeCells('D' . $row . ':D' . ($row + count($failJobdescs) - 1));
                        }
                        $sheet->setCellValue('A' . $row, $no++);
                        $sheet->setCellValue('B' . $row, $karyawan['kode_kartu']);
                        $sheet->setCellValue('C' . $row, $karyawan['nama_karyawan']);
                        $sheet->setCellValue('D' . $row, $karyawan['grade_akhir']);

                        $first = false;
                    }

                    // Jika deskripsi sama dengan baris sebelumnya, merge cell F
                    if ($i > 0 && $failDesc[$i] === $failDesc[$i - 1]) {
                        // Merge cell F dari baris sebelumnya ke baris sekarang
                        $sheet->mergeCells('E' . ($row - 1) . ':E' . $row);
                        // Kosongkan cell F pada baris ini agar tidak double value
                        $sheet->setCellValue('E' . $row, '');
                    } else {
                        $sheet->setCellValue('E' . $row, $failDesc[$i] ?? '-');
                    }
                    // Kolom Jobdesc dan Nilai
                    $sheet->setCellValue('F' . $row, $jobdesc);
                    $sheet->setCellValue('G' . $row, $failNilais[$i] ?? '-');

                    // Apply style untuk setiap baris
                    $sheet->getStyle('A' . $row . ':G' . $row)->applyFromArray([
                        'font' => ['name' => 'Times New Roman', 'size' => 10],
                        'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
                    ]);

                    $row++;
                }
            }

            // Setelah semua data karyawan dimasukkan, baru set auto-size untuk semua kolom
            foreach (range('A', 'G') as $columnID) {
                $sheet->getColumnDimension($columnID)->setAutoSize(true);
            }

            $lastRow = $row - 1;

            // Kolom-kolom yang ingin di-center
            $columnsToCenter = ['A', 'B','C', 'D', 'E', 'F', 'G'];

            foreach ($columnsToCenter as $colID) {
                $sheet->getStyle("{$colID}2:{$colID}{$lastRow}")
                    ->getAlignment()
                    ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
                    ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
            }
        }


        // Simpan file Excel
        $filename = 'Report_Penilaian-' . $main_factory . '-' . $batch_name . '-' . $periode_name . '-' . date('Y-m-d') . '.xlsx';
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
        // dd ($main_factory);
        if ($main_factory === 'all') {
            $batch = $this->paModel->getBatchName();
            foreach ($batch as $key => $b) {
                $batch[$key]['main_factory'] = $b['main_factory'] ?? 'all';
            }
        } else {
            $batch = $this->paModel->getBatchNameByMainFactory($main_factory);
        }
        // dd ($batch);
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


    public function exelReportBatch($id_batch, $main_factory)
    {
        $reportbatch = $this->paModel->getReportBatch($id_batch, $main_factory);
        // dd ($reportbatch);
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
            $scoreAbsensi       = $pctHadir * 0.35;

            // Simpan hasilnya
            $presence[$key] = [
                'id_employee'        => $a['id_employee'],
                'employee_code'      => $a['employee_code'],
                'employee_name'      => $a['employee_name'],
                'id_periode'         => $a['id_periode'],
                'id_factory'         => $a['id_factory_old'],
                'id_job_section'     => $a['id_job_section_old'],
                'job_section_name'   => $a['job_section_name'],
                'score_absensi'        => round($scoreAbsensi, 2),
            ];
        }
        return $presence;
        // Contoh dump hasil
        // dd($presence);
    }


    private function jobdesc($id_batch, $main_factory)
    {
        // 1. Mapping bobot per aspek (jobdesc & jobdesc6s)
        $aspectWeights = [
            'ROSSO'    => ['jobdesc' => 0.35, 'jobdesc6s' => 0.15],
            'MONTIR'   => ['jobdesc' => 0.45, 'jobdesc6s' => 0.15],
            'OPERATOR' => ['jobdesc' => 0.35, 'jobdesc6s' => 0.15],
            'SEWING'   => ['jobdesc' => 0.45, 'jobdesc6s' => 0.20],
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
                'id_employee'        => $jd['id_employee'],
                'employee_code'      => $jd['employee_code'],
                'employee_name'      => $jd['employee_name'],
                'id_periode'         => $jd['id_periode'],
                'id_factory'       => $jd['id_factory'], // Tambahkan id_factory jika ada
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
            $scoreProductivity = $productivityRaw * 0.15;

            // Simpan hasilnya
            $productivity[$key] = [
                'employee_code' => $pd['employee_code'],
                'employee_name' => $pd['employee_name'],
                'id_periode'    => $pd['id_periode'],
                'score_productivity' => round($scoreProductivity, 2),
            ];
        }
        // return $productivity;
        // Contoh dump hasil
        dd($productivity, $prodTotal , $bsTotal , $prodTotal, $productivityData);

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
            $scoreRosso = $nilaiRosso * 0.15;

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

    public function fetchDataFinalAssesment()
    {
        $id_batch     = $this->request->getPost('id_batch');
        $main_factory = $this->request->getPost('main_factory');
        $aspects      = ['ROSSO', 'MONTIR', 'OPERATOR', 'SEWING'];

        // Ambil data mentah dari model/API
        $jobdescRaw       = $this->jobdesc($id_batch, $main_factory);
        $absenRaw         = $this->scorePresence($id_batch, $main_factory);
        $productivityRaw  = $this->productivity($id_batch, $main_factory);
        $rossoScoresRaw   = $this->getProductivityRosso($id_batch, $main_factory);
        $needleBonusesRaw = $this->usedNeedle($id_batch, $main_factory);
        // dd ($jobdescRaw, $absenRaw, $productivityRaw, $rossoScoresRaw, $needleBonusesRaw);
        $final = [];
        $incompleteData = [];

        foreach ($absenRaw as $key => $p) {
            // Jika tidak ada jobrole, skip
            if (! array_key_exists($key, $jobdescRaw) || empty($jobdescRaw[$key]['id_main_job_role'])) {
                continue;
            }
            // Ambil data mentah untuk pengecekan missing
            $j_raw  = $jobdescRaw[$key];
            $pr_raw = $productivityRaw[$key]  ?? [];
            $rs_raw = $rossoScoresRaw[$key]   ?? [];
            $nb_raw = $needleBonusesRaw[$key] ?? [];
            $section = strtoupper($j_raw['main_job_role_name']); // misal ada kolom ini


            // Cek missing sebelum defaulting:
            $missingFields = [];

            if (! array_key_exists('score_absensi', $p)) {
                $missingFields[] = 'score_absensi';
            }
            if (! array_key_exists('score_jobdesc', $j_raw)) {
                $missingFields[] = 'score_performance_job';
            }
            if (! array_key_exists('scoreJobdesc6s', $j_raw)) {
                $missingFields[] = 'score_performance_6s';
            }
            // **Hanya cek productivity kalau bukan SEWING**
            if ($section !== 'SEWING') {
                if (
                    ! array_key_exists('score_productivity', $pr_raw)
                    && ! array_key_exists('score_rosso', $rs_raw)
                    && ! array_key_exists('group', $nb_raw)
                ) {
                    $missingFields[] = 'score_productivity';
                }
            }

            // Simpan detail missing (jika ada)
            if (! empty($missingFields)) {
                $incompleteData[] = [
                    'id_employee'      => $p['id_employee'],
                    'employee_code'    => $p['employee_code'],
                    'job_section_name' => $p['job_section_name'],
                    'id_main_job_role' => $j_raw['id_main_job_role'],
                    'id_periode'       => $j_raw['id_periode'],
                    'id_factory'      => $j_raw['id_factory'] ?? $p['id_factory'], // gunakan id_factory dari jobdesc jika ada
                    'missing'          => $missingFields,
                ];
            }

            // Setelah pengecekan, kita berikan default jika memang key tidak ada
            $j = [
                'id_employee'        => $p['id_employee'],
                'score_jobdesc'      => $j_raw['score_jobdesc']      ?? 0,
                'scoreJobdesc6s'     => $j_raw['scoreJobdesc6s']     ?? 0,
                'id_main_job_role'   => $j_raw['id_main_job_role'], // sudah pasti ada karena kita cek di atas
                'id_periode'         => $j_raw['id_periode'],
                'id_factory'        => $j_raw['id_factory'] ?? $p['id_factory'], // gunakan id_factory dari jobdesc jika ada
            ];
            $pr = ['score_productivity' => $pr_raw['score_productivity'] ?? 0];
            $rs = ['score_rosso'       => $rs_raw['score_rosso']       ?? 0];
            $nb = ['group'             => $nb_raw['group']             ?? 0];

            // Gabungkan ke final
            $final[$key] = [
                'id_employee'           => $p['id_employee'],
                'id_main_job_role'      => $j['id_main_job_role'],
                'id_periode'            => $j['id_periode'],
                'id_factory'           => $j['id_factory'], // gunakan id_factory dari jobdesc jika ada
                'score_presence'        => $p['score_absensi'],
                'score_performance_job' => $j['score_jobdesc'],
                'score_performance_6s'  => $j['scoreJobdesc6s'],
                'score_productivity'    => $pr['score_productivity'] + $rs['score_rosso'] + $nb['group'],
            ];
        }
        log_message('debug', 'Final assessment data: ' . print_r($final, true));
        // Jika final kosong, langsung redirect dengan error
        if (empty($final)) {
            session()->setFlashdata('error', 'Tidak ada data final assessment yang bisa disimpan.');
            return redirect()->to(base_url($this->role . '/reportBatch/' . $main_factory));
        }

        // Ambil data existing lengkap (termasuk nilai-nilai) untuk periode dan karyawan yang relevan
        $uniquePeriode = array_unique(array_column($final, 'id_periode'));
        $employeeIds   = array_column($final, 'id_employee');
        $existingData  = $this->finalAssssmentModel
            ->whereIn('id_periode', $uniquePeriode)
            ->whereIn('id_employee', $employeeIds)
            ->findAll();

        $finalInsert = [];
        $finalUpdate = [];
        $duplicateData = [];

        foreach ($final as $row) {
            $key = "{$row['id_employee']}_{$row['id_main_job_role']}_{$row['id_periode']}";

            $matched = array_filter($existingData, function ($e) use ($row) {
                return $e['id_employee']       == $row['id_employee']
                    && $e['id_main_job_role']  == $row['id_main_job_role']
                    && $e['id_periode']        == $row['id_periode'];
            });

            if (empty($matched)) {
                $finalInsert[] = $row;
            } else {
                $existingRow = reset($matched);
                $hasChanged =
                    (float)$existingRow['score_presence']        !== (float)$row['score_presence'] ||
                    (float)$existingRow['score_performance_job'] !== (float)$row['score_performance_job'] ||
                    (float)$existingRow['score_performance_6s']  !== (float)$row['score_performance_6s'] ||
                    (float)$existingRow['score_productivity']    !== (float)$row['score_productivity'];

                if ($hasChanged) {
                    $row['id'] = $existingRow['id'];
                    $finalUpdate[] = $row;
                } else {
                    $duplicateData[] = $key;
                }
            }
        }

        // Mulai transaksi DB
        $db = \Config\Database::connect();
        $db->transStart();
        // log_message('debug', 'Final assessment insert: ' . print_r($finalInsert, true));
        if (! empty($finalInsert)) {
            $this->finalAssssmentModel->insertBatch($finalInsert);
        }
        if (! empty($finalUpdate)) {
            foreach ($finalUpdate as $row) {
                $id = $row['id'];
                unset($row['id']);
                $this->finalAssssmentModel->update($id, $row);
            }
        }

        $db->transComplete();

        $totalInsert     = count($finalInsert);
        $totalUpdate     = count($finalUpdate);
        $totalDupes      = count($duplicateData);
        $totalIncomplete = count($incompleteData);

        if ($db->transStatus() === false) {
            session()->setFlashdata('error', 'Gagal menyimpan data final assessment.');
        } else {
            $successMsg = "Insert: $totalInsert. Update: $totalUpdate. Duplikat: $totalDupes.";
            if ($totalIncomplete > 0) {
                $successMsg .= " Terdapat $totalIncomplete data tidak lengkap.";
            }
            session()->setFlashdata('success', $successMsg);
        }

        // Jika ada record yang missing fields, set flashdata warning dengan detailnya
        if (! empty($incompleteData)) {
            $msg = "Data tidak lengkap:\n";
            foreach ($incompleteData as $incomplete) {
                $msg .= "- ID Employee: {$incomplete['id_employee']}, employee_code: {$incomplete['employee_code']}, " . "Job Section: {$incomplete['job_section_name']}, " . " Periode: {$incomplete['id_periode']} => Missing: "
                    . implode(', ', $incomplete['missing']) . "\n";
            }
            // log_message('debug', 'Missing fields: ' . $msg);
            session()->setFlashdata('warning', nl2br(esc($msg)));
        }

        return redirect()->to(base_url($this->role . '/reportBatch/' . $main_factory));
    }


    public function finalAssesment($id_batch, $main_factory)
    {
        $reportbatch = $this->finalAssssmentModel->getFinalAssessmentByBatch($id_batch, $main_factory);
        if (empty($reportbatch)) {
            session()->setFlashdata('error', 'Tidak ada data final assessment untuk periode ini.');
            return redirect()->back();
        }

        $batch_name = $this->batchModel->select('batch_name')
            ->where('id_batch', $id_batch)
            ->first()['batch_name'] ?? '';

        $nilaiPerKaryawan = [];

        // 1. Loop pertama: bangun array per karyawan dengan key 'detail'
        foreach ($reportbatch as $rb) {
            $empKey = $rb['employee_code'] . '-' . $rb['employee_name'];
            $nilaiAkhir = round(
                $rb['score_presence'] +
                    $rb['score_performance_job'] +
                    $rb['score_performance_6s'] +
                    $rb['score_productivity'],
                2
            );

            // Simpan setiap periode ke dalam key 'detail'
            $nilaiPerKaryawan[$empKey]['detail'][] = [
                'id_periode'            => $rb['id_periode'],
                'id_factory'           => $rb['id_factory'],
                'periode_name'          => $rb['periode_name'],
                'score_presence'        => round($rb['score_presence'], 2),
                'score_performance_job' => round($rb['score_performance_job'], 2),
                'score_performance_6s'  => round($rb['score_performance_6s'], 2),
                'score_productivity'    => round($rb['score_productivity'], 2),
                'nilai_akhir'           => $nilaiAkhir,
            ];

            // Simpan info umum karyawan (hanya sekali per key empKey)
            $nilaiPerKaryawan[$empKey]['employee_code']      = $rb['employee_code'];
            $nilaiPerKaryawan[$empKey]['employee_name']      = $rb['employee_name'];
            $nilaiPerKaryawan[$empKey]['id_employee']        = $rb['id_employee'];
            $nilaiPerKaryawan[$empKey]['id_main_job_role']   = $rb['id_main_job_role'];
        }

        // 2. Loop kedua: hitung rata-rata dan bangun array JSON detail per id_employee
        $jsonDetail = [];
        foreach ($nilaiPerKaryawan as $key => &$karyawan) {
            $totalNilai    = 0;
            // $jumlahPeriode = count($karyawan['detail']);
            $jumlahPeriode = 3;

            foreach ($karyawan['detail'] as $det) {
                $totalNilai += $det['nilai_akhir'];
            }

            // Simpan rata-rata
            $karyawan['rata_rata'] = $jumlahPeriode > 0
                ? round($totalNilai / $jumlahPeriode, 2)
                : 0;

            // Bangun JSON detail berdasarkan id_employee
            $idEmp = $karyawan['id_employee'];
            $jsonDetail[$idEmp] = [];
            foreach ($karyawan['detail'] as $det) {
                $jsonDetail[$idEmp][] = [
                    'periode'    => $det['periode_name'],
                    'nilaiAkhir' => $det['nilai_akhir'],
                ];
            }
        }
        unset($karyawan); // lepaskan reference
        // dd ($nilaiPerKaryawan);
        // Kirim data ke view, termasuk jsonDetail
        $data = [
            'role'         => session()->get('role'),
            'title'        => 'Final Assessment',
            'active1'      => '',
            'active2'      => '',
            'active3'      => '',
            'active4'      => '',
            'active5'      => '',
            'active6'      => '',
            'active7'      => '',
            'active8'      => '',
            'active9'      => 'active',
            'main_factory' => $main_factory,
            'id_batch'     => $id_batch,
            'reportbatch'  => $nilaiPerKaryawan,
            'batch_name'   => $batch_name,
            'jsonDetail'   => json_encode($jsonDetail),
        ];

        return view('penilaian/finalAssesment', $data);
    }


    public function exportFinalAssessment()
    {
        $id_batch     = $this->request->getPost('id_batch');
        $main_factory = $this->request->getPost('main_factory');
        $nameBatch    = $this->batchModel
            ->select('batch_name')
            ->where('id_batch', $id_batch)
            ->first()['batch_name'] ?? '';

        // Ambil data final assessment (query harus sudah include 'end_date', 'shift', 'gender', 'main_job_role_name', dll.)
        $reportbatch = $this->finalAssssmentModel
            ->getFinalAssessmentByBatch($id_batch, $main_factory);

        if (empty($reportbatch)) {
            session()->setFlashdata('error', 'Tidak ada data final assessment untuk periode ini.');
            return redirect()->back();
        }

        /**
         * 1. Kumpulkan bulan unik (berdasarkan end_date)
         */
        $bulanUnik = [];
        foreach ($reportbatch as $rb) {
            if (empty($rb['end_date'])) continue;
            $monthNum = intval(date('m', strtotime($rb['end_date'])));
            $bulanUnik[$monthNum] = true;
        }
        $bulanUnik = array_keys($bulanUnik);
        sort($bulanUnik, SORT_NUMERIC);

        /**
         * 2. Mapping bulan → kolom Excel (H=8, I=9, dst.)
         */
        $mapBulan2Col  = [];
        $currentColIdx = 8; // kolom ke-8 = “H”
        foreach ($bulanUnik as $bln) {
            $mapBulan2Col[$bln] = $currentColIdx;
            $currentColIdx++;
        }
        // Kolom terakhir (setelah semua bulan) untuk "NILAI AKHIR"
        $lastColLetter = Coordinate::stringFromColumnIndex($currentColIdx);

        /**
         * 3. Build array grouping per karyawan
         */
        $dataForExport = [];
        foreach ($reportbatch as $rb) {
            $key = $rb['employee_code'] . '-' . $rb['employee_name'];

            $nilaiAkhir = round(
                (float) ($rb['score_presence']        ?? 0)
                    + (float) ($rb['score_performance_job'] ?? 0)
                    + (float) ($rb['score_performance_6s']  ?? 0)
                    + (float) ($rb['score_productivity']    ?? 0),
                2
            );

            if (!isset($dataForExport[$key])) {
                $dataForExport[$key] = [
                    'employee_code'       => $rb['employee_code'],
                    'employee_name'       => $rb['employee_name'],
                    'shift'               => $rb['shift']                ?? '',
                    'gender'              => $rb['gender']               ?? '',
                    'date_of_joining'     => $rb['date_of_joining']      ?? '',
                    'main_job_role_name'  => $rb['main_job_role_name']   ?? '',
                    'detail'              => [],
                ];
            }

            $blnInt = intval(date('m', strtotime($rb['end_date'])));
            $dataForExport[$key]['detail'][$blnInt] = [
                'nilai_akhir' => $nilaiAkhir,
            ];
        }

        /**
         * 3.5. SORT DATA FOR EXPORT BERDASARKAN KODE_KARTU
         */
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
        $indexMap = array_flip($sortOrders);

        $orderedKeys = array_keys($dataForExport);
        usort($orderedKeys, function ($aKey, $bKey) use ($dataForExport, $indexMap) {
            $codeA = $dataForExport[$aKey]['employee_code'];
            $codeB = $dataForExport[$bKey]['employee_code'];

            preg_match('/^[A-Z]+/', $codeA, $matchA);
            preg_match('/^[A-Z]+/', $codeB, $matchB);
            $prefixA = $matchA[0] ?? '';
            $prefixB = $matchB[0] ?? '';

            $idxA = $indexMap[$prefixA] ?? PHP_INT_MAX;
            $idxB = $indexMap[$prefixB] ?? PHP_INT_MAX;

            if ($idxA === $idxB) {
                return strcmp($codeA, $codeB);
            }
            return $idxA <=> $idxB;
        });

        /**
         * 4. Group orderedKeys berdasarkan main_job_role_name
         */
        $groupedByRole = [];
        foreach ($orderedKeys as $compositeKey) {
            $role = $dataForExport[$compositeKey]['main_job_role_name'] ?: 'UNKNOWN_ROLE';
            if (!isset($groupedByRole[$role])) {
                $groupedByRole[$role] = [];
            }
            $groupedByRole[$role][] = $compositeKey;
        }

        /**
         * 5. Grupkan juga berdasarkan grade (A, B, C, D)
         *    A: >=90, B: >=80, C: >=70, D: <70
         */
        $gradeGroups = [
            'Grade A' => [],
            'Grade B' => [],
            'Grade C' => [],
            'Grade D' => [],
        ];
        foreach ($orderedKeys as $compositeKey) {
            // Hitung rata-rata nilai akhir
            $totalNilai = array_sum(array_column($dataForExport[$compositeKey]['detail'], 'nilai_akhir'));
            // $countBulan  = count($dataForExport[$compositeKey]['detail']);
            $countBulan = count($mapBulan2Col); // Jumlah bulan yang diharapkan
            $avgNilai    = $countBulan > 0 ? ($totalNilai / $countBulan) : 0;

            if ($avgNilai >= 90) {
                $gradeGroups['Grade A'][] = $compositeKey;
            } elseif ($avgNilai >= 85) {
                $gradeGroups['Grade B'][] = $compositeKey;
            } elseif ($avgNilai >= 75) {
                $gradeGroups['Grade C'][] = $compositeKey;
            } else {
                $gradeGroups['Grade D'][] = $compositeKey;
            }
        }

        /**
         * 6. Buat spreadsheet & sheet per main_job_role_name, lalu sheet per Grade
         */
        $spreadsheet = new Spreadsheet();
        // Hapus sheet default (sheet kosong) jika ada
        if ($spreadsheet->getSheetCount() > 0) {
            $spreadsheet->removeSheetByIndex(0);
        }

        $sheetIndex = 0;

        // 6a. Sheet untuk setiap main_job_role_name
        foreach ($groupedByRole as $roleName => $keysInRole) {
            $sheet = $spreadsheet->createSheet($sheetIndex);
            $roleTitle = substr(str_replace(['/', '\\', '?', '*', '[', ']'], '_', $roleName), 0, 31);
            $sheet->setTitle($roleTitle);

            // === HEADER UTAMA (Baris 1) ===
            $sheet->mergeCells("A1:{$lastColLetter}1");
            $sheet->setCellValue(
                'A1',
                'REPORT PENILAIAN ' . strtoupper($main_factory)
                    . ' - ' . strtoupper($nameBatch)
                    . ' (' . strtoupper($roleName) . ')'
            );
            $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
            $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('A1')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

            // === HEADER KOLOM (Baris 3–4) ===
            $sheet->mergeCells('A3:A4');
            $sheet->setCellValue('A3', 'NO');
            $sheet->mergeCells('B3:B4');
            $sheet->setCellValue('B3', 'KODE KARTU');
            $sheet->mergeCells('C3:C4');
            $sheet->setCellValue('C3', 'NAMA KARYAWAN');
            $sheet->mergeCells('D3:D4');
            $sheet->setCellValue('D3', 'SHIFT');
            $sheet->mergeCells('E3:E4');
            $sheet->setCellValue('E3', 'L/P');
            $sheet->mergeCells('F3:F4');
            $sheet->setCellValue('F3', 'TGL. MASUK KERJA');
            $sheet->mergeCells('G3:G4');
            $sheet->setCellValue('G3', 'BAGIAN');

            $startBulanCol = Coordinate::stringFromColumnIndex(8);
            $endBulanCol   = Coordinate::stringFromColumnIndex($currentColIdx - 1);
            $sheet->mergeCells("{$startBulanCol}3:{$endBulanCol}3");
            $sheet->setCellValue('H3', 'BULAN');

            foreach ($mapBulan2Col as $blnInt => $colIdx) {
                $colLetter = Coordinate::stringFromColumnIndex($colIdx);
                $sheet->setCellValue("{$colLetter}4", strtoupper(date('M Y', mktime(0, 0, 0, $blnInt, 1))));
            }

            $sheet->mergeCells("{$lastColLetter}3:{$lastColLetter}4");
            $sheet->setCellValue("{$lastColLetter}3", 'NILAI AKHIR');

            // Style header 3–4
            $sheet->getStyle("A3:{$lastColLetter}4")->getFont()->setBold(true);
            $sheet->getStyle("A3:{$lastColLetter}4")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("A3:{$lastColLetter}4")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
            $sheet->getStyle("A3:{$lastColLetter}4")->getAlignment()->setWrapText(true);
            $sheet->getStyle("A3:{$lastColLetter}4")->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color'       => ['argb' => 'FF000000'],
                    ],
                ],
            ]);

            // Lebar kolom
            $sheet->getColumnDimension('A')->setWidth(5);
            $sheet->getColumnDimension('B')->setWidth(15);
            $sheet->getColumnDimension('C')->setWidth(25);
            $sheet->getColumnDimension('D')->setWidth(10);
            $sheet->getColumnDimension('E')->setWidth(5);
            $sheet->getColumnDimension('F')->setWidth(15);
            $sheet->getColumnDimension('G')->setWidth(15);
            foreach ($mapBulan2Col as $colIdx) {
                $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($colIdx))->setWidth(15);
            }

            // Format tanggal dan nilai akhir
            $lastRowRole = 5 + count($keysInRole) - 1;
            $sheet->getStyle("F5:F{$lastRowRole}")
                ->getNumberFormat()
                ->setFormatCode(NumberFormat::FORMAT_DATE_DDMMYYYY);
            $sheet->getStyle("{$lastColLetter}5:{$lastColLetter}{$lastRowRole}")
                ->getNumberFormat()
                ->setFormatCode(NumberFormat::FORMAT_NUMBER_00);

            // === ISI DATA (Baris 5 dst.) ===
            $rowIndex = 5;
            foreach ($keysInRole as $compositeKey) {
                $empData = $dataForExport[$compositeKey];

                // NO
                $sheet->setCellValue("A{$rowIndex}", $rowIndex - 4);
                // KODE KARTU
                $sheet->setCellValue("B{$rowIndex}", $empData['employee_code']);
                // NAMA KARYAWAN
                $sheet->setCellValue("C{$rowIndex}", $empData['employee_name']);
                // SHIFT
                $sheet->setCellValue("D{$rowIndex}", $empData['shift']);
                // L/P
                $sheet->setCellValue("E{$rowIndex}", $empData['gender']);
                // TGL. MASUK KERJA
                if (!empty($empData['date_of_joining'])) {
                    $excelDate = \PhpOffice\PhpSpreadsheet\Shared\Date::stringToExcel($empData['date_of_joining']);
                    $sheet->setCellValue("F{$rowIndex}", $excelDate);
                }
                // BAGIAN
                $sheet->setCellValue("G{$rowIndex}", $empData['main_job_role_name']);

                // Bulan nilai akhir
                foreach ($mapBulan2Col as $blnInt => $colIdx) {
                    $colLetter = Coordinate::stringFromColumnIndex($colIdx);
                    if (isset($empData['detail'][$blnInt])) {
                        $sheet->setCellValue("{$colLetter}{$rowIndex}", $empData['detail'][$blnInt]['nilai_akhir']);
                        $sheet->getStyle("{$colLetter}{$rowIndex}")
                            ->getNumberFormat()
                            ->setFormatCode(NumberFormat::FORMAT_NUMBER_00);
                    } else {
                        $sheet->setCellValue("{$colLetter}{$rowIndex}", 0);
                    }
                }

                // NILAI AKHIR (rata-rata)
                $totalNilai = array_sum(array_column($empData['detail'], 'nilai_akhir'));
                // $countBulan  = count($empData['detail']);
                $countBulan = count($mapBulan2Col); // Jumlah bulan yang diharapkan
                $avgNilai    = $countBulan > 0 ? round($totalNilai / $countBulan, 2) : 0;
                $sheet->setCellValue("{$lastColLetter}{$rowIndex}", $avgNilai);
                $sheet->getStyle("{$lastColLetter}{$rowIndex}")
                    ->getNumberFormat()
                    ->setFormatCode(NumberFormat::FORMAT_NUMBER_00);

                // Border & alignment
                $sheet->getStyle("A{$rowIndex}:{$lastColLetter}{$rowIndex}")->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color'       => ['argb' => 'FF000000'],
                        ],
                    ],
                ]);
                $sheet->getStyle("A{$rowIndex}:{$lastColLetter}{$rowIndex}")
                    ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("A{$rowIndex}:{$lastColLetter}{$rowIndex}")
                    ->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

                $rowIndex++;
            }

            $sheetIndex++;
        }

        // 6b. Sheet untuk setiap Grade (A, B, C, D)
        foreach ($gradeGroups as $gradeName => $keysInGrade) {
            $sheet = $spreadsheet->createSheet($sheetIndex);
            $safeTitle = substr(str_replace(['/', '\\', '?', '*', '[', ']'], '_', $gradeName), 0, 31);
            $sheet->setTitle($safeTitle);

            // === HEADER UTAMA (Baris 1) ===
            $sheet->mergeCells("A1:{$lastColLetter}1");
            $sheet->setCellValue(
                'A1',
                'REPORT PENILAIAN ' . strtoupper($main_factory)
                    . ' - ' . strtoupper($nameBatch)
                    . ' (' . $gradeName . ')'
            );
            $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
            $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('A1')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

            // === HEADER KOLOM (Baris 3–4) ===
            $sheet->mergeCells('A3:A4');
            $sheet->setCellValue('A3', 'NO');
            $sheet->mergeCells('B3:B4');
            $sheet->setCellValue('B3', 'KODE KARTU');
            $sheet->mergeCells('C3:C4');
            $sheet->setCellValue('C3', 'NAMA KARYAWAN');
            $sheet->mergeCells('D3:D4');
            $sheet->setCellValue('D3', 'SHIFT');
            $sheet->mergeCells('E3:E4');
            $sheet->setCellValue('E3', 'L/P');
            $sheet->mergeCells('F3:F4');
            $sheet->setCellValue('F3', 'TGL. MASUK KERJA');
            $sheet->mergeCells('G3:G4');
            $sheet->setCellValue('G3', 'BAGIAN');

            $sheet->mergeCells("{$startBulanCol}3:{$endBulanCol}3");
            $sheet->setCellValue('H3', 'BULAN');
            foreach ($mapBulan2Col as $blnInt => $colIdx) {
                $colLetter = Coordinate::stringFromColumnIndex($colIdx);
                $sheet->setCellValue("{$colLetter}4", strtoupper(date('M Y', mktime(0, 0, 0, $blnInt, 1))));
            }

            $sheet->mergeCells("{$lastColLetter}3:{$lastColLetter}4");
            $sheet->setCellValue("{$lastColLetter}3", 'NILAI AKHIR');

            // Style header 3–4
            $sheet->getStyle("A3:{$lastColLetter}4")->getFont()->setBold(true);
            $sheet->getStyle("A3:{$lastColLetter}4")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("A3:{$lastColLetter}4")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
            $sheet->getStyle("A3:{$lastColLetter}4")->getAlignment()->setWrapText(true);
            $sheet->getStyle("A3:{$lastColLetter}4")->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color'       => ['argb' => 'FF000000'],
                    ],
                ],
            ]);

            // Lebar kolom
            $sheet->getColumnDimension('A')->setWidth(5);
            $sheet->getColumnDimension('B')->setWidth(15);
            $sheet->getColumnDimension('C')->setWidth(25);
            $sheet->getColumnDimension('D')->setWidth(10);
            $sheet->getColumnDimension('E')->setWidth(5);
            $sheet->getColumnDimension('F')->setWidth(15);
            $sheet->getColumnDimension('G')->setWidth(15);
            foreach ($mapBulan2Col as $colIdx) {
                $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($colIdx))->setWidth(15);
            }

            // Format tanggal dan nilai akhir
            $lastRowGrade = 5 + count($keysInGrade) - 1;
            $sheet->getStyle("F5:F{$lastRowGrade}")
                ->getNumberFormat()
                ->setFormatCode(NumberFormat::FORMAT_DATE_DDMMYYYY);
            $sheet->getStyle("{$lastColLetter}5:{$lastColLetter}{$lastRowGrade}")
                ->getNumberFormat()
                ->setFormatCode(NumberFormat::FORMAT_NUMBER_00);

            // === ISI DATA (Baris 5 dst.) ===
            $rowIndex = 5;
            foreach ($keysInGrade as $compositeKey) {
                $empData = $dataForExport[$compositeKey];

                // NO
                $sheet->setCellValue("A{$rowIndex}", $rowIndex - 4);
                // KODE KARTU
                $sheet->setCellValue("B{$rowIndex}", $empData['employee_code']);
                // NAMA KARYAWAN
                $sheet->setCellValue("C{$rowIndex}", $empData['employee_name']);
                // SHIFT
                $sheet->setCellValue("D{$rowIndex}", $empData['shift']);
                // L/P
                $sheet->setCellValue("E{$rowIndex}", $empData['gender']);
                // TGL. MASUK KERJA
                if (!empty($empData['date_of_joining'])) {
                    $excelDate = \PhpOffice\PhpSpreadsheet\Shared\Date::stringToExcel($empData['date_of_joining']);
                    $sheet->setCellValue("F{$rowIndex}", $excelDate);
                }
                // BAGIAN
                $sheet->setCellValue("G{$rowIndex}", $empData['main_job_role_name']);

                // Bulan nilai akhir
                foreach ($mapBulan2Col as $blnInt => $colIdx) {
                    $colLetter = Coordinate::stringFromColumnIndex($colIdx);
                    if (isset($empData['detail'][$blnInt])) {
                        $sheet->setCellValue("{$colLetter}{$rowIndex}", $empData['detail'][$blnInt]['nilai_akhir']);
                        $sheet->getStyle("{$colLetter}{$rowIndex}")
                            ->getNumberFormat()
                            ->setFormatCode(NumberFormat::FORMAT_NUMBER_00);
                    } else {
                        $sheet->setCellValue("{$colLetter}{$rowIndex}", 0);
                    }
                }

                // NILAI AKHIR (rata-rata)
                $totalNilai = array_sum(array_column($empData['detail'], 'nilai_akhir'));
                // $countBulan  = count($empData['detail']);
                $countBulan = count($mapBulan2Col); // Jumlah bulan yang diharapkan
                $avgNilai    = $countBulan > 0 ? round($totalNilai / $countBulan, 2) : 0;
                $sheet->setCellValue("{$lastColLetter}{$rowIndex}", $avgNilai);
                $sheet->getStyle("{$lastColLetter}{$rowIndex}")
                    ->getNumberFormat()
                    ->setFormatCode(NumberFormat::FORMAT_NUMBER_00);

                // Border & alignment
                $sheet->getStyle("A{$rowIndex}:{$lastColLetter}{$rowIndex}")->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color'       => ['argb' => 'FF000000'],
                        ],
                    ],
                ]);
                $sheet->getStyle("A{$rowIndex}:{$lastColLetter}{$rowIndex}")
                    ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("A{$rowIndex}:{$lastColLetter}{$rowIndex}")
                    ->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

                $rowIndex++;
            }

            $sheetIndex++;
        }

        /**
         * 7. Tambahkan sheet “Report Grade”
         */
        $sheetReport = $spreadsheet->createSheet($sheetIndex);
        $sheetIndex++;

        // Set nama sheet dan judul
        $sheetReport->setTitle('Report Grade');

        // Judul Utama (A1 sampai D1)
        $sheetReport->mergeCells('A1:D1');
        $sheetReport->setCellValue('A1', 'LAPORAN PREMI SKILL MATRIX');
        $sheetReport->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheetReport->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Subtitle (batch + tahun) di A2:D2
        $sheetReport->mergeCells('A2:D2');
        $sheetReport->setCellValue('A2', 'AREA ' . $main_factory . ' - '  . strtoupper($nameBatch));
        $sheetReport->getStyle('A2')->getFont()->setBold(true);
        $sheetReport->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Header Tabel di Baris 4
        $sheetReport->setCellValue('A4', 'Grade');
        $sheetReport->setCellValue('B4', 'Premi');
        $sheetReport->setCellValue('C4', 'Jumlah Orang');
        $sheetReport->setCellValue('D4', 'Total');
        $sheetReport->getStyle('A4:D4')->getFont()->setBold(true);
        $sheetReport->getStyle('A4:D4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheetReport->getStyle('A4:D4')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

        // Isi masing‐masing grade (A, B, C, D) di Baris 5–8
        //  – Kolom A: "A", "B", "C", "D"
        //  – Kolom B: Premi per grade (harus disesuaikan)
        //  – Kolom C: Rumus COUNTA untuk menghitung jumlah orang di sheet “Grade X”
        //  – Kolom D: Rumus = (nilai di B) × (nilai di C)

        // Baris 5 = Grade A
        $sheetReport->setCellValue('A5', 'A');
        // Premi A = 25.000  (format teks “Rp25.000”)
        $sheetReport->setCellValue('B5', 'Rp25000');
        // Jumlah Orang: hitung dari sheet “Grade A” kolom B (employee_code), baris 5 dst.
        $sheetReport->setCellValue('C5', '=COUNTA(\'Grade A\'!B5:B1000)');
        // Total: =B5 * C5  (tapi B5 masih teks “Rp25.000”, jadi perlu CONVERT ke angka 25000)
        $sheetReport->setCellValue('D5', '=SUBSTITUTE(B5,"Rp","")*C5');

        // Baris 6 = Grade B
        $sheetReport->setCellValue('A6', 'B');
        // Premi B = 20.000
        $sheetReport->setCellValue('B6', 'Rp20000');
        $sheetReport->setCellValue('C6', '=COUNTA(\'Grade B\'!B5:B1000)');
        $sheetReport->setCellValue('D6', '=SUBSTITUTE(B6,"Rp","")*C6');

        // Baris 7 = Grade C
        $sheetReport->setCellValue('A7', 'C');
        // Premi C = 15.000
        $sheetReport->setCellValue('B7', 'Rp15000');
        $sheetReport->setCellValue('C7', '=COUNTA(\'Grade C\'!B5:B1000)');
        $sheetReport->setCellValue('D7', '=SUBSTITUTE(B7,"Rp","")*C7');

        // Baris 8 = Grade D
        $sheetReport->setCellValue('A8', 'D');
        // Premi D = 0
        $sheetReport->setCellValue('B8', 'Rp0');
        $sheetReport->setCellValue('C8', '=COUNTA(\'Grade D\'!B5:B1000)');
        $sheetReport->setCellValue('D8', '=SUBSTITUTE(B8,"Rp","")*C8');

        // Format kolom B (“Premi”) sebagai teks, rata kanan
        $sheetReport->getStyle('B5:B8')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        // Format kolom C (“Jumlah Orang”) sebagai integer, rata tengah
        $sheetReport->getStyle('C5:C8')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        // Format kolom D (“Total”) sebagai number dengan ribuan, 0 desimal
        $sheetReport->getStyle('D5:D8')
            ->getNumberFormat()
            ->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
        $sheetReport->getStyle('D5:D8')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        // Baris 9 = “TOTAL”
        $sheetReport->setCellValue('A9', 'TOTAL');
        // Jumlah Total Orang = SUM(C5:C8)
        $sheetReport->setCellValue('C9', '=SUM(C5:C8)');
        // Total Premi Keseluruhan = SUM(D5:D8)
        $sheetReport->setCellValue('D9', '=SUM(D5:D8)');
        // Format baris TOTAL
        $sheetReport->getStyle('A9:D9')->getFont()->setBold(true);
        $sheetReport->getStyle('A9:D9')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheetReport->getStyle('A9:D9')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheetReport->getStyle('D9')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

        // Style untuk baris TOTAL (bold)
        $sheetReport->getStyle('A9:D9')->getFont()->setBold(true);
        $sheetReport->getStyle('C9:D9')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Set border untuk seluruh tabel (A4:D9)
        $sheetReport->getStyle('A4:D9')->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color'       => ['argb' => 'FF000000'],
                ],
            ],
        ]);

        // Lebar kolom agar pas
        $sheetReport->getColumnDimension('A')->setWidth(10);
        $sheetReport->getColumnDimension('B')->setWidth(15);
        $sheetReport->getColumnDimension('C')->setWidth(15);
        $sheetReport->getColumnDimension('D')->setWidth(18);
        // Simpan & output Excel
        $writer = new Xlsx($spreadsheet);
        $fileName = 'Report_' . $nameBatch . '_' . $main_factory . '_' . date('Y_m_d') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit;
    }

    public function printFinalAssessment()
    {
        $id_batch     = $this->request->getPost('id_batch');
        $main_factory = $this->request->getPost('main_factory');

        // Ambil nama batch
        $batch       = $this->batchModel
            ->select('batch_name')
            ->where('id_batch', $id_batch)
            ->first();
        $batch_name  = $batch['batch_name'] ?? '';

        // Ambil data final assessment
        $reportbatch = $this->finalAssssmentModel
            ->getFinalAssessmentByBatch($id_batch, $main_factory);
        // dd ($reportbatch);
        if (empty($reportbatch)) {
            session()->setFlashdata('error', 'Tidak ada data final assessment untuk periode ini.');
            return redirect()->back();
        }

        // 1. Kumpulkan data per karyawan
        $nilaiPerKaryawan = [];
        foreach ($reportbatch as $rb) {
            $key = $rb['employee_code'] . '|' . $rb['employee_name'] . '|' . $rb['id_periode'];

            if (!isset($nilaiPerKaryawan[$key])) {
                $nilaiPerKaryawan[$key] = [
                    'employee_code'             => $rb['employee_code'],
                    'employee_name'             => $rb['employee_name'],
                    'shift'                     => $rb['shift'] ?? '',
                    'main_job_role_name'        => $rb['main_job_role_name'] ?? '',
                    'factory_name'              => $rb['factory_name'] ?? '',
                    'score_presence'            => $rb['score_presence'] ?? 0,
                    'score_performance_job'     => $rb['score_performance_job'] ?? 0,
                    'score_performance_6s'      => $rb['score_performance_6s'] ?? 0,
                    'score_productivity'        => $rb['score_productivity'] ?? 0,
                    'periode_name'              => $rb['periode_name'] ?? '',
                    'batch_name'                => $rb['batch_name'] ?? $batch_name,
                ];
            }
        }

        // 2. Buat Spreadsheet dan header kolom
        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Final Assessment');

        $headers = [
            'A1' => 'Employee Code',
            'B1' => 'Employee Name',
            'C1' => 'Shift',
            'D1' => 'Main Job Role',
            'E1' => 'Factory',
            'F1' => 'Score Presence',
            'G1' => 'Score Performance Job',
            'H1' => 'Score Performance 6S',
            'I1' => 'Score Productivity',
            'J1' => 'Periode Name',
            'K1' => 'Batch Name',
        ];
        foreach ($headers as $cell => $text) {
            $sheet->setCellValue($cell, $text);
        }

        // 3. Isi data mulai row 2
        $rowNum = 2;
        foreach ($nilaiPerKaryawan as $data) {
            $sheet->setCellValue('A' . $rowNum, $data['employee_code']);
            $sheet->setCellValue('B' . $rowNum, $data['employee_name']);
            $sheet->setCellValue('C' . $rowNum, $data['shift']);
            $sheet->setCellValue('D' . $rowNum, $data['main_job_role_name']);
            $sheet->setCellValue('E' . $rowNum, $data['factory_name']);
            $sheet->setCellValue('F' . $rowNum, $data['score_presence']);
            $sheet->setCellValue('G' . $rowNum, $data['score_performance_job']);
            $sheet->setCellValue('H' . $rowNum, $data['score_performance_6s']);
            $sheet->setCellValue('I' . $rowNum, $data['score_productivity']);
            $sheet->setCellValue('J' . $rowNum, $data['periode_name']);
            $sheet->setCellValue('K' . $rowNum, $data['batch_name']);
            $rowNum++;
        }

        // 4. Kirim header download dan keluarkan file
        $filename = 'Final_Assessment_' . date('Ymd_His') . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment;filename=\"$filename\"");
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}
