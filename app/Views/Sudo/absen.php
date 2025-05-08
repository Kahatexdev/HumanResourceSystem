<?php $this->extend('layout/template'); ?>
<?php $this->section('content'); ?>
<div class="container-fluid">
    <div class="row my-4">
        <div class="col-xl-12 col-sm-12 mb-xl-0 mb-2">
            <div class="card">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="font-weight-bolder mb-0">
                                <a href="" # class="btn bg-gradient-info">
                                    <!-- Icon Data Absen -->
                                    <i class="fas fa-solid fa-2x fa-user-clock"></i>
                                </a>
                                Data Absen
                            </h4>
                        </div>
                        <div>
                            <div class="d-flex justify-content-between">
                                <!-- download report excel -->
                                <a href="<?= base_url('Monitoring/absenReport/') ?>" class="btn bg-gradient-success me-2">
                                    <i class="fas fa-file-excel text-lg me-2"></i>Report Absen
                                </a>
                                <a class="btn bg-gradient-info add-btn" data-bs-toggle="modal" data-bs-target="#addAbsen">
                                    <!-- icon tambah Absen-->
                                    <i class="fas fa-plus text-lg opacity-10" aria-hidden="true"></i>
                                    Data Absen
                                </a>
                                <div> &nbsp;</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xl-12 col-sm-12 mb-xl-0 mb-4 mt-2">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">
                        Import Data Absen
                    </h4>
                    <!-- form import  data absen -->
                    <form action="<?= base_url($role . '/importAbsenSkillMap') ?>" method="post"
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
                    <!-- <form action="<?= base_url($role . '/absenStoreImport') ?>" method="post"
                        enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="id_periode">Periode</label>
                                    <a href="<?= base_url($role . '/downloadTemplateAbsen') ?>"
                                        class="btn btn-success btn-sm mt-2" target="_blank">Download
                                        Template</a>
                                    <select class="form-select" name="id_periode" id="id_periode" required>
                                        <option value="">Pilih Periode</option>
                                        <?php foreach ($periode as $p) : ?>
                                            <option value="<?= $p['id_periode'] ?>">Periode <?= $p['periode_name'] ?> (<?= $p['start_date'] ?> - <?= $p['end_date'] ?>)</option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                        </div>
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
                    </form> -->
                </div>
            </div>
        </div>
    </div>
    <div class="row mt-4">
        <div class="col-xl-12 col-sm-12 mb-xl-0 mb-4 mt-2">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">
                        Tabel Data Absen
                    </h4>
                    <div class="table-responsive">
                        <table id="absenTable" class="table table-striped table-hover table-bordered">
                            <thead>
                                <th>No</th>
                                <th>Nama Karyawan</th>
                                <th>Periode</th>
                                <th>SI(Sakit)</th>
                                <th>MI(Izin)</th>
                                <th>M(Mangkir )</th>
                                <th>T(Cuti)</th>
                                <th>Input By</th>
                                <th>Aksi</th>
                            </thead>
                            <tbody>

                                <?php if (!empty($absen)) : ?>
                                    <?php foreach ($absen as $absen) : ?>
                                        <tr>
                                            <td><?= $absen['id_presence'] ?></td>
                                            <td><?= $absen['employee_name'] ?></td>
                                            <td><?= $absen['batch_name'] ?> - <?= $absen['periode_name'] ?></br></td>
                                            <?php if ($absen['sick'] == 0) : ?>
                                                <td></td>
                                            <?php else : ?>
                                                <td><?= $absen['sick'] ?></td>
                                            <?php endif; ?>
                                            <?php if ($absen['permit'] == 0) : ?>
                                                <td></td>
                                            <?php else : ?>
                                                <td><?= $absen['permit'] ?></td>
                                            <?php endif; ?>
                                            <?php if ($absen['absent'] == 0) : ?>
                                                <td></td>
                                            <?php else : ?>
                                                <td><?= $absen['absent'] ?></td>
                                            <?php endif; ?>
                                            <?php if ($absen['leave'] == 0) : ?>
                                                <td></td>
                                            <?php else : ?>
                                                <td><?= $absen['leave'] ?></td>
                                            <?php endif; ?>
                                            <td><?= $absen['username'] ?></td>
                                            <td>
                                                <a class="btn btn-warning edit-btn"
                                                    data-id="<?= $absen['id_presence'] ?>"
                                                    data-karyawan="<?= $absen['id_employee'] ?>"
                                                    data-idperiode="<?= $absen['id_periode'] ?>"
                                                    data-sakit="<?= $absen['sick'] ?>"
                                                    data-izin="<?= $absen['permit'] ?>"
                                                    data-mangkir="<?= $absen['absent'] ?>"
                                                    data-cuti="<?= $absen['leave'] ?>"
                                                    data-user="<?= $absen['id_user'] ?>"
                                                    data-toggle="modal" data-target="#editAbsen">
                                                    <!-- icon edit -->
                                                    <i class="fas fa-edit text-lg opacity-10" aria-hidden="true"></i>
                                                </a>
                                                <button class="btn bg-gradient-danger btn-sm"
                                                    onclick="confirmDelete('<?= $absen['id_presence'] ?>')">
                                                    <!-- icon hapus -->
                                                    <i class="fas fa-trash text-lg opacity-10" aria-hidden="true"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                    <tr>
                                        <td colspan="9" class="text-center">Data tidak ditemukan</td>
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

<!-- Modal Add Absen -->
<div class="modal fade  bd-example-modal-lg" id="addAbsen" tabindex="-1" role="dialog" aria-labelledby="addAbsen" aria-hidden="true">
    <div class="modal-dialog  modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Form Absen Karyawan</h5>
                <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="<?= base_url($role . '/absenStore') ?>" method="post">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group mb-2">
                                <label for="id_karyawan">Nama Karyawan <small class="text-danger">*</small></label>
                                <select name="id_karyawan" id="id_karyawan" class="form-select" required>
                                    <option value="">Pilih Karyawan</option>
                                    <?php foreach ($karyawan as $kar) : ?>
                                        <option value="<?= $kar['id_employee'] ?>"><?= $kar['employee_code'] ?> - <?= $kar['employee_name'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group mb-2">
                                <label for="id_periode_edit">Periode <small class="text-danger">*</small></label>
                                <select name="id_periode" id="id_periode_edit" class="form-select" required>
                                    <option value="">Pilih Periode</option>
                                    <?php foreach ($periode as $period) : ?>
                                        <option value="<?= $period['id_periode'] ?>"><?= $period['batch_name'] ?> - <?= $period['periode_name'] ?> (<?= $period['start_date'] ?> - <?= $period['end_date'] ?>)</option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group mb-2">
                                <label for="sakit">Sakit</label>
                                <input type="number" name="sakit" id="sakit" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group mb-2">
                                <label for="izin">Izin</label>
                                <input type="number" name="izin" id="izin" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group mb-2">
                                <label for="mangkir">Mangkir</label>
                                <input type="number" name="mangkir" id="mangkir" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group mb-2">
                                <label for="cuti">Cuti</label>
                                <input type="number" name="cuti" id="cuti" class="form-control">
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="id_user" value="<?= session()->get('id_user') ?>">
                    <div class="modal-footer">
                        <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn bg-gradient-info">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit Absen -->
<div class="modal fade  bd-example-modal-lg" id="ModalEdit" tabindex="-1" role="dialog" aria-labelledby="ModalEdit" aria-hidden="true">
    <div class="modal-dialog  modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Form Edit Absen Karyawan</h5>
                <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="" method="post">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group mb-2">
                                <label for="id_karyawan">Nama Karyawan <small class="text-danger">*</small></label>
                                <select name="id_karyawan" id="id_karyawan" class="form-select" required>
                                    <option value="">Pilih Karyawan</option>
                                    <?php foreach ($karyawan as $karyawan) : ?>
                                        <option value="<?= $karyawan['id_employee'] ?>"><?= $karyawan['employee_code'] . ' - ' . $karyawan['employee_name'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group mb-2">
                                <label for="id_periode">Periode <small class="text-danger">*</small></label>
                                <select name="id_periode" id="id_periode" class="form-select" required>
                                    <option value="">Pilih Periode</option>
                                    <?php foreach ($periode as $periode) : ?>
                                        <option value="<?= $periode['id_periode'] ?>"><?= $periode['batch_name'] ?> - <?= $periode['periode_name'] ?> (<?= $periode['start_date'] ?> - <?= $periode['end_date'] ?>)</option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group mb-2">
                                <label for="sakit">Sakit</label>
                                <input type="number" name="sakit" id="sakit" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group mb-2">
                                <label for="izin">Izin</label>
                                <input type="number" name="izin" id="izin" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group mb-2">
                                <label for="mangkir">Mangkir</label>
                                <input type="number" name="mangkir" id="mangkir" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group mb-2">
                                <label for="cuti">Cuti</label>
                                <input type="number" name="cuti" id="cuti" class="form-control">
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="id_absen" id="id_absen">
                    <input type="hidden" name="id_user" value="<?= session()->get('id_user') ?>">
                    <div class="modal-footer">
                        <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn bg-gradient-info">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

//Cek data absen yang tidak masuk
<!-- <?php if ($skip = session()->getFlashdata('skipDetails')): ?>
    <h3>Baris yang Tidak Masuk (<?= count($skip) ?>)</h3>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Baris</th>
                <th>Kode Kartu</th>
                <th>Nama Karyawan</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($skip as $d): ?>
                <tr>
                    <td><?= esc($d['row']) ?></td>
                    <td><?= esc($d['code']) ?></td>
                    <td><?= esc($d['name']) ?></td>
                </tr>
            <?php endforeach ?>
        </tbody>
    </table>
<?php endif ?> -->

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
                window.location.href = "<?= base_url($role . '/absenDelete/') ?>" + id;
            }
        })
    }
</script>
<script>
    $(document).ready(function() {
        // Initialize DataTable with export options
        $('#absenTable').DataTable({});

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

    // Edit Absen
    $('.edit-btn').on('click', function() {
        const id = $(this).data('id');
        const karyawan = $(this).data('karyawan');
        const idperiode = $(this).data('idperiode');
        const sakit = $(this).data('sakit');
        const izin = $(this).data('izin');
        const mangkir = $(this).data('mangkir');
        const cuti = $(this).data('cuti');
        const user = $(this).data('user');

        // console.log(id, karyawan, bulan, sakit, izin, mangkir, cuti, user);
        $('#ModalEdit').find('form').attr('action', '<?= base_url($role . '/absenUpdate/') ?>' + id);
        $('#ModalEdit').find('select[name="id_karyawan"]').val(karyawan);
        $('#ModalEdit').find('select[name="id_periode"]').val(idperiode);
        $('#ModalEdit').find('input[name="sakit"]').val(sakit);
        $('#ModalEdit').find('input[name="izin"]').val(izin);
        $('#ModalEdit').find('input[name="mangkir"]').val(mangkir);
        $('#ModalEdit').find('input[name="cuti"]').val(cuti);
        $('#ModalEdit').find('input[name="id_absen"]').val(id);
        $('#ModalEdit').find('input[name="id_user"]').val(user);
        $('#ModalEdit').modal('show');

    });
</script>

<?php $this->endSection(); ?>