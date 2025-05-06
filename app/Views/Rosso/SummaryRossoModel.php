<?php

namespace App\Models;

use CodeIgniter\Model;

class SummaryRossoModel extends Model
{
    protected $table            = 'sum_rosso';
    protected $primaryKey       = 'id_sr';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['id_sr', 'id_karyawan', 'tgl_input', 'produksi', 'perbaikan', 'created_at', 'updated_at', 'area'];

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

    public function getData()
    {
        return $this->db->table('sum_rosso')
            ->join('karyawan', 'karyawan.id_karyawan = sum_rosso.id_karyawan')
            ->join('bagian', 'bagian.id_bagian = karyawan.id_bagian')
            ->get()->getResultArray();
    }

    public function getDatabyAreaUtama($area_utama)
    {
        return $this->db->table('sum_rosso')
            ->join('karyawan', 'karyawan.id_karyawan = sum_rosso.id_karyawan')
            ->join('bagian', 'bagian.id_bagian = karyawan.id_bagian')
            ->where('bagian.area_utama', $area_utama)
            ->get()->getResultArray();
    }
    public function getDatabyAreaUtamaAndPeriodeInBatch($area_utama, $id_batch)
    {
        return $this->db->table('sum_rosso')
            ->join('karyawan', 'karyawan.id_karyawan = sum_rosso.id_karyawan')
            ->join('bagian', 'bagian.id_bagian = karyawan.id_bagian')
            ->join('batch', 'batch.id_batch = sum_rosso.id_batch')
            ->where('batch.id_batch', $id_batch)
            ->where('bagian.area_utama', $area_utama)
            ->get()->getResultArray();
    }


    public function getDataById($id)
    {
        return $this->db->table('sum_rosso')
            ->join('karyawan', 'karyawan.id_karyawan = sum_rosso.id_karyawan')
            ->where('id_sr', $id)
            ->get()->getRowArray();
    }

    public function sumRosso()
    {
        return $this->select('SUM(qty_prod_rosso) as total_qty_prod_rosso, SUM(qty_bs) as total_qty_bs, karyawan.id_bagian, bagian.nama_bagian, karyawan.kode_kartu, karyawan.nama_karyawan, karyawan.jenis_kelamin, karyawan.tgl_masuk')
            ->join('karyawan', 'karyawan.id_karyawan = sum_rosso.id_karyawan')
            ->join('bagian', 'bagian.id_bagian = karyawan.id_bagian')
            ->groupBy('sum_rosso.id_karyawan')
            ->get()->getResultArray();
    }

    public function getRossoGroupByPeriode()
    {
        return $this->select('SUM(qty_prod_rosso) as total_qty_prod_rosso, SUM(qty_bs) as total_qty_bs, karyawan.id_bagian, bagian.nama_bagian, karyawan.kode_kartu, karyawan.nama_karyawan, karyawan.jenis_kelamin, karyawan.tgl_masuk, periode.start_date, periode.end_date, periode.id_periode, periode.nama_periode, batch.nama_batch, batch.id_batch')
            ->join('karyawan', 'karyawan.id_karyawan = sum_rosso.id_karyawan')
            ->join('bagian', 'bagian.id_bagian = karyawan.id_bagian')
            ->join('periode', 'periode.id_periode = sum_rosso.id_periode')
            ->join('batch', 'batch.id_batch = periode.id_batch')
            ->groupBy('sum_rosso.id_periode')
            ->get()->getResultArray();
    }

    public function getRossoByPeriode($id_periode)
    {
        return $this->select(
            'karyawan.id_bagian, 
             bagian.nama_bagian, 
             karyawan.kode_kartu, 
             karyawan.nama_karyawan, 
             karyawan.jenis_kelamin, 
             karyawan.tgl_masuk, 
             periode.start_date, 
             periode.end_date,
             periode.jml_libur,
             SUM(qty_prod_rosso) as total_qty_prod_rosso, 
             SUM(qty_bs) as total_qty_bs'
        )
            ->join('karyawan', 'karyawan.id_karyawan = sum_rosso.id_karyawan')
            ->join('bagian', 'bagian.id_bagian = karyawan.id_bagian')
            ->join('periode', 'periode.id_periode = sum_rosso.id_periode')
            ->where('sum_rosso.id_periode', $id_periode)
            ->groupBy('sum_rosso.id_karyawan')
            ->get()->getResultArray();
    }

    public function getTop3Produksi($area_utama, $id_batch)
    {
        return $this->select('AVG(sum_rosso.produksi) AS average_produksi, AVG(sum_rosso.perbaikan) AS average_bs,karyawan.id_karyawan, karyawan.nama_karyawan, karyawan.kode_kartu, karyawan.jenis_kelamin, karyawan.tgl_masuk, bagian.nama_bagian, batch.nama_batch')
            ->join('karyawan', 'karyawan.id_karyawan = sum_rosso.id_karyawan')
            ->join('bagian', 'bagian.id_bagian = karyawan.id_bagian')
            ->join('periode', 'sum_rosso.tgl_input BETWEEN periode.start_date AND periode.end_date') // Hubungkan berdasarkan tgl_input
            ->join('batch', 'batch.id_batch = periode.id_batch') // Hubungkan batch dengan periode
            ->where('bagian.area_utama', $area_utama)
            ->where('batch.id_batch', $id_batch)
            ->groupBy('sum_rosso.id_karyawan') // Group by id_karyawan
            ->orderBy('average_produksi', 'DESC') // Order by highest production
            ->orderBy('average_bs', 'ASC') // Order by lowest defect
            ->limit(3) // Limit to top 3
            ->get()->getResultArray();
    }


    public function getMinAvgBS($area_utama, $id_batch)
    {
        return $this->select('MIN(sum_rosso.average_bs) as min_avg_bs,sum_rosso.average_bs, sum_rosso.average_produksi,karyawan.id_karyawan, karyawan.nama_karyawan, karyawan.kode_kartu, karyawan.jenis_kelamin, karyawan.tgl_masuk, bagian.nama_bagian, periode.nama_periode, batch.nama_batch')
            ->join('karyawan', 'karyawan.id_karyawan = sum_rosso.id_karyawan')
            ->join('bagian', 'bagian.id_bagian = karyawan.id_bagian')
            ->join('batch', 'batch.id_batch = periode.id_batch')
            ->where('bagian.area_utama', $area_utama)
            ->where('periode.id_batch', $id_batch)
            ->get()->getRowArray();
    }

    public function getTop3LowestBS($area_utama, $id_batch)
    {
        // Step 1: Get the top 7 data based on highest production
        $top7Data = $this->select('AVG(sum_rosso.produksi) AS average_produksi, AVG(sum_rosso.perbaikan) AS average_bs,karyawan.id_karyawan, karyawan.nama_karyawan, karyawan.kode_kartu, karyawan.jenis_kelamin, karyawan.tgl_masuk, bagian.nama_bagian, batch.nama_batch')
            ->join('karyawan', 'karyawan.id_karyawan = sum_rosso.id_karyawan')
            ->join('bagian', 'bagian.id_bagian = karyawan.id_bagian')
            ->join('periode', 'sum_rosso.tgl_input BETWEEN periode.start_date AND periode.end_date') // Hubungkan berdasarkan tgl_input
            ->join('batch', 'batch.id_batch = periode.id_batch') // Hubungkan batch dengan periode
            ->where('bagian.area_utama', $area_utama)
            ->where('batch.id_batch', $id_batch)
            ->groupBy('sum_rosso.id_karyawan') // Group by id_karyawan
            ->orderBy('average_produksi', 'DESC') // Order by highest production
            ->limit(7) // Limit to top 7 results based on production
            ->get()->getResultArray();

        // Step 2: Sort these 7 results by average_bs in ascending order
        usort($top7Data, function ($a, $b) {
            return $a['average_bs'] <=> $b['average_bs']; // Sort by average_bs (lowest first)
        });

        // Step 3: Return the first 3 with the lowest average_bs
        return array_slice($top7Data, 0, 3); // Return the top 3 with the lowest defects
    }

    public function validasiKaryawan($tgl_input, $id_karyawan)
    {
        return $this->select('sum_rosso.id_sr, sum_rosso.tgl_input, sum_rosso.id_karyawan, karyawan.kode_kartu, karyawan.nama_karyawan, karyawan.tgl_lahir, bagian.area')
            ->join('karyawan', 'karyawan.id_karyawan = sum_rosso.id_karyawan')
            ->join('bagian', 'bagian.id_bagian = karyawan.id_bagian')
            ->where('sum_rosso.tgl_input', $tgl_input)
            ->where('sum_rosso.id_karyawan', $id_karyawan)
            ->first();
    }

    public function getSummaryRosso($area, $id_batch)
    {
        return $this->select('sum_rosso.id_karyawan, karyawan.kode_kartu, karyawan.nama_karyawan, karyawan.jenis_kelamin, karyawan.tgl_masuk,  SUM(sum_rosso.produksi) AS total_produksi, SUM(sum_rosso.perbaikan) AS total_perbaikan, periode.nama_periode, periode.id_batch, sum_rosso.area, periode.start_date, periode.end_date, periode.jml_libur, bagian.nama_bagian')
            ->join('periode', 'sum_rosso.tgl_input BETWEEN periode.start_date AND periode.end_date', 'inner')
            ->join('karyawan', 'karyawan.id_karyawan = sum_rosso.id_karyawan', 'inner')
            ->join('bagian', 'bagian.id_bagian = karyawan.id_bagian', 'inner')
            ->where('sum_rosso.area', $area)
            ->where('periode.id_batch', $id_batch)
            ->groupBy('karyawan.kode_kartu, periode.start_date, periode.end_date') // Grouping berdasarkan kode_kartu dan periode
            ->findAll();
    }

    public function getFilteredData($area_utama, $startDate, $endDate)
    {
        return $this->select('sum_rosso.*, karyawan.kode_kartu, karyawan.nama_karyawan')
            ->join('karyawan', 'karyawan.id_karyawan = sum_rosso.id_karyawan')
            ->where('area', $area_utama)
            ->where('tgl_input >=', $startDate)
            ->where('tgl_input <=', $endDate)
            ->orderBy('tgl_input', 'ASC')
            ->findAll();
    }

    public function getCurrentInput()
    {
        return $this->select('sum_rosso.tgl_input')
            ->orderBy('sum_rosso.tgl_input', 'DESC')
            ->limit(1)
            ->first();
    }

    public function getTopProduksiRosso($area, $id_batch, $limit = 7)
    {
        $db = \Config\Database::connect();

        $sql = "
    SELECT 
        produksi_per_periode.id_karyawan,
        karyawan.nama_karyawan,
        karyawan.kode_kartu,
        karyawan.jenis_kelamin,
        karyawan.tgl_masuk,
        bagian.nama_bagian,
        batch.id_batch,
        -- Jumlah periode aktual dipakai untuk pembagian
        (SUM(produksi_per_periode.avg_produksi) / COUNT(produksi_per_periode.nama_periode)) AS rata_rata_produksi
    FROM (
        SELECT 
            sum_rosso.id_karyawan,
            periode.nama_periode,
            SUM(sum_rosso.produksi) AS total_produksi,
            (DATEDIFF(periode.end_date, periode.start_date) + 1 - periode.jml_libur) AS hari_kerja,
            (SUM(sum_rosso.produksi) / (DATEDIFF(periode.end_date, periode.start_date) + 1 - periode.jml_libur)) AS avg_produksi
        FROM sum_rosso
        JOIN periode 
            ON sum_rosso.tgl_input BETWEEN periode.start_date AND periode.end_date
        WHERE periode.id_batch = ?
        GROUP BY sum_rosso.id_karyawan, periode.nama_periode
    ) AS produksi_per_periode
    JOIN karyawan 
        ON karyawan.id_karyawan = produksi_per_periode.id_karyawan
    JOIN bagian 
        ON bagian.id_bagian = karyawan.id_bagian
    JOIN batch 
        ON batch.id_batch = ?
    WHERE bagian.area = ?
    GROUP BY produksi_per_periode.id_karyawan
    ORDER BY rata_rata_produksi DESC
    LIMIT ?
    ";

        return $db->query($sql, [$id_batch, $id_batch, $area, (int)$limit])
            ->getResultArray();
    }

    public function getTop3BsRossoFromList(array $ids, $id_batch)
    {
        if (empty($ids)) return [];

        $db = \Config\Database::connect();
        $in = implode(',', array_map('intval', $ids));

        $sql = "
    SELECT 
        bs_per_periode.id_karyawan,
        (SUM(bs_per_periode.avg_bs_per_periode) / 3) AS avg_bs
    FROM (
        SELECT 
            sum_rosso.id_karyawan,
            p.nama_periode,
            SUM(sum_rosso.perbaikan) AS total_perbaikan,
            (DATEDIFF(p.end_date, p.start_date) + 1 - p.jml_libur) AS hari_kerja,
            (SUM(sum_rosso.perbaikan) / (DATEDIFF(p.end_date, p.start_date) + 1 - p.jml_libur)) AS avg_bs_per_periode
        FROM sum_rosso
        JOIN periode p ON sum_rosso.tgl_input BETWEEN p.start_date AND p.end_date
        WHERE p.id_batch = ?
          AND sum_rosso.id_karyawan IN ($in)
        GROUP BY sum_rosso.id_karyawan, p.nama_periode
    ) AS bs_per_periode
    GROUP BY bs_per_periode.id_karyawan
    ORDER BY avg_bs ASC
    LIMIT 3
    ";

        return $db->query($sql, [$id_batch])->getResultArray();
    }
}
