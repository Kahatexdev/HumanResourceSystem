<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class BatchController extends BaseController
{
    protected $role;
    protected $batchModel;

    public function __construct()
    {
        $this->batchModel = new \App\Models\BatchModel();
        $this->role = session()->get('role');
    }
    public function index()
    {
        //
    }

    public function store()
    {
        if ($this->batchModel->save([
            'batch_name' => $this->request->getVar('nama_batch')
        ])) {
            session()->setFlashdata('success', 'Data berhasil ditambahkan');
            return redirect()->to(base_url($this->role . '/dataBatch'));
        } else {
            session()->setFlashdata('error', 'Data gagal ditambahkan');
            return redirect()->to(base_url($this->role . '/dataBatch'));
        }
    }

    public function update($id)
    {
        $data = [
            'batch_name' => $this->request->getPost('nama_batch')
        ];

        $this->batchModel->update($id, $data);
        session()->setFlashdata('success', 'Data Batch Berhasil diubah');
        return redirect()->to(base_url($this->role . '/dataBatch'));
    }

    public function delete($id)
    {
        if ($this->batchModel->delete($id)) {
            session()->setFlashdata('success', 'Data berhasil dihapus');
            return redirect()->to(base_url($this->role . '/dataBatch'));
        } else {
            session()->setFlashdata('error', 'Data gagal dihapus');
            return redirect()->to(base_url($this->role . '/dataBatch'));
        }
    }
}
