<?php defined('BASEPATH') OR exit('No direct script access allowed');
if (!function_exists('h')) {
    function h($v) { return htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8'); }
}
$mode = isset($mode) ? $mode : 'create';
$report = isset($report) && is_array($report) ? $report : array();
$defaults = isset($defaults) && is_array($defaults) ? $defaults : array();
$old = isset($old) && is_array($old) ? $old : array();
$months = array(1=>'January',2=>'February',3=>'March',4=>'April',5=>'May',6=>'June',7=>'July',8=>'August',9=>'September',10=>'October',11=>'November',12=>'December');

$value = static function ($key) use ($old, $report, $defaults) {
    if (isset($old[$key]) && $old[$key] !== '') {
        return (string) $old[$key];
    }
    if (isset($report[$key]) && $report[$key] !== '' && $report[$key] !== null) {
        return (string) $report[$key];
    }
    return (string) (isset($defaults[$key]) ? $defaults[$key] : '');
};
$selectedMonth = (int) (isset($old['report_month']) ? $old['report_month']
    : (isset($report['report_month']) ? $report['report_month'] : date('n')));
$selectedYear = (int) (isset($old['report_year']) ? $old['report_year']
    : (isset($report['report_year']) ? $report['report_year'] : date('Y')));
$agency = $value('agency_name') !== '' ? $value('agency_name') : 'Department of Education';
$division = $value('division_name') !== '' ? $value('division_name') : 'Schools Division of Davao de Oro';
?>
<style>
    :root {
        --rai-primary: #1f3a5f;
        --rai-primary-2: #274b7a;
        --rai-accent: #1abc9c;
        --rai-bg: #f4f7fb;
        --rai-border: #e5ecf5;
        --rai-text: #25364a;
        --rai-muted: #7b8794;
        --rai-soft: #eef5ff;
    }

    .content-page { background: var(--rai-bg); min-height: 100vh; }
    .rai-page-shell { padding-bottom: 24px; }

    .rai-hero {
        position: relative;
        overflow: hidden;
        border-radius: 18px;
        padding: 22px 24px;
        margin-bottom: 18px;
        background: linear-gradient(135deg, var(--rai-primary), var(--rai-primary-2));
        box-shadow: 0 14px 35px rgba(31, 58, 95, .18);
        color: #fff;
    }
    .rai-hero:before {
        content: "";
        position: absolute;
        width: 230px; height: 230px;
        border-radius: 50%;
        right: -80px; top: -95px;
        background: rgba(255,255,255,.10);
    }
    .rai-hero:after {
        content: "";
        position: absolute;
        width: 160px; height: 160px;
        border-radius: 50%;
        right: 90px; bottom: -100px;
        background: rgba(26,188,156,.22);
    }
    .rai-hero-content {
        position: relative;
        z-index: 2;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
    }
    .rai-title-block h4 { color: #fff; font-size: 1.25rem; font-weight: 800; letter-spacing: .2px; margin: 0 0 5px; }
    .rai-title-block p { color: rgba(255,255,255,.82); margin: 0; max-width: 760px; font-size: .86rem; line-height: 1.45; }
    .rai-hero-icon {
        width: 54px; height: 54px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(255,255,255,.14);
        border: 1px solid rgba(255,255,255,.18);
        flex: 0 0 auto;
    }
    .rai-hero-icon i { font-size: 28px; color: #fff; }

    .rai-card { border: 0; border-radius: 16px; box-shadow: 0 8px 26px rgba(31, 58, 95, .08); overflow: hidden; }
    .rai-card .card-body { padding: 18px; }

    .rai-section-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 14px;
        flex-wrap: wrap;
    }
    .rai-section-title {
        margin: 0;
        font-size: .94rem;
        font-weight: 800;
        color: var(--rai-text);
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .rai-section-title i {
        width: 32px; height: 32px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: var(--rai-soft);
        color: var(--rai-primary);
        font-size: 17px;
    }
    .rai-help-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        color: var(--rai-muted);
        font-size: .76rem;
        background: #f8fafd;
        border: 1px solid var(--rai-border);
        padding: 6px 10px;
        border-radius: 999px;
    }

    .rai-form-card label {
        font-weight: 800;
        font-size: .68rem;
        text-transform: uppercase;
        letter-spacing: .55px;
        color: var(--rai-muted);
        margin-bottom: 6px;
    }
    .rai-form-card .form-group { margin-bottom: 14px; }
    .rai-req { color: #c0392b; }

    @media (max-width: 991.98px) {
        .rai-hero-content { align-items: flex-start; }
        .rai-hero-icon { display: none; }
        .rai-section-head { align-items: flex-start; }
    }
    @media (max-width: 767.98px) {
        .rai-hero { padding: 18px; border-radius: 14px; }
        .rai-hero-content { flex-direction: column; }
        .rai-title-block h4 { font-size: 1.1rem; }
        .rai-card .card-body { padding: 14px; }
    }
</style>

<div class="content-page">
    <div class="content">
        <div class="container-fluid rai-page-shell">

            <div class="rai-hero">
                <div class="rai-hero-content">
                    <div class="rai-title-block">
                        <h4><?= $mode === 'edit' ? 'Edit Report Information' : 'New Report Information' ?></h4>
                        <p>Save the report header first. Applicants are added afterwards in Manage.</p>
                    </div>

                    <div class="d-flex align-items-center flex-wrap justify-content-end" style="gap:10px;">
                        <a href="<?= h($indexUrl) ?>" class="btn btn-light btn-sm font-weight-bold" style="border-radius:10px;">
                            <i class="ph ph-arrow-left mr-1"></i>All Reports
                        </a>
                        <div class="rai-hero-icon">
                            <i class="ph ph-file-text"></i>
                        </div>
                    </div>
                </div>
            </div>

            <?php if ($this->session->flashdata('danger')): ?>
                <div class="alert alert-danger" role="alert"><i class="ph ph-warning-circle mr-1"></i><?= h($this->session->flashdata('danger')) ?></div>
            <?php endif; ?>

            <div class="card rai-card rai-form-card">
                <div class="card-body">
                    <div class="rai-section-head">
                        <h5 class="rai-section-title">
                            <i class="ph ph-clipboard-text"></i>
                            Report Information
                        </h5>
                        <span class="rai-help-chip">
                            <i class="ph ph-info"></i>
                            Fields marked <span class="rai-req">*</span> are required
                        </span>
                    </div>

                    <form method="post" action="<?= h($formAction) ?>">
                        <div class="form-row">
                            <div class="form-group col-lg-2 col-md-6">
                                <label for="report_month">Reporting Month <span class="rai-req">*</span></label>
                                <select name="report_month" id="report_month" class="form-control">
                                    <?php foreach ($months as $number => $name): ?>
                                        <option value="<?= $number ?>" <?= $selectedMonth === $number ? 'selected' : '' ?>><?= $name ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group col-lg-2 col-md-6">
                                <label for="report_year">Reporting Year <span class="rai-req">*</span></label>
                                <input name="report_year" id="report_year" type="number" min="2000" max="2100" class="form-control" value="<?= (int) $selectedYear ?>">
                            </div>
                            <div class="form-group col-lg-4 col-md-6">
                                <label for="agency_name">Agency <span class="rai-req">*</span></label>
                                <input name="agency_name" id="agency_name" class="form-control" value="<?= h($agency) ?>">
                            </div>
                            <div class="form-group col-lg-4 col-md-6">
                                <label for="division_name">Division <span class="rai-req">*</span></label>
                                <input name="division_name" id="division_name" class="form-control" value="<?= h($division) ?>">
                            </div>
                            <div class="form-group col-lg-3 col-md-6">
                                <label for="csc_resolution_no">CSC Resolution Number</label>
                                <input name="csc_resolution_no" id="csc_resolution_no" class="form-control" value="<?= h($value('csc_resolution_no')) ?>">
                            </div>
                            <div class="form-group col-lg-3 col-md-6">
                                <label for="csc_resolution_date">CSC Resolution Date</label>
                                <input name="csc_resolution_date" id="csc_resolution_date" type="date" class="form-control" value="<?= h($value('csc_resolution_date')) ?>">
                            </div>
                            <div class="form-group col-lg-3 col-md-6">
                                <label for="certifying_officer">Certifying Officer</label>
                                <input name="certifying_officer" id="certifying_officer" class="form-control" value="<?= h($value('certifying_officer')) ?>">
                            </div>
                            <div class="form-group col-lg-3 col-md-6">
                                <label for="certifying_position">Certifying Officer Position</label>
                                <input name="certifying_position" id="certifying_position" class="form-control" value="<?= h($value('certifying_position')) ?>">
                            </div>
                            <div class="form-group col-lg-6 col-md-6">
                                <label for="appointing_officer">Appointing Officer</label>
                                <input name="appointing_officer" id="appointing_officer" class="form-control" value="<?= h($value('appointing_officer')) ?>">
                            </div>
                            <div class="form-group col-lg-6 col-md-6">
                                <label for="appointing_position">Appointing Officer Position</label>
                                <input name="appointing_position" id="appointing_position" class="form-control" value="<?= h($value('appointing_position')) ?>">
                            </div>
                            <div class="form-group col-12">
                                <label for="remarks">CSC Remarks (optional)</label>
                                <textarea name="remarks" id="remarks" class="form-control" rows="3"><?= h($value('remarks')) ?></textarea>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end flex-wrap" style="gap:8px;">
                            <a class="btn btn-light btn-sm font-weight-bold" href="<?= h($indexUrl) ?>" style="border-radius:10px;">
                                <i class="ph ph-x mr-1"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-primary btn-sm font-weight-bold" style="border-radius:10px;">
                                <i class="ph ph-floppy-disk mr-1"></i><?= $mode === 'edit' ? 'Save Changes' : 'Save Report & Continue' ?>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
