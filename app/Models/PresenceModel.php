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

    public function getPresenceData($id_batch, $main_factory)
    {
        // 1. Definisikan aspek yang mau di-LIKE
        $aspects = ['ROSSO', 'MONTIR', 'OPERATOR', 'SEWING'];

        // 2. Mulai membangun query
        $builder = $this->select('
            presences.*,
            employees.id_employee,
            employees.employee_name,
            employees.employee_code,
            job_sections.job_section_name,
            batches.batch_name,
            periodes.id_periode,
            periodes.periode_name,
            users.username
        ')
            ->join('employees', 'employees.id_employee = presences.id_employee')
            ->join('job_sections', 'job_sections.id_job_section = employees.id_job_section')
            ->join('periodes', 'periodes.id_periode = presences.id_periode')
            ->join('batches', 'batches.id_batch = periodes.id_batch')
            ->join('factories', 'factories.id_factory = employees.id_factory')
            ->join('users', 'users.id_user = presences.id_user')
            ->where([
                'batches.id_batch'       => $id_batch,
                'factories.main_factory' => $main_factory,
            ]);

        // 3. Tambahkan GROUPED LIKE CONDITIONS untuk aspek-aspek
        $builder->groupStart();
        foreach ($aspects as $i => $asp) {
            if ($i === 0) {
                // pertama pakai like()
                $builder->like('job_sections.job_section_name', $asp);
            } else {
                // sisanya pakai orLike()
                $builder->orLike('job_sections.job_section_name', $asp);
            }
        }
        $builder->groupEnd();

        // 4. Group, order, fetch
        return $builder
            ->groupBy('employees.employee_code, presences.id_periode')
            ->orderBy('employees.employee_code', 'DESC')
            ->findAll();
    }


    public function getTotalHariKerjaPerPeriode($id_batch, $main_factory)
    {
        return $this->select('periodes.id_periode, periodes.periode_name, DATEDIFF(periodes.end_date, periodes.start_date) + 1 as total_hari_kerja')
            ->join('periodes', 'periodes.id_periode = presences.id_periode')
            ->join('batches', 'batches.id_batch = periodes.id_batch')
            ->join('employees', 'employees.id_employee = presences.id_employee')
            ->join('factories', 'factories.id_factory = employees.id_factory')
            ->where(['batches.id_batch' => $id_batch, 'factories.main_factory' => $main_factory])
            ->groupBy('periodes.id_periode')
            ->findAll();
    }
}
