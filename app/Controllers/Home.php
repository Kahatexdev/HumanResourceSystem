<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index(): string
    {
        return view('dashboard', ['title' => 'Soft UI Dashboard']);
    }

    public function auth(): string
    {
        return view('auth/index', ['title' => 'Soft UI Dashboard']);
    }
}
