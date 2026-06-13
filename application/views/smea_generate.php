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
            .ivy{
                background-color:#2ea6a9 !important;
                color:#000 !important;
            }
            .ivy:hover {
                background-color:#26f5fa !important;
            }

            .back-to-top {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background-color: #2ea6a9;
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

    


    <?php foreach($data as $row){ 
         $io = $this->SGODModel->one_cond_row('sgod_setting_io', 'id', $row->io);
        ?>
    <ul>
        <li>PILLAR : <?= ucfirst($row->pillar); ?></li>
    </ul>

   

    <table >
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
                <td class="ivy" ><a href="<?= base_url(); ?>Page/smea_edit/<?= $pt->id; ?>/<?= $q; ?>"><?= $pt->$pt_sop; ?></a></td>
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
                <td class="ivy"><a href="<?= base_url(); ?>Page/smea_edit/<?= $ft->id; ?>/<?= $q; ?>"><?php echo $ft->$ft_sop;  ?></a></td>
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
                <td class="ivy"><a href="<?= base_url(); ?>Page/smea_edit/<?= $fto->id; ?>/<?= $q; ?>"><?php if($fto->$fto_sop){echo number_format($fto->$fto_sop);}  ?></a></td>
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
                <td class="ivy" ><a href="<?= base_url(); ?>Page/smea_ad_edit/<?= $pt->id; ?>/<?= $q; ?>"><?= $pt->$pt_sop; ?></a></td>
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
                <td class="ivy"><a href="<?= base_url(); ?>Page/smea_ad_edit/<?= $ft->id; ?>/<?= $q; ?>"><?php if($ft->$ft_sop){echo number_format($ft->$ft_sop);}  ?></a></td>
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
                <td class="ivy"><a href="<?= base_url(); ?>Page/smea_edit/<?= $fto->id; ?>/<?= $q; ?>"><?php if($fto->$fto_sop){echo number_format($fto->$fto_sop);}  ?></a></td>
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







    </body>
                </html>