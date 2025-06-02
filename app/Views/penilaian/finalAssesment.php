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
                                    <th class="text-center">Nama Karyawan</th>
                                    <th class="text-center">Nilai Rata-rata</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($reportbatch as $emp): ?>
                                    <tr data-employee-id="<?= $emp['id_employee'] ?>">
                                        <td><?= $emp['employee_code'] ?></td>
                                        <td><?= $emp['employee_name'] ?></td>
                                        <td><?= $emp['rata_rata'] ?></td>
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
                detailData[empId].forEach(function(d) {
                    rowsHtml += '<tr><td>Periode ' + d.periode + '</td><td>' + d.nilaiAkhir + '</td></tr>';
                });

                var detailTable =
                    '<table class="table table-sm mb-0 text-center">' +
                    '<thead class="table-light">' +
                    '<tr><th style="width:20%;" class="text-center">Periode</th><th class="text-center">Nilai Akhir</th></tr>' +
                    '</thead>' +
                    '<tbody>' + rowsHtml + '</tbody>' +
                    '</table>';

                // Tampilkan child row
                row.child(detailTable).show();
                tr.addClass('shown');
            }
        });

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