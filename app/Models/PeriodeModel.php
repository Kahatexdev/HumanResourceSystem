<?php

namespace App\Models;

use CodeIgniter\Model;

class PeriodeModel extends Model
{
    protected $table            = 'periodes';
    protected $primaryKey       = 'id_periode';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_batch',
        'periode_name',
        'start_date',
        'end_date',
        'holiday',
        'status',
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

    public function getPeriode()
    {
        return $this->select('periodes.id_periode, periodes.periode_name, batches.id_batch, batches.batch_name, periodes.start_date, periodes.end_date, periodes.holiday, periodes.status')
            ->join('batches', 'batches.id_batch = periodes.id_batch')
            ->findAll();
    }

    public function getActivePeriode()
    {
        return $this->select('periodes.id_periode, periodes.periode_name, batches.id_batch, batches.batch_name, periodes.start_date, periodes.end_date, periodes.holiday, periodes.status')
            ->join('batches', 'batches.id_batch = periodes.id_batch')
            ->where('periodes.status', 'active')
            ->orderBy('periodes.created_at', 'DESC')
            ->first();
    }

    public function getPeriodeByNamaBatchAndNamaPeriode($batch_name, $periode_name)
    {
        $result = $this->select('periodes.id_periode, periodes.periode_name, batches.id_batch, batches.batch_name, periodes.start_date, periodes.end_date, periodes.holiday')
            ->join('batches', 'batches.id_batch = periodes.id_batch')
            ->where('batches.batch_name', $batch_name)
            ->where('periodes.periode_name', $periode_name)
            ->first();

        // Jika hasil ditemukan, tambahkan format nama bulan
        if ($result) {
            $formatter = new \IntlDateFormatter(
                'id_ID', // Locale untuk Bahasa Indonesia
                \IntlDateFormatter::LONG,
                \IntlDateFormatter::NONE,
                null,
                \IntlDateFormatter::GREGORIAN,
                'MMMM' // Format untuk nama bulan penuh
            );

            $result['nama_bulan'] = $formatter->format(new \DateTime($result['end_date']));
        }

        return $result;
    }
}
