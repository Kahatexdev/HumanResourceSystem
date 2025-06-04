<?php

namespace App\Models;

use CodeIgniter\Model;

class PresenceModel extends Model
{
    protected $table            = 'presences';
    protected $primaryKey       = 'id_presence';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_employee',
        'id_periode',
        'permit',
        'sick',
        'absent',
        'leave',
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

    public function getDataPresence()
    {
        return $this->select('presences.*, employees.employee_name, employees.employee_code,batches.batch_name, periodes.periode_name, users.username')
            ->join('employees', 'employees.id_employee = presences.id_employee')
            ->join('periodes', 'periodes.id_periode = presences.id_periode')
            ->join('batches', 'batches.id_batch = periodes.id_batch')
            ->join('users', 'users.id_user = presences.id_user')
            ->orderBy('presences.created_at', 'DESC')
            ->findAll();
    }

    // public function getPresenceData($id_batch, $main_factory)
    // {
    //     return $this->select('presences.*, employees.employee_name, employees.employee_code, batches.batch_name, periodes.periode_name, users.username')
    //         ->join('employees', 'employees.id_employee = presences.id_employee')
    //         ->join('periodes', 'periodes.id_periode = presences.id_periode')
    //         ->join('batches', 'batches.id_batch = periodes.id_batch')
    //         ->join('factories', 'factories.id_factory = employees.id_factory')
    //         ->join('users', 'users.id_user = presences.id_user')
    //         // where in like ['ROSSO', 'MONTIR', 'OPERATOR', 'SEWING'];

    //         ->where(['batches.id_batch' => $id_batch, 'factories.main_factory' => $main_factory])
    //         ->groupBy('employees.employee_code, presences.id_periode')
    //         ->orderBy('employees.employee_code', 'DESC')
    //         ->findAll();
    // }

    // public function getPresenceData($id_batch, $main_factory)
    // {
    // // 1. Definisikan aspek yang mau di-LIKE
    // $aspects = ['ROSSO', 'MONTIR', 'OPERATOR', 'SEWING'];

    // // 2. Mulai membangun query
    // $builder = $this->select('
    //     presences.*,
    //     employees.id_employee,
    //     employees.employee_name,
    //     employees.employee_code,
    //     job_sections.job_section_name,
    //     batches.batch_name,
    //     periodes.id_periode,
    //     periodes.periode_name,
    //     users.username
    // ')
    //     ->join('employees', 'employees.id_employee = presences.id_employee')
    //     ->join('job_sections', 'job_sections.id_job_section = employees.id_job_section')
    //     ->join('periodes', 'periodes.id_periode = presences.id_periode')
    //     ->join('batches', 'batches.id_batch = periodes.id_batch')
    //     ->join('factories', 'factories.id_factory = employees.id_factory')
    //     ->join('users', 'users.id_user = presences.id_user')
    //     ->where([
    //         'batches.id_batch'       => $id_batch,
    //     ]);
    // if ($main_factory == 'all') {
    //     // kalau main_factory adalah 'all', tidak perlu filter
    // } else {
    //     $builder->where('factories.main_factory', $main_factory);
    // }

    // // 3. Tambahkan GROUPED LIKE CONDITIONS untuk aspek-aspek
    // $builder->groupStart();
    // foreach ($aspects as $i => $asp) {
    //     if ($i === 0) {
    //         // pertama pakai like()
    //         $builder->like('job_sections.job_section_name', $asp);
    //     } else {
    //         // sisanya pakai orLike()
    //         $builder->orLike('job_sections.job_section_name', $asp);
    //     }
    // }
    // $builder->groupEnd();

    // // 4. Group, order, fetch
    // return $builder
    //     ->groupBy('employees.employee_code, presences.id_periode')
    //     ->orderBy('employees.employee_code', 'DESC')
    //     ->findAll();

    // 1. Definisikan aspek (keyword) yang akan di‐LIKE pada nama job_section

    public function getPresenceData($id_batch, $main_factory)
    {
        $aspects = ['ROSSO', 'MONTIR', 'OPERATOR', 'SEWING'];

        // Ambil min & max tanggal dari periode batch
        $periodeModel = new \App\Models\PeriodeModel();
        $periodeRange = $periodeModel
            ->select('MIN(start_date) as min_date, MAX(end_date) as max_date')
            ->where('id_batch', $id_batch)
            ->first();

        $builder = $this->db->table('presences pr');

        $builder->select([
            'pr.*',
            'e.id_employee',
            'e.employee_name',
            'e.employee_code',
            'js.job_section_name',
            'b.batch_name',
            'p.id_periode',
            'p.periode_name',
            'u.username',
            'pr.sick',
            'pr.permit',
            'pr.absent',
            'pr.leave',

            // Ambil juga info mutasi (jika ada)
            'he.date_of_change',
            'he.id_job_section_old',
            'he.id_factory_old',
            'he.id_job_section_new',
            'he.id_factory_new',
            'he.reason'
        ]);

        // Join normal
        $builder->join('employees e',           'e.id_employee = pr.id_employee');
        $builder->join('job_sections js',       'js.id_job_section = e.id_job_section');
        $builder->join('periodes p',            'p.id_periode = pr.id_periode');
        $builder->join('batches b',             'b.id_batch = p.id_batch');
        $builder->join('factories f',           'f.id_factory = e.id_factory');
        $builder->join('users u',               'u.id_user = pr.id_user');

        // LEFT JOIN ke history_employees
        $builder->join(
            'history_employees he',
            "he.id_employee = e.id_employee 
             AND he.date_of_change >= '{$periodeRange['min_date']}' 
             AND he.date_of_change <= '{$periodeRange['max_date']}'",
            'left'
        );


        // Filter utama
        $builder->where('b.id_batch', $id_batch);
        if ($main_factory !== 'all') {
            $builder->where('f.main_factory', $main_factory);
        }

        // Filter aspek job section
        $builder->groupStart();
        foreach ($aspects as $idx => $asp) {
            $idx === 0
                ? $builder->like('js.job_section_name', $asp)
                : $builder->orLike('js.job_section_name', $asp);
        }
        $builder->groupEnd();

        // Group dan Order
        $builder->groupBy(['e.employee_code', 'pr.id_periode']);
        $builder->orderBy('e.employee_code', 'DESC');

        return $builder->get()->getResultArray();
    }



    public function getTotalHariKerjaPerPeriode($id_batch, $main_factory)
    {
        $builder = $this->select(
            'periodes.id_periode, periodes.periode_name, DATEDIFF(periodes.end_date, periodes.start_date) + 1 as total_hari_kerja'
        )
            ->join('periodes', 'periodes.id_periode = presences.id_periode')
            ->join('batches', 'batches.id_batch = periodes.id_batch')
            ->join('employees', 'employees.id_employee = presences.id_employee')
            ->join('factories', 'factories.id_factory = employees.id_factory')
            ->where('batches.id_batch', $id_batch);

        // Tambahkan kondisi main_factory jika bukan 'all'
        if ($main_factory !== 'all') {
            $builder->where('factories.main_factory', $main_factory);
        }

        return $builder
            ->groupBy('periodes.id_periode')
            ->findAll();
    }
}
