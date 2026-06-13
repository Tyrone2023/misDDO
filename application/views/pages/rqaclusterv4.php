<!DOCTYPE html>
<html lang="en">

    <head>
    <title>Management Information System (MIS)</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta content="Responsive bootstrap 4 admin template" name="description" />
        <meta content="Coderthemes" name="author" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <!-- App favicon -->
        <link rel="shortcut icon" href="<?= base_url(); ?>assets/images/hris.ico">

        <!-- Plugins css-->
        <link href="<?= base_url(); ?>assets/libs/sweetalert2/sweetalert2.min.css" rel="stylesheet" type="text/css" />

        <!-- App css -->
        <link href="<?= base_url(); ?>assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" id="bootstrap-stylesheet" />
        <link href="<?= base_url(); ?>assets/css/icons.min.css" rel="stylesheet" type="text/css" />
        <link href="<?= base_url(); ?>assets/css/app.min.css" rel="stylesheet" type="text/css" id="app-stylesheet" />

    </head>

    <body data-layout="horizontal">

        <!-- Begin page -->
        <div id="wrapper">

            <!-- Navigation Bar-->
            <header id="topnav">
                    <!-- Topbar Start -->
                    <div class="navbar-custom">
                        <div class="container-fluid">
                            <ul class="list-unstyled topnav-menu float-right mb-0">
    
                                <li class="dropdown notification-list">
                                    <!-- Mobile menu toggle-->
                                    <a class="navbar-toggle nav-link">
                                        <div class="lines">
                                            <span></span>
                                            <span></span>
                                            <span></span>
                                        </div>
                                    </a>
                                    <!-- End mobile menu toggle-->
                                </li>
                               
                            </ul>
                
                             <!-- LOGO -->
                             <div class="logo-box">
                                <a href="<?= base_url(); ?>Pages/job_vacancy_for_sds_only" class="logo text-center logo-dark">
                                    <span class="logo-lg">
                                        <img src="<?= base_url(); ?>assets/images/logo-dark.png" alt="" height="40">
                                        <!-- <span class="logo-lg-text-dark">Velonic</span> -->
                                    </span>
                                    <span class="logo-sm">
                                        <!-- <span class="logo-lg-text-dark">V</span> -->
                                        <img src="<?= base_url(); ?>assets/images/logo-sm.png" alt="" height="22">
                                    </span>
                                </a>

                                <a href="<?= base_url(); ?>Pages/job_vacancy_for_sds_only" class="logo text-center logo-light">
                                    <span class="logo-lg">
                                        <img src="<?= base_url(); ?>assets/images/logo-light.png" alt="" height="40">
                                        <!-- <span class="logo-lg-text-dark">Velonic</span> -->
                                    </span>
                                    <span class="logo-sm">
                                        <!-- <span class="logo-lg-text-dark">V</span> -->
                                        <img src="<?= base_url(); ?>assets/images/logo-sm.png" alt="" height="20">
                                    </span>
                                </a>
                            </div>
                            <!-- LOGO -->
    
                            <div class="clearfix"></div>
                        </div>
                    </div>
                    <!-- end Topbar -->
    
                    
                </header>
                <!-- End Navigation Bar-->

            <!-- ============================================================== -->
            <!-- Start Page Content here -->
            <!-- ============================================================== -->

            <div class="content-page">
                <div class="content">

                    <!-- Start Content-->
                    <div class="container-fluid">

                    <?php if($this->session->flashdata('success')) : ?>

                        <?= '<br /><div class="alert alert-success alert-dismissible fade show" role="alert">
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>'
                                .$this->session->flashdata('success'). 
                            '</div>'; 
                        ?>
                        <?php endif; ?>

                        <?php if($this->session->flashdata('danger')) : ?>
                        <?= '<br /><div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>'
                                .$this->session->flashdata('danger'). 
                            '</div>'; 
                        ?>
                        <?php endif;  ?>


                        <div class="row">
                            <div class="col-lg-12">
                                <div class="card">
                                    <div class="card-body">
                                    <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                        <!-- <table id="datatable-buttons" class="table table-striped table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;"> -->
                                        <thead>
                                            <tr>
                                                <th>Major</th>
                                                <th>Count</th>
                                                <th style="text-align:center">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                                foreach($data as $row){
                                                $ad = $this->Page_model->rqa_cluster_shs_count($this->uri->segment(3),$row->shss);
                                                if($ad->num_rows() >= 1){
                                            ?>
                                            <tr>
                                                <td><?= $row->shss; ?></td>
                                                <td><?= $ad->num_rows();?></td>
                                                <td class="text-center"><a href="<?= base_url(); ?>Pages/rqa_cluster_list_shsv2/<?= $this->uri->segment(3); ?>/?s=<?= $row->shss; ?>" class="text-success"><i class="mdi mdi-file-document-box-check-outline"></i>Printable View</a></td>
                                            </tr>
                                            <?php } } ?>
                                        </tbody>

                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                     

                    </div>
                    <!-- end container-fluid -->

                </div>
                <!-- end content -->

                

                <!-- Footer Start -->
                <footer class="footer">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-12">
                            2022 - <?= date('Y'); ?> &copy;
                            </div>
                        </div>
                    </div>
                </footer>
                <!-- end Footer -->

            </div>

            <!-- ============================================================== -->
            <!-- End Page content -->
            <!-- ============================================================== -->

        </div>
        <!-- END wrapper -->

        
        

      
        <!-- Vendor js -->
        <script src="<?= base_url(); ?>assets/js/vendor.min.js"></script>

        <!-- Plugin js-->
        <script src="<?= base_url(); ?>assets/libs/parsleyjs/parsley.min.js"></script>

        <!-- Validation init js-->
        <script src="<?= base_url(); ?>assets/js/pages/form-validation.init.js"></script>

        <!-- App js -->
        <script src="<?= base_url(); ?>assets/js/app.min.js"></script>


    </body>

</html>