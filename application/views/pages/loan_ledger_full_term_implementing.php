

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

                        <?php 
                            $monthFrom = $data->effect_from_month;
                            $yearFrom  = $data->effect_from_year;

                            $monthTo   = $data->effect_to_month;   // March
                            $yearTo    = $data->effect_to_year;

                            $totalMonths = (($yearTo - $yearFrom) * 12) + ($monthTo - $monthFrom) + 1;
                            $ivankyle  = intdiv($totalMonths, 12);


                            $principal = $data->principal;
                            $rate = 0.005;      // 0.5% monthly
                            $months = $totalMonths;

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
                                    <div class="card-body">
                                        <div class="clearfix">
                                            <div class="float-left">
                                                <h4 class="text-left">PROVIDENT FUND <br />STATEMENT OF ACCOUNT</h4>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="row">
                                            <div class="col-md-12">

                                                <div class="float-left mt-4">
                                                    <h4>Borrower's Name: <br /><strong><?= $staff->FirstName; ?> <?= $staff->MiddleName; ?> <?= $staff->LastName; ?> </strong> </h4><br />
                                                    <h6><strong>Station code: </strong>  </h6>
                                                    <h6><strong>District/School </strong>  <?= $school->name; ?></h6>
                                                    <h6><strong>Loan Amount: </strong>&#8369; <?= $data->principal; ?> </h6>
                                                    <h6><strong>Interest: </strong>&#8369; <?= number_format($totalInterest, 2); ?></h6>
                                                    <h6><strong>Scheduled Payment: </strong>&#8369; <?= number_format($monthlyPayment, 2) . PHP_EOL; ?></h6>
                                                    <h6><strong>Loan Period: </strong>  <?= $ivankyle; ?> Years</h6>
                                                </div>
                                                <!-- <div class="float-right mt-4">
                                                    <p><strong>Co-Maker: </strong> </p>
                                                    <p class="mt-2"><strong>Station code: </strong>  </p>
                                                    <p class="mt-2"><strong>Scheduled Payment: </strong>  </p>
                                                </div> -->
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
                                                            </tr>
                                                        </thead>
                                                        <tbody class="text-center">
                                                            <?php
                                                            $c = 1;

                                                            // --- Starting values ---
                                                            $balance = (float) $data->principal;     // principal
                                                            $rate = 0.005;                                // monthly interest rate (0.5%)
                                                            $prevInterestBal = round((float) $totalInterest, 2);

                                                            // --- Term: years -> months ---
                                                            $loanMonths = (int) $ivankyle * 12;


                                                            // --- Scheduled payment for months with no DB row ---
                                                            //$scheduledPayment = 966.64; // <-- change this as needed

                                                            // Ensure payments are in a clean numeric index order
                                                            $payments = array_values($payment);
                                                            $paymentCount = count($payments);

                                                            for ($i = 0; $i < $loanMonths; $i++) {

                                                                // Use DB row if present; otherwise null (meaning "assumed scheduled payment")
                                                                $row = ($i < $paymentCount) ? $payments[$i] : null;

                                                                // Payment amount: actual if exists, else scheduled
                                                                $paymentAmt = $row ? round((float) $row->amount, 2) : round((float) $monthlyPayment, 2);

                                                                // Ledger values only available if we have dedID
                                                                $arrears = 0.00;
                                                                $reverse = 0.00;

                                                                if ($row && !empty($row->dedID)) {
                                                                    $ivan = $this->Common->one_cond_row('provident_ledger', 'dedID', $row->dedID);
                                                                    $arrears = isset($ivan->arrears) ? round((float) $ivan->arrears, 2) : 0.00;
                                                                    $reverse = isset($ivan->reverse_int) ? round((float) $ivan->reverse_int, 2) : 0.00;
                                                                }

                                                                // Compute interest this month
                                                                $interest = round($balance * $rate, 2);

                                                                // Payment goes to interest first
                                                                $paymentMadeInterest = min($paymentAmt, $interest);

                                                                // Remaining goes to principal
                                                                $principalPaid = round($paymentAmt - $paymentMadeInterest, 2);
                                                                if ($principalPaid < 0) $principalPaid = 0;

                                                                // Cap principal so balance never goes negative
                                                                if ($principalPaid > $balance) {
                                                                    $principalPaid = $balance;
                                                                    $paymentAmt = round($paymentMadeInterest + $principalPaid, 2);
                                                                }

                                                                // New principal balance
                                                                $newBalance = round($balance - $principalPaid, 2);

                                                                /**
                                                                * Outstanding interest logic:
                                                                * Your original formula:
                                                                *   outstandingInterest = prevInterestBal - paymentMadeInterest + arrears - reverse
                                                                *
                                                                * NOTE: When we "assume" payments (no DB row), we still treat it as paid
                                                                * because we used scheduledPayment and computed paymentMadeInterest.
                                                                */
                                                                $outstandingInterest = round($prevInterestBal - $paymentMadeInterest + $arrears - $reverse, 2);
                                                                if ($outstandingInterest < 0) $outstandingInterest = 0;

                                                                // Total outstanding balance
                                                                $outstandingTotalBal = round($newBalance + $outstandingInterest + $arrears - $reverse, 2);

                                                                // Date display: DB date if exists, else blank (or show "Month X")
                                                                $pdateText = '';
                                                                if ($row && !empty($row->PDate)) {
                                                                    $pdateText = date('d-M-y', strtotime($row->PDate));
                                                                } else {
                                                                    // Optional: show Month number instead of blank
                                                                    // $pdateText = 'Month ' . ($i + 1);
                                                                    $pdateText = '';
                                                                }

                                                                // Optional label to show whether this row is actual or assumed
                                                                $isAssumed = ($row === null);
                                                            ?>
                                                            <tr>
                                                                <td><?= $c++; ?></td>
                                                                <td>
                                                                    <?php 
                                                                        $monthNum = $row->month ?? null;

                                                                        if (!is_numeric($monthNum) || (int)$monthNum < 1 || (int)$monthNum > 12) {
                                                                            // fallback text when month is empty/invalid
                                                                            echo '— ' . ($row->fy ?? '');
                                                                        } else {
                                                                            $monthWord = date("M", mktime(0, 0, 0, (int)$monthNum, 1));
                                                                            echo $monthWord . ' ' . ($row->fy ?? '');
                                                                        }
                                                                    ?>
                                                                </td>

                                                                <td><?= number_format($principalPaid, 2); ?></td>
                                                                <td><?= number_format($paymentMadeInterest, 2); ?></td>
                                                                <td>
                                                                    <?= number_format($paymentAmt, 2); ?>
                                                                    <?php if ($isAssumed): ?>
                                                                        <small class="text-muted">(Scheduled)</small>
                                                                    <?php endif; ?>
                                                                </td>

                                                                <td><?= number_format($newBalance, 2); ?></td>
                                                                <td><?= number_format($arrears, 2); ?></td>
                                                                <td><?= number_format($reverse, 2); ?></td>
                                                                <td><?= number_format($outstandingInterest, 2); ?></td>
                                                                <td><?= number_format($outstandingTotalBal, 2); ?></td>
                                                            </tr>
                                                            <?php
                                                                // Carry forward for next month
                                                                $balance = $newBalance;
                                                                $prevInterestBal = $outstandingInterest;

                                                                // Stop if fully paid (remove this if you still want to show ALL term rows)
                                                                if ($balance <= 0) break;
                                                            }
                                                            ?>
                                                        </tbody>
                                                    </table>
                                                </div>
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

               
               

   

 