<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

use App\Models\HistoryEmployeeModel;
use App\Models\EmployeeModel;
use App\Models\JobSectionModel;
use App\Models\JobRoleModel;
use App\Models\MainJobRoleModel;
use App\Models\BatchModel;
use App\Models\PeriodeModel;
use App\Models\PresenceModel;
use App\Models\EmployeeAssessmentModel;
use App\Models\PerformanceAssessmentModel;
use App\Models\FactoriesModel;
use App\Models\DayModel;
use App\Models\EmploymentStatusModel;
use App\Models\UserModel;

class HistoryEmployeeController extends BaseController
{
    protected $historyEmployeeModel;
    protected $employeeModel;
    protected $jobSectionModel;
    protected $jobRoleModel;
    protected $mainJobRoleModel;
    protected $batchModel;
    protected $periodeModel;
    protected $presenceModel;
    protected $employeeAssessmentModel;
    protected $performanceAssessmentModel;
    protected $factoriesModel;
    protected $days;
    protected $employmentStatusModel;
    protected $userModel;
    protected $role;
    public function __construct()
    {
        $this->historyEmployeeModel = new HistoryEmployeeModel();
        $this->employeeModel = new EmployeeModel();
        $this->jobSectionModel = new JobSectionModel();
        $this->jobRoleModel = new JobRoleModel();
        $this->mainJobRoleModel = new MainJobRoleModel();
        $this->batchModel = new BatchModel();
        $this->periodeModel = new PeriodeModel();
        $this->presenceModel = new PresenceModel();
        $this->employeeAssessmentModel = new EmployeeAssessmentModel();
        $this->performanceAssessmentModel = new PerformanceAssessmentModel();
        $this->factoriesModel = new FactoriesModel();
        $this->days = new DayModel();
        $this->employmentStatusModel = new EmploymentStatusModel();
        $this->userModel = new UserModel();
        $this->role = session()->get('role');
    }
    public function index()
    {
        //
    }

    public function importHistoryEmployee()
    {
        $file = $this->request->getFile('file');
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getTempName());
        $worksheet = $spreadsheet->getActiveSheet();
        $data = [];

        foreach ($worksheet->getRowIterator() as $row) {
            $rowData = [];
            foreach ($row->getCellIterator() as $cell) {
                $rowData[] = $cell->getValue();
            }

            if ($row->getRowIndex() < 4) continue;
            if ($row->getRowIndex() >= $worksheet->getHighestRow() - 1) break;

            $data[] = [
                'employee_code' => $rowData[1],
                'employee_name' => $rowData[2],
                'job_section_name_old' => $rowData[11],
                'factory_name_old' => $rowData[12],
                'job_section_name' => $rowData[13],
                'factory_name' => $rowData[14],
                'date_of_change' => $rowData[15],
                'reason' => $rowData[16],
                'username' => $rowData[18],
            ];
        }

        $insertData = [];
        $skippedRows = [];

        foreach ($data as $index => $row) {
            $employee = $this->employeeModel
                ->where('employee_code', $row['employee_code'])
                ->orWhere('employee_name', $row['employee_name'])
                ->first();

            $jobSectionOld = $this->jobSectionModel->where('job_section_name', $row['job_section_name_old'])->first();
            $factoryOld = $this->factoriesModel->where('factory_name', $row['factory_name_old'])->first();
            $jobSectionNew = $this->jobSectionModel->where('job_section_name', $row['job_section_name'])->first();
            $factoryNew = $this->factoriesModel->where('factory_name', $row['factory_name'])->first();
            $idUser = $this->userModel->where('username', $row['username'])->first();

            // Skip jika salah satu data tidak ditemukan
            if (!$employee || !$jobSectionOld || !$factoryOld || !$jobSectionNew || !$factoryNew) {
                $skippedRows[] = $row; // Bisa juga disimpan untuk laporan
                continue;
            }

            $insertData[] = [
                'id_employee' => $employee['id_employee'],
                'id_job_section_old' => $jobSectionOld['id_job_section'],
                'id_factory_old' => $factoryOld['id_factory'],
                'id_job_section_new' => $jobSectionNew['id_job_section'],
                'id_factory_new' => $factoryNew['id_factory'],
                'date_of_change' => $row['date_of_change'],
                'reason' => $row['reason'],
                'id_user' => $idUser['id_user'],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];
        }
        // dd ($data,$insertData, $skippedRows);
        // Simpan ke database
        if (!empty($insertData)) {
            $this->historyEmployeeModel->insertBatch($insertData);
            session()->setFlashdata('success', 'Data history karyawan berhasil diimpor.');
        } else {
            session()->setFlashdata('error', 'Tidak ada data yang valid untuk diimpor.');
        }
        // Jika ada baris yang dilewati, simpan untuk laporan
        if (!empty($skippedRows)) {
            session()->setFlashdata('skipped_rows', $skippedRows);
        }
        return redirect()->back()
            ->with('success', 'Data history karyawan berhasil diimpor.')
            ->with('skipped_rows', $skippedRows);
    }
}
