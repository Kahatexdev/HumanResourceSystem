<?= $this->extend('layout/template'); ?>
<?= $this->section('content'); ?>

<div class="container mt-4">

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success">
            <?= esc(session()->getFlashdata('success')) ?>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger">
            <?= esc(session()->getFlashdata('error')) ?>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm mb-4">
        <div class="card-header bg-info text-white">
            <strong>Promote Attendance Logs → Attendance Days</strong>
        </div>
        <div class="card-body">
            <form method="post" action="<?= base_url($role . '/attendance/promote'); ?>">
                <?= csrf_field(); ?>

                <div class="row g-3">
                    <div class="col-md-5">
                        <label class="form-label">Tanggal Awal</label>
                        <input type="date"
                            name="date_from"
                            class="form-control"
                            required
                            value="<?= esc($date_from ?? '') ?>">
                    </div>
                    <div class="col-md-5">
                        <label class="form-label">Tanggal Akhir</label>
                        <input type="date"
                            name="date_to"
                            class="form-control"
                            value="<?= esc($date_to ?? '') ?>">
                        <small class="text-muted">Kosongkan kalau hanya 1 hari.</small>
                    </div>
                    <div class="col-md-2 d-flex mb-2 align-items-end">
                        <button type="submit" class="btn btn-info w-100">
                            Kalkulasi Jam Absensi <i class="fas fa-clock"></i>
                        </button>
                    </div>
                </div>


            </form>
        </div>
    </div>

    <?php if (isset($processed) && $processed !== null): ?>
        <div class="alert alert-info">
            <?= esc($processed) ?> data attendance_days berhasil diproses,
            dan <?= esc($resProcessed ?? 0) ?> data attendance_results berhasil dihitung
            untuk tanggal <strong><?= esc($date_from) ?></strong>
            s/d <strong><?= esc($date_to) ?></strong>.
        </div>
    <?php endif; ?>


    <?php if (!empty($days)): ?>
        <div class="card shadow-sm">
            <div class="card-header bg-secondary text-white">
                <strong>Data Attendance Days yang Diproses</strong>
            </div>
            <div class="card-body table-responsive">
                <table class="table table-sm table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center">#</th>
                            <th>Tanggal Kerja</th>
                            <th>NIK</th>
                            <th>Nama</th>
                            <th>Shift</th>
                            <th>IN</th>
                            <th>Break Out</th>
                            <th>Break In</th>
                            <th>OUT</th>
                            <th>Total Kerja (menit)</th>
                            <th>Break (menit)</th>
                            <th>Telat (menit)</th>
                            <th>Pulang Cepat (menit)</th>
                            <th>Lembur (menit)</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; ?>
                        <?php foreach ($days as $row): ?>
                            <tr>
                                <td class="text-center"><?= $no++ ?></td>
                                <td><?= esc($row['work_date']) ?></td>
                                <td><?= esc($row['nik'] ?? '-') ?></td>
                                <td><?= esc($row['employee_name'] ?? '-') ?></td>
                                <td><?= esc($row['shift_name'] ?? ($row['id_shift'] ?? '-')) ?></td>
                                <td><?= esc($row['in_time'] ?? '-') ?></td>
                                <td><?= esc($row['break_out_time'] ?? '-') ?></td>
                                <td><?= esc($row['break_in_time'] ?? '-') ?></td>
                                <td><?= esc($row['out_time'] ?? '-') ?></td>

                                <td class="text-end"><?= esc($row['total_work_min'] ?? 0) ?></td>
                                <td class="text-end"><?= esc($row['total_break_min'] ?? 0) ?></td>
                                <td class="text-end"><?= esc($row['late_min'] ?? 0) ?></td>
                                <td class="text-end"><?= esc($row['early_leave_min'] ?? 0) ?></td>
                                <td class="text-end"><?= esc($row['overtime_min'] ?? 0) ?></td>
                                <td><?= esc($row['status_code'] ?? '-') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <small class="text-muted">
                    Hanya menampilkan data attendance_days pada range tanggal yang diproses.
                </small>
            </div>
        </div>
    <?php elseif (isset($processed) && $processed === 0): ?>
        <div class="alert alert-warning">
            Tidak ada data attendance_days yang terbentuk pada range tanggal tersebut.
            (Mungkin semua group log ≤ 3, atau tidak ada log.)
        </div>
    <?php endif; ?>

</div>

<?= $this->endSection(); ?>