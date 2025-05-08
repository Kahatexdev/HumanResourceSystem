<?php $this->extend('layout/template'); ?>
<?php $this->section('content'); ?>

<div class="container-fluid">

    <div class="row my-2">
        <div class="col-xl-12 col-sm-12 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="font-weight-bolder mb-0">
                                <a href="" # class="btn bg-gradient-info">
                                    <!-- Icon Data Bagian -->
                                    <i class="fas fa-briefcase text-lg opacity-10"></i>
                                </a>
                                Data Bagian
                            </h4>
                        </div>
                        <div>
                            <div class="d-flex justify-content-between">
                                <a href=""
                                    class="btn bg-gradient-info add-btn" data-bs-toggle="modal" data-bs-target="#addBagian">
                                    <!-- Icon Tambah Bagian-->
                                    <i class="fas fa-plus text-lg opacity-10 me-1" aria-hidden="true"></i>
                                    Tambah Bagian
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
        <div class="col-xl-12 col-sm-12 mb-xl-0 mb-2 mt-2">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">
                        Tabel Data Bagian
                    </h5>
                    <div class="table-responsive">
                        <table id="bagianTable" class="table table-striped table-hover table-bordered">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Bagian</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($bagian)) : ?>
                                    <?php $no = 1; ?>
                                    <?php foreach ($bagian as $bagian) : ?>
                                        <tr>
                                            <td><?= $no++ ?></td>
                                            <td><?= $bagian['job_section_name'] ?></td>
                                            <td>
                                                <a href="#"
                                                    class="btn btn-warning edit-btn" data-id="<?= $bagian['id_job_section'] ?> "
                                                    data-nama="<?= $bagian['job_section_name'] ?>"
                                                    data-bs-toggle=" modal" data-bs-target="#editUser">
                                                    <i class=" fas fa-edit text-lg opacity-10" aria-hidden="true"></i>
                                                </a>
                                                <button onclick="confirmDelete(<?= $bagian['id_job_section'] ?>)"
                                                    class="btn bg-gradient-danger btn-sm">
                                                    <i class="fas fa-trash text-lg opacity-10" aria-hidden="true"></i>
                                                </button>
                                                <!-- <a href="<?= base_url('Monitoring/jobroleCreate') ?>" class="btn bg-gradient-info btn-sm">
                                                    <i class="fas fa-plus text-lg opacity-10" aria-hidden="true"></i>
                                                    Job Role
                                                </a> -->
                                            </td>
                                        </tr>
                                    <?php endforeach ?>
                                <?php else : ?>
                                    <tr>
                                        <td colspan="6" class="text-center">Tidak Ada Data Bagian</td>
                                    </tr>
                                <?php endif ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal Tambah -->
    <div class="modal fade  bd-example-modal-lg" id="addBagian" tabindex="-1" role="dialog" aria-labelledby="addBagian" aria-hidden="true">
        <div class="modal-dialog  modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Tambah Bagian</h5>
                    <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="<?= base_url($role . '/bagianStore'); ?>" method="post">

                        <div class="row">
                            <div class="col-lg-12 col-sm-12">
                                <div class="form-group mb-2">
                                    <label for="job_section_name">Nama Bagian</label>
                                    <input type="text" class="form-control" name="job_section_name" id="job_section_name" required>
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
    <!-- Modal Edit -->
    <div class="modal fade  bd-example-modal-lg" id="ModalEdit" tabindex="-1" role="dialog" aria-labelledby="ModalEdit" aria-hidden="true">
        <div class="modal-dialog  modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Edit Bagian</h5>
                    <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="<?= base_url($role . '/bagianUpdate' . $bagian['id_job_section']) ?>" method="post">
                        <div class="row">
                            <div class="col-lg-12 col-sm-12">
                                <div class="form-group mb-2">
                                    <label for="job_section_name">Nama Bagian</label>
                                    <input type="text" class="form-control" name="job_section_name" id="job_section_name"
                                        value="" required>
                                </div>
                            </div>
                        </div>
                        <!-- Tombol Aksi -->
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
                window.location.href = "<?= base_url(session()->get('role') . '/bagianDelete/') ?>" + id;
            }
        })
    }
</script>
<script>
    $(document).ready(function() {
        // Initialize DataTable with export options
        $('#bagianTable').DataTable({});

        // Flash message SweetAlerts
        <?php if (session()->getFlashdata('success')) : ?>
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: '<?= session()->getFlashdata('success') ?>',
            });
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')) : ?>
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: '<?= session()->getFlashdata('error') ?>',
            });
        <?php endif; ?>
    });

    $('.edit-btn').click(function() {
        var id = $(this).data('id');
        var namaBag = $(this).data('nama');
        var areaUtama = $(this).data('areautama');
        var area = $(this).data('area');
        var ket = $(this).data('ket');
        // console.log(id, namaBag, areaUtama, area, ket);
        $('#ModalEdit').find('form').attr('action', '<?= base_url($role . '/bagianUpdate/') ?>' + id);
        $('#ModalEdit').find('input[name="job_section_name"]').val(namaBag);
        $('#ModalEdit').find('input[name="area_utama"]').val(areaUtama);
        $('#ModalEdit').find('input[name="area"]').val(area);
        $('#ModalEdit').find('input[name="keterangan"]').val(ket);
        $('#ModalEdit').modal('show'); // Show the modal
    });
</script>

<?php $this->endSection(); ?>