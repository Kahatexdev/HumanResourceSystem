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
                        </div>
                        <div class="col-4 text-end">
                            <a href="<?= base_url('TrainingSchool/reportHistoryPindahKaryawan') ?>"
                                class="btn bg-gradient-primary me-2">
                                <!-- icon download -->
                                <i class="fas fa-file-excel text-lg opacity-10" aria-hidden="true"></i>
                                Export Excel
                            </a>
                        </div>
                        <div class="col-12">
                            <!-- import history karyawan -->
                            <form action="<?= base_url('TrainingSchool/importHistoryEmployee') ?>" method="post" enctype="multipart/form-data">
                                <div class="input-group mb-3">
                                    <input type="file" class="form-control" name="file" accept=".xlsx, .xls, .csv" required>
                                    <button class="btn bg-gradient-success" type="submit">Import</button>
                                </div>
                            </form>
                        </div>
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
        text: '<?= session()->getFlashdata('success'); ?>',
        confirmButtonText: 'OK'
    });
</script>
<?php endif; ?>
<?php if (session()->getFlashdata('error')): ?>
<script>
    Swal.fire({
        icon: 'error',
        title: 'Gagal',
        text: '<?= session()->getFlashdata('error'); ?>',
        confirmButtonText: 'OK'
    });
</script>
<?php endif; ?>
<?php $this->endSection(); ?>