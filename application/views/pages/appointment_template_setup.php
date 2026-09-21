<?php
if (!function_exists('h')) {
    function h($value) { return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8'); }
}
$groupCounts = array_fill_keys(array_keys($groups), 0);
$guideCount = 0;
foreach ($templates as $template) {
    if (isset($groupCounts[(int) $template->position_group])) {
        $groupCounts[(int) $template->position_group]++;
    }
    if ((int) $template->is_guide === 1) {
        $guideCount++;
    }
}
$docShort = ['appointment' => 'Appointment', 'assumption' => 'Assumption', 'assignment' => 'Assignment Order'];
$extMeta = function ($ext) {
    $ext = strtolower((string) $ext);
    if (in_array($ext, ['xls', 'xlsx'], true)) {
        return ['cls' => 'appt-ic-xls', 'icon' => 'mdi-file-excel-outline'];
    }
    if ($ext === 'docx') {
        return ['cls' => 'appt-ic-docx', 'icon' => 'mdi-file-word-outline'];
    }
    return ['cls' => 'appt-ic-file', 'icon' => 'mdi-file-outline'];
};
?>

<style>
    :root { --appt-primary:#1f3a5f; --appt-accent:#1abc9c; --appt-bg:#eef2f7; --appt-border:#e3eaf3; --appt-muted:#758396; }
    .content-page { background:var(--appt-bg); min-height:100vh; }
    .appt-shell { padding-bottom:30px; }
    .appt-hero { border-radius:18px; padding:26px; margin-bottom:20px; color:#fff; background:linear-gradient(135deg,#1f3a5f 0%,#315b8f 60%,#2b7a8f 100%); box-shadow:0 14px 34px rgba(31,58,95,.20); display:flex; justify-content:space-between; align-items:center; gap:22px; }
    .appt-hero-eyebrow { display:inline-flex; align-items:center; gap:6px; font-size:.66rem; font-weight:800; letter-spacing:1px; text-transform:uppercase; color:rgba(255,255,255,.75); background:rgba(255,255,255,.12); border:1px solid rgba(255,255,255,.18); border-radius:999px; padding:.28rem .7rem; margin-bottom:10px; }
    .appt-hero h4 { color:#fff; margin:0 0 6px; font-weight:800; }
    .appt-hero p { color:rgba(255,255,255,.82); margin:0; max-width:760px; font-size:.85rem; }
    .appt-hero-stats { display:flex; gap:12px; flex:0 0 auto; }
    .appt-hero-stat { min-width:96px; text-align:center; background:rgba(255,255,255,.12); border:1px solid rgba(255,255,255,.18); border-radius:14px; padding:12px 14px; }
    .appt-hero-stat b { display:block; font-size:1.3rem; color:#fff; line-height:1.1; }
    .appt-hero-stat span { font-size:.62rem; text-transform:uppercase; letter-spacing:.5px; color:rgba(255,255,255,.72); font-weight:700; }
    .appt-card { border:0; border-radius:16px; box-shadow:0 8px 26px rgba(31,58,95,.08); overflow:hidden; margin-bottom:18px; }
    .appt-card .card-body { padding:22px; }
    .appt-card-head { display:flex; align-items:flex-start; gap:12px; margin-bottom:16px; }
    .appt-card-ic { width:40px; height:40px; border-radius:12px; background:#eef5ff; color:#315a85; display:flex; align-items:center; justify-content:center; font-size:20px; flex:0 0 auto; }
    .appt-title { font-size:.98rem; font-weight:800; color:#26384d; margin:0 0 4px; }
    .appt-sub { color:var(--appt-muted); font-size:.77rem; margin:0; }
    .appt-label { font-size:.66rem; text-transform:uppercase; letter-spacing:.5px; color:var(--appt-muted); font-weight:800; margin-bottom:6px; }
    .appt-select .select2-container--default .select2-selection--single { border:1px solid var(--appt-border); border-radius:10px; height:42px; padding:6px 4px; }
    .appt-select .select2-container--default .select2-selection--single .select2-selection__arrow { height:40px; }
    .appt-select .select2-container--default.select2-container--focus .select2-selection--single, .appt-select .select2-container--default.select2-container--open .select2-selection--single { border-color:var(--appt-accent); }
    .appt-drop { border:2px dashed #c6d4e4; border-radius:14px; background:#f8fbff; padding:26px 18px; text-align:center; cursor:pointer; transition:.18s; margin-top:4px; }
    .appt-drop:hover, .appt-drop.appt-drop-over { border-color:var(--appt-accent); background:#f2fbf9; }
    .appt-drop>i { font-size:34px; color:#8ba3bc; display:block; margin-bottom:6px; transition:.18s; }
    .appt-drop:hover>i, .appt-drop.appt-drop-over>i { color:var(--appt-accent); }
    .appt-drop b { color:#33475d; font-size:.85rem; }
    .appt-drop-link { color:var(--appt-accent); font-weight:800; text-decoration:underline; }
    .appt-drop-hint { color:var(--appt-muted); font-size:.68rem; margin-top:4px; }
    .appt-drop-file { display:inline-flex; align-items:center; gap:8px; margin-top:10px; background:#fff; border:1px solid var(--appt-border); border-radius:10px; padding:8px 14px; font-size:.78rem; font-weight:700; color:#2c4056; box-shadow:0 4px 12px rgba(31,58,95,.08); }
    .appt-drop-file i { font-size:18px; }
    .appt-drop-file .appt-drop-x { color:#b0bccb; cursor:pointer; }
    .appt-drop-file .appt-drop-x:hover { color:#d9534f; }
    .appt-upload-actions { display:flex; align-items:center; gap:10px; margin-top:14px; flex-wrap:wrap; }
    .appt-btn-save { background:linear-gradient(135deg,#1abc9c,#16a085); border:0; border-radius:10px; padding:.55rem 1.2rem; font-weight:700; }
    .appt-btn-save:hover { background:linear-gradient(135deg,#17ad8f,#139078); }
    .appt-tabs { display:flex; gap:8px; flex-wrap:wrap; margin:4px 0 16px; }
    .appt-tab { border:1px solid var(--appt-border); background:#fff; color:#536477; border-radius:999px; padding:7px 14px; font-size:.74rem; font-weight:800; cursor:pointer; transition:.15s; }
    .appt-tab:hover { border-color:#b9c9da; }
    .appt-tab.active { background:var(--appt-primary); color:#fff; border-color:var(--appt-primary); }
    .appt-tab span { margin-left:5px; opacity:.7; }
    .appt-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(340px,1fr)); gap:14px; }
    .appt-item { display:flex; gap:14px; align-items:flex-start; background:#fff; border:1px solid var(--appt-border); border-radius:14px; padding:16px; transition:.16s; }
    .appt-item:hover { box-shadow:0 10px 24px rgba(31,58,95,.12); transform:translateY(-2px); }
    .appt-item-ic { width:46px; height:46px; border-radius:12px; display:flex; align-items:center; justify-content:center; font-size:24px; flex:0 0 auto; }
    .appt-ic-xls { background:#e9f7ef; color:#1e8e5a; }
    .appt-ic-docx { background:#e8f1fd; color:#2b6cb0; }
    .appt-ic-file { background:#eef1f5; color:#6b7c90; }
    .appt-item-body { flex:1; min-width:0; }
    .appt-file { font-weight:800; color:#23364c; display:block; font-size:.8rem; word-break:break-word; }
    .appt-meta { color:var(--appt-muted); font-size:.68rem; display:block; margin-top:3px; }
    .appt-tags { display:flex; flex-wrap:wrap; gap:5px; margin-top:8px; }
    .appt-pill { display:inline-flex; padding:.18rem .5rem; border-radius:999px; font-size:.63rem; font-weight:800; background:#eef5ff; color:#315a85; border:1px solid #dae8f8; }
    .appt-pill.appt-pill-nature { background:#f0f9f6; color:#14805f; border-color:#d3ece3; }
    .appt-pill.appt-guide { background:#fff6e8; color:#926719; border-color:#f3dfb8; }
    .appt-item-actions { display:flex; gap:6px; flex:0 0 auto; }
    .appt-btn-view { background:var(--appt-primary); border:0; border-radius:9px; font-weight:700; }
    .appt-btn-view:hover { background:#274a77; }
    .appt-btn-icon { width:31px; height:31px; padding:0; display:inline-flex; align-items:center; justify-content:center; border-radius:9px; border:1px solid var(--appt-border); color:#5d7186; background:#fff; }
    .appt-btn-icon:hover { color:var(--appt-primary); border-color:#b9c9da; }
    .appt-btn-icon.appt-btn-del:hover { color:#d9534f; border-color:#f0c7c5; }
    .appt-alert { border-radius:12px; border:0; box-shadow:0 5px 14px rgba(31,58,95,.07); }
    .appt-empty { text-align:center; padding:34px 20px; color:var(--appt-muted); }
    .appt-empty i { display:block; font-size:40px; color:#c4d1df; margin-bottom:8px; }
    .appt-ph-search { max-width:320px; border-radius:10px; border:1px solid var(--appt-border); }
    .appt-placeholder { display:inline-flex; align-items:center; padding:.32rem .55rem; border-radius:8px; background:#f3f7fc; border:1px solid var(--appt-border); font-family:monospace; font-size:.7rem; color:#344b65; margin:3px; cursor:pointer; transition:.14s; }
    .appt-placeholder:hover { background:#e7f5f1; border-color:#bde5da; color:#14805f; }
    @media(max-width:767px){ .appt-hero{flex-direction:column; align-items:flex-start} .appt-hero-stats{width:100%} .appt-hero-stat{flex:1} .appt-card .card-body{padding:15px} .appt-grid{grid-template-columns:1fr} }
</style>

<div class="content-page">
    <div class="content">
        <div class="container-fluid appt-shell">
            <div class="appt-hero">
                <div>
                    <span class="appt-hero-eyebrow"><i class="mdi mdi-file-cog-outline"></i> Document Library</span>
                    <h4><?= h($title); ?></h4>
                    <p>Upload the office's finished Excel or Word layout once. Templates are selected by Position Group and Nature of Appointment, then reused when generating an appointee's appointment, assumption certification, and assignment order.</p>
                </div>
                <div class="appt-hero-stats">
                    <div class="appt-hero-stat"><b><?= count($templates); ?></b><span>Saved formats</span></div>
                    <div class="appt-hero-stat"><b><?= count($templates) - $guideCount; ?></b><span>Uploaded</span></div>
                    <div class="appt-hero-stat"><b><?= count($placeholders); ?></b><span>Data fields</span></div>
                </div>
            </div>

            <?php if ($this->session->flashdata('appointment_success')) : ?>
                <div class="alert alert-success appt-alert"><i class="mdi mdi-check-circle-outline mr-2"></i><?= h($this->session->flashdata('appointment_success')); ?></div>
            <?php endif; ?>
            <?php if ($this->session->flashdata('appointment_error')) : ?>
                <div class="alert alert-danger appt-alert"><i class="mdi mdi-alert-circle-outline mr-2"></i><?= h($this->session->flashdata('appointment_error')); ?></div>
            <?php endif; ?>

            <div class="card appt-card">
                <div class="card-body">
                    <div class="appt-card-head">
                        <div class="appt-card-ic"><i class="mdi mdi-cloud-upload-outline"></i></div>
                        <div>
                            <h5 class="appt-title">Upload or Replace a Template</h5>
                            <p class="appt-sub">Uploading the same Position Group, Nature, and Document Type replaces the active format. Accepted files: XLS, XLSX, and DOCX (maximum 20 MB).</p>
                        </div>
                    </div>
                    <?= form_open_multipart('Pages/appointment_template_upload'); ?>
                    <div class="form-row appt-select">
                        <div class="form-group col-lg-4 col-md-4">
                            <label class="appt-label" for="appt-position-group">Position Group</label>
                            <select class="form-control" id="appt-position-group" name="position_group" required>
                                <option value=""></option>
                                <?php foreach ($groups as $id => $label) : ?><option value="<?= (int) $id; ?>"><?= h($label); ?></option><?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group col-lg-4 col-md-4">
                            <label class="appt-label" for="appt-nature">Nature of Appointment</label>
                            <select class="form-control" id="appt-nature" name="nature_of_appointment" required>
                                <option value=""></option>
                                <?php foreach ($natures as $key => $label) : ?><option value="<?= h($key); ?>"><?= h($label); ?></option><?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group col-lg-4 col-md-4">
                            <label class="appt-label" for="appt-document-type">Document Type</label>
                            <select class="form-control" id="appt-document-type" name="document_type" required>
                                <option value=""></option>
                                <?php foreach ($documentTypes as $key => $label) : ?><option value="<?= h($key); ?>"><?= h($label); ?></option><?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="appt-drop" id="appt-drop">
                        <input type="file" id="appt-template-file" name="template_file" accept=".xls,.xlsx,.docx" required hidden>
                        <i class="mdi mdi-cloud-upload-outline"></i>
                        <div><b>Drag &amp; drop your template here</b> or <span class="appt-drop-link">browse files</span></div>
                        <div class="appt-drop-hint">Excel (.xls, .xlsx) or Word (.docx) · max 20 MB</div>
                        <div class="appt-drop-file" id="appt-drop-file" style="display:none;"></div>
                    </div>
                    <div class="appt-upload-actions">
                        <button class="btn btn-primary appt-btn-save" type="submit"><i class="mdi mdi-content-save-outline mr-1"></i>Save Template Format</button>
                        <a class="btn btn-outline-secondary" href="<?= base_url('Pages/appointment_reports'); ?>"><i class="mdi mdi-file-document-multiple-outline mr-1"></i>Appointment Reports</a>
                    </div>
                    </form>
                </div>
            </div>

            <div class="card appt-card">
                <div class="card-body">
                    <div class="appt-card-head">
                        <div class="appt-card-ic"><i class="mdi mdi-folder-multiple-outline"></i></div>
                        <div>
                            <h5 class="appt-title">Saved Formats</h5>
                            <p class="appt-sub">The five supplied appointment-guide files are registered below as initial Non-Teaching formats. Uploading over one retains the new file as the active format. Click <b>View</b> to inspect a format in the browser.</p>
                        </div>
                    </div>
                    <div class="appt-tabs" id="appt-tabs">
                        <button type="button" class="appt-tab active" data-group="all">All <span><?= count($templates); ?></span></button>
                        <?php foreach ($groups as $id => $label) : ?>
                            <button type="button" class="appt-tab" data-group="<?= (int) $id; ?>"><?= h($label); ?><span><?= (int) $groupCounts[$id]; ?></span></button>
                        <?php endforeach; ?>
                    </div>
                    <div class="appt-grid" id="appt-template-grid">
                        <?php foreach ($templates as $template) :
                            $meta = $extMeta($template->extension);
                        ?>
                            <div class="appt-item" data-group="<?= (int) $template->position_group; ?>">
                                <div class="appt-item-ic <?= h($meta['cls']); ?>"><i class="mdi <?= h($meta['icon']); ?>"></i></div>
                                <div class="appt-item-body">
                                    <span class="appt-file" title="<?= h($template->original_name); ?>"><?= h($template->original_name); ?></span>
                                    <span class="appt-meta"><?= strtoupper(h($template->extension)); ?> · <?= number_format(((int) $template->file_size) / 1024, 1); ?> KB</span>
                                    <div class="appt-tags">
                                        <span class="appt-pill"><?= h($groups[(int) $template->position_group] ?? 'Not Set'); ?></span>
                                        <span class="appt-pill appt-pill-nature"><?= h($natures[$template->nature_of_appointment] ?? $template->nature_of_appointment); ?></span>
                                        <span class="appt-pill"><?= h($docShort[$template->document_type] ?? $template->document_type); ?></span>
                                        <?php if ((int) $template->is_guide === 1) : ?><span class="appt-pill appt-guide">Appointment guide</span><?php endif; ?>
                                    </div>
                                </div>
                                <div class="appt-item-actions">
                                    <a class="btn btn-sm btn-primary appt-btn-view" target="_blank" href="<?= base_url('Pages/appointment_template_view/' . (int) $template->id); ?>"><i class="mdi mdi-eye-outline mr-1"></i>View</a>
                                    <a class="appt-btn-icon" title="Download file" href="<?= base_url('Pages/appointment_template_download/' . (int) $template->id); ?>"><i class="mdi mdi-download"></i></a>
                                    <?php if (!(int) $template->is_guide) : ?>
                                        <?= form_open('Pages/appointment_template_delete/' . (int) $template->id, ['style' => 'display:inline;', 'onsubmit' => "return confirm('Delete this saved format? The Position Group and Nature will fall back to an All Natures or appointment-guide format.');"]); ?>
                                            <button type="submit" class="appt-btn-icon appt-btn-del" title="Delete format"><i class="mdi mdi-delete-outline"></i></button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="appt-empty" id="appt-no-templates" style="display:none;"><i class="mdi mdi-file-hidden"></i>No templates saved for this Position Group.</div>
                </div>
            </div>

            <div class="card appt-card">
                <div class="card-body">
                    <div class="appt-card-head">
                        <div class="appt-card-ic"><i class="mdi mdi-code-braces"></i></div>
                        <div class="flex-grow-1">
                            <h5 class="appt-title">Template Data Fields</h5>
                            <p class="appt-sub">In a new Excel cell or Word paragraph, type any field exactly as shown. Click a field to copy it. The system replaces it when the report is generated. The supplied guide's known cells and sample names are also filled automatically.</p>
                        </div>
                    </div>
                    <input type="text" class="form-control appt-ph-search mb-2" id="appt-ph-search" placeholder="Filter fields, e.g. salary, school, name&hellip;">
                    <div id="appt-ph-list">
                        <?php foreach ($placeholders as $key => $description) : ?>
                            <button type="button" class="appt-placeholder" data-copy="{{<?= h($key); ?>}}" data-search="<?= h(strtolower($key . ' ' . $description)); ?>" title="<?= h($description); ?>">{{<?= h($key); ?>}}</button>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    $('#appt-position-group, #appt-nature, #appt-document-type').select2({ width:'100%', placeholder:'Select an option' });

    var $drop = $('#appt-drop');
    var $fileInput = $('#appt-template-file');
    var $fileChip = $('#appt-drop-file');
    var allowed = ['xls', 'xlsx', 'docx'];
    var maxSize = 20 * 1024 * 1024;
    var extIcon = { xls:'mdi-file-excel-outline', xlsx:'mdi-file-excel-outline', docx:'mdi-file-word-outline' };

    function showFile(file) {
        var kb = file.size / 1024;
        var sizeText = kb >= 1024 ? (kb / 1024).toFixed(1) + ' MB' : kb.toFixed(1) + ' KB';
        var ext = file.name.split('.').pop().toLowerCase();
        $fileChip.html('<i class="mdi ' + (extIcon[ext] || 'mdi-file-outline') + '"></i><span>' + $('<div>').text(file.name).html() + ' &middot; ' + sizeText + '</span><i class="mdi mdi-close appt-drop-x" title="Remove file"></i>');
        $fileChip.show();
    }

    function rejectFile(message) {
        $fileInput.val('');
        $fileChip.hide().html('');
        Swal.fire({ icon:'error', title:'Invalid file', text:message });
    }

    function acceptFile(file, fromDrop) {
        var ext = file.name.split('.').pop().toLowerCase();
        if (allowed.indexOf(ext) === -1) {
            rejectFile('Use an Excel (.xls/.xlsx) or Word (.docx) file.');
            return;
        }
        if (file.size > maxSize) {
            rejectFile('The template must be smaller than 20 MB.');
            return;
        }
        if (fromDrop) {
            try {
                var dt = new DataTransfer();
                dt.items.add(file);
                $fileInput[0].files = dt.files;
            } catch (e) {
                rejectFile('Your browser does not support dropping files here. Please use "browse files" instead.');
                return;
            }
        }
        showFile(file);
    }

    $drop.on('click', function (e) {
        if ($(e.target).hasClass('appt-drop-x')) {
            e.stopPropagation();
            $fileInput.val('');
            $fileChip.hide().html('');
            return;
        }
        $fileInput.trigger('click');
    });
    $fileInput.on('change', function () {
        if (this.files.length) { acceptFile(this.files[0], false); }
    });
    $drop.on('dragover dragenter', function (e) {
        e.preventDefault();
        $drop.addClass('appt-drop-over');
    });
    $drop.on('dragleave dragend drop', function (e) {
        e.preventDefault();
        $drop.removeClass('appt-drop-over');
    });
    $drop.on('drop', function (e) {
        var files = e.originalEvent.dataTransfer.files;
        if (files.length) { acceptFile(files[0], true); }
    });

    $('#appt-tabs').on('click', '.appt-tab', function () {
        var group = String($(this).data('group'));
        $('#appt-tabs .appt-tab').removeClass('active');
        $(this).addClass('active');
        var visible = 0;
        $('#appt-template-grid .appt-item').each(function () {
            var show = group === 'all' || String($(this).data('group')) === group;
            $(this).toggle(show); if (show) visible++;
        });
        $('#appt-no-templates').toggle(visible === 0);
    });

    $('#appt-ph-search').on('input', function () {
        var q = $(this).val().toLowerCase();
        $('#appt-ph-list .appt-placeholder').each(function () {
            $(this).toggle($(this).data('search').indexOf(q) !== -1);
        });
    });

    $('.appt-placeholder').on('click', function () {
        var value = $(this).data('copy');
        if (navigator.clipboard) {
            navigator.clipboard.writeText(value);
            Swal.fire({ toast:true, position:'top-end', icon:'success', title:value + ' copied', timer:1100, showConfirmButton:false });
        }
    });
});
</script>
