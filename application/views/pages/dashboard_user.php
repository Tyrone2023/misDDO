

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
                                    <h4 class="page-title"></h4>
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                        </div>
                        <!-- end page title -->

                        <div class="row">
                            <div class="col-xl-6 col-sm-6">
                                <div class="card bg-info">
                                        <div class="card-body widget-style-2">
                                            <div class="text-white media">
                                                <div class="media-body align-self-center">
                                                    <a href="#"> <!-- Need e disable ang link  -->
                                                    <h2 class="my-0 text-white"><span data-plugin="counterup"><?= $data1->num_rows(); ?></span></h2></a>
                                                    <p class="mb-0 text-white">Job Vacancies</p>
                                                </div>
                                                <i class="ion ion-md-albums"></i>
                                            </div>
                                        </div>
                                    </div>
                            </div>

                            <div class="col-xl-6 col-sm-6">
                                <div class="card bg-purple">
                                    <div class="card-body widget-style-2">
                                        <div class="text-white media">
                                            <div class="media-body align-self-center">
                                            <a href="#"><h2 class="my-0 text-white"><span data-plugin="counterup"><?= $data2->num_rows(); ?></span></h2></a>
                                                <p class="mb-0">My Applications</p>
                                            </div>
                                            <i class="ion ion-md-folder"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- <div class="col-xl-3 col-sm-6">
                                <div class="card bg-info">
                                    <div class="card-body widget-style-2">
                                        <div class="text-white media">
                                            <div class="media-body align-self-center">
                                                <h2 class="my-0 text-white"><span data-plugin="counterup">1,854</span></h2>
                                                <p class="mb-0">Non-Teaching Personnel</p>
                                            </div>
                                            <i class="ion-ios-pricetag"></i>
                                        </div>
                                    </div>
                                </div>
                            </div> -->

                            <!-- <div class="col-xl-3 col-sm-6">
                                <div class="card bg-primary">
                                    <div class="card-body widget-style-2">
                                        <div class="text-white media">
                                            <div class="media-body align-self-center">
                                                <h2 class="my-0 text-white"><a href="#" class="text-white"><span data-plugin="counterup">0</span></a></h2>
                                                <p class="mb-0">Inactive List</p>
                                            </div>
                                            <i class="mdi mdi-comment-multiple"></i>
                                        </div>
                                    </div>
                                </div>
                            </div> -->


                            

                        <?php if ($is_endorser): ?>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header py-3 bg-transparent">
                <div class="card-widgets">
                    <a href="javascript:;" data-toggle="reload"><i class="mdi mdi-refresh"></i></a>
                    <a data-toggle="collapse" href="#cardCollpase3" role="button" aria-expanded="false" aria-controls="cardCollpase3"><i class="mdi mdi-minus"></i></a>
                    <a href="#" data-toggle="remove"><i class="mdi mdi-close"></i></a>
                </div>
                <h5 class="header-title mb-2">Pending Task</h5>
            </div>
            <div id="cardCollpase3" class="collapse show">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-nowrap mb-0">
                            <thead>
                                <tr>
                                    <th>Task</th>
                                    <th style="text-align:center">Counts</th>
                                    <th style="text-align:center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Leave Applications for Recommendation</td>
                                    <td style="text-align:center">
                                        <span class="badge badge-info">
                                        <?= $data5; ?>


                                        </span>
                                    </td>
                                    <td style="text-align:center">
                                        <a href="<?= base_url(); ?>Page/pendingLeave" class="text-success">
                                            <i class="mdi mdi-file-document-box-check-outline"></i>View List
                                        </a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?php if ($is_approver): ?>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header py-3 bg-transparent">
                <div class="card-widgets">
                    <a href="javascript:;" data-toggle="reload"><i class="mdi mdi-refresh"></i></a>
                    <a data-toggle="collapse" href="#cardCollpase3" role="button" aria-expanded="false" aria-controls="cardCollpase3"><i class="mdi mdi-minus"></i></a>
                    <a href="#" data-toggle="remove"><i class="mdi mdi-close"></i></a>
                </div>
                <h5 class="header-title mb-2">Pending Task</h5>
            </div>
            <div id="cardCollpase3" class="collapse show">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-nowrap mb-0">
                            <thead>
                                <tr>
                                    <th>Task</th>
                                    <th style="text-align:center">Counts</th>
                                    <th style="text-align:center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Leave Applications for Approval</td>
                                    <td style="text-align:center">
                                        <span class="badge badge-info">
                                        <?= $data7; ?>


                                        </span>
                                    </td>
                                    <td style="text-align:center">
                                        <a href="<?= base_url(); ?>Page/pendingLeave" class="text-success">
                                            <i class="mdi mdi-file-document-box-check-outline"></i>View List
                                        </a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>
                      
                    </div>
                </div>
                        
                    

                </div>
                <!-- end content -->

                