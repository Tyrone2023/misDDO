<?php
if (!function_exists('h')) {
    function h($v)
    {
        return htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8');
    }
}

$result = is_array($result ?? null) ? $result : null;
$error = trim((string) ($error ?? ''));
?>

<style>
    :root {
        --rqa-primary: #1f3a5f;
        --rqa-bg: #f4f7fb;
        --rqa-border: #e5ecf5;
        --rqa-text: #25364a;
        --rqa-muted: #7b8794;
        --rqa-soft: #eef5ff;
    }

    .content-page { background: var(--rqa-bg); min-height: 100vh; }
    .rqa-page-shell { padding-bottom: 24px; }
    .rqa-hero {
        border-radius: 10px;
        padding: 20px 22px;
        margin-bottom: 18px;
        background: #fff;
        border: 1px solid var(--rqa-border);
        box-shadow: 0 8px 22px rgba(31, 58, 95, .06);
    }
    .rqa-hero-content { display: flex; align-items: center; justify-content: space-between; gap: 16px; }
    .rqa-title-block h4 { color: var(--rqa-text); font-size: 1.15rem; font-weight: 800; margin: 0 0 5px; }
    .rqa-title-block p { color: var(--rqa-muted); margin: 0; max-width: 780px; font-size: .84rem; line-height: 1.45; }
    .rqa-hero-icon {
        width: 48px;
        height: 48px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: var(--rqa-soft);
        color: var(--rqa-primary);
        flex: 0 0 auto;
    }
    .rqa-hero-icon i { font-size: 25px; }
    .rqa-card {
        border: 1px solid var(--rqa-border);
        border-radius: 8px;
        box-shadow: 0 8px 22px rgba(31, 58, 95, .05);
        overflow: hidden;
    }
    .rqa-card .card-body { padding: 18px; }
    .rqa-section-title {
        margin: 0 0 14px;
        font-size: .94rem;
        font-weight: 800;
        color: var(--rqa-text);
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .rqa-section-title i {
        width: 30px;
        height: 30px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: var(--rqa-soft);
        color: var(--rqa-primary);
        font-size: 16px;
    }
    .rqa-upload-label {
        font-weight: 800;
        font-size: .68rem;
        text-transform: uppercase;
        letter-spacing: .55px;
        color: var(--rqa-muted);
        margin-bottom: 6px;
    }
    .rqa-summary-grid {
        display: grid;
        grid-template-columns: repeat(6, minmax(120px, 1fr));
        gap: 10px;
        margin-bottom: 16px;
    }
    .rqa-summary-box {
        border: 1px solid var(--rqa-border);
        border-radius: 8px;
        padding: 12px;
        background: #fff;
    }
    .rqa-summary-box span { display: block; color: var(--rqa-muted); font-size: .68rem; font-weight: 800; text-transform: uppercase; letter-spacing: .35px; }
    .rqa-summary-box strong { display: block; color: var(--rqa-text); font-size: 1.25rem; line-height: 1.2; margin-top: 4px; }
    .rqa-result-table th { background: #f8fbff; color: #516070; font-size: .68rem; text-transform: uppercase; letter-spacing: .35px; }
    .rqa-result-table td { font-size: .78rem; color: var(--rqa-text); vertical-align: middle; }
    @media (max-width: 1200px) {
        .rqa-summary-grid { grid-template-columns: repeat(3, minmax(120px, 1fr)); }
    }
    @media (max-width: 767px) {
        .rqa-hero-content { align-items: flex-start; }
        .rqa-summary-grid { grid-template-columns: repeat(2, minmax(120px, 1fr)); }
    }
</style>

<div class="content-page">
    <div class="content">
        <div class="container-fluid rqa-page-shell">

            <div class="rqa-hero">
                <div class="rqa-hero-content">
                    <div class="rqa-title-block">
                        <h4><?= h($title ?? 'RQA JHS Specialization Upload'); ?></h4>
                        <p>Upload the CAR-RQA JHS workbook to update applicant JHS specialization by Application Code.</p>
                    </div>
                    <div class="rqa-hero-icon"><i class="mdi mdi-file-excel-box"></i></div>
                </div>
            </div>

            <?php if ($error !== '') : ?>
                <div class="alert alert-danger">
                    <strong>Import failed.</strong> <?= h($error); ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($result)) : ?>
                <div class="card rqa-card mb-3">
                    <div class="card-body">
                        <h5 class="rqa-section-title"><i class="mdi mdi-check-circle-outline"></i> Import Result</h5>

                        <div class="rqa-summary-grid">
                            <div class="rqa-summary-box"><span>Rows</span><strong><?= (int) ($result['total_rows'] ?? 0); ?></strong></div>
                            <div class="rqa-summary-box"><span>Matched</span><strong><?= (int) ($result['matched'] ?? 0); ?></strong></div>
                            <div class="rqa-summary-box"><span>Updated</span><strong><?= (int) ($result['updated'] ?? 0); ?></strong></div>
                            <div class="rqa-summary-box"><span>Unchanged</span><strong><?= (int) ($result['unchanged'] ?? 0); ?></strong></div>
                            <div class="rqa-summary-box"><span>Not Found</span><strong><?= (int) ($result['not_found'] ?? 0); ?></strong></div>
                            <div class="rqa-summary-box"><span>Invalid</span><strong><?= (int) ($result['invalid'] ?? 0); ?></strong></div>
                        </div>

                        <?php if (!empty($result['file_name'])) : ?>
                            <p class="mb-3">
                                <span class="text-muted">Saved file:</span>
                                <a href="<?= base_url('uploads/rqa/' . rawurlencode($result['file_name'])); ?>" target="_blank"><?= h($result['file_name']); ?></a>
                            </p>
                        <?php endif; ?>

                        <?php if (!empty($result['not_found_rows'])) : ?>
                            <h6 class="font-weight-bold mt-3">Application Codes Not Found</h6>
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm rqa-result-table">
                                    <thead>
                                        <tr>
                                            <th>Row</th>
                                            <th>Application Code</th>
                                            <th>Specialization</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($result['not_found_rows'] as $row) : ?>
                                            <tr>
                                                <td><?= (int) ($row['row'] ?? 0); ?></td>
                                                <td><?= h($row['record_no'] ?? ''); ?></td>
                                                <td><?= h($row['value'] ?? ''); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($result['invalid_rows'])) : ?>
                            <h6 class="font-weight-bold mt-3">Invalid Rows</h6>
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm rqa-result-table">
                                    <thead>
                                        <tr>
                                            <th>Row</th>
                                            <th>Application Code</th>
                                            <th>Specialization</th>
                                            <th>Reason</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($result['invalid_rows'] as $row) : ?>
                                            <tr>
                                                <td><?= (int) ($row['row'] ?? 0); ?></td>
                                                <td><?= h($row['record_no'] ?? ''); ?></td>
                                                <td><?= h($row['value'] ?? ''); ?></td>
                                                <td><?= h($row['reason'] ?? ''); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($result['duplicate_rows'])) : ?>
                            <h6 class="font-weight-bold mt-3">Duplicate Application Codes Skipped</h6>
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm rqa-result-table">
                                    <thead>
                                        <tr>
                                            <th>Row</th>
                                            <th>Application Code</th>
                                            <th>First Row</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($result['duplicate_rows'] as $row) : ?>
                                            <tr>
                                                <td><?= (int) ($row['row'] ?? 0); ?></td>
                                                <td><?= h($row['record_no'] ?? ''); ?></td>
                                                <td><?= (int) ($row['first_row'] ?? 0); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>

            <div class="card rqa-card">
                <div class="card-body">
                    <h5 class="rqa-section-title"><i class="mdi mdi-upload"></i> Upload Workbook</h5>

                    <form action="<?= base_url('Pages/rqa_jhs_upload_import'); ?>" method="post" enctype="multipart/form-data">
                        <div class="form-group">
                            <label class="rqa-upload-label" for="rqa_file">XLSX File</label>
                            <input type="file" class="form-control" id="rqa_file" name="rqa_file" accept=".xlsx" required>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="mdi mdi-file-upload-outline mr-1"></i> Upload and Import
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
