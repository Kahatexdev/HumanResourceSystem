<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\UserModel;
use App\Models\JobSectionModel;
use App\Models\EmploymentStatusModel;
use App\Models\EmployeeModel;
use App\Models\DayModel;
use App\Models\FactoryModel;
use App\Models\BatchModel;
use App\Models\PeriodeModel;
use App\Models\PresenceModel;
use App\Models\MainJobRoleModel;
use App\Models\JobRoleModel;
use App\Models\JarumModel;
use App\Models\RossoModel;
use App\Models\BsmcModel;

class MonitoringController extends BaseController
{
    protected $role;
    protected $userModel;
    protected $jobSectionModel;
    protected $employmentStatusModel;
    protected $employeeModel;
    protected $dayModel;
    protected $factoryModel;
    protected $batchModel;
    protected $periodeModel;
    protected $presenceModel;
    protected $mainJobRoleModel;
    protected $jobRoleModel;
    protected $jarumModel;
    protected $rossoModel;
    protected $bsmcModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->jobSectionModel = new JobSectionModel();
        $this->employmentStatusModel = new EmploymentStatusModel();
        $this->employeeModel = new EmployeeModel();
        $this->dayModel = new DayModel();
        $this->factoryModel = new FactoryModel();
        $this->batchModel = new BatchModel();
        $this->periodeModel = new PeriodeModel();
        $this->presenceModel = new PresenceModel();
        $this->mainJobRoleModel = new MainJobRoleModel();
        $this->jobRoleModel = new JobRoleModel();
        $this->jarumModel = new JarumModel();
        $this->rossoModel = new RossoModel();
        $this->bsmcModel = new BsmcModel();
        $this->role = session()->get('role');
    }

    public function index()
    {
        //
    }

    public function bsmc()
    {
        $getBatch = $this->batchModel->findAll();
        $periode = $this->periodeModel->getPeriode();
        $getArea = $this->factoryModel->select('*')->groupBy('factory_name')->findAll();
        $getPeriode = $this->periodeModel->getPeriode();
        $getCurrentInput = $this->bsmcModel->getCurrentInput();

        // dd($getArea);
        $sort = [
            'KK1A',
            'KK1B',
            'KK2A',
            'KK2B',
            'KK2C',
            'KK5',
            'KK7K',
            'KK7L',
            'KK8D',
            'KK8F',
            'KK8J',
            'KK9',
            'KK10',
            'KK11',
        ];

        $getArea = array_filter($getArea, function ($area) use ($sort) {
            return in_array($area['factory_name'], $sort);
        });

        // Urutkan data menggunakan usort
        usort($getArea, function ($a, $b) use ($sort) {
            $pos_a = array_search($a['factory_name'], $sort);
            $pos_b = array_search($b['factory_name'], $sort);

            // Jika tidak ditemukan, letakkan di akhir
            $pos_a = ($pos_a === false) ? PHP_INT_MAX : $pos_a;
            $pos_b = ($pos_b === false) ? PHP_INT_MAX : $pos_b;

            return $pos_a - $pos_b;
        });

        $data = [
            'role' => session()->get('role'),
            'title' => 'Bsmc',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'active7' => '',
            'active8' => '',
            'active9' => 'active',
            'getBatch' => $getBatch,
            'periode' => $periode,
            'getArea' => $getArea,
            'getPeriode' => $getPeriode,
            'getCurrentInput' => $getCurrentInput ?? []
        ];
        // dd ($getBatch);
        return view(session()->get('role') . '/bsmc', $data);
    }
    public function jarum()
    {
        $getBatch = $this->batchModel->findAll();
        $periode = $this->periodeModel->getPeriode();
        $getArea = $this->factoryModel->select('*')->groupBy('main_factory')->findAll();
        $getPeriode = $this->periodeModel->getPeriode();
        $getCurrentInput = $this->jarumModel->getCurrentInput();

        // dd($getArea);
        $sort = [
            'KK1A',
            'KK1B',
            'KK2A',
            'KK2B',
            'KK2C',
            'KK5',
            'KK7K',
            'KK7L',
            'KK8D',
            'KK8F',
            'KK8J',
            'KK9',
            'KK10',
            'KK11',
        ];

        // Urutkan data menggunakan usort
        usort($getArea, function ($a, $b) use ($sort) {
            $pos_a = array_search($a['factory_name'], $sort);
            $pos_b = array_search($b['factory_name'], $sort);

            // Jika tidak ditemukan, letakkan di akhir
            $pos_a = ($pos_a === false) ? PHP_INT_MAX : $pos_a;
            $pos_b = ($pos_b === false) ? PHP_INT_MAX : $pos_b;

            return $pos_a - $pos_b;
        });

        $data = [
            'role' => session()->get('role'),
            'title' => 'Jarum',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'active7' => '',
            'active8' => '',
            'active9' => 'active',
            'getBatch' => $getBatch,
            'periode' => $periode,
            'getArea' => $getArea,
            'getPeriode' => $getPeriode,
            'getCurrentInput' => $getCurrentInput ?? []
        ];
        // dd ($getBatch);
        return view(session()->get('role') . '/jarum', $data);
    }

    public function getMontirByArea()
    {
        $montir = [];

        if ($this->request->isAJAX()) {
            $area = $this->request->getPost('area');
            // $id_periode = $this->request->getPost('id_periode'); 
            // $id_periode = 1;

            // Ambil id_bagian berdasarkan area
            $montir = $this->employeeModel->getMontirByArea($area);
            // log_message('info', 'Bagian: ' . json_encode($bagian));
        }

        return $this->response->setJSON($montir);
    }

    public function rosso()
    {
        $tampilperarea = $this->factoryModel->select('*')->groupBy('main_factory')->findAll();
        $getBatch = $this->batchModel->findAll();
        $periode = $this->periodeModel->getPeriode();
        $getCurrentInput = $this->rossoModel->getCurrentInput();

        $sort = [
            'KK1',
            'KK2',
            'KK5',
            'KK7',
            'KK8',
            'KK9',
            'KK10',
            'KK11'
        ];
        // dd($tampilperarea);
        // Urutkan data menggunakan usort
        usort($tampilperarea, function ($a, $b) use ($sort) {
            $pos_a = array_search($a['main_factory'], $sort);
            $pos_b = array_search($b['main_factory'], $sort);

            // Jika tidak ditemukan, letakkan di akhir
            $pos_a = ($pos_a === false) ? PHP_INT_MAX : $pos_a;
            $pos_b = ($pos_b === false) ? PHP_INT_MAX : $pos_b;

            return $pos_a - $pos_b;
        });

        $data = [
            'role' => session()->get('role'),
            'title' => 'Rosso',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'active7' => '',
            'active8' => '',
            'active9' => 'active',
            // 'reportbatch' => $reportbatch,
            // 'getArea' => $getArea,
            'getBatch' => $getBatch,
            'tampilperarea' => $tampilperarea,
            'periode' => $periode,
            'getCurrentInput' => $getCurrentInput
        ];
        // dd ($getBatch);
        return view(session()->get('role') . '/rosso', $data);
    }
}
