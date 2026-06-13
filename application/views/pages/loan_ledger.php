
            <?php 
                $cm = $this->Provident_model->comaker($this->uri->segment(4)); 
                $remark = $this->Common->one_cond_row('provident_loan_remarks','deduction_id',$this->uri->segment(4));
            ?>
            <!-- ============================================================== -->
            <!-- Start Page Content here -->
            <!-- ============================================================== -->

            <div class="content-page">
                <div class="content">
                    
                    <style>
                    @media print {
                    .hp {
                        display: none !important;
                    }
                    }
                    </style>

                    <!-- Start Content-->
                    <div class="container-fluid">

                        <!-- start page title -->
                        <div class="row">
                            <div class="col-12">
                                <div class="page-title-box">
                                    
                                    <?php if (empty($cm)){ ?>
                                    <a href="#" class="btn btn-primary waves-effect waves-light openModalBtn"data-toggle="modal" data-target="#myModal">Add Co-Maker</a>
                                    <?php }else{ ?>
                                    <a href="#" class="btn btn-info waves-effect waves-light openModalBtn" data-toggle="modal" data-target="#cmUpdate">Update Co-Maker</a>
                                    <?php }?>

                                    <?php if (empty($remark)){ ?>
                                    <a href="#" class="btn btn-purple waves-effect waves-light openModalBtn" data-toggle="modal" data-target="#remarks">Remarks</a>
                                    <?php }else{ ?>
                                    <a href="#" class="btn btn-success waves-effect waves-light openModalBtn" data-toggle="modal" data-target="#updateremarks">Update Remarks</a>
                                    <?php }?>

                                    
  
                                    
                                    <div class="clearfix"></div>
                                    
                                </div>
                            </div>
                        </div>
                        <!-- end page title -->

                        <?php 
                            $monthFrom = $data->effectMonthFrom;
                            $yearFrom  = $data->effectYearFrom;

                            $monthTo   = $data->effectMonthTo;   // March
                            $yearTo    = $data->effectYearTo;

                            $totalMonths = (($yearTo - $yearFrom) * 12) + ($monthTo - $monthFrom) + 1;
                            $ivankyle  = intdiv($totalMonths, 12);


                            $principal = $data->principalAmount;
                            $rate = 0.005;      // 0.5% monthly
                            $months = $ivankyle * 12;

                            // monthly amortized payment
                            $monthlyPayment = $principal * $rate / (1 - pow(1 + $rate, -$months));

                            // total payment over 5 years
                            $totalPayment = $monthlyPayment * $months;

                            // total interest
                            $totalInterest = $totalPayment - $principal;
                        ?>

                        <!-- start row -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card">
                                    <!-- <div class="panel-heading">
                                                    <h4>Invoice</h4>
                                                </div> -->
                                                <?php if($this->session->flashdata('success')) : ?>

                                        <?= '<div class="alert alert-success alert-dismissible fade show" role="alert">
                                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>'
                                                .$this->session->flashdata('success'). 
                                            '</div>'; 
                                        ?>
                                        <?php endif; ?>

                                        <?php if($this->session->flashdata('danger')) : ?>
                                        <?= '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>'
                                                .$this->session->flashdata('danger'). 
                                            '</div>'; 
                                        ?>
                                        <?php endif;  ?>
                                    <div class="card-body">
                                        <div class="clearfix">
                                            <div class="text-center">
                                                <h4 class="text-center">DEPED DIVISION OF DAVAO ORIENTAL <br />PROVIDENT FUND <br />STATEMENT OF ACCOUNT</h4>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <?php $school = $this->Common->one_cond_row_select('schools','schoolID,stationCode,schoolName,district','schoolID',$staff->schoolID); ?>

                                                <div class="float-left mt-4">
                                                    <p style="margin:0"><strong>Borrower's Name:<?php echo $data->dedID; ?> </strong><?= $staff->FirstName; ?> <?= $staff->MiddleName; ?> <?= $staff->LastName; ?>  </p>
                                                    <p class="mt-2" style="margin:0 !important"><strong>Station code: </strong>  <?= $school->stationCode; ?></p>
                                                    <p class="mt-2" style="margin:0 !important"><strong>District/School </strong>  <?= $school->schoolName; ?> / <?= strtoupper($school->district); ?></p>
                                                    <p class="mt-2" style="margin:0 !important"><strong>Loan Amount: </strong>&#8369; <?= number_format($data->principalAmount); ?> </p>
                                                    <p class="mt-2" style="margin:0 !important"><strong>Interest: </strong>&#8369; <?= number_format($totalInterest, 2); ?></p>
                                                    <p class="mt-2" style="margin:0 !important"><strong>Scheduled Payment: </strong>&#8369; <?= number_format($monthlyPayment, 2) . PHP_EOL; ?></p>
                                                    <p class="mt-2" style="margin:0 !important"><strong>Loan Period: </strong>  <?= $ivankyle; ?> Years</p>
                                                </div>
                                                <div class="float-right mt-4">
                                                    <p style="margin:0 !important"><strong>Co-Maker: </strong> </strong><?php if (!empty($cm)) : ?><?= $cm->FirstName; ?> <?= $cm->MiddleName; ?> <?= $cm->LastName; ?><?php endif; ?> </p>
                                                    <p class="mt-2" style="margin:0 !important"><strong>Station code: </strong>  <?php if (!empty($cm)) : ?><?= $cm->stationCode; ?><?php endif; ?></p>
                                                    <p class="mt-2" style="margin:0 !important"><strong>District/School: </strong>  <?php if (!empty($cm)) : ?><?= strtoupper($cm->district); ?> / <?= strtoupper($cm->schoolName); ?><?php endif; ?></p>
                                                    <!-- <p class="mt-2"><strong>Order Status: </strong> <span class="badge badge-pink">Pending</span></p>
                                                    <p class="mt-2"><strong>Order ID: </strong> #123456</p> -->
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mt-5"></div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="table-responsive">
                                                    <table class="table mt-4">
                                                        <thead class="text-center">
                                                            <tr>
                                                                <th>No.</th>
                                                                <th>Date</th>
                                                                <th>Payments Made<br /> Principal</th>
                                                                <th>Payments Made<br /> Interest</th>
                                                                <th>Total</th>
                                                                <th>Outstanding<br /> Balance<br /> Principal</th>
                                                                <th>Outstanding<br /> Balance<br /> Arrears</th>
                                                                <th>Outstanding<br /> Balance<br /> Reverse int.</th>
                                                                <th>Outstanding<br /> Balance<br /> Interest</th>
                                                                <th>Outstanding<br /> Balance<br /> Balance</th>
                                                                <th class="hp">Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody class="text-center">
                                                            <?php
                                                                $c = 1;

                                                                $balance = (float) $data->principalAmount;
                                                                $rate = 0.005;
                                                                $prevInterestBal = round((float) $totalInterest, 2);

                                                                // get the last key of the $payment array
                                                                $lastKey = array_key_last($payment);


                                                                foreach ($payment as $k => $row) {
                                                                    
                                                                     
                                                                    $ivan = $this->Common->two_cond_row('provident_ledger','dedID',$data->dedID,'type',0);

                                                                    $paymentAmt = round((float) $row->dedAmount, 2);

                                                                    $interest = round($balance * $rate, 2);
                                                                    $paymentMadeInterest = $interest;

                                                                    $principalPaid = round($paymentAmt - $paymentMadeInterest, 2);
                                                                    if ($principalPaid < 0) $principalPaid = 0;

                                                                    if ($principalPaid > $balance) {
                                                                        $principalPaid = $balance;
                                                                        $paymentAmt = round($paymentMadeInterest + $principalPaid, 2);
                                                                    }

                                                                    $newBalance = round($balance - $principalPaid, 2);

                                                                    $arrears = isset($ivan->arrears) ? round((float)$ivan->arrears, 2) : 0.00;
                                                                    $reverse = isset($ivan->reverse_int) ? round((float)$ivan->reverse_int, 2) : 0.00;

                                                                    $outstandingInterest = round($prevInterestBal - $paymentMadeInterest + $arrears - $reverse, 2);
                                                                    if ($outstandingInterest < 0) $outstandingInterest = 0;

                                                                    $outstandingTotalBal = round($newBalance + $outstandingInterest + $arrears - $reverse, 2);

                                                                    // ✅ TRUE if this is the last row
                                                                    $isLastRow = ($k === $lastKey);
                                                                ?>
                                                                <tr>
                                                                    <td><?= $c++; ?></td>
                                                                    <td><?= $row->dedMonth; ?> <?= $row->dedYear; ?></td>

                                                                    <td><?= number_format($principalPaid, 2); ?></td>
                                                                    <td><?= number_format($paymentMadeInterest, 2); ?></td>
                                                                    <td><?= number_format($paymentAmt, 2); ?></td>

                                                                    <td><?= number_format($newBalance, 2); ?></td>
                                                                    <td><?= number_format($arrears, 2); ?></td>
                                                                    <td><?= number_format($reverse, 2); ?></td>
                                                                    <td><?= number_format($outstandingInterest, 2); ?></td>
                                                                    <td><?= number_format($outstandingTotalBal, 2); ?></td>

                                                                    
                                                                    <td class="hp">
                                                                        <?php $opinfo = $this->Common->three_cond_row('provident_add_info','month',$row->dedMonth,'fy',$row->dedYear,'school_id',$this->uri->segment(3));  ?>
                                                                        <?php if ($isLastRow): ?>
                                                                            <?php if ($ivan): ?>
                                                                                    <a href="#"
                                                                                        class="open-AddBookDialog text-warning"
                                                                                        data-toggle="modal"
                                                                                        data-id="<?= $ivan->id; ?>"
                                                                                        data-target="#renren">
                                                                                        <i class=" fas fa-user-edit"></i>
                                                                                    </a> &nbsp; &nbsp;
                                                                                    <?php if($opinfo){?>
                                                                                        <a href="#" data-toggle="modal" data-target="#editinfo"><i class="fas fa-file-signature text-purple"></i></a> &nbsp; &nbsp;
                                                                                        <a target="_blank" href="<?= base_url(); ?>Provident/order_of_payment_single_psu/<?= $this->uri->segment('3'); ?>/<?= $row->dedMonth; ?>/<?= $row->dedYear; ?>/<?= rawurlencode((string)$reverse); ?>/<?= $school->schoolID; ?>"><i class="fas fa-money-check-alt text-info"></i></a> 
                                                                                    <?Php }else{ ?>
                                                                                        <a href="#" class="open-AddBookDialog" data-toggle="modal" data-id="<?= $row->dedMonth; ?>" data-item="<?= $row->dedYear; ?>" data-target="#addinfo"><i class="fas fa-file-signature text-info"></i></a>
                                                                                    <?php } ?>
                                                                                    <?php else: ?>
                                                                                    <a href="#"
                                                                                        class="btn btn-success waves-effect waves-light openModalBtn open-AddBookDialog"
                                                                                        data-toggle="modal"
                                                                                        data-id="<?= $this->uri->segment(4); ?>"
                                                                                        data-target="#ivykate">
                                                                                        Pay
                                                                                    </a>
                                                                                    <?php endif; ?>
                                                                        <?php endif; ?>
                                                                    </td>
                                                                </tr>
                                                                <?php
                                                                    $balance = $newBalance;
                                                                    $prevInterestBal = $outstandingInterest;

                                                                    if ($balance <= 0) break; // note: if this breaks early, "last row" becomes the break row
                                                                }
                                                                ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                        <?php if(!empty($remark)){ ?>
                                        <div class="row" style="border-radius: 0px">
                                            <div class="col-md-3 offset-md-9">
                                                <p class="text-right"><b>Reloan date:</b> <?= $remark->reloan_date; ?></p>
                                                <p class="text-right">loan amount: &#8369; <?= number_format($remark->loan_amount, 2); ?></p>
                                                <p class="text-right">less loan balance: &#8369; <?= number_format($remark->less_loan_bal, 2); ?></p>
                                                <hr>
                                                <h4 class="text-right">Net: &#8369; <?= number_format($remark->net_amount, 2); ?></h4>
                                            </div>
                                        </div>
                                        <?php } ?>
                                        <br /><br />
                                        <div class="table-responsive">
                                            <table class="table table-borderless mb-0">
                                                <thead>
                                                    <tr>
                                                        <th>Prepared by:</th>
                                                        <th></th>
                                                        <th>Certified Correct:</th>
                                                        <th>&nbsp;</th>
                                                        <th>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; </th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <th scope="row"></th>
                                                        <td><u><strong>MOREZA OSMAN-MANKIKIS</strong></u><br />Provident Fund Secretariat</td>
                                                        <td>&nbsp;</td>
                                                        <td><u><strong>DENNIS Y. BELARMINO</strong></u><br />Accountant III</td>
                                                        <td>&nbsp;</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        
                                        <hr>
                                        <div class="d-print-none">
                                            <div class="float-right">
                                                <a href="javascript:window.print()" class="btn btn-dark waves-effect waves-light mr-1"><i class="fa fa-print"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>

                        </div>
                        <!-- end row -->

                    </div>
                    <!-- end container-fluid -->

                </div>
                <!-- end content -->

               
                <!-- sample modal content -->
                                        <div id="myModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content modal-lg">
                                                    <div class="modal-header bg-info">
                                                        <h5 class="modal-title" id="modalTitle">Add Co-Maker</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                    </div>
                                                    <div class="modal-body">

                                                    <div class="row">
                                                        <div class="col-12">
                                                            <div class="">
                                                                    <?= validation_errors(); ?>
                                                                    <?php
                                                                    $attributes = array('class' => 'parsley-examples form-horizontal');
                                                                    echo form_open('Provident/provident_comaker', $attributes);
                                                                    ?>

                                                                    <div class="form-group row">
                                                                        <label class="col-lg-4 col-form-label">Fullname</label>
                                                                        <div class="col-lg-8">
                                                                            
                                                                           <input type="hidden" value="<?= $this->uri->segment('3'); ?>" name="IDNumber">
                                                                           <input type="hidden" value="<?= $this->uri->segment('4'); ?>" name="deduction_id">
                                                                           
                                                                        
                                                                            <select name="comaker_id" class="form-control" data-toggle="select2" required>
                                                                                <option value="" disabled selected>Select Employee</option>
                                                                                <?php foreach($comaker as $row){?>
                                                                                <option value="<?= $row->IDNumber; ?>"><?= strtoupper($row->LastName); ?>, <?= strtoupper($row->FirstName); ?> <?= !empty($row->MiddleName) ? strtoupper(mb_substr($row->MiddleName, 0, 1)) . '.' : '' ?> </option>
                                                                                <?php } ?>
                                                                            </select>
                                                                        </div>
                                                                    </div>

                                                                
                                                            </div>
                                                        </div>

                                                    </div>
                                                    <!-- end row -->
                                                        
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="submit" class="btn btn-info waves-effect waves-light">Save</button>
                                                    </div>
                                                    </form>
                                                </div>
                                                <!-- /.modal-content -->
                                            </div>
                                            <!-- /.modal-dialog -->
                                        </div>
                                        <!-- /.modal -->

                                        <!-- sample modal content -->
                                        <div id="cmUpdate" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content modal-lg">
                                                    <div class="modal-header bg-info">
                                                        <h5 class="modal-title" id="modalTitle">Update Co-Maker</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                    </div>
                                                    <div class="modal-body">

                                                    <div class="row">
                                                        <div class="col-12">
                                                            <div class="">
                                                                    <?= validation_errors(); ?>
                                                                    <?php
                                                                    $attributes = array('class' => 'parsley-examples form-horizontal');
                                                                    echo form_open('Provident/provident_comaker_update', $attributes);
                                                                    ?>

                                                                    <div class="form-group row">
                                                                        <label class="col-lg-4 col-form-label">Fullname</label>
                                                                        <div class="col-lg-8">
                                                                            
                                                                           <input type="hidden" value="<?= $cm->id; ?>" name="id">
                                                                        
                                                                            <select name="comaker_id" class="form-control" data-toggle="select2" required>
                                                                                <option value="" disabled selected>Select Employee</option>
                                                                                <?php foreach($comaker as $row){?>
                                                                                <option value="<?= $row->IDNumber; ?>"><?= strtoupper($row->LastName); ?>, <?= strtoupper($row->FirstName); ?> <?= !empty($row->MiddleName) ? strtoupper(mb_substr($row->MiddleName, 0, 1)) . '.' : '' ?> </option>
                                                                                <?php } ?>
                                                                            </select>
                                                                        </div>
                                                                    </div>

                                                                
                                                            </div>
                                                        </div>

                                                    </div>
                                                    <!-- end row -->
                                                        
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="submit" class="btn btn-info waves-effect waves-light">Save</button>
                                                    </div>
                                                    </form>
                                                </div>
                                                <!-- /.modal-content -->
                                            </div>
                                            <!-- /.modal-dialog -->
                                        </div>
                                        <!-- /.modal -->

                                        <!-- sample modal content -->
                                        <div id="renren" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content modal-lg">
                                                    <div class="modal-header bg-success">
                                                        <h5 class="modal-title" id="modalTitle">Outstanding Balance</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                    </div>
                                                    <div class="modal-body">

                                                    <div class="row">
                                                        <div class="col-12">
                                                            <div class="">
                                                                    <?= validation_errors(); ?>
                                                                    <?php
                                                                    $attributes = array('class' => 'parsley-examples form-horizontal');
                                                                    echo form_open('Provident/provident_ledger_update', $attributes);
                                                                    ?>

                                                                    <input type="hidden" value="" name="id" id="id">

                                                                    <div class="form-group row">
                                                                        <label class="col-lg-4 col-form-label">Outstanding Balance Arrears</label>
                                                                        <div class="col-lg-8">
                                                                        
                                                                        <input type="text" oninput="formatDecimal(this);" value="" name="arrears" class="form-control">

                                                                        </div>
                                                                    </div>


                                                                    <div class="form-group row">
                                                                        <label class="col-lg-4 col-form-label">Outstanding Balance Reverse int.</label>
                                                                        <div class="col-lg-8">
                                                                        
                                                                        <input type="text" oninput="formatDecimal(this);" value="" name="reverse_int" class="form-control">

                                                                        </div>
                                                                    </div>

                                                                
                                                            </div>
                                                        </div>

                                                    </div>
                                                    <!-- end row -->
                                                        
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="submit" class="btn btn-success waves-effect waves-light">Save</button>
                                                    </div>
                                                    </form>
                                                </div>
                                                <!-- /.modal-content -->
                                            </div>
                                            <!-- /.modal-dialog -->
                                        </div>
                                        <!-- /.modal -->

                                        <!-- sample modal content -->
                                        <div id="ivykate" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content modal-lg">
                                                    <div class="modal-header bg-success">
                                                        <h5 class="modal-title" id="modalTitle">Outstanding Balance</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                    </div>
                                                    <div class="modal-body">

                                                    <div class="row">
                                                        <div class="col-12">
                                                            <div class="">
                                                                    <?= validation_errors(); ?>
                                                                    <?php
                                                                    $attributes = array('class' => 'parsley-examples form-horizontal');
                                                                    echo form_open('Provident/provident_ledger_insert', $attributes);
                                                                    ?>

                                                                    <input type="hidden" value="" name="dedID" id="id">
                                                                    <input type="hidden" value="0" name="type">

                                                                    <div class="form-group row">
                                                                        <label class="col-lg-4 col-form-label">Outstanding Balance Arrears</label>
                                                                        <div class="col-lg-8">
                                                                        
                                                                        <input type="text" oninput="formatDecimal(this);" value="" name="arrears" class="form-control">

                                                                        </div>
                                                                    </div>


                                                                    <div class="form-group row">
                                                                        <label class="col-lg-4 col-form-label">Outstanding Balance Reverse int.</label>
                                                                        <div class="col-lg-8">
                                                                        
                                                                        <input type="text" oninput="formatDecimal(this);" value="" name="reverse_int" class="form-control">

                                                                        </div>
                                                                    </div>

                                                                
                                                            </div>
                                                        </div>

                                                    </div>
                                                    <!-- end row -->
                                                        
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="submit" class="btn btn-success waves-effect waves-light">Save</button>
                                                    </div>
                                                    </form>
                                                </div>
                                                <!-- /.modal-content -->
                                            </div>
                                            <!-- /.modal-dialog -->
                                        </div>
                                        <!-- /.modal -->


                                    <?php if (empty($remark)){ ?>
                                        <!-- sample modal content -->
                                        <div id="remarks" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content modal-lg">
                                                    <div class="modal-header bg-info">
                                                        <h5 class="modal-title" id="modalTitle">Add Remarks</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                    </div>
                                                    <div class="modal-body">

                                                    <div class="row">
                                                        <div class="col-12">
                                                            <div class="">
                                                                    <?= validation_errors(); ?>
                                                                    <?php
                                                                    $attributes = array('class' => 'parsley-examples form-horizontal');
                                                                    echo form_open('Provident/provident_remarks', $attributes);
                                                                    ?>

                                                                    <div class="form-group row">
                                                                        <label class="col-lg-4 col-form-label">Reloan Date</label>
                                                                        <div class="col-lg-8">
                                                                            
                                                                           <input type="hidden" value="<?= $this->uri->segment('3'); ?>" name="IDNumber">
                                                                           <input type="hidden" value="<?= $this->uri->segment('4'); ?>" name="deduction_id">
                                                                           <input type="date" value="" name="reloan_date" class="form-control" required>
                                                                        
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group row">
                                                                        <label class="col-lg-4 col-form-label">Loan amount</label>
                                                                        <div class="col-lg-8">
                                                                            <input type="text"
                                                                                id="loan_amount"
                                                                                value=""
                                                                                oninput="formatDecimal(this); computeNetAmount();"
                                                                                name="loan_amount"
                                                                                class="form-control">
                                                                        </div>
                                                                    </div>

                                                                    <div class="form-group row">
                                                                        <label class="col-lg-4 col-form-label">Less loan Balance</label>
                                                                        <div class="col-lg-8">
                                                                            <input type="text"
                                                                                id="less_loan_bal"
                                                                                value=""
                                                                                oninput="formatDecimal(this); computeNetAmount();"
                                                                                name="less_loan_bal"
                                                                                class="form-control">
                                                                        </div>
                                                                    </div>

                                                                    <div class="form-group row">
                                                                        <label class="col-lg-4 col-form-label">Net Amount</label>
                                                                        <div class="col-lg-8">
                                                                            <input type="text"
                                                                                id="net_amount"
                                                                                value=""
                                                                                name="net_amount"
                                                                                class="form-control"
                                                                                readonly>
                                                                        </div>
                                                                    </div>


                                                                
                                                            </div>
                                                        </div>

                                                    </div>
                                                    <!-- end row -->
                                                        
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="submit" class="btn btn-info waves-effect waves-light">Save</button>
                                                    </div>
                                                    </form>
                                                </div>
                                                <!-- /.modal-content -->
                                            </div>
                                            <!-- /.modal-dialog -->
                                        </div>
                                        <!-- /.modal -->
                            <?php } ?>
                            <?php if (!empty($remark)){ ?>
                                        <!-- sample modal content -->
                                        <div id="updateremarks" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content modal-lg">
                                                    <div class="modal-header bg-success">
                                                        <h5 class="modal-title" id="modalTitle">Update Remarks</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                    </div>
                                                    <div class="modal-body">

                                                    <div class="row">
                                                        <div class="col-12">
                                                            <div class="">
                                                                    <?= validation_errors(); ?>
                                                                    <?php
                                                                    $attributes = array('class' => 'parsley-examples form-horizontal');
                                                                    echo form_open('Provident/provident_remarks_edit', $attributes);
                                                                    ?>

                                                                    <input type="hidden" value="<?= $remark->id; ?>" name="id">

                                                                    <div class="form-group row">
                                                                        <label class="col-lg-4 col-form-label">Reloan Date</label>
                                                                        <div class="col-lg-8">
                                                                            
                                                                           <input type="date" value="<?= $remark->reloan_date; ?>" name="reloan_date" class="form-control">
                                                                        
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group row">
                                                                        <label class="col-lg-4 col-form-label">Loan amount</label>
                                                                        <div class="col-lg-8">
                                                                            <input type="text"
                                                                                id="loan_amount"
                                                                                value="<?= $remark->loan_amount; ?>"
                                                                                oninput="formatDecimal(this); computeNetAmount();"
                                                                                name="loan_amount"
                                                                                class="form-control">
                                                                        </div>
                                                                    </div>

                                                                    <div class="form-group row">
                                                                        <label class="col-lg-4 col-form-label">Less loan Balance</label>
                                                                        <div class="col-lg-8">
                                                                            <input type="text"
                                                                                id="less_loan_bal"
                                                                                value="<?= $remark->less_loan_bal; ?>"
                                                                                oninput="formatDecimal(this); computeNetAmount();"
                                                                                name="less_loan_bal"
                                                                                class="form-control">
                                                                        </div>
                                                                    </div>

                                                                    <div class="form-group row">
                                                                        <label class="col-lg-4 col-form-label">Net Amount</label>
                                                                        <div class="col-lg-8">
                                                                            <input type="text"
                                                                                id="net_amount"
                                                                                value=""
                                                                                name="net_amount"
                                                                                class="form-control"
                                                                                readonly>
                                                                        </div>
                                                                    </div>


                                                                
                                                            </div>
                                                        </div>

                                                    </div>
                                                    <!-- end row -->
                                                        
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="submit" class="btn btn-success waves-effect waves-light">Save</button>
                                                    </div>
                                                    </form>
                                                </div>
                                                <!-- /.modal-content -->
                                            </div>
                                            <!-- /.modal-dialog -->
                                        </div>
                                        <!-- /.modal -->

                                    <?php } ?>

                                     <!-- sample modal content -->
                                    <div id="addinfo" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-info">
                                                        <h5 class="modal-title" id="modalTitle">Add Additional Info</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                    </div>
                                                    <div class="modal-body">

                                                    <div class="row">
                                                        <div class="col-12">
                                                            <div class="">
                                                                    <?= validation_errors(); ?>
                                                                    <?php
                                                                    $attributes = array('class' => 'parsley-examples form-horizontal');
                                                                    echo form_open('Provident/provident_info_add', $attributes);
                                                                    ?>

                                                                    <input type="hidden" id="id" name="month">
                                                                    <input type="hidden" id="item" name="fy">
                                                                    <input type="hidden" name="school_id" value="<?= $this->uri->segment(3); ?>">

                                                                    <div class="form-group row">
                                                                        <label class="col-lg-4 col-form-label">Serial No.</label>
                                                                        <div class="col-lg-8">
                                                                        
                                                                        <input type="text" value=""  name="serial" class="form-control">

                                                                        </div>
                                                                    </div>

                                                                    <div class="form-group row">
                                                                        <label class="col-lg-4 col-form-label">Date</label>
                                                                        <div class="col-lg-8">
                                                                        
                                                                        <input type="date" value=""  name="cdate" class="form-control">

                                                                        </div>
                                                                    </div>


                                                                
                                                            </div>
                                                        </div>

                                                    </div>
                                                    <!-- end row -->
                                                        
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="submit" class="btn btn-info waves-effect waves-light">Save</button>
                                                    </div>
                                                    </form>
                                                </div>
                                                <!-- /.modal-content -->
                                            </div>
                                            <!-- /.modal-dialog -->
                                        </div>
                                        <!-- /.modal -->

                                        <?php if($opinfo){?>

                                        <!-- sample modal content -->
                                        <div id="editinfo" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-info">
                                                        <h5 class="modal-title" id="modalTitle">Update Additional Info</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                    </div>
                                                    <div class="modal-body">

                                                    <div class="row">
                                                        <div class="col-12">
                                                            <div class="">
                                                                    <?= validation_errors(); ?>
                                                                    <?php
                                                                    $attributes = array('class' => 'parsley-examples form-horizontal');
                                                                    echo form_open('Provident/provident_info_update', $attributes);
                                                                    ?>

                                                                    <input type="hidden" name="id" value="<?= $opinfo->id; ?>">


                                                                    <div class="form-group row">
                                                                        <label class="col-lg-4 col-form-label">Serial No.</label>
                                                                        <div class="col-lg-8">
                                                                        
                                                                        <input type="text" value="<?= $opinfo->serial; ?>"  name="serial" class="form-control">

                                                                        </div>
                                                                    </div>

                                                                    <div class="form-group row">
                                                                        <label class="col-lg-4 col-form-label">Date</label>
                                                                        <div class="col-lg-8">
                                                                        
                                                                        <input type="date" value="<?= $opinfo->cdate; ?>"  name="cdate" class="form-control">

                                                                        </div>
                                                                    </div>


                                                                
                                                            </div>
                                                        </div>

                                                    </div>
                                                    <!-- end row -->
                                                        
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="submit" class="btn btn-info waves-effect waves-light">Save</button>
                                                    </div>
                                                    </form>
                                                </div>
                                                <!-- /.modal-content -->
                                            </div>
                                            <!-- /.modal-dialog -->
                                        </div>
                                        <!-- /.modal -->
                                    <?php } ?>


                                    <script>
                                        function formatDecimal(input) {
                                            input.value = input.value
                                                .replace(/[^0-9.]/g, '')
                                                .replace(/(\..*?)\..*/g, '$1')
                                                .replace(/^(\d+)(\.\d{0,2}).*$/, '$1$2');
                                        }

                                        function computeNetAmount() {
                                            let loan = parseFloat(document.getElementById('loan_amount').value) || 0;
                                            let less = parseFloat(document.getElementById('less_loan_bal').value) || 0;

                                            let net = loan - less;

                                            document.getElementById('net_amount').value = net.toFixed(2);
                                        }

                                        document.addEventListener('DOMContentLoaded', function () {
                                            computeNetAmount();
                                        });
                                    </script>

   

 