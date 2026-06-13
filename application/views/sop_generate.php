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

    <img class="logo" src="<?= base_url(); ?>assets/images/report/ke.png" alt="">
    <p>
    <span class="rp">Republic of the Philippines</span><br />
        <span class="de">Department of Education</span><br />
        <span class="r">Region XI</span><br />
        <span class="r">School Division of <?= $school->province; ?></span><br />
        <span class="sadress"><?= strtoupper($school->district); ?><br />
         <?= strtoupper($school->schoolName); ?><br />
         <?= strtoupper($school->sitio); ?> <?= strtoupper($school->brgy); ?>, <?= strtoupper($school->city); ?>, <?= strtoupper($school->province); ?></span> 
    </p>
    <div class="hr"></div>
    <h1>SCHOOL OPERATIONAL PLAN<br />FY <?= $fy; ?></h1>


    <?php foreach($data as $row){ 
         $io = $this->SGODModel->one_cond_row('sgod_setting_io', 'id', $row->io);
        ?>
    <ul>
        <li>PILLAR : <?= ucfirst($row->pillar); ?></li>
        <li>INTERMEDIATE OUTCOME : <?= ucfirst($io->description); ?></li>
        <li>DOMAIN : <?= ucfirst($row->domain); ?></li>	
        <li>STRAND : <?= ucfirst($row->strand); ?></li>
        <li>PIAs:  <?= ucfirst($row->pia); ?></li>
    </ul>

   

    <table>
            <tr>
                <th rowspan="3">Current Administration<br /> Agenda Outcome<br /> Indicators</th>
                <th rowspan="3">SCHOOL IMPROVEMENT PROJECT TITLE</th>
                <th rowspan="3">PROJECT OBJECTIVE</th>
                <th rowspan="3">OUTPUT OF THE YEAR</th>
                <th rowspan="3">STRATEGY ACTIVITY</th>
                <th rowspan="3">PERFORMANCE INDICATORS</th>
                <th rowspan="3">MOV’S</th>
                <th rowspan="3">PERSON/SRESPONSIBLE</th>
                <th rowspan="3">SCHEDULE</th>
                <th rowspan="3">VENUE</th>
                <th rowspan="3">BUDGET PER ACTIVITY</th>
                <th rowspan="3">BUDGET SOURCE</th>
                <th rowspan="3">MATERIALS</th>
                <th colspan="5">PHYSICAL TARGET</th>
                <th colspan="5">FINANCIAL TARGET (MOOE)</th>
                <th colspan="5">FINANCIAL TARGET (OTHER SOURCES OF FUND)</th>
            </tr>
            <tr>
                <th colspan="4">QUARTER</th>
                <th rowspan="2">TOTAL</th>
                <th colspan="4">QUARTER</th>
                <th rowspan="2">TOTAL</th>
                <th colspan="4">QUARTER</th>
                <th rowspan="2">TOTAL</th>
            </tr>
            <tr>
                <th>1ST</th>
                <th>2ND</th>
                <th>3RD</th>
                <th>4TH</th>
                <th>1ST</th>
                <th>2ND</th>
                <th>3RD</th>
                <th>4TH</th>
                <th>1ST</th>
                <th>2ND</th>
                <th>3RD</th>
                <th>4TH</th>
            </tr>   
        <tbody>
            <?php 
            $pia = $this->SGODModel->get_all_aip($row->school_id, $row->fy, $row->pia, $row->b_code);
            $sp = null; 
            foreach($pia as $row){ ?>
            <tr>
            <?php if($sp !== $row->sip_project) { ?>

                <?php $m = $this->SGODModel->one_cond_row('agenda','id',$row->matatag); ?>

                    <td><?= $m->indicator; ?></td>   
                    <td><?= $row->sip_project; ?></td>
                    <td><?= $row->sip_pObjective; ?></td>
                    <td><?= $row->sip_output; ?></td>
                    <td><?= $row->strategy; ?></td>
                    <td><?= $row->pi; ?></td>
                    <td><?= $row->movs; ?></td>
                    <td><?= $row->pr; ?></td>
                    <?php }else{ ?>
                        <td><?= $m->indicator; ?></td>  
                        <td></td>
                        <td></td>
                        <td><?= $row->sip_output; ?></td>
                        <td><?= $row->strategy; ?></td>
                        <td><?= $row->pi; ?></td>
                        <td><?= $row->movs; ?></td>
                        <td><?= $row->pr; ?></td>
                        
                    <?php } ?>
                <?php $sp = $row->sip_project; ?>
                <td><?= $row->schedule; ?></td>
                <td><?= $row->venue; ?></td>
                <td><?= number_format($row->budget); ?></td>
                <td><?= $row->budget_source;  ?></td>
                <td><?= $row->materials; ?></td>
                <?php 
                  $pt = $this->SGODModel->two_cond_row('sgod_sop', 'aip_id',$row->id,'type',1);
                  if(!empty($pt)){
                ?>
                <td><?= $pt->q1; ?></td>
                <td><?= $pt->q2; ?></td>
                <td><?= $pt->q3; ?></td>
                <td><?= $pt->q4; ?></td>
                <td><?= $pt->total; ?></td>
                <?php }else{?>
                    <td></td><td></td><td></td><td></td><td></td>
                <?php } ?>

                <?php 
                  $ft = $this->SGODModel->two_cond_row('sgod_sop', 'aip_id',$row->id,'type',2);
                  if(!empty($ft)){
                ?>
                <td><?php if($ft->q1){echo $ft->q1;}  ?></td>
                <td><?php if($ft->q2){echo $ft->q2;}  ?></td>
                <td><?php if($ft->q3){echo $ft->q3;}  ?></td>
                <td><?php if($ft->q4){echo $ft->q4;}  ?></td>
                <td><?php if($ft->total){echo $ft->total;} ?></td>
                <?php }else{?>
                    <td></td><td></td><td></td><td></td><td></td>
                <?php } ?>


                <?php 
                  $fto = $this->SGODModel->two_cond_row('sgod_sop', 'aip_id',$row->id,'type',3);
                  if(!empty($fto)){
                ?>
                <td><?php if(isset($fto->q1)){echo $fto->q1;} ?></td>
                <td><?php if(isset($fto->q2)){echo $fto->q2;} ?></td>
                <td><?php if(isset($fto->q3)){echo $fto->q3;} ?></td>
                <td><?php if(isset($fto->q4)){echo $fto->q4;} ?></td>
                <td><?php if($fto->total != ''){echo number_format($fto->total);} ?></td>
                <?php }else{?>
                    <td></td><td></td><td></td><td></td><td></td>
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