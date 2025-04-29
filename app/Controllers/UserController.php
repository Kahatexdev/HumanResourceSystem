<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\UserModel;

class UserController extends BaseController
{
    protected $role;
    protected $userModel;
    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->role = session()->get('role');
    }
    public function index()
    {
        //
    }

    public function store()
    {
        $data = [
            'username' => $this->request->getPost('username'),
            'password' => $this->request->getPost('password'),
            'role' => $this->request->getPost('role'),
            'area' => $this->request->getPost('area')
        ];

        // dd($data);
        if ($this->userModel->insert($data)) {
            // set session flashdata
            session()->setFlashdata('success', 'Data berhasil ditambahkan');
        } else {
            session()->setFlashdata('error', 'Data gagal ditambahkan');
        }

        return redirect()->to($this->role . '/dataUser');
    }

    public function edit($id)
    {
        $user = $this->userModel->find($id);
        $data = [
            'role' => session()->get('role'),
            'title' => 'User',
            'active1' => '',
            'active2' => 'active',
            'active3' => '',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'user' => $user
        ];

        return view('pengguna/edit', $data);
    }

    public function update($id)
    {
        $data = [
            'username' => $this->request->getPost('username'),
            'password' => $this->request->getPost('password'),
            'role' => $this->request->getPost('role')
        ];

        if ($this->userModel->update($id, $data)) {
            // set session flashdata
            session()->setFlashdata('success', 'Data berhasil diubah');
        } else {
            session()->setFlashdata('error', 'Data gagal diubah');
        }

        return redirect()->to($this->role . '/dataUser');
    }

    public function delete($id)
    {
        if ($this->userModel->delete($id)) {
            // set session flashdata
            session()->setFlashdata('success', 'Data berhasil dihapus');
        } else {
            session()->setFlashdata('error', 'Data gagal dihapus');
        }

        return redirect()->to($this->role . '/dataUser');
    }
}
