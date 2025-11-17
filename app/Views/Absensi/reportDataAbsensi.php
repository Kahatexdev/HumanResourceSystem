<?php $this->extend('layout/template'); ?>
<?php $this->section('content'); ?>

<style>
    .card {
        border-radius: 8px;
    }

    .card-header {
        border-radius: 8px 8px 0 0 !important;
    }

    .icon-shape-area {
        width: 48px;
        height: 48px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
    }

    .border-radius-md {
        border-radius: 8px;
    }

    .text-uppercase {
        letter-spacing: 0.08em;
    }

    .font-weight-bolder {
        font-weight: 700 !important;
    }

    .rounded-circle {
        border-radius: 50% !important;
    }
</style>

<div class="container-fluid py-4">

    <!-- Summary Cards Row -->
    <div class="row">
        <!-- Total Karyawan Card -->
        <div class="col-xl-6 col-sm-6 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-body position-relative">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-uppercase font-weight-bold text-muted">Total Karyawan</p>
                                <h3 class="font-weight-bolder mb-0">
                                    <span class="text-sm font-weight-normal">Orang</span>
                                </h3>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-info shadow text-center border-radius-md">
                                <i class="fas fa-users text-lg opacity-10"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Perpindahan Karyawan Card -->
        <div class="col-xl-6 col-sm-6 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-body position-relative">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-uppercase font-weight-bold text-muted">Perpindahan Karyawan</p>
                                <h3 class="font-weight-bolder mb-0">
                                    <span class="text-sm font-weight-normal">Orang</span>
                                </h3>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-success shadow text-center border-radius-md">
                                <i class="fas fa-exchange-alt text-lg opacity-10"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>



</div>


<?php $this->endSection(); ?>