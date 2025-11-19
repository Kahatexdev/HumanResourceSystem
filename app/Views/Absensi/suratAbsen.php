<?php $this->extend('layout/template'); ?>
<?php $this->section('content'); ?>
<style>
    /* Wrapper khusus modal HRS */
    .modal-hrs .modal-content {
        border-radius: 1rem;
        border: none;
        box-shadow: 0 18px 45px rgba(15, 23, 42, 0.18);
        overflow: hidden;
    }

    .modal-hrs .modal-header {
        background: linear-gradient(135deg, #5e72e4 0%, #825ee4 100%);
        color: #fff;
        border-bottom: none;
        padding: 1rem 1.5rem;
    }

    .modal-hrs .modal-title {
        font-size: 1rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: .5rem;
    }

    .modal-hrs .modal-title-icon {
        width: 32px;
        height: 32px;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.15);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .modal-hrs .modal-body {
        background-color: #f8fafc;
        padding: 1.5rem;
    }

    .modal-hrs .modal-footer {
        border-top: none;
        padding: 0.75rem 1.5rem 1.25rem;
        background-color: #f9fafb;
    }

    /* Card section di dalam modal */
    .modal-hrs .modal-section {
        background: #ffffff;
        border-radius: 0.75rem;
        padding: 1rem 1.25rem;
        border: 1px solid #e5e7eb;
        box-shadow: 0 8px 20px rgba(15, 23, 42, 0.04);
    }

    .modal-hrs .section-title {
        font-size: .8rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .08em;
        color: #64748b;
        margin-bottom: .75rem;
        display: flex;
        align-items: center;
        gap: .4rem;
    }

    .modal-hrs .section-title i {
        font-size: .9rem;
    }

    /* Label & input */
    .modal-hrs .form-group label {
        font-size: .8rem;
        font-weight: 600;
        color: #475569;
        margin-bottom: .25rem;
    }

    .modal-hrs .form-control,
    .modal-hrs select.form-control {
        border-radius: 0.6rem;
        border: 1px solid #e2e8f0;
        font-size: .85rem;
        padding: 0.45rem 0.75rem;
    }

    .modal-hrs .form-control:focus,
    .modal-hrs select.form-control:focus {
        border-color: #5e72e4;
        box-shadow: 0 0 0 1px rgba(94, 114, 228, 0.25);
    }

    .modal-hrs textarea.form-control {
        min-height: 90px;
        resize: vertical;
    }

    /* Select2 di dalam modal biar serasi */
    .modal-hrs .select2-container--default .select2-selection--single {
        border-radius: 0.6rem;
        border: 1px solid #e2e8f0;
        height: 38px;
        padding: 4px 6px;
    }

    .modal-hrs .select2-container--default .select2-selection__rendered {
        font-size: .85rem;
        line-height: 28px;
    }

    .modal-hrs .select2-container--default .select2-selection__arrow {
        height: 32px;
    }
</style>


<div class="container-fluid">
    <div class="row my-2">
        <div class="col-xl-12 col-sm-12 mb-xl-0 mb-2">
            <div class="card">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="font-weight-bolder mb-0 d-flex align-items-center gap-2">
                                <a href="#" class="btn bg-gradient-info">
                                    <!-- icon data surat absen -->
                                    <i class="fas fa-user-tie text-lg opacity-10" aria-hidden="true"></i>
                                </a>
                                <span>Data Surat Absen</span>
                            </h4>
                        </div>
                        <div>
                            <div class="d-flex justify-content-between">

                                <a href="<?= base_url($role . '/downloadTemplateKaryawan') ?>"
                                    class="btn bg-gradient-success me-2">
                                    <i class="fas fa-download text-lg opacity-10" aria-hidden="true"></i>
                                    Template Excel
                                </a>

                                <a class="btn bg-gradient-info add-btn"
                                    data-bs-toggle="modal"
                                    data-bs-target="#addKaryawan">
                                    <i class="fas fa-user-plus text-lg opacity-10" aria-hidden="true"></i>
                                    Surat Absen
                                </a>

                                <div>&nbsp;</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Input Surat Absen -->
        <div class="modal fade bd-example-modal-lg" id="addKaryawan" tabindex="-1"
            aria-labelledby="addKaryawanLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered modal-hrs" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <div class="modal-title d-flex align-items-center" id="addKaryawanLabel">
                            <div class="modal-title-icon">
                                <i class="fas fa-file-signature text-white"></i>
                            </div>
                            <span>Form Input Surat Absen</span>
                        </div>
                        <button type="button" class="btn-close text-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="modal-section mb-2">
                            <div class="section-title">
                                <i class="fas fa-user-tag"></i>
                                Data Karyawan & Jenis Surat
                            </div>

                            <form action="<?= base_url($role . '/letterStore'); ?>" method="post">
                                <?= csrf_field() ?>
                                <div class="row">
                                    <!-- Kode Kartu / Nama Karyawan -->
                                    <div class="col-sm-6">
                                        <div class="form-group mb-2">
                                            <label for="id_employee">Karyawan (Kode Kartu)</label>
                                            <select name="id_employee" id="id_employee" class="form-control" required>
                                                <option value="">Pilih karyawan / kode kartu</option>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Jenis Surat -->
                                    <div class="col-sm-6">
                                        <div class="form-group mb-2">
                                            <label for="letter_type">Jenis Surat</label>
                                            <select name="letter_type" id="letter_type" class="form-control" required>
                                                <option value="">Pilih jenis surat</option>
                                                <option value="IZIN">Izin</option>
                                                <option value="SAKIT">Sakit</option>
                                                <option value="CUTI">Cuti</option>
                                                <option value="DINAS_LUAR">Dinas Luar</option>
                                                <option value="LAINNYA">Lainnya</option>
                                            </select>
                                        </div>
                                    </div>

                                </div>
                        </div>

                        <div class="modal-section">
                            <div class="section-title">
                                <i class="fas fa-calendar-alt"></i>
                                Periode & Keterangan
                            </div>
                            <div class="row">
                                <!-- Range Tanggal -->
                                <div class="col-sm-6">
                                    <div class="form-group mb-2">
                                        <label for="date_from">Tanggal Mulai</label>
                                        <input type="date" class="form-control" name="date_from" id="date_from" required>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group mb-2">
                                        <label for="date_to">Tanggal Selesai</label>
                                        <input type="date" class="form-control" name="date_to" id="date_to" required>
                                    </div>
                                </div>

                                <!-- Keterangan -->
                                <div class="col-sm-12">
                                    <div class="form-group mb-2">
                                        <label for="description">Keterangan</label>
                                        <textarea name="description" id="description" rows="3"
                                            class="form-control"
                                            placeholder="Isi keterangan singkat surat absen"
                                            required></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div> <!-- /.modal-body -->
                    <div class="modal-footer">
                        <button type="button" class="btn bg-gradient-secondary"
                            data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn bg-gradient-info">Save</button>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Import Surat Absen -->
    <!-- <div class="row">
        <div class="col-xl-12 col-sm-12 mb-xl-0 mb-2 mt-2">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">
                        Import Surat Absen
                    </h5>
                    <form action="<?= base_url('Absensi/AbsensiImport') ?>" method="post"
                        enctype="multipart/form-data">
                        <?= csrf_field() ?>
                        <div class="upload-container">
                            <div class="upload-area" id="upload-area">
                                <i class="fas fa-cloud-upload-alt fa-2x"></i>
                                <p>Drag & drop any file here</p>
                                <span>or <label for="file-upload" class="browse-link">browse file</label> from
                                    device</span>
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
    </div> -->

    <!-- Tabel Surat Absen -->
    <div class="row mt-1">
        <div class="col-xl-12 col-sm-12 mb-xl-0 mb-2">
            <div class="card">
                <div class="card-body p-3">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="dataTable">
                            <thead>
                                <tr>
                                    <th>Tgl</th>
                                    <th>Kode Kartu</th>
                                    <th>Nama</th>
                                    <th>Jenis</th>
                                    <th>Ket</th>
                                    <th>Status</th>
                                    <th>Ttl Hari</th>
                                    <th>Input</th>
                                    <th>Penerima</th>
                                    <th>Tgl Terima</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Data akan diisi melalui proses import / query dari controller -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal Approve / Reject Surat Absen -->
    <div class="modal fade" id="modalApproval" tabindex="-1" aria-labelledby="modalApprovalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-hrs">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="modal-title d-flex align-items-center" id="modalApprovalLabel">
                        <div class="modal-title-icon">
                            <i class="fas fa-calendar-check text-white"></i>
                        </div>
                        <span id="approvalModalTitle">Proses Surat Absen</span>
                    </div>
                    <button type="button" class="btn-close text-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="<?= base_url('Absensi/letterUpdateStatus'); ?>" method="post">
                    <?= csrf_field() ?>
                    <div class="modal-body">
                        <div class="modal-section">
                            <div class="section-title">
                                <i class="fas fa-info-circle"></i>
                                Tanggal Proses
                            </div>

                            <input type="hidden" name="id_letter" id="approval_id_letter">
                            <input type="hidden" name="action_type" id="approval_action_type">

                            <div class="form-group mb-2">
                                <label for="action_date" id="label_action_date">Tanggal Proses</label>
                                <input type="date" class="form-control" name="action_date" id="action_date" required>
                            </div>

                            <small class="text-muted" id="approval_helper_text">
                                Pilih tanggal untuk proses surat absen ini.
                            </small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn bg-gradient-info">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>

<!-- Select2 (pastikan jQuery sudah ada di layout) -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function() {
        // SweetAlert Flash Message (pakai json_encode biar aman dari karakter aneh)
        const msgSuccess = <?= json_encode(session()->getFlashdata('success')) ?>;
        const msgError = <?= json_encode(session()->getFlashdata('error')) ?>;

        if (msgSuccess) {
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                html: msgSuccess,
            });
        }

        if (msgError) {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                html: msgError,
            });
        }

        // DataTable
        // DataTable server-side
        $('#dataTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '<?= base_url('Absensi/lettersData'); ?>', // sesuaikan dgn routes
                type: 'GET'
            },
            columns: [{
                    data: 'tgl',
                    name: 'tgl'
                }, // Tgl
                {
                    data: 'kode_kartu',
                    name: 'kode_kartu'
                }, // Kode Kartu
                {
                    data: 'nama',
                    name: 'nama'
                }, // Nama
                {
                    data: 'jenis',
                    name: 'jenis'
                }, // Jenis Surat
                {
                    data: 'ket',
                    name: 'ket'
                }, // Ket
                {
                    data: 'status',
                    name: 'status'
                }, // Status
                {
                    data: 'total_hari',
                    name: 'total_hari'
                }, // Ttl Hari
                {
                    data: 'input',
                    name: 'input'
                }, // Input
                {
                    data: 'penerima',
                    name: 'penerima'
                }, // Penerima
                {
                    data: 'tgl_terima',
                    name: 'tgl_terima'
                }, // Tgl Terima
                {
                    data: 'aksi',
                    name: 'aksi',
                    orderable: false,
                    searchable: false
                } // Aksi
            ],
            order: [
                [0, 'desc']
            ]
        });

        // Select2 di dalam modal
        $('#id_employee').select2({
            dropdownParent: $('#addKaryawan'),
            placeholder: 'Pilih karyawan / kode kartu',
            width: '100%',
            allowClear: true,
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

        // --- Handler Approve / Reject ---
        const modalApprovalEl = document.getElementById('modalApproval');
        const modalApproval = new bootstrap.Modal(modalApprovalEl);

        $('#dataTable').on('click', '.btn-approve', function() {
            const id = $(this).data('id');

            $('#approval_id_letter').val(id);
            $('#approval_action_type').val('APPROVE');

            $('#approvalModalTitle').text('Approve Surat Absen');
            $('#label_action_date').text('Tanggal Approve');
            $('#approval_helper_text').text('Pilih tanggal approve untuk surat absen ini.');

            // default isi hari ini
            const today = new Date().toISOString().slice(0, 10);
            $('#action_date').val(today);

            modalApproval.show();
        });

        $('#dataTable').on('click', '.btn-reject', function() {
            const id = $(this).data('id');

            $('#approval_id_letter').val(id);
            $('#approval_action_type').val('REJECT');

            $('#approvalModalTitle').text('Reject Surat Absen');
            $('#label_action_date').text('Tanggal Reject');
            $('#approval_helper_text').text('Pilih tanggal reject untuk surat absen ini.');

            const today = new Date().toISOString().slice(0, 10);
            $('#action_date').val(today);

            modalApproval.show();
        });
    });

    // Upload area script
    const fileInput = document.getElementById('file-upload');
    const uploadArea = document.getElementById('upload-area');

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
</script>

<?php $this->endSection(); ?>