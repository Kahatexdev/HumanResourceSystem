<?php

use PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\Month;

$this->extend('Layout/index'); ?>
<?php $this->section('content'); ?>
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row my-4">
        <div class="col-xl-12 col-sm-12 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">Human Resource System</p>
                                <h5 class="font-weight-bolder mb-0">
                                    Data Penilaian <?= $area_utama ?>
                                </h5>
                            </div>
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Cards Batch dan Periode -->
    <div class="row">
        <?php if (!empty($penilaian)) : ?>
            <?php
            $currentBatch = null; // Variabel sementara untuk melacak batch saat ini
            ?>
            <?php foreach ($penilaian as $periode) : ?>
                <!-- Tampilkan nama batch hanya jika berbeda dengan batch sebelumnya -->
                <?php if ($periode['nama_batch'] !== $currentBatch) : ?>
                    <?php $currentBatch = $periode['nama_batch']; ?>
                    <div class="col-xl-12 col-sm-12 mb-xl-0 mb-3 mt-3">
                        <div class="card">
                            <div class="card-body p-3">
                                <div class="row">
                                    <div class="col-8">
                                        <div class="numbers">
                                            <p class="text-sm mb-0 text-capitalize font-weight-bold"></p>
                                            <h5 class="font-weight-bolder mb-0"><?= $periode['nama_batch'] ?>
                                            </h5>
                                        </div>
                                    </div>
                                    <div class="col-4 text-end">
                                        <div class="icon icon-shape bg-gradient-info shadow text-center border-radius-md">
                                            <i class="ni ni-folder-17 text-lg opacity-10" aria-hidden="true"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Tampilkan nama periode -->
                <div class="col-xl-4 col-sm-6 mt-3">
                    <a href="<?= base_url($role.'/reportPenilaian/' . $periode['area_utama'] . '/' . $periode['nama_batch'] . '/' . $periode['nama_periode']) ?>" class="text-decoration-none">
                        <div class="card hover-shadow">
                            <div class="card-body">
                                <h6 class="text-dark font-weight-bold">Periode <?= $periode['nama_periode'] ?> (<?= date('M', strtotime($periode['end_date'])) ?>)</h6>
                                <p class="text-muted small mb-0"><?= date('d-m-Y', strtotime($periode['start_date'])) ?> s/d <?= date('d-m-Y', strtotime($periode['end_date'])) ?></p>
                                <div class="d-flex justify-content-between align-items-center mt-2">
                                    <span class="badge bg-gradient-info text-white">Detail</span>
                                    <div class="icon icon-shape bg-gradient-info shadow text-center border-radius-md">
                                        <i class="ni ni-calendar-grid-58 text-white opacity-10" aria-hidden="true"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        <?php else : ?>
            <div class="col-12">
                <p class="text-center text-muted">Tidak ada data penilaian untuk area <?= $area_utama ?>.</p>
            </div>
        <?php endif; ?>
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