

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

                                         <div class="row">
                                                        <div class="col-12">
                                                            <div class="">
                                                                    <?= validation_errors(); ?>
                                                                    <?php
                                                                    $attributes = array('class' => 'parsley-examples form-horizontal');
                                                                    echo form_open('Provident/provident_update', $attributes);
                                                                    ?>

                                                                    <input type="hidden" value="<?= $data->id; ?>" name="id">
                                                                    <div class="form-group row">
                                                                        <label class="col-lg-4 col-form-label">Fullname</label>
                                                                        <div class="col-lg-8">
                                                                        
                                                                            <select name="employee_no" class="form-control" data-toggle="select2" required>
                                                                                <option value="" disabled selected>Select Employee</option>
                                                                                <?php foreach($staff as $row){?>
                                                                                <option <?php if($data->employee_no == $row->IDNumber){echo " selected ";} ?> value="<?= $row->IDNumber; ?>"><?= strtoupper($row->LastName); ?>, <?= strtoupper($row->FirstName); ?> <?= !empty($row->MiddleName) ? strtoupper(mb_substr($row->MiddleName, 0, 1)) . '.' : '' ?> </option>
                                                                                <?php } ?>
                                                                            </select>
                                                                            <input type="hidden" class="form-control" value="<?= $data->deduction_code; ?>" name="deduction_code">
                                                                        </div>
                                                                    </div>

                                                                        
                                                                        


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
                                                                                    echo "<option ";
                                                                                    if($data->effect_from_month == $num){echo ' selected ';}
                                                                                    echo "value=\"$num\">$name</option>";
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
                                                                                    echo "<option";
                                                                                    if($data->effect_to_month == $num){echo ' selected ';}
                                                                                    echo " value=\"$num\">$name</option>";
                                                                                }
                                                                                ?>
                                                                                </select>
                                                                        </div>
                                                                        <div class="col-lg-4">
                                                                            <label class="col-lg-4 col-form-label">Year</label>
                                                                     
                                                                                <select name="effect_to_year" class="form-control" required>
                                                                                    <option value="" disabled selected>Select Fiscal Year</option>
                                                                                    <?php for ($year = $fiscalStartYear; $year <= $fiscalEndYear; $year++) { ?>
                                                                                    <option <?php if($data->effect_to_year == $year){echo ' selected ';}?> value="<?= $year; ?>"><?= $year; ?></option>
                                                                                    <?php } ?>
                                                                                </select>
                                                                        </div>
                                                                    </div>

                                                                    <div class="form-group row">
                                                                        <label class="col-lg-4 col-form-label">Deduction Amount</label>
                                                                        <div class="col-lg-8">
                                                                        
                                                                        <input type="text" value="<?= $data->deduction; ?>" name="deduction" class="form-control">

                                                                        </div>
                                                                    </div>

                                                                    <div class="form-group row">
                                                                        <label class="col-lg-4 col-form-label">Pricipal Amount</label>
                                                                        <div class="col-lg-8">
                                                                        
                                                                        <input type="text" value="<?= $data->principal; ?>" name="principal" class="form-control">

                                                                        </div>
                                                                    </div>

                                                                    <div class="form-group row">
                                                                        <label class="col-lg-4 col-form-label">Loan Approved Date</label>
                                                                        <div class="col-lg-8">
                                                                        
                                                                        <input type="date" value="<?= $data->approved_date; ?>" name="approved_date" class="form-control">

                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group row">
                                                                        <label class="col-lg-4 col-form-label">For Deletion of Old Amortization For Reloan Only</label>
                                                                        <div class="col-lg-8">
                                                                        
                                                                        <input type="text" oninput="formatDecimal(this);" value="<?= $data->reloan; ?>" name="reloan" class="form-control">

                                                                        </div>
                                                                    </div>

                                                                    <div class="form-group row">
                                                                        <label class="col-lg-4 col-form-label">Remarks</label>
                                                                        <div class="col-lg-8">
                                                                        
                                                                        <input type="text" value="<?= $data->remarks; ?>" name="remarks" class="form-control">

                                                                        </div>
                                                                    </div>

                                                                    <div class="form-group row">
                                                                        <label class="col-lg-4 col-form-label">Date Verified (Division Verifier)</label>
                                                                        <div class="col-lg-8">
                                                                        
                                                                        <input type="date" value="<?= $data->verified; ?>" name="verified" class="form-control">

                                                                        </div>
                                                                    </div>

                                                                    
                                                                
                                                            </div>
                                                        </div>

                                                    </div>
                                                    <!-- end row -->
                                                        
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="submit" class="btn btn-info waves-effect waves-light">Update</button>
                                                    </div>
                                                    </form>
                                                </div>
                                                <!-- /.modal-content -->
                                            </div>
                                            <!-- /.modal-dialog -->
                                        </div>
                                        <!-- /.modal -->


                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- end row -->

                    </div>
                    <!-- end container-fluid -->

                </div>
                <!-- end content -->

               


                            

   

 