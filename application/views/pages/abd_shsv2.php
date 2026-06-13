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
    
  <div class="wrap">
    <div class="inner">
        <table class="data">
            <tr>
                <th rowspan="2" colspan="2">Name of Applicant</th>
                <th rowspan="2">Barangay</th>
                <th rowspan="2">Application Code</th>
                <th rowspan="2">Contact</th>
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