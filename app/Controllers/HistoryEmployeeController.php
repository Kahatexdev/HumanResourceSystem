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

    /**
     * Format date from Excel or string to Y-m-d format.
     */
    private function formattedDate($date)
    {
        if (empty($date)) {
            return null;
        }
        // If the date is a numeric value (Excel date)
        if (is_numeric($date)) {
            return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($date)->format('Y-m-d');
        }
        // Try to parse as string date
        $timestamp = strtotime($date);
        if ($timestamp !== false) {
            return date('Y-m-d', $timestamp);
        }
        return null;
    }

    public function updateEmployeeCode()
    {
        // Ambil data dari excel
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
            // if ($row->getRowIndex() >= $worksheet->getHighestRow() - 1) break;

            $data[] = [
                'employee_code_old' => $rowData[1],
                'employee_code_new' => $rowData[2],
                'employee_name' => $rowData[3],
                'date_of_birth' => $this->formattedDate($rowData[4]),
                'date_of_joining' => $this->formattedDate($rowData[5])
            ];
        }
        // dd ($data);
        $updateData = [];
        $successMessages = [];
        $errorMessages = [];
        foreach ($data as $row) {
            $employee = $this->employeeModel
                ->where('employee_code', $row['employee_code_old'])
                ->orWhere('employee_name', $row['employee_name'])
                ->orWhere('date_of_birth', $row['date_of_birth'])
                ->orWhere('date_of_joining', $row['date_of_joining'])
                ->first();
            // Cek apakah karyawan ditemukan
            if (!$employee) {
                $errorMessages[] = 'Karyawan dengan kode ' . $row['employee_code_old'] . ' tidak ditemukan.';
                continue;
            }
            // Cek apakah kode baru sudah ada
            $existingEmployee = $this->employeeModel
                ->where('employee_code', $row['employee_code_new'])
                ->first();
            if ($existingEmployee) {
                $errorMessages[] = 'Kode baru ' . $row['employee_code_new'] . ' sudah digunakan oleh karyawan lain.';
                continue;
            }
            // Update kode karyawan
            $this->employeeModel->update($employee['id_employee'], [
                'employee_code' => $row['employee_code_new'],
                'updated_at' => date('Y-m-d H:i:s')
            ]);
            // insert ke history_employee
            $this->historyEmployeeModel->insert([
                'id_employee' => $employee['id_employee'],
                'id_job_section_old' => $employee['id_job_section'], // Asumsi id_job_section sudah ada di model Employee
                'id_factory_old' => $employee['id_factory'], // Asumsi id_factory sudah ada di model Employee
                'id_job_section_new' => $employee['id_job_section'], // Asumsi id_job_section_new sama dengan id_job_section
                'id_factory_new' => $employee['id_factory'], // Asumsi id_factory_new sama dengan id_factory
                'date_of_change' => date('Y-m-d H:i:s'),
                'reason' => 'Update Kode Kartu dari ' . $row['employee_code_old'] . ' ke ' . $row['employee_code_new'],
                'id_user' => session()->get('id_user'), // Ambil dari session
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
            $successMessages[] = 'Kode karyawan ' . $row['employee_code_old'] . ' berhasil diperbarui menjadi ' . $row['employee_code_new'] . '.';
        }
        if (!empty($successMessages)) {
            // Kalau ada yang sukses, tampilkan dua-duanya
            $combinedMessages = implode('<br>', array_merge($successMessages, $errorMessages));
            session()->setFlashdata('success', $combinedMessages);
        } elseif (!empty($errorMessages)) {
            // Kalau semuanya gagal
            session()->setFlashdata('error', implode('<br>', $errorMessages));
        }
        return redirect()->back();
    }
}
