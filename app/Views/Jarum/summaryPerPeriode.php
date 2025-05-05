<?php $this->extend('Layout/index'); ?>
<?php $this->section('content'); ?>
<div class="container-fluid py-4">
    <div class="row my-4">
        <div class="col-xl-12 col-sm-12 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">Human Resource System</p>
                                <h5 class="font-weight-bolder mb-0">
                                    Summary Jarum Area (KK1A)
                                </h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <a href="<?= base_url('Monitoring/excelSummaryJarum/') ?>"
                                class="btn bg-gradient-primary me-2">
                                <!-- icon download -->
                                <i class="fas fa-file-excel text-lg opacity-10" aria-hidden="true"></i>
                                Report Excel
                            </a>
                            <!-- button back -->
                            <a href="<?= base_url('Monitoring/dataJarum/') ?>" class="btn bg-gradient-secondary">
                                <!-- icon back -->
                                <i class="fas fa-arrow-left text-lg opacity-10" aria-hidden="true"></i>
                                Back
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xl-12 col-sm-12 mb-xl-0 mb-4 mt-2">
            <div class="card">
                <div class="card-body w-100">
                    <div class="table-responsive">
                        <style>
                            table {
                                border-collapse: collapse;
                                width: 100%;
                            }

                            th,
                            td {
                                text-align: center;
                                padding: 8px;
                            }

                            th.sticky,
                            td.sticky {
                                position: sticky;
                                left: 0;
                                background: #f1f1f1;
                                /* Warna latar belakang kolom sticky */
                                z-index: 2;
                                /* Menjaga agar tetap di atas */
                            }

                            td.sticky:first-of-type {
                                z-index: 3;
                                /* Untuk kolom pertama agar lebih tinggi dari header lainnya */
                            }
                        </style>

                        <table class="table table-responsive w-100">
                            <thead>
                                <tr>
                                    <th class="sticky" colspan="5">SUMMARY JARUM PERIODE AWAL (26-12-2024 S/D 30-12-2024) AREA (KK1A)</th>
                                    <th colspan="21">OKTOBER 2024</th>
                                </tr>
                                <tr>
                                    <th class="sticky">Kode Kartu</th>
                                    <th class="">Nama Lengkap</th>
                                    <th>L/P</th>
                                    <th>TGL. MASUK KERJA</th>
                                    <th>BAGIAN</th>
                                    <?php for ($i = 1; $i <= 20; $i++) : ?>
                                        <th><?= $i ?></th>
                                    <?php endfor; ?>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="sticky">KK1A</td>
                                    <td class="">Andi</td>
                                    <td>L</td>
                                    <td>01-01-2024</td>
                                    <td>MONTIR</td>
                                    <?php for ($i = 1; $i <= 20; $i++) : ?>
                                        <td>0</td>
                                    <?php endfor; ?>
                                    <td>0</td>
                                </tr>
                                <tr>
                                    <td class="sticky">KK1A</td>
                                    <td class="">Budi</td>
                                    <td>L</td>
                                    <td>01-01-2024</td>
                                    <td>MONTIR</td>
                                    <?php for ($i = 1; $i <= 20; $i++) : ?>
                                        <td>0</td>
                                    <?php endfor; ?>
                                    <td>0</td>
                                </tr>
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