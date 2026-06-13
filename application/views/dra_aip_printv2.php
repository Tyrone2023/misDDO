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
                <?= strtoupper(urldecode($this->uri->segment(4)))?>
            </p>
            <div class="hr"></div>


            <table class="dra_aip">
                <tr>
                    <th>No.</th>
                    <th style="text-align:left">School Name </th>
                    <th>School ID</th>
                    <th>Batch Code</th>
                    
                    <?php if($this->uri->segment(3) == 1){ ?> 
                        <th>Date Approved </th>
                    <?php }else{ ?>
                        <th>Date Submitted </th>
                    <?php } ?>
                    
                </tr>
                <?php 
                    $c=1;
                    $s_id=null;
                    foreach($aip as $row){ 
                    $st = $this->SGODModel->one_cond_last_record('sgod_aip_track', 'submit_id', $row->id,'remarks','Approved');
                    $school = $this->SGODModel->one_cond_row('schools', 'schoolID', $row->school_id);
                    $bs = $this->SGODModel->one_cond_row('sgod_school_allocation', 'alloc_batch', $row->b_code);
                    $d = urldecode($this->uri->segment(4));
                    if($school->district == $d){
                    
                ?>
                <tr>
                <?php if($s_id !== $row->school_id){ ?>
                        <td><?= $c++; ?>.</td>
                        <td style="text-align:left"><?= strtoupper($school->schoolName); ?></td>
                        <td><?= $row->school_id; ?></td>
                    <?php }else{ ?>
                        <td></td>
                        <td></td>
                        <td></td>
                        
                    <?php } ?>
                    <?php $s_id = $row->school_id; ?>

                    
                    <td><?= $row->b_code; ?> - <?= $bs->alloc_group; ?></td>

                    <?php if($this->uri->segment(3) == 1){ ?> 
                        <td> <?= $st->tdate; ?></td>
                    <?php }else{ ?>
                        <td><?= $row->date; ?></td>
                    <?php } ?>
                    
                </tr>
                <?php } } ?>
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