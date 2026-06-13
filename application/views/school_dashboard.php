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
                        <h4 class="page-title"><?php echo $data5[0]->schoolName; ?></h4>
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

            <?php if ($this->session->flashdata('success')) : ?>

                <?= '<div class="alert alert-success alert-dismissible fade show" role="alert">
                                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>'
                    . $this->session->flashdata('success') .
                    '</div>';
                ?>
            <?php endif; ?>

            <?php if ($this->session->flashdata('danger')) : ?>
                <?= '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>'
                    . $this->session->flashdata('danger') .
                    '</div>';
                ?>
            <?php endif;  ?>

            <div class="row">
                <div class="col-xl-4 col-sm-6">
                    <div class="card">
                        <div class="card-body widget-style-2">
                            <div class="media">
                                <div class="media-body align-self-center">
                                    <h2 class="my-0"><span data-plugin="counterup">
                                        <?= $active->num_rows(); ?>
                                        </span></h2>
                                        <a href="<?= base_url(); ?>Page/employeelist_active">
                                    <p class="mb-0">Total Active Employees</p>
                                        </a>
                                </div>
                                <i class="mdi mdi-account-multiple-check text-pink bg-light"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-4 col-sm-6">
                    <div class="card">
                        <div class="card-body widget-style-2">
                            <div class="media">
                                <div class="media-body align-self-center">
                                    <h2 class="my-0"><span data-plugin="counterup">

                                            <?php
                                            if (!$data1) {

                                                echo "0";
                                            } else {
                                                echo $data1[0]->Counts;
                                            }
                                            ?>
                                        </span></h2>
                                    <a href="<?= base_url(); ?>Pages/tp">
                                        <p class="mb-0">Teaching Personnel</p>
                                    </a>
                                </div>
                                <i class=" mdi mdi-account-multiple  text-purple bg-light"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-4 col-sm-6">
                    <div class="card">
                        <div class="card-body widget-style-2">
                            <div class="media">
                                <div class="media-body align-self-center">
                                    <h2 class="my-0"><span data-plugin="counterup">

                                            <?php
                                            if (!$data2) {

                                                echo "0";
                                            } else {
                                                echo $data2[0]->Counts;
                                            }
                                            ?>
                                        </span></h2>
                                    <a href="<?= base_url(); ?>Pages/ntp">
                                        <p class="mb-0">Non-Teaching Personnel</p>
                                    </a>
                                </div>
                                <i class="mdi mdi-account-minus-outline text-info bg-light"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-4 col-sm-6">
                    <div class="card">
                        <div class="card-body widget-style-2">
                            <div class="media">
                                <div class="media-body align-self-center">
                                    <h2 class="my-0"><span data-plugin="counterup">
                                            <?php
                                            if (!$data3) {

                                                echo "0";
                                            } else {
                                                echo $data3[0]->Counts;
                                            }
                                            ?>

                                        </span></h2>
                                        <a href="<?= base_url(); ?>Pages/school_inactive">
                                    <p class="mb-0">Inactive</p>
                                        </a>
                                </div>
                                <i class="mdi mdi-tooltip-account  text-primary bg-light"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-4 col-sm-6">
                    <div class="card">
                        <div class="card-body widget-style-2">
                            <div class="media">
                                <div class="media-body align-self-center">
                                    <h2 class="my-0"><span data-plugin="counterup">
                                            <?= $tr->num_rows(); ?>

                                        </span></h2>
                                        <a href="<?= base_url(); ?>Pages/school_tr">
                                    <p class="mb-0">Teaching Related</p>
                                        </a>
                                </div>
                                <i class="mdi mdi-tooltip-account  text-primary bg-light"></i>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <!-- End row -->

            <div class="row">
                <div class="col-md-12">
                    <!-- Default box -->
                    <?php
                    foreach ($data4 as $row) {
                    ?>
                        <div class="card">
                            <div class="card-header py-3 bg-transparent">
                                <div class="card-widgets">
                                    <a href="javascript:;" data-toggle="reload"><i class="mdi mdi-refresh"></i></a>
                                    <a data-toggle="collapse" href="#cardCollpase1" role="button" aria-expanded="false" aria-controls="cardCollpase1"><i class="mdi mdi-minus"></i></a>
                                    <a href="#" data-toggle="remove"><i class="mdi mdi-close"></i></a>
                                </div>
                                <h5 class="header-title mb-0"><?php echo $row->title; ?></h5>

                            </div>
                            <div id="cardCollpase1" class="collapse show">

                                <div class="card-body">
                                    <a href="<?= base_url(); ?>uploads/announcements/<?= $row->fileAttachment; ?>" target="_blank"><img src=" <?= base_url(); ?>uploads/announcements/<?php echo $row->fileAttachment; ?>" class="img-fluid" alt="Announcement" style="width:100%;"></a>
                                </div>

                                <!-- /.card-body -->
                                <div class="card-footer">
                                    _
                                </div>

                                <!-- /.card-footer-->
                            </div>
                        <?php } ?>
                        <!-- /.card -->
                        </div>
                </div>
            </div>




        </div>
        <!-- end container-fluid -->

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

    <?php include('templates/footer.php'); ?>