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

class EmployeeAssessmentController extends BaseController
{
    protected $role;
    protected $employeeModel;
    protected $jobSectionModel;
    protected $jobrolemodel;
    protected $mainJobRoleModel;
    protected $batchModel;
    protected $periodeModel;
    protected $presenceModel;
    protected $employeeAssessmentModel;
    protected $performanceAssessmentModel;
    protected $factoryModel;
    public function __construct()
    {
        $this->employeeModel = new EmployeeModel();
        $this->jobSectionModel = new JobSectionModel();
        $this->jobrolemodel = new JobRoleModel();
        $this->mainJobRoleModel = new MainJobRoleModel();
        $this->batchModel = new BatchModel();
        $this->periodeModel = new PeriodeModel();
        $this->presenceModel = new PresenceModel();
        $this->employeeAssessmentModel = new EmployeeAssessmentModel();
        $this->performanceAssessmentModel = new PerformanceAssessmentModel();
        $this->factoryModel = new FactoriesModel();
        $this->role = session()->get('role');
    }
    public function index()
    {
        //
    }

    public function getKaryawan()
    {
        $filters = [
            'job_section_name' => $this->request->getGet('nama_bagian'),
            'main_factory'  => $this->request->getGet('area_utama'),
            'factory_name'        => $this->request->getGet('area'),
        ];

        // Panggil model dengan filter
        $karyawan  = $this->employeeModel->getEmployeeDataS($filters);

        return $this->response->setJSON([
            'data' => $karyawan,
        ]);
    }

    public function create()
    {
        // 1) Validasi periode
        $id_periode = $this->request->getPost('id_periode');
        if (!$id_periode) {
            return redirect()->back()->with('error', 'Periode not found.');
        }

        // 2) Ambil input
        $nama_bagian = $this->request->getPost('nama_bagian');
        $area_utama  = $this->request->getPost('area_utama');
        $area        = $this->request->getPost('area');
        $role        = session()->get('role');
        $areaSession = session()->get('area');

        // 3) Cek hak akses area (kecuali Sudo)
        if ($role !== 'Sudo' && $area !== $areaSession) {
            return redirect()->back()->with('error', 'Pilih Sesuai Area Anda Bekerja!');
        }

        // 4) Cari id_bagian
        $bagian = $this->jobSectionModel
            ->select('id_job_section')
            ->where('job_section_name', $nama_bagian)
            ->first();
        if (! $bagian) {
            return redirect()->back()->with('error', 'Bagian not found.');
        }
        $id_bagian = $bagian['id_job_section'];

        // 5) Ambil semua jobrole untuk bagian itu
        $jobroles = $this->jobrolemodel->getJobroleData($nama_bagian);
        if (empty($jobroles)) {
            return redirect()->back()->with('error', 'Job role not found.');
        }

        // 6) Grup-kan jobdescription berdasarkan description
        $jobdescWithKet = [];
        foreach ($jobroles as $jr) {
            $ket  = trim($jr['description']);      // misal "OPERATOR" atau "6S"
            $desc = trim($jr['jobdescription']);   // teks detail
            $jobdescWithKet[$ket][] = $desc;
        }
        // dd ($jobdescWithKet);
        // 7) Ambil karyawan yang dipilih (multiple select)
        $selected_karyawan_ids = $this->request->getPost('karyawan');
        // dd ($selected_karyawan_ids);
        if (empty($selected_karyawan_ids)) {
            return redirect()->back()->with('error', 'Pilih minimal satu karyawan.');
        }
        $karyawan = $this->employeeModel
            ->whereIn('id_employee', $selected_karyawan_ids)
            ->findAll();
        if (empty($karyawan)) {
            return redirect()->back()->with('error', 'Tidak ada data karyawan yang ditemukan.');
        }

        $temp = [
            'id_periode' => $id_periode,
            'id_main_job_role' => $jobroles[0]['id_main_job_role'],
            'id_employee' => $karyawan,
            'id_user' => session()->get('id_user'),
            'id_job_section' => $id_bagian
        ];

        // 8) Siapkan data untuk view
        $data = [
            'role'            => $role,
            'title'           => 'Penilaian Mandor',
            'active8'         => 'active',
            'jobroles'        => $jobroles,         // raw array kalau perlu di view
            'jobdescWithKet'  => $jobdescWithKet,   // grouped untuk looping di view
            'karyawan'        => $karyawan,
            'karyawanCount'   => count($karyawan),
            'id_periode'      => $id_periode,
            'id_bagian'       => $id_bagian,
            'temp'           => $temp
        ];

        return view('penilaian/create', $data);
    }

    public function store()
    {
        // 1) Ambil semua POST
        $post         = $this->request->getPost();
        $nilaiPerEmp  = $post['nilai']             ?? [];     // ['5'=>['asd asd'=>'5',...], '6'=>[...], …]
        $idPeriode    = $post['id_periode']        ?? null;
        $idMainRole   = $post['id_main_job_role']  ?? null;
        $idUser       = $post['id_user']           ?? session()->get('id_user');
        // dd($post, $nilaiPerEmp, $idPeriode, $idMainRole, $idUser);
        if (empty($nilaiPerEmp) || ! $idPeriode || ! $idMainRole) {
            return redirect()->back()->with('error', 'Data tidak lengkap.');
        }

        // 2) Ambil data karyawan sekaligus id_factory
        $empIds = array_keys($nilaiPerEmp);
        // dd($empIds);
        $karyawans = $this->employeeModel
            ->select('id_employee,id_job_section,id_factory')
            ->whereIn('id_employee', $empIds)
            ->findAll();
        // dd($karyawans);
        if (empty($karyawans)) {
            return redirect()->back()->with('error', 'Karyawan tidak ditemukan.');
        }
        // buat map: id_employee => id_factory
        $factoryMap = [];
        foreach ($karyawans as $k) {
            $factoryMap[$k['id_employee']] = $k['id_factory'];
        }
        // dd($factoryMap);
        // 3) Ambil semua jobrole untuk bagian terkait, agar bisa mapping jobdescription → id_job_role
        $mainJobName = $this->mainJobRoleModel
            ->select('main_job_role_name')
            ->where('id_main_job_role', $idMainRole)
            ->first();
        $jobroles = $this->jobrolemodel->getJobroleData($mainJobName);
        // dd ($jobroles);
        $descToJobrole = [];
        foreach ($jobroles as $jr) {
            // asumsikan kolom jr['jobdescription'] unik per jr['id_job_role']
            $descToJobrole[trim($jr['jobdescription'])] = $jr['id_job_role'];
        }

        // 4) Siapkan batch array untuk kedua tabel
        $toPerformance = [];
        $toAssessments = [];

        foreach ($nilaiPerEmp as $idEmp => $descScores) {
            // hitung rata-rata score untuk performance_assessments
            $scores = array_map('intval', array_values($descScores));
            $avgScore = count($scores) ? array_sum($scores) / count($scores) : 0;

            // a) record untuk performance_assessments
            $toPerformance[] = [
                'id_employee'      => $idEmp,
                'id_periode'       => $idPeriode,
                'id_main_job_role' => $idMainRole,
                'nilai'            => $avgScore,
                'id_factory'       => $factoryMap[$idEmp] ?? null,
                'id_user'          => $idUser,
            ];

            // b) detail per jobdescription ke assessments
            foreach ($descScores as $desc => $score) {
                $desc = trim($desc);
                if (! isset($descToJobrole[$desc])) {
                    // lewati kalau tidak ada mapping
                    continue;
                }
                $toAssessments[] = [
                    'id_employee' => $idEmp,
                    'id_job_role' => $descToJobrole[$desc],
                    'id_periode'  => $idPeriode,
                    'score'       => intval($score),
                    'id_user'     => $idUser,
                ];
            }
        }

        // 5) Cek duplikasi di performance_assessments
        foreach ($toPerformance as $perf) {
            $exists = $this->performanceAssessmentModel
                ->where('id_main_job_role', $perf['id_main_job_role'])
                ->where('id_periode',       $perf['id_periode'])
                ->where('id_employee',      $perf['id_employee'])
                ->where('id_factory',       $perf['id_factory'])
                ->first();
            $area = $this->factoryModel
                ->select('factory_name')
                ->where('id_factory', $perf['id_factory'])
                ->first();
            $employeName = $this->employeeModel
                ->select('employee_name')
                ->where('id_employee', $perf['id_employee'])
                ->first();
            if ($exists) {
                // kalau ketemu, hentikan dan kasih alert
                return redirect()->to(base_url($this->role . '/dataPenilaian'))
                    ->with(
                        'error',
                        "Penilaian untuk karyawan {$employeName['employee_name']} " .
                            "pada periode sekarang " .
                            "di Area {$area['factory_name']} sudah pernah disimpan."
                    );
            }
        }

        // 6) Kalau belum ada duplikat, simpan batch
        $this->performanceAssessmentModel->insertBatch($toPerformance);
        $this->employeeAssessmentModel->insertBatch($toAssessments);

        return redirect()->to(base_url($this->role . '/dataPenilaian'))->with('success', 'Data penilaian berhasil disimpan.');
    }
}
