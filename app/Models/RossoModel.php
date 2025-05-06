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
}
