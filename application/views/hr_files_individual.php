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
                                    <h4 class="header-title mb-4">201 Files</h4>
                                        
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th style="text-align: center;">Document Name</th>
												<th style="text-align: center;">File Name</th>
												<th style="text-align: center;">View</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                               <?php
										  $i=1;
										  foreach($data as $row)
										  {
										  echo "<tr>";
										  echo "<td>".$row->docName."</td>";
										  echo "<td>".$row->fileName."</td>";
										  ?>
										  <td style="text-align:center;"><a href="<?= base_url(); ?>uploads/201files/<?php echo $row->fileName; ?>" target="_blank"><button type="button" class="btn btn-primary btn-xs waves-effect waves-light"> <i class="fas fa-tv  mr-1"></i> <span>View File</span> </button></a></td> 
                                          
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

             
 