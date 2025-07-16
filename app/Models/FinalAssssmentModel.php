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
        'id_factory',
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

    // public function getFinalAssessmentByBatch($id_batch, $main_factory)
    // {
    // return $this->select('final_assessment.*, employees.*, factories.main_factory, batches.batch_name, periodes.periode_name, periodes.end_date, main_job_roles.main_job_role_name')
    //     ->join('employees', 'employees.id_employee = final_assessment.id_employee')
    //     ->join('factories', 'factories.id_factory = employees.id_factory')
    //     ->join('main_job_roles', 'main_job_roles.id_main_job_role = final_assessment.id_main_job_role')
    //     ->join('periodes', 'periodes.id_periode = final_assessment.id_periode')
    //     ->join('batches', 'batches.id_batch = periodes.id_batch')
    //     // LEFT JOIN history_employee untuk cek riwayat posisi sebelumnya
    //     // ->join('history_employee', 'history_employee.id_employee = final_assessment.id_employee AND history_employee.id_factory_old = final_assessment.id_factory AND Between periodes.start_date AND periodes.end_date', 'left')
    //     ->where('batches.id_batch', $id_batch)
    //     ->where('factories.main_factory', $main_factory)
    //     ->findAll();

    // 1. Hubungkan ke database
    //     $db = \Config\Database::connect();
    //     $builder = $db->table('final_assessment fa');

    //     // 2. Siapkan SELECT dengan semua kolom yang dibutuhkan,
    //     //    plus dua subquery untuk mengambil id_job_section_old & date_of_change terakhir sebelum periode
    //     $builder->select([
    //         'fa.*',
    //         'e.employee_name',
    //         'e.employee_code',
    //         'f.id_factory',
    //         'f.factory_name',
    //         'b.batch_name',
    //         'p.periode_name',
    //         'p.start_date',
    //         'p.end_date',
    //         'mjr.id_main_job_role',
    //         'mjr.main_job_role_name',
    //         'he.date_of_change',
    //         'he.id_job_section_old',
    //         'he.id_factory_old',
    //         'he.id_job_section_new',
    //         'he.id_factory_new',
    //         'he.reason',


    //         // Subquery 1: cari id_job_section_old terakhir sebelum periode.start_date
    //         '(SELECT he2.id_job_section_old
    //            FROM history_employees he2
    //           WHERE he2.id_employee = fa.id_employee
    //             AND he2.date_of_change >= p.start_date
    //           ORDER BY he2.date_of_change DESC
    //           LIMIT 1
    //         ) AS id_job_section_old',

    //         // Subquery 2: tanggal berubah terakhir sebelum periode.start_date
    //         '(SELECT he3.date_of_change
    //            FROM history_employees he3
    //           WHERE he3.id_employee = fa.id_employee
    //             AND he3.date_of_change <= p.start_date
    //           ORDER BY he3.date_of_change DESC
    //           LIMIT 1
    //         ) AS date_of_change'
    //     ]);

    //     // 3. JOIN tabel-tabel utama
    //     $builder->join('employees e',            'e.id_employee = fa.id_employee');
    //     $builder->join('factories f',            'f.id_factory = e.id_factory');
    //     $builder->join('main_job_roles mjr',     'mjr.id_main_job_role = fa.id_main_job_role');
    //     $builder->join('periodes p',             'p.id_periode = fa.id_periode');
    //     $builder->join('batches b',              'b.id_batch = p.id_batch');

    //     // 4. Kondisi utama: batch & main_factory
    //     $builder->where('b.id_batch', $id_batch);
    //     $builder->where('f.main_factory', $main_factory);

    //     // 5. Kalau Anda ingin menambahkan kondisi lain (misal score != 0), bisa di sini:
    //     //    $builder->where('fa.score !=', 0);

    //     // 6. Eksekusi query dan kembalikan hasilnya
    //     return $builder->get()->getResultArray();
    // }

    public function getFinalAssessmentByBatch($id_batch, $main_factory)
    {
        $db = \Config\Database::connect();

        // Ambil rentang tanggal dari periode dalam batch
        $periodeModel = new \App\Models\PeriodeModel();
        $periodeRange = $periodeModel
            ->select('MIN(start_date) as min_date, MAX(end_date) as max_date')
            ->where('id_batch', $id_batch)
            ->first();

        $builder = $db->table('final_assessment fa');

        $builder->select([
            'fa.*',
            'e.employee_name',
            'e.employee_code',
            'e.shift',
            'e.gender',
            'e.date_of_joining',
            'f.id_factory',
            'f.factory_name',
            'b.batch_name',
            'p.periode_name',
            'p.start_date',
            'p.end_date',
            'mjr.id_main_job_role',
            'mjr.main_job_role_name',

            // Data riwayat mutasi
            'he.date_of_change',
            'he.id_job_section_old',
            'he.id_factory_old',
            'he.id_job_section_new',
            'he.id_factory_new',
            'he.reason'
        ]);

        // JOIN utama
        $builder->join('employees e',        'e.id_employee = fa.id_employee');
        $builder->join('main_job_roles mjr', 'mjr.id_main_job_role = fa.id_main_job_role');
        $builder->join('periodes p',         'p.id_periode = fa.id_periode');
        $builder->join('batches b',          'b.id_batch = p.id_batch');
        
        // LEFT JOIN history_employees dibatasi oleh tanggal min/max periode
        $builder->join(
            'history_employees he',
            "he.id_employee = e.id_employee 
            AND he.date_of_change >= '{$periodeRange['min_date']}'
            AND he.date_of_change <= '{$periodeRange['max_date']}'",
            'left'
        );
        // Join factories: jika ada id_factory_old di history, gunakan itu, jika tidak, gunakan e.id_factory
        $builder->join(
            'factories f',
            'f.id_factory = COALESCE(he.id_factory_old, e.id_factory)'
        );

        // Filter utama
        $builder->where('b.id_batch', $id_batch);
        if ($main_factory !== 'all') {
            $builder->where('f.main_factory', $main_factory);
        }

        return $builder->get()->getResultArray();
    }
}
