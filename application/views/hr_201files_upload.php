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
                                    <h4 class="header-title mb-4">Upload 201 Files</h4>
                                        
                                    <form action="<?php echo ('process201Upload'); ?>" enctype='multipart/form-data' method="POST" >
															<!-- general form elements -->
															<div class="box-body">
																<input type="hidden" class="form-control" name="IDNumber" value="<?php echo $_GET['IDNumber']; ?>" readonly >
																<div class="form-group">
																	<label for="lastName">Document Name</label>
																	<div class="col-lg-6">
																		<input type="text" class="form-control" name="docName" >
																	</div>
																</div>
																<div class="form-group">
																	<label for="lastName">File types allowed are JPG, PNG, PDF</label>
																	<div class="col-lg-6">
																		<input type="file" class="form-control" name="nonoy" size="40" >
																	</div>
																</div>											
															</div><!-- /.box-body -->
																
																<div class="box-footer">
																	<!--<input type="submit" name="submit" class="btn btn-primary btn-lg waves-effect waves-light" value="Upload">-->
																	<button type="submit" name="submit" class="btn btn-primary btn-md waves-effect waves-light"> <i class="fas fa-cloud-upload-alt  mr-1"></i> <span>Upload</span> </button>																	
																</div>	
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

             
 