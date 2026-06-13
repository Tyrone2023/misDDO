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
            <h4 style="margin:40px 0">List of applicants who have applied By District</h4>

            <table class="rrr" id='myTable'>
                <tr>
                    <th>No.</th>
                    <th>District</th>
                    <th>kindergarten</th>
                    <th>Elementary</th>
                    <th>Junior High</th>
                    <th>Senior High</th>
                    <th style="text-align:center; background-color:#78ccf8; color:#000;">Total</th>
                </tr>
                <?php
                    $c=1; 
                    $fkc=0;
                    $fec=0;
                    $fjc=0;
                    $fsc=0;
                    $ft=0;
                    foreach($district as $row){
                    $e = $this->Common->two_cond_row('hris_jobvacancy', 'sy', date('Y'),'job_type',1);
                    $j = $this->Common->two_cond_row('hris_jobvacancy', 'sy', date('Y'),'job_type',3);
                    $s = $this->Common->two_cond_row('hris_jobvacancy', 'sy', date('Y'),'job_type',4);
                    $k = $this->Common->two_cond_row('hris_jobvacancy', 'sy', date('Y'),'job_type',5);
                    
                    
                ?>
                <tr>
                    <td><?= $c++; ?></td>
                    <td><?= strtoupper($row->discription); ?> </td>
                    <td style="text-align:center">
                        <?php 
                            if(!empty($k)){
                                $kc = $this->Common->three_cond_count_row('hris_applications', 'jobID', $k->jobID,'district',$row->discription,'app_year',date('Y'));
                               $tkc = $kc->num_rows();
                               echo $tkc;
                            }else{
                                $tkc=0;
                            }
                        ?>
                    </td>
                    <td style="text-align:center">
                        <?php 
                            if(!empty($e)){
                                $ec = $this->Common->three_cond_count_row('hris_applications', 'jobID', $e->jobID,'district',$row->discription,'app_year',date('Y'));
                                $tec = $ec->num_rows();
                               echo $tec;
                            }else{
                                $tec=0;
                            }
                        ?>
                    </td>
                    <td style="text-align:center">
                        <?php 
                            if(!empty($j)){
                                $jc = $this->Common->three_cond_count_row('hris_applications', 'jobID', $j->jobID,'district',$row->discription,'app_year',date('Y'));
                                $tjc = $jc->num_rows();
                                echo $tjc;
                            }else{
                                $tjc=0;
                            }
                        ?>
                    </td>
                    <td style="text-align:center">
                        <?php 
                            if(!empty($s)){
                                $sc = $this->Common->three_cond_count_row('hris_applications', 'jobID', $s->jobID,'district',$row->discription,'app_year',date('Y'));
                                $tsc = $sc->num_rows();
                               echo $tsc;
                            }else{
                                $tsc=0;
                            }
                        ?>
                        
                    </td>
                    <td style="text-align:center; background-color:#78ccf8; color:#000;"><?= $tkc+$tec+$tjc+$tsc; ?><?php $total=$tkc+$tec+$tjc+$tsc; ?></td>
                </tr>
                <?php $fkc += $tkc;  $fec += $tec; $fjc += $tjc; $fsc += $tsc; $ft += $total; ?>
                <?php } ?>
                <td style="text-align:right; background-color:#78ccf8; color:#000;" colspan="2">Total</td>
                <td style="text-align:center; background-color:#78ccf8; color:#000;"><?= $fkc; ?></td>
                <td style="text-align:center; background-color:#78ccf8; color:#000;"><?= $fec; ?></td>
                <td style="text-align:center; background-color:#78ccf8; color:#000;"><?= $fjc; ?></td>
                <td style="text-align:center; background-color:#78ccf8; color:#000;"><?= $fsc; ?></td>
                <td style="text-align:center; background-color:#78ccf8; color:#000;"><?= $ft; ?></td>
            </table>

         
        
        
        </div>

       


        
    </body>
</html>