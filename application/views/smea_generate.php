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
            /* ===== Typography & readability ===== */
            .aip_generate{
                font-family:'Segoe UI', Calibri, 'Helvetica Neue', Arial, sans-serif;
                color:#152731;               /* near-black slate: ~13:1 on white */
                -webkit-font-smoothing:antialiased;
                text-rendering:optimizeLegibility;
            }
            .aip_generate h1{
                font-size:21px;
                font-weight:800;
                letter-spacing:.3px;
                color:#0c4446;               /* deep teal, ~9:1 on white */
                line-height:1.35;
            }
            .aip_generate h3{
                font-size:15px;
                font-weight:700;
                color:#0b5b5e;               /* deep teal, ~6.7:1 on white (was #2ea6a9 ≈ 2.7:1) */
                margin-bottom:4px;
            }
            .aip_generate ul{
                font-size:13px;
                margin-bottom:6px;
            }
            .aip_generate ul li{
                background:#e2f1f1;
                border-left:4px solid #0f6d70;
                color:#123c3d;
                font-weight:700;
                display:inline-block;
                padding:5px 12px;
                border-radius:3px;
            }

            /* ===== Responsive, fit-to-width table ===== */
            .aip_generate table{
                width:100%;
                table-layout:fixed;         /* share width across columns so it never overflows sideways */
                border-collapse:collapse;
                font-size:11px;
            }
            .aip_generate table th,
            .aip_generate table td{
                border:1px solid #8fa6b0;    /* darker grid lines for clearer cell separation */
                padding:5px 4px;
                word-wrap:break-word;
                overflow-wrap:break-word;
                hyphens:auto;
                vertical-align:middle;
            }
            .aip_generate table td{
                background-color:#ffffff;
                color:#152731;
                line-height:1.35;
            }
            .aip_generate table th{
                background-color:#0f6d70;    /* deep teal: white text now ~6.1:1 (was #2ea6a9 ≈ 2.7:1) */
                color:#ffffff;
                font-weight:700;
                font-size:10.5px;
                line-height:1.3;
                text-transform:uppercase;
                letter-spacing:.3px;
            }
            /* Zebra striping + hover for easier row tracking across the wide table */
            .aip_generate tbody tr:nth-child(even) td{
                background-color:#eaf2f3;
            }
            .aip_generate tbody tr:hover td{
                background-color:#d7ebec;
            }

            /* ===== Clickable (highlighted) cells only ===== */
            .ivan{
                background-color:#ffe598 !important;
                color:#000 !important;
            }
            .ivy{
                background-color:#2ec4c7 !important;   /* bright teal action chip: dark text ~7:1 */
                color:#04302e !important;
                font-weight:700;
            }
            .ivy.smea-clickable{
                cursor:pointer;
            }
            .ivy.smea-clickable:hover,
            .aip_generate tbody tr:hover td.ivy{
                background-color:#4ad9dc !important;    /* brighter on hover */
                color:#04302e !important;
            }
            .ivy a,
            .smea-clickable a{
                color:#04302e;
                text-decoration:underline;
                font-weight:700;
            }
            /* Small edit hint shown inside the highlighted (clickable) cell */
            .smea-edit-hint{
                font-size:10px;
                margin-left:3px;
                opacity:.85;
            }

            @media (max-width:900px){
                .aip_generate table{ font-size:9px; }
                .aip_generate table th{ font-size:8px; }
                .aip_generate table th,
                .aip_generate table td{ padding:3px 2px; }
            }

            /* ===== Print: Folio (F4) landscape, fit all columns across the width ===== */
            @page{
                size:330mm 210mm;   /* Folio / F4 in landscape */
                margin:8mm;
            }
            @media print{
                html, body{ width:330mm; }
                .aip_generate{ padding:0; }
                .aip_generate table{ font-size:9px; }
                .aip_generate table th{ font-size:8px; }
                .aip_generate table th,
                .aip_generate table td{ padding:3px 3px; }
                .aip_generate table{ page-break-inside:auto; }
                .aip_generate tr{ page-break-inside:avoid; }
                /* No background colours when printing — plain white cells, black text, borders kept for structure */
                .aip_generate table th,
                .aip_generate table td,
                .aip_generate tbody tr:nth-child(even) td,
                .aip_generate tbody tr:hover td,
                .ivy,
                .ivy.smea-clickable:hover,
                .aip_generate tbody tr:hover td.ivy{
                    background:transparent !important;
                    background-color:transparent !important;
                    color:#000 !important;
                }
                .ivy a, .smea-clickable a{ color:#000 !important; }
                .smea-legend, .smea-edit-hint, .back-to-top, .smea-modal-overlay{ display:none !important; }
            }

            /* --- Legend --- */
            .smea-legend{
                display:inline-flex;
                align-items:center;
                gap:8px;
                border:1px solid #0f6d70;
                background:#eef7f7;
                border-radius:5px;
                padding:6px 12px;
                margin:0 auto 12px;
                font-size:13px;
                font-weight:600;
                color:#123c3d;
            }
            .smea-legend .swatch{
                display:inline-block;
                width:16px;
                height:16px;
                background:#2ec4c7;           /* matches the actual clickable cell colour */
                border:1px solid #0f6d70;
                border-radius:3px;
            }

            /* --- Modal --- */
            .smea-modal-overlay{
                display:none;
                position:fixed;
                inset:0;
                background:rgba(0,0,0,.5);
                z-index:1050;
                align-items:flex-start;
                justify-content:center;
            }
            .smea-modal-overlay.show{ display:flex; }
            .smea-modal{
                background:#fff;
                width:460px;
                max-width:92%;
                margin-top:8vh;
                border-radius:8px;
                box-shadow:0 10px 40px rgba(0,0,0,.3);
                overflow:hidden;
                font-family:inherit;
            }
            .smea-modal-header{
                display:flex;
                align-items:center;
                justify-content:space-between;
                background:#0f6d70;
                color:#fff;
                padding:12px 16px;
                font-size:16px;
                font-weight:600;
            }
            .smea-modal-header .smea-modal-close{
                background:none;
                border:none;
                color:#fff;
                font-size:22px;
                line-height:1;
                cursor:pointer;
            }
            .smea-modal-body{ padding:18px 16px; }
            .smea-modal-sub{ margin:0 0 14px; font-size:13px; color:#3f4c54; }
            .smea-field{ margin-bottom:12px; }
            .smea-field label{
                display:block;
                font-size:12px;
                font-weight:700;
                margin-bottom:4px;
                color:#1e2a31;
            }
            .smea-req{ color:#e74c3c; }
            .smea-input{
                width:100%;
                padding:8px 10px;
                border:1px solid #ccc;
                border-radius:4px;
                font-size:14px;
                box-sizing:border-box;
            }
            .smea-input:disabled{ background:#f1f1f1; color:#666; }
            .smea-actions{
                display:flex;
                gap:8px;
                margin-top:6px;
            }
            .smea-btn-save,.smea-btn-cancel{
                border:none;
                border-radius:4px;
                padding:9px 16px;
                font-size:14px;
                cursor:pointer;
            }
            .smea-btn-save{ background:#0f6d70; color:#fff; font-weight:600; }
            .smea-btn-save:hover{ background:#0b5b5e; color:#fff; }
            .smea-btn-cancel{ background:#e0e0e0; color:#222; }
            .smea-loading{ text-align:center; color:#777; padding:20px 0; }

            .back-to-top {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background-color: #0f6d70;
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
            display: none; /* Initially hidden */
            }

            /* Style when the button is visible */
            .back-to-top.show {
            display: block;
            }

            /* Smooth scrolling behavior */
            html {
            scroll-behavior: smooth;
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

    <?php if(!isset($q)){
        $q=$_SESSION['q'];
    }?>


    <h1>SCHOOL MONITORING, EVALUATION AND ADJUSTMENT<br />FY <?= $fy; ?></h1>
    <h3>Quarter <?= $q; ?></h3>

    <div style="text-align:center;">
        <span class="smea-legend">
            <span class="swatch"></span>
            <span>Only the highlighted cells are clickable &mdash; click one to update its Quarter <?= $q; ?> accomplishment.</span>
        </span>
    </div>

    


    <?php foreach($data as $row){ 
         $io = $this->SGODModel->one_cond_row('sgod_setting_io', 'id', $row->io);
        ?>
    <ul>
        <li>PILLAR : <?= ucfirst($row->pillar); ?></li>
    </ul>

   

    <table >
            <colgroup>
                <col span="2" style="width:11%">
                <col span="19" style="width:4.1%">
            </colgroup>
            <tr>
                <th rowspan="3">SCHOOL IMPROVEMENT PROJECT TITLE</th>
                <th rowspan="3">PERFORMANCE INDICATORS</th>
                <th colspan="7">PHYSICAL ACCOMPLISHMENTS</th>
                <th colspan="7">FINANCIAL ACCOMPLISHMENTS (MOOE)</th>
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

                <th rowspan="2">Funds Allocated<br />for Quarter</th>
                <th rowspan="2">Funds Utilized</th>
                <th rowspan="2">% of <br />Utilization(utilization/<br />allocation x100)</th>
                <th colspan="2">Gap on Allocation versus Utilized(Difference between allocated amount and utilized amount)</th>
                <th rowspan="2">Remarks</th>

                <!-- end of financial accomplishment -->

                <th rowspan="2">Funds Allocated<br />for Quarter</th>
                <th rowspan="2">Funds Utilized</th>
                <th rowspan="2">% of <br />Utilization(utilization/<br />allocation x100)</th>
                <th colspan="2">Gap on Allocation versus Utilized(Difference between allocated amount and utilized amount)</th>
                <th rowspan="2">Remarks</th>

            </tr>
            <tr>
                <th>Amount</th>
                <th>%</th>

                <th>Amount</th>
                <th>%</th>
            </tr>   
        <tbody>
            <?php 
            $pia = $this->Common->four_cond('sgod_aip',"school_id", $row->school_id,"fy", $row->fy,"b_code", $row->b_code,"pillar", $row->pillar);
            //$pia = $this->SmeaModel->smea_by_pillar($row->school_id,$row->fy,$row->b_code,$row->pillar);
            $sp = null; 
            foreach($pia as $row){ 
                $pt = $this->SGODModel->two_cond_row('sgod_sop', 'aip_id',$row->id,'type',1);
                $ft = $this->SGODModel->two_cond_row('sgod_sop', 'aip_id',$row->id,'type',2);
                $fto = $this->SGODModel->two_cond_row('sgod_sop', 'aip_id',$row->id,'type',3);

                $field = 'q' . $q; // example: q1, q2, etc.

                $pt_val = isset($pt->$field) ? $pt->$field : null;
                $ft_val = isset($ft->$field) ? $ft->$field : null;
                $fto_val = isset($fto->$field) ? $fto->$field : null;

                // If all three values are null, '', or 0, skip the row
                if (
                    ($pt_val === null || $pt_val === '' || $pt_val == 0) &&
                    ($ft_val === null || $ft_val === '' || $ft_val == 0) &&
                    ($fto_val === null || $fto_val === '' || $fto_val == 0)
                ) {
                    continue;
                }
            ?>
            <tr>
            <?php if($sp !== $row->sip_project) { ?>

                    <td style="text-align:left"><?= $row->sip_project; ?></td>
                    <td style="text-align:left"><?= $row->pi; ?></td>
                    <?php }else{ ?>
                        <td></td>
                        <td></td>
                        <td><?= $row->pi; ?></td>
                        
                    <?php } ?>

                <?php 
                  
                  $pt_smea = 'smea_q'.$q;
                  $pt_sop = 'q'.$q;
                  if(!empty($pt)){

                    if($pt->$pt_sop != 0){$ptt = $pt->$pt_sop;}else{$ptt = 0;}
                    if($pt->$pt_smea != 0){$smea = $pt->$pt_smea;}else{$smea = 0;}
                        
                    $gain = (int)$smea-(int)$ptt;
                ?>
                <td class="ivy smea-clickable" data-url="<?= base_url(); ?>Page/smea_edit_modal/<?= $pt->id; ?>/<?= $q; ?>" data-q="<?= $q; ?>" data-span="7" title="Click to update Quarter <?= $q; ?> accomplishment"><a href="<?= base_url(); ?>Page/smea_edit/<?= $pt->id; ?>/<?= $q; ?>"><?= $pt->$pt_sop; ?></a><span class="smea-edit-hint">&#9998;</span></td>
                <td><?php

                    if (strpos($pt->$pt_sop, '%') !== false) {
                        echo $pt->$pt_smea . "%";
                    } else {
                        if($pt->$pt_smea != 0){if($pt->$pt_smea > $pt->$pt_sop){echo $pt->$pt_sop;}else{echo $pt->$pt_smea;}}
                    }



                ?></td>
                <td>
                    <?php 

                        if (strpos($pt->$pt_sop, '%') !== false) {
                            echo $pt->$pt_smea . "%";
                        } else {
                            if ($smea != 0 && $ptt != 0) { 
                                if($pt->$pt_smea < $pt->$pt_sop){
                                    echo (int)(($smea / $ptt) * 100) . "%";
                                }else{
                                    echo (int)(($ptt / $ptt) * 100) . "%";
                            }
                        }
                        
                        }
                    ?>
                </td>
                <td><?php 

                    if (strpos($pt->$pt_sop, '%') !== false) {
                        if($gain >= 0){echo abs($gain) . "%";}
                    } else {
                        if ($gain != 0 && $ptt != 0) { 
                            if($gain >= 0){echo $gain;} 
                        }
                    } 
                ?>
                </td>
                <td>
                    <?php 
                        if (strpos($pt->$pt_sop, '%') !== false) {
                            if($gain >= 0){echo abs($gain) . "%";}
                        } else {
                            if ($gain != 0 && $ptt != 0) { 
                                if($gain >= 0){echo (int)(($gain / $ptt) * 100) . "%";}
                            }
                        } 
                        
                    ?>
                </td>
                <td> <?php  
                    if (strpos($pt->$pt_sop, '%') !== false) {
                        if($gain <= 0){echo abs($gain) . "%";}
                    } else {
                        if($gain <= 0){echo abs($gain);}
                    } 
                ?> </td>
                <td>
                    <?php 
                        if (strpos($pt->$pt_sop, '%') !== false) {
                            if($gain <= 0){echo abs($gain) . "%";}
                        } else {
                            if ($gain != 0 && $ptt != 0) { 
                                if($gain <= 0){echo abs((int)(($gain / $ptt) * 100)) . "%";}
                            }
                        }
                        
                    ?>
                </td>
                <?php }else{?>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                <?php } ?>

                <?php 
                  
                  $ft_smea = 'smea_q'.$q;
                  $ft_sop = 'q'.$q;
                  $ft_remarks = 'remarks_q'.$q;
                  if(!empty($ft)){
                    if($ft->$ft_sop != 0){$fptt = $ft->$ft_sop;}else{$fptt = 0;}
                    if($ft->$ft_smea != 0){$fsmea = $ft->$ft_smea;}else{$fsmea = 0;}

                    //$fgain = ((int)$fsmea/(int)$fptt)*100;

                ?>
                <td class="ivy smea-clickable" data-url="<?= base_url(); ?>Page/smea_edit_modal/<?= $ft->id; ?>/<?= $q; ?>" data-q="<?= $q; ?>" data-span="6" title="Click to update Quarter <?= $q; ?> accomplishment"><a href="<?= base_url(); ?>Page/smea_edit/<?= $ft->id; ?>/<?= $q; ?>"><?php echo $ft->$ft_sop;  ?></a><span class="smea-edit-hint">&#9998;</span></td>
                <td><?php if($ft->$ft_smea != 0){echo number_format($ft->$ft_smea, 2);}  ?></td>
                <td>
                    <?php
                        if ($fsmea != 0 && $fptt != 0) {
                            if($fsmea >= 0){echo abs((int)(($fsmea / $fptt) * 100)) . "%";}
                        }
                    ?>
                </td>
                <td>
                <?php
                        if ($fsmea != 0 && $fptt != 0) {
                            if($fsmea >= 0){echo number_format((int)($fptt - $fsmea), 2);}
                        }
                    ?>
                </td>
                <td>
                <?php
                        if ($fsmea != 0 && $fptt != 0) {
                            if($fsmea >= 0){$fgain = abs((int)($fsmea - $fptt));}
                            if($fsmea >= 0){echo number_format(abs((int)(($fgain / $fptt) * 100)), 2) . "%";}
                        }
                    ?>
                </td>
                <td><?= $ft->$ft_remarks; ?></td>
                <?php }else{?>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                <?php } ?>


                <?php 
                  
                  $fto_smea = 'smea_q'.$q;
                  $fto_sop = 'q'.$q;
                  $fto_remarks = 'remarks_q'.$q;
                  if(!empty($fto)){
                    if($fto->$fto_sop != 0){$fptto = $fto->$fto_sop;}else{$fptto = 0;}
                    if($fto->$fto_smea != 0){$fsmeao = $fto->$fto_smea;}else{$fsmeao = 0;}

                    //$fgain = ((int)$fsmea/(int)$fptt)*100;

                ?>
                <td class="ivy smea-clickable" data-url="<?= base_url(); ?>Page/smea_edit_modal/<?= $fto->id; ?>/<?= $q; ?>" data-q="<?= $q; ?>" data-span="6" title="Click to update Quarter <?= $q; ?> accomplishment"><a href="<?= base_url(); ?>Page/smea_edit/<?= $fto->id; ?>/<?= $q; ?>"><?php if($fto->$fto_sop){echo number_format($fto->$fto_sop);}  ?></a><span class="smea-edit-hint">&#9998;</span></td>
                <td><?php if($fto->$fto_smea != 0){echo number_format($fto->$fto_smea, 2);}  ?></td>
                <td>
                    <?php 
                        if ($fsmeao != 0 && $fptto != 0) { 
                            if($fsmeao >= 0){echo abs((int)(($fsmeao / $fptto) * 100)) . "%";}
                        }
                    ?>
                </td>
                <td>
                <?php 
                        if ($fsmeao != 0 && $fptto != 0) { 
                            if($fsmeao >= 0){echo number_format((int)($fptto - $fsmeao), 2);}
                        }
                    ?>
                </td>
                <td>
                    <?php
                        if ($fsmeao != 0 && $fptto != 0 && $fptt != 0) {
                            $fgain = abs((int)($fsmeao - $fptto));
                            echo number_format(abs(($fgain / $fptt) * 100), 2) . "%";
                        }
                    ?>
                </td>
                <td><?= $fto->$fto_remarks?></td>
                <?php }else{?>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                <?php } ?>

            </tr>
            <?php }  ?>
           
        </tbody> 
    </table>
    <?php } ?>
                                    <?php 
                                      $val = array(
                                        "1" => "ACCESS",
                                        "2" => "EQUITY",
                                        "3" => "QUALITY",
                                        "4" => "RESILIENCY AND WELL-BEING",
                                        "5" => "ENABLING MECHANISM",
                                        "6" => "RESILIENCY",

                                      );
                                      if(!empty($adjustment)){
                                    ?>

                                        <h1 id="adjustment">SOP ADJUSTMENT</h1>
                                    <?php } ?>

    <?php foreach($adjustment as $row){ 
         //$io = $this->SGODModel->one_cond_row('sgod_setting_io', 'id', $row->io);
        ?>
    <ul>
        <li>PILLAR : <?= $val[$row->pillar]; ?></li>
    </ul>

   

    <table >
            <colgroup>
                <col span="2" style="width:11%">
                <col span="19" style="width:4.1%">
            </colgroup>
            <tr>
                <th rowspan="3">SCHOOL IMPROVEMENT PROJECT TITLE</th>
                <th rowspan="3">PERFORMANCE INDICATORS</th>
                <th colspan="7">PHYSICAL ACCOMPLISHMENTS</th>
                <th colspan="7">FINANCIAL ACCOMPLISHMENTS (MOOE)</th>
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

                <th rowspan="2">Funds Allocated<br />for Quarter</th>
                <th rowspan="2">Funds Utilized</th>
                <th rowspan="2">% of <br />Utilization(utilization/<br />allocation x100)</th>
                <th colspan="2">Gap on Allocation versus Utilized(Difference between allocated amount and utilized amount)</th>
                <th rowspan="2">Remarks</th>

                <!-- end of financial accomplishment -->

                <th rowspan="2">Funds Allocated<br />for Quarter</th>
                <th rowspan="2">Funds Utilized</th>
                <th rowspan="2">% of <br />Utilization(utilization/<br />allocation x100)</th>
                <th colspan="2">Gap on Allocation versus Utilized(Difference between allocated amount and utilized amount)</th>
                <th rowspan="2">Remarks</th>

            </tr>
            <tr>
                <th>Amount</th>
                <th>%</th>

                <th>Amount</th>
                <th>%</th>
            </tr>   
        <tbody>
            <?php 
            $pia = $this->Common->four_cond('sgod_smea_adjustment',"school_id", $row->school_id,"fy", $row->fy,"b_code", $row->b_code,"pillar", $row->pillar);
            $sp = null; 
            foreach($pia as $row){ ?>
            <tr>
            <?php if($sp !== $row->sip) { ?>

                    <td style="text-align:left"><?= $row->sip; ?></td>
                    <td style="text-align:left"><?= $row->pi; ?></td>
                    <?php }else{ ?>
                        <td></td>
                        <td></td>
                        <td><?= $row->pi; ?></td>
                        
                    <?php } ?>

                <?php 
                  $pt = $this->SGODModel->two_cond_row('sgod_sop_adjustment', 'aip_id',$row->id,'type',1);
                  $pt_smea = 'smea_q'.$q;
                  $pt_sop = 'q'.$q;
                  if(!empty($pt)){

                    if($pt->$pt_sop != 0){$ptt = $pt->$pt_sop;}else{$ptt = 0;}
                    if($pt->$pt_smea != 0){$smea = $pt->$pt_smea;}else{$smea = 0;}
                        
                    $gain = (int)$smea-(int)$ptt;
                ?>
                <td class="ivy smea-clickable" data-url="<?= base_url(); ?>Page/smea_ad_edit_modal/<?= $pt->id; ?>/<?= $q; ?>" data-q="<?= $q; ?>" data-span="7" title="Click to update Quarter <?= $q; ?> accomplishment"><a href="<?= base_url(); ?>Page/smea_ad_edit/<?= $pt->id; ?>/<?= $q; ?>"><?= $pt->$pt_sop; ?></a><span class="smea-edit-hint">&#9998;</span></td>
                <td><?php 

                    if (strpos($pt->$pt_sop, '%') !== false) {
                        echo $pt->$pt_smea . "%";
                    } else {
                        if($pt->$pt_smea != 0){if($pt->$pt_smea > $pt->$pt_sop){echo $pt->$pt_sop;}else{echo $pt->$pt_smea;}} 
                    }

                
                
                ?></td>
                <td>
                    <?php 

                        if (strpos($pt->$pt_sop, '%') !== false) {
                            echo $pt->$pt_smea . "%";
                        } else {
                            if ($smea != 0 && $ptt != 0) { 
                                if($pt->$pt_smea < $pt->$pt_sop){
                                    echo (int)(($smea / $ptt) * 100) . "%";
                                }else{
                                    echo (int)(($ptt / $ptt) * 100) . "%";
                            }
                        }
                        
                        }
                    ?>
                </td>
                <td><?php 

                    if (strpos($pt->$pt_sop, '%') !== false) {
                        if($gain >= 0){echo abs($gain) . "%";}
                    } else {
                        if ($gain != 0 && $ptt != 0) { 
                            if($gain >= 0){echo $gain;} 
                        }
                    } 
                ?>
                </td>
                <td>
                    <?php 
                        if (strpos($pt->$pt_sop, '%') !== false) {
                            if($gain >= 0){echo abs($gain) . "%";}
                        } else {
                            if ($gain != 0 && $ptt != 0) { 
                                if($gain >= 0){echo (int)(($gain / $ptt) * 100) . "%";}
                            }
                        } 
                        
                    ?>
                </td>
                <td> <?php  
                    if (strpos($pt->$pt_sop, '%') !== false) {
                        if($gain <= 0){echo abs($gain) . "%";}
                    } else {
                        if($gain <= 0){echo abs($gain);}
                    } 
                ?> </td>
                <td>
                    <?php 
                        if (strpos($pt->$pt_sop, '%') !== false) {
                            if($gain <= 0){echo abs($gain) . "%";}
                        } else {
                            if ($gain != 0 && $ptt != 0) { 
                                if($gain <= 0){echo abs((int)(($gain / $ptt) * 100)) . "%";}
                            }
                        }
                        
                    ?>
                </td>
                <?php }else{?>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                <?php } ?>

                <?php 
                  $ft = $this->SGODModel->two_cond_row('sgod_sop_adjustment', 'aip_id',$row->id,'type',2);
                  $ft_smea = 'smea_q'.$q;
                  $ft_sop = 'q'.$q;
                  $ft_remarks = 'remarks_q'.$q;
                  if(!empty($ft)){
                    if($ft->$ft_sop != 0){$fptt = $ft->$ft_sop;}else{$fptt = 0;}
                    if($ft->$ft_smea != 0){$fsmea = $ft->$ft_smea;}else{$fsmea = 0;}

                    //$fgain = ((int)$fsmea/(int)$fptt)*100;

                ?>
                <td class="ivy smea-clickable" data-url="<?= base_url(); ?>Page/smea_ad_edit_modal/<?= $ft->id; ?>/<?= $q; ?>" data-q="<?= $q; ?>" data-span="6" title="Click to update Quarter <?= $q; ?> accomplishment"><a href="<?= base_url(); ?>Page/smea_ad_edit/<?= $ft->id; ?>/<?= $q; ?>"><?php if($ft->$ft_sop){echo number_format($ft->$ft_sop);}  ?></a><span class="smea-edit-hint">&#9998;</span></td>
                <td><?php if($ft->$ft_smea != 0){echo number_format($ft->$ft_smea, 2);}  ?></td>
                <td>
                    <?php 
                        if ($fsmea != 0 && $fptt != 0) { 
                            if($fsmea >= 0){echo abs((int)(($fsmea / $fptt) * 100)) . "%";}
                        }
                    ?>
                </td>
                <td>
                <?php 
                        if ($fsmea != 0 && $fptt != 0) { 
                            if($fsmea >= 0){echo number_format((int)($fptt - $fsmea), 2);}
                        }
                    ?>
                </td>
                <td>
                <?php 
                        if ($fsmea != 0 && $fptt != 0) { 
                            if($fsmea >= 0){$fgain = abs((int)($fsmea - $fptt));}
                            if($fsmea >= 0){echo number_format(abs((int)(($fgain / $fptt) * 100)), 2) . "%";}
                        }
                    ?>
                </td>
                <td></td>
                <?php }else{?>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                <?php } ?>


                <?php 
                  $fto = $this->SGODModel->two_cond_row('sgod_sop_adjustment', 'aip_id',$row->id,'type',3);
                  $fto_smea = 'smea_q'.$q;
                  $fto_sop = 'q'.$q;
                  $fto_remarks = 'remarks_q'.$q;
                  if(!empty($fto)){
                    if($fto->$fto_sop != 0){$fptto = $fto->$fto_sop;}else{$fptto = 0;}
                    if($fto->$fto_smea != 0){$fsmeao = $fto->$fto_smea;}else{$fsmeao = 0;}

                    //$fgain = ((int)$fsmea/(int)$fptt)*100;

                ?>
                <td class="ivy smea-clickable" data-url="<?= base_url(); ?>Page/smea_ad_edit_modal/<?= $fto->id; ?>/<?= $q; ?>" data-q="<?= $q; ?>" data-span="6" title="Click to update Quarter <?= $q; ?> accomplishment"><a href="<?= base_url(); ?>Page/smea_ad_edit/<?= $fto->id; ?>/<?= $q; ?>"><?php if($fto->$fto_sop){echo number_format($fto->$fto_sop);}  ?></a><span class="smea-edit-hint">&#9998;</span></td>
                <td><?php if($ft->$ft_smea != 0){echo number_format($ft->$ft_smea, 2);}  ?></td>
                <td>
                    <?php 
                        if ($fsmeao != 0 && $fptto != 0) { 
                            if($fsmeao >= 0){echo abs((int)(($fsmeao / $fptto) * 100)) . "%";}
                        }
                    ?>
                </td>
                <td>
                <?php 
                        if ($fsmeao != 0 && $fptto != 0) { 
                            if($fsmeao >= 0){echo number_format((int)($fptto - $fsmeao), 2);}
                        }
                    ?>
                </td>
                <td>
                <?php 
                        if ($fsmeao != 0 && $fptto != 0) { 
                            if($fsmeao >= 0){$fgain = abs((int)($fsmeao - $fptto));}
                            if($fsmeao >= 0){echo number_format(abs((int)(($fgain / $fptt) * 100)), 2) . "%";}
                        }
                    ?>
                </td>
                <td><?= $fto->$fto_remarks?></td>
                <?php }else{?>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                <?php } ?>

                

            </tr>
            <?php }  ?>
           
        </tbody> 
    </table>
    <?php } ?>


    <div class="fcon">
                <img style="width:90px; float:left;" src="https://qrcode.tec-it.com/API/QRCode?data=<?= base_url(); ?>Page/smea_qr/<?= $data_row->school_id.'/'.$data_row->fy.'/'.$data_row->b_code ?>" title="" />
                <div class="lcon">
                    
                    System Generated Report<br />
                    School Monitoring, Evaluation And Adjustment<br />
                    Date Generated: <?php  date_default_timezone_set('Asia/Manila'); echo date('F d, Y', time()); ?><br />
                    
                </div>
                
                <div class="blocker"></div>
    </div>

    <button id="backToTopBtn" class="back-to-top">Back to Top</button>

    <script>
        // Get the button element
const backToTopButton = document.getElementById("backToTopBtn");

// Show the button when the user scrolls down 100px from the top
window.onscroll = function() {
  if (document.body.scrollTop > 100 || document.documentElement.scrollTop > 100) {
    backToTopButton.classList.add("show");
  } else {
    backToTopButton.classList.remove("show");
  }
};

// Scroll to the top of the page when the button is clicked
backToTopButton.onclick = function() {
  window.scrollTo({ top: 0, behavior: 'smooth' });
};
    </script>

    <!-- SMEA accomplishment modal -->
    <div id="smeaModal" class="smea-modal-overlay">
        <div class="smea-modal">
            <div class="smea-modal-header">
                <span>Update Accomplishment</span>
                <button type="button" class="smea-modal-close" aria-label="Close">&times;</button>
            </div>
            <div class="smea-modal-body" id="smeaModalBody">
                <div class="smea-loading">Loading&hellip;</div>
            </div>
        </div>
    </div>

    <script>
    (function () {
        var overlay = document.getElementById('smeaModal');
        var bodyEl  = document.getElementById('smeaModalBody');

        // Only the highlighted (.ivy) cells are clickable — mark them so.
        document.querySelectorAll('td.ivy[data-url]').forEach(function (cell) {
            cell.classList.add('smea-clickable');
        });

        function openModal(url) {
            overlay.classList.add('show');
            bodyEl.innerHTML = '<div class="smea-loading">Loading&hellip;</div>';
            fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(function (r) { return r.text(); })
                .then(function (html) { bodyEl.innerHTML = html; bindForm(url); })
                .catch(function () { bodyEl.innerHTML = '<div class="smea-loading">Unable to load the form.</div>'; });
        }

        function closeModal() {
            overlay.classList.remove('show');
            bodyEl.innerHTML = '';
        }

        function bindForm(url) {
            var form = bodyEl.querySelector('form');
            if (!form) { return; }

            // Keep the "total accomplishment" running as the user edits this quarter.
            var acc          = form.querySelector('.smea-acc');
            var totalHidden  = form.querySelector('.smea-total');
            var totalDisplay = form.querySelector('.smea-total-display');
            var othersSum    = parseFloat(form.dataset.othersSum) || 0;
            function recompute() {
                var total = othersSum + (parseFloat(acc.value) || 0);
                if (totalHidden)  { totalHidden.value  = total; }
                if (totalDisplay) { totalDisplay.value = total; }
            }
            if (acc) { acc.addEventListener('input', recompute); recompute(); }

            form.addEventListener('submit', function (e) {
                e.preventDefault();
                var btn = form.querySelector('.smea-btn-save');
                if (btn) { btn.disabled = true; btn.textContent = 'Saving…'; }
                fetch(url, {
                    method: 'POST',
                    body: new FormData(form),
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                    .then(function (r) { return r.json(); })
                    .then(function (res) {
                        if (res && res.success) { window.location.reload(); }
                        else { throw new Error('save failed'); }
                    })
                    .catch(function () {
                        if (btn) { btn.disabled = false; btn.textContent = 'Save'; }
                        alert('Unable to save. Please try again.');
                    });
            });
        }

        document.addEventListener('click', function (e) {
            var cell = e.target.closest('.smea-clickable');
            if (cell && cell.dataset.url) { e.preventDefault(); openModal(cell.dataset.url); return; }
            if (e.target.closest('.smea-modal-close') || e.target === overlay) { closeModal(); }
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') { closeModal(); }
        });
    })();
    </script>







    </body>
                </html>