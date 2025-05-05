<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'AuthController::index');
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
    $routes->get('dataKaryawan', 'SudoController::karyawan');
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

    // summary jarum
    $routes->get('dataJarum', 'SudoController::jarum');
    $routes->get('dataJarum/(:segment)', 'JarumController::tampilPerBatch/$1');
    $routes->get('summaryJarum', 'JarumController::summaryJarum');
    $routes->get('downloadTemplateJarum', 'JarumController::downloadTemplate');
    $routes->get('filterJarum/(:segment)', 'JarumController::filterJarum/$1');
    $routes->post('filterJarum/(:segment)', 'JarumController::filterJarum/$1');
    // $routes->post('jarumStoreImport', 'JarumController::upload');
    $routes->post('jarumStoreInput', 'JarumController::upload');
    $routes->post('getMontirByArea', 'SudoController::getMontirByArea');
    $routes->get('reportSummaryJarum/(:segment)/(:num)', 'JarumController::excelSummaryJarum/$1/$2');
    $routes->post('uploadJarum', 'JarumController::uploadJarum');
});

$routes->group('/Monitoring', ['filter' => 'Monitoring'], function ($routes) {
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
    $routes->get('dataKaryawan', 'SudoController::karyawan');
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

    // summary jarum
    $routes->get('dataJarum', 'MonitoringController::jarum');
    $routes->get('dataJarum/(:segment)', 'JarumController::tampilPerBatch/$1');
    $routes->get('summaryJarum', 'JarumController::summaryJarum');
    $routes->get('downloadTemplateJarum', 'JarumController::downloadTemplate');
    $routes->get('filterJarum/(:segment)', 'JarumController::filterJarum/$1');
    $routes->post('filterJarum/(:segment)', 'JarumController::filterJarum/$1');
    // $routes->post('jarumStoreImport', 'JarumController::upload');
    $routes->post('jarumStoreInput', 'JarumController::upload');
    $routes->post('getMontirByArea', 'MonitoringController::getMontirByArea');
    $routes->get('reportSummaryJarum/(:segment)/(:num)', 'JarumController::excelSummaryJarum/$1/$2');
    $routes->post('uploadJarum', 'JarumController::uploadJarum');
});

$routes->group('/Mandor', ['filter' => 'Mandor'], function ($routes) {
    $routes->get('', 'MandorController::dashboard');
    $routes->get('dataKaryawan', 'MandorController::listArea');
    $routes->get('dataKaryawan/(:any)', 'MandorController::detailKaryawanPerArea/$1');
    $routes->get('dataPenilaian', 'MandorController::penilaian');
    $routes->get('raportPenilaian/(:any)', 'MandorController::raportPenilaian/$1');
    $routes->get('instruksiKerja', 'MandorController::instruksiKerja');
    $routes->get('evaluasiKaryawan/(:any)/(:any)', 'MandorController::getEmployeeEvaluationStatus/$1/$2');
});


$routes->group('/TrainingSchool', ['filter' => 'TrainingSchool'], function ($routes) {
    $routes->get('', 'TrainingSchoolController::index');
    $routes->get('dataKaryawan', 'TrainingSchoolController::listArea');
    $routes->get('dataKaryawan/(:any)', 'TrainingSchoolController::detailKaryawanPerArea/$1');
    $routes->get('downloadTemplateKaryawan', 'EmployeeController::downloadTemplate');
    $routes->post('karyawanStoreImport', 'EmployeeController::upload');
    $routes->get('exportKaryawan/(:any)', 'EmployeeController::exportPerArea/$1');

    $routes->get('karyawanCreate', 'EmployeeController::create');
    $routes->post('karyawanStore', 'EmployeeController::store');
    $routes->get('karyawanEdit/(:num)', 'EmployeeController::edit/$1');
    $routes->post('karyawanUpdate/(:num)', 'EmployeeController::update/$1');
    $routes->get('karyawanDelete/(:num)', 'EmployeeController::delete/$1');

    $routes->get('historyPindahKaryawan', 'TrainingSchoolController::historyPindahKaryawan');
    $routes->get('reportHistoryPindahKaryawan', 'HistoryPindahKaryawanController::reportExcel');

    // routes/web.php atau routes.php (tergantung pada versi CodeIgniter)
    $routes->get('contacts', 'ChatController::getContactsWithLastMessage');
    $routes->get('chat', 'TrainingSchoolController::chat');

    // $routes->get('conversation/(:num)/(:num)', 'ChatController::fetchConversation/$1/$2');
    // $routes->post('send-message', 'ChatController::sendMessage');
    $routes->get('conversation/(:num)/(:num)', 'ChatController::fetchConversation/$1/$2');
    $routes->post('send-message', 'ChatController::sendMessage');
    $routes->get('getContacts/(:num)', 'ChatController::getContacts/$1');
    $routes->post('mark-messages-as-read/(:num)', 'ChatController::markMessagesAsRead/$1');
    $routes->get('count-unread-messages', 'ChatController::countUnreadMessages');
    $routes->get('check-new-messages', 'ChatController::checkNewMessages');
    $routes->get('long-poll-new-messages', 'ChatController::longPollNewMessages'); // Untuk long polling


    // penilaian
    $routes->get('reportPenilaian', 'MonitoringController::reportpenilaian');
    $routes->get('reportPenilaian/(:segment)', 'PenilaianController::penilaianPerArea/$1');
    $routes->get('reportPenilaian/(:segment)/(:segment)/(:segment)', 'PenilaianController::excelReportPerPeriode/$1/$2/$3');
    $routes->get('reportBatch', 'MonitoringController::reportBatch');
    $routes->get('reportBatch/(:segment)', 'PenilaianController::reportAreaperBatch/$1');
    $routes->get('exelReportBatch/(:num)/(:segment)', 'PenilaianController::exelReportBatch/$1/$2');
});

$routes->group('api', function ($routes) {
    $routes->get('karyawan', 'ApiController::index');
    $routes->get('karyawan/(:segment)', 'ApiController::show/$1');

    $routes->get('area_utama/(:segment)', 'ApiController::getKaryawanByAreaUtama/$1');
    $routes->get('area/(:segment)', 'ApiController::getKaryawanByArea/$1');
    $routes->get('getdataforbs/(:any)/(:any)', 'ApiController::getDataForBsMc/$1/$2');
});