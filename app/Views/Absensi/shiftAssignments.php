<?php $this->extend('layout/template'); ?>
<?php $this->section('content'); ?>

<style>
    /* Modal HRS modern */
    .modal-hrs {
        border-radius: 1rem;
        border: none;
        box-shadow: 0 18px 45px rgba(15, 23, 42, 0.18);
    }

    .modal-hrs .modal-header .modal-title {
        font-size: 1.05rem;
    }

    .card-section {
        border-radius: 0.75rem;
        border: 1px solid #e5e7eb;
        box-shadow: 0 8px 20px rgba(15, 23, 42, 0.04);
    }

    .card-group {
        border-radius: 0.75rem;
        border-left: 3px solid #0ea5e9;
        /* aksen biru tipis */
    }

    .card-group .form-label {
        font-size: 0.78rem;
        text-transform: none;
    }

    .form-text {
        font-size: 0.7rem;
    }

    /* Select2 di dalam modal biar full width & rapi */
    .select2-container--default .select2-selection--multiple {
        border-radius: 0.5rem;
        padding: 0.25rem 0.4rem;
        border-color: #d1d5db;
        min-height: 2.6rem;
    }

    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        border-radius: 999px;
        border: none;
        padding: 0.1rem 0.6rem;
        margin-top: 0.2rem;
    }
</style>
<?php
// Convenience URLs for JS
$storeUrl  = base_url($role . '/storeShiftAssignment');
$updateUrl = base_url($role . '/updateShiftAssignment');
?>

<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="row mb-3">
        <div class="col-xl-12 col-sm-12 mb-xl-0 mb-2">
            <div class="card">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="font-weight-bolder mb-0 d-flex align-items-center gap-2">
                                <a href="#" class="btn bg-gradient-info">
                                    <!-- icon data Jam Kerja Karyawan -->
                                    <i class="fas fa-clock text-lg opacity-10" aria-hidden="true"></i>
                                </a>
                                <span>Data Jam Kerja Karyawan</span>
                            </h4>
                        </div>
                        <div>
                            <div class="d-flex justify-content-between align-items-center">
                                <a href="<?= base_url($role . '/exportKaryawan') ?>" class="btn bg-gradient-primary me-2">
                                    <i class="fas fa-file-excel text-lg opacity-10" aria-hidden="true"></i>
                                    Export Excel
                                </a>
                                <a href="<?= base_url($role . '/downloadTemplateJamKerja') ?>" class="btn bg-gradient-success me-2">
                                    <i class="fas fa-download text-lg opacity-10" aria-hidden="true"></i>
                                    Template Excel
                                </a>
                                <button type="button" class="btn bg-gradient-info add-btn" data-bs-toggle="modal" data-bs-target="#addShiftAssignment">
                                    <i class="fas fa-user-plus text-lg opacity-10" aria-hidden="true"></i>
                                    Jam Kerja
                                </button>
                                <div>&nbsp;</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- modal input jam kerja -->
        <!-- Modal Input Jam Kerja -->
        <div class="modal fade" id="addShiftAssignment" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content modal-hrs">

                    <!-- Header -->
                    <div class="modal-header border-0 pb-0">
                        <div>
                            <h5 class="modal-title fw-semibold d-flex align-items-center gap-2" id="modalTitleJam">
                                <i class="bi bi-clock-history"></i>
                                Tambah Shift / Jam Kerja
                            </h5>
                            <p class="text-muted small mb-0">
                                Atur jam kerja karyawan secara massal dengan tampilan yang rapi dan mudah dibaca.
                            </p>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <form id="formMasterJam" action="<?= esc($storeUrl) ?>" method="post">
                        <?= csrf_field() ?>

                        <div class="modal-body">

                            <!-- id hidden untuk edit -->
                            <input type="hidden" name="id_shift" id="id_shift">

                            <!-- SECTION: Informasi Dasar -->
                            <div class="card card-section mb-3">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div>
                                            <h6 class="mb-0 fw-semibold">Informasi Dasar</h6>
                                            <small class="text-muted">
                                                Pilih karyawan dan tanggal efektif perubahan jam kerja.
                                            </small>
                                        </div>
                                    </div>

                                    <div class="row g-3">
                                        <!-- Nama Karyawan -->
                                        <div class="col-12">
                                            <label for="employee_ids" class="form-label fw-semibold">
                                                Nama Karyawan
                                            </label>
                                            <select class="form-select select-karyawan"
                                                name="employee_ids[]"
                                                id="employee_ids"
                                                multiple
                                                required>
                                                <!-- option akan di-load via AJAX Select2 -->
                                            </select>
                                            <div class="form-text">
                                                Gunakan kolom pencarian untuk mencari karyawan, dan pilih lebih dari satu bila perlu.
                                            </div>
                                        </div>

                                        <!-- Tanggal Pindah Jam -->
                                        <div class="col-md-4">
                                            <label for="effective_date" class="form-label fw-semibold">
                                                Tanggal Efektif
                                            </label>
                                            <input type="date"
                                                class="form-control"
                                                name="effective_date"
                                                id="effective_date"
                                                required>
                                            <div class="form-text">
                                                Tanggal mulai jam kerja baru diberlakukan.
                                            </div>
                                        </div>

                                        <!-- Catatan -->
                                        <div class="col-md-8">
                                            <label for="note" class="form-label fw-semibold">
                                                Catatan (opsional)
                                            </label>
                                            <textarea class="form-control"
                                                name="note"
                                                id="note"
                                                rows="2"
                                                placeholder="Contoh: Penyesuaian jam kerja karena pergantian shift mingguan."></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- SECTION: Jam Kerja -->
                            <div class="card card-section">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div>
                                            <h6 class="mb-0 fw-semibold">Jam Kerja</h6>
                                            <small class="text-muted">
                                                Tambahkan satu atau beberapa pola jam kerja. Sistem akan menyimpan semuanya.
                                            </small>
                                        </div>
                                        <button type="button" class="btn btn-sm btn-outline-info" id="btnAddWorkGroup">
                                            <i class="bi bi-plus-circle"></i> Tambah Jam
                                        </button>
                                    </div>

                                    <!-- CONTAINER ADD MORE -->
                                    <div id="workGroupContainer" class="d-flex flex-column gap-3">
                                        <div class="work-group-item">
                                            <div class="card card-group mb-0">
                                                <div class="card-body">
                                                    <div class="row g-3 align-items-end work-group">

                                                        <div class="col-md-3">
                                                            <label class="form-label fw-semibold">Jam Masuk</label>
                                                            <input type="time"
                                                                class="form-control"
                                                                name="start_time[]"
                                                                required>
                                                        </div>

                                                        <div class="col-md-3">
                                                            <label class="form-label fw-semibold">Jam Pulang</label>
                                                            <input type="time"
                                                                class="form-control"
                                                                name="end_time[]"
                                                                required>
                                                        </div>

                                                        <div class="col-md-2">
                                                            <label class="form-label fw-semibold">Istirahat (menit)</label>
                                                            <input type="number"
                                                                class="form-control"
                                                                name="break_time[]"
                                                                min="0"
                                                                value="0">
                                                        </div>

                                                        <div class="col-md-2">
                                                            <label class="form-label fw-semibold">Toleransi (menit)</label>
                                                            <input type="number"
                                                                class="form-control"
                                                                name="grace_min[]"
                                                                min="0"
                                                                value="0">
                                                        </div>

                                                        <div class="col-md-2 text-md-end">
                                                            <button type="button"
                                                                class="btn btn-outline-danger btn-remove-group mt-2 mt-md-0">
                                                                <i class="bi bi-trash"></i>
                                                                Hapus
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div> <!-- /.work-group-item -->
                                    </div> <!-- /#workGroupContainer -->

                                </div>
                            </div>

                        </div><!-- /.modal-body -->

                        <!-- Footer -->
                        <div class="modal-footer border-0 pt-3">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                Batal
                            </button>
                            <button type="submit" class="btn bg-gradient-info" id="btnSaveJam">
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-12 col-sm-12 mb-xl-0 mb-2 mt-2">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Import Data Jam Kerja Karyawan</h5>
                    <!-- form import  data karyawan -->
                    <form action="<?= base_url('Absensi/storeUploadTemplate') ?>" method="post" enctype="multipart/form-data">
                        <?= csrf_field() ?>
                        <div class="upload-container">
                            <div class="upload-area" id="upload-area">
                                <i class="fas fa-cloud-upload-alt fa-2x"></i>
                                <p>Drag & drop any file here</p>
                                <span>or <label for="file-upload" class="browse-link">browse file</label> from device</span>
                                <input type="file" id="file-upload" class="file-input" name="file" hidden required>
                            </div>
                            <button type="submit" class="upload-button w-100 mt-3">
                                <i class="fas fa-upload"></i> Upload
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table Card -->
    <div class="row mt-1">
        <div class="col-xl-12 col-sm-12 mb-xl-0 mb-4 mt-2">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Tabel Jam Kerja Karyawan</h4>
                    <div class="table-responsive">
                        <table id="shiftTable" class="table table-striped table-hover table-bordered w-100">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>NIK</th>
                                    <th>Kode Kartu</th>
                                    <th>Nama Karyawan</th>
                                    <th>Nama Shift</th>
                                    <th>Jam Kerja</th>
                                    <th>Istirahat (menit)</th>
                                    <th>Toleransi (menit)</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <?php
                            // ---- Grouping per karyawan (gunakan id_employee kalau ada) ----
                            $groups = [];
                            if (!empty($shift) && is_array($shift)) {
                                foreach ($shift as $r) {
                                    $empKey = $r['id_employee'] ?? (($r['nik'] ?? '-') . '|' . ($r['employee_code'] ?? '-') . '|' . ($r['employee_name'] ?? '-'));
                                    if (!isset($groups[$empKey])) {
                                        $groups[$empKey] = [
                                            'id_employee'   => $r['id_employee'] ?? null,
                                            'nik'           => $r['nik'] ?? '-',
                                            'employee_code' => $r['employee_code'] ?? '-',
                                            'employee_name' => $r['employee_name'] ?? '-',
                                            'rows'          => []
                                        ];
                                    }
                                    $groups[$empKey]['rows'][] = [
                                        'id_shift'    => $r['id_shift'] ?? null,
                                        'shift_name'  => $r['shift_name'] ?? '-',
                                        'start_time'  => $r['start_time'] ?? '-',
                                        'end_time'    => $r['end_time'] ?? '-',
                                        'break_time'  => $r['break_time'] ?? '0',
                                        'grace_min'   => $r['grace_min'] ?? '0',
                                    ];
                                }
                            }
                            // Siapkan data JS untuk child rows
                            $groupsForJs = $groups; // aman, kita encode JSON di bawah
                            ?>

                            <tbody>
                                <?php if (!empty($groups)) : ?>
                                    <?php $no = 1;
                                    foreach ($groups as $key => $g):
                                        // string tersembunyi agar search juga menemukan isi shift
                                        $searchIndex = $g['nik'] . ' ' . $g['employee_code'] . ' ' . $g['employee_name'] . ' '
                                            . implode(' ', array_map(function ($rr) {
                                                return ($rr['shift_name'] . ' ' . $rr['start_time'] . ' ' . $rr['end_time'] . ' ' . $rr['break_time'] . ' ' . $rr['grace_min']);
                                            }, $g['rows']));
                                    ?>
                                        <tr data-group-key="<?= esc($key) ?>">
                                            <td><?= $no++ ?></td>
                                            <td><?= esc($g['nik']) ?></td>
                                            <td><?= esc($g['employee_code']) ?></td>
                                            <td><?= esc($g['employee_name']) ?></td>

                                            <!-- kolom shift/jam kosong di parent; detail muncul saat expand -->
                                            <td class="text-muted">—</td>
                                            <td class="text-muted">—</td>
                                            <td class="text-muted">—</td>
                                            <td class="text-muted">—</td>

                                            <td class="d-flex gap-1">
                                                <button type="button" class="btn btn-sm btn-outline-info btn-expand">
                                                    <i class="fas fa-chevron-down me-1"></i> Detail
                                                </button>

                                                <?php // contoh aksi di parent (opsional) 
                                                ?>
                                                <form action="<?= base_url($role . '/deleteShift') ?>" method="post" class="d-inline">
                                                    <?= csrf_field() ?>
                                                    <input type="hidden" name="id_employee" value="<?= esc($g['id_employee'] ?? '') ?>">
                                                    <button type="submit" class="btn bg-gradient-danger" title="Hapus Data Shift ">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>

                                            <!-- elemen tersembunyi agar search DataTables menemukan teks shift -->
                                            <td class="d-none search-index"><?= esc($searchIndex) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="10" class="text-center">No Data found</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- select2 -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(function() {
        $('#employee_ids').select2({
            dropdownParent: $('#addShiftAssignment'),
            placeholder: 'Pilih satu atau lebih karyawan',
            width: '100%',
            allowClear: true,
            closeOnSelect: false,
            ajax: {
                url: '<?= base_url('Absensi/getEmployeeNames') ?>',
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        q: params.term || '' // keyword pencarian
                    };
                },
                processResults: function(data) {
                    // data = { results: [ {id, text}, ... ] }
                    return {
                        results: data.results
                    };
                },
                cache: true
            }
        });
    });
</script>

<script>
    $(document).ready(function() {

        function toggleRemoveButtons() {
            const total = $('#workGroupContainer .work-group-item').length;
            if (total <= 1) {
                $('#workGroupContainer .btn-remove-group').addClass('d-none');
            } else {
                $('#workGroupContainer .btn-remove-group').removeClass('d-none');
            }
        }

        // awal: kalau cuma 1 row, hide tombol hapus
        toggleRemoveButtons();

        // Tambah group baru
        $('#btnAddWorkGroup').on('click', function() {
            const $last = $('#workGroupContainer .work-group-item').last();
            const $clone = $last.clone();

            // kosongkan nilai input di clone
            $clone.find('input[type="time"]').val('');
            $clone.find('input[type="number"]').val(0);

            // append ke container
            $('#workGroupContainer').append($clone);

            toggleRemoveButtons();
        });

        // Hapus group
        $(document).on('click', '.btn-remove-group', function() {
            const total = $('#workGroupContainer .work-group-item').length;
            if (total > 1) {
                $(this).closest('.work-group-item').remove();
            }
            toggleRemoveButtons();
        });

        // Optional: reset ke 1 group setiap modal dibuka
        $('#addShiftAssignment').on('show.bs.modal', function() {
            const $container = $('#workGroupContainer');
            const $first = $container.find('.work-group-item').first().clone();

            // clear value
            $first.find('input[type="time"]').val('');
            $first.find('input[type="number"]').val(0);

            $container.html($first);
            toggleRemoveButtons();
        });
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const msgSuccess = <?= json_encode(session()->getFlashdata('success')) ?>;
        const msgError = <?= json_encode(session()->getFlashdata('error')) ?>;
        const msgWarn = <?= json_encode(session()->getFlashdata('warning')) ?>;
        const errDetail = <?= json_encode(session()->getFlashdata('error_detail')) ?>; // array atau null

        // helper sanitize sederhana
        const esc = s => String(s ?? '').replace(/[&<>"']/g, m => ({
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            '\'': '&#39;'
        } [m]));

        if (msgSuccess) {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                html: esc(msgSuccess),
            });
        }

        if (msgError || (Array.isArray(errDetail) && errDetail.length)) {
            let html = '';
            if (msgError) html += `<p>${esc(msgError)}</p>`;
            if (Array.isArray(errDetail) && errDetail.length) {
                html += '<ul style="text-align:left;margin-left:1rem">';
                errDetail.forEach(e => html += `<li>${esc(e)}</li>`);
                html += '</ul>';
            }
            Swal.fire({
                icon: 'error',
                title: 'Gagal Memproses',
                html: html
            });
        }

        if (msgWarn) {
            Swal.fire({
                icon: 'warning',
                title: 'Perhatian',
                html: esc(msgWarn),
            });
        }
    });
</script>

<script>
    $(function() {
        // Data grup dari PHP -> JS
        const groups = <?= json_encode($groupsForJs, JSON_UNESCAPED_UNICODE) ?>;

        // Helper: render HTML child table
        function renderChildHTML(group) {
            const rows = group.rows || [];
            let html = `
            <div class="p-2">
              <div class="fw-bold mb-2">Detail Shift: ${group.employee_name} (NIK: ${group.nik}, Kode: ${group.employee_code})</div>
              <div class="table-responsive">
                <table class="table table-sm table-bordered mb-0">
                  <thead>
                    <tr>
                      <th style="width:26%">Nama Shift</th>
                      <th style="width:28%">Jam Kerja</th>
                      <th style="width:18%">Istirahat (menit)</th>
                      <th style="width:18%">Toleransi (menit)</th>
                      <th style="width:10%">Aksi</th>
                    </tr>
                  </thead>
                  <tbody>
        `;
            rows.forEach(rr => {
                const jam = `${rr.start_time} - ${rr.end_time}`;
                html += `
                <tr>
                  <td>${$('<div>').text(rr.shift_name).html()}</td>
                  <td>${$('<div>').text(jam).html()}</td>
                  <td>${$('<div>').text(rr.break_time).html()}</td>
                  <td>${$('<div>').text(rr.grace_min).html()}</td>
                  <td>
                    <button type="button"
                        class="btn btn-sm btn-info btn-edit-master-jam"
                        data-id="${rr.id_shift ?? ''}"
                        data-shift_name="${$('<div>').text(rr.shift_name).html()}"
                        data-start="${rr.start_time ?? ''}"
                        data-end="${rr.end_time ?? ''}"
                        data-break="${rr.break_time ?? 0}"
                        data-grace="${rr.grace_min ?? 0}">
                        Edit
                    </button>
                  </td>
                </tr>
            `;
            });
            html += `
                  </tbody>
                </table>
              </div>
            </div>
        `;
            return html;
        }

        const table = $('#shiftTable').DataTable({
            pageLength: 25,
            responsive: true,
            order: [
                [3, 'asc']
            ], // sort by Nama Karyawan
            columnDefs: [{
                    targets: 0,
                    orderable: false
                }, // kolom No
                {
                    targets: 9,
                    visible: false,
                    searchable: true
                } // kolom hidden search-index
            ]
        });

        // Auto-renumber kolom No saat halaman/ganti urut/cari
        function renumber() {
            let i = 1;
            table.rows({
                page: 'current'
            }).every(function() {
                const d = this.node();
                $('td:eq(0)', d).html(i++);
            });
        }
        table.on('order.dt search.dt draw.dt', renumber);
        renumber();

        // Toggle Expand
        $('#shiftTable').on('click', '.btn-expand', function() {
            const tr = $(this).closest('tr');
            const row = table.row(tr);
            const key = tr.data('group-key');
            const data = groups[key];

            if (!data) return;

            if (row.child.isShown()) {
                row.child.hide();
                tr.removeClass('shown');
                $(this).html('<i class="fas fa-chevron-down me-1"></i> Detail');
            } else {
                // tutup semua child lain
                table.rows('.shown').every(function() {
                    this.child.hide();
                    $(this.node()).removeClass('shown');
                });
                $('.btn-expand').html('<i class="fas fa-chevron-down me-1"></i> Detail');

                row.child(renderChildHTML(data)).show();
                tr.addClass('shown');
                $(this).html('<i class="fas fa-chevron-up me-1"></i> Tutup');
            }
        });

    });
</script>


<script>
    const fileInput = document.getElementById('file-upload');
    const uploadArea = document.getElementById('upload-area');

    if (fileInput && uploadArea) {
        fileInput.addEventListener('change', (event) => {
            const fileName = event.target.files[0] ? event.target.files[0].name : "No file selected";
            uploadArea.querySelector('p').textContent = `Selected File: ${fileName}`;
        });

        uploadArea.addEventListener('dragover', (event) => {
            event.preventDefault();
            uploadArea.style.backgroundColor = "#e6f5ff";
        });

        uploadArea.addEventListener('dragleave', () => {
            uploadArea.style.backgroundColor = "#ffffff";
        });

        uploadArea.addEventListener('drop', (event) => {
            event.preventDefault();
            fileInput.files = event.dataTransfer.files;
            const fileName = event.dataTransfer.files[0] ? event.dataTransfer.files[0].name : "No file selected";
            uploadArea.querySelector('p').textContent = `Selected File: ${fileName}`;
        });
    }
</script>


<?php $this->endSection(); ?>