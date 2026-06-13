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
                                    <!-- <a href="<?= site_url('Page/printEmployeelistv1'); ?> " style="float: right;" class="text-success" target="_blank"><i class="mdi mdi-printer"></i>Print Preview</a>&nbsp;&nbsp;&nbsp;&nbsp; -->
                                    <button type="button" style="float: right;" class="btn btn-success btn-rounded waves-effect width-md waves-light">
                                        <a href="<?= site_url('Page/printEmployeelistv1'); ?>" target="_blank">
                                            <strong style="color: white;"><i class="mdi mdi-printer"></i>Print Preview</strong>
                                        </a>
                                    </button>

                                    <h4 class="header-title mb-4">List of Personnel<br /><span class="float-left badge badge-primary inline mt-2">GSIS, Pagibig, PhilHealth and TIN</span></h4><br />
                                        <!-- <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;"> -->
                                        <table id="datatable-buttons" class="table table-striped table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                        
                                        <thead>
                                            <tr>
                                                <th>Last Name</th>
												<th>First Name</th>
												<th>Middle Name</th>
                                                <th>Employee No.</th>
                                                <th>Position</th>
                                                <th>Department</th>
                                                <th>GSIS</th>
                                                <th>Pagibig</th>
                                                <th>PhilHealth</th>
                                                <th>TIN</th>
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
                                          echo "<td>".$row->Department."</td>";
                                          echo "<td>".$row->gsis."</td>";
                                          echo "<td>".$row->pagibig."</td>";
                                          echo "<td>".$row->philHealth."</td>";
                                          echo "<td>".$row->tinNo."</td>";
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

             
 