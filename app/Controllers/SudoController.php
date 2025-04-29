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

class SudoController extends BaseController
{
    protected $role;
    protected $userModel;
    protected $jobSectionModel;
    protected $employmentStatusModel;
    protected $employeeModel;
    protected $dayModel;
    protected $factoryModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->jobSectionModel = new JobSectionModel();
        $this->employmentStatusModel = new EmploymentStatusModel();
        $this->employeeModel = new EmployeeModel();
        $this->dayModel = new DayModel();
        $this->factoryModel = new FactoryModel();
        $this->role = session()->get('role');
    }
    public function index()
    {
        return view('sudo/index', ['title' => 'Sudo Dashboard']);
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
}
