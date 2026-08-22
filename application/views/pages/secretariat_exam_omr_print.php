<?php
$h = static function ($value) { return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8'); };
$questions = $questions ?? [];
$qualified = $qualified ?? false;
$applicants = $applicants ?? [];
$choiceLetters = range('A', 'F');
$questionsPerPage = 60;
$pageCount = max(1, (int) ceil(count($questions) / $questionsPerPage));
$vacancy = $h($exam->vacancy_title ?: $exam->job_title);

$nameOf = static function ($a) {
    $last = trim((string) ($a->LastName ?? ''));
    $first = trim((string) ($a->FirstName ?? ''));
    $middle = trim((string) ($a->MiddleName ?? ''));
    $ext = trim((string) ($a->NameExtn ?? ''));
    $parts = [];
    if ($last !== '') { $parts[] = $last . ','; }
    if ($first !== '') { $parts[] = $first; }
    if ($middle !== '') { $parts[] = strtoupper(substr($middle, 0, 1)) . '.'; }
    if ($ext !== '') { $parts[] = $ext; }
    return trim(implode(' ', $parts));
};
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title><?= $h($title ?? 'Print OMR Exam'); ?></title>
    <style>
        *{box-sizing:border-box} body{background:#eef1f5;color:#172033;font-family:Arial,sans-serif;margin:0}
        .toolbar{align-items:center;background:#102f55;color:#fff;display:flex;flex-wrap:wrap;gap:10px;padding:12px 18px;position:sticky;top:0;z-index:4}.toolbar strong{margin-right:auto}.toolbar button,.toolbar a{border:0;border-radius:7px;font:inherit;padding:9px 12px}.toolbar button{background:#0d6efd;color:#fff;cursor:pointer;font-weight:700}.toolbar a{background:rgba(255,255,255,.15);color:#fff;text-decoration:none}
        .notice{background:#fff7dc;border:1px solid #e8ca72;border-radius:8px;color:#71550c;margin:16px auto;max-width:210mm;padding:12px 14px}
        .paper{background:#fff;margin:16px auto;min-height:297mm;padding:16mm 15mm;width:210mm}.paper h1{font-size:20pt;margin:0 0 4px;text-align:center}.paper .meta{font-size:10pt;text-align:center}.paper .identity{border-bottom:1px solid #222;display:flex;font-size:10pt;gap:15mm;margin-top:8mm;padding:0 0 3mm}.paper .instructions{border:1px solid #777;font-size:9.5pt;line-height:1.45;margin:7mm 0 8mm;padding:5mm}.question{break-inside:avoid;font-size:10pt;margin:0 0 5mm}.question strong{display:inline-block;min-width:8mm}.choices{display:grid;gap:2mm;grid-template-columns:repeat(2,minmax(0,1fr));margin:2mm 0 0 8mm}.choice{display:flex;gap:2mm}.letter{font-weight:700}
        .answer-sheet{background:#fff;height:297mm;margin:16px auto;width:210mm}.answer-sheet svg{display:block;height:297mm;width:210mm}
        @page{size:A4;margin:0}@media print{body{background:#fff}.toolbar,.notice{display:none!important}.paper,.answer-sheet{margin:0}.paper{break-after:page}.answer-sheet{break-before:page}.question{break-inside:avoid}body.print-questionnaire .answer-sheet{display:none!important}body.print-answers .paper{display:none!important}body.print-answers .answer-sheet.first-answer-sheet{break-before:auto}}
        @media(max-width:850px){.paper,.answer-sheet{height:auto;max-width:100%;min-height:0;width:100%}.answer-sheet svg{height:auto;width:100%}}
    </style>
</head>
<body>
    <div class="toolbar"><strong><?= $qualified ? 'Print qualified OMR answer sheets' : 'Print batch OMR exam'; ?></strong>
        <span><?= count($questions); ?> questions &middot; <?= $pageCount; ?> answer-sheet page<?= $pageCount === 1 ? '' : 's'; ?><?= $qualified ? ' &middot; ' . count($applicants) . ' qualified applicant' . (count($applicants) === 1 ? '' : 's') : ''; ?></span>
        <button type="button" onclick="printPart('questionnaire')">Print questionnaire</button>
        <button type="button" onclick="printPart('answers')">Print answer sheets</button>
        <?php if ($qualified) : ?><a href="<?= base_url('secretariat/exams/' . (int) $exam->exam_id . '/omr/print'); ?>">Generic master</a><?php else : ?><a href="<?= base_url('secretariat/exams/' . (int) $exam->exam_id . '/omr/print?qualified=1'); ?>">Qualified only</a><?php endif; ?>
        <a href="<?= base_url('secretariat/exams/' . (int) $exam->exam_id); ?>">Back</a>
    </div>
    <?php if ($qualified) : ?>
        <div class="notice"><strong>Qualified-applicant workflow:</strong> Each answer sheet below is pre-labelled with the applicant number and the job vacancy. The applicant does not need to shade the 10-digit number grid. Print on A4 at 100% scale with browser headers and footers disabled.</div>
    <?php else : ?>
        <div class="notice"><strong>Batch workflow:</strong> This is a generic master set—photocopy it for every examinee. Nobody needs a personalized sheet. Give each examinee their application number and require all 10 digits, including leading zeroes. Print on A4 at 100% scale with browser headers and footers disabled.</div>
    <?php endif; ?>

    <main class="paper">
        <h1><?= $h($exam->title); ?></h1>
        <div class="meta"><?= $vacancy; ?> &middot; Exam <?= $h($exam->exam_code); ?></div>
        <div class="identity"><span>Name: ______________________________________________</span><span>Application/Examinee No.: __________________</span></div>
        <div class="instructions"><strong>Instructions:</strong> Read each item carefully. Mark answers only on the separate OMR answer sheet using a dark pen or pencil. Completely fill each selected bubble and erase cleanly when changing an answer.
            <?php if (!$qualified) : ?> On every answer-sheet page, shade your 10-digit application/examinee number, adding leading zeroes when necessary (example: application 123 becomes 0000000123).<?php else : ?> The applicant number and vacancy are pre-printed on every answer sheet.<?php endif; ?>
            <?php if (trim((string) $exam->instructions) !== '') : ?><br><?= nl2br($h($exam->instructions)); ?><?php endif; ?>
            <?php if (!empty($exam->time_limit_minutes)) : ?><br><strong>Time limit:</strong> <?= (int) $exam->time_limit_minutes; ?> minutes<?php endif; ?>
        </div>
        <?php foreach ($questions as $index => $question) : ?>
            <?php $type = (string) $question->question_type; ?>
            <section class="question"><div><strong><?= $index + 1; ?>.</strong> <?= nl2br($h($question->prompt)); ?><?= $type === 'multiple_choice' ? ' <em>(Select all that apply.)</em>' : ''; ?></div><div class="choices">
                <?php foreach ((array) $question->choices as $ci => $choice) : ?>
                    <?php $text = is_array($choice) ? ($choice['text'] ?? '') : $choice; ?>
                    <div class="choice"><span class="letter"><?= $choiceLetters[$ci] ?? ($ci + 1); ?>.</span><span><?= $h($text); ?></span></div>
                <?php endforeach; ?>
            </div></section>
        <?php endforeach; ?>
    </main>

    <?php
    $sheets = [];
    if ($qualified) {
        foreach ($applicants as $applicant) {
            for ($page = 1; $page <= $pageCount; $page++) {
                $sheets[] = ['applicant' => $applicant, 'page' => $page];
            }
        }
    } else {
        for ($page = 1; $page <= $pageCount; $page++) {
            $sheets[] = ['applicant' => null, 'page' => $page];
        }
    }
    $globalSheetIndex = 0;
    ?>
    <?php foreach ($sheets as $sheet) : ?>
        <?php
        $applicant = $sheet['applicant'];
        $page = $sheet['page'];
        $pageQuestions = array_slice($questions, ($page - 1) * $questionsPerPage, $questionsPerPage);
        $barcodeText = 'OMR|' . (string) $exam->exam_code . '|PAGE|' . $page;
        $pageCode = $page - 1;
        $isFirst = $globalSheetIndex === 0;
        $globalSheetIndex++;
        ?>
        <section class="answer-sheet <?= $isFirst ? 'first-answer-sheet' : ''; ?>" aria-label="OMR answer sheet page <?= $page; ?><?= $applicant ? ' for ' . $h($nameOf($applicant)) : ''; ?>">
            <svg viewBox="0 0 210 297" xmlns="http://www.w3.org/2000/svg">
                <rect width="210" height="297" fill="#fff"/>
                <g fill="#000"><rect x="5" y="5" width="6" height="6"/><rect x="199" y="5" width="6" height="6"/><rect x="5" y="286" width="6" height="6"/><rect x="199" y="286" width="6" height="6"/></g>

                <?php if (!$qualified) : ?>
                    <text x="16" y="16" font-family="Arial" font-size="3.3" font-weight="700">APPLICATION / EXAMINEE NUMBER — SHADE ALL 10 DIGITS</text>
                    <?php for ($column = 0; $column < 10; $column++) : ?>
                        <?php $x = 23 + ($column * 5.2); ?>
                        <text x="<?= $x; ?>" y="21" font-family="Arial" font-size="2.5" font-weight="700" text-anchor="middle"><?= $column + 1; ?></text>
                    <?php endfor; ?>
                    <?php for ($digit = 0; $digit <= 9; $digit++) : ?>
                        <?php $y = 25 + ($digit * 3.05); ?>
                        <text x="16" y="<?= $y + .9; ?>" font-family="Arial" font-size="2.5" font-weight="700" text-anchor="middle"><?= $digit; ?></text>
                        <?php for ($column = 0; $column < 10; $column++) : ?>
                            <?php $x = 23 + ($column * 5.2); ?>
                            <circle cx="<?= $x; ?>" cy="<?= $y; ?>" r="1.15" fill="#fff" stroke="#000" stroke-width=".35"/>
                        <?php endfor; ?>
                    <?php endfor; ?>
                <?php else : ?>
                    <?php $applicantName = $nameOf($applicant); ?>
                    <?php $applicantNo = trim((string) ($applicant->record_no ?? '')) !== '' ? (string) $applicant->record_no : (string) $applicant->appID; ?>
                    <text x="16" y="16" font-family="Arial" font-size="3.4" font-weight="700">APPLICANT NO.: <?= $h($applicantNo); ?></text>
                    <text x="16" y="21.5" font-family="Arial" font-size="3.0" font-weight="700"><?= $h($applicantName); ?></text>
                    <text x="16" y="27" font-family="Arial" font-size="2.7" font-weight="700">VACANCY: <?= $vacancy; ?></text>
                    <text x="16" y="32.5" font-family="Arial" font-size="2.5">Do not write outside the answer bubbles.</text>
                <?php endif; ?>

                <text x="142" y="18" font-family="Arial" font-size="5" font-weight="700" text-anchor="middle">OMR ANSWER SHEET</text>
                <text x="142" y="24" font-family="Arial" font-size="3.6" font-weight="700" text-anchor="middle"><?= $h($exam->title); ?></text>
                <text x="142" y="29" font-family="Arial" font-size="3.1" text-anchor="middle">Exam <?= $h($exam->exam_code); ?> &middot; Page <?= $page; ?> of <?= $pageCount; ?></text>
                <image href="<?= base_url('barcode.php?text=' . rawurlencode($barcodeText) . '&size=30&orientation=horizontal&codetype=code128&print=false&sizefactor=1'); ?>" x="108" y="32" width="82" height="10" preserveAspectRatio="none"/>
                <text x="149" y="45" font-family="Arial" font-size="2.4" text-anchor="middle"><?= $h($barcodeText); ?></text>

                <!-- Solid sentinel plus eight binary page bits; the scanner can identify pages even without barcode support. -->
                <circle cx="137" cy="51" r="1.35" fill="#000"/><text x="137" y="55" font-family="Arial" font-size="1.8" text-anchor="middle">P</text>
                <?php for ($bit = 0; $bit < 8; $bit++) : ?>
                    <?php $x = 147 + ($bit * 5.7); $on = (($pageCode >> $bit) & 1) === 1; ?>
                    <circle cx="<?= $x; ?>" cy="51" r="1.25" fill="<?= $on ? '#000' : '#fff'; ?>" stroke="#000" stroke-width=".3"/>
                <?php endfor; ?>
                <line x1="14" y1="58" x2="196" y2="58" stroke="#000" stroke-width=".4"/>
                <text x="105" y="62.5" font-family="Arial" font-size="2.9" text-anchor="middle">Fill bubbles completely. For multiple-choice items, fill every selected answer.</text>

                <?php foreach ([0, 1] as $column) : ?>
                    <?php $baseX = 18 + ($column * 99); ?>
                    <?php foreach ($choiceLetters as $ci => $letter) : ?><text x="<?= $baseX + 16 + ($ci * 8); ?>" y="66.5" font-family="Arial" font-size="3.1" font-weight="700" text-anchor="middle"><?= $letter; ?></text><?php endforeach; ?>
                <?php endforeach; ?>

                <?php foreach ($pageQuestions as $localIndex => $question) : ?>
                    <?php
                    $globalIndex = (($page - 1) * $questionsPerPage) + $localIndex;
                    $column = intdiv($localIndex, 30); $row = $localIndex % 30;
                    $baseX = 18 + ($column * 99); $y = 70.5 + ($row * 7.08);
                    $choiceCount = count((array) $question->choices);
                    ?>
                    <text x="<?= $baseX; ?>" y="<?= $y + 1.25; ?>" font-family="Arial" font-size="3.2" font-weight="700" text-anchor="end"><?= $globalIndex + 1; ?></text>
                    <?php foreach ($choiceLetters as $ci => $letter) : ?>
                        <?php $x = $baseX + 16 + ($ci * 8); ?>
                        <?php if ($ci < $choiceCount) : ?><circle cx="<?= $x; ?>" cy="<?= $y; ?>" r="2.45" fill="#fff" stroke="#000" stroke-width=".5"/><text x="<?= $x; ?>" y="<?= $y + 1.15; ?>" font-family="Arial" font-size="2.7" text-anchor="middle"><?= $letter; ?></text>
                        <?php else : ?><circle cx="<?= $x; ?>" cy="<?= $y; ?>" r="2.1" fill="none" stroke="#d6d6d6" stroke-width=".25"/><?php endif; ?>
                    <?php endforeach; ?>
                <?php endforeach; ?>
                <text x="105" y="284" font-family="Arial" font-size="2.8" text-anchor="middle">Keep this page flat and clean. Do not write over the four corner registration squares.</text>
            </svg>
        </section>
    <?php endforeach; ?>
    <script>
        function printPart(part) {
            document.body.classList.remove('print-questionnaire', 'print-answers');
            document.body.classList.add(part === 'answers' ? 'print-answers' : 'print-questionnaire');
            window.print();
        }
        window.addEventListener('afterprint', function () {
            document.body.classList.remove('print-questionnaire', 'print-answers');
        });
    </script>
</body>
</html>
