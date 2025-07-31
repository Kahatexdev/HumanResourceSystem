<?php $this->extend('layout/template'); ?>
<?php $this->section('content'); ?>

<style>
    /* Soft UI Color Scheme */
    :root {
        --primary-color: #4e73df;
        --secondary-color: #1cc88a;
        --light-gray: #f7f7f7;
        --medium-gray: #e2e8f0;
        --dark-gray: #2d3436;
    }

    .container-fluid {
        /* background-color: var(--light-gray); */
        /* border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); */
    }

    .card {
        border: none;
        border-radius: 15px;
        background: #ffffff;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease-in-out;
    }

    .card:hover {
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
    }

    .card-body {
        padding: 20px;
    }

    h5.card-title {
        font-size: 1.25rem;
        /* color: var(--dark-gray); */
        font-weight: 500;
    }

    .btn {
        border-radius: 10px;
        font-weight: bold;
    }

    .btn.bg-gradient-info {
        /* background: linear-gradient(45deg, #4e73df, #2a64c7); */
        color: white;
    }

    .btn.bg-gradient-info:hover {
        /* background: linear-gradient(45deg, #2a64c7, #4e73df); */
    }

    .btn.bg-gradient-secondary {
        /* background: linear-gradient(45deg, #6c757d, #495057); */
        color: white;
    }

    table {
        width: 100%;
        border-radius: 10px;
        border-collapse: collapse;
        margin-top: 20px;
        background-color: var(--light-gray);
    }

    table th,
    table td {
        padding: 12px;
        text-align: left;
        border-bottom: 1px solid var(--medium-gray);
        color: var(--dark-gray);
    }

    table th {
        /* background-color: var(--primary-color); */
        color: white;
    }

    table td input[type="number"] {
        border-radius: 10px;
        padding: 8px;
        border: 1px solid var(--medium-gray);
        width: 100%;
    }

    table td input[type="number"]:focus {
        outline: none;
        /* border-color: var(--primary-color); */
    }

    .form-group {
        margin-bottom: 15px;
    }

    @media (max-width: 768px) {
        .col-xl-6 {
            width: 100%;
        }
    }

    @media (min-width: 992px) {
        .modal-dialog-custom {
            max-width: 90%;
        }
    }

    .modal-content {
        border-radius: 10px;
    }

    .modal-header {
        border-top-left-radius: 10px;
        border-top-right-radius: 10px;
    }

    .modal-footer {
        border-bottom-left-radius: 10px;
        border-bottom-right-radius: 10px;
    }
</style>
<link href="https://cdnjs.cloudflare.com/ajax/libs/soft-ui-dashboard/2.1.0/css/soft-ui-dashboard.min.css"
    rel="stylesheet" />
<div class="container-fluid py-4">
    <!-- Flash message for success and error -->
    <?php if (session()->getFlashdata('success')) : ?>
        <script>
            $(document).ready(function() {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    html: '<?= session()->getFlashdata('success') ?>',
                });
            });
        </script>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')) : ?>
        <script>
            $(document).ready(function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    html: '<?= session()->getFlashdata('error') ?>',
                });
            });
        </script>
    <?php endif; ?>

    <div class="row my-4">
        <div class="col-xl-12 col-sm-12 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="font-weight-bolder mb-0">
                                <a href="#" class="btn bg-gradient-info">
                                    <i class="fas fa-solid fa-tasks text-sm opacity-10"></i>
                                </a>
                                Form Penilaian Karyawan
                            </h4>
                        </div>
                        <div>
                            <a href="<?= base_url(session()->get('role') . '/dataPenilaian') ?>" class="btn bg-gradient-secondary btn-sm">
                                <i class="fas fa-solid fa-arrow-left text-sm opacity-10"></i>
                                Kembali
                            </a>
                            <!-- button modal instruksi pengisian nilai -->
                            <button class="btn bg-gradient-info btn-sm" data-bs-toggle="modal" data-bs-target="#modal-instruksi">
                                <i class="fas fa-info-circle text-sm opacity-10"></i> Instruksi
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <form action="<?= base_url(session()->get('role') . '/penilaianStore') ?>" method="post" id="evaluationForm">
        <div class="row mt-4">
            <div class="col-xl-12 col-sm-12 mb-xl-0 mb-4 mt-2">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <h6>Jumlah Karyawan yang dinilai : <?= $karyawanCount ?> (Org)</h6>

                        </div>
                    </div>
                </div>
            </div>
            <?php foreach ($karyawan as $k) : ?>
                <div class="col-xl-6 col-sm-12 mb-xl-0 mb-4 mt-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="mb-3">[<?= htmlspecialchars($k['employee_code'], ENT_QUOTES, 'UTF-8') ?>] <?= htmlspecialchars($k['employee_name'], ENT_QUOTES, 'UTF-8') ?></h5>
                            <hr>
                            <?php foreach ($jobdescWithKet as $keterangan => $deskripsiList) : ?>
                                <!-- Tampilkan Keterangan -->
                                <h6 class="mt-4">
                                    <!-- <strong>Memperbaiki mesin sesuai dengan tingkat kesulitannya</strong> -->
                                </h6>

                                <!-- Tabel Deskripsi Pekerjaan -->
                                <table class="table table-bordered mt-2">
                                    <thead>
                                        <tr>
                                            <th class="bg-gradient-dark"><?= htmlspecialchars($keterangan, ENT_QUOTES, 'UTF-8') ?></th>
                                            <th class="bg-gradient-dark">Nilai</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($deskripsiList as $deskripsi) : ?>
                                            <tr>
                                                <td class="text-wrap"><?= htmlspecialchars($deskripsi, ENT_QUOTES, 'UTF-8') ?></td>
                                                <td>
                                                    <input type="number" class="form-control nilai-input" data-karyawan-id="<?= $k['id_employee'] ?>" data-jobdesc="<?= htmlspecialchars($deskripsi, ENT_QUOTES, 'UTF-8') ?>" name="nilai[<?= $k['id_employee'] ?>][<?= $deskripsi ?>]" placeholder="Nilai" min="0" max="6" required>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php endforeach; ?>

                            <div class="mt-3">
                                <input type="hidden" class="index-nilai" name="index_nilai[<?= $k['id_employee'] ?>]">
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
            <?php foreach ($temp as $key => $value) : ?>
                <?php if (is_array($value)) : ?>
                    <?php foreach ($value as $item) : ?>
                        <input type="hidden" name="<?= $key ?>[]" value="<?= $item['id_employee'] ?>">
                    <?php endforeach; ?>
                <?php else : ?>
                    <input type="hidden" name="<?= $key ?>" value="<?= $value ?>">
                <?php endif; ?>
            <?php endforeach; ?>
        </div>

        <div class="mt-4">
            <button type="submit" id="submitBtn" class="btn bg-gradient-info w-100">
            <span id="btnText"><i class="fas fa-save opacity-10"></i> Simpan</span>
            <span id="btnLoading" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
            <span id="btnWait" class="d-none ms-2">Mohon tunggu...</span>
            </button>
        </div>
        <div id="overlayLoading" style="display:none;position:fixed;top:0;left:0;width:100vw;height:100vh;z-index:9999;background:rgba(255,255,255,0.7);justify-content:center;align-items:center;">
            <div class="spinner-border text-info" style="width:3rem;height:3rem;" role="status"></div>
        </div>
        <script>
            document.getElementById('evaluationForm').addEventListener('submit', function(e) {
            var btn = document.getElementById('submitBtn');
            btn.disabled = true;
            document.getElementById('btnText').classList.add('d-none');
            document.getElementById('btnLoading').classList.remove('d-none');
            document.getElementById('btnWait').classList.remove('d-none');
            var overlay = document.getElementById('overlayLoading');
            overlay.style.display = 'flex';
            });
        </script>
    </form>
</div>

<div class="modal fade" id="modal-instruksi" tabindex="-1" aria-labelledby="modal-instruksi" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-custom">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Keterangan</h5>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-6">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th class="bg-gradient-dark text-center" colspan="2">GRADE</th>
                                </tr>
                                <tr>
                                    <th class="bg-gradient-dark text-center">Nilai</th>
                                    <th class="bg-gradient-dark text-center">Deskripsi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="text-center">1</td>
                                    <td class="text-wrap">Tidak mengetahui jobdesc</td>
                                </tr>
                                <tr>
                                    <td class="text-center">2</td>
                                    <td class="text-wrap">Mengetahui jobdesc namun tidak menjalankan</td>
                                </tr>
                                <tr>
                                    <td class="text-center">3</td>
                                    <td class="text-wrap">Menjalankan jobdesc namun masih perlu training</td>
                                </tr>
                                <tr>
                                    <td class="text-center">4</td>
                                    <td class="text-wrap">Menjalankan jobdesc tapi perlu pengawasan</td>
                                </tr>
                                <tr>
                                    <td class="text-center">5</td>
                                    <td class="text-wrap">Selalu Menjalankan jobdesc dengan benar</td>
                                </tr>
                                <tr>
                                    <td class="text-center">6</td>
                                    <td class="text-wrap">Mampu menjadi trainer</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-6">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th class="bg-gradient-dark text-center" colspan="2">Indikator 6S</th>
                                </tr>
                                <tr>
                                    <th class="bg-gradient-dark text-center">Nilai</th>
                                    <th class="bg-gradient-dark text-center">Deskripsi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="text-center">1</td>
                                    <td class="text-wrap">Tidak mengetahui 6S</td>
                                </tr>
                                <tr>
                                    <td class="text-center">2</td>
                                    <td class="text-wrap">Mengetahui namun tidak menjalankan</td>
                                </tr>
                                <tr>
                                    <td class="text-center">3</td>
                                    <td class="text-wrap">Menjalankan namun tidak sering (cenderung tidak menjalankan)</td>
                                </tr>
                                <tr>
                                    <td class="text-center">4</td>
                                    <td class="text-wrap">Menjalankan tapi tidak sering (cenderung menjalankan)</td>
                                </tr>
                                <tr>
                                    <td class="text-center">5</td>
                                    <td class="text-wrap">Disiplin menjalankan 6S</td>
                                </tr>
                                <tr>
                                    <td class="text-center">6</td>
                                    <td class="text-wrap">Peduli dengan lingkungan sekitar dan mampu menjadi trainer</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        var myModal = new bootstrap.Modal(document.getElementById("modal-instruksi"));
        myModal.show();
    });
</script>

<?php $this->endSection(); ?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/soft-ui-dashboard/2.1.0/js/soft-ui-dashboard.min.js"></script>