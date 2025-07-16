<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\BatchModel;
use App\Models\PeriodeModel;
use App\Models\RossoModel;
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
// use CodeIgniter\HTTP\RedirectResponse;

use DateTime;

class RossoController extends BaseController
{
    protected $rossoModel;
    protected $batchModel;
    protected $periodeModel;
    protected $employeeModel;
    protected $factoriesModel;
    protected $summaryRosso;
    protected $role;

    public function __construct()
    {
        $this->rossoModel = new RossoModel();
        $this->batchModel = new BatchModel();
        $this->periodeModel = new PeriodeModel();
        $this->employeeModel = new EmployeeModel();
        $this->factoriesModel = new FactoriesModel();
        $this->summaryRosso = new RossoModel();
        $this->role = session()->get('role');
    }
    public function index()
    {
        //
    }

    public function tampilPerBatch($area_utama)
    {
        $summaryRosso = $this->summaryRosso->getDatabyAreaUtama($area_utama);
        $batch = $this->batchModel->findAll();
        // dd ($summaryRosso);
        $data = [
            'role' => session()->get('role'),
            'title' => 'Rosso',
            'active1' => 'active',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'active7' => '',
            'active8' => '',
            'area_utama' => $area_utama,
            'batch' => $batch,
            'summaryRosso' => $summaryRosso
        ];

        return view('Rosso/tampilPerBatch', $data);
    }

    public function excelSummaryRosso($area, $id_batch)
    {
        $sumRosso = $this->summaryRosso->getSummaryRosso($area, $id_batch);
        $namaBatch = $this->batchModel->where('id_batch', $id_batch)->first();
        $start_dates = array_column($sumRosso, 'end_date');
        // Konversi setiap start_date menjadi nama bulan
        $bulan = array_unique(array_map(fn($date) => date('F', strtotime($date)), $start_dates));
        // Urutkan bulan
        $month_order = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
        usort($bulan, fn($a, $b) => array_search($a, $month_order) - array_search($b, $month_order));

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->mergeCells('A1:H2');
        $sheet->setCellValue('A1', 'REPORT SUMMARY BS DAN PRODUKSI ROSSO');
        $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('A1')->getFont()->setBold(true);
        $sheet->getStyle('A1')->getFont()->setUnderline(true);
        $sheet->getStyle('A1')->getFont()->setName('Times New Roman');
        $sheet->getStyle('A1')->getFont()->setSize(16);

        $sheet->mergeCells('A3:B3');
        $sheet->setCellValue('A3', 'AREA');
        $sheet->setCellValue('C3', ': ' . $area);
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
        $sheet->setCellValue('K6', 'PERBAIKAN');
        // Masukkan data bulan ke K7, L7, M7, dst.
        $col = 'K';
        foreach ($bulan as $index => $bln) {
            $sheet->setCellValue($col . '7', substr($bln, 0, 3));
            $col++;
        }
        $sheet->mergeCells('N6:N7');
        $sheet->setCellValue('N6', 'RATA-RATA PERBAIKAN');

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
        foreach ($sumRosso as $row) {
            $kode_kartu = $row['employee_code'];
            if (!isset($groupedData[$kode_kartu])) {
                // Jika kode kartu belum ada, simpan data awal
                $groupedData[$kode_kartu] = [
                    'kode_kartu'    => $row['employee_code'],
                    'nama_karyawan' => $row['employee_name'],
                    'jenis_kelamin' => $row['gender'],
                    'tgl_masuk'     => $row['date_of_joining'],
                    'nama_bagian'   => $row['job_section_name'],
                    'produksi'      => array_fill_keys($bulan, 0), // Inisialisasi produksi per bulan
                    'perbaikan'     => array_fill_keys($bulan, 0), // Inisialisasi perbaikan per bulan
                    'hari_kerja'    => array_fill_keys($bulan, 0), // Inisialisasi hari kerja per bulan
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
                    $groupedData[$kode_kartu]['perbaikan'][$bulanData] += round($row['total_perbaikan'] / $jumlah_hari_kerja);
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

            // Kolom awal produksi dan Perbaikan
            $colProd = 'G';
            $colBS = 'K';
            $totalProduksi = 0;
            $totalBS = 0;
            $totalHariKerja = 0;
            $jumlahBulan = count($bulan);

            // Loop bulan untuk memasukkan produksi & perbaikan
            foreach ($bulan as $bln) {
                $produksiBulan = $data['produksi'][$bln];
                $bsBulan = $data['perbaikan'][$bln];
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

            // Hitung rata-rata produksi dan Perbaikan berdasarkan 3 bulan
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

        // get 3 karyawan dengan max average produksi dan min average perbaikan

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
        $sheet->setCellValue('X7', 'AVG PERBAIKAN');

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

        // header untuk top 3 min avg perbaikan
        $sheet->mergeCells('Z6:AG6');
        $sheet->setCellValue('Z6', 'TOP 3 MIN AVG PERBAIKAN');
        $sheet->getStyle('Z6')->getFont()->setBold(true);
        $sheet->getStyle('Z6')->getFont()->setName('Times New Roman');
        $sheet->getStyle('Z6')->getFont()->setSize(10);
        $sheet->getStyle('Z6')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('Z6')->getAlignment()->setVertical('center');
        $sheet->getStyle('Z6:AG6')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        // Sub-header untuk kolom Top 3 Min Avg Perbaikan
        $sheet->setCellValue('Z7', 'NO');
        $sheet->setCellValue('AA7', 'KODE KARTU');
        $sheet->setCellValue('AB7', 'NAMA KARYAWAN');
        $sheet->setCellValue('AC7', 'L/P');
        $sheet->setCellValue('AD7', 'TGL MASUK');
        $sheet->setCellValue('AE7', 'BAGIAN');
        $sheet->setCellValue('AF7', 'AVG PRODUKSI');
        $sheet->setCellValue('AG7', 'AVG PERBAIKAN');

        $sheet->getStyle('Z7:AG7')->getFont()->setBold(true);
        $sheet->getStyle('Z7:AG7')->getFont()->setName('Times New Roman');
        $sheet->getStyle('Z7:AG7')->getFont()->setSize(10);
        $sheet->getStyle('Z7:AG7')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('Z7:AG7')->getAlignment()->setVertical('center');
        $sheet->getStyle('Z7:AG7')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        $sheet->getStyle('Z7:AG7')->getAlignment()->setWrapText(true);


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
            $avgBS = array_sum($row['perbaikan']) / count($bulan);

            $sheet->setCellValue('Q' . $startRow, $no);
            $sheet->setCellValue('R' . $startRow, $row['kode_kartu']);
            $sheet->setCellValue('S' . $startRow, $row['nama_karyawan']);
            $sheet->setCellValue('T' . $startRow, $row['jenis_kelamin']);
            $sheet->setCellValue('U' . $startRow, $row['tgl_masuk']);
            $sheet->setCellValue('V' . $startRow, $row['nama_bagian']);
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

        // Data Top 3 Min Avg Perbaikan

        // Urutkan berdasarkan produksi tertinggi
        usort($groupedData, function ($a, $b) {
            return array_sum($b['produksi']) <=> array_sum($a['produksi']); // Descending
        });
        // Ambil 7 data produksi tertinggi
        $top7Produksi = array_slice($groupedData, 0, 7);

        // Urutkan 7 data ini berdasarkan Perbaikan terkecil
        usort($top7Produksi, function ($a, $b) {
            return array_sum($a['perbaikan']) <=> array_sum($b['perbaikan']); // Ascending
        });
        // Ambil 3 data Perbaikan terkecil dari Top 7 Produksi
        $top3BS = array_slice($top7Produksi, 0, 3);

        // $getMinAvgBS = $this->bsmcModel->getTop3LowestBS($area_utama, $id_batch);
        $startRow = 8;
        $no = 1;
        foreach ($top3BS as $row) {

            $avgProduksi = array_sum($row['produksi']) / count($bulan);
            $avgBS = array_sum($row['perbaikan']) / count($bulan);

            $sheet->setCellValue('Z' . $startRow, $no);
            $sheet->setCellValue('AA' . $startRow, $row['kode_kartu']);
            $sheet->setCellValue('AB' . $startRow, $row['nama_karyawan']);
            $sheet->setCellValue('AC' . $startRow, $row['jenis_kelamin']);
            $sheet->setCellValue('AD' . $startRow, $row['tgl_masuk']);
            $sheet->setCellValue('AE' . $startRow, $row['nama_bagian']);
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

        $spreadsheet->getActiveSheet()->setTitle('REPORT SUMMARY ROSSO');

        $filename = 'REPORT SUMMARY ROSSO ' . $area . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit;
    }

    public function filterRosso($area_utama)
    {
        // dd ($area_utama);
        $tgl_awal  = $this->request->getPost('tgl_awal');
        $tgl_akhir = $this->request->getPost('tgl_akhir');

        if (empty($tgl_awal) || empty($tgl_akhir)) {
            $tgl_awal  = date('Y-m-d');
            $tgl_akhir = date('Y-m-d');
        }
        $tgl_awal = '2025-05-01';
        $tgl_akhir = '2025-05-08';


        $rosso = $this->summaryRosso->getFilteredData($area_utama, $tgl_awal, $tgl_akhir);
        // dd ($tgl_awal, $tgl_akhir, $rosso);
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
            'area_utama' => $area_utama,
            'rosso' => $rosso
        ];

        return view('rosso/filter-rosso', $data);
    }

    public function exportSummaryRosso()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set header
        $sheet->setCellValue('A1', 'Kode Kartu');
        $sheet->setCellValue('B1', 'Nama Lengkap');
        $sheet->setCellValue('C1', 'Bagian');
        $sheet->setCellValue('D1', 'Area');
        $sheet->setCellValue('E1', 'Area Utama');
        $sheet->setCellValue('F1', 'Tgl Input');
        $sheet->setCellValue('G1', 'Produksi');
        $sheet->setCellValue('H1', 'BS');
        $sheet->setCellValue('I1', 'Created At');
        $sheet->setCellValue('J1', 'Updated At');
        $sheet->getStyle('A1:J1')->getFont()->setBold(true);
        $sheet->getStyle('A1:J1')->getFont()->setName('Times New Roman');
        $sheet->getStyle('A1:J1')->getFont()->setSize(10);
        $sheet->getStyle('A1:J1')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('A1:J1')->getAlignment()->setVertical('center');
        $sheet->getStyle('A1:J1')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        $sheet->getStyle('A1:J1')->getAlignment()->setWrapText(true);
        $sheet->getColumnDimension('A')->setWidth(10);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(20);
        $sheet->getColumnDimension('E')->setWidth(20);
        $sheet->getColumnDimension('F')->setWidth(20);
        $sheet->getColumnDimension('G')->setWidth(20);
        $sheet->getColumnDimension('H')->setWidth(20);
        $sheet->getColumnDimension('I')->setWidth(20);
        $sheet->getColumnDimension('J')->setWidth(20);

        // Get data from database
        $summaryRosso = $this->summaryRosso->getRossoData();
        // dd ($summaryRosso);
        $row = 2;

        foreach ($summaryRosso as $data) {
            $sheet->setCellValue('A' . $row, $data['kode_kartu']);
            $sheet->setCellValue('B' . $row, $data['nama_karyawan']);
            $sheet->setCellValue('C' . $row, $data['nama_bagian']);
            $sheet->setCellValue('D' . $row, $data['area']);
            $sheet->setCellValue('E' . $row, $data['area_utama']);
            $sheet->setCellValue('F' . $row, $data['tgl_input']);
            $sheet->setCellValue('G' . $row, $data['produksi']);
            $sheet->setCellValue('H' . $row, $data['perbaikan']);
            $sheet->setCellValue('I' . $row, $data['created_at']);
            $sheet->setCellValue('J' . $row, $data['updated_at']);

            // Set style for each row
            $sheet->getStyle('A' . $row . ':J' . $row)->getFont()->setName('Times New Roman');
            $sheet->getStyle('A' . $row . ':J' . $row)->getFont()->setSize(10);
            $sheet->getStyle('A' . $row . ':J' . $row)->getAlignment()->setHorizontal('center');
            $sheet->getStyle('A' . $row . ':J' . $row)->getAlignment()->setVertical('center');
            $sheet->getStyle('A' . $row . ':J' . $row)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            $sheet->getStyle('A' . $row . ':J' . $row)->getAlignment()->setWrapText(true);

            // Increment row number
            $row++;
        }
        // Set filename and download
        $filename = 'Summary_Rosso_' . date('d-m-Y') . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit;
    }

    // public function import()
    // {
    //     // 1. Validasi file upload
    //     $file = $this->request->getFile('file');
    //     if (!$file->isValid() || !in_array($file->getClientExtension(), ['xlsx'])) {
    //         return redirect()->back()->with('error', 'File tidak valid atau format tidak didukung. Pastikan berekstensi .xlsx');
    //     }
    //     // dd ($file);
    //     try {
    //         // 2. Load spreadsheet & mapping data reference
    //         $spreadsheet = IOFactory::load($file->getTempName());
    //         $sheetNames  = $spreadsheet->getSheetNames();
    //         $today       = date('Y-m-d');

    //         // 2a. Mapping karyawan: kode → [id, nama, factory]
    //         $karyawanMap = [];
    //         foreach ($this->employeeModel->findAll() as $row) {
    //             $code = strtolower(trim($row['employee_code']));
    //             $karyawanMap[$code] = [
    //                 'id_employee' => $row['id_employee'],
    //                 // Ubah key 'employee_name' jika berbeda di DB
    //                 'name'        => strtolower(trim($row['employee_name'])),
    //                 'id_factory'  => $row['id_factory'] ?? null,
    //             ];
    //         }
    //         // dd ($karyawanMap);
    //         // 2b. Mapping factory → nama area (sheet)
    //         $areaMap = [];
    //         foreach ($this->factoriesModel->findAll() as $row) {
    //             $areaMap[$row['id_factory']] = trim($row['main_factory']);
    //         }
    //         // dd ($areaMap);
    //         // 3. Inisialisasi penampung
    //         $toInsert     = [];
    //         $dupeExcel    = [];
    //         $dupeDb       = [];
    //         $invalidDates = [];
    //         $wrongAreas   = [];
    //         $seenEntries  = [];

    //         // 4. Loop tiap sheet & row
    //         foreach ($sheetNames as $sheetName) {
    //             $ws      = $spreadsheet->getSheetByName($sheetName);
    //             $lastRow = $ws->getHighestRow();

    //             for ($r = 2; $r <= $lastRow; $r++) {
    //                 $rawDate = $ws->getCell('A' . $r)->getFormattedValue();
    //                 $name    = strtolower(trim($ws->getCell('B' . $r)->getValue()));
    //                 $code    = strtolower(trim($ws->getCell('C' . $r)->getValue()));
    //                 $prod    = $ws->getCell('D' . $r)->getValue();
    //                 $rework  = $ws->getCell('E' . $r)->getValue();
    //                 // dd ($rawDate, $name, $code, $prod, $rework);
    //                 // Normalize & composite key
    //                 $tglInput = date('Y-m-d', strtotime(str_replace('/', '-', $rawDate)));
    //                 // dd($tglInput);
    //                 $key      = "{$name}|{$code}|{$tglInput}";

    //                 // 4a. Duplikat di Excel (name+code+date)
    //                 if (isset($seenEntries[$key])) {
    //                     $dupeExcel[] = "{$name} / {$code} ({$tglInput})";
    //                     continue;
    //                 }
    //                 $seenEntries[$key] = true;

    //                 // 4b. Validasi tanggal tidak > hari ini
    //                 if ($tglInput > $today) {
    //                     $invalidDates[] = "{$name} / {$code} ({$tglInput})";
    //                     continue;
    //                 }

    //                 // 4c. Validasi karyawan exist & nama cocok
    //                 if (!isset($karyawanMap[$code]) || $karyawanMap[$code]['name'] !== $name) {
    //                     continue;
    //                 }
    //                 $empl = $karyawanMap[$code];
    //                 $area = $areaMap[$empl['id_factory']] ?? null;

    //                 // 4d. Validasi sheetName sesuai area
    //                 if ($sheetName !== $area) {
    //                     $wrongAreas[] = "{$name} / {$code} (sheet: {$sheetName}, seharusnya: {$area})";
    //                     continue;
    //                 }

    //                 // 4e. Cek duplikat di DB (name+code+date)
    //                 $existing = $this->summaryRosso
    //                     ->select('sr.*')
    //                     ->from('rosso sr')
    //                     ->join('employees e', 'e.id_employee = sr.id_employee')
    //                     ->where('sr.input_date', $tglInput)
    //                     ->where('e.employee_code', $code)
    //                     ->where('LOWER(e.employee_name)', $name)
    //                     ->get()
    //                     ->getRow();
    //                 if ($existing) {
    //                     $dupeDb[] = "{$name} / {$code} ({$tglInput})";
    //                     continue;
    //                 }

    //                 // 4f. Siapkan data insert
    //                 $toInsert[] = [
    //                     'input_date'  => $tglInput,
    //                     'id_employee' => $empl['id_employee'],
    //                     'production'  => is_numeric($prod)   ? $prod   : 0,
    //                     'rework'      => is_numeric($rework) ? $rework : 0,
    //                     'id_factory'  => $empl['id_factory'],
    //                     'created_at'  => date('Y-m-d H:i:s'),
    //                     'updated_at'  => date('Y-m-d H:i:s'),
    //                 ];
    //             }
    //         }
    //         // dd ($toInsert);
    //         // 5. Transaksi & batch insert
    //         $db = \Config\Database::connect();
    //         $db->transBegin();
    //         if (!empty($toInsert)) {
    //             $this->summaryRosso->insertBatch($toInsert);
    //         }
    //         if ($db->transStatus() === false) {
    //             $db->transRollback();
    //             throw new \RuntimeException('Gagal menyimpan data ke database.');
    //         }
    //         $db->transCommit();

    //         // 6. Flash message
    //         $msgs = [];
    //         if ($toInsert) {
    //             $msgs[] = "✅ Berhasil import " . count($toInsert) . " record.";
    //         }
    //         if ($dupeExcel) {
    //             $msgs[] = "⛔ Duplikat di Excel: " . implode(', ', $dupeExcel);
    //         }
    //         if ($invalidDates) {
    //             $msgs[] = "⛔ Tanggal > hari ini: " . implode(', ', $invalidDates);
    //         }
    //         if ($dupeDb) {
    //             $msgs[] = "⛔ Sudah ada di DB: " . implode(', ', $dupeDb);
    //         }
    //         if ($wrongAreas) {
    //             $msgs[] = "⚠️ Salah sheet area: " . implode('; ', $wrongAreas);
    //         }

    //         $status = empty($dupeExcel) && empty($invalidDates) && empty($dupeDb) && empty($wrongAreas)
    //             ? 'success' : 'error';

    //         return redirect()->back()->with($status, implode("<br>", $msgs));
    //     } catch (\Exception $e) {
    //         return redirect()->back()->with('error', 'Error saat import: ' . $e->getMessage());
    //     }
    // }

    public function import()
    {
        $file = $this->request->getFile('file');
        if (!$file->isValid() || !in_array($file->getClientExtension(), ['xlsx'])) {
            return redirect()->back()->with('error', 'File tidak valid atau format tidak didukung. Pastikan berekstensi .xlsx');
        }

        try {
            $spreadsheet = IOFactory::load($file->getTempName());
            $sheetNames  = $spreadsheet->getSheetNames();
            $today       = date('Y-m-d');

            $karyawanMap = [];
            foreach ($this->employeeModel->findAll() as $row) {
                $code = strtolower(trim($row['employee_code']));
                $karyawanMap[$code] = [
                    'id_employee' => $row['id_employee'],
                    'name'        => strtolower(trim($row['employee_name'])),
                    'id_factory'  => $row['id_factory'] ?? null,
                ];
            }

            $areaMap = [];
            foreach ($this->factoriesModel->findAll() as $row) {
                $areaMap[$row['id_factory']] = trim($row['main_factory']);
            }

            $toInsert = [];
            $seenEntries = [];
            $logs = []; // Log error/sukses

            foreach ($sheetNames as $sheetName) {
                $ws = $spreadsheet->getSheetByName($sheetName);
                $lastRow = $ws->getHighestRow();

                for ($r = 2; $r <= $lastRow; $r++) {
                    try {
                        $rawDate = $ws->getCell('A' . $r)->getFormattedValue();
                        $name    = strtolower(trim($ws->getCell('B' . $r)->getValue()));
                        $code    = strtolower(trim($ws->getCell('C' . $r)->getValue()));
                        $prod    = $ws->getCell('D' . $r)->getValue();
                        $rework  = $ws->getCell('E' . $r)->getValue();

                        if (empty($rawDate) || empty($name) || empty($code)) {
                            $logs[] = "❌ [Baris $r / Sheet: $sheetName] Data kosong atau tidak lengkap.";
                            continue;
                        }

                        $tglInput = date('Y-m-d', strtotime(str_replace('/', '-', $rawDate)));
                        $key = "{$name}|{$code}|{$tglInput}";

                        if (isset($seenEntries[$key])) {
                            $logs[] = "⚠️ [Baris $r] Duplikat di Excel: {$name} / {$code} ({$tglInput})";
                            continue;
                        }
                        $seenEntries[$key] = true;

                        if ($tglInput > $today) {
                            $logs[] = "⚠️ [Baris $r] Tanggal lebih dari hari ini: {$tglInput}";
                            continue;
                        }

                        if (!isset($karyawanMap[$code]) || $karyawanMap[$code]['name'] !== $name) {
                            $logs[] = "❌ [Baris $r] Karyawan tidak ditemukan / nama tidak cocok: {$name} / {$code}";
                            continue;
                        }

                        $empl = $karyawanMap[$code];
                        $area = $areaMap[$empl['id_factory']] ?? null;

                        if ($sheetName !== $area) {
                            $logs[] = "⚠️ [Baris $r] Salah sheet area untuk {$name} / {$code}. Sheet: {$sheetName}, Seharusnya: {$area}";
                            continue;
                        }

                        // Duplikat di DB
                        $existing = $this->summaryRosso
                            ->select('sr.*')
                            ->from('rosso sr')
                            ->join('employees e', 'e.id_employee = sr.id_employee')
                            ->where('sr.input_date', $tglInput)
                            ->where('e.employee_code', $code)
                            ->where('LOWER(e.employee_name)', $name)
                            ->get()
                            ->getRow();

                        if ($existing) {
                            $logs[] = "⚠️ [Baris $r] Data sudah ada di DB: {$name} / {$code} ({$tglInput})";
                            continue;
                        }

                        $toInsert[] = [
                            'input_date'  => $tglInput,
                            'id_employee' => $empl['id_employee'],
                            'production'  => is_numeric($prod)   ? $prod   : 0,
                            'rework'      => is_numeric($rework) ? $rework : 0,
                            'id_factory'  => $empl['id_factory'],
                            'created_at'  => date('Y-m-d H:i:s'),
                            'updated_at'  => date('Y-m-d H:i:s'),
                        ];
                    } catch (\Throwable $e) {
                        $logs[] = "❌ [Baris $r] Error parsing data: " . $e->getMessage();
                        continue;
                    }
                }
            }

            $db = \Config\Database::connect();
            $db->transBegin();
            if (!empty($toInsert)) {
                $this->summaryRosso->insertBatch($toInsert);
            }

            if ($db->transStatus() === false) {
                $db->transRollback();
                throw new \RuntimeException('Gagal menyimpan data ke database.');
            }
            $db->transCommit();

            $logs[] = "✅ Berhasil import: " . count($toInsert) . " data.";

            return redirect()->back()->with('info', implode("<br>", $logs));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat import: ' . $e->getMessage());
        }
    }


    public function downloadTemplate()
    {
        // Membuat spreadsheet baru
        $spreadsheet = new Spreadsheet();

        $titles = ['KK1', 'KK2', 'KK5', 'KK7', 'KK8', 'KK11'];
        foreach ($titles as $index => $title) {
            $sheet = $index === 0 ? $spreadsheet->getActiveSheet() : $spreadsheet->createSheet();
            $sheet->setTitle($title);

            // Menyusun header kolom
            $sheet->setCellValue('A1', 'TANGGAL');
            $sheet->setCellValue('B1', 'NAMA LENGKAP');
            $sheet->setCellValue('C1', 'KODE KARTU');
            $sheet->setCellValue('D1', 'PRODUKSI');
            $sheet->setCellValue('E1', 'PERBAIKAN');

            // Mengatur lebar kolom
            $sheet->getColumnDimension('A')->setWidth(20);
            $sheet->getColumnDimension('B')->setWidth(20);
            $sheet->getColumnDimension('C')->setWidth(20);
            $sheet->getColumnDimension('D')->setWidth(20);
            $sheet->getColumnDimension('E')->setWidth(20);

            // Mengatur style header
            $sheet->getStyle('A1:E1')->getFont()->setBold(true);
            $sheet->getStyle('A1:E1')->getFill()->setFillType('solid')->getStartColor()->setARGB('FFA0A0A0');
            $sheet->getStyle('A1:E1')->getAlignment()->setHorizontal('center');

            // Isi data contoh pada sheet pertama
            if ($index === 0) {
                $sheet->setCellValue('A2', '2025-01-25');
                $sheet->setCellValue('B2', 'John Doe');
                $sheet->setCellValue('C2', 'KK0001');
                $sheet->setCellValue('D2', '1000');
                $sheet->setCellValue('E2', '100');
            }
        }

        // Menentukan nama file
        $fileName = 'Template_Summary_Rosso.xlsx';

        // Set header untuk unduhan file
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"{$fileName}\"");
        header('Cache-Control: max-age=0');

        // Buat file Excel dan kirim sebagai unduhan
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}
