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
                                <a href="#" class="btn bg-gradient-info">
                                    <!-- icon data batch -->
                                    <i class="fas fa-database text-lg opacity-10 me-1" aria-hidden="true"></i>
                                </a>
                                Data Batch
                            </h4>
                        </div>
                        <div>
                            <div class="d-flex justify-content-between">
                                <a href=""
                                    class="btn bg-gradient-info add-btn" data-bs-toggle="modal" data-bs-target="#addBatch">
                                    <!-- Icon Tambah Bagian-->
                                    <i class="fas fa-plus text-lg opacity-10 me-1" aria-hidden="true"></i>
                                    Tambah Batch
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
        <div class="col-xl-12 col-sm-12 mb-xl-0 mt-2">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">
                        Tabel Data Batch
                    </h5>
                    <div class="table-responsive">
                        <table id="batchTable" class="table table-striped table-hover table-bordered w-100">
                            <thead>
                                <th>No</th>
                                <th>Nama Batch</th>
                                <th>Aksi</th>
                            </thead>
                            <tbody>
                                <?php if (!empty($batch)) : ?>
                                    <?php $no = 1; ?>
                                    <?php foreach ($batch as $batch) : ?>
                                        <tr>
                                            <td><?= $no++ ?></td>
                                            <td><?= $batch['batch_name'] ?></td>
                                            <td>
                                                <a href="#"
                                                    class="btn btn-warning edit-btn" data-id="<?= $batch['id_batch'] ?>"
                                                    data-batch="<?= $batch['batch_name'] ?>" data-bs-toggle="modal" data-bs-target="#editBatch">
                                                    <i class=" fas fa-edit text-lg opacity-10" aria-hidden="true"></i>
                                                </a>
                                                <button onclick="confirmDelete(<?= $batch['id_batch'] ?>)"
                                                    class="btn bg-gradient-danger btn-sm">
                                                    <i class="fas fa-trash text-lg opacity-10" aria-hidden="true"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach ?>
                                <?php else : ?>
                                    <tr>
                                        <td colspan="4" class="text-center">No Batch found</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal Tambah -->
    <div class="modal fade  bd-example-modal-lg" id="addBatch" tabindex="-1" role="dialog" aria-labelledby="addBatch" aria-hidden="true">
        <div class="modal-dialog  modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Tambah Batch</h5>
                    <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="<?= base_url($role . '/batchStore'); ?>" method="post">

                        <div class="row">
                            <div class="col-lg-12 col-sm-12">
                                <div class="form-group mb-2">
                                    <label for="batch_name">Nama Batch</label>
                                    <input type="text" class="form-control" name="nama_batch" id="nama_batch" required>
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
                    <h5 class="modal-title" id="exampleModalLabel">Edit Batch</h5>
                    <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="" method="post">
                        <div class="row">
                            <div class="col-lg-12 col-sm-12">
                                <div class="form-group mb-2">
                                    <label for="batch_name">Nama Batch</label>
                                    <input type="text" class="form-control" name="nama_batch" id="nama_batch"
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
                    window.location.href = "<?= base_url($role . '/batchDelete/') ?>" + id;
                }
            })
        }
    </script>
    <script>
        $(document).ready(function() {
            // Initialize DataTable with export options
            $('#batchTable').DataTable({});

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

        $('.edit-btn').click(function() {
            var id = $(this).data('id');
            var namaBatch = $(this).data('batch');
            $('#ModalEdit').find('form').attr('action', '<?= base_url($role . '/batchUpdate/') ?>' + id);
            $('#ModalEdit').find('input[name="nama_batch"]').val(namaBatch);
            $('#ModalEdit').modal('show'); // Show the modal
        });
    </script>

    <?php $this->endSection(); ?>