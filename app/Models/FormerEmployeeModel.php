<?php

namespace App\Models;

use CodeIgniter\Model;

class FormerEmployeeModel extends Model
{
    protected $table            = 'former_employee';
    protected $primaryKey       = 'id_former_employee';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'employee_name',
        'employee_code',
        'shift',
        'gender',
        'job_section_name',
        'factory_name',
        'main_factory',
        'employment_status_name',
        'clothes_color',
        'holiday',
        'additional_holiday',
        'date_of_birth',
        'date_of_joining',
        'date_of_leaving',
        'reason_for_leaving',
        'id_user',
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

    public function getFormerKaryawan()
    {
        return $this->select('former_employee.*, users.username as updated_by')
            ->join('users', 'users.id_user = former_employee.id_user')
            ->findAll();
    }
}
