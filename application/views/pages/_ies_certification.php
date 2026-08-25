<?php
/**
 * Closing block of the evaluation sheet when it is issued as a certificate:
 * the applicant attestation is replaced by a certification signed by the
 * Assistant Schools Division Superintendent alone. Expects $job, $signatory
 * (users row, position asst_sds), $ms and $name.
 */
$division = strtoupper($ms->division ?? '');
$jobTypes = isset($jobTypes) ? $jobTypes : [];

$mi = trim((string) ($signatory->mname ?? ''));
$mi = ($mi !== '') ? ' ' . strtoupper(substr($mi, 0, 1)) . '.' : '';
$asdsName = trim(strtoupper(trim((string) ($signatory->fname ?? '')) . $mi . ' ' . trim((string) ($signatory->lname ?? ''))));

// Signature file kept by Pages/esignature. Skip the tag when nothing is on
// file so the certificate shows a clean signature line instead of a broken image.
$esig = trim((string) ($signatory->esig ?? ''));
$esigPath = ($esig !== '') ? FCPATH . 'uploads/esig/' . basename($esig) : '';
$hasEsig = ($esig !== '' && is_file($esigPath));
?>
<style>
    .certtext { margin-bottom: 15px; }
    .certsign { float: right; text-align: center; font-size: 14px; margin-top: 30px; }
    .certsign .isig { display: block; margin: 0 auto -22px; max-height: 75px; }
    .certsign .an { border-bottom: 1px solid #000; font-weight: 600; padding: 0 25px; }
    .certsign .cp { display: block; padding-top: 4px; }
</style>

<p class="certtext">This is to certify that <b><?= strtoupper($name); ?></b> applied for the position of
    <b><?= strtoupper($job->jobTitle); ?> <?= $jobTypes[$job->job_type] ?? ''; ?></b> under
    <b>DEPED <?= $division; ?></b>, and underwent the comparative assessment conducted by the Human Resource
    Merit Promotion and Selection Board (HRMPSB) through the Open Ranking System.</p>

<p class="certtext">This further certifies that the points reflected above are the actual scores obtained by the
    applicant based on the qualifications and documentary requirements submitted, and were confirmed by the
    applicant as the result of the said assessment.</p>

<p class="certtext">Issued this <?= date('jS'); ?> day of <?= date('F Y'); ?> upon the request of the
    above-named applicant for whatever legal purpose it may serve.</p>

<div class="blocker"></div>

<div class="certsign">
    <?php if ($hasEsig) { ?>
        <img class="isig" src="<?= base_url(); ?>uploads/esig/<?= rawurlencode(basename($esig)); ?>" alt="">
    <?php } ?>
    <span class="an"><?= $asdsName; ?></span>
    <span class="cp">Assistant Schools Division Superintendent</span>
</div>

<div class="blocker"></div>
