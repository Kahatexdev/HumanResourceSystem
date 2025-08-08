<?php $this->extend('layout/template'); ?>
<?php $this->section('content'); ?>

<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.24/css/dataTables.bootstrap5.min.css">

<div class="container-fluid py-4">
    <?php if (session()->getFlashdata('warning')): ?>
        <div class="alert alert-warning"><?= session()->getFlashdata('warning') ?></div>
    <?php endif; ?>

    <?php if (!empty($noPeriode) && $noPeriode): ?>
        <div class="alert alert-info"><?= esc($periodeMessage ?? 'Tidak ada periode aktif.') ?></div>
    <?php endif; ?>

    <!-- Header -->
    <div class="card mb-4">
        <div class="card-header text-white">
            <h4 class="mb-0">KARYAWAN YANG BELUM DINILAI - Area <?= esc($area) ?></h4>
        </div>
    </div>

    <!-- Tabel Evaluasi Karyawan -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table id="evaluationTable" class="table table-striped table-bordered" style="width:100%">
                    <thead>
                        <tr class="text-center">
                            <th>No</th>
                            <th>Kode Kartu</th>
                            <th>Nama Karyawan</th>
                            <th>Shift</th>
                            <th>Bagian</th>
                            <th>Area</th>
                            <th>Status Evaluasi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($employees) && is_array($employees)) : ?>
                            <?php $no = 1; ?>
                            <?php foreach ($employees as $row) : ?>
                                <tr>
                                    <td class="text-center"><?= $no++ ?></td>
                                    <td><?= esc($row['employee_code']) ?></td>
                                    <td><?= esc($row['employee_name']) ?></td>
                                    <td><?= esc($row['shift']) ?></td>
                                    <td><?= esc($row['job_section_name']) ?></td>
                                    <td><?= esc($row['factory_name'] ?? $area) ?></td>
                                    <td class="text-center">
                                        <?php if (isset($row['status']) && $row['status'] === 'Sudah Dinilai') : ?>
                                            <span class="badge bg-success">Sudah Dinilai</span>
                                        <?php else : ?>
                                            <span class="badge bg-danger">Belum Dinilai</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <tr>
                                <td colspan="7" class="text-center">Tidak ada data evaluasi karyawan</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php
// Filter karyawan yang BELUM dinilai
$notEvaluated = [];
if (!empty($employees) && is_array($employees)) {
    foreach ($employees as $emp) {
        // jika kolom status tidak ada atau bukan 'Sudah Dinilai' maka dianggap belum
        if (!isset($emp['status']) || $emp['status'] !== 'Sudah Dinilai') {
            $notEvaluated[] = $emp;
        }
    }
}
?>

<!-- Modal hanya dirender jika ada karyawan belum dinilai -->
<?php if (!empty($notEvaluated)) : ?>
    <div class="modal fade" id="alertModal" tabindex="-1" aria-labelledby="alertModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable"> <!-- scrollable jika banyak -->
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="alertModalLabel">Karyawan Belum Dinilai (<?= count($notEvaluated) ?>)</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Berikut adalah daftar karyawan yang belum dinilai:</p>
                    <div class="table-responsive" style="max-height:350px; overflow:auto;">
                        <table class="table table-striped table-sm">
                            <thead>
                                <tr>
                                    <th style="width:1%">#</th>
                                    <th style="width:20%">Kode Kartu</th>
                                    <th>Nama Karyawan</th>
                                    <th>Bagian</th>
                                    <th>Shift</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $i = 1; ?>
                                <?php foreach ($notEvaluated as $employee) : ?>
                                    <tr>
                                        <td><?= $i++ ?></td>
                                        <td><?= esc($employee['employee_code']) ?></td>
                                        <td><?= esc($employee['employee_name']) ?></td>
                                        <td><?= esc($employee['job_section_name'] ?? '-') ?></td>
                                        <td><?= esc($employee['shift'] ?? '-') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <p class="mt-2"><small class="text-muted">Tutup modal jika sudah dilihat atau ingin menilai manual lewat menu penilaian.</small></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

<?php else : ?>
    <!-- Jika tidak ada karyawan belum dinilai, tidak perlu modal -->
    <div class="alert alert-info" role="alert">
        Semua karyawan sudah dinilai.
    </div>
<?php endif; ?>

<!-- Scripting: load jQuery dulu, lalu DataTables, lalu Bootstrap JS (bundle) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- DataTables (requires jQuery) -->
<script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap5.min.js"></script>

<!-- Bootstrap 5 bundle (Popper+Bootstrap JS) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
    $(document).ready(function() {
        // Inisialisasi DataTable sekali saja
        $('#evaluationTable').DataTable({
            paging: true,
            pageLength: 10,
            lengthChange: true,
            searching: true,
            ordering: true,
            info: true,
            responsive: true,
            language: {
                url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json'
            }
        });

        // Tampilkan modal alert hanya jika ada elemen #alertModal (yang berarti ada karyawan belum dinilai)
        var alertModalEl = document.getElementById('alertModal');
        if (alertModalEl) {
            var myModal = new bootstrap.Modal(alertModalEl, {
                backdrop: 'static', // opsional: 'static' agar tidak tertutup klik luar, hapus jika ingin bisa ditutup klik luar
                keyboard: true
            });
            myModal.show();
        }
    });
</script>

<?php $this->endSection(); ?>