

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
                                    <?php if($this->session->position == 'provident'){?>
                                    <a href="#" class="btn btn-primary waves-effect waves-light openModalBtn"data-toggle="modal" data-target="#myModal">Add New</a>
                                    <?php } ?>
                                    <a href="#" class="btn btn-purple waves-effect waves-light openModalBtn"data-toggle="modal" data-target="#ivykate">Generate Report</a>
                                    <div class="page-title-right">
                                        <ol class="breadcrumb p-0 m-0">
                                            <li class="breadcrumb-item"><a href="#" data-toggle="modal" data-target="#renren">Current Fiscal Year : <span class="badge badge-success"><?= $this->session->cur_fy; ?></span></a></li>
                                        </ol>
                                    </div>
                              
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

                                         <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">

                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Employee No.</th>
                                                    <th>Last Name</th>
                                                    <th>First Name</th>
                                                    <th>Middle Name</th>
                                                    <th>Deduction Code</th>
                                                    <th>Effectivity(from)<br /> Month</th>
                                                    <th>Effectivity(from)<br /> Year</th>
                                                    <th>Effectivity(to)<br /> Month</th>
                                                    <th>Effectivity(to)<br /> Year</th>
                                                    <th>Deduction<br /> Amount</th>
                                                    <th>Principal<br /> Amount</th>
                                                    <th>Loan Approved<br /> Date</th>
                                                    <th>Remarks</th>
                                                    <th>Date Verified<br />(Division Verifier)</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>

                                            <tbody>
                                                <?php $c=1; foreach($data as $row){?>
                                                <tr>
                                                    <td><?= $c++; ?></td>
                                                    <td><?= $row->employee_no; ?></td>
                                                    <td><?= strtoupper($row->LastName); ?></td>
                                                    <td><?= strtoupper($row->FirstName); ?></td>
                                                    <td><?= strtoupper($row->MiddleName); ?></td>
                                                    <td><?= $row->deduction_code; ?></td>
                                                    <td><?= $row->effect_from_month; ?></td>
                                                    <td><?= $row->effect_from_year; ?></td>
                                                    <td><?= $row->effect_to_month; ?></td>
                                                    <td><?= $row->effect_to_year; ?></td>
                                                    <td><?= $row->deduction; ?></td>
                                                    <td><?= $row->principal; ?></td>
                                                    <td><?= $row->approved_date; ?></td>
                                                    <td><?= $row->remarks; ?></td>
                                                    <td><?= $row->verified; ?></td>
                                                    <td class="text-center">
                                                        <?php if($row->stat == 0){?>
                                                        <a class="text-success tooltips" data-placement="top" data-toggle="tooltip" data-original-title="Update" href="<?= base_url(); ?>Provident/provident_update/<?= $row->id; ?>"><i class=" fas fa-edit"></i></a> &nbsp; &nbsp;
                                                        <a onclick="return confirm('Are you sure?')" class="text-danger tooltips" data-placement="top" data-toggle="tooltip" data-original-title="Delete" href="<?= base_url(); ?>Provident/provident_delete/<?= $row->id; ?>"><i class="far fa-trash-alt"></i></a>
                                                        <?php } ?>
                                                    </td>
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
                                                        <h5 class="modal-title" id="modalTitle">Add New</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                    </div>
                                                    <div class="modal-body">

                                                    <div class="row">
                                                        <div class="col-12">
                                                            <div class="">
                                                                    <?= validation_errors(); ?>
                                                                    <?php
                                                                    $attributes = array('class' => 'parsley-examples form-horizontal');
                                                                    echo form_open('Provident/provident_insert', $attributes);
                                                                    ?>

                                                                    <div class="form-group row">
                                                                        <label class="col-lg-4 col-form-label">Fullname</label>
                                                                        <div class="col-lg-8">
                                                                        
                                                                            <select name="employee_no" class="form-control" data-toggle="select2" required>
                                                                                <option value="" disabled selected>Select Employee</option>
                                                                                <?php foreach($staff as $row){?>
                                                                                <option value="<?= $row->IDNumber; ?>"><?= strtoupper($row->LastName); ?>, <?= strtoupper($row->FirstName); ?> <?= !empty($row->MiddleName) ? strtoupper(mb_substr($row->MiddleName, 0, 1)) . '.' : '' ?> </option>
                                                                                <?php } ?>
                                                                            </select>
                                                                        </div>
                                                                    </div>

                                                                        
                                                                    <input type="hidden" class="form-control" value="0007" name="deduction_code">

                                                                    <div class="form-group row">
                                                                        <label class="col-lg-4 col-form-label">Effectivity(from)</label>
                                                                        <div class="col-lg-4">
                                                                            <label class="col-lg-4 col-form-label"></label>
                                                                            <label class="col-lg-4 col-form-label">Month</label>
                                                                            <select name="effect_from_month" class="form-control">
                                                                                <?php
                                                                                $months = [
                                                                                    '01' => 'January',  '02' => 'February', '03' => 'March',    '04' => 'April',
                                                                                    '05' => 'May',      '06' => 'June',     '07' => 'July',     '08' => 'August',
                                                                                    '09' => 'September','10' => 'October',  '11' => 'November', '12' => 'December'
                                                                                ];

                                                                                foreach ($months as $num => $name) {
                                                                                    echo "<option value=\"$num\">$name</option>";
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
                                                                                <select name="effect_from_year" class="form-control" required>
                                                                                    <option value="" disabled selected>Select Fiscal Year</option>
                                                                                    <?php for ($year = $fiscalStartYear; $year <= $fiscalEndYear; $year++) { ?>
                                                                                    <option <?php if(date('Y') == $year){echo ' selected ';}?> value="<?= $year; ?>"><?= $year; ?></option>
                                                                                    <?php } ?>
                                                                                </select>
                                                                        </div>
                                                                    </div>

                                                                    <div class="form-group row">
                                                                        <label class="col-lg-4 col-form-label">Effectivity(To)</label>
                                                                        <div class="col-lg-4">
                                                                            <label class="col-lg-4 col-form-label"></label>
                                                                            <label class="col-lg-4 col-form-label">Month</label>
                                                                            <select name="effect_to_month" class="form-control">
                                                                                <?php
                                                                                foreach ($months as $num => $name) {
                                                                                    echo "<option value=\"$num\">$name</option>";
                                                                                }
                                                                                ?>
                                                                                </select>
                                                                        </div>
                                                                        <div class="col-lg-4">
                                                                            <label class="col-lg-4 col-form-label">Year</label>
                                                                     
                                                                                <select name="effect_to_year" class="form-control" required>
                                                                                    <option value="" disabled selected>Select Fiscal Year</option>
                                                                                    <?php for ($year = $fiscalStartYear; $year <= $fiscalEndYear; $year++) { ?>
                                                                                    <option <?php if(date('Y') == $year){echo ' selected ';}?> value="<?= $year; ?>"><?= $year; ?></option>
                                                                                    <?php } ?>
                                                                                </select>
                                                                        </div>
                                                                    </div>

                                                                    <div class="form-group row">
                                                                        <label class="col-lg-4 col-form-label">Deduction Amount</label>
                                                                        <div class="col-lg-8">
                                                                        
                                                                        <input type="text" oninput="formatDecimal(this);" value="" name="deduction" class="form-control">

                                                                        </div>
                                                                    </div>

                                                                    <div class="form-group row">
                                                                        <label class="col-lg-4 col-form-label">Pricipal Amount</label>
                                                                        <div class="col-lg-8">
                                                                        
                                                                        <input type="text" oninput="formatDecimal(this);" value="" name="principal" class="form-control">

                                                                        </div>
                                                                    </div>

                                                                    <div class="form-group row">
                                                                        <label class="col-lg-4 col-form-label">Loan Approved Date</label>
                                                                        <div class="col-lg-8">
                                                                        
                                                                        <input type="date" value="" name="approved_date" class="form-control">

                                                                        </div>
                                                                    </div>
                                                                    
                                                                    <div class="form-group row">
                                                                        <label class="col-lg-4 col-form-label">For Deletion of Old Amortization For Reloan Only</label>
                                                                        <div class="col-lg-8">
                                                                        
                                                                        <input type="text" oninput="formatDecimal(this);" value="" name="reloan" class="form-control">

                                                                        </div>
                                                                    </div>

                                                                    <div class="form-group row">
                                                                        <label class="col-lg-4 col-form-label">Remarks</label>
                                                                        <div class="col-lg-8">
                                                                        
                                                                        <input type="text" value="" name="remarks" class="form-control">

                                                                        </div>
                                                                    </div>

                                                                    <div class="form-group row">
                                                                        <label class="col-lg-4 col-form-label">Date Verified (Division Verifier)</label>
                                                                        <div class="col-lg-8">
                                                                        
                                                                        <input type="date" value="" name="verified" class="form-control">

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
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-success">
                                                        <h5 class="modal-title text-white" id="myModalLabel">Change Fiscal Year</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                    </div>
                                                    <div class="modal-body">
                                                    <form action="<?= base_url('Pages/change_fy') ?>" method="post">
                                                        <div class="form-group row">
                                                            <div class="col-lg-12">
                                                                <select name="new_fy" class="form-control" onchange="this.form.submit()">
                                                                <option disabled selected>Change FY</option>
                                                                <?php for ($y = 2023; $y <= 2030; $y++) : ?>
                                                                    <option value="<?= $y ?>" <?= ($this->session->userdata('cur_fy') == $y) ? 'selected' : '' ?>>
                                                                        <?= $y ?>
                                                                    </option>
                                                                <?php endfor; ?>
                                                            </select>
                                                            </div>
                                                        </div>
                                                    </form>
                                                    </div>
                                                </div>
                                                <!-- /.modal-content -->
                                            </div>
                                            <!-- /.modal-dialog -->
                                        </div>
                                        <!-- /.modal -->

                                        <!-- sample modal content -->
                                        <div id="ivykate" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-purple">
                                                        <h5 class="modal-title text-white" id="myModalLabel">Generate Report</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                    </div>
                                                    <div class="modal-body">
                                                    <form action="<?= base_url('Provident/provident_generate_report') ?>" method="post" target="_blank">
                                                        <div class="form-group row">
                                                            <div class="col-lg-12">
                                                                <select name="month" class="form-control">
                                                                                <?php
                                                                                $months = [
                                                                                    '01' => 'January',  '02' => 'February', '03' => 'March',    '04' => 'April',
                                                                                    '05' => 'May',      '06' => 'June',     '07' => 'July',     '08' => 'August',
                                                                                    '09' => 'September','10' => 'October',  '11' => 'November', '12' => 'December'
                                                                                ];

                                                                                foreach ($months as $num => $name) {
                                                                                    echo "<option ";
                                                                                    echo "value=\"$num\">$name</option>";
                                                                                }
                                                                                ?>
                                                                </select>
                                                            </div>
                                                        </div>


                                                        <div class="form-group row">
                                                            <div class="col-lg-12">
                                                                <select name="fy" class="form-control">
                                                                <option disabled selected>Year</option>
                                                                <?php for ($y = 2023; $y <= 2050; $y++) : ?>
                                                                    <option value="<?= $y ?>" <?= ($this->session->userdata('cur_fy') == $y) ? 'selected' : '' ?>>
                                                                        <?= $y ?>
                                                                    </option>
                                                                <?php endfor; ?>
                                                            </select>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="submit" class="btn btn-purple waves-effect waves-light">Generate</button>
                                                        </div>
                                                    </form>
                                                    </div>
                                                </div>
                                                <!-- /.modal-content -->
                                            </div>
                                            <!-- /.modal-dialog -->
                                        </div>
                                        <!-- /.modal -->



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


                            

   

 