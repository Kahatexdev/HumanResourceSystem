<?php

namespace App\Models;

use CodeIgniter\Model;

class PerformanceAssessmentModel extends Model
{
    protected $table            = 'performance_assessments';
    protected $primaryKey       = 'id_performance_assessment';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_employee',
        'id_periode',
        'id_main_job_role',
        'nilai',
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

    public function raportPenilaian($area)
    {
        return $this->db->table('performance_assessments pa')
            ->select(<<<SQL
                e.employee_code,
                e.employee_name,
                e.gender,
                e.shift,
                j.job_section_name,
                MAX(CASE WHEN MONTH(p.end_date) = 1  THEN COALESCE(pa.nilai, 0) END) AS nilai_jan,
                MAX(CASE WHEN MONTH(p.end_date) = 2  THEN COALESCE(pa.nilai, 0) END) AS nilai_feb,
                MAX(CASE WHEN MONTH(p.end_date) = 3  THEN COALESCE(pa.nilai, 0) END) AS nilai_mar,
                MAX(CASE WHEN MONTH(p.end_date) = 4  THEN COALESCE(pa.nilai, 0) END) AS nilai_apr,
                MAX(CASE WHEN MONTH(p.end_date) = 5  THEN COALESCE(pa.nilai, 0) END) AS nilai_mei,
                MAX(CASE WHEN MONTH(p.end_date) = 6  THEN COALESCE(pa.nilai, 0) END) AS nilai_jun,
                MAX(CASE WHEN MONTH(p.end_date) = 7  THEN COALESCE(pa.nilai, 0) END) AS nilai_jul,
                MAX(CASE WHEN MONTH(p.end_date) = 8  THEN COALESCE(pa.nilai, 0) END) AS nilai_agu,
                MAX(CASE WHEN MONTH(p.end_date) = 9  THEN COALESCE(pa.nilai, 0) END) AS nilai_sep,
                MAX(CASE WHEN MONTH(p.end_date) = 10 THEN COALESCE(pa.nilai, 0) END) AS nilai_okt,
                MAX(CASE WHEN MONTH(p.end_date) = 11 THEN COALESCE(pa.nilai, 0) END) AS nilai_nov,
                MAX(CASE WHEN MONTH(p.end_date) = 12 THEN COALESCE(pa.nilai, 0) END) AS nilai_des
            SQL)
            ->join('employees e',      'e.id_employee       = pa.id_employee',    'left')
            ->join('job_sections j',    'j.id_job_section    = e.id_job_section', 'left')
            ->join('periodes p',        'p.id_periode        = pa.id_periode',     'left')
            ->join('batches b',        'b.id_batch          = p.id_batch',       'left')
            ->join('factories f',      'f.id_factory        = pa.id_factory',     'left')
            ->where('f.factory_name', $area)
            ->groupBy('
                e.employee_code,
                e.employee_name,
                e.gender,
                e.shift,
                j.job_section_name,
            ')
            ->orderBy('e.shift')
            ->get()
            ->getResultArray();
    }

    public function getEmployeeEvaluationStatus($periode, $area)
    {
        $builder = $this->db->table('employees as k');
        $builder->select("
        k.id_employee,
        k.employee_code,
        k.employee_name,
        k.shift,
        job.job_section_name,
        factory.main_factory,
        factory.factory_name,
        IF(p.id_performance_assessment  IS NULL, 'Belum Dinilai', 'Sudah Dinilai') AS status
    ", false);
        $builder->join('job_sections as job', 'job.id_job_section = k.id_job_section', 'left');
        $builder->join('factories as factory', 'factory.id_factory = k.id_factory', 'left');
        $builder->join('performance_assessments as p', "p.id_employee = k.id_employee AND p.id_periode = \"$periode\"", 'left');
        $builder->where('factory.factory_name', $area);
        $builder->groupBy('k.id_employee');
        $builder->groupBy('p.id_periode');

        return $builder->get()->getResultArray();
    }

    public function getAssessmentsByMainFactory($main_factory)
    {
        return $this->db->table('performance_assessments pa')
            ->select('
                pa.id_performance_assessment,
                factories.factory_name,
                factories.main_factory,
                batches.id_batch,
                batches.batch_name,
                periodes.periode_name,
                periodes.start_date,
                periodes.end_date
            ')
            ->join('factories', 'factories.id_factory = pa.id_factory')
            ->join('periodes', 'periodes.id_periode = pa.id_periode')
            ->join('batches', 'batches.id_batch = periodes.id_batch')
            ->where('factories.main_factory', $main_factory)
            ->orderBy('periodes.start_date', 'ASC')
            ->groupBy('batches.id_batch, pa.id_periode, factories.main_factory')
            ->get()
            ->getResultArray();
    }
}
