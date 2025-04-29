<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\UserModel;
use App\Models\JobSectionModel;
use App\Models\EmploymentStatusModel;
use App\Models\EmployeeModel;
use App\Models\HistoryEmployeeModel;

class EmployeeController extends BaseController
{
    protected $role;
    protected $userModel;
    protected $jobSectionModel;
    protected $employmentStatusModel;
    protected $employeeModel;
    protected $historyEmployeeModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->jobSectionModel = new JobSectionModel();
        $this->employmentStatusModel = new EmploymentStatusModel();
        $this->employeeModel = new EmployeeModel();
        $this->historyEmployeeModel = new HistoryEmployeeModel();
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
        $sheet->setCellValue('E2', '2');
        $sheet->setCellValue('F2', '2');
        $sheet->setCellValue('G2', 'PINK');
        $sheet->setCellValue('H2', 'STAFF');
        $sheet->setCellValue('I2', '2001/09/12');
        $sheet->setCellValue('J2', '2024/09/12');
        $sheet->setCellValue('K2', 'KNITTER');
        $sheet->setCellValue('L2', 'KK1');
        $sheet->setCellValue('M2', 'KK1A');
        $sheet->setCellValue('N2', 'Aktif');

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
        // dd ($file);
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $fileType = $file->getClientMimeType();
            if (!in_array($fileType, ['application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'])) {
                return redirect()->to(base_url('Monitoring/datakaryawan'))->with('error', 'Invalid file type. Please upload an Excel file.');
            }

            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getTempName());
            $dataSheet = $spreadsheet->getActiveSheet();
            $startRow = 2;

            $karyawanModel = new \App\Models\KaryawanModel();
            $bagianModel = new \App\Models\BagianModel();

            $successCount = 0;
            $errorCount = 0;
            $errorMessages = [];

            for ($row = $startRow; $row <= $dataSheet->getHighestRow(); $row++) {
                // Deklarasi ulang $isValid pada setiap iterasi
                $isValid = true;
                $errorMessage = "Row {$row}: ";
                $kodeKartu = $dataSheet->getCell('A' . $row)->getValue();
                $namaKaryawan = $dataSheet->getCell('B' . $row)->getValue();
                $shift = $dataSheet->getCell('C' . $row)->getValue();
                $jenisKelamin = $dataSheet->getCell('D' . $row)->getValue();
                $libur = $dataSheet->getCell('E' . $row)->getValue();
                $liburTambahan = $dataSheet->getCell('F' . $row)->getValue();
                $warnaBaju = $dataSheet->getCell('G' . $row)->getValue();
                $statusBaju = $dataSheet->getCell('H' . $row)->getValue();
                $tanggalLahir = $dataSheet->getCell('I' . $row)->getFormattedValue();
                $tanggalMasuk = $dataSheet->getCell('J' . $row)->getFormattedValue();
                $namaBagian = $dataSheet->getCell('K' . $row)->getValue();
                $areaUtama = $dataSheet->getCell('L' . $row)->getValue();
                $area = $dataSheet->getCell('M' . $row)->getValue();
                $statusAktif = $dataSheet->getCell('N' . $row)->getValue();
                // dd($statusAktif);
                // dd($namaBagian, $areaUtama, $area);
                // dd($kodeKartu, $namaKaryawan, $shift, $jenisKelamin, $libur, $liburTambahan, $warnaBaju, $statusBaju, $tanggalLahir, $tanggalMasuk, $namaBagian, $areaUtama, $area, $statusAktif);
                // Validasi kode kartu
                if (empty($kodeKartu)) {
                    $isValid = false;
                    $errorMessage .= "Kode Kartu harus diisi. ";
                } else {
                    $karyawan = $karyawanModel->where('kode_kartu', $kodeKartu)->first();
                    if ($karyawan) {
                        $isValid = false;
                        $errorMessage .= "Kode Kartu sudah ada. ";
                    }
                }

                // Validasi nama karyawan
                // if (empty($namaKaryawan)) {
                //     $isValid = false;
                //     $errorMessage .= "Nama Karyawan harus diisi. ";
                // } else {
                //     $karyawan = $karyawanModel->where('nama_karyawan', $namaKaryawan)->where('kode_kartu', $kodeKartu)->first();
                //     if ($karyawan) {
                //         $isValid = false;
                //         $errorMessage .= "Nama Karyawan dan Kode Kartu sudah ada. ";
                //     }
                // }

                // Validasi shift
                if (empty($shift)) {
                    $shift = '-';
                }
                // validasi libur
                if (empty($libur)) {
                    $libur = '-';
                }
                // Validasi tangal lahir
                if (empty($tanggalLahir)) {
                    $isValid = false;
                    $errorMessage .= "Tanggal Lahir harus diisi. ";
                } else {
                    $tanggalLahir = date_create_from_format('m/d/Y', $tanggalLahir);
                    // dd ($tanggalLahir);
                    if (!$tanggalLahir) {
                        $isValid = false;
                        $errorMessage .= "Format Tanggal Lahir salah. ";
                    }
                }

                // Validasi tanggal masuk
                if (empty($tanggalMasuk)) {
                    $isValid = false;
                    $errorMessage .= "Tanggal Masuk harus diisi. ";
                } else {
                    $tanggalMasuk = date_create_from_format('m/d/Y', $tanggalMasuk);
                    // dd($tanggalMasuk);
                    if (!$tanggalMasuk) {
                        $isValid = false;
                        $errorMessage .= "Format Tanggal Masuk salah. ";
                    }
                }

                // validasi nama bagian ketika area utama dan area cocok dengan table bagian maka data di save
                if (empty($namaBagian)) {
                    $isValid = false;
                    $errorMessage .= "Nama Bagian harus diisi. ";
                } else {
                    if ($area == '') {
                        $area = null;
                    }
                    $bagian = $bagianModel->where('nama_bagian', $namaBagian)->where('area_utama', $areaUtama)->where('area', $area)->first();
                    // var_dump($bagian, $namaBagian, $areaUtama, $area);
                    // dd($bagian, $namaBagian, $areaUtama, $area);
                    if (!$bagian) {
                        $isValid = false;
                        $errorMessage .= "Nama Bagian tidak ditemukan. ";
                    }
                }

                // status aktif
                if (empty($statusAktif)) {
                    continue;
                }

                // dd ($isValid);
                // kalau ada kartu yang sama maka tidak akan di save
                if ($isValid) {
                    $data = [
                        'kode_kartu' => $kodeKartu,
                        'nama_karyawan' => $namaKaryawan,
                        'shift' => $shift,
                        'jenis_kelamin' => $jenisKelamin,
                        'libur' => $libur,
                        'libur_tambahan' => $liburTambahan,
                        'warna_baju' => $warnaBaju,
                        'status_baju' => $statusBaju,
                        'tgl_lahir' => $tanggalLahir->format('Y-m-d'),
                        'tgl_masuk' => $tanggalMasuk->format('Y-m-d'),
                        'id_bagian' => $bagian['id_bagian'],
                        'status_aktif' => $statusAktif,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    ];
                    // dd($data);

                    $karyawanModel->insert($data);
                    // dd ($data);
                    $successMessage = "Data karyawan berhasil disimpan.";
                    $successCount++;
                } else {
                    $errorMessages[] = $errorMessage;
                    $errorCount++;
                }
                // dd ($data);
            }
            $role = session()->get('role');


            // Jika ada data yang gagal disimpan
            if ($errorCount > 0) {
                $errorMessages = implode("<br>", $errorMessages);
                if ($role === 'Monitoring') {
                    return redirect()->to(base_url('Monitoring/datakaryawan'))->with('error', "{$errorCount} data gagal disimpan. <br>{$errorMessages}");
                } else {
                    return redirect()->to(base_url('TrainingSchool/datakaryawan'))->with('error', "{$errorCount} data gagal disimpan. <br>{$errorMessages}");
                }
            } else {
                if ($role === 'Monitoring') {
                    return redirect()->to(base_url('Monitoring/datakaryawan'))->with('success', "{$successCount} data berhasil disimpan.");
                } else {
                    return redirect()->to(base_url('TrainingSchool/datakaryawan'))->with('error', "{$errorCount} data gagal disimpan. <br>{$errorMessages}");
                }
            }
        } else {
            return redirect()->to(base_url('Monitoring/datakaryawan'))->with('error', 'Invalid file.');
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
            return redirect()->to(base_url($this->role.'/datakaryawan'))->with('success', 'Employee data successfully saved.');
        } else {
            return redirect()->to(base_url($this->role .'/datakaryawan'))->with('error', 'Failed to save employee data.');
        }
        
    }


    public function update($id)
    {
        $kodeKartu = $this->request->getPost('kode_kartu');
        $namaKaryawan = $this->request->getPost('nama_karyawan');
        $shift = $this->request->getPost('shift');
        $jenisKelamin = $this->request->getPost('jenis_kelamin');
        $libur = $this->request->getPost('libur');
        $liburTambahan = $this->request->getPost('libur_tambahan');
        $warnaBaju = $this->request->getPost('warna_baju');
        $tanggalLahir = $this->request->getPost('tgl_lahir');
        $tanggalMasuk = $this->request->getPost('tgl_masuk');
        $bagian = $this->request->getPost('bagian');
        $area = $this->request->getPost('area');
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
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];
        // dd($data, $this->request->getPost());
        // $this->employeeModel->update($id, $data);


        // $oldBagian = $this->request->getPost('id_bagian_asal');
        // $tgl_pindah = $this->request->getPost('tgl_pindah');
        // $keterangan = $this->request->getPost('keterangan');
        // $item = [
        //     'id_karyawan' => $id,
        //     'id_bagian_asal' => $oldBagian,
        //     'id_bagian_baru' => $bagian['id_bagian'],
        //     'tgl_pindah' => $tgl_pindah,
        //     'keterangan' => $keterangan,
        //     'created_at' => date('Y-m-d H:i:s'),
        //     'updated_by' => session()->get('id_user')
        // ];
        // // dd ($oldBagian, $tgl_pindah, $keterangan, $item);
        // if (!empty($oldBagian) && !empty($tgl_pindah) && !empty($keterangan)) {
        //     $this->historyPindahKaryawanModel->insert($item);
        // } else {
        //     return redirect()->to(base_url('TrainingSchool/datakaryawan'))->with('error', 'Data karyawan gagal diubah.');
        // }

        if ($this->employeeModel->update($id, $data)) {
            return redirect()->to(base_url($this->role . '/datakaryawan'))->with('success', 'Employee data successfully saved.');
        } else {
            return redirect()->to(base_url($this->role . '/datakaryawan'))->with('error', 'Failed to save employee data.');
        }
    }

    public function delete($id)
    {
        if ($this->employeeModel->delete($id)) {
            return redirect()->to(base_url($this->role . '/datakaryawan'))->with('success', 'Data karyawan berhasil dihapus.');
        } else {
            return redirect()->to(base_url($this->role . '/dataKaryawan'))->with('error', 'Data karyawan gagal dihapus.');
        }
    }

    public function exportAll()
    {
        // Ambil data karyawan
        $dataKaryawan = $this->karyawanModel->exportKaryawanAll();
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
            preg_match('/^[A-Z]+/', $a['kode_kartu'], $matchA);
            preg_match('/^[A-Z]+/', $b['kode_kartu'], $matchB);

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
            preg_match('/\d+/', $a['kode_kartu'], $numberA);
            preg_match('/\d+/', $b['kode_kartu'], $numberB);

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
            $sheet->setCellValue('B' . $row, $id['kode_kartu']);
            $sheet->setCellValue('C' . $row, $id['nama_karyawan']);
            $sheet->setCellValue('D' . $row, $id['shift']);
            $sheet->setCellValue('E' . $row, $id['jenis_kelamin']);
            $sheet->setCellValue('F' . $row, $id['libur']);
            $sheet->setCellValue('G' . $row, $id['libur_tambahan']);
            $sheet->setCellValue('H' . $row, $id['warna_baju']);
            $sheet->setCellValue('I' . $row, $id['status_baju']);
            $sheet->setCellValue('J' . $row, $id['tgl_lahir']);
            $sheet->setCellValue('K' . $row, $id['tgl_masuk']);
            $sheet->setCellValue('L' . $row, $id['nama_bagian']);
            $sheet->setCellValue('M' . $row, $id['area_utama']);
            $sheet->setCellValue('N' . $row, $id['area']);
            $sheet->setCellValue('O' . $row, $id['status_aktif']);
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
        $dataKaryawan = $this->karyawanModel->getKaryawanByArea($area);
        // dd($dataKaryawan);
        // Urutkan data karyawan dengan `usort`
        usort($dataKaryawan, function ($a, $b) use ($sort) {
            // Ekstrak prefix kode kartu
            preg_match('/^[A-Z]+/', $a['kode_kartu'], $matchA);
            preg_match('/^[A-Z]+/', $b['kode_kartu'], $matchB);

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
            preg_match('/\d+/', $a['kode_kartu'], $numberA);
            preg_match('/\d+/', $b['kode_kartu'], $numberB);

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
            $sheet->setCellValue('B' . $row, $id['kode_kartu']);
            $sheet->setCellValue('C' . $row, $id['nama_karyawan']);
            $sheet->setCellValue('D' . $row, $id['shift']);
            $sheet->setCellValue('E' . $row, $id['jenis_kelamin']);
            $sheet->setCellValue('F' . $row, $id['libur']);
            $sheet->setCellValue('G' . $row, $id['libur_tambahan']);
            $sheet->setCellValue('H' . $row, $id['warna_baju']);
            $sheet->setCellValue('I' . $row, $id['status_baju']);
            $sheet->setCellValue('J' . $row, $id['tgl_lahir']);
            $sheet->setCellValue('K' . $row, $id['tgl_masuk']);
            $sheet->setCellValue('L' . $row, $id['nama_bagian']);
            $sheet->setCellValue('M' . $row, $id['area_utama']);
            $sheet->setCellValue('N' . $row, $id['area']);
            $sheet->setCellValue('O' . $row, $id['status_aktif']);
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
