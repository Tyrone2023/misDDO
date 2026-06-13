<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta content="Responsive bootstrap 4 admin template" name="description" />
        <meta content="Coderthemes" name="author" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <!-- App favicon -->
        <link rel="shortcut icon" href="<?= base_url(); ?>assets/images/hris.ico">

        <!-- Plugins css-->
        <link href="<?= base_url(); ?>assets/css/renren.css" rel="stylesheet" type="text/css" />
        <link href="https://db.onlinewebfonts.com/a/0nH393RJctHgt1f2YvZvyruY" rel="stylesheet" type="text/css"/>
        <style>
            @page {
            size: A4;
            margin: 0;
            }
            @media print {
            html, body {
                width: 210mm;
                height: 297mm;
                font-size:14px !important;
            }
           

            .aip_generate .hr{
                margin:20px 0;
                width:100%;
            }

            .cert{
                width:90%;
                padding-top:1px;
            }
            .aip_generate .dra_aip th, 
            .aip_generate .dra_aip td {
                padding:10px 0;
            }

            .footer {
                position: fixed;
                margin: 0 auto; /* Only centers horizontally not vertically! */
                bottom: 0;
                width:86%;
            }
            
           
            }
        </style>
    
    </head>
    <?php 
        $y = date('Y');
        $school = $this->Common->one_cond_row('schools','schoolID',$this->session->c_id); 
        $validated = $this->Common->three_cond_not_equal_count_row('hris_applications', 'pre_school', $this->session->c_id,'app_year',$y,'appStatus','Application Submitted');
        //$validated = $this->Common->three_cond_not_equal_count_row('hris_applications', 'pre_school', $this->session->c_id,'app_year',$y,'dq',1);
        $app = $this->Common->two_cond_count_row('hris_applications', 'pre_school', $this->session->c_id,'app_year',$y);
    ?>



    <body class="aip_generate" id="printTable">

        <div class="cert">
            <img class="logo" src="<?= base_url(); ?>assets/images/report/ke.png" alt="">
            <p style="margin-bottom:0;">
            <span class="rp">Republic of the Philippines</span><br />
                <span class="de">Department of Education</span><br />
                <span class="r">Region XI</span><br />
                <span class="r">Schools Division of Davao Oriental</span><br /> 
                
            </p>
            <p style="line-height:1.2em;"> 
                <span class="sadress"><?= strtoupper($school->district); ?><br />
                <?= strtoupper($school->schoolName); ?><br />
                <?= strtoupper($school->sitio); ?> <?= strtoupper($school->brgy); ?>, <?= strtoupper($school->city); ?>, <?= strtoupper($school->province); ?></span> 
            </p>

            <div class="hr"></div>

            <p style="margin-bottom:20px; font-size:30px"><b>CERTIFICATION OF VALIDATION</b></p>

            <p style="text-align:justify; text-indent: 50px; margin-bottom:10px">This is to certify that we have carefully validated all the uploaded documents in the MIS against the submitted original documents of the applicants.</p>
            <p style="text-align:justify; text-indent: 50px; margin-bottom:10px">We also checked that all uploaded documents were found in the submitted original documents and vice versa.</p>

            <p style="text-align:justify; text-indent: 50px; margin-bottom:10px">This is to certify further that all applicants in the School's MIS account was <?php
                        $a = $app->num_rows();
                        $v = $validated->num_rows();
                        if($v != 0){
                        $per = round(($v / $a) * 100,0);
                        echo $per.'%';
                        }else{
                            echo '0%';
                        }
                        ?> validated. </p>

            <?php 
                foreach($job as $row){ 
                    $data = $this->Common->four_cond_not_equal('hris_applications', 'pre_school',$this->session->c_id,'app_year',date('Y'),'jobID',$row->jobID,'appStatus','Application Submitted','applicant_id','ASC');

                    if(!empty($data)){
            ?>
            <table class="dra_aip">
                <tr>
                    <th colspan="4" style="border-bottom:0; text-align:left"><?= strtoupper($row->jobTitle); ?></th>
                </tr>
                <tr>
                    <th>No.</th>
                    <th>Last Name</th>
                    <th>First Name</th>
                    <th>Middle Name </th>
                </tr>
                <?php 
                    $c = 1;
                    
                    foreach($data as $row){
                        $app = $this->Common->one_cond_row('hris_applicant','id',$row->applicant_id);
                        $job = $this->Common->one_cond_row('hris_jobvacancy','jobID',$row->jobID);
                        $school = $this->Common->one_cond_row('schools', 'schoolID',$this->session->c_id);
                        
                ?>
                <tr>
                        <td><?= $c++; ?>.</td>
                        <td><?= strtoupper($app->LastName); ?></td>
                        <td><?= strtoupper($app->FirstName); ?></td>
                        <td><?= strtoupper($app->MiddleName); ?></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <?php $s_id = $row->applicant_id; ?>

                </tr>
                <?php } ?>
                
            </table>
            <?php }} ?>
            <p style="text-align:justify; text-indent: 50px; margin-bottom:10px">Issued this<?php $d=date('Y-m-d'); ?> <?= date('d', strtotime($d)); ?>th day of <?= date('F', strtotime($d)); ?> </u> <?= date('Y', strtotime($d)); ?> at <?= strtoupper($school->schoolName); ?>, <?= strtoupper($school->district); ?></p>
            <br /><br />


            <p>____________________&nbsp; &nbsp; ____________________ &nbsp; &nbsp; ____________________</p>

            <br />
           
            <!-- <div class="footer">

                <div class="hr"></div>


                <div class="cafooter">
                    <img width="100%" src="<?= base_url(); ?>assets/images/f.png" alt="">
                    <div class="blocker"></div>
                </div>

            </div> -->
        
        
        
        
        </div>




    </body>
</html>