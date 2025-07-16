<?php $this->extend('layout/template'); ?>
<?php $this->section('content'); ?>

<div class="container-fluid py-4">
    <!-- Statistik Cards -->
    <div class="row">
        <!-- Card Total Karyawan -->
        <div class="col-xl-4 col-sm-6 mb-xl-0 mb-4 mt-2">
            <a href="#">
                <div class="card">
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-8">
                                <div class="numbers">
                                    <p class="text-sm mb-0 text-capitalize font-weight-bold">Total Karyawan</p>
                                    <h5 class="font-weight-bolder mb-0"><?= $TtlKaryawan ?> Orang</h5>
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
            </a>
        </div>

        <!-- Card Perpindahan Bulan Ini -->
        <div class="col-xl-4 col-sm-6 mb-xl-0 mb-4 mt-2">
            <a href="<?= base_url($role . '/historyPindahKaryawan'); ?>">
                <div class="card">
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-8">
                                <div class="numbers">
                                    <p class="text-sm mb-0 text-capitalize font-weight-bold">Perpindahan Bulan Ini</p>
                                    <h5 class="font-weight-bolder mb-0"><?= number_format($PerpindahanBulanIni) ?> Orang</h5>
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
            </a>
        </div>

        <!-- Card Rata-rata Grade -->
        <div class="col-xl-4 col-sm-6 mb-xl-0 mb-4 mt-2">
            <a href="#">
                <div class="card">
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-8">
                                <div class="numbers">
                                    <p class="text-sm mb-0 text-capitalize font-weight-bold">Rata-rata Grade</p>
                                    <h5 class="font-weight-bolder mb-0"><?= number_format($RatarataGrade, 2) ?></h5>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-gradient-warning shadow text-center border-radius-md">
                                    <i class="fas fa-star text-lg opacity-10"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <!-- Card Skill Gap
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4 mt-2">
            <a href="#">
                <div class="card">
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-8">
                                <div class="numbers">
                                    <p class="text-sm mb-0 text-capitalize font-weight-bold">Skill Gap</p>
                                    <h5 class="font-weight-bolder mb-0"><?= $SkillGap ?></h5>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-gradient-danger shadow text-center border-radius-md">
                                    <i class="fas fa-chart-line text-lg opacity-10"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div> -->
    </div>
    <?php
    // Di controller (atau di view sebelum di‐encode), kita pisah dengan \n.
    // Misal: [
    // 'ADMINISTRASI & KEUANGAN', 
    // 'OPERATOR PRODUKSI LINE 1', ...
    // ]
    $labelsMulti = array_map(function ($txt) {
        // Ganti spasi setelah kata ke-2 jadi newline
        $words = explode(' ', $txt);
        if (count($words) > 2) {
            return $words[0] . ' ' . $words[1] . "\n" . implode(' ', array_slice($words, 2));
        }
        return $txt;
    }, $labels);
    ?>
    <!-- Grafik Data -->
    <div class="row mt-4">
        <div class="col-6 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="mb-3">Total Karyawan Berdasarkan Bagian
                            <br>
                            <font style="font-size: small; color: darkgray;">(Klik dan drag untuk zoom, scroll untuk zoom in/out)</font>
                        </h6>
                        <button class="btn btn-sm bg-gradient-info text-white" onclick="chartKaryawan.resetZoom()">
                            Reset Zoom
                        </button>
                    </div>


                    <div style="width: 100%;">
                        <canvas id="chartKaryawan" style="width: 100%; height: 400px;"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 mb-4">
            <div class="card">
                <div class="card-body">
                    <!-- Bagian Perpindahan Karyawan per Tanggal (Line Chart) -->
                    <div style="width: 100%;">
                        <h6 class="text-center mb-3">Perpindahan Karyawan Bulan Ini</h6>
                        <canvas id="lineChartPindah" style="width: 100%; height: 400px;"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="container-fluid py-4">
    <!-- Header Monitoring -->
    <div class="card mb-4">
        <div class="card-body">
            <h2 class="card-title h4 mb-0">
                Monitoring Penilaian Karyawan Periode
                <?= esc($current_periode) ?>
                (
                <?= ($start_date && strtotime($start_date)) ? date_format(date_create($start_date), 'd M Y') : '-' ?>
                -
                <?= ($end_date && strtotime($end_date)) ? date_format(date_create($end_date), 'd M Y') : '-' ?>
                )
            </h2>

        </div>
    </div>

    <!-- Card Monitoring -->
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
        <?php if (!empty($cekPenilaian)) : ?>
            <?php foreach ($cekPenilaian as $mandor) : ?>
                <?php
                // Lewati jika tidak ada karyawan
                if ($mandor['total_karyawan'] == 0) {
                    continue;
                }
                // Hanya tampilkan data untuk periode saat ini
                // if ($mandor['id_periode'] != $id_periode) {
                //     continue;
                // }
                // Hitung persentase penilaian dengan benar
                $progress = round(($mandor['total_assessment'] / $mandor['total_karyawan']) * 100);
                $isComplete = $progress >= 100;
                ?>
                <div class="col">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="card-title"><?= esc($mandor['area']); ?></h5>
                                <!-- Tombol modal -->
                                <button type="button" class="btn btn-sm <?= $isComplete ? 'btn-success' : 'btn-danger' ?>"
                                    data-bs-toggle="modal" data-bs-target="#modalEmployeeEvaluation" data-area="<?= esc($mandor['area']); ?>">
                                    <?= $isComplete ? 'Selesai' : 'Belum Selesai'; ?>
                                </button>
                            </div>

                            <div class="d-flex justify-content-between mb-2">
                                <small class="text-muted">Karyawan: <?= esc($mandor['total_karyawan']); ?></small>
                                <small class="text-muted">Dinilai: <?= esc($mandor['total_assessment']); ?></small>
                            </div>
                            <div class="progress mb-3" style="height: 20px;">
                                <div class="progress-bar <?= $isComplete ? 'bg-success' : 'bg-info' ?>"
                                    role="progressbar"
                                    style="width: <?= $progress; ?>%; height: 20px;"
                                    aria-valuenow="<?= $progress; ?>"
                                    aria-valuemin="0"
                                    aria-valuemax="100">
                                    <?= $progress; ?>%
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else : ?>
            <div class="col-12">
                <div class="alert alert-warning text-center" role="alert">
                    Silahkan tambahkan data periode terlebih dahulu.
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal untuk menampilkan data karyawan yang belum dinilai -->
<div class="modal fade" id="modalEmployeeEvaluation" tabindex="-1" aria-labelledby="modalEmployeeEvaluationLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEmployeeEvaluationLabel">Karyawan Belum Dinilai</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-bordered w-100">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Kode Kartu</th>
                                <th>Nama Karyawan</th>
                                <th>Shift</th>
                                <th>Bagian</th>
                                <th>Area</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="employeeEvaluationBody">
                            <!-- Data akan dimuat secara dinamis -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Library Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-zoom@2.0.1/dist/chartjs-plugin-zoom.min.js"></script>

<script>
    const ctx = document.getElementById('chartKaryawan').getContext('2d');

    // Make chartKaryawan global so the button can access it
    window.chartKaryawan = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?= json_encode($labels) ?>,
            datasets: [{
                label: 'Jumlah Karyawan per Bagian',
                data: <?= json_encode($values) ?>,
                backgroundColor: 'rgba(75, 192, 192, 0.6)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                x: {
                    ticks: {
                        autoSkip: false, // tampilkan semua label
                        maxRotation: 90,
                        minRotation: 45
                    }
                },
                y: {
                    beginAtZero: true
                }
            },
            plugins: {
                zoom: {
                    pan: {
                        enabled: true,
                        mode: 'x'
                    },
                    zoom: {
                        wheel: {
                            enabled: true
                        },
                        pinch: {
                            enabled: true
                        },
                        mode: 'x'
                    }
                },
                tooltip: {
                    enabled: true
                },
                legend: {
                    display: false
                }
            }
        }
    });
</script>

<script>
    // ===========================
    // 2. Line Chart (Perpindahan Karyawan per Tanggal)
    // ===========================
    const ctxLine = document.getElementById('lineChartPindah').getContext('2d');

    // Data history perpindahan sudah di-encode di controller: $labelsKar dan $valuesKar
    const labelsLine = <?= json_encode($labelsKar) ?>; // Array: ['2025-06-01', '2025-06-02', ...]
    const valuesLine = <?= json_encode($valuesKar) ?>; // Array: [2, 5, 0, 3, ...]

    new Chart(ctxLine, {
        type: 'line',
        data: {
            labels: labelsLine,
            datasets: [{
                label: 'Jumlah Perpindahan',
                data: valuesLine,
                fill: false,
                tension: 0.3, // kurva halus
                pointBackgroundColor: 'rgba(54, 162, 235, 1)',
                borderColor: 'rgba(54, 162, 235, 0.8)',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: true
                },
                tooltip: {
                    enabled: true
                }
            },
            scales: {
                x: {
                    display: true,
                    title: {
                        display: true,
                        text: 'Tanggal'
                    }
                },
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Jumlah'
                    }
                }
            }
        }
    });
</script>


<script>
    document.addEventListener("DOMContentLoaded", function() {
        var modalElement = document.getElementById('modalEmployeeEvaluation');

        modalElement.addEventListener('show.bs.modal', function(event) {
            // Gunakan current_periode dari PHP
            var id_periode = '<?= $id_periode ?>';
            var area = event.relatedTarget.getAttribute('data-area');

            fetch("<?= base_url('Monitoring/evaluasiKaryawan') ?>/" + id_periode + "/" + area)
                .then(response => response.json())
                .then(data => {
                    var belumDinilai = data.filter(emp => emp.status === "Belum Dinilai");
                    var tbody = document.getElementById("employeeEvaluationBody");
                    tbody.innerHTML = "";

                    if (belumDinilai.length > 0) {
                        belumDinilai.forEach(function(emp) {
                            var tr = document.createElement("tr");
                            tr.innerHTML =
                                "<td>" + (tbody.rows.length + 1) + "</td>" +
                                "<td>" + emp.employee_code + "</td>" +
                                "<td>" + emp.employee_name + "</td>" +
                                "<td>" + emp.shift + "</td>" +
                                "<td>" + emp.job_section_name + "</td>" +
                                "<td>" + emp.factory_name + "</td>" +
                                "<td><span class='badge bg-danger'>Belum Dinilai</span></td>";
                            tbody.appendChild(tr);
                        });
                    } else {
                        tbody.innerHTML = "<tr><td colspan='7' class='text-center'>Karyawan Sudah Dinilai Semua.</td></tr>";
                    }
                })
                .catch(error => {
                    console.error("Error fetching data:", error);
                });
        });
    });
</script>

<?php $this->endSection(); ?>