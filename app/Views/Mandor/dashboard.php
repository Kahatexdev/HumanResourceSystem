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

    /* Modal overall */
    #reminderModal .modal-dialog {
        max-width: 520px;
        margin: 1.75rem auto;
    }

    #reminderModal .modal-content {
        border-radius: 18px;
        overflow: hidden;
        box-shadow: 0 20px 60px rgba(8, 30, 52, 0.35);
        border: 0;
    }

    /* Header dengan gradient */
    #reminderModal .modal-header {
        background: linear-gradient(90deg, rgba(52, 152, 219, 1) 0%, rgba(41, 128, 185, 1) 100%);
        color: #fff;
        border-bottom: 0;
        padding: 18px 22px;
        align-items: center;
    }

    /* Header title */
    #reminderModal .modal-title {
        font-size: 1.15rem;
        font-weight: 700;
        letter-spacing: 0.6px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    /* Header icon */
    #reminderModal .modal-title .icon {
        background: rgba(255, 255, 255, 0.15);
        width: 40px;
        height: 40px;
        display: inline-flex;
        border-radius: 10px;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        color: #fff;
        box-shadow: 0 6px 18px rgba(19, 63, 102, 0.25);
    }

    /* Body */
    #reminderModal .modal-body {
        padding: 20px 22px;
        background: linear-gradient(180deg, #fbfdff 0%, #f7f9fc 100%);
        color: #123;
        font-size: 0.98rem;
        line-height: 1.45;
    }

    /* Emphasize important text */
    #reminderModal .modal-body .important {
        display: inline-block;
        background: rgba(255, 193, 7, 0.12);
        color: #b97a00;
        padding: 6px 10px;
        border-radius: 8px;
        font-weight: 600;
        margin-top: 6px;
    }

    /* Footer */
    #reminderModal .modal-footer {
        padding: 14px 18px;
        border-top: 0;
        background: #fff;
        justify-content: space-between;
    }

    /* Buttons: rounded and subtle shadow */
    #reminderModal .btn-custom {
        border-radius: 10px;
        padding: 9px 16px;
        font-weight: 600;
        box-shadow: 0 8px 20px rgba(18, 52, 86, 0.06);
    }

    /* Primary acknowledge: vibrant */
    #reminderModal .btn-primary {
        background: linear-gradient(90deg, #2d9cdb 0%, #2380c7 100%);
        border: 0;
        color: #fff;
        transition: transform .12s ease, box-shadow .12s ease;
    }

    /* Small hover/active micro-interaction */
    #reminderModal .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 14px 30px rgba(35, 128, 199, 0.18);
    }

    #reminderModal .btn-secondary {
        background: transparent;
        border: 1px solid rgba(18, 52, 86, 0.06);
        color: #123;
    }

    /* Close button (white small X on header) */
    #reminderModal .btn-close {
        filter: brightness(1.4);
        opacity: 0.9;
    }

    /* Add subtle pulse to header icon when within 3 days */
    #reminderModal.pulse .icon {
        animation: pulse 1.8s infinite;
    }

    /* Responsive */
    @media (max-width: 576px) {
        #reminderModal .modal-dialog {
            max-width: 92%;
        }

        #reminderModal .modal-title {
            font-size: 1rem;
        }
    }

    /* Keep consistent pulse keyframes (already in your CSS but redefine if needed) */
    @keyframes pulse {
        0% {
            transform: scale(1);
            opacity: 1;
        }

        50% {
            transform: scale(1.06);
            opacity: 0.85;
        }

        100% {
            transform: scale(1);
            opacity: 1;
        }
    }
</style>
<div class="container-fluid py-4">
    <?php if (session()->getFlashdata('warning')): ?>
        <div class="alert alert-warning text-bold text-dark"><?= session()->getFlashdata('warning') ?></div>
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
</div>


<!-- HTML modal: ganti modal lama dengan ini -->
<div class="modal fade" id="reminderModal" tabindex="-1" aria-labelledby="reminderModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" role="document" aria-modal="true">
            <div class="modal-header">
                <div class="modal-title" id="reminderModalLabel">
                    <span class="icon" aria-hidden="true"><i class="fas fa-bell"></i></span>
                    Pengingat Penilaian
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <p>
                    Tersisa waktu <strong id="reminderDaysText">3 hari</strong> atau kurang untuk menyelesaikan proses penilaian.
                    Mohon kepada <strong>Mandor</strong> untuk segera melakukan penilaian karyawan sebelum periode berakhir.
                </p>
                <p class="important">Catatan: popup ini hanya muncul sekali per periode.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-custom" data-bs-dismiss="modal">Tutup</button>
                <button type="button" id="reminderAcknowledge" class="btn btn-primary btn-custom">Saya Mengerti</button>
            </div>
        </div>
    </div>
</div>
<!-- Scripting: load jQuery dulu, lalu DataTables, lalu Bootstrap JS (bundle) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- DataTables (requires jQuery) -->
<script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap5.min.js"></script>

<!-- Bootstrap 5 bundle (Popper+Bootstrap JS) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const periodeEndDateInput = document.getElementById('periodeEndDate');

        // Ambang peringatan default (24 jam) tetap dipakai untuk "hampir habis"
        const defaultWarningMs = 24 * 60 * 60 * 1000; // 86.400.000 ms

        // Khusus aturan 3 hari: 3 * 24 * 60 * 60 * 1000 = 259.200.000 ms
        const threeDaysMs = 3 * 24 * 60 * 60 * 1000; // = 259200000

        let countdownInterval = null;

        function setAlertState(state, message) {
            const alertEl = document.getElementById('statusAlert');
            const statusMsgEl = document.getElementById('statusMessage');

            alertEl.classList.remove('alert-info', 'alert-warning', 'alert-danger');
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

            statusMsgEl.innerHTML = iconHtml + message;
        }

        function updateTimerDisplay(days, hours, minutes, seconds) {
            document.getElementById('days').innerText = days;
            document.getElementById('hours').innerText = hours;
            document.getElementById('minutes').innerText = minutes;
            document.getElementById('seconds').innerText = seconds;
        }

        // Tampilkan modal pengingat (hanya sekali per periode menggunakan localStorage)
        function showReminderOnce(periodeKey) {
            try {
                const storageKey = 'penilaian_reminder_shown_' + periodeKey;
                if (!localStorage.getItem(storageKey)) {
                    // Tampilkan modal Bootstrap
                    const rm = new bootstrap.Modal(document.getElementById('reminderModal'));
                    rm.show();

                    // ketika user acknowledge, catat ke localStorage supaya tidak muncul lagi
                    document.getElementById('reminderAcknowledge').addEventListener('click', function() {
                        localStorage.setItem(storageKey, '1');
                        rm.hide();
                    });

                    // juga catat jika modal ditutup dengan tombol X atau backdrop
                    document.getElementById('reminderModal').addEventListener('hidden.bs.modal', function() {
                        localStorage.setItem(storageKey, '1');
                    }, {
                        once: true
                    });
                }
            } catch (e) {
                console.warn('localStorage tidak tersedia, reminder mungkin akan muncul berulang.', e);
            }
        }

        function startCountdown(targetTimeMs, warningThresholdMs = defaultWarningMs, periodeKey = '') {
            if (countdownInterval) clearInterval(countdownInterval);

            function tick() {
                const now = Date.now();
                let distance = targetTimeMs - now;

                if (distance <= 0) {
                    updateTimerDisplay(0, 0, 0, 0);
                    setAlertState('danger', 'Waktu penilaian sudah berakhir');
                    clearInterval(countdownInterval);
                    countdownInterval = null;
                    return;
                }

                const days = Math.floor(distance / (1000 * 60 * 60 * 24));
                const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((distance % (1000 * 60)) / 1000);

                updateTimerDisplay(days, hours, minutes, seconds);

                // Jika <= 3 hari, tampilkan peringatan khusus dan modal pengingat
                if (distance <= threeDaysMs) {
                    setAlertState('warning', `Sisa ${days} hari ${hours} jam — segera ingatkan mandor.`);
                    if (periodeKey) showReminderOnce(periodeKey);
                } else if (distance <= warningThresholdMs) {
                    // threshold 'hampir habis' (misal 24 jam)
                    setAlertState('warning', 'Waktu penilaian hampir berakhir — segera lakukan penilaian');
                } else {
                    setAlertState('info', 'Waktu penilaian masih tersedia');
                }
            }

            tick();
            countdownInterval = setInterval(tick, 1000);
        }

        // Reset fungsi (sama seperti sebelumnya)
        window.resetCountdown = function() {
            if (countdownInterval) {
                clearInterval(countdownInterval);
                countdownInterval = null;
            }
            updateTimerDisplay(0, 0, 0, 0);
            setAlertState('info', 'Waktu penilaian masih tersedia');
        };

        // setCountdown manual (tetap kompatibel)
        window.setCountdown = function() {
            const dateInput = document.getElementById('targetDate').value;
            const timeInput = document.getElementById('targetTime').value;
            const titleInput = document.getElementById('reminderTitle')?.value || 'WAKTU PENILAIAN';

            if (!dateInput) {
                alert('Silakan pilih tanggal target terlebih dahulu.');
                return;
            }

            const timePart = timeInput ? timeInput : '23:59:59';
            const iso = dateInput + 'T' + timePart;
            const targetMs = new Date(iso).getTime();

            if (isNaN(targetMs)) {
                alert('Format tanggal/waktu tidak valid.');
                return;
            }

            // gunakan tanggal sebagai key periode supaya reminder hanya muncul sekali per periode
            const periodeKey = dateInput.replace(/[^0-9]/g, '');
            startCountdown(targetMs, defaultWarningMs, periodeKey);
        };

        // Jika DB memberikan periode aktif, start otomatis
        if (periodeEndDateInput && periodeEndDateInput.value) {
            let raw = periodeEndDateInput.value.trim();
            let targetIso;
            if (/^\d{4}-\d{2}-\d{2}$/.test(raw)) {
                targetIso = raw + 'T23:59:59';
            } else {
                targetIso = raw.replace(' ', 'T');
            }

            const targetMs = new Date(targetIso).getTime();
            if (!isNaN(targetMs)) {
                // gunakan end date (YYYYMMDD) sebagai periodeKey
                const periodeKey = (raw.match(/^\d{4}-\d{2}-\d{2}/) || [raw])[0].replace(/-/g, '');
                startCountdown(targetMs, defaultWarningMs, periodeKey);
            } else {
                console.warn('Periode end_date tidak valid:', raw);
                setAlertState('info', '-');
            }
        } else {
            setAlertState('info', '-');
        }
    });
</script>

<?php $this->endSection(); ?>