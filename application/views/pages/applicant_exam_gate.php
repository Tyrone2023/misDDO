<?php
/**
 * The applicant's exam gate: the exams published for the vacancy they applied to,
 * each behind its password. Nothing about a question is shown here.
 */
$ae_h = static function ($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
};

$positionGroups = [
    1 => 'Teaching',
    2 => 'School Administration',
    3 => 'Related Teaching',
    4 => 'Non-Teaching',
];

$exams = $exams ?? [];
$availability = $availability ?? [];
$attempts = $attempts ?? [];
$blocked = (string) ($blocked ?? '');

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
    .aeg-page { --aeg-ink:#1f2a37; --aeg-muted:#6d7885; --aeg-line:#e2e6ec; --aeg-accent:#0d6efd; --aeg-soft:#f7f8fb; padding-bottom:90px; }
    .aeg-page .container-fluid { max-width:960px; }

    .aeg-page .aeg-head { border-bottom:1px solid var(--aeg-line); margin-bottom:20px; padding:6px 0 16px; }
    .aeg-page .aeg-head h2 { color:var(--aeg-ink); font-size:22px; font-weight:700; margin:0; }
    .aeg-page .aeg-head p { color:var(--aeg-muted); font-size:13.5px; line-height:1.55; margin:6px 0 0; }
    .aeg-page .aeg-vacancy { color:var(--aeg-ink); font-size:15px; font-weight:650; margin-top:10px; }
    .aeg-page .aeg-vacancy span { color:var(--aeg-muted); font-size:13px; font-weight:400; }

    .aeg-page .aeg-card { background:#fff; border:1px solid var(--aeg-line); border-radius:12px; margin-bottom:16px; overflow:hidden; }
    .aeg-page .aeg-card-head { align-items:flex-start; border-bottom:1px solid var(--aeg-line); display:flex; flex-wrap:wrap; gap:10px; justify-content:space-between; padding:16px 20px; }
    .aeg-page .aeg-title { color:var(--aeg-ink); font-size:16px; font-weight:700; margin:0; }
    .aeg-page .aeg-meta { color:var(--aeg-muted); display:flex; flex-wrap:wrap; font-size:12.5px; gap:4px 16px; margin-top:6px; }
    .aeg-page .aeg-card-body { padding:18px 20px; }

    .aeg-page .aeg-status { align-items:center; border-radius:6px; display:inline-flex; font-size:11.5px; font-weight:650; gap:5px; padding:5px 10px; white-space:nowrap; }
    .aeg-page .aeg-status.open { background:#e7f5ed; color:#1a7a4c; }
    .aeg-page .aeg-status.scheduled { background:#fdf3e0; color:#96650a; }
    .aeg-page .aeg-status.closed { background:#fbeaea; color:#b03636; }
    .aeg-page .aeg-status.done { background:#eef1f5; color:#5b6673; }

    .aeg-page .aeg-instructions { background:var(--aeg-soft); border-radius:8px; color:#3d4855; font-size:13px; line-height:1.6; margin:0 0 16px; padding:12px 14px; white-space:pre-wrap; }
    .aeg-page .aeg-note { color:var(--aeg-muted); font-size:13px; line-height:1.55; margin:0; }

    .aeg-page .aeg-form { align-items:flex-end; display:flex; flex-wrap:wrap; gap:12px; }
    .aeg-page .aeg-field { flex:1 1 260px; }
    .aeg-page .aeg-label { color:var(--aeg-ink); display:block; font-size:12.5px; font-weight:600; margin-bottom:6px; }
    .aeg-page .aeg-input { border:1px solid #d6dbe4; border-radius:8px; font-size:14px; height:44px; padding:8px 12px; width:100%; }
    .aeg-page .aeg-input:focus { border-color:var(--aeg-accent); box-shadow:0 0 0 3px rgba(13,110,253,.12); outline:none; }
    .aeg-page .aeg-start { background:var(--aeg-accent); border:0; border-radius:8px; color:#fff; font-size:14px; font-weight:600; height:44px; padding:0 24px; }
    .aeg-page .aeg-start:hover, .aeg-page .aeg-start:focus { background:#0b5ed7; color:#fff; }
    .aeg-page .aeg-help { color:var(--aeg-muted); font-size:12px; margin-top:8px; }

    .aeg-page .aeg-resume { background:#fff8e6; border:1px solid #f3dfae; border-radius:8px; color:#96650a; font-size:12.5px; margin-bottom:14px; padding:10px 13px; }
    .aeg-page .aeg-empty { color:var(--aeg-muted); font-size:13.5px; padding:44px 20px; text-align:center; }
    .aeg-page .aeg-empty i { color:#c3cbd6; display:block; font-size:38px; margin-bottom:10px; }
    .aeg-page .aeg-back { color:var(--aeg-muted); font-size:13px; font-weight:600; }
</style>

<div class="content-page aeg-page">
    <div class="content">
        <div class="container-fluid">

            <div class="aeg-head">
                <h2>Examination</h2>
                <p>The examination for the position you applied to. You will need the password given to you by the Secretariat.</p>
                <div class="aeg-vacancy">
                    <?= $ae_h($app->jobTitle); ?>
                    <span>
                        &middot; <?= $ae_h($positionGroups[(int) $app->position] ?? 'Vacancy'); ?>
                        &middot; FY <?= $ae_h($app->sy); ?>
                        &middot; Application #<?= (int) $app->appID; ?>
                    </span>
                </div>
            </div>

            <?php foreach (['success' => 'alert-success', 'danger' => 'alert-danger'] as $flash => $class) : ?>
                <?php if ($this->session->flashdata($flash)) : ?>
                    <div class="alert <?= $class; ?> alert-dismissible fade show" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <?= $ae_h($this->session->flashdata($flash)); ?>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>

            <?php if ($blocked !== '') : ?>
                <div class="alert alert-warning"><?= $ae_h($blocked); ?></div>
            <?php endif; ?>

            <?php if (empty($exams)) : ?>
                <div class="aeg-card">
                    <div class="aeg-empty">
                        <i class="mdi mdi-clipboard-text-outline"></i>
                        No examination has been released for this position yet.<br>
                        You will see it here once the Secretariat publishes one.
                    </div>
                </div>
            <?php else : ?>
                <?php foreach ($exams as $exam) : ?>
                    <?php
                    $examId = (int) $exam->exam_id;
                    $state = $availability[$examId] ?? ['open' => false, 'reason' => 'closed', 'message' => ''];
                    $used = $attempts[$examId] ?? ['total' => 0, 'submitted' => 0, 'best_score' => null, 'last_attempt_id' => 0];
                    $limit = (int) $exam->attempt_limit;
                    $left = $limit > 0 ? max(0, $limit - (int) $used['submitted']) : null;
                    $exhausted = $left !== null && $left === 0;
                    $canStart = $state['open'] && !$exhausted && $blocked === '';

                    if ($exhausted) {
                        $badge = ['done', 'mdi-check-circle-outline', 'Completed'];
                    } elseif ($state['open']) {
                        $badge = ['open', 'mdi-lock-open-variant-outline', 'Open now'];
                    } elseif ($state['reason'] === 'scheduled') {
                        $badge = ['scheduled', 'mdi-clock-outline', 'Not yet open'];
                    } else {
                        $badge = ['closed', 'mdi-lock-outline', 'Closed'];
                    }
                    ?>
                    <div class="aeg-card">
                        <div class="aeg-card-head">
                            <div>
                                <h5 class="aeg-title"><?= $ae_h($exam->title); ?></h5>
                                <div class="aeg-meta">
                                    <span><i class="mdi mdi-help-circle-outline"></i> <?= (int) $exam->question_count; ?> question<?= (int) $exam->question_count === 1 ? '' : 's'; ?></span>
                                    <span><i class="mdi mdi-star-outline"></i> <?= $fmtPoints($exam->total_points); ?> points</span>
                                    <span><i class="mdi mdi-timer-outline"></i> <?= !empty($exam->time_limit_minutes) ? (int) $exam->time_limit_minutes . ' minutes' : 'No time limit'; ?></span>
                                    <span><i class="mdi mdi-refresh"></i>
                                        <?php if ($limit > 0) : ?>
                                            <?= (int) $used['submitted']; ?> of <?= $limit; ?> attempt<?= $limit === 1 ? '' : 's'; ?> used
                                        <?php else : ?>
                                            Unlimited attempts
                                        <?php endif; ?>
                                    </span>
                                    <?php if ($fmtStamp($exam->close_at) !== '') : ?>
                                        <span><i class="mdi mdi-calendar-remove-outline"></i> Closes <?= $ae_h($fmtStamp($exam->close_at)); ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <span class="aeg-status <?= $ae_h($badge[0]); ?>"><i class="mdi <?= $ae_h($badge[1]); ?>"></i><?= $ae_h($badge[2]); ?></span>
                        </div>

                        <div class="aeg-card-body">
                            <?php if (trim((string) $exam->instructions) !== '') : ?>
                                <div class="aeg-instructions"><?= $ae_h($exam->instructions); ?></div>
                            <?php endif; ?>

                            <?php if ($canStart) : ?>
                                <?php if ((int) $used['total'] > (int) $used['submitted']) : ?>
                                    <div class="aeg-resume">
                                        <i class="mdi mdi-progress-clock"></i>
                                        You have an exam already in progress. Entering the password again takes you back to it &mdash;
                                        the timer, if any, has kept running.
                                    </div>
                                <?php endif; ?>

                                <form method="post" action="<?= base_url('applicant/exam/' . (int) $app->appID . '/enter'); ?>" class="aeg-form">
                                    <input type="hidden" name="exam_id" value="<?= $examId; ?>">
                                    <div class="aeg-field">
                                        <label class="aeg-label" for="pw-<?= $examId; ?>">Exam password</label>
                                        <input type="password" class="aeg-input" id="pw-<?= $examId; ?>" name="exam_password"
                                               autocomplete="off" placeholder="Enter the password given to you" required>
                                    </div>
                                    <div>
                                        <button type="submit" class="aeg-start">
                                            <i class="mdi mdi-play-circle-outline mr-1"></i> Start exam
                                        </button>
                                    </div>
                                </form>
                                <p class="aeg-help">
                                    <?php if (!empty($exam->time_limit_minutes)) : ?>
                                        The <?= (int) $exam->time_limit_minutes; ?>-minute timer starts the moment you enter the password. Make sure you can finish in one sitting.
                                    <?php else : ?>
                                        This exam has no time limit, but it can only be submitted once per attempt.
                                    <?php endif; ?>
                                </p>

                            <?php elseif ($exhausted) : ?>
                                <p class="aeg-note">
                                    You have used all <?= $limit; ?> attempt<?= $limit === 1 ? '' : 's'; ?> for this exam.
                                    <?php if ((int) $used['last_attempt_id'] > 0) : ?>
                                        <a href="<?= base_url('applicant/exam/result/' . (int) $used['last_attempt_id']); ?>">View your result</a>.
                                    <?php endif; ?>
                                </p>

                            <?php else : ?>
                                <p class="aeg-note"><?= $ae_h($state['message'] !== '' ? $state['message'] : 'This exam is not open.'); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

            <a href="<?= base_url('Pages/ja/' . (int) $app->applicant_id); ?>" class="aeg-back">
                <i class="mdi mdi-arrow-left"></i> Back to my applications
            </a>

        </div>
    </div>
</div>
