<?php $this->extend('layout/template'); ?>
<?php $this->section('content'); ?>
<!-- swall alert cdn -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
<div class="container-fluid">
    <div class="row my-4">
        <div class="col-xl-12 col-sm-12 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">Skill Mapping</p>
                                <h5 class="font-weight-bolder mb-0">
                                    Data History Pindah Karyawan
                                </h5>
                            </div>
                            <!-- Modal for Import History Karyawan -->
                            <div class="modal fade" id="importHistoryKaryawanModal" tabindex="-1" aria-labelledby="importHistoryKaryawanModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="importHistoryKaryawanModalLabel">Update Kode Kartu</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <form action="<?= base_url('TrainingSchool/updateEmployeeCode') ?>" method="post" enctype="multipart/form-data">
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label for="file" class="form-label">Pilih file Excel (.xlsx, .xls, .csv)</label>
                                                    <input type="file" class="form-control" id="file" name="file" accept=".xlsx, .xls, .csv" required>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                                <button type="submit" class="btn btn-success">Import</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-4 d-flex justify-content-end align-items-center">

                            <a href="<?= base_url('TrainingSchool/reportHistoryPindahKaryawan') ?>"
                                class="btn bg-gradient-primary me-2 d-flex align-items-center">
                                <i class="fas fa-file-excel text-lg opacity-10 me-1" aria-hidden="true"></i>
                                Export Excel
                            </a>
                            <button class="btn bg-gradient-success d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#importHistoryKaryawanModal">
                                <i class="fas fa-file-import text-lg opacity-10 me-1" aria-hidden="true"></i>
                                Update Kode Kartu
                            </button>
                        </div>
                        <!-- <div class="col-12">
                            <form action="<?= base_url('TrainingSchool/importHistoryEmployee') ?>" method="post" enctype="multipart/form-data">
                                <div class="input-group mb-3">
                                    <input type="file" class="form-control" name="file" accept=".xlsx, .xls, .csv" required>
                                    <button class="btn bg-gradient-success" type="submit">Import</button>
                                </div>
                            </form>
                        </div> -->
                    </div>
                </div>
            </div>

        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card card-primary card-outline">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="example1" class="table table-striped table-hover table-bordered w-100">
                            <thead>
                                <tr>
                                    <th>Kode Kartu</th>
                                    <th>Nama Karyawan</th>
                                    <th>Bagian Asal</th>
                                    <th>Bagian Baru</th>
                                    <th>Tanggal Pindah</th>
                                    <th>Keterangan</th>
                                    <th>Diupdate Oleh</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($historyPindahKaryawan as $row): ?>
                                    <tr>
                                        <td><?= esc($row['employee_code']); ?></td>
                                        <td><?= esc($row['employee_name']); ?></td>
                                        <td><?= esc($row['job_section_old']) . '-' . esc($row['mainFactory_old']) . '-' . esc($row['factoryName_old']); ?></td>
                                        <td><?= esc($row['job_section_new']) . '-' . esc($row['mainFactory_new']) . '-' . esc($row['factoryName_new']); ?></td>
                                        <td><?= esc($row['date_of_change']); ?></td>
                                        <td><?= esc($row['reason']); ?></td>
                                        <td><?= esc($row['updated_by']); ?></td>
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
        // Initialize DataTable with export options
        $('#example1').DataTable({});
    });
</script>

<?php if (session()->getFlashdata('success')): ?>
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Berhasil',
            html: '<?= session()->getFlashdata('success'); ?>',
            confirmButtonText: 'OK'
        });
    </script>
<?php endif; ?>
<!-- alert warning -->
 <?php if (session()->getFlashdata('warning')): ?>
    <script>
        Swal.fire({
            icon: 'warning',
            title: 'Peringatan',
            html: '<?= session()->getFlashdata('warning'); ?>',
            confirmButtonText: 'OK'
        });
    </script>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Gagal',
            html: '<?= session()->getFlashdata('error'); ?>',
            confirmButtonText: 'OK'
        });
    </script>
<?php endif; ?>
<?php $this->endSection(); ?>