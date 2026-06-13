            <?php include('templates/head.php'); ?>  
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
                                    <!-- <a href="<?= $link; ?>" class="btn btn-success waves-effect width-md waves-light"><?= $pn; ?></a> -->
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                        </div>
                        <!-- end page title -->

                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body table-responsive">
                                    <h4 class="header-title mb-4">Leave History</h4>
                                 
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
                                                    <!-- <button type="button" class="btn btn-primary waves-effect waves-light" data-toggle="modal" data-target=".bs-example-modal-lg">New Leave Application</button> -->
                                        <br /> <br />
                                        <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">

                                        <thead>
                                            <tr>
                                                <th style='text-align:center'>Date Applied</th>
												<th style='text-align:center'>Leave Type</th>
												<th style='text-align:center'>Duration</th>
                                                <th style='text-align:center'>Total No. of Days</th>
                                                <th style='text-align:center'>Leave Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                               <?php
										  $i=1;
										  foreach($data as $row)
										  {
										  echo "<tr>";
										  echo "<td style='text-align:center'>".$row->appDate."</td>";
										  echo "<td style='text-align:center'>".$row->leaveType."</td>";
										  echo "<td style='text-align:center'>".$row->dateFrom.' to '.$row->dateTo."</td>";
										  echo "<td style='text-align:center'>".$row->daysApplied."</td>";
                                          echo "<td style='text-align:center'>".$row->leaveStatus."</td>";
                                           
										  echo "</tr>";
									  
															}
										   ?>
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
                
<!--  Modal content for the above example -->
<div class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" style="display: none;" aria-hidden="true">
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="myLargeModalLabel">New Leave Application</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                    </div>
                                                    <div class="modal-body">
                                                             <!-- Form row -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-body">
                                     <form method="post">
                                            <div class="form-row">
                                                <div class="form-group col-md-6">
                                                    <label for="inputEmail4" class="col-form-label">Type of Leave</label>
                                                    <select name="leaveType" class="form-control" required>
                                                        <option></option>
                                                        <option>Vacation Leave</option>
                                                        <option>Sick Leave</option>
                                                        <option>COC/Service Credit</option>
													</select>
                                                </div>

                                                <div class="form-group col-md-6">
                                                    <label for="inputPassword4" class="col-form-label">No. of Days Applied</label>
                                                    <input type="number" class="form-control" required name="daysApplied" >
                                                </div>
                                               
                                            </div>

                                            <div class="form-row">
                                                <div class="form-group col-md-6">
                                                    <label for="inputEmail4" class="col-form-label">From</label>
                                                    <input type="date" class="form-control" required name="dateFrom">
                                                </div>

                                                <div class="form-group col-md-6">
                                                    <label for="inputEmail4" class="col-form-label">To</label>
                                                    <input type="date" class="form-control" required name="dateTo">
                                                </div>
                                               
                                            </div>

                                            <!-- <div class="form-row">
                                                <div class="form-group col-md-6">
                                                    <label for="inputEmail4" class="col-form-label">If Vacation Leave</label>
                                                    <select name="Sex" class="form-control" >
                                                        <option></option>
                                                        <option>Within the Philippines</option>
                                                        <option>Abroad</option>
                                                        
													</select>
                                                </div>

                                                <div class="form-group col-md-6">
                                                    <label for="inputEmail4" class="col-form-label">If Sick Leave</label>
                                                    <select name="Sex" class="form-control" >
                                                        <option></option>
                                                        <option>In Hospital</option>
                                                        <option>Out Patient</option>
                                                        
													</select>
                                                </div>
                                               
                                            </div> -->

                                            <!-- <div class="form-group">
                                                <label for="inputAddress" class="col-form-label">Address</label>
                                                <input type="text" class="form-control" id="inputAddress" placeholder="1234 Main St">
                                            </div> -->

                                         
                                            <input type="submit" name="submit" class="btn btn-primary" value="Submit">
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- end row -->
                                                    </div>
                                                </div>
                                                <!-- /.modal-content -->
                                            </div>
                                            <!-- /.modal-dialog -->
                                        </div>
                                        <!-- /.modal -->


             
 