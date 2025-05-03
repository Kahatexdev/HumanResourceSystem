<?php $this->extend('layout/template'); ?>
<?php $this->section('content'); ?>

<div class="container-fluid py-4">

    <!-- PDF Viewer -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body p-0">
                    <!-- Gunakan elemen iframe untuk menampilkan PDF -->
                    <iframe id="pdfViewer" src="<?= base_url('IK/instruksikerjaskillmapping.pdf'); ?>" width="100%" height="600px" style="border: none;"></iframe>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Optional: Membaca file PDF yang dipilih dan menampilkannya di iframe
    document.getElementById('pdfFileInput').addEventListener('change', function(e) {
        var file = e.target.files[0];
        if (file && file.type === "application/pdf") {
            var fileURL = URL.createObjectURL(file);
            document.getElementById('pdfViewer').src = fileURL;
        } else {
            alert("Silakan pilih file PDF yang valid.");
        }
    });
</script>

<?php $this->endSection(); ?>