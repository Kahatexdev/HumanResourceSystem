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
}
