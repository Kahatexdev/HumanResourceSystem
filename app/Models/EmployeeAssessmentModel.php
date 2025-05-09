<?php

namespace App\Models;

use CodeIgniter\Model;

class EmployeeAssessmentModel extends Model
{
    protected $table            = 'assessments';
    protected $primaryKey       = 'id_assessment';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_employee',
        'id_job_role',
        'id_periode',
        'score',
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

    public function getAssessmentsByPeriod($main_factory, $batch_name, $periode_name)
    {
        $builder = $this->db->table('assessments AS a');

        return $builder
            ->select([
                'e.employee_code',
                'e.employee_name',
                'e.shift',
                'e.gender',
                'e.date_of_joining',
                'mj.main_job_role_name',
                "jr.jobdescription",
                'jr.description',
                'a.score',
                'a.id_assessment',
                'pa.nilai',
                'p.permit',
                'p.sick',
                'p.absent',
                'f.factory_name'
            ])
            ->join('employees AS e', 'e.id_employee = a.id_employee')
            ->join('job_roles AS jr', 'jr.id_job_role = a.id_job_role')
            ->join('main_job_roles AS mj', 'mj.id_main_job_role = jr.id_main_job_role')
            ->join('performance_assessments AS pa', 'pa.id_employee = a.id_employee')
            ->join('presences AS p', 'p.id_employee = a.id_employee')
            ->join('factories AS f', 'f.id_factory = e.id_factory')
            ->join('periodes AS pr', 'pr.id_periode = a.id_periode')
            ->join('batches AS b', 'b.id_batch = pr.id_batch')
            ->where('f.main_factory', $main_factory)
            ->where('b.batch_name', $batch_name)
            ->where('pr.periode_name', $periode_name)
            ->groupBy('a.id_job_role,a.id_employee, a.id_periode')
            ->get()
            ->getResultArray();
    }
}
