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
                                    <a href="<?= base_url(); ?>Pages/plantilla_add" class="btn btn-success waves-effect width-md waves-light">Add New</a>
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                        </div>
                        <!-- end page title -->

                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body table-responsive">
                                    <h4 class="header-title mb-4">Plantilla Positions</h4>
                                        <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">

                                        <thead>
                                            <tr>
                                                <th>Item No.</th>
												<th>Position</th>
												<th>SG / Step</th>
                                                <th>Auth. Annual Salary</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                               <?php
										  $i=1;
										  foreach($data as $row)
										  {
										  echo "<tr>";
										  echo "<td>".$row->itemNo."</td>";
										  echo "<td>".$row->itemPosition."</td>";
										  echo "<td style='text-align: center'>".$row->sg.'/ '.$row->step."</td>";
                                          echo "<td style='text-align: right'>".number_format($row->authAnnualSalary,2)."</td>";
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

             
 