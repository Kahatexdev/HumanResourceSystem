<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\UserModel;
use App\Models\JobSectionModel;
use App\Models\EmploymentStatusModel;
use App\Models\EmployeeModel;
use App\Models\HistoryEmployeeModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Models\FactoriesModel;
use App\Models\DayModel;

class EmployeeController extends BaseController
{
    protected $role;
    protected $userModel;
    protected $jobSectionModel;
    protected $employmentStatusModel;
    protected $employeeModel;
    protected $historyEmployeeModel;
    protected $factoryModel;
    protected $dayModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->jobSectionModel = new JobSectionModel();
        $this->employmentStatusModel = new EmploymentStatusModel();
        $this->employeeModel = new EmployeeModel();
        $this->historyEmployeeModel = new HistoryEmployeeModel();
        $this->factoryModel = new FactoriesModel();
        $this->dayModel = new DayModel();

        $this->role = session()->get('role');
    }
    public function index() {}

    public function import()
    {
        $data = [
            'role' => $this->role,
            'title' => 'Karyawan',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => 'active',
            'active5' => '',
            'active6' => ''
        ];
        return view('Karyawan/import', $data);
    }

    public function downloadTemplate()
    {
        // Membuat spreadsheet baru
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Menyusun header kolom
        $sheet->setCellValue('A1', 'Kode Kartu');
        $sheet->setCellValue('B1', 'Nama Karyawan');
        $sheet->setCellValue('C1', 'Shift');
        $sheet->setCellValue('D1', 'Jenis Kelamin');
        $sheet->setCellValue('E1', 'Libur');
        $sheet->setCellValue('F1', 'Libur Tambahan');
        $sheet->setCellValue('G1', 'Warna Baju');
        $sheet->setCellValue('H1', 'Status Baju');
        $sheet->setCellValue('I1', 'Tanggal Lahir');
        $sheet->setCellValue('J1', 'Tanggal Masuk');
        $sheet->setCellValue('K1', 'Nama Bagian');
        $sheet->setCellValue('L1', 'Area Utama');
        $sheet->setCellValue('M1', 'Area');
        $sheet->setCellValue('N1', 'Status Aktif');

        // Mengatur lebar kolom
        $sheet->getColumnDimension('A')->setWidth(20);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(20);
        $sheet->getColumnDimension('E')->setWidth(20);
        $sheet->getColumnDimension('F')->setWidth(20);
        $sheet->getColumnDimension('G')->setWidth(20);
        $sheet->getColumnDimension('H')->setWidth(20);
        $sheet->getColumnDimension('I')->setWidth(20);
        $sheet->getColumnDimension('J')->setWidth(20);
        $sheet->getColumnDimension('K')->setWidth(20);
        $sheet->getColumnDimension('L')->setWidth(20);
        $sheet->getColumnDimension('M')->setWidth(20);
        $sheet->getColumnDimension('N')->setWidth(20);


        // Mengatur style header
        $sheet->getStyle('A1:N1')->getFont()->setBold(true);
        $sheet->getStyle('A1:N1')->getFill()->setFillType('solid')->getStartColor()->setARGB('FFA0A0A0');
        $sheet->getStyle('A1:N1')->getAlignment()->setHorizontal('center');

        // isi data
        $sheet->setCellValue('A2', 'KK001');
        $sheet->setCellValue('B2', 'John Doe');
        $sheet->setCellValue('C2', 'A');
        $sheet->setCellValue('D2', 'L');
        $sheet->setCellValue('E2', 'SENIN');
        $sheet->setCellValue('F2', 'SELASA');
        $sheet->setCellValue('G2', 'PINK');
        $sheet->setCellValue('H2', 'STAFF');
        $sheet->setCellValue('I2', '1999-10-19');
        $sheet->setCellValue('J2', '1999-10-19');
        $sheet->setCellValue('K2', 'KNITTER');
        $sheet->setCellValue('L2', 'KK1');
        $sheet->setCellValue('M2', 'KK1A');
        $sheet->setCellValue('N2', 'Aktif/Tidak Aktif');

        // 
        // Menentukan nama file
        $fileName = 'Template_Data_Karyawan.xlsx';

        // Set header untuk unduhan file
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"{$fileName}\"");
        header('Cache-Control: max-age=0');

        // Buat file Excel dan kirim sebagai unduhan
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public function upload()
    {
        $file = $this->request->getFile('file');
        if (! $file || ! $file->isValid() || $file->hasMoved()) {
            return redirect()->to(base_url($this->role . '/dataKaryawan'))
                ->with('error', 'File tidak valid atau tidak ada file yang diunggah.');
        }

        // Cek MIME type
        $mime = $file->getClientMimeType();
        if (! in_array($mime, [
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        ])) {
            return redirect()->to(base_url($this->role . '/dataKaryawan'))
                ->with('error', 'Invalid file type. Please upload an Excel file.');
        }

        // Load spreadsheet
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getTempName());
        $sheet       = $spreadsheet->getActiveSheet();
        $startRow    = 2;

        $batchData     = [];
        $successCount  = 0;
        $errorCount    = 0;
        $errorMessages = [];

        // Loop tiap baris
        for ($row = $startRow; $row <= $sheet->getHighestRow(); $row++) {
            $isValid     = true;
            $errMsg      = "Row {$row}: ";
            $kodeKartu   = trim((string) $sheet->getCell("A{$row}")->getValue());
            $nama        = trim((string) $sheet->getCell("B{$row}")->getValue());
            $shift       = trim((string) $sheet->getCell("C{$row}")->getValue()) ?: '-';
            $gender      = trim((string) $sheet->getCell("D{$row}")->getValue()) ?: '-';
            $liburName   = trim((string) $sheet->getCell("E{$row}")->getValue());
            $addLiburName = trim((string) $sheet->getCell("F{$row}")->getValue());
            $statusBaju  = trim((string) $sheet->getCell("G{$row}")->getValue());
            $statusAct   = trim((string) $sheet->getCell("N{$row}")->getValue());
            $tglLahirStr = trim((string) $sheet->getCell("I{$row}")->getFormattedValue());
            $tglMasukStr = trim((string) $sheet->getCell("J{$row}")->getFormattedValue());
            $bagianName  = trim((string) $sheet->getCell("K{$row}")->getValue());
            $areaUtama   = trim((string) $sheet->getCell("L{$row}")->getValue());
            $areaName    = trim((string) $sheet->getCell("M{$row}")->getValue());

            // -- Validasi Kode Kartu unik --
            if ($kodeKartu === '') {
                $isValid = false;
                $errMsg .= "Kode Kartu kosong. ";
            } elseif ($this->employeeModel->where('employee_code', $kodeKartu)->first()) {
                $isValid = false;
                $errMsg .= "Kode Kartu '{$kodeKartu}' sudah ada. ";
            }

            // -- Validasi tanggal --
            $tglLahir = $tglLahirStr ? date_create_from_format('Y-m-d', $tglLahirStr) : false;
            $tglMasuk = $tglMasukStr ? date_create_from_format('Y-m-d', $tglMasukStr) : false;
            if (! $tglLahir) {
                $isValid = false;
                $errMsg .= "Format Tanggal Lahir salah. ";
            }
            if (! $tglMasuk) {
                $isValid = false;
                $errMsg .= "Format Tanggal Masuk salah. ";
            }

            // -- Validasi Bagian (job section) --
            $bagian = $this->jobSectionModel
                ->where('job_section_name', $bagianName)
                ->first();
            if (! $bagian) {
                $isValid = false;
                $errMsg .= "Bagian '{$bagianName}' tidak ditemukan. ";
            }

            // -- Validasi Days --
            $libur = $this->dayModel->where('day_name', $liburName)->first();
            if (! $libur) {
                $isValid = false;
                $errMsg .= "Libur '{$liburName}' tidak ditemukan. ";
            }
            $addLibur = $addLiburName
                ? $this->dayModel->where('day_name', $addLiburName)->first()
                : null;
            if ($addLiburName && ! $addLibur) {
                $isValid = false;
                $errMsg .= "Libur Tambahan '{$addLiburName}' tidak ditemukan. ";
            }
            // -- Validasi Status Baju --
            $statusBaju = $this->employmentStatusModel
                ->where('clothes_color', $statusBaju)
                ->first();
            if (! $statusBaju) {
                $isValid = false;
                $errMsg .= "Status Baju '{$statusBaju}' tidak ditemukan. ";
            }

            // -- Validasi Factory --
            $factory = $this->factoryModel->where('factory_name', $areaName)->first();
            if (! $factory) {
                $isValid = false;
                $errMsg .= "Area '{$areaName}' tidak ditemukan. ";
            }

            // -- Status aktif wajib diisi --
            if (! $statusAct) {
                $isValid = false;
                $errMsg .= "Status Aktif harus diisi. ";
            }

            if ($isValid) {
                // Siapkan satu baris data
                $batchData[] = [
                    'employee_code'        => $kodeKartu,
                    'employee_name'        => $nama,
                    'shift'                => $shift,
                    'gender'               => $gender,
                    'holiday'              => $libur['id_day'],
                    'additional_holiday'   => $addLibur['id_day'] ?? null,
                    'id_employment_status' => $statusBaju['id_employment_status'],
                    'date_of_birth'        => $tglLahir->format('Y-m-d'),
                    'date_of_joining'      => $tglMasuk->format('Y-m-d'),
                    'id_job_section'       => $bagian['id_job_section'],
                    'id_factory'           => $factory['id_factory'],
                    'status'               => $statusAct,
                    'created_at'           => date('Y-m-d H:i:s'),
                    'updated_at'           => date('Y-m-d H:i:s'),
                ];
                $successCount++;
            } else {
                $errorCount++;
                $errorMessages[] = $errMsg;
            }
        }

        // Setelah loop: simpan semua yang valid sekaligus
        if (! empty($batchData)) {
            $this->employeeModel->insertBatch($batchData);
        }

        // Bangun flash message
        $role = session()->get('role');
        $redirectUrl = base_url($role . '/dataKaryawan');

        if ($errorCount > 0) {
            $msg = "{$successCount} baris berhasil disimpan, ";
            $msg .= "{$errorCount} baris gagal:<br>" . implode('<br>', $errorMessages);
            return redirect()->to($redirectUrl)->with('error', $msg);
        } else {
            return redirect()->to($redirectUrl)
                ->with('success', "{$successCount} baris berhasil disimpan.");
        }
    }

    public function store()
    {
        $kodeKartu = $this->request->getPost('kode_kartu');
        $namaKaryawan = $this->request->getPost('nama_karyawan');
        $shift = $this->request->getPost('shift');
        $jenisKelamin = $this->request->getPost('jenis_kelamin');
        $libur = $this->request->getPost('libur');
        $liburTambahan = empty($this->request->getPost('libur_tambahan')) ? NULL : $this->request->getPost('libur_tambahan');
        $warnaBaju = $this->request->getPost('warna_baju');
        $tanggalLahir = $this->request->getPost('tgl_lahir');
        $tanggalMasuk = $this->request->getPost('tgl_masuk');
        $bagian = $this->request->getPost('bagian');
        $area = $this->request->getPost('area');
        $statusAktif = $this->request->getPost('status_aktif');
        // dd($bagian);

        $data = [
            'employee_code' => $kodeKartu,
            'employee_name' => $namaKaryawan,
            'shift' => $shift,
            'gender' => $jenisKelamin,
            'holiday' => $libur,
            'additional_holiday' => $liburTambahan,
            'id_employment_status' => $warnaBaju,
            'date_of_birth' => $tanggalLahir,
            'date_of_joining' => $tanggalMasuk,
            'id_job_section' => $bagian,
            'id_factory' => $area,
            'status' => $statusAktif,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        if ($this->employeeModel->insert($data)) {
            return redirect()->to(base_url($this->role. '/dataKaryawan'))->with('success', 'Employee data successfully saved.');
        } else {
            return redirect()->to(base_url($this->role . '/dataKaryawan'))->with('error', 'Failed to save employee data.');
        }
        
    }


    public function update($id)
    {
        // dd($id);
        $kodeKartu = $this->request->getPost('kode_kartu');
        $namaKaryawan = $this->request->getPost('nama_karyawan');
        $shift = $this->request->getPost('shift');
        $jenisKelamin = $this->request->getPost('jenis_kelamin');
        $libur = $this->request->getPost('libur');
        $liburTambahan = $this->request->getPost('libur_tambahan') ?: NULL;
        $warnaBaju = $this->request->getPost('warna_baju');
        $tanggalLahir = $this->request->getPost('tgl_lahir');
        $tanggalMasuk = $this->request->getPost('tgl_masuk');
        $bagian = $this->request->getPost('bagian');
        $area = $this->request->getPost('area');
        $date_of_change = $this->request->getPost('date_of_change');
        $reason = $this->request->getPost('reason');
        $oldBagian = $this->request->getPost('id_job_section_old');
        $oldFactory = $this->request->getPost('id_factory_old');
        $newBagian = $this->request->getPost('id_job_section_new');
        $newFactory = $this->request->getPost('id_factory_new');
        $statusAktif = $this->request->getPost('status_aktif');

        $data = [
            'employee_code' => $kodeKartu,
            'employee_name' => $namaKaryawan,
            'shift' => $shift,
            'gender' => $jenisKelamin,
            'holiday' => $libur,
            'additional_holiday' => $liburTambahan,
            'id_employment_status' => $warnaBaju,
            'date_of_birth' => $tanggalLahir,
            'date_of_joining' => $tanggalMasuk,
            'id_job_section' => $bagian,
            'id_factory' => $area,
            'status' => $statusAktif,
        ];
        // dd ($id, $data);
        // dd($data, $this->request->getPost());
        $update = $this->employeeModel->update($id, $data);

        $oldBagian = $this->request->getPost('id_job_section_old');
        $tgl_pindah = $this->request->getPost('date_of_change');
        $keterangan = $this->request->getPost('reason');
        $item = [
            'id_employee' => $id,
            'id_job_section_old' => $oldBagian,
            'id_job_section_new' => $newBagian,
            'id_factory_old' => $oldFactory,
            'id_factory_new' => $newFactory,
            'date_of_change' => $tgl_pindah,
            'reason' => $keterangan,
            'id_user' => session()->get('id_user')
        ];
        // dd ($oldBagian, $tgl_pindah, $keterangan, $item);
        if (!empty($oldBagian) && !empty($tgl_pindah) && !empty($keterangan)) {
            $this->historyEmployeeModel->insert($item);
        }

        if ($update) {
            return redirect()->to(base_url($this->role . '/dataKaryawan'))->with('success', 'Employee data successfully Updated.');
        } else {
            return redirect()->to(base_url($this->role . '/dataKaryawan'))->with('error', 'Failed to save employee data.');
        }
    }

    public function delete($id)
    {
        if ($this->employeeModel->delete($id)) {
            return redirect()->to(base_url($this->role . '/dataKaryawan'))->with('success', 'Data karyawan berhasil dihapus.');
        } else {
            return redirect()->to(base_url($this->role . '/dataKaryawan'))->with('error', 'Data karyawan gagal dihapus.');
        }
    }

    public function exportAll()
    {
        // Ambil data karyawan
        $dataKaryawan = $this->employeeModel->getEmployeeData();
        // Definisikan urutan kode kartu berdasarkan area
        // Tentukan urutan prefix kode kartu secara global
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
            'KK11NS',
        ];
        // Urutkan data karyawan berdasarkan kode kartu
        usort($dataKaryawan, function ($a, $b) use ($sortOrders) {
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
        // dd($dataKaryawan);
        // Buat spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Style
        $styleTitle = [
            'font' => [
                'size' => 12,
                'bold' => true,
                'name' => 'Arial',
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER, // Alignment rata tengah
                'vertical' => Alignment::VERTICAL_CENTER, // Alignment rata tengah
            ],
        ];
        $styleHeader = [
            'font' => [
                'size' => 10,
                'bold' => true,
                'name' => 'Arial',
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER, // Alignment rata tengah
                'vertical' => Alignment::VERTICAL_CENTER, // Alignment rata tengah
            ],
            'borders' => [
                'outline' => [
                    'borderStyle' => Border::BORDER_THIN, // Gaya garis tipis
                    'color' => ['argb' => 'FF000000'],    // Warna garis hitam
                ],
            ],
        ];

        $styleAlignCenter = [
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ];

        $sheet->setCellValue('A1', 'DATA KARYAWAN');
        $sheet->mergeCells('A1:K1')->getStyle('A1:K1')->applyFromArray($styleTitle);

        $sheet->setCellValue('A3', 'No');
        $sheet->getStyle('A3')->applyFromArray($styleHeader);

        $sheet->setCellValue('B3', 'Kode Kartu');
        $sheet->getStyle('B3')->applyFromArray($styleHeader);

        $sheet->setCellValue('C3', 'Nama Karyawan');
        $sheet->getStyle('C3')->applyFromArray($styleHeader);

        $sheet->setCellValue('D3', 'Shift');
        $sheet->getStyle('D3')->applyFromArray($styleHeader);

        $sheet->setCellValue('E3', 'Jenis Kelamin');
        $sheet->getStyle('E3')->applyFromArray($styleHeader);

        $sheet->setCellValue('F3', 'Libur');
        $sheet->getStyle('F3')->applyFromArray($styleHeader);

        $sheet->setCellValue('G3', 'Libur Tambahan');
        $sheet->getStyle('G3')->applyFromArray($styleHeader);

        $sheet->setCellValue('H3', 'Warna Baju');
        $sheet->getStyle('H3')->applyFromArray($styleHeader);

        $sheet->setCellValue('I3', 'Status Baju');
        $sheet->getStyle('I3')->applyFromArray($styleHeader);

        $sheet->setCellValue('J3', 'Tanggal Lahir');
        $sheet->getStyle('J3')->applyFromArray($styleHeader);

        $sheet->setCellValue('K3', 'Tanggal Masuk');
        $sheet->getStyle('K3')->applyFromArray($styleHeader);

        $sheet->setCellValue('L3', 'Nama Bagian');
        $sheet->getStyle('L3')->applyFromArray($styleHeader);

        $sheet->setCellValue('M3', 'Area Utama');
        $sheet->getStyle('M3')->applyFromArray($styleHeader);

        $sheet->setCellValue('N3', 'Area');
        $sheet->getStyle('N3')->applyFromArray($styleHeader);

        $sheet->setCellValue('O3', 'Status Aktif');
        $sheet->getStyle('O3')->applyFromArray($styleHeader);

        $styleData = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
        ];

        $row = 4;
        $no = 1;
        foreach ($dataKaryawan as $key => $id) {
            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $id['employee_code']);
            $sheet->setCellValue('C' . $row, $id['employee_name']);
            $sheet->setCellValue('D' . $row, $id['shift']);
            $sheet->setCellValue('E' . $row, $id['gender']);
            $sheet->setCellValue('F' . $row, $id['holiday_name']);
            $sheet->setCellValue('G' . $row, $id['additional_holiday_name']);
            $sheet->setCellValue('H' . $row, $id['clothes_color']);
            $sheet->setCellValue('I' . $row, $id['employment_status_name']);
            $sheet->setCellValue('J' . $row, $id['date_of_birth']);
            $sheet->setCellValue('K' . $row, $id['date_of_joining']);
            $sheet->setCellValue('L' . $row, $id['job_section_name']);
            $sheet->setCellValue('M' . $row, $id['main_factory']);
            $sheet->setCellValue('N' . $row, $id['factory_name']);
            $sheet->setCellValue('O' . $row, $id['status']);
            $row++;
        }

        // Terapkan gaya border ke seluruh data
        $dataRange = 'A4:O' . ($row - 1); // Dari baris 4 sampai baris terakhir
        $sheet->getStyle($dataRange)->applyFromArray($styleData);

        // Terapkan alignment rata-tengah ke seluruh data
        $sheet->getStyle($dataRange)->applyFromArray($styleAlignCenter);

        // Autosize untuk setiap kolom
        foreach (range('A', 'O') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        // Set judul file dan header untuk download
        $filename = 'Data Karyawan ' . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        // Tulis file excel ke output
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public function exportPerArea($area)
    {
        if($area === 'ALL') {
            return redirect()->to(base_url($this->role . '/exportKaryawan'));
        }

        // Definisikan urutan kode kartu berdasarkan area
        $sortOrders = [
            'KK1A' => ['KKMA', 'KKMB', 'KKMC', 'KKMNS', 'KKSA', 'KKSB', 'KKSC', 'KKJHA', 'KKJHB', 'KKJHC'],
            'KK1B' => ['KKMA', 'KKMB', 'KKMC', 'KKMNS', 'KKSA', 'KKSB', 'KKSC', 'KKJHA', 'KKJHB', 'KKJHC'],
            'KK2A' => ['KK2MA', 'KK2MB', 'KK2MC', 'KK2MNS', 'KK2SA', 'KK2SB', 'KK2SC'],
            'KK2B' => ['KK2MA', 'KK2MB', 'KK2MC', 'KK2MNS', 'KK2SA', 'KK2SB', 'KK2SC'],
            'KK5'  => ['KK5A', 'KK5B', 'KK5C', 'KK5NS'],
            'KK7K' => ['KK7A', 'KK7B', 'KK7C', 'KK7NS'],
            'KK7L' => ['KK7A', 'KK7B', 'KK7C', 'KK7NS'],
            'KK8D' => ['KK8MA', 'KK8MB', 'KK8MC', 'KK8MNS', 'KK8SA', 'KK8SB', 'KK8SC'],
            'KK8F' => ['KK8MA', 'KK8MB', 'KK8MC', 'KK8MNS', 'KK8SA', 'KK8SB', 'KK8SC'],
            'KK8J' => ['KK8MA', 'KK8MB', 'KK8MC', 'KK8MNS', 'KK8SA', 'KK8SB', 'KK8SC'],
            'KK9'  => ['KK9A', 'KK9B', 'KK9C', 'KK9NS'],
            'KK10'  => ['KK10A', 'KK10B', 'KK10C', 'KK10NS'],
            'KK11'  => ['KK11A', 'KK11B', 'KK11C', 'KK11NS']
        ];

        // Ambil urutan sort berdasarkan area
        $sort = $sortOrders[$area] ?? []; // Default kosong jika area tidak ditemukan
        // dd($sort);
        // Ambil data karyawan
        $dataKaryawan = $this->employeeModel->getKaryawanByArea($area);
        // dd($dataKaryawan);
        // Urutkan data karyawan dengan `usort`
        usort($dataKaryawan, function ($a, $b) use ($sort) {
            // Ekstrak prefix kode kartu
            preg_match('/^[A-Z]+/', $a['employee_code'], $matchA);
            preg_match('/^[A-Z]+/', $b['employee_code'], $matchB);

            $prefixA = $matchA[0] ?? '';
            $prefixB = $matchB[0] ?? '';

            // Cari posisi prefix di array $sort
            $posA = array_search($prefixA, $sort);
            $posB = array_search($prefixB, $sort);

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
        // dd($dataKaryawan);
        // Buat spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Style
        $styleTitle = [
            'font' => [
                'size' => 12,
                'bold' => true,
                'name' => 'Arial',
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER, // Alignment rata tengah
                'vertical' => Alignment::VERTICAL_CENTER, // Alignment rata tengah
            ],
        ];
        $styleHeader = [
            'font' => [
                'size' => 10,
                'bold' => true,
                'name' => 'Arial',
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER, // Alignment rata tengah
                'vertical' => Alignment::VERTICAL_CENTER, // Alignment rata tengah
            ],
            'borders' => [
                'outline' => [
                    'borderStyle' => Border::BORDER_THIN, // Gaya garis tipis
                    'color' => ['argb' => 'FF000000'],    // Warna garis hitam
                ],
            ],
        ];

        $styleAlignCenter = [
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ];

        $sheet->setCellValue('A1', 'DATA KARYAWAN');
        $sheet->mergeCells('A1:K1')->getStyle('A1:K1')->applyFromArray($styleTitle);

        $sheet->setCellValue('A3', 'No');
        $sheet->getStyle('A3')->applyFromArray($styleHeader);

        $sheet->setCellValue('B3', 'Kode Kartu');
        $sheet->getStyle('B3')->applyFromArray($styleHeader);

        $sheet->setCellValue('C3', 'Nama Karyawan');
        $sheet->getStyle('C3')->applyFromArray($styleHeader);

        $sheet->setCellValue('D3', 'Shift');
        $sheet->getStyle('D3')->applyFromArray($styleHeader);

        $sheet->setCellValue('E3', 'Jenis Kelamin');
        $sheet->getStyle('E3')->applyFromArray($styleHeader);

        $sheet->setCellValue('F3', 'Libur');
        $sheet->getStyle('F3')->applyFromArray($styleHeader);

        $sheet->setCellValue('G3', 'Libur Tambahan');
        $sheet->getStyle('G3')->applyFromArray($styleHeader);

        $sheet->setCellValue('H3', 'Warna Baju');
        $sheet->getStyle('H3')->applyFromArray($styleHeader);

        $sheet->setCellValue('I3', 'Status Baju');
        $sheet->getStyle('I3')->applyFromArray($styleHeader);

        $sheet->setCellValue('J3', 'Tanggal Lahir');
        $sheet->getStyle('J3')->applyFromArray($styleHeader);

        $sheet->setCellValue('K3', 'Tanggal Masuk');
        $sheet->getStyle('K3')->applyFromArray($styleHeader);

        $sheet->setCellValue('L3', 'Nama Bagian');
        $sheet->getStyle('L3')->applyFromArray($styleHeader);

        $sheet->setCellValue('M3', 'Area Utama');
        $sheet->getStyle('M3')->applyFromArray($styleHeader);

        $sheet->setCellValue('N3', 'Area');
        $sheet->getStyle('N3')->applyFromArray($styleHeader);

        $sheet->setCellValue('O3', 'Status Aktif');
        $sheet->getStyle('O3')->applyFromArray($styleHeader);

        $styleData = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
        ];

        $row = 4;
        $no = 1;
        foreach ($dataKaryawan as $key => $id) {
            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $id['employee_code']);
            $sheet->setCellValue('C' . $row, $id['employee_name']);
            $sheet->setCellValue('D' . $row, $id['shift']);
            $sheet->setCellValue('E' . $row, $id['gender']);
            $sheet->setCellValue('F' . $row, $id['holiday_name']);
            $sheet->setCellValue('G' . $row, $id['additional_holiday_name']);
            $sheet->setCellValue('H' . $row, $id['clothes_color']);
            $sheet->setCellValue('I' . $row, $id['employment_status_name']);
            $sheet->setCellValue('J' . $row, $id['date_of_birth']);
            $sheet->setCellValue('K' . $row, $id['date_of_joining']);
            $sheet->setCellValue('L' . $row, $id['job_section_name']);
            $sheet->setCellValue('M' . $row, $id['main_factory']);
            $sheet->setCellValue('N' . $row, $id['factory_name']);
            $sheet->setCellValue('O' . $row, $id['status']);
            $row++;
        }

        // Terapkan gaya border ke seluruh data
        $dataRange = 'A4:O' . ($row - 1); // Dari baris 4 sampai baris terakhir
        $sheet->getStyle($dataRange)->applyFromArray($styleData);

        // Terapkan alignment rata-tengah ke seluruh data
        $sheet->getStyle($dataRange)->applyFromArray($styleAlignCenter);

        // Autosize untuk setiap kolom
        foreach (range('A', 'O') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        // Set judul file dan header untuk download
        $filename = 'Data Karyawan ' . $area . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        // Tulis file excel ke output
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }  //
}
