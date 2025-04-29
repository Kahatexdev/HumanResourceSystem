<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\PeriodeModel;
use App\Models\BatchModel;

class PeriodeController extends BaseController
{
    protected $role;
    protected $periodeModel;
    protected $batchModel;

    public function __construct()
    {
        $this->role = session()->get('role');
        $this->periodeModel = new PeriodeModel();
        $this->batchModel = new BatchModel();
    }
    public function index()
    {
        //
    }

    public function store()
    {
        $namaPeriode = $this->request->getPost('nama_periode');
        $idBatch = $this->request->getPost('nama_batch');
        $startDate = $this->request->getPost('start_date');
        $endDate = $this->request->getPost('end_date');
        $status = $this->request->getPost('status');
        // dd($startDate, $endDate);
        // api hari libur

        $url = 'http://172.23.44.14/CapacityApps/public/api/getHariLibur';
        $client = \Config\Services::curlrequest();

        try {
            $response = $client->get($url);
            $rawBody = $response->getBody();

            // Coba tampilkan respon mentah untuk debug
            // dd($rawBody);

            // Jika rawBody tidak langsung JSON, coba bersihkan karakter aneh
            $cleanBody = trim($rawBody);
            // dd ($cleanBody);
            // Decode JSON
            $data = json_decode($cleanBody, true);
            // dd ($data);
            // Validasi JSON
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('Invalid JSON response from API: ' . json_last_error_msg());
            }

            // Ambil data hari libur
            $jml_libur = 0;
            foreach ($data as $item) {
                $tanggal = $item['tanggal'];
                // dd($tanggal);
                if ($tanggal >= $startDate && $tanggal <= $endDate) {
                    $jml_libur++;
                }
            }
            // dd($jml_libur);

        } catch (\Exception $e) {
            return 'Error: ' . $e->getMessage();
        }

        // $jml_libur = $this->request->getPost('jml_libur');
        // dd($namaPeriode, $idBatch, $startDate, $endDate, $status, $jml_libur);
        $errors = [];

        $tempNamaPeriode = $this->periodeModel->where('periode_name', $namaPeriode)->where('id_batch', $idBatch)->first();
        if ($tempNamaPeriode) {
            $errors['periode_name'] = 'Periode Sudah Ada';
        }
        if ($startDate > $endDate) {
            $errors['start_date'] = 'Tanggal mulai tidak boleh lebih besar dari tanggal selesai';
        }
        $tempDate = $this->periodeModel->where('start_date <=', $startDate)->where('end_date >=', $startDate)->where('id_batch', $idBatch)->first();
        if ($tempDate) {
            $errors['start_date'] = 'Tanggal mulai tidak boleh beririsan dengan periode lain';
        }
        $tempDate = $this->periodeModel->where('start_date <=', $endDate)->where('end_date >=', $endDate)->where('id_batch', $idBatch)->first();
        if ($tempDate) {
            $errors['end_date'] = 'Tanggal selesai tidak boleh beririsan dengan periode lain';
        }

        if ($errors) {
            session()->setFlashdata('error', $errors);
            return redirect()->to(base_url($this->role . '/dataPeriode'));
        } else {
            $this->periodeModel->save([
                'periode_name' => $namaPeriode,
                'id_batch' => $idBatch,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'holiday' => $jml_libur,
                'status' => $status
            ]);
            session()->setFlashdata('success', 'Data berhasil ditambahkan');
            return redirect()->to(base_url($this->role . '/dataPeriode'));
        }
    }

    public function update($id)
    {
        $namaPeriode = $this->request->getPost('nama_periode');
        $idBatch = $this->request->getPost('nama_batch');
        $startDate = $this->request->getPost('start_date');
        $endDate = $this->request->getPost('end_date');
        $status = $this->request->getPost('status');
        // $jml_libur = $this->request->getPost('jml_libur');

        $url = 'http://172.23.44.14/CapacityApps/public/api/getHariLibur';
        $client = \Config\Services::curlrequest();

        try {
            $response = $client->get($url);
            $rawBody = $response->getBody();

            // Coba tampilkan respon mentah untuk debug
            // dd($rawBody);

            // Jika rawBody tidak langsung JSON, coba bersihkan karakter aneh
            $cleanBody = trim($rawBody);
            // dd ($cleanBody);
            // Decode JSON
            $data = json_decode($cleanBody, true);
            // dd ($data);
            // Validasi JSON
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('Invalid JSON response from API: ' . json_last_error_msg());
            }

            // Ambil data hari libur
            $jml_libur = 0;
            foreach ($data as $item) {
                $tanggal = $item['tanggal'];
                // dd($tanggal);
                if ($tanggal >= $startDate && $tanggal <= $endDate) {
                    $jml_libur++;
                }
            }
            // dd($jml_libur);

        } catch (\Exception $e) {
            return 'Error: ' . $e->getMessage();
        }

        $errors = [];

        $tempNamaPeriode = $this->periodeModel->where('periode_name', $namaPeriode)->where('id_batch', $idBatch)->where('id_periode !=', $id)->first();
        if ($tempNamaPeriode) {
            $errors['periode_name'] = 'Nama periode sudah ada';
        }
        if ($startDate > $endDate) {
            $errors['start_date'] = 'Tanggal mulai tidak boleh lebih besar dari tanggal selesai';
        }
        $tempDate = $this->periodeModel->where('start_date <=', $startDate)->where('end_date >=', $startDate)->where('id_batch', $idBatch)->where('id_periode !=', $id)->first();
        if ($tempDate) {
            $errors['start_date'] = 'Tanggal mulai tidak boleh beririsan dengan periode lain';
        }
        $tempDate = $this->periodeModel->where('start_date <=', $endDate)->where('end_date >=', $endDate)->where('id_batch', $idBatch)->where('id_periode !=', $id)->first();
        if ($tempDate) {
            $errors['end_date'] = 'Tanggal selesai tidak boleh beririsan dengan periode lain';
        }

        if ($errors) {
            session()->setFlashdata('errors', $errors);
            return redirect()->to(base_url($this->role . '/dataPeriode'));
        } else {
            $this->periodeModel->update($id, [
                'periode_name' => $namaPeriode,
                'id_batch' => $idBatch,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'holiday' => $jml_libur,
                'status' => $status
            ]);
            session()->setFlashdata('success', 'Data berhasil diubah');
            return redirect()->to(base_url($this->role . '/dataPeriode'));
        }
    }

    public function delete($id)
    {
        $this->periodeModel->delete($id);
        session()->setFlashdata('success', 'Data periode berhasil dihapus');
        return redirect()->to(base_url($this->role . '/dataPeriode'));
    }
}
