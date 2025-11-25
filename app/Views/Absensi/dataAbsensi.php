<?php $this->extend('layout/template'); ?>
<?php $this->section('content'); ?>

<div class="container-fluid">
    <div class="row my-2">
        <div class="col-xl-12 col-sm-12 mb-xl-0 mb-2">
            <div class="card">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="font-weight-bolder mb-0">
                                <a href="" # class="btn bg-gradient-info">
                                    <!-- icon data karyawan -->
                                    <i class="fas fa-user-tie text-lg opacity-10" aria-hidden="true"></i>
                                </a>
                                Data Log Absensi
                            </h4>
                        </div>
                        <div>
                            <div class="d-flex justify-content-between">

                                <a href="<?= base_url($role . '/downloadTemplateKaryawan') ?>"
                                    class="btn bg-gradient-success me-2">
                                    <!-- icon download -->
                                    <i class="fas fa-download text-lg opacity-10" aria-hidden="true"></i>
                                    Template Excell
                                </a>
                                <!-- <a
                                    class="btn bg-gradient-info add-btn" data-bs-toggle="modal" data-bs-target="#addKaryawan">
                                
                                    <i class="fas fa-user-plus text-lg opacity-10" aria-hidden="true"></i>
                                    Data Karyawan
                                </a> -->
                                <div> &nbsp;</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade  bd-example-modal-lg" id="addKaryawan" tabindex="-1" role="dialog" aria-labelledby="addKaryawan" aria-hidden="true">
            <div class="modal-dialog  modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Tambah Karyawan</h5>
                        <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="<?= base_url($role . '/karyawanStore'); ?>" method="post">
                            <div class="row">
                                <div class="col-lg-12 col-sm-12">
                                    <div class="form-group mb-2">
                                        <label for="nik">NIK</label>
                                        <input type="text" class="form-control" name="nik" id="nik" required>
                                    </div>
                                    <div class="form-group mb-2">
                                        <label for="kode_kartu">Kode Kartu</label>
                                        <input type="text" class="form-control" name="kode_kartu" id="kode_kartu" required>
                                    </div>
                                    <div class="form-group mb-2">
                                        <label for="nama_karyawan">Nama Karyawan</label>
                                        <input type="text" class="form-control" name="nama_karyawan" id="nama_karyawan" required>
                                    </div>
                                    <div class="form-group mb-2">
                                        <label for="shift">Shift</label>
                                        <select name="shift" id="shift" class="form-control" required>
                                            <option value="">Pilih Shift</option>
                                            <option value="A">A</option>
                                            <option value="B">B</option>
                                            <option value="C">C</option>
                                            <option value="Non Shift">Non Shift</option>
                                        </select>
                                    </div>
                                    <div class="form-group mb-2">
                                        <label for="jenis_kelamin">Jenis Kelamin</label>
                                        <select name="jenis_kelamin" id="jenis_kelamin" class="form-control" required>
                                            <option value="">Pilih Jenis Kelamin</option>
                                            <option value="L">Laki-laki</option>
                                            <option value="P">Perempuan</option>
                                        </select>
                                    </div>
                                    <div class="form-group mb-2">
                                        <label for="tgl_lahir">Tanggal Lahir</label>
                                        <input type="date" class="form-control" name="tgl_lahir" id="tgl_lahir" required>
                                    </div>
                                    <div class="form-group mb-2">
                                        <label for="tgl_masuk">Tanggal Masuk</label>
                                        <input type="date" class="form-control" name="tgl_masuk" id="tgl_masuk" required>
                                    </div>
                                    <div class="form-group mb-2">
                                        <label for="status_aktif">Status</label>
                                        <select name="status_aktif" id="status_aktif" class="form-control" required>
                                            <option value="">Pilih Status</option>
                                            <option value="Aktif">Aktif</option>
                                            <option value="Tidak Aktif">Tidak Aktif</option>
                                        </select>
                                    </div>

                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn bg-gradient-info">Save</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xl-12 col-sm-12 mb-xl-0 mb-2 mt-2">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">
                        Import Data Absensi
                    </h5>
                    <!-- form import  data karyawan -->
                    <form action="<?= base_url('Absensi/AbsensiImport') ?>" method="post"
                        enctype="multipart/form-data">
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
    </div>
    <div class="row mt-1">
        <div class="col-xl-12 col-sm-12 mb-xl-0 mb-4 mt-2">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">
                        Tabel Data Logs Absen
                    </h4>
                    <div class="table-responsive">
                        <table id="dataLogTable" class="table table-striped table-hover table-bordered w-100">
                            <thead>
                                <th>TerminalId</th>
                                <th>NIK</th>
                                <th>CardNo</th>
                                <th>EmployeeName</th>
                                <th>Department</th>
                                <th>logDate</th>
                                <th>logTime</th>
                                <th>Admin</th>
                                <th>Aksi</th>
                            </thead>
                            <tbody>
                                <?php if (!empty($log)) : ?>
                                    <?php foreach ($log as $data) : ?>
                                        <tr>
                                            <td><?= $data['terminal_id'] ?></td>
                                            <td><?= $data['nik'] ?></td>
                                            <td><?= $data['card_no'] ?></td>
                                            <td><?= $data['employee_name'] ?></td>
                                            <td><?= $data['department'] ?></td>

                                            <td><?= $data['log_date'] ?></td>

                                            <td><?= $data['log_time'] ?></td>
                                            <td><?= $data['admin'] ?></td>
                                            <td>
                                                <button
                                                    class="btn btn-sm btn-info btn-edit-employee">
                                                    Edit
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach ?>
                                <?php else : ?>
                                    <tr>
                                        <td colspan="14" class="text-center">No Karyawan found</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            // Initialize DataTable with export options
            $('#dataLogTable').DataTable({});

            // Flash message SweetAlerts
            <?php if (session()->getFlashdata('success')) : ?>
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    html: '<?= session()->getFlashdata('success') ?>',
                });
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')) : ?>
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    html: '<?= session()->getFlashdata('error') ?>',
                });
            <?php endif; ?>
        });
    </script>

    <script>
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