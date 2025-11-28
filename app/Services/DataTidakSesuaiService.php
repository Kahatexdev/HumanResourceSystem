<?php

namespace App\Services;

use App\Models\AttendanceDayModel;

class DataTidakSesuaiService
{
    protected $attendanceDayModel;

    public function __construct()
    {
        $this->attendanceDayModel = new AttendanceDayModel();
    }

    public function getCountPerTanggal()
    {
        return $this->attendanceDayModel->countDataTidakSesuai();
    }
}
