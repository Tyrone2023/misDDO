<!-- ============================================================== -->
<!-- Start Page Content here -->
<!-- ============================================================== -->
<?php
    $temp_permit_count = isset($temp_permit_count) ? (int)$temp_permit_count : 0;
?>

<div class="content-page">
    <div class="content">

        <!-- Start Content-->
        <div class="container-fluid">

            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <h4 class="page-title"><?= $title; ?></h4>
                        <div class="clearfix"></div>
                    </div>
                </div>
            </div>
            <!-- end page title -->

            <div class="row">
                
                <div class="col-xl-3 col-sm-6">
                    <div class="card bg-purple">
                        <div class="card-body widget-style-2">
                            <div class="text-white media">
                                <div class="media-body align-self-center">
                                    <h2 class="my-0 text-white">
                                        <a href="<?= base_url('Research/temp_permits'); ?>" class="text-white">
                                            <span data-plugin="counterup"><?= $temp_permit_count; ?></span>
                                        </a>
                                    </h2>
                                    <p class="mb-0">Temporary Permits</p>
                                </div>
                                <i class="ion ion-md-person-add "></i>
                            </div>
                        </div>
                    </div>
                </div>

                
            </div>
            <!-- End row -->
        </div>
        <!-- end content -->
