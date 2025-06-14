<?php $this->extend('layout/template'); ?>
<?php $this->section('content'); ?>
<div class="container-fluid py-4">
    <div class="row my-4">
        <div class="col-xl-12 col-sm-12 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row align-items-center">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">Human Resource System</p>
                                <h5 class="font-weight-bolder mb-0">
                                    Report Penilaian Per Periode
                                </h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <!-- Fetch Data Button -->
                            <button id="fetch-btn" class="btn bg-gradient-info btn-sm mb-0">Fetch Data</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">

        <?php foreach ($tampilperarea as $key => $ar) : ?>
            <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4 mt-2">
                <a href="<?= base_url($role . '/reportPenilaian/' . $ar['main_factory']) ?>">
                    <div class="card">
                        <div class="card-body p-3">
                            <div class="row">
                                <div class="col-8">
                                    <div class="numbers">
                                        <p class="text-sm mb-0 text-capitalize font-weight-bold"><?= $ar['main_factory'] ?></p>
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
<!-- datatable & fetch script -->
<script>
    $(document).ready(function() {
        // Initialize DataTable
        $('#table_report_batch').DataTable();

        // Fetch Data Button Click
        $('#fetch-btn').on('click', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Fetching data...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });
            $.ajax({
                    url: '<?= base_url($role . "/fetchAssessmentData") ?>',
                    method: 'GET',
                    dataType: 'json'
                })
                .done(function(res) {
                    Swal.close();
                    let html = `<p>${res.message}</p>`;
                    if (res.inserted) {
                        html += `<p>Berhasil: ${res.inserted.length} Karyawan</p>`;
                    }
                    if (res.skipped) {
                        html += `<p>Data yang sudah di Update: ${res.skipped.length} Karyawan</p>`;
                    }
                    Swal.fire({
                        icon: 'success',
                        title: 'Fetch Completed',
                        html: html
                    }).then(() => {
                        // Optionally reload or refresh table/data
                        location.reload();
                    });
                })
                .fail(function(xhr, status, error) {
                    Swal.close();
                    Swal.fire({
                        icon: 'error',
                        title: 'Error fetching data',
                        text: xhr.responseJSON?.message || error
                    });
                });
        });

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