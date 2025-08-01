<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
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
use App\Models\HistoryEmployeeModel;

class MandorController extends BaseController
{
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
    protected $historyEmployeeModel;
    protected $role;

    public function __construct()
    {
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
        $this->historyEmployeeModel = new HistoryEmployeeModel();
        $this->role = session()->get('role');
    }

    public function index()
    {
        //
    }

    public function dashboard()
    {
        $periode = $this->periodeModel->getPeriode();
        $data = [
            'role' => $this->role,
            'area' => session()->get('area'),
            'title' => 'Dashboard',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'active7' => '',
            'active8' => '',
            'periode' => $periode
        ];
        return view(session()->get('role') . '/dashboard', $data);
    }

    public function listArea()
    {
        $apiUrl = 'http://172.23.44.14/CapacityApps/public/api/getPlanMesin';
        $response = file_get_contents($apiUrl);
        $plan = json_decode($response, true);  // Decode JSON response dari API
        $tampilperarea = $this->factoriesModel->groupBy('main_factory')->findAll();
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

        // Fungsi untuk mengurutkan berdasarkan array urutan yang ditentukan
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
            'title' => 'Karyawan',
            'active1' => '',
            'active2' => 'active',
            'active3' => '',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'active7' => '',
            'tampildata' => $tampilperarea,
            'listplan' => $plan
        ];
        // dd($data);
        return view(session()->get('role') . '/karyawan', $data);
    }

    public function detailKaryawanPerArea($area)
    {
        if ($area === 'EMPTY') {
            $karyawan = $this->employeeModel->getKaryawanTanpaArea();
        } else {
            $karyawan = $this->employeeModel->getKaryawanByArea($area);
            // dd ($karyawan);
        }
        // dd ($area);
        // dd($karyawan);
        $bagian = $this->jobSectionModel->findAll();
        $day = $this->days->findAll();
        $baju = $this->employmentStatusModel->findAll();
        $factory = $this->factoriesModel->findAll();
        // $area = $this->factoryModel->findAll();
        // dd($karyawan);
        $data = [
            'role' => session()->get('role'),
            'title' => 'Karyawan',
            'active1' => 'active',
            'active2' => '',
            'active3' => '',
            'karyawan' => $karyawan,
            'area' => $area,
            'bagian' => $bagian,
            'day' => $day,
            'baju' => $baju,
            'factory' => $factory
        ];
        return view(session()->get('role') . '/detailKaryawan', $data);
    }

    public function penilaian()
    { // Ambil filter dari query string
        // $filters = [
        //     'job_section_name' => $this->request->getGet('job_section_name'),
        //     'main_factory'  => $this->request->getGet('main_factory'),
        //     'factory_name'        => $this->request->getGet('factory_name'),
        // ];


        // dd ($karyawan);
        $namabagian = $this->jobSectionModel->whereIn('job_section_name', ['OPERATOR', 'OPERATOR (8D)', 'OPERATOR (8J)', 'OPERATOR (KK9)', 'OPERATOR MEKANIK DOUBLE', 'MONTIR', 'MONTIR (A1)', 'MONTIR (8J)', 'MONTIR (DAKONG)', 'MONTIR (LONATI SINGLE)', 'MONTIR (LONATI DOUBLE)', 'ROSSO', 'SEWING'])->findAll();
        // $penilaian = $this->penilaianModel->getPenilaian();
        $periode   = $this->periodeModel->getActivePeriodeMandor();
        $areaUtama = $this->factoriesModel->select('*')->groupBy('main_factory')->findAll();
        $area = $this->factoriesModel->select('*')->groupBy('factory_name')->whereIn('factory_name', ['KK1A', 'KK1B', 'KK2A', 'KK2B', 'KK2C', 'KK5G', 'KK7K', 'KK7L', 'KK8D', 'KK8F', 'KK8J', 'KK9D', 'KK10', 'KK11M', 'ROSSOKK1', 'ROSSOKK2', 'ROSSOKK5', 'ROSSOKK7', 'ROSSOKK8', 'ROSSOKK11', 'SEWING'])->findAll();

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

    public function raportPenilaian($area)
    {
        $employees = $this->employeeModel->getKaryawanByFactoryName($area); // misal berdasarkan factory_name

        $result = [];

        foreach ($employees as $employee) {
            $idKaryawan = $employee['id_employee'];

            // Ambil nilai area lama jika ada riwayat
            $combinedNilai = [
                'nilai_jan' => null,
                'nilai_feb' => null,
                'nilai_mar' => null,
                'nilai_apr' => null,
                'nilai_mei' => null,
                'nilai_jun' => null,
                'nilai_jul' => null,
                'nilai_agu' => null,
                'nilai_sep' => null,
                'nilai_okt' => null,
                'nilai_nov' => null,
                'nilai_des' => null,
            ];

            // $factoryName = $employee['factory_name'];
            // $status = 'Sekarang';

            $histories = $this->historyEmployeeModel
                ->where('id_employee', $idKaryawan)
                ->findAll();

            if ($histories) {
                foreach ($histories as $history) {
                    $nilaiLama = $this->performanceAssessmentModel->getPenilaianByEmployeeAndFactory(
                        $idKaryawan,
                        $history['id_factory_old']
                    );

                    foreach ($combinedNilai as $key => $val) {
                        if (!empty($nilaiLama[$key])) {
                            $combinedNilai[$key] = $nilaiLama[$key];
                        }
                    }

                    // Ambil nama area lama sebagai referensi area
                    // $factoryOld = $this->factoriesModel->find($history['id_factory_old']);
                    // if ($factoryOld) {
                    //     $factoryName = $factoryOld['factory_name'];
                    // }

                    // $status = 'Pernah Pindah'; // bisa ubah sesuai kebutuhan
                }
            }

            // Ambil nilai area sekarang
            $nilaiBaru = $this->performanceAssessmentModel->getPenilaianByEmployeeAndFactory(
                $idKaryawan,
                $employee['id_factory']
            );

            foreach ($combinedNilai as $key => $val) {
                // Jika nilai sekarang tidak null, override
                if (!empty($nilaiBaru[$key])) {
                    $combinedNilai[$key] = $nilaiBaru[$key];
                }
            }

            $result[] = [
                'employee_code' => $employee['employee_code'],
                'employee_name' => $employee['employee_name'],
                'shift'         => $employee['shift'],
                'nilai'         => $combinedNilai
            ];
        }

        $data = [
            'role' => session()->get('role'),
            'title' => 'Raport Penilaian',
            'active1' => 'active',
            'area' => session()->get('area'),
            'result' => $result
        ];

        return view(session()->get('role') . '/raportpenilaian', $data);
    }

    public function instruksiKerja()
    {
        $data = [
            'role' => session()->get('role'),
            'title' => 'Instruksi Kerja',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'active7' => '',
            'active8' => '',
        ];
        return view(session()->get('role') . '/instruksiKerja', $data);
    }

    public function getEmployeeEvaluationStatus($id_periode, $area)
    {
        $penilaian = $this->performanceAssessmentModel->getEmployeeEvaluationStatus($id_periode, $area);

        return $this->response->setJSON($penilaian);
    }
}
