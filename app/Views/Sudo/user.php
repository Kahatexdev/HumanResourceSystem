<?php $this->extend('layout/template'); ?>
<?php $this->section('content'); ?>

<?php
// swall alert
if (session()->getFlashdata('success')) {
    echo '<div class="alert" role="alert">';
    echo '</div>';
}
if (session()->getFlashdata('error')) {
    echo '<div class="alert" role="alert">';
    echo '</div>';
}
?>
<div class="container-fluid">
    <div class="row my-2">
        <div class="col-xl-12 col-sm-12 mb-xl-0 mb-2">
            <div class="card">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="font-weight-bolder mb-0">
                                <a href="" # class="btn bg-gradient-info">
                                    <!-- icon data user -->
                                    <i class="fas fa-user-tie text-lg opacity-10" aria-hidden="true"></i>
                                </a>
                                Data User
                            </h4>
                        </div>
                        <div>
                            <div class="d-flex justify-content-between">
                                <a href="#"
                                    class="btn bg-gradient-info add-btn" data-bs-toggle="modal" data-bs-target="#addUser"></i>
                                    <!-- Icon Tambah Bagian-->
                                    <i class="fas fa-plus text-lg opacity-10 me-1" aria-hidden="true"></i>
                                    Tambah User
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
                    <h5 class="card-title">
                        Tabel Data User
                    </h5>
                    <div class="table-responsive">
                        <table id="userTable" class="table table-striped table-hover table-bordered">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Username</th>
                                    <th>Role</th>
                                    <th>Area</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($users)) : ?>
                                    <?php foreach ($users as $user) : ?>
                                        <tr>
                                            <td><?= $user['id_user'] ?></td>
                                            <td><?= $user['username'] ?></td>

                                            <td><?= $user['role'] ?></td>
                                            <td><?= $user['area'] ?></td>
                                            <td>
                                                <a href="#"
                                                    class="btn btn-warning edit-btn" data-id="<?= $user['id_user'] ?> " data-usn="<?= $user['username'] ?>" data-pass="<?= $user['password'] ?>" data-bs-toggle="modal" data-bs-target="#editUser">
                                                    <!-- icon edit -->
                                                    <i class="fas fa-edit text-lg opacity-10" aria-hidden="true"></i>
                                                </a>
                                                <button class="btn bg-gradient-danger btn-sm"
                                                    onclick="confirmDelete('<?= $user['id_user'] ?>')">
                                                    <!-- icon delete -->
                                                    <i class="fas fa-trash text-lg opacity-10" aria-hidden="true"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach ?>
                                <?php else : ?>
                                    <tr>
                                        <td colspan="5" class="text-center">No users found</td>
                                    </tr>
                                <?php endif ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade  bd-example-modal-lg" id="addUser" tabindex="-1" role="dialog" aria-labelledby="addUser" aria-hidden="true">
        <div class="modal-dialog  modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Tambah User</h5>
                    <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body ">
                    <form action="<?= base_url(session()->get('role') . '/userStore'); ?>" method="post">
                        <div class="row">
                            <div class="col-lg-12 col-sm-12">
                                <div class="form-group">
                                    <label for="col-lg-6 col-sm-12">Username</label>
                                    <input type="text" class="form-control" name="username">
                                </div>
                                <div class="form-group">
                                    <label for="col-lg-6 col-sm-12">Password</label>
                                    <input type="text" class="form-control" name="password">
                                </div>
                                <div class="form-group">
                                    <label for="role">Role</label>
                                    <select name="role" id="role" class="form-control" required>
                                        <option value="">Pilih Role</option>
                                        <option value="Sudo">Sudo</option>
                                        <option value="Absensi">Absensi</option>
                                        <option value="Mandor">Mandor</option>
                                        <option value="Monitoring">Monitoring</option>
                                        <option value="TrainingSchool">Training School</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="role">Area</label>
                                    <select name="area" id="area" class="form-control">
                                        <option value="">Pilih Area</option>
                                        <option value="KK1A">KK1A</option>
                                        <option value="KK2B">KK2B</option>
                                        <option value="KK5">KK5</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn bg-gradient-primary">Save</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
    <div class="modal fade  bd-example-modal-lg" id="ModalEdit" tabindex="-1" role="dialog" aria-labelledby="ModalEdit" aria-hidden="true">
        <div class="modal-dialog  modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Edit User</h5>
                    <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="<?= base_url('Monitoring/userUpdate' . $user['id_user']); ?>" method="post">

                        <div class="row">
                            <div class="col-lg-12 col-sm-12">
                                <div class="form-group">
                                    <label for="col-lg-6 col-sm-12">Username</label>
                                    <input type="text" class="form-control" name="username">
                                </div>
                                <div class="form-group">
                                    <label for="col-lg-6 col-sm-12">Password</label>
                                    <input type="text" class="form-control" name="password">
                                </div>
                                <div class="form-group">
                                    <label for="role">Role</label>
                                    <select name="role" id="role" class="form-control" required>
                                        <option value="">Pilih Role</option>
                                        <option value="Sudo">Sudo</option>
                                        <option value="Mandor">Mandor</option>
                                        <option value="Monitoring">Monitoring</option>
                                        <option value="TrainingSchool">Training School</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="role">Area</label>
                                    <select name="area" id="area" class="form-control">
                                        <option value="">Pilih Area</option>
                                        <option value="KK1A">KK1A</option>
                                        <option value="KK2B">KK2B</option>
                                        <option value="KK5">KK5</option>
                                    </select>
                                </div>

                            </div>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn bg-gradient-primary">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- modals -->

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
                window.location.href = "<?= base_url(session()->get('role') . '/userDelete/') ?>" + id;
            }
        })
    }
</script>
<script>
    $(document).ready(function() {
        // Initialize DataTable with export options
        $('#userTable').DataTable({});

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
        var username = $(this).data('usn');
        var pass = $(this).data('pass');
        var area = $(this).data('area');
        $('#ModalEdit').find('form').attr('action', '<?= base_url('Monitoring/userUpdate/') ?>' + id);
        $('#ModalEdit').find('input[name="username"]').val(username);
        $('#ModalEdit').find('input[name="password"]').val(pass);
        $('#ModalEdit').modal('show'); // Show the modal
    });
</script>

<?php $this->endSection(); ?>