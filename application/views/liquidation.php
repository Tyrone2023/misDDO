<?php

// Create a function for converting the amount in words
function AmountInWords(float $amount)
{
   $amount_after_decimal = round($amount - ($num = floor($amount)), 2) * 100;
   // Check if there is any number after decimal
   $amt_hundred = null;
   $count_length = strlen($num);
   $x = 0;
   $string = array();
   $change_words = array(0 => '', 1 => 'One', 2 => 'Two',
     3 => 'Three', 4 => 'Four', 5 => 'Five', 6 => 'Six',
     7 => 'Seven', 8 => 'Eight', 9 => 'Nine',
     10 => 'Ten', 11 => 'Eleven', 12 => 'Twelve',
     13 => 'Thirteen', 14 => 'Fourteen', 15 => 'Fifteen',
     16 => 'Sixteen', 17 => 'Seventeen', 18 => 'Eighteen',
     19 => 'Nineteen', 20 => 'Twenty', 30 => 'Thirty',
     40 => 'Forty', 50 => 'Fifty', 60 => 'Sixty',
     70 => 'Seventy', 80 => 'Eighty', 90 => 'Ninety');
    $here_digits = array('', 'Hundred','Thousand','Hundred', 'Million');
    while( $x < $count_length ) {
      $get_divider = ($x == 2) ? 10 : 100;
      $amount = floor($num % $get_divider);
      $num = floor($num / $get_divider);
      $x += $get_divider == 10 ? 1 : 2;
      if ($amount) {
       $add_plural = (($counter = count($string)) && $amount > 9) ? 's' : null;
       $amt_hundred = ($counter == 1 && $string[0]) ? ' and ' : null;
       $string [] = ($amount < 21) ? $change_words[$amount].' '. $here_digits[$counter]. $add_plural.' 
       '.$amt_hundred:$change_words[floor($amount / 10) * 10].' '.$change_words[$amount % 10]. ' 
       '.$here_digits[$counter].$add_plural.' '.$amt_hundred;
        }
   else $string[] = null;
   }
   $implode_to_Rupees = implode('', array_reverse($string));
   $get_paise = ($amount_after_decimal > 0) ? "And " . ($change_words[$amount_after_decimal / 10] . " 
   " . $change_words[$amount_after_decimal % 10]) . ' Centavos' : '';
   return ($implode_to_Rupees ? $implode_to_Rupees . 'Pesos ' : '') . $get_paise;
}
?>
<?php include('templates/head.php'); ?>  
            <?php include('templates/header.php'); ?>          

            <!-- ============================================================== -->
            <!-- Start Page Content here -->
            <!-- ============================================================== -->

            <div class="content-page">
                <div class="content">

                    <!-- Start Content-->
                    <div class="container-fluid">

                        <!-- start page title -->
                        <div class="row">
                            <div class="col-12">
                                <div class="page-title-box">
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                        </div>
                        <!-- end page title -->

                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body table-responsive">
                                    <!-- <button type="button" style="float: right;" class="btn btn-success btn-rounded waves-effect width-md waves-light">
                                        <a href="<?= site_url('Page/printEmployeelistv3'); ?>" target="_blank">
                                            <strong style="color: white;"><i class="mdi mdi-printer"></i>Print Preview</strong>
                                        </a>
                                    </button> -->
                                        <!-- <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;"> -->
                                        <?php 
                                            $arg= array(
                                                'jan'=>'qjan',
                                                'feb'=>'qfeb',
                                                'mar'=>'qmar',
                                                'april'=>'qapril',
                                                'may'=>'qmay',
                                                'june'=>'qjune',
                                                'july'=>'qjuly',
                                                'aug'=>'qaug',
                                                'sept'=>'qsept',
                                                'oct'=>'qoct',
                                                'nov'=>'qnov',
                                                'ddec'=>'qdec',
                                                
                                            );
                                            $m= array(
                                                'jan'=>'qjan',
                                                'feb'=>'qfeb',
                                                'mar'=>'qmar',
                                                'april'=>'qapril',
                                                'may'=>'qmay',
                                                'june'=>'qjune',
                                                'july'=>'qjuly',
                                                'aug'=>'qaug',
                                                'sept'=>'qsept',
                                                'oct'=>'qoct',
                                                'nov'=>'qnov',
                                                'dec'=>'qdec',
                                                
                                            );
                                        ?>
                                        
                                        <table id="datatable-buttons" class="table table-striped table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                        <thead>
                                            <tr>
                                                <th>Batch Code</th>
                                                <th>Allocation Amount</th>
                                                <?php foreach($m as $row => $key){?>
												<th><?= ucfirst($row); ?></th>
                                                <?php } ?>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach($app as $frow){?>
                                              <tr>
                                                <td><?= $frow->b_code; ?></td>
                                                <td></td>
                                                <?php foreach($arg as $row => $key){?>
                                                <td>

                                                <?php 
                                                    $bcode=$frow->b_code;
                                                    $total = $this->SGODModel->get_total_value($row,$key,$bcode);
                                                ?>
                                                <a href="<?= base_url(); ?>Page/l_view/<?= $frow->school_id; ?>/<?= $frow->fy; ?>/<?= $frow->b_code; ?>/<?= $row; ?>/<?= $key; ?>"><?= number_format($total,2); ?></a>

                                                </td>
                                                <?php } ?>
                                              </tr>
                                            <?php } ?>

                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- end row -->

                    </div>
                    <!-- end container-fluid -->

                </div>
                <!-- end content -->

                <?php include('templates/footer.php'); ?>       

             
 