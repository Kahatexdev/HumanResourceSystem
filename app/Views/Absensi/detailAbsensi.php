<?php $this->extend('layout/template'); ?>
<?php $this->section('content'); ?>

<div class="container-fluid">
    <div class="row my-2">
        <div class="col-xl-12 col-sm-12 mb-xl-0 mb-2">
            <div class="card">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="font-weight-bolder mb-0">
                                <a href="" # class="btn bg-gradient-info">
                                    <!-- icon data karyawan -->
                                    <i class="fas fa-user-tie text-lg opacity-10" aria-hidden="true"></i>
                                </a>
                                Data Karyawan
                            </h4>
                        </div>
                        <div>
                            <div class="d-flex justify-content-between">

                                <a href="<?= base_url($role . '/exportKaryawan/') ?>"
                                    class="btn bg-gradient-primary me-2">
                                    <!-- icon download -->
                                    <i class="fas fa-file-excel text-lg opacity-10" aria-hidden="true"></i>
                                    Export Excel
                                </a>
                                <a href="<?= base_url($role . '/downloadTemplateKaryawan') ?>"
                                    class="btn bg-gradient-success me-2">
                                    <!-- icon download -->
                                    <i class="fas fa-download text-lg opacity-10" aria-hidden="true"></i>
                                    Template Excel
                                </a>
                                <a
                                    class="btn bg-gradient-info add-btn" data-bs-toggle="modal" data-bs-target="#addKaryawan">
                                    <!-- icon tambah karyawan-->
                                    <i class="fas fa-user-plus text-lg opacity-10" aria-hidden="true"></i>
                                    Data Karyawan
                                </a>
                                <div> &nbsp;</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row mt-1">
        <div class="col-xl-12 col-sm-12 mb-xl-0 mb-4 mt-2">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">
                        Tabel Data Log Absesn
                    </h4>
                    <div class="table-responsive">
                        <table id="logTable" class="table table-striped table-hover table-bordered w-100">
                            <thead>
                                <tr>
                                    <th>Terminal ID</th>
                                    <th>NIK</th>
                                    <th>Nama Karyawan</th>
                                    <th>Log Date</th>
                                    <th>Log Time</th>
                                    <th>Dept/CardNo</th>
                                    <th>Admin</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {

            $('#logTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "<?= base_url($role . '/getDetailAbsensiAjax/' . $month . '/' . $year) ?>",
                    type: "POST"
                },
                columns: [{
                        data: "terminal_id"
                    },
                    {
                        data: "nik"
                    },
                    {
                        data: "employee_name"
                    },
                    {
                        data: "log_date"
                    },
                    {
                        data: "log_time"
                    },
                    {
                        data: "dept_card"
                    },
                    {
                        data: "admin"
                    }
                ]
            });

        });
    </script>
    <script>
        $(document).ready(function() {
            // Initialize DataTable with export options
            // $('#logTable').DataTable({});

            // Flash message SweetAlerts
            <?php if (session()->getFlashdata('success')) : ?>
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    html: '<?= session()->getFlashdata('success') ?>',
                });
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')) : ?>
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    html: '<?= session()->getFlashdata('error') ?>',
                });
            <?php endif; ?>
        });
    </script>
    <?php $this->endSection(); ?>