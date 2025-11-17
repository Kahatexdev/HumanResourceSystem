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
                                <a href="#" class="btn bg-gradient-info">
                                    <i class="fas fa-clock text-lg opacity-10"></i>
                                </a>
                                Master Jam Kerja
                            </h4>
                        </div>
                        <div>
                            <div class="d-flex justify-content-between">

                                <!-- <a href="<?= base_url($role . '/exportKaryawan/') ?>"
                                    class="btn bg-gradient-primary me-2">
                                    <i class="fas fa-file-excel text-lg opacity-10"></i>
                                    Export Excel
                                </a> -->

                                <a class="btn bg-gradient-info add-btn"
                                    data-bs-toggle="modal"
                                    data-bs-target="#addMasterJam">
                                    <i class="fas fa-user-plus text-lg opacity-10"></i>
                                    Jam Kerja
                                </a>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- INCLUDE MODAL -->
        <?= $this->include('Absensi/Master/modal'); ?>
    </div>

    <!-- DataTable -->
    <div class="row mt-1">
        <div class="col-xl-12 col-sm-12 mb-xl-0 mb-4 mt-2">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Tabel Master Jam Kerja</h4>

                    <div class="table-responsive">
                        <table id="masterTable" class="table table-striped table-bordered w-100">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Shift</th>
                                    <th>Jam Masuk</th>
                                    <th>Jam Pulang</th>
                                    <th>Istirahat</th>
                                    <th>Toleransi</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php if (!empty($shiftDef)) : ?>
                                    <?php $no = 1; ?>
                                    <?php foreach ($shiftDef as $s) : ?>
                                        <tr>
                                            <td><?= $no++; ?></td>
                                            <td><?= esc($s['shift_name']) ?></td>
                                            <td><?= $s['start_time'] ?></td>
                                            <td><?= $s['end_time'] ?></td>
                                            <td><?= $s['break_time'] ?></td>
                                            <td><?= $s['grace_min'] ?></td>
                                            <td>
                                                <button
                                                    type="button"
                                                    class="btn btn-warning btn-edit-master-jam"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#addMasterJam"
                                                    data-id="<?= $s['id_shift']; ?>"
                                                    data-shift_name="<?= esc($s['shift_name']); ?>"
                                                    data-start="<?= $s['start_time']; ?>"
                                                    data-end="<?= $s['end_time']; ?>"
                                                    data-break="<?= $s['break_time']; ?>"
                                                    data-grace="<?= $s['grace_min']; ?>">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach ?>
                                <?php else : ?>
                                    <tr>
                                        <td colspan="7" class="text-center">No data found</td>
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

<!-- JS tetap sama -->
<script>
    $(document).ready(function() {

        $('#masterTable').DataTable();

        const storeUrl = "<?= base_url($role . '/storeShiftDef'); ?>";
        const updateUrl = "<?= base_url($role . '/updateShiftDef'); ?>";

        // Tambah
        $('.add-btn').on('click', function() {
            $('#modalTitleJam').text('Tambah Shift / Jam Kerja');
            $('#btnSaveJam').text('Save');
            $('#formMasterJam').attr('action', storeUrl);

            $('#id_shift').val('');
            $('#shift_name').val('');
            $('#start_time').val('');
            $('#end_time').val('');
            $('#break_time').val(0);
            $('#grace_min').val(0);
        });

        // Edit
        // Delegated event ke tabel (ikut semua halaman)
        $('#masterTable').on('click', '.btn-edit-master-jam', function() {
            const id = $(this).data('id');
            const name = $(this).data('shift_name');
            const start = $(this).data('start');
            const end = $(this).data('end');
            const brk = $(this).data('break');
            const grace = $(this).data('grace');

            $('#modalTitleJam').text('Edit Shift / Jam Kerja');
            $('#btnSaveJam').text('Update');
            $('#formMasterJam').attr('action', updateUrl + '/' + id);

            $('#id_shift').val(id);
            $('#shift_name').val(name);
            $('#start_time').val(start);
            $('#end_time').val(end);
            $('#break_time').val(brk);
            $('#grace_min').val(grace);
        });

        // Flash swal
        <?php if (session()->getFlashdata('success')) : ?>
            Swal.fire({
                icon: 'success',
                title: 'Success',
                html: '<?= session()->getFlashdata('success') ?>'
            });
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')) : ?>
            Swal.fire({
                icon: 'error',
                title: 'Error',
                html: '<?= session()->getFlashdata('error') ?>'
            });
        <?php endif; ?>

    });
</script>

<?php $this->endSection(); ?>