<style>
    /* Khusus dropdown absensi di navbar */
    .nav-absensi-dropdown .dropdown-toggle {
        background: transparent !important;
        border: none !important;
        color: #344767;
        /* sesuaikan dengan tema navbar kamu */
        font-weight: 500;
        font-size: 0.875rem;
        padding: 0.35rem 0.75rem;
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
    }

    .nav-absensi-dropdown .dropdown-toggle:focus,
    .nav-absensi-dropdown .dropdown-toggle:hover {
        background: rgba(0, 0, 0, 0.03) !important;
        box-shadow: none;
        color: #111827;
        /* sedikit lebih gelap saat hover */
    }

    /* Icon di kiri teks */
    .nav-absensi-dropdown .dropdown-toggle i {
        font-size: 0.85rem;
        opacity: 0.8;
    }

    /* Dropdown menu style */
    .nav-absensi-dropdown .dropdown-menu {
        border-radius: 0.75rem;
        border: 1px solid rgba(0, 0, 0, 0.04);
        box-shadow: 0 8px 18px rgba(0, 0, 0, 0.08);
        font-size: 0.85rem;
    }

    .nav-absensi-dropdown .dropdown-item i {
        width: 16px;
        text-align: center;
        margin-right: 0.35rem;
        font-size: 0.75rem;
        opacity: 0.8;
    }

    .nav-absensi-dropdown .dropdown-item:hover {
        background-color: #f5f5f5;
    }
</style>


<nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl" id="navbarBlur" navbar-scroll="true">
    <div class="container-fluid py-1 px-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
                <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="javascript:;">Pages</a></li>
                <li class="breadcrumb-item text-sm text-dark active" aria-current="page"><?= $title ?? 'Dashboard' ?></li>
            </ol>
            <h6 class="font-weight-bolder mb-0"><?= $title ?? 'Dashboard' ?></h6>
        </nav>
        <div class="collapse navbar-collapse mt-sm-0 mt-2 me-md-0 me-sm-4" id="navbar">
            <?php if (session()->get('role') == 'Absensi') : ?>
                <?php if (($title ?? '') == 'Log Absen') : ?>
                    <div class="ms-md-auto pe-md-3 d-flex align-items-center nav-absensi-dropdown">
                        <div class="dropdown">
                            <button
                                class="dropdown-toggle"
                                type="button"
                                id="dropdownAbsensiMenu"
                                data-bs-toggle="dropdown"
                                aria-expanded="false">
                                <i class="fas fa-file-alt"></i>
                                <span>Report</span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownAbsensiMenu">
                                <li>
                                    <a class="dropdown-item"
                                        href="<?= base_url($role . '/reportDataAbsensi') ?>">
                                        <i class="fas fa-file-alt"></i> Report Data Absensi
                                    </a>
                                </li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li>
                                    <a class="dropdown-item"
                                        href="<?= base_url($role . '/ketidaksesuaianAbsensi') ?>">
                                        <i class="fas fa-file-alt"></i> Report Ketidaksesuaian Absensi
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="ms-md-auto pe-md-3 d-flex align-items-center">
                        <div class="input-group">
                            <span class="input-group-text text-body">
                                <i class="fas fa-search" aria-hidden="true"></i>
                            </span>
                            <input type="text" class="form-control" placeholder="Type here...">
                        </div>
                    </div>
                <?php endif; ?>
            <?php else : ?>
                <div class="ms-md-auto pe-md-3 d-flex align-items-center">
                    <div class="input-group">
                        <span class="input-group-text text-body">
                            <i class="fas fa-search" aria-hidden="true"></i>
                        </span>
                        <input type="text" class="form-control" placeholder="Type here...">
                    </div>
                </div>
            <?php endif; ?>

            <li class="nav-item dropdown pe-2 d-flex align-items-center position-relative">

                <!-- Icon Bell + Badge -->
                <a href="javascript:;" class="nav-link text-body p-0 position-relative" id="dropdownNotif" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fa fa-bell cursor-pointer" style="font-size: 1.25rem; color: #64748b;"></i>

                    <?php if (!empty($dataTidakSesuai)) : ?>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill" style="
                    background-color: #ef4444;
                    font-size: 0.65rem;
                    padding: 0.25rem 0.4rem;
                    margin-left: -8px;
                    margin-top: 2px;
                ">
                            <?= count($dataTidakSesuai) ?>
                        </span>
                    <?php endif; ?>
                </a>

                <ul class="dropdown-menu dropdown-menu-end px-2 py-3 me-sm-n4 shadow" style="min-width: 320px; max-height: 400px; overflow-y: auto;" aria-labelledby="dropdownNotif">

                    <?php
                    $limit = 3;
                    $itemCount = 0;
                    ?>

                    <?php if (!empty($dataTidakSesuai)) : ?>
                        <?php foreach ($dataTidakSesuai as $row) : ?>
                            <li class="mb-2 anomali-item <?= ($itemCount >= $limit ? 'd-none extra-item' : '') ?>">
                                <a class="dropdown-item border-radius-md" href="<?= base_url($role . '/ketidaksesuaianAbsensi/' . $row['work_date']) ?>"
                                    style="background-color: #f8fafc;">
                                    <div class="d-flex py-2">
                                        <div class="my-auto">
                                            <div class="d-flex align-items-center justify-content-center" style="
                        width: 40px;height: 40px;background-color: #d1494b;border-radius: 8px;margin-right: 12px;">
                                                <i class="fas fa-calendar-alt" style="font-size: 1.1rem;color:#fff;"></i>
                                            </div>
                                        </div>
                                        <div class="d-flex flex-column justify-content-center">
                                            <h6 class="text-sm mb-1" style="color:#1e293b;">
                                                <?= date('d F Y', strtotime($row['work_date'])) ?>
                                            </h6>
                                            <p class="text-xs mb-0" style="color:#64748b;">
                                                <i class="fas fa-exclamation-circle me-1" style="color:#f59e0b;"></i>
                                                <span style="color:#d1494b;font-weight: 500;">
                                                    <?= $row['total_anomali']; ?> Karyawan
                                                </span> tidak sesuai
                                            </p>
                                        </div>
                                    </div>
                                </a>
                            </li>
                            <?php $itemCount++; ?>
                        <?php endforeach; ?>

                        <!-- Tombol Show All / Show Less -->
                        <?php if (count($dataTidakSesuai) > $limit) : ?>
                            <li class="text-center">
                                <a href="javascript:;" id="toggleShowAll" class="text-primary text-sm fw-bold">
                                    Show All
                                </a>
                            </li>
                        <?php endif; ?>

                    <?php else : ?>
                        <li class="text-center p-4">
                            <i class="fas fa-check-circle mb-2" style="font-size: 2.5rem; color: #10b981;"></i>
                            <p class="text-sm mb-0" style="color: #64748b;">Tidak ada data</p>
                        </li>
                    <?php endif; ?>

                </ul>
            </li>
            <li class="nav-item d-flex align-items-center">
                <a href="" data-bs-toggle="modal" data-bs-target="#LogoutModal"
                    class="nav-link text-body font-weight-bold px-0">
                    <img src="<?= base_url('assets/img/user.png') ?>" alt="User Icon" width="20">
                    <span class="d-sm-inline d-none"><?= session()->get('username') ?></span>
                </a>
            </li>
            <li class="nav-item d-xl-none ps-3 d-flex align-items-center">
                <a href="javascript:;" class="nav-link text-body p-0" id="iconNavbarSidenav">
                    <div class="sidenav-toggler-inner">
                        <i class="sidenav-toggler-line"></i>
                        <i class="sidenav-toggler-line"></i>
                        <i class="sidenav-toggler-line"></i>
                    </div>
                </a>
            </li>
            </ul>
        </div>
    </div>
</nav>