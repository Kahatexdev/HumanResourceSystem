<?php

namespace App\Models;

use CodeIgniter\Model;

class ShiftAssignmentsModel extends Model
{
    protected $table            = 'shift_assignments';
    protected $primaryKey       = 'id_assignment';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_employee',
        'id_shift',
        'date_of_change',
        'note',
        'created_by',
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

    public function getDataShift()
    {
        return $this->select('
                shift_assignments.*,
                shift_assignments.id_assignment AS id,
                employees.nik,
                employees.employee_code,
                employees.employee_name,
                job_sections.job_section_name,
                shift_defs.shift_name,
                shift_defs.start_time,
                shift_defs.end_time,
                shift_defs.break_time,
                shift_defs.grace_min
            ')
            ->join('employees', 'employees.id_employee = shift_assignments.id_employee')
            ->join('job_sections', 'job_sections.id_job_section = employees.id_job_section', 'left')
            ->join('shift_defs', 'shift_defs.id_shift = shift_assignments.id_shift', 'left')
            ->orderBy('employees.employee_name', 'ASC')
            ->orderBy('shift_defs.shift_name', 'ASC')
            ->findAll();
    }
}
