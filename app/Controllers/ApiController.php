<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use App\Models\UserModel;
use App\Models\JobSectionModel;
use App\Models\EmploymentStatusModel;
use App\Models\EmployeeModel;
use App\Models\HistoryEmployeeModel;

class ApiController extends BaseController
{
    use ResponseTrait;
    protected $role;
    protected $userModel;
    protected $jobSectionModel;
    protected $employmentStatusModel;
    protected $employeeModel;
    protected $historyEmployeeModel;
    protected $validation;
    protected $request;
    protected $response;
    protected $session;
    protected $db;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->jobSectionModel = new JobSectionModel();
        $this->employmentStatusModel = new EmploymentStatusModel();
        $this->employeeModel = new EmployeeModel();
        $this->historyEmployeeModel = new HistoryEmployeeModel();
        $this->validation = \Config\Services::validation();
        $this->request = \Config\Services::request();
        $this->response = \Config\Services::response();
        $this->session = \Config\Services::session();
        $this->db = \Config\Database::connect();
    }
    public function index()
    {
        $data = $this->employeeModel->findAll();

        return $this->respond($data, 200);
    }

    public function show($id = null)
    {
        $data = $this->employeeModel->findById($id);
        if ($data) {
            return $this->respond($data);
        } else {
            return $this->failNotFound('No Data Found with id ' . $id);
        }
    }

    public function getKaryawanByAreaUtama($areaUtama)
    {
        $data = $this->employeeModel->getKaryawanByAreaUtama($areaUtama);

        return $this->respond($data, 200);
    }

    public function getKaryawanByArea($area)
    {
        $data = $this->employeeModel->getKaryawanByAreaApi($area);

        return $this->respond($data, 200);
    }

    public function getDataForBsMc($area, $namaKar)
    {
        $data = $this->employeeModel->getKaryawanByAreaApi($area);

        $filteredArea = array_filter($data, function ($item) use ($namaKar) {
            return $item['nama_karyawan'] === $namaKar;
        });

        // Re-index array supaya tidak acak
        $filteredArea = array_values($filteredArea);

        return $this->respond($filteredArea, 200);
    }

    public function getEmployeeByName($name)
    {
        $data = $this->employeeModel->getEmployeeByName($name);

        if ($data) {
            return $this->respond($data, 200);
        } else {
            return $this->failNotFound('No Data Found with name ' . $name);
        }
    }
}
