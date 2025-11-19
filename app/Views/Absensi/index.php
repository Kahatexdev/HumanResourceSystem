<?php $this->extend('layout/template'); ?>
<?php $this->section('content'); ?>

<style>
    .card {
        border-radius: 1rem;
        border: none;
    }

    .card-header {
        border-radius: 1rem 1rem 0 0;
        border-bottom: none;
        background: linear-gradient(135deg, #5e72e4 0%, #825ee4 100%);
        padding: 0.75rem 1.25rem;
    }

    .card-header h6 {
        margin-bottom: 0;
        font-size: 0.95rem;
        letter-spacing: 0.04em;
        text-transform: uppercase;
    }

    /* .icon-shape {
        width: 48px;
        height: 48px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 0.75rem;
    } */

    .border-radius-md {
        border-radius: 0.75rem;
    }

    .badge-status {
        display: inline-block;
        padding: 0.25rem 0.75rem;
        border-radius: 999px;
        font-size: 0.70rem;
        font-weight: 600;
        letter-spacing: 0.03em;
        text-transform: uppercase;
    }

    .badge-hadir {
        background-color: #2dce891a;
        color: #2dce89;
    }

    .badge-izin {
        background-color: #11cdef1a;
        color: #11cdef;
    }

    .badge-sakit {
        background-color: #fb63401a;
        color: #fb6340;
    }

    .badge-alpha {
        background-color: #f5365c1a;
        color: #f5365c;
    }

    .chart-container {
        position: relative;
        min-height: 260px;
    }

    .table thead th {
        font-size: 0.70rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .table tbody td {
        vertical-align: middle;
    }
</style>

<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 font-weight-bolder text-dark mb-1">Dashboard Absensi</h1>
            <p class="text-muted mb-0">Selamat datang di dashboard sistem absensi karyawan</p>
        </div>
    </div>

    <!-- Summary Statistics Row -->
    <div class="row mb-4">
        <!-- Hadir Hari Ini -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-body position-relative">
                    <div class="row align-items-center">
                        <div class="col-8">
                            <p class="text-sm mb-2 text-uppercase font-weight-bold text-muted">Hadir Hari Ini</p>
                            <h3 class="font-weight-bolder mb-0">
                                <?= number_format($HadirHariIni ?? 0) ?>
                            </h3>
                            <p class="text-xs text-muted mb-0">
                                dari <?= number_format($TtlKaryawan ?? 0) ?> karyawan
                            </p>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon-shape bg-gradient-info shadow-sm border-radius-md">
                                <i class="fas fa-check-circle text-white text-lg"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Izin -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-body position-relative">
                    <div class="row align-items-center">
                        <div class="col-8">
                            <p class="text-sm mb-2 text-uppercase font-weight-bold text-muted">Izin</p>
                            <h3 class="font-weight-bolder mb-0">
                                <?= number_format($IzinHariIni ?? 0) ?>
                            </h3>
                            <p class="text-xs text-muted mb-0">Hari ini</p>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon-shape bg-gradient-warning shadow-sm border-radius-md">
                                <i class="fas fa-clipboard text-white text-lg"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sakit -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-body position-relative">
                    <div class="row align-items-center">
                        <div class="col-8">
                            <p class="text-sm mb-2 text-uppercase font-weight-bold text-muted">Sakit</p>
                            <h3 class="font-weight-bolder mb-0">
                                <?= number_format($SakitHariIni ?? 0) ?>
                            </h3>
                            <p class="text-xs text-muted mb-0">Hari ini</p>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon-shape bg-gradient-danger shadow-sm border-radius-md">
                                <i class="fas fa-hospital-alt text-white text-lg"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Alpa/Alpha -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-body position-relative">
                    <div class="row align-items-center">
                        <div class="col-8">
                            <p class="text-sm mb-2 text-uppercase font-weight-bold text-muted">Alpa</p>
                            <h3 class="font-weight-bolder mb-0">
                                <?= number_format($AlpaHariIni ?? 0) ?>
                            </h3>
                            <p class="text-xs text-muted mb-0">Hari ini</p>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon-shape bg-gradient-success shadow-sm border-radius-md">
                                <i class="fas fa-exclamation-circle text-white text-lg"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row mb-4">
        <!-- Statistik Absensi Minggu Ini -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h6 class="text-white font-weight-bolder">Statistik Absensi Minggu Ini</h6>
                </div>
                <div class="card-body p-3">
                    <div class="chart-container">
                        <canvas id="chartAbsensiMinggu"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Persentase Status Absensi -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h6 class="text-white font-weight-bolder">Persentase Status Absensi</h6>
                </div>
                <div class="card-body p-3">
                    <div class="chart-container">
                        <canvas id="chartPersentase"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Records -->
    <div class="row">
        <!-- Absensi Karyawan Hari Ini -->
        <div class="col-12 mb-4">
            <div class="card shadow-sm">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="text-white font-weight-bolder mb-0">Absensi Karyawan Hari Ini</h6>
                        <a href="<?= base_url('absensi/detail') ?>" class="btn btn-sm btn-light">
                            Lihat Semua
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-flush table-hover mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th>No</th>
                                    <th>Nama Karyawan</th>
                                    <th>NIK</th>
                                    <th>Departemen</th>
                                    <th>Jam Masuk</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($AbsensiHariIni)): ?>
                                    <?php $no = 1;
                                    foreach ($AbsensiHariIni as $item): ?>
                                        <tr>
                                            <td class="text-sm"><?= $no++ ?></td>
                                            <td class="text-sm"><?= esc($item['nama_karyawan']) ?></td>
                                            <td class="text-sm"><?= esc($item['nik']) ?></td>
                                            <td class="text-sm"><?= esc($item['departemen']) ?></td>
                                            <td class="text-sm"><?= esc($item['jam_masuk'] ?? '-') ?></td>
                                            <td>
                                                <?php
                                                $status = $item['status'] ?? 'Belum Absen';
                                                $badgeClass = 'badge-status ';
                                                if ($status === 'Hadir') {
                                                    $badgeClass .= 'badge-hadir';
                                                } elseif ($status === 'Izin') {
                                                    $badgeClass .= 'badge-izin';
                                                } elseif ($status === 'Sakit') {
                                                    $badgeClass .= 'badge-sakit';
                                                } else {
                                                    $badgeClass .= 'badge-alpha';
                                                }
                                                ?>
                                                <span class="<?= $badgeClass ?>"><?= esc($status) ?></span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">
                                            Tidak ada data absensi hari ini
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Stats -->
    <div class="row">
        <!-- Karyawan dengan Ketidakhadiran Tinggi -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h6 class="text-white font-weight-bolder">Top 5 Ketidakhadiran Bulan Ini</h6>
                </div>
                <div class="card-body p-3">
                    <div class="table-responsive">
                        <table class="table table-sm table-flush mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th>Nama</th>
                                    <th class="text-end">Alpa</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($TopKetidakhadiran)): ?>
                                    <?php foreach ($TopKetidakhadiran as $item): ?>
                                        <tr>
                                            <td class="text-sm"><?= esc($item['nama_karyawan']) ?></td>
                                            <td class="text-sm text-end text-danger font-weight-bold">
                                                <?= number_format($item['total_alpa'] ?? 0) ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="2" class="text-center text-muted py-3">Tidak ada data</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Ringkasan Bulanan -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h6 class="text-white font-weight-bolder">Ringkasan Absensi Bulan Ini</h6>
                </div>
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-6 mb-3">
                            <p class="text-muted text-sm mb-2">Total Hadir</p>
                            <h5 class="font-weight-bolder text-success mb-0">
                                <?= number_format($TotalHadirBulan ?? 0) ?>
                            </h5>
                        </div>
                        <div class="col-6 mb-3">
                            <p class="text-muted text-sm mb-2">Total Izin</p>
                            <h5 class="font-weight-bolder text-info mb-0">
                                <?= number_format($TotalIzinBulan ?? 0) ?>
                            </h5>
                        </div>
                        <div class="col-6">
                            <p class="text-muted text-sm mb-2">Total Sakit</p>
                            <h5 class="font-weight-bolder text-warning mb-0">
                                <?= number_format($TotalSakitBulan ?? 0) ?>
                            </h5>
                        </div>
                        <div class="col-6">
                            <p class="text-muted text-sm mb-2">Total Alpa</p>
                            <h5 class="font-weight-bolder text-danger mb-0">
                                <?= number_format($TotalAlpaBulan ?? 0) ?>
                            </h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> <!-- /Additional Stats -->
</div>

<!-- Charts Script -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
    // Chart - Statistik Absensi Minggu Ini
    const ctx1 = document.getElementById('chartAbsensiMinggu').getContext('2d');
    new Chart(ctx1, {
        type: 'bar',
        data: {
            labels: ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'],
            datasets: [{
                    label: 'Hadir',
                    data: [<?= implode(',', $DataHadirMinggu ?? []) ?>],
                    backgroundColor: '#2dce89',
                    borderRadius: 5,
                },
                {
                    label: 'Izin',
                    data: [<?= implode(',', $DataIzinMinggu ?? []) ?>],
                    backgroundColor: '#11cdef',
                    borderRadius: 5,
                },
                {
                    label: 'Sakit',
                    data: [<?= implode(',', $DataSakitMinggu ?? []) ?>],
                    backgroundColor: '#fb6340',
                    borderRadius: 5,
                },
                {
                    label: 'Alpa',
                    data: [<?= implode(',', $DataAlpaMinggu ?? []) ?>],
                    backgroundColor: '#f5365c',
                    borderRadius: 5,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });

    // Chart - Persentase Status Absensi
    const ctx2 = document.getElementById('chartPersentase').getContext('2d');
    new Chart(ctx2, {
        type: 'doughnut',
        data: {
            labels: ['Hadir', 'Izin', 'Sakit', 'Alpa'],
            datasets: [{
                data: [
                    <?= $TotalHadirBulan ?? 0 ?>,
                    <?= $TotalIzinBulan ?? 0 ?>,
                    <?= $TotalSakitBulan ?? 0 ?>,
                    <?= $TotalAlpaBulan ?? 0 ?>
                ],
                backgroundColor: [
                    '#2dce89',
                    '#11cdef',
                    '#fb6340',
                    '#f5365c'
                ],
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                }
            }
        }
    });
</script>

<?php $this->endSection(); ?>