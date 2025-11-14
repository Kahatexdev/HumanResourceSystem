<?php $this->extend('layout/template'); ?>
<?php $this->section('content'); ?>

<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="row mb-3">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Data Records</h4>
            <!-- Optional: tombol aksi (tambah, export, dll) -->
            <div class="btn-group">
                <a href="#" class="btn btn-sm btn-primary">
                    <i class="bi bi-plus-lg"></i> Tambah Data
                </a>
                <a href="#" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-arrow-clockwise"></i> Refresh
                </a>
            </div>
        </div>
    </div>

    <!-- Data Table Card -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-transparent">
                    <h6 class="mb-0">Daftar Data</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover table-bordered mb-0 align-middle" id="shiftTable">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 60px;" class="text-center">#</th>
                                    <th>Nama</th>
                                    <th>Bagian</th>
                                    <th>Shift</th>
                                    <th>Jam Masuk & Pulang</th>
                                    <th style="width: 140px;" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (isset($shift) && ! empty($shift) && is_array($shift)) : ?>
                                    <?php $no = 1; ?>
                                    <?php foreach ($shift as $row) : ?>
                                        <tr>
                                            <td class="text-center"><?= $no++; ?></td>
                                            <td><?= esc($row['employee_name'] ?? '-'); ?></td>
                                            <td><?= esc($row['job_section_name'] ?? '-'); ?></td>
                                            <td><?= esc($row['shift_name'] ?? '-'); ?></td>
                                            <td><?= esc($row['start_time'].' - '. $row['end_time']); ?></td>
                                            <td class="text-center">
                                                <!-- Sesuaikan URL dengan route/controller yang kamu pakai -->
                                                <a href="<?= base_url('roster/edit/' . ($row['id'] ?? '')); ?>"
                                                    class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-pencil-square"></i> Edit
                                                </a>
                                                <form action="<?= base_url('roster/delete/' . ($row['id'] ?? '')); ?>"
                                                    method="post"
                                                    class="d-inline"
                                                    onsubmit="return confirm('Yakin ingin menghapus data ini?');">
                                                    <?= csrf_field(); ?>
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                                        <i class="bi bi-trash"></i> Hapus
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">
                                            Tidak ada data yang tersedia.
                                        </td>
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
        // Initialize DataTable with export options
        $('#shiftTable').DataTable({});

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

<?php $this->endSection(); ?>