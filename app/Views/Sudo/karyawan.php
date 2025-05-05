<?php $this->extend('layout/template'); ?>
<?php $this->section('content'); ?>

<?php if ($msg = session()->getFlashdata('success')): ?>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                html: <?= json_encode($msg) ?>,
            });
        });
    </script>
<?php endif; ?>

<?php if ($msg = session()->getFlashdata('error')): ?>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                html: <?= json_encode($msg) ?>,
            });
        });
    </script>
<?php endif; ?>

<div class="container-fluid">
    <div class="row my-2">
        <div class="col-xl-12 col-sm-12 mb-xl-0 mb-2">
            <div class="card">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="font-weight-bolder mb-0">
                                <a href="#" class="btn bg-gradient-info">
                                    <!-- icon data karyawan -->
                                    <i class="fas fa-user-tie text-lg opacity-10" aria-hidden="true"></i>
                                </a>
                                Data Karyawan
                            </h4>
                        </div>
                        <div>
                            <div class="d-flex justify-content-between">
                                <a href="<?= base_url($role . '/exportKaryawan') ?>"
                                    class="btn bg-gradient-info me-2">
                                    <!-- icon download -->
                                    <i class="fas fa-file-excel text-lg opacity-10" aria-hidden="true"></i>
                                    Export Excel
                                </a>
                                <a href="<?= base_url($role . '/downloadTemplateKaryawan') ?>"
                                    class="btn bg-gradient-success me-2">
                                    <i class="fas fa-download text-lg opacity-10" aria-hidden="true"></i>
                                    Template Excel
                                </a>
                                <a href=""
                                    class="btn bg-gradient-info add-btn" data-bs-toggle="modal" data-bs-target="#addKaryawan">
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
                                            <?php foreach ($area as $row) : ?>
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
                    <form action="<?= base_url($role . '/karyawanStoreImport'); ?>" method="post"
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
                                <th>No</th>
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
                                            <td><?= $karyawan['id_employee'] ?></td>
                                            <td><?= $karyawan['employee_code'] ?></td>
                                            <td><?= $karyawan['employee_name'] ?></td>
                                            <td><?= $karyawan['shift'] ?></td>
                                            <td><?= $karyawan['clothes_color'] ?></td>
                                            <input type="hidden" name="id_employment_status" value="<?= $karyawan['id_employment_status'] ?>">
                                            <td><?= $karyawan['job_section_name'] . ' - ' . $karyawan['main_factory'] . ' - ' . $karyawan['factory_name'] ?></td>
                                            <input type="hidden" name="id_job_section" value="<?= $karyawan['id_job_section'] ?>">
                                            <input type="hidden" name="id_factory" value="<?= $karyawan['id_factory'] ?>">
                                            <input type="hidden" name="holiday" value="<?= $karyawan['holiday'] ?>">
                                            <input type="hidden" name="additional_holiday" value="<?= $karyawan['additional_holiday'] ?>">
                                            <td><?= $karyawan['status'] ?></td>
                                            <td>
                                                <a class="btn btn-warning edit-btn"
                                                    data-id="<?= $karyawan['id_employee'] ?> "
                                                    data-kode_kartu="<?= $karyawan['employee_code'] ?>"
                                                    data-nama="<?= $karyawan['employee_name'] ?>"
                                                    data-shift="<?= $karyawan['shift'] ?>"
                                                    data-jenis_kelamin="<?= $karyawan['gender'] ?>"
                                                    data-area="<?= $karyawan['id_factory'] ?>"
                                                    data-libur="<?= $karyawan['holiday'] ?>"
                                                    data-libur_tambahan="<?= $karyawan['additional_holiday'] ?>"
                                                    data-warna_baju="<?= $karyawan['id_employment_status'] ?>"
                                                    data-tgl_lahir="<?= $karyawan['date_of_birth'] ?>"
                                                    data-tgl_masuk="<?= $karyawan['date_of_joining'] ?>"
                                                    data-job_section_name="<?= $karyawan['id_job_section'] ?>"
                                                    data-status_aktif="<?= $karyawan['status'] ?>"
                                                    data-bs-toggle="modal" data-bs-target="#ModalEdit">
                                                    <i class="fas fa-edit text-lg opacity-10" aria-hidden="true"></i>
                                                </a>
                                                <button class="btn bg-gradient-danger btn-sm"
                                                    onclick="confirmDelete('<?= $karyawan['id_employee'] ?>')">
                                                    <i class="fas fa-trash text-lg opacity-10" aria-hidden="true"></i>
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
                    <form action="" method="post">
                        <!-- hidden PK -->
                        <input type="hidden" name="id_employee" id="edit_id_employee">

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
                                <?php foreach ($area as $a): ?>
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
<script type="text/javascript">
    function confirmDelete(id) {
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Data yang dihapus tidak dapat dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal',
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "<?= base_url($role . '/karyawanDelete/') ?>" + id;
            }
        })
    }
</script>
<script>
    $(document).ready(function() {
        // Initialize DataTable with export options
        $('#karyawanTable').DataTable({});
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

    $('.edit-btn').click(function() {
        const id = $(this).data('id');
        const kode_kartu = $(this).data('kode_kartu');
        const nama = $(this).data('nama');
        const shift = $(this).data('shift');
        const jenis_kelamin = $(this).data('jenis_kelamin');
        const area = $(this).data('area');
        const libur = $(this).data('libur');
        const libur_tambahan = $(this).data('libur_tambahan');
        const warna_baju = $(this).data('warna_baju');
        const tgl_lahir = $(this).data('tgl_lahir');
        const tgl_masuk = $(this).data('tgl_masuk');
        const bagian = $(this).data('job_section_name');
        const status_aktif = $(this).data('status_aktif');

        const $modal = $('#ModalEdit');
        $modal.find('form').attr('action', '<?= base_url($role . '/karyawanUpdate/') ?>' + id);
        $modal.find('#edit_id_employee').val(id);
        $modal.find('#edit_kode_kartu').val(kode_kartu);
        $modal.find('#edit_nama_karyawan').val(nama);
        $modal.find('#edit_shift').val(shift);
        $modal.find('#edit_jenis_kelamin').val(jenis_kelamin);
        $modal.find('#edit_area').val(area);
        $modal.find('#edit_libur').val(libur);
        $modal.find('#edit_libur_tambahan').val(libur_tambahan);
        $modal.find('#edit_warna_baju').val(warna_baju);
        $modal.find('#edit_tgl_lahir').val(tgl_lahir);
        $modal.find('#edit_tgl_masuk').val(tgl_masuk);
        $modal.find('#edit_bagian').val(bagian);
        $modal.find('#edit_status_aktif').val(status_aktif);
        $modal.modal('show');
    });
</script>

<script>
    $(document).on('change', '#bagian', function() {
        // Jika modal yang aktif adalah modal tambah, jangan tampilkan input tambahan
        if ($(this).closest('.modal').attr('id') === 'addKaryawan') {
            return;
        }

        var readonlyInputIdOld = 'readonlyInputOld';
        var readonlyInputIdNew = 'readonlyInputNew';
        var tgl_pindah_id = 'tgl_pindah';
        var keterangan_id = 'keterangan';

        // Ambil nilai lama dari data atribut yang sudah diset saat klik tombol edit
        var oldValue = $('#bagian').data('job_section_name');
        // Ambil nilai baru (nama bagian dari option yang dipilih)
        var newValue = $(this).find(':selected').text().trim();

        // Hapus elemen tambahan sebelumnya (jika ada)
        $('#' + readonlyInputIdOld).remove();
        $('#' + readonlyInputIdNew).remove();
        $('#' + tgl_pindah_id).remove();
        $('#' + keterangan_id).remove();

        // Buat markup untuk menampilkan nilai lama dan baru, serta input tanggal dan keterangan
        var readonlyInputOld = `
    <div class="form-group mb-2" id="${readonlyInputIdOld}">
        <input type="hidden" class="form-control" name="readonly_bagian_old" id="readonly_bagian_old" value="${oldValue}" readonly>
    </div>`;

        var readonlyInputNew = `
    <div class="form-group mb-2" id="${readonlyInputIdNew}">
        <input type="hidden" class="form-control" id="readonly_bagian_new" value="${newValue}" readonly>
    </div>`;

        var tgl_pindah_input = `
    <div class="form-group mb-2" id="${tgl_pindah_id}">
        <label for="tgl_pindah">Tanggal Pindah</label>
        <input type="date" class="form-control" name="tgl_pindah" id="tgl_pindah" required>
    </div>`;

        var keterangan_input = `
    <div class="form-group mb-2" id="${keterangan_id}">
        <label for="keterangan">Keterangan</label>
        <textarea class="form-control" name="keterangan" id="keterangan" required></textarea>
    </div>`;

        // Sisipkan elemen-elemen baru sebelum form-group yang berisi select #bagian
        $(this).closest('.form-group').before(readonlyInputOld + readonlyInputNew + tgl_pindah_input + keterangan_input);
    });
</script>

<?php $this->endSection(); ?>