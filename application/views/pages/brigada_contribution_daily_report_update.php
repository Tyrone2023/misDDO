


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
                                    <a href="<?= base_url(); ?>Brigada/contribution_report" class="text-success" ><span class="mdi mdi-keyboard-backspace "></span> Back To Daily Report</a>
                                   
                              
                                    <div class="clearfix"></div>
                                    
                                </div>
                            </div>
                        </div>
                        <!-- end page title -->

                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body">
                                    <div class="row">
                                                        <div class="col-12">
                                                            <div class="">
                                                                    <?= validation_errors(); ?>
                                                                    <?php
                                                                    $attributes = array('class' => 'parsley-examples form-horizontal');
                                                                    echo form_open_multipart('Brigada/contribution_report_edit', $attributes);
                                                                    ?>

                                                                    

                                                                    <div class="row">
                                                                        <div class="col-lg-12">
                                                                            <div class="form-group">
                                                                                <label for="lastName">Partners <span style="color:red">*</span></label>
                                                                                <select class="form-control" required name="partners_id" data-toggle="select2">
                                                                                    <option></option>
                                                                                    <?php foreach($partners as $row){?>
                                                                                    <option <?php if($row->id == $data->partners_id){echo "selected";} ?> value="<?= $row->id; ?>"><?= $row->name; ?></option>
                                                                                    <?php } ?>
                                                                                </select>
                                                                            </div>
                                                                        </div>

                                                                    </div>
                                                                    <div class="row">
                                                                        <div class="col-lg-4">
                                                                            <div class="form-group">
                                                                                <label for="lastName">Date <span style="color:red">*</span></label>
                                                                                <input type="date" class="form-control" name="c_date" required value="<?= $data->c_date; ?>">
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-lg-4">
                                                                            <div class="form-group">
                                                                                <label for="lastName">Contribution Type</label>
                                                                                <select class="form-control" required name="contribution_id">
                                                                                    <option></option>
                                                                                    <?php foreach($contribution as $row){?>
                                                                                    <option <?= $data->contribution_id == $row->id ? 'selected' : '' ?> value="<?= $row->id; ?>"><?= str_replace('_', ' ', $row->name); ?></option>
                                                                                    <?php } ?>
                                                                                </select>
                                                                            </div>
                                                                        </div>

                                                                        <div class="col-lg-4">
                                                                            <div class="form-group">
                                                                                <label for="lastName">Specific Contribution</label>
                                                                                <select class="form-control" required name="spicific_contribution">
                                                                                    <option></option>
                                                                                    <option <?= $data->spicific_contribution == 'Cash' ? 'selected' : '' ?> value="Cash">Cash</option>
                                                                                    <option <?= $data->spicific_contribution == 'Manpower' ? 'selected' : '' ?> value="Manpower">Manpower</option>
                                                                                </select>
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <div class="row">
                                                                        <div class="col-lg-4">
                                                                            <div class="form-group">
                                                                                <label for="lastName">Quantity Contributed</label>
                                                                                <input type="text" class="form-control" name="quantity_of_conftribution" required value="<?= $data->quantity_of_conftribution; ?>">
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-lg-4">
                                                                            <div class="form-group">
                                                                                <label for="lastName">Unit of Contribution</label>
                                                                                <input type="text" class="form-control" name="unit_of_contribution" required value="<?= $data->unit_of_contribution; ?>">
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-lg-4">
                                                                            <div class="form-group">
                                                                                <label for="lastName">Amount of Contribution</label>
                                                                                <input type="text" class="form-control" name="amount" required value="<?= $data->amount; ?>">
                                                                            </div>
                                                                        </div>

                                                                    </div>

                                                                    <div class="row">
                                                                        <div class="col-lg-4">
                                                                            <div class="form-group">
                                                                                <label for="lastName">No. of Beneficiary Learners</label>
                                                                                <input type="text" class="form-control" name="no_beneficiary_learnes" required value="<?= $data->no_beneficiary_learnes; ?>">
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-lg-4">
                                                                            <div class="form-group">
                                                                                <label for="lastName">No. of Beneficiary Personnel</label>
                                                                                <input type="text" class="form-control" name="no_beneficiary_personnel" required value="<?= $data->no_beneficiary_personnel; ?>">
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-lg-4">
                                                                            <div class="form-group">
                                                                                <label for="lastName">Form of Agreement</label>
                                                                                <select class="form-control"required name="form_of_agreement">
                                                                                    <option></option>
                                                                                    <option <?= $data->form_of_agreement == 'Memorandum of Agreement' ? 'selected' : '' ?> value="Memorandum of Agreement">Memorandum of Agreement</option>
                                                                                    <option <?= $data->form_of_agreement == 'Memorandum of Understanding' ? 'selected' : '' ?> value="Memorandum of Understanding">Memorandum of Understanding</option>
                                                                                    <option <?= $data->form_of_agreement == 'Deed of Donation' ? 'selected' : '' ?> value="Deed of Donation">Deed of Donation</option>
                                                                                    <option <?= $data->form_of_agreement == 'Usufruct' ? 'selected' : '' ?> value="Usufruct">Usufruct</option>
                                                                                    <option <?= $data->form_of_agreement == 'Acknowledgement Receipt' ? 'selected' : '' ?> value="Acknowledgement Receipt">Acknowledgement Receipt</option>
                                                                                    <option <?= $data->form_of_agreement == 'No Signed Agreement' ? 'selected' : '' ?> value="No Signed Agreement">No Signed Agreement</option>
                                                                                    <option <?= $data->form_of_agreement == 'Others' ? 'selected' : '' ?> value="Others">Others</option>
                                                                                </select>
                                                                            </div>
                                                                        </div>

                                                                    </div>

                                                                    <div class="row">
                                                                        <div class="col-lg-4">
                                                                            <div class="form-group">
                                                                                <label for="lastName">Agreement Start Date</label>
                                                                                <input type="date" class="form-control" name="agreement_started" required value="<?= $data->agreement_started; ?>">
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-lg-4">
                                                                            <div class="form-group">
                                                                                <label for="lastName">Agreement End Date</label>
                                                                                <input type="date" class="form-control" name="agreement_end" required value="<?= $data->agreement_end; ?>">
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-lg-4">
                                                                            <div class="form-group">
                                                                                <label for="lastName">Project Category </label>
                                                                                <select class="form-control" required name="project_category">
                                                                                    <option></option>
                                                                                    <option <?= $data->project_category == 'Brigada Eskwela' ? 'selected' : '' ?> value="Brigada Eskwela">Brigada Eskwela</option>
                                                                                    <option <?= $data->project_category == 'Other Category' ? 'selected' : '' ?> value="Other Category">Other Category</option>
                                                                                </select>
                                                                            </div>
                                                                        </div>

                                                                    </div>

                                                                    <div class="row">
                                                                        <div class="col-lg-12">
                                                                            <div class="form-group">
                                                                                <label for="lastName">Project Name</label>
                                                                                <input type="text" class="form-control" name="project_name" required value="<?= $data->project_name; ?>">
                                                                            </div>
                                                                        </div>

                                                                    </div>
                                                                    <div class="row">
                                                                        <div class="col-lg-4">
                                                                            <div class="form-group">
                                                                                <label for="lastName">Status of Agreement / Project</label>
                                                                                <select class="form-control" required name="status_agreement">
                                                                                    <option></option>
                                                                                    <option <?= $data->status_agreement == 'Completed' ? 'selected' : '' ?> value="Completed">Completed</option> 
                                                                                    <option <?= $data->status_agreement == 'On-going' ? 'selected' : '' ?> value="On-going">On-going</option>
                                                                                    <option <?= $data->status_agreement == 'For Implementation' ? 'selected' : '' ?> value="For Implementation">For Implementation</option>
                                                                                    <option <?= $data->status_agreement == 'Pending' ? 'selected' : '' ?> value="Pending">Pending</option>
                                                                                    <option <?= $data->status_agreement == 'Others' ? 'selected' : '' ?> value="Others">Others</option>
                                                                                </select>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-lg-4">
                                                                            <div class="form-group">
                                                                                <label for="lastName">Initiated By </label>
                                                                                <select class="form-control" required name="initiated_by">
                                                                                    <option></option>
                                                                                    <option <?= $data->initiated_by == 'Central Office' ? 'selected' : '' ?> value="Central Office">Central Office</option>
                                                                                    <option <?= $data->initiated_by == 'Regional Office' ? 'selected' : '' ?> value="Regional Office">Regional Office</option>
                                                                                    <option <?= $data->initiated_by == 'Schools Division Office' ? 'selected' : '' ?> value="Schools Division Office">Schools Division Office</option>
                                                                                    <option <?= $data->initiated_by == 'School' ? 'selected' : '' ?> value="School">School</option>
                                                                                </select>
                                                                            </div>
                                                                        </div>

                                                                        <div class="col-lg-4">
                                                                            <div class="form-group">
                                                                                <label for="lastName">School Year </label>
                                                                                <?php
                                                                                date_default_timezone_set('Asia/Manila');
                                                                                $currentYear = (int) date('Y');
                                                                                ?>
                                                                                <select name="sy" class="form-control">
                                                                                    <?php
                                                                                    for ($i = -5; $i <= 5; $i++) {
                                                                                        $startYear = $currentYear + $i;
                                                                                        $endYear   = $startYear + 1;
                                                                                        $sy = $startYear . '-' . $endYear;
                                                                                        ?>
                                                                                        <option value="<?= $sy ?>" <?= ($i == 0 ? 'selected' : '') ?>>
                                                                                            <?= $sy ?>
                                                                                        </option>
                                                                                    <?php } ?>
                                                                                </select>
                                                                            </div>
                                                                        </div>

                                                                    </div>
                                                                    <input type="hidden" name="id" value="<?= $data->id; ?>">

                                                                    <div class="row">
                                                                        <div class="col-lg-12">
                                                                            <div class="form-group">
                                                                                <label for="lastName">Remarks</label>
                                                                                <textarea class="form-control" name="remarks" rows="3" id="example-textarea"><?= $data->remarks; ?></textarea>
                                                                            </div>
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
                                    

                                </div>
                            </div>
                        </div>
                        <!-- end row -->

                    </div>
                    <!-- end container-fluid -->

                </div>
                <!-- end content -->


   

 