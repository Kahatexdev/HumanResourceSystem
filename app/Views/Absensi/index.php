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
                                    <?= number_format($TtlKaryawan) ?> <span class="text-sm font-weight-normal">Orang</span>
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
                                    <?= number_format($PerpindahanBulanIni) ?> <span class="text-sm font-weight-normal">Orang</span>
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

    <!-- Charts Row -->
    <div class="row mt-2">
        <?php
        $rgbColors = [
            ['rgba(255, 99, 132, 0.8)', 'rgb(255, 99, 132)'],
            ['rgba(54, 162, 235, 0.8)', 'rgb(54, 162, 235)'],
            ['rgba(255, 206, 86, 0.8)', 'rgb(255, 206, 86)'],
            ['rgba(75, 192, 192, 0.8)', 'rgb(75, 192, 192)'],
            ['rgba(153, 102, 255, 0.8)', 'rgb(153, 102, 255)'],
            ['rgba(255, 159, 64, 0.8)', 'rgb(255, 159, 64)']
        ];

        $colorIndex = 0;
        $chartCount = 0;

        foreach ($groupedData as $areaUtama => $items):
            if ($items[0]['job_section_name'] == '-') {
                continue;
            }

            $colorPair = $rgbColors[$colorIndex % count($rgbColors)];
            $backgroundColor = $colorPair[0];
            $borderColor = $colorPair[1];
            $colorIndex++;

            $labels = [];
            $values = [];
            foreach ($items as $item) {
                $labels[] = $item['job_section_name'] . ' (' . $item['factory_name'] . ')';
                $values[] = (int)$item['jumlah_karyawan'];
            }
            $chartCount++;
        ?>
            <div class="col-lg-6 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-bottom-0 pb-0">
                        <div class="d-flex align-items-center">
                            <div class="icon icon-shape-area shadow-sm text-white rounded-circle me-3" style="background-color: <?= $borderColor ?>;">
                                <i class="fas fa-building"></i>
                            </div>
                            <h5 class="mb-0" style="color: #6c757d;"><?= esc($areaUtama) ?></h5>
                        </div>
                        <p class="text-sm text-muted mt-2">Total: <?= array_sum($values) ?> karyawan</p>
                    </div>
                    <div class="card-body pt-2">
                        <div style="height: 280px;">
                            <canvas id="chart<?= md5($areaUtama) ?>"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    var ctx_<?= md5($areaUtama) ?> = document.getElementById('chart<?= md5($areaUtama) ?>').getContext('2d');
                    var chart<?= md5($areaUtama) ?> = new Chart(ctx_<?= md5($areaUtama) ?>, {
                        type: 'bar',
                        data: {
                            labels: <?= json_encode($labels) ?>,
                            datasets: [{
                                label: 'Jumlah Karyawan',
                                data: <?= json_encode($values) ?>,
                                backgroundColor: '<?= $backgroundColor ?>',
                                borderColor: '<?= $borderColor ?>',
                                borderWidth: 1,
                                borderRadius: 4,
                                maxBarThickness: 40
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: false
                                },
                                tooltip: {
                                    backgroundColor: 'rgba(108, 117, 125, 0.8)',
                                    padding: 10,
                                    bodyFont: {
                                        size: 13
                                    },
                                    titleFont: {
                                        size: 14,
                                        weight: 'bold'
                                    },
                                    callbacks: {
                                        label: function(context) {
                                            return context.parsed.y + ' karyawan';
                                        }
                                    }
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        precision: 0
                                    },
                                    grid: {
                                        display: true,
                                        drawBorder: false,
                                        color: 'rgba(0,0,0,0.05)'
                                    },
                                    title: {
                                        display: true,
                                        text: 'Jumlah Karyawan',
                                        color: '#666',
                                        font: {
                                            size: 12
                                        }
                                    }
                                },
                                x: {
                                    grid: {
                                        display: false,
                                        drawBorder: false
                                    },
                                    ticks: {
                                        maxRotation: 45,
                                        minRotation: 45
                                    }
                                }
                            }
                        }
                    });
                });
            </script>
        <?php endforeach; ?>

        <?php if ($chartCount % 2 != 0): ?>
            <!-- Jika jumlah grafik ganjil, tambahkan elemen khusus -->
            <div class="col-lg-6 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-bottom-0 pb-0">
                        <canvas id="pindahanBarChart" style="width: 100%; height: 300px;"></canvas>
                    </div>
                </div>
            </div>

            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    var ctxBar2 = document.getElementById('pindahanBarChart').getContext('2d');
                    var pindahanBarChart = new Chart(ctxBar2, {
                        type: 'bar',
                        data: {
                            labels: <?= json_encode($labelsKar) ?>,
                            datasets: [{
                                label: 'Jumlah Pindahan',
                                data: <?= json_encode($valuesKar) ?>,
                                backgroundColor: 'rgba(255, 159, 64, 0.6)',
                                borderColor: 'rgba(255, 159, 64, 1)',
                                borderWidth: 1,
                                barPercentage: 0.6,
                                categoryPercentage: 0.6
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                x: {
                                    title: {
                                        display: true,
                                        text: 'Tanggal'
                                    }
                                },
                                y: {
                                    beginAtZero: true,
                                    title: {
                                        display: true,
                                        text: 'Jumlah Pindahan'
                                    }
                                }
                            },
                            plugins: {
                                title: {
                                    display: true,
                                    text: 'Grafik Pindahan Karyawan'
                                },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            return 'Jumlah: ' + context.parsed.y;
                                        }
                                    }
                                },
                                legend: {
                                    display: true
                                }
                            }
                        }
                    });
                });
            </script>

        <?php endif; ?>
    </div>

</div>


<?php $this->endSection(); ?>