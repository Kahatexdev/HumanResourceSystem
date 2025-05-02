<?php $this->extend('layout/template'); ?>
<?php $this->section('content'); ?>
<div class="container-fluid py-4">
    <div class="row my-4">
        <div class="col-xl-12 col-sm-12 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-6">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">Skill Mapping</p>
                                <h5 class="font-weight-bolder mb-0">
                                    Data Karyawan Berdasarkan Area
                                </h5>
                            </div>
                        </div>
                        <div class="col-6 ">


                            <div class="form-group">
                                <select class="form-control" id="planSelect">
                                    <option value="">List Plan Jalan Mesin</option>
                                    <?php foreach ($listplan as $judul) : ?>
                                        <option value="<?= $judul['judul'] ?>"><?= $judul['judul'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>


<div class="row">
    <?php foreach ($tampildata as $key => $ar) : ?>
        <?php
        if ($ar['main_factory'] == "-" || $ar['main_factory'] == NULL) {
            $judul = "Area belum di input";
            $area = "EMPTY";
        } else {
            $judul = $ar['main_factory'];
            $area = $ar['main_factory'];
        } ?>
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4 mt-2">
            <a href="<?= base_url($role . '/dataKaryawan/' . $area) ?>">
                <div class="card">
                    <div class="card-body p-3">
                        <div class="row">

                            <div class="col-8">
                                <div class="numbers">
                                    <p class="text-sm mb-0 text-capitalize font-weight-bold"><?= $judul ?></p>
                                    <h5 class="font-weight-bolder mb-0">
                                    </h5>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-gradient-info shadow text-center border-radius-md">
                                    <i class="ni ni-building text-lg opacity-10" aria-hidden="true"></i>
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
<script type="text/javascript">
    function confirmDelete(id) {
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Data yang dihapus tidak dapat dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal',
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "<?= base_url('TrainingSchool/karyawanDelete/') ?>" + id;
            }
        })
    }
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
<script>
    document.getElementById('planSelect').addEventListener('change', function() {
        var judul = this.value.trim(); // hapus spasi berlebih
        var baseUrl = 'http://172.23.44.14/CapacityApps/public/api/exportPlanningJlMc/';
        if (judul) {
            var link = baseUrl + encodeURIComponent(judul); // encode biar aman
            window.location.href = link;
        }
    });
</script>

<?php $this->endSection(); ?>