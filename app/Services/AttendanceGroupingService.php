<?php

namespace App\Services;

use App\Models\AbsensiModel;
use App\Models\AttendanceDayModel;
use App\Models\ShiftDefModel;
use App\Models\EmployeeModel;
use App\Models\ShiftAssignmentsModel;
use CodeIgniter\Database\BaseConnection;
use DateTime;

class AttendanceGroupingService
{
    protected BaseConnection $db;

    protected AbsensiModel $logM;
    protected AttendanceDayModel $dayM;
    protected ShiftDefModel $shiftM;
    protected EmployeeModel $empM;
    protected ShiftAssignmentsModel $shiftAssignM;

    /**
     * Batas maksimal selisih menit antar log
     * supaya masih dianggap 1 shift.
     * Misal 6 jam = 360 menit.
     */
    // protected int $maxGapBetweenLogsMinutes = 480; // 6 jam??

    /**
     * Batas maksimal selisih menit antara jam masuk dan start shift
     * supaya tetap dianggap satu shift.
     * Misal 3 jam = 180 menit.
     */
    protected int $maxShiftDiffMinutes = 180;

    /**
     * Batas maksimal selisih menit antar log
     * supaya masih dianggap 1 shift.
     * Misal 8 jam = 480 menit.
     */
    protected int $maxGapBetweenLogsMinutes = 479;



    public function __construct()
    {
        $this->db   = db_connect();
        $this->logM = new AbsensiModel();
        $this->dayM = new AttendanceDayModel();
        $this->shiftM = new ShiftDefModel();
        $this->empM   = new EmployeeModel();
        $this->shiftAssignM = new ShiftAssignmentsModel();
    }

    /**
     * Proses pengelompokan log ke attendance_days
     *
     * @param string      $dateFrom  format 'Y-m-d'
     * @param string|null $dateTo    kalau null = sama dengan $dateFrom
     * @return int jumlah record attendance_days yang diproses
     */
    // public function groupLogsToDays(string $dateFrom, ?string $dateTo = null): int
    // {
    //     $dateTo = $dateTo ?? $dateFrom;

    //     // 1. Ambil semua shift
    //     $shifts = $this->shiftM
    //         ->orderBy('start_time', 'ASC')
    //         ->findAll();

    //     if (empty($shifts)) {
    //         // kalau shift belum di-setup, hentikan
    //         return 0;
    //     }

    //     // 2. Ambil log absensi per tanggal
    //     $logs = $this->logM
    //         ->where('log_date >=', $dateFrom)
    //         ->where('log_date <=', $dateTo)
    //         ->orderBy('log_date', 'ASC')
    //         ->orderBy('nik', 'ASC')
    //         ->orderBy('log_time', 'ASC')
    //         ->findAll();
    //     // dd($logs);
    //     if (empty($logs)) {
    //         return 0;
    //     }

    //     // 3. Grouping per karyawan + sesi shift (berdasarkan jarak antar log)
    //     $grouped = [];
    //     $byEmp   = [];

    //     // Kelompokkan log per karyawan dulu
    //     foreach ($logs as $row) {
    //         $employeeId = $this->resolveEmployeeId($row);
    //         if (!$employeeId) {
    //             log_message(
    //                 'warning',
    //                 'AttendanceGroupingService: employee not found for log. NIK: ' . ($row['nik'] ?? '-') .
    //                     ', log_date: ' . ($row['log_date'] ?? '-') .
    //                     ', log_time: ' . ($row['log_time'] ?? '-')
    //             );
    //             continue;
    //         }

    //         $logDate = $row['log_date'] ?? null;
    //         $logTime = $row['log_time'] ?? null;

    //         if (!$logDate || !$logTime) {
    //             log_message(
    //                 'warning',
    //                 'AttendanceGroupingService: log_date/log_time kosong. NIK: ' . ($row['nik'] ?? '-')
    //             );
    //             continue;
    //         }

    //         $dtString = $logDate . ' ' . $logTime;

    //         try {
    //             $dt = new \DateTime($dtString);
    //         } catch (\Exception $e) {
    //             log_message(
    //                 'error',
    //                 'AttendanceGroupingService: invalid datetime "' . $dtString . '" : ' . $e->getMessage()
    //             );
    //             continue;
    //         }

    //         // Simpan objek DateTime untuk sorting & perhitungan selisih
    //         $row['log_datetime_obj'] = $dt;
    //         $row['log_datetime']     = $dt->format('Y-m-d H:i:s');

    //         $byEmp[$employeeId][] = $row;
    //     }

    //     // Bentuk group per "sesi shift"
    //     foreach ($byEmp as $employeeId => $empLogs) {
    //         // sort per datetime
    //         usort($empLogs, function ($a, $b) {
    //             /** @var \DateTime $da */
    //             $da = $a['log_datetime_obj'];
    //             /** @var \DateTime $db */
    //             $db = $b['log_datetime_obj'];
    //             return $da <=> $db;
    //         });

    //         $currentGroup = null;
    //         /** @var \DateTime|null $lastDt */
    //         $lastDt = null;

    //         foreach ($empLogs as $logRow) {
    //             /** @var \DateTime $dt */
    //             $dt = $logRow['log_datetime_obj'];

    //             if ($currentGroup === null) {
    //                 // Mulai sesi baru
    //                 $currentGroup = [
    //                     'id_employee' => $employeeId,
    //                     // work_date = tanggal log pertama di sesi
    //                     'work_date'   => $dt->format('Y-m-d'),
    //                     'logs'        => [],
    //                 ];
    //             } else {
    //                 // Hitung selisih menit dengan log sebelumnya
    //                 $diffMinutes = ($dt->getTimestamp() - $lastDt->getTimestamp()) / 60;

    //                 // Kalau selisih terlalu jauh => anggap shift baru
    //                 if ($diffMinutes > $this->maxGapBetweenLogsMinutes) {
    //                     $grouped[]    = $currentGroup;
    //                     $currentGroup = [
    //                         'id_employee' => $employeeId,
    //                         'work_date'   => $dt->format('Y-m-d'),
    //                         'logs'        => [],
    //                     ];
    //                 }
    //             }

    //             $currentGroup['logs'][] = $logRow;
    //             $lastDt                 = $dt;
    //         }

    //         if ($currentGroup !== null) {
    //             $grouped[] = $currentGroup;
    //         }
    //     }

    //     // HAPUS dd($grouped); kalau sudah tidak dipakai debug
    //     // dd($grouped);



    //     // 4. Mapping ke attendance_days
    //     $processed = 0;

    //     $this->db->transStart();

    //     foreach ($grouped as $group) {
    //         $idEmployee = $group['id_employee'];
    //         $workDate   = $group['work_date'];
    //         $rows       = $group['logs'];

    //         // >>> JIKA JUMLAH LOG < 3, JANGAN DISAVE <<<
    //         if (count($rows) < 3) {
    //             log_message(
    //                 'info',
    //                 "AttendanceGroupingService: skip grouping, log count < 3. " .
    //                     "emp={$idEmployee}, work_date={$workDate}, count=" . count($rows)
    //             );
    //             continue;
    //         } elseif (count($rows) > 4) {
    //             // opsional: batasi maksimal log yang diproses per hari
    //             log_message(
    //                 'info',
    //                 "AttendanceGroupingService: skip grouping, log count > 4. " .
    //                     "emp={$idEmployee}, work_date={$workDate}, count=" . count($rows)
    //             );
    //             continue;
    //         }

    //         // pastikan sort per datetime
    //         usort($rows, function ($a, $b) {
    //             return strcmp($a['log_datetime'], $b['log_datetime']);
    //         });

    //         // mapping jam (pakai full datetime)
    //         $mapped = $this->mapLogsToDay($rows);

    //         // deteksi shift
    //         $inDT  = $mapped['in_time']  ? new DateTime($mapped['in_time'])  : null;
    //         $outDT = $mapped['out_time'] ? new DateTime($mapped['out_time']) : null;

    //         $idShift = $this->detectShiftId($inDT, $outDT, $shifts);
    //         if ($idShift === null) {
    //             log_message(
    //                 'info',
    //                 "AttendanceGroupingService: skip grouping, shift not detected. " .
    //                     "emp={$idEmployee}, work_date={$workDate}"
    //             );
    //             continue;
    //         }

    //         $shiftAssigned = $this->shiftAssignM
    //             ->where('id_employee', $idEmployee)
    //             ->where('id_shift', $idShift)
    //             ->first();
    //         if (!$shiftAssigned) {
    //             log_message(
    //                 'info',
    //                 "AttendanceGroupingService: skip grouping, shift not assigned to employee. " .
    //                     "emp={$idEmployee}, work_date={$workDate}, id_shift={$idShift}"
    //             );
    //             continue;
    //         }
    //         $verifiedBy = session()->get('id_user');
    //         if ($verifiedBy) {
    //             $userExists = $this->db->table('users')
    //                 ->where('id_user', $verifiedBy)
    //                 ->countAllResults() > 0;

    //             if (! $userExists) {
    //                 $verifiedBy = null;
    //             }
    //         } else {
    //             $verifiedBy = null;
    //         }

    //         // siapkan payload untuk attendance_days
    //         $payload = [
    //             'id_employee'    => $idEmployee,
    //             'work_date'      => $workDate,
    //             'id_shift'       => $idShift,
    //             'in_time'        => $mapped['in_time'],
    //             'break_out_time' => $mapped['break_out_time'],
    //             'break_in_time'  => $mapped['break_in_time'],
    //             'out_time'       => $mapped['out_time'],
    //             'status_final'   => 'LOCKED',
    //             'verified_by'    => $verifiedBy,
    //             'verified_at'    => date('Y-m-d H:i:s')
    //         ];

    //         // cek existing & insert/update
    //         $existing = $this->dayM
    //             ->where('id_employee', $idEmployee)
    //             ->where('work_date', $workDate)
    //             ->first();

    //         if ($existing) {
    //             $this->dayM->update($existing['id_attendance'], $payload);
    //         } else {
    //             $this->dayM->insert($payload);
    //         }

    //         $processed++;
    //     }

    //     $this->db->transComplete();

    //     // ⬇⬇⬇  TAMBAHKAN BAGIAN INI  ⬇⬇⬇
    //     if ($this->db->transStatus() === false) {
    //         // kalau transaksi gagal, balikin 0 (atau boleh lempar exception kalau mau)
    //         return 0;
    //     }

    //     return $processed;
    // }

    public function groupLogsToDays(string $dateFrom, ?string $dateTo = null): int
    {
        $dateTo = $dateTo ?? $dateFrom;

        // 1. Ambil semua shift
        $shifts = $this->shiftM->orderBy('start_time', 'ASC')->findAll();
        if (empty($shifts)) return 0;

        // convert shift list ke map by id_shift (dipakai detectShiftId)
        $shiftMap = [];
        foreach ($shifts as $s) {
            $shiftMap[$s['id_shift']] = $s;
        }

        // 2. Ambil log absensi per tanggal SEKALI
        $logs = $this->logM
            ->where('log_date >=', $dateFrom)
            ->where('log_date <=', $dateTo)
            ->orderBy('log_date', 'ASC')
            ->orderBy('nik', 'ASC')
            ->orderBy('log_time', 'ASC')
            ->findAll();

        if (empty($logs)) {
            return 0;
        }

        // 3. Kelompokkan logs per employee (resolveEmployeeId tetap dipanggil,
        // tapi hanya saat pembentukan kelompok — tidak nge-query DB tiap kali)
        $byEmp = [];
        foreach ($logs as $row) {
            $employeeId = $this->resolveEmployeeId($row);
            if (!$employeeId) {
                log_message('warning', 'AttendanceGroupingService: employee not found for log. NIK: ' . ($row['nik'] ?? '-'));
                continue;
            }
            $logDate = $row['log_date'] ?? null;
            $logTime = $row['log_time'] ?? null;
            if (!$logDate || !$logTime) {
                log_message('warning', 'AttendanceGroupingService: log_date/log_time kosong. NIK: ' . ($row['nik'] ?? '-'));
                continue;
            }
            try {
                $dt = new \DateTime($logDate . ' ' . $logTime);
            } catch (\Exception $e) {
                log_message('error', 'AttendanceGroupingService: invalid datetime "' . ($logDate . ' ' . $logTime) . '" : ' . $e->getMessage());
                continue;
            }
            $row['log_datetime_obj'] = $dt;
            $row['log_datetime'] = $dt->format('Y-m-d H:i:s');
            $byEmp[$employeeId][] = $row;
        }

        // 4. Bentuk groups per sesi (sama seperti sebelumnya)
        $grouped = [];
        foreach ($byEmp as $employeeId => $empLogs) {
            usort($empLogs, function ($a, $b) {
                return $a['log_datetime'] <=> $b['log_datetime'];
            });

            $currentGroup = null;
            $lastDt = null;
            foreach ($empLogs as $logRow) {
                $dt = $logRow['log_datetime_obj'];
                if ($currentGroup === null) {
                    $currentGroup = [
                        'id_employee' => $employeeId,
                        'work_date'   => $dt->format('Y-m-d'),
                        'logs'        => [],
                    ];
                } else {
                    $diffMinutes = ($dt->getTimestamp() - $lastDt->getTimestamp()) / 60;
                    if ($diffMinutes > $this->maxGapBetweenLogsMinutes) {
                        $grouped[] = $currentGroup;
                        $currentGroup = [
                            'id_employee' => $employeeId,
                            'work_date'   => $dt->format('Y-m-d'),
                            'logs'        => [],
                        ];
                    }
                }
                $currentGroup['logs'][] = $logRow;
                $lastDt = $dt;
            }
            if ($currentGroup !== null) $grouped[] = $currentGroup;
        }

        if (empty($grouped)) return 0;

        // --- OPTIMISASI: preload data yang dipakai dalam loop ---
        // 5. Ambil semua shift_assignments untuk semua employee yang ada di grouped
        $empIds = array_values(array_unique(array_map(function ($g) {
            return $g['id_employee'];
        }, $grouped)));
        $assignRows = $this->shiftAssignM
            ->whereIn('id_employee', $empIds)
            ->findAll();

        // map: employee -> array of assigned id_shift
        $assignMap = [];
        foreach ($assignRows as $r) {
            $assignMap[$r['id_employee']][] = (int)$r['id_shift'];
        }

        // 6. Preload existing attendance_days untuk range (id_employee + work_date)
        // Ambil unique pairs
        $pairs = [];
        foreach ($grouped as $g) {
            $pairs[$g['id_employee'] . '|' . $g['work_date']] = [$g['id_employee'], $g['work_date']];
        }
        $keys = array_values($pairs);
        // Build where clause: (id_employee = x AND work_date = y) OR ...
        // Simpler: ambil semua attendance_days di rentang tanggal untuk employees yg terlibat
        $existingDaysRows = $this->dayM
            ->whereIn('id_employee', $empIds)
            ->where('work_date >=', $dateFrom)
            ->where('work_date <=', $dateTo)
            ->findAll();

        $existingDaysMap = [];
        foreach ($existingDaysRows as $r) {
            $existingDaysMap[$r['id_employee'] . '|' . $r['work_date']] = $r;
        }

        // 7. Cek verified_by user sekali saja (cache)
        $verifiedBy = session()->get('id_user') ?: null;
        if ($verifiedBy) {
            $userExists = $this->db->table('users')->where('id_user', $verifiedBy)->countAllResults() > 0;
            if (! $userExists) $verifiedBy = null;
        }

        // 8. Siapkan batch arrays
        $toInsert = [];
        $toUpdate = [];

        foreach ($grouped as $group) {
            $idEmployee = $group['id_employee'];
            $workDate   = $group['work_date'];
            $rows       = $group['logs'];

            if (count($rows) < 3 || count($rows) > 4) {
                // skip seperti rule awal
                continue;
            }

            usort($rows, function ($a, $b) {
                return strcmp($a['log_datetime'], $b['log_datetime']);
            });

            $mapped = $this->mapLogsToDay($rows);

            $inDT  = $mapped['in_time']  ? new DateTime($mapped['in_time'])  : null;
            $outDT = $mapped['out_time'] ? new DateTime($mapped['out_time']) : null;

            $idShift = $this->detectShiftId($assignMap[$idEmployee] ?? [], $inDT, $outDT, $shiftMap);
            if ($idShift === null) {
                continue;
            }

            // cek apakah shift ini termasuk assignment employee (pakai preload assignMap)
            $assignedShifts = $assignMap[$idEmployee] ?? [];
            if (!in_array($idShift, $assignedShifts, true)) {
                continue;
            }

            $payload = [
                'id_employee'    => $idEmployee,
                'work_date'      => $workDate,
                'id_shift'       => $idShift,
                'in_time'        => $mapped['in_time'],
                'break_out_time' => $mapped['break_out_time'],
                'break_in_time'  => $mapped['break_in_time'],
                'out_time'       => $mapped['out_time'],
                'status_final'   => 'LOCKED',
                'verified_by'    => $verifiedBy,
                'verified_at'    => date('Y-m-d H:i:s')
            ];

            $key = $idEmployee . '|' . $workDate;
            if (isset($existingDaysMap[$key])) {
                // prepare update: include id_attendance key
                $payload['id_attendance'] = $existingDaysMap[$key]['id_attendance'];
                $toUpdate[] = $payload;
            } else {
                $toInsert[] = $payload;
            }
        }

        // 9. Batch write (transaction)
        $this->db->transStart();

        if (!empty($toInsert)) {
            // insertBatch expects array of arrays
            $this->dayM->insertBatch($toInsert, true);
        }
        if (!empty($toUpdate)) {
            // updateBatch needs index key name; ensure id_attendance exists in payload
            $this->dayM->updateBatch($toUpdate, 'id_attendance');
        }

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            return 0;
        }

        // return jumlah record changed (insert + update)
        return count($toInsert) + count($toUpdate);
    }


    /**
     * Resolve id_employee berdasarkan log (nik / card_no).
     * Silakan sesuaikan dengan struktur tabel employees kamu.
     */
    protected function resolveEmployeeId(array $logRow): ?int
    {
        // Prioritas 1: NIK
        if (!empty($logRow['nik'])) {
            $emp = $this->empM->where('nik', $logRow['nik'])->first();
            if ($emp) {
                return (int) $emp['id_employee'];
            }
        }

        // Prioritas 2: card_no
        // if (!empty($logRow['card_no'])) {
        //     $emp = $this->empM->where('card_no', $logRow['card_no'])->first();
        //     if ($emp) {
        //         return (int) $emp['id_employee'];
        //     }
        // }

        return null;
    }

    /**
     * Mapping sekumpulan log dalam 1 hari jadi:
     * in_time, break_out_time, break_in_time, out_time
     *
     * Aturan simple:
     * - record pertama  -> in_time
     * - record kedua    -> break_out_time (kalau ada)
     * - record ketiga   -> break_in_time (kalau ada)
     * - record terakhir -> out_time
     *
     * Kalau cuma 1 log => dianggap in_time saja
     * Kalau 2 log       => in_time & out_time
     * Kalau 3 log       => in_time, break_out_time, out_time
     * Kalau >=4 log     => in_time, break_out_time, break_in_time, out_time (pakai sebelum terakhir & terakhir)
     */
    protected function mapLogsToDay(array $rows): array
    {
        $count = count($rows);

        $inTime        = null;
        $breakOutTime  = null;
        $breakInTime   = null;
        $outTime       = null;

        if ($count >= 1) {
            $inTime = $rows[0]['log_datetime'];            // full datetime asli
        }

        if ($count == 2) {
            $outTime = $rows[1]['log_datetime'];
        } elseif ($count == 3) {
            $breakOutTime = $rows[1]['log_datetime'];
            $outTime      = $rows[2]['log_datetime'];
        } elseif ($count >= 4) {
            $breakOutTime = $rows[1]['log_datetime'];
            $breakInTime  = $rows[$count - 2]['log_datetime'];
            $outTime      = $rows[$count - 1]['log_datetime'];
        }

        return [
            'in_time'        => $inTime,
            'break_out_time' => $breakOutTime,
            'break_in_time'  => $breakInTime,
            'out_time'       => $outTime,
        ];
    }

    /**
     * Deteksi shift berdasarkan jam masuk (utama) dan jam keluar (opsional).
     * Untuk awal, kita pakai aturan sederhana:
     * - Ambil selisih menit antara jam masuk dan start_time shift
     * - Pilih shift dengan selisih terkecil
     * - Kalau selisih > maxShiftDiffMinutes => dianggap tidak cocok (NULL)
     */
    protected function detectShiftId(array $assignMap, ?DateTime $inTime, ?DateTime $outTime, array $shiftMap): ?int
    {
        // if (!$inTime && !$outTime) {
        //     return null;
        // }

        // $inMinutes = $inTime
        //     ? ((int)$inTime->format('H')) * 60 + (int)$inTime->format('i')
        //     : null;

        // $bestShiftId    = null;
        // $bestDiff       = PHP_INT_MAX;

        // foreach ($shifts as $shift) {
        //     // konversi start_time ke menit dari 00:00
        //     [$h, $m, $s] = explode(':', $shift['start_time']);
        //     $startMinutes = (int)$h * 60 + (int)$m;

        //     if ($inMinutes !== null) {
        //         $diff = abs($inMinutes - $startMinutes);
        //     } else {
        //         // kalau tidak ada in_time, bisa pakai logika lain (misal pakai out_time)
        //         $diff = 0;
        //     }

        //     if ($diff < $bestDiff) {
        //         $bestDiff    = $diff;
        //         $bestShiftId = (int)$shift['id_shift'];
        //     }
        // }

        // if ($bestDiff > $this->maxShiftDiffMinutes) {
        //     // terlalu jauh dari jam shift, bisa dianggap tidak ada shift
        //     return null;
        // }

        // return $bestShiftId;

        if (!$inTime) {
            return null;
        }

        $inMinutes = ((int)$inTime->format('H')) * 60 + (int)$inTime->format('i');
        $bestShiftId    = null;
        $bestDiff       = PHP_INT_MAX;

        foreach ($assignMap as $idShift) {
            if (!isset($shiftMap[$idShift])) {
                continue;
            }
            $shift = $shiftMap[$idShift];
            [$h, $m, $s] = explode(':', $shift['start_time']);
            $startMinutes = (int)$h * 60 + (int)$m;

            $diff = abs($inMinutes - $startMinutes);

            if ($diff < $bestDiff) {
                $bestDiff    = $diff;
                $bestShiftId = (int)$shift['id_shift'];
            }
        }

        if ($bestDiff > $this->maxShiftDiffMinutes) {
            return null;
        }

        return $bestShiftId;
    }
}
