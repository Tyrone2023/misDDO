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
                                    <h4 class="header-title mb-4">Plantilla Group Summary</h4>
                                        <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">

                                        <thead>
															<tr>
																<th>DEPARTMENT/SECTION</th>
																<th style="text-align: center;">COUNTS</th>
                                                                <th style="text-align: center;">ACTION</th>
																
															</tr>
														</thead>
														<tbody>
															   <?php
														  $i=1;
														  foreach($data as $row)
														  {
														  echo "<tr>";
														  ?>
														  
														    <td><?php echo $row->department; ?></td>
                                                            <td style="text-align:center;"><?php echo $row->Counts; ?></td>
                                                            <td style="text-align:center"><a href="<?= base_url(); ?>Page/employeelistDepartment?department=<?php echo $row->department; ?>" class="text-success"><i class="mdi mdi-file-document-box-check-outline"></i>View List</a></td>
														  <?php
														 
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

             
 