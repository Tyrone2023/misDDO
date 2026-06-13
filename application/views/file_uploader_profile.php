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
                                    <a href="https://softtechservices.net/downloads/Template - Personnel Profile.xlsx" class="btn btn-success waves-effect width-md waves-light">Download the Template Here</a>
                                    <!-- <a href="#" class="btn btn-success waves-effect width-md waves-light">Download the Template Here</a> -->
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                        </div>
                        <!-- end page title -->

                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body table-responsive">
                                    <h4 class="header-title mb-4">Mass Upload <br /><span class="float-left badge badge-primary inline mt-2">Personnel Profile</span></h4><br />
                     
                                        <div class="float-left">
                                                <h4 class="text-right">
                                                
                                                </h4>

                                            </div>

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
                                          
                                            <form method='post' action='' enctype="multipart/form-data">
										    <!-- general form elements -->
											<div class="box-body">
													<div class="form-group">
														<label></label>
														<input type='file' class="form-control" name='file' required >
													</div>											
												</div><!-- /.box-body -->
													
													<div class="box-footer">
														<input type='submit' value='Upload' class="btn btn-info float-right" name='upload'>
													</div>	
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

             
 