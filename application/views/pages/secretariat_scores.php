<?php
$score_h = static function ($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
};
$score_is_encoded = static function ($value) {
    return $value !== null && abs((float) $value - 0.00001) > 0.000001;
};

$vacancies = $vacancies ?? [];
$applicants = $applicants ?? [];
$selectedJobId = (int) ($selectedJobId ?? 0);
$selectedVacancy = $selectedVacancy ?? null;
$scoreCounts = $scoreCounts ?? [];
$encodingMode = in_array(($encodingMode ?? 'both'), ['written', 'interview', 'both'], true) ? $encodingMode : 'both';
$showInterview = in_array($encodingMode, ['interview', 'both'], true);
$showWritten = in_array($encodingMode, ['written', 'both'], true);
$selectedCounts = $scoreCounts[$selectedJobId] ?? ['total' => count($applicants), 'interview' => 0, 'written' => 0, 'complete' => 0];
$encodedForMode = $encodingMode === 'interview'
    ? (int) $selectedCounts['interview']
    : ($encodingMode === 'written' ? (int) $selectedCounts['written'] : (int) $selectedCounts['complete']);
$modeLabels = ['written' => 'Written only', 'interview' => 'Interview only', 'both' => 'Both scores'];
$successMessage = $this->session->flashdata('success');
$dangerMessage = $this->session->flashdata('danger');
?>

<style>
    .score-workspace { --sw-ink:#173252; --sw-muted:#6b7b91; --sw-line:#dfe6ef; --sw-blue:#2457d6; --sw-soft:#f5f8fc; }
    .score-workspace .sw-top { align-items:center; display:flex; justify-content:space-between; margin-bottom:12px; }
    .score-workspace .sw-title { color:var(--sw-ink); font-size:21px; font-weight:800; margin:0; }
    .score-workspace .sw-subtitle { color:var(--sw-muted); font-size:12px; margin:3px 0 0; }
    .score-workspace .sw-card { border:1px solid var(--sw-line); border-radius:11px; box-shadow:0 3px 13px rgba(31,58,91,.04); }
    .score-workspace .sw-toolbar { align-items:end; display:grid; gap:12px; grid-template-columns:minmax(260px,1.4fr) auto minmax(220px,.8fr); }
    .score-workspace .sw-label { color:#617189; display:block; font-size:10px; font-weight:750; letter-spacing:.05em; margin-bottom:5px; text-transform:uppercase; }
    .score-workspace .sw-mode { display:flex; }
    .score-workspace .sw-mode .btn { border-color:#cfd8e5; border-radius:0; color:#4e617a; font-size:12px; white-space:nowrap; }
    .score-workspace .sw-mode .btn:first-child { border-radius:7px 0 0 7px; }
    .score-workspace .sw-mode .btn:last-child { border-radius:0 7px 7px 0; }
    .score-workspace .sw-mode .btn.active { background:var(--sw-blue); border-color:var(--sw-blue); color:#fff; }
    .score-workspace .sw-search { position:relative; }
    .score-workspace .sw-search i { color:#8794a6; left:11px; position:absolute; top:10px; }
    .score-workspace .sw-search input { padding-left:34px; }
    .score-workspace .sw-info { align-items:center; background:#f8fafc; border-bottom:1px solid var(--sw-line); display:flex; flex-wrap:wrap; gap:8px 18px; justify-content:space-between; padding:10px 14px; }
    .score-workspace .sw-job { color:var(--sw-ink); font-weight:800; }
    .score-workspace .sw-job small { color:var(--sw-muted); font-weight:400; margin-left:6px; }
    .score-workspace .sw-progress { color:#53677f; font-size:12px; }
    .score-workspace .sw-progress strong { color:var(--sw-ink); }
    .score-workspace .sw-live { align-items:center; color:#278457; display:inline-flex; font-size:11px; gap:5px; }
    .score-workspace .sw-live:before { background:#35aa70; border-radius:50%; box-shadow:0 0 0 3px rgba(53,170,112,.14); content:""; height:7px; width:7px; }
    .score-workspace .sw-table-wrap { max-height:calc(100vh - 290px); min-height:330px; overflow:auto; }
    .score-workspace .sw-table { margin:0; min-width:760px; }
    .score-workspace .sw-table.sw-table-both { min-width:880px; }
    .score-workspace .sw-table thead th { background:#f3f6fa; border-bottom:1px solid #dce4ee; border-top:0; color:#64758b; font-size:10px; letter-spacing:.055em; padding:9px 11px; position:sticky; text-transform:uppercase; top:0; z-index:2; }
    .score-workspace .sw-table td { border-color:#edf1f6; padding:8px 11px; vertical-align:middle; }
    .score-workspace .sw-table tbody tr:hover { background:#fafcff; }
    .score-workspace .sw-row-number { color:#9aa7b7; font-size:11px; text-align:center; width:46px; }
    .score-workspace .sw-name { color:var(--sw-ink); font-size:13px; font-weight:750; }
    .score-workspace .sw-meta { color:var(--sw-muted); font-size:10px; margin-top:2px; }
    .score-workspace .sw-dq { color:#b34545; font-size:10px; line-height:1.25; margin-top:3px; max-width:310px; }
    .score-workspace .sw-score-cell { text-align:center; width:135px; }
    .score-workspace .sw-score-input { border:1px solid #cbd5e2; border-radius:7px; color:var(--sw-ink); font-size:16px; font-weight:750; height:38px; margin:auto; padding:5px 7px; text-align:center; width:92px; }
    .score-workspace .sw-score-input:focus { border-color:var(--sw-blue); box-shadow:0 0 0 3px rgba(36,87,214,.12); }
    .score-workspace .sw-score-input.is-invalid { background-image:none; border-color:#dc4c4c; padding-right:7px; }
    .score-workspace .sw-score-max { color:#9aa7b7; font-size:9px; margin-top:2px; }
    .score-workspace .sw-save-state { color:#8290a3; font-size:10px; min-width:92px; white-space:nowrap; }
    .score-workspace .sw-save-state i { font-size:15px; margin-right:3px; vertical-align:-2px; }
    .score-workspace .sw-save-state.saving { color:#a66a00; }
    .score-workspace .sw-save-state.saved { color:#238052; }
    .score-workspace .sw-save-state.error { color:#c34444; white-space:normal; }
    .score-workspace .sw-ma-link { color:#5f7086; font-size:18px; }
    .score-workspace .sw-empty { color:var(--sw-muted); padding:48px 20px; text-align:center; }
    .score-workspace .sw-foot { align-items:center; background:#fff; border-top:1px solid var(--sw-line); color:var(--sw-muted); display:flex; flex-wrap:wrap; font-size:10px; gap:8px 18px; justify-content:space-between; padding:8px 13px; }
    @media (max-width:1050px) { .score-workspace .sw-toolbar { grid-template-columns:1fr 1fr; } .score-workspace .sw-search { grid-column:1/-1; } }
    @media (max-width:680px) { .score-workspace .sw-top { align-items:flex-start; flex-direction:column; gap:8px; } .score-workspace .sw-toolbar { grid-template-columns:1fr; } .score-workspace .sw-search { grid-column:auto; } .score-workspace .sw-mode { width:100%; } .score-workspace .sw-mode .btn { flex:1; padding-left:5px; padding-right:5px; } .score-workspace .sw-table-wrap { max-height:none; } }
</style>

<div class="content-page score-workspace">
    <div class="content">
        <div class="container-fluid">
            <div class="sw-top">
                <div>
                    <h2 class="sw-title">Score Encoding</h2>
                    <p class="sw-subtitle">Fast entry for Interview and Written Examination scores.</p>
                </div>
                <a href="<?= base_url(); ?>" class="btn btn-outline-secondary btn-sm"><i class="mdi mdi-arrow-left mr-1"></i>Dashboard</a>
            </div>

            <?php if ($successMessage) : ?><div class="alert alert-success py-2"><?= $score_h($successMessage); ?></div><?php endif; ?>
            <?php if ($dangerMessage) : ?><div class="alert alert-danger py-2"><?= $score_h($dangerMessage); ?></div><?php endif; ?>

            <div class="card sw-card mb-2">
                <div class="card-body py-3">
                    <form method="get" action="<?= base_url('secretariat/scores'); ?>" class="sw-toolbar" id="score-toolbar-form">
                        <div>
                            <label class="sw-label" for="score-vacancy">Vacancy</label>
                            <select class="form-control form-control-sm" name="job_id" id="score-vacancy">
                                <option value="">Select an assigned vacancy</option>
                                <?php foreach ($vacancies as $vacancy) : ?>
                                    <option value="<?= (int) $vacancy->jobID; ?>" <?= (int) $vacancy->jobID === $selectedJobId ? 'selected' : ''; ?>>
                                        <?= $score_h($vacancy->jobTitle); ?> — FY <?= $score_h($vacancy->sy); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <span class="sw-label">Encode</span>
                            <div class="sw-mode" role="group" aria-label="Score fields to encode">
                                <?php foreach ($modeLabels as $modeValue => $modeLabel) : ?>
                                    <button type="submit" name="mode" value="<?= $modeValue; ?>" class="btn btn-sm <?= $encodingMode === $modeValue ? 'active' : ''; ?>"><?= $modeLabel; ?></button>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <div class="sw-search">
                            <label class="sw-label" for="score-applicant-search">Find applicant</label>
                            <i class="mdi mdi-magnify"></i>
                            <input type="search" id="score-applicant-search" class="form-control form-control-sm" placeholder="Name, ID, status, reason" autocomplete="off">
                        </div>
                    </form>
                </div>
            </div>

            <?php if (empty($vacancies)) : ?>
                <div class="alert alert-warning">No open score-eligible vacancy is assigned to your Secretariat account.</div>
            <?php elseif (empty($selectedVacancy)) : ?>
                <div class="card sw-card"><div class="sw-empty"><i class="mdi mdi-format-list-numbered" style="font-size:38px"></i><h5 class="mt-2">Select a vacancy to start</h5></div></div>
            <?php else : ?>
                <div class="card sw-card">
                    <div class="sw-info">
                        <div class="sw-job">
                            <?= $score_h($selectedVacancy->jobTitle); ?>
                            <small>Job #<?= (int) $selectedVacancy->jobID; ?> &middot; <?= $score_h($modeLabels[$encodingMode]); ?></small>
                        </div>
                        <div class="d-flex align-items-center flex-wrap" style="gap:10px 20px">
                            <span class="sw-progress"><strong id="mode-encoded-count"><?= $encodedForMode; ?></strong> of <strong><?= (int) $selectedCounts['total']; ?></strong> encoded</span>
                            <span class="sw-live">Autosave active</span>
                        </div>
                    </div>

                    <?php if (empty($applicants)) : ?>
                        <div class="sw-empty">No applicants found for this vacancy.</div>
                    <?php else : ?>
                        <div class="sw-table-wrap">
                            <table class="table sw-table <?= $encodingMode === 'both' ? 'sw-table-both' : ''; ?>" id="score-applicant-table">
                                <thead>
                                    <tr>
                                        <th class="text-center">#</th>
                                        <th>Applicant</th>
                                        <th>Status</th>
                                        <?php if ($showWritten) : ?><th class="text-center">Written Examination</th><?php endif; ?>
                                        <?php if ($showInterview) : ?><th class="text-center">Interview</th><?php endif; ?>
                                        <th>Save status</th>
                                        <th class="text-center">MA</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($applicants as $index => $applicant) : ?>
                                        <?php
                                        $name = trim(implode(' ', array_filter([
                                            trim((string) $applicant->FirstName),
                                            trim((string) $applicant->MiddleName),
                                            trim((string) $applicant->LastName),
                                            trim((string) $applicant->NameExtn),
                                        ])));
                                        if ($name === '') {
                                            $name = 'Applicant ' . $applicant->applicant_id;
                                        }
                                        $interviewEncoded = $score_is_encoded($applicant->interview ?? null);
                                        $writtenEncoded = $score_is_encoded($applicant->written ?? null);
                                        $modeComplete = ($encodingMode === 'interview' && $interviewEncoded)
                                            || ($encodingMode === 'written' && $writtenEncoded)
                                            || ($encodingMode === 'both' && $interviewEncoded && $writtenEncoded);
                                        $searchText = strtolower($name . ' ' . $applicant->applicant_id . ' ' . $applicant->record_no . ' ' . $applicant->appStatus . ' ' . ($applicant->dq_reason ?? ''));
                                        $formId = 'score-form-' . (int) $applicant->appID;
                                        $profileUrl = '';
                                        if (!empty($applicant->profile_route) && !empty($applicant->profile_id)) {
                                            $profileUrl = base_url(
                                                'Pages/' . $applicant->profile_route . '/'
                                                . rawurlencode((string) $applicant->profile_id) . '/'
                                                . (int) $applicant->jobID . '/'
                                                . rawurlencode((string) ($applicant->pre_school ?: 0)) . '/'
                                                . (int) $applicant->appID
                                            );
                                        }
                                        ?>
                                        <tr data-score-search="<?= $score_h($searchText); ?>" data-mode-complete="<?= $modeComplete ? '1' : '0'; ?>">
                                            <td class="sw-row-number"><?= $index + 1; ?></td>
                                            <td>
                                                <form method="post" action="<?= base_url('secretariat/scores/save'); ?>" id="<?= $formId; ?>" class="score-auto-form">
                                                    <input type="hidden" name="app_id" value="<?= (int) $applicant->appID; ?>">
                                                    <input type="hidden" name="job_id" value="<?= (int) $applicant->jobID; ?>">
                                                    <input type="hidden" name="mode" value="<?= $score_h($encodingMode); ?>">
                                                </form>
                                                <div class="sw-name"><?= $score_h($name); ?></div>
                                                <div class="sw-meta">ID <?= $score_h($applicant->applicant_id); ?> &middot; App #<?= (int) $applicant->appID; ?> &middot; <?= $score_h($applicant->record_no); ?></div>
                                            </td>
                                            <td>
                                                <span class="badge <?= (int) $applicant->dq === 2 ? 'badge-danger' : 'badge-light'; ?>"><?= $score_h($applicant->appStatus ?: 'No status'); ?></span>
                                                <?php if ((int) $applicant->dq === 2) : ?>
                                                    <div class="sw-dq"><strong>DQ</strong><?= !empty($applicant->dq_reason) ? ': ' . $score_h($applicant->dq_reason) : ''; ?></div>
                                                <?php endif; ?>
                                            </td>
                                            <?php if ($showWritten) : ?>
                                                <td class="sw-score-cell">
                                                    <input form="<?= $formId; ?>" type="number" inputmode="decimal" min="0" max="20" step="0.01" name="written" data-field="written" data-last-saved="<?= $writtenEncoded ? $score_h((float) $applicant->written) : ''; ?>" data-counted="<?= $writtenEncoded ? '1' : '0'; ?>" class="form-control sw-score-input" value="<?= $writtenEncoded ? $score_h((float) $applicant->written) : ''; ?>" aria-label="Written Examination score for <?= $score_h($name); ?>">
                                                    <div class="sw-score-max">out of 20</div>
                                                </td>
                                            <?php endif; ?>
                                            <?php if ($showInterview) : ?>
                                                <td class="sw-score-cell">
                                                    <input form="<?= $formId; ?>" type="number" inputmode="decimal" min="0" max="20" step="0.01" name="interview" data-field="interview" data-last-saved="<?= $interviewEncoded ? $score_h((float) $applicant->interview) : ''; ?>" data-counted="<?= $interviewEncoded ? '1' : '0'; ?>" class="form-control sw-score-input" value="<?= $interviewEncoded ? $score_h((float) $applicant->interview) : ''; ?>" aria-label="Interview score for <?= $score_h($name); ?>">
                                                    <div class="sw-score-max">out of 20</div>
                                                </td>
                                            <?php endif; ?>
                                            <td><span class="sw-save-state <?= $modeComplete ? 'saved' : ''; ?>"><i class="mdi <?= $modeComplete ? 'mdi-check-circle-outline' : 'mdi-circle-edit-outline'; ?>"></i><span><?= $modeComplete ? 'Saved' : 'Ready'; ?></span></span></td>
                                            <td class="text-center">
                                                <?php if ($profileUrl !== '') : ?><a href="<?= $score_h($profileUrl); ?>" class="sw-ma-link" target="_blank" rel="noopener" title="Open MA page"><i class="mdi mdi-open-in-new"></i></a><?php else : ?><span class="text-muted">—</span><?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="sw-foot">
                            <span><strong><i class="mdi mdi-keyboard-return"></i> Enter</strong> saves now and moves to the next applicant in the same score column.</span>
                            <span><span id="score-visible-count"><?= count($applicants); ?></span> applicant<?= count($applicants) === 1 ? '' : 's'; ?> shown &middot; changes also save after a short pause</span>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
(function () {
    var toolbar = document.getElementById('score-toolbar-form');
    var vacancy = document.getElementById('score-vacancy');
    var search = document.getElementById('score-applicant-search');
    var table = document.getElementById('score-applicant-table');
    var visibleCount = document.getElementById('score-visible-count');
    var encodedCount = document.getElementById('mode-encoded-count');
    var formStates = new WeakMap();

    if (vacancy && toolbar) {
        vacancy.addEventListener('change', function () {
            var activeMode = toolbar.querySelector('.sw-mode .active');
            if (activeMode) {
                var hiddenMode = document.createElement('input');
                hiddenMode.type = 'hidden';
                hiddenMode.name = 'mode';
                hiddenMode.value = activeMode.value;
                toolbar.appendChild(hiddenMode);
            }
            toolbar.submit();
        });
    }

    if (!table) return;

    function stateFor(form) {
        if (!formStates.has(form)) {
            formStates.set(form, { timer: null, saving: false, queued: false });
        }
        return formStates.get(form);
    }

    function normalized(value) {
        value = String(value == null ? '' : value).trim();
        if (value === '') return '';
        var number = Number(value);
        return Number.isFinite(number) ? String(number) : value;
    }

    function statusFor(form) {
        return form.closest('tr').querySelector('.sw-save-state');
    }

    function setStatus(form, type, message) {
        var status = statusFor(form);
        if (!status) return;
        status.className = 'sw-save-state' + (type ? ' ' + type : '');
        var icon = type === 'saved' ? 'mdi-check-circle-outline'
            : (type === 'saving' ? 'mdi-loading mdi-spin' : (type === 'error' ? 'mdi-alert-circle-outline' : 'mdi-circle-edit-outline'));
        status.querySelector('i').className = 'mdi ' + icon;
        status.querySelector('span').textContent = message;
    }

    function dirtyInputs(form) {
        return Array.prototype.filter.call(form.closest('tr').querySelectorAll('.sw-score-input'), function (input) {
            return input.value.trim() !== '' && normalized(input.value) !== normalized(input.dataset.lastSaved || '');
        });
    }

    function validate(input) {
        var empty = input.value.trim() === '';
        var valid = empty || (input.checkValidity() && Number(input.value) >= 0 && Number(input.value) <= 20);
        input.classList.toggle('is-invalid', !valid);
        return valid;
    }

    function refreshModeCount(form) {
        var row = form.closest('tr');
        var allCounted = Array.prototype.every.call(row.querySelectorAll('.sw-score-input'), function (input) {
            return input.dataset.counted === '1';
        });
        if (allCounted && row.dataset.modeComplete !== '1') {
            row.dataset.modeComplete = '1';
            if (encodedCount) encodedCount.textContent = String(Number(encodedCount.textContent || 0) + 1);
        }
    }

    function saveForm(form) {
        var state = stateFor(form);
        if (state.timer) {
            clearTimeout(state.timer);
            state.timer = null;
        }
        if (state.saving) {
            state.queued = true;
            return Promise.resolve();
        }

        var inputs = dirtyInputs(form);
        if (!inputs.length) return Promise.resolve();
        if (!inputs.every(validate)) {
            setStatus(form, 'error', 'Use 0–20');
            return Promise.resolve();
        }

        var data = new FormData();
        data.append('app_id', form.querySelector('[name="app_id"]').value);
        data.append('job_id', form.querySelector('[name="job_id"]').value);
        data.append('mode', form.querySelector('[name="mode"]').value);
        var sent = {};
        inputs.forEach(function (input) {
            sent[input.name] = normalized(input.value);
            data.append(input.name, input.value);
        });

        state.saving = true;
        state.queued = false;
        var savedOk = false;
        setStatus(form, 'saving', 'Saving…');

        return fetch(form.action, {
            method: 'POST',
            body: data,
            credentials: 'same-origin',
            keepalive: true,
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        }).then(function (response) {
            return response.json().catch(function () { return {}; }).then(function (payload) {
                if (!response.ok || !payload.ok) throw new Error(payload.message || 'Save failed');
                return payload;
            });
        }).then(function (payload) {
            savedOk = true;
            Object.keys(sent).forEach(function (field) {
                var input = form.closest('tr').querySelector('.sw-score-input[name="' + field + '"]');
                if (!input) return;
                input.dataset.lastSaved = sent[field];
                input.dataset.counted = '1';
            });
            refreshModeCount(form);
            setStatus(form, 'saved', payload.saved_at ? 'Saved ' + payload.saved_at : 'Saved');
        }).catch(function (error) {
            setStatus(form, 'error', error.message || 'Save failed');
        }).finally(function () {
            state.saving = false;
            if (state.queued || (savedOk && dirtyInputs(form).length)) {
                state.queued = false;
                scheduleSave(form, 150);
            }
        });
    }

    function scheduleSave(form, delay) {
        var state = stateFor(form);
        if (state.timer) clearTimeout(state.timer);
        state.timer = setTimeout(function () { saveForm(form); }, delay == null ? 500 : delay);
    }

    function focusNext(input) {
        var field = input.dataset.field;
        var candidates = Array.prototype.filter.call(table.querySelectorAll('.sw-score-input[data-field="' + field + '"]'), function (candidate) {
            return candidate.closest('tr').style.display !== 'none';
        });
        var index = candidates.indexOf(input);
        if (index >= 0 && candidates[index + 1]) {
            candidates[index + 1].focus();
            candidates[index + 1].select();
        }
    }

    table.querySelectorAll('.score-auto-form').forEach(function (form) {
        form.addEventListener('submit', function (event) {
            event.preventDefault();
            saveForm(form);
        });
    });

    table.querySelectorAll('.sw-score-input').forEach(function (input) {
        var form = document.getElementById(input.getAttribute('form'));
        input.addEventListener('input', function () {
            if (!validate(input)) {
                setStatus(form, 'error', 'Use 0–20');
                return;
            }
            if (input.value.trim() === '') {
                setStatus(form, '', input.dataset.lastSaved ? 'Current kept' : 'Ready');
                return;
            }
            if (normalized(input.value) === normalized(input.dataset.lastSaved || '')) {
                setStatus(form, input.dataset.counted === '1' ? 'saved' : '', input.dataset.counted === '1' ? 'Saved' : 'Ready');
                return;
            }
            setStatus(form, '', 'Typing…');
            scheduleSave(form, 500);
        });
        input.addEventListener('blur', function () {
            if (input.value.trim() !== '' && validate(input)) saveForm(form);
        });
        input.addEventListener('keydown', function (event) {
            if (event.key !== 'Enter') return;
            event.preventDefault();
            if (input.value.trim() !== '' && validate(input)) saveForm(form);
            focusNext(input);
        });
    });

    if (search) {
        search.addEventListener('keydown', function (event) {
            if (event.key === 'Enter') event.preventDefault();
        });
        search.addEventListener('input', function () {
            var needle = search.value.trim().toLowerCase();
            var visible = 0;
            table.querySelectorAll('tbody tr').forEach(function (row) {
                var matches = !needle || (row.getAttribute('data-score-search') || '').indexOf(needle) !== -1;
                row.style.display = matches ? '' : 'none';
                if (matches) visible += 1;
            });
            if (visibleCount) visibleCount.textContent = visible;
        });
    }
})();
</script>
