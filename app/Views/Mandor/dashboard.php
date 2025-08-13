<?php $this->extend('layout/template'); ?>
<?php $this->section('content'); ?>

<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.24/css/dataTables.bootstrap5.min.css">
<style>
    :root {
        /* Simplified blue & white palette */
        --primary: #17c1e8;
        /* blue-600 */
        --primary-dark: #106a7eff;
        /* blue-800 */
        --accent: #bfdbfe;
        /* light blue */
        --success: #16a34a;
        --warning: #f59e0b;
        --danger: #ef4444;
        --bg-light: #f8fafc;
        /* very light background */
        --glass-bg: rgba(37, 99, 235, 0.06);
        /* subtle blue glass */
        --card-bg: #ffffff;
        --radius: 12px;
        --shadow-sm: 0 6px 12px rgba(16, 24, 40, 0.04);
        --shadow-lg: 0 20px 40px rgba(16, 24, 40, 0.08);
        --transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    }

    * {
        box-sizing: border-box;
    }

    body {
        background: var(--bg-light);
        min-height: 100vh;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial;
        color: #0f172a;
        /* neutral dark text */
        overflow-x: hidden;
    }

    /* subtle background particles kept but toned down */
    .bg-particles {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        pointer-events: none;
        z-index: 1;
        opacity: 0.06;
    }

    .particle {
        position: absolute;
        width: 5px;
        height: 5px;
        background: var(--primary);
        border-radius: 50%;
        animation: float 8s ease-in-out infinite;
    }

    @keyframes float {

        0%,
        100% {
            transform: translateY(0px) rotate(0deg);
        }

        50% {
            transform: translateY(-18px) rotate(180deg);
        }
    }

    .main-container {
        position: relative;
        z-index: 10;
        padding: 2rem 1rem;
        min-height: 100vh;
    }

    .card-animated {
        animation: slideInUp 0.6s cubic-bezier(0.16, 1, 0.3, 1);
        background: var(--card-bg);
        border-radius: var(--radius);
        box-shadow: var(--shadow-lg);
        transition: var(--transition);
        overflow: hidden;
        border: 1px solid rgba(14, 42, 100, 0.04);
    }

    .card-animated:hover {
        transform: translateY(-6px);
        box-shadow: 0 30px 50px rgba(14, 42, 100, 0.08);
    }

    @keyframes slideInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Countdown styling - simple blue header with white cards */
    .countdown-container {
        background: linear-gradient(180deg, var(--primary-dark), var(--primary));
        border-radius: var(--radius);
        padding: 1.75rem;
        color: white;
        text-align: center;
        position: relative;
        overflow: hidden;
    }

    .countdown-title {
        color: white;
        font-size: clamp(1.25rem, 3.5vw, 1.75rem);
        font-weight: 700;
    }

    .countdown-subtitle {
        color: white;
        font-size: 0.95rem;
        opacity: 0.95;
        margin-bottom: 1rem;
    }

    .countdown-timer {
        display: flex;
        gap: .75rem;
        justify-content: center;
        flex-wrap: wrap;
    }

    .time-unit {
        background: #ffffff;
        /* white card */
        color: var(--primary-dark);
        border-radius: 10px;
        padding: 1rem 0.9rem;
        text-align: center;
        min-width: 78px;
        transition: var(--transition);
        box-shadow: var(--shadow-sm);
        cursor: default;
    }

    .time-unit:hover {
        transform: translateY(-4px);
    }

    .time-number {
        display: block;
        font-size: 1.4rem;
        font-weight: 800;
        line-height: 1;
    }

    .time-label {
        font-size: 0.72rem;
        font-weight: 600;
        color: rgba(2, 6, 23, 0.6);
        margin-top: 0.35rem;
    }

    .pulse {
        animation: pulse 2s infinite;
    }

    @keyframes pulse {

        0%,
        100% {
            transform: scale(1);
            opacity: 1
        }

        50% {
            transform: scale(1.06);
            opacity: 0.9
        }
    }

    /* Employee stats card - simplified blue accent */
    .employee-stats {
        background: linear-gradient(135deg, rgba(37, 99, 235, 0.06), rgba(255, 255, 255, 0));
        color: var(--primary-dark);
        border-radius: var(--radius);
        padding: 1.5rem;
        position: relative;
    }

    .avatar {
        width: 56px;
        height: 56px;
        border-radius: 10px;
        background: var(--primary);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
    }

    .avatar:hover {
        transform: scale(1.05);
    }

    /* Progress circle */
    .circle-progress {
        position: relative;
        width: 110px;
        height: 110px;
    }

    .circle-chart {
        width: 100%;
        height: 100%;
        transform: rotate(-90deg);
    }

    .circle-bg {
        fill: none;
        stroke: rgba(2, 6, 23, 0.06);
        stroke-width: 8;
    }

    .circle-fill {
        fill: none;
        stroke: var(--primary);
        stroke-width: 8;
        stroke-linecap: round;
        transition: stroke-dasharray .9s ease;
    }

    .circle-text {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--primary-dark);
    }

    /* Buttons */
    .btn-interactive {
        border-radius: 10px;
        font-weight: 600;
        padding: .6rem 1rem;
        border: none;
        cursor: pointer;
        transition: var(--transition);
    }

    .btn-primary-custom {
        background: var(--primary);
        color: white;
    }

    .btn-primary-custom:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 30px rgba(37, 99, 235, 0.15);
    }

    .btn-secondary-custom {
        background: rgba(37, 99, 235, 0.06);
        color: var(--primary-dark);
    }

    /* Charts container */
    .chart-container {
        background: var(--card-bg);
        border-radius: var(--radius);
        padding: 1.25rem;
        box-shadow: var(--shadow-sm);
        transition: var(--transition);
    }

    .chart-container:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-lg);
    }

    .chart-container::before {
        content: '';
        position: absolute;
        left: 0;
        right: 0;
        top: 0;
        height: 3px;
        background: var(--primary);
        opacity: 0.08;
    }

    /* Alert styles (simplified) */
    .alert-custom {
        border-radius: 10px;
        border: none;
        padding: .9rem 1.1rem;
        font-weight: 500;
    }

    .alert-info-custom {
        background: rgba(37, 99, 235, 0.06);
        color: var(--primary-dark);
        border-left: 4px solid var(--primary);
    }

    .alert-warning-custom {
        background: rgba(245, 158, 11, 0.06);
        color: var(--warning);
        border-left: 4px solid var(--warning);
    }

    .alert-danger-custom {
        background: rgba(239, 68, 68, 0.06);
        color: var(--danger);
        border-left: 4px solid var(--danger);
    }

    /* Modal tweaks */
    .modal-custom .modal-content {
        border-radius: 12px;
        border: none;
        box-shadow: var(--shadow-lg);
    }

    .modal-custom .modal-header {
        background: var(--primary);
        color: white;
        border-bottom: none;
        padding: 1rem 1.25rem;
    }

    .modal-custom .modal-body {
        padding: 1rem 1.25rem;
    }

    /* Table */
    .table-responsive {
        border-radius: var(--radius);
        box-shadow: var(--shadow-sm);
    }

    .table th {
        background: var(--primary);
        color: white;
        font-weight: 600;
        border: none;
    }

    .table tbody tr:hover {
        background-color: rgba(37, 99, 235, 0.03);
        transform: none;
    }

    /* Status indicators simplified */
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: .4rem;
        padding: .25rem .6rem;
        border-radius: 999px;
        font-size: .75rem;
        font-weight: 700;
    }

    .status-success {
        background: rgba(16, 185, 129, 0.08);
        color: var(--success);
    }

    .status-warning {
        background: rgba(245, 158, 11, 0.08);
        color: var(--warning);
    }

    .status-danger {
        background: rgba(239, 68, 68, 0.08);
        color: var(--danger);
    }

    /* Responsive */
    @media (max-width:768px) {
        .main-container {
            padding: 1rem
        }

        .countdown-container,
        .employee-stats {
            padding: 1rem
        }

        .time-unit {
            min-width: 64px;
            padding: .8rem
        }
    }

    @media (max-width:576px) {
        .circle-progress {
            width: 90px;
            height: 90px
        }

        .btn-interactive {
            padding: .45rem .9rem
        }
    }

    /* small loading */
    .loading {
        display: inline-block;
        width: 18px;
        height: 18px;
        border: 2px solid rgba(255, 255, 255, 0.3);
        border-radius: 50%;
        border-top-color: white;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        to {
            transform: rotate(360deg);
        }
    }

    /* slide out animation for alerts */
    @keyframes slideOutUp {
        from {
            opacity: 1;
            transform: translateY(0)
        }

        to {
            opacity: 0;
            transform: translateY(-20px)
        }
    }
</style>

<!-- Animated background -->
<div class="bg-particles" id="particles"></div>

<div class="main-container">
    <!-- Alert section -->
    <div id="alertContainer" class="mb-4"></div>

    <div class="row g-4">
        <!-- Countdown Section -->
        <div class="col-lg-6">
            <div class="card-animated" style="animation-delay: 0.1s;">
                <div id="countdownContainer" class="countdown-container">
                    <h1 class="countdown-title">
                        <i class="fas fa-clock me-2"></i>
                        INFORMASI
                    </h1>
                    <h2 class="countdown-subtitle">WAKTU PENILAIAN</h2>

                    <div class="countdown-timer" id="countdownTimer">
                        <div class="time-unit" onclick="pulseEffect(this)">
                            <span id="days" class="time-number">0</span>
                            <div class="time-label">Hari</div>
                        </div>
                        <div class="time-unit" onclick="pulseEffect(this)">
                            <span id="hours" class="time-number">0</span>
                            <div class="time-label">Jam</div>
                        </div>
                        <div class="time-unit" onclick="pulseEffect(this)">
                            <span id="minutes" class="time-number">0</span>
                            <div class="time-label">Menit</div>
                        </div>
                        <div class="time-unit" onclick="pulseEffect(this)">
                            <span id="seconds" class="time-number pulse">0</span>
                            <div class="time-label">Detik</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Employee Stats Section -->
        <div class="col-lg-6">
            <div class="card-animated employee-stats" style="animation-delay: 0.2s;">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <div class="avatar" onclick="animateAvatar(this)">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                    <div class="col">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h5 class="card-title mb-1">
                                    <span class="badge bg-light text-dark ms-2" id="areaBadge">Area: <?= $area ?></span>
                                </h5>
                                <p class="mb-3" style="color: rgba(2,6,23,0.6);">Status penilaian periode ini</p>

                                <div class="row text-center">
                                    <div class="col-6">
                                        <div class="h3 mb-0" id="totalKaryawan"><?= $totalKaryawan ?></div>
                                        <small class="text-muted">Total Karyawan</small>
                                    </div>
                                    <div class="col-6">
                                        <div class="h3 mb-0" id="averageGrade"><?= $grade ?></div>
                                        <small class="text-muted">Diagram Grade</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 text-center">
                                <div class="circle-progress mx-auto">
                                    <svg class="circle-chart" viewBox="0 0 100 100">
                                        <circle cx="50" cy="50" r="40" class="circle-bg"></circle>
                                        <circle cx="50" cy="50" r="40" class="circle-fill" id="progressCircle" stroke-dasharray="0 251.2" stroke-dashoffset="0"></circle>
                                    </svg>
                                    <div class="circle-text" id="progressText"><?= number_format($progress, 2) ?>%</div>
                                </div>
                                <small class="d-block mt-2" style="color: rgba(2,6,23,0.6);">Progress Penilaian</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="row g-4 mt-2">
        <div class="col-lg-6">
            <div class="chart-container card-animated" style="animation-delay: 0.3s; position:relative;">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-line me-2" style="color:var(--primary);"></i>
                        Rata-Rata Grade Periode Sebelumnya
                    </h5>
                </div>
                <div class="card mb-3">
                    <div class="card-body p-3">
                        <div class="chart">
                            <canvas id="softBar" class="chart-canvas" height="300px"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="chart-container card-animated" style="animation-delay: 0.4s;">
                <h5 class="mb-3">
                    <i class="fas fa-exclamation-triangle me-2" style="color:var(--warning)"></i>
                    Grade D Periode Sebelumnya
                </h5>
                <div class="table-responsive">
                    <table class="table table-sm table-bordered nowrap" id="gradeDTable" style="width:100%">
                        <thead class="table-primary">
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th>Kode Kartu</th>
                                <th>Bagian</th>
                                <th>Grade</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($karGradeD)) : ?>
                                <?php foreach ($karGradeD as $index => $karyawan) : ?>
                                    <tr>
                                        <td><?= $index + 1 ?></td>
                                        <td><?= esc($karyawan['employee_name']) ?></td>
                                        <td><?= esc($karyawan['employee_code']) ?></td>
                                        <td><?= esc($karyawan['job_section_name']) ?></td>
                                        <td class="fw-bold text-danger">D</td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted">
                                        Tidak ada karyawan dengan Grade D
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



<!-- Scripting: load jQuery dulu, lalu DataTables, lalu Bootstrap JS (bundle) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- DataTables (requires jQuery) -->
<script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap5.min.js"></script>

<!-- Bootstrap 5 bundle (Popper+Bootstrap JS) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- pastikan ini ada dan berada SEBELUM script yang membuat Chart() -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    // Global variables
    let countdownInterval;
    let autoRefreshInterval;
    let trendChart;

    // Initialize dashboard
    document.addEventListener('DOMContentLoaded', function() {
        // Ambil end_date dari PHP
        const endDate = "<?= $periode['end_date'] ?>"; // format Y-m-d

        // Buat target date jam 23:59:59 pada tanggal end_date
        const target = new Date(endDate + "T23:59:59");
        const progress = <?= json_encode($progress) ?>;
        updateProgressCircle(progress);

        startCountdown(target);
        createParticles();
        initializeCountdown();
        // initializeChart();
        initializeDataTable();
        startAutoRefresh();
        animateNumbers();

        // Show welcome alert
        showAlert('info', 'Dashboard berhasil dimuat. Selamat datang!');
    });

    // Create animated background particles
    function createParticles() {
        const particlesContainer = document.getElementById('particles');
        const particleCount = 50;

        for (let i = 0; i < particleCount; i++) {
            const particle = document.createElement('div');
            particle.className = 'particle';
            particle.style.left = Math.random() * 100 + '%';
            particle.style.top = Math.random() * 100 + '%';
            particle.style.animationDelay = Math.random() * 6 + 's';
            particle.style.animationDuration = (Math.random() * 3 + 3) + 's';
            particlesContainer.appendChild(particle);
        }
    }

    // Countdown functionality
    function initializeCountdown() {
        startCountdown(targetDate);
    }

    function startCountdown(targetDate) {
        if (countdownInterval) clearInterval(countdownInterval);

        countdownInterval = setInterval(() => {
            const now = new Date().getTime();
            const distance = targetDate.getTime() - now;

            if (distance < 0) {
                clearInterval(countdownInterval);
                updateCountdownDisplay(0, 0, 0, 0);
                showAlert('danger', 'Waktu penilaian telah berakhir!');
                return;
            }

            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);

            updateCountdownDisplay(days, hours, minutes, seconds);

            // Alert warnings
            if (days <= 3 && !localStorage.getItem('warning_shown')) {
                showAlert('warning', `Peringatan: Sisa ${days} hari untuk menyelesaikan penilaian!`);
                localStorage.setItem('warning_shown', 'true');
            }

            if (days <= 1 && !localStorage.getItem('urgent_warning_shown')) {
                showAlert('danger', 'URGENT: Waktu penilaian hampir habis!');
                localStorage.setItem('urgent_warning_shown', 'true');
                if (document.getElementById('soundAlert').checked) {
                    playNotificationSound();
                }
            }
        }, 1000);
    }

    function updateCountdownDisplay(days, hours, minutes, seconds) {
        animateNumber('days', days);
        animateNumber('hours', hours);
        animateNumber('minutes', minutes);
        animateNumber('seconds', seconds);
    }

    function animateNumber(elementId, newValue) {
        const element = document.getElementById(elementId);
        const currentValue = parseInt(element.textContent) || 0;

        if (currentValue !== newValue) {
            element.style.transform = 'scale(1.2)';
            element.style.color = '#00ff88';

            setTimeout(() => {
                element.textContent = newValue.toString().padStart(2, '0');
                element.style.transform = 'scale(1)';
                element.style.color = '';
            }, 150);
        }
    }

    // Interactive functions
    function pulseEffect(element) {
        element.style.transform = 'scale(1.1)';
        element.style.boxShadow = '0 0 20px rgba(255, 255, 255, 0.5)';

        setTimeout(() => {
            element.style.transform = '';
            element.style.boxShadow = '';
        }, 200);
    }

    function animateAvatar(element) {
        element.style.transform = 'rotate(360deg) scale(1.2)';
        element.style.background = 'rgba(255, 255, 255, 0.3)';

        setTimeout(() => {
            element.style.transform = '';
            element.style.background = '';
        }, 500);
    }

    function refreshCountdown() {
        const button = event.target;
        const originalText = button.innerHTML;
        button.innerHTML = '<div class="loading"></div> Refreshing...';
        button.disabled = true;

        setTimeout(() => {
            initializeCountdown();
            updateProgressData();
            button.innerHTML = originalText;
            button.disabled = false;
            showAlert('success', 'Data berhasil diperbarui!');
        }, 1500);
    }

    function showSettings() {
        const modal = new bootstrap.Modal(document.getElementById('settingsModal'));
        modal.show();
    }

    function sendReminder() {
        const modal = new bootstrap.Modal(document.getElementById('reminderModal'));
        modal.show();
    }

    function viewDetails() {
        showAlert('info', 'Membuka halaman detail penilaian...');
        // In real implementation, navigate to details page
    }

    // Alert system
    function showAlert(type, message) {
        const alertContainer = document.getElementById('alertContainer');
        const alertId = 'alert_' + Date.now();

        const alertHtml = `
                <div id="${alertId}" class="alert alert-${type}-custom alert-custom alert-dismissible fade show">
                    <i class="fas fa-${getAlertIcon(type)} me-2"></i>
                    ${message}
                    <button type="button" class="btn-close" onclick="dismissAlert('${alertId}')"></button>
                </div>
            `;

        alertContainer.innerHTML = alertHtml + alertContainer.innerHTML;

        // Auto dismiss after 5 seconds
        setTimeout(() => {
            dismissAlert(alertId);
        }, 5000);
    }

    function getAlertIcon(type) {
        switch (type) {
            case 'success':
                return 'check-circle';
            case 'warning':
                return 'exclamation-triangle';
            case 'danger':
                return 'times-circle';
            default:
                return 'info-circle';
        }
    }

    function dismissAlert(alertId) {
        const alert = document.getElementById(alertId);
        if (alert) {
            alert.style.animation = 'slideOutUp 0.3s ease-in forwards';
            setTimeout(() => {
                alert.remove();
            }, 300);
        }
    }

    // Progress circle animation
    function updateProgressCircle(percentage) {
        const circle = document.getElementById('progressCircle');
        const text = document.getElementById('progressText');
        if (!circle || !text) return;

        const circumference = 2 * Math.PI * 40; // radius = 40
        const offset = circumference - (percentage / 100) * circumference;

        circle.style.strokeDasharray = circumference;
        circle.style.strokeDashoffset = offset;

        let current = 0;
        const increment = percentage / 50;
        const timer = setInterval(() => {
            current += increment;
            if (current >= percentage) {
                current = percentage;
                clearInterval(timer);
            }
            text.textContent = Math.round(current) + '%';
        }, 20);
    }

    // DataTable initialization
    function initializeDataTable() {
        $('#gradeDTable').DataTable({
            responsive: true,
            paging: true,
            searching: true,
            ordering: true,
            lengthChange: false,
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Cari data...",
                zeroRecords: "Tidak ada data ditemukan",
                info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
                infoEmpty: "Tidak ada data",
                paginate: {
                    previous: "Sebelumnya",
                    next: "Berikutnya"
                }
            }
        });
    }


    // Modal functions
    function toggleSelectAll() {
        const selectAll = document.getElementById('selectAll');
        const checkboxes = document.querySelectorAll('.employee-check');

        checkboxes.forEach(checkbox => {
            checkbox.checked = selectAll.checked;
        });
    }

    function sendReminderConfirm() {
        const checkedBoxes = document.querySelectorAll('.employee-check:checked');
        if (checkedBoxes.length === 0) {
            showAlert('warning', 'Pilih minimal satu karyawan untuk dikirim pengingat.');
            return;
        }

        const modal = bootstrap.Modal.getInstance(document.getElementById('reminderModal'));
        modal.hide();

        showAlert('success', `Pengingat berhasil dikirim kepada ${checkedBoxes.length} karyawan.`);
    }

    function updateArea() {
        const select = document.getElementById('areaSelect');
        const badge = document.getElementById('areaBadge');
        badge.textContent = `Area: ${select.options[select.selectedIndex].text}`;

        // Simulate data update based on area
        updateProgressData();
    }

    function toggleAutoRefresh() {
        const checkbox = document.getElementById('autoRefresh');
        if (checkbox.checked) {
            startAutoRefresh();
            showAlert('info', 'Auto refresh diaktifkan (30 detik).');
        } else {
            stopAutoRefresh();
            showAlert('info', 'Auto refresh dinonaktifkan.');
        }
    }

    function toggleSoundAlert() {
        const checkbox = document.getElementById('soundAlert');
        showAlert('info', checkbox.checked ?
            'Notifikasi suara diaktifkan.' :
            'Notifikasi suara dinonaktifkan.');
    }

    // function saveSettings() {
    //     const endDate = document.getElementById('endDateInput').value;
    //     if (endDate) {
    //         const targetDate = new Date(endDate);
    //         startCountdown(targetDate);
    //     }

    //     const modal = bootstrap.Modal.getInstance(document.getElementById('settingsModal'));
    //     modal.hide();

    //     showAlert('success', 'Pengaturan berhasil disimpan!');
    // }

    function exportGradeD() {
        showAlert('info', 'Mengunduh data karyawan grade D...');
        // In real implementation, trigger file download
    }

    // Auto refresh functionality
    function startAutoRefresh() {
        stopAutoRefresh(); // Clear existing interval
        autoRefreshInterval = setInterval(() => {
            updateProgressData();
            if (Math.random() > 0.7) { // Simulate occasional updates
                showAlert('info', 'Data otomatis diperbarui.');
            }
        }, 30000);
    }

    function stopAutoRefresh() {
        if (autoRefreshInterval) {
            clearInterval(autoRefreshInterval);
            autoRefreshInterval = null;
        }
    }

    // Simulate data updates
    function updateProgressData() {
        const percentage = <?= $progress ?>;
        updateProgressCircle(percentage);

        // Update other stats with slight variations
        const total = <?= $totalKaryawan ?>;
        const assessed = Math.floor(total * percentage / 100);
        // const pending = Math.floor(Math.random() * 5) + 20;
        const notAssessed = total - assessed;

        animateCounter(document.getElementById('totalKaryawan'), total);
        animateCounter(document.getElementById('assessedCount'), assessed);
        // animateCounter(document.getElementById('pendingCount'), pending);
        animateCounter(document.getElementById('notAssessedCount'), notAssessed);
        animateCounter(document.getElementById('averageScore'), (Math.random() * 2 + 7).toFixed(1) * 1);
    }

    // Number animation
    function animateNumbers() {
        updateProgressCircle(68);
    }

    function animateCounter(element, target) {
        const start = parseInt(element.textContent) || 0;
        const duration = 1000;
        const startTime = Date.now();

        function update() {
            const elapsed = Date.now() - startTime;
            const progress = Math.min(elapsed / duration, 1);
            const current = Math.round(start + (target - start) * easeOutQuart(progress));

            element.textContent = current;

            if (progress < 1) {
                requestAnimationFrame(update);
            }
        }

        update();
    }

    function easeOutQuart(t) {
        return 1 - Math.pow(1 - t, 4);
    }

    // Sound notification
    function playNotificationSound() {
        // Create audio context for notification sound
        try {
            const audioContext = new(window.AudioContext || window.webkitAudioContext)();
            const oscillator = audioContext.createOscillator();
            const gainNode = audioContext.createGain();

            oscillator.connect(gainNode);
            gainNode.connect(audioContext.destination);

            oscillator.frequency.setValueAtTime(800, audioContext.currentTime);
            oscillator.frequency.setValueAtTime(600, audioContext.currentTime + 0.1);

            gainNode.gain.setValueAtTime(0.3, audioContext.currentTime);
            gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.5);

            oscillator.start();
            oscillator.stop(audioContext.currentTime + 0.5);
        } catch (e) {
            console.warn('Audio not supported');
        }
    }

    // CSS animations for slide out
    const style = document.createElement('style');
    style.textContent = `
            @keyframes slideOutUp {
                from {
                    opacity: 1;
                    transform: translateY(0);
                }
                to {
                    opacity: 0;
                    transform: translateY(-20px);
                }
            }
        `;
    document.head.appendChild(style);
</script>

<script>
    let softBarChart;

    function makeGradient(ctx, area) {
        const g = ctx.createLinearGradient(0, area.bottom, 0, area.top);
        // top brighter, bottom softer
        g.addColorStop(0, 'rgba(23, 193, 232, 0.25)');
        g.addColorStop(1, 'rgba(23, 193, 232, 0.75)');
        return g;
    }

    function initSoftBarChart(labels, values) {
        const canvas = document.getElementById('softBar');
        const ctx = canvas.getContext('2d');

        if (softBarChart) softBarChart.destroy();

        // Plugin untuk gradient dinamis (sesuai area chart)
        const gradientPlugin = {
            id: 'softGradientFill',
            beforeDatasetsDraw(chart) {
                const {
                    ctx,
                    chartArea
                } = chart;
                if (!chartArea) return;
                const ds = chart.data.datasets[0];
                ds.backgroundColor = makeGradient(ctx, chartArea);
            }
        };

        softBarChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels,
                datasets: [{
                    label: 'Total',
                    data: values,
                    // Soft UI details
                    borderRadius: 10,
                    borderWidth: 0,
                    hoverBorderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                layout: {
                    padding: 8
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(15, 23, 42, 0.9)',
                        titleColor: '#e2e8f0',
                        bodyColor: '#f8fafc',
                        cornerRadius: 12,
                        padding: 12,
                        displayColors: false,
                        callbacks: {
                            label: (ctx) => ` ${ctx.dataset.label}: ${Number(ctx.parsed.y).toFixed(2)}`
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: '#64748b',
                            font: {
                                size: 12
                            }
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(2, 6, 23, 0.06)',
                            drawBorder: false
                        },
                        ticks: {
                            color: '#94a3b8',
                            font: {
                                size: 12
                            },
                            callback: (v) => v % 1 === 0 ? v : ''
                        }
                    }
                },
                animation: {
                    duration: 900,
                    easing: 'easeOutQuart'
                },
                elements: {
                    bar: {
                        borderSkipped: false,
                        // Shadow untuk batang (Soft UI)
                        inflateAmount: 2
                    }
                }
            },
            plugins: [gradientPlugin]
        });

        // Drop shadow halus via canvas shadow (tanpa ganggu tooltip)
        const origDraw = softBarChart.draw;
        softBarChart.draw = function() {
            const {
                ctx
            } = this;
            ctx.save();
            ctx.shadowColor = 'rgba(13, 38, 76, 0.18)';
            ctx.shadowBlur = 16;
            ctx.shadowOffsetY = 8;
            origDraw.apply(this, arguments);
            ctx.restore();
        };
        softBarChart.update();
    }

    // Demo data — ganti dengan data kamu
    const demoLabels = <?= json_encode($labels) ?>;
    const demoValues = <?= json_encode($data) ?>;
    initSoftBarChart(demoLabels, demoValues);

    // Contoh refresh handler: ganti data dinamis
    document.getElementById('refreshBtn').addEventListener('click', () => {
        const shuffled = demoValues.map(v => Math.max(0, Math.min(5, (v + (Math.random() - 0.5)))));
        initSoftBarChart(demoLabels, shuffled);
    });

    // Helper update dari API/backend kamu
    // panggil: updateSoftBar(labelsBaru, valuesBaru);
    function updateSoftBar(newLabels, newValues) {
        initSoftBarChart(newLabels, newValues);
    }
</script>


<?php $this->endSection(); ?>