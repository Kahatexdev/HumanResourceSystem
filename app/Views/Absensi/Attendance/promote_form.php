<?= $this->extend('layout/template'); ?>
<?= $this->section('content'); ?>

<style>
    /* Sedikit custom untuk tabel & badge jam */
    .table-soft thead tr th {
        background: transparent;
        border-bottom: 1px solid rgba(0, 0, 0, .05);
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: .04em;
        font-weight: 700;
        color: #6c757d;
    }

    .table-soft tbody tr:hover {
        background-color: rgba(244, 247, 254, 0.7);
        transition: background-color 0.15s ease-in-out;
    }

    .time-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0.25rem 0.6rem;
        border-radius: 0.5rem;
        font-family: "Courier New", monospace;
        font-size: 0.75rem;
        background-color: #f8f9fa;
        border: 1px dashed rgba(0, 0, 0, 0.05);
    }

    .metric-positive {
        color: #17c1e8;
        font-weight: 600;
    }

    .metric-negative {
        color: #ea0606;
        font-weight: 600;
    }

    .metric-neutral {
        color: #67748e;
        font-weight: 600;
    }
</style>

<div class="container-fluid py-4">

    <!-- Flash Messages -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show text-white bg-gradient-success shadow border-radius-lg" role="alert">
            <span class="alert-icon align-middle me-2">
                <i class="fas fa-check-circle"></i>
            </span>
            <span class="alert-text">
                <?= esc(session()->getFlashdata('success')) ?>
            </span>
            <button type="button" class="btn-close text-white" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show text-white bg-gradient-danger shadow border-radius-lg" role="alert">
            <span class="alert-icon align-middle me-2">
                <i class="fas fa-exclamation-circle"></i>
            </span>
            <span class="alert-text">
                <?= esc(session()->getFlashdata('error')) ?>
            </span>
            <button type="button" class="btn-close text-white" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-gradient-info border-radius-xl shadow-lg">
                <div class="card-body p-4">
                    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between">
                        <div class="d-flex align-items-center mb-3 mb-md-0">
                            <div class="icon icon-shape bg-white shadow text-center border-radius-md me-3">
                                <i class="fas fa-calendar-check text-info opacity-10"></i>
                            </div>
                            <div>
                                <h4 class="mb-1 text-white">Kalkulasi Absensi</h4>
                                <p class="mb-0 text-sm text-white-50">
                                    Proses & hitung data kehadiran karyawan berdasarkan rentang tanggal
                                </p>
                            </div>
                        </div>
                        <?php if (!empty($date_from) || !empty($date_to)): ?>
                            <div class="text-sm text-white-50">
                                <span class="d-block">
                                    <i class="fas fa-calendar-alt me-1"></i>
                                    Periode:
                                    <strong class="text-white">
                                        <?= esc($date_from ?? '-') ?><?= !empty($date_to) ? ' s/d ' . esc($date_to) : '' ?>
                                    </strong>
                                </span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Date Range Selection Form -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-radius-xl shadow-sm">
                <div class="card-header pb-0">
                    <h6 class="mb-0">
                        <i class="fas fa-filter text-info me-2"></i>
                        Filter Periode Absensi
                    </h6>
                    <p class="text-sm text-muted mb-0">
                        Pilih tanggal untuk memproses kalkulasi kehadiran
                    </p>
                </div>
                <div class="card-body pt-3 pb-4">
                    <form method="post" action="<?= base_url($role . '/attendance/promote'); ?>">
                        <?= csrf_field(); ?>

                        <div class="row g-3 align-items-end">
                            <div class="col-md-5">
                                <label class="form-label text-sm text-uppercase text-secondary font-weight-bolder">
                                    Tanggal Awal
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-calendar-day text-info"></i>
                                    </span>
                                    <input
                                        type="date"
                                        name="date_from"
                                        class="form-control"
                                        required
                                        value="<?= esc($date_from ?? '') ?>">
                                </div>
                                <small class="text-xs text-danger d-block mt-1">
                                    *
                                    Tanggal awal wajib diisi
                                </small>
                            </div>

                            <div class="col-md-5">
                                <label class="form-label text-sm text-uppercase text-secondary font-weight-bolder">
                                    Tanggal Akhir
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-calendar-day text-info"></i>
                                    </span>
                                    <input
                                        type="date"
                                        name="date_to"
                                        class="form-control"
                                        value="<?= esc($date_to ?? '') ?>">
                                </div>
                                <small class="text-xs text-muted d-block mt-1">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Kosongkan jika hanya memproses 1 hari
                                </small>
                            </div>

                            <div class="col-md-2">
                                <button type="submit" class="btn bg-gradient-info w-100 mb-0">
                                    <i class="fas fa-calculator me-2"></i> Proses
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Processing Summary -->
    <?php if (isset($processed) && $processed !== null): ?>
        <div class="row mb-4">
            <div class="col-md-6 mb-3 mb-md-0">
                <div class="card border-radius-xl shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="icon icon-shape bg-gradient-info shadow text-center border-radius-md">
                                <i class="fas fa-calendar-check text-white"></i>
                            </div>
                            <div class="ms-3">
                                <p class="text-sm text-uppercase text-secondary mb-1 font-weight-bolder">
                                    Data Absensi Diproses
                                </p>
                                <h3 class="mb-0 font-weight-bolder">
                                    <?= esc($processed) ?>
                                </h3>
                                <p class="text-xs text-muted mb-0">
                                    <?= esc($date_from) ?><?= !empty($date_to) ? ' s/d ' . esc($date_to) : '' ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card border-radius-xl shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="icon icon-shape bg-gradient-success shadow text-center border-radius-md">
                                <i class="fas fa-chart-line text-white"></i>
                            </div>
                            <div class="ms-3">
                                <p class="text-sm text-uppercase text-secondary mb-1 font-weight-bolder">
                                    Hasil Kalkulasi
                                </p>
                                <h3 class="mb-0 font-weight-bolder">
                                    <?= esc($resProcessed ?? 0) ?>
                                </h3>
                                <p class="text-xs text-muted mb-0">
                                    Record berhasil dihitung
                                </p>
                            </div>
                            <?php if (($processed ?? 0) > 0): ?>
                                <div class="ms-auto text-end">
                                    <span class="badge bg-gradient-light text-dark text-xxs">
                                        <i class="fas fa-percentage me-1 text-success"></i>
                                        <?= number_format((($resProcessed ?? 0) / max($processed, 1)) * 100, 1) ?>% sukses
                                    </span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Attendance Data Table -->
    <?php if (!empty($days)): ?>
        <div class="card border-radius-xl shadow-sm">
            <div class="card-header pb-0">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0">
                            <i class="fas fa-table text-info me-2"></i>
                            Detail Data Absensi
                        </h6>
                        <p class="text-xs text-muted mb-0">
                            Menampilkan data hasil kalkulasi dalam rentang tanggal terpilih
                        </p>
                    </div>
                    <div class="text-end">
                        <span class="badge bg-gradient-info">
                            <?= count($days) ?> Records
                        </span>
                    </div>
                </div>
            </div>
            <div class="card-body px-0 pt-0 pb-2">
                <div class="table-responsive">
                    <table class="table align-items-center mb-0 table-soft">
                        <thead>
                            <tr>
                                <th class="text-center">#</th>
                                <th>Tanggal</th>
                                <th>NIK</th>
                                <th>Nama Karyawan</th>
                                <th>Shift</th>
                                <th class="text-center">Masuk</th>
                                <th class="text-center">Istirahat</th>
                                <th class="text-center">Kembali</th>
                                <th class="text-center">Pulang</th>
                                <th class="text-end">Kerja</th>
                                <th class="text-end">Break</th>
                                <th class="text-end">Telat</th>
                                <th class="text-end">P.Cepat</th>
                                <th class="text-end">Lembur</th>
                                <th class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; ?>
                            <?php foreach ($days as $row): ?>
                                <tr>
                                    <td class="text-center text-xs text-secondary">
                                        <?= $no++ ?>
                                    </td>
                                    <td class="text-sm">
                                        <span class="d-flex align-items-center">
                                            <i class="fas fa-calendar-alt text-muted me-2"></i>
                                            <?= esc($row['work_date']) ?>
                                        </span>
                                    </td>
                                    <td class="text-sm font-weight-bolder">
                                        <?= esc($row['nik'] ?? '-') ?>
                                    </td>
                                    <td class="text-sm">
                                        <?= esc($row['employee_name'] ?? '-') ?>
                                    </td>
                                    <td class="text-sm">
                                        <span class="badge bg-gradient-secondary">
                                            <?= esc($row['shift_name'] ?? ($row['id_shift'] ?? '-')) ?>
                                        </span>
                                    </td>
                                    <td class="text-center text-xs">
                                        <span class="time-badge">
                                            <?= esc($row['in_time'] ?? '-') ?>
                                        </span>
                                    </td>
                                    <td class="text-center text-xs">
                                        <span class="time-badge">
                                            <?= esc($row['break_out_time'] ?? '-') ?>
                                        </span>
                                    </td>
                                    <td class="text-center text-xs">
                                        <span class="time-badge">
                                            <?= esc($row['break_in_time'] ?? '-') ?>
                                        </span>
                                    </td>
                                    <td class="text-center text-xs">
                                        <span class="time-badge">
                                            <?= esc($row['out_time'] ?? '-') ?>
                                        </span>
                                    </td>
                                    <td class="text-end text-xs metric-positive">
                                        <?= esc($row['total_work_min'] ?? 0) ?> <small>m</small>
                                    </td>
                                    <td class="text-end text-xs metric-neutral">
                                        <?= esc($row['total_break_min'] ?? 0) ?> <small>m</small>
                                    </td>
                                    <td class="text-end text-xs metric-negative">
                                        <?php if (($row['late_min'] ?? 0) > 0): ?>
                                            <i class="fas fa-arrow-up me-1"></i>
                                        <?php endif; ?>
                                        <?= esc($row['late_min'] ?? 0) ?> <small>m</small>
                                    </td>
                                    <td class="text-end text-xs metric-negative">
                                        <?php if (($row['early_leave_min'] ?? 0) > 0): ?>
                                            <i class="fas fa-arrow-up me-1"></i>
                                        <?php endif; ?>
                                        <?= esc($row['early_leave_min'] ?? 0) ?> <small>m</small>
                                    </td>
                                    <td class="text-end text-xs metric-positive">
                                        <?php if (($row['overtime_min'] ?? 0) > 0): ?>
                                            <i class="fas fa-plus-circle me-1"></i>
                                        <?php endif; ?>
                                        <?= esc($row['overtime_min'] ?? 0) ?> <small>m</small>
                                    </td>
                                    <td class="text-center text-xs">
                                        <?php
                                        $status    = $row['status_code'] ?? '-';
                                        $badgeClass = 'bg-gradient-secondary';
                                        if ($status === 'H') $badgeClass = 'bg-gradient-success';
                                        elseif ($status === 'A') $badgeClass = 'bg-gradient-danger';
                                        elseif ($status === 'L') $badgeClass = 'bg-gradient-warning';
                                        ?>
                                        <span class="badge <?= $badgeClass ?> px-3">
                                            <?= esc($status) ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer d-flex justify-content-between align-items-center pt-0">
                <p class="text-xs text-muted mb-0">
                    <i class="fas fa-info-circle me-1"></i>
                    Menampilkan <strong><?= count($days) ?></strong> data absensi dalam rentang tanggal yang diproses
                </p>
            </div>
        </div>
    <?php elseif (isset($processed) && $processed === 0): ?>
        <div class="card border-radius-xl shadow-sm">
            <div class="card-body text-center py-5">
                <div class="icon icon-shape bg-gradient-warning shadow text-center border-radius-md mb-3">
                    <i class="fas fa-exclamation-triangle text-white"></i>
                </div>
                <h5 class="text-warning mb-2">Tidak Ada Data</h5>
                <p class="text-sm text-muted mb-0">
                    Tidak ada data absensi yang terbentuk pada rentang tanggal tersebut.<br>
                    <small>Mungkin semua group log ≤ 3, atau tidak ada log absensi.</small>
                </p>
            </div>
        </div>
    <?php endif; ?>

</div>

<?= $this->endSection(); ?>