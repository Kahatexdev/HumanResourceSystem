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
use App\Models\FinalAssssmentModel;

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
    protected $FinalAssessmentModel;
    protected $db;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->jobSectionModel = new JobSectionModel();
        $this->employmentStatusModel = new EmploymentStatusModel();
        $this->employeeModel = new EmployeeModel();
        $this->historyEmployeeModel = new HistoryEmployeeModel();
        $this->FinalAssessmentModel = new FinalAssssmentModel();
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

    public function chartData()
    {
        $periodeId = $this->request->getGet('periodeId');
        $metric   = $this->request->getGet('metric') ?? 'all';
        $area = $this->session->get('area');

        $idMainJobRole = [
            'OPERATOR' => 2,
            'OPERATOR (8J)' => 16,
            'OPERATOR (KK9)' => 18,
            'MONTIR (A1)' => 46,
            'MONTIR' => 47,
            'MONTIR (8J)' => 60,
            'MONTIR (DAKONG)' => 65,
            'MONTIR (LONATI DOUBLE)' => 66,
            'ROSSO' => 67,
            'SEWING' => 133,
            'OPERATOR MEKANIK DOUBLE' => 157,
            'OPERATOR (8D)' => 158
        ];

        $valid = ['Absensi', 'Penilaian', '6S', 'Productivity'];

        if (!$periodeId) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'periodeId is required']);
        }

        $all = $this->FinalAssessmentModel->getScoreCountsByPeriodeUnion($periodeId, $area, $idMainJobRole);
        // dd($all);
        // helper untuk build array labels/data dari associative array
        $buildMetric = function ($counts) {
            $labels = array_keys($counts);
            $isNumeric = true;
            foreach ($labels as $l) {
                if ($l === 'NULL' || !is_numeric($l)) {
                    $isNumeric = false;
                    break;
                }
            }
            if ($isNumeric) {
                usort($labels, function ($a, $b) {
                    return floatval($a) <=> floatval($b);
                });
            } else {
                sort($labels, SORT_STRING);
            }
            $data = [];
            foreach ($labels as $lab) {
                $val = $counts[$lab] ?? 0;
                $data[] = is_numeric($val) ? (float)$val : 0.0;
            }
            return [
                'labels' => $labels,
                'data'   => $data,
                'total'  => array_sum($data),
            ];
        };

        // deteksi struktur
        $topKeys = is_array($all) ? array_keys($all) : [];
        $isMetricTopLevel = !empty(array_intersect($topKeys, $valid));
        $isRoleTopLevel = false;
        if (!$isMetricTopLevel && !empty($all) && is_array($all)) {
            foreach ($all as $k => $v) {
                if (is_array($v)) {
                    foreach ($valid as $m) {
                        if (array_key_exists($m, $v)) {
                            $isRoleTopLevel = true;
                            break 2;
                        }
                    }
                }
            }
        }

        // jika user minta metric tertentu (mis. ?metric=presence)
        if ($metric !== 'all') {
            if (!in_array($metric, $valid)) {
                return $this->response->setStatusCode(400)->setJSON(['error' => 'invalid metric']);
            }

            if ($isMetricTopLevel) {
                $counts = $all[$metric] ?? [];
                return $this->response->setJSON($buildMetric($counts));
            } elseif ($isRoleTopLevel) {
                $counts = [];
                foreach ($all as $role => $metrics) {
                    if (is_array($metrics)) {
                        $counts[$role] = isset($metrics[$metric]) ? (float)$metrics[$metric] : 0.0;
                    }
                }
                return $this->response->setJSON($buildMetric($counts));
            } else {
                // fallback: mungkin metric scalar
                if (isset($all[$metric]) && is_numeric($all[$metric])) {
                    return $this->response->setJSON($buildMetric([$metric => (float)$all[$metric]]));
                }
                return $this->response->setJSON(['labels' => [], 'data' => [], 'total' => 0]);
            }
        }

        // ---------- PERUBAHAN UTAMA: jika metric == 'all' dan struktur role-top-level,
        // kembalikan keyed-by-role: role => { labels: [metrics], data: [values], total }
        $out = [];
        if ($isRoleTopLevel) {
            foreach ($all as $role => $metrics) {
                if (!is_array($metrics)) continue;
                $labels = [];
                $data = [];
                foreach ($valid as $m) {
                    // masukkan hanya aspek yang ada (agar labels = ['presence','...'])
                    if (array_key_exists($m, $metrics)) {
                        $labels[] = $m;
                        $data[] = is_numeric($metrics[$m]) ? (float)$metrics[$m] : 0.0;
                    }
                }
                $out[$role] = [
                    'labels' => $labels,
                    'data'   => $data,
                    'total'  => array_sum($data),
                ];
            }
        } elseif ($isMetricTopLevel) {
            // struktur lama: metric => [label => count,...] (tetap dukung)
            foreach ($valid as $m) {
                $out[$m] = $buildMetric($all[$m] ?? []);
            }
        } else {
            // fallback: kemungkinan metric scalar
            foreach ($valid as $m) {
                if (isset($all[$m]) && is_numeric($all[$m])) {
                    $out[$m] = $buildMetric([$m => (float)$all[$m]]);
                } else {
                    $out[$m] = ['labels' => [], 'data' => [], 'total' => 0];
                }
            }
        }

        return $this->response->setJSON(['metrics' => $out]);
    }
}
