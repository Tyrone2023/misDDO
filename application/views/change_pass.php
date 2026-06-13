            <?php include('templates/head.php'); ?> 
            <link href="<?= base_url(); ?>assets/libs/custombox/custombox.min.css" rel="stylesheet" type="text/css">
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
                                 <!-- <button type="button" class="btn btn-primary waves-effect waves-light" data-toggle="modal" data-backdrop="static" data-keyboard="false" data-target=".bs-example-modal-lg">Add New</button> -->
                                                
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                        </div>
                        <!-- end page title -->

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
                                <div class="card">
                                    <div class="card-body table-responsive">
                                    <h4 class="header-title mb-4">Change Password</h4><br />
                                       
                                    <form class="form-horizontal" method="POST" action="<?php echo base_url(); ?>page/update_password" >
										  <input type="hidden" name="txt_hidden" value="<?php echo $this->session->userdata('username');?>">
											<div class="card-body">
											  <div class="form-group row">
												<label for="inputEmail3" class="col-sm-4 col-form-label">Current Password</label>
												<div class="col-sm-8">
												  <input type="password" class="form-control" name="currentpassword" placeholder="Current Password">
												</div>
											  </div>
											  <div class="form-group row">
												<label for="inputPassword3" class="col-sm-4 col-form-label">New Password</label>
												<div class="col-sm-8">
												  <input type="password" class="form-control" name="newpassword" placeholder=" New Password">
												</div>
											  </div>
											  <div class="form-group row">
												<label for="inputPassword3" class="col-sm-4 col-form-label">Confirm Password</label>
												<div class="col-sm-8">
												  <input type="password" class="form-control" name="cnewpassword" placeholder="Confirm Password">
												</div>
											  </div>
											</div>
											<!-- /.card-body -->
											<div class="card-footer">
											  <button type="submit" name="updatePwd" class="btn btn-info float-right"><i class="fa fa-save fa-fw"></i> Update Password</button>
											
											</div>
											<!-- /.card-footer -->
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

                <?php include('templates/footer.php'); ?>  

       
       
 <!--  Add New Vacancies -->
 <div class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" style="display: none;" aria-hidden="true">
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="myLargeModalLabel">Training Needs</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                    </div>
                                                    <div class="modal-body">
                                                        
                                                    <form class="form-horizontal" method="post">
                                                        <div class="form-group row">
                                                            <label for="inputEmail3" class="col-md-3 col-form-label">Training Need</label>
                                                            <div class="col-md-9">
                                                                <input type="text" class="form-control" name="trainingNeeds" >
                                                            </div>
                                                        </div>
                                                      
                                                        <div class="form-group row">
                                                            <label for="inputPassword5" class="col-md-3 col-form-label">Justification</label>
                                                            <div class="col-md-9">
                                                                <textarea class="form-control" rows="5" name="justification"></textarea>
                                                            </div>
                                                        </div>

                                                        <div class="form-group row">
                                                            <label for="inputEmail3" class="col-md-3 col-form-label">Category</label>
                                                            <div class="col-md-9">
                                                                <select class="form-control" name="trainingCat">
                                                                       <option>Acquisition Financial Management</option>         
                                                                       <option>Budget Calculation</option>
                                                                       <option>Contact Management</option>         
                                                                       <option>Financial Budget and Program Analysis</option>
                                                                       <option>Administrative Support</option>
                                                                       <option>Internal Resource Management</option>
                                                                       <option>Occupational Health and Safety Knowledge</option>
                                                                       <option>Process Management</option>
                                                                       <option>Ethics Knowledge</option>
                                                                       <option>Performance Management for Human Resource Professionals</option>
                                                                </select>
                                                            </div>
                                                        </div>


                                                        <div class="form-group mb-0 justify-content-end row">
                                                            <div class="col-md-9">
                                                                <input type="submit" name="submit" value="Submit" class="btn btn-info waves-effect waves-light">
                                                                <!-- <button type="submit" class="btn btn-info waves-effect waves-light">Submit</button> -->
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


