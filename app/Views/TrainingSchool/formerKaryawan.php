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
                                Data Resign Karyawan
                            </h4>
                        </div>
                        <div>
                            <div class="d-flex justify-content-between">

                                <a href="<?= base_url($role . '/exportFormerKaryawan') ?>"
                                    class="btn bg-gradient-success me-2">
                                    <!-- icon download -->
                                    <i class="fas fa-file-excel text-lg opacity-10" aria-hidden="true"></i>
                                    Export Excel
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row mt-1">
        <div class="col-xl-12 col-sm-12 mb-xl-0 mb-4 mt-2">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">
                        Tabel Data Resign Karyawan
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
                                <th>Tgl Resign</th>
                                <th>Alasan Resign</th>
                                <th>Diupdate Oleh</th>
                                <th>Action</th>
                            </thead>
                            <tbody>
                                <?php if (!empty($karyawan)) : ?>
                                    <?php foreach ($karyawan as $karyawan) : ?>
                                        <tr>
                                            <td><?= $karyawan['employee_code'] ?></td>
                                            <td><?= $karyawan['employee_name'] ?></td>
                                            <td><?= $karyawan['shift'] ?></td>

                                            <td><?= $karyawan['employment_status_name'] ?></td>

                                            <td><?= $karyawan['job_section_name'] . ' - ' . $karyawan['main_factory'] . ' - ' . $karyawan['factory_name'] ?></td>
                                            <td><?= $karyawan['date_of_leaving'] ?></td>
                                            <td><?= $karyawan['reason_for_leaving'] ?></td>
                                            <td><?= $karyawan['updated_by'] ?></td>
                                            <td>
                                                <button type="button"
                                                    class="btn bg-gradient-success btn-sm"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#modalReactive"
                                                    data-id="<?= $karyawan['id_former_employee']; ?>"
                                                    data-name="<?= $karyawan['employee_name']; ?>"
                                                    data-factory="<?= $karyawan['id_factory']; ?>"
                                                    data-job="<?= $karyawan['id_job_section']; ?>">
                                                    Reactive
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach ?>
                                <?php else : ?>
                                    <tr>
                                        <td colspan="9" class="text-center">No Karyawan found</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modalReactive" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form action="<?= base_url($role . '/formerEmployee/reactiveKaryawan') ?>" method="POST" id="formReactive">
                <input type="hidden" name="id_former_employee" id="modal_id">

                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Reaktivasi Karyawan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <p>Aktifkan kembali karyawan: <b id="modal_name"></b>?</p>
                        <div class="mb-3">
                            <label>Factory</label>
                            <select name="factory" class="form-control" id="modal_factory" required>
                                <?php foreach ($factories as $factory): ?>
                                    <option value="<?= $factory['id_factory'] ?>"><?= $factory['factory_name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label>Job Section</label>
                            <select name="job_section" class="form-control" id="modal_job" required>
                                <?php foreach ($jobSections as $job): ?>
                                    <option value="<?= $job['id_job_section'] ?>"><?= $job['job_section_name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label>Keterangan</label>
                            <textarea name="keterangan" class="form-control" required></textarea>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Simpan & Aktifkan</button>
                    </div>

                </div>
            </form>
        </div>
    </div>

</div>

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
    document.addEventListener('DOMContentLoaded', function() {
        var modal = document.getElementById('modalReactive');
        modal.addEventListener('show.bs.modal', function(event) {
            var button = event.relatedTarget;
            var id = button.getAttribute('data-id');
            var name = button.getAttribute('data-name');
            var factory = button.getAttribute('data-factory');
            var job = button.getAttribute('data-job');

            modal.querySelector('#modal_id').value = id;
            modal.querySelector('#modal_name').innerText = name;

            // ✅ Set default pilihan factory
            var selectFactory = modal.querySelector('#modal_factory');
            selectFactory.value = factory;

            // ✅ Set default pilihan job
            var selectJob = modal.querySelector('#modal_job');
            selectJob.value = job;
        });
    });
</script>



<?php $this->endSection(); ?>