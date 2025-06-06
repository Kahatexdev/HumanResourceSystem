<?php

namespace App\Models;

use CodeIgniter\Model;

class BatchModel extends Model
{
    protected $table            = 'batches';
    protected $primaryKey       = 'id_batch';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_batch',
        'batch_name',
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

    public function getBatch()
    {
        return $this->db->table('batches')
            ->get()->getResultArray();
    }

    public function getBulanPerBatch($idBatch)
    {
        return $this->db->table('batches')
            ->select('MONTH(periodes.end_date) as bulan')
            ->join('periodes', 'periodes.id_batch = batches.id_batch')
            ->where('periodes.id_batch', $idBatch)
            ->groupBy('MONTH(periodes.end_date)')
            ->get()->getResultArray();
    }
}
