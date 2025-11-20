<?php

namespace App\Models;

use CodeIgniter\Model;

class AttendanceResultModel extends Model
{
    protected $table            = 'attendance_results';
    protected $primaryKey       = 'id_result';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields = [
        'id_attendance',
        'total_work_min',
        'total_break_min',
        'late_min',
        'early_leave_min',
        'overtime_min',
        'status_code',
        'processed_at',
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    public function getEmployeePresentTodayCount()
    {
        return $this->db->table($this->table)
            ->join('attendance_days', 'attendance_results.id_attendance = attendance_days.id_attendance')
            ->where('DATE(attendance_days.work_date)', date('Y-m-d'))
            ->where('attendance_results.status_code', 'PRESENT')
            ->countAllResults();
    }

    public function getEmployeePresentWeekByDay(): array
    {
        $dow   = (int) date('N'); // 1..7
        $start = date('Y-m-d', strtotime('-' . ($dow - 1) . ' days'));
        $end   = date('Y-m-d', strtotime('+' . (7 - $dow) . ' days'));

        $rows = $this->db->table($this->table)
            ->select('attendance_days.work_date, COUNT(*) as total_hadir')
            ->join('attendance_days', 'attendance_results.id_attendance = attendance_days.id_attendance')
            ->where('attendance_days.work_date >=', $start)
            ->where('attendance_days.work_date <=', $end)
            ->where('attendance_results.status_code', 'PRESENT')
            ->groupBy('attendance_days.work_date')
            ->get()
            ->getResultArray();

        // map work_date -> jumlah hadir
        $map = [];
        foreach ($rows as $row) {
            $dayIndex = (int) date('N', strtotime($row['work_date'])); // 1..7
            $map[$dayIndex] = (int) $row['total_hadir'];
        }

        // Senin (1) s/d Sabtu (6)
        $result = [];
        for ($i = 1; $i <= 6; $i++) {
            $result[] = $map[$i] ?? 0;
        }

        return $result;
    }

    // di AttendanceResultModel (atau nama modelmu)
    public function getEmployeeStatusMonth(string $statusCode, ?int $month = null, ?int $year = null): int
    {
        $month = $month ?? (int) date('m'); // bulan sekarang
        $year  = $year  ?? (int) date('Y'); // tahun sekarang

        return $this->db->table($this->table)
            ->join('attendance_days', 'attendance_results.id_attendance = attendance_days.id_attendance')
            ->where('YEAR(attendance_days.work_date)', $year)
            ->where('MONTH(attendance_days.work_date)', $month)
            ->where('attendance_results.status_code', $statusCode)
            ->countAllResults();
    }

    public function getAttendanceTodayAllEmployees(): array
    {
        $today = date('Y-m-d');

        return $this->db->table('employees')
            ->select('
            employees.id_employee,
            employees.nik,
            employees.employee_code,
            employees.employee_name,
            job_sections.job_section_name,
            attendance_days.work_date,
            attendance_days.in_time,
            attendance_results.status_code,
            attendance_letters.letter_type AS status,
            attendance_letters.date_from,
            attendance_letters.date_to,
            attendance_letters.reason,
            attendance_letters.status AS letter_status
        ')
            // join section
            ->join('job_sections', 'job_sections.id_job_section = employees.id_job_section', 'left')

            // join ke attendance_days HARI INI
            ->join(
                'attendance_days',
                "attendance_days.id_employee = employees.id_employee
             AND attendance_days.work_date = " . $this->db->escape($today),
                'left'
            )

            // join ke attendance_results (kalau ada hasil)
            ->join(
                'attendance_results',
                'attendance_results.id_attendance = attendance_days.id_attendance',
                'left'
            )

            // join ke surat (izin/sakit/dll) kalau range-nya kena hari ini
            ->join(
                'attendance_letters',
                'attendance_letters.employee_id = employees.id_employee
             AND ' . $this->db->escape($today) . ' BETWEEN attendance_letters.date_from AND attendance_letters.date_to',
                'left'
            )

            // hanya karyawan aktif
            ->where('employees.status', 'Aktif')

            // ⬇️ HANYA yang ada di attendance_days atau attendance_letters
            ->groupStart()
            ->where('attendance_days.id_attendance IS NOT NULL', null, false)
            ->orWhere('attendance_letters.id_letter IS NOT NULL', null, false)
            ->groupEnd()

            ->orderBy('attendance_days.work_date', 'DESC')
            ->limit(10)
            ->get()
            ->getResultArray();
    }
}
