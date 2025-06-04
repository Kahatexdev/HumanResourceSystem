<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\NewPAModel;
use App\Models\EmployeeAssessmentModel;
use App\Models\FactoriesModel;
class NewPAController extends BaseController
{
    protected $newPAModel;
    protected $employeeAssessmentModel;
    protected $factoriesModel;
    protected $role;

    public function __construct()
    {
        $this->role = session()->get('role');
        if (!$this->role) {
            return redirect()->to(base_url('login'));
        }
        $this->newPAModel = new NewPAModel();
        $this->employeeAssessmentModel = new EmployeeAssessmentModel();
        $this->factoriesModel = new FactoriesModel();
    }

    public function fetchAssessmentData()
    {
        // 1. Ambil data mentah dari model
        $data = $this->employeeAssessmentModel->getData();
        // dd ($data);
        if (empty($data)) {
            return $this->response->setJSON([
                'message' => 'No assessment data found.',
                'inserted' => [],
                'skipped'  => []
            ]);
        }

        // 2. Persiapkan array berisi data yang akan dicek/insert
        $assessmentData = [];
        foreach ($data as $row) {
            if($row['factory_name'] != $row['user_area'])
            {
                $findIdFactory = $this->factoriesModel
                    ->where('factory_name', $row['user_area'])
                    ->first();
                if ($findIdFactory) {
                    $row['id_factory'] = $findIdFactory['id_factory'];
                } else {
                    // Jika tidak ditemukan, skip record ini
                    continue;
                }
            } else {
                $row['id_factory'] = $row['id_factory'];
            }
            $assessmentData[] = [
                'id_employee'       => $row['id_employee'],
                'id_periode'        => $row['id_periode'],
                'id_main_job_role'  => $row['id_main_job_role'],
                'performance_score' => number_format(
                    ($row['total_score'] / ($row['ttlJobdesk'] * 6)) * 100,
                    2,
                    '.',
                    ''
                ),
                'id_factory'        => $row['id_factory'],
                'id_user'    => $row['id_user'],
            ];
        }
        // dd ($assessmentData);
        // 3. Siapkan array untuk menampung hasil (inserted vs skipped)
        $inserted = [];
        $skipped  = [];
        
        // 4. Loop tiap record, cek eksistensi berdasarkan kombinasi id_employee + id_periode
        foreach ($assessmentData as $item) {
            $existing = $this->newPAModel
            ->where('id_employee', $item['id_employee'])
                ->where('id_periode',  $item['id_periode'])
                ->first();
                
                if ($existing) {
                    // Kalau sudah ada, tandai sebagai skipped
                    $skipped[] = [
                        'id_employee' => $item['id_employee'],
                        'id_periode'  => $item['id_periode']
                    ];
                    continue;
                }
                
                // Kalau belum ada, lakukan insert dan tandai sebagai inserted
                $this->newPAModel->insert($item);
                $inserted[] = [
                    'id_employee' => $item['id_employee'],
                    'id_periode'  => $item['id_periode']
                ];
            }
            // dd ($assessmentData, $inserted, $skipped);
        // 5. Kembalikan response JSON yang mencantumkan siapa saja yang inserted & siapa yang skipped
        return $this->response->setJSON([
            'message'  => 'Proses selesai.',
            'inserted' => $inserted,
            'skipped'  => $skipped
        ]);
    }

    public function index()
    {
        //
    }
}
