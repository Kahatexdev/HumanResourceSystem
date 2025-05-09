<?php

namespace App\Models;

use CodeIgniter\Model;

class JobRoleModel extends Model
{
    protected $table            = 'job_roles';
    protected $primaryKey       = 'id_job_role';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_main_job_role',
        'jobdescription',
        'description',
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

    public function getJobroleData($key)
    {
        return $this->select('job_roles.*, main_job_roles.*')
            ->join('main_job_roles', 'main_job_roles.id_main_job_role = job_roles.id_main_job_role')
            ->where('main_job_roles.main_job_role_name', $key)
            ->findAll();
    }
}
