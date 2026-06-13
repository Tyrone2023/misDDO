

            <!-- ============================================================== -->
            <!-- Start Page Content here -->
            <!-- ============================================================== -->
             <?php 
                $monthFrom = $loan->effect_from_month;
                $yearFrom  = $loan->effect_from_year;

                $monthTo   = $loan->effect_to_month;   // March
                $yearTo    = $loan->effect_to_year;

                $totalMonths = (($yearTo - $yearFrom) * 12) + ($monthTo - $monthFrom) + 1;
                $ivankyle  = intdiv($totalMonths, 12);
             ?>
         
            <div class="content-page">
                <div class="content">

                    <!-- Start Content-->
                    <div class="container-fluid">

                        <!-- start page title -->
                        <div class="row">
                            <div class="col-12">
                                <div class="page-title-box">
                                    <a href="#" class="btn btn-primary waves-effect waves-light openModalBtn"data-toggle="modal" data-target="#myModal">Add Payment</a>
                                    
                              
                                    <div class="clearfix"></div>
                                    
                                </div>
                            </div>
                        </div>
                        <!-- end page title -->

                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body table-responsive">
                                        <div class="clearfix">
                                            <div class="float-left">
                                                <h4 class="text-left">
                                                    <?= strtoupper($emp->LastName); ?>, <?= strtoupper($emp->FirstName); ?> <?= !empty($emp->MiddleName) ? strtoupper(mb_substr($emp->MiddleName, 0, 1)) . '.' : '' ?><br />
                                                    
                                                </h4>
                                                <h6>Employee # : <?= $this->uri->segment(3); ?><br /></h6>
                                                <h6>School Name : <?= $school->name; ?><br /></h6>
                                                <h6>Loan Period: <?= $ivankyle; ?> Years</h6>

                                            </div>
                                            <div class="float-right">
                                                <h5>Pricipal Amount : &#8369; <?= number_format($loan->principal, 2); ?></h5>
                                                <h6>Scheduled Payment: &#8369; <?= number_format($loan->deduction, 2); ?></h6>
                                            </div>
                                        </div>
                                        <hr>
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

                                         <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">

                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Month</th>
                                                    <th>Year</th>
                                                    <th>Amount</th>
                                                    <th>Remarks</th>
                                                    <?php if($this->session->position == 'provident'){?>
                                                    <th>Action</th>
                                                    <?php } ?>
                                                </tr>
                                            </thead>

                                            <tbody>
                                                <?php $c=1; foreach($data as $row){
                                                ?>
                                                <tr>
                                                    <td><?= $c++; ?></td>
                                                    <td>
                                                        <?php 
                                                            $monthNum = (int) $row->month;
                                                            $monthWord = date("F", mktime(0, 0, 0, $monthNum, 1));
                                                            echo $monthWord ;
                                                        ?>
                                                    </td>
                                                    <td><?= $row->fy; ?></td>
                                                    <td><?= number_format($row->amount, 2); ?></td>
                                                    <td><?= $row->remarks; ?></td>
                                                    <?php if($this->session->position == 'provident'){?>
                                                    <td class="text-center">
                                                        <a onclick="return confirm('Are you sure?')" class="text-danger tooltips" data-placement="top" data-toggle="tooltip" data-original-title="Delete" href="<?= base_url(); ?>Provident/provident_implementing_delete/<?= $row->id; ?>"><i class="far fa-trash-alt"></i></a> &nbsp; &nbsp;
                                                    </td>
                                                    <?php } ?>
                                                </tr>
                                                <?php } ?>
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
                                                        <h5 class="modal-title" id="modalTitle">Payment</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                    </div>
                                                    <div class="modal-body">

                                                    <div class="row">
                                                        <div class="col-12">
                                                            <div class="">
                                                                    <?= validation_errors(); ?>
                                                                    <?php
                                                                    $attributes = array('class' => 'parsley-examples form-horizontal');
                                                                    echo form_open('Provident/provident_imp_payment_insert', $attributes);
                                                                    ?>

                                                                    <input type="hidden" name="IDNumber" value="<?= $this->uri->segment(3); ?>">
                                                                    <input type="hidden" name="loan_id" value="<?= $this->uri->segment(2); ?>">
                                                                    <input type="hidden" name="school_id" value="<?= $school->school_id; ?>">

                                                                    <div class="form-group row">
                                                                        <label class="col-lg-4 col-form-label">Amount</label>
                                                                        <div class="col-lg-8">
                                                                        
                                                                        <input type="text" value="<?=$loan->deduction; ?>" oninput="this.value=this.value.replace(/[^0-9.]/g,'').replace(/(\..*)\./g,'$1').replace(/^(\d+)(\.\d{0,2})?.*$/,'$1$2')" name="amount" class="form-control">

                                                                        </div>
                                                                    </div>


                                                                    <div class="form-group row">
                                                                        <label class="col-lg-4 col-form-label">Date</label>
                                                                        <div class="col-lg-4">
                                                                            <label class="col-lg-4 col-form-label"></label>
                                                                            <label class="col-lg-4 col-form-label">Month</label>
                                                                            <select name="month" class="form-control">
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
                                                                                $fiscalStartYear = $currentYear - 1;
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

                                                                        
                                                                    

                                                                    

                                                                    

                                                                    <div class="form-group row">
                                                                        <label class="col-lg-4 col-form-label">Remarks</label>
                                                                        <div class="col-lg-8">
                                                                        <textarea class="form-control" name="remarks" rows="3" id="example-textarea"></textarea>

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

                                        

                            

   

 