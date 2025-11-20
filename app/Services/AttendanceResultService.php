<?php

namespace App\Services;

use App\Models\AttendanceDayModel;
use App\Models\AttendanceResultModel;
use App\Models\ShiftDefModel;
use DateTime;

class AttendanceResultService
{
    protected AttendanceDayModel    $dayM;
    protected AttendanceResultModel $resM;
    protected ShiftDefModel         $shiftM;

    public function __construct()
    {
        $this->dayM  = new AttendanceDayModel();
        $this->resM  = new AttendanceResultModel();
        $this->shiftM = new ShiftDefModel();
    }

    /**
     * Proses hasil untuk range tanggal
     */
    // public function processRange(string $dateFrom, ?string $dateTo = null): int
    // {
    //     $dateTo = $dateTo ?? $dateFrom;

    //     // Ambil attendance_days di range tanggal tsb
    //     $days = $this->dayM
    //         ->where('work_date >=', $dateFrom)
    //         ->where('work_date <=', $dateTo)
    //         ->findAll();

    //     if (empty($days)) {
    //         return 0;
    //     }

    //     // Ambil semua shift, simpan ke map by id_shift
    //     $shifts = $this->shiftM->findAll();
    //     $shiftMap = [];
    //     foreach ($shifts as $s) {
    //         $shiftMap[$s['id_shift']] = $s;
    //     }

    //     $processed = 0;

    //     foreach ($days as $day) {
    //         $result = $this->calculateResultForDay($day, $shiftMap);

    //         if (!$result) {
    //             // misal: data tidak lengkap → skip
    //             continue;
    //         }

    //         // cek apakah sudah ada result untuk id_attendance ini
    //         $existing = $this->resM
    //             ->where('id_attendance', $day['id_attendance'])
    //             ->first();

    //         $payload = array_merge($result, [
    //             'id_attendance' => $day['id_attendance'],
    //             'processed_at'  => date('Y-m-d H:i:s'),
    //         ]);

    //         if ($existing) {
    //             $this->resM->update($existing['id_result'], $payload);
    //         } else {
    //             $this->resM->insert($payload);
    //         }

    //         $processed++;
    //     }

    //     return $processed;
    // }

    public function processRange(string $dateFrom, ?string $dateTo = null): int
    {
        $dateTo = $dateTo ?? $dateFrom;

        // Ambil attendance_days di range tanggal tsb SEKALI
        $days = $this->dayM
            ->where('work_date >=', $dateFrom)
            ->where('work_date <=', $dateTo)
            ->findAll();

        if (empty($days)) {
            return 0;
        }

        // Ambil semua shift, simpan ke map by id_shift
        $shifts = $this->shiftM->findAll();
        $shiftMap = [];
        foreach ($shifts as $s) {
            $shiftMap[$s['id_shift']] = $s;
        }

        // Preload existing results untuk id_attendance yang akan diproses
        $attendanceIds = array_column($days, 'id_attendance');
        $existingResults = [];
        if (!empty($attendanceIds)) {
            $rows = $this->resM->whereIn('id_attendance', $attendanceIds)->findAll();
            foreach ($rows as $r) {
                $existingResults[$r['id_attendance']] = $r;
            }
        }

        $toInsert = [];
        $toUpdate = [];

        foreach ($days as $day) {
            $result = $this->calculateResultForDay($day, $shiftMap);
            if (!$result) continue;

            $payload = array_merge($result, [
                'id_attendance' => $day['id_attendance'],
                'processed_at'  => date('Y-m-d H:i:s'),
            ]);

            if (isset($existingResults[$day['id_attendance']])) {
                // include id_result if needed by updateBatch or use id_attendance as key
                $payload['id_result'] = $existingResults[$day['id_attendance']]['id_result'];
                $toUpdate[] = $payload;
            } else {
                $toInsert[] = $payload;
            }
        }

        // Batch write
        if (!empty($toInsert)) {
            $this->resM->insertBatch($toInsert);
        }
        if (!empty($toUpdate)) {
            // updateBatch: must include the primary key or use id_result as key; ensure payload has id_result
            $this->resM->updateBatch($toUpdate, 'id_result');
        }

        return count($toInsert) + count($toUpdate);
    }


    /**
     * Hitung total kerja, break, telat, pulang cepat, lembur, status
     * untuk 1 baris attendance_days.
     *
     * @param array $day = row dari attendance_days
     * @param array $shiftMap = [id_shift => row shift]
     * @return array|null payload untuk attendance_results
     */
    protected function calculateResultForDay(array $day, array $shiftMap): ?array
    {
        $in  = $day['in_time']        ?? null;
        $bo  = $day['break_out_time'] ?? null;
        $bi  = $day['break_in_time']  ?? null;
        $out = $day['out_time']       ?? null;

        $idShift = $day['id_shift'] ?? null;
        $shift   = $idShift && isset($shiftMap[$idShift])
            ? $shiftMap[$idShift]
            : null;

        // ----- 1. Status dasar: ABSENT kalau tidak ada in/out sama sekali -----
        if (!$in && !$out) {
            return [
                'total_work_min'  => 0,
                'total_break_min' => 0,
                'late_min'        => 0,
                'early_leave_min' => 0,
                'overtime_min'    => 0,
                'status_code'     => 'ABSENT',
            ];
        }

        // Helper parse datetime
        $inDt  = $in  ? new DateTime($in)  : null;
        $boDt  = $bo  ? new DateTime($bo)  : null;
        $biDt  = $bi  ? new DateTime($bi)  : null;
        $outDt = $out ? new DateTime($out) : null;

        // ----- 2. Hitung total break -----
        $totalBreakMin = 0;
        if ($boDt && $biDt) {
            $totalBreakMin = max(0, (int) round(($biDt->getTimestamp() - $boDt->getTimestamp()) / 60));
        }

        // ----- 3. Hitung total waktu kerja (min) -----
        $totalWorkMin = 0;
        if ($inDt && $outDt) {
            $totalDuration = max(0, (int) round(($outDt->getTimestamp() - $inDt->getTimestamp()) / 60));
            $totalWorkMin  = max(0, $totalDuration - $totalBreakMin);
        }

        // Default
        $lateMin       = 0;
        $earlyLeaveMin = 0;
        $overtimeMin   = 0;
        $statusCode    = 'PRESENT';

        // ----- 4. Kalau ada shift, hitung telat/pulang cepat/lembur -----
        if ($shift) {
            // Asumsi shift punya start_time & end_time (TIME), bisa cross-midnight
            $workDate = $day['work_date']; // Y-m-d (tanggal kerja, dari attendance_days)

            $shiftStartStr = $workDate . ' ' . $shift['start_time'];
            $shiftEndStr   = $workDate . ' ' . $shift['end_time'];

            $shiftStart = new DateTime($shiftStartStr);
            $shiftEnd   = new DateTime($shiftEndStr);

            // Kalau end <= start → berarti shift nyebrang hari (malam)
            if ($shiftEnd <= $shiftStart) {
                $shiftEnd->modify('+1 day');
            }

            // Hitung telat
            if ($inDt && $inDt > $shiftStart) {
                $lateMin = (int) round(($inDt->getTimestamp() - $shiftStart->getTimestamp()) / 60);
            }

            // Hitung pulang cepat
            if ($outDt && $outDt < $shiftEnd) {
                $earlyLeaveMin = (int) round(($shiftEnd->getTimestamp() - $outDt->getTimestamp()) / 60);
            }

            // Durasi shift standar
            $shiftDurationMin = (int) round(($shiftEnd->getTimestamp() - $shiftStart->getTimestamp()) / 60);

            // Lembur: kerja - durasi shift (kalau positif)
            if ($totalWorkMin > $shiftDurationMin) {
                $overtimeMin = $totalWorkMin - $shiftDurationMin;
            }

            // Status dasar: kalau telat tapi tetap hadir
            if ($lateMin > 0 && $totalWorkMin > 0) {
                $statusCode = 'LATE';
            } else {
                $statusCode = 'PRESENT';
            }
        } else {
            // Tidak ada shift tapi ada jam → anggap hadir biasa
            $statusCode = 'PRESENT';
        }

        return [
            'total_work_min'  => $totalWorkMin,
            'total_break_min' => $totalBreakMin,
            'late_min'        => $lateMin,
            'early_leave_min' => $earlyLeaveMin,
            'overtime_min'    => $overtimeMin,
            'status_code'     => $statusCode,
        ];
    }
}
