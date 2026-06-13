<?php if($this->session->logged_in == false){
redirect(base_url().'log_in');
} ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Responsive bootstrap 4 admin template" name="description" />
    <meta content="Coderthemes" name="author" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <!-- App favicon -->
    <link rel="shortcut icon" href="<?= base_url(); ?>assets/images/hris.ico">
    <link href="<?= base_url(); ?>assets/css/renren_style.css" rel="stylesheet" type="text/css" />
    <title><?= $title; ?></title>
</head>
<body>
    <?php 
         $jobTypes = [
                    1 => '- Elementary',
                    2 => '- Secondary',
                    3 => '- Junior High School',
                    4 => '- Senior High School',
                    5 => '- Kindergarten',
                    6 => '- IPED Elementary',
                    7 => '- IPED Secondary',
                    8 => '- IPED Junior High School',
                    9 => '- IPED Senior High School',
                    10 => '- SNED',
                ];

    ?>


  <div class="wrap">
    <div class="inner">
        <h5>Annex 1-1</h5>
        <h1><?= $title; ?><br />PER MUNICIPALITY</h1>

        <table class="toptable">
            <tr>
                <td>Position</td>
                <td class="wb"><?= $job->jobTitle; ?> <?= $jobTypes[$job->job_type] ?? ''; ?></td>
                <td class="ren"></td>
                <td>Plantilla Item Number:</td>
                <td class="wb"></td>
            </tr>
            <tr>
                <td>Office/Bureau/Service/Unit where the vacancy exists</td>
 <td class="wb">Division of <?php echo $settings->division; ?></td>
                 <td class="ren"></td>
                <td>Date of Final Deliberation:</td>
                <td class="wb"><?= $sign->pdate; ?></td>
            </tr>
            <tr>
                <td>Municipality</td>
                <td class="wb"><?= $this->input->get('mun'); ?></td>
            </tr>
        </table>

        <table class="data">
            <tr>
                <th rowspan="2" colspan="2">Application Code</th>
                <th rowspan="2">Barangay</th>
                <th colspan="7">COMPARATIVE ASSESSMENT RESULTS</th>
                <th rowspan="2">Remarks</th>
                <th colspan="2">For Background<br />Investigation<br />(Y/N)</th>
                <th rowspan="2">For<br />Appointment<br /><i>(To filled-out by the<br />Appointing<br />Officer/Authority,<br />Please sign opposite<br />the name of the applicant)</th>
                <th rowspan="2">For<br />Appointment<br/><i>(Based on availabilty<br />of PBET/LET/LEPT)</i></th>
            </tr>
            <tr>
                <th>Education <br />(10 pts)</th>
                <th>Training <br />(10 pts)</th>
                <th>Experience <br /> (10 pts)</th>
                <th>PBET/LET/<br />LEPT RATING<br />(10 pts)</th>
                <th>PPST COIs<br />(Classroom<br /> Observation)<br />(35 pts)</th>
                <th>PPST NCOIs<br />(Teacher Reflection)<br />(25 pts)</th>
                <th>Total<br />(100 pts)</th>
                <th>Yes</th>
                <th>No</th>
            </tr>
            <?php foreach ($grouped_applicants as $brgy => $group): ?>
            <tr>
                <td colspan="17" style="text-align:left"><?= htmlspecialchars(str_replace('<br />', ' ', $brgy)); ?></td>
            </tr>
            <?php $c=1; foreach ($group as $person): ?>
            <tr>
                <td><?= $c++; ?></td>
                <td><?= $person->code; ?></td>
                <td><?= $person->brgy; ?></td>
                <td><?= ($person->education != 0.00001) ? $person->education : ""; ?></td>
                <td><?= ($person->training != 0.00001) ? $person->training : ""; ?></td>
                <td><?= ($person->experience != 0.00001) ? $person->experience : ""; ?></td>
                <td><?= ($person->let_rating != 0.00001) ? $person->let_rating : ""; ?></td>
                <td><?= ($person->demo_rating != 0.00001) ? $person->demo_rating : ""; ?></td>
                <td><?= ($person->tr_rating != 0.00001) ? $person->tr_rating : ""; ?></td>
                <td><?= $person->total_points ? number_format($person->total_points, 2) : ""; ?></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <?php endforeach; ?>
        <?php endforeach; ?>

        </table>
        
        <p class="prep">Prepared by the HRMPSB <span>Appointment conferred by:</span><br />(All members should affix signature) </p>
        <?php $rqa_sign = $this->Common->one_cond_row('settings', 'id', 8); if($rqa_sign->status == 0){?>
        <table class="sign">
            <tr>
                <td><img class="isig" src="<?= base_url(); ?>assets/isig/<?= $sign->m1_sign; ?>.png" alt=""><span><?= $sign->m1n; ?></span><br /><?= $sign->m1p; ?><br />Member</td>
                <td><img class="isig" src="<?= base_url(); ?>assets/isig/<?= $sign->m2_sign; ?>.png" alt=""><span><?= $sign->m2n; ?></span><br /><?= $sign->m2p; ?><br />Member</td>
                <td><img class="isig" src="<?= base_url(); ?>assets/isig/<?= $sign->m3_sign; ?>.png" alt=""><span><?= $sign->m3n; ?></span><br /><?= $sign->m3p; ?><br />Member</td>
                <td><img class="isig" src="<?= base_url(); ?>assets/isig/<?= $sign->m4_sign; ?>.png" alt=""><span><?= $sign->m4n; ?></span><br /><?= $sign->m4p; ?><br />Member</td>
                <td></td>
                <td><img class="isig" src="<?= base_url(); ?>assets/isig/<?= $sign->sds_sign; ?>.png" alt=""><span><?= $sign->sdsn; ?></span><br /><?= $sign->sdsp; ?><br /><br /></td>
            </tr>
            <tr>
                <td><img class="isig" src="<?= base_url(); ?>assets/isig/<?= $sign->m5_sign; ?>.png" alt=""><span><?= $sign->m5n; ?></span><br /><?= $sign->m5p; ?><br />Member</td>
                <td><img class="isig" src="<?= base_url(); ?>assets/isig/<?= $sign->m6_sign; ?>.png" alt=""><span><?= $sign->m6n; ?></span><br /><?= $sign->m6p; ?><br />Member</td>
                <td><img class="isig" src="<?= base_url(); ?>assets/isig/<?= $sign->m7s; ?>.png" alt=""><span><?= $sign->m7n; ?></span><br /><?= $sign->m7p; ?><br />Member</td>
                <td><?php if($sign->m8s != ''){?><img class="isig" src="<?= base_url(); ?>assets/isig/<?= $sign->m8s; ?>.png" alt=""><span><?= $sign->m8n; ?></span><br /><?= $sign->m8p; ?><br />Member<?php } ?></td>
                <td><img class="isig" src="<?= base_url(); ?>assets/isig/<?= $sign->asds_sign; ?>.png" alt=""><span><?= $sign->asdsn; ?></span><br /><?= $sign->asdsp; ?><br />Chairperson</td>
                <td></td>
            </tr>
        </table>
        <?php } ?>

        

    </div>
  </div>

  
    <!-- <p class="down aa"><b>Notes and Instruction for the HRMO:</b></p>
    <p class="down">A)For the purpose of posting the CAR, column C(Name of the applicant) and columns L to P (Remarks to probation status) shall be concealed in accordance with RA no. 10163 (Data Privacy Act.)</p>
    <p class="down">The only information that shall be made public are the Application Code, Comparative Assessment Results(Component from Education to PPST NCOIs) and the total scores of the applicants.</p>
    <p class="down">b)If the information does not apply to the applicant, please put N/A</p>
    <p class="down dd">C) Applicants who failed to appear in any phase of the Open Ranking process and other evaluative assessments, and/or have withdrawn their application shall be provided with a notation beside the application code(e.g., withdrawn application, etc.)</p>
 -->



    
</body>
</html>