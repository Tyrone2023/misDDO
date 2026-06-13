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
            4 => '- Senior High School'
        ];

        $pt = $this->Common->one_cond_row('hris_positions','title',$job->jobTitle);
        $ptp = $this->Common->one_cond_row('hris_position_points','id',$pt->bracket);

        
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
                <td class="wb"><?= $job->assign; ?></td>
                 <td class="ren"></td>
                <td>Date of Final Deliberation:</td>
                <td class="wb"><?= $sign->pdate; ?></td>
            </tr>
        </table>

        <table class="data">
            <tr>
                <th rowspan="2">No.</th>
                <th rowspan="2">Fullname</th>
                <th rowspan="2">Application Code</th>
                <th rowspan="2">Address</th>
                <th rowspan="2">Contact</th>
                <th colspan="9">COMPARATIVE ASSESSMENT RESULTS</th>
                <th rowspan="2">Remarks</th>
                <th colspan="2">For Background<br />Investigation<br />(Y/N)</th>
                <th rowspan="2">For<br />Appointment<br /><i>(To filled-out by the<br />Appointing<br />Officer/Authority,<br />Please sign opposite<br />the name of the applicant)</th>
                <th rowspan="2">For<br />Appointment<br/><i>Please identify period of<br /> Probation (6 months or 1<br /> year) in accordance with<br /> Section F of<br /> DO 019,s.2022</i></th>
            </tr>
            <tr>
                <th>Education <br />(<?= $ptp->educ; ?> pts)</th>
                <th>Training <br />(<?= $ptp->tr; ?> pts)</th>
                <th>Experience <br /> (<?= $ptp->exp; ?> pts)</th>
                <th>Performance<br /> (<?= $ptp->per; ?> pts)</th>
                <th>Outstanding<br/>Accomplishments<br /> (<?= $ptp->oa; ?> pts)</th>
                <th>Application of <br />Education<br /> (<?= $ptp->ae; ?> pts)</th>
                <th>Application of <br />L&D <br /> (<?= $ptp->ald; ?> pts)</th>
                <th>Potential<br /> (<?= $ptp->potential; ?> pts)</th>
                <th>Total<br />(100 pts)</th>
                <th>Yes</th>
                <th>No</th>
            </tr>
            <?php foreach ($grouped_applicants as $municipality => $group): ?>
            <tr>
                <td colspan="17" style="text-align:left"><?= strtoupper(htmlspecialchars($municipality)); ?></td>
            </tr>
            <?php $c=1; foreach ($group as $person): ?>
            <!-- <?php $oa = $this->Common->two_cond_count_row('hris_applications','empEmail', $person->renren,'app_year',date('Y')-1); ?> -->
            <tr>
                <!-- <?php $multi = $this->Common->one_cond_count_row('hris_rating_none','appID',$person->appID); ?> -->
                <td><?= $c++; ?></td>
                <td style="white-space: nowrap; text-align:left"><?= strtoupper($person->FirstName . ' ' . $person->LastName); ?></td>
                <td <?php //if($multi->num_rows() > 1){echo 'style="background-color:#0054ff !important"';} ?> ><?= strtoupper($person->code); ?></td>
                <td><?= strtoupper($person->brgy); ?></td>
                <td><?= strtoupper($person->contactNo); ?></td>
                <td><?= ($person->educ != 0.00001) ? $person->educ : ""; ?></td>
                <td><?= ($person->trainings != 0.00001) ? $person->trainings : ""; ?></td>
                <td><?= ($person->experience != 0.00001) ? $person->experience : ""; ?></td>
                <td><?= ($person->performance != 0.00001) ? number_format($person->performance, 2) : ''; ?></td>
                <td><?= ($person->oa != 0.00001) ? $person->oa : ""; ?></td>
                <td><?= ($person->ae != 0.00001) ? $person->ae : ""; ?></td>
                <td><?= ($person->ald != 0.00001) ? $person->ald : ""; ?></td>
                <td><?= number_format($person->interview+$person->written+$person->skills, 2); ?></td>
                <td><?= ($person->total_points != 0.00001) ? number_format($person->total_points, 3) : ''; ?></td>
                <td style="font-size:8px"><?= $person->position; ?></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <?php endforeach; ?>
        <?php endforeach; ?>

        </table>
        
        <p class="prep">Prepared by the HRMPSB <span>Appointment conferred by:</span><br />(All members should affix signature) </p>
        <table class="sign">
            <?php $rqa_sign = $this->Common->one_cond_row('hris_rqa_sign', 'id', $job->sign); if($rqa_sign->nr == 1){?>
                <tr>
                    <td><?php if($this->uri->segment(4) == 0){?><img class="isig" src="<?= base_url(); ?>assets/isig/<?= $sign->m1_sign; ?>.png" alt=""><?php } ?><span><?= $sign->m1n; ?></span><br /><?= $sign->m1p; ?><?= $sign->m1p ? '<br>Member' : '' ?></td>
                    <td><?php if($this->uri->segment(4) == 0){?><img class="isig" src="<?= base_url(); ?>assets/isig/<?= $sign->m2_sign; ?>.png" alt=""><?php } ?><span><?= $sign->m2n; ?></span><br /><?= $sign->m2p; ?><?= $sign->m2p ? '<br>Member' : '' ?></td>
                    <td><?php if($this->uri->segment(4) == 0){?><img class="isig" src="<?= base_url(); ?>assets/isig/<?= $sign->m3_sign; ?>.png" alt=""><?php } ?><span><?= $sign->m3n; ?></span><br /><?= $sign->m3p; ?><?= $sign->m3p ? '<br>Member' : '' ?></td>
                    <td></td>
                    <td><?php if($this->uri->segment(4) == 0){?><img class="isig" src="<?= base_url(); ?>assets/isig/<?= $sign->sds_sign; ?>.png" alt=""><?php } ?><span><?= $sign->sdsn; ?></span><br /><?= $sign->sdsp; ?><br /><br /></td>
                </tr>
                <tr>
                    <td><?php if($this->uri->segment(4) == 0){?><img class="isig" src="<?= base_url(); ?>assets/isig/<?= $sign->m4_sign; ?>.png" alt=""><?php } ?><span><?= $sign->m4n; ?></span><br /><?= $sign->m4p; ?><?= $sign->m4p ? '<br>Member' : '' ?></td>
                    <td><?php if($this->uri->segment(4) == 0){?><img class="isig" src="<?= base_url(); ?>assets/isig/<?= $sign->m5_sign; ?>.png" alt=""><?php } ?><span><?= $sign->m5n; ?></span><br /><?= $sign->m5p; ?><?= $sign->m5p ? '<br>Member' : '' ?></td>
                    <td><?php if($this->uri->segment(4) == 0){?><img class="isig" src="<?= base_url(); ?>assets/isig/<?= $sign->asds_sign; ?>.png" alt=""><?php } ?><span><?= $sign->asdsn; ?></span><br /><?= $sign->asdsp; ?><br />Chairperson</td>
                    <td></td>
                    <td></td>
                </tr>
            <?php }else{ ?>
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
                    <td><img class="isig" src="<?= base_url(); ?>assets/isig/<?= $sign->m8s; ?>.png" alt=""><span><?= $sign->m8n; ?></span><br /><?= $sign->m8p; ?><br />Member</td>
                    <td><img class="isig" src="<?= base_url(); ?>assets/isig/<?= $sign->asds_sign; ?>.png" alt=""><span><?= $sign->asdsn; ?></span><br /><?= $sign->asdsp; ?><br />Chairperson</td>
                    <td></td>
                </tr>
            <?php } ?>
        </table>

        

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