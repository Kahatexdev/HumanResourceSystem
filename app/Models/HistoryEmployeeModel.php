<?php

namespace App\Models;

use CodeIgniter\Model;

class HistoryEmployeeModel extends Model
{
    protected $table            = 'history_employees';
    protected $primaryKey       = 'id_history_employee';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_history_employee',
        'id_employee',
        'id_job_section_old',
        'id_factory_old',
        'id_job_section_new',
        'id_factory_new',
        'date_of_change',
        'reason',
        'id_user',
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

    public function getPindahGroupedByDate()
    {
        return $this->select('DATE(date_of_change) as tgl, COUNT(*) as jumlah')
            ->groupBy('DATE(date_of_change)')
            ->orderBy('DATE(date_of_change)', 'DESC')
            ->findAll();
    }
    public function getHistoryPindahKaryawan()
    {
        return $this->db->table('history_employees')
            ->select('history_employees.*, employees.employee_code, employees.employee_name,  job_section_old.job_section_name AS job_section_old, 
                factories_old.factory_name AS factoryName_old, factories_old.main_factory AS mainFactory_old,
                factories_new.factory_name AS factoryName_new, factories_new.main_factory AS mainFactory_new,
                job_section_new.job_section_name AS job_section_new,
                users.username as updated_by')
            ->join('employees', 'employees.id_employee = history_employees.id_employee')
            ->join('factories AS factories_old', 'factories_old.id_factory = history_employees.id_factory_old')
            ->join('factories AS factories_new', 'factories_new.id_factory = history_employees.id_factory_new')
            ->join('job_sections AS job_section_old', 'job_section_old.id_job_section = history_employees.id_job_section_old')
            ->join('job_sections AS job_section_new', 'job_section_new.id_job_section = history_employees.id_job_section_new')
            ->join('users', 'users.id_user = history_employees.id_user')
            ->get()
            ->getResultArray();
    }
}
