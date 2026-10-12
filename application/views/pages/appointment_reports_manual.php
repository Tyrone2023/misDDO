<?php
if (!function_exists('h')) {
    function h($value) { return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8'); }
}
$empTypes = ['Permanent', 'Temporary', 'Substitute', 'Provisional', 'Coterminous', 'Fixed Term', 'Contractual', 'Casual'];
?>

<style>
    :root { --ar-primary:#1f3a5f; --ar-accent:#1abc9c; --ar-bg:#f4f7fb; --ar-border:#e4ebf4; --ar-muted:#758396; }
    .content-page { background:var(--ar-bg); min-height:100vh; }
    .ar-shell { padding-bottom:30px; }
    .ar-hero { border-radius:18px; padding:24px; margin-bottom:18px; color:#fff; background:linear-gradient(135deg,#1f3a5f,#315b8f); box-shadow:0 14px 34px rgba(31,58,95,.17); display:flex; justify-content:space-between; align-items:center; gap:15px; }
    .ar-hero h4 { color:#fff; margin:0 0 6px; font-weight:800; }
    .ar-hero p { color:rgba(255,255,255,.82); margin:0; font-size:.86rem; }
    .ar-hero-actions { display:flex; gap:8px; flex-wrap:wrap; flex:0 0 auto; }
    .ar-card { border:0; border-radius:16px; box-shadow:0 8px 26px rgba(31,58,95,.08); margin-bottom:18px; overflow:hidden; }
    .ar-card .card-body { padding:20px; }
    .ar-title { color:#293b50; font-size:.98rem; font-weight:800; margin:0 0 13px; display:flex; align-items:center; gap:8px; flex-wrap:wrap; }
    .ar-title .ar-title-actions { margin-left:auto; display:flex; gap:6px; }
    .ar-label { font-size:.68rem; text-transform:uppercase; letter-spacing:.5px; color:var(--ar-muted); font-weight:800; }
    .ar-step { display:inline-flex; align-items:center; justify-content:center; width:19px; height:19px; border-radius:50%; background:var(--ar-primary); color:#fff; font-size:.62rem; font-weight:800; margin-right:6px; }
    .ar-req { color:#d9534f; margin-left:2px; }
    .ar-section { font-size:.72rem; font-weight:800; color:#3c5068; text-transform:uppercase; letter-spacing:.5px; margin:6px 0 10px; padding-bottom:6px; border-bottom:1px dashed var(--ar-border); }
    .ar-section-opt { color:var(--ar-muted); font-weight:700; text-transform:none; letter-spacing:0; }
    .ar-form .form-control { font-size:.84rem; }
    .ar-form .is-invalid + .select2-container .select2-selection { border-color:#d9534f; }
    .ar-salary-pair { display:flex; gap:8px; }
    .ar-salary-pair > div { flex:1; min-width:0; }
    .ar-form-foot { display:flex; align-items:center; justify-content:flex-end; gap:10px; flex-wrap:wrap; border-top:1px solid var(--ar-border); padding-top:14px; margin-top:4px; }
    .ar-dirty { color:#b9741a; font-size:.74rem; font-weight:700; display:none; margin-right:auto; }
    .ar-context { display:flex; flex-wrap:wrap; align-items:center; gap:8px; padding:11px 13px; background:#f8fbff; border:1px solid var(--ar-border); border-radius:12px; margin-bottom:15px; }
    .ar-context-name { font-weight:800; color:#26384d; font-size:.84rem; }
    .ar-context-sep { color:#c3cedb; }
    .ar-context-text { color:var(--ar-muted); font-size:.75rem; }
    .ar-chip { display:inline-flex; align-items:center; gap:4px; padding:.16rem .5rem; border-radius:999px; font-size:.64rem; font-weight:800; background:#eef5ff; color:#315a85; border:1px solid #dae8f8; }
    .ar-chip.ar-chip-nature { background:#f0f9f6; color:#14805f; border-color:#d3ece3; }
    .ar-chip.ar-chip-manual { background:#f4f0fb; color:#6a4a9c; border-color:#e3d9f3; }
    .ar-salary-line { display:flex; flex-wrap:wrap; align-items:center; gap:10px 16px; padding:11px 14px; border:1px dashed #cfdbe8; border-radius:12px; margin-bottom:15px; font-size:.8rem; color:#2c3f55; }
    .ar-salary-line .ar-label { margin:0; }
    .ar-salary-line b { font-weight:800; letter-spacing:.2px; }
    .ar-salary-line .ar-salary-warn { color:#b9741a; font-weight:700; }
    .ar-report-grid { display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:14px; }
    .ar-report { position:relative; border:1px solid var(--ar-border); border-radius:14px; padding:17px; background:#fff; display:flex; flex-direction:column; min-height:205px; transition:.16s; }
    .ar-report.is-ready:hover { box-shadow:0 10px 24px rgba(31,58,95,.12); transform:translateY(-2px); }
    .ar-report.is-locked { background:#fbfcfe; opacity:.72; }
    .ar-report-icon { width:42px; height:42px; border-radius:12px; background:#eef5ff; color:var(--ar-primary); display:flex; align-items:center; justify-content:center; font-size:22px; margin-bottom:13px; }
    .ar-report.is-ready .ar-report-icon { background:#eaf8f3; color:#14805f; }
    .ar-report-state { position:absolute; top:15px; right:15px; font-size:.6rem; font-weight:800; letter-spacing:.3px; text-transform:uppercase; padding:.16rem .45rem; border-radius:999px; }
    .ar-state-ready { background:#eaf8f3; color:#14805f; border:1px solid #cfeadf; }
    .ar-state-none { background:#fdf3e6; color:#b9741a; border:1px solid #f4d6b4; }
    .ar-state-wait { background:#eef1f6; color:#758396; border:1px solid #e1e7ef; }
    .ar-report h5 { font-size:.86rem; font-weight:800; color:#293b50; min-height:36px; padding-right:62px; }
    .ar-report-meta { color:var(--ar-muted); font-size:.7rem; margin-bottom:14px; overflow-wrap:anywhere; }
    .ar-report-file { display:block; font-weight:700; color:#3c5068; overflow-wrap:anywhere; }
    .ar-view-format { color:var(--ar-accent); font-weight:800; white-space:nowrap; }
    .ar-view-format:hover { color:#14805f; text-decoration:underline; }
    .ar-report .btn { margin-top:auto; }
    .ar-missing { color:#a06b17; background:#fff7e8; border:1px solid #f1dfbc; border-radius:9px; padding:8px 9px; font-size:.69rem; margin-top:auto; }
    .ar-empty { display:flex; flex-direction:column; align-items:center; text-align:center; padding:42px 20px; color:var(--ar-muted); }
    .ar-empty i { font-size:46px; color:#c4d1df; margin-bottom:9px; }
    .ar-empty strong { color:#2d4055; }
    .ar-manage { color:#fff!important; border-color:rgba(255,255,255,.4)!important; }
    @media(max-width:991px){.ar-report-grid{grid-template-columns:1fr}}
    @media(max-width:575px){.ar-hero{align-items:flex-start;flex-direction:column}.ar-card .card-body{padding:15px}.ar-title .ar-title-actions{margin-left:0}}
</style>

<div class="content-page">
    <div class="content">
        <div class="container-fluid ar-shell">
            <div class="ar-hero">
                <div>
                    <h4><?= h($title); ?></h4>
                    <p>For appointees with no application or recommendation record in the system. Type the name, choose the Position Group, Nature of Appointment, and SG/Step, then preview and print the same appointment documents from the saved templates.</p>
                </div>
                <div class="ar-hero-actions">
                    <a href="<?= base_url('Pages/appointment_reports'); ?>" class="btn btn-outline-light btn-sm ar-manage"><i class="mdi mdi-account-search-outline mr-1"></i>System Applicants</a>
                    <a href="<?= base_url('Pages/appointment_template_setup'); ?>" class="btn btn-outline-light btn-sm ar-manage"><i class="mdi mdi-file-cog-outline mr-1"></i>Template Setup</a>
                </div>
            </div>

            <div class="card ar-card">
                <div class="card-body">
                    <h5 class="ar-title">
                        <span><i class="mdi mdi-account-edit-outline mr-1"></i>Encode Appointee</span>
                        <span class="ar-title-actions">
                            <button type="button" class="btn btn-light btn-sm" id="am-new"><i class="mdi mdi-plus mr-1"></i>New</button>
                            <button type="button" class="btn btn-outline-danger btn-sm" id="am-delete" style="display:none;"><i class="mdi mdi-trash-can-outline mr-1"></i>Remove</button>
                        </span>
                    </h5>

                    <div class="form-group">
                        <label class="ar-label" for="am-saved">Previously Encoded</label>
                        <select id="am-saved" class="form-control"><option value=""></option>
                            <?php foreach ($applicants as $applicant) : ?>
                                <option value="<?= h($applicant['recId']); ?>" <?= $selectedRecId === (string) $applicant['recId'] ? 'selected' : ''; ?>><?= h($applicant['name'] . ($applicant['position'] !== '' ? ' — ' . $applicant['position'] : '') . ' · ' . $applicant['nature']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <form id="am-form" class="ar-form" autocomplete="off" onsubmit="return false;">
                        <input type="hidden" name="id" id="am-id" value="0">
                        <div class="ar-section"><span class="ar-step">1</span>Name</div>
                        <div class="form-row">
                            <div class="form-group col-md-3">
                                <label class="ar-label" for="am-first">First Name<span class="ar-req">*</span></label>
                                <input type="text" class="form-control" id="am-first" name="first_name" maxlength="100">
                            </div>
                            <div class="form-group col-md-3">
                                <label class="ar-label" for="am-middle">Middle Name</label>
                                <input type="text" class="form-control" id="am-middle" name="middle_name" maxlength="100">
                            </div>
                            <div class="form-group col-md-3">
                                <label class="ar-label" for="am-last">Last Name<span class="ar-req">*</span></label>
                                <input type="text" class="form-control" id="am-last" name="last_name" maxlength="100">
                            </div>
                            <div class="form-group col-md-1 col-6">
                                <label class="ar-label" for="am-ext">Ext.</label>
                                <input type="text" class="form-control" id="am-ext" name="name_extn" maxlength="20" placeholder="Jr.">
                            </div>
                            <div class="form-group col-md-2 col-6">
                                <label class="ar-label" for="am-sex">Sex</label>
                                <select class="form-control" id="am-sex" name="sex">
                                    <option value="">—</option><option value="Male">Male</option><option value="Female">Female</option>
                                </select>
                            </div>
                        </div>

                        <div class="ar-section"><span class="ar-step">2</span>Position <span class="ar-section-opt">— Position Group decides which template is used</span></div>
                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label class="ar-label" for="am-position">Position Title</label>
                                <input type="text" class="form-control" id="am-position" name="position_title" maxlength="255" placeholder="e.g. Teacher I">
                            </div>
                            <div class="form-group col-md-4">
                                <label class="ar-label" for="am-group">Position Group<span class="ar-req">*</span></label>
                                <select class="form-control" id="am-group" name="position_group"><option value=""></option>
                                    <?php foreach ($groups as $key => $label) : ?><option value="<?= h($key); ?>"><?= h($label); ?></option><?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <label class="ar-label" for="am-item">Item Number</label>
                                <input type="text" class="form-control" id="am-item" name="item_number" maxlength="100">
                            </div>
                            <div class="form-group col-md-4">
                                <label class="ar-label" for="am-school">School / Station</label>
                                <select class="form-control" id="am-school" name="school_id"><option value=""></option>
                                    <?php foreach ($schools as $school) : ?><option value="<?= (int) $school->recID; ?>"><?= h($school->schoolName); ?></option><?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <label class="ar-label" for="am-emptype">Employment Status</label>
                                <select class="form-control" id="am-emptype" name="emp_type"><option value=""></option>
                                    <?php foreach ($empTypes as $type) : ?><option value="<?= h($type); ?>"><?= h($type); ?></option><?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <label class="ar-label" for="am-hired">Date Hired / Assumption</label>
                                <input type="date" class="form-control" id="am-hired" name="date_hired">
                            </div>
                        </div>

                        <div class="ar-section"><span class="ar-step">3</span>Appointment</div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label class="ar-label" for="am-nature">Nature of Appointment<span class="ar-req">*</span></label>
                                <select class="form-control" id="am-nature" name="nature_of_appointment"><option value=""></option>
                                    <?php foreach ($natures as $key => $label) : ?><option value="<?= h($key); ?>"><?= h($label); ?></option><?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label class="ar-label" for="am-sg">Salary Grade &amp; Step<span class="ar-req">*</span></label>
                                <div class="ar-salary-pair">
                                    <div>
                                        <select id="am-sg" class="form-control" name="sg"><option value=""></option>
                                            <?php for ($sg = 1; $sg <= 33; $sg++) : ?><option value="<?= $sg; ?>">SG <?= $sg; ?></option><?php endfor; ?>
                                        </select>
                                    </div>
                                    <div>
                                        <select id="am-step" class="form-control" name="step">
                                            <?php for ($step = 1; $step <= 8; $step++) : ?><option value="<?= $step; ?>">Step <?= $step; ?></option><?php endfor; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="ar-form-foot">
                            <span class="ar-dirty" id="am-dirty"><i class="mdi mdi-alert-outline mr-1"></i>Unsaved changes — save to refresh the reports.</span>
                            <button type="button" class="btn btn-primary" id="am-save"><i class="mdi mdi-content-save-outline mr-1"></i>Save &amp; Show Reports</button>
                        </div>
                    </form>
                </div>
            </div>

            <div id="ar-empty" class="card ar-card"><div class="ar-empty"><i class="mdi mdi-file-document-multiple-outline"></i><strong>Encode or select an appointee</strong><span>Fill in the details above and click <b>Save &amp; Show Reports</b> to generate the appointment documents.</span></div></div>

            <div id="ar-workspace" style="display:none;">
                <div class="card ar-card"><div class="card-body">
                    <h5 class="ar-title"><span><i class="mdi mdi-file-document-multiple-outline mr-1"></i>Appointment Documents</span></h5>
                    <div class="ar-context" id="ar-context"></div>
                    <div class="ar-salary-line" id="ar-salary-line" style="display:none;"></div>
                    <div class="ar-report-grid">
                        <?php $icons = ['appointment'=>'mdi-file-document-check-outline','assumption'=>'mdi-certificate-outline','assignment'=>'mdi-school-outline']; ?>
                        <?php foreach ($documentTypes as $type => $label) : ?>
                            <div class="ar-report" data-report="<?= h($type); ?>">
                                <span class="ar-report-state ar-state-wait">Waiting</span>
                                <div class="ar-report-icon"><i class="mdi <?= h($icons[$type]); ?>"></i></div>
                                <h5><?= h($label); ?></h5>
                                <div class="ar-report-meta">Checking the saved template&hellip;</div>
                                <a class="btn btn-primary btn-sm ar-download" href="#" target="_blank" style="display:none;"><i class="mdi mdi-printer mr-1"></i>Preview &amp; Print</a>
                                <div class="ar-missing" style="display:none;"><i class="mdi mdi-alert-outline mr-1"></i><span>No matching template configured.</span></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div></div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var applicants = <?= json_encode($applicants, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
    var statusUrl = '<?= base_url('Pages/appointment_report_status'); ?>';
    var saveUrl = '<?= base_url('Pages/appointment_manual_save'); ?>';
    var deleteUrl = '<?= base_url('Pages/appointment_manual_delete'); ?>';
    var byId = {}, current = null;
    applicants.forEach(function (a) { byId[a.recId] = a; });

    $('#am-saved').select2({ width:'100%', placeholder:'Search a manually encoded appointee', allowClear:true });
    $('#am-group').select2({ width:'100%', placeholder:'Select position group', minimumResultsForSearch:Infinity });
    $('#am-school').select2({ width:'100%', placeholder:'Select school (optional)', allowClear:true });
    $('#am-emptype').select2({ width:'100%', placeholder:'Select status (optional)', allowClear:true, minimumResultsForSearch:Infinity });
    $('#am-nature').select2({ width:'100%', placeholder:'Select nature of appointment', minimumResultsForSearch:Infinity });
    $('#am-sg').select2({ width:'100%', placeholder:'SG' });
    $('#am-step').select2({ width:'100%', minimumResultsForSearch:Infinity });

    function esc(value) { return $('<div>').text(value == null || value === '' ? '—' : value).html(); }
    function chip(text, cls) { return '<span class="ar-chip ' + (cls || '') + '">' + esc(text) + '</span>'; }
    function optionLabel(a) { return a.name + (a.position ? ' — ' + a.position : '') + ' · ' + a.nature; }

    function fillForm(a) {
        a = a || {};
        $('#am-id').val(a.id || 0);
        $('#am-first').val(a.firstName || '');
        $('#am-middle').val(a.middleName || '');
        $('#am-last').val(a.lastName || '');
        $('#am-ext').val(a.nameExtn || '');
        $('#am-sex').val(a.sex || '');
        $('#am-position').val(a.position || '');
        $('#am-item').val(a.itemNumber || '');
        $('#am-hired').val(a.dateHired && a.dateHired !== '0000-00-00' ? a.dateHired : '');
        $('#am-group').val(a.positionGroup ? String(a.positionGroup) : '').trigger('change.select2');
        $('#am-school').val(a.schoolId ? String(a.schoolId) : '').trigger('change.select2');
        $('#am-emptype').val(a.empType || '').trigger('change.select2');
        $('#am-nature').val(a.nature || '').trigger('change.select2');
        $('#am-sg').val(a.sg ? String(a.sg) : '').trigger('change.select2');
        $('#am-step').val(String(a.step || 1)).trigger('change.select2');
        $('#am-form .is-invalid').removeClass('is-invalid');
        $('#am-delete').toggle(!!a.id);
        setDirty(false);
    }

    function setDirty(dirty) {
        $('#am-dirty').toggle(dirty);
        if (dirty && current) { lockReports('Save the changes to refresh the reports.'); }
    }

    function renderContext(a) {
        var parts = ['<span class="ar-context-name">' + esc(a.name) + '</span>'];
        if (a.position) { parts.push('<span class="ar-context-sep">·</span><span class="ar-context-text">' + esc(a.position) + '</span>'); }
        if (a.itemNumber) { parts.push('<span class="ar-context-sep">·</span><span class="ar-context-text">Item ' + esc(a.itemNumber) + '</span>'); }
        if (a.school) { parts.push('<span class="ar-context-sep">·</span><span class="ar-context-text">' + esc(a.school) + '</span>'); }
        parts.push(chip(a.positionGroupName || 'Not Set'));
        parts.push(chip('Manual', 'ar-chip-manual'));
        parts.push(chip(a.nature, 'ar-chip-nature'));
        $('#ar-context').html(parts.join(' '));
    }

    function resetReports(message) {
        $('.ar-report').removeClass('is-ready is-locked').each(function () {
            $(this).find('.ar-report-state').attr('class', 'ar-report-state ar-state-wait').text('Checking');
            $(this).find('.ar-report-meta').text(message || 'Checking the saved template…');
            $(this).find('.ar-download,.ar-missing').hide();
        });
    }

    function lockReports(message) {
        $('.ar-report').removeClass('is-ready').addClass('is-locked').each(function () {
            $(this).find('.ar-report-state').attr('class', 'ar-report-state ar-state-wait').text('Locked');
            $(this).find('.ar-report-meta').text(message);
            $(this).find('.ar-download,.ar-missing').hide();
        });
    }

    function renderSalary(salary) {
        salary = salary || {};
        var html = '<span class="ar-label">Compensation</span><b>(SG ' + esc(salary.sg) + ' STEP ' + esc(salary.step || 1) + ')</b>';
        if (salary.monthly) {
            html += '<b>' + esc(salary.words) + '</b><b>(P' + esc(salary.monthly) + ')</b>';
        } else {
            html += '<span class="ar-salary-warn"><i class="mdi mdi-alert-outline"></i> No monthly rate for this SG/Step in the active salary schedule.</span>';
        }
        $('#ar-salary-line').html(html).show();
    }

    function showReports(a) {
        current = a;
        if (!a) { $('#ar-workspace').hide(); $('#ar-empty').show(); return; }
        $('#ar-empty').hide(); $('#ar-workspace').show();
        renderContext(a);
        resetReports();
        $.getJSON(statusUrl, { rec_id:a.recId, nature:a.nature, sg:a.sg, step:a.step || 1 }).done(function (res) {
            if (!res || res.status !== 'success') { resetReports((res && res.message) || 'Unable to load report formats.'); return; }
            renderSalary(res.salary);
            Object.keys(res.reports).forEach(function (key) {
                var report = res.reports[key], $card = $('.ar-report[data-report="' + key + '"]');
                var $meta = $card.find('.ar-report-meta'), $state = $card.find('.ar-report-state');
                if (report.available) {
                    $card.removeClass('is-locked').addClass('is-ready');
                    $state.attr('class', 'ar-report-state ar-state-ready').text('Ready');
                    $meta.html('<span class="ar-report-file">' + esc(report.templateName) + '</span>' + esc(report.extension) + ' template · ' + esc(a.nature));
                    if (report.templateUrl) {
                        $meta.append(' · <a href="' + report.templateUrl + '" target="_blank" class="ar-view-format"><i class="mdi mdi-eye-outline"></i> View format</a>');
                    }
                    $card.find('.ar-download').attr('href', report.url).show();
                    $card.find('.ar-missing').hide();
                } else {
                    $card.removeClass('is-ready is-locked');
                    $state.attr('class', 'ar-report-state ar-state-none').text('No template');
                    $meta.text('Position Group: ' + (a.positionGroupName || 'Not Set') + ' · Nature: ' + a.nature);
                    $card.find('.ar-download').hide();
                    $card.find('.ar-missing').show();
                }
            });
        }).fail(function () { resetReports('Unable to load report formats.'); });
    }

    function validate() {
        var ok = true;
        $('#am-form .is-invalid').removeClass('is-invalid');
        ['#am-first', '#am-last', '#am-group', '#am-nature', '#am-sg'].forEach(function (sel) {
            if ($.trim($(sel).val() || '') === '') { $(sel).addClass('is-invalid'); ok = false; }
        });
        return ok;
    }

    $('#am-save').on('click', function () {
        if (!validate()) {
            Swal.fire({icon:'warning',title:'Incomplete details',text:'First Name, Last Name, Position Group, Nature of Appointment, and Salary Grade are required.'});
            return;
        }
        var $btn = $(this).prop('disabled', true);
        $.post(saveUrl, $('#am-form').serialize(), null, 'json').done(function (res) {
            if (!res || res.status !== 'success') {
                Swal.fire({icon:'error',title:'Unable to save',text:(res && res.message) || 'Please try again.'});
                return;
            }
            var a = res.applicant, isNew = !byId[a.recId];
            byId[a.recId] = a;
            if (isNew) {
                $('#am-saved').prepend(new Option(optionLabel(a), a.recId, false, false));
            } else {
                $('#am-saved option[value="' + a.recId + '"]').text(optionLabel(a));
            }
            $('#am-saved').val(a.recId).trigger('change.select2');
            fillForm(a);
            showReports(a);
            Swal.fire({toast:true,position:'top-end',icon:'success',title:res.message,timer:1300,showConfirmButton:false});
        }).fail(function () {
            Swal.fire({icon:'error',title:'Unable to save',text:'Please try again.'});
        }).always(function () { $btn.prop('disabled', false); });
    });

    $('#am-saved').on('change', function () {
        var a = byId[$(this).val() || ''];
        fillForm(a);
        showReports(a || null);
    });

    $('#am-new').on('click', function () {
        $('#am-saved').val('').trigger('change.select2');
        fillForm(null);
        showReports(null);
        $('#am-first').trigger('focus');
    });

    $('#am-delete').on('click', function () {
        var id = parseInt($('#am-id').val(), 10) || 0, a = byId['man_' + id];
        if (!a) { return; }
        Swal.fire({icon:'warning',title:'Remove appointee?',text:'"' + a.name + '" and any saved print edits will be removed.',showCancelButton:true,confirmButtonText:'Remove',confirmButtonColor:'#d9534f'}).then(function (result) {
            if (!(result.value || result.isConfirmed)) { return; }
            $.post(deleteUrl, { id:id }, null, 'json').done(function (res) {
                if (!res || res.status !== 'success') {
                    Swal.fire({icon:'error',title:'Unable to remove',text:(res && res.message) || 'Please try again.'});
                    return;
                }
                delete byId[a.recId];
                $('#am-saved option[value="' + a.recId + '"]').remove();
                $('#am-new').trigger('click');
                Swal.fire({toast:true,position:'top-end',icon:'success',title:res.message,timer:1300,showConfirmButton:false});
            });
        });
    });

    $('#am-form').on('input change', 'input, select', function () {
        $(this).removeClass('is-invalid');
        setDirty(true);
    });

    var initial = byId[$('#am-saved').val() || ''];
    if (initial) { fillForm(initial); showReports(initial); }
});
</script>
