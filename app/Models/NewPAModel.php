<?php

namespace App\Models;

use CodeIgniter\Model;

class NewPAModel extends Model
{
    protected $table            = 'new_performance_assessments';
    protected $primaryKey       = 'id_performance';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_employee',
        'id_periode',
        'id_main_job_role',
        'performance_score',
        'id_factory',
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

    // public function getJobdescData($id_batch, $main_factory)
    // {
    //     return $this->select('new_performance_assessments.*, employees.employee_name, employees.employee_code, batches.batch_name, periodes.periode_name, users.username, main_job_roles.main_job_role_name')
    //         ->join('employees', 'employees.id_employee = new_performance_assessments.id_employee')
    //         ->join('main_job_roles', 'main_job_roles.id_main_job_role = new_performance_assessments.id_main_job_role')
    //         ->join('periodes', 'periodes.id_periode = new_performance_assessments.id_periode')
    //         ->join('batches', 'batches.id_batch = periodes.id_batch')
    //         ->join('factories', 'factories.id_factory = new_performance_assessments.id_factory')
    //         ->join('users', 'users.id_user = new_performance_assessments.id_user')
    //         ->where('periodes.id_batch', $id_batch)
    //         ->where('factories.main_factory', $main_factory)
    //         ->orderBy('new_performance_assessments.id_performance', 'DESC')
    //         ->findAll();
    // }

    public function getJobdescData($id_batch, $main_factory, array $aspects = [])
    {
        // bangun query dasar
        $builder = $this->db->table('new_performance_assessments')
            ->select('new_performance_assessments.*, 
                employees.id_employee,
                  employees.employee_name, 
                  employees.employee_code, 
            periodes.id_periode,
                  periodes.id_batch, 
                  main_job_roles.id_main_job_role,
                  main_job_roles.main_job_role_name,
                  job_roles.description,')
            ->join('employees',           'employees.id_employee           = new_performance_assessments.id_employee')
            ->join('main_job_roles',      'main_job_roles.id_main_job_role  = new_performance_assessments.id_main_job_role')
            ->join('job_roles',           'job_roles.id_main_job_role           = main_job_roles.id_main_job_role')
            ->join('periodes',            'periodes.id_periode            = new_performance_assessments.id_periode')
            ->join('factories',           'factories.id_factory           = new_performance_assessments.id_factory')
            ->where('periodes.id_batch',      $id_batch)
            ->where('factories.main_factory', $main_factory);

        // kalau aspek dikirim, tambahkan filter WHERE IN
        if (! empty($aspects)) {
            // Exact match:
            // $builder->whereIn('main_job_roles.main_job_role_name', $aspects);

            // — atau, kalau kamu butuh LIKE (misal mencari substring):
            $builder
                ->groupStart()
                    ->like('main_job_roles.main_job_role_name', $aspects[0])
                    ->orLike('main_job_roles.main_job_role_name', $aspects[1])
                    ->orLike('main_job_roles.main_job_role_name', $aspects[2])
                    ->orLike('main_job_roles.main_job_role_name', $aspects[3])
                ->groupEnd();
        }

        return $builder
            ->orderBy('new_performance_assessments.id_performance', 'DESC')
            ->get()
            ->getResultArray();
    }
}
