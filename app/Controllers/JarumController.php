<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\BatchModel;
use App\Models\PeriodeModel;
use App\Models\JarumModel;
use App\Models\EmployeeModel;
use App\Models\FactoriesModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Font;
use DateTime;

class JarumController extends BaseController
{
    protected $summaryJarum;
    protected $batchModel;
    protected $periodeModel;
    protected $employeeModel;
    protected $factoriesModel;
    protected $role;
    public function __construct()
    {
        $this->summaryJarum = new JarumModel();
        $this->batchModel = new BatchModel();
        $this->periodeModel = new PeriodeModel();
        $this->employeeModel = new EmployeeModel();
        $this->factoriesModel = new FactoriesModel();
        $this->role = session()->get('role');
    }
    public function index()
    {
        //
    }

    public function tampilPerBatch($area)
    {
        $summaryJarum = $this->summaryJarum->getDatabyArea($area);
        $batch = $this->batchModel->findAll();
        $data = [
            'role' => session()->get('role'),
            'title' => 'Jarum',
            'active1' => 'active',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'active7' => '',
            'active8' => '',
            'area' => $area,
            'batch' => $batch,
            'summaryJarum' => $summaryJarum

        ];

        return view('Jarum/tampilPerBatch', $data);
    }

    public function summaryJarum()
    {
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
            'active8' => 'active'

        ];

        // dd ($summaryRosso);
        return view('Jarum/summaryPerPeriode', $data);
    }

    public function downloadTemplate()
    {
        // Membuat spreadsheet baru
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Menyusun header kolom
        $sheet->setCellValue('A1', 'KODE KARTU');
        $sheet->setCellValue('B1', 'NAMA LENGKAP');
        $sheet->setCellValue('C1', 'L/P');
        $sheet->setCellValue('D1', 'TGL. MASUK KERJA');
        $sheet->setCellValue('E1', 'BAGIAN');
        $sheet->setCellValue('F1', 'RATA-RATA PEMAKAIAN JARUM');

        // Mengatur lebar kolom
        $sheet->getColumnDimension('A')->setWidth(20);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(20);
        $sheet->getColumnDimension('E')->setWidth(20);
        $sheet->getColumnDimension('F')->setWidth(20);



        // Mengatur style header
        $sheet->getStyle('A1:F1')->getFont()->setBold(true);
        $sheet->getStyle('A1:F1')->getFill()->setFillType('solid')->getStartColor()->setARGB('FFA0A0A0');
        $sheet->getStyle('A1:F1')->getAlignment()->setHorizontal('center');

        // isi data
        $sheet->setCellValue('A2', 'KK001');
        $sheet->setCellValue('B2', 'John Doe');
        $sheet->setCellValue('C2', 'L');
        $sheet->setCellValue('D2', '24/05/2023');
        $sheet->setCellValue('E2', 'KNITTER');
        $sheet->setCellValue('F2', '4');

        // 
        // Menentukan nama file
        $fileName = 'Template_Summary_Jarum.xlsx';

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
        // $tes = $this->request->getPost();
        // dd ($tes);
        $getTglInput = $this->request->getPost('tgl_input');
        $getArea = $this->request->getPost('area');
        $getKaryawan = $this->request->getPost('id_karyawan');
        $getNeedle = $this->request->getPost('used_needle');

        $currentDate = date('Y-m-d');

        if ($getTglInput > $currentDate) {
            return redirect()->to(base_url($this->role . '/dataJarum'))->with('error', 'Tanggal input tidak boleh lebih dari hari ini');
        }
        $data = [];
        for ($i = 0; $i < count($getKaryawan); $i++) {
            $data[] = [
                'tgl_input'    => $getTglInput,
                'id_employee'  => $getKaryawan[$i],
                'used_needle'  => $getNeedle[$i],
                'created_at'   => date('Y-m-d H:i:s'),
                'id_factory'         => $getArea
            ];
        }
        if (!empty($data)) {
            $this->summaryJarum->insertBatch($data);
        }

        return redirect()->to(base_url($this->role . '/dataJarum'))->with('success', 'Data berhasil diupload');
    }

    public function excelSummaryJarum($area, $id_batch)
    {
        $area = $this->factoriesModel->where('factory_name', $area)->first();
        // dd ($area);
        $summaryJarum = $this->summaryJarum->getSummaryJarumv2($area['id_factory'], $id_batch);
        // dd ($summaryJarum);
        $namaBatch = $this->batchModel->where('id_batch', $id_batch)->first();
        $start_dates = array_column($summaryJarum, 'end_date');
        // Konversi setiap start_date menjadi nama bulan
        $bulan = array_unique(array_map(fn($date) => date('F', strtotime($date)), $start_dates));

        // Urutkan bulan
        $month_order = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
        usort($bulan, fn($a, $b) => array_search($a, $month_order) - array_search($b, $month_order));

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->mergeCells('A1:H2');
        $sheet->setCellValue('A1', 'REPORT SUMMARY JARUM');
        $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('A1')->getFont()->setBold(true);
        $sheet->getStyle('A1')->getFont()->setUnderline(true);
        $sheet->getStyle('A1')->getFont()->setName('Times New Roman');
        $sheet->getStyle('A1')->getFont()->setSize(16);

        $sheet->mergeCells('A3:B3');
        $sheet->setCellValue('A3', 'AREA');
        $sheet->setCellValue('C3', ': ' . $area['factory_name']);
        $sheet->getStyle('A3:C3')->getFont()->setBold(true);

        $sheet->mergeCells('A4:B4');
        $sheet->setCellValue('A4', 'NAMA BATCH');
        $sheet->setCellValue('C4', ': ' . $namaBatch['batch_name']);
        $sheet->getStyle('A4:C4')->getFont()->setBold(true);
        $sheet->getStyle('A3:C4')->getFont()->setName('Times New Roman');
        $sheet->getStyle('A3:C4')->getFont()->setSize(12);


        $sheet->mergeCells('A6:A7');
        $sheet->setCellValue('A6', 'NO');
        $sheet->mergeCells('B6:B7');
        $sheet->setCellValue('B6', 'KODE KARTU');
        $sheet->mergeCells('C6:C7');
        $sheet->setCellValue('C6', 'NAMA LENGKAP');
        $sheet->mergeCells('D6:D7');
        $sheet->setCellValue('D6', 'L/P');
        $sheet->mergeCells('E6:E7');
        $sheet->setCellValue('E6', 'TGL. MASUK KERJA');
        $sheet->mergeCells('F6:F7');
        $sheet->setCellValue('F6', 'BAGIAN');
        $sheet->mergeCells('G6:I6');
        $sheet->setCellValue('G6', 'NEEDLE USED');
        // Masukkan data bulan ke G7, H7, I7, dst.
        $col = 'G';
        foreach ($bulan as $index => $bln) {
            $sheet->setCellValue($col . '7', substr($bln, 0, 3)); // Gunakan 3 huruf awal bulan
            $col++;
        }
        $sheet->setCellValue('J6', 'AVG USED NEEDLE');
        $sheet->mergeCells('J6:J7');

        $sheet->getStyle('A6:J7')->getFont()->setBold(true);
        $sheet->getStyle('A6:J7')->getFont()->setName('Times New Roman');
        $sheet->getStyle('A6:J7')->getFont()->setSize(10);
        $sheet->getStyle('A6:J7')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('A6:J7')->getAlignment()->setVertical('center');
        $sheet->getStyle('A6:J7')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        $sheet->getStyle('A6:J7')->getAlignment()->setWrapText(true);

        $sheet->getColumnDimension('A')->setWidth(5);
        $sheet->getColumnDimension('B')->setWidth(10);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(5);
        $sheet->getColumnDimension('E')->setWidth(15);
        $sheet->getColumnDimension('F')->setWidth(10);
        $sheet->getColumnDimension('J')->setWidth(10);

        $startRow = 8;
        $no = 1;

        // Array untuk menyimpan data unik berdasarkan kode kartu
        $groupedData = [];

        foreach ($summaryJarum as $row) {
            $kode_kartu = $row['employee_code'];
            if (!isset($groupedData[$kode_kartu])) {
                // Jika kode kartu belum ada, simpan data awal
                $groupedData[$kode_kartu] = [
                    'kode_kartu'    => $row['employee_code'],
                    'nama_karyawan' => $row['employee_name'],
                    'jenis_kelamin' => $row['gender'],
                    'tgl_masuk'     => $row['date_of_joining'],
                    'nama_bagian'   => $row['job_section_name'],
                    'used_needle'   => array_fill_keys($bulan, 0), // Inisialisasi penggunaan jarum per bulan
                    'hari_kerja'    => array_fill_keys($bulan, 0), // Inisialisasi hari kerja per bulan
                ];
            }
            // dd ($groupedData);
            // Menghitung jumlah hari kerja dalam bulan tersebut
            $startDate = new DateTime($row['start_date']);
            $endDate   = new DateTime($row['end_date']);
            $jumlahHari = $endDate->diff($startDate)->days + 1; // Total hari dalam periode
            $hariKerja = $jumlahHari - (int) $row['holiday']; // Hari kerja setelah dikurangi libur

            // Ambil jumlah hari kerja dari tabel periode
            $periode = $this->periodeModel->where('start_date <=', $row['end_date'])
                ->where('end_date >=', $row['end_date'])
                ->first();

            if ($periode) {
                $jumlah_hari_kerja = ((strtotime($periode['end_date']) - strtotime($periode['start_date'])) / (60 * 60 * 24)) + 1 - $periode['holiday'];
                if ($jumlah_hari_kerja > 0) {
                    $bulanData = date('F', strtotime($row['end_date']));
                    $groupedData[$kode_kartu]['used_needle'][$bulanData] += round($row['total_jarum'] / $jumlah_hari_kerja);
                }
            }
        }

        // Loop untuk memasukkan data ke dalam Excel
        foreach ($groupedData as $data) {

            $sheet->setCellValue('A' . $startRow, $no);
            $sheet->setCellValue('B' . $startRow, $data['kode_kartu']);
            $sheet->setCellValue('C' . $startRow, $data['nama_karyawan']);
            $sheet->setCellValue('D' . $startRow, $data['jenis_kelamin']);
            $sheet->setCellValue('E' . $startRow, $data['tgl_masuk']);
            $sheet->setCellValue('F' . $startRow, $data['nama_bagian']);

            $colProd = 'G';
            $totalProduksi = 0;
            $totalHariKerja = 0;
            $jumlahBulan = count($bulan);

            // Loop bulan untuk memasukkan produksi & bs
            foreach ($bulan as $bln) {
                $produksiBulan = $data['used_needle'][$bln];
                $hariKerjaBulan = $data['hari_kerja'][$bln];
                $totalProduksi += $produksiBulan;
                $totalHariKerja += $hariKerjaBulan;
                $sheet->setCellValue($colProd . $startRow, $produksiBulan);

                // Geser ke kolom berikutnya
                $colProd++;
            }
            // dd ($groupedData);
            // Hitung rata-rata penggunaan jarum berdasarkan 3 bulan
            $rataJarumPerBatch = $jumlahBulan > 0 ? round($totalProduksi / $jumlahBulan) : 0;
            // dd($rataJarumPerBatch);
            // Masukkan rata-rata ke kolom yang sesuai
            $sheet->setCellValue('J' . $startRow, $rataJarumPerBatch);

            $sheet->getStyle('A' . $startRow . ':J' . $startRow)->getFont()->setName('Times New Roman');
            $sheet->getStyle('A' . $startRow . ':J' . $startRow)->getFont()->setSize(10);
            $sheet->getStyle('A' . $startRow . ':J' . $startRow)->getAlignment()->setHorizontal('center');
            $sheet->getStyle('A' . $startRow . ':J' . $startRow)->getAlignment()->setVertical('center');
            $sheet->getStyle('A' . $startRow . ':J' . $startRow)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            $sheet->getStyle('A' . $startRow . ':J' . $startRow)->getAlignment()->setWrapText(true);

            $no++;
            $startRow++;
        }
        // dd ($groupedData);

        // Header untuk Top 3 Rata-Rata Penggunaan Jarum
        $sheet->mergeCells('M6:S6');
        $sheet->setCellValue('M6', 'TOP 3 AVG USED NEEDLE');
        $sheet->getStyle('M6')->getFont()->setBold(true);
        $sheet->getStyle('M6')->getFont()->setName('Times New Roman');
        $sheet->getStyle('M6')->getFont()->setSize(10);
        $sheet->getStyle('M6')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('M6')->getAlignment()->setVertical('center');
        $sheet->getStyle('M6:S6')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        // Sub-header untuk kolom Top 3 Produksi
        $sheet->setCellValue('M7', 'NO');
        $sheet->setCellValue('N7', 'KODE KARTU');
        $sheet->setCellValue('O7', 'NAMA KARYAWAN');
        $sheet->setCellValue('P7', 'L/P');
        $sheet->setCellValue('Q7', 'TGL MASUK');
        $sheet->setCellValue('R7', 'BAGIAN');
        $sheet->setCellValue('S7', 'AVG USED NEEDLE');

        $sheet->getStyle('M7:S7')->getFont()->setBold(true);
        $sheet->getStyle('M7:S7')->getFont()->setName('Times New Roman');
        $sheet->getStyle('M7:S7')->getFont()->setSize(10);
        $sheet->getStyle('M7:S7')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('M7:S7')->getAlignment()->setVertical('center');
        $sheet->getStyle('M7:S7')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        $sheet->getStyle('M7:S7')->getAlignment()->setWrapText(true);

        // column dimension
        $sheet->getColumnDimension('M')->setWidth(5);
        $sheet->getColumnDimension('N')->setWidth(10);
        $sheet->getColumnDimension('O')->setWidth(20);
        $sheet->getColumnDimension('P')->setWidth(5);
        $sheet->getColumnDimension('Q')->setWidth(15);
        $sheet->getColumnDimension('R')->setWidth(10);
        $sheet->getColumnDimension('S')->setWidth(10);

        // Data Top 3 Minimum Penggunaan Jarum
        // Urutkan 7 data ini berdasarkan BS terkecil
        usort($groupedData, function ($a, $b) {
            return array_sum($a['used_needle']) <=> array_sum($b['used_needle']); // Ascending
        });
        // Ambil 3 data Min Penggunaan Jarum
        $getTop3 = array_slice($groupedData, 0, 3);

        $startRow = 8;
        $no = 1;

        foreach ($getTop3 as $row) {

            $avgNeedle = array_sum($row['used_needle']) / count($bulan);
            // dd (count($bulan));
            $sheet->setCellValue('M' . $startRow, $no);
            $sheet->setCellValue('N' . $startRow, $row['kode_kartu']);
            $sheet->setCellValue('O' . $startRow, $row['nama_karyawan']);
            $sheet->setCellValue('P' . $startRow, $row['jenis_kelamin']);
            $sheet->setCellValue('Q' . $startRow, $row['tgl_masuk']);
            $sheet->setCellValue('R' . $startRow, $row['nama_bagian']);
            $sheet->setCellValue('S' . $startRow, round($avgNeedle));

            $sheet->getStyle('M' . $startRow . ':S' . $startRow)->getFont()->setName('Times New Roman');
            $sheet->getStyle('M' . $startRow . ':S' . $startRow)->getFont()->setSize(10);
            $sheet->getStyle('M' . $startRow . ':S' . $startRow)->getAlignment()->setHorizontal('center');
            $sheet->getStyle('M' . $startRow . ':S' . $startRow)->getAlignment()->setVertical('center');
            $sheet->getStyle('M' . $startRow . ':S' . $startRow)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            $sheet->getStyle('M' . $startRow . ':S' . $startRow)->getAlignment()->setWrapText(true);

            $no++;
            $startRow++;
        }

        $spreadsheet->getActiveSheet()->setTitle('REPORT SUMMARY JARUM');

        $filename = 'REPORT SUMMARY JARUM ' . $area['factory_name'] . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit;
    }

    public function filterJarum($area)
    {
        $tgl_awal  = $this->request->getPost('tgl_awal');
        $tgl_akhir = $this->request->getPost('tgl_akhir');

        if (empty($tgl_awal) || empty($tgl_akhir)) {
            $tgl_awal  = date('Y-m-d');
            $tgl_akhir = date('Y-m-d');
        }

        $pJarum = $this->summaryJarum->getFilteredData($area, $tgl_awal, $tgl_akhir);
        // dd ($pJarum);
        $data = [
            'role' => $this->role,
            'title' => 'Penggunaan Jarum',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'active7' => '',
            'active8' => '',
            'active9' => 'active',
            'area' => $area,
            'pJarum' => $pJarum
        ];

        return view('jarum/filter-jarum', $data);
    }

    public function uploadJarum()
    {
        $file = $this->request->getFile('file');

        // Validasi file
        if (! $file || ! $file->isValid() || $file->hasMoved()) {
            return redirect()->to(base_url('Sudo/dataJarum'))
                ->with('error', 'File tidak valid atau sudah dipindahkan.');
        }

        // Cek MIME type
        $fileType = $file->getClientMimeType();
        if (! in_array($fileType, [
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        ])) {
            return redirect()->to(base_url('Sudo/dataJarum'))
                ->with('error', 'File harus berupa Excel (.xlsx)');
        }

        // Load spreadsheet
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getTempName());
        $sheet       = $spreadsheet->getActiveSheet();
        $startRow    = 2;

        $data         = [];
        $errorCount   = 0;
        $errorMessages = [];

        // Loop setiap baris
        for ($row = $startRow; $row <= $sheet->getHighestRow(); $row++) {
            $kodeKartu = trim($sheet->getCell('A' . $row)->getValue());
            $namaK      = trim($sheet->getCell('B' . $row)->getValue());
            $area       = trim($sheet->getCell('D' . $row)->getValue());

            // Tanggal input
            $rawTgl = $sheet->getCell('F' . $row)->getValue();
            if (is_numeric($rawTgl)) {
                $tglInput = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($rawTgl)
                    ->format('Y-m-d');
            } else {
                $tglInput = date('Y-m-d', strtotime($rawTgl));
            }

            $usedNeedle = (int) $sheet->getCell('G' . $row)->getValue();

            // created_at
            $rawCreated = $sheet->getCell('H' . $row)->getValue();
            if (is_numeric($rawCreated)) {
                $createdAt = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($rawCreated)
                    ->format('Y-m-d H:i:s');
            } else {
                $createdAt = date('Y-m-d H:i:s', strtotime($rawCreated));
            }

            // updated_at
            $rawUpdated = $sheet->getCell('I' . $row)->getValue();
            if (is_numeric($rawUpdated)) {
                $updatedAt = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($rawUpdated)
                    ->format('Y-m-d H:i:s');
            } else {
                $updatedAt = date('Y-m-d H:i:s', strtotime($rawUpdated));
            }

            // Validasi dasar
            if (empty($kodeKartu) || empty($namaK) || empty($tglInput)) {
                $errorCount++;
                $errorMessages[] = "Baris {$row}: Data tidak lengkap.";
                continue;
            }
            if ($usedNeedle < 0) {
                $errorCount++;
                $errorMessages[] = "Baris {$row}: Used Needle tidak boleh negatif.";
                continue;
            }

            // Cari karyawan
            $emp = $this->employeeModel->where('employee_code', $kodeKartu)->first();
            if (! $emp) {
                $errorCount++;
                $errorMessages[] = "Baris {$row}: Kode Kartu tidak ditemukan.";
                continue;
            }

            // Cari pabrik
            $fac = $this->factoriesModel->where('factory_name', $area)->first();
            if (! $fac) {
                $errorCount++;
                $errorMessages[] = "Baris {$row}: Area tidak ditemukan.";
                continue;
            }

            // Simpan data sementara
            $data[] = [
                'id_employee' => $emp['id_employee'],
                'id_factory'  => $fac['id_factory'],
                'tgl_input'   => $tglInput,
                'used_needle' => $usedNeedle,
                'created_at'  => $createdAt,
                'updated_at'  => $updatedAt,
            ];
        }

        // Filter duplikat sebelum insert
        $toInsert = [];
        foreach ($data as $d) {
            $exists = $this->summaryJarum
                ->where('id_employee', $d['id_employee'])
                ->where('id_factory', $d['id_factory'])
                ->where('tgl_input', $d['tgl_input'])
                ->first();

            if (! $exists) {
                $toInsert[] = $d;
            } else {
                $errorCount++;
                $errorMessages[] = "Duplikat: Employee {$d['id_employee']} tanggal {$d['tgl_input']}.";
            }
        }

        // Insert batch jika ada data
        if (count($toInsert) > 0) {
            $this->summaryJarum->insertBatch($toInsert);
            $successCount = count($toInsert);
            $successMsg   = "Berhasil upload {$successCount} baris.";
        } else {
            $successMsg = 'Tidak ada data valid untuk diupload.';
        }

        // Komposisi pesan akhir
        $errorMsg = $errorCount > 0
            ? implode('<br>', $errorMessages)
            : 'Tidak ada error.';

        return redirect()->to(base_url($this->role . '/dataJarum'))
            ->with('success', $successMsg . '<br>' . $errorMsg);
    }
}
