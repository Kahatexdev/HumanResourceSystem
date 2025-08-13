<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="apple-touch-icon" sizes="76x76" href="<?= base_url('assets/img/apple-icon.png') ?>">
    <link rel="icon" type="image/png" href="<?= base_url('assets/img/logo-ct.png') ?>">
    <title><?= $title ?? 'Dashboard' ?></title>

    <!--     Fonts and icons     -->
    <link href="<?php base_url('assets/fonts/open_sans_family.css') ?>" rel="stylesheet" />
    <!-- Nucleo Icons -->
    <link href="<?= base_url('assets/css/nucleo-icons.css') ?>" rel=" stylesheet" />
    <link href="<?= base_url('assets/css/nucleo-svg.css') ?>" rel=" stylesheet" />
    <!-- Font Awesome Icons -->
    <script src="<?= base_url('assets/fa/js/fontawesome.min.js') ?>"></script>
    <link href="<?= base_url('assets/fa/css/all.min.css') ?>" rel=" stylesheet" />
    <!-- CSS Files -->
    <link id="pagestyle" href="<?= base_url('assets/css/soft-ui-dashboard.css?v=1.0.7') ?>" rel="stylesheet" />
    <!--  -->
    <script src="<?= base_url('assets/js/jquery/jquery-3.7.1.min.js') ?>" crossorigin="anonymous"></script>
    <link href="<?= base_url('assets/css/dataTables.dataTables.css') ?>" rel="stylesheet">
    <script src="<?= base_url('assets/js/dataTables.min.js') ?>"></script>
    <link rel="stylesheet" href="<?= base_url('assets/css/jquery.dataTables.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/buttons.dataTables.min.css') ?>">
    <!-- <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.3.4/css/buttons.dataTables.min.css"> -->
    <style>
        .upload-container {
            text-align: center;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 10px;
            border: 1px solid #e3e6f0;
        }

        .upload-area {
            border: 2px dashed #007bff;
            padding: 30px;
            border-radius: 10px;
            background-color: #ffffff;
            color: #007bff;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .upload-area:hover {
            background-color: #e9f4ff;
        }

        .upload-area i {
            font-size: 2rem;
            margin-bottom: 10px;
        }

        .upload-area p {
            font-size: 16px;
            font-weight: bold;
        }

        .browse-link {
            color: #007bff;
            text-decoration: underline;
            cursor: pointer;
        }

        .upload-button {
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 10px;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .upload-button:hover {
            background-color: #0056b3;
        }
    </style>


</head>

<body class="g-sidenav-show  bg-gray-100">
    <?php if (session()->get('username') == null) : ?>
        <script>
            window.location.href = "<?= base_url('login') ?>";
        </script>
    <?php endif; ?>
    <?php if (session()->get('role') == 'Sudo') : ?>
        <?= $this->include('layout/sidebarSudo') ?>
    <?php endif; ?>
    <?php if (session()->get('role') == 'Monitoring') : ?>
        <?= $this->include('layout/sidebarMonitoring') ?>
    <?php endif; ?>
    <?php if (session()->get('role') == 'Mandor') : ?>
        <?= $this->include('layout/sidebarMandor') ?>
    <?php endif; ?>
    <?php if (session()->get('role') == 'TrainingSchool') : ?>
        <?= $this->include('layout/sidebarTs') ?>
    <?php endif; ?>


    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
        <!-- Navbar -->
        <?= $this->include('layout/navbar') ?>

        <!-- End Navbar -->
        <div class="container-fluid py-4">
            <?= $this->renderSection('content') ?>
            <div class="modal fade  bd-example-modal-lg" id="LogoutModal" tabindex="-1" role="dialog" aria-labelledby="modalCancel" aria-hidden="true">
                <div class="modal-dialog  modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Log Out</h5>
                            <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form action="<?= base_url('logout') ?>" method="get">

                                Apakah anda yakin untuk keluar?
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn bg-gradient-danger">Keluar</button>
                        </div>
                        </form>
                    </div>
                </div>
            </div>
            <?= $this->include('layout/footer') ?>


        </div>
    </main>
    <!--   Core JS Files   -->
    <script src="<?= base_url('assets/js/sweetalert2@11.js') ?>"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="<?= base_url('assets/js/select2.min.js') ?>"></script>
    <script src="<?= base_url('assets/js/core/popper.min.js') ?>"></script>
    <script src="<?= base_url('assets/js/core/bootstrap.min.js') ?>"></script>
    <script src="<?= base_url('assets/js/plugins/perfect-scrollbar.min.js') ?>"></script>
    <script src="<?= base_url('assets/js/plugins/smooth-scrollbar.min.js') ?>"></script>
    <!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> -->
    <!-- <script src="<?= base_url('assets/js/jquery/jquery-3.7.1.min.js') ?>"></script> -->
    <!-- <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script> -->
    <script src="<?= base_url('assets/js/jquery/jquery.dataTables.min.js') ?>"></script>
    <!-- <script src="https://cdn.datatables.net/buttons/2.3.4/js/dataTables.buttons.min.js"></script> -->
    <!-- <script src="<?= base_url('assets/js/dataTables.buttons.min.js') ?>"></script> -->
    <!-- <script src="https://cdn.datatables.net/buttons/2.3.4/js/buttons.html5.min.js"></script> -->
    <script src="<?= base_url('assets/js/buttons.html5.min.js') ?>"></script>
    <script>
        
    </script>
    <script>
        var win = navigator.platform.indexOf('Win') > -1;
        if (win && document.querySelector('#sidenav-scrollbar')) {
            var options = {
                damping: '0.5'
            }
            Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
        }
    </script>
    <!-- Github buttons -->
    <script async defer src="https://buttons.github.io/buttons.js"></script>
    <!-- Control Center for Soft Dashboard: parallax effects, scripts for the example pages etc -->
    <script src="<?= base_url('assets/js/soft-ui-dashboard.min.js') ?>"></script>
</body>

</html>