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
}
