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
                        <h4 class="page-title">Dashboard</h4>
                        <div class="page-title-right">
                            <ol class="breadcrumb p-0 m-0">
                                <!-- <li class="breadcrumb-item"><a href="#">Velonic</a></li>
                                            <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                                            <li class="breadcrumb-item active">Dashboard 3</li> -->
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
                <div class="col-xl-3 col-sm-6">
                    <div class="card">
                        <div class="card-body widget-style-2">
                            <div class="media">
                                <div class="media-body align-self-center">
                                    <h2 class="my-0"><span data-plugin="counterup">
                                           0
                                        </span></h2>
                                        <a href="#">
                                    <p class="mb-0">Schools</p>
                                        </a>
                                </div>
                                <i class="mdi mdi-account-multiple-check text-pink bg-light"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-sm-6">
                    <div class="card">
                        <div class="card-body widget-style-2">
                            <div class="media">
                                <div class="media-body align-self-center">
                                    <h2 class="my-0"><span data-plugin="counterup">

                                         0
                                        </span></h2>
                                        <a href="#">
                                        <p class="mb-0">Accomplishments</p>
                                    </a>
                                </div>
                                <i class=" mdi mdi-account-multiple  text-purple bg-light"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-sm-6">
                    <div class="card">
                        <div class="card-body widget-style-2">
                            <div class="media">
                                <div class="media-body align-self-center">
                                    <h2 class="my-0"><span data-plugin="counterup">

                                         0
                                        </span></h2>
                                        <a href="#">
                                        <p class="mb-0">Sections</p>
                                    </a>
                                </div>
                                <i class="mdi mdi-account-minus-outline text-info bg-light"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-sm-6">
                    <div class="card">
                        <div class="card-body widget-style-2">
                            <div class="media">
                                <div class="media-body align-self-center">
                                    <h2 class="my-0"><span data-plugin="counterup">
                                        0

                                        </span></h2>
                                        <a href="#">
                                    <p class="mb-0">Users</p>
                                        </a>
                                </div>
                                <i class="mdi mdi-tooltip-account  text-primary bg-light"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End row -->

           

        </div>
        <!-- end container-fluid -->

    </div>
    <!-- end content -->

    <?php include('templates/footer.php'); ?>