<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\BatchModel;
use App\Models\PeriodeModel;
use App\Models\RossoModel;
use App\Models\EmployeeModel;
use App\Models\FactoriesModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Font;
use DateTime;

class RossoController extends BaseController
{
    protected $rossoModel;
    protected $batchModel;
    protected $periodeModel;
    protected $employeeModel;
    protected $factoriesModel;
    protected $summaryRosso;
    protected $role;

    public function __construct()
    {
        $this->rossoModel = new RossoModel();
        $this->batchModel = new BatchModel();
        $this->periodeModel = new PeriodeModel();
        $this->employeeModel = new EmployeeModel();
        $this->factoriesModel = new FactoriesModel();
        $this->summaryRosso = new RossoModel();
        $this->role = session()->get('role');
    }
    public function index()
    {
        //
    }

    public function tampilPerBatch($area_utama)
    {
        $summaryRosso = $this->summaryRosso->getDatabyAreaUtama($area_utama);
        $batch = $this->batchModel->findAll();
        // dd ($summaryRosso);
        $data = [
            'role' => session()->get('role'),
            'title' => 'Rosso',
            'active1' => 'active',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'active7' => '',
            'active8' => '',
            'area_utama' => $area_utama,
            'batch' => $batch,
            'summaryRosso' => $summaryRosso
        ];

        return view('Rosso/tampilPerBatch', $data);
    }
}
