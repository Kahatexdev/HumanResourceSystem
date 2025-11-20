<div class="modal fade bd-example-modal-lg" id="addMasterJam" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="modalTitleJam">Tambah Shift / Jam Kerja</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form id="formMasterJam" action="" method="post">
                <div class="modal-body">

                    <input type="hidden" name="id_shift" id="id_shift">

                    <div class="mb-3">
                        <label class="form-label">Nama Shift</label>
                        <input type="text" class="form-control" name="shift_name" id="shift_name" required>
                    </div>

                    <div class="row g-3">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">

                                    <div class="row g-2 align-items-end">
                                        <div class="col-md-4">
                                            <label class="form-label">Jam Masuk</label>
                                            <input type="time" class="form-control" name="start_time" id="start_time" required>
                                        </div>

                                        <div class="col-md-4">
                                            <label class="form-label">Jam Pulang</label>
                                            <input type="time" class="form-control" name="end_time" id="end_time" required>
                                        </div>

                                        <div class="col-md-2">
                                            <label class="form-label">Istirahat (menit)</label>
                                            <input type="number" class="form-control" name="break_time" id="break_time" value="0">
                                        </div>

                                        <div class="col-md-2">
                                            <label class="form-label">Toleransi (menit)</label>
                                            <input type="number" class="form-control" name="grace_min" id="grace_min" value="0">
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn bg-gradient-info" id="btnSaveJam">Save</button>
                </div>

            </form>

        </div>
    </div>
</div>