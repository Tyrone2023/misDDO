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
    


    <body class="aip_generate app_print aip" id="printTable">


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
        <span class="r">School Division of Davao Oriental</span><br />
        <span class="sadress"><?= strtoupper($school->district); ?><br />
         <?= strtoupper($school->schoolName); ?><br />
         <?= strtoupper($school->sitio); ?> <?= strtoupper($school->brgy); ?>, <?= strtoupper($school->city); ?>, <?= strtoupper($school->province); ?></span> 
    </p>
    <div class="hr"></div>
    <h1><?= $title; ?><br />FY <?= $fy; ?> </h1> 
   

    <table width="100%">
        <?php 
            $mb = '.'.$abp->mb;
            $mr = '.'.$abp->mr;
            $tlip = '.'.$abp->tli;
            $tst = '.'.$abp->tst;



            $mooe = $ssa->alloc_amount; 
            $mandatory = $mooe*$mb;
            $minor = $mooe*$mr;
            $tli = $mooe*$tlip;
            $ac = $mooe*$tst;
            $monthly = $mooe/12;
            $quarterly = $monthly*3;

        ?>
        
            <tr>
            <th colspan="21" class="nobc"></th>
            <th colspan="2">Monthly</th>
            </tr>
            <tr>
                <th colspan="2">Source of Fund:</th>
                <th colspan="8" class="nobc"> <?= $ft->alloc_type; ?></th>
                <th colspan="8">Mandatory   (<?= $abp->mb; ?>%):</th>
                <th colspan="3" class="nobc"><?php if($abp->mb != 100){echo number_format(($mandatory), 2, '.', ',');}else{echo number_format($ft->alloc_amount, 2);} ?></th>
                <th colspan="2" class="nobc"></th><?php $mbmc = $monthly*$mb; ?>
            </tr>
            <tr>
                <th colspan="2">Annual Amount:</th>
                <th colspan="8" class="nobc"><?= number_format(($mooe), 2, '.', ','); ?></th>
                <th colspan="8">Minor Repair (<?= $abp->mr; ?>%)</th>
                <th colspan="3" class="nobc"><?php if($abp->mr != 100){echo number_format(($minor), 2, '.', ',');}else{echo number_format($ft->alloc_amount, 2);} ?></th>
                <th colspan="2" class="nobc"></th><?php $mrmc = $monthly*$mr; ?>
            </tr>
            <tr>
                <th colspan="2">Monthly</th>
                <th colspan="8" class="nobc"></th></th>
                <th colspan="8">Teaching-Learning Instructions (<?= $abp->tli; ?>%)</th>
                <th colspan="3" class="nobc"><?php if($abp->tli != 100){echo number_format(($tli), 2, '.', ',');}else{echo number_format($ft->alloc_amount, 2);} ?></th>
                <th colspan="2" class="nobc"></th><?php $tlimc = $monthly*$tlip; ?>
            </tr>
            <tr>
                <th colspan="2">Quarterly</th>
                <th colspan="8" class="nobc"></th>
                <th colspan="8">Attendance to & Conduct of Trainings/Seminars/Conferences (<?= $abp->tst; ?>%) </th>
                <th colspan="3" class="nobc"><?php if($abp->tst != 100){echo number_format(($ac), 2, '.', ',');}else{echo number_format($ft->alloc_amount, 2);} ?></th>
                <th colspan="2" class="nobc"></th><?php $tstmc = $monthly*$tst; ?>
            </tr>
            
            
         
        <tbody >
            <tr>
                <td rowspan="2">No.</td>
                <td rowspan="2">ITEM & DESCRIPTION</td>
                <td rowspan="2">UNIT PRICE</td>
                <td rowspan="2">Quantity</td>
                <td rowspan="2">Unit Measure</td>
                <td rowspan="2">Budget Allocated</td>
                <td colspan="4">1st Quarter</td>
                <td colspan="4">2nd Quarter</td>
                <td colspan="4">3rd Quarter</td>
                <td colspan="4">4rth Quarter</td>
                <td>Total</td>
            </tr>
            <tr>
                <td>Jan.</td>
                <td>Feb.</td>
                <td>Mar.</td>
                <td>Total</td>
                <td>April</td>
                <td>May</td>
                <td>June</td>
                <td>Total</td>
                <td>July</td>
                <td>Aug.</td>
                <td>sep.</td>
                <td>Total</td>
                <td>oct.</td>
                <td>Nov.</td>
                <td>Dec.</td>
                <td>Total</td>
                <td>Amount</td>
            </tr>
            <tr>
                <td colspan="23" class="bar">I. MANDATORY (<?= $abp->mb; ?>%)</td>
            </tr>
            <?php 
                $aip_by_pillar = $this->SGODModel->aipv2('sgod_aip',$school_id,$fy,$b_code,'MANDATORY BILLS');
                $otba = 0;
                $otjan = 0;
                $otfeb = 0;
                $otmar = 0;
                $otapril = 0;
                $otmay = 0;
                $otjune = 0;
                $otjuly = 0;
                $otaug = 0;
                $otsept = 0;
                $otoct = 0;
                $otnov = 0;
                $otdec = 0;
                $otfq = 0;
                $otsq = 0;
                $ottq = 0;
                $otfrq = 0;
                foreach($aip_by_pillar as $row){
                    
            ?>
            <tr>
                <td><?= $row->pillar; ?></td>
                <td colspan="8" class="pt"><?= $row->sip_project; ?> / <?= $row->strategy; ?></td>
                <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
            </tr>
            <?php 
            $get_app = $this->SGODModel->one_cond('sgod_app', 'aip_id',$row->id);
            $ba = 0;
            $jan = 0;
            $feb = 0;
            $mar = 0;
            $april = 0;
            $may = 0;
            $june = 0;
            $july = 0;
            $aug = 0;
            $sept = 0;
            $oct = 0;
            $nov = 0;
            $dec = 0;
            $fq = 0;
            $sq = 0;
            $tqs = 0;
            $frq = 0;
            foreach($get_app as $approw){ ?>
            <?php $tq = (double)$approw->qjan+(double)$approw->qfeb+(double)$approw->qmar+(double)$approw->qapril+(double)$approw->qmay+(double)$approw->qjune+(double)$approw->qjuly+(double)$approw->qaug+(double)$approw->qsept+(double)$approw->qoct+(double)$approw->qnov+(double)$approw->qdec; ?>
                
                <tr>
                    <td></td>
                    <td><?= $approw->materials; ?></td>
                    <td><?php if($approw->unit_price != ""){echo number_format($approw->unit_price);}  ?></td>
                    <td><?= $tq; ?> </td>
                    <td><?= $approw->unit_measure; ?></td>
                    <?php 
                        $mbmtjan = (double)$approw->jan*(double)$approw->qjan;
                        $mbmtfeb = (double)$approw->feb*(double)$approw->qfeb;
                        $mbmtmar = (double)$approw->mar*(double)$approw->qmar;
                        $firstqs = $mbmtjan+$mbmtfeb+$mbmtmar;
                        $mbmtapril = (double)$approw->april*(double)$approw->qapril;
                        $mbmtmay = (double)$approw->may*(double)$approw->qmay;
                        $mbmtjune = (double)$approw->june*(double)$approw->qjune;
                        $secondq = $mbmtapril+$mbmtmay+$mbmtjune;
                        $mbmtjuly = (double)$approw->july*(double)$approw->qjuly;
                        $mbmtaug = (double)$approw->aug*(double)$approw->qaug;
                        $mbmtsept = (double)$approw->sept*(double)$approw->qsept;
                        $threedq = $mbmtjuly+$mbmtaug+$mbmtsept;
                        $mbmtoct = (double)$approw->oct*(double)$approw->qoct;
                        $mbmtnov = (double)$approw->nov*(double)$approw->qnov;
                        $mbmtdec = (double)$approw->ddec*(double)$approw->qdec;
                        $fourthq = $mbmtoct+$mbmtnov+$mbmtdec;
                    ?>
                    <td><?php if(isset($approw->unit_price)){$tbs = $firstqs+$secondq+$threedq+$fourthq; echo number_format($tbs);} ?></td>
                    <td><?php $mbmtjan = (double)$approw->jan*(double)$approw->qjan; echo number_format($mbmtjan); ?></td>
                    <td><?php $mbmtfeb = (double)$approw->feb*(double)$approw->qfeb; echo number_format($mbmtfeb); ?></td>
                    <td><?php $mbmtmar = (double)$approw->mar*(double)$approw->qmar; echo number_format($mbmtmar); ?></td>

                    <td><?php $firstq = $mbmtjan+$mbmtfeb+$mbmtmar; echo number_format($firstq); ?></td>

                    <td><?php $mbmtapril = (double)$approw->april*(double)$approw->qapril; echo number_format($mbmtapril); ?></td>
                    <td><?php $mbmtmay = (double)$approw->may*(double)$approw->qmay; echo number_format($mbmtmay); ?></td>
                    <td><?php $mbmtjune = (double)$approw->june*(double)$approw->qjune; echo number_format($mbmtjune); ?></td>

                    <td><?php $secondq = $mbmtapril+$mbmtmay+$mbmtjune; echo number_format($secondq); ?></td>

                    <td><?php $mbmtjuly = (double)$approw->july*(double)$approw->qjuly; echo number_format($mbmtjuly); ?></td>
                    <td><?php $mbmtaug = (double)$approw->aug*(double)$approw->qaug; echo number_format($mbmtaug); ?></td>
                    <td><?php $mbmtsept = (double)$approw->sept*(double)$approw->qsept; echo number_format($mbmtsept); ?></td>

                    <td><?php $threedq = $mbmtjuly+$mbmtaug+$mbmtsept; echo number_format($threedq); ?></td>

                    <td><?php $mbmtoct = (double)$approw->oct*(double)$approw->qoct; echo number_format($mbmtoct); ?></td>
                    <td><?php $mbmtnov = (double)$approw->nov*(double)$approw->qnov; echo number_format($mbmtnov); ?></td>
                    <td><?php $mbmtdec = (double)$approw->ddec*(double)$approw->qdec; echo number_format($mbmtdec); ?></td>

                    <td><?php $fourthq = $mbmtoct+$mbmtnov+$mbmtdec; echo number_format($fourthq); ?></td>
                    <td><?php $yt = $firstq+$secondq+$threedq+$fourthq; echo number_format($yt); ?></td>
                    <?php 
                        $ba += (double)$tbs; 
                        $jan += (double)$mbmtjan; 
                        $feb += (double)$mbmtfeb; 
                        $mar += (double)$mbmtmar; 
                        $april += (double)$mbmtapril; 
                        $may += (double)$mbmtmay; 
                        $june += (double)$mbmtjune; 
                        $july += (double)$mbmtjuly; 
                        $aug += (double)$mbmtaug; 
                        $sept += (double)$mbmtsept; 
                        $oct += (double)$mbmtoct; 
                        $nov += (double)$mbmtnov; 
                        $dec += (double)$mbmtdec; 
                        $fq += (double)$firstq;
                        $sq += (double)$secondq;
                        $tqs += (double)$threedq;
                        $frq += (double)$fourthq;
                    ?>
                </tr>

            <?php } ?>
        
            <tr>
               <td></td>
               <td class="st">Total</td>
               <td class="st"></td>
               <td class="st"></td>
               <td class="st"></td>
               <td class="st"><?= number_format($ba); ?></td>
               <td class="st"><?= number_format($jan); ?></td>
               <td class="st"><?= number_format($feb); ?></td>
               <td class="st"><?= number_format($mar); ?></td>

               <td class="st"><?= number_format($fq); ?></td>
               <td class="st"><?= number_format($april); ?></td>
               <td class="st"><?= number_format($may); ?></td>
               <td class="st"><?= number_format($june); ?></td>

               <td class="st"><?= number_format($sq); ?></td>
               <td class="st"><?= number_format($july); ?></td>
               <td class="st"><?= number_format($aug); ?></td>
               <td class="st"><?= number_format($sept); ?></td>

               <td class="st"><?= number_format($tqs); ?></td>
               <td class="st"><?= number_format($oct); ?></td>
               <td class="st"><?= number_format($nov); ?></td>
               <td class="st"><?= number_format($dec); ?></td>
               
               <td class="st"><?= number_format($frq); ?></td>
               <td class="st"><?= number_format($fq+$sq+$tqs+$frq); ?></td>

            </tr>
            <?php
                $otba += (double)$ba;
                $otjan += (double)$jan; 
                $otfeb += (double)$feb; 
                $otmar += (double)$mar; 
                $otapril += (double)$april; 
                $otmay += (double)$may; 
                $otjune += (double)$june; 
                $otjuly += (double)$july; 
                $otaug += (double)$aug; 
                $otsept += (double)$sept; 
                $otoct += (double)$oct; 
                $otnov += (double)$nov; 
                $otdec += (double)$dec; 
                $otfq += (double)$fq;
                $otsq += (double)$sq;
                $ottq += (double)$tqs;
                $otfrq += (double)$frq;
             ?>
        
        <?php } ?>

            <tr>
                <td class="t"></td>
                <td class="t">Total Mandatory Bills</td>
                <td class="t"></td>
                <td class="t"></td>
                <td class="t"></td>
                <td class="t"><?= number_format($otba); ?></td>
                <td class="t"><?= number_format($otjan); ?>  </td><?php $mbjan = $otjan; ?>
                <td class="t"><?= number_format($otfeb); ?></td><?php $mbfeb = $otfeb; ?>
                <td class="t"><?= number_format($otmar); ?></td><?php $mbmar = $otmar; ?>
                <td class="t"><b><?= number_format($otfq); ?></b></td><?php $mbfq = $otfq; ?>
                <td class="t"><?= number_format($otapril); ?></td><?php $mbapril = $otapril; ?>
                <td class="t"><?= number_format($otmay); ?></td><?php $mbmay = $otmay; ?>
                <td class="t"><?= number_format($otjune); ?></td><?php $mbjune = $otjune; ?>
                <td class="t"><b><?= number_format($otsq); ?></b></td><?php $mbsq = $otsq; ?>
                <td class="t"><?= number_format($otjuly); ?></td><?php $mbjuly = $otjuly; ?>
                <td class="t"><?= number_format($otaug); ?></td><?php $mbaug = $otaug; ?>
                <td class="t"><?= number_format($otsept); ?></td><?php $mbsept = $otsept; ?>
                <td class="t"><b><?= number_format($ottq); ?></b></td><?php $mbtq = $ottq; ?>
                <td class="t"><?= number_format($otoct); ?></td><?php $mboct = $otoct; ?>
                <td class="t"><?= number_format($otnov); ?></td><?php $mbnov = $otnov; ?>
                <td class="t"><?= number_format($otdec); ?></td><?php $mbdec = $otdec; ?>
                <td class="t"><b><?= number_format($otdec); ?></b></td><?php $mbfrq = $otfrq; ?>
                <td class="t"><?= number_format($otfq+$otsq+$ottq+$otfrq); ?></td></td><?php $mbtotal = $otfq+$otsq+$ottq+$otfrq; ?>
            </tr>


            <tr>
                <td colspan="23" class="bar">II. MINOR REPAIR     ( <?= $abp->mr; ?>% )</td>
            </tr>
            <?php 
                $aip_by_pillar = $this->SGODModel->aipv2('sgod_aip',$school_id,$fy,$b_code,'MINOR REPAIR');
                $otba = 0;
                $otjan = 0;
                $otfeb = 0;
                $otmar = 0;
                $otapril = 0;
                $otmay = 0;
                $otjune = 0;
                $otjuly = 0;
                $otaug = 0;
                $otsept = 0;
                $otoct = 0;
                $otnov = 0;
                $otdec = 0;
                $otfq = 0;
                $otsq = 0;
                $ottq = 0;
                $otfrq = 0;
                foreach($aip_by_pillar as $row){
                    
            ?>
            <tr>
                <td><?= $row->pillar; ?></td>
                <td colspan="8" class="pt"><?= $row->sip_project; ?> / <?= $row->strategy; ?></td>
                <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
            </tr>
            <?php 
            $get_app = $this->SGODModel->one_cond('sgod_app', 'aip_id',$row->id);
            $ba = 0;
            $jan = 0;
            $feb = 0;
            $mar = 0;
            $april = 0;
            $may = 0;
            $june = 0;
            $july = 0;
            $aug = 0;
            $sept = 0;
            $oct = 0;
            $nov = 0;
            $dec = 0;
            $fq = 0;
            $sq = 0;
            $tqs = 0;
            $frq = 0;
            foreach($get_app as $approw){ ?>
                
                <?php $tq = (double)$approw->qjan+(double)$approw->qfeb+(double)$approw->qmar+(double)$approw->qapril+(double)$approw->qmay+(double)$approw->qjune+(double)$approw->qjuly+(double)$approw->qaug+(double)$approw->qsept+(double)$approw->qoct+(double)$approw->qnov+(double)$approw->qdec; ?>
                
                <tr>
                    <td></td>
                    <td><?= $approw->materials; ?></td>
                    <td><?php if($approw->unit_price != ""){echo number_format($approw->unit_price);}  ?></td>
                    <td><?= $tq; ?></td>
                    <td><?= $approw->unit_measure; ?></td>
                    <?php 
                        $mbmtjan = (double)$approw->jan*(double)$approw->qjan;
                        $mbmtfeb = (double)$approw->feb*(double)$approw->qfeb;
                        $mbmtmar = (double)$approw->mar*(double)$approw->qmar;
                        $firstqs = $mbmtjan+$mbmtfeb+$mbmtmar;
                        $mbmtapril = (double)$approw->april*(double)$approw->qapril;
                        $mbmtmay = (double)$approw->may*(double)$approw->qmay;
                        $mbmtjune = (double)$approw->june*(double)$approw->qjune;
                        $secondq = $mbmtapril+$mbmtmay+$mbmtjune;
                        $mbmtjuly = (double)$approw->july*(double)$approw->qjuly;
                        $mbmtaug = (double)$approw->aug*(double)$approw->qaug;
                        $mbmtsept = (double)$approw->sept*(double)$approw->qsept;
                        $threedq = $mbmtjuly+$mbmtaug+$mbmtsept;
                        $mbmtoct = (double)$approw->oct*(double)$approw->qoct;
                        $mbmtnov = (double)$approw->nov*(double)$approw->qnov;
                        $mbmtdec = (double)$approw->ddec*(double)$approw->qdec;
                        $fourthq = $mbmtoct+$mbmtnov+$mbmtdec;
                    ?>
                    <td><?php if(isset($approw->unit_price)){$tbs = $firstqs+$secondq+$threedq+$fourthq; echo number_format($tbs);} ?></td>
                    <td><?php $mbmtjan = (double)$approw->jan*(double)$approw->qjan; echo number_format($mbmtjan); ?></td>
                    <td><?php $mbmtfeb = (double)$approw->feb*(double)$approw->qfeb; echo number_format($mbmtfeb); ?></td>
                    <td><?php $mbmtmar = (double)$approw->mar*(double)$approw->qmar; echo number_format($mbmtmar); ?></td>

                    <td><?php $firstq = $mbmtjan+$mbmtfeb+$mbmtmar; echo number_format($firstq); ?></td>

                    <td><?php $mbmtapril = (double)$approw->april*(double)$approw->qapril; echo number_format($mbmtapril); ?></td>
                    <td><?php $mbmtmay = (double)$approw->may*(double)$approw->qmay; echo number_format($mbmtmay); ?></td>
                    <td><?php $mbmtjune = (double)$approw->june*(double)$approw->qjune; echo number_format($mbmtjune); ?></td>

                    <td><?php $secondq = $mbmtapril+$mbmtmay+$mbmtjune; echo number_format($secondq); ?></td>

                    <td><?php $mbmtjuly = (double)$approw->july*(double)$approw->qjuly; echo number_format($mbmtjuly); ?></td>
                    <td><?php $mbmtaug = (double)$approw->aug*(double)$approw->qaug; echo number_format($mbmtaug); ?></td>
                    <td><?php $mbmtsept = (double)$approw->sept*(double)$approw->qsept; echo number_format($mbmtsept); ?></td>

                    <td><?php $threedq = $mbmtjuly+$mbmtaug+$mbmtsept; echo number_format($threedq); ?></td>

                    <td><?php $mbmtoct = (double)$approw->oct*(double)$approw->qoct; echo number_format($mbmtoct); ?></td>
                    <td><?php $mbmtnov = (double)$approw->nov*(double)$approw->qnov; echo number_format($mbmtnov); ?></td>
                    <td><?php $mbmtdec = (double)$approw->ddec*(double)$approw->qdec; echo number_format($mbmtdec); ?></td>

                    <td><?php $fourthq = $mbmtoct+$mbmtnov+$mbmtdec; echo number_format($fourthq); ?></td>
                    <td><?php $yt = $firstq+$secondq+$threedq+$fourthq; echo number_format($yt); ?></td>
                    <?php 
                        $ba += (double)$tbs; 
                        $jan += (double)$mbmtjan; 
                        $feb += (double)$mbmtfeb; 
                        $mar += (double)$mbmtmar; 
                        $april += (double)$mbmtapril; 
                        $may += (double)$mbmtmay; 
                        $june += (double)$mbmtjune; 
                        $july += (double)$mbmtjuly; 
                        $aug += (double)$mbmtaug; 
                        $sept += (double)$mbmtsept; 
                        $oct += (double)$mbmtoct; 
                        $nov += (double)$mbmtnov; 
                        $dec += (double)$mbmtdec; 
                        $fq += (double)$firstq;
                        $sq += (double)$secondq;
                        $tqs += (double)$threedq;
                        $frq += (double)$fourthq;
                    ?>
                </tr>

            <?php } ?>
        
            <tr>
               <td></td>
               <td class="st">Total</td>
               <td class="st"></td>
               <td class="st"></td>
               <td class="st"></td>
               <td class="st"><?= number_format($ba); ?></td>
               <td class="st"><?= number_format($jan); ?></td>
               <td class="st"><?= number_format($feb); ?></td>
               <td class="st"><?= number_format($mar); ?></td>
               <td class="st"><?= number_format($fq); ?></td>
               <td class="st"><?= number_format($april); ?></td>
               <td class="st"><?= number_format($may); ?></td>
               <td class="st"><?= number_format($june); ?></td>
               <td class="st"><?= number_format($sq); ?></td>
               <td class="st"><?= number_format($july); ?></td>
               <td class="st"><?= number_format($aug); ?></td>
               <td class="st"><?= number_format($sept); ?></td>
               <td class="st"><?= number_format($tqs); ?></td>
               <td class="st"><?= number_format($oct); ?></td>
               <td class="st"><?= number_format($nov); ?></td>
               <td class="st"><?= number_format($dec); ?></td>
               <td class="st"><?= number_format($frq); ?></td>
               <td class="st"><?= number_format($fq+$sq+$tqs+$frq); ?></td>

            </tr>
            <?php
                $otba += (double)$ba;
                $otjan += (double)$jan; 
                $otfeb += (double)$feb; 
                $otmar += (double)$mar; 
                $otapril += (double)$april; 
                $otmay += (double)$may; 
                $otjune += (double)$june; 
                $otjuly += (double)$july; 
                $otaug += (double)$aug; 
                $otsept += (double)$sept; 
                $otoct += (double)$oct; 
                $otnov += (double)$nov; 
                $otdec += (double)$dec; 
                $otfq += (double)$fq;
                $otsq += (double)$sq;
                $ottq += (double)$tqs;
                $otfrq += (double)$frq;
             ?>
        
        <?php } ?>

            <tr>
                <td class="t"></td>
                <td class="t">Total Minor Repair </td>
                <td class="t"></td>
                <td class="t"></td>
                <td class="t"></td>
                <td class="t"><?= number_format($otba); ?></td>
                <td class="t"><?= number_format($otjan); ?></td><?php $mrjan = $otjan; ?>
                <td class="t"><?= number_format($otfeb); ?></td><?php $mrfeb = $otfeb; ?>
                <td class="t"><?= number_format($otmar); ?></td><?php $mrmar = $otmar; ?>
                <td class="t"><?= number_format($otfq); ?></td><?php $mrfq = $otfq; ?>
                <td class="t"><?= number_format($otapril); ?></td><?php $mrapril = $otapril; ?>
                <td class="t"><?= number_format($otmay); ?></td><?php $mrmay = $otmay; ?>
                <td class="t"><?= number_format($otjune); ?></td><?php $mrjune = $otjune; ?>
                <td class="t"><?= number_format($otsq); ?></td><?php $mrsq = $otsq; ?>
                <td class="t"><?= number_format($otjuly); ?></td><?php $mrjuly = $otjuly; ?>
                <td class="t"><?= number_format($otaug); ?></td><?php $mraug = $otaug; ?>
                <td class="t"><?= number_format($otsept); ?></td><?php $mrsept = $otsept; ?>
                <td class="t"><?= number_format($ottq); ?></td><?php $mrtq = $ottq; ?>
                <td class="t"><?= number_format($otoct); ?></td><?php $mroct = $otoct; ?>
                <td class="t"><?= number_format($otnov); ?></td><?php $mrnov = $otnov; ?>
                <td class="t"><?= number_format($otdec); ?></td><?php $mrdec = $otdec; ?>
                <td class="t"><?= number_format($otfrq); ?></td><?php $mrfrq = $otfrq; ?>
                <td class="t"><?= number_format($otfq+$otsq+$ottq+$otfrq); ?></td></td><?php $mrtotal = $otfq+$otsq+$ottq+$otfrq; ?>
            </tr>

            <tr>
                <td colspan="23" class="bar">III. TEACHING-LEARNING INSTRUCTION   (<?= $abp->tli; ?>% )</td>
            </tr>
            <?php 
                $aip_by_pillar = $this->SGODModel->aipv2('sgod_aip',$school_id,$fy,$b_code,'TEACHING-LEARNING INSTRUCTION');
                $otba = 0;
                $otjan = 0;
                $otfeb = 0;
                $otmar = 0;
                $otapril = 0;
                $otmay = 0;
                $otjune = 0;
                $otjuly = 0;
                $otaug = 0;
                $otsept = 0;
                $otoct = 0;
                $otnov = 0;
                $otdec = 0;
                $otfq = 0;
                $otsq = 0;
                $ottq = 0;
                $otfrq = 0;
                foreach($aip_by_pillar as $row){
                    
            ?>
            <tr>
                <td><?= $row->pillar; ?></td>
                <td colspan="8" class="pt"><?= $row->sip_project; ?> / <?= $row->strategy; ?></td>
                <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
            </tr>
            <?php 
            $get_app = $this->SGODModel->one_cond('sgod_app', 'aip_id',$row->id);
            $ba = 0;
            $jan = 0;
            $feb = 0;
            $mar = 0;
            $april = 0;
            $may = 0;
            $june = 0;
            $july = 0;
            $aug = 0;
            $sept = 0;
            $oct = 0;
            $nov = 0;
            $dec = 0;
            $fq = 0;
            $sq = 0;
            $tqs = 0;
            $frq = 0;
            foreach($get_app as $approw){ ?>
                
                <?php $tq = (double)$approw->qjan+(double)$approw->qfeb+(double)$approw->qmar+(double)$approw->qapril+(double)$approw->qmay+(double)$approw->qjune+(double)$approw->qjuly+(double)$approw->qaug+(double)$approw->qsept+(double)$approw->qoct+(double)$approw->qnov+(double)$approw->qdec; ?>
                
                <tr>
                    <td></td>
                    <td><?= $approw->materials; ?></td>
                    <td><?php if($approw->unit_price != ""){echo number_format($approw->unit_price);}  ?></td>
                    <td><?= $tq; ?></td>
                    <td><?= $approw->unit_measure; ?></td>
                    <?php 
                        $mbmtjan = (double)$approw->jan*(double)$approw->qjan;
                        $mbmtfeb = (double)$approw->feb*(double)$approw->qfeb;
                        $mbmtmar = (double)$approw->mar*(double)$approw->qmar;
                        $firstqs = $mbmtjan+$mbmtfeb+$mbmtmar;
                        $mbmtapril = (double)$approw->april*(double)$approw->qapril;
                        $mbmtmay = (double)$approw->may*(double)$approw->qmay;
                        $mbmtjune = (double)$approw->june*(double)$approw->qjune;
                        $secondq = $mbmtapril+$mbmtmay+$mbmtjune;
                        $mbmtjuly = (double)$approw->july*(double)$approw->qjuly;
                        $mbmtaug = (double)$approw->aug*(double)$approw->qaug;
                        $mbmtsept = (double)$approw->sept*(double)$approw->qsept;
                        $threedq = $mbmtjuly+$mbmtaug+$mbmtsept;
                        $mbmtoct = (double)$approw->oct*(double)$approw->qoct;
                        $mbmtnov = (double)$approw->nov*(double)$approw->qnov;
                        $mbmtdec = (double)$approw->ddec*(double)$approw->qdec;
                        $fourthq = $mbmtoct+$mbmtnov+$mbmtdec;
                    ?>
                    <td><?php if(isset($approw->unit_price)){$tbs = $firstqs+$secondq+$threedq+$fourthq; echo number_format($tbs);} ?></td>
                    <td><?php $mbmtjan = (double)$approw->jan*(double)$approw->qjan; echo number_format($mbmtjan); ?></td>
                    <td><?php $mbmtfeb = (double)$approw->feb*(double)$approw->qfeb; echo number_format($mbmtfeb); ?></td>
                    <td><?php $mbmtmar = (double)$approw->mar*(double)$approw->qmar; echo number_format($mbmtmar); ?></td>

                    <td><?php $firstq = $mbmtjan+$mbmtfeb+$mbmtmar; echo number_format($firstq); ?></td>

                    <td><?php $mbmtapril = (double)$approw->april*(double)$approw->qapril; echo number_format($mbmtapril); ?></td>
                    <td><?php $mbmtmay = (double)$approw->may*(double)$approw->qmay; echo number_format($mbmtmay); ?></td>
                    <td><?php $mbmtjune = (double)$approw->june*(double)$approw->qjune; echo number_format($mbmtjune); ?></td>

                    <td><?php $secondq = $mbmtapril+$mbmtmay+$mbmtjune; echo number_format($secondq); ?></td>

                    <td><?php $mbmtjuly = (double)$approw->july*(double)$approw->qjuly; echo number_format($mbmtjuly); ?></td>
                    <td><?php $mbmtaug = (double)$approw->aug*(double)$approw->qaug; echo number_format($mbmtaug); ?></td>
                    <td><?php $mbmtsept = (double)$approw->sept*(double)$approw->qsept; echo number_format($mbmtsept); ?></td>

                    <td><?php $threedq = $mbmtjuly+$mbmtaug+$mbmtsept; echo number_format($threedq); ?></td>

                    <td><?php $mbmtoct = (double)$approw->oct*(double)$approw->qoct; echo number_format($mbmtoct); ?></td>
                    <td><?php $mbmtnov = (double)$approw->nov*(double)$approw->qnov; echo number_format($mbmtnov); ?></td>
                    <td><?php $mbmtdec = (double)$approw->ddec*(double)$approw->qdec; echo number_format($mbmtdec); ?></td>

                    <td><?php $fourthq = $mbmtoct+$mbmtnov+$mbmtdec; echo number_format($fourthq); ?></td>
                    <td><?php $yt = $firstq+$secondq+$threedq+$fourthq; echo number_format($yt); ?></td>
                    <?php 
                        $ba += (double)$tbs; 
                        $jan += (double)$mbmtjan; 
                        $feb += (double)$mbmtfeb; 
                        $mar += (double)$mbmtmar; 
                        $april += (double)$mbmtapril; 
                        $may += (double)$mbmtmay; 
                        $june += (double)$mbmtjune; 
                        $july += (double)$mbmtjuly; 
                        $aug += (double)$mbmtaug; 
                        $sept += (double)$mbmtsept; 
                        $oct += (double)$mbmtoct; 
                        $nov += (double)$mbmtnov; 
                        $dec += (double)$mbmtdec; 
                        $fq += (double)$firstq;
                        $sq += (double)$secondq;
                        $tqs += (double)$threedq;
                        $frq += (double)$fourthq;
                    ?>
                </tr>

            <?php } ?>
        
            <tr>
               <td></td>
               <td class="st">Total</td>
               <td class="st"></td>
               <td class="st"></td>
               <td class="st"></td>
               <td class="st"><?= number_format($ba); ?></td>
               <td class="st"><?= number_format($jan); ?></td>
               <td class="st"><?= number_format($feb); ?></td>
               <td class="st"><?= number_format($mar); ?></td>
               <td class="st"><?= number_format($fq); ?></td>
               <td class="st"><?= number_format($april); ?></td>
               <td class="st"><?= number_format($may); ?></td>
               <td class="st"><?= number_format($june); ?></td>
               <td class="st"><?= number_format($sq); ?></td>
               <td class="st"><?= number_format($july); ?></td>
               <td class="st"><?= number_format($aug); ?></td>
               <td class="st"><?= number_format($sept); ?></td>
               <td class="st"><?= number_format($tqs); ?></td>
               <td class="st"><?= number_format($oct); ?></td>
               <td class="st"><?= number_format($nov); ?></td>
               <td class="st"><?= number_format($dec); ?></td>
               <td class="st"><?= number_format($frq); ?></td>
               <td class="st"><?= number_format($fq+$sq+$tqs+$frq); ?></td>

            </tr>
            <?php
                $otba += (double)$ba;
                $otjan += (double)$jan; 
                $otfeb += (double)$feb; 
                $otmar += (double)$mar; 
                $otapril += (double)$april; 
                $otmay += (double)$may; 
                $otjune += (double)$june; 
                $otjuly += (double)$july; 
                $otaug += (double)$aug; 
                $otsept += (double)$sept; 
                $otoct += (double)$oct; 
                $otnov += (double)$nov; 
                $otdec += (double)$dec; 
                $otfq += (double)$fq;
                $otsq += (double)$sq;
                $ottq += (double)$tqs;
                $otfrq += (double)$frq;
             ?>
        
        <?php } ?>

            <tr>
                <td class="t"></td>
                <td class="t">Total Teaching-learning Instruction </td>
                <td class="t"></td>
                <td class="t"></td>
                <td class="t"></td>
                <td class="t"><?= number_format($otba); ?></td>
                <td class="t"><?= number_format($otjan); ?></td><?php $tijan = $otjan; ?>
                <td class="t"><?= number_format($otfeb); ?></td><?php $tifeb = $otfeb; ?>
                <td class="t"><?= number_format($otmar); ?></td><?php $timar = $otmar; ?>
                <td class="t"><?= number_format($otfq); ?></td><?php $tifq = $otfq; ?>
                <td class="t"><?= number_format($otapril); ?></td><?php $tiapril = $otapril; ?>
                <td class="t"><?= number_format($otmay); ?></td><?php $timay = $otmay; ?>
                <td class="t"><?= number_format($otjune); ?></td><?php $tijune = $otjune; ?>
                <td class="t"><?= number_format($otsq); ?></td><?php $tisq = $otsq; ?>
                <td class="t"><?= number_format($otjuly); ?></td><?php $tijuly = $otjuly; ?>
                <td class="t"><?= number_format($otaug); ?></td><?php $tiaug = $otaug; ?>
                <td class="t"><?= number_format($otsept); ?></td><?php $tisept = $otsept; ?>
                <td class="t"><?= number_format($ottq); ?></td><?php $titq = $ottq; ?>
                <td class="t"><?= number_format($otoct); ?></td><?php $tioct = $otoct; ?>
                <td class="t"><?= number_format($otnov); ?></td><?php $tinov = $otnov; ?>
                <td class="t"><?= number_format($otdec); ?></td><?php $tidec = $otdec; ?>
                <td class="t"><?= number_format($otfrq); ?></td><?php $tifrq = $otfrq; ?>
                <td class="t"><?= number_format($otfq+$otsq+$ottq+$otfrq); ?></td></td><?php $titotal = $otfq+$otsq+$ottq+$otfrq; ?>
                
            </tr>

            <tr>
                <td colspan="23" class="bar">IV. Attendance to & Conduct of Trainings/Seminars/Conferences (<?= $abp->tst; ?>%)  </td>
            </tr>
            <?php 
                $aip_by_pillar = $this->SGODModel->aipv2('sgod_aip',$school_id,$fy,$b_code,'TRAININGS/SEMINAR/TRAVEL');
                $otba = 0;
                $otjan = 0;
                $otfeb = 0;
                $otmar = 0;
                $otapril = 0;
                $otmay = 0;
                $otjune = 0;
                $otjuly = 0;
                $otaug = 0;
                $otsept = 0;
                $otoct = 0;
                $otnov = 0;
                $otdec = 0;
                $otfq = 0;
                $otsq = 0;
                $ottq = 0;
                $otfrq = 0;
                foreach($aip_by_pillar as $row){
                    
            ?>
            <tr>
                <td><?= $row->pillar; ?></td>
                <td colspan="8" class="pt"><?= $row->sip_project; ?> / <?= $row->strategy; ?></td>
                <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
            </tr>
            <?php 
            $get_app = $this->SGODModel->one_cond('sgod_app', 'aip_id',$row->id);
            $ba = 0;
            $jan = 0;
            $feb = 0;
            $mar = 0;
            $april = 0;
            $may = 0;
            $june = 0;
            $july = 0;
            $aug = 0;
            $sept = 0;
            $oct = 0;
            $nov = 0;
            $dec = 0;
            $fq = 0;
            $sq = 0;
            $tqs = 0;
            $frq = 0;
            foreach($get_app as $approw){ ?>
                
                <?php $tq = (double)$approw->qjan+(double)$approw->qfeb+(double)$approw->qmar+(double)$approw->qapril+(double)$approw->qmay+(double)$approw->qjune+(double)$approw->qjuly+(double)$approw->qaug+(double)$approw->qsept+(double)$approw->qoct+(double)$approw->qnov+(double)$approw->qdec; ?>
                
                <tr>
                    <td></td>
                    <td><?= $approw->materials; ?></td>
                    <td><?php if($approw->unit_price != ""){echo number_format($approw->unit_price);}  ?></td>
                    <td><?= $tq; ?></td>
                    <td><?= $approw->unit_measure; ?></td>
                    <?php 
                        $mbmtjan = (double)$approw->jan*(double)$approw->qjan;
                        $mbmtfeb = (double)$approw->feb*(double)$approw->qfeb;
                        $mbmtmar = (double)$approw->mar*(double)$approw->qmar;
                        $firstqs = $mbmtjan+$mbmtfeb+$mbmtmar;
                        $mbmtapril = (double)$approw->april*(double)$approw->qapril;
                        $mbmtmay = (double)$approw->may*(double)$approw->qmay;
                        $mbmtjune = (double)$approw->june*(double)$approw->qjune;
                        $secondq = $mbmtapril+$mbmtmay+$mbmtjune;
                        $mbmtjuly = (double)$approw->july*(double)$approw->qjuly;
                        $mbmtaug = (double)$approw->aug*(double)$approw->qaug;
                        $mbmtsept = (double)$approw->sept*(double)$approw->qsept;
                        $threedq = $mbmtjuly+$mbmtaug+$mbmtsept;
                        $mbmtoct = (double)$approw->oct*(double)$approw->qoct;
                        $mbmtnov = (double)$approw->nov*(double)$approw->qnov;
                        $mbmtdec = (double)$approw->ddec*(double)$approw->qdec;
                        $fourthq = $mbmtoct+$mbmtnov+$mbmtdec;
                    ?>
                    <td><?php if(isset($approw->unit_price)){$tbs = $firstqs+$secondq+$threedq+$fourthq; echo number_format($tbs);} ?></td>
                    <td><?php $mbmtjan = (double)$approw->jan*(double)$approw->qjan; echo number_format($mbmtjan); ?></td>
                    <td><?php $mbmtfeb = (double)$approw->feb*(double)$approw->qfeb; echo number_format($mbmtfeb); ?></td>
                    <td><?php $mbmtmar = (double)$approw->mar*(double)$approw->qmar; echo number_format($mbmtmar); ?></td>

                    <td><?php $firstq = $mbmtjan+$mbmtfeb+$mbmtmar; echo number_format($firstq); ?></td>

                    <td><?php $mbmtapril = (double)$approw->april*(double)$approw->qapril; echo number_format($mbmtapril); ?></td>
                    <td><?php $mbmtmay = (double)$approw->may*(double)$approw->qmay; echo number_format($mbmtmay); ?></td>
                    <td><?php $mbmtjune = (double)$approw->june*(double)$approw->qjune; echo number_format($mbmtjune); ?></td>

                    <td><?php $secondq = $mbmtapril+$mbmtmay+$mbmtjune; echo number_format($secondq); ?></td>

                    <td><?php $mbmtjuly = (double)$approw->july*(double)$approw->qjuly; echo number_format($mbmtjuly); ?></td>
                    <td><?php $mbmtaug = (double)$approw->aug*(double)$approw->qaug; echo number_format($mbmtaug); ?></td>
                    <td><?php $mbmtsept = (double)$approw->sept*(double)$approw->qsept; echo number_format($mbmtsept); ?></td>

                    <td><?php $threedq = $mbmtjuly+$mbmtaug+$mbmtsept; echo number_format($threedq); ?></td>

                    <td><?php $mbmtoct = (double)$approw->oct*(double)$approw->qoct; echo number_format($mbmtoct); ?></td>
                    <td><?php $mbmtnov = (double)$approw->nov*(double)$approw->qnov; echo number_format($mbmtnov); ?></td>
                    <td><?php $mbmtdec = (double)$approw->ddec*(double)$approw->qdec; echo number_format($mbmtdec); ?></td>

                    <td><?php $fourthq = $mbmtoct+$mbmtnov+$mbmtdec; echo number_format($fourthq); ?></td>
                    <td><?php $yt = $firstq+$secondq+$threedq+$fourthq; echo number_format($yt); ?></td>
                    <?php 
                        $ba += (double)$tbs; 
                        $jan += (double)$mbmtjan; 
                        $feb += (double)$mbmtfeb; 
                        $mar += (double)$mbmtmar; 
                        $april += (double)$mbmtapril; 
                        $may += (double)$mbmtmay; 
                        $june += (double)$mbmtjune; 
                        $july += (double)$mbmtjuly; 
                        $aug += (double)$mbmtaug; 
                        $sept += (double)$mbmtsept; 
                        $oct += (double)$mbmtoct; 
                        $nov += (double)$mbmtnov; 
                        $dec += (double)$mbmtdec; 
                        $fq += (double)$firstq;
                        $sq += (double)$secondq;
                        $tqs += (double)$threedq;
                        $frq += (double)$fourthq;
                    ?>
                </tr>

            <?php } ?>
        
            <tr>
               <td></td>
               <td class="st">Total</td>
               <td class="st"></td>
               <td class="st"></td>
               <td class="st"></td>
               <td class="st"><?= number_format($ba); ?></td>
               <td class="st"><?= number_format($jan); ?></td>
               <td class="st"><?= number_format($feb); ?></td>
               <td class="st"><?= number_format($mar); ?></td>
               <td class="st"><?= number_format($fq); ?></td>
               <td class="st"><?= number_format($april); ?></td>
               <td class="st"><?= number_format($may); ?></td>
               <td class="st"><?= number_format($june); ?></td>
               <td class="st"><?= number_format($sq); ?></td>
               <td class="st"><?= number_format($july); ?></td>
               <td class="st"><?= number_format($aug); ?></td>
               <td class="st"><?= number_format($sept); ?></td>
               <td class="st"><?= number_format($tqs); ?></td>
               <td class="st"><?= number_format($oct); ?></td>
               <td class="st"><?= number_format($nov); ?></td>
               <td class="st"><?= number_format($dec); ?></td>
               <td class="st"><?= number_format($frq); ?></td>
               <td class="st"><?= number_format($fq+$sq+$tqs+$frq); ?></td>

            </tr>
            <?php
                $otba += (double)$ba;
                $otjan += (double)$jan; 
                $otfeb += (double)$feb; 
                $otmar += (double)$mar; 
                $otapril += (double)$april; 
                $otmay += (double)$may; 
                $otjune += (double)$june; 
                $otjuly += (double)$july; 
                $otaug += (double)$aug; 
                $otsept += (double)$sept; 
                $otoct += (double)$oct; 
                $otnov += (double)$nov; 
                $otdec += (double)$dec; 
                $otfq += (double)$fq;
                $otsq += (double)$sq;
                $ottq += (double)$tqs;
                $otfrq += (double)$frq;
             ?>
        
        <?php } ?>

            <tr>
                <td class="t"></td>
                <td class="t">Monthly Cash Allocation</td>
                <td class="t"></td>
                <td class="t"></td>
                <td class="t"></td>
                <td class="t"><?= number_format($otba); ?></td>
                <td class="t"><?= number_format($otjan); ?></td><?php $majan = $otjan; ?>
                <td class="t"><?= number_format($otfeb); ?></td><?php $mafeb = $otfeb; ?>
                <td class="t"><?= number_format($otmar); ?></td><?php $mamar = $otmar; ?>
                <td class="t"><?= number_format($otfq); ?></td><?php $mafq = $otfq; ?>
                <td class="t"><?= number_format($otapril); ?></td><?php $maapril = $otapril; ?>
                <td class="t"><?= number_format($otmay); ?></td><?php $mamay = $otmay; ?>
                <td class="t"><?= number_format($otjune); ?></td><?php $majune = $otjune; ?>
                <td class="t"><?= number_format($otsq); ?></td><?php $masq = $otsq; ?>
                <td class="t"><?= number_format($otjuly); ?></td><?php $majuly = $otjuly; ?>
                <td class="t"><?= number_format($otaug); ?></td><?php $maaug = $otaug; ?>
                <td class="t"><?= number_format($otsept); ?></td><?php $masept = $otsept; ?>
                <td class="t"><?= number_format($ottq); ?></td><?php $matq = $ottq; ?>
                <td class="t"><?= number_format($otoct); ?></td><?php $maoct = $otoct; ?>
                <td class="t"><?= number_format($otnov); ?></td><?php $manov = $otnov; ?>
                <td class="t"><?= number_format($otdec); ?></td><?php $madec = $otdec; ?>
                <td class="t"><?= number_format($otfrq); ?></td><?php $mafrq = $otfrq; ?>
                <td class="t"><?= number_format($otfq+$otsq+$ottq+$otfrq); ?></td></td><?php $matotal = $otfq+$otsq+$ottq+$otfrq; ?>
            </tr>
            <tr>
                <?php 
                  $ojan = $majan+$tijan+$mrjan+$mbjan;
                  $ofeb = $mafeb+$tifeb+$mrfeb+$mbfeb;
                  $omar = $mamar+$timar+$mrmar+$mbmar;

                  $oapril = $maapril+$tiapril+$mrapril+$mbapril;
                  $omay = $mamay+$timay+$mrmay+$mbmay;
                  $ojune = $majune+$tijune+$mrjune+$mbjune;

                  $ojuly = $majuly+$tijuly+$mrjuly+$mbjuly;
                  $oaug = $maaug+$tiaug+$mraug+$mbaug;
                  $osept = $masept+$tisept+$mrsept+$mbsept;

                  $ooct = $maoct+$tioct+$mroct+$mboct;
                  $onov = $manov+$tinov+$mrnov+$mbnov;
                  $odec = $madec+$tidec+$mrdec+$mbdec;
                  
                ?>
                <td></td>
                <td class="ot" style="text-align:left; font-weight:bold" colspan="5">Monthly Cash Allocation</td>
                <td class="ot"><a class="a" href="<?= base_url(); ?>Page/rca_view/<?= $data_row->school_id.'/'.$data_row->fy.'/'.$data_row->b_code.'/jan'; ?>" target="_blank"><b><?= number_format($ojan); ?></b></a></td>
                <td class="ot"><a class="a" href="<?= base_url(); ?>Page/rca_view/<?= $data_row->school_id.'/'.$data_row->fy.'/'.$data_row->b_code.'/feb'; ?>" target="_blank"><b><?= number_format($ofeb); ?></b></a></td>
                <td class="ot"><a class="a" href="<?= base_url(); ?>Page/rca_view/<?= $data_row->school_id.'/'.$data_row->fy.'/'.$data_row->b_code.'/mar'; ?>" target="_blank"><b><?= number_format($omar); ?></b></a></td>
                <td class="ot "><b><?= number_format($mafq+$tifq+$mrfq+$mbfq); ?></b></td>
                <td class="ot"><a class="a" href="<?= base_url(); ?>Page/rca_view/<?= $data_row->school_id.'/'.$data_row->fy.'/'.$data_row->b_code.'/april'; ?>" target="_blank"><b><?= number_format($oapril); ?></b></a></td>
                <td class="ot"><a class="a" href="<?= base_url(); ?>Page/rca_view/<?= $data_row->school_id.'/'.$data_row->fy.'/'.$data_row->b_code.'/may'; ?>" target="_blank"><b><?= number_format($omay); ?></b></a></td>
                <td class="ot"><a class="a" href="<?= base_url(); ?>Page/rca_view/<?= $data_row->school_id.'/'.$data_row->fy.'/'.$data_row->b_code.'/june'; ?>" target="_blank"><b><?= number_format($ojune); ?></b></a></td>
                <td class="ot"><b><?= number_format($masq+$tisq+$mrsq+$mbsq); ?></b></td>
                <td class="ot"><a class="a" href="<?= base_url(); ?>Page/rca_view/<?= $data_row->school_id.'/'.$data_row->fy.'/'.$data_row->b_code.'/july'; ?>" target="_blank"><b><?= number_format($ojuly); ?></b></a></td>
                <td class="ot"><a class="a" href="<?= base_url(); ?>Page/rca_view/<?= $data_row->school_id.'/'.$data_row->fy.'/'.$data_row->b_code.'/aug'; ?>" target="_blank"><b><?= number_format($oaug); ?></b></a></td>
                <td class="ot"><a class="a" href="<?= base_url(); ?>Page/rca_view/<?= $data_row->school_id.'/'.$data_row->fy.'/'.$data_row->b_code.'/sept'; ?>" target="_blank"><?= number_format($osept); ?></b></a></td>
                <td class="ot"><b><?= number_format($matq+$titq+$mrtq+$mbtq); ?></b></td>
                <td class="ot"><a class="a" href="<?= base_url(); ?>Page/rca_view/<?= $data_row->school_id.'/'.$data_row->fy.'/'.$data_row->b_code.'/oct'; ?>" target="_blank"><b><?= number_format($ooct); ?></b></a></td>
                <td class="ot"><a class="a" href="<?= base_url(); ?>Page/rca_view/<?= $data_row->school_id.'/'.$data_row->fy.'/'.$data_row->b_code.'/nov'; ?>" target="_blank"><b><?= number_format($onov); ?></b></a></td>
                <td class="ot"><a class="a" href="<?= base_url(); ?>Page/rca_view/<?= $data_row->school_id.'/'.$data_row->fy.'/'.$data_row->b_code.'/ddec'; ?>" target="_blank"><b><?= number_format($odec); ?></b></a></td>
                <td class="ot"><b><?= number_format($mafrq+$tifrq+$mrfrq+$mbfrq); ?></b></td>
                <td class="ot"><?= number_format($matotal+$titotal+$mrtotal+$mbtotal); ?></td>
            </tr>


        </tbody> 
    </table>

    <div class="fcon">
                <img style="width:90px; float:left;" src="https://qrcode.tec-it.com/API/QRCode?data=<?= base_url(); ?>Page/aip_admin/<?= $data_row->school_id.'/'.$data_row->fy.'/'.$data_row->b_code.'/'.$data_row->id; ?>" title="" />
                <div class="lcon">
                    
                    System Generated Report<br />
                    Annual Procurement Plan<br />
                    Date Generated: <?php  date_default_timezone_set('Asia/Manila'); echo date('F d, Y', time()); ?><br />
                    
                </div>
                
                <div class="blocker"></div>
    </div>
        







    </body>
            </html>

    