<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\JobRoleModel;
use App\Models\JobSectionModel;
use App\Models\MainJobRoleModel;
use App\Models\BatchModel;
use App\Models\PeriodeModel;
use PhpOffice\PhpSpreadsheet\IOFactory;


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

    public function importExcel()
    {
        helper('form');
        // 1) Validasi upload
        if (! $this->validate([
            'file' => 'uploaded[file]|ext_in[file,xlsx,xls]|max_size[file,10240]'
        ])) {
            return redirect()->back()->with('error', $this->validator->getError('file'));
        }

        // 2) Pindahkan file ke writable/uploads
        $upload = $this->request->getFile('file');
        // dd ($upload);
        $newName = $upload->getRandomName();
        $upload->move(WRITEPATH . 'uploads', $newName);
        $fullPath = WRITEPATH . 'uploads/' . $newName;

        // 3) Load spreadsheet
        $spreadsheet = IOFactory::load($fullPath);

        // 4) Import sheet "mainjobrole"
        $sheet1 = $spreadsheet->getSheetByName('mainjobrole');
        $rows1  = $sheet1->toArray(null, true, true, true);

        foreach ($rows1 as $idx => $row) {
            if ($idx < 2) continue; // skip header
            $data = [
                'id_main_job_role'    => $row['A'],
                'main_job_role_name'  => $row['B'],
                'created_at'          => $row['C'],
                'updated_at'          => $row['D'],
            ];
            // replace(): insert baru atau update jika primary key sudah ada
            $this->mainJobRoleModel->replace($data);
        }

        // 5) Import sheet "jobroles"
        $sheet2 = $spreadsheet->getSheetByName('jobroles');
        $rows2  = $sheet2->toArray(null, true, true, true);

        foreach ($rows2 as $idx => $row) {
            if ($idx < 2) continue; // skip header
            $data = [
                'id_main_job_role'   => $row['A'],
                'jobdescription'     => $row['B'],
                'description'        => $row['C'],
                'created_at'         => $row['D'],
                'updated_at'         => $row['E'],
            ];
            $this->jobRoleModel->insert($data);
        }

        // 6) Hapus file temp dan redirect
        unlink($fullPath);
        return redirect()->back()->with('success', 'Import jobroles berhasil.');
    }
}
