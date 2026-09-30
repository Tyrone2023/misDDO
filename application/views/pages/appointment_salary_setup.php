<?php
if (!function_exists('h')) {
    function h($value) { return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8'); }
}
$rateCount = 0;
foreach ($rates as $steps) {
    $rateCount += count($steps);
}
$isActive = !empty($schedule) && (int) $schedule->is_active === 1;
$pageUrl = base_url('Pages/appointment_salary_setup');
?>

<style>
    :root { --sal-primary:#1f3a5f; --sal-accent:#12a37f; --sal-accent-soft:#e7f6f1; --sal-bg:#f3f6fa; --sal-border:#e3e9f1; --sal-grid:#e8edf3; --sal-muted:#7a8898; --sal-text:#223449; }
    .content-page { background:var(--sal-bg); min-height:100vh; }
    .sal-shell { padding-bottom:30px; }

    .sal-head { display:flex; justify-content:space-between; align-items:center; gap:16px; flex-wrap:wrap; margin:6px 0 18px; }
    .sal-head h4 { margin:0 0 3px; font-weight:800; color:var(--sal-text); letter-spacing:-.2px; }
    .sal-head p { margin:0; color:var(--sal-muted); font-size:.86rem; }
    .sal-head-actions { display:flex; gap:8px; flex-wrap:wrap; }
    .sal-btn { border-radius:10px; font-weight:700; font-size:.82rem; padding:.5rem 1rem; display:inline-flex; align-items:center; gap:6px; }
    .sal-btn-primary { background:var(--sal-accent); border:1px solid var(--sal-accent); color:#fff; box-shadow:0 6px 14px rgba(18,163,127,.22); }
    .sal-btn-primary:hover { background:#0f8f6f; border-color:#0f8f6f; color:#fff; }
    .sal-btn-light { background:#fff; border:1px solid var(--sal-border); color:#4a5d72; }
    .sal-btn-light:hover { border-color:#c3d0de; color:var(--sal-primary); background:#fff; }
    .sal-btn-danger:hover { color:#d9534f; border-color:#f1c9c7; }
    .sal-alert { border-radius:12px; border:0; font-size:.84rem; }

    .sal-sheet { background:#fff; border:1px solid var(--sal-border); border-radius:16px; box-shadow:0 10px 30px rgba(31,58,95,.07); overflow:hidden; }

    /* Schedule details: title and date are edited in place. */
    .sal-meta { display:flex; justify-content:space-between; align-items:center; gap:16px; flex-wrap:wrap; padding:18px 22px; border-bottom:1px solid var(--sal-border); }
    .sal-meta-main { flex:1; min-width:260px; }
    .sal-title-row { display:flex; align-items:center; gap:10px; }
    .sal-title-input { font-size:1.2rem; font-weight:800; color:var(--sal-text); border:1px solid transparent; border-radius:8px; padding:2px 8px; margin-left:-9px; background:transparent; width:100%; max-width:420px; transition:.15s; }
    .sal-title-input:hover { border-color:var(--sal-border); }
    .sal-title-input:focus { outline:0; border-color:var(--sal-accent); background:#fff; box-shadow:0 0 0 3px rgba(18,163,127,.12); }
    .sal-badge { display:inline-flex; align-items:center; gap:4px; font-size:12px; font-weight:800; padding:.2rem .6rem; border-radius:999px; white-space:nowrap; }
    .sal-badge-active { background:var(--sal-accent-soft); color:#0d7a5f; }
    .sal-badge-off { background:#eef1f5; color:#6b7c90; }
    .sal-sub { display:flex; align-items:center; gap:14px; flex-wrap:wrap; margin-top:6px; color:var(--sal-muted); font-size:.82rem; }
    .sal-sub i { color:#a3b1c1; }
    .sal-date-input { border:1px solid transparent; border-radius:7px; padding:1px 6px; color:var(--sal-text); font-weight:600; background:transparent; font-size:.82rem; }
    .sal-date-input:hover { border-color:var(--sal-border); }
    .sal-date-input:focus { outline:0; border-color:var(--sal-accent); background:#fff; }
    .sal-meta-actions { display:flex; align-items:center; gap:8px; flex-wrap:wrap; }
    .sal-meta-actions form { display:inline; margin:0; }
    .sal-switch { height:38px; min-width:230px; border:1px solid var(--sal-border); border-radius:10px; padding:0 10px; color:var(--sal-text); background:#fff; font-size:.82rem; font-weight:600; }

    /* Formula bar, as in Excel: selected cell on the left, its value next to it. */
    .sal-fbar { display:flex; align-items:stretch; gap:0; border-bottom:1px solid var(--sal-border); background:#fafbfd; font-size:.86rem; }
    .sal-fbar > div { display:flex; align-items:center; padding:9px 16px; border-right:1px solid var(--sal-border); }
    .sal-namebox { min-width:150px; font-weight:800; color:var(--sal-primary); }
    .sal-fx { color:#9aa8b8; font-style:italic; font-weight:700; }
    .sal-fvalue { flex:1; font-weight:700; color:var(--sal-text); font-variant-numeric:tabular-nums; }
    .sal-fannual { color:var(--sal-muted); white-space:nowrap; }
    .sal-fannual b { color:var(--sal-text); margin-left:6px; font-variant-numeric:tabular-nums; }
    .sal-status { border-right:0 !important; white-space:nowrap; font-weight:700; font-size:12px; color:#0d7a5f; gap:6px; }
    .sal-status.is-saving { color:#b7791f; }
    .sal-status.is-error { color:#d9534f; }

    /* Grid */
    .sal-grid-wrap { max-height:calc(100vh - 330px); min-height:380px; overflow:auto; }
    .sal-grid { width:100%; min-width:760px; table-layout:fixed; border-collapse:separate; border-spacing:0; font-size:.88rem; }
    .sal-grid th { position:sticky; top:0; z-index:2; background:#f4f6f9; color:#607185; font-size:12px; font-weight:800; text-transform:uppercase; letter-spacing:.4px; padding:10px 12px; text-align:right; border-bottom:1px solid #dbe2ea; border-right:1px solid var(--sal-grid); transition:.12s; }
    .sal-grid th:first-child, .sal-grid td.sal-rowhead { position:sticky; left:0; width:78px; text-align:center; background:#f4f6f9; color:#607185; font-weight:800; border-right:1px solid #dbe2ea; }
    .sal-grid th:first-child { z-index:3; }
    .sal-grid td.sal-rowhead { z-index:1; font-size:12px; transition:.12s; }
    .sal-grid th.is-hl, .sal-grid td.sal-rowhead.is-hl { background:var(--sal-accent-soft); color:#0d7a5f; }
    .sal-grid td { padding:0; border-bottom:1px solid var(--sal-grid); border-right:1px solid var(--sal-grid); background:#fff; position:relative; }
    .sal-grid tbody tr:nth-child(even) td:not(.sal-rowhead) { background:#fcfdfe; }
    .sal-cell { display:block; width:100%; height:40px; border:0; background:transparent; padding:0 12px; text-align:right; color:var(--sal-text); font-variant-numeric:tabular-nums; cursor:cell; caret-color:var(--sal-accent); }
    .sal-cell::placeholder { color:transparent; }
    .sal-cell:focus { outline:2px solid var(--sal-accent); outline-offset:-2px; background:#fff; cursor:text; position:relative; z-index:1; box-shadow:0 4px 14px rgba(18,163,127,.18); }
    .sal-cell.is-saving { color:#b7791f; }
    .sal-cell.is-saved { animation:salSaved 1.1s ease; }
    .sal-cell.is-error { background:#fdecec; color:#c0392b; }
    @keyframes salSaved { 0% { background:#d5f3e9; } 100% { background:transparent; } }

    .sal-hint { display:flex; gap:18px; flex-wrap:wrap; padding:11px 22px; border-top:1px solid var(--sal-border); background:#fafbfd; color:var(--sal-muted); font-size:12px; }
    .sal-hint kbd { background:#fff; color:#4a5d72; border:1px solid #d6dee8; border-bottom-width:2px; border-radius:5px; padding:1px 6px; font-size:11px; font-weight:700; box-shadow:none; margin-right:3px; }

    .sal-empty { text-align:center; padding:70px 20px; color:var(--sal-muted); }
    .sal-empty i { font-size:52px; color:#c4d1df; display:block; margin-bottom:10px; }
    .sal-empty b { display:block; color:var(--sal-text); font-size:1rem; margin-bottom:6px; }
    .sal-empty .sal-head-actions { justify-content:center; margin-top:16px; }

    .sal-drop { border:2px dashed #c9d6e4; border-radius:12px; background:#f8fbff; padding:26px 16px; text-align:center; cursor:pointer; transition:.15s; }
    .sal-drop:hover, .sal-drop.sal-drop-over { border-color:var(--sal-accent); background:#f2fbf8; }
    .sal-drop > i { font-size:34px; color:#8ba3bc; display:block; margin-bottom:4px; }
    .sal-drop .sal-drop-link { color:var(--sal-accent); font-weight:800; text-decoration:underline; }
    .sal-drop small { display:block; color:var(--sal-muted); margin-top:4px; font-size:12px; }
    .sal-drop-file { display:none; margin-top:10px; font-weight:700; color:var(--sal-text); font-size:.82rem; }
    .sal-modal .modal-content { border:0; border-radius:16px; }
    .sal-modal .modal-header, .sal-modal .modal-footer { border-color:var(--sal-border); }
    .sal-modal label { font-size:12px; font-weight:800; text-transform:uppercase; letter-spacing:.4px; color:var(--sal-muted); margin-bottom:4px; }
    .sal-modal .form-control { border-radius:9px; border-color:var(--sal-border); }
    .sal-modal small { font-size:12px; }

    @media(max-width:767px){ .sal-meta{padding:15px} .sal-fannual{display:none !important} .sal-hint{display:none} .sal-switch{min-width:0; width:100%} }
</style>

<div class="content-page">
    <div class="content">
        <div class="container-fluid sal-shell">

            <div class="sal-head">
                <div>
                    <h4><?= h($title); ?></h4>
                    <p>The active schedule supplies the monthly salary printed on appointments, based on the appointee's Salary Grade and Step.</p>
                </div>
                <div class="sal-head-actions">
                    <a class="btn sal-btn sal-btn-light" href="<?= base_url('Pages/appointment_template_setup'); ?>"><i class="mdi mdi-arrow-left"></i>Template Setup</a>
                    <button type="button" class="btn sal-btn sal-btn-light sal-new"><i class="mdi mdi-plus"></i>New Schedule</button>
                    <button type="button" class="btn sal-btn sal-btn-primary" data-toggle="modal" data-target="#sal-upload-modal"><i class="mdi mdi-upload"></i>Upload Schedule</button>
                </div>
            </div>

            <?php if ($this->session->flashdata('appointment_success')) : ?>
                <div class="alert alert-success sal-alert"><i class="mdi mdi-check-circle-outline mr-2"></i><?= h($this->session->flashdata('appointment_success')); ?></div>
            <?php endif; ?>
            <?php if ($this->session->flashdata('appointment_error')) : ?>
                <div class="alert alert-danger sal-alert"><i class="mdi mdi-alert-circle-outline mr-2"></i><?= h($this->session->flashdata('appointment_error')); ?></div>
            <?php endif; ?>

            <div class="row">
                <div class="col-12">
                    <div class="sal-sheet">
                        <?php if (empty($schedule)) : ?>
                            <div class="sal-empty">
                                <i class="mdi mdi-table-large"></i>
                                <b>No salary schedule yet</b>
                                Upload the NBC/DBM Monthly Salary Schedule, or start a blank one and type the amounts.
                                <div class="sal-head-actions">
                                    <button type="button" class="btn sal-btn sal-btn-light sal-new"><i class="mdi mdi-plus"></i>New Schedule</button>
                                    <button type="button" class="btn sal-btn sal-btn-primary" data-toggle="modal" data-target="#sal-upload-modal"><i class="mdi mdi-upload"></i>Upload Schedule</button>
                                </div>
                            </div>
                        <?php else : ?>
                            <div class="sal-meta">
                                <div class="sal-meta-main">
                                    <div class="sal-title-row">
                                        <input type="text" class="sal-title-input" id="sal-title" maxlength="150" value="<?= h($schedule->title); ?>" data-orig="<?= h($schedule->title); ?>" title="Click to rename" aria-label="Schedule title">
                                        <span class="sal-badge <?= $isActive ? 'sal-badge-active' : 'sal-badge-off'; ?>"><i class="mdi <?= $isActive ? 'mdi-check-circle' : 'mdi-circle-outline'; ?>"></i><?= $isActive ? 'Active' : 'Inactive'; ?></span>
                                    </div>
                                    <div class="sal-sub">
                                        <span><i class="mdi mdi-calendar-outline"></i> Effective <input type="date" class="sal-date-input" id="sal-effectivity" value="<?= h($schedule->effectivity_date ?? ''); ?>" aria-label="Effectivity date"></span>
                                        <span><i class="mdi mdi-table"></i> <b id="sal-grade-count"><?= count($rates); ?></b> grades · <b id="sal-rate-count"><?= $rateCount; ?></b> rates</span>
                                        <?php if (!empty($schedule->source_name)) : ?><span><i class="mdi mdi-paperclip"></i> <?= h($schedule->source_name); ?></span><?php endif; ?>
                                    </div>
                                </div>
                                <div class="sal-meta-actions">
                                    <?php if (count($schedules) > 1) : ?>
                                        <select class="sal-switch" id="sal-switch" aria-label="Switch schedule">
                                            <?php foreach ($schedules as $item) : ?>
                                                <option value="<?= (int) $item->id; ?>" <?= (int) $item->id === (int) $schedule->id ? 'selected' : ''; ?>><?= h($item->title); ?><?= (int) $item->is_active === 1 ? ' — Active' : ''; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    <?php endif; ?>
                                    <?php if (!$isActive) : ?>
                                        <?= form_open('Pages/appointment_salary_activate/' . (int) $schedule->id, ['class' => 'sal-confirm', 'data-title' => 'Set as active?', 'data-text' => 'Appointment reports will use this schedule for the monthly salary.', 'data-button' => 'Set Active']); ?>
                                            <button type="submit" class="btn sal-btn sal-btn-primary"><i class="mdi mdi-check-circle-outline"></i>Set Active</button>
                                        </form>
                                        <?= form_open('Pages/appointment_salary_delete/' . (int) $schedule->id, ['class' => 'sal-confirm', 'data-title' => 'Delete this schedule?', 'data-text' => 'This removes the schedule and all of its rates.', 'data-button' => 'Delete', 'data-danger' => '1']); ?>
                                            <button type="submit" class="btn sal-btn sal-btn-light sal-btn-danger" title="Delete schedule"><i class="mdi mdi-delete-outline"></i></button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="sal-fbar">
                                <div class="sal-namebox" id="sal-namebox">—</div>
                                <div class="sal-fx">fx</div>
                                <div class="sal-fvalue" id="sal-fvalue">Select a cell</div>
                                <div class="sal-fannual">Annual<b id="sal-fannual">—</b></div>
                                <div class="sal-status" id="sal-status"><i class="mdi mdi-cloud-check-outline"></i><span>All changes saved</span></div>
                            </div>

                            <div class="sal-grid-wrap">
                                <table class="sal-grid" id="sal-grid">
                                    <thead>
                                        <tr>
                                            <th>SG</th>
                                            <?php for ($step = 1; $step <= 8; $step++) : ?><th data-col="<?= $step; ?>">Step <?= $step; ?></th><?php endfor; ?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php for ($sg = 1; $sg <= 33; $sg++) : ?>
                                            <tr>
                                                <td class="sal-rowhead" data-row="<?= $sg; ?>"><?= $sg; ?></td>
                                                <?php for ($step = 1; $step <= 8; $step++) :
                                                    $amount = $rates[$sg][$step] ?? null;
                                                    $display = $amount !== null ? number_format($amount, 2) : '';
                                                ?>
                                                    <td><input type="text" class="sal-cell" inputmode="decimal" autocomplete="off" spellcheck="false" data-sg="<?= $sg; ?>" data-step="<?= $step; ?>" data-orig="<?= $display; ?>" value="<?= $display; ?>" aria-label="SG <?= $sg; ?> Step <?= $step; ?>"></td>
                                                <?php endfor; ?>
                                            </tr>
                                        <?php endfor; ?>
                                    </tbody>
                                </table>
                            </div>

                            <div class="sal-hint">
                                <span><i class="mdi mdi-cursor-default-click-outline"></i> Click a cell and type — changes save automatically</span>
                                <span><kbd>Enter</kbd>next row</span>
                                <span><kbd>Tab</kbd>next step</span>
                                <span><kbd>↑</kbd><kbd>↓</kbd><kbd>←</kbd><kbd>→</kbd>move</span>
                                <span><kbd>Esc</kbd>undo cell</span>
                                <span><kbd>Del</kbd>clear</span>
                                <span><kbd>Ctrl/⌘ V</kbd>paste rows from Excel</span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<?= form_open('Pages/appointment_salary_save', ['id' => 'sal-new-form', 'style' => 'display:none;']); ?>
    <input type="hidden" name="schedule_id" value="0">
    <input type="hidden" name="salary_title" id="sal-new-title">
</form>

<div class="modal fade sal-modal" id="sal-upload-modal" tabindex="-1" role="dialog" aria-labelledby="sal-upload-title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <?= form_open_multipart('Pages/appointment_salary_upload', ['id' => 'sal-upload-form']); ?>
                <div class="modal-header">
                    <h5 class="modal-title" id="sal-upload-title">Upload Salary Schedule</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="sal-drop" id="sal-drop">
                        <input type="file" id="sal-file" name="salary_file" accept=".xls,.xlsx" hidden>
                        <i class="mdi mdi-file-excel-outline"></i>
                        <div><b>Drop the Excel file here</b> or <span class="sal-drop-link">browse</span></div>
                        <small>NBC/DBM Monthly Salary Schedule format · .xls or .xlsx</small>
                        <div class="sal-drop-file" id="sal-drop-file"></div>
                    </div>
                    <div class="form-group mt-3 mb-2">
                        <label for="sal-upload-title-input">Title <span class="text-muted text-lowercase font-weight-normal">(optional)</span></label>
                        <input type="text" class="form-control" id="sal-upload-title-input" name="salary_title" maxlength="150" placeholder="Taken from the file if left blank">
                    </div>
                    <div class="form-group mb-0">
                        <label for="sal-upload-effectivity">Effectivity Date <span class="text-muted text-lowercase font-weight-normal">(optional)</span></label>
                        <input type="date" class="form-control" id="sal-upload-effectivity" name="effectivity_date">
                    </div>
                    <small class="d-block text-muted mt-3"><i class="mdi mdi-information-outline"></i> The uploaded schedule becomes the active one. Earlier schedules are kept.</small>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn sal-btn sal-btn-light" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn sal-btn sal-btn-primary"><i class="mdi mdi-upload"></i>Upload &amp; Activate</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var scheduleId = <?= !empty($schedule) ? (int) $schedule->id : 0; ?>;
    var cellsUrl = '<?= base_url('Pages/appointment_salary_cells'); ?>';
    var detailsUrl = '<?= base_url('Pages/appointment_salary_details'); ?>';

    var clean = function (v) { return String(v || '').replace(/[,\s₱]/g, ''); };
    var money = function (n) { return Number(n).toLocaleString('en-US', { minimumFractionDigits:2, maximumFractionDigits:2 }); };
    // Normalises typed text: '' when blank, formatted amount when valid, null when invalid.
    function normalise(v) {
        var raw = clean(v);
        if (raw === '') { return ''; }
        if (!/^\d+(\.\d+)?$/.test(raw) || parseFloat(raw) <= 0) { return null; }
        return money(parseFloat(raw));
    }

    // ---- Save status ------------------------------------------------------
    var pending = 0;
    var $status = $('#sal-status');
    function status(state, text) {
        var icons = { saved:'mdi-cloud-check-outline', saving:'mdi-loading mdi-spin', error:'mdi-alert-circle-outline' };
        $status.removeClass('is-saving is-error').addClass(state === 'saved' ? '' : 'is-' + state)
            .html('<i class="mdi ' + icons[state] + '"></i><span>' + text + '</span>');
    }
    function toast(message) {
        Swal.fire({ toast:true, position:'top-end', type:'error', icon:'error', title:message, showConfirmButton:false, timer:3500 });
    }

    // ---- Grid -------------------------------------------------------------
    var $grid = $('#sal-grid');
    function cellAt(sg, step) { return $grid.find('.sal-cell[data-sg="' + sg + '"][data-step="' + step + '"]'); }

    function refreshCounts() {
        var grades = {}, rates = 0;
        $grid.find('.sal-cell').each(function () {
            if (this.value !== '') { rates++; grades[this.dataset.sg] = true; }
        });
        $('#sal-rate-count').text(rates);
        $('#sal-grade-count').text(Object.keys(grades).length);
    }

    function showFormula(cell) {
        var amount = clean(cell.value);
        $('#sal-namebox').text('SG ' + cell.dataset.sg + ' · Step ' + cell.dataset.step);
        if (amount === '' || isNaN(parseFloat(amount))) {
            $('#sal-fvalue').text('—');
            $('#sal-fannual').text('—');
        } else {
            $('#sal-fvalue').text('₱ ' + money(parseFloat(amount)));
            $('#sal-fannual').text('₱ ' + money(parseFloat(amount) * 12));
        }
    }

    function save(cells) {
        pending++;
        status('saving', 'Saving…');
        var data = { schedule_id: scheduleId };
        cells.forEach(function (c, i) {
            data['cells[' + i + '][sg]'] = c.el.dataset.sg;
            data['cells[' + i + '][step]'] = c.el.dataset.step;
            data['cells[' + i + '][amount]'] = clean(c.value);
            $(c.el).addClass('is-saving').removeClass('is-error');
        });
        $.post(cellsUrl, data, null, 'json').done(function (res) {
            if (!res || res.status !== 'success') { return fail(res && res.message); }
            cells.forEach(function (c) {
                c.el.dataset.orig = c.value;
                $(c.el).removeClass('is-saving is-saved');
                void c.el.offsetWidth;
                $(c.el).addClass('is-saved');
            });
            if (--pending === 0) { status('saved', 'All changes saved'); }
        }).fail(function () { fail(); });

        function fail(message) {
            cells.forEach(function (c) { $(c.el).removeClass('is-saving').addClass('is-error'); });
            pending = Math.max(0, pending - 1);
            status('error', 'Not saved — try again');
            toast(message || 'The amount could not be saved.');
        }
    }

    // Commits one cell when it loses focus or Enter is pressed.
    function commit(el) {
        var value = normalise(el.value);
        if (value === null) {
            toast('Enter a valid amount, e.g. 31705 or 31,705.00');
            el.value = el.dataset.orig;
            return;
        }
        el.value = value;
        if (value !== el.dataset.orig) {
            save([{ el: el, value: value }]);
            refreshCounts();
        }
    }

    function move(el, dRow, dCol) {
        var sg = parseInt(el.dataset.sg, 10) + dRow;
        var step = parseInt(el.dataset.step, 10) + dCol;
        var $next = cellAt(sg, step);
        if ($next.length) { $next.trigger('focus'); }
        return $next.length > 0;
    }

    $grid.on('focus', '.sal-cell', function () {
        var el = this;
        $grid.find('.is-hl').removeClass('is-hl');
        $grid.find('th[data-col="' + el.dataset.step + '"], td.sal-rowhead[data-row="' + el.dataset.sg + '"]').addClass('is-hl');
        $(el).removeClass('is-error');
        el.dataset.typed = '';
        showFormula(el);
        setTimeout(function () { el.select(); }, 0);
    });
    $grid.on('blur', '.sal-cell', function () { commit(this); });
    $grid.on('input', '.sal-cell', function () { this.dataset.typed = '1'; showFormula(this); });
    $grid.on('keydown', '.sal-cell', function (e) {
        var el = this;
        var whole = el.selectionStart === 0 && el.selectionEnd === el.value.length;
        switch (e.key) {
            case 'Enter':
                e.preventDefault();
                // Moving focus commits the cell on blur; on the last row commit here.
                if (!move(el, e.shiftKey ? -1 : 1, 0)) { commit(el); }
                break;
            case 'ArrowDown': e.preventDefault(); move(el, 1, 0); break;
            case 'ArrowUp': e.preventDefault(); move(el, -1, 0); break;
            case 'ArrowLeft':
                if (whole || !el.dataset.typed || el.selectionStart === 0) { e.preventDefault(); move(el, 0, -1); }
                break;
            case 'ArrowRight':
                if (whole || !el.dataset.typed || el.selectionEnd === el.value.length) { e.preventDefault(); move(el, 0, 1); }
                break;
            case 'Escape':
                e.preventDefault();
                el.value = el.dataset.orig;
                el.dataset.typed = '';
                showFormula(el);
                el.select();
                break;
        }
    });

    // Pasting a block copied from Excel fills the grid from the selected cell.
    $grid.on('paste', '.sal-cell', function (e) {
        var text = (e.originalEvent.clipboardData || window.clipboardData).getData('text');
        if (!/[\t\n]/.test(text.trim())) { return; }
        e.preventDefault();
        var startSg = parseInt(this.dataset.sg, 10), startStep = parseInt(this.dataset.step, 10);
        var changed = [], skipped = 0;
        text.replace(/\r/g, '').replace(/\n$/, '').split('\n').forEach(function (line, r) {
            line.split('\t').forEach(function (val, c) {
                var $cell = cellAt(startSg + r, startStep + c);
                if (!$cell.length) { return; }
                var value = normalise(val);
                if (value === null) { skipped++; return; }
                $cell.val(value);
                if (value !== $cell[0].dataset.orig) { changed.push({ el: $cell[0], value: value }); }
            });
        });
        if (changed.length) { save(changed); refreshCounts(); }
        if (skipped) { toast(skipped + ' pasted value(s) were not amounts and were skipped.'); }
        showFormula(this);
    });

    // ---- Title / effectivity (autosave) -------------------------------------
    function saveDetails() {
        var $title = $('#sal-title');
        var title = $.trim($title.val());
        if (title === '') { $title.val($title.data('orig')); toast('The schedule title cannot be blank.'); return; }
        pending++;
        status('saving', 'Saving…');
        $.post(detailsUrl, { schedule_id: scheduleId, salary_title: title, effectivity_date: $('#sal-effectivity').val() }, null, 'json')
            .done(function (res) {
                if (!res || res.status !== 'success') { pending = Math.max(0, pending - 1); status('error', 'Not saved'); toast(res && res.message); return; }
                $title.data('orig', title);
                $('#sal-switch option[value="' + scheduleId + '"]').text(title + ($('#sal-switch option[value="' + scheduleId + '"]').text().indexOf('— Active') > -1 ? ' — Active' : ''));
                if (--pending === 0) { status('saved', 'All changes saved'); }
            })
            .fail(function () { pending = Math.max(0, pending - 1); status('error', 'Not saved'); toast('The details could not be saved.'); });
    }
    $('#sal-title').on('change', saveDetails).on('keydown', function (e) {
        if (e.key === 'Enter') { e.preventDefault(); this.blur(); }
        if (e.key === 'Escape') { this.value = $(this).data('orig'); this.blur(); }
    });
    $('#sal-effectivity').on('change', saveDetails);

    window.addEventListener('beforeunload', function (e) {
        if (pending > 0) { e.preventDefault(); e.returnValue = ''; }
    });

    // ---- Schedules ----------------------------------------------------------
    $('#sal-switch').on('change', function () {
        window.location = '<?= $pageUrl; ?>?schedule=' + encodeURIComponent(this.value);
    });

    $('.sal-new').on('click', function () {
        Swal.fire({
            title: 'New salary schedule',
            input: 'text',
            inputPlaceholder: 'e.g. NBC 601, s. 2026',
            showCancelButton: true,
            confirmButtonText: 'Create',
            confirmButtonColor: '#12a37f',
            inputValidator: function (value) { return !$.trim(value) && 'Enter a title for the schedule.'; }
        }).then(function (result) {
            if (result.value) {
                $('#sal-new-title').val($.trim(result.value));
                $('#sal-new-form')[0].submit();
            }
        });
    });

    $('form.sal-confirm').on('submit', function (e) {
        var form = this;
        if (form.dataset.confirmed) { return; }
        e.preventDefault();
        Swal.fire({
            type: form.dataset.danger ? 'warning' : 'question',
            title: form.dataset.title,
            text: form.dataset.text,
            showCancelButton: true,
            confirmButtonText: form.dataset.button,
            confirmButtonColor: form.dataset.danger ? '#d9534f' : '#12a37f'
        }).then(function (result) {
            if (result.value) {
                form.dataset.confirmed = '1';
                form.submit();
            }
        });
    });

    // ---- Upload drop zone ---------------------------------------------------
    var $drop = $('#sal-drop'), $input = $('#sal-file'), $chip = $('#sal-drop-file');
    function pick(file, fromDrop) {
        var ext = file.name.split('.').pop().toLowerCase();
        if (['xls', 'xlsx'].indexOf(ext) === -1 || file.size > 10 * 1024 * 1024) {
            $input.val('');
            $chip.hide().text('');
            Swal.fire({ type:'error', title:'Invalid file', text:'Use an Excel (.xls/.xlsx) file smaller than 10 MB.' });
            return;
        }
        if (fromDrop) {
            try {
                var dt = new DataTransfer();
                dt.items.add(file);
                $input[0].files = dt.files;
            } catch (err) {
                Swal.fire({ type:'error', title:'Invalid file', text:'Please use "browse" instead.' });
                return;
            }
        }
        $chip.text(file.name).show();
    }
    $drop.on('click', function (e) { if (e.target !== $input[0]) { $input[0].click(); } });
    $input.on('change', function () { if (this.files.length) { pick(this.files[0], false); } });
    $drop.on('dragover dragenter', function (e) { e.preventDefault(); $drop.addClass('sal-drop-over'); });
    $drop.on('dragleave dragend drop', function (e) { e.preventDefault(); $drop.removeClass('sal-drop-over'); });
    $drop.on('drop', function (e) {
        var files = e.originalEvent.dataTransfer.files;
        if (files.length) { pick(files[0], true); }
    });
    $('#sal-upload-form').on('submit', function (e) {
        if (!$input[0].files.length) {
            e.preventDefault();
            Swal.fire({ type:'warning', title:'No file selected', text:'Choose the salary schedule Excel file.' });
        }
    });
});
</script>
