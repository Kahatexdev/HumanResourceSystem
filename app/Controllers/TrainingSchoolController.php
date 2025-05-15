<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\UserModel;
use App\Models\EmployeeModel;
use App\Models\JobSectionModel;
use App\Models\EmploymentStatusModel;
use App\Models\FactoriesModel;
use App\Models\HistoryEmployeeModel;
use App\Models\DayModel;

class TrainingSchoolController extends BaseController
{
    protected $role;
    protected $userModel;
    protected $jobSectionModel;
    protected $employmentStatusModel;
    protected $employeeModel;
    protected $historyPindahKaryawanModel;
    protected $factoriesModel;
    protected $days;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->jobSectionModel = new JobSectionModel();
        $this->employmentStatusModel = new EmploymentStatusModel();
        $this->employeeModel = new EmployeeModel();
        $this->historyPindahKaryawanModel = new HistoryEmployeeModel();
        $this->factoriesModel = new FactoriesModel();
        $this->days = new DayModel();
        $this->role = session()->get('role');
    }
    public function index()
    {
        $TtlKaryawan = $this->employeeModel->where('status', 'active')->countAll();
        $PerpindahanBulanIni = $this->historyPindahKaryawanModel->where('MONTH(date_of_change)', date('m'))->countAllResults();
        $dataKaryawan = $this->employeeModel->getActiveKaryawanByBagiaAndArea();
        // dd($dataKaryawan);
        // Group data berdasarkan area_utama
        $groupedData = [];
        foreach ($dataKaryawan as $row) {
            $groupedData[$row['main_factory']][] = $row;
        }

        // Sort berdasarkan angka setelah 'KK'
        uksort($groupedData, function ($a, $b) {
            return (int) filter_var($a, FILTER_SANITIZE_NUMBER_INT) <=> (int) filter_var($b, FILTER_SANITIZE_NUMBER_INT);
        });

        $totalKaryawan = 0;
        foreach ($dataKaryawan as $row) {
            $totalKaryawan += $row['jumlah_karyawan'];
        }

        $dataPindah = $this->historyPindahKaryawanModel->getPindahGroupedByDate();
        // dd($dataPindah);
        $labelsKar = [];
        $valuesKar = [];
        foreach ($dataPindah as $row) {
            $labelsKar[] = $row['tgl'];
            $valuesKar[] = (int)$row['jumlah'];
        }

        return view($this->role . '/index', [
            'role' => $this->role,
            'title' => 'Dashboard',
            'active1' => 'active',
            'active2' => '',
            'active3' => '',
            'TtlKaryawan' => $TtlKaryawan,
            'PerpindahanBulanIni' => $PerpindahanBulanIni,
            'groupedData' => $groupedData,
            'labelsKar' => $labelsKar,
            'valuesKar' => $valuesKar
        ]);
    }

    public function listArea()
    {
        $apiUrl = 'http://172.23.44.14/CapacityApps/public/api/getPlanMesin';
        $response = file_get_contents($apiUrl);
        $plan = json_decode($response, true);  // Decode JSON response dari API
        $tampilperarea = $this->factoriesModel
            ->where('factory_name !=', '')
            ->where('factory_name !=', '-')
            ->groupBy('main_factory')
            ->findAll();

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

        $all = 'ALL';
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
            'listplan' => $plan,
            'all' => $all
        ];
        // dd($data);
        return view(session()->get('role') . '/karyawan', $data);
    }
    public function detailKaryawanPerArea($area)
    {
        if ($area === 'ALL') {
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

    public function historyPindahKaryawan()
    {
        $historyPindahKaryawan = $this->historyPindahKaryawanModel->getHistoryPindahKaryawan();
        $data = [
            'role' => session()->get('role'),
            'title' => 'History Pindah Karyawan',
            'active1' => '',
            'active2' => '',
            'active3' => 'active',
            'historyPindahKaryawan' => $historyPindahKaryawan
        ];
        return view(session()->get('role') . '/historyPindahKaryawan', $data);
    }

    public function chat()
    {
        $userId = session()->get('id_user'); // ID pengguna yang login
        $contacts = $this->usermodel->findAll(); // Ambil semua kontak dari database (selain pengguna yang login)

        $contactsWithLastMessage = [];

        foreach ($contacts as $contact) {
            if ($contact['id_user'] != $userId) {
                // Ambil pesan terakhir antara pengguna yang login dan kontak ini
                $lastMessage = $this->messageModel
                    ->where("(sender_id = $userId AND receiver_id = {$contact['id_user']}) OR (sender_id = {$contact['id_user']} AND receiver_id = $userId)")
                    ->orderBy('created_at', 'DESC')
                    ->limit(1)
                    ->first();

                $contactsWithLastMessage[] = [
                    'contact' => $contact,
                    'last_message' => $lastMessage
                ];
            }
        }

        $data = [
            'role' => session()->get('role'),
            'title' => 'Chat',
            'active4' => 'active',
            'contacts' => $contactsWithLastMessage // Kirim data kontak beserta pesan terakhir
        ];

        return view('chat/index', $data);
    }
}
