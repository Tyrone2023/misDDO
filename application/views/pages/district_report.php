<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta content="Responsive bootstrap 4 admin template" name="description" />
        <meta content="Coderthemes" name="author" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <!-- App favicon -->
        <link rel="shortcut icon" href="<?= base_url(); ?>assets/images/favicon.ico">

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



    <body class="aip_generate" id="printTable">

        <div class="cert">
            <img class="logo" src="<?= base_url(); ?>assets/images/report/ke.png" alt="">
            <p>
            <span class="rp">Republic of the Philippines</span><br />
                <span class="de">Department of Education</span><br />
                <span class="r">Region XI</span><br />
                <span class="r">Schools Division of Davao Oriental</span><br /> 
                <b><span class="r"><?= $district->discription; ?></span></b>
            </p>

            <div class="hr"></div>


            <p>We certify that the following applicants have uploaded their documents in the MIS Online Hiring System and we have validated it against the original documents.</p>


            <table class="dra_aip">
                <tr>
                    <th>No.</th>
                    <th>Last Name</th>
                    <th>First Name</th>
                    <th>Middle Name </th>
                    <th>PrePrepared School</th>
                    <th>Job Title</th>
                </tr>
                <?php 
                    $c = 1;
                    $s_id=null;
                    foreach($data as $row){
                        $app = $this->Common->one_cond_row('hris_applicant','id',$row->applicant_id);
                        $job = $this->Common->one_cond_row('hris_jobvacancy','jobID',$row->jobID);
                        $school = $this->Common->one_cond_row('schools','schoolID',$row->pre_school);
                        
                ?>
                <tr>
                    <?php if($s_id !== $row->applicant_id){ ?>
                        <td><?= $c++; ?>.</td>
                        <td><?= $app->LastName; ?></td>
                        <td><?= $app->FirstName; ?></td>
                        <td><?= $app->MiddleName; ?></td>
                    <?php }else{ ?>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        
                    <?php } ?>
                    <?php $s_id = $row->applicant_id; ?>

                    <td><?= $school->schoolName; ?></td>
                    <td><?= $job->jobTitle; ?></td>
                </tr>
                <?php } ?>
                
            </table>
           
            <div class="footer">

                <div class="hr"></div>


                <div class="cafooter">
                    <img width="100%" src="<?= base_url(); ?>assets/images/f.png" alt="">
                    <div class="blocker"></div>
                </div>

            </div>
        
        
        
        
        </div>




    </body>
</html>