<?php

namespace App\Models;

use CodeIgniter\Model;

class BsmcModel extends Model
{
    protected $table            = 'sum_bsmc';
    protected $primaryKey       = 'id_bsmc';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_bsmc',
        'id_employee',
        'tgl_input',
        'produksi',
        'bs_mc',
        'id_factory',
        'created_at',
        'updated_at',
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

    public function getDatabyArea($factory_name)
    {
        return $this->db->table('sum_bsmc')
            ->join('employees', 'employees.id_employee = sum_bsmc.id_employee')
            ->join('factories', 'factories.id_factory = employees.id_factory')
            ->where('factories.factory_name', $factory_name)
            ->get()->getResultArray();
    }

    // public function getSummaryBSMesin($id_batch, $factory_name)
    // {
    //     return $this->select('bs_mc.id_karyawan, karyawan.kode_kartu, karyawan.nama_karyawan, karyawan.jenis_kelamin, karyawan.tgl_masuk,  SUM(bs_mc.produksi) AS total_produksi, SUM(bs_mc.bs_mc) AS total_bs, periode.nama_periode, periode.id_batch, bs_mc.area, periode.start_date, periode.end_date, periode.jml_libur, bagian.nama_bagian')
    //         ->join('periode', 'bs_mc.tgl_input BETWEEN periode.start_date AND periode.end_date', 'inner')
    //         ->join('karyawan', 'karyawan.id_karyawan = bs_mc.id_karyawan', 'inner')
    //         ->join('bagian', 'bagian.id_bagian = karyawan.id_bagian', 'inner')
    //         ->where('periode.id_batch', $id_batch)
    //         ->where('bs_mc.area', $area)
    //         ->groupBy('karyawan.kode_kartu, periode.start_date, periode.end_date') // Grouping berdasarkan kode_kartu dan periode
    //         ->findAll();
    // }
    public function getSummaryBSMesin($id_batch, $factory_name)
    {
        return $this->select('sum_bsmc.id_employee, employees.employee_code, employees.employee_name, employees.gender, employees.date_of_joining,  SUM(sum_bsmc.produksi) AS total_produksi, SUM(sum_bsmc.bs_mc) AS total_bs, periodes.periode_name, periodes.id_batch, sum_bsmc.id_factory, periodes.start_date, periodes.end_date, periodes.holiday, job_sections.job_section_name, factories.factory_name')
            ->join('periodes', 'sum_bsmc.tgl_input BETWEEN periodes.start_date AND periodes.end_date', 'inner')
            ->join('employees', 'employees.id_employee = sum_bsmc.id_employee', 'inner')
            ->join('job_sections', 'job_sections.id_job_section = employees.id_job_section', 'inner')
            ->join('factories', 'factories.id_factory = sum_bsmc.id_factory')
            ->where('periodes.id_batch', $id_batch)
            ->where('factories.factory_name', $factory_name)
            ->groupBy('employees.employee_code, periodes.start_date, periodes.end_date') // Grouping berdasarkan kode_kartu dan periode
            ->findAll();
    }

    public function getCurrentInput()
    {
        return $this->select('tgl_input')
            ->orderBy('tgl_input', 'DESC')
            ->limit(1)
            ->first();
    }

    public function getFilteredData($area, $startDate, $endDate)
    {
        return $this->select('sum_bsmc.*, employees.*, job_sections.*, factories.*')
            ->join('employees', 'employees.id_employee = sum_bsmc.id_employee')
            ->join('job_sections', 'job_sections.id_job_section = employees.id_job_section')
            ->join('factories', 'factories.id_factory = employees.id_factory')
            ->where('factories.factory_name', $area)
            ->where('sum_bsmc.tgl_input >=', $startDate)
            ->where('sum_bsmc.tgl_input <=', $endDate)
            ->orderBy('sum_bsmc.tgl_input', 'ASC')
            ->findAll();
    }

    public function getProductivityData($id_batch, $main_factory)
    {
        return $this->select(' sum_bsmc.id_employee, employees.employee_code, employees.employee_name,sum_bsmc.tgl_input, SUM(sum_bsmc.produksi) AS total_produksi, SUM(sum_bsmc.bs_mc) AS total_bs, sum_bsmc.id_factory,
            , periodes.id_periode, periodes.periode_name, batches.batch_name')
            ->join('periodes', 'sum_bsmc.tgl_input BETWEEN periodes.start_date AND periodes.end_date', 'left')
            ->join('batches', 'batches.id_batch = periodes.id_batch', 'left')
            ->join('employees', 'employees.id_employee = sum_bsmc.id_employee', 'left')
            ->join('job_sections', 'job_sections.id_job_section = employees.id_job_section', 'left')
            ->join('factories', 'factories.id_factory = sum_bsmc.id_factory')
            ->where('periodes.id_batch', $id_batch)
            ->where('factories.main_factory', $main_factory)
            ->groupBy('employees.employee_code, periodes.id_periode')
            ->findAll();
    }
}
