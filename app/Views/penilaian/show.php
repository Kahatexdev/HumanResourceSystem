<?php $this->extend('Layout/index'); ?>
<?php $this->section('content'); ?>

<div class="container-fluid py-4">
    <div class="row my-4">
        <div class="col-xl-12 col-sm-12 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between">
                        <div>
                            Skill Mapping
                            <h4 class="font-weight-bolder">
                                Data Penilaian Mandor
                            </h4>
                            <?= $judul ?>
                        </div>
                        <div>
                            <div class="d-flex justify-content-between">
                                <!-- download report excel -->
                                <a href="<?= base_url('Monitoring/penilaianExcel/' . $penilaian[0]['id_bagian'] . '/' . $penilaian[0]['id_periode'] . '/' . $penilaian[0]['id_jobrole']) ?>" class="btn bg-gradient-success me-2">
                                    <i class="fas fa-file-excel text-lg me-2"></i>Download Excel
                                </a>
                                <a href="<?= base_url('Monitoring/reportPenilaian') ?>" class="btn bg-gradient-secondary ">
                                    <i class="fas fa-solid fa-arrow-left text-lg opacity-10"></i>
                                    Kembali
                                </a>
                                <!-- <a href="<?= base_url('Monitoring/penilaianEdit/' . $penilaian[0]['id_penilaian']) ?>" class="btn bg-gradient-warning">Edit</a> -->
                                <div> &nbsp;</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped w-100" id="tableDetailPenilaianMandor">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Karyawan</th>
                                    <th>Jobdesk</th>
                                    <th>Bobot Nilai</th>
                                    <th>Grade</th>
                                    <th>Before</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $no = 1;
                                $targetCategory = [];
                                function renderList($jobdesc, $keterangan, $targetCategory)
                                {
                                    $output = '';
                                    foreach ($jobdesc as $key => $desc) {
                                        if ($keterangan[$key] == $targetCategory) {
                                            $output .= '<li>' . htmlspecialchars($desc, ENT_QUOTES, 'UTF-8') . ' - ' . htmlspecialchars($keterangan[$key], ENT_QUOTES, 'UTF-8') . '</li>';
                                        }
                                    }
                                    return $output;
                                }

                                foreach ($penilaian as $p) :
                                    $jobdesc = json_decode($p['jobdesc'], true) ?? [];
                                    $keterangan = json_decode($p['keterangan'], true) ?? [];
                                    $index_nilai = json_decode($p['index_nilai'], true) ?? [];
                                    $bobot_nilai = json_decode($p['bobot_nilai'], true) ?? [];

                                    $total_nilai = 0;
                                    $total_bobot = 0;

                                    if (!empty($bobot_nilai) && !empty($index_nilai)) {
                                        foreach ($bobot_nilai as $key => $value) {
                                            $indexVal = $index_nilai[$key] ?? 0;
                                            $total_nilai += $indexVal * $value;
                                            $total_bobot += $value;
                                        }
                                        $grade = $total_bobot > 0 ? $total_nilai / $total_bobot : 0;
                                    } else {
                                        $grade = 0;
                                    }

                                    $badgeClasses = [
                                        'D' => 'bg-gradient-danger',
                                        'C' => 'bg-gradient-warning',
                                        'B' => 'bg-gradient-info',
                                        'A' => 'bg-gradient-primary',
                                    ];

                                    $indexBadge = strtoupper($p['index_nilai']);
                                    $badgeClass = $badgeClasses[$indexBadge] ?? 'bg-gradient-secondary';
                                ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td><?= htmlspecialchars($p['nama_karyawan'], ENT_QUOTES, 'UTF-8') ?></td>
                                        <td>
                                            <ol><?= renderList($jobdesc, $keterangan, 'KNITTER') ?></ol>
                                            <ol><?= renderList($jobdesc, $keterangan, 'C.O') ?></ol>
                                            <ol><?= renderList($jobdesc, $keterangan, 'Ringan') ?></ol>
                                            <ol><?= renderList($jobdesc, $keterangan, 'Standar') ?></ol>
                                            <ol><?= renderList($jobdesc, $keterangan, 'Sulit') ?></ol>
                                            <ol><?= renderList($jobdesc, $keterangan, 'ROSSO') ?></ol>
                                            <ol><?= renderList($jobdesc, $keterangan, 'SETTING') ?></ol>
                                            <ol><?= renderList($jobdesc, $keterangan, 'Potong Manual') ?></ol>
                                            <ol><?= renderList($jobdesc, $keterangan, 'Overdeck') ?></ol>
                                            <ol><?= renderList($jobdesc, $keterangan, 'Obras') ?></ol>
                                            <ol><?= renderList($jobdesc, $keterangan, 'Single Needle') ?></ol>
                                            <ol><?= renderList($jobdesc, $keterangan, 'Mc Lipat') ?></ol>
                                            <ol><?= renderList($jobdesc, $keterangan, 'Mc Kancing') ?></ol>
                                            <ol><?= renderList($jobdesc, $keterangan, 'Mc Press') ?></ol>
                                            <ol><?= renderList($jobdesc, $keterangan, 'JOB') ?></ol>
                                            <ol><?= renderList($jobdesc, $keterangan, '6S') ?></ol>

                                        </td>
                                        <td>
                                            <ul>
                                                <?php foreach ($bobot_nilai as $bobot) : ?>
                                                    <li><?= htmlspecialchars($bobot, ENT_QUOTES, 'UTF-8') ?></li>
                                                <?php endforeach; ?>
                                            </ul>
                                        </td>
                                        <td><span class="badge <?= $badgeClass ?>"><?= htmlspecialchars($indexBadge, ENT_QUOTES, 'UTF-8') ?></span></td>
                                        <td></td>
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
    // datatable
    $(document).ready(function() {
        $('#tableDetailPenilaianMandor').DataTable();
    });
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