<?php $this->extend('layout/template'); ?>
<?php $this->section('content'); ?>

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-xl-12 col-sm-12 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-body position-relative">
                    <div class="row">
                        <div class="col-10">
                            <div class="numbers">
                                <h3 class="">Tambah Data Absensi</h3>
                            </div>
                        </div>
                        <div class="col-2 text-end">
                            <div class="icon icon-shape bg-gradient-info shadow text-center border-radius-md">
                                <i class="fas fa-file-alt text-lg opacity-10"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xl-12 col-sm-12 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-body position-relative">
                    <form action="<?= base_url($role . '/tambahDataAbsensiStore') ?>" method="post">
                        <div class="mb-3">
                            <label for="attendance_date" class="form-label">Tanggal Absensi</label>
                            <div class="input-group">
                                <input type="date" class="form-control" id="attendance_date" name="attendance_date" required>
                                <button type="button" class="btn btn-secondary" id="btnCariKaryawan">Cari Karyawan</button>
                            </div>
                            <div class="mt-2" id="employeeFeedback" style="display:none;"></div>

                            <!-- Pilihan karyawan yang didapat dari controller -->
                            <div class="mb-3 mt-2" id="employeeSelectWrapper" style="display:none;">
                                <label for="employee_select" class="form-label">Pilih Karyawan</label>
                                <select id="employee_select" class="form-select">
                                    <!-- opsi akan diisi lewat JS -->
                                </select>
                            </div>

                        </div>
                        <div class="mb-3">
                            <label for="exampleInputEmail1" class="form-label">ID Karyawan</label>
                            <input type="text" class="form-control" id="employee_id" name="employee_id" required>
                        </div>
                        <div class="mb-3">
                            <label for="exampleInputEmail1" class="form-label">Waktu Masuk</label>
                            <input type="time" class="form-control" id="in_time" name="in_time" required>
                        </div>
                        <div class="mb-3">
                            <label for="exampleInputEmail1" class="form-label">Waktu Keluar</label>
                            <input type="time" class="form-control" id="out_time" name="out_time" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('btnCariKaryawan').addEventListener('click', function() {
        const date = document.getElementById('attendance_date').value;
        const feedback = document.getElementById('employeeFeedback');
        const selectWrapper = document.getElementById('employeeSelectWrapper');
        const select = document.getElementById('employee_select');
        const employeeIdInput = document.getElementById('employee_id');

        feedback.style.display = 'none';
        selectWrapper.style.display = 'none';
        select.innerHTML = '';

        if (!date) {
            feedback.style.display = 'block';
            feedback.className = 'text-danger';
            feedback.textContent = 'Pilih tanggal terlebih dahulu.';
            return;
        }

        feedback.style.display = 'block';
        feedback.className = 'text-muted';
        feedback.textContent = 'Mencari karyawan...';

        fetch("<?= base_url($role . '/getKaryawanByTglAbsen') ?>?date=" + encodeURIComponent(date))
            .then(function(res) {
                if (!res.ok) throw new Error('Network response was not ok');
                return res.json();
            })
            .then(function(data) {
                if (!Array.isArray(data) || data.length === 0) {
                    feedback.className = 'text-warning';
                    feedback.textContent = 'Tidak ditemukan karyawan untuk tanggal tersebut.';
                    employeeIdInput.value = '';
                    return;
                }

                // Isi select dengan data karyawan (asumsi objek {id, name})
                data.forEach(function(k) {
                    const opt = document.createElement('option');
                    opt.value = k.id;
                    opt.textContent = (k.name ? k.name : k.nama) + ' (' + k.id + ')';
                    select.appendChild(opt);
                });

                // tampilkan select dan set default ke yang pertama
                selectWrapper.style.display = '';
                feedback.className = 'text-success';
                feedback.textContent = 'Pilih karyawan dari daftar.';
                employeeIdInput.value = select.value || '';

                // saat mengganti pilihan, isi input employee_id
                select.addEventListener('change', function() {
                    employeeIdInput.value = this.value;
                });
            })
            .catch(function(err) {
                feedback.className = 'text-danger';
                feedback.textContent = 'Terjadi kesalahan saat mengambil data karyawan.';
                console.error(err);
            });
    });
</script>

<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

<script>
    $(document).ready(function() {
        $('#tableAbsensi').DataTable({
            "language": {
                "url": "https://cdn.datatables.net/plug-ins/1.13.6/i18n/id.json"
            }
        });
    });
</script>

<?php $this->endSection(); ?>