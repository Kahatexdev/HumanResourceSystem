<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('login', 'AuthController::index');
$routes->post('authverify', 'AuthController::login');
$routes->get('logout', 'AuthController::logout');

$routes->group('/Sudo', ['filter' => 'Sudo'], function ($routes) {
    // user
    $routes->get('/', 'SudoController::index');
    $routes->get('dataUser', 'SudoController::user');
    $routes->get('userCreate', 'UserController::create');
    $routes->post('userStore', 'UserController::store');
    $routes->get('userEdit/(:num)', 'UserController::edit/$1');
    $routes->post('userUpdate/(:num)', 'UserController::update/$1');
    $routes->get('userDelete/(:num)', 'UserController::delete/$1');
    // bagian
    $routes->get('dataBagian', 'SudoController::bagian');
    $routes->get('bagianCreate', 'JobSectionController::create');
    $routes->post('bagianStore', 'JobSectionController::store');
    $routes->get('bagianEdit/(:num)', 'JobSectionController::edit/$1');
    $routes->post('bagianUpdate/(:num)', 'JobSectionController::update/$1');
    $routes->get('bagianDelete/(:num)', 'JobSectionController::delete/$1');
    // karyawan
    $routes->get('datakaryawan', 'SudoController::karyawan');
    $routes->get('downloadTemplateKaryawan', 'EmployeeController::downloadTemplate');
    $routes->post('karyawanStoreImport', 'EmployeeController::upload');
    $routes->get('karyawanCreate', 'EmployeeController::create');
    $routes->post('karyawanStore', 'EmployeeController::store');
    $routes->get('karyawanEdit/(:num)', 'EmployeeController::edit/$1');
    $routes->post('karyawanUpdate/(:num)', 'EmployeeController::update/$1');
    $routes->get('karyawanDelete/(:num)', 'EmployeeController::delete/$1');
    $routes->get('exportKaryawan/', 'EmployeeController::exportAll');
});
