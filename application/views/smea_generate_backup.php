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



    <?php if($this->session->position == 'School'){ ?>
        <div class="links">
                <a href="<?= base_url(); ?>Page/generate_aip" class="btn">AIP</a>
                <a href="<?= base_url(); ?>Page/generate_sop" class="btn btn-info">SOP</a>
                <?php if($ft->fund_type == 0){?>
                    <a href="<?= base_url(); ?>Page/generate_app" class="btn btn-secondary">APP</a>
                <?php }else{ ?>
                    <a class="btn btn-secondary" href="<?= base_url(); ?>Page/generate_appv2">APP</a>
                <?php } ?>
                <!-- <a href="<?= base_url(); ?>Page/generate_ppmp" class="btn btn-primary">PPMP</a> -->
                <a href="<?= base_url(); ?>Page/implementation_plans" class="btn btn-success">PLANS</a>
                <?php if(!empty($aip_submit)){?>
                <a href="<?= base_url(); ?>Page/aip_track/<?= $aip_submit->id; ?>" class="btn btn-info">View Status</a>
                <?php } ?>
        </div>
    <?php }elseif($this->session->position == 'Admin' || $this->session->position == 'smme'){ ?>
        <div class="links">
                <a href="<?= base_url(); ?>Page/aip_admin/<?= $data_row->school_id.'/'.$data_row->fy.'/'.$data_row->b_code.'/'.$data_row->id; ?>" class="btn">AIP</a>
                <a href="<?= base_url(); ?>Page/generate_sop_admin/<?= $data_row->school_id.'/'.$data_row->fy.'/'.$data_row->b_code.'/'.$data_row->id; ?>" class="btn btn-info">SOP</a>
                <?php $sap = $this->SGODModel->two_cond_row('sgod_app_percentage','b_code',$data_row->b_code,'fy',$data_row->fy); ?>
                <?php if(!empty($sap)){?>
                <a href="<?= base_url(); ?>Page/generate_app_admin/<?= $data_row->school_id.'/'.$data_row->fy.'/'.$data_row->b_code.'/'.$data_row->id; ?>" class="btn btn-secondary">APP</a>
                <a href="<?= base_url(); ?>Page/generate_ppmp_admin/<?= $school->schoolID.'/'.$data_row->fy.'/'.$data_row->b_code.'/'.$data_row->id; ?>" class="btn btn-primary">PPMP</a>
                <?php } ?>
                <a href="<?= base_url(); ?>Page/aip_sub_district" class="btn btn-info">By District</a>
                <a href="<?= base_url(); ?>Page/aip_track/<?= $this->uri->segment(6); ?>" class="btn btn-success">View Status</a>
        </div>
    <?php } ?>

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
    <h1>SCHOOL MONITORING, EVALUATION AND ADJUSTMENT<br />FY <?= $fy; ?></h1>


    <?php foreach($data as $row){ 
         $io = $this->SGODModel->one_cond_row('sgod_setting_io', 'id', $row->io);
        ?>
    <ul>
        <li>PILLAR : <?= ucfirst($row->pillar); ?></li>
    </ul>

   

    <table >
            <tr>
                <th rowspan="5">SCHOOL IMPROVEMENT PROJECT TITLE</th>
                <th rowspan="5">PERFORMANCE INDICATORS</th>
                <th colspan="28">PHYSICAL ACCOMPLISHMENTS</th>
                <th colspan="20">FINANCIAL ACCOMPLISHMENTS (MOOE)</th>
                <th colspan="20">FINANCIAL ACCOMPLISHMENTS (OTHER SOURCES OF FUND)</th>
            </tr>
            <tr>
                <th colspan="28">QUARTER</th>
                <th colspan="20">QUARTER</th>
                <th colspan="20">QUARTER</th>
            </tr>
            <tr>
                <th colspan="7">1ST</th>
                <th colspan="7" class="ivan">2ND</th>
                <th colspan="7">3RD</th>
                <th colspan="7" class="ivan">4TH</th>

                <th colspan="5">1ST</th>
                <th colspan="5" class="ivan">2ND</th>
                <th colspan="5">3RD</th>
                <th colspan="5" class="ivan">4TH</th>

                <th colspan="5">1ST</th>
                <th colspan="5" class="ivan">2ND</th>
                <th colspan="5">3RD</th>
                <th colspan="5" class="ivan">4TH</th>

            </tr>
            <tr>
                <th rowspan="2">Number of<br /> Physical <br />target</th>
                <th rowspan="2">Achieved <br />(base on <br />planned<br /> targets)</th>
                <th rowspan="2">%age of <br />Accomplish<br/>ment</th>
                <th rowspan="2">Gain <br />(base on <br />planned<br /> targets)</th>
                <th rowspan="2">%age of <br />Gain</th>
                <th rowspan="2">Gap/Balance <br />(base on <br />planned<br /> targets)</th>
                <th rowspan="2">%age of <br />Gap</th>

                <th rowspan="2" class="ivan">Number of<br /> Physical <br />target</th>
                <th rowspan="2" class="ivan">Achieved <br />(base on <br />planned<br /> targets)</th>
                <th rowspan="2" class="ivan">%age of <br />Accomplish<br/>ment</th>
                <th rowspan="2" class="ivan">Gain <br />(base on <br />planned<br /> targets)</th>
                <th rowspan="2" class="ivan">%age of <br />Gain</th>
                <th rowspan="2" class="ivan">Gap/Balance <br />(base on <br />planned<br /> targets)</th>
                <th rowspan="2" class="ivan">%age of <br />Gap</th>

                <th rowspan="2">Number of<br /> Physical <br />target</th>
                <th rowspan="2">Achieved <br />(base on <br />planned<br /> targets)</th>
                <th rowspan="2" >%age of <br />Accomplish<br/>ment</th>
                <th rowspan="2">Gain <br />(base on <br />planned<br /> targets)</th>
                <th rowspan="2">%age of <br />Gain</th>
                <th rowspan="2">Gap/Balance <br />(base on <br />planned<br /> targets)</th>
                <th rowspan="2">%age of <br />Gap</th>

                <th rowspan="2" class="ivan">Number of<br /> Physical <br />target</th>
                <th rowspan="2" class="ivan">Achieved <br />(base on <br />planned<br /> targets)</th>
                <th rowspan="2" class="ivan">%age of <br />Accomplish<br/>ment</th>
                <th rowspan="2" class="ivan">Gain <br />(base on <br />planned<br /> targets)</th>
                <th rowspan="2" class="ivan">%age of <br />Gain</th>
                <th rowspan="2" class="ivan">Gap/Balance <br />(base on <br />planned<br /> targets)</th>
                <th rowspan="2" class="ivan">%age of <br />Gap</th>

                <!-- end of physical accomplishment -->

                <th rowspan="2">Funds Allocated<br />for Qaurter</th>
                <th rowspan="2">Funds Utilized</th>
                <th rowspan="2">% of <br />Utilization(utilization/<br />allocation x100)</th>
                <th colspan="2">Gap on Allocation versus Utilized(Difference between allocated amount and utilized amount)</th>

                <th rowspan="2" class="ivan">Funds Allocated<br />for Qaurter</th>
                <th rowspan="2" class="ivan">Funds Utilized</th>
                <th rowspan="2" class="ivan">% of <br />Utilization(utilization/<br />allocation x100)</th>
                <th colspan="2" class="ivan">Gap on Allocation versus Utilized(Difference between allocated amount and utilized amount)</th>

                <th rowspan="2">Funds Allocated<br />for Qaurter</th>
                <th rowspan="2">Funds Utilized</th>
                <th rowspan="2">% of <br />Utilization(utilization/<br />allocation x100)</th>
                <th colspan="2">Gap on Allocation versus Utilized(Difference between allocated amount and utilized amount)</th>

                <th rowspan="2" class="ivan">Funds Allocated<br />for Qaurter</th>
                <th rowspan="2" class="ivan">Funds Utilized</th>
                <th rowspan="2" class="ivan">% of <br />Utilization(utilization/<br />allocation x100)</th>
                <th colspan="2" class="ivan">Gap on Allocation versus Utilized(Difference between allocated amount and utilized amount)</th>

                <!-- end of financial accomplishment -->

                <th rowspan="2">Funds Allocated<br />for Qaurter</th>
                <th rowspan="2">Funds Utilized</th>
                <th rowspan="2">% of <br />Utilization(utilization/<br />allocation x100)</th>
                <th colspan="2">Gap on Allocation versus Utilized(Difference between allocated amount and utilized amount)</th>

                <th rowspan="2" class="ivan">Funds Allocated<br />for Qaurter</th>
                <th rowspan="2" class="ivan">Funds Utilized</th>
                <th rowspan="2" class="ivan">% of <br />Utilization(utilization/<br />allocation x100)</th>
                <th colspan="2" class="ivan">Gap on Allocation versus Utilized(Difference between allocated amount and utilized amount)</th>

                <th rowspan="2">Funds Allocated<br />for Qaurter</th>
                <th rowspan="2">Funds Utilized</th>
                <th rowspan="2">% of <br />Utilization(utilization/<br />allocation x100)</th>
                <th colspan="2">Gap on Allocation versus Utilized(Difference between allocated amount and utilized amount)</th>

                <th rowspan="2" class="ivan">Funds Allocated<br />for Qaurter</th>
                <th rowspan="2" class="ivan">Funds Utilized</th>
                <th rowspan="2" class="ivan">% of <br />Utilization(utilization/<br />allocation x100)</th>
                <th colspan="2" class="ivan">Gap on Allocation versus Utilized(Difference between allocated amount and utilized amount)</th>

            </tr>
            <tr>
                <th>Amount</th>
                <th>%</th>
                <th class="ivan">Amount</th>
                <th class="ivan">%</th>
                <th>Amount</th>
                <th>%</th>
                <th class="ivan">Amount</th>
                <th class="ivan">%</th>

                <th>Amount</th>
                <th>%</th>
                <th class="ivan">Amount</th>
                <th class="ivan">%</th>
                <th>Amount</th>
                <th>%</th>
                <th class="ivan">Amount</th>
                <th class="ivan">%</th>
            </tr>   
        <tbody>
            <?php 
            $pia = $this->SGODModel->get_all_aip($row->school_id, $row->fy, $row->pia, $row->b_code);
            $sp = null; 
            foreach($pia as $row){ ?>
            <tr>
            <?php if($sp !== $row->sip_project) { ?>

                    <td style="text-align:left"><?= $row->sip_project; ?></td>
                    <td style="text-align:left"><?= $row->sip_pObjective; ?></td>
                    <?php }else{ ?>
                        <td></td>
                        <td></td>
                        <td><?= $row->pi; ?></td>
                        
                    <?php } ?>

                <?php 
                  $pt = $this->SGODModel->two_cond_row('sgod_sop', 'aip_id',$row->id,'type',1);
                  if(!empty($pt)){
                ?>
                <td><a href="<?= base_url(); ?>Page/smea_edit/<?= $pt->id; ?>/1"><?= $pt->q1; ?></a></td>
                <td><?php if($pt->smea_q1 != 0){echo $pt->smea_q1;} ?></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td><a href="<?= base_url(); ?>Page/smea_edit/<?= $pt->id; ?>/2"><?= $pt->q2; ?></a></td>
                <td><?php if($pt->smea_q2 != 0){echo $pt->smea_q2;} ?></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td><a href="<?= base_url(); ?>Page/smea_edit/<?= $pt->id; ?>/3"><?= $pt->q3; ?></a></td>
                <td><?php if($pt->smea_q3 != 0){echo $pt->smea_q3;} ?></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td><a href="<?= base_url(); ?>Page/smea_edit/<?= $pt->id; ?>/4"><?= $pt->q4; ?></a></td>
                <td><?php if($pt->smea_q4 != 0){echo $pt->smea_q1;} ?></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <?php }else{?>
                    <td></td><td></td><td></td><td></td><td></td><td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                <?php } ?>

                <?php 
                  $ft = $this->SGODModel->two_cond_row('sgod_sop', 'aip_id',$row->id,'type',2);
                  if(!empty($ft)){
                ?>
                <td><a href="<?= base_url(); ?>Page/smea_edit/<?= $ft->id; ?>/1"><?php if($ft->q1){echo number_format($ft->q1);}  ?></a></td>
                <td><?php if($ft->smea_q1 != 0){echo number_format($ft->smea_q1);}  ?></td>
                <td></td>
                <td></td>
                <td></td>
                <td><a href="<?= base_url(); ?>Page/smea_edit/<?= $ft->id; ?>/2"><?php if($ft->q2){echo number_format($ft->q2);}  ?></a></td>
                <td><?php if($ft->smea_q2 != 0){echo number_format($ft->smea_q2);}  ?></td>
                <td></td>
                <td></td>
                <td></td>
                <td><a href="<?= base_url(); ?>Page/smea_edit/<?= $ft->id; ?>/3"><?php if($ft->q3){echo number_format($ft->q3);}  ?></a></td>
                <td><?php if($ft->smea_q3 != 0){echo number_format($ft->smea_q3);}  ?></td>
                <td></td>
                <td></td>
                <td></td>
                <td><a href="<?= base_url(); ?>Page/smea_edit/<?= $ft->id; ?>/4"><?php if($ft->q4){echo number_format($ft->q4);}  ?></a></td>
                <td><?php if($ft->smea_q4 != 0){echo number_format($ft->smea_q4);}  ?></td>
                <td></td>
                <td></td>
                <td></td>
                <?php }else{?>
                    <td></td><td></td><td></td><td></td><td></td><td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                <?php } ?>


                <?php 
                  $fto = $this->SGODModel->two_cond_row('sgod_sop', 'aip_id',$row->id,'type',3);
                  if(!empty($fto)){
                ?>
                <td><?php if($fto->smea_q1){echo number_format($fto->smea_q1);} ?></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td><?php if($fto->smea_q2){echo number_format($fto->smea_q2);} ?></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td><?php if($fto->smea_q3){echo number_format($fto->smea_q3);} ?></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td><?php if($fto->smea_q4){echo number_format($fto->smea_q4);} ?></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <?php }else{?>
                    <td></td><td></td><td></td><td></td><td></td><td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                <?php } ?>

                

            </tr>
            <?php } ?>
           
        </tbody> 
    </table>
    <?php } ?>


    <div class="fcon">
                <img style="width:90px; float:left;" src="https://qrcode.tec-it.com/API/QRCode?data=<?= base_url(); ?>Page/aip_admin/<?= $data_row->school_id.'/'.$data_row->fy.'/'.$data_row->b_code.'/'.$data_row->id; ?>" title="" />
                <div class="lcon">
                    
                    System Generated Report<br />
                    School Operational Plan<br />
                    Date Generated: <?php  date_default_timezone_set('Asia/Manila'); echo date('F d, Y', time()); ?><br />
                    
                </div>
                
                <div class="blocker"></div>
    </div>







    </body>
                </html>