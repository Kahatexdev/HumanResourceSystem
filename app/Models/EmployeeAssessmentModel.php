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

    public function getAssessmentByMainFactoryByNamaBatchByNamaPeriode($main_factory, $batch_name, $periode_name)
    {
        return $this->table('assessments a')
            ->select('
            a.id_assessment,
            a.id_employee,
            a.id_periode,
            a.score,
            performance_assessments.nilai,
            a.grade_akhir,
            a.id_user,
            a.id_jobrole,
            employees.employee_code,
            employees.employee_name,
            employees.gender,
            employees.date_of_joining,
            employees.shift,
            job_roles.jobdesccription,
            job_roles.description,
            job_sections.job_section_name,
            factories.factory_name,
            factories.main_factory,
            presence.id_employee,
            presence.id_periode,
            presence.permit,
            presence.sick,
            presence.absent,
            presence.leave,
            batches.id_batch,
            batches.batch_name,
            periodes.periode_name,
            periodes.start_date,
            periodes.end_date,
            ')
            // ROUND(SUM(sum_bsmc.produksi),2)       AS prod_op,
            // ROUND(SUM(sum_bsmc.bs_mc),2)           AS bs_mc,
            // ROUND(SUM(rosso.production),2)    AS prod_rosso,
            // ROUND(SUM(rosso.rework),2)   AS perb_rosso,
            // ROUND(SUM(sum_jarum.used_needle),2) AS used_needle,
            // (SELECT grade_akhir 
            //  FROM penilaian AS prev_penilaian
            //  JOIN periode AS prev_periode ON prev_penilaian.id_periode = prev_periode.id_periode
            //  WHERE prev_penilaian.karyawan_id = penilaian.karyawan_id
            //  AND prev_periode.end_date < periode.start_date
            //  ORDER BY prev_periode.end_date DESC LIMIT 1
            // ) AS previous_grade
            ->join('employees', 'employees.id_employee = assessments.id_employee')
            ->join('job_roles', 'job_roles.id_job_role = assessments.id_job_role')
            ->join('bagian', 'bagian.id_bagian = job_role.id_bagian')
            ->join('absen', 'absen.id_karyawan = penilaian.karyawan_id', 'left')
            ->where('absen.id_periode = penilaian.id_periode')
            ->join('periode', 'periode.id_periode = penilaian.id_periode')
            ->join('batch', 'batch.id_batch = periode.id_batch')
            ->join('bs_mc', "bs_mc.id_karyawan = penilaian.karyawan_id
                        AND bs_mc.tgl_input BETWEEN periode.start_date AND periode.end_date", 'left')
            ->join('sum_rosso', "sum_rosso.id_karyawan = penilaian.karyawan_id
                            AND sum_rosso.tgl_input BETWEEN periode.start_date AND periode.end_date", 'left')
            ->join('sum_jarum', "sum_jarum.id_karyawan = penilaian.karyawan_id
                            AND sum_jarum.tgl_input BETWEEN periode.start_date AND periode.end_date", 'left')
            ->where('bagian.main_factory', $main_factory)
            ->where('batch.batch_name', $batch_name)
            ->where('periode.periode$periode_name', $periode_name)
            ->groupBy('penilaian.id_penilaian')
            ->get()
            ->getResultArray();
    }
}
