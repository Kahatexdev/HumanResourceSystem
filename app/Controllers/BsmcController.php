<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\BsmcModel;
use App\Models\EmployeeModel;
use App\Models\FactoriesModel;
use App\Models\BatchModel;
use App\Models\PeriodeModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Border;
use DateTime;
use Config\Database;

class BsmcController extends BaseController
{
    protected $role;
    protected $bsmcModel;
    protected $employeeModel;
    protected $factoryModel;
    protected $batchModel;
    protected $periodeModel;
    protected $db;

    public function __construct()
    {
        $this->bsmcModel = new BsmcModel();
        $this->employeeModel = new EmployeeModel();
        $this->factoryModel = new FactoriesModel();
        $this->batchModel = new BatchModel();
        $this->periodeModel = new PeriodeModel();
        $this->db = Database::connect();
        $this->role = session()->get('role');
    }

    public function index()
    {
        //
    }

    public function tampilPerBatch($factory_name)
    {
        // dd($factory_name);
        $summaryBsmc = $this->bsmcModel->getDatabyArea($factory_name);
        $batch = $this->batchModel->getBatch();
        // dd($batch);
        $data = [
            'role' => session()->get('role'),
            'title' => 'Bs Mesin',
            'active1' => 'active',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'active7' => '',
            'active8' => '',
            'factory_name' => $factory_name,
            'batch' => $batch,
            'summaryBsmc' => $summaryBsmc
        ];

        return view('Bsmc/tampilPerBatch', $data);
    }

    public function sumBsMesin($factory_name, $id_batch)
    {
        $sumBs = $this->bsmcModel->getSummaryBSMesin($id_batch, $factory_name);
        // dd($sumBs);
        $namaBatch = $this->batchModel->where('id_batch', $id_batch)->first();
        $start_dates = array_column($sumBs, 'end_date');
        // Konversi setiap start_date menjadi nama bulan
        $bulan = array_unique(array_map(fn($date) => date('F', strtotime($date)), $start_dates));

        // Urutkan bulan
        $month_order = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
        usort($bulan, fn($a, $b) => array_search($a, $month_order) - array_search($b, $month_order));

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->mergeCells('A1:H2');
        $sheet->setCellValue('A1', 'REPORT SUMMARY BS MESIN');
        $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('A1')->getFont()->setBold(true);
        $sheet->getStyle('A1')->getFont()->setUnderline(true);
        $sheet->getStyle('A1')->getFont()->setName('Times New Roman');
        $sheet->getStyle('A1')->getFont()->setSize(16);

        $sheet->mergeCells('A3:B3');
        $sheet->setCellValue('A3', 'AREA');
        $sheet->setCellValue('C3', ': ' . $factory_name);
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
        $sheet->setCellValue('G6', 'PRODUKSI');
        // Masukkan data bulan ke G7, H7, I7, dst.
        $col = 'G';
        foreach ($bulan as $index => $bln) {
            $sheet->setCellValue($col . '7', substr($bln, 0, 3)); // Gunakan 3 huruf awal bulan
            $col++;
        }
        $sheet->mergeCells('J6:J7');
        $sheet->setCellValue('J6', 'RATA-RATA PRODUKSI');
        $sheet->mergeCells('K6:M6');
        $sheet->setCellValue('K6', 'BS');
        // Masukkan data bulan ke K7, L7, M7, dst.
        $col = 'K';
        foreach ($bulan as $index => $bln) {
            $sheet->setCellValue($col . '7', substr($bln, 0, 3));
            $col++;
        }
        $sheet->mergeCells('N6:N7');
        $sheet->setCellValue('N6', 'RATA-RATA BS');

        $sheet->getStyle('A6:N7')->getFont()->setBold(true);
        $sheet->getStyle('A6:N7')->getFont()->setName('Times New Roman');
        $sheet->getStyle('A6:N7')->getFont()->setSize(10);
        $sheet->getStyle('A6:N7')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('A6:N7')->getAlignment()->setVertical('center');
        $sheet->getStyle('A6:N7')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        $sheet->getStyle('A6:N7')->getAlignment()->setWrapText(true);

        $sheet->getColumnDimension('A')->setWidth(5);
        $sheet->getColumnDimension('B')->setWidth(10);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(5);
        $sheet->getColumnDimension('E')->setWidth(15);
        $sheet->getColumnDimension('F')->setWidth(10);
        $sheet->getColumnDimension('J')->setWidth(15);
        $sheet->getColumnDimension('N')->setWidth(13);

        $startRow = 8;
        $no = 1;

        // Array untuk menyimpan data unik berdasarkan kode kartu
        $groupedData = [];

        // Proses data untuk mengelompokkan berdasarkan kode_kartu
        foreach ($sumBs as $row) {
            $kode_kartu = $row['employee_code'];
            if (!isset($groupedData[$kode_kartu])) {
                // Jika kode kartu belum ada, simpan data awal
                $groupedData[$kode_kartu] = [
                    'employee_code'   => $row['employee_code'],
                    'employee_name'   => $row['employee_name'],
                    'gender'          => $row['gender'],
                    'date_of_joining' => $row['date_of_joining'],
                    'job_section_name' => $row['job_section_name'],
                    'produksi'        => array_fill_keys($bulan, 0), // Inisialisasi produksi per bulan
                    'bs'              => array_fill_keys($bulan, 0), // Inisialisasi bs per bulan
                    'hari_kerja'      => array_fill_keys($bulan, 0), // Inisialisasi hari kerja per bulan
                ];
            }

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
                    $groupedData[$kode_kartu]['produksi'][$bulanData] += round($row['total_produksi'] / $jumlah_hari_kerja);
                    $groupedData[$kode_kartu]['bs'][$bulanData] += round($row['total_bs'] / $jumlah_hari_kerja);
                }
            }
        }

        // Loop untuk memasukkan data ke dalam Excel
        foreach ($groupedData as $data) {
            $sheet->setCellValue('A' . $startRow, $no);
            $sheet->setCellValue('B' . $startRow, $data['employee_code']);
            $sheet->setCellValue('C' . $startRow, $data['employee_name']);
            $sheet->setCellValue('D' . $startRow, $data['gender']);
            $sheet->setCellValue('E' . $startRow, $data['date_of_joining']);
            $sheet->setCellValue('F' . $startRow, $data['job_section_name']);

            // Kolom awal produksi dan BS
            $colProd = 'G';
            $colBS = 'K';
            $totalProduksi = 0;
            $totalBS = 0;
            $totalHariKerja = 0;
            $jumlahBulan = count($bulan);

            // Loop bulan untuk memasukkan produksi & bs
            foreach ($bulan as $bln) {
                $produksiBulan = $data['produksi'][$bln];
                $bsBulan = $data['bs'][$bln];
                $hariKerjaBulan = $data['hari_kerja'][$bln];

                $totalProduksi += $produksiBulan;
                $totalBS += $bsBulan;
                $totalHariKerja += $hariKerjaBulan;
                $sheet->setCellValue($colProd . $startRow, $produksiBulan);
                $sheet->setCellValue($colBS . $startRow, $bsBulan);

                // Geser ke kolom berikutnya
                $colProd++;
                $colBS++;
            }

            // Hitung rata-rata produksi dan BS berdasarkan 3 bulan
            $rataProduksiPerBatch = $jumlahBulan > 0 ? round($totalProduksi / $jumlahBulan) : 0;
            $rataBSPerBatch = $jumlahBulan > 0 ? round($totalBS / $jumlahBulan) : 0;

            // Masukkan rata-rata ke kolom yang sesuai
            $sheet->setCellValue('J' . $startRow, $rataProduksiPerBatch);
            $sheet->setCellValue('N' . $startRow, $rataBSPerBatch);

            $sheet->getStyle('A' . $startRow . ':N' . $startRow)->getFont()->setName('Times New Roman');
            $sheet->getStyle('A' . $startRow . ':N' . $startRow)->getFont()->setSize(10);
            $sheet->getStyle('A' . $startRow . ':N' . $startRow)->getAlignment()->setHorizontal('center');
            $sheet->getStyle('A' . $startRow . ':N' . $startRow)->getAlignment()->setVertical('center');
            $sheet->getStyle('A' . $startRow . ':N' . $startRow)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            $sheet->getStyle('A' . $startRow . ':N' . $startRow)->getAlignment()->setWrapText(true);

            $no++;
            $startRow++;
        }

        // get 3 karyawan dengan max average produksi dan min average bs

        // Header untuk Top 3 Produksi
        $sheet->mergeCells('Q6:X6');
        $sheet->setCellValue('Q6', 'TOP 3 PRODUKSI');
        $sheet->getStyle('Q6')->getFont()->setBold(true);
        $sheet->getStyle('Q6')->getFont()->setName('Times New Roman');
        $sheet->getStyle('Q6')->getFont()->setSize(10);
        $sheet->getStyle('Q6')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('Q6')->getAlignment()->setVertical('center');
        $sheet->getStyle('Q6:X6')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        // Sub-header untuk kolom Top 3 Produksi
        $sheet->setCellValue('Q7', 'NO');
        $sheet->setCellValue('R7', 'KODE KARTU');
        $sheet->setCellValue('S7', 'NAMA KARYAWAN');
        $sheet->setCellValue('T7', 'L/P');
        $sheet->setCellValue('U7', 'TGL MASUK');
        $sheet->setCellValue('V7', 'BAGIAN');
        $sheet->setCellValue('W7', 'AVG PRODUKSI');
        $sheet->setCellValue('X7', 'AVG BS');

        $sheet->getStyle('Q7:X7')->getFont()->setBold(true);
        $sheet->getStyle('Q7:X7')->getFont()->setName('Times New Roman');
        $sheet->getStyle('Q7:X7')->getFont()->setSize(10);
        $sheet->getStyle('Q7:X7')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('Q7:X7')->getAlignment()->setVertical('center');
        $sheet->getStyle('Q7:X7')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        $sheet->getStyle('Q7:X7')->getAlignment()->setWrapText(true);

        // column dimension
        $sheet->getColumnDimension('Q')->setWidth(5);
        $sheet->getColumnDimension('R')->setWidth(10);
        $sheet->getColumnDimension('S')->setWidth(20);
        $sheet->getColumnDimension('T')->setWidth(5);
        $sheet->getColumnDimension('U')->setWidth(15);
        $sheet->getColumnDimension('V')->setWidth(10);
        $sheet->getColumnDimension('W')->setWidth(10);
        $sheet->getColumnDimension('X')->setWidth(10);

        // header untuk top 3 min avg bs
        $sheet->mergeCells('Z6:AG6');
        $sheet->setCellValue('Z6', 'TOP 3 MIN AVG BS');
        $sheet->getStyle('Z6')->getFont()->setBold(true);
        $sheet->getStyle('Z6')->getFont()->setName('Times New Roman');
        $sheet->getStyle('Z6')->getFont()->setSize(10);
        $sheet->getStyle('Z6')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('Z6')->getAlignment()->setVertical('center');
        $sheet->getStyle('Z6:AG6')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        // Sub-header untuk kolom Top 3 Min Avg BS
        $sheet->setCellValue('Z7', 'NO');
        $sheet->setCellValue('AA7', 'KODE KARTU');
        $sheet->setCellValue('AB7', 'NAMA KARYAWAN');
        $sheet->setCellValue('AC7', 'L/P');
        $sheet->setCellValue('AD7', 'TGL MASUK');
        $sheet->setCellValue('AE7', 'BAGIAN');
        $sheet->setCellValue('AF7', 'AVG PRODUKSI');
        $sheet->setCellValue('AG7', 'AVG BS');

        $sheet->getStyle('Z7:AG7')->getFont()->setBold(true);
        $sheet->getStyle('Z7:AG7')->getFont()->setName('Times New Roman');
        $sheet->getStyle('Z7:AG7')->getFont()->setSize(10);
        $sheet->getStyle('Z7:AG7')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('Z7:AG7')->getAlignment()->setVertical('center');
        $sheet->getStyle('Z7:AG7')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        // Data Top 3 Produksi
        // Urutkan data berdasarkan rata-rata produksi tertinggi
        usort($groupedData, function ($a, $b) {
            return array_sum($b['produksi']) <=> array_sum($a['produksi']); // Descending
        });
        $top3Produksi = array_slice($groupedData, 0, 3); // Ambil 3 terbesar

        $startRow = 8;
        $no = 1;
        foreach ($top3Produksi as $row) {

            $avgProduksi = array_sum($row['produksi']) / count($bulan);
            $avgBS = array_sum($row['bs']) / count($bulan);

            $sheet->setCellValue('Q' . $startRow, $no);
            $sheet->setCellValue('R' . $startRow, $row['employee_code']);
            $sheet->setCellValue('S' . $startRow, $row['employee_name']);
            $sheet->setCellValue('T' . $startRow, $row['gender']);
            $sheet->setCellValue('U' . $startRow, $row['date_of_joining']);
            $sheet->setCellValue('V' . $startRow, $row['job_section_name']);
            $sheet->setCellValue('W' . $startRow, round($avgProduksi));
            $sheet->setCellValue('X' . $startRow, round($avgBS));

            $sheet->getStyle('Q' . $startRow . ':X' . $startRow)->getFont()->setName('Times New Roman');
            $sheet->getStyle('Q' . $startRow . ':X' . $startRow)->getFont()->setSize(10);
            $sheet->getStyle('Q' . $startRow . ':X' . $startRow)->getAlignment()->setHorizontal('center');
            $sheet->getStyle('Q' . $startRow . ':X' . $startRow)->getAlignment()->setVertical('center');
            $sheet->getStyle('Q' . $startRow . ':X' . $startRow)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            $sheet->getStyle('Q' . $startRow . ':X' . $startRow)->getAlignment()->setWrapText(true);

            $no++;
            $startRow++;
        }

        // Data Top 3 Min Avg BS

        // Urutkan berdasarkan produksi tertinggi
        usort($groupedData, function ($a, $b) {
            return array_sum($b['produksi']) <=> array_sum($a['produksi']); // Descending
        });
        // Ambil 7 data produksi tertinggi
        $top7Produksi = array_slice($groupedData, 0, 7);

        // Urutkan 7 data ini berdasarkan BS terkecil
        usort($top7Produksi, function ($a, $b) {
            return array_sum($a['bs']) <=> array_sum($b['bs']); // Ascending
        });
        // Ambil 3 data BS terkecil dari Top 7 Produksi
        $top3BS = array_slice($top7Produksi, 0, 3);

        // $getMinAvgBS = $this->bsmcModel->getTop3LowestBS($area_utama, $id_batch);
        $startRow = 8;
        $no = 1;
        foreach ($top3BS as $row) {

            $avgProduksi = array_sum($row['produksi']) / count($bulan);
            $avgBS = array_sum($row['bs']) / count($bulan);

            $sheet->setCellValue('Z' . $startRow, $no);
            $sheet->setCellValue('AA' . $startRow, $row['employee_code']);
            $sheet->setCellValue('AB' . $startRow, $row['employee_name']);
            $sheet->setCellValue('AC' . $startRow, $row['gender']);
            $sheet->setCellValue('AD' . $startRow, $row['date_of_joining']);
            $sheet->setCellValue('AE' . $startRow, $row['job_section_name']);
            $sheet->setCellValue('AF' . $startRow, round($avgProduksi));
            $sheet->setCellValue('AG' . $startRow, round($avgBS));

            $sheet->getStyle('Z' . $startRow . ':AG' . $startRow)->getFont()->setName('Times New Roman');
            $sheet->getStyle('Z' . $startRow . ':AG' . $startRow)->getFont()->setSize(10);
            $sheet->getStyle('Z' . $startRow . ':AG' . $startRow)->getAlignment()->setHorizontal('center');
            $sheet->getStyle('Z' . $startRow . ':AG' . $startRow)->getAlignment()->setVertical('center');
            $sheet->getStyle('Z' . $startRow . ':AG' . $startRow)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            $sheet->getStyle('Z' . $startRow . ':AG' . $startRow)->getAlignment()->setWrapText(true);

            $no++;
            $startRow++;
        }

        $spreadsheet->getActiveSheet()->setTitle('REPORT SUMMARY BS MESIN');

        $filename = 'REPORT SUMMARY BS MESIN ' . $factory_name . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit;
    }
    // Bekas pindahin sum bsmc dari db skillmapping
    public function importExcelBsmc()
    {
        $file = $this->request->getFile('file');
        if (! $file || ! $file->isValid()) {
            return redirect()->back()->with('error', 'File tidak valid');
        }

        $sheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getTempName())
            ->getActiveSheet();
        // true,true,true,true → hitung formula, format data, dan kembalikan assoc by col letter
        $rows = $sheet->toArray(null, true, true, true);

        $dataToInsert = [];

        foreach ($rows as $rowNum => $row) {
            if ($rowNum === 1) continue; // header di row 1

            // Debug: cek isi baris
            \log_message('debug', "Import row {$rowNum}: " . json_encode($row));

            // Ambil employee & factory
            $employee = $this->employeeModel
                ->where('employee_code', $row['A'])
                ->first();
            $factory  = $this->factoryModel
                ->where('factory_name', $row['C'])
                ->first();

            if (! $employee || ! $factory) {
                // skip baris ini, bisa juga catat error/log
                continue;
            }

            $dataToInsert[] = [
                'id_employee' => $employee['id_employee'],
                'id_factory'  => $factory['id_factory'],
                'tgl_input'   => date('Y-m-d', strtotime($row['D'])),
                'produksi'    => $row['E'],     // pastikan ini tidak kosong di log
                'bs_mc'       => $row['F'],
                'created_at'  => date('Y-m-d H:i:s', strtotime($row['G'])),
                'updated_at'  => date('Y-m-d H:i:s', strtotime($row['H'])),
            ];
        }

        if (! empty($dataToInsert)) {
            $this->bsmcModel->insertBatch($dataToInsert);
        }

        return redirect()->back()->with('success', 'Import berhasil!');
    }

    public function fetchDataBsmc()
    {
        // Ambil rentang tanggal dari form atau default ke hari ini
        $tglFrom = $this->request->getGet('tgl_from') ?? date('Y-m-d');
        $tglTo   = $this->request->getGet('tgl_to')   ?? $tglFrom;

        // Buat daftar tanggal dalam rentang
        $period = new \DatePeriod(
            new \DateTime($tglFrom),
            new \DateInterval('P1D'),
            (new \DateTime($tglTo))->modify('+1 day')
        );

        $factoryNames = [
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

        $errors  = [];
        $inserts = [];
        $updates = [];
        $bsmcModel  = new \App\Models\BsmcModel();
        $factoryModel = new \App\Models\FactoryModel(); // Sesuaikan model
        $factoryMap = [];
        foreach ($factoryNames as $name) {
            $factory = $factoryModel->where('factory_name', $name)->first();
            if ($factory) $factoryMap[$name] = $factory['id_factory'];
        }
        $rowCounter = 0;

        // Loop tiap tanggal dan tiap factory
        foreach ($period as $date) {
            $currentDate = $date->format('Y-m-d');
            foreach ($factoryNames as $factory) {
                $id_factory_current = $factoryMap[$factory] ?? null;
                if (!$id_factory_current) {
                    $errors[] = "Factory $factory tidak terdaftar!";
                    continue;
                }
                $rowCounter++;
                $url = "http://172.23.44.14/CapacityApps/public/api/prodBsDaily/{$factory}/{$currentDate}";
                $response = @file_get_contents($url);
                if ($response === false) {
                    $errors[] = "No response for {$factory} on {$currentDate}.";
                    continue;
                }
                $rows = json_decode($response, true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    $errors[] = "Invalid JSON for {$factory} on {$currentDate}: " . json_last_error_msg();
                    continue;
                }

               
                $employeeData = [];
                foreach ($rows as $row => $i) {
                    $em = $this->employeeModel->select('employee_name,id_factory')
                    ->where('employee_name', strtoupper($i['nama_karyawan']))
                    ->first();
                    // dd ($em['id_factory'], $i['nama_karyawan'], $i['qty_produksi'], $i['qty_pcs']);

                    $employeeData[] = [
                        'name' => strtoupper($em['employee_name'] ?? ''),
                        'area' => $em['id_factory'] ?? null, // Ambil id factory dari karyawan
                    ];
                    // dd ($employeeData);
                    $employeeMap = $this->getEmployeeMap($employeeData);

                    $key = strtoupper($employeeData[$row]['name']) . '|' . $employeeData[$row]['area'];
                    // dd ($key, $employeeMap);
                    $emp = $employeeMap[$key] ?? null; // Data karyawan
                    // dd($emp['id_employee'], $emp['id_factory'], $emp['shift'], $id_factory_current);
                    $idEmp = $emp['id_employee'] ?? null;
                    $fac   = $emp['id_factory']  ?? null;
                    if (!$emp) {
                        $errors[] = "Karyawan " . strtoupper($i['nama_karyawan']) . " tidak ditemukan di factory $factory";
                        continue;
                    }

                    // Siapkan record
                    $record = [
                        'id_employee' => $idEmp,
                        'tgl_input'   => $currentDate,
                        'produksi'    => $i['qty_produksi'],
                        'bs_mc'       => round($i['qty_pcs']),
                        'id_factory'  => $fac,
                    ];

                    // dd($record);
                    // Cek existensi
                    $exists = $bsmcModel
                        ->where('id_employee', $idEmp)
                        ->where('tgl_input',   $currentDate)
                        ->where('id_factory',  $fac)
                        ->first();

                    if ($exists) {
                        // update existing
                        $updates[] = array_merge($record, ['id_bsmc' => $exists['id_bsmc']]);
                    } else {
                        // insert baru
                        $inserts[] = $record;
                    }
                }
            }
        }
        // dd ($inserts, $updates, $errors);
        // Hitung dan eksekusi batch
        $countInserts = count($inserts);
        $countUpdates = count($updates);

        if ($countInserts > 0) {
            $bsmcModel->insertBatch($inserts);
        }
        if ($countUpdates > 0) {
            $bsmcModel->updateBatch($updates, 'id_bsmc');
        }

        if ($countInserts === 0 && $countUpdates === 0) {
            return redirect()->back()
                ->with('error', 'Tidak ada data baru atau yang perlu diperbarui.')
                ->with('validation_errors', $errors);
        }

        return redirect()->back()
            ->with('success', sprintf(
                'Data berhasil diproses dari %s sampai %s: %d insert, %d update.',
                $tglFrom,
                $tglTo,
                $countInserts,
                $countUpdates
            ))
            ->with('validation_errors', $errors);
    }

    // Helper: Ambil mapping karyawan
    private function getEmployeeMap(array $employeeData)
    {
        $names = array_column($employeeData, 'name');
        $areaIds = array_column($employeeData, 'area');
        // $shifts = array_column($employeeData, 'shift');
        // dd($factoryId);


        $builder = $this->db->table('employees')
            ->select('id_employee, employee_name, shift, id_factory');

        // Tambahkan kondisi hanya jika array tidak kosong
        if (!empty($names)) {
            $builder->whereIn('employee_name', $names);
        }

        if (!empty($areaIds)) {
            $builder->whereIn('id_factory', $areaIds);
        }

        // if (!empty($shifts)) {
        //     $builder->whereIn('shift', $shifts);
        // }

        $result = $builder->get()->getResultArray();

        $map = [];
        foreach ($result as $emp) {
            $key = $emp['employee_name'] . '|' . $emp['id_factory'];
            $map[$key] = $emp;
        }
        // dd ($map);
        return $map;
    }

    public function filterBsmc($area)
    {
        $tgl_awal  = $this->request->getPost('tgl_awal');
        $tgl_akhir = $this->request->getPost('tgl_akhir');

        if (empty($tgl_awal) || empty($tgl_akhir)) {
            $tgl_awal  = date('Y-m-d');
            $tgl_akhir = date('Y-m-d');
        }

        $bs_mc = $this->bsmcModel->getFilteredData($area, $tgl_awal, $tgl_akhir);

        $data = [
            'role' => session()->get('role'),
            'title' => 'Bs Mesin',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'active7' => 'active',
            'area' => $area,
            'bsmc' => $bs_mc
        ];

        return view('Bsmc/filter-bsmc', $data);
    }
}
