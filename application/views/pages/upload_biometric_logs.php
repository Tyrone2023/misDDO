<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">
            <div style="margin-left: 250px;">

            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <h4 class="mb-0">Upload Biometric Logs</h4>
                    </div>
                </div>
            </div>

            <?php if ($this->session->flashdata('success')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= $this->session->flashdata('success'); ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <?php endif; ?>

            <?php if ($this->session->flashdata('danger')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= $this->session->flashdata('danger'); ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <?php endif; ?>

            <div class="row">
                <div class="col-lg-8 col-md-10">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Upload .csv File</h4>
                            <p class="card-title-desc">Select and upload a biometric export file.</p>

                            <form action="<?= base_url('System_settings/upload_biometric_logs_submit'); ?>" method="post" enctype="multipart/form-data">
                                <div class="form-group mb-3">
                                    <label for="biometric_file">Biometric Log File</label>
                                    <input type="file" name="biometric_file" id="biometric_file" class="form-control" accept=".csv" required>
                                    <small class="form-text text-muted">Allowed file type: .csv</small>
                                </div>

                                <button type="submit" class="btn btn-primary">
                                    Upload File
                                </button>
                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </div>
</div>