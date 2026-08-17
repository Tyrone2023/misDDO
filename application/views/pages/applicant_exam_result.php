<?php
/**
 * What the applicant sees after submitting.
 *
 * Deliberately no answer key and no per-question breakdown: this is a hiring exam
 * that others still have to sit, and a review screen is the fastest way to leak it.
 * The score, and whether anything still needs a human to read it, is the whole of
 * what an applicant needs from this page.
 */
$ae_h = static function ($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
};

$fmtPoints = static function ($value) {
    return rtrim(rtrim(number_format((float) $value, 2, '.', ','), '0'), '.');
};

$pending = (int) $attempt->pending_review_count;
$passingScore = $attempt->passing_score === null ? null : (float) $attempt->passing_score;

// A pending essay can still move the total, so a pass/fail verdict is only
// reported once nothing is left for a person to score.
$verdict = null;
if ($passingScore !== null && $pending === 0) {
    $verdict = ((float) $attempt->score >= $passingScore) ? 'passed' : 'failed';
}
?>

<style>
    .aer-page { --aer-ink:#1f2a37; --aer-muted:#6d7885; --aer-line:#e2e6ec; --aer-soft:#f7f8fb; padding-bottom:90px; }
    .aer-page .container-fluid { max-width:760px; }

    .aer-page .aer-card { background:#fff; border:1px solid var(--aer-line); border-radius:12px; margin-bottom:16px; overflow:hidden; }
    .aer-page .aer-hero { padding:30px 26px; text-align:center; }
    .aer-page .aer-icon { align-items:center; border-radius:50%; display:inline-flex; height:66px; justify-content:center; margin-bottom:14px; width:66px; }
    .aer-page .aer-icon i { font-size:34px; }
    .aer-page .aer-icon.done { background:#e7f5ed; color:#17845a; }
    .aer-page .aer-icon.review { background:#fdf3e0; color:#a8730c; }
    .aer-page .aer-icon.timeout { background:#fbeaea; color:#b03636; }
    .aer-page .aer-hero h2 { color:var(--aer-ink); font-size:21px; font-weight:700; margin:0 0 6px; }
    .aer-page .aer-hero p { color:var(--aer-muted); font-size:13.5px; line-height:1.6; margin:0 auto; max-width:520px; }

    .aer-page .aer-score { border-top:1px solid var(--aer-line); display:grid; grid-template-columns:repeat(3,1fr); }
    .aer-page .aer-score div { border-left:1px solid var(--aer-line); padding:18px 14px; text-align:center; }
    .aer-page .aer-score div:first-child { border-left:0; }
    .aer-page .aer-score-label { color:var(--aer-muted); font-size:11.5px; font-weight:650; letter-spacing:.04em; text-transform:uppercase; }
    .aer-page .aer-score-value { color:var(--aer-ink); font-size:27px; font-weight:800; letter-spacing:-.02em; line-height:1.15; margin-top:5px; }
    .aer-page .aer-score-value.passed { color:#17845a; }
    .aer-page .aer-score-value.failed { color:#b03636; }
    .aer-page .aer-score-sub { color:#98a1ad; font-size:11.5px; margin-top:3px; }

    .aer-page .aer-detail { padding:18px 22px; }
    .aer-page .aer-row { border-top:1px solid #eef0f4; display:flex; flex-wrap:wrap; gap:8px; justify-content:space-between; padding:10px 0; }
    .aer-page .aer-row:first-child { border-top:0; }
    .aer-page .aer-row dt { color:var(--aer-muted); font-size:13px; font-weight:500; margin:0; }
    .aer-page .aer-row dd { color:var(--aer-ink); font-size:13px; font-weight:600; margin:0; }

    .aer-page .aer-note { background:var(--aer-soft); border-radius:8px; color:#4b5a6b; font-size:12.5px; line-height:1.6; margin:16px 22px 20px; padding:12px 14px; }
    .aer-page .aer-actions { display:flex; flex-wrap:wrap; gap:10px; justify-content:center; }
    .aer-page .aer-btn { background:#0d6efd; border-radius:8px; color:#fff; font-size:13.5px; font-weight:600; padding:10px 20px; }
    .aer-page .aer-btn:hover, .aer-page .aer-btn:focus { background:#0b5ed7; color:#fff; text-decoration:none; }
    .aer-page .aer-btn-ghost { background:#fff; border:1px solid var(--aer-line); color:#4b5a6b; }
    .aer-page .aer-btn-ghost:hover, .aer-page .aer-btn-ghost:focus { background:var(--aer-soft); color:var(--aer-ink); }
</style>

<div class="content-page aer-page">
    <div class="content">
        <div class="container-fluid">

            <?php foreach (['success' => 'alert-success', 'danger' => 'alert-danger'] as $flash => $class) : ?>
                <?php if ($this->session->flashdata($flash)) : ?>
                    <div class="alert <?= $class; ?> alert-dismissible fade show" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <?= $ae_h($this->session->flashdata($flash)); ?>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>

            <div class="aer-card">
                <div class="aer-hero">
                    <?php
                    if ((int) $attempt->timed_out === 1) {
                        $icon = ['timeout', 'mdi-timer-off-outline'];
                    } elseif ($pending > 0) {
                        $icon = ['review', 'mdi-account-clock-outline'];
                    } else {
                        $icon = ['done', 'mdi-check-circle-outline'];
                    }
                    ?>
                    <div class="aer-icon <?= $ae_h($icon[0]); ?>"><i class="mdi <?= $ae_h($icon[1]); ?>"></i></div>
                    <h2>
                        <?php if ((int) $attempt->timed_out === 1) : ?>
                            Time ran out
                        <?php else : ?>
                            Exam submitted
                        <?php endif; ?>
                    </h2>
                    <p>
                        <?= $ae_h($attempt->exam_title); ?> &mdash; <?= $ae_h($attempt->vacancy_title ?: $attempt->job_title); ?>.
                        <?php if ((int) $attempt->timed_out === 1) : ?>
                            The exam closed when the timer reached zero and was submitted as it stood.
                        <?php endif; ?>
                        <?php if ($pending > 0) : ?>
                            <?= $pending; ?> answer<?= $pending === 1 ? ' still needs' : 's still need'; ?> to be read by the Secretariat, so this score is not final.
                        <?php endif; ?>
                    </p>
                </div>

                <div class="aer-score">
                    <div>
                        <div class="aer-score-label">Score</div>
                        <div class="aer-score-value <?= $verdict ? $ae_h($verdict) : ''; ?>"><?= $fmtPoints($attempt->score); ?></div>
                        <div class="aer-score-sub">of <?= $fmtPoints($attempt->total_points); ?> points</div>
                    </div>
                    <div>
                        <div class="aer-score-label">Percentage</div>
                        <div class="aer-score-value"><?= $fmtPoints($attempt->percentage); ?>%</div>
                        <div class="aer-score-sub"><?= (int) $attempt->answered_count; ?> answered</div>
                    </div>
                    <div>
                        <div class="aer-score-label">Result</div>
                        <div class="aer-score-value <?= $verdict ? $ae_h($verdict) : ''; ?>" style="font-size:19px;">
                            <?php if ($verdict === 'passed') : ?>
                                Passed
                            <?php elseif ($verdict === 'failed') : ?>
                                Did not pass
                            <?php elseif ($pending > 0) : ?>
                                For review
                            <?php else : ?>
                                Recorded
                            <?php endif; ?>
                        </div>
                        <div class="aer-score-sub">
                            <?php if ($passingScore !== null) : ?>
                                Passing <?= $fmtPoints($passingScore); ?>
                            <?php else : ?>
                                No passing mark set
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="aer-detail">
                    <dl class="mb-0">
                        <div class="aer-row"><dt>Attempt</dt><dd>#<?= (int) $attempt->attempt_no; ?><?= (int) $attempt->attempt_limit > 0 ? ' of ' . (int) $attempt->attempt_limit : ''; ?></dd></div>
                        <div class="aer-row"><dt>Started</dt><dd><?= $ae_h(date('M j, Y g:i A', strtotime((string) $attempt->started_at))); ?></dd></div>
                        <div class="aer-row"><dt>Submitted</dt><dd><?= $ae_h(date('M j, Y g:i A', strtotime((string) $attempt->submitted_at))); ?></dd></div>
                        <div class="aer-row"><dt>Reference</dt><dd><?= $ae_h($attempt->exam_code); ?>-<?= (int) $attempt->attempt_id; ?></dd></div>
                    </dl>
                </div>

                <div class="aer-note">
                    <i class="mdi mdi-information-outline"></i>
                    The question paper and the correct answers are not shown after submission, because other applicants
                    still have to sit this exam. Keep the reference above if you need to raise a question about your score.
                </div>
            </div>

            <div class="aer-actions">
                <?php if ($app) : ?>
                    <a href="<?= base_url('applicant/exam/' . (int) $app->appID); ?>" class="aer-btn aer-btn-ghost">Back to the exam page</a>
                    <a href="<?= base_url('Pages/ja/' . (int) $app->applicant_id); ?>" class="aer-btn">My applications</a>
                <?php else : ?>
                    <a href="<?= base_url(); ?>" class="aer-btn">Back to my dashboard</a>
                <?php endif; ?>
            </div>

        </div>
    </div>
</div>
