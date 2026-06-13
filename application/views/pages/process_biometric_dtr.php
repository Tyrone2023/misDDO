<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">
            <div style="margin-left: 250px;">


            <div class="page-title-box d-flex align-items-center justify-content-between">
                <h4 class="mb-0 font-size-18">Process Biometric DTR</h4>
            </div>

            <?php if ($this->session->flashdata('success')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= $this->session->flashdata('success'); ?>
                    <button type="button" class="close" data-dismiss="alert">
                        <span>&times;</span>
                    </button>
                </div>
            <?php endif; ?>

            <?php if ($this->session->flashdata('danger')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= $this->session->flashdata('danger'); ?>
                    <button type="button" class="close" data-dismiss="alert">
                        <span>&times;</span>
                    </button>
                </div>
            <?php endif; ?>

            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-3">Generate Daily Time Record from Raw Logs</h4>

                    <form method="post" action="<?= base_url('System_settings/run_biometric_dtr_process'); ?>">
                        <div class="form-group">
                            <label>Select Uploaded File</label>
                            <select name="upload_id" class="form-control" required>
                                <option value="">Select upload...</option>
                                <?php foreach ($uploads as $upload): ?>
                                    <option value="<?= $upload->id; ?>">
                                        #<?= $upload->id; ?> - <?= htmlspecialchars($upload->original_filename); ?> - <?= $upload->uploaded_at; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary mt-3">Process DTR</button>
                    </form>
                </div>
            </div>
            </div>
        </div>
    </div>
</div>