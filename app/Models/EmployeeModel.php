<?php

namespace App\Models;

use CodeIgniter\Model;

class EmployeeModel extends Model
{
    protected $table            = 'employees';
    protected $primaryKey       = 'id_employee';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_employee',
        'employee_name',
        'employee_code',
        'shift',
        'gender',
        'id_job_section',
        'id_factory',
        'id_employment_status',
        'holiday',
        'additional_holiday',
        'date_of_birth',
        'date_of_joining',
        'status'
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

    public function getEmployeeData()
    {
        return $this->db->table('employees')
            ->select('employees.*,
                       job_sections.*,
                       factories.*,
                       employment_statuses.*,
                       days.day_name AS holiday_name,
                       days2.day_name AS additional_holiday_name')
            ->join('job_sections', 'job_sections.id_job_section = employees.id_job_section')
            ->join('factories', 'factories.id_factory = employees.id_factory')
            ->join('employment_statuses', 'employment_statuses.id_employment_status = employees.id_employment_status')
            ->join('days', 'days.id_day = employees.holiday')
            ->join('days AS days2', 'days2.id_day = employees.additional_holiday', 'left')
            ->groupBy('employees.id_employee')
            ->get()
            ->getResultArray();
    }

    public function getEmployeeDataS(array $filters = []): array
    {
        $builder = $this->db->table($this->table)
            ->select('employees.*,
                       job_sections.job_section_name,
                       factories.factory_name,
                       factories.main_factory,
                       employment_statuses.employment_status_name,
                       days.day_name')
            ->join('job_sections',         'job_sections.id_job_section = employees.id_job_section', 'left')
            ->join('factories',            'factories.id_factory = employees.id_factory',           'left')
            ->join('employment_statuses',  'employment_statuses.id_employment_status = employees.id_employment_status', 'left')
            ->join(
                'days',
                "days.id_day = employees.holiday OR days.id_day = employees.additional_holiday",
                'left'
            );

        if (! empty($filters['job_section_name'])) {
            $builder->where('job_sections.job_section_name', $filters['job_section_name']);
        }
        if (! empty($filters['main_factory'])) {
            $builder->where('factories.main_factory',     $filters['main_factory']);
        }
        if (! empty($filters['factory_name'])) {
            $builder->where('factories.factory_name',           $filters['factory_name']);
        }

        return $builder
            ->groupBy('employees.id_employee')
            ->get()
            ->getResultArray();
    }

    public function getKaryawanByAreaUtama($areaUtama)
    {
        return $this->db->table('employees')
            ->select('employees.id_employee AS id_karyawan, employees.employee_name AS nama_karyawan, employees.employee_code AS kode_kartu, employees.shift, employees.gender AS jenis_kelamin, employees.date_of_birth AS tgl_lahir, employees.date_of_joining AS tanggal_masuk, employees.status AS status_aktif,
                       job_sections.job_section_name AS nama_bagian,
                       factories.factory_name AS area, factories.main_factory AS area_utama,
                       employment_statuses.clothes_color AS warna_baju, employment_statuses.employment_status_name AS status_baju,
                       days.day_name AS libur,
                       days2.day_name AS libur_tambahan')
            ->join('job_sections', 'job_sections.id_job_section = employees.id_job_section')
            ->join('factories', 'factories.id_factory = employees.id_factory')
            ->join('employment_statuses', 'employment_statuses.id_employment_status = employees.id_employment_status')
            ->join('days', 'days.id_day = employees.holiday')
            ->join('days AS days2', 'days2.id_day = employees.additional_holiday', 'left')
            ->where('factories.main_factory', $areaUtama)
            ->groupBy('employees.id_employee')
            ->get()
            ->getResultArray();
    }
    public function getKaryawanByAreaApi($area)
    {
        return $this->db->table('employees')
            ->select('employees.id_employee AS id_karyawan, employees.employee_name AS nama_karyawan, employees.employee_code AS kode_kartu, employees.shift, employees.gender AS jenis_kelamin, employees.date_of_birth AS tgl_lahir, employees.date_of_joining AS tanggal_masuk, employees.status AS status_aktif,
                       job_sections.job_section_name AS nama_bagian,
                       factories.factory_name AS area, factories.main_factory AS area_utama,
                       employment_statuses.clothes_color AS warna_baju, employment_statuses.employment_status_name AS status_baju,
                       days.day_name AS libur,
                       days2.day_name AS libur_tambahan')
            ->join('job_sections', 'job_sections.id_job_section = employees.id_job_section')
            ->join('factories', 'factories.id_factory = employees.id_factory')
            ->join('employment_statuses', 'employment_statuses.id_employment_status = employees.id_employment_status')
            ->join('days', 'days.id_day = employees.holiday')
            ->join('days AS days2', 'days2.id_day = employees.additional_holiday', 'left')
            ->where('factories.factory_name', $area)
            ->groupBy('employees.id_employee')
            ->get()
            ->getResultArray();
    }

    public function getActiveKaryawanByBagiaAndArea()
    {
        return $this->db->table('employees')
            ->select('employees.*,COUNT(employees.id_employee) AS jumlah_karyawan,
                       job_sections.*,
                       factories.*,
                       employment_statuses.*,
                       days.day_name AS holiday_name,
                       days2.day_name AS additional_holiday_name')
            ->join('job_sections', 'job_sections.id_job_section = employees.id_job_section')
            ->join('factories', 'factories.id_factory = employees.id_factory')
            ->join('employment_statuses', 'employment_statuses.id_employment_status = employees.id_employment_status')
            ->join('days', 'days.id_day = employees.holiday')
            ->join('days AS days2', 'days2.id_day = employees.additional_holiday', 'left')
            ->where('employees.status', 'Aktif')
            ->groupBy('factories.factory_name')
            ->groupBy('job_sections.job_section_name')
            ->get()
            ->getResultArray();
    }

    public function getKaryawanTanpaArea()
    {
        return $this->db->table('employees')
            ->select('employees.*,
                       job_sections.*,
                       factories.*,
                       employment_statuses.*,
                       days.day_name AS holiday_name,
                       days2.day_name AS additional_holiday_name')
            ->join('job_sections', 'job_sections.id_job_section = employees.id_job_section')
            ->join('factories', 'factories.id_factory = employees.id_factory')
            ->join('employment_statuses', 'employment_statuses.id_employment_status = employees.id_employment_status')
            ->join('days', 'days.id_day = employees.holiday')
            ->join('days AS days2', 'days2.id_day = employees.additional_holiday', 'left')
            ->groupBy('employees.id_employee')
            ->get()
            ->getResultArray();
    }

    public function getKaryawanByArea($area)
    {
        return $this->db->table('employees')
            ->select('employees.*,
                       job_sections.*,
                       factories.*,
                       employment_statuses.*,
                       days.day_name AS holiday_name,
                       days2.day_name AS additional_holiday_name')
            ->join('job_sections', 'job_sections.id_job_section = employees.id_job_section')
            ->join('factories', 'factories.id_factory = employees.id_factory')
            ->join('employment_statuses', 'employment_statuses.id_employment_status = employees.id_employment_status')
            ->join('days', 'days.id_day = employees.holiday')
            ->join('days AS days2', 'days2.id_day = employees.additional_holiday', 'left')
            ->where('factories.main_factory', $area)
            ->groupBy('employees.id_employee')
            ->get()
            ->getResultArray();
    }

    public function getMontirByArea($area)
    {
        return $this->db->table('employees')
            ->select('employees.*,
                       job_sections.*,
                       factories.*')
            ->join('job_sections', 'job_sections.id_job_section = employees.id_job_section')
            ->join('factories', 'factories.id_factory = employees.id_factory')
            ->where('factories.id_factory', $area)
            ->like('job_sections.job_section_name', 'MONTIR')
            ->groupBy('employees.id_employee, factories.id_factory, job_sections.id_job_section')
            ->get()
            ->getResultArray();
    }

    public function getActiveEmployeeByJobSection()
    {
        return $this->select('COUNT(employees.employee_name) AS jumlah_employees, job_sections.job_section_name, factories.main_factory, factories.factory_name')
            ->join('job_sections', 'employees.id_job_section = job_sections.id_job_section')
            ->join('factories', 'factories.id_factory = employees.id_factory')
            ->where('employees.status', 'Aktif')
            ->groupBy('job_sections.job_section_name')
            ->findAll();
    }

    public function getKaryawanByFactoryName($area)
    {
        return $this->select('employees.id_employee, employees.employee_name, employees.employee_code, employees.shift, employees.id_factory, factories.factory_name')
            ->join('factories', 'factories.id_factory = employees.id_factory')
            ->where('factories.factory_name', $area)
            ->whereIn('employees.id_job_section', [11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 153, 154, 155])
            ->orderBy('employees.shift', 'ASC')
            ->findAll();
    }
}
