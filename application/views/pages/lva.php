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
                <span class="r">Schools Division of Davao Oriental</span>
            </p>

            <div class="hr" style="margin:10px 0"></div>
            <h4 style="margin:40px 0">List of Validated Applicant</h4>

            <table class="rrr" id='myTable'>
                <?php 
                    $c=1;
                    foreach($data as $row){
                     $school = $this->Common->one_cond_row('schools', 'schoolID', $row->pre_school);
                     $app = $this->Common->one_cond_count_row('hris_applications', 'pre_school', $row->pre_school);
                     $validated = $this->Common->two_cond_count_row('hris_applications', 'pre_school', $row->pre_school,'appStatus','Endorsed for Rating');
                     $sv = $this->Common->two_cond('hris_applications', 'pre_school', $row->pre_school,'appStatus','Endorsed for Rating');
                     
                   
                ?>
                <tr>

                    <td colspan="4" style="text-align:center"><b><?= strtoupper($row->schoolName); ?></b></td>
                    
                </tr>
                <?php 
                    $ca=1;
                    foreach($sv as $row){
                    $applicant = $this->Common->one_cond_row('hris_applicant', 'empEmail', $row->empEmail);
                ?>
                <tr>
                    <td><?= $ca++; ?></td>
                    <td><?= strtoupper($applicant->FirstName.' '.$applicant->MiddleName.' '.$applicant->LastName); ?></td>
                    <td><?= strtoupper($applicant->bd); ?></td>
                    <td><?= strtoupper($applicant->jhss); ?></td>
                </tr>
                <?php } } ?>
            </table>

         
        
        
        </div>

       


        
    </body>
</html>