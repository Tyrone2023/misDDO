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



    <body class="aip_generate aip_print aip" id="printTable">

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
    <h1>ANNUAL IMPLEMENTATION PLAN<br />FY <?= $fy; ?></h1>
                                        
                                        
                                        

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
                <th>Current Administration<br /> Agenda Outcome<br /> Indicators</th>
                <th>SCHOOL IMPROVEMENT PROJECT TITLE</th>
                <th>PROJECT OBJECTIVE</th>
                <th>OUTPUT OF THE YEAR</th>
                <th>STRATEGY ACTIVITY</th>
                <th>PERFORMANCE INDICATORS</th>
                <th>MOV’S</th>
                <th>PERSON/SRESPONSIBLE</th>
                <th>SCHEDULE</th>
                <th>VENUE</th>
                <th>BUDGET PER ACTIVITY</th>
                <th>BUDGET SOURCE</th>
                <th>MATERIALS</th>
            </tr>
        
        <tbody >
            <?php 
            $pia = $this->SGODModel->get_all_aip($row->school_id, $row->fy, $row->pia,$row->b_code);
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
                    <td><?= $row->sip_pObjective; ?></td>
                    <td><?= $row->sip_output; ?></td>
                    <td><?= $row->strategy; ?></td>
                    <td><?= $row->pi; ?></td>
                    <td><?= $row->movs; ?></td>
                    <td><?= $row->pr; ?></td>
                    
                <?php } ?>
                <?php $sp = $row->sip_project; ?>

                <?php $prd = $row->pr; ?>
                <td><?= $row->schedule; ?></td>
                <td><?= $row->venue; ?></td>
                <td><?= number_format($row->budget); ?></td>
                <td><?= $row->budget_source; ?></td>
                <td class="mat"><?= $row->materials; ?></td>
            </tr>
            <?php } ?>
           
        </tbody> 
    </table>
    <?php } ?>

    <div class="fcon">
                <img style="width:90px; float:left;" src="https://qrcode.tec-it.com/API/QRCode?data=<?= base_url(); ?>Page/aip_admin/<?= $data_row->school_id.'/'.$data_row->fy.'/'.$data_row->b_code.'/'.$data_row->id; ?>" title="" />
                <div class="lcon">
                    
                    System Generated Report<br />
                    Annual Implementation Plan <br />
                    Date Generated : <?php  date_default_timezone_set('Asia/Manila'); echo date('F d, Y', time()); ?><br />
                    Date Approved : 
                    
                </div>
                
                <div class="blocker"></div>
    </div>







    </body>
    </hmlm>