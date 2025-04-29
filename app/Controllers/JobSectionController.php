<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\UserModel;
use App\Models\JobSectionModel;

class JobSectionController extends BaseController
{
    protected $role;
    protected $jobSectionModel;
    public function __construct()
    {
        $this->jobSectionModel = new JobSectionModel();
        $this->role = session()->get('role');
    }
    public function store()
    {
        $data = [
            'job_section_name' => $this->request->getPost('job_section_name'),
        ];

        $this->jobSectionModel->insert($data);
        session()->setFlashdata('success', 'Data Bagian Berhasil Ditambahkan');
        return redirect()->to(base_url($this->role . '/dataBagian'));
    }

    public function update($id)
    {
        $data = [
            'job_section_name' => $this->request->getPost('job_section_name'),
        ];

        $this->jobSectionModel->update($id, $data);
        session()->setFlashdata('success', 'Data Bagian Berhasil Diubah');
        return redirect()->to(base_url($this->role . '/dataBagian'));
    }

    public function delete($id)
    {
        $this->jobSectionModel->delete($id);
        return redirect()->to(base_url($this->role . '/dataBagian'))->with('success', 'Data Bagian Berhasil Dihapus');
    }
}
