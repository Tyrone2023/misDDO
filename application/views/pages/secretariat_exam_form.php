<?php
/**
 * Exam Builder form, ported from the SRMS College Assessment Suite create view.
 *
 * The settings strip, the six question types and the JSON the builder posts are
 * the college shapes. What differs is the top of the form: the college binds an
 * assessment to a class section, this one binds an exam to a vacancy, and the
 * vacancy is rendered on every screen in place of subject / section.
 */
$ex_h = static function ($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
};

$positionGroups = [
    1 => 'Teaching',
    2 => 'School Administration',
    3 => 'Related Teaching',
    4 => 'Non-Teaching',
];

$mode = ($mode ?? 'create') === 'edit' ? 'edit' : 'create';
$isEdit = $mode === 'edit';
$vacancies = $vacancies ?? [];
$exam = $exam ?? null;
$builderQuestions = $builderQuestions ?? [];
$selectedJobId = (int) ($selectedJobId ?? 0);
$old = $old ?? [];

// A rejected save comes back through flashdata, so what was typed wins over
// what is stored; only then does the stored exam, then the default, apply.
$val = static function (string $key, $fallback = '') use ($old, $exam) {
    if (array_key_exists($key, $old) && $old[$key] !== '') {
        return $old[$key];
    }
    if ($exam && isset($exam->$key) && $exam->$key !== null) {
        return $exam->$key;
    }
    return $fallback;
};

$toLocalInput = static function ($value) {
    $value = trim((string) $value);
    if ($value === '' || strpos($value, '0000-00-00') === 0) {
        return '';
    }
    // Already a datetime-local value bounced back from a failed save.
    if (strpos($value, 'T') !== false) {
        return substr($value, 0, 16);
    }
    $stamp = strtotime($value);
    return $stamp ? date('Y-m-d\TH:i', $stamp) : '';
};

$curJobId = (int) ($old['job_id'] ?? 0) ?: ($isEdit ? (int) $exam->job_id : $selectedJobId);
$curTitle = (string) $val('title');
$curStatus = (string) $val('status', 'published');
$curInstructions = (string) ($old['instructions'] ?? ($exam->instructions ?? ''));
$curPassword = (string) ($old['exam_password'] ?? ($exam->password_plain ?? ''));
$curAttemptLimit = (string) ($old['attempt_limit'] ?? ($exam ? (string) (int) $exam->attempt_limit : '1'));
$curPassingScore = (string) ($old['passing_score'] ?? ($exam && $exam->passing_score !== null ? rtrim(rtrim(number_format((float) $exam->passing_score, 2, '.', ''), '0'), '.') : ''));
$curTimeLimit = (string) ($old['time_limit_minutes'] ?? ($exam && $exam->time_limit_minutes ? (string) (int) $exam->time_limit_minutes : ''));
$curTimeLimitCustom = (string) ($old['time_limit_minutes_custom'] ?? '');
$curOpenAt = $toLocalInput($old['open_at'] ?? ($exam->open_at ?? ''));
$curCloseAt = $toLocalInput($old['close_at'] ?? ($exam->close_at ?? ''));

$timeLimitPresets = ['15', '20', '30', '45', '60', '90', '120', '180'];
// A custom minute count posted earlier must still select "Custom" on the way back.
if ($curTimeLimit === 'custom') {
    $curTimeLimit = $curTimeLimitCustom;
}
$timeLimitIsCustom = $curTimeLimit !== '' && !in_array($curTimeLimit, $timeLimitPresets, true);

$restored = $old['questions_json'] ?? '';
if (trim((string) $restored) === '') {
    $restored = json_encode($builderQuestions, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?: '[]';
}

$selectedVacancy = null;
foreach ($vacancies as $vacancy) {
    if ((int) $vacancy->jobID === $curJobId) {
        $selectedVacancy = $vacancy;
        break;
    }
}

$formAction = $isEdit
    ? base_url('secretariat/exams/' . (int) $exam->exam_id . '/update')
    : base_url('secretariat/exams/store');
?>

<style>
    /* templates/footer.php's .footer is position:absolute at the document bottom,
       and .content-page reserves padding-bottom:65px as the strip it sits over.
       This class lands on the same element, so a smaller value here lets the
       footer bar draw on top of the submit row. Stay above 65px. */
    .exb-page { --exb-ink:#1f2a37; --exb-muted:#6d7885; --exb-line:#e2e6ec; --exb-accent:#0d6efd; --exb-accent-strong:#0b5ed7; --exb-accent-light:#eef4ff; --exb-soft:#f7f8fb; --exb-danger:#dc3545; --exb-radius:12px; --exb-radius-sm:8px; padding-bottom:90px; }
    .exb-page .container-fluid { max-width:1280px; }

    .exb-page .exb-hero { align-items:center; background:linear-gradient(125deg,#103764 0%,#2357d5 62%,#4d82ed 100%); border-radius:14px; color:#fff; display:flex; flex-wrap:wrap; gap:16px; justify-content:space-between; margin-bottom:20px; padding:22px 24px; }
    .exb-page .exb-hero-avatar { align-items:center; background:rgba(255,255,255,.16); border-radius:12px; display:flex; height:52px; justify-content:center; width:52px; }
    .exb-page .exb-hero-avatar i { font-size:24px; }
    .exb-page .exb-hero-main { flex:1 1 320px; }
    .exb-page .exb-hero-eyebrow { color:#cfe0ff; font-size:11.5px; font-weight:700; letter-spacing:.1em; text-transform:uppercase; }
    .exb-page .exb-hero h3 { color:#fff; font-size:22px; font-weight:700; margin:4px 0 4px; }
    .exb-page .exb-hero-sub { color:#dce8ff; font-size:13px; margin:0; }
    .exb-page .exb-hero-chip { align-items:center; background:rgba(255,255,255,.16); border:0; border-radius:8px; color:#fff; display:inline-flex; font-size:12.5px; font-weight:600; gap:6px; padding:8px 13px; }
    .exb-page .exb-hero-chip:hover, .exb-page .exb-hero-chip:focus { background:rgba(255,255,255,.28); color:#fff; text-decoration:none; }
    .exb-page .exb-hero-layout { align-items:center; display:flex; flex:1 1 auto; gap:16px; }

    /* Settings strip: an explicit 12-column grid.
       It used to be repeat(auto-fill,minmax(160px,1fr)), so the number of tracks
       changed with the viewport and every field was pinned to whatever rhythm that
       produced - which is why Status and Attempts came out cramped next to the wide
       fields, and why a row could break in a different place at another width.
       Declaring 12 columns and a span per field makes each row add up to exactly 12
       and stay put. Spans are namespaced (exb-col-*) so they cannot collide with
       Bootstrap's own .col-* classes. */
    .exb-page .settings-strip { background:#fff; border:1px solid var(--exb-line); border-radius:var(--exb-radius); display:grid; gap:16px 18px; grid-template-columns:repeat(12,1fr); margin-bottom:20px; padding:20px 22px; }
    .exb-page .settings-strip > div { min-width:0; }
    .exb-page .exb-col-2 { grid-column:span 2; }
    .exb-page .exb-col-3 { grid-column:span 3; }
    .exb-page .exb-col-4 { grid-column:span 4; }
    .exb-page .exb-col-5 { grid-column:span 5; }
    .exb-page .exb-col-12 { grid-column:span 12; }
    /* Rows align on their own baseline, so a two-line help text under one field
       does not push the field beside it down. */
    .exb-page .settings-strip > div { align-self:start; }
    .exb-page .field-label { color:var(--exb-muted); display:block; font-size:11.5px; font-weight:600; letter-spacing:.5px; margin-bottom:5px; text-transform:uppercase; }
    .exb-page .field-label .req { color:var(--exb-danger); margin-left:2px; }
    .exb-page .field-help { color:var(--exb-muted); display:block; font-size:11.5px; line-height:1.45; margin-top:6px; }

    .exb-page .fc { appearance:none; -webkit-appearance:none; background:#fff; border:1px solid var(--exb-line); border-radius:var(--exb-radius-sm); color:var(--exb-ink); font-size:13.5px; outline:none; padding:8px 10px; transition:border-color .15s,box-shadow .15s; width:100%; }
    .exb-page .fc:focus { border-color:var(--exb-accent); box-shadow:0 0 0 3px rgba(13,110,253,.15); }
    .exb-page .fc::placeholder { color:#b5bfc9; }
    .exb-page select.fc { background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8'%3E%3Cpath d='M1 1l5 5 5-5' stroke='%238492a6' stroke-width='1.5' fill='none' stroke-linecap='round'/%3E%3C/svg%3E"); background-position:right 10px center; background-repeat:no-repeat; padding-right:28px; }
    .exb-page textarea.fc { min-height:60px; resize:vertical; }
    .exb-page .instructions-textarea { min-height:140px; }
    .exb-page .locked-field { align-items:center; background:var(--exb-soft); border:1px solid var(--exb-line); border-radius:var(--exb-radius-sm); color:var(--exb-ink); display:flex; font-size:13.5px; gap:8px; padding:8px 10px; }
    .exb-page .locked-field i { color:var(--exb-muted); }
    .exb-page .count-badge { background:var(--exb-soft); border:1px solid var(--exb-line); border-radius:var(--exb-radius-sm); color:var(--exb-muted); display:inline-block; font-size:12px; padding:8px 10px; text-align:center; width:100%; }

    /* Section cards */
    .exb-page .section-card { background:#fff; border:1px solid var(--exb-line); border-radius:var(--exb-radius); margin-bottom:20px; }
    .exb-page .section-head { align-items:center; border-bottom:1px solid var(--exb-line); color:var(--exb-ink); display:flex; font-size:13.5px; font-weight:600; gap:8px; justify-content:space-between; padding:14px 20px; }
    .exb-page .section-body { padding:18px 20px; }

    /* Builder toolbar */
    .exb-page .builder-toolbar { display:flex; flex-wrap:wrap; gap:7px; margin-bottom:18px; }
    .exb-page .type-btn { background:var(--exb-soft); border:1px solid var(--exb-line); border-radius:var(--exb-radius-sm); color:var(--exb-muted); cursor:pointer; font-size:12px; padding:6px 12px; transition:border-color .12s,color .12s,background .12s; }
    .exb-page .type-btn:hover { background:var(--exb-accent-light); border-color:var(--exb-accent); color:var(--exb-accent); }

    /* Question cards */
    .exb-page .q-card { border:1px solid var(--exb-line); border-radius:var(--exb-radius); margin-bottom:12px; overflow:hidden; }
    .exb-page .q-card-head { align-items:center; background:var(--exb-soft); border-bottom:1px solid var(--exb-line); color:var(--exb-muted); display:flex; font-size:12.5px; font-weight:600; justify-content:space-between; padding:10px 14px; }
    .exb-page .q-card-body { padding:14px 16px; }
    .exb-page .q-row { display:grid; gap:12px; grid-template-columns:1fr auto; margin-bottom:14px; }
    .exb-page .option-row { align-items:center; display:grid; gap:8px; grid-template-columns:auto 1fr auto; margin-bottom:8px; }
    .exb-page .pair-row { align-items:center; display:grid; gap:8px; grid-template-columns:1fr 1fr auto; margin-bottom:8px; }
    .exb-page .inline-label { color:var(--exb-muted); display:block; font-size:12px; font-weight:600; margin-bottom:6px; }
    .exb-page .builder-empty { border:1px dashed #d3dae3; border-radius:var(--exb-radius-sm); color:var(--exb-muted); font-size:12.5px; padding:26px 18px; text-align:center; }

    .exb-page .exb-btn-ghost { background:var(--exb-soft); border:1px solid var(--exb-line); border-radius:var(--exb-radius-sm); color:var(--exb-ink); cursor:pointer; font-size:12px; padding:5px 10px; transition:border-color .12s,color .12s; }
    .exb-page .exb-btn-ghost:hover { border-color:#cbd5e1; }
    .exb-page .exb-btn-danger { background:none; border:1px solid #fca5a5; border-radius:var(--exb-radius-sm); color:var(--exb-danger); cursor:pointer; font-size:12px; padding:5px 10px; transition:background .12s; }
    .exb-page .exb-btn-danger:hover { background:#fef2f2; }
    .exb-page .exb-btn-danger:disabled { cursor:not-allowed; opacity:.35; }

    /* Sticky submit bar: .content-page has overflow:hidden which kills
       position:sticky, so we use position:fixed pinned to the viewport bottom.
       The left offset matches the sidebar width (240px) and the top bar height
       is already accounted for by anchoring to bottom. The bar sits over the
       page content with a solid background and shadow so questions scroll
       underneath it cleanly. */
    .exb-page .exb-submit-row { align-items:center; background:#fff; border-top:1px solid var(--exb-line); bottom:0; box-shadow:0 -4px 12px rgba(0,0,0,.06); display:flex; flex-wrap:wrap; gap:12px; justify-content:flex-end; left:240px; padding:12px 24px; position:fixed; right:0; z-index:100; }
    .exb-page .exb-submit { background:var(--exb-accent); border:1px solid var(--exb-accent); border-radius:var(--exb-radius-sm); box-shadow:0 6px 16px rgba(13,110,253,.18); color:#fff; cursor:pointer; font-size:13.5px; font-weight:600; padding:9px 22px; transition:background .12s,border-color .12s; }
    .exb-page .exb-submit:hover, .exb-page .exb-submit:focus { background:var(--exb-accent-strong); border-color:var(--exb-accent-strong); color:#fff; text-decoration:none; }
    .exb-page .exb-cancel { color:var(--exb-muted); font-size:13px; font-weight:600; }
    .exb-page .exb-points-note { color:var(--exb-muted); font-size:12.5px; margin-right:auto; }
    .exb-page .exb-points-note strong { color:var(--exb-ink); }
    /* The fixed bar overlaps the last question, so reserve space at the bottom
       of the form to scroll the last question into view. */
    .exb-page .exb-spacer { height:64px; }

    /* The 12 columns are kept at every width; only the spans regroup, so the fields
       stay on a shared grid instead of collapsing into one ragged stack.

       Grid auto-placement fills each row in source order and wraps as soon as an
       item will not fit, so a set of spans only looks tidy if it partitions the
       field order into groups that each total 12. The field order is
         Vacancy, Title, Status, Password, Attempts, Passing, Time Limit,
         Open At, Closes At, Questions
       and each mapping below was chosen to divide exactly:
         <=1199: 6,6 | 3,6,3 | 3,3,6 | 6,6
         <=991:  12 | 12 | 6,6 | 6,6 | 6,6 | 6,6
         <=575:  one field per row */
    @media (max-width:1199px) {
        .exb-page .exb-col-2, .exb-page .exb-col-3 { grid-column:span 3; }
        .exb-page .exb-col-4, .exb-page .exb-col-5 { grid-column:span 6; }
    }
    @media (max-width:991px) {
        .exb-page .exb-col-2, .exb-page .exb-col-3, .exb-page .exb-col-4 { grid-column:span 6; }
        .exb-page .exb-col-5 { grid-column:span 12; }
    }
    @media (max-width:767px) {
        .exb-page .q-row { grid-template-columns:1fr; }
        .exb-page .pair-row { grid-template-columns:1fr; }
        .exb-page .exb-submit-row { justify-content:stretch; }
        .exb-page .exb-submit { width:100%; }
    }
    @media (max-width:575px) {
        .exb-page .exb-col-2, .exb-page .exb-col-3,
        .exb-page .exb-col-4, .exb-page .exb-col-5 { grid-column:span 12; }
    }

    /* GIFT / XML import */
    .exb-page .import-row { display:flex; flex-wrap:wrap; gap:16px; }
    .exb-page .import-col { flex:1 1 320px; min-width:0; }
    .exb-page .upload-zone { align-items:center; background:var(--exb-soft); border:2px dashed #c4cdd9; border-radius:var(--exb-radius-sm); cursor:pointer; display:flex; flex-direction:column; gap:6px; justify-content:center; padding:22px 16px; text-align:center; transition:border-color .15s,background .15s; }
    .exb-page .upload-zone:hover, .exb-page .upload-zone.drag-over, .exb-page .upload-zone:focus { background:var(--exb-accent-light); border-color:var(--exb-accent); outline:none; }
    .exb-page .upload-zone.has-file { border-color:var(--exb-accent); border-style:solid; background:#fff; }
    .exb-page .upload-zone input[type=file] { display:none; }
    .exb-page .upload-icon { color:var(--exb-muted); font-size:26px; }
    .exb-page .upload-title { color:var(--exb-ink); font-size:13px; font-weight:600; }
    .exb-page .upload-sub { color:var(--exb-muted); font-size:12px; }
    .exb-page .fmt-tags { display:flex; gap:6px; justify-content:center; margin-top:4px; }
    .exb-page .fmt-tag { background:#fff; border:1px solid var(--exb-line); border-radius:4px; color:var(--exb-muted); font-size:10.5px; font-weight:600; padding:2px 7px; }
    .exb-page .file-info { align-items:center; background:#fff; border:1px solid var(--exb-line); border-radius:var(--exb-radius-sm); display:none; gap:8px; margin-top:10px; padding:8px 12px; }
    .exb-page .file-info.visible { display:flex; }
    .exb-page .file-info .file-name { color:var(--exb-ink); flex:1; font-size:12.5px; font-weight:600; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
    .exb-page .file-info .file-clear { background:none; border:0; color:var(--exb-danger); cursor:pointer; font-size:12px; font-weight:600; }
    .exb-page .import-actions { align-items:center; display:flex; gap:8px; margin-top:10px; }
    .exb-page .import-btn { background:var(--exb-accent); border:1px solid var(--exb-accent); border-radius:var(--exb-radius-sm); color:#fff; cursor:pointer; font-size:12.5px; font-weight:600; padding:7px 16px; transition:background .12s; }
    .exb-page .import-btn:hover { background:var(--exb-accent-strong); border-color:var(--exb-accent-strong); }
    .exb-page .import-btn:disabled { cursor:not-allowed; opacity:.5; }
    .exb-page .import-status { border-radius:var(--exb-radius-sm); display:none; font-size:12.5px; line-height:1.5; margin-top:10px; padding:10px 12px; }
    .exb-page .import-status.visible { display:block; }
    .exb-page .import-status.loading { background:var(--exb-accent-light); color:var(--exb-accent-strong); }
    .exb-page .import-status.success { background:#e7f5ed; color:#1a7a4c; }
    .exb-page .import-status.error { background:#fbeaea; color:#b03636; }
    .exb-page .import-status ul { margin:6px 0 0; padding-left:18px; }
    .exb-page .imported-badge { background:var(--exb-accent-light); border-radius:4px; color:var(--exb-accent-strong); font-size:10.5px; font-weight:600; margin-left:6px; padding:1px 6px; }

    /* Sticky submit bar: sidebar-aware left offset. The sidebar is 240px by
       default, 70px when the body has .enlarged (collapsed sidebar), and 0 on
       mobile. The fixed bar must track each so it always spans the content area. */
    body.enlarged .exb-page .exb-submit-row { left:70px; }
    @media (max-width:767.98px) {
        .exb-page .exb-submit-row { left:0 !important; padding:10px 14px; }
        .exb-page .exb-spacer { height:56px; }
    }
</style>

<div class="content-page exb-page">
    <div class="content">
        <div class="container-fluid">

            <div class="exb-hero">
                <div class="exb-hero-layout">
                    <div class="exb-hero-avatar"><i class="mdi mdi-clipboard-text-outline"></i></div>
                    <div class="exb-hero-main">
                        <div class="exb-hero-eyebrow"><i class="mdi mdi-briefcase-outline mr-1"></i> Recruitment</div>
                        <h3><?= $isEdit ? 'Edit Exam' : 'New Exam'; ?></h3>
                        <p class="exb-hero-sub">
                            <?php if ($isEdit) : ?>
                                Update the settings and question bank. Saving replaces the stored bank with what is in the builder below.
                            <?php else : ?>
                                Pick the vacancy this exam belongs to, then build its question bank. One exam belongs to one vacancy.
                            <?php endif; ?>
                        </p>
                    </div>
                </div>
                <div>
                    <a href="<?= base_url('secretariat/exams' . ($curJobId > 0 ? '?job_id=' . $curJobId : '')); ?>" class="exb-hero-chip">
                        <i class="mdi mdi-arrow-left"></i> Back to exams
                    </a>
                </div>
            </div>

            <?php foreach (['success' => 'alert-success', 'danger' => 'alert-danger'] as $flash => $class) : ?>
                <?php if ($this->session->flashdata($flash)) : ?>
                    <div class="alert <?= $class; ?> alert-dismissible fade show" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <?= $ex_h($this->session->flashdata($flash)); ?>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>

            <form method="post" action="<?= $formAction; ?>" id="examForm">

                <!--
                    12-column rows:
                      Vacancy 5 + Title 5 + Status 2
                      Password 4 + Attempts 2 + Passing Score 3 + Time Limit 3
                      Open At 4 + Closes At 4 + Questions 4
                -->
                <div class="settings-strip">
                    <!-- Vacancy: the class-section slot of the college build -->
                    <div class="exb-col-5">
                        <span class="field-label">Vacancy <span class="req">*</span></span>
                        <?php if ($isEdit) : ?>
                            <div class="locked-field">
                                <i class="mdi mdi-lock-outline"></i>
                                <span><?= $ex_h($exam->vacancy_title ?: $exam->job_title); ?></span>
                            </div>
                            <span class="field-help">Fixed once the exam exists &mdash; the question bank was written for this position.</span>
                        <?php else : ?>
                            <select name="job_id" id="jobId" class="fc" required>
                                <option value="">Select a vacancy</option>
                                <?php foreach ($vacancies as $vacancy) : ?>
                                    <?php $jobId = (int) $vacancy->jobID; ?>
                                    <option value="<?= $jobId; ?>" <?= $jobId === $curJobId ? 'selected' : ''; ?>>
                                        <?= $ex_h($vacancy->jobTitle . ' — ' . ($positionGroups[(int) $vacancy->position] ?? 'Vacancy') . ' — FY ' . $vacancy->sy); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <span class="field-help">Only the open vacancies assigned to your Secretariat account are listed.</span>
                        <?php endif; ?>
                    </div>

                    <div class="exb-col-5">
                        <span class="field-label">Title <span class="req">*</span></span>
                        <input type="text" name="title" class="fc" placeholder="e.g. Written Examination - Set A" value="<?= $ex_h($curTitle); ?>" required>
                    </div>

                    <div class="exb-col-2">
                        <span class="field-label">Status</span>
                        <select name="status" class="fc">
                            <option value="published" <?= $curStatus === 'published' ? 'selected' : ''; ?>>Published</option>
                            <option value="draft" <?= $curStatus === 'draft' ? 'selected' : ''; ?>>Draft</option>
                        </select>
                    </div>

                    <div class="exb-col-4">
                        <span class="field-label">Password <span class="req">*</span></span>
                        <input type="text" name="exam_password" id="examPassword" class="fc" value="<?= $ex_h($curPassword); ?>" placeholder="e.g. AOII-2026-SETA" required>
                        <span class="field-help">The applicant's entry point &mdash; this is what they key in to start the exam. Kept in plain text here so you can read it out or print it.</span>
                    </div>

                    <div class="exb-col-2">
                        <span class="field-label">Attempts</span>
                        <select name="attempt_limit" class="fc">
                            <option value="1" <?= $curAttemptLimit === '1' ? 'selected' : ''; ?>>1</option>
                            <option value="2" <?= $curAttemptLimit === '2' ? 'selected' : ''; ?>>2</option>
                            <option value="3" <?= $curAttemptLimit === '3' ? 'selected' : ''; ?>>3</option>
                            <option value="0" <?= $curAttemptLimit === '0' ? 'selected' : ''; ?>>Unlimited</option>
                        </select>
                    </div>

                    <div class="exb-col-3">
                        <span class="field-label">Passing Score</span>
                        <input type="number" name="passing_score" class="fc" value="<?= $ex_h($curPassingScore); ?>" min="0" step="0.01" placeholder="Optional">
                    </div>

                    <div class="exb-col-3">
                        <span class="field-label">Time Limit (mins)</span>
                        <select name="time_limit_minutes" id="timeLimitMinutes" class="fc">
                            <option value="" <?= $curTimeLimit === '' ? 'selected' : ''; ?>>No limit</option>
                            <?php foreach ($timeLimitPresets as $preset) : ?>
                                <option value="<?= $preset; ?>" <?= $curTimeLimit === $preset ? 'selected' : ''; ?>>
                                    <?= $preset; ?> minutes<?= (int) $preset >= 60 ? ' (' . rtrim(rtrim(number_format((int) $preset / 60, 1, '.', ''), '0'), '.') . ' hr)' : ''; ?>
                                </option>
                            <?php endforeach; ?>
                            <option value="custom" <?= $timeLimitIsCustom ? 'selected' : ''; ?>>Custom</option>
                        </select>
                        <input type="number" name="time_limit_minutes_custom" id="timeLimitMinutesCustom" class="fc mt-2"
                               value="<?= $timeLimitIsCustom ? $ex_h($curTimeLimit) : ''; ?>"
                               min="1" placeholder="Enter custom minutes" style="<?= $timeLimitIsCustom ? '' : 'display:none;'; ?>">
                    </div>

                    <div class="exb-col-4">
                        <span class="field-label">Open At</span>
                        <input type="datetime-local" name="open_at" id="openAt" class="fc" value="<?= $ex_h($curOpenAt); ?>">
                        <span class="field-help">Optional. Before this moment the password will not let anyone in.</span>
                    </div>

                    <div class="exb-col-4">
                        <span class="field-label">Closes At</span>
                        <input type="datetime-local" name="close_at" class="fc" value="<?= $ex_h($curCloseAt); ?>">
                        <span class="field-help">Optional. Must fall after Open At.</span>
                    </div>

                    <div class="exb-col-4">
                        <span class="field-label">Questions</span>
                        <div class="count-badge" id="questionCount">0 questions</div>
                    </div>
                </div>

                <div class="section-card">
                    <div class="section-head">Instructions</div>
                    <div class="section-body">
                        <textarea name="instructions" rows="6" class="fc instructions-textarea"
                                  placeholder="Shown to the applicant before the exam starts."><?= $ex_h($curInstructions); ?></textarea>
                    </div>
                </div>

                <div class="section-card">
                    <div class="section-head">GIFT / XML Import</div>
                    <div class="section-body">
                        <div class="field-help" style="margin:0 0 14px;">
                            Upload a <code>.txt</code>, <code>.gift</code>, or <code>.xml</code> question bank, or paste GIFT / XML content below, then click <strong>Preview import</strong>. The parsed questions appear in the builder underneath, where you can review and edit them before saving.
                        </div>
                        <div class="import-row">
                            <div class="import-col">
                                <span class="field-label">Upload file</span>
                                <div class="upload-zone" id="uploadZone" tabindex="0" role="button" aria-label="Upload question bank file">
                                    <input type="file" name="gift_file" id="giftFileInput" accept=".txt,.gift,.xml,text/xml,application/xml">
                                    <i class="mdi mdi-upload-outline upload-icon"></i>
                                    <div class="upload-title"><span>Click to browse</span> or drag &amp; drop</div>
                                    <div class="upload-sub">Your question bank file</div>
                                    <div class="fmt-tags">
                                        <span class="fmt-tag">.TXT</span>
                                        <span class="fmt-tag">.GIFT</span>
                                        <span class="fmt-tag">.XML</span>
                                    </div>
                                </div>
                                <div class="file-info" id="fileInfo">
                                    <i class="mdi mdi-file-document-outline" style="color:var(--exb-accent);"></i>
                                    <span class="file-name" id="fileNameEl">filename.gift</span>
                                    <button type="button" class="file-clear" id="fileClear">Remove</button>
                                </div>
                            </div>
                            <div class="import-col">
                                <span class="field-label">Or paste GIFT / XML content</span>
                                <textarea name="gift_text" id="giftText" rows="8" class="fc" placeholder="Paste your GIFT or XML question bank here&#10;&#10;::Question 1:: What is 2+2? { =4 ~3 ~5 }"></textarea>
                                <div class="import-actions">
                                    <button type="button" class="import-btn" id="previewImportBtn"><i class="mdi mdi-magnify-scan mr-1"></i> Preview import</button>
                                    <small style="color:var(--exb-muted);font-size:12px;">Supports GIFT and Moodle XML.</small>
                                </div>
                            </div>
                        </div>
                        <div class="import-status" id="importStatus" role="status" aria-live="polite"></div>
                    </div>
                </div>

                <div class="section-card">
                    <div class="section-head">
                        <span>Question Builder</span>
                        <span class="text-muted" style="font-size:12px;font-weight:400;" id="pointsTotal">0 points</span>
                    </div>
                    <div class="section-body">
                        <div class="field-help" style="margin:0 0 14px;">
                            Add questions, set their points, and mark the correct answers. Everything in this builder is what gets saved.
                        </div>
                        <div class="builder-toolbar">
                            <button type="button" class="type-btn js-add-question" data-type="single_choice">+ Single Choice</button>
                            <button type="button" class="type-btn js-add-question" data-type="multiple_choice">+ Multiple Choice</button>
                            <button type="button" class="type-btn js-add-question" data-type="true_false">+ True / False</button>
                            <button type="button" class="type-btn js-add-question" data-type="short_answer">+ Short Answer</button>
                            <button type="button" class="type-btn js-add-question" data-type="matching">+ Matching</button>
                            <button type="button" class="type-btn js-add-question" data-type="essay">+ Essay</button>
                        </div>
                        <div id="questionBuilder"></div>
                        <input type="hidden" name="questions_json" id="questionsJson">
                    </div>
                </div>

                <div class="alert alert-warning" id="noQuestionAlert" role="alert" style="display:none;">
                    Add at least one question before saving this exam. Nothing you typed has been lost.
                </div>

                <div class="exb-submit-row">
                    <span class="exb-points-note"><strong id="pointsSummary">0</strong> total points across <strong id="questionSummary">0</strong> questions</span>
                    <a href="<?= base_url('secretariat/exams' . ($curJobId > 0 ? '?job_id=' . $curJobId : '')); ?>" class="exb-cancel">Cancel</a>
                    <button type="submit" class="exb-submit"><?= $isEdit ? 'Save Exam' : 'Create Exam'; ?></button>
                </div>
                <div class="exb-spacer"></div>
            </form>

        </div>
    </div>
</div>

<script>
    (function () {
        'use strict';

        var restoredQuestions = [];
        try {
            // JSON_HEX_TAG matters: a saved prompt is free to contain a literal
            // closing script tag, which would otherwise end this block early.
            // (The HTML parser does not care that it sits inside a JS string.)
            restoredQuestions = JSON.parse(<?= json_encode((string) $restored, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>) || [];
        } catch (err) {
            restoredQuestions = [];
        }
        if (!Array.isArray(restoredQuestions)) {
            restoredQuestions = [];
        }

        var builder = document.getElementById('questionBuilder');
        var hidden = document.getElementById('questionsJson');
        var countField = document.getElementById('questionCount');
        var pointsTotal = document.getElementById('pointsTotal');
        var pointsSummary = document.getElementById('pointsSummary');
        var questionSummary = document.getElementById('questionSummary');
        var questions = restoredQuestions.slice();

        function uid() {
            return 'c_' + Math.random().toString(36).slice(2, 10);
        }

        function toEntries(obj) {
            return Object.entries(obj || {});
        }

        var defaults = {
            single_choice: function () {
                return { question_type: 'single_choice', prompt: '', points: 1, choices: [{ id: uid(), text: '' }, { id: uid(), text: '' }], answer_key: [] };
            },
            multiple_choice: function () {
                return { question_type: 'multiple_choice', prompt: '', points: 1, choices: [{ id: uid(), text: '' }, { id: uid(), text: '' }], answer_key: [] };
            },
            true_false: function () {
                return { question_type: 'true_false', prompt: '', points: 1, answer_key: ['true'] };
            },
            short_answer: function () {
                return { question_type: 'short_answer', prompt: '', points: 1, answer_key: [''] };
            },
            matching: function () {
                return { question_type: 'matching', prompt: '', points: 1, answer_key: { 'Pair 1': '', 'Pair 2': '' } };
            },
            essay: function () {
                return { question_type: 'essay', prompt: '', points: 1, answer_key: [] };
            }
        };

        var labels = {
            single_choice: 'Single Choice',
            multiple_choice: 'Multiple Choice',
            true_false: 'True / False',
            short_answer: 'Short Answer',
            matching: 'Matching',
            essay: 'Essay'
        };

        function labelFor(type) {
            return labels[type] || type;
        }

        function escapeHtml(v) {
            return String(v === null || v === undefined ? '' : v)
                .replace(/&/g, '&amp;').replace(/</g, '&lt;')
                .replace(/>/g, '&gt;').replace(/"/g, '&quot;');
        }

        // Empty choices, blank accepted answers and half-filled pairs are dropped
        // on the way out: the server rejects those, so the count shown here must
        // match what the server will actually count.
        function normalizeQuestion(q) {
            var out = Object.assign({}, q);
            delete out._imported;
            if (q.question_type === 'matching') {
                var cleaned = {};
                toEntries(q.answer_key).forEach(function (pair) {
                    if (String(pair[0] || '').trim() && String(pair[1] || '').trim()) {
                        cleaned[pair[0]] = pair[1];
                    }
                });
                out.answer_key = cleaned;
                return out;
            }
            if (q.question_type === 'short_answer') {
                out.answer_key = (q.answer_key || []).filter(function (a) { return String(a || '').trim() !== ''; });
                return out;
            }
            if (q.question_type === 'single_choice' || q.question_type === 'multiple_choice') {
                out.choices = (q.choices || []).filter(function (c) { return String(c.text || '').trim() !== ''; });
                out.answer_key = (q.answer_key || []).filter(Boolean);
                return out;
            }
            return out;
        }

        function syncHidden() {
            var payload = questions.map(normalizeQuestion);
            hidden.value = JSON.stringify(payload);

            var points = payload.reduce(function (sum, q) {
                return sum + (Number(q.points) || 0);
            }, 0);
            var rounded = Math.round(points * 100) / 100;

            countField.textContent = questions.length + ' question' + (questions.length === 1 ? '' : 's');
            pointsTotal.textContent = rounded + ' point' + (rounded === 1 ? '' : 's');
            pointsSummary.textContent = rounded;
            questionSummary.textContent = questions.length;
        }

        function renderBody(q, i) {
            if (q.question_type === 'single_choice' || q.question_type === 'multiple_choice') {
                var inputType = q.question_type === 'single_choice' ? 'radio' : 'checkbox';
                var rows = (q.choices || []).map(function (c) {
                    return '<div class="option-row">' +
                        '<input type="' + inputType + '" ' + ((q.answer_key || []).indexOf(c.id) !== -1 ? 'checked' : '') + ' data-action="toggle-choice" data-index="' + i + '" data-choice="' + escapeHtml(c.id) + '">' +
                        '<input type="text" class="fc" value="' + escapeHtml(c.text) + '" data-action="choice-text" data-index="' + i + '" data-choice="' + escapeHtml(c.id) + '" placeholder="Choice text">' +
                        '<button type="button" class="exb-btn-danger" data-action="remove-choice" data-index="' + i + '" data-choice="' + escapeHtml(c.id) + '" ' + ((q.choices || []).length <= 2 ? 'disabled' : '') + '>&times;</button>' +
                        '</div>';
                }).join('');
                return '<span class="inline-label">Choices</span>' + rows +
                    '<button type="button" class="exb-btn-ghost" data-action="add-choice" data-index="' + i + '">+ Add choice</button>';
            }
            if (q.question_type === 'true_false') {
                var cur = (q.answer_key || [])[0] || 'true';
                return '<span class="inline-label">Correct Answer</span>' +
                    '<div style="display:flex;gap:20px;margin-top:4px;">' +
                    '<label style="font-size:13.5px;display:flex;align-items:center;gap:6px;cursor:pointer;margin:0;"><input type="radio" name="tf_' + i + '" value="true" ' + (cur === 'true' ? 'checked' : '') + ' data-action="set-true-false" data-index="' + i + '"> True</label>' +
                    '<label style="font-size:13.5px;display:flex;align-items:center;gap:6px;cursor:pointer;margin:0;"><input type="radio" name="tf_' + i + '" value="false" ' + (cur === 'false' ? 'checked' : '') + ' data-action="set-true-false" data-index="' + i + '"> False</label>' +
                    '</div>';
            }
            if (q.question_type === 'short_answer') {
                var answers = (q.answer_key || []).map(function (a, ai) {
                    return '<div class="option-row">' +
                        '<span style="font-size:11px;color:#6d7885;font-weight:600;">#' + (ai + 1) + '</span>' +
                        '<input type="text" class="fc" value="' + escapeHtml(a) + '" data-action="short-answer" data-index="' + i + '" data-answer-index="' + ai + '" placeholder="Accepted answer">' +
                        '<button type="button" class="exb-btn-danger" data-action="remove-short-answer" data-index="' + i + '" data-answer-index="' + ai + '" ' + ((q.answer_key || []).length <= 1 ? 'disabled' : '') + '>&times;</button>' +
                        '</div>';
                }).join('');
                return '<span class="inline-label">Accepted Answers</span>' + answers +
                    '<button type="button" class="exb-btn-ghost" data-action="add-short-answer" data-index="' + i + '">+ Add answer</button>';
            }
            if (q.question_type === 'matching') {
                var pairs = toEntries(q.answer_key).map(function (pair, pi) {
                    return '<div class="pair-row">' +
                        '<input type="text" class="fc" value="' + escapeHtml(pair[0]) + '" data-action="pair-left" data-index="' + i + '" data-pair-index="' + pi + '" placeholder="Left prompt">' +
                        '<input type="text" class="fc" value="' + escapeHtml(pair[1]) + '" data-action="pair-right" data-index="' + i + '" data-pair-index="' + pi + '" placeholder="Right answer">' +
                        '<button type="button" class="exb-btn-danger" data-action="remove-pair" data-index="' + i + '" data-pair-index="' + pi + '" ' + (toEntries(q.answer_key).length <= 2 ? 'disabled' : '') + '>&times;</button>' +
                        '</div>';
                }).join('');
                return '<span class="inline-label">Matching Pairs</span>' + pairs +
                    '<button type="button" class="exb-btn-ghost" data-action="add-pair" data-index="' + i + '">+ Add pair</button>';
            }
            if (q.question_type === 'essay') {
                return '<div class="text-muted" style="font-size:12px;">An essay answer is stored for manual review &mdash; it is not graded automatically.</div>';
            }
            return '';
        }

        function render() {
            var alertBox = document.getElementById('noQuestionAlert');
            if (alertBox && questions.length > 0) {
                alertBox.style.display = 'none';
            }

            if (questions.length === 0) {
                builder.innerHTML = '<div class="builder-empty">No questions yet. Add one with the buttons above.</div>';
            } else {
                builder.innerHTML = questions.map(function (q, i) {
                    var importedBadge = q._imported ? '<span class="imported-badge">Imported preview</span>' : '';
                    return '<div class="q-card">' +
                        '<div class="q-card-head">' +
                        '<span>Q' + (i + 1) + ' &middot; ' + labelFor(q.question_type) + importedBadge + '</span>' +
                        '<button type="button" class="exb-btn-danger" data-action="remove-question" data-index="' + i + '">Remove</button>' +
                        '</div>' +
                        '<div class="q-card-body">' +
                        '<div style="margin-bottom:14px;">' +
                        '<span class="inline-label">Question Name <span style="font-weight:400;">(optional)</span></span>' +
                        '<input type="text" class="fc" data-action="question-name" data-index="' + i + '" value="' + escapeHtml(q.question_name) + '" placeholder="Internal question name">' +
                        '</div>' +
                        '<div class="q-row">' +
                        '<div><span class="inline-label">Prompt</span>' +
                        '<textarea class="fc" rows="2" data-action="prompt" data-index="' + i + '">' + escapeHtml(q.prompt) + '</textarea></div>' +
                        '<div style="width:90px;"><span class="inline-label">Points</span>' +
                        '<input type="number" min="0" step="0.25" class="fc" data-action="points" data-index="' + i + '" value="' + escapeHtml(q.points) + '"></div>' +
                        '</div>' +
                        renderBody(q, i) +
                        '</div></div>';
                }).join('');
            }

            syncHidden();
        }

        Array.prototype.forEach.call(document.querySelectorAll('.js-add-question'), function (btn) {
            btn.addEventListener('click', function () {
                questions.push(defaults[btn.dataset.type]());
                render();
            });
        });

        builder.addEventListener('click', function (e) {
            var btn = e.target.closest('button[data-action]');
            if (!btn) {
                return;
            }

            var action = btn.dataset.action;
            var i = Number(btn.dataset.index);
            var q = questions[i];

            if (action === 'remove-question') {
                questions.splice(i, 1);
                render();
                return;
            }
            if (!q) {
                return;
            }

            if (action === 'add-choice') {
                q.choices.push({ id: uid(), text: '' });
            }
            if (action === 'remove-choice') {
                q.choices = q.choices.filter(function (c) { return c.id !== btn.dataset.choice; });
                q.answer_key = (q.answer_key || []).filter(function (id) { return id !== btn.dataset.choice; });
            }
            if (action === 'add-short-answer') {
                q.answer_key.push('');
            }
            if (action === 'remove-short-answer') {
                q.answer_key.splice(Number(btn.dataset.answerIndex), 1);
            }
            if (action === 'add-pair') {
                q.answer_key['Pair ' + (toEntries(q.answer_key).length + 1)] = '';
            }
            if (action === 'remove-pair') {
                var entries = toEntries(q.answer_key);
                entries.splice(Number(btn.dataset.pairIndex), 1);
                q.answer_key = Object.fromEntries(entries);
            }

            render();
        });

        builder.addEventListener('input', function (e) {
            var t = e.target;
            var action = t.dataset.action;
            var q = questions[Number(t.dataset.index)];
            if (!q || !action) {
                return;
            }

            if (action === 'question-name') {
                q.question_name = t.value;
            }
            if (action === 'prompt') {
                q.prompt = t.value;
            }
            if (action === 'points') {
                q.points = Number(t.value || 0);
            }
            if (action === 'choice-text') {
                var choice = (q.choices || []).find(function (c) { return c.id === t.dataset.choice; });
                if (choice) {
                    choice.text = t.value;
                }
            }
            if (action === 'short-answer') {
                q.answer_key[Number(t.dataset.answerIndex)] = t.value;
            }
            // Renaming a pair's left side rebuilds the whole map: the left text is
            // the key, so the insertion order has to be preserved by hand.
            if (action === 'pair-left' || action === 'pair-right') {
                var entries = toEntries(q.answer_key);
                var pair = entries[Number(t.dataset.pairIndex)];
                if (!pair) {
                    return;
                }
                if (action === 'pair-left') {
                    pair[0] = t.value;
                } else {
                    pair[1] = t.value;
                }
                q.answer_key = Object.fromEntries(entries);
            }

            syncHidden();
        });

        builder.addEventListener('change', function (e) {
            var t = e.target;
            var action = t.dataset.action;
            var q = questions[Number(t.dataset.index)];
            if (!q || !action) {
                return;
            }

            if (action === 'toggle-choice') {
                if (q.question_type === 'single_choice') {
                    q.answer_key = t.checked ? [t.dataset.choice] : [];
                } else if (t.checked && (q.answer_key || []).indexOf(t.dataset.choice) === -1) {
                    q.answer_key.push(t.dataset.choice);
                } else if (!t.checked) {
                    q.answer_key = (q.answer_key || []).filter(function (id) { return id !== t.dataset.choice; });
                }
                render();
                return;
            }
            if (action === 'set-true-false') {
                q.answer_key = [t.value];
                render();
            }
        });

        // Custom time limit
        var timeLimitSelect = document.getElementById('timeLimitMinutes');
        var timeLimitCustom = document.getElementById('timeLimitMinutesCustom');
        if (timeLimitSelect && timeLimitCustom) {
            timeLimitSelect.addEventListener('change', function () {
                var isCustom = timeLimitSelect.value === 'custom';
                timeLimitCustom.style.display = isCustom ? 'block' : 'none';
                if (isCustom) {
                    timeLimitCustom.focus();
                }
            });
        }

        // Stop an empty bank at the form rather than losing the typed settings to
        // a redirect round-trip.
        document.getElementById('examForm').addEventListener('submit', function (e) {
            if (questions.length === 0) {
                e.preventDefault();
                var alertBox = document.getElementById('noQuestionAlert');
                if (alertBox) {
                    alertBox.style.display = 'block';
                    alertBox.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }
        });

        // ── Open At gates access instead of a password ───────────────────────
        // When Open At is set, the schedule is the gate, so the password becomes
        // optional and the examinee can enter directly once the window opens.
        var openAtField = document.getElementById('openAt');
        var examPasswordField = document.getElementById('examPassword');

        function syncPasswordRequirement() {
            if (!openAtField || !examPasswordField) {
                return;
            }
            var hasOpenAt = String(openAtField.value || '').trim() !== '';
            examPasswordField.required = !hasOpenAt;
            var help = examPasswordField.parentNode.querySelector('.field-help');
            if (help) {
                help.innerHTML = hasOpenAt
                    ? 'Optional &mdash; an Open At window is set, so applicants can enter without a password once the exam opens. Set one only if you want an extra gate.'
                    : 'The applicant\'s entry point &mdash; this is what they key in to start the exam. Kept in plain text here so you can read it out or print it.';
            }
            var label = examPasswordField.parentNode.querySelector('.field-label');
            if (label) {
                var req = label.querySelector('.req');
                if (req) {
                    req.style.display = hasOpenAt ? 'none' : '';
                }
            }
        }

        if (openAtField) {
            openAtField.addEventListener('input', syncPasswordRequirement);
            openAtField.addEventListener('change', syncPasswordRequirement);
            syncPasswordRequirement();
        }

        // ── GIFT / XML import ────────────────────────────────────────────────
        var uploadZone = document.getElementById('uploadZone');
        var giftFileInput = document.getElementById('giftFileInput');
        var fileInfo = document.getElementById('fileInfo');
        var fileNameEl = document.getElementById('fileNameEl');
        var fileClear = document.getElementById('fileClear');
        var giftText = document.getElementById('giftText');
        var previewImportBtn = document.getElementById('previewImportBtn');
        var importStatus = document.getElementById('importStatus');
        var importPreviewUrl = <?= json_encode(site_url('secretariat/exams/preview-import')); ?>;
        var selectedFile = null;

        function setImportStatus(kind, html) {
            if (!importStatus) return;
            importStatus.className = 'import-status visible ' + kind;
            importStatus.innerHTML = html;
        }

        function clearImportStatus() {
            if (!importStatus) return;
            importStatus.className = 'import-status';
            importStatus.innerHTML = '';
        }

        function showFile(name) {
            if (!uploadZone) return;
            uploadZone.classList.add('has-file');
            if (fileNameEl) fileNameEl.textContent = name;
            if (fileInfo) fileInfo.classList.add('visible');
        }

        function hideFile() {
            selectedFile = null;
            if (giftFileInput) giftFileInput.value = '';
            if (uploadZone) uploadZone.classList.remove('has-file');
            if (fileInfo) fileInfo.classList.remove('visible');
        }

        if (giftFileInput) {
            giftFileInput.addEventListener('change', function () {
                selectedFile = giftFileInput.files && giftFileInput.files[0] ? giftFileInput.files[0] : null;
                if (selectedFile) {
                    showFile(selectedFile.name);
                    clearImportStatus();
                    // Auto-trigger preview on file selection, like the college build.
                    runPreviewImport(selectedFile);
                } else {
                    hideFile();
                }
            });
        }

        if (fileClear) {
            fileClear.addEventListener('click', function (e) {
                e.stopPropagation();
                hideFile();
            });
        }

        if (uploadZone) {
            // Clicking anywhere on the zone opens the file picker. The input is
            // display:none, so without this the zone is not clickable at all.
            uploadZone.addEventListener('click', function (e) {
                if (e.target === fileClear || (fileClear && fileClear.contains(e.target))) return;
                if (giftFileInput) giftFileInput.click();
            });
            uploadZone.addEventListener('keydown', function (e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    if (giftFileInput) giftFileInput.click();
                }
            });
            uploadZone.addEventListener('dragover', function (e) {
                e.preventDefault();
                uploadZone.classList.add('drag-over');
            });
            uploadZone.addEventListener('dragleave', function () {
                uploadZone.classList.remove('drag-over');
            });
            uploadZone.addEventListener('drop', function (e) {
                e.preventDefault();
                uploadZone.classList.remove('drag-over');
                if (e.dataTransfer && e.dataTransfer.files && e.dataTransfer.files[0]) {
                    var dropped = e.dataTransfer.files[0];
                    var dt = new DataTransfer();
                    dt.items.add(dropped);
                    if (giftFileInput) giftFileInput.files = dt.files;
                    selectedFile = dropped;
                    showFile(selectedFile.name);
                    clearImportStatus();
                    runPreviewImport(selectedFile);
                }
            });
        }

        // Map a parser question into the builder's shape. Choice ids from the
        // parser are stable strings, which the builder handles just fine.
        function importQuestion(q) {
            var out = {
                question_name: String(q.gift_name || ''),
                question_type: String(q.question_type || ''),
                prompt: String(q.prompt || ''),
                points: Number(q.points) || 1,
                choices: [],
                answer_key: q.answer_key
            };
            if (q.question_type === 'single_choice' || q.question_type === 'multiple_choice') {
                out.choices = (q.choices || []).map(function (c) {
                    return { id: String(c.id || uid()), text: String(c.text || '') };
                });
            } else if (q.question_type === 'true_false') {
                out.choices = [
                    { id: 'true', text: 'True' },
                    { id: 'false', text: 'False' }
                ];
            }
            return out;
        }

        function runPreviewImport(fileArg) {
            clearImportStatus();
            var formData = new FormData();
            var hasSource = false;
            var fileForImport = fileArg || selectedFile;
            if (giftText && String(giftText.value || '').trim() !== '') {
                formData.append('gift_text', giftText.value);
                hasSource = true;
            }
            if (fileForImport) {
                formData.append('gift_file', fileForImport, fileForImport.name);
                hasSource = true;
            }
            if (!hasSource) {
                setImportStatus('error', 'Choose a question bank file or paste GIFT / XML content first.');
                return;
            }

            if (previewImportBtn) previewImportBtn.disabled = true;
            setImportStatus('loading', '<i class="mdi mdi-loading mdi-spin mr-1"></i> Parsing question bank…');

            fetch(importPreviewUrl, {
                method: 'POST',
                body: formData,
                credentials: 'same-origin',
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
                .then(function (res) {
                    var ct = res.headers.get('content-type') || '';
                    if (!ct.includes('application/json')) {
                        throw new Error('Your session may have expired. Reload the page and try again.');
                    }
                    return res.json();
                })
                .then(function (data) {
                    if (!data || !data.ok) {
                        var msg = (data && data.message) ? data.message : 'The import could not be parsed.';
                        var warnings = (data && data.warnings && data.warnings.length) ? '<ul>' + data.warnings.map(function (w) { return '<li>' + escapeHtml(w) + '</li>'; }).join('') + '</ul>' : '';
                        setImportStatus('error', escapeHtml(msg) + warnings);
                        return;
                    }
                    var imported = (data.questions || []).map(function (q) {
                        var mapped = importQuestion(q);
                        mapped._imported = true;
                        return mapped;
                    });
                    if (imported.length === 0) {
                        setImportStatus('error', 'No valid questions were found in this import.');
                        return;
                    }
                    // Append so a manual bank already in the builder is kept.
                    var firstImportedIndex = questions.length;
                    imported.forEach(function (q) { questions.push(q); });
                    render();
                    var warnHtml = (data.warnings && data.warnings.length)
                        ? '<ul>' + data.warnings.map(function (w) { return '<li>' + escapeHtml(w) + '</li>'; }).join('') + '</ul>'
                        : '';
                    setImportStatus('success', '<i class="mdi mdi-check-circle-outline mr-1"></i> Imported ' + imported.length + ' question' + (imported.length === 1 ? '' : 's') + ' into the builder below.' + warnHtml);
                    // Scroll to the first imported question so the preview is visible.
                    var builderEl = document.getElementById('questionBuilder');
                    if (builderEl && builderEl.children[firstImportedIndex]) {
                        builderEl.children[firstImportedIndex].scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }
                    // Clear the file input after a successful file import so a second
                    // import doesn't re-send the same file.
                    if (fileForImport) {
                        hideFile();
                    }
                })
                .catch(function (err) {
                    setImportStatus('error', escapeHtml(err && err.message ? err.message : 'The preview request failed. Please try again.'));
                })
                .finally(function () {
                    if (previewImportBtn) previewImportBtn.disabled = false;
                });
        }

        if (previewImportBtn) {
            previewImportBtn.addEventListener('click', function () { runPreviewImport(); });
        }

        render();
    })();
</script>
