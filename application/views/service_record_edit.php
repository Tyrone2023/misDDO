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
                                    <h4 class="header-title mb-4">EDIT SERVICE RECORD<br /><span class="float-left badge badge-primary inline mt-2"><?php echo $_GET['f'].' '.$_GET['l']; ?></span></h4><br />
                                    <?php
										  foreach($data as $row)
										  { ?>
                                                   
                                                <form method="post" class="parsley-examples">
                                                   <div class="form-row">
                                                       <div class="form-group col-md-4">
                                                           <label  class="col-form-label">From</label>
                                                           <input type="text" class="form-control" required value="<?php echo $row->appointDate; ?>" name="appointDate" >
                                                       </div>
                                                       <div class="form-group col-md-4">
                                                           <label  class="col-form-label">To</label>
                                                           <input type="text" required class="form-control" value="<?php echo $row->endDate; ?>" name="endDate" >
                                                       </div>
                                                       <div class="form-group col-md-4">
                                                           <label  class="col-form-label">Designation</label>
                                                           <input type="text" required name="empPosition" value="<?php echo $row->empPosition; ?>" class="form-control" >
                                                       </div>
                                                   </div>

                                                   <div class="form-row">
                                                       
                                                       <div class="form-group col-md-4">
                                                           <label  class="col-form-label">Status</label>
                                                           <input type="text" required name="empStatus" value="<?php echo $row->empStatus; ?>" class="form-control">
                                                       </div>

                                                       <div class="form-group col-md-4">
                                                           <label  class="col-form-label">Annual Salary</label>
                                                           <input type="text" required name="salary" value="<?php echo $row->salary; ?>" class="form-control">
                                                       </div>

                                                       <div class="form-group col-md-4">
                                                           <label  class="col-form-label">Office Entity</label>
                                                           <input type="text" required name="empStation" value="<?php echo $row->empStation; ?>" class="form-control">
                                                       </div>


                                                   </div>

                                                 

                                                   <div class="form-row">
                                                       <div class="form-group col-md-4">
                                                           <label class="col-form-label">Separation Date</label>
                                                           <input type="text"  name="separationDate" value="<?php echo $row->separationDate; ?>" class="form-control">
                                                       </div>
                                                       <div class="form-group col-md-4">
                                                           <label class="col-form-label">Separation Cause/d</label>
                                                           <input type="text" required name="separation" value="<?php echo $row->separation; ?>" class="form-control">
                                                       </div>

                                                       <div class="form-group col-md-4">
                                                           <label class="col-form-label">Remarks</label>
                                                           <input type="text"  name="remarks" value="<?php echo $row->remarks; ?>" class="form-control">
                                                       </div>
                                                   </div>

                                                   <input type="hidden" name="empID" value="<?php echo $_GET['id']; ?>"/>
                                                
                                              </div>
                                              <div class="modal-footer">
                                              <input type="submit" name="submit" value="Update" class="btn btn-primary waves-effect waves-light">
                                              </div>
                                          </div>
                                           </form>
                                           <?php } ?>
              
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

       
           