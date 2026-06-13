
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
    <link href="https://db.onlinewebfonts.com/a/0nH393RJctHgt1f2YvZvyruY" rel="stylesheet" type="text/css"/>
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
    <div class="hwrap">
    <img class="logo" src="<?= base_url(); ?>assets/images/ke.png" alt="">
        <p class="textwrap">
        <span class="rp">Republic of the Philippines</span>
            <span class="de">Department of Education</span>
            <span class="r">Region XI</span>
            <span class="r">Schools Division of Davao Oriental</span>
        </p>
</div>
<div class="blocker"></div>
    
  <div class="wrap">
    <div class="inner">
        <h5>Annex 1-4</h5>
        <h1 style="font-size:20px"><?= $title; ?></h1>

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
                <th rowspan="2" colspan="2">Name of Applicant</th>
                <th rowspan="2">Application Code</th>
                <th colspan="9">COMPARATIVE ASSESSMENT RESULTS</th>
                <th rowspan="2">Remarks</th>
                <th colspan="2">For Background<br />Investigation<br />(Y/N)</th>
                <th rowspan="2">For<br />Recommendation<br /><i>(To be filled-out by the<br /> Schools Division<br /> Superintendent;<br />Please sign opposite<br /> the name of the<br /> applicant)</i></th>
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
            <?php 
            //$data = array("CRODUA, IRENEO O JR.","AMIGO, EDGARDO V.","JANNDEL BUOT","EDONG, JOSELITO");
            $no = 1;
            foreach($car as $row){
                $b = $this->Common->one_cond_row('hris_applicant', 'record_no', $row->record_no);
                if(!empty($b)){
                    $ap = $this->Common->one_cond_row('hris_applicant', 'record_no', $row->record_no);
                }else{
                    $ap = $this->Common->one_cond_row('hris_staff', 'IDNumber', $row->record_no);
                }
                if(!empty($ap)){
            ?>
            <tr>
                <td><?= $no++; ?></td>
                <td style="white-space: nowrap; text-align:left"><?= strtoupper($ap->FirstName .' '.$ap->FirstName. ' ' . $ap->LastName); ?></td>
                <td><?= $row->record_no; ?> </td>
                <!-- <td><?= $ap->resBarangay; ?>, <?= $ap->resCity; ?></td> -->
                <td><?= ($row->educ != 0.00001) ? $row->educ : ""; ?></td>
                <td><?= ($row->trainings != 0.00001) ? $row->trainings : ""; ?></td>
                <td><?= ($row->experience != 0.00001) ? $row->experience : ""; ?></td>
                <td><?= ($row->performance != 0.00001) ? number_format($row->performance, 2) : ""; ?></td>
                <td><?= ($row->oa != 0.00001) ? $row->oa : ""; ?></td>
                <td><?= ($row->ae != 0.00001) ? $row->ae : ""; ?></td>
                <td><?= ($row->ald != 0.00001) ? $row->ald : ""; ?></td>
                <td><?= number_format($row->interview+$row->written+$row->skills, 2); ?></td>
                <td><?= number_format(($row->total_points != 0.00001) ? $row->total_points : "", 3); ?></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <?php }} ?>
        </table>
        
        <p class="prep">Prepared by the HRMPSB <span>Appointment conferred by:</span><br />(All members should affix signature) </p>

        <table class="sign">
            <?php $rqa_sign = $this->Common->one_cond_row('hris_rqa_sign', 'id', $job->sign); if($rqa_sign->nr == 1){?>
                <tr>
                    <td><img class="isig" src="<?= base_url(); ?>assets/isig/<?= $sign->m1_sign; ?>.png" alt=""><span><?= $sign->m1n; ?></span><br /><?= $sign->m1p; ?><br />Member</td>
                    <td><img class="isig" src="<?= base_url(); ?>assets/isig/<?= $sign->m2_sign; ?>.png" alt=""><span><?= $sign->m2n; ?></span><br /><?= $sign->m2p; ?><br />Member</td>
                    <td><img class="isig" src="<?= base_url(); ?>assets/isig/<?= $sign->m3_sign; ?>.png" alt=""><span><?= $sign->m3n; ?></span><br /><?= $sign->m3p; ?><br />Member</td>
                    <td style="color:#fff;"><span style="border:0 !important"><?= $sign->sdsn; ?></span><br /><?= $sign->sdsp; ?></td>
                    <td><img class="isig" src="<?= base_url(); ?>assets/isig/<?= $sign->sds_sign; ?>.png" alt=""><span><?= $sign->sdsn; ?></span><br /><?= $sign->sdsp; ?><br /><br /></td>
                </tr>
                <tr>
                    <td><img class="isig" src="<?= base_url(); ?>assets/isig/<?= $sign->m4_sign; ?>.png" alt=""><span><?= $sign->m4n; ?></span><br /><?= $sign->m4p; ?><br />Member</td>
                    <td><img class="isig" src="<?= base_url(); ?>assets/isig/<?= $sign->m5_sign; ?>.png" alt=""><span><?= $sign->m5n; ?></span><br /><?= $sign->m5p; ?><br />Member</td>
                    <td><img class="isig" src="<?= base_url(); ?>assets/isig/<?= $sign->asds_sign; ?>.png" alt=""><span><?= $sign->asdsn; ?></span><br /><?= $sign->asdsp; ?><br />Chairperson</td>
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