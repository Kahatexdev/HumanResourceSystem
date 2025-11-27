<?php $this->extend('layout/template'); ?>
<?php $this->section('content'); ?>

<div class="container-fluid py-4">
    <!-- HEADER SECTION -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm attendance-header-card">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <div class="d-flex align-items-center">
                                <div>
                                    <h3 class="mb-1 fw-bold text-dark">Data Ketidaksesuaian Absensi</h3>
                                    <p class="mb-0 text-muted small">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Kelola dan catat kehadiran karyawan
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- FILTER SECTION -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-3">
                    <form id="filterForm" class="row g-3 align-items-end">
                        <div class="col-md-4">
                            <label for="startDate" class="form-label fw-semibold mb-2">
                                <i class="fas fa-calendar-alt me-1"></i>Tanggal Mulai
                            </label>
                            <input type="date" class="form-control" id="startDate" name="startDate">
                        </div>
                        <div class="col-md-4">
                            <label for="endDate" class="form-label fw-semibold mb-2">
                                <i class="fas fa-calendar-alt me-1"></i>Tanggal Akhir
                            </label>
                            <input type="date" class="form-control" id="endDate" name="endDate">
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-info px-4">
                                    <i class="fas fa-search me-2"></i>Filter
                                </button>
                                <button type="button" class="btn btn-outline-secondary px-4" id="resetFilter">
                                    <i class="fas fa-redo me-2"></i>Reset
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- TABLE SECTION -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body px-0 pt-0 pb-2">
                    <div class="table-responsive">
                        <table class="table align-items-center mb-0 table-soft" id="tableAbsensi">
                            <thead>
                                <tr>
                                    <th class="text-center text-uppercase">No</th>
                                    <th class="text-center text-uppercase">Tanggal</th>
                                    <th class="text-center text-uppercase">NIK</th>
                                    <th class="text-center text-uppercase">Nama Karyawan</th>
                                    <th class="text-center text-uppercase">Jam Kerja Masuk</th>
                                    <th class="text-center text-uppercase">Jam Kerja Pulang</th>
                                    <th class="text-center text-uppercase">Total Jam Istirahat</th>
                                    <th class="text-center text-uppercase">Shift</th>
                                    <th class="text-center text-uppercase">Masuk</th>
                                    <th class="text-center text-uppercase">Istirahat</th>
                                    <th class="text-center text-uppercase">Kembali</th>
                                    <th class="text-center text-uppercase">Pulang</th>
                                    <th class="text-center text-uppercase">Kerja</th>
                                    <th class="text-center text-uppercase">Break</th>
                                    <th class="text-center text-uppercase">Telat Istirahat</th>
                                    <th class="text-center text-uppercase">P.Cepat</th>
                                    <th class="text-center text-uppercase">P.Telat</th>
                                    <th class="text-center text-uppercase">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Data akan dimuat di sini -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    let table = $('#tableAbsensi').DataTable({
        processing: true,
        serverSide: false,
        ajax: {
            url: "<?= base_url($role . '/ketidaksesuaianAbsensi/getData') ?>",
            data: function(d) {
                d.startDate = $('#startDate').val();
                d.endDate = $('#endDate').val();
            },
            dataSrc: 'data'
        },
        columns: [{
                data: null,
                className: "text-center",
                render: (data, type, row, meta) => meta.row + 1
            },
            {
                data: "work_date"
            },
            {
                data: "nik"
            },
            {
                data: "employee_name"
            },
            {
                data: "start_time"
            },
            {
                data: "end_time"
            },
            {
                data: "break_time"
            },
            {
                data: "shift_name"
            },
            {
                data: "in_time",
                className: "text-center"
            },
            {
                data: "break_out_time",
                className: "text-center"
            },
            {
                data: "break_in_time",
                className: "text-center"
            },
            {
                data: "out_time",
                className: "text-center"
            },
            {
                data: "total_work_min",
                className: "text-end"
            },
            {
                data: "total_break_min",
                className: "text-end"
            },
            {
                data: "late_min",
                className: "text-end"
            },
            {
                data: "early_leave_min",
                className: "text-end"
            },
            {
                data: "overtime_min",
                className: "text-end"
            },
            {
                data: "status_code",
                className: "text-center"
            }
        ]
    });

    // Prefill dari controller (PHP)
    const prefillStart = <?= isset($startDate) && $startDate ? json_encode($startDate) : 'null' ?>;
    const prefillEnd = <?= isset($endDate) && $endDate ? json_encode($endDate) : 'null' ?>;

    window.addEventListener('DOMContentLoaded', function() {
        // jika ada tanggal dari URL (notif), isi input dan reload tabel
        if (prefillStart) {
            $('#startDate').val(prefillStart);
            // jika end tidak ada, set end = start
            if (!prefillEnd) {
                $('#endDate').val(prefillStart);
            } else {
                $('#endDate').val(prefillEnd);
            }
            // reload table sekali untuk memuat data yang terfilter
            table.ajax.reload();
        }

        // Behavior tombol Filter dan Reset (kalau belum ada)
        $('#filterForm').on('submit', function(e) {
            e.preventDefault();
            table.ajax.reload();
        });

        $('#resetFilter').on('click', function() {
            $('#startDate').val('');
            $('#endDate').val('');
            table.ajax.reload();
        });
    });
</script>
<?php $this->endSection(); ?>