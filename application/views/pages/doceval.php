

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
                            <div class="col-xl-3 col-sm-6">
                                <div class="card bg-pink">
                                    <div class="card-body widget-style-2">
                                        <div class="text-white media">
                                            <div class="media-body align-self-center">
                                                <h2 class="my-0 text-white"><span data-plugin="counterup"><?= $data->num_rows(); ?></span></h2>
                                                <p class="mb-0 ">Total Employees</p>
                                            </div>
                                            <i class="ion ion-md-people"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-3 col-sm-6">
                                <div class="card bg-purple">
                                    <div class="card-body widget-style-2">
                                        <div class="text-white media">
                                            <div class="media-body align-self-center">
                                                <h2 class="my-0 text-white"><span data-plugin="counterup"><?= $data2->num_rows(); ?></span></h2>
                                                <p class="mb-0">Teaching Personnel</p>
                                            </div>
                                            <i class="ion ion-md-person-add "></i>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-3 col-sm-6">
                                <div class="card bg-info">
                                    <div class="card-body widget-style-2">
                                        <div class="text-white media">
                                            <div class="media-body align-self-center">
                                                <h2 class="my-0 text-white"><span data-plugin="counterup"><?= $data3->num_rows(); ?></span></h2>
                                                <p class="mb-0">Non-Teaching Personnel</p>
                                            </div>
                                            <i class=" ion ion-md-contact"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-3 col-sm-6">
                                <div class="card bg-primary">
                                    <div class="card-body widget-style-2">
                                        <div class="text-white media">
                                            <div class="media-body align-self-center">
                                                <h2 class="my-0 text-white"><span data-plugin="counterup"><?= $data1->num_rows(); ?></span></h2>
                                                <p class="mb-0">Inactive List</p>
                                            </div>
                                            <i class="  ion ion-md-person"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- End row -->

                        <?php if ($this->session->position != 'Admin'){ ?>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-header py-3 bg-transparent">
                                        <div class="card-widgets">
                                            <a href="javascript:;" data-toggle="reload"><i class="mdi mdi-refresh"></i></a>
                                            <a data-toggle="collapse" href="#cardCollpase3" role="button" aria-expanded="false" aria-controls="cardCollpase3"><i class="mdi mdi-minus"></i></a>
                                            <a href="#" data-toggle="remove"><i class="mdi mdi-close"></i></a>
                                        </div>
                                        <h5 class="header-title mb-2"> Pending Task</h5>
                                    </div>
                                    <div id="cardCollpase3" class="collapse show">
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table class="table table-nowrap mb-0">
                                                    <thead>
                                                        <tr>
                                                            <th>Task</th>
                                                            <th style='text-align:center'>Counts</th>
                                                            <th style='text-align:center'>Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td>Leave Applications for Approval</td>
                                                            <td style='text-align:center'><span class="badge badge-info"><?= $data4->num_rows(); ?></span></td>
                                                            <td style='text-align:center'>
                                                                <i class="mdi mdi-file-document-box-check-outline"></i>View List
                                                            </td>
                                                        </tr>
                                                       

                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- end card-->

                            </div>
                        </div>
                        <!-- end row -->
                        <?php } ?>

                        
                    

                </div>
                <!-- end content -->

                

                