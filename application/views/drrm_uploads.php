<?php /* application/views/drrm_uploads.php */ ?>
<!DOCTYPE html>
<html lang="en">
<?php $this->load->view('templates/head'); ?>
<body>
<div id="wrapper">

  <?php $this->load->view('templates/header'); ?>

  <div class="content-page">
    <div class="content">
      <div class="container-fluid">

        <div class="row mb-3">
          <div class="col-12">
            <div class="d-flex align-items-center justify-content-between">
              <h4 class="page-title m-0">DRRM Documents</h4>
              <span class="badge badge-primary" style="font-size:.9rem;padding:.4rem .6rem;">
                SY <b><?= html_escape($this->session->userdata('sy')); ?></b> &nbsp;|&nbsp;
                <b><?= html_escape($this->session->userdata('semester')); ?></b>
              </span>
            </div>
            <hr style="border:0;height:2px;background:linear-gradient(to right,#4285F4 60%,#FBBC05 80%,#34A853 100%);border-radius:1px;margin:12px 0;" />
          </div>
        </div>

        <?php if ($this->session->flashdata('msg')): ?>
          <div class="alert alert-info"><?= $this->session->flashdata('msg'); ?></div>
        <?php endif; ?>
        <?php if ($this->session->flashdata('error')): ?>
          <div class="alert alert-danger"><?= $this->session->flashdata('error'); ?></div>
        <?php endif; ?>

        <div class="row">
          <div class="col-lg-4">
            <div class="card shadow-sm border-0">
              <div class="card-body">
                <h5 class="mb-3">Upload PDF</h5>

                <form method="post" action="<?= base_url('Drrm/upload'); ?>" enctype="multipart/form-data" autocomplete="off">
                  <?php if ($this->security->get_csrf_token_name()): ?>
                    <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
                  <?php endif; ?>

                  <div class="form-group">
                    <label>Title</label>
                    <input type="text" name="title" class="form-control" required maxlength="150">
                  </div>

                  <div class="form-group">
                    <label>Notes</label>
                    <textarea name="notes" rows="3" class="form-control" maxlength="500" placeholder="Optional"></textarea>
                  </div>

                  <div class="form-group">
                    <label>PDF File</label>
                    <input type="file" name="pdf" accept="application/pdf" class="form-control" required>
                    <small class="text-muted">Accepted: PDF • Max 20MB</small>
                  </div>

                  <div class="text-right">
                    <button type="submit" class="btn btn-primary">Upload</button>
                  </div>
                </form>

                <div class="text-muted small mt-2">Uploads path: <code>uploads/DRRM/</code></div>
              </div>
            </div>
          </div>

          <div class="col-lg-8">
            <div class="card shadow-sm border-0">
              <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-3">
                  <h5 class="mb-0">Files</h5>
                  <span class="badge badge-light"><?= isset($files) ? count($files) : 0; ?> item(s)</span>
                </div>

                <?php if (empty($files)): ?>
                  <div class="alert alert-secondary mb-0">No files uploaded yet.</div>
                <?php else: ?>
                  <div class="row">
                    <?php foreach ($files as $f): ?>
                      <div class="col-6 col-md-4 col-xl-3 mb-3">
                        <div class="card h-100">
                          <a href="<?= $f['url']; ?>" target="_blank" class="d-block" style="background:#f8f9fa;">
                            <img src="<?= $f['thumb']; ?>" class="img-fluid w-100" style="height:170px;object-fit:contain;" alt="preview">
                          </a>
                          <div class="card-body p-2">
                            <div class="text-truncate" title="<?= $f['name']; ?>" style="font-weight:600;font-size:.92rem;"><?= $f['name']; ?></div>
                            <div class="text-muted" style="font-size:.8rem;"><?= $f['size_human']; ?> • <?= $f['date_human']; ?></div>
                          </div>
                          <div class="card-footer bg-white border-0 pt-0 pb-2 px-2">
                            <div class="d-flex">
                              <a href="<?= $f['url']; ?>" target="_blank" class="btn btn-sm btn-outline-primary w-100">View</a>
                              <a href="<?= $f['url']; ?>" download class="btn btn-sm btn-outline-secondary ml-1">Download</a>
                              <?php if (!empty($f['delete_url'])): ?>
                                <a href="<?= $f['delete_url']; ?>" class="btn btn-sm btn-outline-danger ml-1" onclick="return confirm('Delete this file?');">Delete</a>
                              <?php endif; ?>
                            </div>
                          </div>
                        </div>
                      </div>
                    <?php endforeach; ?>
                  </div>
                <?php endif; ?>

              </div>
            </div>
          </div>
        </div>

      </div>
    </div>

    <?php $this->load->view('templates/footer'); ?>
  </div>
</div>

<script src="<?= base_url('assets/js/vendor.min.js'); ?>"></script>
<script src="<?= base_url('assets/js/app.min.js'); ?>"></script>
</body>
</html>
