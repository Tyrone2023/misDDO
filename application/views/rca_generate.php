<?php $chief = $this->Common->one_cond_row('aip_sign_settings','action',3); ?>
<?php $sds = $this->Common->one_cond_row('aip_sign_settings','action',4); ?>
<?php $fund = $this->Common->one_cond_row('aip_sign_settings','action',2); ?>
<?php
$ivy = $this->Common->one_cond_row_select('mis_settings','sgod_sign_type','settingsID',1);

// Create a function for converting the amount in words
function AmountInWords(float $amount): string
{
    $amount = round($amount, 2);

    $num   = (int) floor($amount);
    $cents = (int) round(($amount - $num) * 100);

    // Handle cases like 10.995 becoming 10 pesos and 100 cents
    if ($cents === 100) {
        $num++;
        $cents = 0;
    }

    $ones = [
        0 => '', 1 => 'One', 2 => 'Two', 3 => 'Three', 4 => 'Four', 5 => 'Five',
        6 => 'Six', 7 => 'Seven', 8 => 'Eight', 9 => 'Nine', 10 => 'Ten',
        11 => 'Eleven', 12 => 'Twelve', 13 => 'Thirteen', 14 => 'Fourteen',
        15 => 'Fifteen', 16 => 'Sixteen', 17 => 'Seventeen', 18 => 'Eighteen',
        19 => 'Nineteen'
    ];

    $tens = [
        2 => 'Twenty', 3 => 'Thirty', 4 => 'Forty', 5 => 'Fifty',
        6 => 'Sixty', 7 => 'Seventy', 8 => 'Eighty', 9 => 'Ninety'
    ];

    $scales = ['', 'Thousand', 'Million', 'Billion', 'Trillion'];

    $twoDigits = function(int $n) use ($ones, $tens): string {
        if ($n == 0) return '';
        if ($n < 20) return $ones[$n] ?? '';
        if ($n < 100) {
            $t = intdiv($n, 10);
            $o = $n % 10;
            return ($tens[$t] ?? '') . ($o ? ' ' . ($ones[$o] ?? '') : '');
        }
        return '';
    };

    $threeDigits = function(int $n) use ($ones, $twoDigits): string {
        $out = [];
        $h = intdiv($n, 100);
        $r = $n % 100;

        if ($h) $out[] = ($ones[$h] ?? '') . ' Hundred';
        if ($r) $out[] = $twoDigits($r);

        return implode(' ', $out);
    };

    $words = [];
    $wholeNum = $num;

    if ($wholeNum === 0) {
        $words[] = 'Zero';
    } else {
        $i = 0;
        while ($wholeNum > 0) {
            $chunk = $wholeNum % 1000;
            if ($chunk) {
                $chunkWords = $threeDigits($chunk);
                $scale = $scales[$i] ?? '';
                $words[] = trim($chunkWords . ($scale ? " $scale" : ''));
            }
            $wholeNum = intdiv($wholeNum, 1000);
            $i++;
        }
        $words = array_reverse($words);
    }

    $pesosWords = trim(implode(' ', $words)) . ' Pesos';

    $centWords = '';
    if ($cents > 0) {
        $centWords = ' And ' . $twoDigits($cents) . ' Centavos';
    }

    return $pesosWords . $centWords;
}
$validated = $this->Common->one_cond_row('aip_sign_settings','action',1);
?>

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
                    size: A4 portrait;
                    margin: 14mm 12mm;
                    }

            /* ---- A4 sheet look on screen ---- */
            @media screen {
                html { background: #e4e6eb; }
                body.aip_generate {
                    width: 210mm;
                    min-height: 297mm;
                    margin: 24px auto;
                    padding: 16mm 14mm;
                    background: #fff;
                    box-shadow: 0 2px 18px rgba(0,0,0,0.28);
                    box-sizing: border-box;
                }
            }

            /* ---- RCA signatory block ---- */
            .signWrapper .trusting{
                text-align: left;
                text-indent: 50px;
                margin: 18px 0 28px !important;
            }
            .rca-requester{
                width: 74%;
                margin: 10px 0 36px;
            }
            .rca-requester p{
                margin: 0 !important;
                text-align: left;
                font-weight: normal;
                line-height: 1.5;
            }
            .rca-requester .rca-truly{
                margin-bottom: 55px !important;
            }
            .rca-requester .rca-req-name{
                font-weight: bold;
            }
            .rca-requester .rca-bond{
                margin-top: 20px !important;
            }

            .rca-signatories{
                width: 100%;
            }
            .rca-sig-row{
                width: 100%;
                margin-bottom: 14px;
            }
            .rca-sig-row .rca-sig{
                width: 48%;
            }
            .rca-sig-row .rca-left{
                float: left;
            }
            .rca-sig-row .rca-right{
                float: right;
            }
            .rca-sig.rca-center{
                width: 62%;
                margin: 0 auto 18px;
            }
            /* beat ".rca p { text-align:left; margin-bottom:30px }" */
            .rca-sig p{
                margin: 0 !important;
            }
            .rca-sig .rca-role{
                text-align: left;
                font-weight: normal;
                margin-bottom: 4px !important;
            }
            .rca-sig.rca-center .rca-role{
                text-align: center;
            }
            .rca-sig .rca-signspace{
                position: relative;
                height: 60px;
            }
            .rca-sig .rca-signspace img{
                position: absolute;
                left: 50%;
                bottom: -4px;
                transform: translateX(-50%);
                max-height: 80px;
                max-width: 200px;
            }
            .rca-sig .rca-signname{
                text-align: center;
                font-weight: bold;
            }
            .rca-sig .rca-signpos{
                text-align: center;
                font-weight: normal;
            }
        </style>
    </head>


    <body class="aip_generate" id="printTable">

        <table style="width:100%; display:none;">
            <tr>
                <th>Quantity</th>
                <th>Unit Of Measure</th>
                <th>Item Description</th>
                <th>Stock No.</th>
                <th>Estimated Unit Cost</th>
                <th>Estimated Cost </th>
            </tr>

            
            <tr>
                <td colspan="5" class="alignLeft2">I. MANDATORY BILLS</td>
                <td></td>
            </tr>
            <?php $tmb = 0;
                foreach($mb as $row){?>
                <?php 
                $get_app = $this->SGODModel->two_cond_rca('sgod_app', 'aip_id',$row->id,$mon);
                
                foreach($get_app as $row){?>
                <?php 
                    if($mon == 'jan'){
                        $mq = $row->qjan;
                        $m = $row->jan;
                    }elseif($mon == 'feb'){
                        $mq = $row->qfeb;
                        $m = $row->feb;
                    }elseif($mon == 'mar'){
                        $mq = $row->qmar;
                        $m = $row->mar;
                    }elseif($mon == 'april'){
                        $mq = $row->qapril;
                        $m = $row->april;
                    }elseif($mon == 'may'){
                        $mq = $row->qmay;
                        $m = $row->may;
                    }elseif($mon == 'june'){
                        $mq = $row->qjune;
                        $m = $row->june;
                    }elseif($mon == 'july'){
                        $mq = $row->qjuly;
                        $m = $row->july;
                    }elseif($mon == 'aug'){
                        $mq = $row->qaug;
                        $m = $row->aug;
                    }elseif($mon == 'sept'){
                        $mq = $row->qsept;
                        $m = $row->sept;
                    }elseif($mon == 'oct'){
                        $mq = $row->qoct;
                        $m = $row->oct;
                    }elseif($mon == 'nov'){
                        $mq = $row->qnov;
                        $m = $row->nov;
                    }else{
                        $mq = $row->qdec;
                        $m = $row->ddec;
                    }
                    
                    ?>

                <tr>
                    <td><?= $mq; ?></td>
                    <td><?= $row->unit_measure; ?></td>
                    <td><?= $row->materials; ?></td>
                    <td></td>
                    <td><?= number_format($m); ?></td>
                    <td><?php $mb_ups = (double)$m*(double)$mq; echo  number_format($mb_ups); ?></td>
                    
                </tr>

                <?php $tmb+=$mb_ups;?>
                <?php  } ?>
                
            <?php  }  ?>


            <tr>
                <td colspan="5" class="alignLeft2">  II. MINOR REPAIR</td>
                <td></td>
            </tr>
            <?php $tmr = 0;
                foreach($mr as $row){?>
                <?php 
                $get_app = $this->SGODModel->two_cond_rca('sgod_app', 'aip_id',$row->id,$mon);
                
                foreach($get_app as $row){?>
                <?php 
                    if($mon == 'jan'){
                        $mq = $row->qjan;
                        $m = $row->jan;
                    }elseif($mon == 'feb'){
                        $mq = $row->qfeb;
                        $m = $row->feb;
                    }elseif($mon == 'mar'){
                        $mq = $row->qmar;
                        $m = $row->mar;
                    }elseif($mon == 'april'){
                        $mq = $row->qapril;
                        $m = $row->april;
                    }elseif($mon == 'may'){
                        $mq = $row->qmay;
                        $m = $row->may;
                    }elseif($mon == 'june'){
                        $mq = $row->qjune;
                        $m = $row->june;
                    }elseif($mon == 'july'){
                        $mq = $row->qjuly;
                        $m = $row->july;
                    }elseif($mon == 'aug'){
                        $mq = $row->qaug;
                        $m = $row->aug;
                    }elseif($mon == 'sept'){
                        $mq = $row->qsept;
                        $m = $row->sept;
                    }elseif($mon == 'oct'){
                        $mq = $row->qoct;
                        $m = $row->oct;
                    }elseif($mon == 'nov'){
                        $mq = $row->qnov;
                        $m = $row->nov;
                    }else{
                        $mq = $row->qdec;
                        $m = $row->ddec;
                    }

                    
                    ?>
                <tr>
                    <td><?= $mq; ?></td>
                    <td><?= $row->unit_measure; ?></td>
                    <td><?= $row->materials; ?></td>
                    <td></td>
                    <td><?= number_format($m); ?></td>
                    <td><?php $mb_ups = (double)$m*(double)$mq; echo  number_format($mb_ups); ?></td>
                    
                </tr>

                <?php $tmr+=$mb_ups;?>
                <?php  } ?>
                
            <?php  }  ?>

            <tr>
                <td colspan="5" class="alignLeft2"> III. TEACHING-LEARNING INSTRUCTION</td>
                <td></td>
            </tr>
            <?php $ttli = 0;
                foreach($tli as $row){?>
                <?php 
                $get_app = $this->SGODModel->two_cond_rca('sgod_app', 'aip_id',$row->id,$mon);
                
                foreach($get_app as $row){?>
                <?php 
                    if($mon == 'jan'){
                        $mq = $row->qjan;
                        $m = $row->jan;
                    }elseif($mon == 'feb'){
                        $mq = $row->qfeb;
                        $m = $row->feb;
                    }elseif($mon == 'mar'){
                        $mq = $row->qmar;
                        $m = $row->mar;
                    }elseif($mon == 'april'){
                        $mq = $row->qapril;
                        $m = $row->april;
                    }elseif($mon == 'may'){
                        $mq = $row->qmay;
                        $m = $row->may;
                    }elseif($mon == 'june'){
                        $mq = $row->qjune;
                        $m = $row->june;
                    }elseif($mon == 'july'){
                        $mq = $row->qjuly;
                        $m = $row->july;
                    }elseif($mon == 'aug'){
                        $mq = $row->qaug;
                        $m = $row->aug;
                    }elseif($mon == 'sept'){
                        $mq = $row->qsept;
                        $m = $row->sept;
                    }elseif($mon == 'oct'){
                        $mq = $row->qoct;
                        $m = $row->oct;
                    }elseif($mon == 'nov'){
                        $mq = $row->qnov;
                        $m = $row->nov;
                    }else{
                        $mq = $row->qdec;
                        $m = $row->ddec;
                    }
                    
                    ?>
                <tr>
                    <td><?= $mq; ?></td>
                    <td><?= $row->unit_measure; ?></td>
                    <td><?= $row->materials; ?></td>
                    <td></td>
                    <td><?= number_format($m); ?></td>
                    <td><?php $mb_ups = (double)$m*(double)$mq; echo  number_format($mb_ups); ?></td>
                    
                </tr>
                <?php $ttli+=$mb_ups;?>
                <?php  } ?>
                
            <?php  }  ?>
            <tr>
                <td colspan="5" class="alignLeft2">IV.TRAININGS/SEMINAR/TRAVEL</td>
                <td></td>
            </tr>
            <?php $ttst = 0;
                foreach($tst as $row){?>
                <?php 
                $get_app = $this->SGODModel->two_cond_rca('sgod_app', 'aip_id',$row->id,$mon);
                
                foreach($get_app as $row){?>
                <?php 
                    if($mon == 'jan'){
                        $mq = $row->qjan;
                        $m = $row->jan;
                    }elseif($mon == 'feb'){
                        $mq = $row->qfeb;
                        $m = $row->feb;
                    }elseif($mon == 'mar'){
                        $mq = $row->qmar;
                        $m = $row->mar;
                    }elseif($mon == 'april'){
                        $mq = $row->qapril;
                        $m = $row->april;
                    }elseif($mon == 'may'){
                        $mq = $row->qmay;
                        $m = $row->may;
                    }elseif($mon == 'june'){
                        $mq = $row->qjune;
                        $m = $row->june;
                    }elseif($mon == 'july'){
                        $mq = $row->qjuly;
                        $m = $row->july;
                    }elseif($mon == 'aug'){
                        $mq = $row->qaug;
                        $m = $row->aug;
                    }elseif($mon == 'sept'){
                        $mq = $row->qsept;
                        $m = $row->sept;
                    }elseif($mon == 'oct'){
                        $mq = $row->qoct;
                        $m = $row->oct;
                    }elseif($mon == 'nov'){
                        $mq = $row->qnov;
                        $m = $row->nov;
                    }else{
                        $mq = $row->qdec;
                        $m = $row->ddec;
                    }
                    
                    ?>
                <tr>
                    <td><?= $mq; ?></td>
                    <td><?= $row->unit_measure; ?></td>
                    <td><?= $row->materials; ?></td>
                    <td></td>
                    <td><?= number_format($m); ?></td>
                    <td><?php $mb_ups = (double)$m*(double)$mq; echo  number_format($mb_ups); ?></td>
                    
                </tr>
                <?php $ttst+=$mb_ups;?>
                <?php  } ?>
                
            <?php  }  ?>

            <tr>
                <td colspan="5" class="alignLeft">TOTAL</td>
                <td><?php $omtt = (double)($tmb+$tmr+$ttli+$ttst);   ?></td>
            </tr>
        </table>



    <img class="logo" src="<?= base_url(); ?>assets/images/report/ke.png" alt="">
    <p>
    <span class="rp">Republic of the Philippines</span><br />
        <span class="de">Department of Education</span><br />
        <span class="r">Region XI</span><br />
        <span class="r">School Division of <?= $school->division; ?></span><br />
        <?php $get_amount= AmountInWords($omtt); ?>
        <span class="sadress"><?= strtoupper($school->district); ?><br />
         <?= strtoupper($school->schoolName); ?><br />
         <?= strtoupper($school->sitio); ?> <?= strtoupper($school->brgy); ?>, <?= strtoupper($school->city); ?>, <?= strtoupper($school->province); ?></span> 
    </p>

    
    <div class="hr hrrca"></div>

    <?php 
                if($mon == 'jan'){
                    $month = 'January';
                }elseif($mon == 'feb'){
                    $month = 'February';
                }elseif($mon == 'mar'){
                    $month = 'March';
                }elseif($mon == 'april'){
                    $month = 'April';
                }elseif($mon == 'may'){
                    $month = 'May';
                }elseif($mon == 'june'){
                    $month = 'June';
                }elseif($mon == 'july'){
                    $month = 'July';
                }elseif($mon == 'aug'){
                    $month = 'August';
                }elseif($mon == 'sept'){
                    $month = 'September';
                }elseif($mon == 'oct'){
                    $month = 'October';
                }elseif($mon == 'nov'){
                    $month = 'November';
                }else{
                    $month = 'December';
                }
       
                ?>



    <div class="rca">
    <p><?php 
        date_default_timezone_set('Asia/Manila');
        //echo date('F d, Y', strtotime('20130814')); 
        echo date('F d, Y', time());
    ?></p>
    <p class="name"><b><?= $sds->fullname; ?></b><br /><?= $sds->position; ?></p>
    <p class="sname"><b>Thru: <?= $fund->fullname; ?></b><br/><span class="hide"><b>Thru: </b></span><?= $fund->position; ?></p>
    <p class="maam">Maam:</p>
    <p class="coa">In pursuance to COA Circular No. 97-002 dated February 10, 2007, the undersigned respectfully request the amount of <b> <?= $get_amount; ?></b>(&#8369; <?= number_format($omtt); ?> ) as Cash Advance for the Month of <b><?= $month; ?></b> the Maintenance & Other Operating Expenses (MOOE) of <b><?= strtoupper($school->schoolName); ?> <?php if(isset($bs->alloc_group)){echo strtoupper($bs->alloc_group);} ?>, <?php if(isset($school->sitio)){strtoupper($school->sitio).', ';} ?><?= strtoupper($school->brgy); ?>, <?= strtoupper($school->city); ?>, <?= strtoupper($school->province); ?></b> for payments of bills/travels/ purchases of various supplies and materials listed below for the Month of <b><?= $month; ?></b>.</p>


    <table style="width:100%; margin-bottom:0">
        <tr>
            <th>Quantity</th>
            <th>Unit Of Measure</th>
            <th>Item Description</th>
            <th>Stock No.</th>
            <th>Estimated Unit Cost</th>
            <th>Estimated Cost </th>
        </tr>

        
        <tr>
            <td colspan="5" class="alignLeft2">I. MANDATORY BILLS</td>
            <td></td>
        </tr>
        <?php $tmb = 0;
            foreach($mb as $row){?>
            <?php 
            $get_app = $this->SGODModel->two_cond_rca('sgod_app', 'aip_id',$row->id,$mon);
            
            foreach($get_app as $row){?>
            <?php 
                if($mon == 'jan'){
                    $mq = $row->qjan;
                    $m = $row->jan;
                }elseif($mon == 'feb'){
                    $mq = $row->qfeb;
                    $m = $row->feb;
                }elseif($mon == 'mar'){
                    $mq = $row->qmar;
                    $m = $row->mar;
                }elseif($mon == 'april'){
                    $mq = $row->qapril;
                    $m = $row->april;
                }elseif($mon == 'may'){
                    $mq = $row->qmay;
                    $m = $row->may;
                }elseif($mon == 'june'){
                    $mq = $row->qjune;
                    $m = $row->june;
                }elseif($mon == 'july'){
                    $mq = $row->qjuly;
                    $m = $row->july;
                }elseif($mon == 'aug'){
                    $mq = $row->qaug;
                    $m = $row->aug;
                }elseif($mon == 'sept'){
                    $mq = $row->qsept;
                    $m = $row->sept;
                }elseif($mon == 'oct'){
                    $mq = $row->qoct;
                    $m = $row->oct;
                }elseif($mon == 'nov'){
                    $mq = $row->qnov;
                    $m = $row->nov;
                }else{
                    $mq = $row->qdec;
                    $m = $row->ddec;
                }
                
                ?>

            <tr>
                <td><?= $mq; ?></td>
                <td><?= $row->unit_measure; ?></td>
                <td><?= $row->materials; ?></td>
                <td></td>
                <td><?= number_format($m); ?></td>
                <td><?php $mb_ups = (double)$m*(double)$mq; echo  number_format($mb_ups); ?></td>
                
            </tr>

            <?php $tmb+=$mb_ups;?>
            <?php  } ?>
            
        <?php  }  ?>


        <tr>
            <td colspan="5" class="alignLeft2">  II. MINOR REPAIR</td>
            <td></td>
        </tr>
        <?php $tmr = 0;
            foreach($mr as $row){?>
            <?php 
            $get_app = $this->SGODModel->two_cond_rca('sgod_app', 'aip_id',$row->id,$mon);
            
            foreach($get_app as $row){?>
            <?php 
                if($mon == 'jan'){
                    $mq = $row->qjan;
                    $m = $row->jan;
                }elseif($mon == 'feb'){
                    $mq = $row->qfeb;
                    $m = $row->feb;
                }elseif($mon == 'mar'){
                    $mq = $row->qmar;
                    $m = $row->mar;
                }elseif($mon == 'april'){
                    $mq = $row->qapril;
                    $m = $row->april;
                }elseif($mon == 'may'){
                    $mq = $row->qmay;
                    $m = $row->may;
                }elseif($mon == 'june'){
                    $mq = $row->qjune;
                    $m = $row->june;
                }elseif($mon == 'july'){
                    $mq = $row->qjuly;
                    $m = $row->july;
                }elseif($mon == 'aug'){
                    $mq = $row->qaug;
                    $m = $row->aug;
                }elseif($mon == 'sept'){
                    $mq = $row->qsept;
                    $m = $row->sept;
                }elseif($mon == 'oct'){
                    $mq = $row->qoct;
                    $m = $row->oct;
                }elseif($mon == 'nov'){
                    $mq = $row->qnov;
                    $m = $row->nov;
                }else{
                    $mq = $row->qdec;
                    $m = $row->ddec;
                }

                
                ?>
            <tr>
                <td><?= $mq; ?></td>
                <td><?= $row->unit_measure; ?></td>
                <td><?= $row->materials; ?></td>
                <td></td>
                <td><?= number_format($m); ?></td>
                <td><?php $mb_ups = (double)$m*(double)$mq; echo  number_format($mb_ups); ?></td>
                
            </tr>

            <?php $tmr+=$mb_ups;?>
            <?php  } ?>
            
        <?php  }  ?>

        <tr>
            <td colspan="5" class="alignLeft2"> III. TEACHING-LEARNING INSTRUCTION</td>
            <td></td>
        </tr>
        <?php $ttli = 0;
            foreach($tli as $row){?>
            <?php 
            $get_app = $this->SGODModel->two_cond_rca('sgod_app', 'aip_id',$row->id,$mon);
            
            foreach($get_app as $row){?>
            <?php 
                if($mon == 'jan'){
                    $mq = $row->qjan;
                    $m = $row->jan;
                }elseif($mon == 'feb'){
                    $mq = $row->qfeb;
                    $m = $row->feb;
                }elseif($mon == 'mar'){
                    $mq = $row->qmar;
                    $m = $row->mar;
                }elseif($mon == 'april'){
                    $mq = $row->qapril;
                    $m = $row->april;
                }elseif($mon == 'may'){
                    $mq = $row->qmay;
                    $m = $row->may;
                }elseif($mon == 'june'){
                    $mq = $row->qjune;
                    $m = $row->june;
                }elseif($mon == 'july'){
                    $mq = $row->qjuly;
                    $m = $row->july;
                }elseif($mon == 'aug'){
                    $mq = $row->qaug;
                    $m = $row->aug;
                }elseif($mon == 'sept'){
                    $mq = $row->qsept;
                    $m = $row->sept;
                }elseif($mon == 'oct'){
                    $mq = $row->qoct;
                    $m = $row->oct;
                }elseif($mon == 'nov'){
                    $mq = $row->qnov;
                    $m = $row->nov;
                }else{
                    $mq = $row->qdec;
                    $m = $row->ddec;
                }
                
                ?>
            <tr>
                <td><?= $mq; ?></td>
                <td><?= $row->unit_measure; ?></td>
                <td><?= $row->materials; ?></td>
                <td></td>
                <td><?= number_format($m); ?></td>
                <td><?php $mb_ups = (double)$m*(double)$mq; echo  number_format($mb_ups); ?></td>
                
            </tr>
            <?php $ttli+=$mb_ups;?>
            <?php  } ?>
            
        <?php  }  ?>
        <tr>
            <td colspan="5" class="alignLeft2">IV.TRAININGS/SEMINAR/TRAVEL</td>
            <td></td>
        </tr>
        <?php $ttst = 0;
            foreach($tst as $row){?>
            <?php 
            $get_app = $this->SGODModel->two_cond_rca('sgod_app', 'aip_id',$row->id,$mon);
            
            foreach($get_app as $row){?>
            <?php 
                if($mon == 'jan'){
                    $mq = $row->qjan;
                    $m = $row->jan;
                }elseif($mon == 'feb'){
                    $mq = $row->qfeb;
                    $m = $row->feb;
                }elseif($mon == 'mar'){
                    $mq = $row->qmar;
                    $m = $row->mar;
                }elseif($mon == 'april'){
                    $mq = $row->qapril;
                    $m = $row->april;
                }elseif($mon == 'may'){
                    $mq = $row->qmay;
                    $m = $row->may;
                }elseif($mon == 'june'){
                    $mq = $row->qjune;
                    $m = $row->june;
                }elseif($mon == 'july'){
                    $mq = $row->qjuly;
                    $m = $row->july;
                }elseif($mon == 'aug'){
                    $mq = $row->qaug;
                    $m = $row->aug;
                }elseif($mon == 'sept'){
                    $mq = $row->qsept;
                    $m = $row->sept;
                }elseif($mon == 'oct'){
                    $mq = $row->qoct;
                    $m = $row->oct;
                }elseif($mon == 'nov'){
                    $mq = $row->qnov;
                    $m = $row->nov;
                }else{
                    $mq = $row->qdec;
                    $m = $row->ddec;
                }
                
                ?>
            <tr>
                <td><?= $mq; ?></td>
                <td><?= $row->unit_measure; ?></td>
                <td><?= $row->materials; ?></td>
                <td></td>
                <td><?= number_format($m); ?></td>
                <td><?php $mb_ups = (double)$m*(double)$mq; echo  number_format($mb_ups); ?></td>
                
            </tr>
            <?php $ttst+=$mb_ups;?>
            <?php  } ?>
            
        <?php  }  ?>

        <tr>
            <td colspan="5" class="alignLeft">TOTAL</td>
            <td><?php $omt = $tmb+$tmr+$ttli+$ttst;  echo number_format($omt); ?></td>
            <?php $_SESSION['omt'] = $omt; ?>
        </tr>
    </table>
    <?php $aipstat = (!empty($school) && !empty($data_row)) ? $this->Common->two_cond_row('sgod_aip_submit', 'school_id', $school->schoolID, 'b_code', $data_row->b_code) : null; ?>

    <?php if($ivy->sgod_sign_type == 1){?>
        
    <div class="signWrapper">
         <p class="trusting">Trusting your preferential approval to herein request.</p>
         <?php $sh = $this->Common->one_cond_row_select('schools','','schoolID',1); ?>

         <div class="rca-requester">
            <p class="rca-truly">Very Truly yours,</p>
            <p class="rca-req-name"><?= strtoupper($school->adminFName .' '. mb_substr($school->adminMName, 0, 1, 'UTF-8') .'. '. $school->adminLName); ?></p>
            <p><?= $school->adminDesignation; ?></p>
            <p class="rca-bond">
                Fidelity Bond Risk Number: ____________________<br />
                Date of Expiration: ___________________________
            </p>
         </div>

         <div class="rca-signatories">

            <div class="rca-sig-row">

                <div class="rca-sig rca-left">
                    <p class="rca-role">Validated by</p>
                    <div class="rca-signspace">
                        <?php if (!empty($aipstat) && $aipstat->status == 1) { ?>
                        <img src="<?= base_url()?>assets/images/<?= $validated->sign; ?>" alt="">
                        <?php } ?>
                    </div>
                    <p class="rca-signname"><?= $validated->fullname; ?></p>
                    <p class="rca-signpos"><?= $validated->position; ?></p>
                </div>

                <div class="rca-sig rca-right">
                    <p class="rca-role">Funds Available:</p>
                    <div class="rca-signspace">
                        <?php if (!empty($aipstat) && $aipstat->status == 1) { ?>
                        <img src="<?= base_url()?>assets/images/<?= $fund->sign; ?>" alt="">
                        <?php } ?>
                    </div>
                    <p class="rca-signname"><?= $fund->fullname; ?></p>
                    <p class="rca-signpos"><?= $fund->position; ?></p>
                </div>

                <div class="blocker"></div>
            </div>

            <div class="rca-sig rca-center">
                <p class="rca-role">Recommending Approval:</p>
                <div class="rca-signspace">
                    <?php if (!empty($aipstat) && $aipstat->status == 1) { ?>
                    <img src="<?= base_url()?>assets/images/<?= $chief->sign; ?>" alt="">
                    <?php } ?>
                </div>
                <p class="rca-signname"><?= $chief->fullname; ?></p>
                <p class="rca-signpos"><?= $chief->position; ?></p>
            </div>

            <div class="rca-sig rca-center">
                <p class="rca-role">Approved:</p>
                <div class="rca-signspace">
                    <?php if (!empty($aipstat) && $aipstat->status == 1) { ?>
                    <img src="<?= base_url()?>assets/images/<?= $sds->sign; ?>" alt="">
                    <?php } ?>
                </div>
                <p class="rca-signname"><?= $sds->fullname; ?></p>
                <p class="rca-signpos"><?= $sds->position; ?></p>
            </div>

         </div>

         <div class="blocker"></div>

    </div>
    <?php }else{ ?>
        <div style="margin-top:50px"></div>
        <div class="fcon">
                <img style="width:90px; float:left;" src="https://qrcode.tec-it.com/API/QRCode?data=<?= base_url(); ?>Page/rca_view/<?= !empty($data_row) ? $data_row->school_id.'/'.$data_row->fy.'/'.$data_row->b_code.'/'.$mon : ''; ?>" title="" />
                <div class="lcon">
                    System Generated Report<br />
                    Date Generated: <?php  date_default_timezone_set('Asia/Manila'); echo date('F d, Y', time()); ?><br />
                </div>
                <div class="blocker"></div>
        </div>

    <?php } ?>



    


    </div>
    </body>
    </hmlm>
