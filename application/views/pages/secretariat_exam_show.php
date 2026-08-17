<?php
/**
 * One exam, with the vacancy it belongs to rendered in the position the college
 * build gives to subject / section, and its full question bank with answer keys.
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

$questionTypes = [
    'single_choice' => 'Single Choice',
    'multiple_choice' => 'Multiple Choice',
    'true_false' => 'True / False',
    'short_answer' => 'Short Answer',
    'matching' => 'Matching',
    'essay' => 'Essay',
];

$questions = $questions ?? [];

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

// Same rule as the list: a reached Open At on a draft means the exam is open.
$status = (string) $exam->status;
$openStamp = $exam->open_at ? strtotime((string) $exam->open_at) : null;
$closeStamp = $exam->close_at ? strtotime((string) $exam->close_at) : null;

if ($status === 'draft' && $openStamp && time() >= $openStamp) {
    $state = ['label' => 'Open', 'class' => 'open'];
} elseif ($status === 'draft' && $openStamp) {
    $state = ['label' => 'Scheduled', 'class' => 'scheduled'];
} elseif ($status === 'draft') {
    $state = ['label' => 'Draft', 'class' => 'draft'];
} elseif ($closeStamp && time() > $closeStamp) {
    $state = ['label' => 'Closed', 'class' => 'closed'];
} elseif ($openStamp && time() < $openStamp) {
    $state = ['label' => 'Scheduled', 'class' => 'scheduled'];
} else {
    $state = ['label' => 'Published', 'class' => 'open'];
}

// The stored vacancy title is the snapshot taken when the exam was built; a
// renamed or archived vacancy is reported rather than silently overwritten.
$vacancyTitle = trim((string) ($exam->vacancy_title ?? '')) ?: (string) $exam->job_title;
$vacancyGone = trim((string) ($exam->vacancy_title ?? '')) === '';
$vacancyRenamed = !$vacancyGone && trim((string) $exam->job_title) !== '' && $vacancyTitle !== trim((string) $exam->job_title);
$vacancyGroup = (int) ($exam->vacancy_position ?? $exam->position_group);
$vacancySy = trim((string) ($exam->vacancy_sy ?? '')) ?: (string) $exam->sy;
?>

<style>
    /* Above .content-page's reserved 65px, or the absolutely-positioned footer in
       templates/footer.php draws over the last question. */
    .exs-page { --exs-ink:#1f2a37; --exs-muted:#6d7885; --exs-line:#e2e6ec; --exs-accent:#0d6efd; --exs-soft:#f7f8fb; padding-bottom:90px; }
    .exs-page .container-fluid { max-width:1120px; }

    .exs-page .exs-hero { align-items:flex-start; background:linear-gradient(125deg,#103764 0%,#2357d5 62%,#4d82ed 100%); border-radius:14px; color:#fff; display:flex; flex-wrap:wrap; gap:16px; justify-content:space-between; margin-bottom:20px; padding:24px 26px; }
    .exs-page .exs-hero-eyebrow { color:#cfe0ff; font-size:11.5px; font-weight:700; letter-spacing:.1em; text-transform:uppercase; }
    .exs-page .exs-hero h2 { color:#fff; font-size:23px; font-weight:700; margin:5px 0 8px; }
    .exs-page .exs-hero-meta { color:#dce8ff; font-size:13px; }
    .exs-page .exs-hero-actions { display:flex; flex-wrap:wrap; gap:8px; }
    .exs-page .exs-chip { align-items:center; background:rgba(255,255,255,.16); border:0; border-radius:8px; color:#fff; display:inline-flex; font-size:12.5px; font-weight:600; gap:6px; padding:8px 13px; }
    .exs-page .exs-chip:hover, .exs-page .exs-chip:focus { background:rgba(255,255,255,.28); color:#fff; text-decoration:none; }
    .exs-page .exs-chip-danger { background:rgba(255,255,255,.16); }
    .exs-page .exs-chip-danger:hover { background:#dc3545; }

    .exs-page .exs-card { background:#fff; border:1px solid var(--exs-line); border-radius:12px; margin-bottom:18px; }
    .exs-page .exs-card-head { align-items:center; border-bottom:1px solid var(--exs-line); display:flex; flex-wrap:wrap; gap:8px; justify-content:space-between; padding:15px 20px; }
    .exs-page .exs-card-title { color:var(--exs-ink); font-size:15px; font-weight:700; margin:0; }
    .exs-page .exs-card-body { padding:18px 20px; }

    .exs-page .exs-vacancy { border-left:3px solid var(--exs-accent); padding-left:14px; }
    .exs-page .exs-vacancy-label { color:var(--exs-muted); font-size:11.5px; font-weight:700; letter-spacing:.06em; text-transform:uppercase; }
    .exs-page .exs-vacancy-title { color:var(--exs-ink); font-size:17px; font-weight:700; margin-top:3px; }
    .exs-page .exs-vacancy-meta { color:var(--exs-muted); font-size:13px; margin-top:3px; }

    .exs-page .exs-facts { display:grid; gap:12px; grid-template-columns:repeat(auto-fill,minmax(150px,1fr)); margin-top:18px; }
    .exs-page .exs-fact { border:1px solid var(--exs-line); border-radius:10px; padding:12px 14px; }
    .exs-page .exs-fact-label { color:var(--exs-muted); font-size:11.5px; font-weight:600; letter-spacing:.03em; text-transform:uppercase; }
    .exs-page .exs-fact-value { color:var(--exs-ink); font-size:16px; font-weight:700; margin-top:4px; word-break:break-word; }
    .exs-page .exs-fact-value.exs-mono { font-family:ui-monospace,SFMono-Regular,Menlo,monospace; font-size:15px; letter-spacing:.08em; }
    .exs-page .exs-fact-sub { color:#98a1ad; font-size:11.5px; margin-top:2px; }

    .exs-page .exs-status { align-items:center; border-radius:6px; display:inline-flex; font-size:11.5px; font-weight:650; gap:4px; padding:4px 9px; }
    .exs-page .exs-status.open { background:#e7f5ed; color:#1a7a4c; }
    .exs-page .exs-status.draft { background:#eef1f5; color:#5b6673; }
    .exs-page .exs-status.scheduled { background:#fdf3e0; color:#96650a; }
    .exs-page .exs-status.closed { background:#fbeaea; color:#b03636; }

    .exs-page .exs-instructions { color:#3d4855; font-size:13.5px; line-height:1.65; margin:0; white-space:pre-wrap; }
    .exs-page .exs-muted-note { color:var(--exs-muted); font-size:13px; margin:0; }

    .exs-page .exs-q { border:1px solid var(--exs-line); border-radius:10px; margin-bottom:12px; overflow:hidden; }
    .exs-page .exs-q-head { align-items:center; background:var(--exs-soft); border-bottom:1px solid var(--exs-line); color:var(--exs-muted); display:flex; flex-wrap:wrap; font-size:11.5px; font-weight:650; gap:6px; justify-content:space-between; letter-spacing:.03em; padding:9px 14px; text-transform:uppercase; }
    .exs-page .exs-q-body { padding:14px 16px; }
    .exs-page .exs-q-prompt { color:var(--exs-ink); font-size:14px; font-weight:600; line-height:1.55; margin-bottom:10px; white-space:pre-wrap; }
    .exs-page .exs-q-name { color:var(--exs-muted); font-size:11.5px; font-weight:600; margin-bottom:5px; }
    .exs-page ul.exs-choices { list-style:none; margin:0; padding:0; }
    .exs-page ul.exs-choices li { align-items:flex-start; color:#3d4855; display:flex; font-size:13.5px; gap:8px; padding:5px 0; }
    .exs-page ul.exs-choices li i { color:#c3cbd6; font-size:15px; line-height:1.4; }
    .exs-page ul.exs-choices li.correct { color:#146c43; font-weight:600; }
    .exs-page ul.exs-choices li.correct i { color:#198754; }
    .exs-page .exs-answer-tag { background:#e7f5ed; border-radius:5px; color:#146c43; display:inline-block; font-size:12.5px; font-weight:600; margin:0 6px 6px 0; padding:4px 9px; }
    .exs-page table.exs-pairs { margin:0; width:100%; }
    .exs-page table.exs-pairs th { background:var(--exs-soft); border-bottom:1px solid var(--exs-line); color:#5b6673; font-size:11px; font-weight:650; letter-spacing:.03em; padding:8px 10px; text-transform:uppercase; }
    .exs-page table.exs-pairs td { border-bottom:1px solid #eef0f4; font-size:13.5px; padding:9px 10px; }
    .exs-page .exs-manual { background:#fdf3e0; border-radius:6px; color:#96650a; display:inline-block; font-size:12.5px; font-weight:600; padding:5px 10px; }

    .exs-page .exs-nothing { color:var(--exs-muted); font-size:13px; padding:36px 20px; text-align:center; }
    .exs-page .exs-nothing i { color:#c3cbd6; display:block; font-size:36px; margin-bottom:8px; }

    @media (max-width:575px) {
        .exs-page .exs-hero { padding:20px; }
        .exs-page .exs-hero-actions { width:100%; }
        .exs-page .exs-hero-actions .exs-chip { flex:1 1 auto; justify-content:center; }
    }
</style>

<div class="content-page exs-page">
    <div class="content">
        <div class="container-fluid">

            <div class="exs-hero">
                <div>
                    <div class="exs-hero-eyebrow"><i class="mdi mdi-clipboard-text-outline mr-1"></i> Examination</div>
                    <h2><?= $ex_h($exam->title); ?></h2>
                    <div class="exs-hero-meta">
                        <?= $ex_h($vacancyTitle); ?>
                        <?php if ($vacancySy !== '') : ?>&middot; FY <?= $ex_h($vacancySy); ?><?php endif; ?>
                        &middot; Built <?= $ex_h(date('M j, Y', strtotime((string) $exam->created_at))); ?>
                        <?php if (!empty($exam->created_by_username)) : ?>by <?= $ex_h($exam->created_by_username); ?><?php endif; ?>
                    </div>
                </div>
                <div class="exs-hero-actions">
                    <a href="<?= base_url('secretariat/exams?job_id=' . (int) $exam->job_id); ?>" class="exs-chip"><i class="mdi mdi-arrow-left"></i> All exams</a>
                    <a href="<?= base_url('secretariat/exams/' . (int) $exam->exam_id . '/edit'); ?>" class="exs-chip"><i class="mdi mdi-pencil-outline"></i> Edit</a>
                    <form method="post" action="<?= base_url('secretariat/exams/' . (int) $exam->exam_id . '/delete'); ?>"
                          onsubmit="return confirm('Delete this exam? Its question bank is removed with it.');" style="margin:0;">
                        <button type="submit" class="exs-chip exs-chip-danger"><i class="mdi mdi-trash-can-outline"></i> Delete</button>
                    </form>
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

            <div class="card exs-card">
                <div class="exs-card-head">
                    <h5 class="exs-card-title">Exam details</h5>
                    <span class="exs-status <?= $ex_h($state['class']); ?>"><?= $ex_h($state['label']); ?></span>
                </div>
                <div class="exs-card-body">
                    <div class="exs-vacancy">
                        <div class="exs-vacancy-label">Vacancy this exam belongs to</div>
                        <div class="exs-vacancy-title"><?= $ex_h($vacancyTitle); ?></div>
                        <div class="exs-vacancy-meta">
                            <?= $ex_h($positionGroups[$vacancyGroup] ?? 'Vacancy'); ?>
                            <?php if ($vacancySy !== '') : ?>&middot; FY <?= $ex_h($vacancySy); ?><?php endif; ?>
                            <?php if (!empty($exam->vacancy_item_no)) : ?>&middot; Item <?= $ex_h($exam->vacancy_item_no); ?><?php endif; ?>
                            <?php if (!empty($exam->vacancy_department)) : ?>&middot; <?= $ex_h($exam->vacancy_department); ?><?php endif; ?>
                            <?php if (!empty($exam->vacancy_status)) : ?>&middot; <?= $ex_h($exam->vacancy_status); ?><?php endif; ?>
                        </div>
                        <?php if ($vacancyGone) : ?>
                            <div class="exs-vacancy-meta text-warning mt-1">
                                <i class="mdi mdi-alert-outline"></i>
                                This vacancy is no longer on file. The title above is the one recorded when the exam was built.
                            </div>
                        <?php elseif ($vacancyRenamed) : ?>
                            <div class="exs-vacancy-meta mt-1">
                                <i class="mdi mdi-information-outline"></i>
                                Recorded as &ldquo;<?= $ex_h($exam->job_title); ?>&rdquo; when the exam was built.
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="exs-facts">
                        <div class="exs-fact">
                            <div class="exs-fact-label">Access code</div>
                            <div class="exs-fact-value exs-mono"><?= $ex_h($exam->exam_code); ?></div>
                        </div>
                        <div class="exs-fact">
                            <div class="exs-fact-label">Password</div>
                            <div class="exs-fact-value exs-mono"><?= !empty($exam->password_plain) ? $ex_h($exam->password_plain) : '&mdash;'; ?></div>
                            <div class="exs-fact-sub">The applicant's entry point</div>
                        </div>
                        <div class="exs-fact">
                            <div class="exs-fact-label">Questions</div>
                            <div class="exs-fact-value"><?= (int) $exam->question_count; ?></div>
                        </div>
                        <div class="exs-fact">
                            <div class="exs-fact-label">Total points</div>
                            <div class="exs-fact-value"><?= $fmtPoints($exam->total_points); ?></div>
                            <?php if ($exam->passing_score !== null) : ?>
                                <div class="exs-fact-sub">Passing <?= $fmtPoints($exam->passing_score); ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="exs-fact">
                            <div class="exs-fact-label">Time limit</div>
                            <div class="exs-fact-value"><?= !empty($exam->time_limit_minutes) ? (int) $exam->time_limit_minutes . ' min' : 'None'; ?></div>
                        </div>
                        <div class="exs-fact">
                            <div class="exs-fact-label">Attempts</div>
                            <div class="exs-fact-value"><?= (int) $exam->attempt_limit > 0 ? (int) $exam->attempt_limit : 'Unlimited'; ?></div>
                        </div>
                        <div class="exs-fact">
                            <div class="exs-fact-label">Opens</div>
                            <div class="exs-fact-value" style="font-size:13.5px;"><?= $fmtStamp($exam->open_at) !== '' ? $ex_h($fmtStamp($exam->open_at)) : '&mdash;'; ?></div>
                        </div>
                        <div class="exs-fact">
                            <div class="exs-fact-label">Closes</div>
                            <div class="exs-fact-value" style="font-size:13.5px;"><?= $fmtStamp($exam->close_at) !== '' ? $ex_h($fmtStamp($exam->close_at)) : '&mdash;'; ?></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card exs-card">
                <div class="exs-card-head">
                    <h5 class="exs-card-title">Instructions</h5>
                </div>
                <div class="exs-card-body">
                    <?php if (trim((string) $exam->instructions) !== '') : ?>
                        <p class="exs-instructions"><?= $ex_h($exam->instructions); ?></p>
                    <?php else : ?>
                        <p class="exs-muted-note">No instructions were written for this exam.</p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card exs-card">
                <div class="exs-card-head">
                    <h5 class="exs-card-title">Question bank</h5>
                    <span class="text-muted" style="font-size:12.5px;">
                        <?= count($questions); ?> question<?= count($questions) === 1 ? '' : 's'; ?> &middot; <?= $fmtPoints($exam->total_points); ?> points
                    </span>
                </div>

                <?php if (empty($questions)) : ?>
                    <div class="exs-nothing">
                        <i class="mdi mdi-help-circle-outline"></i>
                        This exam has no questions yet.
                        <a href="<?= base_url('secretariat/exams/' . (int) $exam->exam_id . '/edit'); ?>">Add some</a>.
                    </div>
                <?php else : ?>
                    <div class="exs-card-body">
                        <?php foreach ($questions as $i => $question) : ?>
                            <?php
                            $type = (string) $question->question_type;
                            $answerKey = (array) $question->answer_key;
                            ?>
                            <div class="exs-q">
                                <div class="exs-q-head">
                                    <span>Q<?= (int) $i + 1; ?> &middot; <?= $ex_h($questionTypes[$type] ?? str_replace('_', ' ', $type)); ?></span>
                                    <span><?= $fmtPoints($question->points); ?> pt<?= (float) $question->points === 1.0 ? '' : 's'; ?></span>
                                </div>
                                <div class="exs-q-body">
                                    <?php if (trim((string) $question->question_name) !== '') : ?>
                                        <div class="exs-q-name"><?= $ex_h($question->question_name); ?></div>
                                    <?php endif; ?>
                                    <div class="exs-q-prompt"><?= $ex_h($question->prompt); ?></div>

                                    <?php if ($type === 'single_choice' || $type === 'multiple_choice' || $type === 'true_false') : ?>
                                        <ul class="exs-choices">
                                            <?php foreach ((array) $question->choices as $choice) : ?>
                                                <?php
                                                $choiceId = is_array($choice) ? (string) ($choice['id'] ?? '') : (string) $choice;
                                                $choiceText = is_array($choice) ? (string) ($choice['text'] ?? '') : (string) $choice;
                                                $isCorrect = in_array($choiceId, array_map('strval', $answerKey), true);
                                                ?>
                                                <li class="<?= $isCorrect ? 'correct' : ''; ?>">
                                                    <i class="mdi <?= $isCorrect ? 'mdi-check-circle' : 'mdi-checkbox-blank-circle-outline'; ?>"></i>
                                                    <span><?= $ex_h($choiceText); ?></span>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>

                                    <?php elseif ($type === 'short_answer') : ?>
                                        <div class="inline-label text-muted" style="font-size:11.5px;font-weight:600;margin-bottom:6px;">ACCEPTED ANSWERS</div>
                                        <?php foreach ($answerKey as $answer) : ?>
                                            <span class="exs-answer-tag"><?= $ex_h($answer); ?></span>
                                        <?php endforeach; ?>

                                    <?php elseif ($type === 'matching') : ?>
                                        <div class="table-responsive">
                                            <table class="exs-pairs">
                                                <thead><tr><th>Prompt</th><th>Correct match</th></tr></thead>
                                                <tbody>
                                                    <?php foreach ($answerKey as $left => $right) : ?>
                                                        <tr><td><?= $ex_h($left); ?></td><td><?= $ex_h($right); ?></td></tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>

                                    <?php elseif ($type === 'essay') : ?>
                                        <span class="exs-manual"><i class="mdi mdi-account-edit-outline"></i> Manually reviewed &mdash; no automatic answer key</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

        </div>
    </div>
</div>
