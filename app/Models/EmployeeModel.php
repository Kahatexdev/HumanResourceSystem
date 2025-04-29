<?php

namespace App\Models;

use CodeIgniter\Model;

class EmployeeModel extends Model
{
    protected $table            = 'employees';
    protected $primaryKey       = 'id_employee';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_employee',
        'employee_name',
        'employee_code',
        'shift',
        'gender',
        'id_job_section',
        'id_factory',
        'id_employment_status',
        'holiday',
        'additional_holiday',
        'date_of_birth',
        'date_of_joining',
        'status'
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

    public function getEmployeeData(){
        return $this->db->table('employees')
            ->join('job_sections', 'job_sections.id_job_section = employees.id_job_section')
            ->join('factories', 'factories.id_factory = employees.id_factory')
            ->join('employment_statuses', 'employment_statuses.id_employment_status = employees.id_employment_status')
            ->join('days', 'days.id_day = employees.holiday OR employees.additional_holiday')
            ->groupBy('employees.id_employee')
            ->get()
            ->getResultArray();
    }
}
