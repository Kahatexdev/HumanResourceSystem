<?php

namespace App\Models;

use CodeIgniter\Model;

class AttendanceDayModel extends Model
{
    protected $table            = 'attendance_days';
    protected $primaryKey       = 'id_attendance';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields = [
        'id_employee',
        'work_date',
        'id_shift',
        'in_time',
        'break_out_time',
        'break_in_time',
        'out_time',
        'status_final',
        'verified_by',
        'verified_at',
        'note',
        'created_at',
        'updated_at',
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = true;
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

    public function getAttendanceResults($dateFrom, $dateTo)
    {
        return $this->select('
            attendance_days.*,
            e.nik,
            e.employee_name,
            s.shift_name,
            r.total_work_min,
            r.total_break_min,
            r.late_min,
            r.early_leave_min,
            r.overtime_min,
            r.status_code
        ')
            ->join('employees e', 'e.id_employee = attendance_days.id_employee', 'left')
            ->join('shift_defs s', 's.id_shift = attendance_days.id_shift', 'left')
            ->join('attendance_results r', 'r.id_attendance = attendance_days.id_attendance', 'left')
            ->where('work_date >=', $dateFrom)
            ->where('work_date <=', $dateTo)
            ->orderBy('work_date', 'ASC')
            ->orderBy('e.nik', 'ASC')
            ->findAll();
    }

    public function getKaryawanByTglAbsen($tglAbsen)
    {
        return $this->select('
            e.nik,
            e.employee_name,
            e.id_employee
        ')
            ->join('employees e', 'e.id_employee = attendance_days.id_employee', 'left')
            ->where('attendance_days.work_date', $tglAbsen)
            ->groupBy('attendance_days.id_employee')
            ->findAll();
    }

    public function getAttendanceDayById($id)
    {
        return $this->where('id_attendance', $id)->first();
    }
}
