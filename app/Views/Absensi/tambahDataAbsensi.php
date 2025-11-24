<?php $this->extend('layout/template'); ?>
<?php $this->section('content'); ?>

<style>
    /* Card Styling */
    .attendance-card {
        border-radius: 12px;
        transition: all 0.3s ease;
        overflow: hidden;
    }

    .attendance-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12) !important;
    }

    /* Icon Circle */
    .icon-circle {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
    }

    /* Time Input Box */
    .time-input-box {
        background-color: #f8f9fa;
        transition: all 0.3s ease;
        border: 2px solid #e9ecef !important;
    }

    .time-input-box:hover {
        background-color: #fff;
        border-color: #0d6efd !important;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(13, 110, 253, 0.1);
    }

    .time-input-box:focus-within {
        background-color: #fff;
        border-color: #0d6efd !important;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15);
    }

    /* Time Icon */
    .time-icon {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        flex-shrink: 0;
    }

    /* Input Styling */
    .time-input-box input[type="datetime-local"] {
        font-size: 14px;
        font-weight: 500;
        color: #212529;
        background-color: transparent;
    }

    .time-input-box input[type="datetime-local"]:focus {
        box-shadow: none;
        outline: none;
    }

    /* Select2 Styling */
    .employee_select {
        border: 2px solid #e9ecef;
        border-radius: 8px;
        padding: 10px 12px;
        font-size: 14px;
    }

    .employee_select:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15);
    }

    /* Button Styling */
    .btn-group .btn {
        padding: 6px 12px;
        font-size: 13px;
        font-weight: 500;
        border-radius: 8px;
    }

    .addCard {
        background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
        border: none;
        color: white;
    }

    .addCard:hover {
        background: linear-gradient(135deg, #138496 0%, #117a8b 100%);
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(23, 162, 184, 0.3);
    }

    .removeCard {
        background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
        border: none;
        color: white;
    }

    .removeCard:hover {
        background: linear-gradient(135deg, #c82333 0%, #bd2130 100%);
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(220, 53, 69, 0.3);
    }

    /* Card Header */
    .card-header {
        padding: 1rem 1.5rem;
    }

    /* Alert Info */
    .alert-light {
        background-color: #f8f9fa;
        border-radius: 8px;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .icon-circle {
            width: 36px;
            height: 36px;
            font-size: 16px;
        }

        .time-input-box {
            margin-bottom: 1rem;
        }

        .btn-group .btn {
            font-size: 12px;
            padding: 5px 10px;
        }

        .card-header h6 {
            font-size: 14px;
        }

        .card-header small {
            font-size: 11px;
        }
    }

    /* Animation */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .attendance-card {
        animation: fadeInUp 0.4s ease-out;
    }

    /* Time Icon dengan opacity custom */
    .time-icon-success {
        background-color: rgba(25, 135, 84, 0.1);
        color: #198754;
    }

    .time-icon-warning {
        background-color: rgba(255, 193, 7, 0.1);
        color: #ffc107;
    }

    .time-icon-info {
        background-color: rgba(13, 202, 240, 0.1);
        color: #0dcaf0;
    }

    .time-icon-danger {
        background-color: rgba(220, 53, 69, 0.1);
        color: #dc3545;
    }

    /* Icon Circle untuk header */
    .icon-circle.bg-primary.bg-opacity-10 {
        background-color: rgba(13, 110, 253, 0.1) !important;
    }

    .btn-dark.logs {
        background: linear-gradient(135deg, #212529 0%, #343a40 100%);
        border: none;
        color: white;
    }

    .btn-dark.logs:hover {
        background: linear-gradient(135deg, #343a40 0%, #495057 100%);
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(33, 37, 41, 0.4);
        color: white;
    }

    /* Style Logs */
    .text-purple {
        color: #6f42c1;
    }

    .bg-purple-subtle {
        background-color: rgba(111, 66, 193, 0.1);
        color: #6f42c1;
    }

    /* Logs Section */
    .logs-section {
        background-color: #f8f9fa;
        border-radius: 12px;
        padding: 1rem;
    }

    .logs-timeline {
        display: flex;
        gap: 1rem;
        overflow-x: auto;
        padding: 1rem 0;
    }

    .log-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        min-width: 100px;
        position: relative;
    }

    .log-item:not(:last-child)::after {
        content: '';
        position: absolute;
        top: 16px;
        left: 50%;
        width: 100%;
        height: 2px;
        background: linear-gradient(to right, #dee2e6 0%, transparent 100%);
        z-index: 0;
    }

    .log-dot {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        border: 3px solid white;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        z-index: 1;
        position: relative;
    }

    .log-content {
        text-align: center;
        margin-top: 0.5rem;
    }

    .log-time {
        font-size: 14px;
        font-weight: 600;
        color: #212529;
        margin-bottom: 2px;
    }

    .log-date {
        font-size: 11px;
        color: #6c757d;
        margin-bottom: 4px;
    }

    .log-label {
        font-size: 12px;
        color: #495057;
        font-weight: 500;
    }

    /* Scrollbar Styling */
    .logs-timeline::-webkit-scrollbar {
        height: 6px;
    }

    .logs-timeline::-webkit-scrollbar-track {
        background: #e9ecef;
        border-radius: 10px;
    }

    .logs-timeline::-webkit-scrollbar-thumb {
        background: #6f42c1;
        border-radius: 10px;
    }
</style>

<div class="container-fluid py-4">
    <!-- HEADER SECTION -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm attendance-header-card">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <div class="d-flex align-items-center">
                                <div>
                                    <h3 class="mb-1 fw-bold text-dark">Tambah Data Absensi</h3>
                                    <p class="mb-0 text-muted small">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Kelola dan catat kehadiran karyawan
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <form action="<?= base_url($role . '/tambahDataAbsensiStore') ?>" method="post">
        <div class="row">
            <div class="col-xl-12 col-sm-12 mb-4">
                <div class="card shadow-sm border-0">
                    <div class="card-body position-relative">
                        <div class="row">
                            <div class="col-md-6">
                                <label for="attendance_date" class="form-label">Tanggal Absensi tes</label>
                                <input type="date" class="form-control" id="attendance_date" name="attendance_date" required>
                            </div>
                            <div class="col-md-6">
                                <button type="button" class="btn btn-info" id="btnCariKaryawan"><i class="fas fa-search"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="cardContainer">
            <div class="card shadow-sm border-0 attendance-card mb-3">
                <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center py-3">
                    <div class="d-flex align-items-center">
                        <div class="icon-circle text-primary me-3">
                            <i class="fas fa-user-clock"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 fw-semibold">Data Absensi Karyawan</h6>
                            <small class="text-muted">Isi data kehadiran karyawan</small>
                        </div>
                    </div>
                    <div class="button">
                        <button type="button" class="btn btn-info addCard" title="Tambah Card">
                            <i class="fas fa-plus"></i>
                        </button>
                        <button type="button" class="btn btn-danger removeCard" title="Hapus Card">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-12">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-user text-primary me-2"></i>Pilih Karyawan
                            </label>
                            <select name="employee[]" class="form-select select2 employee_select" required>
                                <input type="hidden" name="employee_name[]" class="employee_name">
                                <option value="">-- Pilih Karyawan --</option>
                            </select>
                        </div>
                    </div>

                    <!-- Time Inputs Grid -->
                    <div class="row g-3">
                        <div class="col-md-6 col-lg-3">
                            <div class="time-input-box border rounded-3 p-3 h-100">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="time-icon time-icon-success me-2">
                                        <i class="fas fa-sign-in-alt"></i>
                                    </div>
                                    <label class="form-label mb-0 fw-semibold small">Waktu Masuk</label>
                                </div>
                                <input type="datetime-local" class="form-control border-0 px-0" name="in_time[]">
                            </div>
                        </div>

                        <div class="col-md-6 col-lg-3">
                            <div class="time-input-box border rounded-3 p-3 h-100">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="time-icon time-icon-warning me-2">
                                        <i class="fas fa-coffee"></i>
                                    </div>
                                    <label class="form-label mb-0 fw-semibold small">Waktu Istirahat</label>
                                </div>
                                <input type="datetime-local" class="form-control border-0 px-0" name="break_out_time[]">
                            </div>
                        </div>

                        <div class="col-md-6 col-lg-3">
                            <div class="time-input-box border rounded-3 p-3 h-100">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="time-icon time-icon-info me-2">
                                        <i class="fas fa-undo"></i>
                                    </div>
                                    <label class="form-label mb-0 fw-semibold small">Waktu Kembali</label>
                                </div>
                                <input type="datetime-local" class="form-control border-0 px-0" name="break_in_time[]">
                            </div>
                        </div>

                        <div class="col-md-6 col-lg-3">
                            <div class="time-input-box border rounded-3 p-3 h-100">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="time-icon time-icon-danger me-2">
                                        <i class="fas fa-sign-out-alt"></i>
                                    </div>
                                    <label class="form-label mb-0 fw-semibold small">Waktu Pulang</label>
                                </div>
                                <input type="datetime-local" class="form-control border-0 px-0" name="out_time[]">
                            </div>
                        </div>
                    </div>

                    <!-- LOGS SECTION -->
                    <div class="logs-section mt-4 pt-3 border-top">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h6 class="mb-0 fw-semibold">
                                <i class="fas fa-history text-purple me-2"></i>Riwayat Aktivitas
                            </h6>
                            <span class="badge bg-purple-subtle text-purple logs-count">0 aktivitas</span>
                        </div>

                        <div class="logs-timeline"></div>
                    </div>

                </div>
            </div>

        </div>


        <button type="submit" class="btn btn-primary">Simpan</button>
    </form>
</div>

<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<?php if (session()->getFlashdata('error')) : ?>
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            html: `<?= nl2br(session()->getFlashdata('error')); ?>`,
        });
    </script>
<?php endif; ?>

<?php if (session()->getFlashdata('success')) : ?>
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Berhasil',
            text: '<?= session()->getFlashdata('success'); ?>',
        });
    </script>
<?php endif; ?>



<script>
    let optionsHtml = '<option></option>';

    $(document).ready(function() {
        $('.select2').select2({
            width: '100%'
        });

        // Event tombol tambah card
        $(document).on('click', '.addCard', function() {
            let original = $(this).closest('.attendance-card');
            let clone = original.clone(false);

            // kosongkan value di clone
            clone.find('select').each(function() {
                $(this)
                    .removeClass('select2-hidden-accessible')
                    .removeAttr('data-select2-id')
                    .val(null);

                const $maybeContainer = $(this).next('.select2');
                if ($maybeContainer.length) $maybeContainer.remove();
            });

            clone.find('.employee_select').html(optionsHtml);
            clone.find('.employee_select').val(null);

            // masukkan clone ke container
            $('#cardContainer').append(clone);

            clone.find('.employee_select').each(function() {
                $(this).html(optionsHtml);
            });

            clone.find('.select2').each(function() {
                if ($(this).hasClass('select2-hidden-accessible')) {
                    $(this).select2('destroy');
                }
                $(this).select2({
                    width: '100%'
                });
            });

            //reset input datetime
            clone.find('input[type="datetime-local"]').val('');

            // reset logs in the cloned card
            clone.removeData('logs');
            clone.find('.logs-timeline').html('');
            clone.find('.logs-count').text('0 aktivitas');
        });

        // Event tombol hapus card
        $(document).on('click', '.removeCard', function() {
            if ($('.attendance-card').length === 1) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Tidak Bisa Hapus',
                    text: 'Minimal harus ada 1 data absensi.'
                });
                return;
            }
            $(this).closest('.attendance-card').remove();
        });

    });

    document.getElementById('btnCariKaryawan').addEventListener('click', function() {
        const date = document.getElementById('attendance_date').value;
        const select = document.getElementById('employee_select');

        // Clear hanya select karyawan, bukan semua select2
        $(select).empty().trigger('change');

        if (!date) {
            Swal.fire({
                icon: 'warning',
                title: 'Tanggal belum dipilih',
                text: 'Silakan pilih tanggal absensi terlebih dahulu.'
            });
            return;
        }

        Swal.fire({
            title: 'Mencari data...',
            text: 'Harap tunggu sebentar',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });

        fetch("<?= base_url($role . '/getKaryawanByTglAbsen') ?>?date=" + encodeURIComponent(date))
            .then(res => res.json())
            .then(data => {
                Swal.close();

                if (data.length === 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Tidak Ada Data',
                        text: 'Karyawan tidak ditemukan pada tanggal tersebut.'
                    });
                    return;
                }

                optionsHtml = '<option></option>';
                data.forEach(function(k) {
                    const val = (k.nik !== undefined && k.nik !== null) ? k.nik.toString() : '';
                    const nama_karyawan = (k.employee_name !== undefined && k.employee_name !== null) ? k.employee_name : 'Nama Tidak Diketahui';
                    optionsHtml += `<option value="${val}" data-nama="${nama_karyawan}">${nama_karyawan} (${val})</option>`;
                });

                $('.employee_select').each(function() {
                    $(this).html(optionsHtml);
                    $(this).trigger('change');
                });

                $('#cardContainer').find('.attendance-card').each(function() {
                    $(this).find('.employee_select').each(function() {
                        if ($(this).children('option').length <= 1) {
                            $(this).html(optionsHtml);
                            $(this).trigger('change');
                        }
                    });
                });

                Swal.fire({
                    icon: 'success',
                    title: 'Data ditemukan',
                    text: 'Silakan pilih karyawan dari daftar.'
                });
            })
            .catch(err => {
                Swal.fire({
                    icon: 'error',
                    title: 'Kesalahan',
                    text: 'Gagal mengambil data.'
                });
                console.error(err);
            });
    });

    $(document).on('change', '.employee_select', function() {
        let nik = $(this).val();
        let date = $('#attendance_date').val();
        let card = $(this).closest('.attendance-card');
        const nama = $(this).find(':selected').data('nama') || '';

        $(this).closest('.card-body').find('.employee_name').val(nama);

        if (!nik || !date) {
            return;
        }

        fetch("<?= base_url($role . '/getLogAbsensiByNIKAndDate') ?>?nik=" + nik + "&date=" + date)
            .then(res => res.json())
            .then(data => {

                // simpan data logs di card
                card.data('logs', data);

                // ambil container logs
                const timeline = card.find('.logs-timeline');
                const badge = card.find('.logs-count');

                // clear timeline
                timeline.html('');

                if (!data || data.length === 0) {
                    badge.text("0 aktivitas");
                    timeline.html(`<div class="text-muted small">Tidak ada aktivitas.</div>`);
                    return;
                }

                // update badge jumlah log
                badge.text(`${data.length} aktivitas`);

                // generate logs
                data.forEach((log, index) => {

                    const warnaList = ["bg-secondary"];
                    let color = warnaList[index % warnaList.length];

                    const item = `
                    <div class="log-item">
                        <div class="log-dot ${color}"></div>
                        <div class="log-content">
                        <div class="log-date">${log.log_date}</div>
                            <div class="log-time">${log.log_time}</div>
                            <div class="log-label">${log.label ?? ''}</div>
                        </div>
                    </div>
                `;

                    timeline.append(item);
                });

            })
            .catch(err => {
                console.error(err);
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal mengambil log',
                    text: 'Terjadi kesalahan mengambil data log absensi.'
                });
            });
    });
</script>


<?php $this->endSection(); ?>