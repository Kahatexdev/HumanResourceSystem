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
                <?php if (($title ?? '') == 'Data Absensi') : ?>
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

            <ul class="navbar-nav justify-content-end">
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