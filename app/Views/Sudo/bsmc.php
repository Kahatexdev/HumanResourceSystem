<?php $this->extend('layout/template'); ?>
<?php $this->section('content'); ?>

<?php if (session()->getFlashdata('validation_errors')): ?>
    <div class="alert alert-warning">
        <ul class="mb-0">
            <?php foreach (session()->getFlashdata('validation_errors') as $err): ?>
                <li><?= esc($err) ?></li>
            <?php endforeach ?>
        </ul>
    </div>
<?php endif; ?>
<?php if (session()->getFlashdata('success')): ?>
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Success',
            text: '<?= session()->getFlashdata('success') ?>',
            showConfirmButton: false,
            timer: 1500
        });
    </script>
<?php endif; ?>
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-xl-12 col-sm-12 mb-xl-0 mb-4 mt-2">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">
                        Import Summary BS Mesin
                        <p>Tanggal Terakhir Input : <?= $getCurrentInput['tgl_input'] ?? 0 ?></p>
                    </h4>
                    <!-- form import  Summary BSMC -->
                    <form action="<?= base_url($role . '/importExcelBsmc') ?>" method="post"
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
        <div class="col-12 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row align-items-center">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <p class="text-sm mb-0 text-capitalize font-weight-bold">Human Resource System</p>
                            <h5 class="font-weight-bolder mb-0">Summary Bs Mesin Per Area</h5>
                        </div>
                        <div class="col-md-6">
                            <form action="<?= base_url($role . '/fetchDataBsmc') ?>" method="get">
                                <div class="row g-2">
                                    <div class="col-12 col-sm-8">
                                        <!-- <label for="tgl_input" class="form-label">Tanggal Input:</label> -->
                                        <input type="date" id="tgl_input" name="tgl_input" class="form-control" required>
                                    </div>
                                    <div class="col-12 col-sm-4 d-flex align-items-end">
                                        <button type="submit" class="btn bg-gradient-info w-100">
                                            <i class="fas fa-server text-lg opacity-10 me-2"></i> Fetch Data
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class=" row mt-2">
        <?php foreach ($getArea as $key => $ar) : ?>
            <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4 mt-2">
                <a href="<?= base_url($role . '/dataBsmc/' . $ar['factory_name']) ?>">
                    <div class="card">
                        <div class="card-body p-3">
                            <div class="row">
                                <div class="col-8">
                                    <div class="numbers">
                                        <p class="text-sm mb-0 text-capitalize font-weight-bold"><?= $ar['factory_name'] ?></p>
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