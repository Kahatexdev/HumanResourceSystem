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
        // dd($logAbsen);
        $data = [
            'role' => session()->get('role'),
            'title' => 'Data Absensi',
            'active1' => '',
            'active2' => '',
            'active3' => 'active',
            'month'     => $bulanAbsen
        ];
        return view(session()->get('role') . '/dataAbsensi', $data);
    }

    public function detailAbsensi($month, $year)
    {
        $logAbsen = $this->absensiModel->getDetailLogAbsensi($month, $year);
        dd ($logAbsen);
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

    // public function upload()
    // {
    //     $file = $this->request->getFile('file');

    //     // 1. Validasi file upload
    //     if (! $file || ! $file->isValid() || $file->hasMoved()) {
    //         return redirect()->to(base_url($this->role . '/dataAbsensi'))
    //             ->with('error', 'File tidak valid atau tidak ada file yang diunggah.');
    //     }

    //     // 2. Validasi MIME type (hanya Excel)
    //     $mime = $file->getClientMimeType();
    //     if (! in_array($mime, [
    //         'application/vnd.ms-excel',
    //         'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
    //     ])) {
    //         return redirect()->to(base_url($this->role . '/dataAbsensi'))
    //             ->with('error', 'Tipe file tidak valid. Harus file Excel (.xls / .xlsx).');
    //     }

    //     // 3. Load spreadsheet
    //     $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getTempName());
    //     $sheet       = $spreadsheet->getActiveSheet();

    //     // Asumsi baris pertama adalah header → mulai dari baris 1
    //     $startRow = 1;
    //     $lastRow  = $sheet->getHighestRow();

    //     $batchData     = [];
    //     $successCount  = 0;
    //     $errorCount    = 0;
    //     $errorMessages = [];

    //     // 4. Loop tiap baris
    //     for ($row = $startRow; $row <= $lastRow; $row++) {

    //         // Ambil nilai dari tiap kolom (sesuai mapping file-mu)
    //         $terminalId = trim((string) $sheet->getCell("B{$row}")->getValue());
    //         $dateRaw    = trim((string) $sheet->getCell("C{$row}")->getFormattedValue());
    //         $time       = trim((string) $sheet->getCell("D{$row}")->getFormattedValue());
    //         $nik        = trim((string) $sheet->getCell("H{$row}")->getValue());
    //         $cardNo     = trim((string) $sheet->getCell("I{$row}")->getValue());
    //         $guestName  = trim((string) $sheet->getCell("J{$row}")->getValue());
    //         $department = trim((string) $sheet->getCell("K{$row}")->getValue());
    //         $verSource  = trim((string) $sheet->getCell("N{$row}")->getValue());

    //         // Kalau baris kosong semua → skip
    //         if ($terminalId === '' && $dateRaw === '' && $nik === '') {
    //             continue;
    //         }

    //         $isValid = true;
    //         $errMsg  = "Row {$row}: ";

    //         // 4.a Validasi wajib isi
    //         if ($terminalId === '') {
    //             $isValid  = false;
    //             $errMsg  .= "Terminal ID kosong. ";
    //         }

    //         if ($nik === '') {
    //             $isValid  = false;
    //             $errMsg  .= "NIK kosong. ";
    //         }

    //         // 4.b Validasi & normalisasi tanggal
    //         $dateSql = null;
    //         if ($dateRaw === '') {
    //             $isValid  = false;
    //             $errMsg  .= "Tanggal kosong. ";
    //         } else {
    //             // Coba beberapa format umum (silakan sesuaikan kalau formatmu fix)
    //             $formats = ['Y-m-d', 'd-m-Y', 'd/m/Y', 'm/d/Y'];
    //             $parsed  = null;

    //             foreach ($formats as $fmt) {
    //                 $dt = \DateTime::createFromFormat($fmt, $dateRaw);
    //                 if ($dt && $dt->format($fmt) === $dateRaw) {
    //                     $parsed = $dt;
    //                     break;
    //                 }
    //             }

    //             if ($parsed) {
    //                 $dateSql = $parsed->format('Y-m-d');
    //             } else {
    //                 // Kalau dari mesin absensi berupa angka Excel date
    //                 if (is_numeric($dateRaw)) {
    //                     try {
    //                         $dt = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($dateRaw);
    //                         $dateSql = $dt->format('Y-m-d');
    //                     } catch (\Throwable $e) {
    //                         $dateSql = null;
    //                     }
    //                 }

    //                 if ($dateSql === null) {
    //                     $isValid = false;
    //                     $errMsg .= "Format tanggal tidak dikenali ({$dateRaw}). ";
    //                 }
    //             }
    //         }

    //         $source = 'IMPORT';
    //         $admin  = session()->get('username');

    //         // 5. Jika lolos validasi → siapkan untuk insert
    //         if ($isValid) {
    //             $batchData[] = [
    //                 // ==== SESUAIKAN DENGAN NAMA KOLOM DI TABEL import kamu ====
    //                 'terminal_id'         => $terminalId,
    //                 'log_date'          => $dateSql,          // atau 'log_date'
    //                 'log_time'          => $time ?: null,     // atau 'log_time'
    //                 'nik'                 => $nik ?: null,
    //                 'card_no'             => $cardNo,
    //                 'employee_name'       => $guestName,
    //                 'department'          => $department,
    //                 'verification_source' => $verSource,
    //                 'source'              => $source,
    //                 'admin'               => $admin,
    //                 'created_at'          => date('Y-m-d H:i:s'),
    //                 'updated_at'          => date('Y-m-d H:i:s'),
    //             ];

    //             $successCount++;
    //         } else {
    //             $errorCount++;
    //             $errorMessages[] = $errMsg;
    //         }
    //     }
    //     // dd($batchData);
    //     // 6. Simpan semua baris valid sekaligus
    //     if (! empty($batchData)) {
    //         // GANTI model dengan model tabel import absensimu
    //         $this->absensiModel->insertBatch($batchData);
    //     }

    //     // 7. Bangun flash message & redirect
    //     $role        = session()->get('role');
    //     $redirectUrl = base_url($role . '/dataAbsensi');

    //     if ($errorCount > 0) {
    //         $msg  = "{$successCount} baris berhasil disimpan, ";
    //         $msg .= "{$errorCount} baris gagal:<br>" . implode('<br>', $errorMessages);

    //         return redirect()->to($redirectUrl)->with('error', $msg);
    //     }

    //     return redirect()->to($redirectUrl)
    //         ->with('success', "{$successCount} baris berhasil disimpan.");
    // }

    // public function upload()
    // {
    //     $file = $this->request->getFile('file');

    //     // 1. Validasi file upload
    //     if (! $file || ! $file->isValid() || $file->hasMoved()) {
    //         return redirect()->to(base_url($this->role . '/dataAbsensi'))
    //             ->with('error', 'File tidak valid atau tidak ada file yang diunggah.');
    //     }

    //     // 2. Validasi MIME type (hanya Excel)
    //     $mime = $file->getClientMimeType();
    //     if (! in_array($mime, [
    //         'application/vnd.ms-excel',
    //         'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
    //     ])) {
    //         return redirect()->to(base_url($this->role . '/dataAbsensi'))
    //             ->with('error', 'Tipe file tidak valid. Harus file Excel (.xls / .xlsx).');
    //     }

    //     // 3. Load spreadsheet
    //     $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getTempName());
    //     $sheet       = $spreadsheet->getActiveSheet();

    //     // Asumsi baris pertama adalah header → mulai dari baris 1
    //     $startRow = 1;
    //     $lastRow  = $sheet->getHighestRow();

    //     $batchData     = [];
    //     $successCount  = 0;
    //     $errorCount    = 0;
    //     $errorMessages = [];

    //     // untuk menyimpan range tanggal yang berhasil di-import
    //     $minDate = null;
    //     $maxDate = null;

    //     // 4. Loop tiap baris
    //     for ($row = $startRow; $row <= $lastRow; $row++) {

    //         // Ambil nilai dari tiap kolom (sesuai mapping file-mu)
    //         $terminalId = trim((string) $sheet->getCell("B{$row}")->getValue());
    //         $dateRaw    = trim((string) $sheet->getCell("C{$row}")->getFormattedValue());
    //         $time       = trim((string) $sheet->getCell("D{$row}")->getFormattedValue());
    //         $nik        = trim((string) $sheet->getCell("H{$row}")->getValue());
    //         $cardNo     = trim((string) $sheet->getCell("I{$row}")->getValue());
    //         $guestName  = trim((string) $sheet->getCell("J{$row}")->getValue());
    //         $department = trim((string) $sheet->getCell("K{$row}")->getValue());
    //         $verSource  = trim((string) $sheet->getCell("N{$row}")->getValue());

    //         // Kalau baris kosong semua → skip
    //         if ($terminalId === '' && $dateRaw === '' && $nik === '') {
    //             continue;
    //         }

    //         $isValid = true;
    //         $errMsg  = "Row {$row}: ";

    //         // 4.a Validasi wajib isi
    //         if ($terminalId === '') {
    //             $isValid  = false;
    //             $errMsg  .= "Terminal ID kosong. ";
    //         }

    //         if ($nik === '') {
    //             $isValid  = false;
    //             $errMsg  .= "NIK kosong. ";
    //         }

    //         // 4.b Validasi & normalisasi tanggal
    //         $dateSql = null;
    //         if ($dateRaw === '') {
    //             $isValid  = false;
    //             $errMsg  .= "Tanggal kosong. ";
    //         } else {
    //             // Coba beberapa format umum (silakan sesuaikan kalau formatmu fix)
    //             $formats = ['Y-m-d', 'd-m-Y', 'd/m/Y', 'm/d/Y'];
    //             $parsed  = null;

    //             foreach ($formats as $fmt) {
    //                 $dt = \DateTime::createFromFormat($fmt, $dateRaw);
    //                 if ($dt && $dt->format($fmt) === $dateRaw) {
    //                     $parsed = $dt;
    //                     break;
    //                 }
    //             }

    //             if ($parsed) {
    //                 $dateSql = $parsed->format('Y-m-d');
    //             } else {
    //                 // Kalau dari mesin absensi berupa angka Excel date
    //                 if (is_numeric($dateRaw)) {
    //                     try {
    //                         $dt = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($dateRaw);
    //                         $dateSql = $dt->format('Y-m-d');
    //                     } catch (\Throwable $e) {
    //                         $dateSql = null;
    //                     }
    //                 }

    //                 if ($dateSql === null) {
    //                     $isValid = false;
    //                     $errMsg .= "Format tanggal tidak dikenali ({$dateRaw}). ";
    //                 }
    //             }
    //         }

    //         $source = 'IMPORT';
    //         $admin  = session()->get('username');

    //         // 5. Jika lolos validasi → siapkan untuk insert
    //         if ($isValid) {

    //             // update range tanggal min/max yang berhasil di-import
    //             if ($dateSql) {
    //                 if ($minDate === null || $dateSql < $minDate) {
    //                     $minDate = $dateSql;
    //                 }
    //                 if ($maxDate === null || $dateSql > $maxDate) {
    //                     $maxDate = $dateSql;
    //                 }
    //             }

    //             $batchData[] = [
    //                 // ==== SESUAIKAN DENGAN NAMA KOLOM DI TABEL attendance_logs ====
    //                 'terminal_id'         => $terminalId,
    //                 'log_date'            => $dateSql,
    //                 'log_time'            => $time ?: null,
    //                 'nik'                 => $nik ?: null,
    //                 'card_no'             => $cardNo,
    //                 'employee_name'       => $guestName,
    //                 'department'          => $department,
    //                 'verification_source' => $verSource,
    //                 'source'              => $source,
    //                 'admin'               => $admin,
    //                 'created_at'          => date('Y-m-d H:i:s'),
    //                 'updated_at'          => date('Y-m-d H:i:s'),
    //             ];

    //             $successCount++;
    //         } else {
    //             $errorCount++;
    //             $errorMessages[] = $errMsg;
    //         }
    //     }
    //     // dd($batchData);

    //     // 6. Simpan semua baris valid sekaligus
    //     $processedDays = 0; // jumlah record attendance_days yang diproses oleh service

    //     if (! empty($batchData)) {

    //         // --- Hitung ulang min/max date dari batchData (lebih aman) ---
    //         $dates = array_column($batchData, 'log_date');
    //         $minDate = min($dates);
    //         $maxDate = max($dates);

    //         // --- Ambil log yang sudah ada di DB di range tanggal tsb ---
    //         $existingRows = $this->absensiModel
    //             ->select('terminal_id, log_date, log_time, nik')
    //             ->where('log_date >=', $minDate)
    //             ->where('log_date <=', $maxDate)
    //             ->findAll();

    //         // Simpan sebagai set key unik
    //         $existingKeys = [];
    //         foreach ($existingRows as $er) {
    //             $key = ($er['terminal_id'] ?? '') . '|' .
    //                 ($er['log_date']    ?? '') . '|' .
    //                 ($er['log_time']    ?? '') . '|' .
    //                 ($er['nik']         ?? '');
    //             $existingKeys[$key] = true;
    //         }

    //         // --- Filter duplikat (di file & di DB) ---
    //         $seenInFile  = [];
    //         $finalBatch  = [];
    //         $dupCount    = 0;

    //         foreach ($batchData as $row) {
    //             $key = ($row['terminal_id'] ?? '') . '|' .
    //                 ($row['log_date']    ?? '') . '|' .
    //                 ($row['log_time']    ?? '') . '|' .
    //                 ($row['nik']         ?? '');

    //             // Duplikat di file atau sudah ada di DB
    //             if (isset($seenInFile[$key]) || isset($existingKeys[$key])) {
    //                 $dupCount++;
    //                 $errorCount++;
    //                 $errorMessages[] = "Duplikat log: NIK {$row['nik']} tanggal {$row['log_date']} jam {$row['log_time']}";
    //                 continue;
    //             }

    //             $seenInFile[$key] = true;
    //             $finalBatch[]     = $row;
    //         }

    //         // Revisi jumlah sukses: yang benar-benar akan di-insert
    //         $successCount = count($finalBatch);

    //         if (! empty($finalBatch)) {
    //             // pastikan model ini adalah model untuk tabel attendance_logs
    //             $this->absensiModel->insertBatch($finalBatch);

    //             // 6.b Setelah insert ke attendance_logs, panggil service grouping (kalau mau)
    //             // if ($minDate !== null) {
    //             //     $svc = service('attendanceGrouping'); // dari Config\Services
    //             //     $processedDays = $svc->groupLogsToDays($minDate, $maxDate);
    //             // }
    //         }
    //     }

    //     // 7. Bangun flash message & redirect
    //     $role        = session()->get('role');
    //     $redirectUrl = base_url($role . '/dataAbsensi');

    //     if ($errorCount > 0) {
    //         $msg  = "{$successCount} baris berhasil disimpan ke log, ";
    //         $msg .= "{$errorCount} baris gagal:<br>" . implode('<br>', $errorMessages);

    //         if ($processedDays > 0) {
    //             $msg .= "<br><br>{$processedDays} data hari kerja berhasil dipetakan ke attendance_days.";
    //         }

    //         return $this->response->setJSON([
    //             'status'  => 'error',
    //             'message' => $msg
    //         ]);
    //     }

    //     $msg = "{$successCount} baris berhasil disimpan ke log.";
    //     if ($processedDays > 0) {
    //         $msg .= " {$processedDays} data hari kerja berhasil dipetakan ke attendance_days.";
    //     }

    //     return $this->response->setJSON([
    //         'status'  => 'success',
    //         'message' => $msg
    //     ]);
    // }

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


    public function promote()
    {
        $role = session()->get('role');

        $dateFrom = $this->request->getPost('date_from');
        $dateTo   = $this->request->getPost('date_to') ?: $dateFrom;

        // 1) Jalankan grouping
        $processed = $this->groupSvc->groupLogsToDays($dateFrom, $dateTo);

        // 2) Ambil data attendance_days yang sudah ada di range tanggal tsb
        $days = $this->dayM
            ->select('attendance_days.*, e.nik, e.full_name, s.shift_name')
            ->join('employees e', 'e.id_employee = attendance_days.id_employee', 'left')
            ->join('shift_defs s', 's.id_shift = attendance_days.id_shift', 'left')
            ->where('work_date >=', $dateFrom)
            ->where('work_date <=', $dateTo)
            ->orderBy('work_date', 'ASC')
            ->orderBy('e.nik', 'ASC')
            ->findAll();

        $data = [
            'role'      => $role,
            'title'     => 'Promote Form',
            'active1'   => '',
            'active2'   => '',
            'active3'   => 'active',
            'date_from' => $dateFrom,
            'date_to'   => $dateTo,
            'processed' => $processed,
            'days'      => $days,
        ];

        return view($role . '/Attendance/promote_form', $data);
    }

    /**
     * Halaman form sederhana untuk pilih tanggal yang mau diproses
     */
    public function promoteForm()
    {
        $data = [
            'role'      => session()->get('role'),
            'title'     => 'Promote Form',
            'active1'   => '',
            'active2'   => '',
            'active3'   => 'active',
            // default kosong
            'date_from' => null,
            'date_to'   => null,
            'processed' => null,
            'days'      => [],
        ];
        return view(session()->get('role') . '/Attendance/promote_form', $data);
    }

    /**
     * Proses grouping log -> attendance_days
     */
    public function promoteSubmit()
    {
        $role = session()->get('role');
        $dateFrom = $this->request->getPost('date_from');
        $dateTo   = $this->request->getPost('date_to') ?: $dateFrom;

        if (empty($dateFrom)) {
            return redirect()->back()->with('error', 'Tanggal awal wajib diisi.');
        }

        // 1) Jalankan service grouping: logs → attendance_days
        $groupSvc     = service('attendanceGrouping'); // pastikan sudah di Config\Services
        $daysCount    = $groupSvc->groupLogsToDays($dateFrom, $dateTo);

        // 2) Hitung hasil kerja: attendance_days → attendance_results
        $resultSvc    = new \App\Services\AttendanceResultService();
        $resultsCount = $resultSvc->processRange($dateFrom, $dateTo);

        // Ambil data hasil grouping
        // 3) Ambil data hasil grouping + result untuk ditampilkan
        $dayM = new \App\Models\AttendanceDayModel();
        $days = $dayM
            ->select('
            attendance_days.*,
            e.nik,
            e.employee_name,
            s.shift_name,
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
            ->where('work_date >=', $dateFrom)
            ->where('work_date <=', $dateTo)
            ->orderBy('work_date', 'ASC')
            ->orderBy('e.nik', 'ASC')
            ->findAll();

        $data = [
            'role'        => $role,
            'title'       => 'Promote Form',
            'active1'     => '',
            'active2'     => '',
            'active3'     => 'active',
            'date_from'   => $dateFrom,
            'date_to'     => $dateTo,
            'processed'   => $daysCount,
            'resProcessed' => $resultsCount,
            'days'        => $days,
        ];

        return view($role . '/Attendance/promote_form', $data);
    }
}
