

            <!-- ============================================================== -->
            <!-- Start Page Content here -->
            <!-- ============================================================== -->

            <div class="content-page">
                <div class="content">


                    <!-- Start Content-->
                    <div class="container-fluid">
                        <!-- <br />
                    <img src="<?=base_url(); ?>assets/images/dashboard.jpg" width="100%"> -->
                        <!-- start page title -->
                        <div class="row">
                            <div class="col-12">
                                <div class="page-title-box">
                                    <h4 class="page-title"></h4>
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


                        <div class="row">
                            <div class="col-xl-3 col-sm-6">
                                <div class="card bg-success">
                                        <div class="card-body widget-style-2">
                                            <div class="text-white media">
                                                <div class="media-body align-self-center">
                                                    <a href="<?=base_url(); ?>Page/trainingNeeds"><h2 class="my-0 text-white"><span data-plugin="counterup"><?= $data1->num_rows(); ?></span></h2></a>
                                                    <p class="mb-0 text-white">Training Needs</p>
                                                </div>
                                                <i class="ion ion-ios-checkbox-outline"></i>
                                            </div>
                                        </div>
                                    </div>
                            </div>

                            <div class="col-xl-3 col-sm-6">
                                <div class="card bg-purple">
                                    <div class="card-body widget-style-2">
                                        <div class="text-white media">
                                            <div class="media-body align-self-center">
                                            <a href="<?=base_url(); ?>Page/individualDevelopment"><h2 class="my-0 text-white"><span data-plugin="counterup"><?= $data2->num_rows(); ?></span></h2></a>
                                                <p class="mb-0">Development Plans</p>
                                            </div>
                                            <i class="ion ion-ios-filing"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-3 col-sm-6">
                                <div class="card bg-info">
                                    <div class="card-body widget-style-2">
                                        <div class="text-white media">
                                            <div class="media-body align-self-center">
                                                <h2 class="my-0 text-white"><span data-plugin="counterup"><?php if(!$data3) { echo "0"; } else{ echo $data3[0]->vlTotal; } ?></span></h2>
                                                <p class="mb-0">Vacation Leave</p>
                                            </div>
                                            <i class="ion ion-md-folder "></i>
                                        </div>
                                    </div>
                                </div>
                            </div> 

                            <div class="col-xl-3 col-sm-6">
                                <div class="card bg-primary">
                                    <div class="card-body widget-style-2">
                                        <div class="text-white media">
                                            <div class="media-body align-self-center">
                                                <h2 class="my-0 text-white"><a href="#" class="text-white"><span data-plugin="counterup"><?php if(!$data3) { echo "0"; } else{ echo $data3[0]->slTotal; } ?></span></a></h2>
                                                <p class="mb-0">Sick Leave</p>
                                            </div>
                                            <i class="ion ion-md-folder-open "></i>
                                        </div>
                                    </div>
                                </div>
                            </div> 
                        </div>
                        <!-- End row -->







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

                

                