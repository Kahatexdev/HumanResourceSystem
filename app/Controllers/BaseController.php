<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers:
 *     class Home extends BaseController
 *
 * For security be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
    /**
     * Instance of the main Request object.
     *
     * @var CLIRequest|IncomingRequest
     */
    protected $request;

    /**
     * An array of helpers to be loaded automatically upon
     * class instantiation. These helpers will be available
     * to all other controllers that extend BaseController.
     *
     * @var list<string>
     */
    protected $helpers = [];

    /**
     * Be sure to declare properties for any property fetch you initialized.
     * The creation of dynamic property is deprecated in PHP 8.2.
     */
    // protected $session;

    /**
     * @return void
     */
    protected $attendanceDayModel;
    protected $dataTidakSesuaiService;

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);

        // Preload any models, libraries, etc, here.

        // E.g.: $this->session = service('session');

        // Load Model Anomali
        // $this->attendanceDayModel = new \App\Models\AttendanceDayModel();

        // // Ambil data anomali per tanggal
        // $this->dataTidakSesuaiService = service('dataTidakSesuaiService');
    }

    // protected function renderView($view, $data = [])
    // {
    //     $data['anomali'] = $this->anomali; // inject data ke semua view

    //     return view($view, $data);
    // }

    // protected function renderView($view, $data = [])
    // {
    //     $globalData = $this->loadGlobalData();
    //     $data = array_merge($globalData, $data);

    //     return view($view, $data);
    // }

    // protected function loadGlobalData()
    // {
    //     return [
    //         'dataTidakSesuai' => $this->dataTidakSesuaiService->getCountPerTanggal(),
    //     ];
    // }
}
