<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\BsmcModel;
use App\Models\EmployeeModel;
use App\Models\FactoriesModel;

class BsmcController extends BaseController
{
    protected $role;
    protected $bsmcModel;
    protected $employeeModel;
    protected $factoryModel;

    public function __construct()
    {
        $this->bsmcModel = new BsmcModel();
        $this->employeeModel = new EmployeeModel();
        $this->factoryModel = new FactoriesModel();

        $this->role = session()->get('role');
    }

    public function index()
    {
        //
    }

    public function importExcelBsmc()
    {
        $file = $this->request->getFile('file');
        if ($file && $file->isValid()) {
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getTempName());
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();

            $dataToInsert = [];

            foreach ($rows as $index => $row) {
                if ($index === 0) continue; // Skip header

                // Ambil id_employee berdasarkan kode_kartu
                $employee = $this->employeeModel
                    ->where('employee_code', $row[0])
                    ->first();

                if (!$employee) continue; // Skip jika employee tidak ditemukan

                $factory = $this->factoryModel
                    ->where('factory_name', $row[2])
                    ->first();

                if (!$factory) continue; // Skip jika factory tidak ditemukan

                $dataToInsert[] = [
                    'id_employee' => $employee['id_employee'],
                    'id_factory'  => $factory['id_factory'],
                    'tgl_input'    => date('Y-m-d', strtotime($row[3])),
                    'produksi'     => $row[4],
                    'bs_mc'        => $row[5],
                    'created_at'  => date('Y-m-d H:i:s', strtotime($row[6])),
                    'updated_at'  => date('Y-m-d H:i:s', strtotime($row[7]))
                ];
            }

            // Simpan data ke DB
            if (!empty($dataToInsert)) {
                $this->bsmcModel->insertBatch($dataToInsert);
            }

            return redirect()->back()->with('success', 'Import berhasil!');
        }

        return redirect()->back()->with('error', 'File tidak valid');
    }
}
