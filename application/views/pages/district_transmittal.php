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
        <!-- <link href="https://db.onlinewebfonts.com/a/0nH393RJctHgt1f2YvZvyruY" rel="stylesheet" type="text/css"/> -->
        <style>
            @page {
            size: A4;
            margin: 50px 0 50px 0;
            }
            @media print {
            html, body {
                width: 210mm;
                height: 297mm;
                font-size:14px !important;
                font-family:'Bookman Old Style';
            }
           

            .aip_generate .hr{
                margin:10px 0;
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

            
           
            }
        </style>
    
    </head>



    <body class="aip_generate" id="printTable">

        <div class="cert">
            <img class="logo" src="<?= base_url(); ?>assets/images/report/ke.png" alt="">
            <p style="line-height:1.5em; margin-bottom:10px">
            <span class="rp">Republic of the Philippines</span><br />
                <span class="de">Department of Education</span><br />
                <span class="r">Region XI</span><br />
                <span class="r">Schools Division of Davao Oriental</span><br /> 
            </p>

            <div class="hr"></div>

            <p style="margin-bottom:20px"><u>&nbsp; &nbsp; <b><?= strtoupper($district->discription); ?></b> &nbsp; &nbsp;</u> <br />District Name</b>

            <p style="text-align:right; margin-bottom:20px"><u> &nbsp; &nbsp;<?= date('F d, Y'); ?> &nbsp; &nbsp;</u></p>
            <p style="text-align:left; line-height:1em"><b>DR. JOSEPHINE L. FADUL</b><span style="display:block;">Schools Division Superintendent</span></p>
            <p style="text-align:left; margin-bottom:20px">Ma’am:</p>
            <p style="text-align:left; text-indent: 50px; margin-bottom:20px">This is to submit the following certifications submitted by the School Screening Committee to wit:</p>



            <ol style="text-align:left; margin-left:65px; margin-bottom:50px">
               <?php
                 foreach($data as $row){
                    $app = $this->Common->one_cond_row('hris_applicant','id',$row->applicant_id);
                    $job = $this->Common->one_cond_row('hris_jobvacancy','jobID',$row->jobID);
                    $school = $this->Common->one_cond_row('schools','schoolID',$row->pre_school);
               ?>
                <li><td><?= strtoupper($school->schoolName); ?></td></li>
                <?php } ?>
            </ol>

            

            <p style="text-align:left; margin-bottom:20px">Please acknowledge receipt thereof.</p>

            <p style="text-align:right; padding-right:250px">Very truly yours,</p>
            <p style="border-top:1px solid #222; display:inline-block; padding:0 150px; float:right;">Name</p>
            <div class="blocker"></div>
            <p style="border-top:1px solid #222; display:inline-block; padding:0 100px; float:right;">Position/Designation</p>
           
            <div class="blocker"></div>
        
        
        
        
        
        </div>




    </body>
</html>