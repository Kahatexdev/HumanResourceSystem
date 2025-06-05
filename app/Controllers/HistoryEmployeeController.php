<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

use App\Models\HistoryEmployeeModel;
use App\Models\EmployeeModel;
use App\Models\JobSectionModel;
use App\Models\JobRoleModel;
use App\Models\MainJobRoleModel;
use App\Models\BatchModel;
use App\Models\PeriodeModel;
use App\Models\PresenceModel;
use App\Models\EmployeeAssessmentModel;
use App\Models\PerformanceAssessmentModel;
use App\Models\FactoriesModel;
use App\Models\DayModel;
use App\Models\EmploymentStatusModel;
use App\Models\UserModel;
use CodeIgniter\Database\Exceptions\DataException;
use CodeIgniter\Database\Exceptions\DatabaseException;
use PhpOffice\PhpSpreadsheet\IOFactory;

class HistoryEmployeeController extends BaseController
{
    protected $historyEmployeeModel;
    protected $employeeModel;
    protected $jobSectionModel;
    protected $jobRoleModel;
    protected $mainJobRoleModel;
    protected $batchModel;
    protected $periodeModel;
    protected $presenceModel;
    protected $employeeAssessmentModel;
    protected $performanceAssessmentModel;
    protected $factoriesModel;
    protected $days;
    protected $employmentStatusModel;
    protected $userModel;
    protected $role;
    protected $db;
    public function __construct()
    {
        $this->historyEmployeeModel = new HistoryEmployeeModel();
        $this->employeeModel = new EmployeeModel();
        $this->jobSectionModel = new JobSectionModel();
        $this->jobRoleModel = new JobRoleModel();
        $this->mainJobRoleModel = new MainJobRoleModel();
        $this->batchModel = new BatchModel();
        $this->periodeModel = new PeriodeModel();
        $this->presenceModel = new PresenceModel();
        $this->employeeAssessmentModel = new EmployeeAssessmentModel();
        $this->performanceAssessmentModel = new PerformanceAssessmentModel();
        $this->factoriesModel = new FactoriesModel();
        $this->days = new DayModel();
        $this->employmentStatusModel = new EmploymentStatusModel();
        $this->userModel = new UserModel();
        $this->db = \Config\Database::connect();
        $this->role = session()->get('role');
    }
    public function index()
    {
        //
    }

    public function importHistoryEmployee()
    {
        $file = $this->request->getFile('file');
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getTempName());
        $worksheet = $spreadsheet->getActiveSheet();
        $data = [];

        foreach ($worksheet->getRowIterator() as $row) {
            $rowData = [];
            foreach ($row->getCellIterator() as $cell) {
                $rowData[] = $cell->getValue();
            }

            if ($row->getRowIndex() < 4) continue;
            if ($row->getRowIndex() >= $worksheet->getHighestRow() - 1) break;

            $data[] = [
                'employee_code' => $rowData[1],
                'employee_name' => $rowData[2],
                'job_section_name_old' => $rowData[11],
                'factory_name_old' => $rowData[12],
                'job_section_name' => $rowData[13],
                'factory_name' => $rowData[14],
                'date_of_change' => $rowData[15],
                'reason' => $rowData[16],
                'username' => $rowData[18],
            ];
        }

        $insertData = [];
        $skippedRows = [];

        foreach ($data as $index => $row) {
            $employee = $this->employeeModel
                ->where('employee_code', $row['employee_code'])
                ->orWhere('employee_name', $row['employee_name'])
                ->first();

            $jobSectionOld = $this->jobSectionModel->where('job_section_name', $row['job_section_name_old'])->first();
            $factoryOld = $this->factoriesModel->where('factory_name', $row['factory_name_old'])->first();
            $jobSectionNew = $this->jobSectionModel->where('job_section_name', $row['job_section_name'])->first();
            $factoryNew = $this->factoriesModel->where('factory_name', $row['factory_name'])->first();
            $idUser = $this->userModel->where('username', $row['username'])->first();

            // Skip jika salah satu data tidak ditemukan
            if (!$employee || !$jobSectionOld || !$factoryOld || !$jobSectionNew || !$factoryNew) {
                $skippedRows[] = $row; // Bisa juga disimpan untuk laporan
                continue;
            }

            $insertData[] = [
                'id_employee' => $employee['id_employee'],
                'id_job_section_old' => $jobSectionOld['id_job_section'],
                'id_factory_old' => $factoryOld['id_factory'],
                'id_job_section_new' => $jobSectionNew['id_job_section'],
                'id_factory_new' => $factoryNew['id_factory'],
                'date_of_change' => $row['date_of_change'],
                'reason' => $row['reason'],
                'id_user' => $idUser['id_user'],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];
        }
        // dd ($data,$insertData, $skippedRows);
        // Simpan ke database
        if (!empty($insertData)) {
            $this->historyEmployeeModel->insertBatch($insertData);
            session()->setFlashdata('success', 'Data history karyawan berhasil diimpor.');
        } else {
            session()->setFlashdata('error', 'Tidak ada data yang valid untuk diimpor.');
        }
        // Jika ada baris yang dilewati, simpan untuk laporan
        if (!empty($skippedRows)) {
            session()->setFlashdata('skipped_rows', $skippedRows);
        }
        return redirect()->back()
            ->with('success', 'Data history karyawan berhasil diimpor.')
            ->with('skipped_rows', $skippedRows);
    }

    /**
     * Format date from Excel or string to Y-m-d format.
     */
    private function formattedDate($date)
    {
        if (empty($date)) {
            return null;
        }
        // If the date is a numeric value (Excel date)
        if (is_numeric($date)) {
            return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($date)->format('Y-m-d');
        }
        // Try to parse as string date
        $timestamp = strtotime($date);
        if ($timestamp !== false) {
            return date('Y-m-d', $timestamp);
        }
        return null;
    }

    // public function updateEmployeeCode()
    // {
    //     // 1. Ambil data dari Excel
    //     $file = $this->request->getFile('file');
    //     $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getTempName());
    //     $worksheet = $spreadsheet->getActiveSheet();

    //     $data = [];
    //     foreach ($worksheet->getRowIterator() as $row) {
    //         $rowIndex = $row->getRowIndex();
    //         if ($rowIndex < 4) {
    //             // Lewatkan header (baris 1–3)
    //             continue;
    //         }

    //         $cellIterator = $row->getCellIterator();
    //         $cellIterator->setIterateOnlyExistingCells(true);

    //         $rowData = [];
    //         foreach ($cellIterator as $cell) {
    //             $rowData[] = $cell->getValue();
    //         }

    //         // Pastikan setidaknya ada kolom 1..5
    //         // [0] kosong (karena kolom A biasanya nomor), [1]=old, [2]=new, [3]=name, [4]=dob, [5]=doj
    //         if (! isset($rowData[1], $rowData[2], $rowData[3], $rowData[4], $rowData[5])) {
    //             continue;
    //         }

    //         $data[] = [
    //             'employee_code_old'  => trim($rowData[1]),
    //             'employee_code_new'  => trim($rowData[2]),
    //             'employee_name'      => trim($rowData[3]),
    //             'date_of_birth'      => $this->formattedDate($rowData[4]),
    //             'date_of_joining'    => $this->formattedDate($rowData[5]),
    //             'row_index'          => $rowIndex,  // untuk referensi jika error
    //         ];
    //     }

    //     // 2. Siapkan counter dan array untuk detail error
    //     $successCount  = 0;
    //     $failureCount  = 0;
    //     $errorDetails  = [];

    //     foreach ($data as $row) {
    //         $oldCode = $row['employee_code_old'];
    //         $newCode = $row['employee_code_new'];
    //         $name    = $row['employee_name'];
    //         $dob     = $row['date_of_birth'];
    //         $doj     = $row['date_of_joining'];
    //         $idx     = $row['row_index'];

    //         // 2a. Cari karyawan hanya berdasarkan employee_code_old terlebih dahulu
    //         $employee = $this->employeeModel
    //             ->where('employee_code', $oldCode)
    //             ->where('employee_name', $name)
    //             ->where('date_of_birth', $dob)
    //             ->where('date_of_joining', $doj)
    //             ->first();

    //         // 2b. Jika tidak ditemukan via employee_code, baru fallback ke name+dob+doj
    //         if (! $employee) {
    //             // $employee = $this->employeeModel
    //             //     ->where('employee_name', $name)
    //             //     ->where('date_of_birth', $dob)
    //             //     ->where('date_of_joining', $doj)
    //             //     ->first();

    //             if ($employee) {
    //                 // Kami anggap ini “matching fallback”,
    //                 // tapi tambahkan catatan agar user paham nanti di pesan.
    //                 $matchedByName = true;
    //             } else {
    //                 $matchedByName = false;
    //             }
    //         } else {
    //             $matchedByName = false;
    //         }

    //         // 3. Kalau masih belum ketemu, record ini gagal
    //         if (! $employee) {
    //             $failureCount++;
    //             $errorDetails[] = "Baris {$idx}: Karyawan dengan kode lama “{$oldCode}” atau (nama: “{$name}”, TglLahir: {$dob}, TglMasuk: {$doj}) tidak ditemukan.";
    //             continue;
    //         }

    //         // 4. Cek apakah newCode sudah dipakai oleh orang lain (meski mungkin kita matching pakai nama+dob+doj)
    //         $existingNew = $this->employeeModel
    //             ->where('employee_code', $newCode)
    //             ->first();

    //         if ($existingNew) {
    //             $failureCount++;
    //             $errorDetails[] = "Baris {$idx}: Kode baru “{$newCode}” sudah ada di sistem (dipakai karyawan lain).";
    //             continue;
    //         }

    //         // 5. Lakukan update di tabel employee
    //         $this->employeeModel->update($employee['id_employee'], [
    //             'employee_code' => $newCode,
    //             'updated_at'    => date('Y-m-d H:i:s'),
    //         ]);

    //         // 6. Insert ke tabel history_employees
    //         $this->historyEmployeeModel->insert([
    //             'id_employee'        => $employee['id_employee'],
    //             'id_job_section_old' => $employee['id_job_section'],
    //             'id_factory_old'     => $employee['id_factory'],
    //             // Kalau logika Anda mengizinkan job_section_new berbeda,
    //             // ganti sesuai kolom yang benar. Saya asumsikan sama:
    //             'id_job_section_new' => $employee['id_job_section'],
    //             'id_factory_new'     => $employee['id_factory'],
    //             'date_of_change'     => date('Y-m-d H:i:s'),
    //             'reason'             => 'Update Kode Kartu dari ' . $oldCode . ' ke ' . $newCode,
    //             'id_user'            => session()->get('id_user'),
    //             'created_at'         => date('Y-m-d H:i:s'),
    //             'updated_at'         => date('Y-m-d H:i:s'),
    //         ]);

    //         $successCount++;
    //     }

    //     // 7. Buat pesan summary
    //     $messages = [];
    //     if ($successCount > 0) {
    //         $messages[] = "{$successCount} baris berhasil diperbarui.";
    //     }
    //     if ($failureCount > 0) {
    //         $messages[] = "{$failureCount} baris gagal diperbarui:";
    //         // Tampilkan detail error hanya untuk yang gagal
    //         foreach ($errorDetails as $detail) {
    //             $messages[] = "- " . $detail;
    //         }
    //     }

    //     // 8. Simpan ke flashdata dan redirect kembali
    //     if ($successCount > 0) {
    //         // Tampilkan baris sukses dan juga detail error (jika ada)
    //         session()->setFlashdata('success', implode("<br>", $messages));
    //     } else {
    //         // Kalau semua gagal, tampilkan sebagai error
    //         session()->setFlashdata('error', implode("<br>", $messages));
    //     }

    //     return redirect()->back();
    // }

    public function updateEmployeeCode()
    {
        // ------- 1. Load dari Excel -------
        $file = $this->request->getFile('file');
        $spreadsheet = IOFactory::load($file->getTempName());
        $worksheet   = $spreadsheet->getActiveSheet();

        $data = [];  // nantinya berisi setiap baris: old,new,name,dob,doj,rowIndex
        foreach ($worksheet->getRowIterator() as $row) {
            $idx = $row->getRowIndex();
            if ($idx < 4) {
                // Lewatkan header (baris 1–3)
                continue;
            }

            $cells = $row->getCellIterator();
            $cells->setIterateOnlyExistingCells(true);
            $vals = [];
            foreach ($cells as $c) {
                $vals[] = $c->getValue();
            }

            // Pastikan kolom minimal: [1]=old, [2]=new, [3]=name, [4]=dob, [5]=doj
            if (! isset($vals[1], $vals[2], $vals[3], $vals[4], $vals[5])) {
                continue;
            }

            $data[] = [
                'old'  => trim($vals[1]),
                'new'  => trim($vals[2]),
                'name' => trim($vals[3]),
                'dob'  => $this->formattedDate($vals[4]),
                'doj'  => $this->formattedDate($vals[5]),
                'row'  => $idx,
            ];
        }

        if (empty($data)) {
            session()->setFlashdata('error', 'File Excel tidak berisi data yang valid.');
            return redirect()->back();
        }

        // ------- 2. Validasi Dasar -------
        // 2a. Cek duplikat “old → new” di Excel
        $mapOldToNewList = []; // [old] => [ list of new found ]
        $allOldCodes      = [];
        $allNewCodes      = [];
        foreach ($data as $r) {
            $mapOldToNewList[$r['old']][] = $r['new'];
            $allOldCodes[] = $r['old'];
            $allNewCodes[] = $r['new'];
        }

        // 2a.i. Cari old yang muncul > 1 kali mapping berbeda
        $duplicateOlds = [];
        foreach ($mapOldToNewList as $old => $listNew) {
            if (count(array_unique($listNew)) > 1) {
                $duplicateOlds[$old] = array_unique($listNew);
            }
        }
        if (! empty($duplicateOlds)) {
            $err = [];
            foreach ($duplicateOlds as $old => $listNew) {
                $err[] = "Di Excel, kode lama “{$old}” muncul lebih dari sekali dengan newCode berbeda: [" .
                    implode(', ', $listNew) . "].";
            }
            session()->setFlashdata('error', implode('<br>', $err));
            return redirect()->back();
        }

        // 2b. Cek duplikat new di Excel (satu new muncul >1 kali untuk old berbeda)
        $countNews = array_count_values($allNewCodes);
        $dupeNewInExcel = [];
        foreach ($countNews as $newCode => $count) {
            if ($count > 1) {
                $dupeNewInExcel[] = $newCode;
            }
        }
        if (! empty($dupeNewInExcel)) {
            $err = [];
            foreach ($dupeNewInExcel as $nc) {
                $err[] = "Di Excel, newCode “{$nc}” muncul lebih dari sekali (pada row berbeda).";
            }
            session()->setFlashdata('error', implode('<br>', $err));
            return redirect()->back();
        }

        // 2c. Pastikan semua oldCode betul‐betul ada di DB
        $existingOldRows = $this->employeeModel
            ->whereIn('employee_code', array_unique($allOldCodes))
            ->findAll();
        $foundOldCodes   = array_map(fn($e) => $e['employee_code'], $existingOldRows);
        $missingOldCodes = array_diff(array_unique($allOldCodes), $foundOldCodes);

        if (! empty($missingOldCodes)) {
            $err = [];
            foreach ($data as $r) {
                if (in_array($r['old'], $missingOldCodes, true)) {
                    $err[] = "Baris {$r['row']}: Kode lama “{$r['old']}” tidak ditemukan di sistem.";
                }
            }
            session()->setFlashdata('error', implode('<br>', $err));
            return redirect()->back();
        }

        // 2d. Pastikan newCode tidak bentrok di DB, kecuali bentrok itu karena
        //     pemilik newCode juga ada di daftar oldCode (akan di‐update dalam batch).
        $existingNewRows = $this->employeeModel
            ->whereIn('employee_code', array_unique($allNewCodes))
            ->findAll();
        $foundNewCodes   = array_map(fn($e) => $e['employee_code'], $existingNewRows);

        // Kita akan membedakan 2 jenis bentrokan newCode:
        //  - Tipe A: newCode sama dengan oldCode di baris yang sama (misalnya old=X, new=X → artinya no change)
        //  - Tipe B: newCode dipakai A sekarang, tetapi A juga ada di daftar oldCode (A akan ikut di‐update → boleh)
        //  - Tipe C: newCode dipakai A sekarang, A TIDAK ada di oldCode list → fatal error
        $conflictNewFatal = [];
        foreach ($data as $r) {
            $old = $r['old'];
            $new = $r['new'];

            // Jika new == old, artinya tidak ganti apa‐apa, skip cek bentrok
            if ($new === $old) {
                continue;
            }

            // Jika newCode ditemukan di DB, periksa apakah pemilik “new” ada di oldCode list
            if (in_array($new, $foundNewCodes, true)) {
                // Cari row DB yang benar‐benar punya employee_code = $new
                $ownerOfNew = null;
                foreach ($existingNewRows as $e) {
                    if ($e['employee_code'] === $new) {
                        $ownerOfNew = $e;
                        break;
                    }
                }

                // Kalau ownerOfNew tidak ada di daftar ‘oldCode untuk batch ini’, berarti: bentrok final
                if (! in_array($new, $allOldCodes, true)) {
                    $conflictNewFatal[] = "Baris {$r['row']}: Kode baru “{$new}” sudah dipakai karyawan lain (kode tersebut bukan bagian oldCode batch ini).";
                }
                // Kalau ownerOfNew ada di oldCode list, maka ini termasuk Tipe B (boleh, karena nanti kode ownerOfNew juga di‐update).
            }
        }

        if (! empty($conflictNewFatal)) {
            // Abort total jika ada bentrokan fatal tipe C
            session()->setFlashdata('error', implode('<br>', $conflictNewFatal));
            return redirect()->back();
        }

        // ------- 3. Siapkan graf mapping old→new dan deteksi cycle -------
        // 3a. Karena kita sudah yakin “old unik” dan “new unik” di Excel, 
        //     kita dapat bangun graf sederhana:
        $mapOldToNew = [];
        foreach ($data as $r) {
            $mapOldToNew[$r['old']] = $r['new'];
        }

        // 3b. Temukan semua cycle di mapOldToNew (fungsi findCycles ada di bawah)
        $cycles = $this->findCycles($mapOldToNew);
        // Contoh $cycles bisa memuat: [ ['X','Y'], ['A','B','C'] ]

        // ------- 4. Siapkan kode sementara (temp) untuk semua node yang di‐cycle -------
        $tempPrefixes = [];
        $timestamp    = time();
        foreach ($cycles as $cycleGroup) {
            // Misalnya $cycleGroup = ['X','Y'], maka buat prefix yang sama:
            $randomHex = bin2hex(random_bytes(3));
            foreach ($cycleGroup as $oldNode) {
                $tempPrefixes[$oldNode] = 'TMP_' . $timestamp . "_{$randomHex}_";
            }
        }
        dd ($tempPrefixes);
        // ------- 5. Bangun semua SQL query untuk UPDATE (cycle + non‐cycle) -------
        $queries = [];

        // 5a. Tahap I: Move setiap node cycle → temp
        foreach ($tempPrefixes as $oldNode => $prefix) {
            $tempCode   = $prefix . $oldNode;
            $escapedOld = $this->db->escape($oldNode);
            $escapedTmp = $this->db->escape($tempCode);

            $queries[] = "
                UPDATE employees
                SET employee_code = {$escapedTmp}
                WHERE employee_code = {$escapedOld}
            ";
        }

        // 5b. Tahap II: Update semua mapping yang BUKAN bagian cycle
        foreach ($mapOldToNew as $oldNode => $newNode) {
            // Jika oldNode ada di cycle, skip (karena sudah di‐handle di tahapan temp)
            if (isset($tempPrefixes[$oldNode])) {
                continue;
            }

            // Update langsung oldNode → newNode
            $escapedOld = $this->db->escape($oldNode);
            $escapedNew = $this->db->escape($newNode);
            $queries[]  = "
                UPDATE employees
                SET employee_code = {$escapedNew}
                WHERE employee_code = {$escapedOld}
            ";
        }

        // 5c. Tahap III: Tutup semua temp→new (hanya untuk node di cycle)
        foreach ($tempPrefixes as $oldNode => $prefix) {
            $finalNew   = $mapOldToNew[$oldNode];
            $tempCode   = $prefix . $oldNode;
            $escapedTmp = $this->db->escape($tempCode);
            $escapedNew = $this->db->escape($finalNew);

            $queries[] = "
                UPDATE employees
                SET employee_code = {$escapedNew}
                WHERE employee_code = {$escapedTmp}
            ";
        }

        // ------- 6. Lakukan semua UPDATE dalam satu transaksi -------
        $this->db->transStart();
        foreach ($queries as $sql) {
            $this->db->query($sql);
        }
        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            session()->setFlashdata('error', 'Gagal meng‐update semua karyawan (transaksi dibatalkan).');
            return redirect()->back();
        }

        // ------- 7. Insert ke history_employees untuk setiap baris data yang diproses -------
        foreach ($data as $r) {
            $old = $r['old'];
            $new = $r['new'];

            // Karena tabel employees sekarang sudah di‐update final, 
            // kita cari pegawai berdasar kode 'new'.
            $emp = $this->employeeModel
                ->where('employee_code', $new)
                ->first();
            if (! $emp) {
                // Seharusnya tidak mungkin, tapi safe guard:
                continue;
            }

            // Ambil info job_section & factory sebelum update (asumsikan tidak berubah)
            // Jika logika Anda mengizinkan pindah job_section/factory juga, sesuaikan di sini.
            $this->historyEmployeeModel->insert([
                'id_employee'        => $emp['id_employee'],
                'id_job_section_old' => $emp['id_job_section'],
                'id_factory_old'     => $emp['id_factory'],
                'id_job_section_new' => $emp['id_job_section'],
                'id_factory_new'     => $emp['id_factory'],
                'date_of_change'     => date('Y-m-d H:i:s'),
                'reason'             => "Update Kode: {$old} → {$new} (batch Excel)",
                'id_user'            => session()->get('id_user'),
                'created_at'         => date('Y-m-d H:i:s'),
                'updated_at'         => date('Y-m-d H:i:s'),
            ]);
        }

        // ------- 8. Sukses! -------
        session()->setFlashdata('success', 'Semua employee_code berhasil di‐update, termasuk kasus bentrok/swap.');
        return redirect()->back();
    }

    /**
     * Utility: Mencari semua cycle di directed graph $graph (old→new).
     * Mengembalikan array of arrays, di mana tiap sub‐array adalah satu cycle (list node).
     */
    private function findCycles(array $graph): array
    {
        $visited = [];  // node yang sudah selesai diperiksa
        $cycles  = [];  // kumpulan cycle

        foreach ($graph as $start => $_) {
            if (isset($visited[$start])) {
                continue;
            }

            $path       = [];
            $inThisPath = [];
            $current    = $start;

            while (true) {
                if (! isset($graph[$current])) {
                    // Tidak ada next → tidak ada cycle dari sini
                    break;
                }

                $path[]         = $current;
                $inThisPath[$current] = true;
                $next = $graph[$current];

                if (isset($inThisPath[$next])) {
                    // Ditemukan cycle: potong path dari posisi $next
                    $idxCycleStart = array_search($next, $path, true);
                    $cycleNodes    = array_slice($path, $idxCycleStart);
                    $cycles[]      = $cycleNodes;
                    break;
                }

                if (isset($visited[$next])) {
                    // Sudah dikunjungi, dan tidak membentuk cycle baru
                    break;
                }

                $current = $next;
            }

            // Tandai semua node di path sbg telah dikunjungi
            foreach ($path as $n) {
                $visited[$n] = true;
            }
        }

        return $cycles;
    }

    // private function findCycles(array $graph): array
    // {
    //     $visited = [];
    //     $cycles  = [];

    //     foreach ($graph as $startNode => $_) {
    //         if (isset($visited[$startNode])) {
    //             continue;
    //         }
    //         $path       = [];
    //         $current    = $startNode;
    //         $inThisPath = []; // untuk cek cepat apakah node sudah di path

    //         while (true) {
    //             if (! isset($graph[$current])) {
    //                 // tidak ada outgoing edge → break
    //                 break;
    //             }
    //             $path[] = $current;
    //             $inThisPath[$current] = true;

    //             $next = $graph[$current];
    //             if (isset($inThisPath[$next])) {
    //                 // kita menemukan cycle: ambil semua elemen dari posisi $next di $path hingga akhir
    //                 $idx = array_search($next, $path, true);
    //                 $cycle = array_slice($path, $idx);
    //                 $cycles[] = $cycle;
    //                 break;
    //             }
    //             if (isset($visited[$next])) {
    //                 // next sudah dicek di iterasi lain
    //                 break;
    //             }
    //             $current = $next;
    //         }

    //         // tandai semua node di path sbg visited
    //         foreach ($path as $n) {
    //             $visited[$n] = true;
    //         }
    //     }

    //     return $cycles;
    // }

    // public function updateEmployeeCode()
    // {
    //     // 1. Baca data Excel (sama seperti sebelumnya)
    //     $file = $this->request->getFile('file');
    //     $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getTempName());
    //     $worksheet = $spreadsheet->getActiveSheet();

    //     $data = [];
    //     foreach ($worksheet->getRowIterator() as $row) {
    //         $idx = $row->getRowIndex();
    //         if ($idx < 4) continue;

    //         $cellIterator = $row->getCellIterator();
    //         $cellIterator->setIterateOnlyExistingCells(true);

    //         $cols = [];
    //         foreach ($cellIterator as $c) {
    //             $cols[] = $c->getValue();
    //         }
    //         if (! isset($cols[1], $cols[2], $cols[3], $cols[4], $cols[5])) continue;

    //         $data[] = [
    //             'old' => trim($cols[1]),
    //             'new' => trim($cols[2]),
    //             'name' => trim($cols[3]),
    //             'dob' => $this->formattedDate($cols[4]),
    //             'doj' => $this->formattedDate($cols[5]),
    //             'row' => $idx,
    //         ];
    //     }

    //     $errors        = [];
    //     $rowsToProceed = [];

    //     // 2. Validasi per baris → pisahkan yang valid dan yang error
    //     foreach ($data as $r) {
    //         $old = $r['old'];
    //         $new = $r['new'];
    //         $row = $r['row'];

    //         // Cek karyawan dengan oldCode
    //         $empDb = $this->employeeModel
    //             ->where('employee_code', $old)
    //             ->first();

    //         if (! $empDb) {
    //             $errors[] = "Baris {$row}: Kode lama '{$old}' tidak ditemukan → SKIP.";
    //             continue;
    //         }

    //         // Cek if newCode bentrok
    //         if ($new !== $old) {
    //             $eNew = $this->employeeModel
    //                 ->where('employee_code', $new)
    //                 ->first();

    //             if ($eNew && $eNew['id_employee'] !== $empDb['id_employee']) {
    //                 $errors[] = "Baris {$row}: Kode baru '{$new}' sudah dipakai karyawan lain → SKIP.";
    //                 continue;
    //             }
    //         }

    //         // Kalau lolos semua, tambahkan ke rowsToProceed
    //         $rowsToProceed[] = $r;
    //     }

    //     if (empty($rowsToProceed)) {
    //         // Semua baris di‐skip
    //         session()->setFlashdata('error', implode('<br>', $errors));
    //         return redirect()->back();
    //     }

    //     // 3. Tampilkan warning/error untuk baris yang di‐skip (jika ada)
    //     if (! empty($errors)) {
    //         session()->setFlashdata('warning', implode('<br>', $errors));
    //     }

    //     // 4. Dari $rowsToProceed, kita bikin mapping old→new lagi
    //     $mapOldToNew   = [];
    //     $allOldCodes   = [];
    //     $allNewCodes   = [];
    //     foreach ($rowsToProceed as $r) {
    //         $mapOldToNew[$r['old']] = $r['new'];
    //         $allOldCodes[] = $r['old'];
    //         $allNewCodes[] = $r['new'];
    //     }

    //     // 5. Deteksi cycle (hanya di $mapOldToNew)
    //     $cycles       = $this->findCycles($mapOldToNew);
    //     $tempPrefixes = [];
    //     $timestamp    = time();
    //     foreach ($cycles as $cycle) {
    //         foreach ($cycle as $node) {
    //             $tempPrefixes[$node] = 'TMP_' . $timestamp . '_' . bin2hex(random_bytes(3)) . '_';
    //         }
    //     }

    //     // 6. Bangun SQL queries per tahap:
    //     $queries = [];

    //     // 6a. Tahap 1: Move cycle nodes → temp
    //     foreach ($tempPrefixes as $old => $prefix) {
    //         $tempCode = $prefix . $old;
    //         $queries[] = "UPDATE employees 
    //                   SET employee_code = " . $this->db->escape($tempCode) . "
    //                   WHERE employee_code = " . $this->db->escape($old);
    //     }

    //     // 6b. Tahap 2: Update mapping non‐cycle
    //     foreach ($mapOldToNew as $old => $new) {
    //         if (isset($tempPrefixes[$old])) {
    //             // kalau old ada di cycle, skip (sudah di tahap 1)
    //             continue;
    //         }
    //         $queries[] = "UPDATE employees 
    //                   SET employee_code = " . $this->db->escape($new) . "
    //                   WHERE employee_code = " . $this->db->escape($old);
    //     }

    //     // 6c. Tahap 3: Tutup semua temp→final (hanya untuk cycle)
    //     foreach ($tempPrefixes as $old => $prefix) {
    //         $finalNew   = $mapOldToNew[$old];
    //         $tempCode   = $prefix . $old;
    //         $queries[]  = "UPDATE employees 
    //                    SET employee_code = " . $this->db->escape($finalNew) . "
    //                    WHERE employee_code = " . $this->db->escape($tempCode);
    //     }

    //     // 7. Mulai transaksi, eksekusi semua queries
    //     $db = \Config\Database::connect();
    //     $db->transStart();
    //     foreach ($queries as $q) {
    //         $db->query($q);
    //     }
    //     $db->transComplete();

    //     if ($db->transStatus() === false) {
    //         session()->setFlashdata('error', 'Terjadi kesalahan pada proses update batch. Silakan cek log.');
    //         return redirect()->back();
    //     }

    //     // 8. Insert history_employees hanya untuk baris yang diproses ($rowsToProceed)
    //     foreach ($rowsToProceed as $r) {
    //         $old = $r['old'];
    //         $new = $r['new'];
    //         // Cari lagi employee berdasar newCode (karena sekarang di DB sudah final)
    //         $emp = $this->employeeModel->where('employee_code', $new)->first();
    //         if (! $emp) continue;

    //         $this->historyEmployeeModel->insert([
    //             'id_employee'        => $emp['id_employee'],
    //             'id_job_section_old' => $emp['id_job_section'],
    //             'id_factory_old'     => $emp['id_factory'],
    //             'id_job_section_new' => $emp['id_job_section'],
    //             'id_factory_new'     => $emp['id_factory'],
    //             'date_of_change'     => date('Y-m-d H:i:s'),
    //             'reason'             => "Update Kode: {$old} → {$new} (via batch Excel)",
    //             'id_user'            => session()->get('id_user'),
    //             'created_at'         => date('Y-m-d H:i:s'),
    //             'updated_at'         => date('Y-m-d H:i:s'),
    //         ]);
    //     }

    //     session()->setFlashdata('success', 'Proses batch update selesai. Baris valid telah di‐update; baris bermasalah sudah diabaikan.');
    //     return redirect()->back();
    // }

    public function reportExcel()
    {
        $historyPindahKaryawan = $this->historyEmployeeModel->getHistoryPindahKaryawan();
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set header
        $sheet->setCellValue('A1', 'Kode Kartu');
        $sheet->setCellValue('B1', 'Nama Karyawan');
        $sheet->setCellValue('C1', 'Bagian Asal');
        $sheet->setCellValue('D1', 'Bagian Baru');
        $sheet->setCellValue('E1', 'Tanggal Pindah');
        $sheet->setCellValue('F1', 'Keterangan');
        $sheet->setCellValue('G1', 'Diupdate Oleh');

        // Fill data
        $row = 2;
        foreach ($historyPindahKaryawan as $data) {
            $sheet->setCellValue('A' . $row, esc($data['employee_code']));
            $sheet->setCellValue('B' . $row, esc($data['employee_name']));
            $sheet->setCellValue('C' . $row, esc($data['job_section_old']) . '-' . esc($data['mainFactory_old']) . '-' . esc($data['factoryName_old']));
            $sheet->setCellValue('D' . $row, esc($data['job_section_new']) . '-' . esc($data['mainFactory_new']) . '-' . esc($data['factoryName_new']));
            $sheet->setCellValue('E' . $row, esc($data['date_of_change']));
            $sheet->setCellValue('F' . $row, esc($data['reason']));
            $sheet->setCellValue('G' . $row, esc($data['updated_by']));
            $row++;
        }

        // Set filename and download
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="History_Pindah_Karyawan.xlsx"');
        header('Cache-Control: max-age=0');

        // Save to output stream
        $writer = new Xlsx($spreadsheet);
        return $writer->save("php://output");
    }
}
