<?php

namespace App\Models;

use CodeIgniter\Model;

class JarumModel extends Model
{
    protected $table            = 'sum_jarum';
    protected $primaryKey       = 'id_sj';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_employee',
        'tgl_input',
        'used_needle',
        'id_factory',
        'created_at',
        'updated_at'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = false;
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
        return $this->select('sum_jarum.tgl_input')
            ->orderBy('sum_jarum.tgl_input', 'DESC')
            ->limit(1)
            ->first();
    }

    public function getDatabyArea($area)
    {
        return $this->db->table('sum_jarum')
            ->join('employees', 'employees.id_employee = sum_jarum.id_employee')
            ->join('job_sections', 'job_sections.id_job_section = employees.id_job_section')
            ->join('factories', 'factories.id_factory = employees.id_factory')
            ->where('factories.factory_name', $area)
            ->get()->getResultArray();
    }

    public function getSummaryJarum($id_factory, $id_batch)
    {
        return $this->select('sum_jarum.id_sj, sum_jarum.id_employee, sum_jarum.tgl_input, SUM(sum_jarum.used_needle) AS total_jarum, sum_jarum.id_factory,
            sum_jarum.*, periodes.*, job_sections.*, factories.*, employees.*')
            ->join('periodes', 'sum_jarum.tgl_input BETWEEN periodes.start_date AND periodes.end_date', 'inner')
            ->join('employees', 'employees.id_employee = sum_jarum.id_employee', 'inner')
            ->join('job_sections', 'job_sections.id_job_section = employees.id_job_section', 'inner')
            ->join('factories', 'factories.id_factory = employees.id_factory', 'inner')
            ->join('history_employees h', 'h.id_employee = sum_jarum.id_employee', 'left')
            // ->join('history_pindah_karyawan h', 'h.id_employee = sum_jarum.id_employee', 'left')
            ->where('periodes.id_batch', $id_batch)
            // Group kondisi: id_factory saat ini OR data id_factory lama sebelum tanggal pindah
            ->groupStart()
            // 1) Data di id_factory target
            ->where('sum_jarum.id_factory', $id_factory)
            // 2) OR data dari id_factory asal, tapi hanya yg tgl_input < tanggal_pindah
            ->orWhere(
            "sum_jarum.id_factory = (
                    SELECT id_factory_old 
                    FROM history_employees h
                    WHERE h.id_employee = sum_jarum.id_employee
                )
                AND sum_jarum.tgl_input < h.date_of_change",
                null,
                false
            )
            ->groupEnd()
            ->groupBy('employees.employee_code, periodes.start_date, periodes.end_date')
            ->findAll();
    }

    public function getFilteredData($area, $startDate, $endDate)
    {
        return $this->select('sum_jarum.*, employees.employee_code, employees.employee_name, factories.factory_name')
            ->join('employees', 'employees.id_employee = sum_jarum.id_employee')
            ->join('job_sections', 'job_sections.id_job_section = employees.id_job_section')
            ->join('factories', 'factories.id_factory = employees.id_factory')
            ->where('factories.factory_name', $area)
            ->where('tgl_input >=', $startDate)
            ->where('tgl_input <=', $endDate)
            ->orderBy('tgl_input', 'ASC')
            ->findAll();
    }

    public function getJarumByData($idEmployee, $tglInput, $idFactory)
    {
        return $this->where('id_employee', $idEmployee)
            ->where('tgl_input', $tglInput)
            ->where('id_factory', $idFactory)
            ->first();
    }
}
