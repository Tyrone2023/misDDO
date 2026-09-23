<?php
if (!function_exists('h')) {
    function h($value) { return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8'); }
}
?>

<style>
    :root { --ar-primary:#1f3a5f; --ar-accent:#1abc9c; --ar-bg:#f4f7fb; --ar-border:#e4ebf4; --ar-muted:#758396; }
    .content-page { background:var(--ar-bg); min-height:100vh; }
    .ar-shell { padding-bottom:30px; }
    .ar-hero { border-radius:18px; padding:24px; margin-bottom:18px; color:#fff; background:linear-gradient(135deg,#1f3a5f,#315b8f); box-shadow:0 14px 34px rgba(31,58,95,.17); display:flex; justify-content:space-between; align-items:center; gap:15px; }
    .ar-hero h4 { color:#fff; margin:0 0 6px; font-weight:800; }
    .ar-hero p { color:rgba(255,255,255,.82); margin:0; font-size:.86rem; }
    .ar-card { border:0; border-radius:16px; box-shadow:0 8px 26px rgba(31,58,95,.08); margin-bottom:18px; overflow:hidden; }
    .ar-card .card-body { padding:20px; }
    .ar-title { color:#293b50; font-size:.98rem; font-weight:800; margin:0 0 13px; }
    .ar-label { font-size:.68rem; text-transform:uppercase; letter-spacing:.5px; color:var(--ar-muted); font-weight:800; }
    .ar-step { display:inline-flex; align-items:center; justify-content:center; width:19px; height:19px; border-radius:50%; background:var(--ar-primary); color:#fff; font-size:.62rem; font-weight:800; margin-right:6px; }
    .ar-req { display:inline-block; margin-left:6px; padding:.06rem .38rem; border-radius:999px; background:#fdeee0; color:#b9741a; border:1px solid #f4d6b4; font-size:.58rem; font-weight:800; letter-spacing:.3px; }
    #ar-nature-group.ar-need .select2-container--default .select2-selection--single { border-color:#e6a23c; box-shadow:0 0 0 3px rgba(230,162,60,.16); animation:ar-pulse 1.5s ease-in-out 2; }
    @keyframes ar-pulse { 0%,100%{box-shadow:0 0 0 3px rgba(230,162,60,.16)} 50%{box-shadow:0 0 0 6px rgba(230,162,60,.28)} }
    .ar-context { display:flex; flex-wrap:wrap; align-items:center; gap:8px; padding:11px 13px; background:#f8fbff; border:1px solid var(--ar-border); border-radius:12px; margin-bottom:15px; }
    .ar-context-name { font-weight:800; color:#26384d; font-size:.84rem; }
    .ar-context-sep { color:#c3cedb; }
    .ar-context-text { color:var(--ar-muted); font-size:.75rem; }
    .ar-chip { display:inline-flex; align-items:center; gap:4px; padding:.16rem .5rem; border-radius:999px; font-size:.64rem; font-weight:800; background:#eef5ff; color:#315a85; border:1px solid #dae8f8; }
    .ar-chip.ar-chip-nature { background:#f0f9f6; color:#14805f; border-color:#d3ece3; }
    .ar-chip.ar-chip-warn { background:#fdf3e6; color:#b9741a; border-color:#f4d6b4; }
    .ar-need-banner { display:flex; align-items:center; gap:10px; border:1px solid #f4d6b4; background:#fff8ee; border-radius:12px; padding:13px 15px; margin-bottom:15px; }
    .ar-need-banner i { font-size:22px; color:#e6a23c; }
    .ar-need-banner b { display:block; color:#8a5a12; font-size:.8rem; }
    .ar-need-banner span { color:#a5761f; font-size:.72rem; }
    .ar-need-banner .btn { margin-left:auto; }
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
    @media(max-width:991px){.ar-report-grid{grid-template-columns:1fr}.ar-need-banner .btn{margin-left:0}}
    @media(max-width:575px){.ar-hero{align-items:flex-start;flex-direction:column}.ar-card .card-body{padding:15px}.ar-need-banner{flex-wrap:wrap}}
</style>

<div class="content-page">
    <div class="content">
        <div class="container-fluid ar-shell">
            <div class="ar-hero">
                <div>
                    <h4><?= h($title); ?></h4>
                    <p>Search any applicant — whether the appointment was issued in the system, the vacancy stopped at RQA, or no recommendation exists at all. The three appointment documents are then filled from the saved Position Group and Nature templates, ready to preview and print.</p>
                </div>
                <?php if ($canManage) : ?><a href="<?= base_url('Pages/appointment_template_setup'); ?>" class="btn btn-outline-light btn-sm ar-manage"><i class="mdi mdi-file-cog-outline mr-1"></i>Template Setup</a><?php endif; ?>
            </div>

            <div class="card ar-card">
                <div class="card-body">
                    <h5 class="ar-title"><i class="mdi mdi-account-search-outline mr-1"></i>Select Appointee</h5>
                    <div class="form-row">
                        <div class="form-group col-lg-8">
                            <label class="ar-label" for="ar-applicant"><span class="ar-step">1</span>Applicant</label>
                            <select id="ar-applicant" class="form-control"><option value=""></option>
                                <?php foreach ($applicants as $applicant) : ?>
                                    <option value="<?= h($applicant['recId']); ?>" <?= $selectedRecId === (string) $applicant['recId'] ? 'selected' : ''; ?>><?= h($applicant['name'] . ' — ' . $applicant['position'] . ' — ' . $applicant['itemNumber'] . ' · ' . $applicant['statusLabel']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group col-lg-4" id="ar-nature-group">
                            <label class="ar-label" for="ar-nature"><span class="ar-step">2</span>Nature of Appointment <span class="ar-req">Required</span></label>
                            <select id="ar-nature" class="form-control" disabled><option value=""></option>
                                <?php foreach ($natures as $key => $label) : ?><option value="<?= h($key); ?>"><?= h($label); ?></option><?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div id="ar-empty" class="card ar-card"><div class="ar-empty"><i class="mdi mdi-file-document-multiple-outline"></i><strong>Select an applicant</strong><span>Any applicant can generate appointment documents here — appointed, hired, or still in the application stage.</span></div></div>

            <div id="ar-workspace" style="display:none;">
                <div class="card ar-card"><div class="card-body">
                    <h5 class="ar-title"><i class="mdi mdi-file-document-multiple-outline mr-1"></i>Appointment Documents</h5>
                    <div class="ar-context" id="ar-context"></div>
                    <div class="ar-need-banner" id="ar-need-nature" style="display:none;">
                        <i class="mdi mdi-alert-circle-outline"></i>
                        <div>
                            <b>Step 2 &mdash; Select the Nature of Appointment</b>
                            <span>The documents stay locked until a Nature is chosen, because each Nature uses its own saved template.</span>
                        </div>
                        <button type="button" class="btn btn-sm btn-warning" id="ar-pick-nature"><i class="mdi mdi-cursor-default-click-outline mr-1"></i>Select now</button>
                    </div>
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
    var canManage = <?= $canManage ? 'true' : 'false'; ?>;
    var statusUrl = '<?= base_url('Pages/appointment_report_status'); ?>';
    var saveNatureUrl = '<?= base_url('Pages/appointment_report_nature'); ?>';
    var byId = {};
    applicants.forEach(function (a) { byId[String(a.recId)] = a; });

    $('#ar-applicant').select2({ width:'100%', placeholder:'Search applicant, position, or item number', allowClear:true });
    $('#ar-nature').select2({ width:'100%', placeholder:'Select nature of appointment', minimumResultsForSearch:Infinity });

    function esc(value) { return $('<div>').text(value == null || value === '' ? '—' : value).html(); }
    function chip(text, cls) { return '<span class="ar-chip ' + (cls || '') + '">' + esc(text) + '</span>'; }

    function renderContext(row) {
        var parts = ['<span class="ar-context-name">' + esc(row.name) + '</span>'];
        if (row.position) { parts.push('<span class="ar-context-sep">·</span><span class="ar-context-text">' + esc(row.position) + '</span>'); }
        if (row.itemNumber) { parts.push('<span class="ar-context-sep">·</span><span class="ar-context-text">Item ' + esc(row.itemNumber) + '</span>'); }
        parts.push(chip(row.positionGroupName || 'Not Set'));
        parts.push(chip(row.statusLabel));
        parts.push(row.nature ? chip(row.nature, 'ar-chip-nature') : chip('Nature not selected', 'ar-chip-warn'));
        $('#ar-context').html(parts.join(' '));
    }

    /** Nature is mandatory: highlight it and keep the documents locked until it is chosen. */
    function requireNature(openPicker) {
        $('#ar-nature-group').addClass('ar-need');
        $('#ar-need-nature').show();
        lockReports('Waiting for the Nature of Appointment.');
        if (openPicker) { setTimeout(function () { $('#ar-nature').select2('open'); }, 250); }
    }

    /** Put the picker and the on-screen state back to the applicant's stored Nature. */
    function revertNature(row) {
        $('#ar-nature').val(row.nature || '').trigger('change.select2');
        renderContext(row);
        if (row.nature) { clearNatureWarning(); loadReports(row.recId, row.nature); } else { requireNature(false); }
    }

    function clearNatureWarning() {
        $('#ar-nature-group').removeClass('ar-need');
        $('#ar-need-nature').hide();
    }

    function renderApplicant(isInitial) {
        var id = String($('#ar-applicant').val() || '');
        var row = byId[id];
        if (!row) {
            $('#ar-workspace').hide(); $('#ar-empty').show();
            clearNatureWarning();
            $('#ar-nature').val('').prop('disabled', true).trigger('change.select2');
            return;
        }
        $('#ar-empty').hide(); $('#ar-workspace').show();
        renderContext(row);
        $('#ar-nature').prop('disabled', false).val(row.nature || '').trigger('change.select2');
        if (row.nature) {
            clearNatureWarning();
            loadReports(row.recId, row.nature);
        } else {
            requireNature(!isInitial);
        }
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

    function loadReports(recId, nature) {
        resetReports();
        $.getJSON(statusUrl, { rec_id:recId, nature:nature }).done(function (res) {
            if (!res || res.status !== 'success') { resetReports((res && res.message) || 'Unable to load report formats.'); return; }
            Object.keys(res.reports).forEach(function (key) {
                var report = res.reports[key], $card = $('.ar-report[data-report="' + key + '"]');
                var $meta = $card.find('.ar-report-meta'), $state = $card.find('.ar-report-state');
                if (report.available) {
                    $card.removeClass('is-locked').addClass('is-ready');
                    $state.attr('class', 'ar-report-state ar-state-ready').text('Ready');
                    $meta.html('<span class="ar-report-file">' + esc(report.templateName) + '</span>' + esc(report.extension) + ' template · ' + esc(nature));
                    if (report.templateUrl) {
                        $meta.append(' · <a href="' + report.templateUrl + '" target="_blank" class="ar-view-format"><i class="mdi mdi-eye-outline"></i> View format</a>');
                    }
                    $card.find('.ar-download').attr('href', report.url).show();
                    $card.find('.ar-missing').hide();
                } else {
                    $card.removeClass('is-ready is-locked');
                    $state.attr('class', 'ar-report-state ar-state-none').text('No template');
                    $meta.text('Position Group: ' + (byId[String(recId)].positionGroupName || 'Not Set') + ' · Nature: ' + nature);
                    $card.find('.ar-download').hide();
                    $card.find('.ar-missing').show();
                }
            });
        }).fail(function () { resetReports('Unable to load report formats.'); });
    }

    $('#ar-applicant').on('change', function () { renderApplicant(false); });
    $('#ar-pick-nature').on('click', function () { $('#ar-nature').select2('open'); });
    $('#ar-nature').on('change', function () {
        var id = String($('#ar-applicant').val() || ''), nature = $(this).val() || '', row = byId[id];
        if (row && !nature) { requireNature(false); return; }
        if (nature) { clearNatureWarning(); if (row) { renderContext($.extend({}, row, { nature: nature })); } }
        if (!row || !nature || nature === row.nature) { if (row && nature) loadReports(row.recId, nature); return; }
        if (!canManage) { loadReports(row.recId, nature); return; }
        $(this).prop('disabled', true);
        $.post(saveNatureUrl, { rec_id:row.recId, nature_of_appointment:nature }, null, 'json').done(function (res) {
            $('#ar-nature').prop('disabled', false);
            if (res && res.status === 'success') {
                row.nature = nature; renderContext(row); loadReports(row.recId, nature);
                Swal.fire({toast:true,position:'top-end',icon:'success',title:res.message,timer:1300,showConfirmButton:false});
            } else {
                revertNature(row);
                Swal.fire({icon:'error',title:'Unable to save',text:(res && res.message) || 'Please try again.'});
            }
        }).fail(function () { $('#ar-nature').prop('disabled', false); revertNature(row); });
    });

    renderApplicant(true);
});
</script>
