<?php $this->extend('layout/template'); ?>
<?php $this->section('content'); ?>

<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="row mb-3">
        <div class="col-xl-12 col-sm-12 mb-xl-0 mb-2">
            <div class="card">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="font-weight-bolder mb-0">
                                <a href="" # class="btn bg-gradient-info">
                                    <!-- icon data Jam Kerja Karyawan -->
                                    <i class="fas fa-clock text-lg opacity-10" aria-hidden="true"></i>
                                </a>
                                Master Jam Kerja
                            </h4>
                        </div>
                        <div>
                            <div class="d-flex justify-content-between">

                                <a href="<?= base_url($role . '/exportKaryawan/') ?>"
                                    class="btn bg-gradient-primary me-2">
                                    <!-- icon download -->
                                    <i class="fas fa-file-excel text-lg opacity-10" aria-hidden="true"></i>
                                    Export Excel
                                </a>
                                <a
                                    class="btn bg-gradient-info add-btn" data-bs-toggle="modal" data-bs-target="#addMasterJam">
                                    <!-- icon tambah karyawan-->
                                    <i class="fas fa-user-plus text-lg opacity-10" aria-hidden="true"></i>
                                    Jam Kerja
                                </a>
                                <div> &nbsp;</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- modal input jam kerja -->
        <!-- modal input jam kerja -->
        <div class="modal fade bd-example-modal-lg" id="addMasterJam" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitleJam">Tambah Shift / Jam Kerja</h5>
                        <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <form id="formMasterJam" action="<?= base_url($role . '/storeShiftDef'); ?>" method="post">
                        <div class="modal-body">
                            <!-- id hidden untuk edit -->
                            <input type="hidden" name="id_shift" id="id_shift">

                            <!-- Nama Shift -->
                            <div class="mb-3">
                                <label for="shift_name" class="form-label">Nama Shift</label>
                                <input type="text" class="form-control" name="shift_name" id="shift_name" required>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-12">
                                    <div class="card border">
                                        <div class="card-body">
                                            <div class="row g-2 align-items-end work-group">
                                                <div class="col-md-4">
                                                    <label class="form-label">Jam Masuk</label>
                                                    <input type="time" class="form-control" name="start_time" id="start_time" required>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label">Jam Pulang</label>
                                                    <input type="time" class="form-control" name="end_time" id="end_time" required>
                                                </div>
                                                <div class="col-md-2">
                                                    <label class="form-label">Istirahat (menit)</label>
                                                    <input type="number" class="form-control" name="break_time" id="break_time" min="0" value="0">
                                                </div>
                                                <div class="col-md-2">
                                                    <label class="form-label">Toleransi (menit)</label>
                                                    <input type="number" class="form-control" name="grace_min" id="grace_min" min="0" value="0">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div><!-- /.modal-body -->

                        <div class="modal-footer">
                            <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn bg-gradient-info" id="btnSaveJam">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <!-- Data Table Card -->
    <div class="row mt-1">
        <div class="col-xl-12 col-sm-12 mb-xl-0 mb-4 mt-2">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">
                        Tabel Master Jam Kerja
                    </h4>
                    <div class="table-responsive">
                        <table id="karyawanTable" class="table table-striped table-hover table-bordered w-100">
                            <thead>
                                <th>No</th>
                                <th>Nama Shift</th>
                                <th>Jam Masuk</th>
                                <th>Jam Pulang</th>
                                <th>Istirahat(menit)</th>
                                <th>Toleransi Waktu(menit)</th>
                                <th>Aksi</th>
                            </thead>
                            <tbody>
                                <?php if (!empty($shiftDef)) : ?>
                                    <?php foreach ($shiftDef as $shiftDef) : ?>
                                        <tr>
                                            <td><?php static $no = 1;
                                                echo $no++; ?></td>
                                            <td><?= $shiftDef['shift_name'] ?></td>
                                            <td><?= $shiftDef['start_time'] ?></td>
                                            <td><?= $shiftDef['end_time'] ?></td>
                                            <td><?= $shiftDef['break_time'] ?></td>
                                            <td><?= $shiftDef['grace_min'] ?></td>
                                            <td>
                                                <button
                                                    type="button"
                                                    class="btn btn-warning btn-edit-master-jam"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#addMasterJam"
                                                    data-id="<?= $shiftDef['id_shift']; ?>"
                                                    data-shift_name="<?= esc($shiftDef['shift_name']); ?>"
                                                    data-start="<?= $shiftDef['start_time']; ?>"
                                                    data-end="<?= $shiftDef['end_time']; ?>"
                                                    data-break="<?= $shiftDef['break_time']; ?>"
                                                    data-grace="<?= $shiftDef['grace_min']; ?>">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach ?>
                                <?php else : ?>
                                    <tr>
                                        <td colspan="14" class="text-center">No Master found</td>
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

<script>
    $(document).ready(function() {
        // id tabel kamu: karyawanTable, jadi init ini
        $('#karyawanTable').DataTable({});

        // base url untuk update
        const storeUrl = "<?= base_url($role . '/storeShiftDef'); ?>";
        const updateUrl = "<?= base_url($role . '/updateShiftDef'); ?>"; // nanti /{id}

        // Klik tombol "Jam Kerja" (tambah)
        $('.add-btn').on('click', function() {
            // mode tambah
            $('#modalTitleJam').text('Tambah Shift / Jam Kerja');
            $('#btnSaveJam').text('Save');
            $('#formMasterJam').attr('action', storeUrl);

            // reset form
            $('#id_shift').val('');
            $('#shift_name').val('');
            $('#start_time').val('');
            $('#end_time').val('');
            $('#break_time').val(0);
            $('#grace_min').val(0);
        });

        // Klik tombol edit di tabel
        $('.btn-edit-master-jam').on('click', function() {
            const id = $(this).data('id');
            const name = $(this).data('shift_name');
            const start = $(this).data('start');
            const end = $(this).data('end');
            const brk = $(this).data('break');
            const grace = $(this).data('grace');

            // mode edit
            $('#modalTitleJam').text('Edit Shift / Jam Kerja');
            $('#btnSaveJam').text('Update');
            $('#formMasterJam').attr('action', updateUrl + '/' + id);

            // isi form
            $('#id_shift').val(id);
            $('#shift_name').val(name);
            $('#start_time').val(start);
            $('#end_time').val(end);
            $('#break_time').val(brk);
            $('#grace_min').val(grace);
        });

        // SweetAlert flash message (punyamu tetap)
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


<?php $this->endSection(); ?>