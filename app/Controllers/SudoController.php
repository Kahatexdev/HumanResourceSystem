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

class SudoController extends BaseController
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
        $this->role = session()->get('role');
    }
    public function index()
    {
        $data = [
            'role' => $this->role,
            'title' => 'Dashboard',
            'active1' => 'active',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => '',
            'active6' => ''
        ];
        return view($this->role . '/dashboard', $data);
    }

    public function user()
    {

        $users = $this->userModel->findAll();

        $data = [
            'role' => $this->role,
            'title' => 'User',
            'active1' => '',
            'active2' => 'active',
            'active3' => '',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'users' => $users
        ];


        // dd($users);
        return view($this->role . '/user', $data);
    }

    public function bagian()
    {
        $bagian = $this->jobSectionModel->findAll();
        $data = [
            'role' => $this->role,
            'title' => 'Bagian',
            'active1' => '',
            'active2' => '',
            'active3' => 'active',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'bagian' => $bagian
        ];
        return view($this->role . '/bagian', $data);
    }

    public function karyawan()
    {
        
        $bagian = $this->jobSectionModel->findAll();
        $karyawan = $this->employeeModel->getEmployeeData(); 
        // dd($karyawan);
        $day = $this->dayModel->findAll();
        $baju = $this->employmentStatusModel->findAll();
        $area = $this->factoryModel->findAll();


        // dd($karyawan);
        $data = [
            'role' => session()->get('role'),
            'title' => 'Karyawan',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => 'active',
            'active5' => '',
            'active6' => '',
            'karyawan' => $karyawan,
            'bagian' => $bagian,
            'day' => $day,
            'baju' => $baju,
            'area' => $area
        ];

        // dd ($karyawan, $bagian);    
        return view(session()->get('role') . '/karyawan', $data);
    }

    public function batch()
    {
        $batch = $this->batchModel->findAll();
        $data = [
            'role' => $this->role,
            'title' => 'Batch',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => 'active',
            'active6' => '',
            'batch' => $batch
        ];
        return view($this->role . '/batch', $data);
    }

    public function periode()
    {
        $periode = $this->periodeModel->getPeriode();
        $batch = $this->batchModel->findAll();
        $data = [
            'role' => $this->role,
            'title' => 'Periode',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => 'active',
            'active6' => '',
            'periode' => $periode,
            'batch' => $batch
        ];
        return view($this->role . '/periode', $data);
    }

    public function absen()
    {
        $absen = $this->presenceModel->getDataPresence();
        // dd ($absen);
        $users = $this->userModel->findAll();
        $karyawan = $this->employeeModel->getEmployeeData();
        $periode = $this->periodeModel->getPeriode();

        $data = [
            'role' => session()->get('role'),
            'title' => 'Absen',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => 'active',
            'active6' => '',
            'absen' => $absen,
            'users' => $users,
            'karyawan' => $karyawan,
            'periode' => $periode
        ];
        // dd($absen);
        return view(session()->get('role') . '/absen', $data);
    }

    public function job()
    {
        $jobrole = $this->jobRoleModel->findAll();
        $mainjobrole = $this->mainJobRoleModel->findAll();
        $data = [
            'role' => $this->role,
            'title' => 'Job Role',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => '',
            'active6' => 'active',
            'mainjobrole' => $mainjobrole
        ];

        // dd ($jobrole);
        return view($this->role . '/jobrole', $data);
    }

    public function penilaian()
    { // Ambil filter dari query string
        // $filters = [
        //     'job_section_name' => $this->request->getGet('job_section_name'),
        //     'main_factory'  => $this->request->getGet('main_factory'),
        //     'factory_name'        => $this->request->getGet('factory_name'),
        // ];

       
        // dd ($karyawan);
        $namabagian = $this->jobSectionModel->findAll();
        // $penilaian = $this->penilaianModel->getPenilaian();
        $periode   = $this->periodeModel->getActivePeriode();
        $areaUtama = $this->factoryModel->select('*')->groupBy('main_factory')->findAll();
        $area = $this->factoryModel->select('*')->groupBy('factory_name')->findAll();

        $data = [
            'role'       => $this->role,
            'title'      => 'Penilaian Karyawan',
            'active9'    => 'active',
            'namabagian' => $namabagian,
            'periode'    => $periode,
            'areaUtama'  => $areaUtama,
            'area'       => $area,
            // 'penilaian'  => $penilaian,
        ];

        return view($this->role . '/penilaian', $data);
    }

    public function jarum()
    {
        $getBatch = $this->batchModel->findAll();
        $periode = $this->periodeModel->getPeriode();
        $getArea = $this->factoryModel->select('*')->groupBy('main_factory')->findAll();
        $getPeriode = $this->periodeModel->getPeriode();
        $getCurrentInput = $this->jarumModel->getCurrentInput();

        // dd($getPeriode);
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
            'getCurrentInput' => $getCurrentInput
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
