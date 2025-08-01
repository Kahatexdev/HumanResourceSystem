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
                                Data Karyawan
                            </h4>
                        </div>
                        <div>
                            <div class="d-flex justify-content-between">

                                <a href="<?= base_url($role . '/exportKaryawan/' . $area) ?>"
                                    class="btn bg-gradient-primary me-2">
                                    <!-- icon download -->
                                    <i class="fas fa-file-excel text-lg opacity-10" aria-hidden="true"></i>
                                    Export Excel
                                </a>
                                <a href="<?= base_url($role . '/downloadTemplateKaryawan') ?>"
                                    class="btn bg-gradient-success me-2">
                                    <!-- icon download -->
                                    <i class="fas fa-download text-lg opacity-10" aria-hidden="true"></i>
                                    Template Excel
                                </a>
                                <a
                                    class="btn bg-gradient-info add-btn" data-bs-toggle="modal" data-bs-target="#addKaryawan">
                                    <!-- icon tambah karyawan-->
                                    <i class="fas fa-user-plus text-lg opacity-10" aria-hidden="true"></i>
                                    Data Karyawan
                                </a>
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
                                        <label for="libur">Libur</label>
                                        <select name="libur" id="libur" class="form-control" required>
                                            <option value="">Pilih Hari Libur</option>
                                            <?php foreach ($day as $row) : ?>
                                                <option value="<?= $row['id_day'] ?>">
                                                    <?= $row['day_name'] ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="form-group mb-2">
                                        <label for="libur_tambahan">Libur Tambahan</label>
                                        <select name="libur_tambahan" id="libur_tambahan" class="form-control">
                                            <option value="">Pilih Hari Libur</option>
                                            <?php foreach ($day as $row) : ?>
                                                <option value="<?= $row['id_day'] ?>">
                                                    <?= $row['day_name'] ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="form-group mb-2">
                                        <label for="warna_baju">Warna Baju</label>
                                        <select name="warna_baju" id="warna_baju" class="form-control" required>
                                            <option value="">Pilih Warna Baju</option>
                                            <?php foreach ($baju as $row) : ?>
                                                <option value="<?= $row['id_employment_status'] ?>">
                                                    <?= $row['clothes_color'] . ' - ' .  $row['employment_status_name'] ?></option>
                                            <?php endforeach; ?>
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
                                        <label for="bagian">Bagian</label>
                                        <select name="bagian" id="bagian" class="form-control" required>
                                            <option value="">Pilih Bagian</option>
                                            <?php foreach ($bagian as $row) : ?>
                                                <option value="<?= $row['id_job_section'] ?>">
                                                    <?= $row['job_section_name'] ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="form-group mb-2">
                                        <label for="bagian">Area</label>
                                        <select name="area" id="area" class="form-control" required>
                                            <option value="">Pilih Area</option>
                                            <?php foreach ($factory as $row) : ?>
                                                <option value="<?= $row['id_factory'] ?>">
                                                    <?= $row['main_factory'] . ' - ' . $row['factory_name'] ?></option>
                                            <?php endforeach; ?>
                                        </select>
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
                        Import Data Karyawan
                    </h5>
                    <!-- form import  data karyawan -->
                    <form action="<?= base_url('TrainingSchool/karyawanStoreImport') ?>" method="post"
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
                        Tabel Data Karyawan
                    </h4>
                    <div class="table-responsive">
                        <table id="karyawanTable" class="table table-striped table-hover table-bordered w-100">
                            <thead>
                                <!-- <th>No</th> -->
                                <th>Kode Kartu</th>
                                <th>Nama Karyawan</th>
                                <th>Shift</th>
                                <th>Warna Baju</th>
                                <th>Bagian</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </thead>
                            <tbody>
                                <?php if (!empty($karyawan)) : ?>
                                    <?php foreach ($karyawan as $karyawan) : ?>
                                        <tr>
                                            <!-- <td><?= $karyawan['id_employee'] ?></td> -->
                                            <td><?= $karyawan['employee_code'] ?></td>
                                            <td><?= $karyawan['employee_name'] ?></td>
                                            <td><?= $karyawan['shift'] ?></td>

                                            <td><?= $karyawan['employment_status_name'] ?></td>

                                            <td><?= $karyawan['job_section_name'] . ' - ' . $karyawan['main_factory'] . ' - ' . $karyawan['factory_name'] ?></td>
                                            <input type="hidden" name="id_job_section" value="<?= $karyawan['id_job_section'] ?>">
                                            <td><?= $karyawan['status'] ?></td>
                                            <td>
                                                <button
                                                    class="btn btn-sm btn-info btn-edit-employee"
                                                    data-id-employee="<?= $karyawan['id_employee'] ?>"
                                                    data-kode-kartu="<?= $karyawan['employee_code'] ?>"
                                                    data-nama-karyawan="<?= $karyawan['employee_name'] ?>"
                                                    data-shift="<?= $karyawan['shift'] ?>"
                                                    data-jenis-kelamin="<?= $karyawan['gender'] ?>"
                                                    data-libur="<?= $karyawan['holiday'] ?>"
                                                    data-libur-tambahan="<?= $karyawan['additional_holiday'] ?>"
                                                    data-baju-id="<?= $karyawan['id_employment_status'] ?>"
                                                    data-tgl-lahir="<?= $karyawan['date_of_birth'] ?>"
                                                    data-tgl-masuk="<?= $karyawan['date_of_joining'] ?>"
                                                    data-bagian-id="<?= $karyawan['id_job_section'] ?>"
                                                    data-area-id="<?= $karyawan['id_factory'] ?>"
                                                    data-status-aktif="<?= $karyawan['status'] ?>">
                                                    Edit
                                                </button>

                                                <button class="btn bg-gradient-danger btn-sm"
                                                    onclick="getEmployeeDataById('<?= $karyawan['id_employee'] ?>')">
                                                    <i class="fas fa-user-slash text-lg opacity-10" aria-hidden="true" title="Former Employee"></i>
                                                </button>

                                                <!-- <form action="<?= base_url($role . '/formerEmployee') ?>" method="post">
                                                    <input type="hidden" name="id_employee" value="<?= $karyawan['id_employee'] ?>">
                                                    <button type="submit" class="btn bg-gradient-danger btn-sm">
                                                        <i class="fas fa-user-slash text-lg opacity-10" aria-hidden="true" title="Former Employee"></i>
                                                    </button>
                                                </form> -->
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

    <div class="modal fade  bd-example-modal-lg" id="ModalEdit" tabindex="-1" role="dialog" aria-labelledby="ModalEdit" aria-hidden="true">
        <div class="modal-dialog  modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Edit Data Karyawan</h5>
                    <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="formEditKaryawan"
                        action="#" method="post">
                        <!-- hidden PK -->
                        <input type="hidden" name="id_employee" id="edit_id_employee">
                        <input type="hidden" name="id_user" id="edit_id_user"
                            value="<?= session()->get('id_user') ?>">

                        <div class="form-group mb-2">
                            <label for="edit_kode_kartu">Kode Kartu</label>
                            <input type="text" class="form-control" name="kode_kartu" id="edit_kode_kartu" required>
                        </div>
                        <div class="form-group mb-2">
                            <label for="edit_nama_karyawan">Nama Karyawan</label>
                            <input type="text" class="form-control" name="nama_karyawan" id="edit_nama_karyawan" required>
                        </div>
                        <div class="form-group mb-2">
                            <label for="edit_shift">Shift</label>
                            <select name="shift" id="edit_shift" class="form-control" required>
                                <option value="">Pilih Shift</option>
                                <option value="A">A</option>
                                <option value="B">B</option>
                                <option value="C">C</option>
                                <option value="Non Shift">Non Shift</option>
                            </select>
                        </div>
                        <div class="form-group mb-2">
                            <label for="edit_jenis_kelamin">Jenis Kelamin</label>
                            <select name="jenis_kelamin" id="edit_jenis_kelamin" class="form-control" required>
                                <option value="">Pilih Jenis Kelamin</option>
                                <option value="L">Laki-laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                        </div>
                        <div class="form-group mb-2">
                            <label for="edit_libur">Libur</label>
                            <select name="libur" id="edit_libur" class="form-control" required>
                                <option value="">Pilih Hari Libur</option>
                                <?php foreach ($day as $d): ?>
                                    <option value="<?= $d['id_day'] ?>"><?= $d['day_name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group mb-2">
                            <label for="edit_libur_tambahan">Libur Tambahan</label>
                            <select name="libur_tambahan" id="edit_libur_tambahan" class="form-control">
                                <option value="">Pilih Hari Libur</option>
                                <?php foreach ($day as $d): ?>
                                    <option value="<?= $d['id_day'] ?>"><?= $d['day_name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group mb-2">
                            <label for="edit_warna_baju">Warna Baju</label>
                            <select name="warna_baju" id="edit_warna_baju" class="form-control" required>
                                <option value="">Pilih Warna Baju</option>
                                <?php foreach ($baju as $b): ?>
                                    <option value="<?= $b['id_employment_status'] ?>">
                                        <?= $b['clothes_color'] . ' - ' . $b['employment_status_name'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group mb-2">
                            <label for="edit_tgl_lahir">Tanggal Lahir</label>
                            <input type="date" class="form-control" name="tgl_lahir" id="edit_tgl_lahir" required>
                        </div>
                        <div class="form-group mb-2">
                            <label for="edit_tgl_masuk">Tanggal Masuk</label>
                            <input type="date" class="form-control" name="tgl_masuk" id="edit_tgl_masuk" required>
                        </div>
                        <div class="form-group mb-2">
                            <label for="edit_bagian">Bagian</label>
                            <select name="bagian" id="edit_bagian" class="form-control" required>
                                <option value="">Pilih Bagian</option>
                                <?php foreach ($bagian as $row): ?>
                                    <option value="<?= $row['id_job_section'] ?>">
                                        <?= $row['job_section_name'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group mb-2">
                            <label for="edit_area">Area</label>
                            <select name="area" id="edit_area" class="form-control" required>
                                <option value="">Pilih Area</option>
                                <?php foreach ($factory as $a): ?>
                                    <option value="<?= $a['id_factory'] ?>">
                                        <?= $a['main_factory'] . ' - ' . $a['factory_name'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group mb-2">
                            <label for="edit_status_aktif">Status</label>
                            <select name="status_aktif" id="edit_status_aktif" class="form-control" required>
                                <option value="">Pilih Status</option>
                                <option value="Aktif">Aktif</option>
                                <option value="Tidak Aktif">Tidak Aktif</option>
                            </select>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn bg-gradient-info">Save changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Detail & Former Employee -->
<div class="modal fade" id="employeeDetailModal" tabindex="-1" aria-labelledby="employeeDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">

            <div class="modal-header bg-gradient-info">
                <h5 class="modal-title text-white" id="employeeDetailModalLabel">Detail Karyawan</h5>
                <button type="button" class="btn-close text-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form
                id="formerEmployeeForm"
                action="<?= base_url($role . '/formerEmployee') ?>"
                method="POST">
                <div class="modal-body">

                    <!-- Hidden ID Employee -->
                    <input type="hidden" name="id_employee" id="form_id_employee" />

                    <!-- Detail Karyawan -->
                    <div class="row mb-3">
                        <div class="col-sm-6">
                            <strong>Nama</strong><br>
                            <span id="card_nama_karyawan"></span>
                        </div>
                        <div class="col-sm-6">
                            <strong>Kode Kartu</strong><br>
                            <span id="card_kode_kartu"></span>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-sm-6">
                            <strong>Shift</strong><br>
                            <span id="card_shift"></span>
                        </div>
                        <div class="col-sm-6">
                            <strong>Jenis Kelamin</strong><br>
                            <span id="card_jenis_kelamin"></span>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-sm-6">
                            <strong>Libur Reguler</strong><br>
                            <span id="card_libur"></span>
                        </div>
                        <div class="col-sm-6">
                            <strong>Libur Tambahan</strong><br>
                            <span id="card_libur_tambahan"></span>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-sm-6">
                            <strong>Warna Baju</strong><br>
                            <span id="card_warna_baju"></span>
                        </div>
                        <div class="col-sm-6">
                            <strong>Status Pekerjaan</strong><br>
                            <span id="card_status_pekerjaan"></span>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-sm-6">
                            <strong>Tanggal Lahir</strong><br>
                            <span id="card_tgl_lahir"></span>
                        </div>
                        <div class="col-sm-6">
                            <strong>Tanggal Masuk</strong><br>
                            <span id="card_tgl_masuk"></span>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-sm-6">
                            <strong>Bagian</strong><br>
                            <span id="card_bagian"></span>
                        </div>
                        <div class="col-sm-6">
                            <strong>Area</strong><br>
                            <span id="card_area"></span>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-sm-12">
                            <strong>Status Aktif</strong><br>
                            <span id="card_status_aktif"></span>
                        </div>
                    </div>

                    <hr>

                    <!-- Form Keluar -->
                    <div class="row mb-3">
                        <div class="col-sm-6">
                            <label for="form_tgl_out" class="form-label"><strong>Tanggal Keluar</strong></label>
                            <input
                                type="date"
                                class="form-control"
                                id="form_tgl_out"
                                name="tgl_out"
                                required />
                        </div>
                        <div class="col-sm-6">
                            <label for="form_reason" class="form-label"><strong>Alasan Keluar</strong></label>
                            <textarea
                                class="form-control"
                                id="form_reason"
                                name="reason"
                                rows="3"
                                placeholder="Masukkan alasan resign atau pemberhentian"
                                required></textarea>
                        </div>
                    </div>

                </div> <!-- /.modal-body -->

                <div class="modal-footer">
                    <button
                        type="button"
                        class="btn bg-gradient-secondary"
                        data-bs-dismiss="modal">
                        Batal
                    </button>
                    <button
                        type="submit"
                        class="btn bg-gradient-danger">
                        Simpan & Hapus Karyawan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">
    // Ambil data karyawan by ID dan tampilkan di form edit
    function getEmployeeDataById(id) {
        $.ajax({
            url: "<?= base_url($role . '/getEmployeeDataById') ?>",
            type: "GET",
            data: {
                id: id
            },
            dataType: "json",
            success: function(response) {
                if (response && response.status === 'success') {
                    const d = response.data;

                    // Isi konten modal detail
                    // $('#card_id_employee').text(d.id_employee);
                    $('#form_id_employee').val(d.id_employee);
                    $('#card_nama_karyawan').text(d.employee_name);
                    $('#card_kode_kartu').text(d.employee_code);
                    $('#card_shift').text(d.shift);
                    $('#card_jenis_kelamin').text(d.gender === 'P' ? 'Perempuan' : 'Laki-laki');
                    $('#card_libur').text(d.holiday_name);
                    $('#card_libur_tambahan').text(d.additional_holiday_name || '-');
                    $('#card_warna_baju').text(d.clothes_color);
                    $('#card_status_pekerjaan').text(d.employment_status_name);
                    $('#card_tgl_lahir').text(d.date_of_birth);
                    $('#card_tgl_masuk').text(d.date_of_joining);
                    $('#card_bagian').text(d.job_section_name);
                    $('#card_area').text(d.main_factory + ' - ' + d.factory_name);
                    $('#card_status_aktif').text(d.status);

                    // Tampilkan modal detail
                    $('#employeeDetailModal').modal('show');
                } else {
                    Swal.fire('Error', 'Data tidak ditemukan', 'error');
                }
            },
            error: function() {
                Swal.fire('Error', 'Gagal mengambil data', 'error');
            }
        });
    }
</script>
<script>
    $(document).ready(function() {
        // Initialize DataTable with export options
        $('#karyawanTable').DataTable({});

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

    $(document).on('click', '.btn-edit-employee', function() {
        // 1. Ambil semua data lama dari tombol
        const btn = $(this);
        const idEmp = btn.data('id-employee');
        const kode = btn.data('kode-kartu');
        const nama = btn.data('nama-karyawan');
        const shift = btn.data('shift');
        const jk = btn.data('jenis-kelamin');
        const libur = btn.data('libur');
        const libur2 = btn.data('libur-tambahan');
        const baju = btn.data('baju-id');
        const lahir = btn.data('tgl-lahir');
        const masuk = btn.data('tgl-masuk');
        const bagianOld = btn.data('bagian-id');
        const areaOld = btn.data('area-id');
        const status = btn.data('status-aktif');

        const urlBase = "<?= base_url($role . '/karyawanUpdate/') ?>";
        $('#formEditKaryawan').attr('action', urlBase + idEmp);
        // 2. Tampilkan modal
        $('#ModalEdit').modal('show');

        // 3. Isi field visible
        $('#edit_id_employee').val(idEmp);
        $('#edit_kode_kartu').val(kode);
        $('#edit_nama_karyawan').val(nama);
        $('#edit_shift').val(shift);
        $('#edit_jenis_kelamin').val(jk);
        $('#edit_libur').val(libur);
        $('#edit_libur_tambahan').val(libur2);
        $('#edit_warna_baju').val(baju);
        $('#edit_tgl_lahir').val(lahir);
        $('#edit_tgl_masuk').val(masuk);
        $('#edit_bagian').val(bagianOld);
        $('#edit_area').val(areaOld);
        $('#edit_status_aktif').val(status);

        // 4. Buat atau update hidden‐fields untuk OLD
        upsertHidden('id_job_section_old', bagianOld);
        upsertHidden('id_factory_old', areaOld);

        // 5. Set id_user (dari input hidden) dan timestamps
        const userId = $('#edit_id_user').val();
        upsertHidden('id_user', userId);

        const now = new Date().toISOString().slice(0, 19).replace('T', ' ');
        upsertHidden('created_at', now);
        upsertHidden('updated_at', now);

        // fungsi bantu untuk membuat/merubah hidden input
        function upsertHidden(name, val) {
            let $f = $(`#ModalEdit form input[name="${name}"]`);
            if ($f.length) {
                $f.val(val);
            } else {
                $('<input>').attr({
                    type: 'hidden',
                    name: name,
                    value: val
                }).appendTo('#ModalEdit form');
            }
        }
    });

    // ketika user pilih bagian baru atau area baru
    $(document).on('change', '#edit_bagian, #edit_area', function() {
        // ambil new values
        const newBagian = $('#edit_bagian').val();
        const newArea = $('#edit_area').val();

        // upsert hidden baru
        upsertHidden('id_job_section_new', newBagian);
        upsertHidden('id_factory_new', newArea);

        // upsert field date_of_change + reason (jika belum ada)
        if (!$('#ModalEdit form #date_of_change').length) {
            const today = new Date().toISOString().slice(0, 10);
            const fld = `
      <div class="form-group mb-2" id="date_of_change">
        <label for="date_of_change_input">Tanggal Pindah</label>
        <input type="date" name="date_of_change" 
               class="form-control" id="date_of_change_input" 
               value="${today}" required>
      </div>
      <div class="form-group mb-2" id="reason_group">
        <label for="reason">Keterangan</label>
        <textarea name="reason" class="form-control" 
                  id="reason" required></textarea>
      </div>`;
            // sisipkan sebelum button save
            $('#ModalEdit .modal-footer').before(fld);
        }

        // pastikan hidden‐fields nya ada
        upsertHidden('date_of_change', $('#date_of_change_input').val());
        upsertHidden('reason', $('#reason').val());

        // helper sama seperti di atas
        function upsertHidden(name, val) {
            let $f = $(`#ModalEdit form input[name="${name}"], #ModalEdit form textarea[name="${name}"]`);
            if ($f.length) {
                $f.val(val);
            } else {
                $('<input>').attr({
                    type: 'hidden',
                    name: name,
                    value: val
                }).appendTo('#ModalEdit form');
            }
        }
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('formerEmployeeForm');

        form.addEventListener('submit', function(e) {
            e.preventDefault(); // cegah submit otomatis

            Swal.fire({
                title: 'Anda Yakin?',
                text: 'Karyawan ini akan diresign dan data aslinya akan dihapus!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, resign & hapus',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // Jika user setuju, submit form
                    form.submit();
                }
                // Jika dibatalkan, tidak terjadi apa-apa
            });
        });
    });
</script>
<?php $this->endSection(); ?>