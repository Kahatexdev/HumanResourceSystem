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

    public function countDataTidakSesuai()
    {
        return $this->select('ad.work_date, COUNT(DISTINCT ad.id_attendance) AS total_anomali')
            ->from('attendance_days ad')
            ->join('attendance_results ar', 'ar.id_attendance = ad.id_attendance', 'left')
            ->join('shift_defs sd', 'sd.id_shift = ad.id_shift', 'left')
            ->groupStart()
            ->where('ad.in_time > CONCAT(ad.work_date, " ", DATE_ADD(sd.start_time, INTERVAL sd.grace_min MINUTE))')
            ->orWhere('ar.total_break_min > sd.break_time')
            ->orWhere('ar.total_work_min < (TIMESTAMPDIFF(MINUTE, sd.start_time, sd.end_time) - sd.break_time)')
            ->groupEnd()
            ->groupBy('ad.work_date')
            ->orderBy('ad.work_date', 'ASC')
            ->findAll();
    }

    public function getDataTidakSesuaiByDate($workDateStart = null, $workDateEnd = null)
    {
        $builder = $this->db->table('attendance_days ad');

        $builder->select('
        ad.id_attendance,
        ad.id_employee,
        e.employee_name,
        e.nik,
        ad.work_date,
        ad.id_shift,
        sd.shift_name,
        ad.in_time,
        ad.break_out_time,
        ad.break_in_time,
        ad.out_time,
        ar.total_work_min,
        ar.total_break_min,
        ar.late_min,
        ar.early_leave_min,
        ar.overtime_min,
        ar.status_code,
        sd.start_time,
        sd.end_time,
        sd.break_time,
        sd.grace_min
    ');

        $builder->join('attendance_results ar', 'ar.id_attendance = ad.id_attendance', 'left');
        $builder->join('shift_defs sd', 'sd.id_shift = ad.id_shift', 'left');
        $builder->join('employees e', 'e.id_employee = ad.id_employee', 'left');

        // Filter tanggal fleksibel
        if ($workDateStart && $workDateEnd) {
            $builder->where("ad.work_date BETWEEN '{$workDateStart}' AND '{$workDateEnd}'");
        } elseif ($workDateStart) {
            $builder->where('ad.work_date', $workDateStart);
        }

        // Kondisi anomali
        $builder->groupStart()
            ->where('ad.in_time > CONCAT(ad.work_date, " ", DATE_ADD(sd.start_time, INTERVAL sd.grace_min MINUTE))')
            ->orWhere('ar.total_break_min > sd.break_time')
            ->orWhere('ar.total_work_min < (TIMESTAMPDIFF(MINUTE, sd.start_time, sd.end_time) - sd.break_time)')
            ->groupEnd();

        $builder->groupBy('ad.id_attendance');
        $builder->orderBy('ad.id_attendance', 'ASC');

        return $builder->get()->getResultArray();
    }
}
