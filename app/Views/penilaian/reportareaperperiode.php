<?php $this->extend('Layout/index'); ?>
<?php $this->section('content'); ?>
<div class="container-fluid py-4">
    <div class="row my-4">
        <div class="col-xl-12 col-sm-12 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <?php if (!empty($penilaian)) : ?>
                        <?php
                        $currentBatch = '';
                        foreach ($penilaian as $periode) :
                            // Buat header batch baru jika batch berubah
                            if ($currentBatch !== $periode['nama_batch']) :
                                if ($currentBatch !== '') : ?>
                </div>
            <?php endif; ?>
            <h4 class="mt-4"><?= $periode['nama_batch'] . ' ' . date('Y', strtotime($periode['start_date'])) ?></h4>
            <div class="row">
                <?php $currentBatch = $periode['nama_batch']; ?>
            <?php endif; ?>

            <!-- Card untuk setiap periode -->
            <div class="col-md-4">
                <div class="card text-center mb-4">
                    <div class="card-header bg-gradient-primary text-white">
                        <?= $periode['nama_periode'] ?>
                    </div>
                    <div class="card-body">
                        <p><?= $periode['start_date'] . ' - ' . $periode['end_date'] ?></p>
                        <a href="<?= base_url('Monitoring/reportPenilaian/' . $periode['area_utama'] . '/' . $periode['nama_periode']) ?>"
                            class="btn bg-gradient-info btn-sm">
                            Lihat Detail
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
            </div> <!-- Tutup row terakhir -->
        <?php else : ?>
            <p class="text-center">Tidak ada data penilaian.</p>
        <?php endif; ?>
            </div>
        </div>
    </div>
</div>
</div>
<?php $this->endSection(); ?>