<?php $this->extend('layout/template'); ?>
<?php $this->section('content'); ?>

<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.24/css/dataTables.bootstrap5.min.css">
<style>
    .countdown-container {
        background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
        border-radius: 15px;
        padding: 30px;
        margin: 20px 0;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        position: relative;
        overflow: hidden;
    }

    .countdown-container::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        opacity: 0.3;
    }

    .countdown-content {
        position: relative;
        z-index: 1;
    }

    .countdown-title {
        color: #ffffff;
        font-size: 2.5rem;
        font-weight: bold;
        text-align: center;
        margin-bottom: 10px;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        letter-spacing: 3px;
    }

    .countdown-subtitle {
        color: #f8f9fa;
        font-size: 1.3rem;
        text-align: center;
        margin-bottom: 30px;
        font-weight: 500;
        letter-spacing: 2px;
    }

    .countdown-timer {
        display: flex;
        justify-content: center;
        gap: 20px;
        flex-wrap: wrap;
    }

    .time-unit {
        background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 12px;
        padding: 20px 15px;
        text-align: center;
        min-width: 100px;
        transition: all 0.3s ease;
    }

    .time-unit:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
    }

    .time-number {
        display: block;
        font-size: 2.8rem;
        font-weight: bold;
        color: #ffffff;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
        line-height: 1;
    }

    .time-label {
        font-size: 0.9rem;
        color: #e9ecef;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-top: 5px;
    }

    .alert-info {
        background: rgba(13, 202, 240, 0.1);
        border: 1px solid rgba(13, 202, 240, 0.3);
        color: #0dcaf0;
        border-radius: 10px;
    }

    .alert-warning {
        background: rgba(255, 193, 7, 0.1);
        border: 1px solid rgba(255, 193, 7, 0.3);
        color: #ffc107;
        border-radius: 10px;
    }

    .alert-danger {
        background: rgba(220, 53, 69, 0.1);
        border: 1px solid rgba(220, 53, 69, 0.3);
        color: #dc3545;
        border-radius: 10px;
    }

    .pulse {
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0% {
            opacity: 1;
        }

        50% {
            opacity: 0.7;
        }

        100% {
            opacity: 1;
        }
    }

    .expired {
        background: linear-gradient(135deg, #ff6b6b 0%, #ee5a5a 100%);
    }

    .expired .time-unit {
        background: rgba(255, 255, 255, 0.2);
    }

    @media (max-width: 768px) {
        .countdown-title {
            font-size: 1.8rem;
            letter-spacing: 1px;
        }

        .countdown-subtitle {
            font-size: 1rem;
            letter-spacing: 1px;
        }

        .time-number {
            font-size: 2rem;
        }

        .time-unit {
            min-width: 80px;
            padding: 15px 10px;
        }
    }

    .settings-panel {
        background: #f8f9fa;
        border-radius: 10px;
        padding: 20px;
        margin: 20px 0;
    }

    .btn-custom {
        border-radius: 8px;
        font-weight: 500;
        padding: 8px 16px;
    }
</style>
<div class="container-fluid py-4">
    <?php if (session()->getFlashdata('warning')): ?>
        <div class="alert alert-warning"><?= session()->getFlashdata('warning') ?></div>
    <?php endif; ?>

    <?php if (!empty($noPeriode) && $noPeriode): ?>
        <div class="alert alert-info"><?= esc($periodeMessage ?? 'Tidak ada periode aktif.') ?></div>
    <?php endif; ?>

    <!-- Pengingat Waktu Penilaian -->
    <div id="countdownContainer" class="countdown-container">
        <div class="countdown-content">
            <h1 class="countdown-title">INFORMASI</h1>
            <h2 class="countdown-subtitle">WAKTU PENILAIAN</h2>
            <input type="hidden" id="periodeEndDate" value="<?= esc($periode['end_date'] ?? '') ?>">
            <div class="countdown-timer">
                <div class="time-unit">
                    <span id="days" class="time-number">0</span>
                    <div class="time-label">Hari</div>
                </div>
                <div class="time-unit">
                    <span id="hours" class="time-number">0</span>
                    <div class="time-label">Jam</div>
                </div>
                <div class="time-unit">
                    <span id="minutes" class="time-number">0</span>
                    <div class="time-label">Menit</div>
                </div>
                <div class="time-unit">
                    <span id="seconds" class="time-number pulse">0</span>
                    <div class="time-label">Detik</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert Status -->
    <div id="statusAlert" class="alert alert-info d-flex align-items-center" role="alert">
        <div id="statusMessage"></div>
    </div>

    <!-- Test Alert -->
    <!-- <div class="settings-panel">
        <h5><i class="fas fa-cog me-2"></i>Pengaturan Waktu Penilaian</h5>
        <div class="row">
            <div class="col-md-3">
                <label for="targetDate" class="form-label">Tanggal Target:</label>
                <input type="date" class="form-control" id="targetDate">
            </div>
            <div class="col-md-3">
                <label for="targetTime" class="form-label">Waktu Target:</label>
                <input type="time" class="form-control" id="targetTime">
            </div>
            <div class="col-md-3">
                <label for="reminderTitle" class="form-label">Judul:</label>
                <input type="text" class="form-control" id="reminderTitle" value="WAKTU PENILAIAN">
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button class="btn btn-primary btn-custom me-2" onclick="setCountdown()">
                    <i class="fas fa-play me-1"></i>Mulai
                </button>
                <button class="btn btn-secondary btn-custom" onclick="resetCountdown()">
                    <i class="fas fa-refresh me-1"></i>Reset
                </button>
            </div>
        </div>
    </div> -->

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
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Ambil end_date dari DB (hidden input). Contoh: "2025-08-31" atau "2025-08-31 23:59:59"
        const periodeEndDateInput = document.getElementById('periodeEndDate');
        const defaultWarningMs = 24 * 60 * 60 * 1000; // 24 jam => threshold peringatan (ubah jika perlu)

        // Interval handle supaya bisa direset
        let countdownInterval = null;

        // Ubah alert state (info / warning / danger) dan message + icon
        function setAlertState(state, message) {
            const alertEl = document.getElementById('statusAlert');
            const statusMsgEl = document.getElementById('statusMessage');

            // Hapus semua kelas
            alertEl.classList.remove('alert-info', 'alert-warning', 'alert-danger');

            // Pilih icon sesuai state
            let iconHtml = '<i class="fas fa-info-circle me-2"></i> ';
            if (state === 'info') {
                alertEl.classList.add('alert-info');
                iconHtml = '<i class="fas fa-info-circle me-2"></i> ';
            } else if (state === 'warning') {
                alertEl.classList.add('alert-warning');
                iconHtml = '<i class="fas fa-exclamation-triangle me-2"></i> ';
            } else if (state === 'danger') {
                alertEl.classList.add('alert-danger');
                iconHtml = '<i class="fas fa-times-circle me-2"></i> ';
            }

            // Masukkan isi dengan icon + message
            statusMsgEl.innerHTML = iconHtml + message;
        }

        // Utility: tampilkan nilai waktu di UI
        function updateTimerDisplay(days, hours, minutes, seconds) {
            document.getElementById('days').innerText = days;
            document.getElementById('hours').innerText = hours;
            document.getElementById('minutes').innerText = minutes;
            document.getElementById('seconds').innerText = seconds;
        }

        // Mulai countdown dari targetTime (ms epoch). title optional untuk status.
        function startCountdown(targetTimeMs, warningThresholdMs = defaultWarningMs, title = 'WAKTU PENILAIAN') {
            // Clear interval sebelumnya jika ada
            if (countdownInterval) clearInterval(countdownInterval);

            // Update judul (kalau mau)
            const reminderTitleInput = document.getElementById('reminderTitle');
            if (reminderTitleInput) reminderTitleInput.value = title;

            function tick() {
                const now = new Date().getTime();
                let distance = targetTimeMs - now;

                if (distance <= 0) {
                    // Waktu habis
                    updateTimerDisplay(0, 0, 0, 0);
                    setAlertState('danger', 'Waktu penilaian sudah berakhir');
                    clearInterval(countdownInterval);
                    countdownInterval = null;
                    return;
                }

                // Kalkulasi
                const days = Math.floor(distance / (1000 * 60 * 60 * 24));
                const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((distance % (1000 * 60)) / 1000);

                // Update tampilan timer
                updateTimerDisplay(days, hours, minutes, seconds);

                // Ubah alert ketika sudah mendekati habis
                if (distance <= warningThresholdMs) {
                    setAlertState('warning', 'Waktu penilaian hampir berakhir — segera lakukan penilaian');
                } else {
                    setAlertState('info', 'Waktu penilaian masih tersedia');
                }
            }

            // Jalankan tick segera dan set interval
            tick();
            countdownInterval = setInterval(tick, 1000);
        }

        // Reset countdown: hentikan dan kembalikan ke state awal
        window.resetCountdown = function() {
            if (countdownInterval) {
                clearInterval(countdownInterval);
                countdownInterval = null;
            }
            updateTimerDisplay(0, 0, 0, 0);
            setAlertState('info', 'Waktu penilaian masih tersedia');
        };

        // Fungsi yang dipanggil tombol "Mulai" (menggunakan nilai manual dari input date/time)
        window.setCountdown = function() {
            // Ambil input manual jika user klik Mulai
            const dateInput = document.getElementById('targetDate').value;
            const timeInput = document.getElementById('targetTime').value;
            const titleInput = document.getElementById('reminderTitle').value || 'WAKTU PENILAIAN';

            if (!dateInput) {
                alert('Silakan pilih tanggal target terlebih dahulu.');
                return;
            }

            // Jika timeInput kosong, set ke 23:59:59 supaya full hari
            const timePart = timeInput ? timeInput : '23:59:59';
            // Gabungkan jadi ISO local-friendly
            const iso = dateInput + 'T' + timePart;
            const targetMs = new Date(iso).getTime();

            if (isNaN(targetMs)) {
                alert('Format tanggal/waktu tidak valid.');
                return;
            }

            startCountdown(targetMs, defaultWarningMs, titleInput);
        };

        // Bila DB memberikan periode aktif, start otomatis
        if (periodeEndDateInput && periodeEndDateInput.value) {
            let raw = periodeEndDateInput.value.trim(); // contoh: "2025-08-31" atau "2025-08-31 23:59:59"

            // Jika hanya format YYYY-MM-DD, tambahkan 23:59:59 (seharusnya end of day)
            // Jika ada jam sudah termasuk, gunakan apa adanya
            let targetIso;
            if (/^\d{4}-\d{2}-\d{2}$/.test(raw)) {
                targetIso = raw + 'T23:59:59';
            } else {
                // Ubah spasi jadi 'T' jika perlu untuk compatibilitas new Date()
                targetIso = raw.replace(' ', 'T');
            }

            const targetMs = new Date(targetIso).getTime();
            if (!isNaN(targetMs)) {
                // Optional: set judul otomatis (bisa ambil nama periode jika mau nambah ke view)
                const title = document.getElementById('reminderTitle')?.value || 'WAKTU PENILAIAN';
                startCountdown(targetMs, defaultWarningMs, title);
            } else {
                console.warn('Periode end_date tidak valid:', raw);
            }
        } else {
            // Tidak ada periode aktif: tetap tampilkan state info default
            setAlertState('info', '-');
        }
    });
</script>

<?php $this->endSection(); ?>