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
        <link rel="shortcut icon" href="<?= base_url(); ?>assets/images/hris.ico">

        <!-- Plugins css-->
         <link href="<?= base_url(); ?>assets/css/renren.css" rel="stylesheet" type="text/css" />
         <style>
            .ivan{
                background-color:#ffe598 !important;
                color:#000 !important;
            }
            
         </style>
         

    </head>


    <body class="aip_generate sop_gen aip" id="printTable">




    <!-- <img class="logo" src="<?= base_url(); ?>assets/images/report/ke.png" alt="">
    <p>
    <span class="rp">Republic of the Philippines</span><br />
        <span class="de">Department of Education</span><br />
        <span class="r">Region XI</span><br />
        <span class="r">School Division of Davao Oriental</span><br />
        <span class="sadress"><?= strtoupper($school->district); ?><br />
         <?= strtoupper($school->schoolName); ?><br />
         <?= strtoupper($school->sitio); ?> <?= strtoupper($school->brgy); ?>, <?= strtoupper($school->city); ?>, <?= strtoupper($school->province); ?></span> 
    </p>
    <div class="hr"></div> -->
    <?php $q=$this->input->post('q')?>
    <h1>
        SCHOOL MONITORING, EVALUATION AND ADJUSTMENT<br />FY <?= $fy; ?><br />
        QUARTER <?= $q; ?>
        
    </h1>





    <table >
            <tr>
                <th rowspan="3">NO</th>
                <th rowspan="3">SCHOOL ID</th>
                <th rowspan="3">SCHOOL NAME</th>
                <th colspan="7">PHYSICAL ACCOMPLISHMENTS</th>
                <th colspan="5">FINANCIAL ACCOMPLISHMENTS (MOOE)</th>
                <th colspan="5">FINANCIAL ACCOMPLISHMENTS (OTHER SOURCES OF FUND)</th>
            </tr>
           
            <tr>
                <th rowspan="2">Number of<br /> Physical <br />target</th>
                <th rowspan="2">Achieved <br />(base on <br />planned<br /> targets)</th>
                <th rowspan="2">%age of <br />Accomplish<br/>ment</th>
                <th rowspan="2">Gain <br />(base on <br />planned<br /> targets)</th>
                <th rowspan="2">%age of <br />Gain</th>
                <th rowspan="2">Gap/Balance <br />(base on <br />planned<br /> targets)</th>
                <th rowspan="2">%age of <br />Gap</th>

                

                <!-- end of physical accomplishment -->

                <th rowspan="2">Funds Allocated<br />for Qaurter</th>
                <th rowspan="2">Funds Utilized</th>
                <th rowspan="2">% of <br />Utilization(utilization/<br />allocation x100)</th>
                <th colspan="2">Gap on Allocation versus Utilized(Difference between allocated amount and utilized amount)</th>

                

                <!-- end of financial accomplishment -->

                <th rowspan="2">Funds Allocated<br />for Qaurter</th>
                <th rowspan="2">Funds Utilized</th>
                <th rowspan="2">% of <br />Utilization(utilization/<br />allocation x100)</th>
                <th colspan="2">Gap on Allocation versus Utilized(Difference between allocated amount and utilized amount)</th>

                

            </tr>
            <tr>
                <th>Amount</th>
                <th>%</th>

                <th>Amount</th>
                <th>%</th>
            </tr>   
        <tbody>
            <?php $ic=1; foreach($smea as $row){
                        $school = $this->Common->one_cond_row('schools', 'schoolID', $row->school_id);
                        $smea_school = $this->Common->two_cond('sgod_aip', 'b_code', $row->b_code, 'school_id',$row->school_id); 
                        $sop_total=0;
                        $smea_total=0;

                        $ft_sop_total=0;
                        $ft_smea_total=0;

                        $oft_sop_total=0;
                        $oft_smea_total=0;


                        foreach($smea_school as $srow){
                            $pt = $this->Common->two_cond_row('sgod_sop', 'aip_id', $srow->id,'type',1); 
                            $ft = $this->Common->two_cond_row('sgod_sop', 'aip_id', $srow->id,'type',2); 
                            $oft = $this->Common->two_cond_row('sgod_sop', 'aip_id', $srow->id,'type',3); 

                            $sop_q = 'q'.$q;
                            $smea_q = 'smea_q'.$q;

                            if(!empty($pt)){
                            $sop_total += (int)$pt->$sop_q; 
                            $smea_total += (int)$pt->$smea_q;
                            }

                            if(!empty($ft)){
                            $ft_sop_total += (int)$ft->$sop_q; 
                            $ft_smea_total += (int)$ft->$smea_q; 
                            }

                            if(!empty($oft)){
                            $oft_sop_total += (int)$oft->$sop_q; 
                            $oft_smea_total += (int)$oft->$smea_q; 
                            }

                        }
                    ?>
            <tr>
                <td><?= $ic++; ?></td>
                <td><?= $row->school_id; ?></td>
                <td style="white-space: nowrap;"><?= $school->schoolName; ?></td>
                <td><?= $sop_total; ?> </td>
                <td><?= $smea_total; ?></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td><?= $ft_sop_total; ?></td>
                <td><?= $ft_smea_total; ?></td>
                <td></td>
                <td></td>
                <td></td>
                <td><?php if(!empty($oft)){$ft_sop_total;} ?></td>
                <td><?php if(!empty($oft)){$ft_smea_total;} ?></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <?php } ?>
           
        </tbody> 
    </table>


    <div class="fcon">
                <img style="width:90px; float:left;" src="https://qrcode.tec-it.com/API/QRCode?data=<?= base_url(); ?>Page/aip_admin/" title="" />
                <div class="lcon">
                    
                    System Generated Report<br />
                    School Operational Plan<br />
                    Date Generated: <?php  date_default_timezone_set('Asia/Manila'); echo date('F d, Y', time()); ?><br />
                    
                </div>
                
                <div class="blocker"></div>
    </div>







    </body>
                </html>