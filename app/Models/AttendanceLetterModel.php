<?php

namespace App\Models;

use CodeIgniter\Model;

class AttendanceLetterModel extends Model
{
    protected $table            = 'attendance_letters';
    protected $primaryKey       = 'id_letter';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'employee_id',
        'letter_type',
        'date_from',
        'date_to',
        'total_days',
        'reason',
        'status',
        'approved_by',
        'approved_at',
        'created_by',
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
    
    public function getDataLetters()
    {
        return $this->db->table($this->table)
            ->select('attendance_letters.*, employees.employee_name, employees.employee_code')
            ->join('employees', 'employees.id_employee = attendance_letters.employee_id', 'left')
            ->get()
            ->getResultArray();
    }

    public function getIzinTodayCount()
    {
        return $this->where('date_from <=', date('Y-m-d'))
            ->where('date_to >=', date('Y-m-d'))
            ->where('letter_type', 'MI')
            ->where('status', 'APPROVED')
            ->countAllResults();
    }

    public function getSakitTodayCount()
    {
        return $this->where('date_from <=', date('Y-m-d'))
            ->where('date_to >=', date('Y-m-d'))
            ->where('letter_type', 'SI')
            ->where('status', 'APPROVED')
            ->countAllResults();
    }

    public function getMangkirTodayCount()
    {
        return $this->where('date_from <=', date('Y-m-d'))
            ->where('date_to >=', date('Y-m-d'))
            ->where('letter_type', 'M')
            ->where('status', 'APPROVED')
            ->countAllResults();
    }

    public function getIzinWeekByDay()
    {
        // Calculate start (Monday) and end (Sunday) of current week
        $startOfWeek = date('Y-m-d', strtotime('monday this week'));
        $endOfWeek   = date('Y-m-d', strtotime('sunday this week'));

        $results = [];
        $currentDate = $startOfWeek;

        while ($currentDate <= $endOfWeek) {
            $count = $this->db->table($this->table)
                ->where('date_from <=', $currentDate)
                ->where('date_to >=', $currentDate)
                ->where('letter_type', 'MI')
                ->where('status', 'APPROVED')
                ->countAllResults();

            $results[$currentDate] = $count;
            $currentDate = date('Y-m-d', strtotime($currentDate . ' +1 day'));
        }

        return $results;
    }

    public function getSakitWeekByDay()
    {
        // Calculate start (Monday) and end (Sunday) of current week
        $startOfWeek = date('Y-m-d', strtotime('monday this week'));
        $endOfWeek   = date('Y-m-d', strtotime('sunday this week'));

        $results = [];
        $currentDate = $startOfWeek;

        while ($currentDate <= $endOfWeek) {
            $count = $this->db->table($this->table)
                ->where('date_from <=', $currentDate)
                ->where('date_to >=', $currentDate)
                ->where('letter_type', 'SI')
                ->where('status', 'APPROVED')
                ->countAllResults();

            $results[$currentDate] = $count;
            $currentDate = date('Y-m-d', strtotime($currentDate . ' +1 day'));
        }

        return $results;
    }

    public function getMangkirWeekByDay()
    {
        // Calculate start (Monday) and end (Sunday) of current week
        $startOfWeek = date('Y-m-d', strtotime('monday this week'));
        $endOfWeek   = date('Y-m-d', strtotime('sunday this week'));

        $results = [];
        $currentDate = $startOfWeek;

        while ($currentDate <= $endOfWeek) {
            $count = $this->db->table($this->table)
                ->where('date_from <=', $currentDate)
                ->where('date_to >=', $currentDate)
                ->where('letter_type', 'M')
                ->where('status', 'APPROVED')
                ->countAllResults();

            $results[$currentDate] = $count;
            $currentDate = date('Y-m-d', strtotime($currentDate . ' +1 day'));
        }

        return $results;
    }

    public function getIzinMonthCount()
    {
        $startOfMonth = date('Y-m-01');
        $endOfMonth   = date('Y-m-t');

        return $this->db->table($this->table)
            ->where('date_from <=', $endOfMonth)
            ->where('date_to >=', $startOfMonth)
            ->where('letter_type', 'MI')
            ->where('status', 'APPROVED')
            ->countAllResults();
    }
    public function getSakitMonthCount()
    {
        $startOfMonth = date('Y-m-01');
        $endOfMonth   = date('Y-m-t');

        return $this->db->table($this->table)
            ->where('date_from <=', $endOfMonth)
            ->where('date_to >=', $startOfMonth)
            ->where('letter_type', 'SI')
            ->where('status', 'APPROVED')
            ->countAllResults();
    }
    public function getMangkirMonthCount()
    {
        $startOfMonth = date('Y-m-01');
        $endOfMonth   = date('Y-m-t');

        return $this->db->table($this->table)
            ->where('date_from <=', $endOfMonth)
            ->where('date_to >=', $startOfMonth)
            ->where('letter_type', 'M')
            ->where('status', 'APPROVED')
            ->countAllResults();
    }

    public function getTopKetidakhadiran(int $limit = 5)
    {
        return $this->db->table($this->table)
            ->select('employees.employee_name,employees.employee_code, COUNT(attendance_letters.id_letter) AS total_mangkir')
            ->join('employees', 'employees.id_employee = attendance_letters.employee_id', 'left')
            ->where('attendance_letters.letter_type', 'M')
            ->where('attendance_letters.status', 'APPROVED')
            ->groupBy('attendance_letters.employee_id')
            ->orderBy('total_mangkir', 'DESC')
            ->limit($limit)
            ->get()
            ->getResultArray();
    }
}