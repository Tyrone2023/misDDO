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
    .ar-summary { display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); gap:12px; }
    .ar-fact { background:#f8fbff; border:1px solid var(--ar-border); border-radius:12px; padding:12px; min-height:74px; }
    .ar-fact span { display:block; color:var(--ar-muted); font-size:.64rem; text-transform:uppercase; letter-spacing:.45px; font-weight:800; margin-bottom:5px; }
    .ar-fact strong { display:block; color:#2b3e54; font-size:.78rem; overflow-wrap:anywhere; }
    .ar-report-grid { display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:14px; }
    .ar-report { border:1px solid var(--ar-border); border-radius:14px; padding:17px; background:#fff; display:flex; flex-direction:column; min-height:205px; }
    .ar-report-icon { width:42px; height:42px; border-radius:12px; background:#eef5ff; color:var(--ar-primary); display:flex; align-items:center; justify-content:center; font-size:22px; margin-bottom:13px; }
    .ar-report h5 { font-size:.86rem; font-weight:800; color:#293b50; min-height:36px; }
    .ar-report-meta { color:var(--ar-muted); font-size:.7rem; margin-bottom:14px; overflow-wrap:anywhere; }
    .ar-view-format { color:var(--ar-accent); font-weight:800; white-space:nowrap; }
    .ar-view-format:hover { color:#14805f; text-decoration:underline; }
    .ar-report .btn { margin-top:auto; }
    .ar-missing { color:#a06b17; background:#fff7e8; border:1px solid #f1dfbc; border-radius:9px; padding:8px 9px; font-size:.69rem; margin-top:auto; }
    .ar-empty { display:flex; flex-direction:column; align-items:center; text-align:center; padding:42px 20px; color:var(--ar-muted); }
    .ar-empty i { font-size:46px; color:#c4d1df; margin-bottom:9px; }
    .ar-empty strong { color:#2d4055; }
    .ar-manage { color:#fff!important; border-color:rgba(255,255,255,.4)!important; }
    @media(max-width:991px){.ar-summary{grid-template-columns:repeat(2,minmax(0,1fr))}.ar-report-grid{grid-template-columns:1fr}}
    @media(max-width:575px){.ar-summary{grid-template-columns:1fr}.ar-hero{align-items:flex-start;flex-direction:column}.ar-card .card-body{padding:15px}}
</style>

<div class="content-page">
    <div class="content">
        <div class="container-fluid ar-shell">
            <div class="ar-hero">
                <div>
                    <h4><?= h($title); ?></h4>
                    <p>Search any applicant — whether the appointment was issued in the system, the vacancy stopped at RQA, or no recommendation exists at all. Then generate the three appointment documents from the saved Position Group and Nature templates.</p>
                </div>
                <?php if ($canManage) : ?><a href="<?= base_url('Pages/appointment_template_setup'); ?>" class="btn btn-outline-light btn-sm ar-manage"><i class="mdi mdi-file-cog-outline mr-1"></i>Template Setup</a><?php endif; ?>
            </div>

            <div class="card ar-card">
                <div class="card-body">
                    <h5 class="ar-title"><i class="mdi mdi-account-search-outline mr-1"></i>Select Appointee</h5>
                    <div class="form-row">
                        <div class="form-group col-lg-8">
                            <label class="ar-label" for="ar-applicant">Applicant</label>
                            <select id="ar-applicant" class="form-control"><option value=""></option>
                                <?php foreach ($applicants as $applicant) : ?>
                                    <option value="<?= h($applicant['recId']); ?>" <?= $selectedRecId === (string) $applicant['recId'] ? 'selected' : ''; ?>><?= h($applicant['name'] . ' — ' . $applicant['position'] . ' — ' . $applicant['itemNumber'] . ' · ' . $applicant['statusLabel']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group col-lg-4">
                            <label class="ar-label" for="ar-nature">Nature of Appointment</label>
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
                    <h5 class="ar-title">Appointment Information</h5>
                    <div id="ar-status-note" class="alert alert-info py-2" style="display:none; font-size:.76rem;"><i class="mdi mdi-information-outline mr-1"></i>No appointment has been issued through the system for this applicant. You can still generate the documents here.</div>
                    <div class="ar-summary" id="ar-summary"></div>
                </div></div>
                <div class="card ar-card"><div class="card-body">
                    <h5 class="ar-title">Linked Reports</h5>
                    <div class="ar-report-grid">
                        <?php $icons = ['appointment'=>'mdi-file-document-check-outline','assumption'=>'mdi-certificate-outline','assignment'=>'mdi-school-outline']; ?>
                        <?php foreach ($documentTypes as $type => $label) : ?>
                            <div class="ar-report" data-report="<?= h($type); ?>">
                                <div class="ar-report-icon"><i class="mdi <?= h($icons[$type]); ?>"></i></div>
                                <h5><?= h($label); ?></h5>
                                <div class="ar-report-meta">Checking the saved template…</div>
                                <a class="btn btn-primary btn-sm ar-download" href="#" target="_blank" style="display:none;"><i class="mdi mdi-download mr-1"></i>Generate &amp; Download</a>
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
    function fact(label, value) { return '<div class="ar-fact"><span>' + esc(label) + '</span><strong>' + esc(value) + '</strong></div>'; }

    function renderApplicant() {
        var id = String($('#ar-applicant').val() || '');
        var row = byId[id];
        if (!row) {
            $('#ar-workspace').hide(); $('#ar-empty').show();
            $('#ar-nature').val('').prop('disabled', true).trigger('change.select2');
            return;
        }
        $('#ar-empty').hide(); $('#ar-workspace').show();
        $('#ar-summary').html(
            fact('Applicant', row.name) + fact('Applicant Code', row.code) +
            fact('Position', row.position) + fact('Position Group', row.positionGroupName) +
            fact('Status', row.statusLabel) +
            fact('Item Number', row.itemNumber) + fact('School Assigned', row.school) +
            fact('Date Hired', row.dateHired) + fact('Appointment Issued', row.dateIssued || 'Not issued through the system') +
            fact('Complete Address', row.address) + fact('Contact Number', row.contact) + fact('Email', row.email)
        );
        $('#ar-status-note').toggle(row.status !== 'appointed');
        $('#ar-nature').prop('disabled', false).val(row.nature || '').trigger('change.select2');
        if (row.nature) loadReports(row.recId, row.nature); else resetReports('Select the Nature of Appointment to link the correct formats.');
    }

    function resetReports(message) {
        $('.ar-report').each(function () {
            $(this).find('.ar-report-meta').text(message || 'Checking the saved template…');
            $(this).find('.ar-download,.ar-missing').hide();
        });
    }

    function loadReports(recId, nature) {
        resetReports();
        $.getJSON(statusUrl, { rec_id:recId, nature:nature }).done(function (res) {
            if (!res || res.status !== 'success') { resetReports((res && res.message) || 'Unable to load report formats.'); return; }
            Object.keys(res.reports).forEach(function (key) {
                var report = res.reports[key], $card = $('.ar-report[data-report="' + key + '"]');
                if (report.available) {
                    var $meta = $card.find('.ar-report-meta');
                    $meta.text(report.templateName + ' · ' + report.extension);
                    if (report.templateUrl) {
                        $meta.append(' · <a href="' + report.templateUrl + '" target="_blank" class="ar-view-format"><i class="mdi mdi-eye-outline"></i> View format</a>');
                    }
                    $card.find('.ar-download').attr('href', report.url).show();
                    $card.find('.ar-missing').hide();
                } else {
                    $card.find('.ar-report-meta').text('Position Group: ' + (byId[String(recId)].positionGroupName || 'Not Set') + ' · Nature: ' + nature);
                    $card.find('.ar-download').hide();
                    $card.find('.ar-missing').show();
                }
            });
        }).fail(function () { resetReports('Unable to load report formats.'); });
    }

    $('#ar-applicant').on('change', renderApplicant);
    $('#ar-nature').on('change', function () {
        var id = String($('#ar-applicant').val() || ''), nature = $(this).val() || '', row = byId[id];
        if (!row || !nature || nature === row.nature) { if (row && nature) loadReports(row.recId, nature); return; }
        if (!canManage) { loadReports(row.recId, nature); return; }
        $(this).prop('disabled', true);
        $.post(saveNatureUrl, { rec_id:row.recId, nature_of_appointment:nature }, null, 'json').done(function (res) {
            $('#ar-nature').prop('disabled', false);
            if (res && res.status === 'success') {
                row.nature = nature; loadReports(row.recId, nature);
                Swal.fire({toast:true,position:'top-end',icon:'success',title:res.message,timer:1300,showConfirmButton:false});
            } else {
                $('#ar-nature').val(row.nature || '').trigger('change.select2');
                Swal.fire({icon:'error',title:'Unable to save',text:(res && res.message) || 'Please try again.'});
            }
        }).fail(function () { $('#ar-nature').prop('disabled', false).val(row.nature || '').trigger('change.select2'); });
    });

    renderApplicant();
});
</script>
