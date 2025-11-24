<?php $this->extend('layout/template'); ?>
<?php $this->section('content'); ?>

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-xl-12 col-sm-12 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="row">
                        <div class="col-10">
                            <div class="numbers">
                                <h3 class="">Filter Data Absensi</h3>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-2">

                        <div class="col-md-3">
                            <label for="">Tanggal Awal</label>
                            <input type="date" class="form-control" id="tglAwal">
                        </div>

                        <div class="col-md-3">
                            <label for="">Tanggal Akhir</label>
                            <input type="date" class="form-control" id="tglAkhir">
                        </div>

                        <div class="col-md-3">
                            <label for="">Filter</label>
                            <button class="btn btn-info w-100" id="btnFilter">
                                <i class="fas fa-filter me-2"></i>
                            </button>
                        </div>

                        <div class="col-md-3">
                            <label for="">Tambah Absen</label>
                            <a href="<?= base_url($role . '/tambahDataAbsensi') ?>" class="btn btn-info w-100">
                                <i class="fas fa-plus me-2"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body px-0 pt-0 pb-2">
        <div class="table-responsive">
            <table class="table align-items-center mb-0 table-soft" id="tableAbsensi">
                <thead>
                    <tr>
                        <th class="text-center">No</th>
                        <th>Tanggal</th>
                        <th>NIK</th>
                        <th>Nama Karyawan</th>
                        <th>Shift</th>
                        <th class="text-center">Masuk</th>
                        <th class="text-center">Istirahat</th>
                        <th class="text-center">Kembali</th>
                        <th class="text-center">Pulang</th>
                        <th class="text-end">Kerja</th>
                        <th class="text-end">Break</th>
                        <th class="text-end">Telat</th>
                        <th class="text-end">P.Cepat</th>
                        <th class="text-end">Lembur</th>
                        <th class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; ?>
                    <?php foreach ($results as $r): ?>
                        <tr>
                            <td class="text-center"><?= $no++; ?></td>
                            <td><?= $r['work_date']; ?></td>
                            <td><?= $r['nik']; ?></td>
                            <td><?= $r['employee_name']; ?></td>
                            <td><?= $r['shift_name']; ?></td>
                            <td class="text-center"><?= $r['in_time']; ?></td>
                            <td class="text-center"><?= $r['break_out_time']; ?></td>
                            <td class="text-center"><?= $r['break_in_time']; ?></td>
                            <td class="text-center"><?= $r['out_time']; ?></td>
                            <td class="text-end"><?= $r['total_work_min']; ?></td>
                            <td class="text-end"><?= $r['total_break_min']; ?></td>
                            <td class="text-end"><?= $r['late_min']; ?></td>
                            <td class="text-end"><?= $r['early_leave_min']; ?></td>
                            <td class="text-end"><?= $r['overtime_min']; ?></td>
                            <td class="text-center"><?= $r['status_code']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#tableAbsensi').DataTable({
            "ordering": true,
            "paging": true,
            "searching": true
        });
    });
    document.getElementById('btnFilter').addEventListener('click', function() {
        let tglAwal = document.getElementById('tglAwal').value;
        let tglAkhir = document.getElementById('tglAkhir').value;

        if (tglAwal === '' || tglAkhir === '') {
            alert('Pilih tanggal awal dan tanggal akhir terlebih dahulu.');
            return;
        }

        const baseUrl = "<?= base_url($role . '/reportDataAbsensi'); ?>";
        window.location.href = baseUrl + "?tglAwal=" + tglAwal + "&tglAkhir=" + tglAkhir;
    });
</script>

<?php $this->endSection(); ?>