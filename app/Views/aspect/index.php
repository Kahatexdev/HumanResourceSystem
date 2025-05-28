<?php $this->extend('layout/template'); ?>
<?php $this->section('content'); ?>


<div class="container-fluid py-4">
    
    <div class="row mt-4">
        <div class="col-xl-12 col-sm-12 mb-xl-0 mb-4 mt-2">
            <div class="card">
                <div class="card-body">
                    <h4>Daftar Aspek Penilaian</h4>
                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger">
                            <?= session()->getFlashdata('error') ?>
                        </div>
                    <?php endif; ?>

                    <?php if (session()->getFlashdata('success')): ?>
                        <div class="alert alert-success">
                            <?= session()->getFlashdata('success') ?>
                        </div>
                    <?php endif; ?>

                    <table class="table table-striped table-bordered w-100">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Bagian</th>
                                <th>Aspek Penilaian</th>
                                <th>Persentase (%)</th>
                                <?php if ($role === 'Sudo'): ?>
                                    <th>Aksi</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($aspects as $index => $aspect): ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    <td><?= $aspect['department'] ?></td>
                                    <td><?= $aspect['aspect'] ?></td>
                                    <td><?= $aspect['percentage'] ?></td>
                                    
                                    <?php if ($role === 'Sudo'): ?>
                                        <td>
                                            <a href="<?= base_url('Sudo/aspectEdit/' . $aspect['id_aspect']) ?>" class="btn btn-warning btn-sm">Edit</a>
                                            <a href="<?= base_url('Sudo/aspectDelete/' . $aspect['id_aspect']) ?>" class="btn btn-danger btn-sm">Hapus</a>
                                        </td>
                                    <?php endif; ?>
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