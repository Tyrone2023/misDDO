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
                                    <h4 class="header-title mb-4">Reports <br /><span class="float-left badge badge-primary inline mt-2">Employee Summary</span></h4><br />

                                                <div class="row">
                                                    <div class="col-xl-4 col-sm-6">
                                                        <div class="card">
                                                            <div class="card-body widget-style-2">
                                                                <div class="media">
                                                                    <div class="media-body align-self-center">
                                                                        <h2 class="my-0"><span data-plugin="counterup"><?= number_format($data[0]->counts,0); ?></span></h2>
                                                                        <p class="mb-0"><a href="<?= base_url(); ?>Page/empGroup2/Elementary">Elementary</a></p>
                                                                    </div>
                                                                    <i class="ion-md-paper text-pink bg-light"></i>
                                                                   
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-xl-4 col-sm-6">
                                                        <div class="card">
                                                            <div class="card-body widget-style-2">
                                                                <div class="media">
                                                                    <div class="media-body align-self-center">
                                                                        <h2 class="my-0"><span data-plugin="counterup"><?= number_format($data1[0]->counts,0); ?></span></h2>
                                                                        <p class="mb-0"><a href="<?= base_url(); ?>Page/empGroup2/Secondary">Secondary</a></p>
                                                                    </div>
                                                                    <i class="ion-md-paper text-purple bg-light"></i>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-xl-4 col-sm-6">
                                                        <div class="card">
                                                            <div class="card-body widget-style-2">
                                                                <div class="media">
                                                                    <div class="media-body align-self-center">
                                                                        <h2 class="my-0"><span data-plugin="counterup"><?= number_format($data2[0]->counts,0); ?></span></h2>
                                                                        <p class="mb-0"><a href="<?= base_url(); ?>Page/empGroup2/SENIOR HIGH">Senior High</a></p>
                                                                    </div>
                                                                    <i class="ion-md-paper text-info bg-light"></i>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
<!-- 
                                                    <div class="col-xl-3 col-sm-6">
                                                        <div class="card">
                                                            <div class="card-body widget-style-2">
                                                                <div class="media">
                                                                    <div class="media-body align-self-center">
                                                                        <h2 class="my-0"><span data-plugin="counterup">145</span></h2>
                                                                        <p class="mb-0">Provisionary</p>
                                                                    </div>
                                                                    <i class="mdi mdi-comment-multiple text-primary bg-light"></i>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div> -->
                                                </div>

                                                <!-- End of the row -->
                                                <div class="row">
                                                    <div class="col-xl-4 col-sm-6 ">
                                                        <!-- Portlet card -->
                                                        <div class="card">
                                                        <div class="card-header bg-success py-3 text-white">
                                                                <div class="card-widgets">
                                                                    <a href="javascript:;" data-toggle="reload"><i class="mdi mdi-refresh"></i></a>
                                                                    <a data-toggle="collapse" href="#cardCollpase1" role="button" aria-expanded="false" aria-controls="cardCollpase2"><i class="mdi mdi-minus"></i></a>
                                                                    <a href="#" data-toggle="remove"><i class="mdi mdi-close"></i></a>
                                                                </div>
                                                                <h5 class="card-title mb-0 text-white">Elementary Summary</h5>
                                                            </div>
                                                            <div id="cardCollpase1" class="collapse show">
                                                                <div class="card-body">
                                                                    <div class="button-list">
                                                                            <a href="<?= base_url(); ?>Page/empGroup/Teaching/Elementary"><button type="button" class="btn btn-outline-success waves-effect width-md waves-light">Teaching</button></a>
                                                                            <a href="<?= base_url(); ?>Page/empGroup/Non-Teaching/Elementary"><button type="button" class="btn btn-outline-success waves-effect width-md">Non-Teaching</button></a>
                                                                        </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <!-- end card-->

                                                    </div>
                                                    <!-- end col -->
                                                    <div class="col-xl-4 col-sm-6 ">
                                                        <!-- Portlet card -->
                                                        <div class="card">
                                                            <div class="card-header bg-primary py-3 text-white">
                                                                <div class="card-widgets">
                                                                    <a href="javascript:;" data-toggle="reload"><i class="mdi mdi-refresh"></i></a>
                                                                    <a data-toggle="collapse" href="#cardCollpase2" role="button" aria-expanded="false" aria-controls="cardCollpase2"><i class="mdi mdi-minus"></i></a>
                                                                    <a href="#" data-toggle="remove"><i class="mdi mdi-close"></i></a>
                                                                </div>
                                                                <h5 class="card-title mb-0 text-white">Secondary Summary</h5>
                                                            </div>
                                                            <div id="cardCollpase2" class="collapse show">
                                                                <div class="card-body">
                                                                    <div class="button-list">
                                                                                <a href="<?= base_url(); ?>Page/empGroup/Teaching/Secondary"><button type="button" class="btn btn-outline-primary waves-effect width-md waves-light">Teaching</button></a>
                                                                                <a href="<?= base_url(); ?>Page/empGroup/Non-Teaching/Secondary"><button type="button" class="btn btn-outline-primary waves-effect width-md">Non-Teaching</button></a>
                                                                        </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <!-- end card-->

                                                    </div>
                                                    <!-- end col -->

                                                    <div class="col-xl-4 col-sm-6 ">
                                                        <!-- Portlet card -->
                                                        <div class="card">
                                                            <div class="card-header bg-info py-3 text-white">
                                                                <div class="card-widgets">
                                                                    <a href="javascript:;" data-toggle="reload"><i class="mdi mdi-refresh"></i></a>
                                                                    <a data-toggle="collapse" href="#cardCollpase3" role="button" aria-expanded="false" aria-controls="cardCollpase2"><i class="mdi mdi-minus"></i></a>
                                                                    <a href="#" data-toggle="remove"><i class="mdi mdi-close"></i></a>
                                                                </div>
                                                                <h5 class="card-title mb-0 text-white">SHS Summary</h5>
                                                            </div>
                                                            <div id="cardCollpase3" class="collapse show">
                                                                <div class="card-body">
                                                                    <div class="button-list">
                                                                                <a href="<?= base_url(); ?>Page/empGroup/Teaching/SENIOR HIGH"><button type="button" class="btn btn-outline-info waves-effect width-md waves-light">Teaching</button></a>
                                                                                <!-- <a href="<?= base_url(); ?>Page/empGroup?cat=Non-Teaching&group=SENIOR HIGH"><button type="button" class="btn btn-outline-info waves-effect width-md">Non-Teaching</button></a> -->
                                                                                <a href="<?= base_url(); ?>Page/empGroup/Non-Teaching/SENIOR HIGH"><button type="button" class="btn btn-outline-info waves-effect width-md">Non-Teaching</button></a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <!-- end card-->

                                                    </div>
                                                    </div>
                                                    </div>
                                                </div>
                                                    <!-- end col -->
                                                <!-- End of the Row -->

                                                <!-- Summary For Loyalty -->
                                                <div class="row">
                                                            <div class="col-sm-6 col-xl-4">
                                                                <div class="card bg-success">
                                                                    <div class="card-body text-center">
                                                                        <a href="<?= base_url(); ?>Page/loyalty">
                                                                            <div class="h2 mt-2 text-white"><?= number_format($data3[0]->counts,0); ?></div>
                                                                                <span class="mb-2 text-white">For Loyalty This Year</span>
                                                                            </div>
                                                                        </a>
                                                                    </div>
                                                                </div>   
                                                                
                                                                
                                                                <div class="col-sm-6 col-xl-4">
                                                                <div class="card bg-primary">
                                                                    <div class="card-body text-center">
                                                                    <a href="#">
                                                                        <div class="h2 mt-2 text-white"><?= number_format($data4[0]->counts,0); ?></div>
                                                                            <span class="mb-2 text-white">Retired Personnel</span>
                                                                        </div>
                                                                    </a>
                                                                    </div>
                                                                </div>   


                                                                <div class="col-sm-6 col-xl-4">
                                                                <div class="card bg-info">
                                                                    <div class="card-body text-center">
                                                                    <a href="#">
                                                                        <div class="h2 mt-2 text-white"><?= number_format($data5[0]->counts,0); ?></div>
                                                                            <span class="mb-2 text-white">Resigned Personnel</span>
                                                                        </div>
                                                                    </a>
                                                                    </div>
                                                                </div>   

                                                </div>

                                                <!-- End of the row -->
                                                       
                                                            

                                                </div>

                                                
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

             
 