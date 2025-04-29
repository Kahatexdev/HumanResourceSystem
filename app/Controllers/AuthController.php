<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\UserModel;

class AuthController extends BaseController
{
    protected $userModel;
    public function __construct()
    {
        $this->userModel = new UserModel();
    }
    public function index(): string
    {
        return view('auth/index');
    }

    public function login()
    {
        //Password perlu di hash?
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');
        $userData = $this->userModel->where('username', $username)->first();
        // dd ($userData);
        if (!$userData) {
            return redirect()->to(base_url('/login'))->withInput()->with('error', 'Invalid username or password');
        }
        if ($password != $userData['password']) {
            return redirect()->to(base_url('/login'))->withInput()->with('error', 'Invalid username or password');
        }
        session()->set('id_user', $userData['id_user']);
        session()->set('username', $userData['username']);
        session()->set('role', $userData['role']);
        session()->set('area', $userData['area']);
        switch ($userData['role']) {
            case 'Sudo':
                return redirect()->to(base_url('/Sudo'));
            case 'Monitoring':
                return redirect()->to(base_url('/Monitoring'));
            case 'Mandor':
                return redirect()->to(base_url('/Mandor'));
            case 'TrainingSchool':
                return redirect()->to(base_url('/TrainingSchool'));
            default:
                return redirect()->to(base_url('/login'))->withInput()->with('error', 'Invalid username or password');
                break;
        }
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to(base_url('/login'));
    }
}
