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
    public function fetchAssessmentData()
    {
        $data = $this->employeeAssessmentModel->getData();
        // Check if data is empty
        if (empty($data)) {
            return $this->response->setJSON(['message' => 'No assessment data found.']);
        }
        // Process the data to presentase raw
        $assessmentData = [];
        foreach ($data as $row) {
            $assessmentData[] = [
                'id_employee' => $row['id_employee'],
                // 'employee_code' => $row['employee_code'],
                // 'employee_name' => $row['employee_name'],
                // 'main_job_role_name' => $row['main_job_role_name'],
                'id_periode' => $row['id_periode'],
                'id_main_job_role' => $row['id_main_job_role'],
                // 'periode_name' => $row['periode_name'],
                // 'total_score' => $row['total_score'],
                'performance_score' => number_format(($row['total_score']/($row['ttlJobdesk']*6))*100,2, '.', ''),
                'id_factory' => $row['id_factory'],
                // 'factory_name' => $row['factory_name'],
                'id_user' => $row['id_user'],
                // $existingData = $this->newPAModel->where('id_employee', $row['id_employee'])
                //     ->where('id_periode', $row['id_periode'])
                //     ->first(),

            ];
        }
        // dd ($assessmentData);    
        // return $this->response->setJSON($assessmentData);
        // Check if there is existing data for the employee and period
        // if (!empty($existingData)) {
        //     return $this->response->setJSON(['message' => 'Data already exists for this employee and period.']);
        // }
        // // Insert the data into the new performance assessments table
        $this->newPAModel->insertBatch($assessmentData);
        // return $this->response->setJSON(['message' => 'Data successfully inserted into new performance assessments table.']);
    }
    public function index()
    {
        //
    }
}
