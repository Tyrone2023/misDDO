<?php
if (!function_exists('h')) {
    function h($v)
    {
        return htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8');
    }
}

$esig_file = isset($user->esig) ? trim((string) $user->esig) : '';
$esig_path = $esig_file !== '' ? FCPATH . 'uploads/esig/' . basename($esig_file) : '';
$has_esig  = $esig_file !== '' && is_file($esig_path);
?>

<style>
    :root {
        --esig-primary: #1f3a5f;
        --esig-primary-2: #274b7a;
        --esig-bg: #f4f7fb;
        --esig-border: #e5ecf5;
        --esig-text: #25364a;
        --esig-muted: #7b8794;
        --esig-soft: #eef5ff;
    }

    .content-page { background: var(--esig-bg); min-height: 100vh; }
    .esig-page-shell { padding-bottom: 24px; }

    .esig-hero {
        position: relative; overflow: hidden; border-radius: 18px;
        padding: 22px 24px; margin-bottom: 18px;
        background: linear-gradient(135deg, var(--esig-primary), var(--esig-primary-2));
        box-shadow: 0 14px 35px rgba(31, 58, 95, .18); color: #fff;
    }
    .esig-hero-content { position: relative; z-index: 2; display: flex; align-items: center; justify-content: space-between; gap: 16px; flex-wrap: wrap; }
    .esig-title-block h4 { color: #fff; font-size: 1.25rem; font-weight: 800; margin: 0 0 5px; }
    .esig-title-block p { color: rgba(255,255,255,.82); margin: 0; max-width: 760px; font-size: .86rem; line-height: 1.45; }
    .esig-hero-icon { width: 54px; height: 54px; border-radius: 16px; display: flex; align-items: center; justify-content: center; background: rgba(255,255,255,.14); border: 1px solid rgba(255,255,255,.18); flex: 0 0 auto; }
    .esig-hero-icon i { font-size: 28px; color: #fff; }

    .esig-card { border: 0; border-radius: 16px; box-shadow: 0 8px 26px rgba(31, 58, 95, .08); overflow: hidden; }
    .esig-card .card-body { padding: 18px; }
    .esig-section-title { margin: 0 0 14px; font-size: .94rem; font-weight: 800; color: var(--esig-text); display: flex; align-items: center; gap: 8px; }
    .esig-section-title i { width: 32px; height: 32px; border-radius: 10px; display: inline-flex; align-items: center; justify-content: center; background: var(--esig-soft); color: var(--esig-primary); font-size: 17px; }

    .esig-preview {
        border: 1px dashed var(--esig-border); border-radius: 12px; background: #fff;
        min-height: 150px; display: flex; align-items: center; justify-content: center;
        padding: 16px; text-align: center;
    }
    .esig-preview img { max-width: 100%; max-height: 130px; }
    .esig-preview .esig-empty { color: var(--esig-muted); font-size: .82rem; }
    .esig-preview .esig-empty i { display: block; font-size: 34px; margin-bottom: 6px; color: #c3cede; }

    .esig-file-name { margin-top: 10px; font-size: .72rem; color: var(--esig-muted); word-break: break-all; }
    .esig-note { font-size: .78rem; color: var(--esig-muted); line-height: 1.5; }
    .esig-form label { font-weight: 800; font-size: .68rem; text-transform: uppercase; letter-spacing: .55px; color: var(--esig-muted); margin-bottom: 6px; }
</style>

<div class="content-page">
    <div class="content">
        <div class="container-fluid esig-page-shell">

            <div class="esig-hero">
                <div class="esig-hero-content">
                    <div class="esig-title-block">
                        <h4><?= h($title ?? 'Electronic Signature'); ?></h4>
                        <p>Upload the signature image used on the documents you sign. The file is renamed automatically from your name and replaces the one currently on file.</p>
                    </div>
                    <div class="esig-hero-icon"><i class="mdi mdi-draw"></i></div>
                </div>
            </div>

            <?php if ($this->session->flashdata('success')) : ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <?= h($this->session->flashdata('success')); ?>
                </div>
            <?php endif; ?>

            <?php if ($this->session->flashdata('danger')) : ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <?= h($this->session->flashdata('danger')); ?>
                </div>
            <?php endif; ?>

            <div class="row">
                <div class="col-lg-5">
                    <div class="card esig-card">
                        <div class="card-body">
                            <h5 class="esig-section-title"><i class="mdi mdi-image-outline"></i> Signature on File</h5>

                            <div class="esig-preview">
                                <?php if ($has_esig) : ?>
                                    <img src="<?= base_url(); ?>uploads/esig/<?= h(basename($esig_file)); ?>?v=<?= @filemtime($esig_path); ?>" alt="Electronic Signature">
                                <?php else : ?>
                                    <div class="esig-empty">
                                        <i class="mdi mdi-draw"></i>
                                        No signature uploaded yet.
                                    </div>
                                <?php endif; ?>
                            </div>

                            <?php if ($has_esig) : ?>
                                <div class="esig-file-name"><i class="mdi mdi-file-outline"></i> <?= h(basename($esig_file)); ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="col-lg-7">
                    <div class="card esig-card">
                        <div class="card-body">
                            <h5 class="esig-section-title"><i class="mdi mdi-upload-outline"></i> Upload Signature</h5>

                            <?= form_open_multipart('Pages/esignature_upload', array('class' => 'esig-form')); ?>
                                <div class="form-group">
                                    <label for="userfile">Signature Image</label>
                                    <input type="file" id="userfile" name="userfile" class="form-control" accept=".png,.jpg,.jpeg" required>
                                </div>

                                <p class="esig-note">
                                    PNG or JPG only, maximum 2MB. 
                                    <!-- The file is saved to <code>uploads/esig</code> and renamed automatically based on your name. -->
                                </p>

                                <button type="submit" class="btn btn-primary waves-effect waves-light">
                                    <i class="mdi mdi-content-save-outline"></i> Save Signature
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
