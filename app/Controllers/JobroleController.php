<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\JobRoleModel;
use App\Models\JobSectionModel;
use App\Models\MainJobRoleModel;
use App\Models\BatchModel;
use App\Models\PeriodeModel;

class JobroleController extends BaseController
{
    protected $role;
    protected $jobRoleModel;
    protected $jobSectionModel;
    protected $mainJobRoleModel;
    protected $batchModel;
    protected $periodeModel;
    public function __construct()
    {
        $this->role = session()->get('role');
        $this->jobRoleModel = new JobRoleModel();
        $this->jobSectionModel = new JobSectionModel();
        $this->mainJobRoleModel = new MainJobRoleModel();
        $this->batchModel = new BatchModel();
        $this->periodeModel = new PeriodeModel();
    }
    public function index()
    {
    }
    
    public function mainJobStore(){
        $mainJobRoleName = $this->request->getPost('main_job_role_name');
        // dd ($mainJobRoleName);   
        if ($this->mainJobRoleModel->save([
            'main_job_role_name' => $mainJobRoleName
        ])) {
            session()->setFlashdata('success', 'Data berhasil ditambahkan');
            return redirect()->to(base_url($this->role . '/dataJob'));
        } else {
            session()->setFlashdata('error', 'Data gagal ditambahkan');
            return redirect()->to(base_url($this->role . '/dataJob'));
        }
    }

    public function mainJobUpdate($id)
    {
        $data = [
            'main_job_role_name' => $this->request->getPost('main_job_role_name')
        ];

        $this->mainJobRoleModel->update($id, $data);
        session()->setFlashdata('success', 'Data Main Job Role Berhasil diubah');
        return redirect()->to(base_url($this->role . '/dataJob'));
    }
    public function mainJobDelete($id)
    {
        if ($this->mainJobRoleModel->delete($id)) {
            session()->setFlashdata('success', 'Data berhasil dihapus');
            return redirect()->to(base_url($this->role . '/dataJob'));
        } else {
            session()->setFlashdata('error', 'Data gagal dihapus');
            return redirect()->to(base_url($this->role . '/dataJob'));
        }
    }

    public function jobRoleStore()
    {
        // dd ($this->request->getPost());
        $postData = $this->request->getPost();
        $idMainJobRole = $postData['id_main_job_role'];
        $description = $postData['description'];
        $jobdescription = $postData['jobdescription'];

        $data = [];
        foreach ($description as $key => $value) {
            if(isset($jobdescription[$key]) && !empty($description[$key])) {
                $data[] = [
                    'id_main_job_role' => $idMainJobRole,
                    'description' => $description[$key],
                    'jobdescription' => $jobdescription[$key]
                ];
            }
        }

        // dd ($data);
        if (!empty($data)) {
            $this->jobRoleModel->insertBatch($data);
            session()->setFlashdata('success', 'Data berhasil ditambahkan');
            return redirect()->to(base_url($this->role . '/dataJob'));
        } 
        session()->setFlashdata('error', 'Data gagal ditambahkan');
        return redirect()->to(base_url($this->role . '/dataJob'));
    }

    public function getJobRoles($idMain){
        $rows = $this->jobRoleModel->where('id_main_job_role', $idMain)->findAll();
        return $this->response->setJSON($rows);
    }

    public function jobRoleUpdate($id)
    {
        $data = [
            'description' => $this->request->getPost('description'),
            'jobdescription' => $this->request->getPost('jobdescription')
        ];
        // dd ($data);
        $this->jobRoleModel->update($id, $data);
        session()->setFlashdata('success', 'Data Job Role Berhasil diubah');
        return redirect()->to(base_url($this->role . '/dataJob'));
    }
    public function jobRoleDelete($id)
    {
        if ($this->jobRoleModel->delete($id)) {
            session()->setFlashdata('success', 'Data berhasil dihapus');
            return redirect()->to(base_url($this->role . '/dataJob'));
        } else {
            session()->setFlashdata('error', 'Data gagal dihapus');
            return redirect()->to(base_url($this->role . '/dataJob'));
        }
    }
}
