<?php
$a = $application;
$signatories = isset($signatories) && is_array($signatories) ? $signatories : array();

function e($v)
{
    return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
}

function full_name($last, $first, $middle = '')
{
    $name = trim((string)$last);
    if (trim((string)$first) !== '') {
        $name .= ($name !== '' ? ', ' : '') . trim((string)$first);
    }
    if (trim((string)$middle) !== '') {
        $name .= ' ' . trim((string)$middle);
    }
    return trim($name);
}

function chk($condition)
{
    return $condition ? '✓' : '';
}

function first_non_empty(...$values)
{
    foreach ($values as $value) {
        if (trim((string)$value) !== '') {
            return (string)$value;
        }
    }
    return '';
}

function signatory_row($signatories, $role)
{
    return isset($signatories[$role]) && is_array($signatories[$role]) ? $signatories[$role] : array();
}

function signatory_name($row, $fallback_last = '', $fallback_first = '', $fallback_middle = '')
{
    if (!empty($row['full_name'])) {
        return (string)$row['full_name'];
    }
    return full_name($fallback_last, $fallback_first, $fallback_middle);
}

function signatory_position($row, ...$fallbacks)
{
    if (!empty($row['position_title'])) {
        return (string)$row['position_title'];
    }
    return first_non_empty(...$fallbacks);
}

function signatory_signature($row, ...$fallbacks)
{
    if (!empty($row['signature_path'])) {
        return (string)$row['signature_path'];
    }
    return first_non_empty(...$fallbacks);
}

function signatory_status($row, $fallback = '')
{
    if (!empty($row['action_status'])) {
        return strtoupper(trim((string)$row['action_status']));
    }
    return strtoupper(trim((string)$fallback));
}

$cert_row = signatory_row($signatories, 'HR_CERTIFICATION');
$rec_row  = signatory_row($signatories, 'RECOMMENDING_AUTHORITY');
$app_row  = signatory_row($signatories, 'APPROVING_AUTHORITY');

$leave_code = strtoupper(trim((string)($a->leave_type_code ?? $a->leave_type ?? '')));
$details_of_leave = strtolower(trim((string)($a->details_of_leave ?? '')));
$commutation = strtoupper(trim((string)($a->commutation ?? 'REQUESTED')));
$app_status = strtoupper(trim((string)($a->status ?? '')));

$applicant_name = full_name($a->emp_lastname ?? '', $a->emp_firstname ?? '', $a->emp_middlename ?? '');
$certifier_name = signatory_name($cert_row, $a->cert_lastname ?? '', $a->cert_firstname ?? '', $a->cert_middlename ?? '');
$recommender_name = signatory_name($rec_row, $a->rec_lastname ?? '', $a->rec_firstname ?? '', $a->rec_middlename ?? '');
$approver_name = signatory_name($app_row, $a->app_lastname ?? '', $a->app_firstname ?? '', $a->app_middlename ?? '');

$cert_position = signatory_position($cert_row, $a->cert_position ?? '', $a->assigned_cert_position ?? '', $a->certify_by_position ?? '');
$rec_position  = signatory_position($rec_row, $a->rec_position ?? '', $a->assigned_rec_position ?? '', $a->recommend_by_position ?? '');
$app_position  = signatory_position($app_row, $a->app_position ?? '', $a->assigned_app_position ?? '', $a->approve_by_position ?? '');

$applicant_esig = trim((string)($a->emp_esig ?? ''));
$cert_esig = trim((string)signatory_signature($cert_row, $a->cert_esig ?? '', $a->assigned_cert_esig ?? ''));
$rec_esig  = trim((string)signatory_signature($rec_row, $a->rec_esig ?? '', $a->assigned_rec_esig ?? ''));
$app_esig  = trim((string)signatory_signature($app_row, $a->app_esig ?? '', $a->assigned_app_esig ?? ''));

$cert_status = signatory_status($cert_row, '');
$rec_status  = signatory_status($rec_row, '');
$app_status_role = signatory_status($app_row, '');

$is_rejected = ($app_status === 'REJECTED');
$is_approved = ($app_status === 'APPROVED');

$has_certified_stage = ($cert_status === 'CERTIFIED') || !empty($a->certified_at) || in_array($app_status, array('CERTIFIED','RECOMMENDED','APPROVED','REJECTED'));
$has_recommended_stage = ($rec_status === 'RECOMMENDED') || !empty($a->recommended_at) || in_array($app_status, array('RECOMMENDED','APPROVED','REJECTED'));
$has_approved_stage = (!$is_rejected) && (($app_status_role === 'APPROVED') || (!empty($a->approved_at) && $is_approved));

$show_cert_signature = $has_certified_stage && $cert_esig !== '';
$show_rec_signature  = $has_recommended_stage && $rec_esig !== '';
$show_app_signature  = $has_approved_stage && $app_esig !== '';

$show_cert_name = $has_certified_stage && $certifier_name !== '';
$show_rec_name  = $has_recommended_stage && $recommender_name !== '';
$show_app_name  = $has_approved_stage && $approver_name !== '';

$office_department = (string)($a->emp_department ?? '');
$last_name  = (string)($a->emp_lastname ?? '');
$first_name = (string)($a->emp_firstname ?? '');
$middle_name = (string)($a->emp_middlename ?? '');
$position = (string)($a->emp_position ?? '');
$salary_display = isset($a->salary) && $a->salary !== null && $a->salary !== '' ? number_format((float)$a->salary, 2) : '';
$date_filed = (string)($a->date_filed ?? '');
$date_from = (string)($a->date_from ?? '');
$date_to = (string)($a->date_to ?? '');
$working_days = (string)($a->working_days ?? '');
$reason = (string)($a->reason ?? '');
$leave_type_other = (string)($a->leave_type_other ?? '');
$disapproval_reason = (string)($a->rejection_reason ?? '');

$vacation_balance = (string)($a->vacation_balance ?? '');
$sick_balance = (string)($a->sick_balance ?? '');
$vacation_less = (string)($a->vacation_less ?? '');
$sick_less = (string)($a->sick_less ?? '');
$vacation_after = (string)($a->vacation_after ?? '');
$sick_after = (string)($a->sick_after ?? '');

$approved_with_pay = (string)($a->approved_with_pay ?? '');
$approved_without_pay = (string)($a->approved_without_pay ?? '');
$approved_others = (string)($a->approved_others ?? '');
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Application for Leave - Print Preview</title>
    <style>
        @page { size: A4 portrait; margin: 9mm; }
        * { box-sizing: border-box; }
        body { margin: 0; font-family: Arial, Helvetica, sans-serif; font-size: 11px; color: #000; background: #fff; }
        .page { width: 192mm; margin: 0 auto; }
        .center { text-align: center; }
        .right { text-align: right; }
        .bold { font-weight: bold; }
        .tiny { font-size: 9px; }
        .small { font-size: 10px; }
        .mt-4 { margin-top: 4px; }
        .mt-6 { margin-top: 6px; }
        .mt-8 { margin-top: 8px; }
        .mt-10 { margin-top: 10px; }
        .mt-12 { margin-top: 12px; }
        .title-1 { font-size: 11px; font-weight: bold; }
        .title-2 { font-size: 18px; font-weight: bold; letter-spacing: 0.2px; }
        table.form { width: 100%; border-collapse: collapse; table-layout: fixed; }
        table.form td, table.form th { border: 1px solid #000; padding: 4px 6px; vertical-align: top; word-wrap: break-word; }
        .no-border td, .no-border th { border: none !important; padding: 0; }
        .section-head { font-weight: bold; background: #efefef; }
        .underline-cell { border-bottom: 1px solid #000; min-height: 16px; display: inline-block; width: 100%; }
        .checkbox { display: inline-block; width: 13px; height: 13px; border: 1px solid #000; text-align: center; line-height: 12px; font-size: 12px; font-weight: bold; vertical-align: middle; margin-right: 4px; }
        .sig-box { text-align: center; min-height: 86px; padding-top: 2px; }
        .sig-img-wrap { height: 48px; display: flex; align-items: flex-end; justify-content: center; margin-bottom: 3px; }
        .myesig { max-height: 44px; max-width: 150px; display: block; margin: 0 auto; }
        .sig-name { font-weight: bold; text-transform: uppercase; border-top: 1px solid #000; display: inline-block; min-width: 170px; padding-top: 2px; }
        .print-btn { margin: 8px 0 12px 0; }
        .text-center { text-align: center; }
        .muted-line { min-height: 52px; border-bottom: 1px solid #000; margin-top: 6px; }
        .label-row { margin-bottom: 4px; }
        .compact div { margin-bottom: 4px; }
        @media print { .print-btn { display: none; } .page { width: 100%; } }
    </style>
</head>
<body>
<div class="page">
    <div class="page">
    <div class="print-btn"><button onclick="window.print()">Print</button></div>

    <div class="center title-1">Civil Service Form No. 6</div>
    <div class="center small">Revised 2020</div>
    <div class="center mt-6">Republic of the Philippines</div>
    <div class="center">Department of Education</div>
    <div class="center bold">DIVISION OF DAVAO ORIENTAL</div>
    <div class="center">Government Center, Dahican, Mati City, Davao Oriental</div>
    <div class="center title-2 mt-8">APPLICATION FOR LEAVE</div>

    <table class="form mt-8">
        <colgroup>
            <col style="width: 34%;">
            <col style="width: 16%;">
            <col style="width: 16%;">
            <col style="width: 24%;">
            <col style="width: 10%;">
        </colgroup>
        <tr>
            <td><span class="bold">1. OFFICE/DEPARTMENT</span><br><?= e($office_department); ?></td>
            <td colspan="4">
                <span class="bold">2. NAME:</span><br>
                <table class="no-border" style="width:100%;">
                    <tr>
                        <td style="width:33%; border:none; padding:0 4px 0 0;">
                            <div class="underline-cell center"><?= e($last_name); ?></div>
                            <div class="tiny center">(Last)</div>
                        </td>
                        <td style="width:33%; border:none; padding:0 4px;">
                            <div class="underline-cell center"><?= e($first_name); ?></div>
                            <div class="tiny center">(First)</div>
                        </td>
                        <td style="width:34%; border:none; padding:0 0 0 4px;">
                            <div class="underline-cell center"><?= e($middle_name); ?></div>
                            <div class="tiny center">(Middle)</div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td><span class="bold">3. DATE OF FILING</span><br><?= e($date_filed); ?></td>
            <td colspan="2"><span class="bold">4. POSITION</span><br><?= e($position); ?></td>
            <td colspan="2"><span class="bold">5. SALARY</span><br><div class="center"><?= e($salary_display); ?></div></td>
        </tr>

        <tr><td colspan="5" class="section-head">6. DETAILS OF APPLICATION</td></tr>
        <tr>
            <td colspan="2" class="bold">6.A TYPE OF LEAVE TO BE AVAILED OF</td>
            <td colspan="3" class="bold">6.B DETAILS OF LEAVE</td>
        </tr>
        <tr>
            <td colspan="2">
                <?php
                $leave_code_aliases = array(
                    'VL' => array('VL', 'VACATION', 'VACATION LEAVE'),
                    'FL' => array('FL', 'MANDATORY', 'FORCED', 'MANDATORY/FORCED LEAVE'),
                    'SL' => array('SL', 'SICK', 'SICK LEAVE'),
                    'ML' => array('ML', 'MATERNITY', 'MATERNITY LEAVE'),
                    'PL' => array('PL', 'PATERNITY', 'PATERNITY LEAVE'),
                    'SPL' => array('SPL', 'SPECIAL PRIVILEGE', 'SPECIAL PRIVILEGE LEAVE'),
                    'SOLO_PARENT' => array('SOLO_PARENT', 'SOLOPARENT', 'SOLO PARENT', 'SOLO PARENT LEAVE'),
                    'STUDY' => array('STUDY', 'STL', 'STUDY LEAVE'),
                    'VAWC' => array('VAWC', '10-DAY VAWC LEAVE', '10 DAY VAWC LEAVE'),
                    'REHAB' => array('REHAB', 'RL', 'REHABILITATION', 'REHABILITATION PRIVILEGE'),
                    'SLBW' => array('SLBW', 'SPECIAL LEAVE BENEFITS FOR WOMEN'),
                    'SEL' => array('SEL', 'SPECIAL EMERGENCY', 'SPECIAL EMERGENCY LEAVE', 'CALAMITY'),
                    'ADOPTION' => array('ADOPTION', 'AL', 'ADOPTION LEAVE')
                );
                $normalized_leave_code = strtoupper(trim((string)$leave_code));
                $is_leave_type = function($key) use ($leave_code_aliases, $normalized_leave_code) {
                    return isset($leave_code_aliases[$key]) && in_array($normalized_leave_code, $leave_code_aliases[$key], true);
                };
                $others_value = trim((string)($a->others_label ?? ''));
                if ($others_value === '' && in_array($normalized_leave_code, array('COC', 'WELLNESS', 'PERSONAL'), true)) {
                    $others_value = $normalized_leave_code === 'WELLNESS' ? 'Wellness' : ($normalized_leave_code === 'PERSONAL' ? 'Personal' : 'COC');
                }
                $is_other_leave = $others_value !== '' || in_array($normalized_leave_code, array('COC', 'WELLNESS', 'PERSONAL'), true);
                ?>
                <div><span class="checkbox"><?= chk($is_leave_type('VL')); ?></span> Vacation Leave</div>
                <div><span class="checkbox"><?= chk($is_leave_type('FL')); ?></span> Mandatory/Forced Leave</div>
                <div><span class="checkbox"><?= chk($is_leave_type('SL')); ?></span> Sick Leave</div>
                <div><span class="checkbox"><?= chk($is_leave_type('ML')); ?></span> Maternity Leave</div>
                <div><span class="checkbox"><?= chk($is_leave_type('PL')); ?></span> Paternity Leave</div>
                <div><span class="checkbox"><?= chk($is_leave_type('SPL')); ?></span> Special Privilege Leave </div>
                <div><span class="checkbox"><?= chk($is_leave_type('SOLO_PARENT')); ?></span> Solo Parent Leave </div>
                <div><span class="checkbox"><?= chk($is_leave_type('STUDY')); ?></span> Study Leave </div>
                <div><span class="checkbox"><?= chk($is_leave_type('VAWC')); ?></span> 10-Day VAWC Leave</div>
                <div><span class="checkbox"><?= chk($is_leave_type('REHAB')); ?></span> Rehabilitation Privilege </div>
                <div><span class="checkbox"><?= chk($is_leave_type('SLBW')); ?></span> Special Leave Benefits for Women</div>
                <div><span class="checkbox"><?= chk($is_leave_type('SEL')); ?></span> Special Emergency (Calamity) Leave</div>
                <div><span class="checkbox"><?= chk($is_leave_type('ADOPTION')); ?></span> Adoption Leave</div>
                <div><span class="checkbox"><?= chk($is_other_leave); ?></span> Others: <?= e($others_value); ?></div>
            </td>
            <td colspan="23">
                <?php
                $vacation_scope = strtoupper(trim((string)($a->vacation_scope ?? '')));
                $vacation_abroad_specify = trim((string)($a->vacation_abroad_specify ?? ''));
                $sick_scope = strtoupper(trim((string)($a->sick_scope ?? '')));
                $sick_illness_specify = trim((string)($a->sick_illness_specify ?? ''));
                $study_purpose = strtoupper(trim((string)($a->study_purpose ?? '')));
                $study_other_specify = trim((string)($a->study_other_specify ?? ''));
                ?>
                <div class="bold">In case of Vacation/Special Privilege Leave:</div>
                <div><span class="checkbox"><?= chk(($is_leave_type('VL') || $is_leave_type('SPL')) && $vacation_scope === 'WITHIN_PH'); ?></span> Within the Philippines</div>
                <div><span class="checkbox"><?= chk(($is_leave_type('VL') || $is_leave_type('SPL')) && $vacation_scope === 'ABROAD'); ?></span> Abroad (Specify) <?= e($vacation_abroad_specify); ?></div>

                <div class="bold mt-8">In case of Sick Leave:</div>
                <div><span class="checkbox"><?= chk($is_leave_type('SL') && $sick_scope === 'IN_HOSPITAL'); ?></span> In Hospital (Specify Illness) <?= e($sick_illness_specify); ?></div>
                <div><span class="checkbox"><?= chk($is_leave_type('SL') && $sick_scope === 'OUT_PATIENT'); ?></span> Out Patient (Specify Illness) <?= e($sick_illness_specify); ?></div>

                <div class="bold mt-8">In case of Study Leave:</div>
                <div><span class="checkbox"><?= chk($is_leave_type('STUDY') && $study_purpose === 'MASTERS_COMPLETION'); ?></span> Completion of Master's Degree</div>
                <div><span class="checkbox"><?= chk($is_leave_type('STUDY') && $study_purpose === 'BAR_BOARD_REVIEW'); ?></span> BAR/Board Examination Review</div>
                <div><span class="checkbox"><?= chk($is_leave_type('STUDY') && $study_purpose === 'OTHER'); ?></span> Other: <?= e($study_other_specify); ?></div>

                <div class="bold mt-8">Other purpose:</div>
                <div><span class="checkbox"><?= chk(strpos($details_of_leave, 'monetization') !== false); ?></span> Monetization of Leave Credits</div>
                <div><span class="checkbox"><?= chk(strpos($details_of_leave, 'terminal') !== false); ?></span> Terminal Leave</div>
                <?php if ($reason !== ''): ?>
                    <div class="mt-8"><span class="bold">Details:</span> <?= e($reason); ?></div>
                <?php endif; ?>
            </td>
        </tr>

        <tr>
            <td colspan="2"><span class="bold">6.C NUMBER OF WORKING DAYS APPLIED FOR</span><br><br><div style="text-align: center;">
                                <?= e((int)$working_days); ?> <?= (int)$working_days == 1 ? 'Day' : 'Days'; ?>
                            </div></td>
            <td colspan="3">
                <span class="bold">6.D COMMUTATION</span><br>
                <div class="label-row"><span class="checkbox"><?= chk($commutation === 'NOT REQUESTED'); ?></span> Not Requested</div>
                <div class="label-row"><span class="checkbox"><?= chk($commutation === 'REQUESTED'); ?></span> Requested</div>
            </td>
        </tr>

        <tr>
            <td colspan="2"><span class="bold" >INCLUSIVE DATES</span><br><br>    
            <div style="text-align: center;"><?= e($date_from); ?><?= ($date_from !== '' && $date_to !== '') ? ' to ' : ''; ?><?= e($date_to); ?></div>
            </td>
            <td colspan="3" class="text-center">
                <div class="sig-box">
                    <div class="sig-img-wrap">
                        <?php if ($applicant_esig !== ''): ?>
                            <img class="myesig" src="<?= base_url('uploads/esig/' . rawurlencode($applicant_esig)); ?>" alt="">
                        <?php endif; ?>
                    </div>
                    <div class="sig-name"><?= e($applicant_name); ?></div>
                    <div class="tiny">(Signature of Applicant)</div>
                </div>
            </td>
        </tr>

        <tr><td colspan="5" class="section-head">7. DETAILS OF ACTION ON APPLICATION</td></tr>
        <tr>
            <td colspan="2" class="bold">7.A CERTIFICATION OF LEAVE CREDITS</td>
            <td colspan="3" class="bold">7.B RECOMMENDATION</td>
        </tr>
        <tr>
            <td colspan="2">
                <div>As of ______________________</div>
                <table class="form mt-6">
                    <colgroup>
                        <col style="width:38%;"><col style="width:31%;"><col style="width:31%;">
                    </colgroup>
                    <tr><td></td><td class="text-center bold">Vacation Leave</td><td class="text-center bold">Sick Leave</td></tr>
                    <tr><td>Total Earned</td><td><?= e($vacation_balance); ?></td><td><?= e($sick_balance); ?></td></tr>
                    <tr><td>Less this application</td><td><?= e($vacation_less); ?></td><td><?= e($sick_less); ?></td></tr>
                    <tr><td>Balance</td><td><?= e($vacation_after); ?></td><td><?= e($sick_after); ?></td></tr>
                </table>
                <div class="sig-box mt-10">
                    <div class="sig-img-wrap">
                        <?php if ($show_cert_signature && $cert_esig !== ''): ?>
                            <img class="myesig" src="<?= base_url('uploads/esig/' . rawurlencode($cert_esig)); ?>" alt="">
                        <?php endif; ?>
                    </div>
                    <?php if ($show_cert_name): ?>
                        <div class="sig-name"><?= e($certifier_name); ?></div>
                        <div><?= e($cert_position); ?></div>
                    <?php else: ?>
                        <div class="sig-name">&nbsp;</div>
                        <div>&nbsp;</div>
                    <?php endif; ?>
                </div>
            </td>
            <td colspan="3">
                <div class="label-row"><span class="checkbox"><?= chk($show_rec_signature); ?></span> For approval</div>
                <div class="label-row mt-6"><span class="checkbox">&#9744;</span> For disapproval due to:</div>
                <div class="muted-line"></div>
                <div class="sig-box mt-10">
                    <div class="sig-img-wrap">
                        <?php if ($show_rec_signature && $rec_esig !== ''): ?>
                            <img class="myesig" src="<?= base_url('uploads/esig/' . rawurlencode($rec_esig)); ?>" alt="">
                        <?php endif; ?>
                    </div>
                    <?php if ($show_rec_name): ?>
                        <div class="sig-name"><?= e($recommender_name); ?></div>
                        <div><?= e($rec_position); ?></div>
                    <?php else: ?>
                        <div class="sig-name">&nbsp;</div>
                        <div>&nbsp;</div>
                    <?php endif; ?>
                </div>
            </td>
        </tr>

        <tr>
            <td colspan="2" class="bold">7.C APPROVED FOR:</td>
            <td colspan="3" class="bold">7.D DISAPPROVED DUE TO:</td>
        </tr>
        <tr>
            <td colspan="2">
                <div><?= $is_approved ? e($approved_with_pay) : ''; ?><?= $is_approved ? ' days with pay' : 'days with pay'; ?></div>
                <div class="mt-6"><?= $is_approved ? e($approved_without_pay) : ''; ?><?= $is_approved ? ' days without pay' : 'days without pay'; ?></div>
                <div class="mt-6">others (Specify): <?= $is_approved ? e($approved_others) : ''; ?></div>
                <div class="sig-box mt-12">
                    <div class="sig-img-wrap">
                        <?php if ($show_app_signature && $app_esig !== ''): ?>
                            <img class="myesig" src="<?= base_url('uploads/esig/' . rawurlencode($app_esig)); ?>" alt="">
                        <?php endif; ?>
                    </div>
                    <?php if ($show_app_name): ?>
                        <div class="sig-name"><?= e($approver_name); ?></div>
                        <div><?= e($app_position); ?></div>
                    <?php else: ?>
                        <div class="sig-name">&nbsp;</div>
                        <div>&nbsp;</div>
                    <?php endif; ?>
                </div>
            </td>
            <td colspan="3" style="min-height:110px;"><?= $is_rejected ? e($disapproval_reason) : ''; ?></td>
        </tr>
    </table>
</div>
</body>
</html>
