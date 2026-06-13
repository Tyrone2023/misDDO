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
                                        <?php if($this->session->userdata('position')==='Admin'): ?>
                                            <a href="<?= base_url(); ?>/Page/=<?php echo $_GET['id']; ?>" class="text-success" target="_blank"><i class="mdi mdi-printer"></i>Print Preview</a>&nbsp;&nbsp;&nbsp;&nbsp;
                                        <?php elseif($this->session->userdata('position')==='user'): 
                                            
                                        endif; ?>
                                    
                                                
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
                                    <h4 class="header-title mb-4">SERVICE RECORD<br /><span class="float-left badge badge-primary inline mt-2">
                                        
                                        <?php if($this->session->userdata('position')==='Admin'): echo $_GET['fname'].' '.$_GET['mname'].' '.$_GET['lname']; 
                                        elseif($this->session->userdata('position')==='user'):
                                            echo $this->session->user;
                                        endif; ?>	
                                        </span></h4><br />
                                       
                                        <!-- <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;"> -->
                                        <table class="table mb-0">
                                        <thead>
                                            <tr>
                                                <th>Service</th>
												<th>Designation</th>
                                                <th>Status</th>
                                                <th>Annual Salary</th>
                                                <th>Office Entity</th>
                                                <th>SLV/ABS W/out Pay</th>
                                                <th>Separation Cause/d</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                               <?php
										  foreach($data as $row)
										  {
										  echo "<tr>";
										  echo "<td>".$row->appointDate.' - '.$row->endDate."</td>";
										  echo "<td>".$row->empPosition."</td>";
                                          echo "<td>".$row->empStatus."</td>";
										  echo "<td>".$row->salary."</td>";
                                          echo "<td>".$row->empStation."</td>";
                                          echo "<td>".$row->lvwithoutpay."</td>";
                                          echo "<td>".$row->separation."</td>";
                                        
                                            echo "</tr>"; } ?>
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

                <?php include('templates/footer.php'); ?>  

       
       
 <!--  Add New Vacancies -->
 <div class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" style="display: none;" aria-hidden="true">
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="myLargeModalLabel">Competency Assessment and Development Plan</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                    </div>
                                                    <div class="modal-body">
                                                        
                                                    <form class="form-horizontal" method="post">
                                                        <div class="form-group row">
                                                            <label for="inputEmail3" class="col-md-3 col-form-label">Target Competency</label>
                                                            <div class="col-md-9">
                                                                <input type="text" class="form-control" name="targetCompetency" >
                                                            </div>
                                                        </div>
                                                        <div class="form-group row">
                                                            <label for="inputEmail3" class="col-md-3 col-form-label">Priority</label>
                                                            <div class="col-md-9">
                                                                <input type="number" class="form-control" name="priority" >
                                                            </div>
                                                        </div>

                                                        <div class="form-group row">
                                                            <label for="inputEmail3" class="col-md-3 col-form-label">Development Activity</label>
                                                            <div class="col-md-9">
                                                                <input type="text" class="form-control" name="devActivity" >
                                                            </div>
                                                        </div>
                                                      
                                                        <div class="form-group row">
                                                            <label for="inputPassword5" class="col-md-3 col-form-label">Support Needed</label>
                                                            <div class="col-md-9">
                                                                <input type="text" class="form-control" name="supportNeeded" >
                                                            </div>
                                                        </div>

                                                        <div class="form-group row">
                                                            <label for="inputEmail3" class="col-md-3 col-form-label">Training Provider</label>
                                                            <div class="col-md-9">
                                                                <input type="text" class="form-control" name="trainingProvider" >
                                                            </div>
                                                        </div>

                                                        <div class="form-group row">
                                                            <label for="inputEmail3" class="col-md-3 col-form-label">Completion Schedule</label>
                                                            <div class="col-md-9">
                                                                <input type="text" class="form-control" name="sched" >
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


