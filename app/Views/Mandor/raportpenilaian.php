<?php $this->extend('layout/template'); ?>
<?php $this->section('content'); ?>

<div class="container-fluid py-4">
    <div class="card mb-4">
        <div class="card-header text-white">
            <h4 class="mb-0">Raport Penilaian Karyawan <?= esc($area) ?></h4>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table id="evaluationTable" class="table table-bordered text-center" style="width:100%">
                    <thead>
                        <tr>
                            <th rowspan="2">No</th>
                            <th rowspan="2">Kode Kartu</th>
                            <th rowspan="2">Nama Karyawan</th>
                            <th rowspan="2">Shift</th>
                            <th colspan="12">PENILAIAN</th>
                        </tr>
                        <tr>
                            <th>Jan</th>
                            <th>Feb</th>
                            <th>Mar</th>
                            <th>Apr</th>
                            <th>Mei</th>
                            <th>Jun</th>
                            <th>Jul</th>
                            <th>Agu</th>
                            <th>Sep</th>
                            <th>Okt</th>
                            <th>Nov</th>
                            <th>Des</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($raport)): ?>
                            <tr>
                                <td colspan="15" class="text-center">Tidak ada data penilaian</td>
                            </tr>
                        <?php else: ?>
                            <?php $no = 1; ?>
                            <?php foreach ($raport as $row): ?>
                                <tr>
                                    <td><?= $no++; ?></td>
                                    <td><?= esc($row['employee_code']); ?></td>
                                    <td class="text-left"><?= esc($row['employee_name']); ?></td>
                                    <td class="text-left"><?= esc($row['shift']); ?></td>
                                    <td><?= esc($row['nilai_jan'] ?? '-'); ?></td>
                                    <td><?= esc($row['nilai_feb'] ?? '-'); ?></td>
                                    <td><?= esc($row['nilai_mar'] ?? '-'); ?></td>
                                    <td><?= esc($row['nilai_apr'] ?? '-'); ?></td>
                                    <td><?= esc($row['nilai_mei'] ?? '-'); ?></td>
                                    <td><?= esc($row['nilai_jun'] ?? '-'); ?></td>
                                    <td><?= esc($row['nilai_jul'] ?? '-'); ?></td>
                                    <td><?= esc($row['nilai_agu'] ?? '-'); ?></td>
                                    <td><?= esc($row['nilai_sep'] ?? '-'); ?></td>
                                    <td><?= esc($row['nilai_okt'] ?? '-'); ?></td>
                                    <td><?= esc($row['nilai_nov'] ?? '-'); ?></td>
                                    <td><?= esc($row['nilai_des'] ?? '-'); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php $this->endSection(); ?>