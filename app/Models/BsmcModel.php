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
        'prodkusi',
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

    public function getDataSumBsmc()
    {
        return $this->select('sum_bsmc.*')
            ->orderBy('sum_bsmc.tgl_input', 'DESC')
            ->findAll();
    }
    public function getCurrentInput()
    {
        return $this->select('tgl_input')
            ->orderBy('tgl_input', 'DESC')
            ->limit(1)
            ->first();
    }
}
