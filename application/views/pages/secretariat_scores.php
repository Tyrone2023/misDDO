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
$activity = $activity ?? [];
$lastActions = $lastActions ?? [];
$modeOptions = $modeOptions ?? ['written', 'interview', 'both'];
$encodingMode = in_array(($encodingMode ?? 'both'), ['written', 'interview', 'both'], true) ? $encodingMode : 'both';
$showInterview = in_array($encodingMode, ['interview', 'both'], true);
$showWritten = in_array($encodingMode, ['written', 'both'], true);
$selectedCounts = $scoreCounts[$selectedJobId] ?? ['total' => count($applicants), 'interview' => 0, 'written' => 0, 'complete' => 0];
$encodedForMode = $encodingMode === 'interview'
    ? (int) $selectedCounts['interview']
    : ($encodingMode === 'written' ? (int) $selectedCounts['written'] : (int) $selectedCounts['complete']);
$modeTotal = (int) $selectedCounts['total'];
$modePercent = $modeTotal > 0 ? round(($encodedForMode / $modeTotal) * 100) : 0;
$modeLabels = ['written' => 'Written only', 'interview' => 'Interview only', 'both' => 'Both scores'];
$successMessage = $this->session->flashdata('success');
$dangerMessage = $this->session->flashdata('danger');

/** Server time is authoritative (Asia/Manila) - format it, never re-zone it. */
$score_when = static function ($value) {
    $stamp = strtotime((string) $value);
    return $stamp ? date('M j, Y g:i A', $stamp) : (string) $value;
};

/** 'Encoded ...' vs 'Edited ...' - the two things the trail records. */
$score_action_kind = static function ($description) {
    return stripos((string) $description, 'Edited') === 0 ? 'edit' : 'encode';
};

/**
 * Who last touched ONE score field, rendered under that field's own box, so a
 * row encoded by two people credits each of them where their score sits.
 */
$score_field_actor = static function ($action) use ($score_h, $score_when, $score_action_kind) {
    if (empty($action)) {
        return '<div class="sw-field-actor is-empty" data-field-actor>'
            . '<div class="sw-fa-who"><i class="mdi mdi-account-off-outline"></i><span>Not encoded</span></div>'
            . '<div class="sw-fa-when"></div></div>';
    }

    $who = trim((string) $action['name']) !== '' ? $action['name'] : $action['username'];
    $icon = $score_action_kind($action['description']) === 'edit' ? 'mdi-pencil-outline' : 'mdi-account-check-outline';

    return '<div class="sw-field-actor" data-field-actor title="' . $score_h($action['description']) . '">'
        . '<div class="sw-fa-who"><i class="mdi ' . $icon . '"></i><span>' . $score_h($who) . '</span></div>'
        . '<div class="sw-fa-when">' . $score_h($score_when($action['when'])) . '</div></div>';
};
?>

<style>
    .score-workspace { --sw-ink:#132c4a; --sw-muted:#6b7b91; --sw-line:#e5eaf2; --sw-blue:#2457d6; --sw-soft:#f6f9fd; }
    .score-workspace .sw-hero { align-items:flex-end; background:linear-gradient(135deg,#ffffff 0%,#f4f8ff 100%); border:1px solid var(--sw-line); border-radius:14px; box-shadow:0 4px 16px rgba(24,52,88,.05); display:flex; flex-wrap:wrap; gap:12px 22px; justify-content:space-between; margin-bottom:12px; padding:14px 18px; }
    .score-workspace .sw-hero-lead { flex:1 1 250px; min-width:0; }
    .score-workspace .sw-eyebrow { color:#7b8ca3; font-size:10px; font-weight:800; letter-spacing:.12em; text-transform:uppercase; }
    .score-workspace .sw-title { color:var(--sw-ink); font-size:20px; font-weight:800; letter-spacing:-.3px; margin:3px 0 0; }
    .score-workspace .sw-subtitle { color:var(--sw-muted); font-size:12px; margin:3px 0 0; }
    .score-workspace .sw-back { align-items:center; background:#fff; border:1px solid #dbe3ee; border-radius:9px; color:#3d5876; display:inline-flex; font-size:12px; font-weight:650; gap:5px; height:36px; padding:0 13px; transition:all .14s ease; }
    .score-workspace .sw-back:hover { background:#f2f6fd; border-color:#b9cbe8; color:var(--sw-blue); text-decoration:none; }

    .score-workspace .sw-card { background:#fff; border:1px solid var(--sw-line); border-radius:16px; box-shadow:0 4px 18px rgba(31,58,91,.045); overflow:hidden; }
    .score-workspace .sw-toolbar { align-items:flex-end; display:flex; flex-wrap:wrap; gap:10px 14px; padding:0; }
    .score-workspace .sw-tool-vacancy { min-width:270px; }
    .score-workspace .sw-label { color:#7285a0; display:block; font-size:10px; font-weight:800; letter-spacing:.07em; margin-bottom:6px; text-transform:uppercase; }
    .score-workspace .sw-select { background:#fff; border:1px solid #d9e1ec; border-radius:9px; color:var(--sw-ink); font-size:13px; font-weight:600; height:36px; padding:0 10px; width:100%; }
    .score-workspace .sw-select:focus { border-color:var(--sw-blue); box-shadow:0 0 0 3px rgba(36,87,214,.1); outline:none; }
    .score-workspace .sw-mode { background:#eff3f9; border-radius:10px; display:inline-flex; padding:3px; }
    .score-workspace .sw-mode .btn { background:transparent; border:0; border-radius:8px; color:#5a6d87; font-size:12px; font-weight:700; padding:6px 13px; white-space:nowrap; }
    .score-workspace .sw-mode .btn:hover { color:var(--sw-blue); }
    .score-workspace .sw-mode .btn.active { background:#fff; box-shadow:0 2px 6px rgba(24,52,88,.12); color:var(--sw-blue); }
    .score-workspace .sw-mode-locked { align-items:center; background:#eff3f9; border-radius:10px; color:#41577a; display:inline-flex; font-size:12px; font-weight:700; gap:6px; height:36px; padding:0 14px; white-space:nowrap; }
    .score-workspace .sw-mode-locked i { color:#7b8ca3; font-size:15px; }
    .score-workspace .sw-mode-locked span { color:#8b9ab0; font-size:10.5px; font-weight:600; }
    .score-workspace .sw-search { min-width:230px; position:relative; }
    .score-workspace .sw-search i { color:#93a0b2; left:11px; position:absolute; top:50%; transform:translateY(-50%); }
    .score-workspace .sw-search input { background:#fff; border:1px solid #d9e1ec; border-radius:20px; font-size:12px; height:32px; padding:0 12px 0 32px; width:100%; }
    .score-workspace .sw-search input:focus { border-color:var(--sw-blue); box-shadow:0 0 0 3px rgba(36,87,214,.1); outline:none; }

    .score-workspace .sw-info { align-items:center; background:#fbfcfe; border-bottom:1px solid var(--sw-line); display:flex; flex-wrap:wrap; gap:8px 22px; justify-content:space-between; padding:10px 16px; }
    .score-workspace .sw-job { color:var(--sw-ink); font-size:14px; font-weight:800; }
    .score-workspace .sw-job-meta { color:var(--sw-muted); font-size:11px; margin-top:2px; }
    .score-workspace .sw-meter { min-width:210px; }
    .score-workspace .sw-meter-top { align-items:baseline; color:var(--sw-muted); display:flex; font-size:11px; gap:6px; justify-content:space-between; margin-bottom:5px; }
    .score-workspace .sw-meter-top strong { color:var(--sw-ink); font-size:12px; }
    .score-workspace .sw-meter-bar { background:#e8eef6; border-radius:8px; height:7px; overflow:hidden; }
    .score-workspace .sw-meter-bar span { background:linear-gradient(90deg,#2e9e6b,#4ecb92); border-radius:8px; display:block; height:100%; transition:width .35s ease; }
    .score-workspace .sw-autosave { align-items:center; background:#eaf7f0; border-radius:20px; color:#1f7a51; display:inline-flex; font-size:11px; font-weight:700; gap:6px; padding:7px 13px; transition:all .2s ease; }
    .score-workspace .sw-autosave i { font-size:14px; }
    .score-workspace .sw-autosave.is-pending { background:#fff5e2; color:#96650c; }
    .score-workspace .sw-autosave.is-error { background:#fdeceb; color:#b8443c; }

    .score-workspace .sw-filters { align-items:center; border-bottom:1px solid var(--sw-line); display:flex; flex-wrap:wrap; gap:8px; padding:8px 16px; }
    .score-workspace .sw-fchip { background:#fff; border:1px solid #dde4ee; border-radius:20px; color:#5a6d87; cursor:pointer; font-size:11.5px; font-weight:700; padding:5px 12px; transition:all .14s ease; }
    .score-workspace .sw-fchip:hover { border-color:#b9cbe8; color:var(--sw-blue); }
    .score-workspace .sw-fchip.active { background:var(--sw-blue); border-color:var(--sw-blue); color:#fff; }
    .score-workspace .sw-fchip b { font-weight:800; opacity:.8; }
    .score-workspace .sw-fspacer { flex:1; }
    .score-workspace .sw-hint { color:#8b9ab0; font-size:11px; }
    .score-workspace .sw-hint kbd { background:#eef2f8; border:1px solid #dbe3ee; border-radius:4px; box-shadow:none; color:#4a5d76; font-size:10px; font-weight:700; padding:1px 5px; }

    .score-workspace .sw-table-wrap { max-height:calc(100vh - 255px); min-height:340px; overflow:auto; }
    .score-workspace .sw-table { margin:0; min-width:960px; width:100%; }
    .score-workspace .sw-table thead th { background:#f4f7fb; border-bottom:1px solid #dfe6f0; border-top:0; color:#6c7f96; font-size:9.5px; font-weight:800; letter-spacing:.06em; padding:8px 10px; position:sticky; text-transform:uppercase; top:0; z-index:2; }
    .score-workspace .sw-table td { border-color:#eef2f8; padding:6px 10px; vertical-align:middle; }
    .score-workspace .sw-th-max { color:#a3b0c0; font-weight:700; letter-spacing:0; }
    .score-workspace .sw-table tbody tr { transition:background .12s ease; }
    .score-workspace .sw-table tbody tr:hover { background:#fafcff; }
    .score-workspace .sw-table tbody tr.is-focused { background:#f2f7ff; box-shadow:inset 3px 0 0 var(--sw-blue); }
    .score-workspace .sw-row-number { color:#a3b0c0; font-size:11px; font-weight:700; text-align:center; width:48px; }
    .score-workspace .sw-name { color:var(--sw-ink); font-size:12.5px; font-weight:750; }
    .score-workspace .sw-meta { color:var(--sw-muted); font-size:10.5px; margin-top:2px; }
    .score-workspace .sw-pill { border-radius:20px; display:inline-block; font-size:9.5px; font-weight:700; padding:3px 9px; }
    .score-workspace .sw-pill-ok { background:#eef2f7; color:#57697f; }
    .score-workspace .sw-pill-dq { background:#fdeceb; color:#b8443c; }
    .score-workspace .sw-dq { color:#b34545; font-size:10px; line-height:1.3; margin-top:4px; max-width:300px; }
    .score-workspace .sw-score-cell { text-align:center; width:190px; }
    .score-workspace .sw-score-input { border:1px solid #d5deea; border-radius:9px; color:var(--sw-ink); font-size:17px; font-weight:750; height:38px; margin:auto; padding:4px 7px; text-align:center; transition:border-color .14s ease,box-shadow .14s ease,background .14s ease; width:112px; }
    .score-workspace .sw-score-input:hover { border-color:#bccbe0; }
    .score-workspace .sw-score-input:focus { background:#fff; border-color:var(--sw-blue); box-shadow:0 0 0 3px rgba(36,87,214,.12); outline:none; }
    .score-workspace .sw-score-input.is-filled { background:#f3faf6; border-color:#bfe3d0; }
    .score-workspace .sw-score-input.is-invalid { background-image:none; background-color:#fdf4f3; border-color:#dc4c4c; padding-right:7px; }
    .score-workspace .sw-total { color:#a3b0c0; font-size:16px; font-weight:800; text-align:center; width:84px; }
    .score-workspace .sw-total.has-value { color:#1f7a51; }
    .score-workspace .sw-save-state { align-items:center; color:#93a2b6; display:inline-flex; font-size:10.5px; font-weight:650; gap:4px; min-width:96px; }
    .score-workspace .sw-save-state i { font-size:14px; }
    .score-workspace .sw-save-state.saving { color:#a66a00; }
    .score-workspace .sw-save-state.saved { color:#238052; }
    .score-workspace .sw-save-state.error { color:#c34444; }
    .score-workspace .sw-actor { align-items:center; color:#5a6d87; display:flex; font-size:10.5px; font-weight:650; gap:4px; margin-top:4px; max-width:190px; }
    .score-workspace .sw-actor i { font-size:12px; }
    .score-workspace .sw-actor span { overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
    .score-workspace .sw-actor-empty { color:#b3bece; font-weight:600; }
    .score-workspace .sw-actor-when { color:#a3b0c0; font-size:10px; margin-top:1px; }
    .score-workspace .sw-field-actor { border-top:1px dashed #e5ecf6; margin:5px auto 0; max-width:176px; padding-top:4px; }
    .score-workspace .sw-field-actor .sw-fa-who { align-items:center; color:#33547d; display:flex; font-size:10.5px; font-weight:750; gap:3px; justify-content:center; }
    .score-workspace .sw-field-actor .sw-fa-who i { flex:0 0 auto; font-size:12px; }
    .score-workspace .sw-field-actor .sw-fa-who span { overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
    .score-workspace .sw-field-actor .sw-fa-when { color:#a3b0c0; font-size:9.5px; margin-top:1px; text-align:center; }
    .score-workspace .sw-field-actor.is-empty { border-top-color:#f1f4f9; }
    .score-workspace .sw-field-actor.is-empty .sw-fa-who { color:#bcc6d4; font-weight:600; }

    .score-workspace .sw-trail { margin-top:16px; }
    .score-workspace .sw-trail-head { align-items:center; background:#fbfcfe; border-bottom:1px solid var(--sw-line); display:flex; flex-wrap:wrap; gap:10px; justify-content:space-between; padding:14px 18px; }
    .score-workspace .sw-trail-title { align-items:center; color:var(--sw-ink); display:flex; font-size:14px; font-weight:800; gap:7px; margin:0; }
    .score-workspace .sw-trail-title i { color:var(--sw-blue); font-size:17px; }
    .score-workspace .sw-trail-note { color:var(--sw-muted); font-size:11.5px; margin:2px 0 0; }
    .score-workspace .sw-trail-tools { align-items:center; display:flex; flex-wrap:wrap; gap:6px; }
    .score-workspace .sw-tchip { background:#fff; border:1px solid #dde4ee; border-radius:20px; color:#5a6d87; cursor:pointer; font-size:11px; font-weight:700; padding:6px 12px; transition:all .14s ease; }
    .score-workspace .sw-tchip:hover { border-color:#b9cbe8; color:var(--sw-blue); }
    .score-workspace .sw-tchip.active { background:var(--sw-blue); border-color:var(--sw-blue); color:#fff; }
    .score-workspace .sw-trail-list { max-height:360px; overflow-y:auto; }
    .score-workspace .sw-trail-item { border-bottom:1px solid #f0f4f9; display:flex; gap:11px; padding:11px 18px; }
    .score-workspace .sw-trail-item:last-child { border-bottom:0; }
    .score-workspace .sw-trail-icon { align-items:center; border-radius:9px; display:flex; flex:0 0 30px; font-size:15px; height:30px; justify-content:center; width:30px; }
    .score-workspace .sw-trail-icon.is-encode { background:#eaf7f0; color:#1f7a51; }
    .score-workspace .sw-trail-icon.is-edit { background:#fff5e2; color:#96650c; }
    .score-workspace .sw-trail-body { min-width:0; }
    .score-workspace .sw-trail-line { color:var(--sw-ink); font-size:12.5px; line-height:1.4; }
    .score-workspace .sw-trail-line b { font-weight:750; }
    .score-workspace .sw-trail-meta { align-items:center; color:var(--sw-muted); display:flex; flex-wrap:wrap; font-size:10.5px; gap:4px 10px; margin-top:3px; }
    .score-workspace .sw-trail-role { background:#eef3fb; border-radius:20px; color:#41577a; font-size:9.5px; font-weight:800; letter-spacing:.04em; padding:2px 8px; text-transform:uppercase; }
    .score-workspace .sw-trail-empty { color:var(--sw-muted); font-size:12.5px; padding:34px 18px; text-align:center; }
    .score-workspace .sw-trail-empty i { color:#c3cddc; display:block; font-size:32px; margin-bottom:6px; }
    .score-workspace .sw-ma-link { color:#7488a1; font-size:17px; }
    .score-workspace .sw-ma-link:hover { color:var(--sw-blue); }
    .score-workspace .sw-empty { color:var(--sw-muted); padding:52px 20px; text-align:center; }
    .score-workspace .sw-foot { align-items:center; background:#fbfcfe; border-top:1px solid var(--sw-line); color:var(--sw-muted); display:flex; flex-wrap:wrap; font-size:10.5px; gap:6px 18px; justify-content:space-between; padding:8px 16px; }

    @media (max-width:1050px) {
        .score-workspace .sw-hero { align-items:flex-start; }
        .score-workspace .sw-toolbar { width:100%; }
    }
    @media (max-width:680px) {
        .score-workspace .sw-hero { align-items:flex-start; flex-direction:column; }
        .score-workspace .sw-tool-vacancy, .score-workspace .sw-search { min-width:0; width:100%; }
        .score-workspace .sw-mode { width:100%; }
        .score-workspace .sw-mode .btn { flex:1; padding-left:6px; padding-right:6px; }
        .score-workspace .sw-table-wrap { max-height:none; }
        .score-workspace .sw-hint { display:none; }
    }
</style>

<div class="content-page score-workspace">
    <div class="content">
        <div class="container-fluid">
            <div class="sw-hero">
                <div class="sw-hero-lead">
                    <div class="sw-eyebrow">Recruitment</div>
                    <h2 class="sw-title">Score Encoding</h2>
                    <p class="sw-subtitle">Encode Interview and Written Examination scores. Every entry saves on its own.</p>
                </div>
                <form method="get" action="<?= base_url('secretariat/scores'); ?>" class="sw-toolbar" id="score-toolbar-form">
                    <div class="sw-tool-vacancy">
                        <label class="sw-label" for="score-vacancy">Vacancy</label>
                        <select class="sw-select" name="job_id" id="score-vacancy">
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
                        <?php if (count($modeOptions) <= 1) : ?>
                            <div class="sw-mode-locked">
                                <i class="mdi mdi-lock-outline"></i>
                                <?= $score_h($modeLabels[$modeOptions[0] ?? 'both']); ?>
                                <span>your account's permission on this vacancy</span>
                            </div>
                        <?php else : ?>
                            <div class="sw-mode" role="group" aria-label="Score fields to encode">
                                <?php foreach ($modeOptions as $modeValue) : ?>
                                    <button type="submit" name="mode" value="<?= $modeValue; ?>" class="btn <?= $encodingMode === $modeValue ? 'active' : ''; ?>"><?= $score_h($modeLabels[$modeValue]); ?></button>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <?php if ($this->session->position !== 'Field Encoder') : ?>
                        <a href="<?= base_url(); ?>" class="sw-back"><i class="mdi mdi-arrow-left"></i>Dashboard</a>
                    <?php endif; ?>
                </form>
            </div>

            <?php if ($successMessage) : ?><div class="alert alert-success py-2"><?= $score_h($successMessage); ?></div><?php endif; ?>
            <?php if ($dangerMessage) : ?><div class="alert alert-danger py-2"><?= $score_h($dangerMessage); ?></div><?php endif; ?>

            <?php if (empty($vacancies)) : ?>
                <div class="alert alert-warning">No open score-eligible vacancy is assigned to your Secretariat account.</div>
            <?php elseif (empty($selectedVacancy)) : ?>
                <div class="sw-card sw-empty">
                    <i class="mdi mdi-format-list-numbered" style="font-size:40px"></i>
                    <h5 class="mt-2">Select a vacancy to start</h5>
                    <p class="mb-0 small">Scores are encoded one vacancy at a time.</p>
                </div>
            <?php else : ?>
                <div class="sw-card">
                    <div class="sw-info">
                        <div>
                            <div class="sw-job"><?= $score_h($selectedVacancy->jobTitle); ?></div>
                            <div class="sw-job-meta">Job #<?= (int) $selectedVacancy->jobID; ?> &middot; FY <?= $score_h($selectedVacancy->sy); ?> &middot; <?= $score_h($modeLabels[$encodingMode]); ?></div>
                        </div>
                        <div class="sw-meter">
                            <div class="sw-meter-top">
                                <span>Encoded</span>
                                <span><strong id="mode-encoded-count"><?= $encodedForMode; ?></strong> of <strong><?= $modeTotal; ?></strong></span>
                            </div>
                            <div class="sw-meter-bar"><span id="mode-encoded-bar" style="width:<?= (int) $modePercent; ?>%"></span></div>
                        </div>
                        <div>
                            <span class="sw-autosave" id="sw-autosave"><i class="mdi mdi-cloud-check-outline"></i><span>All changes saved</span></span>
                        </div>
                    </div>

                    <?php if (empty($applicants)) : ?>
                        <div class="sw-empty">No applicants found for this vacancy.</div>
                    <?php else : ?>
                        <?php
                        $pendingRows = 0;
                        $dqRows = 0;
                        foreach ($applicants as $applicant) {
                            $iEnc = $score_is_encoded($applicant->interview ?? null);
                            $wEnc = $score_is_encoded($applicant->written ?? null);
                            $complete = ($encodingMode === 'interview' && $iEnc)
                                || ($encodingMode === 'written' && $wEnc)
                                || ($encodingMode === 'both' && $iEnc && $wEnc);
                            if (!$complete) {
                                $pendingRows++;
                            }
                            if ((int) $applicant->dq === 2) {
                                $dqRows++;
                            }
                        }
                        ?>
                        <div class="sw-filters">
                            <button type="button" class="sw-fchip active" data-filter="all">All <b id="chip-all"><?= count($applicants); ?></b></button>
                            <button type="button" class="sw-fchip" data-filter="pending">Not encoded <b id="chip-pending"><?= $pendingRows; ?></b></button>
                            <button type="button" class="sw-fchip" data-filter="done">Encoded <b id="chip-done"><?= count($applicants) - $pendingRows; ?></b></button>
                            <?php if ($dqRows > 0) : ?>
                                <button type="button" class="sw-fchip" data-filter="dq">Disqualified <b><?= $dqRows; ?></b></button>
                            <?php endif; ?>
                            <span class="sw-fspacer"></span>
                            <div class="sw-search">
                                <i class="mdi mdi-magnify"></i>
                                <input type="search" id="score-applicant-search" placeholder="Find applicant — name, ID, status, reason" autocomplete="off" aria-label="Find applicant">
                            </div>
                        </div>

                        <div class="sw-table-wrap">
                            <table class="table sw-table" id="score-applicant-table">
                                <thead>
                                    <tr>
                                        <th class="text-center">#</th>
                                        <th>Applicant</th>
                                        <th>Status</th>
                                        <th>Evaluator</th>
                                        <?php if ($showWritten) : ?><th class="text-center">Written <span class="sw-th-max">/ 20</span></th><?php endif; ?>
                                        <?php if ($showInterview) : ?><th class="text-center">Interview <span class="sw-th-max">/ 20</span></th><?php endif; ?>
                                        <?php if ($showWritten && $showInterview) : ?><th class="text-center">Total</th><?php endif; ?>
                                        <th>Save state</th>
                                        <th class="text-center">View Application</th>
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
                                        $isDq = (int) $applicant->dq === 2;
                                        $rowTotal = ($interviewEncoded && $writtenEncoded)
                                            ? (float) $applicant->interview + (float) $applicant->written
                                            : null;
                                        $searchText = strtolower($name . ' ' . $applicant->applicant_id . ' ' . $applicant->record_no . ' ' . $applicant->appStatus . ' ' . ($applicant->dq_reason ?? ''));
                                        // Per-field audit rows: written and interview can belong to two different encoders.
                                        $rowActions = $lastActions[(int) $applicant->appID] ?? [];
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
                                        <tr data-score-search="<?= $score_h($searchText); ?>" data-mode-complete="<?= $modeComplete ? '1' : '0'; ?>" data-dq="<?= $isDq ? '1' : '0'; ?>">
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
                                                <span class="sw-pill <?= $isDq ? 'sw-pill-dq' : 'sw-pill-ok'; ?>"><?= $score_h($applicant->appStatus ?: 'No status'); ?></span>
                                                <?php if ($isDq) : ?>
                                                    <div class="sw-dq"><strong>DQ</strong><?= !empty($applicant->dq_reason) ? ': ' . $score_h($applicant->dq_reason) : ''; ?></div>
                                                <?php endif; ?>
                                            </td>
                                            <td class="sw-evaluator">
                                                <?= !empty($applicant->evaluator_name) ? $score_h($applicant->evaluator_name) : '&mdash;'; ?>
                                            </td>
                                            <?php if ($showWritten) : ?>
                                                <td class="sw-score-cell">
                                                    <input form="<?= $formId; ?>" type="number" inputmode="decimal" min="0" max="20" step="0.01" name="written" data-field="written" data-last-saved="<?= $writtenEncoded ? $score_h((float) $applicant->written) : ''; ?>" data-counted="<?= $writtenEncoded ? '1' : '0'; ?>" class="form-control sw-score-input <?= $writtenEncoded ? 'is-filled' : ''; ?>" value="<?= $writtenEncoded ? $score_h((float) $applicant->written) : ''; ?>" aria-label="Written Examination score for <?= $score_h($name); ?>">
                                                    <?= $score_field_actor($rowActions['written'] ?? null); ?>
                                                </td>
                                            <?php endif; ?>
                                            <?php if ($showInterview) : ?>
                                                <td class="sw-score-cell">
                                                    <input form="<?= $formId; ?>" type="number" inputmode="decimal" min="0" max="20" step="0.01" name="interview" data-field="interview" data-last-saved="<?= $interviewEncoded ? $score_h((float) $applicant->interview) : ''; ?>" data-counted="<?= $interviewEncoded ? '1' : '0'; ?>" class="form-control sw-score-input <?= $interviewEncoded ? 'is-filled' : ''; ?>" value="<?= $interviewEncoded ? $score_h((float) $applicant->interview) : ''; ?>" aria-label="Interview score for <?= $score_h($name); ?>">
                                                    <?= $score_field_actor($rowActions['interview'] ?? null); ?>
                                                </td>
                                            <?php endif; ?>
                                            <?php if ($showWritten && $showInterview) : ?>
                                                <td class="sw-total <?= $rowTotal !== null ? 'has-value' : ''; ?>"><?= $rowTotal !== null ? $score_h(rtrim(rtrim(number_format($rowTotal, 2, '.', ''), '0'), '.')) : '&mdash;'; ?></td>
                                            <?php endif; ?>
                                            <td>
                                                <span class="sw-save-state <?= $modeComplete ? 'saved' : ''; ?>"><i class="mdi <?= $modeComplete ? 'mdi-check-circle-outline' : 'mdi-circle-edit-outline'; ?>"></i><span><?= $modeComplete ? 'Saved' : 'Ready'; ?></span></span>
                                            </td>
                                            <td class="text-center">
                                                <?php if ($profileUrl !== '') : ?><a href="<?= $score_h($profileUrl); ?>" class="sw-ma-link" target="_blank" rel="noopener" title="Open MA page"><i class="mdi mdi-open-in-new"></i></a><?php else : ?><span class="text-muted">&mdash;</span><?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="sw-foot">
                            <span><i class="mdi mdi-information-outline"></i> Leaving a box blank keeps the score already saved &mdash; it never clears it.</span>
                            <span class="sw-hint"><kbd>Enter</kbd> save &amp; next &middot; <kbd>&uarr;</kbd><kbd>&darr;</kbd> move</span>
                            <span><strong id="score-visible-count"><?= count($applicants); ?></strong> of <?= count($applicants); ?> applicant<?= count($applicants) === 1 ? '' : 's'; ?> shown</span>
                        </div>
                    <?php endif; ?>
                </div>
                </div>
                </div>
                <br>

                <!-- <div class="sw-card sw-trail" id="score-trail" data-job-id="<?= $selectedJobId; ?>" data-activity-url="<?= base_url('secretariat/scores/activity'); ?>">
                    <div class="sw-trail-head">
                        <div>
                            <h5 class="sw-trail-title"><i class="mdi mdi-history"></i>Encoding Activity</h5>
                            <p class="sw-trail-note">Every encode and edit on this vacancy, newest first, with who did it.</p>
                        </div>
                        <div class="sw-trail-tools">
                            <button type="button" class="sw-tchip active" data-trail-filter="all">All</button>
                            <button type="button" class="sw-tchip" data-trail-filter="encode">Encoded</button>
                            <button type="button" class="sw-tchip" data-trail-filter="edit">Edited</button>
                            <button type="button" class="sw-tchip" data-trail-filter="written">Written</button>
                            <button type="button" class="sw-tchip" data-trail-filter="interview">Interview</button>
                            <button type="button" class="sw-tchip" id="score-trail-refresh" title="Reload the trail"><i class="mdi mdi-refresh"></i> Refresh</button>
                        </div>
                    </div>
                    <div class="sw-trail-list" id="score-trail-list">
                        <?php if (empty($activity)) : ?>
                            <div class="sw-trail-empty">
                                <i class="mdi mdi-clipboard-text-clock-outline"></i>
                                No score has been encoded for this vacancy yet.
                            </div>
                        <?php else : ?>
                            <?php foreach ($activity as $entry) :
                                $kind = $score_action_kind($entry->description);
                                $actor = trim(trim((string) $entry->fname) . ' ' . trim((string) $entry->lname));
                                if ($actor === '') {
                                    $actor = (string) $entry->username;
                                }
                                $applicantName = trim(trim(trim((string) $entry->app_last) . ', ' . trim((string) $entry->app_first)), ', ');
                            ?>
                                <div class="sw-trail-item" data-trail-kind="<?= $kind; ?>" data-trail-field="<?= $score_h($entry->field); ?>">
                                    <div class="sw-trail-icon is-<?= $kind; ?>"><i class="mdi <?= $kind === 'edit' ? 'mdi-pencil-outline' : 'mdi-plus-circle-outline'; ?>"></i></div>
                                    <div class="sw-trail-body">
                                        <div class="sw-trail-line"><b><?= $score_h($actor); ?></b> &mdash; <?= $score_h($entry->description); ?></div>
                                        <div class="sw-trail-meta">
                                            <?php if ($entry->position) : ?><span class="sw-trail-role"><?= $score_h($entry->position); ?></span><?php endif; ?>
                                            <span><i class="mdi mdi-account-outline"></i> <?= $score_h($applicantName !== '' ? $applicantName : ('App #' . (int) $entry->app_id)); ?></span>
                                            <span><i class="mdi mdi-clock-outline"></i> <?= $score_h($score_when($entry->created_at)); ?></span>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    <div class="sw-foot">
                        <span><i class="mdi mdi-shield-check-outline"></i> Recorded automatically in the system audit trail.</span>
                        <span><strong id="score-trail-count"><?= count($activity); ?></strong> action<?= count($activity) === 1 ? '' : 's'; ?> shown</span>
                    </div>
                </div> -->
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
    var encodedBar = document.getElementById('mode-encoded-bar');
    var autosaveBadge = document.getElementById('sw-autosave');
    var chipAll = document.getElementById('chip-all');
    var chipPending = document.getElementById('chip-pending');
    var chipDone = document.getElementById('chip-done');
    var modeTotal = <?= (int) $modeTotal; ?>;
    var formStates = new WeakMap();
    var activeFilter = 'all';

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

    var allRows = Array.prototype.slice.call(table.querySelectorAll('tbody tr'));

    function stateFor(form) {
        if (!formStates.has(form)) {
            formStates.set(form, { timer: null, saving: false, queued: false, retried: false });
        }
        return formStates.get(form);
    }

    function escapeText(value) {
        return String(value == null ? '' : value)
            .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;').replace(/'/g, '&#039;');
    }

    // Credit the save to the account that made it, under that field's own box.
    function stampFieldActor(input, payload, field) {
        var cell = input.closest('td');
        var box = cell ? cell.querySelector('[data-field-actor]') : null;
        if (!box || !payload || !payload.actor) return;

        var action = (payload.actions || {})[field] || {};
        box.className = 'sw-field-actor';
        box.title = action.description || '';
        box.innerHTML = '<div class="sw-fa-who"><i class="mdi '
            + (action.kind === 'edit' ? 'mdi-pencil-outline' : 'mdi-account-check-outline') + '"></i>'
            + '<span>' + escapeText(payload.actor) + '</span></div>'
            + '<div class="sw-fa-when">' + escapeText(payload.when || '') + '</div>';
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
        refreshAutosaveBadge();
    }

    // Header pill mirrors the whole page: unsaved beats saving beats error beats clean.
    function refreshAutosaveBadge() {
        if (!autosaveBadge) return;
        var saving = 0, dirty = 0, failed = 0;
        allRows.forEach(function (row) {
            var status = row.querySelector('.sw-save-state');
            if (status && status.classList.contains('saving')) saving += 1;
            if (status && status.classList.contains('error')) failed += 1;
            if (rowDirtyInputs(row).length) dirty += 1;
        });
        var icon = 'mdi-cloud-check-outline';
        var text = 'All changes saved';
        var cls = '';
        if (failed) {
            icon = 'mdi-cloud-alert';
            text = failed + ' failed to save';
            cls = ' is-error';
        } else if (saving) {
            icon = 'mdi-cloud-sync-outline';
            text = 'Saving…';
            cls = ' is-pending';
        } else if (dirty) {
            icon = 'mdi-cloud-upload-outline';
            text = dirty + ' unsaved';
            cls = ' is-pending';
        }
        autosaveBadge.className = 'sw-autosave' + cls;
        autosaveBadge.querySelector('i').className = 'mdi ' + icon;
        autosaveBadge.querySelector('span').textContent = text;
    }

    function rowDirtyInputs(row) {
        return Array.prototype.filter.call(row.querySelectorAll('.sw-score-input'), function (input) {
            return input.value.trim() !== '' && normalized(input.value) !== normalized(input.dataset.lastSaved || '');
        });
    }

    function dirtyInputs(form) {
        return rowDirtyInputs(form.closest('tr'));
    }

    function validate(input) {
        var empty = input.value.trim() === '';
        var valid = empty || (input.checkValidity() && Number(input.value) >= 0 && Number(input.value) <= 20);
        input.classList.toggle('is-invalid', !valid);
        input.classList.toggle('is-filled', !empty && valid);
        return valid;
    }

    function trimNumber(value) {
        return String(Math.round(value * 100) / 100);
    }

    function refreshRowTotal(row) {
        var cell = row.querySelector('.sw-total');
        if (!cell) return;
        var inputs = row.querySelectorAll('.sw-score-input');
        var sum = 0;
        var complete = true;
        Array.prototype.forEach.call(inputs, function (input) {
            var saved = input.dataset.lastSaved || '';
            if (saved === '') { complete = false; return; }
            sum += Number(saved);
        });
        cell.textContent = complete ? trimNumber(sum) : '—';
        cell.classList.toggle('has-value', complete);
    }

    function refreshCounts() {
        var pending = 0;
        allRows.forEach(function (row) {
            if (row.dataset.modeComplete !== '1') pending += 1;
        });
        if (chipAll) chipAll.textContent = allRows.length;
        if (chipPending) chipPending.textContent = pending;
        if (chipDone) chipDone.textContent = allRows.length - pending;
    }

    function refreshModeCount(form) {
        var row = form.closest('tr');
        var allCounted = Array.prototype.every.call(row.querySelectorAll('.sw-score-input'), function (input) {
            return input.dataset.counted === '1';
        });
        if (allCounted && row.dataset.modeComplete !== '1') {
            row.dataset.modeComplete = '1';
            if (encodedCount) {
                var next = Number(encodedCount.textContent || 0) + 1;
                encodedCount.textContent = String(next);
                if (encodedBar && modeTotal > 0) encodedBar.style.width = Math.round((next / modeTotal) * 100) + '%';
            }
            refreshCounts();
            applyFilters();
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
                if (!response.ok || !payload.ok) {
                    var error = new Error(payload.message || 'Save failed');
                    error.rejected = response.status === 422;
                    throw error;
                }
                return payload;
            });
        }).then(function (payload) {
            savedOk = true;
            state.retried = false;
            var row = form.closest('tr');
            Object.keys(sent).forEach(function (field) {
                var input = row.querySelector('.sw-score-input[name="' + field + '"]');
                if (!input) return;
                input.dataset.lastSaved = sent[field];
                input.dataset.counted = '1';
                input.classList.add('is-filled');
                stampFieldActor(input, payload, field);
            });
            refreshRowTotal(row);
            refreshModeCount(form);
            setStatus(form, 'saved', payload.saved_at ? 'Saved ' + payload.saved_at : 'Saved');
            document.dispatchEvent(new CustomEvent('score:saved'));
        }).catch(function (error) {
            setStatus(form, 'error', error.message || 'Save failed');
            // A dropped connection is worth one silent retry; a rejected value is not.
            if (!error.rejected && !state.retried) {
                state.retried = true;
                state.queued = true;
            }
        }).finally(function () {
            state.saving = false;
            if (state.queued || (savedOk && dirtyInputs(form).length)) {
                state.queued = false;
                scheduleSave(form, savedOk ? 150 : 2500);
            }
            refreshAutosaveBadge();
        });
    }

    function scheduleSave(form, delay) {
        var state = stateFor(form);
        if (state.timer) clearTimeout(state.timer);
        state.timer = setTimeout(function () { saveForm(form); }, delay == null ? 600 : delay);
    }

    function flushAll() {
        table.querySelectorAll('.score-auto-form').forEach(function (form) {
            if (dirtyInputs(form).length) saveForm(form);
        });
    }

    function visibleInputs(field) {
        return Array.prototype.filter.call(table.querySelectorAll('.sw-score-input[data-field="' + field + '"]'), function (candidate) {
            return candidate.closest('tr').style.display !== 'none';
        });
    }

    function moveFocus(input, step) {
        var candidates = visibleInputs(input.dataset.field);
        var index = candidates.indexOf(input);
        var next = candidates[index + step];
        if (index >= 0 && next) {
            next.focus();
            next.select();
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
        var row = input.closest('tr');

        input.addEventListener('focus', function () { row.classList.add('is-focused'); });
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
            scheduleSave(form, 600);
        });
        input.addEventListener('blur', function () {
            row.classList.remove('is-focused');
            if (input.value.trim() !== '' && validate(input)) saveForm(form);
        });
        input.addEventListener('keydown', function (event) {
            if (event.key === 'Enter') {
                event.preventDefault();
                if (input.value.trim() !== '' && validate(input)) saveForm(form);
                moveFocus(input, 1);
                return;
            }
            if (event.key === 'ArrowDown' || event.key === 'ArrowUp') {
                event.preventDefault();
                if (input.value.trim() !== '' && validate(input)) saveForm(form);
                moveFocus(input, event.key === 'ArrowDown' ? 1 : -1);
            }
        });
    });

    function applyFilters() {
        var needle = search ? search.value.trim().toLowerCase() : '';
        var visible = 0;
        allRows.forEach(function (row) {
            var matchesText = !needle || (row.getAttribute('data-score-search') || '').indexOf(needle) !== -1;
            var matchesChip = activeFilter === 'all'
                || (activeFilter === 'pending' && row.dataset.modeComplete !== '1')
                || (activeFilter === 'done' && row.dataset.modeComplete === '1')
                || (activeFilter === 'dq' && row.dataset.dq === '1');
            var show = matchesText && matchesChip;
            row.style.display = show ? '' : 'none';
            if (show) visible += 1;
        });
        if (visibleCount) visibleCount.textContent = visible;
    }

    document.querySelectorAll('.sw-fchip').forEach(function (chip) {
        chip.addEventListener('click', function () {
            document.querySelectorAll('.sw-fchip').forEach(function (other) { other.classList.remove('active'); });
            chip.classList.add('active');
            activeFilter = chip.dataset.filter;
            applyFilters();
        });
    });

    if (search) {
        search.addEventListener('keydown', function (event) {
            if (event.key === 'Enter') event.preventDefault();
        });
        search.addEventListener('input', applyFilters);
    }

    // Nothing typed should be lost to a tab switch or a closed window.
    document.addEventListener('visibilitychange', function () {
        if (document.visibilityState === 'hidden') flushAll();
    });
    window.addEventListener('beforeunload', function (event) {
        flushAll();
        // Only a save that actually failed is worth a confirm dialog; an
        // in-flight keepalive request finishes on its own after unload.
        var failed = allRows.some(function (row) {
            var status = row.querySelector('.sw-save-state');
            return status && status.classList.contains('error');
        });
        if (!failed) return;
        event.preventDefault();
        event.returnValue = '';
    });

    refreshCounts();
    refreshAutosaveBadge();
})();

/* ---- Encoding activity trail ---- */
(function () {
    var trail = document.getElementById('score-trail');
    if (!trail) return;

    var list = document.getElementById('score-trail-list');
    var countLabel = document.getElementById('score-trail-count');
    var refreshBtn = document.getElementById('score-trail-refresh');
    var jobId = trail.dataset.jobId;
    var activityUrl = trail.dataset.activityUrl;
    var filter = 'all';
    var pending = null;
    var loading = false;

    function escapeHtml(value) {
        return String(value == null ? '' : value)
            .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;').replace(/'/g, '&#039;');
    }

    // The stamp is already Asia/Manila server time - parse the parts by hand
    // rather than through Date(), which would re-zone it to the browser.
    function formatWhen(value) {
        var match = /^(\d{4})-(\d{2})-(\d{2})[ T](\d{2}):(\d{2})/.exec(String(value || ''));
        if (!match) return String(value || '');
        var months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
        var hour = Number(match[4]);
        var suffix = hour >= 12 ? 'PM' : 'AM';
        var hour12 = hour % 12 === 0 ? 12 : hour % 12;
        return months[Number(match[2]) - 1] + ' ' + Number(match[3]) + ', ' + match[1]
            + ' ' + hour12 + ':' + match[5] + ' ' + suffix;
    }

    function kindOf(description) {
        return /^edited/i.test(String(description || '')) ? 'edit' : 'encode';
    }

    function applyFilter() {
        var shown = 0;
        trail.querySelectorAll('.sw-trail-item').forEach(function (item) {
            var match = filter === 'all'
                || item.dataset.trailKind === filter
                || item.dataset.trailField === filter;
            item.style.display = match ? '' : 'none';
            if (match) shown += 1;
        });
        if (countLabel) countLabel.textContent = shown;
    }

    function render(entries) {
        if (!entries.length) {
            list.innerHTML = '<div class="sw-trail-empty"><i class="mdi mdi-clipboard-text-clock-outline"></i>No score has been encoded for this vacancy yet.</div>';
            if (countLabel) countLabel.textContent = '0';
            return;
        }

        list.innerHTML = entries.map(function (entry) {
            var kind = kindOf(entry.description);
            var who = entry.actor || entry.username || '';
            var applicant = entry.applicant || ('App #' + entry.app_id);
            return '<div class="sw-trail-item" data-trail-kind="' + kind + '" data-trail-field="' + escapeHtml(entry.field) + '">'
                + '<div class="sw-trail-icon is-' + kind + '"><i class="mdi ' + (kind === 'edit' ? 'mdi-pencil-outline' : 'mdi-plus-circle-outline') + '"></i></div>'
                + '<div class="sw-trail-body">'
                + '<div class="sw-trail-line"><b>' + escapeHtml(who) + '</b> &mdash; ' + escapeHtml(entry.description) + '</div>'
                + '<div class="sw-trail-meta">'
                + (entry.role ? '<span class="sw-trail-role">' + escapeHtml(entry.role) + '</span>' : '')
                + '<span><i class="mdi mdi-account-outline"></i> ' + escapeHtml(applicant) + '</span>'
                + '<span><i class="mdi mdi-clock-outline"></i> ' + escapeHtml(formatWhen(entry.when)) + '</span>'
                + '</div></div></div>';
        }).join('');

        applyFilter();
    }

    // The trail is ordered newest first, so the first entry seen for an
    // application is the one its row should advertise.
    function refreshRowActors(entries) {
        var seen = {};
        entries.forEach(function (entry) {
            if (seen[entry.app_id]) return;
            seen[entry.app_id] = entry;
        });

        document.querySelectorAll('#score-applicant-table tbody tr').forEach(function (row) {
            var form = row.querySelector('.score-auto-form');
            var actor = row.querySelector('[data-row-actor]');
            if (!form || !actor) return;

            var entry = seen[form.querySelector('[name="app_id"]').value];
            if (!entry) return;

            var kind = kindOf(entry.description);
            actor.className = 'sw-actor';
            actor.title = entry.description;
            actor.innerHTML = '<i class="mdi ' + (kind === 'edit' ? 'mdi-pencil-outline' : 'mdi-plus-circle-outline') + '"></i>'
                + '<span>' + escapeHtml((entry.field === 'interview' ? 'Interview' : 'Written') + ' by ' + (entry.actor || entry.username)) + '</span>';

            var when = actor.nextElementSibling;
            if (when && when.classList.contains('sw-actor-when')) {
                when.textContent = formatWhen(entry.when);
            }
        });
    }

    function load() {
        if (loading || !jobId || jobId === '0') return;
        loading = true;
        if (refreshBtn) refreshBtn.disabled = true;

        fetch(activityUrl + '?job_id=' + encodeURIComponent(jobId), {
            credentials: 'same-origin',
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        }).then(function (response) {
            return response.json();
        }).then(function (payload) {
            if (!payload || !payload.ok) return;
            render(payload.entries || []);
            refreshRowActors(payload.entries || []);
        }).catch(function () {
            /* A failed refresh leaves the trail as it was - nothing to undo. */
        }).finally(function () {
            loading = false;
            if (refreshBtn) refreshBtn.disabled = false;
        });
    }

    trail.querySelectorAll('[data-trail-filter]').forEach(function (chip) {
        chip.addEventListener('click', function () {
            trail.querySelectorAll('[data-trail-filter]').forEach(function (other) { other.classList.remove('active'); });
            chip.classList.add('active');
            filter = chip.dataset.trailFilter;
            applyFilter();
        });
    });

    if (refreshBtn) {
        refreshBtn.addEventListener('click', load);
    }

    // Autosave fires per keystroke burst; coalesce the reloads it triggers.
    document.addEventListener('score:saved', function () {
        if (pending) clearTimeout(pending);
        pending = setTimeout(load, 900);
    });
})();
</script>
