<?php
$ex_h = static function ($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
};

$positionGroups = [
    1 => 'Teaching',
    2 => 'School Administration',
    3 => 'Related Teaching',
    4 => 'Non-Teaching',
];

$vacancies = $vacancies ?? [];
$grouped = $grouped ?? [];
$selectedVacancy = $selectedVacancy ?? null;
$selectedJobId = (int) ($selectedJobId ?? 0);
$totals = array_merge(['exams' => 0, 'published' => 0, 'draft' => 0, 'questions' => 0, 'points' => 0.0], $totals ?? []);

// A draft carrying a reached Open At is effectively open; showing it as "Draft"
// while applicants can already sit it would misreport the exam's real state.
$examState = static function ($exam) {
    $status = (string) $exam->status;
    $openAt = trim((string) $exam->open_at);
    $closeAt = trim((string) $exam->close_at);

    if ($status === 'draft') {
        if ($openAt !== '' && ($stamp = strtotime($openAt)) && time() >= $stamp) {
            return ['label' => 'Open', 'class' => 'open', 'icon' => 'mdi-check-circle'];
        }
        if ($openAt !== '') {
            return ['label' => 'Scheduled', 'class' => 'scheduled', 'icon' => 'mdi-clock-outline'];
        }
        return ['label' => 'Draft', 'class' => 'draft', 'icon' => 'mdi-pencil-outline'];
    }

    if ($closeAt !== '' && ($stamp = strtotime($closeAt)) && time() > $stamp) {
        return ['label' => 'Closed', 'class' => 'closed', 'icon' => 'mdi-lock-outline'];
    }

    if ($openAt !== '' && ($stamp = strtotime($openAt)) && time() < $stamp) {
        return ['label' => 'Scheduled', 'class' => 'scheduled', 'icon' => 'mdi-clock-outline'];
    }

    return ['label' => 'Published', 'class' => 'open', 'icon' => 'mdi-check-circle'];
};

$fmtStamp = static function ($value) {
    $value = trim((string) $value);
    if ($value === '' || strpos($value, '0000-00-00') === 0) {
        return '';
    }
    $stamp = strtotime($value);
    return $stamp ? date('M j, Y g:i A', $stamp) : $value;
};

$fmtPoints = static function ($value) {
    return rtrim(rtrim(number_format((float) $value, 2, '.', ','), '0'), '.');
};
?>

<style>
    /* Above .content-page's reserved 65px, or the absolutely-positioned footer in
       templates/footer.php draws over the last row. */
    .ex-page { --ex-ink:#1f2a37; --ex-muted:#6d7885; --ex-line:#e6e9ef; --ex-accent:#0d6efd; --ex-soft:#f7f8fb; padding-bottom:90px; }
    .ex-page .container-fluid { max-width:1280px; }

    .ex-page .ex-head { align-items:flex-start; border-bottom:1px solid var(--ex-line); display:flex; flex-wrap:wrap; gap:14px; justify-content:space-between; margin-bottom:20px; padding:6px 0 16px; }
    .ex-page .ex-head h2 { color:var(--ex-ink); font-size:22px; font-weight:700; letter-spacing:-.01em; margin:0; }
    .ex-page .ex-head p { color:var(--ex-muted); font-size:13.5px; line-height:1.55; margin:6px 0 0; max-width:720px; }
    .ex-page .ex-btn-new { background:var(--ex-accent); border:0; border-radius:8px; color:#fff; font-size:13.5px; font-weight:600; padding:10px 16px; }
    .ex-page .ex-btn-new:hover, .ex-page .ex-btn-new:focus { background:#0b5ed7; color:#fff; text-decoration:none; }

    .ex-page .ex-card { background:#fff; border:1px solid var(--ex-line); border-radius:12px; margin-bottom:18px; }
    .ex-page .ex-card .card-body { padding:20px 22px; }
    .ex-page label.ex-label { color:var(--ex-ink); display:block; font-size:12.5px; font-weight:600; margin-bottom:6px; }
    .ex-page .ex-select { border:1px solid #d6dbe4; border-radius:8px; box-shadow:none; color:var(--ex-ink); height:42px; }
    .ex-page .ex-select:focus { border-color:var(--ex-accent); box-shadow:0 0 0 3px rgba(13,110,253,.12); }
    .ex-page .ex-btn-go { background:#495057; border:0; border-radius:8px; color:#fff; font-size:13.5px; font-weight:600; height:42px; }
    .ex-page .ex-btn-go:hover, .ex-page .ex-btn-go:focus { background:#343a40; color:#fff; }

    .ex-page .ex-stats { display:grid; gap:12px; grid-template-columns:repeat(4,1fr); }
    .ex-page .ex-stat { border:1px solid var(--ex-line); border-radius:10px; padding:13px 15px; }
    .ex-page .ex-stat-label { color:var(--ex-muted); font-size:12px; font-weight:600; }
    .ex-page .ex-stat-value { color:var(--ex-ink); font-size:26px; font-weight:700; letter-spacing:-.02em; line-height:1.1; margin-top:4px; }
    .ex-page .ex-stat-note { color:#98a1ad; font-size:11.5px; margin-top:3px; }

    .ex-page .ex-group { border:1px solid var(--ex-line); border-radius:12px; margin-bottom:16px; }
    /* The group head keeps its own corners rounded; the table below needs no
       clipping since the group no longer crops overflow. */
    .ex-page .ex-group-head { border-radius:12px 12px 0 0; }
    /* Keep the dropdown menu above the table and let it overflow the group. */
    .ex-page .ex-group .table-responsive { overflow:visible; }
    .ex-page .ex-group .dropdown-menu { z-index:1050; }
    .ex-page .ex-group-head { align-items:center; background:linear-gradient(135deg,#f0f7ff 0%,#e3efff 100%); border-bottom:1px solid #cfe0f8; border-left:4px solid var(--ex-accent); display:flex; flex-wrap:wrap; gap:10px; justify-content:space-between; padding:15px 18px; }
    .ex-page .ex-group-title { color:var(--ex-ink); font-size:15px; font-weight:700; }
    .ex-page .ex-group-meta { color:var(--ex-muted); font-size:12.5px; margin-top:3px; }
    .ex-page .ex-group-actions { align-items:center; display:flex; gap:8px; }
    .ex-page .ex-group-count { background:#fff; border:1px solid #cfe0f8; border-radius:20px; color:#0b5ed7; font-size:11.5px; font-weight:650; padding:4px 11px; }

    .ex-page table.ex-table { background:#fff; margin:0; width:100%; }
    .ex-page table.ex-table thead th { background:var(--ex-soft); border-bottom:1px solid var(--ex-line); border-top:0; color:#5b6673; font-size:11.5px; font-weight:650; letter-spacing:.02em; padding:11px 14px; text-transform:uppercase; }
    .ex-page table.ex-table td { border-color:#eef0f4; font-size:13px; padding:13px 14px; vertical-align:middle; }
    .ex-page table.ex-table tbody tr:hover { background:#fafbfd; }

    .ex-page .ex-row-title { color:var(--ex-ink); font-size:14px; font-weight:650; }
    .ex-page .ex-row-meta { color:var(--ex-muted); display:flex; flex-wrap:wrap; font-size:12px; gap:4px 14px; margin-top:5px; }
    .ex-page .ex-code { background:#f4f6f9; border-radius:5px; font-family:ui-monospace,SFMono-Regular,Menlo,monospace; font-size:11.5px; letter-spacing:.06em; padding:2px 6px; }
    .ex-page .ex-num { color:var(--ex-ink); font-size:16px; font-weight:700; }

    .ex-page .ex-status { align-items:center; border-radius:6px; display:inline-flex; font-size:11.5px; font-weight:650; gap:4px; padding:4px 9px; }
    .ex-page .ex-status.open { background:#e7f5ed; color:#1a7a4c; }
    .ex-page .ex-status.draft { background:#eef1f5; color:#5b6673; }
    .ex-page .ex-status.scheduled { background:#fdf3e0; color:#96650a; }
    .ex-page .ex-status.closed { background:#fbeaea; color:#b03636; }
    .ex-page .ex-status-sub { color:var(--ex-muted); display:block; font-size:11px; margin-top:4px; }

    .ex-page .ex-empty-group { color:var(--ex-muted); font-size:13px; padding:24px 18px; text-align:center; }
    .ex-page .ex-empty-group a { font-weight:600; }
    .ex-page .ex-nothing { color:var(--ex-muted); font-size:13px; padding:40px 22px; text-align:center; }
    .ex-page .ex-nothing i { color:#c3cbd6; display:block; font-size:38px; margin-bottom:8px; }

    @media (max-width:991px) {
        .ex-page .ex-stats { grid-template-columns:repeat(2,1fr); }
    }
    @media (max-width:575px) {
        .ex-page .ex-stats { grid-template-columns:1fr; }
    }
</style>

<div class="content-page ex-page">
    <div class="content">
        <div class="container-fluid">

            <div class="ex-head">
                <div>
                    <h2>Exam Builder</h2>
                    <p>Build the written examination, quiz, or skills test for a vacancy assigned to you. Each exam belongs to one vacancy and carries its own question bank, schedule, and access code.</p>
                </div>
                <?php if (!empty($vacancies)) : ?>
                    <a href="<?= base_url('secretariat/exams/create' . ($selectedJobId > 0 ? '?job_id=' . $selectedJobId : '')); ?>" class="btn ex-btn-new">
                        <i class="mdi mdi-plus mr-1"></i> New Exam
                    </a>
                <?php endif; ?>
            </div>

            <?php foreach (['success' => 'alert-success', 'danger' => 'alert-danger'] as $flash => $class) : ?>
                <?php if ($this->session->flashdata($flash)) : ?>
                    <div class="alert <?= $class; ?> alert-dismissible fade show" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <?= $ex_h($this->session->flashdata($flash)); ?>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>

            <?php if (empty($vacancies)) : ?>
                <div class="alert alert-warning">
                    <strong>No vacancy assigned.</strong> Ask a Super Admin to tag an open vacancy to your Secretariat account before building an exam.
                </div>
            <?php else : ?>

                <div class="card ex-card">
                    <div class="card-body">
                        <form method="get" action="<?= base_url('secretariat/exams'); ?>">
                            <div class="form-row align-items-end">
                                <div class="col-lg-9 col-md-8">
                                    <label for="job_id" class="ex-label">Vacancy</label>
                                    <select name="job_id" id="job_id" class="form-control ex-select">
                                        <option value="">All assigned vacancies</option>
                                        <?php foreach ($vacancies as $vacancy) : ?>
                                            <?php $jobId = (int) $vacancy->jobID; ?>
                                            <option value="<?= $jobId; ?>" <?= $jobId === $selectedJobId ? 'selected' : ''; ?>>
                                                <?= $ex_h($vacancy->jobTitle . ' — ' . ($positionGroups[(int) $vacancy->position] ?? 'Vacancy') . ' — FY ' . $vacancy->sy); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-lg-3 col-md-4 mt-2 mt-md-0">
                                    <button type="submit" class="btn btn-block ex-btn-go">View exams</button>
                                </div>
                            </div>
                        </form>

                        <div class="ex-stats mt-3">
                            <div class="ex-stat">
                                <div class="ex-stat-label">Exams</div>
                                <div class="ex-stat-value"><?= (int) $totals['exams']; ?></div>
                                <div class="ex-stat-note"><?= $selectedVacancy ? 'In this vacancy' : 'Across your vacancies'; ?></div>
                            </div>
                            <div class="ex-stat">
                                <div class="ex-stat-label">Published</div>
                                <div class="ex-stat-value"><?= (int) $totals['published']; ?></div>
                                <div class="ex-stat-note"><?= (int) $totals['draft']; ?> still draft</div>
                            </div>
                            <div class="ex-stat">
                                <div class="ex-stat-label">Questions</div>
                                <div class="ex-stat-value"><?= (int) $totals['questions']; ?></div>
                                <div class="ex-stat-note">Across every question bank</div>
                            </div>
                            <div class="ex-stat">
                                <div class="ex-stat-label">Total points</div>
                                <div class="ex-stat-value"><?= $fmtPoints($totals['points']); ?></div>
                                <div class="ex-stat-note">Sum of all exam totals</div>
                            </div>
                        </div>
                    </div>
                </div>

                <?php foreach ($grouped as $group) : ?>
                    <?php
                    $vacancy = $group['vacancy'];
                    $jobId = (int) $vacancy->jobID;
                    $exams = $group['exams'];
                    ?>
                    <div class="ex-group">
                        <div class="ex-group-head">
                            <div>
                                <div class="ex-group-title"><?= $ex_h($vacancy->jobTitle); ?></div>
                                <div class="ex-group-meta">
                                    <?= $ex_h($positionGroups[(int) $vacancy->position] ?? 'Vacancy'); ?>
                                    &middot; FY <?= $ex_h($vacancy->sy); ?>
                                    <?php if (!empty($vacancy->itemNo)) : ?>&middot; Item <?= $ex_h($vacancy->itemNo); ?><?php endif; ?>
                                    &middot; <?= (int) $vacancy->applicant_total; ?> applicant<?= (int) $vacancy->applicant_total === 1 ? '' : 's'; ?>
                                </div>
                            </div>
                            <div class="ex-group-actions">
                                <span class="ex-group-count"><?= count($exams); ?> exam<?= count($exams) === 1 ? '' : 's'; ?></span>
                                <a href="<?= base_url('secretariat/exams/create?job_id=' . $jobId); ?>" class="btn btn-sm btn-outline-primary">
                                    <i class="mdi mdi-plus"></i> Add exam
                                </a>
                            </div>
                        </div>

                        <?php if (empty($exams)) : ?>
                            <div class="ex-empty-group">
                                No exam built for this vacancy yet.
                                <a href="<?= base_url('secretariat/exams/create?job_id=' . $jobId); ?>">Build the first one</a>.
                            </div>
                        <?php else : ?>
                            <div class="table-responsive">
                                <table class="table ex-table">
                                    <thead>
                                        <tr>
                                            <th>Exam</th>
                                            <th class="text-center">Questions</th>
                                            <th class="text-center">Points</th>
                                            <th>Status</th>
                                            <th class="text-right">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($exams as $exam) : ?>
                                            <?php $state = $examState($exam); ?>
                                            <tr>
                                                <td data-label="Exam">
                                                    <div class="ex-row-title">
                                                        <a href="<?= base_url('secretariat/exams/' . (int) $exam->exam_id); ?>"><?= $ex_h($exam->title); ?></a>
                                                    </div>
                                                    <div class="ex-row-meta">
                                                        <span title="Access code"><i class="mdi mdi-pound"></i> <span class="ex-code"><?= $ex_h($exam->exam_code); ?></span></span>
                                                        <span title="Password applicants key in to enter"><i class="mdi mdi-shield-key-outline"></i> <span class="ex-code"><?= $ex_h($exam->password_plain); ?></span></span>
                                                        <span><i class="mdi mdi-refresh"></i> <?= (int) $exam->attempt_limit > 0 ? (int) $exam->attempt_limit . '&times; attempt' . ((int) $exam->attempt_limit === 1 ? '' : 's') : 'Unlimited attempts'; ?></span>
                                                        <span><i class="mdi mdi-timer-outline"></i> <?= !empty($exam->time_limit_minutes) ? (int) $exam->time_limit_minutes . ' min' : 'No time limit'; ?></span>
                                                        <?php if ($exam->passing_score !== null) : ?>
                                                            <span><i class="mdi mdi-target"></i> Passing <?= $fmtPoints($exam->passing_score); ?></span>
                                                        <?php endif; ?>
                                                        <span><i class="mdi mdi-calendar-blank-outline"></i> Built <?= $ex_h(date('M j, Y', strtotime((string) $exam->created_at))); ?></span>
                                                    </div>
                                                </td>
                                                <td class="text-center" data-label="Questions"><span class="ex-num"><?= (int) $exam->question_count; ?></span></td>
                                                <td class="text-center" data-label="Points"><span class="ex-num"><?= $fmtPoints($exam->total_points); ?></span></td>
                                                <td data-label="Status">
                                                    <span class="ex-status <?= $ex_h($state['class']); ?>">
                                                        <i class="mdi <?= $ex_h($state['icon']); ?>"></i><?= $ex_h($state['label']); ?>
                                                    </span>
                                                    <?php if ($fmtStamp($exam->open_at) !== '') : ?>
                                                        <small class="ex-status-sub">Opens <?= $ex_h($fmtStamp($exam->open_at)); ?></small>
                                                    <?php endif; ?>
                                                    <?php if ($fmtStamp($exam->close_at) !== '') : ?>
                                                        <small class="ex-status-sub">Closes <?= $ex_h($fmtStamp($exam->close_at)); ?></small>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-right" data-label="Action">
                                                    <div class="dropdown d-inline-block">
                                                        <button type="button" class="btn btn-sm btn-light dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                            <i class="mdi mdi-cog-outline"></i> Actions
                                                        </button>
                                                        <div class="dropdown-menu dropdown-menu-right">
                                                            <a class="dropdown-item" href="<?= base_url('secretariat/exams/' . (int) $exam->exam_id); ?>">
                                                                <i class="mdi mdi-folder-open-outline mr-1"></i> Open
                                                            </a>
                                                            <a class="dropdown-item" href="<?= base_url('secretariat/exams/' . (int) $exam->exam_id . '/edit'); ?>">
                                                                <i class="mdi mdi-pencil-outline mr-1"></i> Edit
                                                            </a>
                                                            <div class="dropdown-divider"></div>
                                                            <form method="post" action="<?= base_url('secretariat/exams/' . (int) $exam->exam_id . '/delete'); ?>"
                                                                  onsubmit="return confirm('Delete this exam? Its question bank is removed with it.');">
                                                                <button type="submit" class="dropdown-item text-danger">
                                                                    <i class="mdi mdi-trash-can-outline mr-1"></i> Delete
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>

                <?php if (empty($grouped)) : ?>
                    <div class="card ex-card">
                        <div class="ex-nothing">
                            <i class="mdi mdi-clipboard-text-outline"></i>
                            No vacancy to show.
                        </div>
                    </div>
                <?php endif; ?>

            <?php endif; ?>

        </div>
    </div>
</div>
