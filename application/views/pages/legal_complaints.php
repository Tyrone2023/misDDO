

            <!-- ============================================================== -->
            <!-- Start Page Content here -->
            <!-- ============================================================== -->
             <?php 
                $modality = [
                        1 => 'Face to Face',
                        2 => 'Online',
                        3 => 'Blended',
                    ];

             ?>

            <div class="content-page">
                <div class="content">

                    <!-- Start Content-->
                    <div class="container-fluid">

                        <!-- start page title -->
                        <div class="row">
                            <div class="col-12">
                                <div class="page-title-box">
                                    <a href="#" class="btn btn-primary waves-effect waves-light openModalBtn"data-toggle="modal" data-target="#myModal">Add New</a>
                              
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
                                                    <th>NO.</th>
                                                    <th>FISCAL YEAR</th>
                                                    <th>NATURE OF OFFENSE</th>
                                                    <th>CASE NO.</th>
                                                    <th>COMPLAINANT</th>
                                                    <th>RESPONDENT/PERSON<br /> COMPLAINT OF</th>
                                                    <th>STATUS</th>
                                                    <th>REMARKS</th>
                                                    <th>ACTION</th>
                                                </tr>
                                            </thead>

                                            <tbody>
                                                <?php $c=1; foreach($data as $row){ ?>
                                                    <tr>
                                                        <td><?= $c++; ?></td>
                                                        <td><?= $row->fy; ?></td>
                                                        <td><?= $row->ivy; ?></td>
                                                        <td><?= $row->case_no; ?></td>
                                                        <td><?= $row->complainant; ?></td>
                                                        <td><?= $row->respondent; ?></td>
                                                        <td><?= $row->ivan; ?></td>
                                                        <td><?= $row->remarks; ?></td>
                                                        <td class="text-center">
                                                            <a class="text-success tooltips" data-placement="top" data-toggle="tooltip" data-original-title="Update" href="<?= base_url(); ?>Legal/complaint_update/<?= $row->ic; ?>"><i class=" fas fa-edit"></i></a> &nbsp; &nbsp;
                                                            <a onclick="return confirm('Are you sure?')" class="text-danger tooltips" data-placement="top" data-toggle="tooltip" data-original-title="Delete" href="<?= base_url(); ?>Legal/complaint_delete/<?= $row->ic; ?>"><i class="far fa-trash-alt"></i></a>
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
                                                        <h5 class="modal-title" id="modalTitle">Add New COMPLAINT</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                    </div>
                                                    <div class="modal-body">

                                                    <div class="row">
                                                        <div class="col-12">
                                                            <div class="">
                                                                    <?= validation_errors(); ?>
                                                                    <?php
                                                                    $attributes = array('class' => 'parsley-examples form-horizontal');
                                                                    echo form_open('Legal/legal_complaint_insert', $attributes);
                                                                    ?>

                                                                    <div class="form-group row">
                                                                        <label class="col-lg-4 col-form-label">Nature of Offense</label>
                                                                        <div class="col-lg-8">
                                                                        
                                                                            <select name="nature_of_offense" class="form-control" required>
                                                                                <option value="" disabled selected>Select Nature of Offense</option>
                                                                                <?php foreach($offinse as $row){?>
                                                                                <option value="<?= $row->id; ?>"><?= $row->description; ?></option>
                                                                                <?php } ?>
                                                                            </select>
                                                                        </div>
                                                                    </div>


                                                                    <div class="form-group row">
                                                                        <label class="col-lg-4 col-form-label">Case No.</label>
                                                                        <div class="col-lg-8">
                                                                        
                                                                        <textarea class="form-control" name="case_no" rows="3" id="example-textarea"></textarea>

                                                                        </div>
                                                                    </div>

                                                                    <div class="form-group row">
                                                                        <label class="col-lg-4 col-form-label">Complainant</label>
                                                                        <div class="col-lg-8">
                                                                        
                                                                        <textarea class="form-control" name="complainant" rows="3" id="example-textarea"></textarea>

                                                                        </div>
                                                                    </div>

                                                                    <div class="form-group row">
                                                                        <label class="col-lg-4 col-form-label">Respondent/Person Complaint Of</label>
                                                                        <div class="col-lg-8">
                                                                        
                                                                        <textarea class="form-control" name="respondent" rows="3" id="example-textarea"></textarea>

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
                                                                                    <option <?php if(date('Y') == $year){echo ' selected ';}?> value="<?= $year; ?>"><?= $year; ?></option>
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
                                                                                <option value="<?= $row->id; ?>"><?= $row->description; ?></option>
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


                            

   

 