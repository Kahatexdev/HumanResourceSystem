<?php $this->extend('layout/template'); ?>
<?php $this->section('content'); ?>
<link href="<?= base_url('assets/css/select2.min.css') ?>" rel="stylesheet" />
<div class="container-fluid py-4">
    <div class="row mt-4">
        <div class="col-xl-12 col-sm-12 mb-xl-0 mb-4 mt-2">
            <div class="card">
                <div class="card-body">
                    <h4>Form Penilaian Karyawan</h4>
                    <form action="<?= base_url($role . '/penilaianCreate') ?>" method="POST" id="formPenilaian">
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label for="id_periode">Batch Penilaian</label>
                                <select class="form-select" id="id_periode" name="id_periode" required>
                                    <option value="">Pilih Batch Penilaian</option>
                                    <?php foreach ($periode as $b) : ?>
                                        <option value="<?= $b['id_periode'] ?>">Periode <?= $b['periode_name'] ?> - <?= $b['batch_name'] ?> (<?= $b['start_date'] ?> s/d <?= $b['end_date'] ?>)</option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-3 mb-3">
                                <label for="nama_bagian">Bagian</label>
                                <select class="form-select" id="nama_bagian" name="nama_bagian" required>
                                    <option value="">Pilih Bagian</option>
                                    <?php foreach ($namabagian as $b): ?>
                                        <option value="<?= $b['job_section_name'] ?>"><?= $b['job_section_name'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-3 mb-3">
                                <label for="area_utama">Area Utama</label>
                                <select class="form-select" id="area_utama" name="area_utama" required>
                                    <option value="">Pilih Area Utama</option>
                                    <?php foreach ($areaUtama as $f): ?>
                                        <option value="<?= $f['main_factory'] ?>"><?= $f['main_factory'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-3 mb-3">
                                <label for="area">Area</label>
                                <select class="form-select" id="area" name="area">
                                    <option value="">Pilih Area</option>
                                    <?php foreach ($area as $fac): ?>
                                        <option value="<?= $fac['factory_name'] ?>"><?= $fac['factory_name'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="karyawan">Pilih Karyawan</label>
                                <select class="form-select select2-multiple" id="karyawan" name="karyawan[]" multiple required>
                                    <option value="">Pilih Karyawan</option>
                                </select>
                            </div>
                        </div>
                        <input type="hidden" class="form-control" id="id_jobrole" name="id_jobrole" required>
                        <button type="submit" class="btn bg-gradient-info w-100">Buat Form Penilaian</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="<?= base_url('assets/js/select2.min.js') ?>"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const namaBagian = document.getElementById('nama_bagian');
        const areaUtama = document.getElementById('area_utama');
        const areaSelect = document.getElementById('area');
        const karyawanSel = document.getElementById('karyawan');

        areaSelect.addEventListener('change', function() {
            const namaBag = namaBagian.value;
            const areaUt = areaUtama.value;
            const area = this.value;

            // Reset dropdown
            karyawanSel.innerHTML = '<option value="">Pilih Karyawan</option>';

            if (namaBag && areaUt && area) {
                fetch(`<?= base_url($role . '/getKaryawan') ?>` +
                        `?nama_bagian=${encodeURIComponent(namaBag)}` +
                        `&area_utama=${encodeURIComponent(areaUt)}` +
                        `&area=${encodeURIComponent(area)}`)
                    .then(res => res.json())
                    .then(json => {
                        // json.data adalah array karyawan
                        json.data.forEach(item => {
                            const opt = document.createElement('option');
                            opt.value = item.id_employee; // gunakan id_employee
                            opt.textContent = `${item.employee_name} — ${item.employee_code}`;
                            karyawanSel.appendChild(opt);
                        });

                        // Jika pakai Select2, trigger ulang:
                        // $(karyawanSel).trigger('change.select2');
                    })
                    .catch(err => console.error('Error fetching karyawan:', err));
            }
        });
    });

    $(document).ready(function() {
        // Aktifkan library select2 untuk multiple select
        $('#karyawan').select2({
            placeholder: "Pilih Karyawan",
            allowClear: true
        });

    });
</script>
<script>
    $(document).ready(function() {
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