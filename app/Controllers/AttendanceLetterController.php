<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use DateTime;
use App\Models\AttendanceLetterModel;
use App\Models\EmployeeModel;
use Exception;


class AttendanceLetterController extends BaseController
{
    protected $attendanceLetterModel;
    protected $employeeModel;
    public function __construct()
    {
        $this->attendanceLetterModel = new AttendanceLetterModel();
        $this->employeeModel = new EmployeeModel();
    }

    public function index()
    {
        $data = [
            'role'    => session()->get('role'),
            'title'   => 'Surat Absen',
            'active1' => '',
            'active2' => '',
            'active3' => 'active',
        ];

        return view(session()->get('role') . '/suratAbsen', $data);
    }

    public function lettersData()
    {
        if (! $this->request->isAJAX()) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException();
        }

        $request = $this->request->getGet(); // kita pakai GET biar aman dari CSRF

        $draw   = (int) ($request['draw']   ?? 1);
        $start  = (int) ($request['start']  ?? 0);
        $length = (int) ($request['length'] ?? 10);
        $search = $request['search']['value'] ?? '';

        // mapping kolom DataTables -> kolom DB
        $columns = [
            0  => 'attendance_letters.date_from',
            1  => 'e.employee_code',
            2  => 'e.employee_name',
            3  => 'attendance_letters.letter_type',
            4  => 'attendance_letters.reason',
            5  => 'attendance_letters.status',
            6  => 'attendance_letters.total_days',
            7  => 'input_name',
            8  => 'receiver_name',
            9  => 'received_at',
            10 => 'aksi',
        ];

        $orderColIndex = (int) ($request['order'][0]['column'] ?? 0);
        $orderDir      = $request['order'][0]['dir'] ?? 'asc';
        $orderColumn   = $columns[$orderColIndex] ?? 'attendance_letters.date_from';

        // 1) base query – TANPA ->from()
        $builder = $this->attendanceLetterModel
            ->select("
            attendance_letters.id_letter,
            attendance_letters.date_from,
            attendance_letters.date_to,
            attendance_letters.letter_type,
            attendance_letters.reason,
            attendance_letters.status,
            attendance_letters.total_days,
            attendance_letters.created_at,
            e.employee_code,
            e.employee_name,
            ui.username AS input_name,
            u.username  AS receiver_name,
            attendance_letters.approved_at AS received_at
        ")
            ->join('employees e', 'e.id_employee = attendance_letters.employee_id', 'left')
            ->join('users ui', 'ui.id_user = attendance_letters.created_by', 'left')
            ->join('users u',  'u.id_user  = attendance_letters.approved_by', 'left');

        // total records (sebelum filter)
        $totalRecords = $builder->countAllResults(false);

        // 2) filter global search
        if (! empty($search)) {
            $builder->groupStart()
                ->like('e.employee_code', $search)
                ->orLike('e.employee_name', $search)
                ->orLike('attendance_letters.letter_type', $search)
                ->orLike('attendance_letters.reason', $search)
                ->orLike('attendance_letters.status', $search)
                ->groupEnd();
        }

        // total setelah filter
        $totalFiltered = $builder->countAllResults(false);

        // 3) order + limit
        $builder->orderBy($orderColumn, $orderDir);
        if ($length > 0) {
            $builder->limit($length, $start);
        }

        $rows = $builder->get()->getResultArray();

        // 4) format data untuk DataTables
        $data = [];
        foreach ($rows as $r) {
            $tglRange = $r['date_from'];
            if (! empty($r['date_to']) && $r['date_to'] !== $r['date_from']) {
                $tglRange .= ' s/d ' . $r['date_to'];
            }

            // tombol aksi
            $btnApprove = '<button type="button" class="btn btn-sm bg-gradient-success btn-approve" 
                data-id="' . $r['id_letter'] . '">
                    <i class="fas fa-check-circle fa-sm"></i> APPROVE
               </button>';

            $btnReject = '<button type="button" class="btn btn-sm bg-gradient-danger ms-1 btn-reject" 
                data-id="' . $r['id_letter'] . '">
                    <i class="fas fa-times-circle fa-sm"></i> REJECT
              </button>';

            // logika tampilan kolom aksi
            if ($r['status'] === 'PENDING') {
                $aksiHtml = $btnApprove . ' ' . $btnReject;
            } else {
                if ($r['status'] === 'APPROVED') {
                    $badgeClass = 'bg-gradient-success';
                    $label      = 'SUDAH DI-APPROVE';
                } else {
                    $badgeClass = 'bg-gradient-danger';
                    $label      = 'REJECT';
                }

                $aksiHtml = '<span class="badge ' . $badgeClass . ' px-3 py-2">
                    <i class="fas fa-check-double fa-xs me-1"></i>' . $label . '
                 </span>';
            }

            $data[] = [
                'tgl'        => $tglRange,
                'kode_kartu' => $r['employee_code']      ?? '-',
                'nama'       => $r['employee_name']      ?? '-',
                'jenis'      => $r['letter_type'],
                'ket'        => $r['reason'],
                'status'     => $r['status'],
                'total_hari' => $r['total_days'],
                'input'      => $r['input_name']    ?? '-',
                'penerima'   => $r['receiver_name'] ?? '-',
                'tgl_terima' => $r['received_at'] ? date('Y-m-d', strtotime($r['received_at'])) : '-',
                'aksi'       => $aksiHtml,
            ];
        }

        return $this->response->setJSON([
            'draw'            => $draw,
            'recordsTotal'    => $totalRecords,
            'recordsFiltered' => $totalFiltered,
            'data'            => $data,
        ]);
    }

    public function letterStore()
    {
        $post = $this->request->getPost();

        // 1. Validasi dasar pakai CI4 Validation
        $rules = [
            'id_employee'  => 'required|integer',
            'letter_type'  => 'required|string',
            'date_from'    => 'required|valid_date[Y-m-d]',
            'date_to'      => 'required|valid_date[Y-m-d]',
            'description'  => 'permit_empty|string',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Validasi form tidak lengkap / tidak sesuai.')
                ->with('error_detail', $this->validator->getErrors());
        }

        // 2. Ambil nilai yang sudah dipastikan valid
        $employeeId   = (int) $post['id_employee'];
        $letterType   = $post['letter_type'];
        $dateFrom     = $post['date_from'];
        $dateTo       = $post['date_to'];
        $description  = $post['description'] ?? null;

        // 3. Hitung range hari (INCLUSIVE)
        try {
            $start = new \DateTimeImmutable($dateFrom);
            $end   = new \DateTimeImmutable($dateTo);

            if ($end < $start) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Tanggal selesai tidak boleh lebih kecil dari tanggal mulai.');
            }

            $totalDays = $start->diff($end)->days + 1; // inclusive
        } catch (\Throwable $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Format tanggal tidak valid.');
        }

        // 4. Susun data untuk insert
        $letterData = [
            'employee_id' => $employeeId,
            'letter_type' => $letterType,
            'date_from'   => $dateFrom,
            'date_to'     => $dateTo,
            'total_days'  => $totalDays,
            'reason'      => $description,
            'status'      => 'PENDING',
            'created_by'  => session()->get('id_user'),
        ];

        // 5. Cek duplikat (opsional, selain unique index di DB)
        $exists = $this->attendanceLetterModel
            ->where([
                'employee_id' => $employeeId,
                'date_from'   => $dateFrom,
                'date_to'     => $dateTo,
            ])
            ->first();

        if ($exists) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Surat absen untuk karyawan dan rentang tanggal tersebut sudah ada.');
        }

        // 6. Simpan ke database
        try {
            if (! $this->attendanceLetterModel->insert($letterData)) {
                // kalau pakai Model CI4, bisa ambil detail errornya
                $modelErrors = $this->attendanceLetterModel->errors();

                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Gagal menyimpan surat absen.')
                    ->with('error_detail', $modelErrors);
            }

            return redirect()->back()
                ->with('success', 'Surat absen berhasil disimpan.');
        } catch (\Throwable $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat menyimpan surat absen.')
                ->with('error_detail', [$e->getMessage()]);
        }
    }

    public function letterUpdateStatus()
    {
        if (! $this->request->is('post')) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException();
        }

        $idLetter   = (int) $this->request->getPost('id_letter');
        $actionType = $this->request->getPost('action_type'); // APPROVE / REJECT
        $actionDate = $this->request->getPost('action_date');

        if (! $idLetter || ! $actionType || ! $actionDate) {
            return redirect()->back()->with('error', 'Data tidak lengkap untuk proses approve/reject.');
        }

        // validasi tanggal
        try {
            $dt         = new \DateTimeImmutable($actionDate);
            $actionDate = $dt->format('Y-m-d');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Format tanggal tidak valid.');
        }

        $userId = session()->get('id_user');

        $updateData = [];

        if ($actionType === 'APPROVE') {

            $updateData = [
                'status'      => 'APPROVED',
                'approved_at' => $actionDate,
            ];

            // CEK KOLOM approved_by DI DATABASE, BUKAN DI MODEL
            $db    = \Config\Database::connect();
            $table = $this->attendanceLetterModel->getTable(); // misal: 'attendance_letters'

            if ($db->fieldExists('approved_by', $table)) {
                $updateData['approved_by'] = $userId;
            }
        } elseif ($actionType === 'REJECT') {

            $updateData = [
                'status' => 'REJECTED',
            ];
        } else {
            return redirect()->back()->with('error', 'Tipe aksi tidak dikenal.');
        }

        try {
            $this->attendanceLetterModel->update($idLetter, $updateData);

            $msg = $actionType === 'APPROVE'
                ? 'Surat absen berhasil di-approve.'
                : 'Surat absen berhasil di-reject.';

            return redirect()->back()->with('success', $msg);
        } catch (\Throwable $e) {
            return redirect()->back()
                ->with('error', 'Gagal memproses surat absen.')
                ->with('error_detail', [$e->getMessage()]);
        }
    }
}
