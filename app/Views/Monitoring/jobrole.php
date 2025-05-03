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
                                Data <?= $title; ?>
                            </h4>
                        </div>
                        <div>
                            <div class="d-flex justify-content-between">
                                <a href=""
                                    class="btn bg-gradient-info add-btn me-2" data-bs-toggle="modal" data-bs-target="#addJobdesk">
                                    <!-- Icon Tambah Bagian-->
                                    <i class="fas fa-plus text-lg opacity-10 me-1" aria-hidden="true"></i>
                                    Tambah Jobdesk
                                </a>
                                <a href="#"
                                    class="btn bg-gradient-info me-2"
                                    data-bs-toggle="modal"
                                    data-bs-target="#addJobRole">
                                    <i class="fas fa-tasks text-lg opacity-10 me-1"></i>
                                    Tambah Jobrole
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
                        Tabel Data <?= $title; ?>
                    </h5>
                    <div class="table-responsive">

                        <table id="jobdeskTable" class="table table-striped table-hover table-bordered w-100">
                            <thead>
                                <th></th> <!-- untuk ikon expand/collapse -->
                                <th>No</th>
                                <th>Nama Jobdesk</th>
                                <th>Aksi</th>
                            </thead>
                            <tbody>
                                <?php if (!empty($mainjobrole)) : ?>
                                    <?php foreach ($mainjobrole as $main) : ?>
                                        <tr data-id="<?= $main['id_main_job_role'] ?>">
                                            <td class="details-control text-center" style="cursor:pointer">
                                                <i class="fas fa-plus-circle"></i>
                                            </td>
                                            <td><?= $main['id_main_job_role'] ?></td>
                                            <td><?= $main['main_job_role_name'] ?></td>
                                            <td>
                                                <a href="#"
                                                    class="btn btn-warning edit-btn" data-id="<?= $main['id_main_job_role'] ?>"
                                                    data-name="<?= $main['main_job_role_name'] ?>" data-bs-toggle="modal" data-bs-target="#ModalEdit">
                                                    <i class=" fas fa-edit text-lg opacity-10" aria-hidden="true"></i>
                                                </a>
                                                <button onclick="confirmDelete(<?= $main['id_main_job_role'] ?>)"
                                                    class="btn bg-gradient-danger">
                                                    <i class="fas fa-trash text-lg opacity-10" aria-hidden="true"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach ?>
                                <?php else : ?>
                                    <tr>
                                        <td colspan="4" class="text-center">No Jobdesk found</td>
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
    <div class="modal fade  bd-example-modal-lg" id="addJobdesk" tabindex="-1" role="dialog" aria-labelledby="addJobdesk" aria-hidden="true">
        <div class="modal-dialog  modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Tambah Jobdesk</h5>
                    <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="<?= base_url($role . '/mainJobStore'); ?>" method="post">

                        <div class="row">
                            <div class="col-lg-12 col-sm-12">
                                <div class="form-group mb-2">
                                    <label for="main_job_role_name">Nama Jobdesk</label>
                                    <input type="text" class="form-control" name="main_job_role_name" id="main_job_role_name" required>
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
                    <h5 class="modal-title" id="exampleModalLabel">Edit Jobdesk</h5>
                    <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="" method="post">
                        <div class="row">
                            <div class="col-lg-12 col-sm-12">
                                <div class="form-group mb-2">
                                    <label for="main_job_role_name">Nama Jobdesk</label>
                                    <input type="text" class="form-control" name="main_job_role_name" id="main_job_role_name"
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


    <!-- Modal Tambah Jobrole -->
    <div class="modal fade" id="addJobRole" tabindex="-1" aria-labelledby="addJobRoleLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg"><!-- modal-lg untuk desktop, full di mobile -->
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addJobRoleLabel">Tambah Jobrole</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form action="<?= base_url($role . '/jobRoleStore'); ?>" method="post" class="p-4">
                    <!-- pilih main job role -->
                    <div class="mb-3">
                        <label for="id_main_job_role" class="form-label">Main Job Role</label>
                        <select name="id_main_job_role" id="id_main_job_role" class="form-select" required>
                            <option value="">Pilih Main Job Role</option>
                            <?php foreach ($mainjobrole as $mjr): ?>
                                <option value="<?= $mjr['id_main_job_role'] ?>">
                                    <?= esc($mjr['main_job_role_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>


                    <!-- container dynamic rows -->
                    <div id="jobrole-container">
                        <div class="row g-2 align-items-end jobrole-group mb-3">
                            <!-- NO -->
                            <div class="col-md-1 text-center">
                                <!-- <label class="form-label">No.</label> -->
                                <div class="row-number">1</div>
                            </div>
                            <!-- Keterangan -->
                            <div class="col-md-3">
                                <label class="form-label">Keterangan</label>
                                <select name="description[]" class="form-control" required>
                                    <option value="">Pilih Keterangan</option>
                                    <option value="OPERATOR">OPERATOR</option>
                                    <option value="C.O">C.O</option>
                                    <option value="Ringan">Ringan</option>
                                    <option value="Standar">Standar</option>
                                    <option value="Sulit">Sulit</option>
                                    <option value="JOB">JOB</option>
                                    <option value="ROSSO">ROSSO</option>
                                    <option value="SETTING">SETTING</option>
                                    <option value="Potong Manual">Potong Manual</option>
                                    <option value="Overdeck">Overdeck</option>
                                    <option value="Obras">Obras</option>
                                    <option value="Single Needle">Single Needle</option>
                                    <option value="Mc Lipat">Mc Lipat</option>
                                    <option value="Mc Kancing">Mc Kancing</option>
                                    <option value="Mc Press">Mc Press</option>
                                    <option value="6S">6S</option>
                                </select>
                            </div>
                            <!-- Job Description -->
                            <div class="col-md-6">
                                <label class="form-label">Job Description</label>
                                <input type="text" name="jobdescription[]" class="form-control" placeholder="Masukkan Job Description" required>
                            </div>

                            <!-- tombol Add/Remove -->
                            <div class="col-md-2 text-end">
                                <div class="d-flex justify-content-end">
                                    <button type="button" class="btn btn-info me-1 add-more-btn">
                                        <i class="fas fa-plus-circle"></i>
                                    </button>
                                    <!-- tombol REMOVE hanya muncul setelah baris pertama, di-JS -->
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-info">Simpan Semua</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Offcanvas View Jobroles -->
    <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasJobroles" aria-labelledby="offcanvasJobrolesLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="offcanvasJobrolesLabel">Jobroles untuk: <span id="offcanvasJobrolesTitle"></span></h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <table id="offcanvasJobRoleTable" class="table table-striped table-bordered w-100">
                <thead>
                    <tr>
                        <th style="width:5%">No.</th>
                        <th style="width:25%">Keterangan</th>
                        <th style="width:50%">Job Description</th>
                        <th style="width:20%">Aksi</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>

    <!-- Modal Edit Jobrole -->
    <div class="modal fade" id="editJobRoleModal" tabindex="-1" aria-labelledby="editJobRoleLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form id="editJobRoleForm" method="post">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editJobRoleLabel">Edit Jobrole</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id_job_role" id="edit_id_job_role">
                        <div class="mb-3">
                            <label for="edit_description" class="form-label">Keterangan</label>
                            <select name="description" id="edit_description" class="form-select" required>
                                <option value="">Pilih Keterangan</option>
                                <option>OPERATOR</option>
                                <option>C.O</option>
                                <option>Ringan</option>
                                <option>Standar</option>
                                <option>Sulit</option>
                                <option>JOB</option>
                                <option>ROSSO</option>
                                <option>SETTING</option>
                                <option>Potong Manual</option>
                                <option>Overdeck</option>
                                <option>Obras</option>
                                <option>Single Needle</option>
                                <option>Mc Lipat</option>
                                <option>Mc Kancing</option>
                                <option>Mc Press</option>
                                <option>6S</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="edit_jobdescription" class="form-label">Job Description</label>
                            <input type="text" name="jobdescription" id="edit_jobdescription" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-info">Update</button>
                    </div>
                </form>
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
                    window.location.href = "<?= base_url($role . '/mainJobDelete/') ?>" + id;
                }
            })
        }
        document.addEventListener('DOMContentLoaded', function() {
            const container = document.getElementById('jobrole-container');
            const addBtn = document.querySelector('.add-more-btn');

            // fungsi untuk meremapping nomor
            function updateRowNumbers() {
                container.querySelectorAll('.jobrole-group')
                    .forEach((row, idx) => {
                        row.querySelector('.row-number').textContent = idx + 1;
                    });
            }

            // event tombol Add More
            addBtn.addEventListener('click', () => {
                const orig = container.querySelector('.jobrole-group');
                const clone = orig.cloneNode(true);

                // Hapus tombol Add dari clone
                const plusBtnClone = clone.querySelector('.add-more-btn');
                if (plusBtnClone) plusBtnClone.remove();

                // Kosongkan input dan select
                clone.querySelectorAll('input, select').forEach(el => el.value = '');

                // Tambahkan tombol Remove di kolom action
                const actionCol = clone.querySelector('.col-md-2 .d-flex');
                if (!actionCol.querySelector('.remove-row')) {
                    actionCol.insertAdjacentHTML('beforeend', `
      <button type="button" class="btn btn-danger remove-row">
        <i class="fas fa-trash-alt"></i>
      </button>
    `);
                }

                container.appendChild(clone);
                updateRowNumbers();
            });

            // event delegation untuk tombol Remove
            container.addEventListener('click', e => {
                if (e.target.closest('.remove-row')) {
                    const groups = container.querySelectorAll('.jobrole-group');
                    if (groups.length > 1) {
                        e.target.closest('.jobrole-group').remove();
                        updateRowNumbers();
                    }
                }
            });

            // inisiasi penomoran pertama
            updateRowNumbers();
        });
    </script>
    <script>
        $(document).ready(function() {
            // 1) Inisiasi DataTable
            const table = $('#jobdeskTable').DataTable({
                ordering: false,
                columnDefs: [{
                        orderable: false,
                        targets: 0
                    } // disable sort di kolom expand
                ]
            });

            // 2) Formatter: bikin small-table jobroles
            function formatJobroles(rows) {
                let html = '<table class="table table-sm mb-0"><thead><tr>' +
                    '<th>No.</th><th>Keterangan</th><th>Job Description</th><th>Aksi</th>' +
                    '</tr></thead><tbody>';
                rows.forEach((r, i) => {
                    html += `<tr>
        <td>${i+1}</td>
        <td>${r.description}</td>
        <td>${r.jobdescription}</td>
        <td>
          <button class="btn btn-warning btn-sm edit-jobrole me-1"
                  data-id="${r.id_job_role}"
                  data-description="${r.description}"
                  data-jobdescription="${r.jobdescription}">
            <i class="fas fa-edit"></i>
          </button>
          <button class="btn btn-danger btn-sm delete-jobrole"
                  data-id="${r.id_job_role}">
            <i class="fas fa-trash-alt"></i>
          </button>
        </td>
      </tr>`;
                });
                html += '</tbody></table>';
                return html;
            }

            // 3) Toggle child rows on click
            $('#jobdeskTable tbody').on('click', 'td.details-control', function() {
                const tr = $(this).closest('tr');
                const row = table.row(tr);
                const id = tr.data('id');

                if (row.child.isShown()) {
                    // collapse
                    row.child.hide();
                    $(this).find('i').toggleClass('fa-minus-circle fa-plus-circle');
                } else {
                    // fetch via AJAX
                    $.getJSON(`<?= base_url($role . '/getJobRoles') ?>/${id}`, function(data) {
                        row.child(formatJobroles(data)).show();
                        $(tr).next().find('td').addClass('bg-light');
                        tr.find('td.details-control i').toggleClass('fa-plus-circle fa-minus-circle');
                    });
                }
            });

            // 4) Delegate Edit/Delete di child row
            $('#jobdeskTable tbody').on('click', '.edit-jobrole', function() {
                const id = $(this).data('id');
                const desc = $(this).data('description');
                const job = $(this).data('jobdescription');
                // isi dan tampilkan modal edit top-level
                $('#edit_id_job_role').val(id);
                $('#edit_description').val(desc);
                $('#edit_jobdescription').val(job);
                $('#editJobRoleForm').attr('action', '<?= base_url($role . '/jobRoleUpdate/') ?>' + id);
                $('#editJobRoleModal').modal('show');
            });

            $('#jobdeskTable tbody').on('click', '.delete-jobrole', function() {
                const id = $(this).data('id');
                Swal.fire({
                    title: 'Yakin hapus?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, hapus!'
                }).then(r => {
                    if (r.isConfirmed) {
                        window.location.href = '<?= base_url($role . '/jobRoleDelete/') ?>' + id;
                    }
                });
            });

        });
    </script>
    <script>
        $(document).ready(function() {
            // Initialize DataTable with export options
            // $('#jobdeskTable').DataTable({});

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
            var main_job_role_name = $(this).data('name');
            $('#ModalEdit').find('form').attr('action', '<?= base_url($role . '/mainJobUpdate/') ?>' + id);
            $('#ModalEdit').find('input[name="main_job_role_name"]').val(main_job_role_name);
            $('#ModalEdit').modal('show'); // Show the modal
        });
    </script>

    <?php $this->endSection(); ?>