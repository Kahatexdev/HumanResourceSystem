<?php

namespace App\Models;

use CodeIgniter\Model;

class RossoModel extends Model
{
    protected $table            = 'rosso';
    protected $primaryKey       = 'id_rosso';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_employee',
        'input_date',
        'production',
        'rework',
        'id_factory',
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

    public function getCurrentInput()
    {
        return $this->select('rosso.input_date')
            ->orderBy('rosso.input_date', 'DESC')
            ->limit(1)
            ->first();
    }

    public function getDatabyAreaUtama($area_utama)
    {
        return $this->db->table('rosso')
            ->join('employees', 'employees.id_employee = rosso.id_employee')
            ->join('job_sections', 'job_sections.id_job_section = employees.id_job_section')
            ->join('factories', 'factories.id_factory = employees.id_factory')
            ->where('factories.main_factory', $area_utama)
            ->get()->getResultArray();
    }

    public function getSummaryRosso($area, $id_batch)
    {
        return $this->select('rosso.id_employee, employees.*,  SUM(rosso.production) AS total_produksi, SUM(rosso.rework) AS total_perbaikan, periodes.periode_name, periodes.id_batch, rosso.id_factory, periodes.start_date, periodes.end_date, periodes.holiday, job_sections.job_section_name, factories.factory_name')
            ->join('periodes', 'rosso.input_date BETWEEN periodes.start_date AND periodes.end_date', 'inner')
            ->join('employees', 'employees.id_employee = rosso.id_employee', 'inner')
            ->join('job_sections', 'job_sections.id_job_section = employees.id_job_section', 'inner')
            ->join('factories', 'factories.id_factory = employees.id_factory', 'inner')
            ->where('factories.main_factory', $area)
            ->where('periodes.id_batch', $id_batch)
            ->groupBy('employees.employee_code, periodes.start_date, periodes.end_date') // Grouping berdasarkan kode_kartu dan periodes
            ->findAll();
    }

    public function getFilteredData($area_utama, $startDate, $endDate)
    {
        return $this->select('rosso.*, employees.*, job_sections.*, factories.*')
            ->join('employees', 'employees.id_employee = rosso.id_employee')
            ->join('job_sections', 'job_sections.id_job_section = employees.id_job_section')
            ->join('factories', 'factories.id_factory = employees.id_factory')
            ->where('factories.main_factory', $area_utama)
            ->where('rosso.input_date >=', $startDate)
            ->where('rosso.input_date <=', $endDate)
            ->orderBy('rosso.input_date', 'ASC')
            ->findAll();
    }

    public function getRossoData()
    {
        return $this->select('sum_rosso.*, karyawan.*, bagian.*, periode.*')
            ->join('karyawan', 'karyawan.id_karyawan = sum_rosso.id_karyawan')
            ->join('bagian', 'bagian.id_bagian = karyawan.id_bagian')
            ->join('periode', 'sum_rosso.tgl_input BETWEEN periode.start_date AND periode.end_date')
            ->findAll();
    }

    public function validasiKaryawan($tgl_input, $id_karyawan)
    {
        return $this->select('rosso.*, job_sections.*, factories.*, employees.*')
            ->join('employees', 'employees.id_employee = rosso.id_employee')
            ->join('job_sections', 'job_sections.id_job_section = employees.id_job_section')
            ->join('factories', 'factories.id_factory = employees.id_factory')
            ->where('rosso.input_date', $tgl_input)
            ->where('rosso.id_employee', $id_karyawan)
            ->first();
    }

    public function getRossoDataForFinal($id_batch, $main_factory)
    {
        $builder = $this->select('rosso.id_employee, employees.employee_code, employees.employee_name, rosso.input_date, SUM(rosso.production) AS total_produksi, SUM(rosso.rework) AS total_perbaikan, rosso.id_factory,
            periodes.id_periode, periodes.periode_name, batches.batch_name')
            ->join('periodes', 'rosso.input_date BETWEEN periodes.start_date AND periodes.end_date', 'left')
            ->join('batches', 'batches.id_batch = periodes.id_batch', 'left')
            ->join('employees', 'employees.id_employee = rosso.id_employee', 'left')
            ->join('factories', 'factories.id_factory = rosso.id_factory')
            ->where('periodes.id_batch', $id_batch);

        if ($main_factory == 'all') {
            // kalau main_factory adalah 'all', tidak perlu filter
        } else {
            $builder->where('factories.main_factory', $main_factory);
        }

        return $builder
            ->groupBy('employees.employee_code, periodes.id_periode') // Grouping berdasarkan kode_kartu dan periode
            ->findAll();
    }
}
