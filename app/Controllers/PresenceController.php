<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\PresenceModel;
use App\Models\EmployeeModel;
use App\Models\UserModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Border;
use DateTime;

class PresenceController extends BaseController
{
    protected $role;
    protected $presenceModel;
    protected $employeeModel;
    protected $userModel;
    public function __construct()
    {
        $this->role = session()->get('role');
        $this->presenceModel = new PresenceModel();
        $this->employeeModel = new EmployeeModel();
        $this->presenceModel = new PresenceModel();
    }
    public function index() {}

    public function store()
    {
        $data = [
            'id_employee' => $this->request->getPost('id_karyawan'),
            'id_periode' => $this->request->getPost('id_periode'),
            'permit' => $this->request->getPost('izin'),
            'sick' => $this->request->getPost('sakit'),
            'absent' => $this->request->getPost('mangkir'),
            'leave' => $this->request->getPost('cuti'),
            'id_user' => $this->request->getPost('id_user')
        ];
        // dd ($data);
        if ($this->presenceModel->insert($data)) {
            session()->setFlashdata('success', 'Data berhasil ditambahkan');
        } else {
            session()->setFlashdata('error', 'Data gagal ditambahkan');
        }

        return redirect()->to(base_url($this->role . '/dataAbsen'));
    }

    public function update($id)
    {
        $data = [
            'id_employee' => $this->request->getPost('id_karyawan'),
            'id_periode' => $this->request->getPost('id_periode'),
            'permit' => $this->request->getPost('izin'),
            'sick' => $this->request->getPost('sakit'),
            'absent' => $this->request->getPost('mangkir'),
            'leave' => $this->request->getPost('cuti'),
            'id_user' => $this->request->getPost('id_user')
        ];

        $id_karyawan = $this->presenceModel->where('id_employee', $this->request->getPost('id_karyawan'))
            ->where('id_periode', $this->request->getPost('id_periode'))
            ->first();
        // dd ($id_karyawan);
        // validasi id_karyawan masuk karyawan

        if ($this->presenceModel->update($id, $data)) {
            session()->setFlashdata('success', 'Data berhasil diubah');
        } else {
            session()->setFlashdata('error', 'Data gagal diubah');
        }
        return redirect()->to(base_url($this->role . '/dataAbsen'));
    }

    public function delete($id)
    {
        if ($this->presenceModel->delete($id)) {
            session()->setFlashdata('success', 'Data berhasil dihapus');
        } else {
            session()->setFlashdata('error', 'Data gagal dihapus');
        }

        return redirect()->to(base_url($this->role . '/dataAbsen'));
    }

    public function import()
    {
        $data = [
            'role' => session()->get('role'),
            'title' => 'Absen',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => 'active',
            'active6' => ''
        ];
        return view('absen/import', $data);
    }

    public function downloadTemplate()
    {
        // Membuat spreadsheet baru
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Menyusun header kolom
        $sheet->setCellValue('A1', 'Nama Karyawan');
        $sheet->setCellValue('B1', 'Bulan');
        $sheet->setCellValue('C1', 'Keterangan Absen');
        $sheet->setCellValue('D1', 'Nama User');

        // Mengatur lebar kolom
        $sheet->getColumnDimension('A')->setWidth(20);
        $sheet->getColumnDimension('B')->setWidth(30);
        $sheet->getColumnDimension('C')->setWidth(15);
        $sheet->getColumnDimension('D')->setWidth(15);


        // Mengatur style header
        $sheet->getStyle('A1:D1')->getFont()->setBold(true);
        $sheet->getStyle('A1:D1')->getFill()->setFillType('solid')->getStartColor()->setARGB('FFA0A0A0');
        $sheet->getStyle('A1:D1')->getAlignment()->setHorizontal('center');

        // isi data
        $sheet->setCellValue('A2', 'Budi');
        $sheet->setCellValue('B2', '2021-01-01');
        $sheet->setCellValue('C2', 'Hadir');
        $sheet->setCellValue('D2', session()->get('username'));


        // Menentukan nama file
        $fileName = 'Template_Data_Absen.xlsx';

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

        // Check if the file is valid and has not been moved
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $fileType = $file->getClientMimeType();

            // Validate the file type
            if (!in_array($fileType, ['application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'])) {
                return redirect()->to(base_url('Monitoring/karyawanImport'))->with('error', 'Invalid file type. Please upload an Excel file.');
            }

            // Load the spreadsheet
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getTempName());
            $dataSheet = $spreadsheet->getActiveSheet();
            $startRow = 3; // Assuming the first row is for headers

            // Models
            $absenModel = new \App\Models\PresenceModel();
            $karyawanModel = new \App\Models\EmployeeModel();

            // Counters for success and errors
            $successCount = 0;
            $errorCount = 0;
            $errorMessages = [];

            // Iterate through each row of the spreadsheet
            for ($row = $startRow; $row <= $dataSheet->getHighestRow(); $row++) {
                $isValid = true;
                $errorMessage = "Row {$row}: ";

                // Get cell values
                $kodeKartu = $dataSheet->getCell('A' . $row)->getValue();
                $namaKaryawan = $dataSheet->getCell('D' . $row)->getValue();
                $sakit = $dataSheet->getCell('I' . $row)->getValue();
                $izin = $dataSheet->getCell('J' . $row)->getValue();
                $cuti = $dataSheet->getCell('K' . $row)->getValue();
                $mangkir = $dataSheet->getCell('L' . $row)->getValue();
                $idUser = session()->get('id_user');


                // dd ($kodeKartu, $namaKaryawan, $id_periode, $sakit, $izin, $cuti, $mangkir, $idUser);
                // Validate data
                // Validasi tangal 
                $id_periode = $this->request->getPost('id_periode');
                if (empty($id_periode)) {
                    $isValid = false;
                    $errorMessage .= "Periode is required. ";
                }
                if (empty($namaKaryawan)) {
                    $isValid = false;
                    $errorMessage .= "Nama Karyawan is required. ";
                }
                if (empty($kodeKartu)) {
                    $isValid = false;
                    $errorMessage .= "Kode Kartu is required. ";
                }

                // If valid, proceed to save
                if ($isValid) {
                    // Fetch the karyawan data
                    $karyawan = $karyawanModel->where('employee_name', $namaKaryawan)->first();
                    if ($karyawan) {
                        // Prepare the data for saving
                        $data = [
                            'id_employee' => $karyawan['id_employee'],
                            'id_periode' => $id_periode,
                            'sick' => $sakit,
                            'permit' => $izin,
                            'leave' => $cuti,
                            'absent' => $mangkir,
                            'id_user' => $idUser
                        ];

                        // kalau ada data karyawan dan tanggal absen sama maka tidak bisa diinputkan
                        $absen = $absenModel->where('id_employee', $karyawan['id_employee'])
                            ->where('id_periode', $id_periode)
                            ->first();
                        if ($absen) {
                            $isValid = false;
                            $errorMessage .= "Data absen sudah ada. ";
                        } else {
                            // Save the data
                            $absenModel->insert($data);
                            $successCount++;
                        }
                    } else {
                        $isValid = false;
                        $errorMessage .= "Karyawan tidak ditemukan. ";
                    }
                }

                // If invalid, add to error count and log the error message
                if (!$isValid) {
                    $errorCount++;
                    $errorMessages[] = $errorMessage;
                }
            }

            // Redirect with success and error messages
            $message = "Data absen berhasil diupload. Total data: {$successCount}.";
            if ($errorCount > 0) {
                $message .= " Terdapat {$errorCount} data yang gagal diupload.";
                $message .= "<ul>";
                foreach ($errorMessages as $msg) {
                    $message .= "<li>{$msg}</li>";
                }
                $message .= "</ul>";
            }
            return redirect()->to(base_url('Monitoring/dataAbsen'))->with('success', $message);
        } else {
            return redirect()->to(base_url('Monitoring/dataAbsen'))->with('error', 'Invalid file. Please upload an Excel file.');
        }
    }

    public function absenReport()
    {
        $data = $this->presenceModel->getDataPresence();
        // export data ke excel
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // header
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Kode Kartu');
        $sheet->setCellValue('C1', 'Nama Karyawan');
        $sheet->setCellValue('D1', 'Periode');
        $sheet->setCellValue('E1', 'Izin');
        $sheet->setCellValue('F1', 'Sakit');
        $sheet->setCellValue('G1', 'Mangkir');
        $sheet->setCellValue('H1', 'Cuti');
        $sheet->setCellValue('I1', 'Input By');
        $sheet->setCellValue('J1', 'Created At');
        $sheet->setCellValue('K1', 'Updated At');

        $no = 1;
        $column = 2;

        // style kolom manual
        $sheet->getColumnDimension('A')->setWidth(5);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(20);
        $sheet->getColumnDimension('E')->setWidth(10);
        $sheet->getColumnDimension('F')->setWidth(10);
        $sheet->getColumnDimension('G')->setWidth(10);
        $sheet->getColumnDimension('H')->setWidth(10);
        $sheet->getColumnDimension('I')->setWidth(20);
        $sheet->getColumnDimension('J')->setWidth(20);
        $sheet->getColumnDimension('K')->setWidth(20);

        $sheet->getStyle('A1:K1')->getFont()->setBold(true);
        $sheet->getStyle('A1:K1')->getFill()->setFillType('solid')->getStartColor()->setARGB('FFA0A0A0');
        $sheet->getStyle('A1:K1')->getAlignment()->setHorizontal('center');
        foreach ($data as $row) {
            $sheet->setCellValue('A' . $column, $no++);
            $sheet->setCellValue('B' . $column, $row['employee_code'] ?? '');
            $sheet->setCellValue('C' . $column, $row['employee_name']);
            $sheet->setCellValue('D' . $column, $row['periode_name']);
            $sheet->setCellValue('E' . $column, $row['permit']);
            $sheet->setCellValue('F' . $column, $row['sick']);
            $sheet->setCellValue('G' . $column, $row['absent']);
            $sheet->setCellValue('H' . $column, $row['leave']);
            $sheet->setCellValue('I' . $column, $row['username']);
            $sheet->setCellValue('J' . $column, $row['created_at']);
            $sheet->setCellValue('K' . $column, $row['updated_at']);

            $column++;
        }

        // Set the header
        $fileName = 'Data_Absen.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"{$fileName}\"");
        header('Cache-Control: max-age=0');

        // Save the file
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public function empty()
    {
        $absen = new AbsenModel();

        if ($absen->truncate()) {
            session()->setFlashdata('success', 'Data berhasil dihapus');
        } else {
            session()->setFlashdata('error', 'Data gagal dihapus');
        }

        return redirect()->to('/Monitoring/dataAbsen');
    }

    // Bekas pindahin data absen dari db skillmapping
    public function importAbsenSkillMap()
    {
        $file = $this->request->getFile('file');

        if (! $file->isValid() || $file->getExtension() !== 'xlsx') {
            return redirect()->back()->with('error', 'File tidak valid atau bukan Excel.');
        }

        $spreadsheet   = IOFactory::load($file->getTempName());
        $sheet         = $spreadsheet->getActiveSheet();
        $highestRow    = $sheet->getHighestRow();

        $successCount  = 0;
        $skipCount     = 0;
        $skipDetails   = [];

        for ($row = 2; $row <= $highestRow; $row++) {
            $kodeKartu    = trim($sheet->getCell('A' . $row)->getValue());
            $namaKaryawan = trim($sheet->getCell('B' . $row)->getValue());

            // Cari id_employee (case-insensitive & trim)
            $karyawan = $this->employeeModel
                ->where('TRIM(LOWER(employee_code))', strtolower($kodeKartu))
                ->where('TRIM(LOWER(employee_name))', strtolower($namaKaryawan))
                ->first();

            if (! $karyawan) {
                ++$skipCount;
                $skipDetails[] = [
                    'row'  => $row,
                    'code' => $kodeKartu,
                    'name' => $namaKaryawan,
                    'reason' => 'Employee not found',
                ];
                continue;
            }

            // Siapkan data
            $data = [
                'id_employee' => $karyawan['id_employee'],
                'id_periode'  => $sheet->getCell('C' . $row)->getValue(),
                'permit'      => $sheet->getCell('D' . $row)->getValue(),
                'sick'        => $sheet->getCell('E' . $row)->getValue(),
                'absent'      => $sheet->getCell('F' . $row)->getValue(),
                'leave'       => $sheet->getCell('G' . $row)->getValue(),
                'id_user'     => $sheet->getCell('H' . $row)->getValue(),
                'created_at'  => $sheet->getCell('I' . $row)->getValue(),
                'updated_at'  => $sheet->getCell('J' . $row)->getValue(),
            ];

            if ($this->presenceModel->insert($data)) {
                ++$successCount;
            } else {
                ++$skipCount;
                $skipDetails[] = [
                    'row'    => $row,
                    'code'   => $kodeKartu,
                    'name'   => $namaKaryawan,
                    'reason' => 'Insert failed: ' . json_encode($this->presenceModel->errors()),
                ];
            }
        }

        session()->setFlashdata('skipDetails', $skipDetails);

        return redirect()->back()->with('success', "Import selesai. Berhasil: {$successCount}, Dilewati: {$skipCount}");
    }
}
