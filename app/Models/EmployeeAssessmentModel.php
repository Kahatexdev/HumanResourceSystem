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

    public function getJobdescData($id_batch, $main_factory)
    {
        return $this->db->table('assessments AS a')
            ->select([
                'e.employee_code',
                'e.employee_name',
                'e.shift',
                'a.id_periode',
                'a.id_employee',
                'a.id_job_role',
                'jr.jobdescription',
                'jr.description',
                'mj.main_job_role_name',
                'f.factory_name',
                'pr.periode_name',
                'b.batch_name'
            ])
            ->join('employees AS e', 'e.id_employee = a.id_employee')
            ->join('job_roles AS jr', 'jr.id_job_role = a.id_job_role')
            ->join('main_job_roles AS mj', 'mj.id_main_job_role = jr.id_main_job_role')
            ->join('factories AS f', 'f.id_factory = e.id_factory')
            ->join('periodes AS pr', 'pr.id_periode = a.id_periode')
            ->join('batches AS b', 'b.id_batch = pr.id_batch')
            ->where('f.main_factory', $main_factory)
            ->where('b.id_batch', $id_batch)
            ->groupBy('a.id_job_role, a.id_employee, a.id_periode')
            ->get()
            ->getResultArray();

    }

    public function getData()
    {
        return $this->select('assessments.id_assessment, assessments.id_employee,assessments.id_periode, COUNT(assessments.id_job_role) AS ttlJobdesk, employees.employee_name, employees.employee_code, SUM(assessments.score) AS total_score,main_job_roles.id_main_job_role, main_job_roles.main_job_role_name, job_roles.jobdescription, job_roles.description, periodes.periode_name, batches.batch_name,factories.id_factory, factories.factory_name, users.id_user')
            ->join('employees', 'employees.id_employee = assessments.id_employee')
            ->join('job_roles', 'job_roles.id_job_role = assessments.id_job_role')
            ->join('main_job_roles', 'main_job_roles.id_main_job_role = job_roles.id_main_job_role')
            ->join('periodes', 'periodes.id_periode = assessments.id_periode')
            ->join('batches', 'batches.id_batch = periodes.id_batch')
            ->join('factories', 'factories.id_factory = employees.id_factory')
            ->join('users', 'users.id_user = assessments.id_user')
            ->where('assessments.score != 0')
            ->groupBy('assessments.id_employee, assessments.id_periode')
            ->orderBy('employees.employee_code', 'ASC')
            ->findAll();
    }
}
