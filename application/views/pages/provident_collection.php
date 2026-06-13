

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
                                    <a href="#" class="btn btn-primary waves-effect waves-light openModalBtn"data-toggle="modal" data-target="#myModal">Date</a><br />
                                    <span class="badge badge-success"><?= $this->session->cur_month; ?></span> <span class="badge badge-info"><?= $this->session->cur_fy; ?></span>
                              
                                    <div class="clearfix"></div>
                                    
                                </div>
                            </div>
                        </div>
                        <!-- end page title -->

                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body table-responsive">
                                        
                                    <h4 class="header-title mb-4"><?= $title; ?></h4>

                                    

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

                                         <table class="table table-sm mb-0">

                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Employee</th>
                                                    <th>Name</th>
                                                    <th>Type</th>
                                                    <th>Begining<br /> Principal</th>
                                                    <th>Effective</th>
                                                    <th>Termination</th>
                                                    <th>Deduction</th>
                                                    <th>Principal</th>
                                                    <th>Interest</th>
                                                    <th>End Principal</th>
                                                </tr>
                                            </thead>

                                            <tbody>
                                                <?php
                                                // ==========================
                                                // PSU List (dedCode = 0007)
                                                // ==========================
                                                $psu = $this->Common->one_cond_select_ob(
                                                    'payroll_empdeductions',
                                                    'dedID,IDNumber,fName,mName,lName,principalAmount,effectMonthFrom,effectYearFrom,effectMonthTo,effectYearTo,dedAmount',
                                                    'dedCode','0007',
                                                    'lName','ASC'
                                                );

                                                $ivy       = 1;
                                                $ivan      = 0;
                                                $ic        = 0;
                                                $ivykate   = 0;
                                                $ivankyle  = 0;
                                                $ivyclaire = 0;

                                                // ✅ Dynamic cutoff (END OF CALCULATION)
                                                $cutoffMonthNum = (int)($this->session->cur_month ?? 0); // 1..12
                                                $cutoffYear     = (int)($this->session->cur_fy ?? 0);

                                                if ($cutoffMonthNum < 1 || $cutoffMonthNum > 12 || $cutoffYear <= 0) {
                                                    $cutoffMonthNum = (int)date('n');
                                                    $cutoffYear     = (int)date('Y');
                                                }

                                                // ✅ SQL month-number expression that supports "February", "Feb", "02", "2"
                                                $monthNumExpr = "
                                                CASE
                                                WHEN TRIM(dedMonth) REGEXP '^[0-9]{1,2}$' THEN CAST(TRIM(dedMonth) AS UNSIGNED)

                                                WHEN LOWER(TRIM(dedMonth)) IN ('jan','january') THEN 1
                                                WHEN LOWER(TRIM(dedMonth)) IN ('feb','february') THEN 2
                                                WHEN LOWER(TRIM(dedMonth)) IN ('mar','march') THEN 3
                                                WHEN LOWER(TRIM(dedMonth)) IN ('apr','april') THEN 4
                                                WHEN LOWER(TRIM(dedMonth)) IN ('may') THEN 5
                                                WHEN LOWER(TRIM(dedMonth)) IN ('jun','june') THEN 6
                                                WHEN LOWER(TRIM(dedMonth)) IN ('jul','july') THEN 7
                                                WHEN LOWER(TRIM(dedMonth)) IN ('aug','august') THEN 8
                                                WHEN LOWER(TRIM(dedMonth)) IN ('sep','sept','september') THEN 9
                                                WHEN LOWER(TRIM(dedMonth)) IN ('oct','october') THEN 10
                                                WHEN LOWER(TRIM(dedMonth)) IN ('nov','november') THEN 11
                                                WHEN LOWER(TRIM(dedMonth)) IN ('dec','december') THEN 12
                                                ELSE 0
                                                END
                                                ";
                                                ?>

                                                <?php foreach($psu as $row): ?>

                                                <?php
                                                // ==========================
                                                // Pull payments UP TO cutoff (inclusive)
                                                // ==========================
                                                $this->db->reset_query();

                                                $payment = $this->db
                                                    ->from('payroll_empdeductions_monthly')
                                                    ->where('IDNumber', $row->IDNumber)
                                                    ->where('dedCode', '0007')
                                                    ->where('effectYearTo', $row->effectYearTo)
                                                    ->where('effectMonthTo', $row->effectMonthTo)
                                                    
                                                    ->where("(dedYear < {$cutoffYear} OR (dedYear = {$cutoffYear} AND ({$monthNumExpr}) <= {$cutoffMonthNum}))", null, false)
                                                    ->order_by('dedYear', 'ASC')
                                                    ->order_by("({$monthNumExpr})", 'ASC', false)
                                                    ->get()
                                                    ->result();

                                                // ==========================
                                                // Ledger computation
                                                // ==========================
                                                $balance = (float) $row->principalAmount;
                                                $rate    = 0.005;

                                                $prevInterestBal = 0;

                                                $ledgerRow = $this->Common->one_cond_row('provident_ledger', 'dedID', $row->dedID);

                                                $lastLedger = [
                                                    'principalPaid'         => 0,
                                                    'interestPaid'          => 0,
                                                    'paymentAmt'            => 0,
                                                    'newBalance'            => round($balance, 2),
                                                    'outstandingInterest'   => round($prevInterestBal, 2),
                                                    'outstandingTotalBal'   => round($balance + $prevInterestBal, 2),
                                                ];

                                                if (!empty($payment)) {

                                                    foreach ($payment as $p) {

                                                        $paymentAmt = round((float)($p->dedAmount ?? 0), 2);

                                                        $interest = round($balance * $rate, 2);
                                                        $paymentMadeInterest = $interest;

                                                        $principalPaid = round($paymentAmt - $paymentMadeInterest, 2);
                                                        if ($principalPaid < 0) $principalPaid = 0;

                                                        if ($principalPaid > $balance) {
                                                            $principalPaid = $balance;
                                                            $paymentAmt = round($paymentMadeInterest + $principalPaid, 2);
                                                        }

                                                        $newBalance = round($balance - $principalPaid, 2);

                                                        $arrears = isset($ledgerRow->arrears) ? round((float)$ledgerRow->arrears, 2) : 0.00;
                                                        $reverse = isset($ledgerRow->reverse_int) ? round((float)$ledgerRow->reverse_int, 2) : 0.00;

                                                        $outstandingInterest = round($prevInterestBal - $paymentMadeInterest + $arrears - $reverse, 2);
                                                        if ($outstandingInterest < 0) $outstandingInterest = 0;

                                                        $outstandingTotalBal = round($newBalance + $outstandingInterest + $arrears - $reverse, 2);

                                                        $lastLedger = [
                                                            'principalPaid'       => $principalPaid,
                                                            'interestPaid'        => $paymentMadeInterest,
                                                            'paymentAmt'          => $paymentAmt,
                                                            'newBalance'          => $newBalance,
                                                            'outstandingInterest' => $outstandingInterest,
                                                            'outstandingTotalBal' => $outstandingTotalBal,
                                                        ];

                                                        $balance = $newBalance;
                                                        $prevInterestBal = $outstandingInterest;

                                                        if ($balance <= 0) break;
                                                    }
                                                }

                                                // ==========================
                                                // Totals
                                                // ==========================
                                                $ivan      += (float)$row->principalAmount;
                                                $ic        += (float)$row->dedAmount;
                                                $ivykate   += (float)$lastLedger['principalPaid'];
                                                $ivankyle  += (float)$lastLedger['interestPaid'];
                                                $ivyclaire += (float)$lastLedger['outstandingTotalBal'];
                                                ?>

                                                <tr>
                                                    <td><?= $ivy++; ?></td>
                                                    <td>
                                                        <a target="_blank" href="<?= base_url(); ?>Provident/loan_ledger/<?= $row->IDNumber; ?>/<?= $row->dedID; ?>">
                                                            <?= $row->IDNumber; ?>
                                                        </a>
                                                    </td>
                                                    <td>
                                                        <a target="_blank" href="<?= base_url(); ?>Provident/loan_ledger_full_term/<?= $row->IDNumber; ?>/<?= $row->dedID; ?>">
                                                            <?= strtoupper($row->lName); ?>, <?= strtoupper($row->fName); ?> <?= strtoupper($row->mName); ?>
                                                        </a>
                                                    </td>
                                                    <td>PSU</td>
                                                    <td><?= number_format((float)$row->principalAmount, 2); ?></td>
                                                    <td><?= $row->effectMonthFrom; ?>-<?= $row->effectYearFrom; ?></td>
                                                    <td><?= $row->effectMonthTo; ?>-<?= $row->effectYearTo; ?></td>
                                                    <td><?= number_format((float)$row->dedAmount, 2); ?></td>

                                                    <td><?= number_format((float)$lastLedger['principalPaid'], 2); ?></td>
                                                    <td><?= number_format((float)$lastLedger['interestPaid'], 2); ?></td>
                                                    <td><?= number_format((float)$lastLedger['outstandingTotalBal'], 2); ?></td>
                                                </tr>

                                                <?php endforeach; ?>

                                                <tr class="table-success">
                                                    <td></td>
                                                    <td><strong>SUB TOTAL</strong></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td><?= number_format($ivan, 2); ?></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td><?= number_format($ic, 2); ?></td>
                                                    <td><?= number_format($ivykate, 2); ?></td>
                                                    <td><?= number_format($ivankyle, 2); ?></td>
                                                    <td><?= number_format($ivyclaire, 2); ?></td>
                                                </tr>
                                                
                                                <?php
                                                    // ==========================
                                                    // IUS SUMMARY (ARRANGED)
                                                    // ==========================
                                                    $kate         = 0;
                                                    $kyle         = 0;
                                                    $ivykate01    = 0;
                                                    $ivankyle01   = 0;
                                                    $ivyclaire01  = 0;

                                                    $cutoffMonthNum = (int)($this->session->cur_month ?? 0); // 1..12
                                                    $cutoffFy       = (int)($this->session->cur_fy ?? 0);

                                                    if ($cutoffMonthNum < 1 || $cutoffMonthNum > 12 || $cutoffFy <= 0) {
                                                        $cutoffMonthNum = (int)date('n');
                                                        $cutoffFy       = (int)date('Y');
                                                    }

                                                    $ius = $this->Common->two_join_one_cond_not_gb(
                                                        'provident_implementing',
                                                        'hris_staff',
                                                        'b.FirstName,b.MiddleName,b.LastName,b.IDNumber,a.*',
                                                        'a.employee_no = b.IDNumber',
                                                        'a.stat', 0,
                                                        'b.LastName', 'asc'
                                                    );

                                                    foreach ($ius as $row) {

                                                        $empNo     = (string) $row->employee_no;
                                                        $principal = (float)  $row->principal;
                                                        $deduction = (float)  $row->deduction;

                                                        $this->db->reset_query();
                                                        $payment = $this->db
                                                            ->where('IDNumber', $empNo)
                                                            ->where("(fy < {$cutoffFy} OR (fy = {$cutoffFy} AND month <= {$cutoffMonthNum}))", null, false)
                                                            ->order_by('fy', 'ASC')
                                                            ->order_by('month', 'ASC')
                                                            ->get('provident_implementing_payment')
                                                            ->result();

                                                        $ledgerRow = $this->Common->one_cond_row('provident_ledger', 'dedID', $row->id);

                                                        $arrears = isset($ledgerRow->arrears) ? round((float)$ledgerRow->arrears, 2) : 0.00;
                                                        $reverse = isset($ledgerRow->reverse_int) ? round((float)$ledgerRow->reverse_int, 2) : 0.00;

                                                        $rate = 0.005;

                                                        $balance         = $principal;
                                                        $prevInterestBal = 0;

                                                        $lastLedger = [
                                                            'principalPaid'       => 0,
                                                            'interestPaid'        => 0,
                                                            'paymentAmt'          => 0,
                                                            'newBalance'          => round($balance, 2),
                                                            'outstandingInterest' => round($prevInterestBal, 2),
                                                            'outstandingTotalBal' => round($balance + $prevInterestBal, 2),
                                                        ];

                                                        if (!empty($payment)) {
                                                            foreach ($payment as $p) {

                                                                $pFy    = (int)($p->fy ?? 0);
                                                                $pMonth = (int)($p->month ?? 0);
                                                                if ($pFy > $cutoffFy || ($pFy === $cutoffFy && $pMonth > $cutoffMonthNum)) {
                                                                    break;
                                                                }

                                                                $paymentAmt = round((float)($p->amount ?? 0), 2);

                                                                $interestPaid = round($balance * $rate, 2);

                                                                $principalPaid = round($paymentAmt - $interestPaid, 2);
                                                                if ($principalPaid < 0) $principalPaid = 0;

                                                                if ($principalPaid > $balance) {
                                                                    $principalPaid = $balance;
                                                                    $paymentAmt = round($interestPaid + $principalPaid, 2);
                                                                }

                                                                $newBalance = round($balance - $principalPaid, 2);

                                                                $outstandingInterest = round($prevInterestBal - $interestPaid + $arrears - $reverse, 2);
                                                                if ($outstandingInterest < 0) $outstandingInterest = 0;

                                                                $outstandingTotalBal = round($newBalance + $outstandingInterest + $arrears - $reverse, 2);

                                                                $lastLedger = [
                                                                    'principalPaid'       => $principalPaid,
                                                                    'interestPaid'        => $interestPaid,
                                                                    'paymentAmt'          => $paymentAmt,
                                                                    'newBalance'          => $newBalance,
                                                                    'outstandingInterest' => $outstandingInterest,
                                                                    'outstandingTotalBal' => $outstandingTotalBal,
                                                                ];

                                                                $balance = $newBalance;
                                                                $prevInterestBal = $outstandingInterest;

                                                                if ($balance <= 0) break;
                                                            }
                                                        }

                                                        $kate       += $principal;
                                                        $kyle       += $deduction;
                                                        $ivykate01  += (float)$lastLedger['principalPaid'];
                                                        $ivankyle01 += (float)$lastLedger['interestPaid'];
                                                        $ivyclaire01 += (float)$lastLedger['outstandingTotalBal'];
                                                    ?>
                                                    <tr>
                                                        <td><?= $ivy++; ?></td>
                                                        <td>
                                                            <a target="_blank" href="<?= base_url(); ?>Provident/loan_ledger_implementing/<?= $empNo; ?>/<?= $row->id; ?>">
                                                                <?= $empNo; ?>
                                                            </a>
                                                        </td>
                                                        <td>
                                                            <a target="_blank" href="<?= base_url(); ?>Provident/loan_ledger_full_term_implementing/<?= $empNo; ?>/<?= $row->id; ?>">
                                                                <?= strtoupper($row->LastName); ?>, <?= strtoupper($row->FirstName); ?> <?= strtoupper($row->MiddleName); ?>
                                                            </a>
                                                        </td>
                                                        <td>IUS</td>
                                                        <td><?= number_format($principal, 2); ?></td>
                                                        <td><?= $row->effect_from_month; ?> <?= $row->effect_from_year; ?></td>
                                                        <td><?= $row->effect_to_month; ?> <?= $row->effect_to_year; ?></td>
                                                        <td><?= number_format($deduction, 2); ?></td>

                                                        <td><?= number_format((float)$lastLedger['principalPaid'], 2); ?></td>
                                                        <td><?= number_format((float)$lastLedger['interestPaid'], 2); ?></td>
                                                        <td><?= number_format((float)$lastLedger['outstandingTotalBal'], 2); ?></td>
                                                    </tr>
                                                    <?php } ?>

                                                    <tr class="table-success">
                                                        <td></td>
                                                        <td><strong>SUB TOTAL</strong></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td><?= number_format($kate, 2); ?></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td><?= number_format($kyle, 2); ?></td>
                                                        <td><?= number_format($ivykate01, 2); ?></td>
                                                        <td><?= number_format($ivankyle01, 2); ?></td>
                                                        <td><?= number_format($ivyclaire01, 2); ?></td>
                                                    </tr>

                                                    <tr class="table-warning">
                                                        <td></td>
                                                        <td><strong>GRAND TOTAL</strong></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td><?= number_format($kate + $ivan, 2); ?></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td><?= number_format($kyle + $ic, 2); ?></td>
                                                        <td><?= number_format($ivykate01 + $ivykate, 2); ?></td>
                                                        <td><?= number_format($ivankyle01 + $ivankyle, 2); ?></td>
                                                        <td><?= number_format($ivyclaire01 + $ivyclaire, 2); ?></td>
                                                    </tr>
                                                
                                            </tbody>
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

                <!-- sample modal content -->
                                        <div id="myModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content modal-lg">
                                                    <div class="modal-header bg-info">
                                                        <h5 class="modal-title" id="modalTitle">Select Date</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                    </div>
                                                    <div class="modal-body">

                                                    <div class="row">
                                                        <div class="col-12">
                                                            <div class="">
                                                                    <?= validation_errors(); ?>
                                                                    <?php
                                                                    $attributes = array('class' => 'parsley-examples form-horizontal');
                                                                    echo form_open('Provident/change_fy_and_m', $attributes);
                                                                    ?>

                                                                    


                                                                    <input type="hidden" class="form-control" value="0007" name="deduction_code">


                                                                    <div class="form-group row">
                                                                        <label class="col-lg-4 col-form-label">Month and Year</label>
                                                                        <div class="col-lg-4">
                                                                            <label class="col-lg-4 col-form-label"></label>
                                                                            <label class="col-lg-4 col-form-label">Month</label>
                                                                            <select name="month" class="form-control" required>
                                                                                <?php
                                                                                date_default_timezone_set('Asia/Manila');
                                                                                $months = [
                                                                                    '01' => 'January',  '02' => 'February', '03' => 'March',    '04' => 'April',
                                                                                    '05' => 'May',      '06' => 'June',     '07' => 'July',     '08' => 'August',
                                                                                    '09' => 'September','10' => 'October',  '11' => 'November', '12' => 'December'
                                                                                ];

                                                                                foreach ($months as $num => $name) {
                                                                                    echo "<option ";
                                                                                    if(date("m") == $num){echo " selected ";}
                                                                                    echo " value=\"$num\">$name</option>";
                                                                                }
                                                                                ?>
                                                                                </select>
                                                                        </div>
                                                                        <div class="col-lg-4">
                                                                            <label class="col-lg-4 col-form-label">Year</label>
                                                                                <?php
                                                                                $currentYear = date("Y");
                                                                                $fiscalStartYear = $currentYear - 10;
                                                                                $fiscalEndYear = $currentYear + 30;

                                                                                ?>
                                                                                <select name="fy" class="form-control" required>
                                                                                    <option value="" disabled selected>Select Fiscal Year</option>
                                                                                    <?php for ($year = $fiscalStartYear; $year <= $fiscalEndYear; $year++) { ?>
                                                                                    <option <?php if(date('Y') == $year){echo ' selected ';}?> value="<?= $year; ?>"><?= $year; ?></option>
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
                                                        <button type="submit" class="btn btn-info waves-effect waves-light">Submit</button>
                                                    </div>
                                                    </form>
                                                </div>
                                                <!-- /.modal-content -->
                                            </div>
                                            <!-- /.modal-dialog -->
                                        </div>
                                        <!-- /.modal -->

                                        

                            

   

 