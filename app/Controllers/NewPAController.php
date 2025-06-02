<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\NewPAModel;
use App\Models\EmployeeAssessmentModel;
class NewPAController extends BaseController
{
    protected $newPAModel;
    protected $employeeAssessmentModel;
    protected $role;

    public function __construct()
    {
        $this->role = session()->get('role');
        if (!$this->role) {
            return redirect()->to(base_url('login'));
        }
        $this->newPAModel = new NewPAModel();
        $this->employeeAssessmentModel = new EmployeeAssessmentModel();
    }

    // fecth data assessment to be used in newPA
    // public function fetchAssessmentData()
    // {
    //     $data = $this->employeeAssessmentModel->getData();
    //     // Check if data is empty
    //     if (empty($data)) {
    //         return $this->response->setJSON(['message' => 'No assessment data found.']);
    //     }
    //     // Process the data to presentase raw
    //     $assessmentData = [];
    //     foreach ($data as $row) {
    //         $assessmentData[] = [
    //             'id_employee' => $row['id_employee'],
    //             // 'employee_code' => $row['employee_code'],
    //             // 'employee_name' => $row['employee_name'],
    //             // 'main_job_role_name' => $row['main_job_role_name'],
    //             'id_periode' => $row['id_periode'],
    //             'id_main_job_role' => $row['id_main_job_role'],
    //             // 'periode_name' => $row['periode_name'],
    //             // 'total_score' => $row['total_score'],
    //             'performance_score' => number_format(($row['total_score']/($row['ttlJobdesk']*6))*100,2, '.', ''),
    //             'id_factory' => $row['id_factory'],
    //             // 'factory_name' => $row['factory_name'],
    //             'id_user' => $row['id_user'],
    //             // $existingData = $this->newPAModel->where('id_employee', $row['id_employee'])
    //             //     ->where('id_periode', $row['id_periode'])
    //             //     ->first(),

    //         ];
    //     }
    //     // dd ($assessmentData);    
    //     // return $this->response->setJSON($assessmentData);
    //     // Check if there is existing data for the employee and period
    //     // if (!empty($existingData)) {
    //     //     return $this->response->setJSON(['message' => 'Data already exists for this employee and period.']);
    //     // }
    //     // // Insert the data into the new performance assessments table
    //     // $this->newPAModel->insertBatch($assessmentData);
    //     // return $this->response->setJSON(['message' => 'Data successfully inserted into new performance assessments table.']);

    //     // cek data yang sudah ada di tabel new_performance_assessments
    //     $existingData = $this->newPAModel->findAll();
    //     foreach ($assessmentData as $data) {
    //         // jika data sudah ada, skip insert
    //         if (in_array($data, $existingData)) {
    //             continue;
    //         }
    //         // jika data belum ada, insert ke tabel new_performance_assessments
    //         $this->newPAModel->insert($data);
    //     }
    //     return $this->response->setJSON(['message' => 'Data successfully inserted into new performance assessments table.']);
    // }

    public function fetchAssessmentData()
    {
        // 1. Ambil data mentah dari model
        $data = $this->employeeAssessmentModel->getData();
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
                'id_factory' => $row['id_factory'],
                'id_user'    => $row['id_user'],
            ];
        }

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
