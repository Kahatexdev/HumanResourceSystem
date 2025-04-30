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
    // batch
    $routes->get('dataBatch', 'SudoController::batch');
    $routes->get('batchCreate', 'BatchController::create');
    $routes->post('batchStore', 'BatchController::store');
    $routes->get('batchEdit/(:num)', 'BatchController::edit/$1');
    $routes->post('batchUpdate/(:num)', 'BatchController::update/$1');
    $routes->get('batchDelete/(:num)', 'BatchController::delete/$1');
    // periode
    $routes->get('dataPeriode', 'SudoController::periode');
    $routes->get('periodeCreate', 'PeriodeController::create');
    $routes->post('periodeStore', 'PeriodeController::store');
    $routes->get('periodeEdit/(:num)', 'PeriodeController::edit/$1');
    $routes->post('periodeUpdate/(:num)', 'PeriodeController::update/$1');
    $routes->get('periodeDelete/(:num)', 'PeriodeController::delete/$1');
    // absen
    $routes->get('dataAbsen', 'SudoController::absen');
    $routes->get('absenCreate', 'PresenceController::create');
    $routes->post('absenStore', 'PresenceController::store');
    $routes->get('absenEdit/(:num)', 'PresenceController::edit/$1');
    $routes->post('absenUpdate/(:num)', 'PresenceController::update/$1');
    $routes->get('absenDelete/(:num)', 'PresenceController::delete/$1');
    $routes->get('absenImport', 'PresenceController::import');
    $routes->get('downloadTemplateAbsen', 'PresenceController::downloadTemplate');
    $routes->post('absenStoreImport', 'PresenceController::upload');
    $routes->get('absenReport', 'PresenceController::absenReport');
    $routes->get('absenEmpty', 'PresenceController::empty');
    // jobrole
    $routes->get('dataJob', 'SudoController::job');
    $routes->post('mainJobStore', 'JobroleController::mainJobStore');
    $routes->post('mainJobUpdate/(:num)', 'JobroleController::mainJobUpdate/$1');
    $routes->get('mainJobDelete/(:num)', 'JobroleController::mainJobDelete/$1');
    $routes->get('getJobRoles/(:num)', 'JobroleController::getJobRoles/$1');
    $routes->get('jobroleCreate', 'JobroleController::create');
    $routes->post('jobRoleStore', 'JobroleController::jobRoleStore');
    $routes->post('jobRoleUpdate/(:num)', 'JobroleController::jobRoleUpdate/$1');
    $routes->get('jobRoleDelete/(:num)', 'JobroleController::jobRoleDelete/$1');
    $routes->get('jobroleEdit/(:num)', 'JobroleController::edit/$1');
    // penilaian
    $routes->get('dataPenilaian', 'SudoController::penilaian');
    $routes->get('getKaryawan', 'EmployeeAssessmentController::getKaryawan');
    $routes->post('penilaianCreate', 'EmployeeAssessmentController::create');
    $routes->post('penilaianStore', 'EmployeeAssessmentController::store');
});
