<?php $this->extend('layout/template'); ?>
<?php $this->section('content'); ?>
<div class="container-fluid py-4">
    <?php if (session()->has('warning')): ?>
        <div class="alert alert-warning">
            <?= session('warning') ?>
        </div>
    <?php endif; ?>
    <div class="row my-4">
        <div class="col-xl-12 col-sm-12 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row align-items-center">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">Skill Mapping</p>
                                <h5 class="font-weight-bolder mb-0">
                                    Report Batch Penilaian
                                </h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <!-- button fetch data -->
                            <form action="<?= base_url($role . '/fetchDataFinalAssesment') ?>" method="POST" id="formFetchData">
                                <div class="form-group mb-0 d-flex justify-content-end align-items-end gap-2">
                                    <div>
                                        <input type="hidden" name="main_factory" value="<?= $main_factory ?>">
                                        <select class="form-select form-select-sm" id="id_batch" name="id_batch" required>
                                            <option value="">Pilih Batch Penilaian</option>
                                            <?php foreach ($reportbatch as $batch) : ?>
                                                <option value="<?= $batch['id_batch'] ?>"><?= $batch['batch_name'] ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="align-self-end">
                                        <button type="submit" class="btn bg-gradient-info btn-sm mb-1">Fetch Data</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <?php foreach ($reportbatch as $ar) : ?>
            <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4 mt-2">
                <a href="<?= base_url($role . '/finalAssesment/' . $ar['id_batch'] . '/' . $ar['main_factory'] ?? 'all') ?>">
                    <div class="card">
                        <div class="card-body p-3">
                            <div class="row">
                                <div class="col-8">
                                    <div class="numbers">
                                        <p class="text-sm mb-0 text-capitalize font-weight-bold"><?= $ar['batch_name'] ?></p>
                                        <h5 class="font-weight-bolder mb-0">
                                        </h5>
                                    </div>
                                </div>
                                <div class="col-4 text-end">
                                    <div class="icon icon-shape bg-gradient-info shadow text-center border-radius-md">
                                        <i class="ni ni-money-coins text-lg opacity-10" aria-hidden="true"></i>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </a>
            </div>
        <?php endforeach ?>

    </div>

</div>
<!-- datatable -->
<script>
    $(document).ready(function() {
        // Initialize DataTable with export options
        $('#table_report_batch').DataTable({});

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