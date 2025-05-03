<?php $this->extend('layout/template'); ?>
<?php $this->section('content'); ?>

<!-- Add DataTables CSS -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.24/css/dataTables.bootstrap5.min.css">

<div class="container-fluid py-4">
    <!-- Header -->
    <div class="card mb-4">
        <div class="card-header text-white">
            <h4 class="mb-0">Status Penilaian Karyawan - Area <?= $area ?></h4>
        </div>
    </div>

    <!-- Select Option untuk Periode -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="form-group">
                <label for="periodeSelect">Pilih Periode</label>
                <select id="periodeSelect" class="form-control">
                    <?php if (!empty($periode)) : ?>
                        <?php foreach ($periode as $p) : ?>
                            <option value="<?= $p['id_periode']; ?>">
                                <?= $p['batch_name']; ?> - Periode <?= $p['periode_name']; ?> (<?= date('d/M/Y', strtotime($p['start_date'])); ?> - <?= date('d/M/Y', strtotime($p['end_date'])); ?>)
                            </option>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <option value="">Pilih Periode</option>
                    <?php endif; ?>
                </select>
            </div>
        </div>
    </div>

    <!-- Tabel Evaluasi Karyawan -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table id="evaluationTable" class="table table-striped table-bordered" style="width:100%">
                    <thead>
                        <tr class="text-center">
                            <th>No</th>
                            <th>Kode Kartu</th>
                            <th>Nama Karyawan</th>
                            <th>Shift</th>
                            <th>Bagian</th>
                            <th>Area</th>
                            <th>Status Evaluasi</th>
                        </tr>
                    </thead>
                    <tbody id="evaluationBody">
                        <tr>
                            <td colspan="7" class="text-center">Memuat data...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add DataTables JS -->
<script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap5.min.js"></script>

<script>
    $(document).ready(function() {
        let dataTable = null;

        function loadEvaluationData() {
            const id_periode = $('#periodeSelect').val();
            const area = "<?= $area ?>";

            $.ajax({
                url: "<?= base_url($role . '/evaluasiKaryawan') ?>/" + id_periode + "/" + area,
                method: "GET",
                dataType: "json",
                success: function(data) {
                    let html = "";
                    let no = 1;

                    if (data.length > 0) {
                        data.forEach(function(row) {
                            html += `
                                <tr>
                                    <td class="text-center">${no}</td>
                                    <td>${row.employee_code}</td>
                                    <td>${row.employee_name}</td>
                                    <td>${row.shift}</td>
                                    <td>${row.job_section_name}</td>
                                    <td>${row.factory_name}</td>
                                    <td class="text-center">
                                        ${row.status === 'Sudah Dinilai' 
                                            ? '<span class="badge bg-info">Sudah Dinilai</span>' 
                                            : '<span class="badge bg-danger">Belum Dinilai</span>'}
                                    </td>
                                </tr>
                            `;
                            no++;
                        });
                    } else {
                        html = `<tr><td colspan="7" class="text-center">Tidak ada data evaluasi karyawan</td></tr>`;
                    }

                    // Destroy existing DataTable
                    if (dataTable !== null) {
                        dataTable.destroy();
                    }

                    // Update table body
                    $('#evaluationBody').html(html);

                    // Initialize DataTable
                    dataTable = $('#evaluationTable').DataTable({
                        paging: true,
                        pageLength: 10,
                        lengthChange: true,
                        searching: true,
                        ordering: true,
                        info: true,
                        responsive: true,
                        language: {
                            url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json'
                        }
                    });
                },
                error: function(xhr, status, error) {
                    console.error("AJAX error: " + status + " - " + error);
                    $('#evaluationBody').html(
                        `<tr><td colspan="7" class="text-center">Gagal memuat data</td></tr>`
                    );
                }
            });
        }

        // Initial load
        loadEvaluationData();

        // Reload on periode change
        $('#periodeSelect').change(function() {
            loadEvaluationData();
        });
    });
</script>

<?php $this->endSection(); ?>