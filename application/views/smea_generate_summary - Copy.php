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

      


    <h1>SCHOOL MONITORING, EVALUATION AND ADJUSTMENT<br />FY <?= $fy; ?></h1>

    <?php for ($q = 1; $q <= 4; $q++) {  ?>
    
        <h3>Quarter <?= $q; ?></h3>

    


    


   

    <table >
        
            <tr>
                <th rowspan="3">PILLAR</th>
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
          
        <?php foreach($data as $row){ 
         $pillar = $this->Common->four_cond('sgod_aip',"school_id", $row->school_id,"fy", $row->fy,"b_code", $row->b_code,"pillar", $row->pillar);
        ?>
            <tr>
                <td><?= $row->pillar; ?></td>
                <td>
                    <?php 
                        $psum=0; 
                        $sop = 'q'.$q;
                        $c=1; foreach($pillar as $prow){
                        $pt = $this->SGODModel->two_cond_row('sgod_sop', 'aip_id',$prow->id,'type',1);
                        if ($pt->$sop != 0 && $pt->$sop != "") {
                        // echo $pt->$sop;
                        // echo "<br />";
                        $psum += 1;
                    }}?>
                    <br />
                    <?php echo $psum; $sop_sum = $psum;?>

                    
                </td>
                <td>
                <?php 
                    $psum=0; 
                    $c=1; 
                    $sop = 'q'.$q;
                    $smea = 'smea_q'.$q;
                    foreach($pillar as $prow){
                        $pt = $this->SGODModel->two_cond_row('sgod_sop', 'aip_id',$prow->id,'type',1);
                        if($pt->$smea >= $pt->$sop && $pt->$sop != "") {
                            // echo $pt->$smea;
                            // echo "<br />";
                            $psum += 1;
                    } }?>
                    <br />
                    <?php echo $psum; $smea_sum = $psum;?>

                </td>
                <td>
                <?php //$per = ($smea_sum / $sop_sum) * 100; ?>
                <?php 
                    if ($smea_sum == 0) {
                        $per = "0.00%";  // Handle the error appropriately
                        echo $per;
                    } else {
                        //$per = ($smea_sum / $sop_sum) * 100;
                        //echo number_format($per, 2).'%';
                    }
                    
                ?>
                </td>
                <td>
                <?php 
                    $psum=0; 
                    $c=1; 
                    $sop = 'q'.$q;
                    $smea = 'smea_q'.$q;
                    foreach($pillar as $prow){
                        $pt = $this->SGODModel->two_cond_row('sgod_sop', 'aip_id',$prow->id,'type',1);
                        if($pt->$smea > $pt->$sop && $pt->$sop != "") {
                            // echo $pt->$smea;
                            // echo "<br />";
                            $psum += 1;
                    } }?>
                    <br />
                    <?php $gain = $psum; if($gain != 0){echo $gain;}?>
                </td>
                <td>
                <?php 
                    if ($gain == 0) {
                        $per = "";  // Handle the error appropriately
                        echo $per;
                    } else {
                        $per = ($gain / $sop_sum) * 100;
                        echo number_format($per, 2).'%';
                    }
                    
                ?>
                </td>
                <td>
                    <?php 
                        $psum=0; 
                        $c=1; 
                        $sop = 'q'.$q;
                        $smea = 'smea_q'.$q;
                        foreach($pillar as $prow){
                            $pt = $this->SGODModel->two_cond_row('sgod_sop', 'aip_id',$prow->id,'type',1);
                            if ($pt->$smea == 0 || $pt->$smea == "") {
                                continue; // Skip to the next iteration of the loop
                            }
                            
                            if($pt->$smea < $pt->$sop ){
                                // echo $pt->$smea;
                                // echo "<br />";
                                $psum += 1;
                        } } ?>
                        <br />
                        <?php $gap = $psum; if($gap != 0){echo $gap;}?>
                    
                </td>
                <td>
                <?php 
                    if ($gap == 0) {
                        $per = ""; 
                        echo $per;
                    } else {
                        $per = ($gap / $sop_sum) * 100;
                        echo number_format($per, 2).'%';
                    }
                    
                ?>
                </td>
                <td>
                <?php 
                        $psum=0; 
                        $sop = 'q'.$q;
                        $c=1; foreach($pillar as $prow){
                        $pt = $this->SGODModel->two_cond_row('sgod_sop', 'aip_id',$prow->id,'type',2);
                        if ($pt->$sop != 0 && $pt->$sop != "") {
                        // echo $pt->$sop;
                        // echo "<br />";
                        $psum += $pt->$sop;
                    }}?>
                    <br />
                    <?php  $psum; $fsop_sum = $psum;?>
                </td>
                <td>
                <?php 
                    $psum=0; 
                    $c=1; 
                    $sop = 'q'.$q;
                    $smea = 'smea_q'.$q;
                    foreach($pillar as $prow){
                        $pt = $this->SGODModel->two_cond_row('sgod_sop', 'aip_id',$prow->id,'type',3);
                        if($pt->$smea >= $pt->$sop && $pt->$sop != "") {
                            // echo $pt->$smea;
                            // echo "<br />";
                            $psum += $pt->$smea;
                    } }?>
                    <br />
                    <?php echo $psum; $sfmea_sum = $psum;?>
                </td>
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
            </tr>
        <?php } ?>

        
            
           
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