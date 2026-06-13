
            
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
                                    <?php if($this->session->position == 'Accountant'){ ?>
                                    <h4 class="page-title">Last Batch Code <span class="badge badge-warning"><?= $last->alloc_batch; ?></span></h4>
                                    <?php } ?>

                                    <div class="page-title-right">
                                        <ol class="breadcrumb p-0 m-0">
                                            <li class="breadcrumb-item"><a href="#" data-toggle="modal" data-target="#myModal">Current Fiscal Year : <span class="badge badge-success"><?= $this->session->cur_fy; ?></span></a></li>
                                        </ol>
                                    </div>
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
                                                <h2 class="my-0 text-white"><a href="<?=base_url(); ?>Page/aip_sub_district" class="text-white"><span data-plugin="counterup">18</span></a></h2>
                                                <p class="mb-0 ">District</p>
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
                                                <h2 class="my-0 text-white"><a href="<?=base_url(); ?>Page/aip_sub"  class="text-white"><span data-plugin="counterup"><?= $sub->num_rows(); ?></span></a></h2>
                                                <p class="mb-0">SUBMITTED</p>
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
                                                <h2 class="my-0 text-white"><a href="<?=base_url(); ?>Page/aip_approved"  class="text-white"><span data-plugin="counterup"><?= $ap->num_rows(); ?></span></a></h2>
                                                <p class="mb-0">APPROVED</p>
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
                                                <h2 class="my-0 text-white"><a href="<?=base_url(); ?>Page/aip_requested" class="text-white"><span data-plugin="counterup"><?= $req->num_rows(); ?></span></a></h2>
                                                <p class="mb-0">REQUESTED</p>
                                            </div>
                                            <i class="  ion ion-md-person"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- End row -->

                        <!-- <div class="row">
                            <div class="col-12">
                                <div id="barchart_values"></div>
                            </div>
                        </div> -->

                        

                        

                        
                    

                </div>
                <!-- end content -->

                 <!-- sample modal content -->
                                        <div id="myModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-success">
                                                        <h5 class="modal-title text-white" id="myModalLabel">Change Fiscal Year</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                    </div>
                                                    <div class="modal-body">
                                                    <form action="<?= base_url('Pages/change_fy') ?>" method="post">
                                                        <div class="form-group row">
                                                            <div class="col-lg-12">
                                                                <select name="new_fy" class="form-control" onchange="this.form.submit()">
                                                                <option disabled selected>Change FY</option>
                                                                <?php for ($y = 2023; $y <= 2030; $y++) : ?>
                                                                    <option value="<?= $y ?>" <?= ($this->session->userdata('cur_fy') == $y) ? 'selected' : '' ?>>
                                                                        <?= $y ?>
                                                                    </option>
                                                                <?php endfor; ?>
                                                            </select>
                                                            </div>
                                                        </div>
                                                    </form>
                                                    </div>
                                                </div>
                                                <!-- /.modal-content -->
                                            </div>
                                            <!-- /.modal-dialog -->
                                        </div>
                                        <!-- /.modal -->

                

                