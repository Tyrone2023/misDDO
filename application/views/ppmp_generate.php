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


    <body class="aip_generate app_print ppmp aip" id="printTable">
    <?php if($this->session->position == 'School'){ ?>
        <div class="links">
                <a href="<?= base_url(); ?>Page/generate_aip" class="btn">AIP</a>
                <a href="<?= base_url(); ?>Page/generate_sop" class="btn btn-info">SOP</a>
                <a href="<?= base_url(); ?>Page/generate_app" class="btn btn-secondary">APP</a>
                <!-- <a href="<?= base_url(); ?>Page/generate_ppmp" class="btn btn-primary">PPMP</a> -->
                <a href="<?= base_url(); ?>Page/implementation_plans" class="btn btn-success">PLANS</a>
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
    
    <div class="at">
    
   
    </div>
   


    <img class="logo" src="<?= base_url(); ?>assets/images/report/ke.png" alt="">
    <p>
    <span class="rp">Republic of the Philippines</span><br />
        <span class="de">Department of Education</span><br />
        <span class="r">Region XI</span><br />
        <span class="r">School Division of <?= strtoupper($school->division); ?></span><br />
        <span class="sadress"><?= strtoupper($school->district); ?><br />
         <?= strtoupper($school->schoolName); ?><br />
         <?= strtoupper($school->sitio); ?> <?= strtoupper($school->brgy); ?>, <?= strtoupper($school->city); ?>, <?= strtoupper($school->province); ?></span> 
    </p>
    <div class="hr"></div>
    <h1><?= $title; ?><br />FY <?= $fy; ?> </h1>
   

    <table>
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
            <th colspan="32" class="nobc"></th>
            <th colspan="2">Monthly</th>
            </tr>
            <tr>
                <th colspan="4">Source of Fund:</th>
                <th colspan="8" class="nobc"> MOOE</th>
                <th colspan="14">Mandatory   (<?= $abp->mb; ?>%):</th>
                <th colspan="6" class="nobc"><?= number_format(($mandatory), 2, '.', ','); ?></th>
                <th colspan="2" class="nobc"><?= number_format(($monthly*$mb), 2, '.', ','); ?></th><?php $mbmc = $monthly*$mb; ?>
            </tr>
            <tr>
                <th colspan="4">Annual Amount:</th>
                <th colspan="8" class="nobc"><?= number_format(($mooe), 2, '.', ','); ?></th>
                <th colspan="14">Minor Repair (<?= $abp->mr; ?>%)</th>
                <th colspan="6" class="nobc"><?= number_format(($minor), 2, '.', ','); ?></th>
                <th colspan="2" class="nobc"><?= number_format(($monthly*$mr), 2, '.', ','); ?></th><?php $mrmc = $monthly*$mr; ?>
            </tr>
            <tr>
                <th colspan="4">Monthly</th>
                <th colspan="8" class="nobc"><?= number_format(($monthly), 2, '.', ','); ?></th></th>
                <th colspan="14">Teaching-Learning Instructions (<?= $abp->tli; ?>%)</th>
                <th colspan="6" class="nobc"><?= number_format(($tli), 2, '.', ','); ?></th>
                <th colspan="2" class="nobc"><?= number_format(($monthly*$tlip), 2, '.', ','); ?></th><?php $tlimc = $monthly*$tlip; ?>
            </tr>
            <tr>
                <th colspan="4">Quarterly</th>
                <th colspan="8" class="nobc"><?= number_format(($quarterly), 2, '.', ','); ?></th>
                <th colspan="14">Attendance to & Conduct of Trainings/Seminars/Conferences (<?= $abp->tst; ?>%) </th>
                <th colspan="6" class="nobc"><?= number_format(($ac), 2, '.', ','); ?></th>
                <th colspan="2" class="nobc"><?= number_format(($monthly*$tst), 2, '.', ','); ?></th><?php $tstmc = $monthly*$tst; ?>
            </tr>
            
            
         
        <tbody >
            <tr>
                <td rowspan="2">No.</td>
                <td rowspan="2">ITEM & DESCRIPTION</td>
                <td rowspan="2">UNIT PRICE</td>
                <td rowspan="2">Unit Measure</td>
                <td colspan="7">1st Quarter</td>
                <td colspan="7">2nd Quarter</td>
                <td colspan="7">3rd Quarter</td>
                <td colspan="7">4rth Quarter</td>
                <td>Total</td>
            </tr>
            <tr>
                <td colspan="2">January</td>
                <td colspan="2">February</td>
                <td colspan="2">March</td>
                <td>Total</td>
                <td colspan="2">April</td>
                <td colspan="2">May</td>
                <td colspan="2">June</td>
                <td>Total</td>
                <td colspan="2">July</td>
                <td colspan="2">August</td>
                <td colspan="2">September</td>
                <td>Total</td>
                <td colspan="2">October</td>
                <td colspan="2">November</td>
                <td colspan="2">December</td>
                <td>Total</td>
                <td>Amount</td>
            </tr>
            <tr>
                <td colspan="4"></td>
                <td>Quantity</td>
                <td>Amount </td>
                <td>Quantity</td>
                <td>Amount </td>
                <td>Quantity</td>
                <td>Amount </td>
                <td></td>
                <td>Quantity</td>
                <td>Amount </td>
                <td>Quantity</td>
                <td>Amount </td>
                <td>Quantity</td>
                <td>Amount </td>
                <td></td>
                <td>Quantity</td>
                <td>Amount </td>
                <td>Quantity</td>
                <td>Amount </td>
                <td>Quantity</td>
                <td>Amount </td>
                <td></td>
                <td>Quantity</td>
                <td>Amount </td>
                <td>Quantity</td>
                <td>Amount </td>
                <td>Quantity</td>
                <td>Amount </td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td colspan="33" class="bar">I. MANDATORY (<?= $abp->mb; ?>%)</td>
            </tr>
            <?php 
                $aip_by_pillar = $this->SGODModel->aip('sgod_aip',$school_id,$fy,$b_code,'MANDATORY BILLS');
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
                <tr>
                    <td></td>
                    <td><?= $approw->materials; ?></td>
                    <td><?php if($approw->unit_price != ""){echo number_format($approw->unit_price);}  ?></td>
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
                    
                    <td><?= $approw->qjan; ?></td>
                    <td><?php $mbmtjan = (double)$approw->jan*(double)$approw->qjan; echo number_format($mbmtjan); ?></td>
                    <td><?= $approw->qfeb; ?></td>
                    <td><?php $mbmtfeb = (double)$approw->feb*(double)$approw->qfeb; echo number_format($mbmtfeb); ?></td>
                    <td><?= $approw->qmar; ?></td>
                    <td><?php $mbmtmar = (double)$approw->mar*(double)$approw->qmar; echo number_format($mbmtmar); ?></td>

                    <td><?php $firstq = $mbmtjan+$mbmtfeb+$mbmtmar; echo number_format($firstq); ?></td>
                    <td><?= $approw->qapril; ?></td>
                    <td><?php $mbmtapril = (double)$approw->april*(double)$approw->qapril; echo number_format($mbmtapril); ?></td>
                    <td><?= $approw->qmay; ?></td>
                    <td><?php $mbmtmay = (double)$approw->may*(double)$approw->qmay; echo number_format($mbmtmay); ?></td>
                    <td><?= $approw->qjune; ?></td>
                    <td><?php $mbmtjune = (double)$approw->june*(double)$approw->qjune; echo number_format($mbmtjune); ?></td>

                    <td><?php $secondq = $mbmtapril+$mbmtmay+$mbmtjune; echo number_format($secondq); ?></td>

                    <td><?= $approw->qjuly; ?></td>
                    <td><?php $mbmtjuly = (double)$approw->july*(double)$approw->qjuly; echo number_format($mbmtjuly); ?></td>
                    <td><?= $approw->qaug; ?></td>
                    <td><?php $mbmtaug = (double)$approw->aug*(double)$approw->qaug; echo number_format($mbmtaug); ?></td>
                    <td><?= $approw->qsept; ?></td>
                    <td><?php $mbmtsept = (double)$approw->sept*(double)$approw->qsept; echo number_format($mbmtsept); ?></td>

                    <td><?php $threedq = $mbmtjuly+$mbmtaug+$mbmtsept; echo number_format($secondq); ?></td>

                    <td><?= $approw->qoct; ?></td>
                    <td><?php $mbmtoct = (double)$approw->oct*(double)$approw->qoct; echo number_format($mbmtoct); ?></td>
                    <td><?= $approw->qnov; ?></td>
                    <td><?php $mbmtnov = (double)$approw->nov*(double)$approw->qnov; echo number_format($mbmtnov); ?></td>
                    <td><?= $approw->qdec; ?></td>
                    <td><?php $mbmtdec = (double)$approw->ddec*(double)$approw->qdec; echo number_format($mbmtdec); ?></td>

                    <td><?php $fourthq = $mbmtoct+$mbmtnov+$mbmtdec; echo number_format($fourthq); ?></td>
                    <td><?php $yt = $firstq+$secondq+$threedq+$fourthq; echo number_format($yt); ?></td>
                    <?php 
                        
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
        
            <!-- <tr>
               <td></td>
               <td class="st">Total</td>
               <td class="st"></td>
               <td class="st"></td>
               <td class="st"></td>
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

            </tr> -->
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
                <td class="t <?php if($otjan > $mbmc){echo "red";}elseif($otjan == $mbmc){echo "green";}?>"><?= number_format($otjan); ?></td><?php $mbjan = $otjan; ?>
                <td class="t"></td>
                <td class="t <?php if($otfeb > $mbmc){echo "red";}elseif($otfeb == $mbmc){echo "green";}?>"><?= number_format($otfeb); ?></td><?php $mbfeb = $otfeb; ?>
                <td class="t"></td>
                <td class="t <?php if($otmar > $mbmc){echo "red";}elseif($otmar == $mbmc){echo "green";}?>"><?= number_format($otmar); ?></td><?php $mbmar = $otmar; ?>
                <td class="t"><b><?= number_format($otfq); ?></b></td><?php $mbfq = $otfq; ?>
                
                <td class="t"></td>
                <td class="t <?php if($otapril > $mbmc){echo "red";}elseif($otapril == $mbmc){echo "green";}?>"><?= number_format($otapril); ?></td><?php $mbapril = $otapril; ?>
                <td class="t"></td>
                <td class="t <?php if($otmay > $mbmc){echo "red";}elseif($otmay == $mbmc){echo "green";}?>"><?= number_format($otmay); ?></td><?php $mbmay = $otmay; ?>
                <td class="t"></td>
                <td class="t <?php if($otjune > $mbmc){echo "red";}elseif($otjune == $mbmc){echo "green";}?>"><?= number_format($otjune); ?></td><?php $mbjune = $otjune; ?>
                <td class="t"><b><?= number_format($otsq); ?></b></td><?php $mbsq = $otsq; ?>

                <td class="t"></td>
                <td class="t <?php if($otjuly > $mbmc){echo "red";}elseif($otjuly == $mbmc){echo "green";}?>"><?= number_format($otjuly); ?></td><?php $mbjuly = $otjuly; ?>
                <td class="t"></td>
                <td class="t <?php if($otaug > $mbmc){echo "red";}elseif($otaug == $mbmc){echo "green";}?>"><?= number_format($otaug); ?></td><?php $mbaug = $otaug; ?>
                <td class="t"></td>
                <td class="t <?php if($otsept > $mbmc){echo "red";}elseif($otsept == $mbmc){echo "green";}?>"><?= number_format($otsept); ?></td><?php $mbsept = $otsept; ?>
                <td class="t"><b><?= number_format($ottq); ?></b></td><?php $mbtq = $ottq; ?>

                <td class="t"></td>
                <td class="t <?php if($otoct > $mbmc){echo "red";}elseif($otoct == $mbmc){echo "green";}?>"><?= number_format($otoct); ?></td><?php $mboct = $otoct; ?>
                <td class="t"></td>
                <td class="t <?php if($otnov > $mbmc){echo "red";}elseif($otnov == $mbmc){echo "green";}?>"><?= number_format($otnov); ?></td><?php $mbnov = $otnov; ?>
                <td class="t"></td>
                <td class="t <?php if($otdec > $mbmc){echo "red";}elseif($otjan == $mbmc){echo "green";}?>"><?= number_format($otdec); ?></td><?php $mbdec = $otdec; ?>
                
                <td class="t"><b><?= number_format($otfrq); ?></b></td><?php $mbfrq = $otfrq; ?>
                <td class="t"><?= number_format($otfq+$otsq+$ottq+$otfrq); ?></td></td><?php $mbtotal = $otfq+$otsq+$ottq+$otfrq; ?>
            </tr>


            <tr>
                <td colspan="33" class="bar">II. MINOR REPAIR     ( <?= $abp->mr; ?>% )</td>
            </tr>
            <?php 
                $aip_by_pillar = $this->SGODModel->aip('sgod_aip',$school_id,$fy,$b_code,'MINOR REPAIR');
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
                
                <tr>
                    <td></td>
                    <td><?= $approw->materials; ?></td>
                    <td><?php if($approw->unit_price != ""){echo number_format($approw->unit_price);}  ?></td>
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
                    <td><?= $approw->qjan; ?></td>
                    <td><?php $mbmtjan = (double)$approw->jan*(double)$approw->qjan; echo number_format($mbmtjan); ?></td>
                    <td><?= $approw->qfeb; ?></td>
                    <td><?php $mbmtfeb = (double)$approw->feb*(double)$approw->qfeb; echo number_format($mbmtfeb); ?></td>
                    <td><?= $approw->qmar; ?></td>
                    <td><?php $mbmtmar = (double)$approw->mar*(double)$approw->qmar; echo number_format($mbmtmar); ?></td>

                    <td><?php $firstq = $mbmtjan+$mbmtfeb+$mbmtmar; echo number_format($firstq); ?></td>

                    <td><?= $approw->qapril; ?></td>
                    <td><?php $mbmtapril = (double)$approw->april*(double)$approw->qapril; echo number_format($mbmtapril); ?></td>
                    <td><?= $approw->qmay; ?></td>
                    <td><?php $mbmtmay = (double)$approw->may*(double)$approw->qmay; echo number_format($mbmtmay); ?></td>
                    <td><?= $approw->qjune; ?></td>
                    <td><?php $mbmtjune = (double)$approw->june*(double)$approw->qjune; echo number_format($mbmtjune); ?></td>

                    <td><?php $secondq = $mbmtapril+$mbmtmay+$mbmtjune; echo number_format($secondq); ?></td>

                    <td><?= $approw->qjuly; ?></td>
                    <td><?php $mbmtjuly = (double)$approw->july*(double)$approw->qjuly; echo number_format($mbmtjuly); ?></td>
                    <td><?= $approw->qaug; ?></td>
                    <td><?php $mbmtaug = (double)$approw->aug*(double)$approw->qaug; echo number_format($mbmtaug); ?></td>
                    <td><?= $approw->qsept; ?></td>
                    <td><?php $mbmtsept = (double)$approw->sept*(double)$approw->qsept; echo number_format($mbmtsept); ?></td>

                    <td><?php $threedq = $mbmtjuly+$mbmtaug+$mbmtsept; echo number_format($threedq); ?></td>

                    <td><?= $approw->qoct; ?></td>
                    <td><?php $mbmtoct = (double)$approw->oct*(double)$approw->qoct; echo number_format($mbmtoct); ?></td>
                    <td><?= $approw->qnov; ?></td>
                    <td><?php $mbmtnov = (double)$approw->nov*(double)$approw->qnov; echo number_format($mbmtnov); ?></td>
                    <td><?= $approw->qdec; ?></td>
                    <td><?php $mbmtdec = (double)$approw->ddec*(double)$approw->qdec; echo number_format($mbmtdec); ?></td>

                    <td><?php $fourthq = $mbmtoct+$mbmtnov+$mbmtdec; echo number_format($fourthq); ?></td>
                    <td><?php $yt = $firstq+$secondq+$threedq+$fourthq; echo number_format($yt); ?></td>
                    <?php 
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
                <td class="t <?php if($otjan > $mrmc){echo "red";}elseif($otjan == $mrmc){echo "green";}?>"><?= number_format($otjan); ?></td><?php $mrjan = $otjan; ?>
                <td class="t"></td>
                <td class="t <?php if($otfeb > $mrmc){echo "red";}elseif($otfeb == $mrmc){echo "green";}?>"><?= number_format($otfeb); ?></td><?php $mrfeb = $otfeb; ?>
                <td class="t"></td>
                <td class="t <?php if($otmar > $mrmc){echo "red";}elseif($otmar == $mrmc){echo "green";}?>"><?= number_format($otmar); ?></td><?php $mrmar = $otmar; ?>
                <td class="t"><?= number_format($otfq); ?></td><?php $mrfq = $otfq; ?>

                <td class="t"></td>
                <td class="t <?php if($otapril > $mrmc){echo "red";}elseif($otapril == $mrmc){echo "green";}?>"><?= number_format($otapril); ?></td><?php $mrapril = $otapril; ?>
                <td class="t"></td>
                <td class="t <?php if($otmay > $mrmc){echo "red";}elseif($otmay == $mrmc){echo "green";}?>"><?= number_format($otmay); ?></td><?php $mrmay = $otmay; ?>
                <td class="t"></td>
                <td class="t <?php if($otjune > $mrmc){echo "red";}elseif($otjune == $mrmc){echo "green";}?>"><?= number_format($otjune); ?></td><?php $mrjune = $otjune; ?>
                <td class="t"><?= number_format($otsq); ?></td><?php $mrsq = $otsq; ?>

                <td class="t"></td>
                <td class="t <?php if($otjuly > $mrmc){echo "red";}elseif($otjuly == $mrmc){echo "green";}?>"><?= number_format($otjuly); ?></td><?php $mrjuly = $otjuly; ?>
                <td class="t"></td>
                <td class="t <?php if($otaug > $mrmc){echo "red";}elseif($otaug == $mrmc){echo "green";}?>"><?= number_format($otaug); ?></td><?php $mraug = $otaug; ?>
                <td class="t"></td>
                <td class="t <?php if($otsept > $mrmc){echo "red";}elseif($otsept == $mrmc){echo "green";}?>"><?= number_format($otsept); ?></td><?php $mrsept = $otsept; ?>
                <td class="t"><?= number_format($ottq); ?></td><?php $mrtq = $ottq; ?>

                <td class="t"></td>
                <td class="t <?php if($otoct > $mrmc){echo "red";}elseif($otoct == $mrmc){echo "green";}?>"><?= number_format($otoct); ?></td><?php $mroct = $otoct; ?>
                <td class="t"></td>
                <td class="t <?php if($otnov > $mrmc){echo "red";}elseif($otnov == $mrmc){echo "green";}?>"><?= number_format($otnov); ?></td><?php $mrnov = $otnov; ?>
                <td class="t"></td>
                <td class="t <?php if($otdec > $mrmc){echo "red";}elseif($otdec == $mrmc){echo "green";}?>"><?= number_format($otdec); ?></td><?php $mrdec = $otdec; ?>

                <td class="t"><?= number_format($otfrq); ?></td><?php $mrfrq = $otfrq; ?>
                <td class="t"><?= number_format($otfq+$otsq+$ottq+$otfrq); ?></td></td><?php $mrtotal = $otfq+$otsq+$ottq+$otfrq; ?>
            </tr>

            <tr>
                <td colspan="33" class="bar">III. TEACHING-LEARNING INSTRUCTION   (<?= $abp->tli; ?>% )</td>
            </tr>
            <?php 
                $aip_by_pillar = $this->SGODModel->aip('sgod_aip',$school_id,$fy,$b_code,'TEACHING-LEARNING INSTRUCTION');
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
                
                
                <tr>
                    <td></td>
                    <td><?= $approw->materials; ?></td>
                    <td><?php if($approw->unit_price != ""){echo number_format($approw->unit_price);}  ?></td>
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
                    <td><?= $approw->qjan; ?></td>
                    <td><?php $mbmtjan = (double)$approw->jan*(double)$approw->qjan; echo number_format($mbmtjan); ?></td>
                    <td><?= $approw->qfeb; ?></td>
                    <td><?php $mbmtfeb = (double)$approw->feb*(double)$approw->qfeb; echo number_format($mbmtfeb); ?></td>
                    <td><?= $approw->qmar; ?></td>
                    <td><?php $mbmtmar = (double)$approw->mar*(double)$approw->qmar; echo number_format($mbmtmar); ?></td>

                    <td><?php $firstq = $mbmtjan+$mbmtfeb+$mbmtmar; echo number_format($firstq); ?></td>

                    <td><?= $approw->qapril; ?></td>
                    <td><?php $mbmtapril = (double)$approw->april*(double)$approw->qapril; echo number_format($mbmtapril); ?></td>
                    <td><?= $approw->qmay; ?></td>
                    <td><?php $mbmtmay = (double)$approw->may*(double)$approw->qmay; echo number_format($mbmtmay); ?></td>
                    <td><?= $approw->qjune; ?></td>
                    <td><?php $mbmtjune = (double)$approw->june*(double)$approw->qjune; echo number_format($mbmtjune); ?></td>

                    <td><?php $secondq = $mbmtapril+$mbmtmay+$mbmtjune; echo number_format($secondq); ?></td>

                    <td><?= $approw->qjuly; ?></td>
                    <td><?php $mbmtjuly = (double)$approw->july*(double)$approw->qjuly; echo number_format($mbmtjuly); ?></td>
                    <td><?= $approw->qaug; ?></td>
                    <td><?php $mbmtaug = (double)$approw->aug*(double)$approw->qaug; echo number_format($mbmtaug); ?></td>
                    <td><?= $approw->qsept; ?></td>
                    <td><?php $mbmtsept = (double)$approw->sept*(double)$approw->qsept; echo number_format($mbmtsept); ?></td>

                    <td><?php $threedq = $mbmtjuly+$mbmtaug+$mbmtsept; echo number_format($threedq); ?></td>

                    <td><?= $approw->qoct; ?></td>
                    <td><?php $mbmtoct = (double)$approw->oct*(double)$approw->qoct; echo number_format($mbmtoct); ?></td>
                    <td><?= $approw->qnov; ?></td>
                    <td><?php $mbmtnov = (double)$approw->nov*(double)$approw->qnov; echo number_format($mbmtnov); ?></td>
                    <td><?= $approw->qdec; ?></td>
                    <td><?php $mbmtdec = (double)$approw->ddec*(double)$approw->qdec; echo number_format($mbmtdec); ?></td>

                    <td><?php $fourthq = $mbmtoct+$mbmtnov+$mbmtdec; echo number_format($fourthq); ?></td>
                    <td><?php $yt = $firstq+$secondq+$threedq+$fourthq; echo number_format($yt); ?></td>
                    <?php 
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
        
           
            <?php
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
                <td class="t <?php if($otjan > $tlimc){echo "red";}elseif($otjan == $tlimc){echo "green";}?>"><?= number_format($otjan); ?></td><?php $tijan = $otjan; ?>
                <td class="t"></td>
                <td class="t <?php if($otfeb > $tlimc){echo "red";}elseif($otfeb == $tlimc){echo "green";}?>"><?= number_format($otfeb); ?></td><?php $tifeb = $otfeb; ?>
                <td class="t"></td>
                <td class="t <?php if($otmar > $tlimc){echo "red";}elseif($otmar == $tlimc){echo "green";}?>"><?= number_format($otmar); ?></td><?php $timar = $otmar; ?>
                <td class="t"><?= number_format($otfq); ?></td><?php $tifq = $otfq; ?>
                
                <td class="t"></td>
                <td class="t <?php if($otapril > $tlimc){echo "red";}elseif($otapril == $tlimc){echo "green";}?>"><?= number_format($otapril); ?></td><?php $tiapril = $otapril; ?>
                <td class="t"></td>
                <td class="t <?php if($otmay > $tlimc){echo "red";}elseif($otmay == $tlimc){echo "green";}?>"><?= number_format($otmay); ?></td><?php $timay = $otmay; ?>
                <td class="t"></td>
                <td class="t <?php if($otjune > $tlimc){echo "red";}elseif($otjune == $tlimc){echo "green";}?>"><?= number_format($otjune); ?></td><?php $tijune = $otjune; ?>
                <td class="t"><?= number_format($otsq); ?></td><?php $tisq = $otsq; ?>
                
                <td class="t"></td>
                <td class="t <?php if($otjuly > $tlimc){echo "red";}elseif($otjuly == $tlimc){echo "green";}?>"><?= number_format($otjuly); ?></td><?php $tijuly = $otjuly; ?>
                <td class="t"></td>
                <td class="t <?php if($otaug > $tlimc){echo "red";}elseif($otaug == $tlimc){echo "green";}?>"><?= number_format($otaug); ?></td><?php $tiaug = $otaug; ?>
                <td class="t"></td>
                <td class="t <?php if($otsept > $tlimc){echo "red";}elseif($otsept == $tlimc){echo "green";}?>"><?= number_format($otsept); ?></td><?php $tisept = $otsept; ?>
                <td class="t"><?= number_format($ottq); ?></td><?php $titq = $ottq; ?>
                
                <td class="t"></td>
                <td class="t <?php if($otoct > $tlimc){echo "red";}elseif($otoct == $tlimc){echo "green";}?>"><?= number_format($otoct); ?></td><?php $tioct = $otoct; ?>
                <td class="t"></td>
                <td class="t <?php if($otnov > $tlimc){echo "red";}elseif($otnov == $tlimc){echo "green";}?>"><?= number_format($otnov); ?></td><?php $tinov = $otnov; ?>
                <td class="t"></td>
                <td class="t <?php if($otdec > $tlimc){echo "red";}elseif($otdec == $tlimc){echo "green";}?>"><?= number_format($otdec); ?></td><?php $tidec = $otdec; ?>
                <td class="t"><?= number_format($otfrq); ?></td><?php $tifrq = $otfrq; ?>
                
                <td class="t"><?= number_format($otfq+$otsq+$ottq+$otfrq); ?></td></td><?php $titotal = $otfq+$otsq+$ottq+$otfrq; ?>
                
            </tr>

            <tr>
                <td colspan="33" class="bar">IV. Attendance to & Conduct of Trainings/Seminars/Conferences (<?= $abp->tst; ?>%)  </td>
            </tr>
            <?php 
                $aip_by_pillar = $this->SGODModel->aip('sgod_aip',$school_id,$fy,$b_code,'TRAININGS/SEMINAR/TRAVEL');
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
                
                <tr>
                    <td></td>
                    <td><?= $approw->materials; ?></td>
                    <td><?php if($approw->unit_price != ""){echo number_format($approw->unit_price);}  ?></td>
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
                    <td><?= $approw->qjan; ?></td>
                    <td><?php $mbmtjan = (double)$approw->jan*(double)$approw->qjan; echo number_format($mbmtjan); ?></td>
                    <td><?= $approw->qfeb; ?></td>
                    <td><?php $mbmtfeb = (double)$approw->feb*(double)$approw->qfeb; echo number_format($mbmtfeb); ?></td>
                    <td><?= $approw->qmar; ?></td>
                    <td><?php $mbmtmar = (double)$approw->mar*(double)$approw->qmar; echo number_format($mbmtmar); ?></td>

                    <td><?php $firstq = $mbmtjan+$mbmtfeb+$mbmtmar; echo number_format($firstq); ?></td>

                    <td><?= $approw->qapril; ?></td>
                    <td><?php $mbmtapril = (double)$approw->april*(double)$approw->qapril; echo number_format($mbmtapril); ?></td>
                    <td><?= $approw->qmay; ?></td>
                    <td><?php $mbmtmay = (double)$approw->may*(double)$approw->qmay; echo number_format($mbmtmay); ?></td>
                    <td><?= $approw->qjune; ?></td>
                    <td><?php $mbmtjune = (double)$approw->june*(double)$approw->qjune; echo number_format($mbmtjune); ?></td>

                    <td><?php $secondq = $mbmtapril+$mbmtmay+$mbmtjune; echo number_format($secondq); ?></td>

                    <td><?= $approw->qjuly; ?></td>
                    <td><?php $mbmtjuly = (double)$approw->july*(double)$approw->qjuly; echo number_format($mbmtjuly); ?></td>
                    <td><?= $approw->qaug; ?></td>
                    <td><?php $mbmtaug = (double)$approw->aug*(double)$approw->qaug; echo number_format($mbmtaug); ?></td>
                    <td><?= $approw->qsept; ?></td>
                    <td><?php $mbmtsept = (double)$approw->sept*(double)$approw->qsept; echo number_format($mbmtsept); ?></td>

                    <td><?php $threedq = $mbmtjuly+$mbmtaug+$mbmtsept; echo number_format($threedq); ?></td>

                    <td><?= $approw->qoct; ?></td>
                    <td><?php $mbmtoct = (double)$approw->oct*(double)$approw->qoct; echo number_format($mbmtoct); ?></td>
                    <td><?= $approw->qnov; ?></td>
                    <td><?php $mbmtnov = (double)$approw->nov*(double)$approw->qnov; echo number_format($mbmtnov); ?></td>
                    <td><?= $approw->qdec; ?></td>
                    <td><?php $mbmtdec = (double)$approw->ddec*(double)$approw->qdec; echo number_format($mbmtdec); ?></td>

                    <td><?php $fourthq = $mbmtoct+$mbmtnov+$mbmtdec; echo number_format($fourthq); ?></td>
                    <td><?php $yt = $firstq+$secondq+$threedq+$fourthq; echo number_format($yt); ?></td>
                    <?php 
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
                <td class="t <?php if($otjan > $tstmc){echo "red";}elseif($otjan == $tstmc){echo "green";}?>"><?= number_format($otjan); ?></td><?php $majan = $otjan; ?>
                <td class="t"></td>
                <td class="t <?php if($otfeb > $tstmc){echo "red";}elseif($otfeb == $tstmc){echo "green";}?>"><?= number_format($otfeb); ?></td><?php $mafeb = $otfeb; ?>
                <td class="t"></td>
                <td class="t <?php if($otmar > $tstmc){echo "red";}elseif($otmar == $tstmc){echo "green";}?>"><?= number_format($otmar); ?></td><?php $mamar = $otmar; ?>
                <td class="t"><?= number_format($otfq); ?></td><?php $mafq = $otfq; ?>
                
                <td class="t"></td>
                <td class="t <?php if($otapril > $tstmc){echo "red";}elseif($otapril == $tstmc){echo "green";}?>"><?= number_format($otapril); ?></td><?php $maapril = $otapril; ?>
                <td class="t"></td>
                <td class="t <?php if($otmay > $tstmc){echo "red";}elseif($otmay == $tstmc){echo "green";}?>"><?= number_format($otmay); ?></td><?php $mamay = $otmay; ?>
                <td class="t"></td>
                <td class="t <?php if($otjune > $tstmc){echo "red";}elseif($otjune == $tstmc){echo "green";}?>"><?= number_format($otjune); ?></td><?php $majune = $otjune; ?>
                <td class="t"><?= number_format($otsq); ?></td><?php $masq = $otsq; ?>
                
                <td class="t"></td>
                <td class="t <?php if($otjuly > $tstmc){echo "red";}elseif($otjuly == $tstmc){echo "green";}?>"><?= number_format($otjuly); ?></td><?php $majuly = $otjuly; ?>
                <td class="t"></td>
                <td class="t <?php if($otaug > $tstmc){echo "red";}elseif($otaug == $tstmc){echo "green";}?>"><?= number_format($otaug); ?></td><?php $maaug = $otaug; ?>
                <td class="t"></td>
                <td class="t <?php if($otsept > $tstmc){echo "red";}elseif($otsept == $tstmc){echo "green";}?>"><?= number_format($otsept); ?></td><?php $masept = $otsept; ?>
                <td class="t"><?= number_format($ottq); ?></td><?php $matq = $ottq; ?>
                
                <td class="t"></td>
                <td class="t <?php if($otoct > $tstmc){echo "red";}elseif($otoct == $tstmc){echo "green";}?>"><?= number_format($otoct); ?></td><?php $maoct = $otoct; ?>
                <td class="t"></td>
                <td class="t <?php if($otnov > $tstmc){echo "red";}elseif($otnov == $tstmc){echo "green";}?>"><?= number_format($otnov); ?></td><?php $manov = $otnov; ?>
                <td class="t"></td>
                <td class="t <?php if($otdec > $tstmc){echo "red";}elseif($otdec == $tstmc){echo "green";}?>"><?= number_format($otdec); ?></td><?php $madec = $otdec; ?>
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
                <td class="ot" style="text-align:left; font-weight:bold" colspan="3">Monthly Cash Allocation</td>
                <td class="ot"></td>
                <td class="ot"><a class="a <?php if($ojan > $monthly){echo "red";}elseif($ojan == $monthly){echo "g";}?>" href="generate_rcav2/jan" target="_blank"><?= number_format($ojan); ?></a></td>
                <td class="ot"></td>
                <td class="ot"><a class="a <?php if($ofeb > $monthly){echo "red";}elseif($ofeb == $monthly){echo "g";}?>" href="generate_rcav2/feb" target="_blank"><?= number_format($ofeb); ?></a></td>
                <td class="ot"></td>
                <td class="ot"><a class="a <?php if($omay > $monthly){echo "red";}elseif($omay == $monthly){echo "g";}?>" href="generate_rcav2/mar" target="_blank"><?= number_format($omar); ?></a></td>
                <td class="ot "><b><?= number_format($mafq+$tifq+$mrfq+$mbfq); ?></b></td>
                
                <td class="ot"></td>
                <td class="ot"><a class="a <?php if($oapril > $monthly){echo "red";}elseif($oapril == $monthly){echo "g";}?>" href="generate_rcav2/april" target="_blank"><?= number_format($oapril); ?></a></td>
                <td class="ot"></td>
                <td class="ot"><a class="a <?php if($omay > $monthly){echo "red";}elseif($omay == $monthly){echo "g";}?>" href="generate_rcav2/may" target="_blank"><?= number_format($omay); ?></a></td>
                <td class="ot"></td>
                <td class="ot"><a class="a <?php if($ojune > $monthly){echo "red";}elseif($ojune == $monthly){echo "g";}?>" href="generate_rcav2/june" target="_blank"><?= number_format($ojune); ?></a></td>
                <td class="ot"><b><?= number_format($masq+$tisq+$mrsq+$mbsq); ?></b></td>
                
                <td class="ot"></td>
                <td class="ot"><a class="a <?php if($ojuly > $monthly){echo "red";}elseif($ojuly == $monthly){echo "g";}?>" href="generate_rcav2/july" target="_blank"><?= number_format($ojuly); ?></a></td>
                <td class="ot"></td>
                <td class="ot"><a class="a <?php if($oaug > $monthly){echo "red";}elseif($oaug == $monthly){echo "g";}?>" href="generate_rcav2/aug" target="_blank"><?= number_format($oaug); ?></a></td>
                <td class="ot"></td>
                <td class="ot"><a class="a <?php if($osept > $monthly){echo "red";}elseif($osept == $monthly){echo "g";}?>" href="generate_rcav2/sept" target="_blank"><?= number_format($osept); ?></a></td>
                <td class="ot"><b><?= number_format($matq+$titq+$mrtq+$mbtq); ?></b></td>
                
                <td class="ot"></td>
                <td class="ot"><a class="a <?php if($ooct > $monthly){echo "red";}elseif($ooct == $monthly){echo "g";}?>" href="generate_rcav2/oct" target="_blank"><?= number_format($ooct); ?></a></td>
                <td class="ot"></td>
                <td class="ot"><a class="a <?php if($onov > $monthly){echo "red";}elseif($onov == $monthly){echo "g";}?>" href="generate_rcav2/nov" target="_blank"><?= number_format($onov); ?></a></td>
                <td class="ot"></td>
                <td class="ot"><a class="a <?php if($odec > $monthly){echo "red";}elseif($odec == $monthly){echo "g";}?>" href="generate_rcav2/dec" target="_blank"><?= number_format($odec); ?></a></td>
                <td class="ot"><b><?= number_format($mafrq+$tifrq+$mrfrq+$mbfrq); ?></b></td>
                <td class="ot"><?= number_format($matotal+$titotal+$mrtotal+$mbtotal); ?></td>
            </tr>


        </tbody> 
    </table>








    </body>
    </html>

    