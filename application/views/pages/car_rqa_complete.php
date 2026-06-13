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
    <style>
        .sign td img.msign{
        position:relative;
        top:25px;
        left:60px;
        display:block;

    }
    </style>
    
</head>
<body>
<h1 style="font-size:20px">FOR SCHOOL YEAR <?= date('Y'); ?> - <?= date('Y')+1; ?></h1>

<?php 
        $jobTypes = [
            1 => '- Elementary',
            2 => '- Secondary',
            3 => '- Junior High School',
            4 => '- Senior High School'
        ];
    ?>
    
  <div class="wrap">
    <div class="inner">
        <h5>Annex 1-1</h5>
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
                 <td class="wb">Division of <?php echo $settings->division; ?></td>
                <td class="ren"></td>
                <td>Date of Final Deliberation:</td>
                <td class="wb"><?= $sign->pdate; ?></td>
            </tr>
        </table>

        <table class="data">
            <tr>
                <th rowspan="2">No.</th>
                <th rowspan="2">Name of Applicant</th>
                <th rowspan="2">Application Code</th>
                 <th rowspan="2">Barangay</th>
                <th rowspan="2">Municipality</th>
        <th rowspan="2">Province</th>
                <th rowspan="2">Contact</th>

                <?php if($job->job_type != 1){ ?>
                <th rowspan="2">Specialization</th>
                <?php } ?>
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
          <?php if (empty($is_excel_export)) flush(); $no = 1; ?>
<?php foreach($car as $row): ?>
<?php
    // $ap = $applicants[$row->record_no] ?? $staff[$row->record_no] ?? null;
    // if (!$ap) continue;

    //   $brgy = isset($ap->resBarangay) ? strtoupper($ap->resBarangay) : (isset($ap->perBarangay) ? strtoupper($ap->perBarangay) : '');
    //     $city = isset($ap->resCity) ? strtoupper($ap->resCity) : (isset($ap->perMunicipality) ? strtoupper($ap->perMunicipality) : '');
    //     $prov = isset($ap->resProvince) ? strtoupper($ap->resProvince) : (isset($ap->perProvince) ? strtoupper($ap->perProvince) : '');

    //     // Mobile number
    //     $mobile = isset($ap->contactNo) ? strtoupper($ap->contactNo) : '';
    $oa = $this->Common->two_cond_count_row('hris_applications','empEmail', $row->empEmail,'app_year','2024');
?>
<tr>
    <td><?= $no++; ?></td>
    <td class="name"><?= rqa_applicant_name($row); ?></td>
    <td><?= $row->code; ?></td>
    <td><?= $row->brgy; ?></td>
    <td><?= $row->resCity; ?></td>
    <td><?= $row->province; ?></td>
    <td><?= $row->contactNo; ?></td>
    <?php if($job->job_type != 1): ?>
        <?php
            $jobType = (int) $job->job_type;
            $specialization = $row->specialization ?? '';
            if(in_array($jobType, [3, 8, 16], true) && !empty($row->jhss)){
                $specialization = $row->jhss;
            } elseif(in_array($jobType, [4, 9, 11, 12, 13, 14], true) && !empty($row->shss)){
                $specialization = $row->shss;
            }
        ?>
        <td><?= $specialization; ?></td>
    <?php endif; ?>
    <td><?= ($row->education != 0.00001) ? $row->education : ""; ?></td>
    <td><?= ($row->training != 0.00001) ? $row->training : ""; ?></td>
    <td><?= ($row->experience != 0.00001) ? $row->experience : ""; ?></td>
    <td><?= ($row->let_rating != 0.00001) ? $row->let_rating : ""; ?></td>
    <td><?= ($row->demo_rating != 0.00001) ? $row->demo_rating : ""; ?></td>
    <td><?= ($row->tr_rating != 0.00001) ? $row->tr_rating : ""; ?></td>
    <td><?= $row->total_points ? number_format($row->total_points, 2) : ""; ?></td>
    <td style="font-size:8px"><?php if($oa->num_rows() >= 1){echo "SY 2024-2025 Applicant";} ?></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
</tr>
<?php endforeach; ?>

        </table>
        
        <p class="prep">Prepared by the HRMPSB <span>Appointment conferred by:</span><br />(All members should affix signature) </p>
        <?php $rqa_sign = $this->Common->one_cond_row('settings', 'id', 8); if($rqa_sign->status == 0){?>
        <table class="sign">
            <tr>
                <td><span><?= strtoupper($sign->m1n); ?></span><br /><?= $sign->m1p; ?><br />Member</td>
                <td><span><?= strtoupper($sign->m2n); ?></span><br /><?= $sign->m2p; ?><br />Member</td>
                <td><span><?= strtoupper($sign->m3n); ?></span><br /><?= $sign->m3p; ?><br />Member</td>
                <td><span><?= strtoupper($sign->m4n); ?></span><br /><?= $sign->m4p; ?><br />Member</td>
                <td></td>
                <td><span><?= strtoupper($sign->sdsn); ?></span><br /><?= $sign->sdsp; ?><br /><br /></td>
            </tr>
            <tr>
                <td><span><?= strtoupper($sign->m5n); ?></span><br /><?= $sign->m5p; ?><br />Member</td>
                <td><span><?= strtoupper($sign->m6n); ?></span><br /><?= $sign->m6p; ?><br />Member</td>
                <td><span><?= strtoupper($sign->m7n); ?></span><br /><?= $sign->m7p; ?><br />Member</td>
                <td><?php if($sign->m8n != ''){?><span><?= strtoupper($sign->m8n); ?></span><br /><?= $sign->m8p; ?><br />Member<?php } ?></td>
                <td><span><?= strtoupper($sign->asdsn); ?></span><br /><?= $sign->asdsp; ?><br />Chairperson</td>
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
