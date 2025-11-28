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
use App\Models\AbsensiModel;
use App\Models\AttendanceResultModel;
use App\Models\AttendanceDayModel;
use App\Models\ShiftAssignmentsModel;
use App\Models\AttendanceLetterModel;
use App\Models\ShiftDefModel;
use DateTime;

class AbsensiController extends BaseController
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
    protected $absensiModel;
    protected $attendanceResultModel;
    protected $attendanceDayModel;
    protected $attendanceLogModel;
    protected $shiftAssignmentsModel;
    protected $attendanceLetterModel;
    protected $shiftDefsModel;
    protected $idUser;

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
        $this->absensiModel = new AbsensiModel();
        $this->attendanceResultModel = new AttendanceResultModel();
        $this->attendanceDayModel = new AttendanceDayModel();
        $this->shiftAssignmentsModel = new ShiftAssignmentsModel();
        $this->attendanceLetterModel = new AttendanceLetterModel();
        $this->shiftDefsModel = new ShiftDefModel();

        $this->idUser = session()->get('id_user');
        $this->role = session()->get('role');
    }
    public function index()
    {
        // harian
        $HadirHariIni = $this->attendanceResultModel->getEmployeePresentTodayCount();
        $TtlKaryawan = $this->employeeModel->where('status', 'Aktif')->countAllResults();
        $IzinHariIni = $this->attendanceLetterModel->getIzinTodayCount();
        $SakitHariIni = $this->attendanceLetterModel->getSakitTodayCount();
        $MangkirHariIni = $this->attendanceLetterModel->getMangkirTodayCount();

        // minggu
        $DataHadirMinggu = $this->attendanceResultModel->getEmployeePresentWeekByDay();
        $DataIzinMinggu = $this->attendanceLetterModel->getIzinWeekByDay();
        $DataSakitMinggu = $this->attendanceLetterModel->getSakitWeekByDay();
        $DataMangkirMinggu = $this->attendanceLetterModel->getMangkirWeekByDay();

        // bulan
        $TotalHadirBulan = $this->attendanceResultModel->getEmployeeStatusMonth('PRESENT');
        $TotalIzinBulan = $this->attendanceLetterModel->getIzinMonthCount();
        $TotalSakitBulan = $this->attendanceLetterModel->getSakitMonthCount();
        $TotalMangkirBulan = $this->attendanceLetterModel->getMangkirMonthCount();

        // absen karyawan
        $AbsensiHariIni = $this->attendanceResultModel->getAttendanceTodayAllEmployees();

        // data top ketidakhadiran
        $TopKetidakhadiran = $this->attendanceLetterModel->getTopKetidakhadiran();
        // dd($TopKetidakhadiran);
        return view($this->role . '/index', [
            'role' => $this->role,
            'title' => 'Dashboard',
            'active1' => 'active',
            'active2' => '',
            'active3' => '',
            'HadirHariIni' => $HadirHariIni,
            'TtlKaryawan' => $TtlKaryawan,
            'IzinHariIni'   => $IzinHariIni,
            'SakitHariIni' => $SakitHariIni,
            'MangkirHariIni' => $MangkirHariIni,
            'DataHadirMinggu' => $DataHadirMinggu,
            'DataIzinMinggu' => $DataIzinMinggu,
            'DataSakitMinggu' => $DataSakitMinggu,
            'DataMangkirMinggu' => $DataMangkirMinggu,
            'TotalHadirBulan' => $TotalHadirBulan,
            'TotalIzinBulan' => $TotalIzinBulan,
            'TotalSakitBulan' => $TotalSakitBulan,
            'TotalMangkirBulan' => $TotalMangkirBulan,
            'AbsensiHariIni'    => $AbsensiHariIni,
            'TopKetidakhadiran' => $TopKetidakhadiran
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
            'karyawan' => $karyawan,
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

    public function dataAbsensi()
    {
        $bulanAbsen = $this->absensiModel->getLogAbsensi();

        $data = [
            'role'          => session()->get('role'),
            'title'         => 'Log Absen',
            'active1'       => '',
            'active2'       => '',
            'active3'       => 'active',
            'month'         => $bulanAbsen
        ];

        return view(session()->get('role') . '/dataAbsensi', $data);
    }


    public function detailAbsensi($month, $year)
    {
        $logAbsen = $this->absensiModel->getDetailLogAbsensi($month, $year);
        // dd($logAbsen);
        $data = [
            'role' => session()->get('role'),
            'title' => 'Data Absensi',
            'active1' => '',
            'active2' => '',
            'active3' => 'active',
            'logAbsen'     => $logAbsen,
            'month'        => $month,
            'year'         => $year
        ];
        return view(session()->get('role') . '/detailAbsensi', $data);
    }

    public function getDetailAbsensiAjax($month, $year)
    {
        $request = service('request');

        $start  = $request->getPost('start');
        $length = $request->getPost('length');
        $search = $request->getPost('search')['value'];

        $orderColIndex = $request->getPost('order')[0]['column'];
        $orderDir      = $request->getPost('order')[0]['dir'];
        $orderColumn   = $request->getPost('columns')[$orderColIndex]['data'];

        // Ambil data dari model
        $result = $this->absensiModel->getDetailLogAbsensiServer(
            $month,
            $year,
            $start,
            $length,
            $search,
            $orderColumn,
            $orderDir
        );

        // Format output DataTables
        $data = [];
        foreach ($result['data'] as $r) {
            $data[] = [
                'terminal_id'  => $r['terminal_id'],
                'nik'          => $r['nik'],
                'employee_name' => $r['employee_name'],
                'log_date'     => $r['log_date'],
                'log_time'     => $r['log_time'],
                'dept_card'    => $r['department'] . ' - ' . $r['card_no'],
                'admin'        => $r['admin'],
            ];
        }

        return $this->response->setJSON([
            "draw"            => intval($request->getPost('draw')),
            "recordsTotal"    => $result['total'],
            "recordsFiltered" => $result['filtered'],
            "data"            => $data
        ]);
    }


    public function upload()
    {
        $isAjax = $this->request->isAJAX();

        $sendError = function (string $message, int $code = 400) use ($isAjax) {
            if ($isAjax) {
                return $this->response
                    ->setStatusCode($code)
                    ->setJSON([
                        'status'  => 'error',
                        'message' => $message,
                    ]);
            }

            return redirect()->to(base_url($this->role . '/dataAbsensi'))
                ->with('error', $message);
        };

        $sendSuccess = function (string $message) use ($isAjax) {
            if ($isAjax) {
                return $this->response->setJSON([
                    'status'  => 'success',
                    'message' => $message,
                ]);
            }

            return redirect()->to(base_url($this->role . '/dataAbsensi'))
                ->with('success', $message);
        };

        if (! $this->request->is('post')) {
            return $sendError('Metode request tidak diizinkan.', 405);
        }

        $file = $this->request->getFile('file');

        // JANGAN pakai redirect langsung di bawah sini, pakai $sendError()
        if (! $file || ! $file->isValid() || $file->hasMoved()) {
            return $sendError('File tidak valid atau tidak ada file yang diunggah.');
        }

        $mime = $file->getClientMimeType();
        if (! in_array($mime, [
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        ], true)) {
            return $sendError('Tipe file tidak valid. Harus file Excel (.xls / .xlsx).');
        }

        if ($file->getSize() > 10 * 1024 * 1024) {
            return $sendError('Ukuran file melebihi 10 MB.');
        }

        try {
            // 3. Load spreadsheet
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getTempName());
            // $sheet       = $spreadsheet->getActiveSheet();

            $startRow = 2;
            // $lastRow  = $sheet->getHighestRow();

            $batchData     = [];
            $successCount  = 0;
            $errorCount    = 0;
            $errorMessages = [];
            $minDate       = null;
            $maxDate       = null;

            // 4. Loop tiap SHEET
            foreach ($spreadsheet->getWorksheetIterator() as $sheetIndex => $sheet) {
                $sheetName = $sheet->getTitle();
                $lastRow   = $sheet->getHighestRow();

                // Loop tiap BARIS di sheet ini
                // 4. Loop tiap baris
                for ($row = $startRow; $row <= $lastRow; $row++) {
                    $terminalId = trim((string) $sheet->getCell("B{$row}")->getValue());
                    $dateRaw    = trim((string) $sheet->getCell("C{$row}")->getFormattedValue());
                    $time       = trim((string) $sheet->getCell("D{$row}")->getFormattedValue());
                    $nik        = trim((string) $sheet->getCell("H{$row}")->getValue());
                    $cardNo     = trim((string) $sheet->getCell("I{$row}")->getValue());
                    $guestName  = trim((string) $sheet->getCell("J{$row}")->getValue());
                    $department = trim((string) $sheet->getCell("K{$row}")->getValue());
                    $verSource  = trim((string) $sheet->getCell("N{$row}")->getValue());

                    // Baris kosong → skip
                    if ($row === 1 || ($terminalId === '' && $dateRaw === '' && $nik === '')) {
                        continue;
                    }

                    $isValid = true;
                    $errMsg  = "Row {$row}: ";

                    // Wajib isi
                    if ($terminalId === '') {
                        $isValid  = false;
                        $errMsg  .= "Terminal ID kosong. ";
                    }

                    if ($nik === '') {
                        $isValid  = false;
                        $errMsg  .= "NIK kosong. ";
                    }

                    // Validasi tanggal
                    $dateSql = null;
                    if ($dateRaw === '') {
                        $isValid  = false;
                        $errMsg  .= "Tanggal kosong. ";
                    } else {
                        $formats = ['Y-m-d', 'd-m-Y', 'd/m/Y', 'm/d/Y'];
                        $parsed  = null;

                        foreach ($formats as $fmt) {
                            $dt = \DateTime::createFromFormat($fmt, $dateRaw);
                            if ($dt && $dt->format($fmt) === $dateRaw) {
                                $parsed = $dt;
                                break;
                            }
                        }

                        if ($parsed) {
                            $dateSql = $parsed->format('Y-m-d');
                        } else {
                            if (is_numeric($dateRaw)) {
                                try {
                                    $dt = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($dateRaw);
                                    $dateSql = $dt->format('Y-m-d');
                                } catch (\Throwable $e) {
                                    $dateSql = null;
                                }
                            }

                            if ($dateSql === null) {
                                $isValid = false;
                                $errMsg .= "Format tanggal tidak dikenali ({$dateRaw}). ";
                            }
                        }
                    }

                    $source = 'IMPORT';
                    $admin  = session()->get('username');

                    if ($isValid) {
                        if ($dateSql) {
                            if ($minDate === null || $dateSql < $minDate) {
                                $minDate = $dateSql;
                            }
                            if ($maxDate === null || $dateSql > $maxDate) {
                                $maxDate = $dateSql;
                            }
                        }

                        $batchData[] = [
                            'terminal_id'         => $terminalId,
                            'log_date'            => $dateSql,
                            'log_time'            => $time ?: null,
                            'nik'                 => $nik ?: null,
                            'card_no'             => $cardNo,
                            'employee_name'       => $guestName,
                            'department'          => $department,
                            'verification_source' => $verSource,
                            'source'              => $source,
                            'admin'               => $admin,
                            'created_at'          => date('Y-m-d H:i:s'),
                            'updated_at'          => date('Y-m-d H:i:s'),
                        ];

                        $successCount++;
                    } else {
                        $errorCount++;
                        $errorMessages[] = $errMsg;
                    }
                }
            }

            $processedDays = 0;

            // 6. Simpan batch
            if (! empty($batchData)) {
                $dates   = array_column($batchData, 'log_date');
                $minDate = min($dates);
                $maxDate = max($dates);

                $existingRows = $this->absensiModel
                    ->select('terminal_id, log_date, log_time, nik')
                    ->where('log_date >=', $minDate)
                    ->where('log_date <=', $maxDate)
                    ->findAll();

                $existingKeys = [];
                foreach ($existingRows as $er) {
                    $key = ($er['terminal_id'] ?? '') . '|' .
                        ($er['log_date']    ?? '') . '|' .
                        ($er['log_time']    ?? '') . '|' .
                        ($er['nik']         ?? '');
                    $existingKeys[$key] = true;
                }

                $seenInFile = [];
                $finalBatch = [];

                foreach ($batchData as $row) {
                    $key = ($row['terminal_id'] ?? '') . '|' .
                        ($row['log_date']    ?? '') . '|' .
                        ($row['log_time']    ?? '') . '|' .
                        ($row['nik']         ?? '');

                    if (isset($seenInFile[$key]) || isset($existingKeys[$key])) {
                        $errorCount++;
                        $errorMessages[] = "Duplikat log: NIK {$row['nik']} tanggal {$row['log_date']} jam {$row['log_time']}";
                        continue;
                    }

                    $seenInFile[$key] = true;
                    $finalBatch[]     = $row;
                }

                $successCount = count($finalBatch);

                if (! empty($finalBatch)) {
                    $this->absensiModel->insertBatch($finalBatch);

                    // Kalau mau grouping ke attendance_days, aktifkan ini:
                    // if ($minDate !== null) {
                    //     $svc = service('attendanceGrouping');
                    //     $processedDays = $svc->groupLogsToDays($minDate, $maxDate);
                    // }
                }
            }

            // 7. Build message
            if ($errorCount > 0) {
                $msg  = "{$successCount} baris berhasil disimpan ke log, ";
                $msg .= "{$errorCount} baris gagal:<br>" . implode('<br>', $errorMessages);

                if ($processedDays > 0) {
                    $msg .= "<br><br>{$processedDays} data hari kerja berhasil dipetakan ke attendance_days.";
                }

                return $sendError($msg);
            }

            $msg = "{$successCount} baris berhasil disimpan ke log.";
            if ($processedDays > 0) {
                $msg .= " {$processedDays} data hari kerja berhasil dipetakan ke attendance_days.";
            }

            return $sendSuccess($msg);
            // return $sendSuccess('Import absensi OK (dummy setelah try)');
        } catch (\Throwable $e) {
            log_message('error', 'Gagal import absensi: {err}', ['err' => $e->getMessage()]);

            return $sendError('Terjadi kesalahan saat memproses file. Coba lagi atau hubungi IT.');
        }
    }

    public function promoteView()
    {
        $dateFrom = $this->request->getGet('date_from');
        $dateTo   = $this->request->getGet('date_to') ?: $dateFrom;

        $data = [
            'role'         => session()->get('role'),
            'title'        => 'Kalkulasi Absen',
            'active1'      => '',
            'active2'      => '',
            'active3'      => 'active',
            'date_from'    => $dateFrom,
            'date_to'      => $dateTo,
            // card summary ambil dari flash hasil POST sebelumnya
            'processed'    => session()->getFlashdata('processed'),
            'resProcessed' => session()->getFlashdata('resProcessed'),
            // table sekarang selalu server-side, jadi tidak perlu kirim data ke view
            'days'         => [],
        ];
        // dd ($data);
        return view(session()->get('role') . '/Attendance/promote_form', $data);
    }

    public function promoteSubmit()
    {
        $role = session()->get('role');
        // if ($this->request->getMethod() !== 'post') {
        //     return redirect()->to(base_url($role . '/attendance/promote'));
        // }

        $dateFrom = $this->request->getPost('date_from');
        $dateTo   = $this->request->getPost('date_to') ?: $dateFrom;

        if (empty($dateFrom)) {
            return redirect()->back()->with('error', 'Tanggal awal wajib diisi.');
        }

        // 1) Jalankan service grouping: logs → attendance_days
        $groupSvc  = new \App\Services\AttendanceGroupingService();
        $daysCount = $groupSvc->groupLogsToDays($dateFrom, $dateTo);
        // 2) Hitung hasil kerja: attendance_days → attendance_results
        $resultSvc    = new \App\Services\AttendanceResultService();
        $resultsCount = $resultSvc->processRange($dateFrom, $dateTo);
        // dd ($dateFrom, $dateTo, $daysCount, $resultsCount);

        // Tidak lagi ambil semua data di sini.
        // View akan load data via DataTables server-side.

        return redirect()
            ->to(base_url($role . '/attendance/promote?date_from=' . $dateFrom . '&date_to=' . $dateTo))
            ->with('success', 'Kalkulasi absensi selesai.')
            ->with('processed', $daysCount)
            ->with('resProcessed', $resultsCount);
    }

    public function promoteData()
    {
        $role = session()->get('role');
        if (! $this->request->isAJAX()) {
            throw PageNotFoundException::forPageNotFound();
        }

        $dateFrom = $this->request->getGet('date_from');
        $dateTo   = $this->request->getGet('date_to') ?: $dateFrom;

        // Kalau belum pilih tanggal, balikin kosong saja
        if (empty($dateFrom)) {
            return $this->response->setJSON([
                'draw'            => (int) ($this->request->getGet('draw') ?? 1),
                'recordsTotal'    => 0,
                'recordsFiltered' => 0,
                'data'            => [],
            ]);
        }

        $draw   = (int) ($this->request->getGet('draw') ?? 1);
        $start  = (int) ($this->request->getGet('start') ?? 0);
        $length = (int) ($this->request->getGet('length') ?? 10);

        $searchValue = $this->request->getGet('search')['value'] ?? '';

        $orderColumnIndex = (int) ($this->request->getGet('order')[0]['column'] ?? 1);
        $orderDir         = $this->request->getGet('order')[0]['dir'] ?? 'asc';

        // Mapping kolom sesuai urutan di DataTables
        $columns = [
            0  => 'attendance_days.id_attendance', // #
            1  => 'attendance_days.work_date',
            2  => 'e.nik',
            3  => 'e.employee_name',
            4  => 's.shift_name',
            5  => 'attendance_days.in_time',
            6  => 'attendance_days.break_out_time',
            7  => 'attendance_days.break_in_time',
            8  => 'attendance_days.out_time',
            9  => 'r.total_work_min',
            10 => 'r.total_break_min',
            11 => 'r.late_min',
            12 => 'r.early_leave_min',
            13 => 'r.overtime_min',
            14 => 'r.status_code',
        ];

        $orderColumn = $columns[$orderColumnIndex] ?? 'attendance_days.work_date';

        $dayM = new AttendanceDayModel();
        $db   = $dayM->db; // atau db_connect()

        // Base query
        $builder = $db->table('attendance_days')
            ->select('
                attendance_days.id_attendance,
                attendance_days.work_date,
                attendance_days.in_time,
                attendance_days.break_out_time,
                attendance_days.break_in_time,
                attendance_days.out_time,
                e.nik,
                e.employee_name,
                s.shift_name,
                s.id_shift,
                r.total_work_min,
                r.total_break_min,
                r.late_min,
                r.early_leave_min,
                r.overtime_min,
                r.status_code
            ')
            ->join('employees e', 'e.id_employee = attendance_days.id_employee', 'left')
            ->join('shift_defs s', 's.id_shift = attendance_days.id_shift', 'left')
            ->join('attendance_results r', 'r.id_attendance = attendance_days.id_attendance', 'left')
            ->where('attendance_days.work_date >=', $dateFrom)
            ->where('attendance_days.work_date <=', $dateTo);

        // Total tanpa filter search
        $builderTotal = clone $builder;
        $recordsTotal = $builderTotal->countAllResults();

        // Filtering search (nik, nama, tanggal)
        if ($searchValue !== '') {
            $builder->groupStart()
                ->like('e.nik', $searchValue)
                ->orLike('e.employee_name', $searchValue)
                ->orLike('attendance_days.work_date', $searchValue)
                ->groupEnd();
        }

        // Total setelah filter search
        $builderFiltered   = clone $builder;
        $recordsFiltered   = $builderFiltered->countAllResults();

        // Order, limit, offset
        $builder->orderBy($orderColumn, $orderDir)
            ->limit($length, $start);

        $query = $builder->get();
        $rows  = $query->getResultArray();

        $data  = [];
        $no    = $start + 1;

        foreach ($rows as $row) {
            $status     = $row['status_code'] ?? '-';
            $badgeClass = 'bg-gradient-secondary';
            if ($status === 'PRESENT') {
                $badgeClass = 'bg-gradient-info';
            } elseif ($status === 'LATE') {
                $badgeClass = 'bg-gradient-danger';
            } elseif ($status === 'L') {
                $badgeClass = 'bg-gradient-warning';
            }

            $statusHtml = '<span class="badge ' . $badgeClass . ' px-3">'
                . esc($status)
                . '</span>';

            $shiftLabel = esc($row['shift_name'] ?? ($row['id_shift'] ?? '-'));
            $shiftHtml  = '<span class="badge bg-gradient-secondary">'
                . $shiftLabel
                . '</span>';

            // Sesuaikan HTML sama view-mu (icon, time-badge, etc)
            $data[] = [
                // #
                '<span class="text-xs text-secondary">' . $no++ . '</span>',

                // Tanggal
                '<span class="d-flex align-items-center">
                    <i class="fas fa-calendar-alt text-muted me-2"></i>'
                    . esc($row['work_date']) .
                    '</span>',

                // NIK
                esc($row['nik'] ?? '-'),

                // Nama
                esc($row['employee_name'] ?? '-'),

                // Shift
                $shiftHtml,

                // Masuk
                '<span class="time-badge">' . esc($row['in_time'] ?? '-') . '</span>',

                // Istirahat
                '<span class="time-badge">' . esc($row['break_out_time'] ?? '-') . '</span>',

                // Kembali
                '<span class="time-badge">' . esc($row['break_in_time'] ?? '-') . '</span>',

                // Pulang
                '<span class="time-badge">' . esc($row['out_time'] ?? '-') . '</span>',

                // Kerja
                '<span class="metric-positive text-xs">'
                    . esc($row['total_work_min'] ?? 0) . ' <small>m</small>'
                    . '</span>',

                // Break
                '<span class="metric-neutral text-xs">'
                    . esc($row['total_break_min'] ?? 0) . ' <small>m</small>'
                    . '</span>',

                // Telat
                '<span class="metric-negative text-xs">'
                    . (($row['late_min'] ?? 0) > 0 ? '<i class="fas fa-arrow-up me-1"></i>' : '')
                    . esc($row['late_min'] ?? 0) . ' <small>m</small>'
                    . '</span>',

                // Pulang cepat
                '<span class="metric-negative text-xs">'
                    . (($row['early_leave_min'] ?? 0) > 0 ? '<i class="fas fa-arrow-up me-1"></i>' : '')
                    . esc($row['early_leave_min'] ?? 0) . ' <small>m</small>'
                    . '</span>',

                // Lembur
                '<span class="metric-positive text-xs">'
                    . (($row['overtime_min'] ?? 0) > 0 ? '<i class="fas fa-plus-circle me-1"></i>' : '')
                    . esc($row['overtime_min'] ?? 0) . ' <small>m</small>'
                    . '</span>',

                // Status
                $statusHtml,
            ];
        }

        return $this->response->setJSON([
            'draw'            => $draw,
            'recordsTotal'    => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data'            => $data,
        ]);
    }

    public function reportDataAbsensi()
    {
        $dateFrom = $this->request->getGet('tglAwal');
        $dateTo   = $this->request->getGet('tglAkhir');

        $results = [];

        if ($dateFrom && $dateTo) {
            $results = $this->attendanceDayModel->getAttendanceResults($dateFrom, $dateTo);
        }

        $data = [
            'role'        => session()->get('role'),
            'title'       => 'Report Data Absensi',
            'active1'     => '',
            'active2'     => '',
            'active3'     => 'active',
            'results'     => $results,
            'tglAwal'     => $dateFrom,
            'tglAkhir'    => $dateTo
        ];

        return view(session()->get('role') . '/reportDataAbsensi', $data);
    }

    public function tambahDataAbsensi()
    {
        $data = [
            'role'      => session()->get('role'),
            'title'     => 'Tambah Data Absensi',
            'active1'   => '',
            'active2'   => '',
            'active3'   => 'active',
        ];
        return view(session()->get('role') . '/tambahDataAbsensi', $data);
    }

    public function getKaryawanByTglAbsen()
    {
        $tglAbsen = $this->request->getGet('date');

        $sudahdiolah = $this->attendanceDayModel->getKaryawanByTglAbsen($tglAbsen);
        $logs = $this->absensiModel->getkaryawan($tglAbsen);

        $idSudah = array_column($sudahdiolah, 'id_employee');

        $karyawan = [];

        foreach ($logs as $data) {
            $idEmpLog = $data['id_employee'];
            if (!in_array($idEmpLog, $idSudah)) {
                $karyawan[] = $data;
            }
        }

        return $this->response->setJSON($karyawan);
    }

    public function getLogAbsensiByNIKAndDate()
    {
        $nik = $this->request->getGet('nik');
        $date = $this->request->getGet('date');

        $logs = $this->absensiModel->getLogAbsensiByNIKAndDate($nik, $date);

        return $this->response->setJSON($logs);
    }

    public function tambahDataAbsensiStore()
    {
        $data = $this->request->getPost();
        $idEmployee = [];

        foreach ($data['employee'] as $i => $nik) {
            $nama = $data['employee_name'][$i];

            $getEmployee = $this->employeeModel
                ->select('id_employee, employee_name, nik')
                ->where('nik', $nik)
                ->orWhere('employee_name', $nama)
                ->first();

            $idEmployee[] = $getEmployee;
        }

        $workDate = [];
        foreach ($data['in_time'] as $key) {
            $workDate[] = date('Y-m-d', strtotime($key));
        }

        $idShiftFinal = [];
        $dataError = [];

        foreach ($idEmployee as $index => $empId) {

            $getAllShift = $this->shiftAssignmentsModel->getShiftByEmployee($empId['id_employee']);
            $namaKaryawan = $empId['employee_name'];

            if (!$getAllShift || count($getAllShift) == 0) {
                $nik = $data['employee'][$index];
                $dataError[] = "$namaKaryawan, NIK = $nik belum memiliki shift assignment!";
                continue;
            }

            $shiftStart = array_column($getAllShift, 'start_time');
            $shiftId    = array_column($getAllShift, 'id_shift');

            $inTime = $data['in_time'][$index];
            $jamMasuk = date('H:i', strtotime($inTime));

            $foundShift = null;

            foreach ($shiftStart as $i => $start) {

                if (!$start) continue;
                $shiftJam = date('H:i', strtotime($start));
                $diff = abs(strtotime($jamMasuk) - strtotime($shiftJam));

                if ($foundShift === null || $diff < $foundShift['diff']) {
                    $foundShift = [
                        'id_shift' => $shiftId[$i],
                        'diff' => $diff
                    ];
                }
            }

            $idShiftFinal[] = $foundShift['id_shift'];
        }

        if (!empty($dataError)) {
            return redirect()->back()->with('error', implode("\n", $dataError));
        }

        foreach ($idEmployee as $i => $empId) {
            $this->attendanceDayModel->insert([
                'id_employee'    => $empId['id_employee'],
                'work_date'      => $workDate[$i],
                'id_shift'       => $idShiftFinal[$i],
                'in_time'        => $data['in_time'][$i],
                'break_out_time' => $data['break_out_time'][$i],
                'break_in_time'  => $data['break_in_time'][$i],
                'out_time'       => $data['out_time'][$i],
                'status_final'   => 'LOCKED',
                'verified_by'    => $this->idUser,
                'verified_at'    => date('Y-m-d H:i:s'),
                'note'           => null,
                'created_at'     => date('Y-m-d H:i:s'),
                'updated_at'     => date('Y-m-d H:i:s'),
            ]);
            $idAttendance[] = $this->attendanceDayModel->getInsertID();
        }

        function timeToSeconds($time)
        {
            list($h, $m, $s) = explode(':', $time);
            return ($h * 3600) + ($m * 60) + $s;
        }

        foreach ($idAttendance as $id) {

            $dataAttendanceDay = $this->attendanceDayModel->getAttendanceDayById($id);

            $inTimeNew        = new DateTime($dataAttendanceDay['in_time']);
            $breakOutTimeNew  = new DateTime($dataAttendanceDay['break_out_time']);
            $breakInTimeNew   = new DateTime($dataAttendanceDay['break_in_time']);
            $outTimeNew       = new DateTime($dataAttendanceDay['out_time']);

            $jamMasukAktual      = $inTimeNew->getTimestamp();
            $jamKeluarIstirahat  = $breakOutTimeNew->getTimestamp();
            $jamMasukIstirahat   = $breakInTimeNew->getTimestamp();
            $jamPulangAktual     = $outTimeNew->getTimestamp();

            $totIstirahat = $jamMasukIstirahat - $jamKeluarIstirahat;
            $totKerja     = ($jamPulangAktual - $jamMasukAktual) - $totIstirahat;

            $totIstirahatMenit = $totIstirahat / 60;
            $totKerjaMenit     = $totKerja / 60;

            // Ambil jam masuk shift
            $cekJamMasuk   = $this->shiftDefsModel->getShiftById($dataAttendanceDay['id_shift']);

            $masterJamMasukShift  = $cekJamMasuk['start_time'];
            $masterJamKeluarShift = $cekJamMasuk['end_time'];

            $inTimeOnly  = $inTimeNew->format('H:i:s');
            $outTimeOnly = $outTimeNew->format('H:i:s');

            // Konversi semua ke detik
            $secInActual   = timeToSeconds($inTimeOnly);
            $secOutActual  = timeToSeconds($outTimeOnly);
            $secShiftStart = timeToSeconds($masterJamMasukShift);
            $secShiftEnd   = timeToSeconds($masterJamKeluarShift);

            // Hitung telat masuk (menit)
            $diffMasuk = $secInActual - $secShiftStart; // Positif = telat
            if ($diffMasuk > 0) {
                $lateMin = (int) round($diffMasuk / 60);
            } else {
                $lateMin = 0;
            }

            $diffPulang = $secOutActual - $secShiftEnd;

            if ($diffPulang < 0) {
                $earlyLeaveMin = (int) round(abs($diffPulang) / 60); // Pulang lebih awal
                $overtimeMin = 0;
            } elseif ($diffPulang > 0) {
                $overtimeMin = (int) round($diffPulang / 60); // Lembur
                $earlyLeaveMin = 0;
            } else {
                $earlyLeaveMin = 0;
                $overtimeMin = 0;
            }

            $this->attendanceResultModel->insert([
                'id_attendance'    => $id,
                'total_work_min'   => $totKerjaMenit,
                'total_break_min'  => $totIstirahatMenit,
                'late_min'         => $lateMin,
                'early_leave_min'  => $earlyLeaveMin,
                'overtime_min'     => $overtimeMin,
                'status_code'      => 'PRESENT',
                'processed_at'     => date('Y-m-d H:i:s')
            ]);
        }

        return redirect()->back()->with('success', 'Data absensi berhasil ditambahkan!');
    }

    public function ketidaksesuaianAbsensi($workDateStart = null, $workDateEnd = null)
    {
        if ($workDateStart !== null && $workDateEnd === null) {
            $workDateEnd = $workDateStart;
        }

        $data = [
            'role'      => session()->get('role'),
            'title'     => 'Ketidaksesuaian Absensi',
            'active1'   => '',
            'active2'   => '',
            'active3'   => 'active',
            'startDate' => $workDateStart,
            'endDate'   => $workDateEnd
        ];
        return view(session()->get('role') . '/ketidaksesuaianAbsensi', $data);
    }

    public function getKetidaksesuaianData()
    {
        $startDate = $this->request->getGet('startDate');
        $endDate   = $this->request->getGet('endDate');

        if ($startDate && !$endDate) {
            $endDate = $startDate;
        }

        $data = $this->attendanceDayModel->getDataTidakSesuaiByDate($startDate, $endDate);

        return $this->response->setJSON([
            'data' => $data
        ]);
    }

    public function updateAbsen($id)
    {
        $dataAbsen = $this->attendanceDayModel->find($id);

        $inTime = date('Y-m-d H:i:s', strtotime($this->request->getPost('in_time')));
        $breakOutTime = date('Y-m-d H:i:s', strtotime($this->request->getPost('break_out_time')));
        $breakInTime = date('Y-m-d H:i:s', strtotime($this->request->getPost('break_in_time')));
        $outTime = date('Y-m-d H:i:s', strtotime($this->request->getPost('out_time')));

        $data = [
            'in_time'        => $inTime,
            'break_out_time' => $breakOutTime,
            'break_in_time'  => $breakInTime,
            'out_time'       => $outTime,
        ];

        $update = $this->attendanceDayModel->update($id, $data);

        // Ambil Attendance Result nya
        $idResult = $this->attendanceResultModel
            ->select('id_result')
            ->where('id_attendance', $id)
            ->first();

        // Master shift
        $idShift = $dataAbsen['id_shift'];
        $masterShift = $this->shiftDefsModel->getShiftById($idShift);

        $masterJamMasuk = strtotime($dataAbsen['work_date'] . " " . $masterShift['start_time']);
        $masterJamPulang = strtotime($dataAbsen['work_date'] . " " . $masterShift['end_time']);
        $masterTotIst = (int) $masterShift['break_time']; // menit berdasarkan master shift

        // Convert ke timestamp
        $tsMasuk = strtotime($inTime);
        $tsKeluar = strtotime($outTime);
        $tsBreakOut = strtotime($breakOutTime);
        $tsBreakIn = strtotime($breakInTime);

        // Hitung durasi
        $totIstirahat = (int) round(($tsBreakIn - $tsBreakOut) / 60);
        $totKerja = (int) round(($tsKeluar - $tsMasuk) / 60) - $totIstirahat;

        // Hitung keterlambatan
        $diffMasuk = $tsMasuk - $masterJamMasuk;
        $lateMin = $diffMasuk > 0 ? (int) round($diffMasuk / 60) : 0;

        // Hitung pulang awal atau lembur
        $diffPulang = $tsKeluar - $masterJamPulang;

        if ($diffPulang < 0) {
            $earlyLeaveMin = (int) round(abs($diffPulang) / 60);
            $overtimeMin = 0;
        } elseif ($diffPulang > 0) {
            $overtimeMin = (int) round($diffPulang / 60);
            $earlyLeaveMin = 0;
        } else {
            $overtimeMin = 0;
            $earlyLeaveMin = 0;
        }

        // Update hasil perhitungan ke table result
        $this->attendanceResultModel->update($idResult['id_result'], [
            'id_attendance'    => $id,
            'total_work_min'   => $totKerja,
            'total_break_min'  => $totIstirahat,
            'late_min'         => $lateMin,
            'early_leave_min'  => $earlyLeaveMin,
            'overtime_min'     => $overtimeMin,
            'status_code'      => 'PRESENT',
            'processed_at'     => date('Y-m-d H:i:s'),
        ]);

        return $this->response->setJSON([
            'status'  => $update ? 'success' : 'error',
            'message' => $update ? 'Berhasil update absensi' : 'Gagal update absensi'
        ]);
    }
}
