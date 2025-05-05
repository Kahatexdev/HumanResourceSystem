<?php $this->extend('layout/template'); ?>
<?php $this->section('content'); ?>

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-xl-12 col-sm-12 mb-xl-0 mb-4 mt-2">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">
                        Filter Data Jarum
                    </h4>
                    <form action="<?= base_url($role . '/filterJarum/' . $area) ?>" method="post">
                        <div class="row">
                            <div class="col-4">
                                <label for="start_date">Tanggal Awal:</label>
                                <input class="form-control" type="date" name="tgl_awal" id="tgl_awal" value="" required>
                            </div>
                            <div class="col-4">
                                <label for="end_date">Tanggal Akhir:</label>
                                <input class="form-control" type="date" name="tgl_akhir" id="tgl_akhir" value="" required>
                            </div>
                            <div class="col-4">
                                <label for="">Aksi</label>
                                <button class="form-control btn btn-info" type="submit">Filter</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-12 col-sm-12 mb-xl-0 mb-4 mt-2">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">
                        Data Penggunaan Jarum
                    </h4>
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Kode Kartu</th>
                                    <th>Nama Karyawan</th>
                                    <th>Penggunaan Jarum</th>
                                    <th>Tanggal Input</th>
                                    <th>Area</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1;
                                        foreach ($pJarum as $jarum) : ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td><?= $jarum['employee_code'] ?></td>
                                        <td><?= $jarum['employee_name'] ?></td>
                                        <td><?= $jarum['used_needle'] ?></td>
                                        <td><?= $jarum['tgl_input'] ?></td>
                                        <td><?= $jarum['factory_name'] ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php $this->endSection(); ?>