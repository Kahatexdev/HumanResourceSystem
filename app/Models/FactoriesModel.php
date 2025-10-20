<?php

namespace App\Models;

use CodeIgniter\Model;

class FactoriesModel extends Model
{
    protected $table            = 'factories';
    protected $primaryKey       = 'id_factory';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'factory_name',
        'main_factory',
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

    public function getMainFactoryGroupByMainFactory()
    {
        return $this->select('main_factory')
            ->where('main_factory !=', '-')
            ->where('factory_name !=', '-')
            ->groupBy('main_factory')
            ->findAll();
    }
    public function getFactoryName()
    {
        return $this->select('id_factory, factory_name')
            // ->where('main_factory !=', '-')
            // ->where('factory_name !=', '-')
            ->groupBy('factory_name')
            ->findAll();
    }
}
