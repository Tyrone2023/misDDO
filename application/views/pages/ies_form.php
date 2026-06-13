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
    <link href="<?= base_url(); ?>assets/css/my_style.css" rel="stylesheet" type="text/css" />
    <link href="<?= base_url(); ?>assets/css/renren_print_style.css" media="print" rel="stylesheet" type="text/css" />
    <title><?= $title; ?></title>
</head>
<body>
    
  <div class="wrap">
    <div class="inner">
        <?php $ms = $this->Common->one_cond_row_select('mis_settings','division,settingsID','settingsID',1); ?>
        <?php $name = $ap->FirstName.' '.$ap->MiddleName.' '.$ap->LastName.' '.$ap->NameExtn; ?>

        <h4>Annex G-1</h4>
        <h1><?= $title; ?></h1>
        <ul class="info">
            <li><span class="n">Name of Applicant</span><span class="la">: <?= strtoupper($name); ?></span></li>
            <li><span class="n">Position Applied for</span><span class="la">: <?= strtoupper($job->jobTitle); ?></span></li>
            <li><span class="n">Schools Division Office</span><span class="la">: <?= strtoupper($ms->division); ?></span></li>
            <li><span class="n">Contact Number:</span><span  class="la">: <?= $ap->empMobile; ?></span></li>
            <li><span class="n">Job Group/SG-Level:</span><span class="la">: </span></li>
        </ul>

        <ul class="app_code">
            <li>Applicant No.<span>: <?= $rate->appID; ?></span></li>
        </ul>

        <div class="blocker"></div>


        <table>
            <tr>
                <th rowspan="2">Criteria</th>
                <th rowspan="2">Weight Allocation</th>
                <th colspan="3">Applicant's Actual Qualifications</th>
            </tr>
            <tr>
                <th>Details of Applicant's Qualifications<br /><span class="small">(Relevant documents submitted <br />additional requirments, notes<br /> of HRMPSB Members)</span></th>
                <th>Computation</th>
                <th>Actual Score</th>
            </tr>
            <tr>
                <td>Education</td>
                <td class="txtcenter">10</td>
                <td></td>
                <td></td>
                <td class="txtcenter"><?php  if($rate->education != 0.00001){echo $rate->education; } ?></td>
            </tr>
            <tr>
                <td>Training</td>
                <td class="txtcenter">10</td>
                <td></td>
                <td></td>
                <td class="txtcenter"><?php  if($rate->training != 0.00001){echo $rate->training; } ?></td>
            </tr>
            <tr>
                <td>Experience</td>
                <td class="txtcenter">10</td>
                <td></td>
                <td></td>
                <td class="txtcenter"><?php  if($rate->experience != 0.00001){echo $rate->experience; } ?></td>
            </tr>
            <tr>
                <td>PBET/LET/LEPT Rating</i></td>
                <td class="txtcenter">10</td>
                <td></td>
                <td></td>
                <td class="txtcenter"><?php  if($rate->let_rating != 0.00001){echo $rate->let_rating; } ?></td>
            </tr>
            <tr>
                <td>PBET/LET/LEPT RatingPPST Classroom Observable<br /> Indicators <br /><i>(Demonstration Teaching <br />using COT-RSP)</i></td>
                <td class="txtcenter">35</td>
                <td></td>
                <td></td>
                <td class="txtcenter"><?php  if($rate->demo_rating != 0.00001){echo $rate->demo_rating; } ?></td>
            </tr>
            <tr>
                <td>PPST Non-classroom<br /> Observable Indicators<br /> <i>(Teacher Reflection)</i></td>
                <td class="txtcenter">25</td>
                <td></td>
                <td></td>
                <td class="txtcenter"><?php  if($rate->tr_rating != 0.00001){echo $rate->tr_rating; } ?></td>
            </tr>
            <tr>
                <td class="total">TOTAL</td>
                <td class="total">100</td>
                <td class="total"></td>
                <td class="total"></td>
                <td class="txtcenter"><?= $rate->total_points ? $rate->total_points: ''; ?></td>
            </tr>
        </table>








        <p>I hereby attest to the conduct of the paplication and assesment process in accordance with the applicable guidelines; and acknowlede, upon discussion with the Human resouce Merit Promotion and Selection Board(HRMPSB), the results of the comparative assessment and the points given to me based on my qualifications and submitted documentary requirements for <b><?= $job->jobTitle; ?></b> under <b>DEPED <?= strtoupper($ms->division); ?></b>.</p>
        <p>Furthermore, I hereby affix my signature in this Form to attest to the objective and judicious conduct of the HRMPSB evaluation through Open Ranking System.</p>

        <div class="blocker"></div>
        
        <h3><span class="an"><?= strtoupper($name); ?></span>
        <br />Name and Signature of Applicant
        <br /><span class="d">Date: <?= date("m-d-Y"); ?></span></h3>
        <div class="blocker"></div>
        <h5>Attested:</h5>

        <h6>HRMPSB Chair</h6>

        <div class="blocker"></div>

    </div>
  </div>




    
</body>
</html>