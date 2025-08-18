<?php $this->extend('layout/template'); ?>
<?php $this->section('content'); ?>
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row my-2">
        <div class="col-xl-12 col-sm-12 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">Human Resource System</p>
                                <h5 class="font-weight-bolder mb-0">
                                    Data Penilaian <?= $main_factory ?> Batch <?= $batch_name ?>
                                </h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="d-flex justify-content-end align-items-end gap-2">
                                <?php if (session()->get('role') == 'Monitoring'): ?>
                                    <form action="<?= base_url($role . '/exportFinalAssessment') ?>" method="POST" id="formExport">
                                        <input type="hidden" name="id_batch" value="<?= $id_batch ?>">
                                        <input type="hidden" name="main_factory" value="<?= $main_factory ?? 'all' ?>">
                                        <button type="submit" class="btn bg-gradient-success mb-1">
                                            <i class="fas fa-file-excel me-1"></i>
                                            Export Data
                                        </button>
                                    </form>
                                    <form action="<?= base_url($role . '/printFinalAssessment') ?>" method="POST" id="formPrint">
                                        <input type="hidden" name="id_batch" value="<?= $id_batch ?>">
                                        <input type="hidden" name="main_factory" value="<?= $main_factory ?? 'all' ?>">
                                        <button type="submit" class="btn bg-gradient-primary mb-1">
                                            <i class="fas fa-print me-1"></i>
                                            Aspek Penilaian
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel utama -->
    <div class="row">
        <div class="col-xl-12 col-sm-12 mb-xl-0 mb-4 mt-2">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Tabel Data Penilaian Akhir Karyawan</h5>
                    <div class="table-responsive">
                        <table id="tableFinalAssessment" class="table table-bordered table-striped table-hover text-center">
                            <thead>
                                <tr>
                                    <th class="text-center">Kode Kartu</th>
                                    <th class="text-center">Nama Lengkap</th>
                                    <th class="text-center">L/P</th>
                                    <th class="text-center">TGL. Masuk Kerja</th>
                                    <th class="text-center">Bagian</th>
                                    <th class="text-center">Grade</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($reportbatch as $emp): ?>
                                    <tr data-employee-id="<?= $emp['id_employee'] ?>">
                                        <td><?= $emp['employee_code'] ?></td>
                                        <td><?= $emp['employee_name'] ?></td>
                                        <td><?= $emp['gender'] ?></td>
                                        <td><?= $emp['date_of_joining'] ?></td>
                                        <td><?= $emp['main_job_role_name'] ?></td>
                                        <td><?= $emp['grade'] ?></td>
                                        <td>
                                            <button class="btn btn-sm bg-gradient-info btn-show-detail">
                                                Lihat Detail
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Parse JSON detail yang dikirim dari Controller
        var detailData = <?= $jsonDetail ?>;

        // Inisialisasi DataTable
        var table = $('#tableFinalAssessment').DataTable({
            columnDefs: [{
                    orderable: false,
                    targets: 3
                } // Kolom "Aksi" tidak bisa di-sort
            ],
            order: [
                [0, 'asc']
            ],
            language: {
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ entri",
                zeroRecords: "Tidak ditemukan data yang cocok",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
                infoEmpty: "Menampilkan 0 sampai 0 dari 0 entri",
                paginate: {
                    first: "Pertama",
                    last: "Terakhir",
                    next: "Berikutnya",
                    previous: "Sebelumnya"
                }
            }
        });

        // Event klik tombol “Lihat Detail”
        $('#tableFinalAssessment tbody').on('click', 'button.btn-show-detail', function() {
            var tr = $(this).closest('tr');
            var row = table.row(tr);
            var empId = tr.data('employee-id');

            if (row.child.isShown()) {
                // Sembunyikan kembali detail
                row.child.hide();
                tr.removeClass('shown');
            } else {
                // Bangun HTML sub-tabel berdasarkan detailData[empId]
                var rowsHtml = '';
                // detailData[empId].forEach(function(d) {
                //     console.log("Row detail:", d);
                // });

                detailData[empId].forEach(function(d) {
                    rowsHtml += '<tr><td>' +
                        d.month + '</td><td>' +
                        d.perf_job_pct + '% </td><td>' +
                        d.perf_presence + '% </td><td>' +
                        // PRODUKSI
                        ((d.prod !== undefined && d.prod !== null && d.prod != 0) ?
                            '<span>' + d.prod + '</span>' :
                            '<span class="fw-bold">' + (d.rosso_prod || 0) + ''
                        ) + '</td><td>' +
                        // BS
                        ((d.bs !== undefined && d.bs !== null && d.bs != 0) ?
                            '<span>' + d.bs + '</span>' :
                            '<span class="fw-bold">' + (d.rosso_bs || 0) + ''
                        ) + '</td><td>' +
                        (d.prod_needle || 0) + '</td>' +
                        '</tr>';
                });

                var detailTable =
                    '<table class="table table-sm mb-0 text-center table-bordered table-striped">' +
                    '<thead class="table-light">' +
                    '<tr><th style="width:10%;" class="text-center align-middle" rowspan="2">Periode</th><th class="text-center align-middle" rowspan="2">Skill</th><th class="text-center align-middle" rowspan="2">Absen</th><th class="text-center align-middle" colspan="1">Produksi</th><th class="text-center align-middle" colspan="1">BS</th><th class="text-center align-middle" colspan="1">Pemakaian Jarum</th></tr>' +
                    '<tr><th class="text-center align-middle">Total</th>' +
                    '<th class="text-center align-middle">Total</th>' +
                    '<th class="text-center align-middle">Total</th>' +
                    '</tr>' +
                    '</thead>' +
                    '<tbody>' + rowsHtml + '</tbody>' +
                    '</table>';

                // Tampilkan child row
                row.child(detailTable).show();
                tr.addClass('shown');
            }
        });

        // Flash message warning dengan SweetAlert (jika ada)
        <?php if (session()->getFlashdata('warning')): ?>
            Swal.fire({
                icon: 'warning',
                title: 'Warning!',
                html: '<?= session()->getFlashdata('warning') ?>',
            });
        <?php endif; ?>

        // Flash message dengan SweetAlert (jika ada)
        <?php if (session()->getFlashdata('success')): ?>
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                html: '<?= session()->getFlashdata('success') ?>',
            });
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                html: '<?= session()->getFlashdata('error') ?>',
            });
        <?php endif; ?>
    });
</script>
<?php $this->endSection(); ?>