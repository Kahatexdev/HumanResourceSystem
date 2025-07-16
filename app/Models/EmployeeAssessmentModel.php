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
                'npa.performance_score',
                'npa.id_periode',
                'pr.periode_name',
                // 'p.permit',
                // 'p.sick',
                // 'p.absent',
                'f.factory_name',
                // Previous performance tanpa batch filter:
                '( SELECT npa2.performance_score
            FROM new_performance_assessments AS npa2
            JOIN periodes AS pr2 
              ON pr2.id_periode = npa2.id_periode
            WHERE npa2.id_employee = a.id_employee
              AND pr2.start_date < pr.start_date
            ORDER BY pr2.start_date DESC
            LIMIT 1
         ) AS previous_performance_score',
            ])
            ->join('employees AS e', 'e.id_employee = a.id_employee')
            ->join('job_roles AS jr', 'jr.id_job_role = a.id_job_role')
            ->join('main_job_roles AS mj', 'mj.id_main_job_role = jr.id_main_job_role')
            ->join('new_performance_assessments AS npa', 'npa.id_employee = a.id_employee AND npa.id_periode = a.id_periode')
            // ->join('presences AS p', 'p.id_employee = a.id_employee')
            ->join('factories AS f', 'f.id_factory = e.id_factory')
            ->join('periodes AS pr', 'pr.id_periode = a.id_periode')
            ->join('batches AS b', 'b.id_batch = pr.id_batch')
            ->where('f.main_factory', $main_factory)
            ->where('b.batch_name', $batch_name)
            ->where('pr.periode_name', $periode_name)
            // ->where('e.id_employment_status  =', 3)
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

    // public function getData()
    // {
    //     return $this->select('assessments.id_assessment, assessments.id_employee,assessments.id_periode, COUNT(assessments.id_job_role) AS ttlJobdesk, employees.employee_name, employees.employee_code, SUM(assessments.score) AS total_score,main_job_roles.id_main_job_role, main_job_roles.main_job_role_name, job_roles.jobdescription, job_roles.description, periodes.periode_name, batches.batch_name,factories.id_factory, factories.factory_name, users.id_user')
    //         ->join('employees', 'employees.id_employee = assessments.id_employee')
    //         ->join('job_roles', 'job_roles.id_job_role = assessments.id_job_role')
    //         ->join('main_job_roles', 'main_job_roles.id_main_job_role = job_roles.id_main_job_role')
    //         ->join('periodes', 'periodes.id_periode = assessments.id_periode')
    //         ->join('batches', 'batches.id_batch = periodes.id_batch')
    //         ->join('history_employees', 'history_employees.id_employee = assessments.id_employee AND history_employees.date_of_change >= periodes.start_date AND history_employees.date_of_change <= periodes.end_date', 'left')
    //         ->join('factories', 'factories.id_factory = employees.id_factory')
    //         ->join('users', 'users.id_user = assessments.id_user')
    //         ->where('assessments.score != 0')
    //         ->where('assessments.id_employee =882')
    //         ->groupBy('assessments.id_employee, assessments.id_periode')
    //         ->orderBy('employees.employee_code', 'ASC')
    //         ->findAll();
    // }
    public function getData()
    {
        // 1. Ambil Query Builder untuk assessments (alias "a")
        $builder = $this->db->table('assessments a');

        // 2. SELECT + JOIN
        $builder->select([
            'a.id_assessment',
            'a.id_employee',
            'a.id_periode',
            'SUM(a.score)/(COUNT(a.id_job_role)*6)*100      AS performance_score', // Hitung persentase
            'e.employee_name',
            'e.employee_code',
            'mjr.id_main_job_role',
            'mjr.main_job_role_name',
            'jr.jobdescription',
            'jr.description',
            'p.periode_name',
            'p.start_date',
            'p.end_date',
            'b.batch_name',
            'f.id_factory',
            'f.factory_name',

            // Sub‐select untuk cari  id_factory_old terakhir sebelum periode.start_date:
            '(SELECT he2.id_factory_old
              FROM history_employees he2
              WHERE he2.id_employee = a.id_employee
                AND he2.date_of_change < p.start_date
              ORDER BY he2.date_of_change DESC
              LIMIT 1
            ) AS id_factory_old',

            // Sub‐select untuk tanggal pindah terakhir sebelum periode.start_date
            '(SELECT he3.date_of_change
              FROM history_employees he3
              WHERE he3.id_employee = a.id_employee
                AND he3.date_of_change < p.start_date
              ORDER BY he3.date_of_change DESC
              LIMIT 1
            ) AS date_of_change',

            'u.id_user',
            'u.username',
            'u.area            AS user_area'
        ]);

        $builder->join('employees e',   'e.id_employee = a.id_employee');
        $builder->join('job_roles jr',  'jr.id_job_role    = a.id_job_role');
        $builder->join('main_job_roles mjr', 'mjr.id_main_job_role = jr.id_main_job_role');
        $builder->join('periodes p',    'p.id_periode      = a.id_periode');
        $builder->join('batches b',     'b.id_batch        = p.id_batch');
        $builder->join('factories f',   'f.id_factory      = e.id_factory');
        $builder->join('users u',       'u.id_user         = a.id_user');

        // 3. Filter: hanya score != 0 dan employee sesuai
        $builder->where('a.score !=', 0);
        // $builder->where('a.id_employee', 882);

        // 4. Group by (semua kolom non‐aggregate)
        $builder->groupBy([
            'a.id_employee',
            'a.id_periode',
            // 'e.employee_name',
            // 'e.employee_code',
            // 'mjr.id_main_job_role',
            // 'mjr.main_job_role_name',
            // 'jr.jobdescription',
            // 'jr.description',
            // 'p.periode_name',
            // 'p.start_date',
            // 'p.end_date',
            // 'b.batch_name',
            // 'f.id_factory',
            // 'f.factory_name',
            // 'u.id_user',
            // 'u.username',
            // 'u.area'
            // Tidak perlu id_factory_old atau date_of_change dalam groupBy
            // karena sudah di‐select menggunakan subquery (bukan tabel langsung)
        ]);

        $builder->orderBy('e.employee_code', 'ASC');

        // 5. Eksekusi
        return $builder->get()->getResultArray();
    }
}
