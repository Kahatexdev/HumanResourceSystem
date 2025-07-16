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
        // // bangun query dasar
        // $builder = $this->db->table('new_performance_assessments')
        //     ->select('new_performance_assessments.*, 
        //         employees.id_employee,
        //           employees.employee_name, 
        //           employees.employee_code, 
        //     periodes.id_periode,
        //           periodes.id_batch, 
        //           main_job_roles.id_main_job_role,
        //           main_job_roles.main_job_role_name,
        //           job_roles.description,')
        //     ->join('employees',           'employees.id_employee           = new_performance_assessments.id_employee')
        //     ->join('main_job_roles',      'main_job_roles.id_main_job_role  = new_performance_assessments.id_main_job_role')
        //     ->join('job_roles',           'job_roles.id_main_job_role           = main_job_roles.id_main_job_role')
        //     ->join('periodes',            'periodes.id_periode            = new_performance_assessments.id_periode')
        //     ->join('factories',           'factories.id_factory           = new_performance_assessments.id_factory')
        //     ->where('periodes.id_batch',      $id_batch);
        // if ($main_factory == 'all') {
        //     // kalau main_factory adalah 'all', tidak perlu filter
        // } else {
        //     $builder->where('factories.main_factory', $main_factory);
        // }

        // // kalau aspek dikirim, tambahkan filter WHERE IN
        // if (! empty($aspects)) {
        //     // Exact match:
        //     // $builder->whereIn('main_job_roles.main_job_role_name', $aspects);

        //     // — atau, kalau kamu butuh LIKE (misal mencari substring):
        //     $builder
        //         ->groupStart()
        //             ->like('main_job_roles.main_job_role_name', $aspects[0])
        //             ->orLike('main_job_roles.main_job_role_name', $aspects[1])
        //             ->orLike('main_job_roles.main_job_role_name', $aspects[2])
        //             ->orLike('main_job_roles.main_job_role_name', $aspects[3])
        //         ->groupEnd();
        // }

        // return $builder
        //     ->orderBy('new_performance_assessments.id_performance', 'DESC')
        //     ->get()
        //     ->getResultArray();
        // 1. Inisialisasi Query Builder
        $builder = $this->db->table('new_performance_assessments npa');

        // 2. SELECT BIASA plus subquery untuk “riwayat pindah”:
        $builder->select([
            'npa.*',
            'npa.id_employee',
            'e.employee_name',
            'e.employee_code',
            'p.id_periode',
            'p.id_batch',
            'mjr.id_main_job_role',
            'mjr.main_job_role_name',
            // 'jr.description AS jobdescription',
            'p.periode_name',
            'b.batch_name',
            'f.id_factory',
            'f.factory_name',

            // ===== SUBQUERY 1 =====
            // Cari id_job_section_old terakhir sebelum periode.dimulai (p.start_date)
            '(SELECT he1.id_job_section_old
               FROM history_employees he1
              WHERE he1.id_employee = npa.id_employee
                AND he1.date_of_change < p.start_date
              ORDER BY he1.date_of_change DESC
              LIMIT 1
            ) AS last_id_job_section_old',

            // ===== SUBQUERY 2 =====
            // Cari date_of_change terakhir sebelum periode.dimulai
            '(SELECT he2.date_of_change
               FROM history_employees he2
              WHERE he2.id_employee = npa.id_employee
                AND he2.date_of_change < p.start_date
              ORDER BY he2.date_of_change DESC
              LIMIT 1
            ) AS last_date_of_change'
        ]);

        // 3. JOIN tabel‐tabel yang diperlukan
        $builder->join('employees e',           'e.id_employee        = npa.id_employee');
        $builder->join('main_job_roles mjr',    'mjr.id_main_job_role = npa.id_main_job_role');
        // $builder->join('job_roles jr',          'jr.id_job_role       = npa.id_job_role');
        $builder->join('periodes p',            'p.id_periode         = npa.id_periode');
        $builder->join('batches b',             'b.id_batch           = p.id_batch');
        $builder->join('factories f',           'f.id_factory         = npa.id_factory');

        // 4. Kondisi utama: batch dan (bila perlu) main_factory
        $builder->where('p.id_batch', $id_batch);
        if ($main_factory !== 'all') {
            $builder->where('f.main_factory', $main_factory);
        }

        // 5. Jika ada aspek yang ingin difilter (nama main_job_role_name)
        if (! empty($aspects)) {
            // Contoh: exact match dengan WHERE IN
            // $builder->whereIn('mjr.main_job_role_name', $aspects);

            // Atau, jika ingin pakai LIKE di setiap elemen array:
            $builder->groupStart();
            foreach ($aspects as $idx => $val) {
                // Untuk safety, gunakan group di dalam loopen:
                if ($idx === 0) {
                    $builder->like('mjr.main_job_role_name', $val);
                } else {
                    $builder->orLike('mjr.main_job_role_name', $val);
                }
            }
            $builder->groupEnd();
        }

        // 6. Urutkan (opsional)
        $builder->orderBy('npa.id_performance', 'DESC');

        // 7. Eksekusi dan kembalikan sebagai array
        return $builder->get()->getResultArray();
    }

    public function getRataRataGrade()
    {
        return $this->select('AVG(performance_score) as rata_rata')
            // ->groupBy('id_periode')
            ->first();
    }
}
