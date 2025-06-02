<?php

namespace App\Models;

use CodeIgniter\Model;

class FinalAssssmentModel extends Model
{
    protected $table            = 'final_assessment';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_employee',
        'id_main_job_role',
        'id_periode',
        'score_presence',
        'score_performance_job',
        'score_performance_6s',
        'score_productivity',
        'id_user',
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

    public function getFinalAssessmentByBatch($id_batch, $main_factory)
    {
        return $this->select('final_assessment.*, employees.*, factories.main_factory, batches.batch_name, periodes.periode_name, periodes.end_date, main_job_roles.main_job_role_name')
            ->join('employees', 'employees.id_employee = final_assessment.id_employee')
            ->join('factories', 'factories.id_factory = employees.id_factory')
            ->join('main_job_roles', 'main_job_roles.id_main_job_role = final_assessment.id_main_job_role')
            ->join('periodes', 'periodes.id_periode = final_assessment.id_periode')
            ->join('batches', 'batches.id_batch = periodes.id_batch')
            ->where('batches.id_batch', $id_batch)
            ->where('factories.main_factory', $main_factory)
            ->findAll();
    }
}
