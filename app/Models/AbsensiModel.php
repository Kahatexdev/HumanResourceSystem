<?php

namespace App\Models;

use CodeIgniter\Model;

class AbsensiModel extends Model
{
    protected $table            = 'attendance_logs';
    protected $primaryKey       = 'id_log';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_log',
        'terminal_id',
        'nik',
        'card_no',
        'employee_name',
        'department',
        'log_date',
        'log_time',
        'source',
        'verification_source',
        'admin',
        'created_at',
        'updated_at'
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


    public function getLogAbsensi()
    {
        return $this->select('MONTH(attendance_logs.log_date) AS month,
                YEAR(attendance_logs.log_date) AS year')
            ->orderBy('log_date', 'ASC')
            ->orderBy('log_time', 'ASC')
            ->groupBy('MONTH(attendance_logs.log_date), YEAR(attendance_logs.log_date)')
            ->get()
            ->getResultArray();
    }

    public function getDetailLogAbsensi($month, $year)
    {
        return $this->select('attendance_logs.*')
            ->where('MONTH(attendance_logs.log_date)', $month)
            ->where('YEAR(attendance_logs.log_date)', $year)
            ->orderBy('log_date', 'ASC')
            ->orderBy('log_time', 'ASC')
            ->get()
            ->getResultArray();
    }

    public function getkaryawan($tglAbsen)
    {
        return $this->select('
            attendance_logs.nik,
            attendance_logs.employee_name,
            employees.id_employee
        ')
            ->join('employees', 'employees.nik = attendance_logs.nik', 'left')
            ->where('attendance_logs.log_date', $tglAbsen)
            ->groupBy('attendance_logs.nik')
            ->findAll();
    }

    public function getDetailLogAbsensiServer(
        $month,
        $year,
        $start,
        $length,
        $search,
        $orderColumn,
        $orderDir
    ) {
        // total rows
        $builder = $this->builder();
        $builder->where('MONTH(log_date)', $month);
        $builder->where('YEAR(log_date)', $year);
        $total = $builder->countAllResults(false);

        // filter search
        if (!empty($search)) {
            $builder->groupStart()
                ->like('nik', $search)
                ->orLike('employee_name', $search)
                ->orLike('department', $search)
                ->groupEnd();
        }

        $filtered = $builder->countAllResults(false);

        // order
        $builder->orderBy($orderColumn, $orderDir);

        // limit
        $builder->limit($length, $start);

        $query = $builder->get();
        $data  = $query->getResultArray();

        return [
            'data'     => $data,
            'total'    => $total,
            'filtered' => $filtered,
        ];
    }

    public function getLogAbsensiByNIKAndDate($nik, $date)
    {
        $yesterday = date('Y-m-d', strtotime($date . ' -1 day'));

        return $this->select('attendance_logs.nik, attendance_logs.employee_name, attendance_logs.log_date, attendance_logs.log_time')
            ->where('attendance_logs.nik', $nik)
            ->where('attendance_logs.log_date >=', $yesterday)
            ->where('attendance_logs.log_date <=', $date)
            ->orderBy('attendance_logs.log_date', 'ASC')
            ->orderBy('attendance_logs.log_time', 'ASC')
            ->findAll();
    }
}
