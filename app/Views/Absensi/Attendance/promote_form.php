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
    <!-- Attendance Data Table -->
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
                    <span class="badge bg-gradient-info" id="js-record-count">
                        0 Records
                    </span>
                </div>
            </div>
        </div>
        <div class="card-body px-0 pt-0 pb-2">
            <div class="table-responsive">
                <table
                    class="table align-items-center mb-0 table-soft js-promote-table"
                    style="width: 100%;">
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
                        <!-- Kosong, diisi DataTables server-side -->
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer d-flex justify-content-between align-items-center pt-0">
            <p class="text-xs text-muted mb-0">
                <i class="fas fa-info-circle me-1"></i>
                Menampilkan data absensi berdasarkan rentang tanggal yang diproses
            </p>
        </div>
    </div>

    <?php if (isset($processed) && $processed === 0): ?>
        <div class="card border-radius-xl shadow-sm mt-3">
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

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tableEl = document.querySelector('.js-promote-table');
        if (!tableEl) return;

        const dateFrom = <?= json_encode($date_from ?? '') ?>;
        const dateTo = <?= json_encode($date_to ?? '') ?>;
        const role = <?= json_encode($role ?? '') ?>;

        // Kalau belum ada tanggal, jangan init DataTables dulu
        if (!dateFrom) return;

        const ajaxUrl = '<?= base_url() ?>/' + role + '/attendance/promote-data' +
            '?date_from=' + encodeURIComponent(dateFrom) +
            '&date_to=' + encodeURIComponent(dateTo);

        const dt = $(tableEl).DataTable({
            processing: true,
            serverSide: true,
            searching: true,
            ordering: true,
            pageLength: 25,
            lengthMenu: [10, 25, 50, 100],
            ajax: {
                url: ajaxUrl,
                type: 'GET'
            },
            order: [
                [1, 'asc']
            ],
            columns: [{
                    data: 0,
                    orderable: false,
                    searchable: false,
                    className: 'text-center'
                },
                {
                    data: 1
                },
                {
                    data: 2
                },
                {
                    data: 3
                },
                {
                    data: 4
                },
                {
                    data: 5,
                    className: 'text-center'
                },
                {
                    data: 6,
                    className: 'text-center'
                },
                {
                    data: 7,
                    className: 'text-center'
                },
                {
                    data: 8,
                    className: 'text-center'
                },
                {
                    data: 9,
                    className: 'text-end'
                },
                {
                    data: 10,
                    className: 'text-end'
                },
                {
                    data: 11,
                    className: 'text-end'
                },
                {
                    data: 12,
                    className: 'text-end'
                },
                {
                    data: 13,
                    className: 'text-end'
                },
                {
                    data: 14,
                    className: 'text-center'
                },
            ],
            drawCallback: function(settings) {
                const info = this.api().page.info();
                const total = info.recordsDisplay;
                const badge = document.getElementById('js-record-count');
                if (badge) {
                    badge.textContent = total + ' Records';
                }
            }
        });
    });
</script>


<?= $this->endSection(); ?>