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
                                    <h4 class="header-title mb-4">List of Personnel <br /><span class="float-left badge badge-primary inline mt-2">Active, Resigned, Retired, Transferred and Deceased</span></h4><br />
                     
                                        <div class="float-left">
                                                <h4 class="text-right">
                                                    <a href="employeelistv5"><button type="button" class="btn btn-info">Active</button></a>
                                                    <a href="Resigned"><button type="button" class="btn btn-warning">Resigned</button></a>
                                                    <a href="Retired"><button type="button" class="btn btn-success">Retired</button></a>
                                                    <a href="Transferred"><button type="button" class="btn btn-dark">Transferred</button></a>
                                                    <a href="Deceased"><button type="button" class="btn btn-primary">Deceased</button></a>
                                                </h4>

                                            </div>
                                            <div class="float-right">
                                                <h5><strong>Employee Masterlist</strong></h5>
                                            </div>

                                        <table class="table table-sm mb-0">
                                        <!-- <table id="tech-companies-1" class="table table-striped mb-0"> -->
                                        <thead>
                                                    <tr>
                                                        <th>Last Name</th>
                                                        <th data-priority="1">First Name</th>
                                                        <th data-priority="3">Middle Name</th>
                                                        <th data-priority="1">Employee No.</th>
                                                        <th data-priority="3">Position</th>
                                                        <th data-priority="3">Date Hired</th>
                                                    </tr>
                                                </thead>
                                    
                                        <tbody>
                                               <?php
										  $i=1;
										  foreach($data as $row)
										  {
										  echo "<tr>";
										  echo "<td>".$row->LastName."</td>";
										  echo "<td>".$row->FirstName."</td>";
										  echo "<td>".$row->MiddleName."</td>";
										  echo "<td>".$row->IDNumber."</td>";
                                          echo "<td>".$row->empPosition."</td>";
                                          echo "<td>".$row->origAppointmentDate."</td>";
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

             
 