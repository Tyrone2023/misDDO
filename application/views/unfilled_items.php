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
                                    <h4 class="header-title mb-4">Unfilled Items <br /><span class="float-left badge badge-primary inline mt-2">For Permanent Position</span></h4><br />
                     
                                        
                                            <!-- <div class="float-right">
                                                <h5><strong>Employee Masterlist</strong></h5>
                                            </div> -->
                                       

                                            <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                        
                                        <thead>
                                                    <tr>
                                                        <th>Item No.</th>
                                                        <th>Position</th>
                                                        <th>SG</th>
                                                        <th>Step</th>
                                                        <th>Office</th>
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
										  echo "<td>".$row->sg."</td>";
										  echo "<td>".$row->step."</td>";
                                          echo "<td>".$row->pGroup."</td>";
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

             
 