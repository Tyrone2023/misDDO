<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="utf-8" />
        <?php include('includes/page-title.php'); ?>
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
                <span class="r">Schools Division of Davao Oriental</span>
            </p>
            <div class="hr"></div>


            <table class="dra_aip">
                <tr>
                    <th>Batch Code</th>
                    <th>School ID</th>
                    <th>School Name </th>
                    <th>District</th>
                    <th>Date Approved </th>
                </tr>
                <?php 
                    foreach($aip as $row){ 
                    $as = $this->SGODModel->one_cond_row('sgod_aip_submit', 'id', $row->submit_id);
                    $school = $this->SGODModel->one_cond_row('schools', 'schoolID', $as->school_id);
                ?>
                <tr>
                    <td><?= $as->b_code; ?></td>
                    <td><?= $as->school_id; ?></td>
                    <td><a target="_blank" style="text-decoration:none; color:#5d5c5c;" href="<?= base_url(); ?>page/generate_ca/<?= $as->school_id; ?>/<?= $as->b_code; ?>/<?= $as->id; ?>"><?= strtoupper($school->schoolName); ?></a></td>
                    <td><?= strtoupper($school->district); ?></td>
                    <td><?= $row->tdate; ?></td>
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