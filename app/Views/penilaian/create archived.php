<?php $this->extend('Layout/index'); ?>
<?php $this->section('content'); ?>
<style>
    .nav-pills .nav-link {
        border: 1px solid transparent;
        border-radius: 0.375rem;
        /* Soft rounded edges */
        color: #344767;
        /* Default text color */
        background-color: transparent;
        transition: background-color 0.3s ease, color 0.3s ease;
        padding: 0.5rem 1rem;
        font-weight: 600;
    }

    .nav-pills .nav-link:hover {
        color: #fff;
        background-color: #5e72e4;
        /* Hover effect with Soft UI primary color */
    }

    .nav-pills .nav-link.active {
        color: #fff;
        background-color: #5e72e4;
        /* Active tab color */
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1),
            0 1px 3px rgba(0, 0, 0, 0.06);
        /* Soft shadow effect */
        border-color: #5e72e4;
        /* Active border to match background */
    }

    .tab-pane {
        background-color: #f8f9fa;
        /* Light background for tab content */
        border: 1px solid #dee2e6;
        /* Border to separate content */
        border-radius: 0.375rem;
        padding: 1.5rem;
        margin-top: 0.75rem;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .table thead th {
        color: #5e72e4;
        /* Table header color to match theme */
        font-size: 0.875rem;
        text-transform: uppercase;
        font-weight: 700;
        border-bottom: 1px solid #dee2e6;
    }

    .table tbody tr td {
        font-size: 0.875rem;
        color: #525f7f;
    }

    .table tbody tr td .form-control {
        font-size: 0.875rem;
        border-radius: 0.375rem;
    }

    /* .btn-primary {
        background-color: #5e72e4;
        border-color: #5e72e4;
        transition: background-color 0.2s, transform 0.2s;
    }

    .btn-primary:hover {
        background-color: #324cdd;
        transform: scale(1.05);
    }

    .btn-secondary {
        background-color: #8392ab;
        border-color: #8392ab;
        color: #ffffff;
    }

    .btn-secondary:hover {
        background-color: #6c757d;
    } */
</style>

<link href="https://cdnjs.cloudflare.com/ajax/libs/soft-ui-dashboard/2.1.0/css/soft-ui-dashboard.min.css"
    rel="stylesheet" />

<div class="container-fluid py-4">
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
                                Form Input Penilaian Mandor
                            </h4>
                        </div>
                        <div>
                            <a href="<?= base_url('monitoring/dataPenilaian') ?>"
                                class="btn bg-gradient-secondary btn-sm">
                                <i class="fas fa-solid fa-arrow-left text-sm opacity-10"></i>
                                Kembali
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <!-- <div class="card-header pb-0">
            <div class="d-flex align-items-center">
                <h6>Employee Evaluation Form</h6>
                <a href="<?= base_url('monitoring/datakaryawan') ?>" class="btn bg-gradient-secondary btn-sm ms-auto">
                    <i class="fas fa-arrow-left me-2"></i> Back
                </a>
            </div>
        </div> -->
        <div class="card-body">
            <?php
            $jobroles = json_decode($jobrole['jobdesc'], true);
            ?>

            <div class="nav-wrapper position-relative end-0">
                <ul class="nav nav-pills nav-fill p-1 bg-gradient-secondary" role="tablist">
                    <?php foreach ($jobroles as $index => $role): ?>
                        <li class="nav-item">
                            <a class="nav-link mb-0 <?= $index === 0 ? 'active' : '' ?>"
                                id="tab-<?= htmlspecialchars(str_replace(' ', '_', $role)) ?>-tab" data-bs-toggle="tab"
                                href="#tab-<?= htmlspecialchars(str_replace(' ', '_', $role)) ?>" role="tab">
                                <?= htmlspecialchars($role) ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <form action="<?= base_url('monitoring/penilaianStore') ?>" method="post" id="evaluationForm">
                <div class="tab-content mt-4">
                    <input type="hidden" name="id_batch" value="<?= $temp['id_batch'] ?>">
                    <input type="hidden" name="id_jobrole" value="<?= $temp['id_jobrole'] ?>">
                    <input type="hidden" name="id_user" value="<?= $temp['id_user'] ?>">
                    <?php foreach ($jobroles as $index => $role): ?>
                        <div class="tab-pane fade <?= $index === 0 ? 'show active' : '' ?>"
                            id="tab-<?= htmlspecialchars(str_replace(' ', '_', $role)) ?>" role="tabpanel">
                            <table class="table align-items-center mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Employee</th>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                            Score</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($karyawan as $k): ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex px-2 py-1">
                                                    <div>
                                                        <h6 class="mb-0 text-sm"><?= htmlspecialchars($k['nama_karyawan']) ?>
                                                        </h6>
                                                        <input type="hidden" name="id_karyawan[]"
                                                            value="<?= $k['id_karyawan'] ?>">
                                                        <p class="text-xs text-secondary mb-0">
                                                            <?= htmlspecialchars($k['kode_kartu']) ?></p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <input type="number" class="form-control"
                                                    name="nilai[<?= $k['id_karyawan'] ?>][<?= htmlspecialchars($role) ?>]"
                                                    placeholder="Score" min="0" max="6" required>
                                                <input type="hidden"
                                                    name="index_nilai[<?= $k['id_karyawan'] ?>][<?= htmlspecialchars($role) ?>]"
                                                    value="<?= $index ?>">
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <button type="submit" class="btn bg-gradient-info">
                        <i class="fas fa-save me-2"></i> Save Evaluations
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/soft-ui-dashboard/2.1.0/js/soft-ui-dashboard.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tabLinks = document.querySelectorAll('.nav-link');
        const tabPanes = document.querySelectorAll('.tab-pane');

        // Get the last active tab from localStorage
        const activeTabId = localStorage.getItem('activeTab') || tabLinks[0].getAttribute('href');
        // ketika direload halaman, active tab kosong
        localStorage.removeItem('activeTab');
        // Activate the saved tab
        tabLinks.forEach(link => {
            if (link.getAttribute('href') === activeTabId) {
                link.classList.add('active');
            } else {
                link.classList.remove('active');
            }
        });

        tabPanes.forEach(pane => {
            if ('#' + pane.id === activeTabId) {
                pane.classList.add('show', 'active');
            } else {
                pane.classList.remove('show', 'active');
            }
        });

        // Update active tab in localStorage on tab click
        tabLinks.forEach(link => {
            link.addEventListener('click', function() {
                localStorage.setItem('activeTab', this.getAttribute('href'));
            });
        });
    });
</script>

<!-- script index_nilai 
jika bobot nilai 1 bernilai 15
jika 2 bernilai 30
jika 3 bernilai 45
jika 4 bernilai 60
jika 5 bernilai 85
jika 6 bernilai 100
lalu jumlahkan total nilai,
jika sudah hitunglah rata-ratanya
kurang dari 59 bernilai D
kurang dari 75 bernilai C
kurang dari 85 bernilai B
kurang dari 101 bernilai A
hasil grade merupakan data untuk index_nilai
 -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const evaluationForm = document.getElementById('evaluationForm');

        evaluationForm.addEventListener('submit', function(e) {
            e.preventDefault(); // Mencegah form submit untuk sementara waktu

            const bobotNilai = {
                1: 15,
                2: 30,
                3: 45,
                4: 60,
                5: 85,
                6: 100
            };

            const nilaiInputs = evaluationForm.querySelectorAll('input[name^="nilai"]');
            const indexNilai = {}; // Menyimpan hasil id_karyawan dan grade

            nilaiInputs.forEach(input => {
                const [idKaryawan, role] = input.name.match(/\[(.*?)\]/g).map(x => x.replace(
                    /\[|\]/g, ''));
                const nilai = parseInt(input.value, 10) || 0;

                if (!indexNilai[idKaryawan]) {
                    indexNilai[idKaryawan] = {
                        totalBobot: 0,
                        totalNilai: 0
                    };
                }

                indexNilai[idKaryawan].totalNilai++;
                indexNilai[idKaryawan].totalBobot += bobotNilai[nilai];
            });

            for (const idKaryawan in indexNilai) {
                const {
                    totalBobot,
                    totalNilai
                } = indexNilai[idKaryawan];
                const rataRata = totalBobot / totalNilai;
                let grade;

                if (rataRata < 59) grade = 'D';
                else if (rataRata < 75) grade = 'C';
                else if (rataRata < 85) grade = 'B';
                else grade = 'A';

                // Menyimpan id_karyawan dan grade ke index_nilai
                const indexNilaiInput = document.createElement('input');
                indexNilaiInput.type = 'hidden';
                indexNilaiInput.name = `index_nilai[${idKaryawan}]`;
                indexNilaiInput.value = grade;
                evaluationForm.appendChild(indexNilaiInput);
            }

            // Submit form setelah perhitungan selesai
            this.submit();
        });
    });
</script>




<?php $this->endSection(); ?>