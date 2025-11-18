<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\UserModel;
use App\Models\EmployeeModel;
use App\Models\JobSectionModel;
use App\Models\EmploymentStatusModel;
use App\Models\FactoriesModel;
use App\Models\HistoryEmployeeModel;
use App\Models\DayModel;
use App\Models\FormerEmployeeModel;

class TrainingSchoolController extends BaseController
{
    protected $role;
    protected $userModel;
    protected $jobSectionModel;
    protected $employmentStatusModel;
    protected $employeeModel;
    protected $historyPindahKaryawanModel;
    protected $factoriesModel;
    protected $days;
    protected $formerEmployeeModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->jobSectionModel = new JobSectionModel();
        $this->employmentStatusModel = new EmploymentStatusModel();
        $this->employeeModel = new EmployeeModel();
        $this->historyPindahKaryawanModel = new HistoryEmployeeModel();
        $this->factoriesModel = new FactoriesModel();
        $this->days = new DayModel();

        $this->formerEmployeeModel = new FormerEmployeeModel();
        $this->role = session()->get('role');
    }
    public function index()
    {
        $TtlKaryawan = $this->employeeModel->where('status', 'active')->countAll();
        $PerpindahanBulanIni = $this->historyPindahKaryawanModel->where('MONTH(date_of_change)', date('m'))->countAllResults();
        $dataKaryawan = $this->employeeModel->getActiveKaryawanByBagiaAndArea();
        // dd($dataKaryawan);
        // Group data berdasarkan area_utama
        $groupedData = [];
        foreach ($dataKaryawan as $row) {
            $groupedData[$row['main_factory']][] = $row;
        }

        // Sort berdasarkan angka setelah 'KK'
        uksort($groupedData, function ($a, $b) {
            return (int) filter_var($a, FILTER_SANITIZE_NUMBER_INT) <=> (int) filter_var($b, FILTER_SANITIZE_NUMBER_INT);
        });

        $totalKaryawan = 0;
        foreach ($dataKaryawan as $row) {
            $totalKaryawan += $row['jumlah_karyawan'];
        }

        $dataPindah = $this->historyPindahKaryawanModel->getPindahGroupedByDate();
        // dd($dataPindah);
        $labelsKar = [];
        $valuesKar = [];
        foreach ($dataPindah as $row) {
            $labelsKar[] = $row['tgl'];
            $valuesKar[] = (int)$row['jumlah'];
        }

        return view($this->role . '/index', [
            'role' => $this->role,
            'title' => 'Dashboard',
            'active1' => 'active',
            'active2' => '',
            'active3' => '',
            'TtlKaryawan' => $TtlKaryawan,
            'PerpindahanBulanIni' => $PerpindahanBulanIni,
            'groupedData' => $groupedData,
            'labelsKar' => $labelsKar,
            'valuesKar' => $valuesKar
        ]);
    }

    public function listArea()
    {
        $apiUrl = 'http://172.23.44.14/CapacityApps/public/api/getPlanMesin';
        $response = file_get_contents($apiUrl);
        $plan = json_decode($response, true);  // Decode JSON response dari API
        $tampilperarea = $this->factoriesModel
            ->where('factory_name !=', '')
            ->where('factory_name !=', '-')
            ->groupBy('main_factory')
            ->findAll();

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

        // Fungsi untuk mengurutkan berdasarkan array urutan yang ditentukan
        usort($tampilperarea, function ($a, $b) use ($sort) {
            $pos_a = array_search($a['main_factory'], $sort);
            $pos_b = array_search($b['main_factory'], $sort);

            // Jika tidak ditemukan, letakkan di akhir
            $pos_a = ($pos_a === false) ? PHP_INT_MAX : $pos_a;
            $pos_b = ($pos_b === false) ? PHP_INT_MAX : $pos_b;

            return $pos_a - $pos_b;
        });

        $all = 'ALL';
        $data = [
            'role' => session()->get('role'),
            'title' => 'Karyawan',
            'active1' => '',
            'active2' => 'active',
            'active3' => '',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'active7' => '',
            'tampildata' => $tampilperarea,
            'listplan' => $plan,
            'all' => $all
        ];
        // dd($data);
        return view(session()->get('role') . '/karyawan', $data);
    }
    public function detailKaryawanPerArea($area)
    {
        if ($area === 'ALL') {
            $karyawan = $this->employeeModel->getKaryawanTanpaArea();
        } else {
            $karyawan = $this->employeeModel->getKaryawanByArea($area);
            // dd ($karyawan);
        }
        // dd ($area);
        // dd($karyawan);
        $bagian = $this->jobSectionModel->findAll();
        $day = $this->days->findAll();
        $baju = $this->employmentStatusModel->findAll();
        $factory = $this->factoriesModel->findAll();
        // $area = $this->factoryModel->findAll();
        // dd($karyawan);
        $data = [
            'role' => session()->get('role'),
            'title' => 'Karyawan',
            'active1' => 'active',
            'active2' => '',
            'active3' => '',
            // 'karyawan' => $karyawan,
            'area' => $area,
            'bagian' => $bagian,
            'day' => $day,
            'baju' => $baju,
            'factory' => $factory
        ];
        return view(session()->get('role') . '/detailKaryawan', $data);
    }

    public function historyPindahKaryawan()
    {
        $historyPindahKaryawan = $this->historyPindahKaryawanModel->getHistoryPindahKaryawan();
        $data = [
            'role' => session()->get('role'),
            'title' => 'History Pindah Karyawan',
            'active1' => '',
            'active2' => '',
            'active3' => 'active',
            'historyPindahKaryawan' => $historyPindahKaryawan
        ];
        return view(session()->get('role') . '/historyPindahKaryawan', $data);
    }

    public function chat()
    {
        $userId = session()->get('id_user'); // ID pengguna yang login
        $contacts = $this->usermodel->findAll(); // Ambil semua kontak dari database (selain pengguna yang login)

        $contactsWithLastMessage = [];

        foreach ($contacts as $contact) {
            if ($contact['id_user'] != $userId) {
                // Ambil pesan terakhir antara pengguna yang login dan kontak ini
                $lastMessage = $this->messageModel
                    ->where("(sender_id = $userId AND receiver_id = {$contact['id_user']}) OR (sender_id = {$contact['id_user']} AND receiver_id = $userId)")
                    ->orderBy('created_at', 'DESC')
                    ->limit(1)
                    ->first();

                $contactsWithLastMessage[] = [
                    'contact' => $contact,
                    'last_message' => $lastMessage
                ];
            }
        }

        $data = [
            'role' => session()->get('role'),
            'title' => 'Chat',
            'active4' => 'active',
            'contacts' => $contactsWithLastMessage // Kirim data kontak beserta pesan terakhir
        ];

        return view('chat/index', $data);
    }

    public function formerKaryawan()
    {
        $karyawan = $this->formerEmployeeModel->getFormerKaryawan();
        $factories = $this->factoriesModel->getFactoryName();
        $jobSections = $this->jobSectionModel->getJobSectionName();

        $data = [
            'role' => session()->get('role'),
            'title' => 'Former Karyawan',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'karyawan' => $karyawan,
            'factories' => $factories,
            'jobSections' => $jobSections,
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

    public function reactiveKaryawan()
    {
        $userId = session()->get('id_user');
        $id = $this->request->getPost('id_former_employee');
        $employeeCode = $this->request->getPost('employee_code');
        $tglReaktivasi = $this->request->getPost('tgl_reaktivasi');
        $tglPindah = $this->request->getPost('date_of_change');
        $keterangan = $this->request->getPost('keterangan');
        $idFactoryNew = $this->request->getPost('factory');
        $idJobSectionNew = $this->request->getPost('job_section');

        // 1️⃣ Ambil data karyawan yang akan direaktifasi
        $former = $this->formerEmployeeModel->where('id_former_employee', $id)->first();
        if (!$former) {
            return redirect()->back()->with('error', 'Data tidak ditemukan');
        }

        $job = $former['job_section_name'];
        $factoryName =  $former['factory_name'];
        $mainFactory = $former['main_factory'];
        $employmentStatus = $former['employment_status_name'];
        $clothesColor = $former['clothes_color'];
        $holiday = $former['holiday'];
        $additionalHoliday = $former['additional_holiday'];

        // 2️⃣ Update status former_employee jadi aktif (1)
        $update = $this->formerEmployeeModel->update($id, ['status' => '1']);
        if ($update) {
            $idJobSectionOld = $this->jobSectionModel->select('id_job_section')->where('job_section_name', $job)->first()['id_job_section'];
            $idFactoryOld = $this->factoriesModel->select('id_factory')->where('main_factory', $mainFactory)->where('factory_name', $factoryName)->first()['id_factory'];
            $idEmploymentStatus = $this->employmentStatusModel->select('id_employment_status')->where('employment_status_name', $employmentStatus)->where('clothes_color', $clothesColor)->first()['id_employment_status'];
            $idHoliday = $this->days->select('id_day')->where('day_name', $holiday)->first()['id_day'];
            $idAdditionalHoliday = $this->days->select('id_day')->where('day_name', $additionalHoliday)->first()['id_day'];

            // 3️⃣ Insert kembali ke table employee
            $this->employeeModel->insert([
                'employee_name'       => $former['employee_name'],
                'employee_code'       => $employeeCode,
                'shift'               => $former['shift'],
                'id_job_section'      => $idJobSectionNew,
                'id_factory'          => $idFactoryNew,
                'id_employment_status' => $idEmploymentStatus,
                'holiday'             => $idHoliday,
                'additional_holiday'  => $idAdditionalHoliday,
                'date_of_birth'       => $former['date_of_birth'],
                'date_of_joining'     => $tglReaktivasi,
                'status'              => 'Aktif'
            ]);

            $idEmployee = $this->employeeModel->getInsertID();

            // 3️⃣ Insert kembali ke table history employees
            $this->historyPindahKaryawanModel->insert([
                'id_employee'           => $idEmployee,
                'id_job_section_old'    => $idJobSectionOld,
                'id_factory_old'        => $idFactoryOld,
                'id_job_section_new'    => $idJobSectionNew,
                'id_factory_new'        => $idFactoryNew,
                'date_of_change'        => $tglPindah,
                'reason'                => $keterangan,
                'id_user'               => $userId,
            ]);

            return redirect()->back()->with('success', 'Karyawan berhasil diaktifkan kembali!');
        }
    }
}
