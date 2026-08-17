<?php
/**
 * The exam paper.
 *
 * Rendered without the app chrome - templates/head.php stops at </head>, so this
 * view owns <body> - because the sidebar and topbar are entirely navigation, and
 * navigating away mid-exam is the one thing this page should not invite.
 *
 * The controller strips every answer key before the questions get here, so nothing
 * on this page or in its markup reveals a correct answer.
 */
$ae_h = static function ($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
};

$questions = $questions ?? [];
$secondsRemaining = $secondsRemaining ?? null;

$totalPoints = 0.0;
foreach ($questions as $question) {
    $totalPoints += (float) $question->points;
}

$fmtPoints = static function ($value) {
    return rtrim(rtrim(number_format((float) $value, 2, '.', ','), '0'), '.');
};
?>
<body class="exam-paper">
<style>
    .exam-paper { background:#eef1f6; margin:0; }
    .ex-take { --ext-ink:#1f2a37; --ext-muted:#6d7885; --ext-line:#e2e6ec; --ext-accent:#0d6efd; --ext-soft:#f7f8fb; }
    .ex-take * { box-sizing:border-box; }

    .ex-take .ext-bar { background:#132f5e; box-shadow:0 2px 10px rgba(19,47,94,.2); color:#fff; left:0; position:fixed; right:0; top:0; z-index:20; }
    .ex-take .ext-bar-inner { align-items:center; display:flex; flex-wrap:wrap; gap:12px; justify-content:space-between; margin:0 auto; max-width:920px; padding:12px 20px; }
    .ex-take .ext-bar-title { font-size:15px; font-weight:700; }
    .ex-take .ext-bar-sub { color:#c6d8f5; font-size:12px; margin-top:2px; }
    .ex-take .ext-clock { align-items:center; background:rgba(255,255,255,.14); border-radius:8px; display:inline-flex; font-size:17px; font-weight:700; gap:7px; letter-spacing:.04em; padding:8px 14px; }
    .ex-take .ext-clock.warn { background:#a8420f; }
    .ex-take .ext-clock.danger { animation:extPulse 1s ease-in-out infinite; background:#a51b1b; }
    @keyframes extPulse { 50% { opacity:.6; } }
    .ex-take .ext-untimed { color:#c6d8f5; font-size:12.5px; }

    .ex-take .ext-wrap { margin:0 auto; max-width:920px; padding:96px 20px 40px; }
    .ex-take .ext-card { background:#fff; border:1px solid var(--ext-line); border-radius:12px; margin-bottom:16px; }
    .ex-take .ext-intro { padding:20px 22px; }
    .ex-take .ext-intro h1 { color:var(--ext-ink); font-size:20px; font-weight:700; margin:0 0 6px; }
    .ex-take .ext-intro .ext-meta { color:var(--ext-muted); display:flex; flex-wrap:wrap; font-size:12.5px; gap:4px 16px; }
    .ex-take .ext-instructions { background:var(--ext-soft); border-radius:8px; color:#3d4855; font-size:13px; line-height:1.6; margin-top:14px; padding:12px 14px; white-space:pre-wrap; }

    .ex-take .ext-q { border-bottom:1px solid var(--ext-line); padding:20px 22px; }
    .ex-take .ext-q:last-child { border-bottom:0; }
    .ex-take .ext-q-head { align-items:baseline; color:var(--ext-muted); display:flex; font-size:11.5px; font-weight:650; gap:8px; justify-content:space-between; letter-spacing:.03em; margin-bottom:8px; text-transform:uppercase; }
    .ex-take .ext-q-prompt { color:var(--ext-ink); font-size:15px; font-weight:600; line-height:1.55; margin-bottom:14px; white-space:pre-wrap; }

    .ex-take .ext-choice { align-items:flex-start; border:1px solid var(--ext-line); border-radius:8px; cursor:pointer; display:flex; gap:10px; margin-bottom:8px; padding:11px 13px; transition:border-color .12s, background .12s; }
    .ex-take .ext-choice:hover { background:#f7faff; border-color:#b9d0f5; }
    .ex-take .ext-choice input { margin:2px 0 0; }
    .ex-take .ext-choice span { color:#3d4855; font-size:14px; line-height:1.45; }
    .ex-take .ext-choice.checked { background:#eef4ff; border-color:var(--ext-accent); }

    .ex-take .ext-input { border:1px solid #d6dbe4; border-radius:8px; font-size:14px; padding:10px 12px; width:100%; }
    .ex-take .ext-input:focus { border-color:var(--ext-accent); box-shadow:0 0 0 3px rgba(13,110,253,.12); outline:none; }
    .ex-take textarea.ext-input { min-height:130px; resize:vertical; }

    .ex-take .ext-pair { align-items:center; display:grid; gap:10px; grid-template-columns:1fr 1fr; margin-bottom:9px; }
    .ex-take .ext-pair-left { background:var(--ext-soft); border-radius:8px; color:#3d4855; font-size:14px; padding:10px 12px; }

    .ex-take .ext-hint { color:var(--ext-muted); font-size:12px; margin-top:8px; }

    .ex-take .ext-submit-card { align-items:center; display:flex; flex-wrap:wrap; gap:14px; justify-content:space-between; padding:18px 22px; }
    .ex-take .ext-progress { color:var(--ext-muted); font-size:13px; }
    .ex-take .ext-progress strong { color:var(--ext-ink); }
    .ex-take .ext-submit { background:#17845a; border:0; border-radius:8px; color:#fff; cursor:pointer; font-size:15px; font-weight:650; padding:12px 30px; }
    .ex-take .ext-submit:hover { background:#136d4a; }
    .ex-take .ext-warn { background:#fff8e6; border:1px solid #f3dfae; border-radius:8px; color:#8a6100; font-size:12.5px; margin:0 22px 18px; padding:11px 13px; }

    @media (max-width:575px) {
        .ex-take .ext-wrap { padding-top:112px; }
        .ex-take .ext-pair { grid-template-columns:1fr; }
        .ex-take .ext-submit { width:100%; }
    }
</style>

<div class="ex-take">
    <div class="ext-bar">
        <div class="ext-bar-inner">
            <div>
                <div class="ext-bar-title"><?= $ae_h($attempt->exam_title); ?></div>
                <div class="ext-bar-sub">
                    <?= $ae_h($attempt->vacancy_title ?: $attempt->job_title); ?>
                    &middot; Attempt <?= (int) $attempt->attempt_no; ?>
                </div>
            </div>
            <?php if ($secondsRemaining !== null) : ?>
                <div class="ext-clock" id="examClock" data-remaining="<?= (int) $secondsRemaining; ?>">
                    <i class="mdi mdi-timer-outline"></i><span id="examClockText">--:--</span>
                </div>
            <?php else : ?>
                <div class="ext-untimed"><i class="mdi mdi-infinity"></i> No time limit</div>
            <?php endif; ?>
        </div>
    </div>

    <div class="ext-wrap">
        <div class="ext-card ext-intro">
            <h1><?= $ae_h($attempt->exam_title); ?></h1>
            <div class="ext-meta">
                <span><?= count($questions); ?> question<?= count($questions) === 1 ? '' : 's'; ?></span>
                <span><?= $fmtPoints($totalPoints); ?> points</span>
                <?php if (!empty($attempt->time_limit_minutes)) : ?>
                    <span><?= (int) $attempt->time_limit_minutes; ?>-minute limit</span>
                <?php endif; ?>
            </div>
            <?php if (trim((string) $attempt->instructions) !== '') : ?>
                <div class="ext-instructions"><?= $ae_h($attempt->instructions); ?></div>
            <?php endif; ?>
        </div>

        <form method="post" action="<?= base_url('applicant/exam/attempt/' . (int) $attempt->attempt_id . '/submit'); ?>" id="examForm">
            <div class="ext-card">
                <?php foreach ($questions as $question) : ?>
                    <?php $qid = (int) $question->question_id; ?>
                    <div class="ext-q" data-question="<?= $qid; ?>">
                        <div class="ext-q-head">
                            <span>Question <?= (int) $question->number; ?></span>
                            <span><?= $fmtPoints($question->points); ?> pt<?= (float) $question->points === 1.0 ? '' : 's'; ?></span>
                        </div>
                        <div class="ext-q-prompt"><?= $ae_h($question->prompt); ?></div>

                        <?php if ($question->question_type === 'single_choice' || $question->question_type === 'true_false') : ?>
                            <?php foreach ($question->choices as $choice) : ?>
                                <label class="ext-choice">
                                    <input type="radio" name="answers[<?= $qid; ?>]" value="<?= $ae_h(is_array($choice) ? ($choice['id'] ?? '') : $choice); ?>">
                                    <span><?= $ae_h(is_array($choice) ? ($choice['text'] ?? '') : $choice); ?></span>
                                </label>
                            <?php endforeach; ?>

                        <?php elseif ($question->question_type === 'multiple_choice') : ?>
                            <?php foreach ($question->choices as $choice) : ?>
                                <label class="ext-choice">
                                    <input type="checkbox" name="answers[<?= $qid; ?>][]" value="<?= $ae_h(is_array($choice) ? ($choice['id'] ?? '') : $choice); ?>">
                                    <span><?= $ae_h(is_array($choice) ? ($choice['text'] ?? '') : $choice); ?></span>
                                </label>
                            <?php endforeach; ?>
                            <div class="ext-hint">Select every answer that applies.</div>

                        <?php elseif ($question->question_type === 'short_answer') : ?>
                            <input type="text" class="ext-input" name="answers[<?= $qid; ?>]" autocomplete="off" placeholder="Type your answer">

                        <?php elseif ($question->question_type === 'matching') : ?>
                            <?php foreach ($question->pairs as $pairIndex => $left) : ?>
                                <div class="ext-pair">
                                    <div class="ext-pair-left"><?= $ae_h($left); ?></div>
                                    <select class="ext-input" name="answers[<?= $qid; ?>][<?= (int) $pairIndex; ?>]">
                                        <option value="">Choose the match</option>
                                        <?php foreach ($question->options as $option) : ?>
                                            <option value="<?= $ae_h($option); ?>"><?= $ae_h($option); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            <?php endforeach; ?>

                        <?php else : ?>
                            <textarea class="ext-input" name="answers[<?= $qid; ?>]" placeholder="Write your answer"></textarea>
                            <div class="ext-hint">This answer is read and scored by the Secretariat, not by the system.</div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="ext-card">
                <div class="ext-warn">
                    <i class="mdi mdi-alert-outline"></i>
                    Your answers are recorded only when you press Submit. Nothing is saved as you go, so finish in one sitting.
                </div>
                <div class="ext-submit-card">
                    <div class="ext-progress"><strong id="answeredCount">0</strong> of <?= count($questions); ?> answered</div>
                    <button type="submit" class="ext-submit" id="submitBtn">
                        <i class="mdi mdi-send-check-outline"></i> Submit exam
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    (function () {
        'use strict';

        var form = document.getElementById('examForm');
        var submitted = false;

        // Highlight the chosen option, and keep the answered tally honest.
        function refresh() {
            var answered = 0;
            Array.prototype.forEach.call(document.querySelectorAll('.ext-q'), function (block) {
                var done = false;

                Array.prototype.forEach.call(block.querySelectorAll('.ext-choice'), function (label) {
                    var input = label.querySelector('input');
                    label.classList.toggle('checked', !!(input && input.checked));
                });

                var checked = block.querySelector('input[type="radio"]:checked, input[type="checkbox"]:checked');
                if (checked) {
                    done = true;
                }

                var text = block.querySelector('input[type="text"], textarea');
                if (text && text.value.trim() !== '') {
                    done = true;
                }

                var selects = block.querySelectorAll('select');
                if (selects.length) {
                    var filled = 0;
                    Array.prototype.forEach.call(selects, function (s) {
                        if (s.value !== '') { filled++; }
                    });
                    done = filled === selects.length;
                }

                if (done) { answered++; }
            });

            document.getElementById('answeredCount').textContent = answered;
            return answered;
        }

        form.addEventListener('change', refresh);
        form.addEventListener('input', refresh);
        refresh();

        form.addEventListener('submit', function (e) {
            if (submitted) {
                return;
            }
            // The timeout path submits without asking; a person pressing the button
            // gets one confirmation, because there is no going back.
            if (!form.dataset.auto) {
                var answered = refresh();
                var total = document.querySelectorAll('.ext-q').length;
                var message = answered < total
                    ? 'You have answered ' + answered + ' of ' + total + ' questions. Submit anyway? You cannot return to this attempt.'
                    : 'Submit your exam? You cannot return to this attempt.';
                if (!window.confirm(message)) {
                    e.preventDefault();
                    return;
                }
            }
            submitted = true;
            document.getElementById('submitBtn').disabled = true;
        });

        // Leaving mid-exam loses the answers, since nothing is saved until submit.
        window.addEventListener('beforeunload', function (e) {
            if (submitted) {
                return;
            }
            e.preventDefault();
            e.returnValue = '';
        });

        var clock = document.getElementById('examClock');
        if (!clock) {
            return;
        }

        var remaining = parseInt(clock.getAttribute('data-remaining'), 10) || 0;
        var text = document.getElementById('examClockText');

        function tick() {
            var m = Math.floor(remaining / 60);
            var s = remaining % 60;
            text.textContent = m + ':' + (s < 10 ? '0' : '') + s;

            clock.classList.toggle('warn', remaining <= 300 && remaining > 60);
            clock.classList.toggle('danger', remaining <= 60);

            if (remaining <= 0) {
                // Hand in whatever is on the page rather than losing it entirely.
                form.dataset.auto = '1';
                submitted = true;
                form.submit();
                return;
            }

            remaining--;
            window.setTimeout(tick, 1000);
        }

        tick();
    })();
</script>
</body>
</html>
