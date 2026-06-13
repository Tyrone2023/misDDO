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
            .aip_generate  .rrr{
                margin-bottom:20px;
                font-family:"Calibri", sans-serif;
            }
            .aip_generate  .rrr th{
                background-color:#fff;
                color:#000;
            }
            .aip_generate  .rrr td{
                text-align:left;
            }
            .rsign{
                float:left;
                margin-right:30px;
                margin-top:30px;
                position:relative;
            }
            .rsign span{
                display:block;
                line-height:1em;
            }
            .rsign .chona{
                position:absolute;
                left:20px;
                top:30px;
            }
            .rsign .feb{
                position:absolute;
                top:40px;
                width:50px;
            }
            @page {
            size: A4;
            margin: 50px 0;
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
                                                            $jobTypes = [
                                                                1 => '- Elementary',
                                                                2 => '- Secondary',
                                                                3 => '- Junior High School',
                                                                4 => '- Senior High School',
                                                                5 => '- kindergarten',
                                                                6 => '- IPED Elementary',
                                                                7 => '- IPED Secondary',
                                                                8 => '- IPED Junior High School',
                                                                9 => '- IPED Senior High School',
                                                                10 => '- SNED',
                                                                
                                                            ];
                                                            $job = $this->Common->two_cond_select('hris_jobvacancy','jobID,job_type,jobTitle, jvStatus,sy','sy',date('Y'),'jvStatus','Open');

                                                        ?>



    <body class="aip_generate" id="printTable">

        <div class="cert">
            <img class="logo" src="<?= base_url(); ?>assets/images/report/ke.png" alt="">
            <p style="margin-bottom:0;">
            <span class="rp">Republic of the Philippines</span><br />
                <span class="de">Department of Education</span><br />
                <span class="r">Region XI</span><br />
                <span class="r">Schools Division of <?php echo $settings->division; ?></span>
            </p>

            <div class="hr" style="margin:10px 0"></div>
            <h4 style="margin:40px 0">SUMMARY OF APPLICANTS FOR TEACHER I POSITIONS</h4>

            <table class="rrr" id='myTable'>
              <tr>
                <th>NO.</th>
                <th>SCHOOL</th>
                <th>DISTRICT</th>
                <th>NO. OF APPLICANTS</th>
                <th>DISQUALIFIED</th>
                <th>QUALIFIED</th>
              </tr>

              <?php foreach($job as $jrow){ ?>
    <tr>
        <td colspan="7" style="background:#f1c40f; padding:10px 15px; color:#fff">
            <?= strtoupper($jrow->jobTitle.' '.$jobTypes[$jrow->job_type] ?? ''); ?>
        </td>
    </tr>
<!-- back it to org. summary -->
<?php 
                $c=1;
                $app = $this->Common->two_cond_select_gb('hris_applications','jobID,app_year,district,pre_school','app_year',date('Y'),'jobID',$jrow->jobID,'pre_school');
                foreach($app as $row){
                $school = $this->Common->one_cond_row('schools','schoolID',$row->pre_school);
                $app_count = $this->Common->two_cond_count_row('hris_applications','pre_school',$row->pre_school,'jobID',$jrow->jobID);
                $app_count_dq = $this->Common->three_cond_count_row('hris_applications','pre_school',$row->pre_school,'jobID',$jrow->jobID,'dq',2);
                $app_count_q = $this->Common->three_cond_count_row('hris_applications','pre_school',$row->pre_school,'jobID',$jrow->jobID,'dq',1);
              ?>
              <tr>
                <td><?= $c++; ?></td>
                <td><?= $school->schoolName; ?></td>
                <td><?= $row->district; ?></td>
                <td style="text-align:center"><?= $app_count->num_rows(); ?></td>
                <td style="text-align:center"><?= $app_count_dq->num_rows(); ?></td>
                <td style="text-align:center"><?= $app_count_q->num_rows(); ?></td>
              </tr>
              <?php }} ?>

                
            </table>

         
        
        
        </div>

       


        
    </body>
</html>