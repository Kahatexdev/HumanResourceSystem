<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Database\Migrations\Shiftdef;
use CodeIgniter\HTTP\ResponseInterface;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

use App\Models\UserModel;
use App\Models\EmployeeModel;
use App\Models\ShiftDefModel;
use App\Models\ShiftAssignmentsModel;


class ShiftController extends BaseController
{
    protected $userM;
    protected $emPM;
    protected $shiftDeftM;
    protected $shiftAssignM;
    protected $db;
    protected $role;

    public function __construct()
    {
        $this->userM = new UserModel();
        $this->emPM = new EmployeeModel();
        $this->shiftDeftM = new ShiftDefModel();
        $this->shiftAssignM = new ShiftAssignmentsModel();
        $this->db = \Config\Database::connect();
        $this->role = session()->get('role');
    }

    public function index()
    {
        $shiftData = $this->shiftAssignM->getDataShift();
        $data = [
            'role'      => session()->get('role'),
            'title'     => 'Shift Assignments',
            'active1'   => '',
            'active2'   => '',
            'active3'   => 'active',
            'shift'     => $shiftData
        ];
        return view(session()->get('role') . '/shiftAssignments.php', $data);
    }

    public function getEmployeeNames()
    {
        // ambil keyword dari Select2 (param: ?q=...)
        $term = $this->request->getGet('q') ?? '';

        $builder = $this->db->table('employees');
        $builder->select("
        id_employee AS id,
        CONCAT(employee_code, ' - ', employee_name) AS text
    ");

        if (!empty($term)) {
            $builder->groupStart()
                ->like('employee_code', $term)
                ->orLike('employee_name', $term)
                ->groupEnd();
        }

        $builder->orderBy('employee_name', 'ASC')
            ->limit(20);

        $results = $builder->get()->getResult();

        // format sesuai Select2: { results: [ { id, text }, ... ] }
        return $this->response->setJSON([
            'results' => $results
        ]);
    }

    public function storeShiftAssignment()
    {
        $employeeIds    = (array) $this->request->getPost('employee_ids'); // array of id_employee
        $startTimes     = (array) $this->request->getPost('start_time');   // array
        $endTimes       = (array) $this->request->getPost('end_time');     // array
        $breakTimes     = (array) $this->request->getPost('break_time');   // array
        $graceMins      = (array) $this->request->getPost('grace_min');    // array
        $effectiveDate  = $this->request->getPost('effective_date') ?: date('Y-m-d');
        $notes          = $this->request->getPost('note') ?: null;

        $errors = [];

        // --- Validasi dasar ---
        if (empty(array_filter($employeeIds))) {
            $errors[] = 'Minimal pilih satu karyawan.';
        }

        if (empty(array_filter($startTimes))) {
            $errors[] = 'Minimal isi satu pola jam kerja.';
        }

        if (! empty($errors)) {
            // error utama + detail list (dipakai oleh JS swal)
            return redirect()->back()
                ->withInput()
                ->with('error', 'Input belum lengkap. Mohon periksa kembali formulir yang Anda isi.')
                ->with('error_detail', $errors);
        }

        $dataInsert = [];
        $notFound   = []; // kombinasi jam kerja yang tidak ada di master

        foreach ($employeeIds as $idEmp) {
            if (empty($idEmp)) {
                continue;
            }

            foreach ($startTimes as $idx => $start) {
                $start = trim((string) $start);
                $end   = trim((string) ($endTimes[$idx] ?? ''));

                // skip baris yang incomplete
                if ($start === '' || $end === '') {
                    continue;
                }

                $break = (int) ($breakTimes[$idx] ?? 0);
                $grace = (int) ($graceMins[$idx] ?? 0);

                // cari id_shift di master shift_defs
                $shiftMaster = $this->shiftDeftM
                    ->where('start_time', $start)
                    ->where('end_time', $end)
                    ->where('break_time', $break)
                    ->where('grace_min', $grace)
                    ->first();

                if (! $shiftMaster) {
                    // kumpulkan kombinasi jam kerja yang tidak terdaftar
                    $notFound[] = "Masuk {$start}, Pulang {$end}, Istirahat {$break} menit, Toleransi {$grace} menit";
                    continue;
                }

                $dataInsert[] = [
                    'id_employee'    => (int) $idEmp,
                    'id_shift'       => (int) $shiftMaster['id_shift'],
                    'date_of_change' => $effectiveDate,
                    'note'           => $notes,
                    'created_by'     => session()->get('id_user'),
                ];
            }
        }

        // --- Jika ada jam kerja yang tidak terdaftar di master ---
        if (! empty($notFound)) {
            $notFound = array_values(array_unique($notFound)); // buang duplikat & reindex

            return redirect()->back()
                ->withInput()
                ->with('error', 'Beberapa jam kerja belum terdaftar di Master Jam Kerja.')
                ->with('error_detail', $notFound);
        }

        // --- Jika setelah diproses, tidak ada data valid ---
        if (empty($dataInsert)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Tidak ada data jam kerja yang valid untuk disimpan. Periksa kembali jam masuk & jam pulang.')
                ->with('error_detail', [
                    'Pastikan setiap jam masuk memiliki jam pulang.',
                    'Pastikan kombinasi jam kerja sudah terdaftar di Master Jam Kerja.'
                ]);
        }

        // --- Simpan ke database ---
        try {
            $shiftAssignModel = new \App\Models\ShiftAssignmentsModel();
            $shiftAssignModel->insertBatch($dataInsert);
        } catch (\Throwable $e) {
            log_message('error', '[ShiftAssignment] Gagal insertBatch: {msg}', [
                'msg' => $e->getMessage(),
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat menyimpan data shift.')
                ->with('error_detail', [
                    'Silakan coba lagi beberapa saat lagi.',
                    'Jika masalah berlanjut, hubungi tim IT.'
                ]);
        }

        // gunakan unique employees untuk pesan sukses
        $totalKaryawan = count(array_unique(array_filter($employeeIds)));

        return redirect()->back()
            ->with('success', 'Shift / jam kerja berhasil disimpan untuk ' . $totalKaryawan . ' karyawan.');
    }


    public function downloadTemplate()
    {
        // 1) Buat spreadsheet & sheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Jam Kerja');

        // 2) Header kolom
        $headers = ['A1' => 'NIK', 'B1' => 'Kode Kartu', 'C1' => 'Nama Karyawan', 'D1' => 'Shift', 'E1' => 'Masuk (HH:mm)', 'F1' => 'Pulang (HH:mm)', 'G1' => 'Istirahat (menit)'];
        foreach ($headers as $cell => $text) {
            $sheet->setCellValue($cell, $text);
        }

        // 3) Lebar kolom
        $sheet->getColumnDimension('A')->setWidth(20);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(28);
        $sheet->getColumnDimension('D')->setWidth(12);
        $sheet->getColumnDimension('E')->setWidth(16);
        $sheet->getColumnDimension('F')->setWidth(16);
        $sheet->getColumnDimension('G')->setWidth(18);

        // 4) Styling header
        $sheet->getStyle('A1:G1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF3B82F6']], // biru
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFCBD5E1']]]
        ]);
        $sheet->getRowDimension(1)->setRowHeight(36);
        $sheet->setAutoFilter('A1:G1');
        $sheet->freezePane('A2');

        // 5) Format kolom
        // A & B sebagai TEXT (agar 0 di depan tidak hilang)
        $sheet->getStyle('A2:A10000')->getNumberFormat()->setFormatCode('@');
        $sheet->getStyle('B2:B10000')->getNumberFormat()->setFormatCode('@');
        // E & F Time HH:mm
        $sheet->getStyle('E2:F10000')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_DATE_TIME3); // HH:mm
        // G angka (menit)
        $sheet->getStyle('G2:G10000')->getNumberFormat()->setFormatCode('0');

        // 6) Data Validation untuk kolom Shift (D): A,B,C,NS
        $validation = $sheet->getCell('D2')->getDataValidation();
        $validation->setType(DataValidation::TYPE_LIST);
        $validation->setErrorStyle(DataValidation::STYLE_STOP);
        $validation->setAllowBlank(false);
        $validation->setShowInputMessage(true);
        $validation->setShowErrorMessage(true);
        $validation->setShowDropDown(true);
        $validation->setErrorTitle('Input tidak valid');
        $validation->setError('Pilih salah satu dari daftar.');
        $validation->setPromptTitle('Pilih Shift');
        $validation->setPrompt('Pilih A, B, C, atau NS');
        $validation->setFormula1('"A,B,C,NS"');
        // Copy DV sampai baris 10000
        for ($r = 3; $r <= 10000; $r++) {
            $sheet->getCell("D{$r}")->setDataValidation(clone $validation);
        }

        // 7) Contoh isi baris (benar dan konsisten)
        // NIK & Kode Kartu sebagai TEXT agar tidak diubah Excel
        $sheet->setCellValueExplicit('A2', 'TESNIK25', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
        $sheet->setCellValueExplicit('B2', 'KK001', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
        $sheet->setCellValue('C2', 'John Doe');
        $sheet->setCellValue('D2', 'A');
        // Masuk 07:00, Pulang 16:00, Istirahat 60 menit
        $sheet->setCellValue('E2', \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel(\DateTime::createFromFormat('H:i', '07:00')));
        $sheet->setCellValue('F2', \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel(\DateTime::createFromFormat('H:i', '16:00')));
        $sheet->setCellValue('G2', 60);

        // 8) Border tipis untuk area kerja default (A1:G100)
        $sheet->getStyle('A1:G100')->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FFE5E7EB']
                ]
            ]
        ]);

        // 9) Catatan bantuan (komentar kecil di header)
        $sheet->getComment('A1')->getText()->createTextRun("Format text. Contoh: 000123");
        $sheet->getComment('B1')->getText()->createTextRun("Format text. Contoh: KK001");
        $sheet->getComment('D1')->getText()->createTextRun("Pilih A/B/C/NS");
        $sheet->getComment('E1')->getText()->createTextRun("Format jam HH:mm (misal 07:00)");
        $sheet->getComment('F1')->getText()->createTextRun("Format jam HH:mm (misal 16:00)");
        $sheet->getComment('G1')->getText()->createTextRun("Total menit istirahat (misal 60)");

        // 10) Output
        $fileName = 'Template_Jam_Kerja.xlsx';
        // (opsional) set properties
        $spreadsheet->getProperties()
            ->setCreator('HRS')
            ->setTitle($fileName)
            ->setDescription('Template import jam kerja karyawan');

        // Header unduhan
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public function storeUploadTemplate()
    {
        $requestId = bin2hex(random_bytes(6)); // untuk korelasi log satu proses
        $rolePath  = $this->role . '/shiftAssignments';

        $effectiveDate = $this->request->getPost('effective_date') ?: date('Y-m-d');
        $noteDefault   = $this->request->getPost('note') ?: null;
        $userId        = session()->get('id_user') ?? null;

        // log_message('info', '[{rid}] START import shift template | effectiveDate={date} userId={uid}', [
        //     'rid'  => $requestId,
        //     'date' => $effectiveDate,
        //     'uid'  => $userId,
        // ]);

        $file = $this->request->getFile('file');
        if (!$file || !$file->isValid() || $file->hasMoved()) {
            // log_message('error', '[{rid}] Upload check failed | valid={valid} moved={moved}', [
            //     'rid'   => $requestId,
            //     'valid' => $file ? $file->isValid() : false,
            //     'moved' => $file ? $file->hasMoved() : null,
            // ]);
            return redirect()->to(base_url($rolePath))
                ->with('error', 'File tidak valid atau tidak ada file yang diunggah.');
        }

        // Validasi MIME
        $mime = $file->getClientMimeType();
        // log_message('debug', '[{rid}] Detected MIME: {mime}', ['rid' => $requestId, 'mime' => $mime]);

        if (!in_array($mime, [
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        ], true)) {
            // log_message('error', '[{rid}] Invalid MIME type blocked: {mime}', ['rid' => $requestId, 'mime' => $mime]);
            return redirect()->to(base_url($rolePath))
                ->with('error', 'Invalid file type. Harus Excel .xls/.xlsx');
        }

        // Load Excel
        try {
            $spreadsheet = IOFactory::load($file->getTempName());
        } catch (\Throwable $e) {
            // log_message('error', '[{rid}] Failed to load spreadsheet: {msg}', [
            //     'rid' => $requestId,
            //     'msg' => $e->getMessage(),
            // ]);
            return redirect()->to(base_url($rolePath))
                ->with('error', 'Gagal membaca file Excel: ' . $e->getMessage());
        }

        $sheet    = $spreadsheet->getActiveSheet();
        $startRow = 2;

        $success = 0;
        $updated = 0;
        $failed  = 0;
        $errors  = [];

        $this->db->transBegin();
        // log_message('debug', '[{rid}] DB transaction begin', ['rid' => $requestId]);

        try {
            foreach ($sheet->getRowIterator($startRow) as $row) {
                $r = $row->getRowIndex();

                // Ambil nilai (getFormattedValue untuk jaga leading zero)
                $nik          = trim((string) $sheet->getCell("A{$r}")->getFormattedValue());
                $cardCode     = trim((string) $sheet->getCell("B{$r}")->getFormattedValue());
                $employeeName = trim((string) $sheet->getCell("C{$r}")->getFormattedValue());
                $shiftCode    = strtoupper(trim((string) $sheet->getCell("D{$r}")->getFormattedValue()));
                $inCell       = $sheet->getCell("E{$r}")->getValue();
                $outCell      = $sheet->getCell("F{$r}")->getValue();
                $breakCell    = $sheet->getCell("G{$r}")->getValue();

                // Baris kosong?
                if ($nik === '' && $cardCode === '' && $employeeName === '' && $shiftCode === '') {
                    // log_message('debug', '[{rid}] R{row}: skipped empty row', ['rid' => $requestId, 'row' => $r]);
                    continue;
                }

                // log_message('debug', '[{rid}] R{row}: raw values nik={nik} card={card} name={name} shiftCode={shift}', [
                //     'rid'   => $requestId,
                //     'row'   => $r,
                //     'nik'   => $nik,
                //     'card'  => $cardCode,
                //     'name'  => $employeeName,
                //     'shift' => $shiftCode,
                // ]);

                $rowErr = [];

                // --- Resolusi employee ---
                $emp = null;
                if ($nik !== '') {
                    $emp = $this->emPM->where('nik', $nik)->first();
                    // log_message('debug', '[{rid}] R{row}: lookup by NIK -> {found}', [
                    //     'rid' => $requestId,
                    //     'row' => $r,
                    //     'found' => $emp ? 'FOUND' : 'NOT_FOUND'
                    // ]);
                }
                if (!$emp && $cardCode !== '') {
                    $emp = $this->emPM->where('employee_code', $cardCode)->first();
                    // log_message('debug', '[{rid}] R{row}: lookup by cardCode -> {found}', [
                    //     'rid' => $requestId,
                    //     'row' => $r,
                    //     'found' => $emp ? 'FOUND' : 'NOT_FOUND'
                    // ]);
                }
                if (!$emp) {
                    $msg = "R{$r}: Karyawan tidak ditemukan (NIK='{$nik}', Kartu='{$cardCode}').";
                    $rowErr[] = $msg;
                    // log_message('warning', '[{rid}] {msg}', ['rid' => $requestId, 'msg' => $msg]);
                }

                // --- Validasi/normalisasi jam dari Excel ---
                $parsedIn  = self::excelTimeToHHmm($inCell);
                $parsedOut = self::excelTimeToHHmm($outCell);
                $parsedBrk = self::parseBreakToInt($breakCell); // menit atau null

                // log_message('debug', '[{rid}] R{row}: parsed times in={in} out={out} break(min)={brk}', [
                //     'rid' => $requestId,
                //     'row' => $r,
                //     'in' => $parsedIn,
                //     'out' => $parsedOut,
                //     'brk' => $parsedBrk
                // ]);

                $noteWarn = [];
                // Lookup master shift (full match); kalau tidak ketemu, coba by name saja
                $shiftMaster = $this->shiftDeftM->where('shift_name', $shiftCode)
                    ->where('start_time', $parsedIn)
                    ->where('end_time', $parsedOut)
                    ->where('break_time', $parsedBrk ?? 0)
                    ->first();

                // if (!$shiftMaster) {
                //     $noteWarn[] = "Jam kerja tidak sesuai master shift.";
                //     // log_message('warning', '[{rid}] R{row}: exact shift match NOT FOUND, trying by name only', [
                //     //     'rid' => $requestId,
                //     //     'row' => $r
                //     // ]);
                //     // $shiftMaster = $this->shiftDeftM->where('id_shift', $shiftCode)->first();
                // }

                if (!$shiftMaster) {
                    $msg = "R{$r}: Master shift '{$shiftCode}' tidak ditemukan.";
                    $rowErr[] = $msg;
                    // log_message('error', '[{rid}] {msg}', ['rid' => $requestId, 'msg' => $msg]);
                }

                if (!empty($rowErr)) {
                    $failed++;
                    $errors = array_merge($errors, $rowErr);
                    continue;
                }

                // --- Upsert per (id_employee, date_of_change) ---
                $payload = [
                    'id_employee'    => (int)$emp['id_employee'],
                    'id_shift'       => (int)$shiftMaster['id_shift'],
                    'date_of_change' => $effectiveDate,
                    'note'           => self::composeNote($noteDefault, $noteWarn, $employeeName, $nik, $cardCode),
                    'created_by'     => $userId,
                ];

                // log_message('debug', '[{rid}] R{row}: payload -> {payload}', [
                //     'rid' => $requestId,
                //     'row' => $r,
                //     'payload' => json_encode($payload)
                // ]);

                // cek existing
                $existing = $this->shiftAssignM->where('id_employee', $payload['id_employee'])
                    ->where('id_shift', $payload['id_shift'])
                    ->where('date_of_change', $effectiveDate)
                    ->first();

                if ($existing) {
                    // NOTE: perbaiki key 'id_assignment' (hilangkan spasi)
                    $assignmentId = $existing['id_assignment'] ?? null;
                    $ok = $this->shiftAssignM->update($assignmentId, [
                        'id_shift'       => $payload['id_shift'],
                        'date_of_change' => $payload['date_of_change'],
                        'created_by'     => $payload['created_by'],
                        'note'           => $payload['note'],
                        'updated_at'     => date('Y-m-d H:i:s'),
                    ]);
                    if ($ok === false) {
                        $failed++;
                        $err = $this->shiftAssignM->errors();
                        // log_message('error', '[{rid}] R{row}: UPDATE failed id={id} | errors={errs}', [
                        //     'rid' => $requestId,
                        //     'row' => $r,
                        //     'id' => $assignmentId,
                        //     'errs' => json_encode($err)
                        // ]);
                        $errors[] = "R{$r}: Gagal update assignment (id={$assignmentId}).";
                    } else {
                        $updated++;
                        // log_message('info', '[{rid}] R{row}: UPDATE ok id={id}', [
                        //     'rid' => $requestId,
                        //     'row' => $r,
                        //     'id' => $assignmentId
                        // ]);
                    }
                } else {
                    $newId = $this->shiftAssignM->insert($payload, true);
                    if (!$newId) {
                        $failed++;
                        $err = $this->shiftAssignM->errors();
                        // log_message('error', '[{rid}] R{row}: INSERT failed | errors={errs}', [
                        //     'rid' => $requestId,
                        //     'row' => $r,
                        //     'errs' => json_encode($err)
                        // ]);
                        $errors[] = "R{$r}: Gagal insert assignment baru.";
                    } else {
                        $success++;
                        // log_message('info', '[{rid}] R{row}: INSERT ok id={id}', [
                        //     'rid' => $requestId,
                        //     'row' => $r,
                        //     'id' => $newId
                        // ]);
                    }
                }
            }

            if ($this->db->transStatus() === false) {
                // log_message('error', '[{rid}] Transaction status FALSE sebelum commit', ['rid' => $requestId]);
                throw new \RuntimeException('DB transaction failed.');
            }

            $this->db->transCommit();
            // log_message('info', '[{rid}] COMMIT ok | success={s} updated={u} failed={f}', [
            //     'rid' => $requestId,
            //     's' => $success,
            //     'u' => $updated,
            //     'f' => $failed
            // ]);
        } catch (\Throwable $e) {
            $this->db->transRollback();
            // log_message('critical', '[{rid}] ROLLBACK: {msg} | trace={trace}', [
            //     'rid'   => $requestId,
            //     'msg'   => $e->getMessage(),
            //     'trace' => $e->getTraceAsString(),
            // ]);

            return redirect()->to(base_url($rolePath))
                ->with('error', 'Import gagal: ' . $e->getMessage());
        }

        // Rangkuman + sebagian error
        $summary = "Import selesai. Baru: {$success}, Update: {$updated}, Gagal: {$failed}.";
        if (!empty($errors)) {
            $firstErrors = array_slice($errors, 0, 10);
            session()->setFlashdata('error_detail', $firstErrors); // simpan array
            // log_message('warning', '[{rid}] Partial errors (first 10): {errs}', [
            //     'rid'  => $requestId,
            //     'errs' => json_encode($firstErrors),
            // ]);
        } else {
            // log_message('info', '[{rid}] No row-level errors recorded', ['rid' => $requestId]);
        }

        // log_message('info', '[{rid}] END import shift template | {sum}', [
        //     'rid' => $requestId,
        //     'sum' => $summary
        // ]);

        return redirect()->to(base_url($rolePath))
            ->with('success', $summary);
    }

    /**
     * Ubah nilai Excel (serial/tulisan) menjadi 'HH:mm' atau null.
     */
    private static function excelTimeToHHmm($cellValue): ?string
    {
        if ($cellValue === null || $cellValue === '') return null;

        // Jika numeric serial Excel
        if (is_numeric($cellValue)) {
            // 24h * 60min = 1440; Excel menyimpan waktu sebagai fraksi hari
            $seconds = (int) round($cellValue * 24 * 60 * 60);
            $h = floor($seconds / 3600) % 24;
            $m = floor(($seconds % 3600) / 60);
            return sprintf('%02d:%02d', $h, $m);
        }

        // Jika string waktu
        $str = trim((string)$cellValue);
        // normalisasi 7:0, 07.00, 07-00, 07 00
        $str = str_replace(['.', '-', ' '], ':', $str);
        if (preg_match('/^(\d{1,2}):(\d{1,2})$/', $str, $m)) {
            return sprintf('%02d:%02d', (int)$m[1], (int)$m[2]);
        }
        return null;
    }

    private static function parseBreakToInt($val): ?int
    {
        if ($val === null || $val === '') return null;
        if (is_numeric($val)) return (int) $val;
        // kalau string "60" atau "60 menit"
        if (preg_match('/(\d+)/', (string)$val, $m)) {
            return (int)$m[1];
        }
        return null;
    }

    /**
     * Compose catatan: note default + warning + identitas baris (opsional).
     */
    private static function composeNote(?string $noteDefault, array $warns, string $name, string $nik, string $card): ?string
    {
        $parts = [];
        if ($noteDefault) $parts[] = $noteDefault;
        if (!empty($warns)) $parts[] = 'Validasi: ' . implode(', ', $warns);
        // opsional: identitas
        // $parts[] = trim("{$name} {$nik} {$card}");
        return empty($parts) ? null : implode(' | ', $parts);
    }

    public function shiftDef()
    {
        $shifData = $this->shiftDeftM->findAll();
        $data = [
            'role'      => session()->get('role'),
            'title'     => 'Master Jam Kerja',
            'active1'   => '',
            'active2'   => '',
            'active3'   => 'active',
            'shiftDef'     => $shifData
        ];
        return view(session()->get('role') . '/Master/shiftDef.php', $data);
    }

    public function shiftMasterStore()
    {
        $shift = $this->request->getPost();

        $exist = $this->shiftDeftM->where('shift_name', $shift['shift_name'],)
                                  ->where('start_time', $shift['start_time'],)
                                  ->where('end_time', $shift['end_time'],)
                                  ->where('grace_min', $shift['grace_min'],)
                                  ->first();

        if ($exist) {
            return redirect()->back()->with('error', 'Data jam kerja sudah ada sebelumnya.');
        } else {
            $this->shiftDeftM->insert($shift);
            return redirect()->back()->with('success', 'Data jam kerja berhasil ditambahkan.');
        }
    }

    
}
