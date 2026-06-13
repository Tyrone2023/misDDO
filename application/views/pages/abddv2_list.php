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

                                                    
    ?>



    <body class="aip_generate" id="printTable">

        <div class="cert">
            <img class="logo" src="<?= base_url(); ?>assets/images/report/ke.png" alt="">
            <p style="margin-bottom:0;">
            <span class="rp">Republic of the Philippines</span><br />
                <span class="de">Department of Education</span><br />
                <span class="r">Region XI</span><br />
                <span class="r">Schools Division of Davao Oriental</span>
            </p>

            <div class="hr" style="margin:10px 0"></div>
            <h4 style="margin:40px 0"><?= $title; ?></h4>

            <table class="rrr" id='myTable'>
            <?php 

            $job = $this->Common->two_cond('hris_jobvacancy','position','1','jvStatus','Open');
            foreach($job as $row){
            ?>
                <tr>
                    <td colspan="11" style="background-color:#78ccf8; color:#000; text-align:center"><?= $row->jobTitle; ?> <?= $jobTypes[$row->job_type] ?? '';?></td>
                </tr>
                <tr>
                    <td colspan='2' style="text-align:center; background-color:#fafcb8;"><b>FULLNAME</b></td>
                    <td style="text-align:center; background-color:#fafcb8;"><b>BACHELORS DEGREE</b></td>
                    <td style="text-align:center; background-color:#fafcb8;"><b>SPECIALIZATION</b></td>
                    <td style="text-align:center; background-color:#fafcb8;">Letter of Intent</td>
                    <td style="text-align:center; background-color:#fafcb8;">PDS</td>
                    <td style="text-align:center; background-color:#fafcb8;">PRC</td>
                    <td style="text-align:center; background-color:#fafcb8;">Transcript of Records of the Baccalaureate Degree</td>
                    <td style="text-align:center; background-color:#fafcb8;">Omnibus Sworn Statement</td>
                    <td style="text-align:center; background-color:#fafcb8;">Address</td>
                    <td style="text-align:center; background-color:#fafcb8;">Responsible</td>
                </tr>
                <?php 
                $ca=1;
                $d = $this->input->get('d');
                $app = $this->Common->three_cond_select('hris_applications','district,dq,appID,empEmail,pre_school,jobID','district',$d,'dq',1,'jobID',$row->jobID);
                foreach($app as $aprow){
                    $b = $this->Common->one_cond_row('hris_applicant','empEmail',$aprow->empEmail);
                    if(!empty($b)){
                        $a = $this->Common->one_cond_row('hris_applicant','empEmail',$aprow->empEmail);
                        $id = $a->id;
                        $record = $a->record_no;
                    }else{
                        $a = $this->Common->one_cond_row('hris_staff','IDNumber',$aprow->empEmail);
                        $id = $a->IDNumber;
                        $record = $a->IDNumber;
                    }
                    $dq = $this->Common->one_cond_row('hris_app_dq', 'appID', $aprow->appID);
                    if(!empty($dq)){
                    $user = $this->Common->one_cond_row('users', 'id', $dq->res);
                ?>
                <tr>
                <td><?= $ca++; ?></td>
                    <td><a style="text-decoration:none; color:#222;" href="<?= base_url(); ?>Pages/ma/<?= $id; ?>/<?= $aprow->jobID; ?>/<?= $aprow->pre_school; ?>" target="_blank"><?= strtoupper($a->FirstName.' '.$a->MiddleName.' '.$a->LastName); ?></a></td>
                    <td><?= strtoupper($a->bd); ?></td>
                    <td><?= strtoupper($a->jhss); ?></td>
                    <td style="text-align:center; <?php if($dq->li == 0){echo "color:red; font-weight:bold";} ?>"><?php if($dq->li == 1){echo "&#x1F5F8;";}else{echo "&#215;";}?></td>
                    <td style="text-align:center; <?php if($dq->da_pds == 0){echo "color:red; font-weight:bold";} ?>"><?php if($dq->da_pds == 1){echo "&#x1F5F8;";}else{echo "&#215;";}?></td>
                    <td style="text-align:center; <?php if($dq->prc == 0){echo "color:red; font-weight:bold";} ?>"><?php if($dq->prc == 1){echo "&#x1F5F8;";}else{echo "&#215;";}?></td>
                    <td style="text-align:center; <?php if($dq->trbd == 0){echo "color:red; font-weight:bold";} ?>"><?php if($dq->trbd == 1){echo "&#x1F5F8;";}else{echo "&#215;";}?></td>
                    <td style="text-align:center; <?php if($dq->omni == 0){echo "color:red; font-weight:bold";} ?>"><?php if($dq->omni == 1){echo "&#x1F5F8;";}else{echo "&#215;";}?></td>
                    <td style="text-align:center; <?php if($dq->local == 1){echo "color:red; font-weight:bold";} ?>"><?php if($dq->local == 0){echo "&#x1F5F8;";}else{echo "&#215;";}?></td>
                    <td style="text-align:center;"><?= $user->fname; ?></td>
                </tr>
            <?php } } } ?>
            </table>

         
        
        
        </div>

       


        
    </body>
</html>