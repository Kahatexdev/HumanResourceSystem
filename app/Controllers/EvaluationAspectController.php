<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\EvaluationAspectModel;
class EvaluationAspectController extends BaseController
{
    protected $aspectModel;
    protected $role;
    public function __construct()
    {
        $this->role = session()->get('role');
        if (!$this->role) {
            return redirect()->to(base_url('login'));
        }
        $this->aspectModel = new EvaluationAspectModel();
    }
    public function index()
    {
        $aspect = $this->aspectModel->findAll();
        $data = [
            'title' => 'Daftar Aspek Penilaian',
            'aspects' => $aspect,
            'role' => $this->role,
        ];
        return view('aspect/index', $data);

    }
}
