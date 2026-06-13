

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
                                                                    echo form_open('Legal/complaint_update', $attributes);
                                                                    ?>

                                                                    <input type="hidden" value="<?= $data->id; ?>" name="id">

                                                                    <div class="form-group row">
                                                                        <label class="col-lg-4 col-form-label">Nature of Offinse</label>
                                                                        <div class="col-lg-8">
                                                                        
                                                                            <select name="nature_of_offense" class="form-control" required>
                                                                                <option value="" disabled selected>Select Nature of Offinse</option>
                                                                                <?php foreach($offinse as $row){?>
                                                                                <option <?php if($row->id == $data->nature_of_offense){echo "selected";}?> value="<?= $row->id; ?>"><?= $row->description; ?></option>
                                                                                <?php } ?>
                                                                            </select>
                                                                        </div>
                                                                    </div>


                                                                    <div class="form-group row">
                                                                        <label class="col-lg-4 col-form-label">Case No.</label>
                                                                        <div class="col-lg-8">
                                                                        
                                                                        <textarea class="form-control" name="case_no" rows="3" id="example-textarea"><?= $data->case_no; ?></textarea>

                                                                        </div>
                                                                    </div>

                                                                    <div class="form-group row">
                                                                        <label class="col-lg-4 col-form-label">Complaints</label>
                                                                        <div class="col-lg-8">
                                                                        
                                                                        <textarea class="form-control" name="complaint" rows="3" id="example-textarea"><?= $data->complainant; ?></textarea>

                                                                        </div>
                                                                    </div>

                                                                    <div class="form-group row">
                                                                        <label class="col-lg-4 col-form-label">Respondent/Person Complaint Of</label>
                                                                        <div class="col-lg-8">
                                                                        
                                                                        <textarea class="form-control" name="respondent" rows="3" id="example-textarea"><?= $data->respondent; ?></textarea>

                                                                        </div>
                                                                    </div>

                                                                    <div class="form-group row">
                                                                        <label class="col-lg-4 col-form-label">Fiscal Year</label>
                                                                        <div class="col-lg-8">
                                                                            
                                                                            <?php
                                                                                $currentYear = date("Y");
                                                                                $fiscalStartYear = $currentYear - 3;
                                                                                $fiscalEndYear = $currentYear + 3;

                                                                                ?>
                                                                                <select name="fy" class="form-control" required>
                                                                                    <option value="" disabled selected>Select Fiscal Year</option>
                                                                                    <?php for ($year = $fiscalStartYear; $year <= $fiscalEndYear; $year++) { ?>
                                                                                    <option <?php if($year == $data->fy){echo "selected";}?> value="<?= $year; ?>"><?= $year; ?></option>
                                                                                    <?php } ?>
                                                                                </select>
                                                                            

                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group row">
                                                                        <label class="col-lg-4 col-form-label">Status</label>
                                                                        <div class="col-lg-8">
                                                                        
                                                                            <select name="stat" class="form-control" required>
                                                                                <option value="" disabled selected>Select Status</option>
                                                                                <?php foreach($stat as $row){?>
                                                                                <option <?php if($row->id == $data->stat){echo "selected";}?> value="<?= $row->id; ?>"><?= $row->description; ?></option>
                                                                                <?php } ?>
                                                                            </select>
                                                                        </div>
                                                                    </div>


                                                                    

                                                                    <div class="form-group row">
                                                                        <label class="col-lg-4 col-form-label">Remarks</label>
                                                                        <div class="col-lg-8">
                                                                        
                                                                        <textarea class="form-control" name="remarks" rows="3" id="example-textarea"><?= $data->remarks; ?></textarea>

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

               


                            

   

 