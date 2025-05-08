<?php $this->extend('layout/template'); ?>
<?php $this->section('content'); ?>
<div class="container-fluid py-4">

    <div class="row">
        <div class="col-xl-12 col-sm-12 mb-xl-0 mb-4 mt-2">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">
                        Import Summary Rosso
                        <p>Tanggal Terakhir Input : <?= $getCurrentInput['input_date'] ?? '-' ?></p>
                    </h4>
                    <!-- Form Import Summary BSMC -->
                    <form action="<?= base_url($role . '/rossoStoreImport') ?>" method="post"
                        enctype="multipart/form-data">
                        <div class="upload-container">
                            <div class="upload-area" id="upload-area">
                                <i class="fas fa-cloud-upload-alt fa-2x"></i>
                                <p>Drag & drop any file here</p>
                                <span>or <label for="file-upload" class="browse-link">browse file</label> from
                                    device</span>
                                <input type="file" id="file-upload" class="file-input" name="file" hidden required>
                            </div>
                            <button type="submit" class="upload-button w-100 mt-3">
                                <i class="fas fa-upload"></i> Upload
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="row my-4">
        <div class="col-xl-12 col-sm-12 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">Human Resource System</p>
                                <h5 class="font-weight-bolder mb-0">
                                    Summary Rosso Per Area
                                </h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <a href="<?= base_url($role . '/downloadTemplateRosso') ?>"
                                class="btn bg-gradient-success me-2">
                                <!-- icon download -->
                                <i class="fas fa-download text-lg opacity-10" aria-hidden="true"></i>
                                Template Excel
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- <div class="row my-4">
        <div class="col-xl-12 col-sm-12 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">Human Resource System</p>
                                <h5 class="font-weight-bolder mb-0">
                                    Summary Rosso Per Area

                                </h5>
                                <a href="<?= base_url($role . '/exportSummaryRosso') ?>" class="btn btn-primary mt-2">Lihat Summary Rosso</a>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <a href="<?= base_url($role . '/downloadTemplateRosso') ?>"
                                class="btn bg-gradient-success me-2">
                                <i class="fas fa-download text-lg opacity-10" aria-hidden="true"></i>
                                Template Excel
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> -->

    <!-- ROW UUPLOAD -->
    <!-- <div class="row my-4">
        <div class="col-xl-12 col-sm-12 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <form action="<?= base_url($role . '/uploadJarum') ?>" method="post" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-6">
                                <label for="">Upload File</label>
                                <input type="file" name="file" id="file" class="form-control" required>
                            </div>
                            <div class="col-md-6 text-center">
                                <label for="">Aksi</label>
                                <button type="submit" class="btn bg-gradient-info mt-3 w-100">Upload</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div> -->
    <div class="row mt-2">
        <?php foreach ($tampilperarea as $key => $ar) : ?>
            <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4 mt-2">
                <a href="<?= base_url($role . '/dataRosso/' . $ar['main_factory']) ?>">
                    <div class="card">
                        <div class="card-body p-3">
                            <div class="row">
                                <div class="col-8">
                                    <div class="numbers">
                                        <p class="text-sm mb-0 text-capitalize font-weight-bold"><?= $ar['main_factory'] ?></p>
                                        <h5 class="font-weight-bolder mb-0">
                                        </h5>
                                    </div>
                                </div>
                                <div class="col-4 text-end">
                                    <div class="icon icon-shape bg-gradient-info shadow text-center border-radius-md">
                                        <i class="ni ni-money-coins text-lg opacity-10" aria-hidden="true"></i>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </a>
            </div>
        <?php endforeach ?>

    </div>

</div>
<!-- datatable -->
<script>
    $(document).ready(function() {
        // Initialize DataTable with export options
        $('#table_report_batch').DataTable({});

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
    const fileInput = document.getElementById('file-upload');
    const uploadArea = document.getElementById('upload-area');

    fileInput.addEventListener('change', (event) => {
        const fileName = event.target.files[0] ? event.target.files[0].name : "No file selected";
        uploadArea.querySelector('p').textContent = `Selected File: ${fileName}`;
    });

    uploadArea.addEventListener('dragover', (event) => {
        event.preventDefault();
        uploadArea.style.backgroundColor = "#e6f5ff";
    });

    uploadArea.addEventListener('dragleave', () => {
        uploadArea.style.backgroundColor = "#ffffff";
    });

    uploadArea.addEventListener('drop', (event) => {
        event.preventDefault();
        fileInput.files = event.dataTransfer.files;
        const fileName = event.dataTransfer.files[0] ? event.dataTransfer.files[0].name : "No file selected";
        uploadArea.querySelector('p').textContent = `Selected File: ${fileName}`;
    });
</script>
<?php $this->endSection(); ?>