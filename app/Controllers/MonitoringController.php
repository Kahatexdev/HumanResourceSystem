<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\UserModel;
use App\Models\JobSectionModel;
use App\Models\EmploymentStatusModel;
use App\Models\EmployeeModel;
use App\Models\DayModel;
use App\Models\BatchModel;
use App\Models\PeriodeModel;
use App\Models\PresenceModel;
use App\Models\MainJobRoleModel;
use App\Models\JobRoleModel;
use App\Models\JarumModel;
use App\Models\RossoModel;
use App\Models\BsmcModel;
use App\Models\FactoriesModel;
use App\Models\HistoryEmployeeModel;
use App\Models\EmployeeAssessmentModel;
use App\Models\PerformanceAssessmentModel;
use App\Models\NewPAModel;
use App\Models\FormerEmployeeModel;

class MonitoringController extends BaseController
{
    protected $role;
    protected $userModel;
    protected $jobSectionModel;
    protected $employmentStatusModel;
    protected $employeeModel;
    protected $dayModel;
    protected $factoryModel;
    protected $batchModel;
    protected $periodeModel;
    protected $presenceModel;
    protected $mainJobRoleModel;
    protected $jobRoleModel;
    protected $jarumModel;
    protected $rossoModel;
    protected $bsmcModel;
    protected $historyEmployeeModel;
    protected $eaModel;
    protected $paModel;
    protected $newPAModel;
    protected $formerEmployeeModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->jobSectionModel = new JobSectionModel();
        $this->employmentStatusModel = new EmploymentStatusModel();
        $this->employeeModel = new EmployeeModel();
        $this->dayModel = new DayModel();
        $this->factoryModel = new FactoriesModel();
        $this->batchModel = new BatchModel();
        $this->periodeModel = new PeriodeModel();
        $this->presenceModel = new PresenceModel();
        $this->mainJobRoleModel = new MainJobRoleModel();
        $this->jobRoleModel = new JobRoleModel();
        $this->jarumModel = new JarumModel();
        $this->rossoModel = new RossoModel();
        $this->bsmcModel = new BsmcModel();
        $this->historyEmployeeModel = new HistoryEmployeeModel();
        $this->eaModel = new EmployeeAssessmentModel();
        $this->paModel = new PerformanceAssessmentModel();
        $this->newPAModel = new NewPAModel();
        $this->formerEmployeeModel = new FormerEmployeeModel();
        $this->role = session()->get('role');
    }

    public function index()
    {
        $TtlKaryawan = $this->employeeModel->where('status', 'Aktif')->countAll();
        $PerpindahanBulanIni = $this->historyEmployeeModel->where('MONTH(date_of_change)', date('m'))->countAllResults();
        $dataKaryawan = $this->employeeModel->getActiveEmployeeByJobSection();
        // dd ($dataKaryawan);
        $periodeAktif = $this->periodeModel->getActivePeriode();

        // Default values jika tidak ada periode aktif
        $id_periode = null;
        $current_periode = 'Tidak Ada Periode Aktif';
        $start_date = '-';
        $end_date = '-';
        $cekPenilaian = null;

        if ($periodeAktif) {
            $id_periode = $periodeAktif['id_periode'];
            $current_periode = $periodeAktif['periode_name'];
            $start_date = $periodeAktif['start_date'];
            $end_date = $periodeAktif['end_date'];
            $cekPenilaian = $this->paModel->getMandorEvaluationStatus($id_periode);
        }
        $RatarataGrade = 0;
        $SkillGap = 0;
        // Hitung total karyawan (jika diperlukan)
        $totalKaryawan = 0;
        foreach ($dataKaryawan as $row) {
            $totalKaryawan += $row['jumlah_employees'];
        }
        // dd($cekPenilaian);

        $RatarataGrade = $this->newPAModel->getRataRataGrade(); // Lanjut nanti kalo grade akhir udah ada
        // dd ($RatarataGrade);
        // $RatarataGrade = 0;

        $dataPindah = $this->historyEmployeeModel->getPindahGroupedByDate();
        // Siapkan data untuk grafik line
        $labelsKar = [];
        $valuesKar = [];
        foreach ($dataPindah as $row) {
            $labelsKar[] = $row['tgl'];
            $valuesKar[] = (int)$row['jumlah'];
        }
        // // Siapkan array untuk FusionCharts
        // $chartData = [];
        // foreach ($dataKaryawan as $row) {
        //     $chartData[] = [
        //         'label' => $row['job_section_name'],
        //         'value' => $row['jumlah_employees'],
        //     ];
        // }
        // ekstrak dua array: labels & values
        $labels = array_column($dataKaryawan, 'job_section_name');
        $values = array_column($dataKaryawan, 'jumlah_employees');
        return view('Monitoring/index', [
            'role' => session()->get('role'),
            'title' => 'Dashboard',
            'active1' => 'active',
            'active2' => '',
            'active3' => '',
            'TtlKaryawan' => $TtlKaryawan,
            'PerpindahanBulanIni' => $PerpindahanBulanIni,
            'RatarataGrade' => $RatarataGrade['rata_rata'],
            // 'RataRataGrade' => $RatarataGrade['average_grade_letter'],
            'SkillGap' => $SkillGap,
            'karyawanByBagian' => $dataKaryawan,
            'labelsKar' => $labelsKar,
            'valuesKar' => $valuesKar,
            'cekPenilaian' => $cekPenilaian,
            'id_periode' => $id_periode,
            'current_periode' => $current_periode,
            'start_date' => $start_date,
            'end_date' => $end_date,
            // 'chartData' => json_encode($chartData)
            'labels' => $labels,
            'values' => $values
        ]);
    }

    public function bsmc()
    {
        $getBatch = $this->batchModel->findAll();
        $periode = $this->periodeModel->getPeriode();
        $getArea = $this->factoryModel->select('*')->groupBy('factory_name')->findAll();
        $getPeriode = $this->periodeModel->getPeriode();
        $getCurrentInput = $this->bsmcModel->getCurrentInput();

        // dd($getArea);
        $sort = [
            'KK1A',
            'KK1B',
            'KK2A',
            'KK2B',
            'KK2C',
            'KK5G',
            'KK7K',
            'KK7L',
            'KK8D',
            'KK8F',
            'KK8J',
            'KK9D',
            'KK10',
            'KK11M'
        ];

        $getArea = array_filter($getArea, function ($area) use ($sort) {
            return in_array($area['factory_name'], $sort);
        });

        // Urutkan data menggunakan usort
        usort($getArea, function ($a, $b) use ($sort) {
            $pos_a = array_search($a['factory_name'], $sort);
            $pos_b = array_search($b['factory_name'], $sort);

            // Jika tidak ditemukan, letakkan di akhir
            $pos_a = ($pos_a === false) ? PHP_INT_MAX : $pos_a;
            $pos_b = ($pos_b === false) ? PHP_INT_MAX : $pos_b;

            return $pos_a - $pos_b;
        });

        $data = [
            'role' => session()->get('role'),
            'title' => 'Bsmc',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'active7' => '',
            'active8' => '',
            'active9' => 'active',
            'getBatch' => $getBatch,
            'periode' => $periode,
            'getArea' => $getArea,
            'getPeriode' => $getPeriode,
            'getCurrentInput' => $getCurrentInput ?? []
        ];
        // dd ($getBatch);
        return view(session()->get('role') . '/bsmc', $data);
    }
    public function jarum()
    {
        $getBatch = $this->batchModel->findAll();
        $periode = $this->periodeModel->getPeriode();
        $getArea = $this->factoryModel->select('*')->groupBy('factory_name')->whereIn('factory_name', ['KK1A', 'KK1B', 'KK2A', 'KK2B', 'KK2C', 'KK5G', 'KK7K', 'KK7L', 'KK8D', 'KK8F', 'KK8J', 'KK9D', 'KK10', 'KK11M'])->findAll();
        $getPeriode = $this->periodeModel->getPeriode();
        $getCurrentInput = $this->jarumModel->getCurrentInput();

        // dd($getArea);
        $sort = [
            'KK1A',
            'KK1B',
            'KK2A',
            'KK2B',
            'KK2C',
            'KK5G',
            'KK7K',
            'KK7L',
            'KK8D',
            'KK8F',
            'KK8J',
            'KK9D',
            'KK10',
            'KK11M'
        ];

        // Urutkan data menggunakan usort
        usort($getArea, function ($a, $b) use ($sort) {
            $pos_a = array_search($a['factory_name'], $sort);
            $pos_b = array_search($b['factory_name'], $sort);

            // Jika tidak ditemukan, letakkan di akhir
            $pos_a = ($pos_a === false) ? PHP_INT_MAX : $pos_a;
            $pos_b = ($pos_b === false) ? PHP_INT_MAX : $pos_b;

            return $pos_a - $pos_b;
        });

        $data = [
            'role' => session()->get('role'),
            'title' => 'Jarum',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'active7' => '',
            'active8' => '',
            'active9' => 'active',
            'getBatch' => $getBatch,
            'periode' => $periode,
            'getArea' => $getArea,
            'getPeriode' => $getPeriode,
            'getCurrentInput' => $getCurrentInput ?? []
        ];
        // dd ($getBatch);
        return view(session()->get('role') . '/jarum', $data);
    }

    public function getMontirByArea()
    {
        $montir = [];

        if ($this->request->isAJAX()) {
            $area = $this->request->getPost('area');
            // $id_periode = $this->request->getPost('id_periode'); 
            // $id_periode = 1;

            // Ambil id_bagian berdasarkan area
            $montir = $this->employeeModel->getMontirByArea($area);
            // log_message('info', 'Bagian: ' . json_encode($bagian));
        }

        return $this->response->setJSON($montir);
    }

    public function rosso()
    {
        $tampilperarea = $this->factoryModel->select('*')->groupBy('main_factory')->findAll();
        $getBatch = $this->batchModel->findAll();
        $periode = $this->periodeModel->getPeriode();
        $getCurrentInput = $this->rossoModel->getCurrentInput();

        $sort = [
            'KK1',
            'KK2',
            'KK5',
            'KK7',
            'KK8',
            'KK9',
            'KK10',
            'KK11'
        ];
        // dd($tampilperarea);
        // Urutkan data menggunakan usort
        usort($tampilperarea, function ($a, $b) use ($sort) {
            $pos_a = array_search($a['main_factory'], $sort);
            $pos_b = array_search($b['main_factory'], $sort);

            // Jika tidak ditemukan, letakkan di akhir
            $pos_a = ($pos_a === false) ? PHP_INT_MAX : $pos_a;
            $pos_b = ($pos_b === false) ? PHP_INT_MAX : $pos_b;

            return $pos_a - $pos_b;
        });

        $data = [
            'role' => session()->get('role'),
            'title' => 'Rosso',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'active7' => '',
            'active8' => '',
            'active9' => 'active',
            // 'reportbatch' => $reportbatch,
            // 'getArea' => $getArea,
            'getBatch' => $getBatch,
            'tampilperarea' => $tampilperarea,
            'periode' => $periode,
            'getCurrentInput' => $getCurrentInput
        ];
        // dd ($getBatch);
        return view(session()->get('role') . '/rosso', $data);
    }

    public function reportpenilaian()
    {
        $tampilperarea = $this->factoryModel->getMainFactoryGroupByMainFactory();
        $sort = [
            'KK1',
            'KK2',
            'KK5',
            'KK7',
            'KK8',
            'KK9',
            'KK10',
            'KK11'
        ];
        // Urutkan data menggunakan usort
        usort($tampilperarea, function ($a, $b) use ($sort) {
            $pos_a = array_search($a['main_factory'], $sort);
            $pos_b = array_search($b['main_factory'], $sort);

            // Jika tidak ditemukan, letakkan di akhir
            $pos_a = ($pos_a === false) ? PHP_INT_MAX : $pos_a;
            $pos_b = ($pos_b === false) ? PHP_INT_MAX : $pos_b;

            return $pos_a - $pos_b;
        });
        // $getArea = $this->bagianmodel->getAreaGroupByAreaUtama();
        // dd($tampilperarea);
        // dd($reportbatch);
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
            'active8' => '',
            'active9' => 'active',
            // 'getArea' => $getArea,
            'tampilperarea' => $tampilperarea
        ];
        // dd ($getBatch);
        return view(session()->get('role') . '/reportpenilaian', $data);
    }

    public function reportbatch()
    {
        $tampilperarea = $this->factoryModel->getMainFactoryGroupByMainFactory();
        array_unshift($tampilperarea, ['main_factory' => 'all']); //Menambahkan data All Area
        $sort = [
            'KK1',
            'KK2',
            'KK5',
            'KK7',
            'KK8',
            'KK9',
            'KK10',
            'KK11',
            'all'
        ];

        // Urutkan data menggunakan usort
        usort($tampilperarea, function ($a, $b) use ($sort) {
            $pos_a = array_search($a['main_factory'], $sort);
            $pos_b = array_search($b['main_factory'], $sort);

            // Jika tidak ditemukan, letakkan di akhir
            $pos_a = ($pos_a === false) ? PHP_INT_MAX : $pos_a;
            $pos_b = ($pos_b === false) ? PHP_INT_MAX : $pos_b;

            return $pos_a - $pos_b;
        });

        // $getBulan = $this->penilaianmodel->getBatchGroupByBulanPenilaian();
        // $getBagian = $this->bagianmodel->getBagian();
        // $getBatch = $this->penilaianmodel->getPenilaianGroupByBatch();

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
            // 'getBulan' => $getBulan,
            // 'getBagian' => $getBagian,
            // 'getArea' => $tampilperarea,
            // 'getBatch' => $getBatch,
            'tampilperarea' => $tampilperarea,
        ];
        // dd ($getBatch);
        return view(session()->get('role') . '/reportbatch', $data);
    }

    public function formerKaryawan()
    {
        $karyawan = $this->formerEmployeeModel->getFormerKaryawan();

        $data = [
            'role' => session()->get('role'),
            'title' => 'Former Karyawan',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'karyawan' => $karyawan
        ];
        return view(session()->get('role') . '/formerKaryawan', $data);
    }

    public function exportFormerKaryawan()
    {
        $karyawan = $this->formerEmployeeModel->getFormerKaryawan();

        // Load library PhpSpreadsheet
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set judul sheet
        $sheet->setTitle('Former Karyawan');

        // Merge dan set judul utama
        $sheet->mergeCells('A1:H1');
        $sheet->setCellValue('A1', 'Data Resign Karyawan');
        $sheet->getStyle('A1')->getFont()->setSize(16)->setBold(true);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        // Border untuk judul utama
        $sheet->getStyle('A1:H1')->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        // 1. Set header labels
        $headers = [
            'A2' => 'Kode Kartu',
            'B2' => 'Nama Karyawan',
            'C2' => 'Shift',
            'D2' => 'Warna Baju',
            'E2' => 'Bagian',
            'F2' => 'Tgl Resign',
            'G2' => 'Alasan Resign',
            'H2' => 'Diupdate Oleh',
        ];
        foreach ($headers as $cell => $text) {
            $sheet->setCellValue($cell, $text);
        }

        // 2. Styling header: bold + background + center + border
        $headerRange = 'A2:H2';
        $sheet->getStyle($headerRange)
            ->getFont()
            ->setBold(true);
        $sheet->getStyle($headerRange)
            ->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()
            ->setARGB('FFEFEFEF');
        $sheet->getStyle($headerRange)
            ->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle($headerRange)
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        // Freeze header row
        $sheet->freezePane('A3');

        // 3. Isi data dan styling
        $row = 3;
        foreach ($karyawan as $data) {
            $sheet->setCellValue('A' . $row, $data['employee_code']);
            $sheet->setCellValue('B' . $row, $data['employee_name']);
            $sheet->setCellValue('C' . $row, $data['shift']);
            $sheet->setCellValue('D' . $row, $data['clothes_color']);
            $sheet->setCellValue('E' . $row, "{$data['job_section_name']} - {$data['main_factory']} - {$data['factory_name']}");
            $sheet->setCellValue('F' . $row, $data['date_of_leaving']);
            $sheet->setCellValue('G' . $row, $data['reason_for_leaving']);
            $sheet->setCellValue('H' . $row, $data['updated_by']);

            // Center alignment + border
            $sheet->getStyle("A{$row}:H{$row}")
                ->getAlignment()
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("A{$row}:H{$row}")
                ->getBorders()
                ->getAllBorders()
                ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

            $row++;
        }

        // 4. Auto-size all columns A–H
        foreach (range('A', 'H') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // 5. Siapkan header HTTP untuk download
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="former_karyawan.xlsx"');
        header('Cache-Control: max-age=0');

        // 6. Buat writer dan output
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}
