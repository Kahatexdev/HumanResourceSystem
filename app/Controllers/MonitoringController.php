<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\UserModel;
use App\Models\JobSectionModel;
use App\Models\EmploymentStatusModel;
use App\Models\EmployeeModel;
use App\Models\DayModel;
use App\Models\BatchModel;
use App\Models\PeriodeModel;
use App\Models\PresenceModel;
use App\Models\MainJobRoleModel;
use App\Models\JobRoleModel;
use App\Models\JarumModel;
use App\Models\RossoModel;
use App\Models\BsmcModel;
use App\Models\FactoriesModel;
use App\Models\HistoryEmployeeModel;
use App\Models\EmployeeAssessmentModel;
use App\Models\PerformanceAssessmentModel;

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
    protected $historyEmployeeModel;
    protected $eaModel;
    protected $paModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->jobSectionModel = new JobSectionModel();
        $this->employmentStatusModel = new EmploymentStatusModel();
        $this->employeeModel = new EmployeeModel();
        $this->dayModel = new DayModel();
        $this->factoryModel = new FactoriesModel();
        $this->batchModel = new BatchModel();
        $this->periodeModel = new PeriodeModel();
        $this->presenceModel = new PresenceModel();
        $this->mainJobRoleModel = new MainJobRoleModel();
        $this->jobRoleModel = new JobRoleModel();
        $this->jarumModel = new JarumModel();
        $this->rossoModel = new RossoModel();
        $this->bsmcModel = new BsmcModel();
        $this->historyEmployeeModel = new HistoryEmployeeModel();
        $this->eaModel = new EmployeeAssessmentModel();
        $this->paModel = new PerformanceAssessmentModel();
        $this->role = session()->get('role');
    }

    public function index()
    {
        $TtlKaryawan = $this->employeeModel->where('status', 'Aktif')->countAll();
        $PerpindahanBulanIni = $this->historyEmployeeModel->where('MONTH(date_of_change)', date('m'))->countAllResults();
        $dataKaryawan = $this->employeeModel->getActiveEmployeeByJobSection();

        $periodeAktif = $this->periodeModel->getActivePeriode();

        // Default values jika tidak ada periode aktif
        $id_periode = null;
        $current_periode = 'Tidak Ada Periode Aktif';
        $start_date = '-';
        $end_date = '-';
        $cekPenilaian = null;

        if ($periodeAktif) {
            $id_periode = $periodeAktif['id_periode'];
            $current_periode = $periodeAktif['periode_name'];
            $start_date = $periodeAktif['start_date'];
            $end_date = $periodeAktif['end_date'];
            $cekPenilaian = $this->paModel->getMandorEvaluationStatus($id_periode);
        }
        $RatarataGrade = 0;
        $SkillGap = 0;
        // Hitung total karyawan (jika diperlukan)
        $totalKaryawan = 0;
        foreach ($dataKaryawan as $row) {
            $totalKaryawan += $row['jumlah_employees'];
        }
        // dd($cekPenilaian);

        // $RatarataGrade = $this->penilaianmodel->getRataRataGrade(); // Lanjut nanti kalo grade akhir udah ada
        $RatarataGrade = 0;

        $dataPindah = $this->historyEmployeeModel->getPindahGroupedByDate();
        // Siapkan data untuk grafik line
        $labelsKar = [];
        $valuesKar = [];
        foreach ($dataPindah as $row) {
            $labelsKar[] = $row['tgl'];
            $valuesKar[] = (int)$row['jumlah'];
        }

        return view('Monitoring/index', [
            'role' => session()->get('role'),
            'title' => 'Dashboard',
            'active1' => 'active',
            'active2' => '',
            'active3' => '',
            'TtlKaryawan' => $TtlKaryawan,
            'PerpindahanBulanIni' => $PerpindahanBulanIni,
            'RataRataGrade' => $RatarataGrade,
            // 'RataRataGrade' => $RatarataGrade['average_grade_letter'],
            'SkillGap' => $SkillGap,
            'karyawanByBagian' => $dataKaryawan,
            'labelsKar' => $labelsKar,
            'valuesKar' => $valuesKar,
            'cekPenilaian' => $cekPenilaian,
            'id_periode' => $id_periode,
            'current_periode' => $current_periode,
            'start_date' => $start_date,
            'end_date' => $end_date
        ]);
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
        $getArea = $this->factoryModel->select('*')->groupBy('factory_name')->whereIn('factory_name', ['KK1A', 'KK1B', 'KK2A', 'KK2B', 'KK2C', 'KK5', 'KK7K', 'KK7L', 'KK8D', 'KK8F', 'KK8J', 'KK9', 'KK10', 'KK11'])->findAll();
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

    public function reportpenilaian()
    {
        $tampilperarea = $this->factoryModel->getMainFactoryGroupByMainFactory();
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
        // Urutkan data menggunakan usort
        usort($tampilperarea, function ($a, $b) use ($sort) {
            $pos_a = array_search($a['main_factory'], $sort);
            $pos_b = array_search($b['main_factory'], $sort);

            // Jika tidak ditemukan, letakkan di akhir
            $pos_a = ($pos_a === false) ? PHP_INT_MAX : $pos_a;
            $pos_b = ($pos_b === false) ? PHP_INT_MAX : $pos_b;

            return $pos_a - $pos_b;
        });
        // $getArea = $this->bagianmodel->getAreaGroupByAreaUtama();
        // dd($tampilperarea);
        // dd($reportbatch);
        $data = [
            'role' => session()->get('role'),
            'title' => 'Report Penilaian',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'active7' => '',
            'active8' => '',
            'active9' => 'active',
            // 'getArea' => $getArea,
            'tampilperarea' => $tampilperarea
        ];
        // dd ($getBatch);
        return view(session()->get('role') . '/reportpenilaian', $data);
    }

    public function reportbatch()
    {
        $tampilperarea = $this->factoryModel->getMainFactoryGroupByMainFactory();
        array_unshift($tampilperarea, ['main_factory' => 'all']); //Menambahkan data All Area
        $sort = [
            'KK1',
            'KK2',
            'KK5',
            'KK7',
            'KK8',
            'KK9',
            'KK10',
            'KK11',
            'all'
        ];

        // Urutkan data menggunakan usort
        usort($tampilperarea, function ($a, $b) use ($sort) {
            $pos_a = array_search($a['main_factory'], $sort);
            $pos_b = array_search($b['main_factory'], $sort);

            // Jika tidak ditemukan, letakkan di akhir
            $pos_a = ($pos_a === false) ? PHP_INT_MAX : $pos_a;
            $pos_b = ($pos_b === false) ? PHP_INT_MAX : $pos_b;

            return $pos_a - $pos_b;
        });

        // $getBulan = $this->penilaianmodel->getBatchGroupByBulanPenilaian();
        // $getBagian = $this->bagianmodel->getBagian();
        // $getBatch = $this->penilaianmodel->getPenilaianGroupByBatch();

        $data = [
            'role' => session()->get('role'),
            'title' => 'Report Batch',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'active7' => '',
            'active8' => '',
            'active9' => 'active',
            // 'getBulan' => $getBulan,
            // 'getBagian' => $getBagian,
            // 'getArea' => $tampilperarea,
            // 'getBatch' => $getBatch,
            'tampilperarea' => $tampilperarea,
        ];
        // dd ($getBatch);
        return view(session()->get('role') . '/reportbatch', $data);
    }
}
