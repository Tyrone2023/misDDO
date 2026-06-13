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
                        <!-- <h4 class="page-title">SCHOOL HEALTH AND NUTRITION SECTION</h4><br> -->
                        <!-- <img class="logo" src="<?= base_url(); ?>assets/images/hris/MediSkwela.jpg" alt=""> -->
                        <img class="img-fluid logo" src="<?= base_url(); ?>assets/images/hris/MediSkwela.jpg" alt="MediSkwela" width="100%">

                        <div class="page-title-right">
                            <ol class="breadcrumb p-0 m-0">

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
                                    <h2 class="my-0">
                                        <span data-plugin="counterup">
                                            <?= isset($pendingConsultations) ? htmlspecialchars($pendingConsultations) : '0'; ?>
                                        </span>
                                    </h2>

                                    <?php if ($this->session->userdata('sp') == 0): ?>
                                        <a href="<?= base_url(); ?>Page/med_patient_pending">
                                            <p class="mb-0">Pending Consultations</p>
                                        </a>
                                    <?php else: ?>
                                        <p class="mb-0 text-muted">Pending Consultations</p>
                                    <?php endif; ?>

                                </div>
                                <i class="mdi mdi-account-multiple text-purple bg-light"></i>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="col-xl-4 col-sm-6">
                    <div class="card">
                        <div class="card-body widget-style-2">
                            <div class="media">
                                <div class="media-body align-self-center">
                                    <h2 class="my-0">
                                        <span data-plugin="counterup">
                                            <?= isset($processedConsultations) ? htmlspecialchars($processedConsultations) : '0'; ?>
                                        </span>
                                    </h2>
                                    <a href="<?= base_url(); ?>Page/med_patient">
                                        <p class="mb-0">Total Consultations</p>
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
                                    <h2 class="my-0">
                                        <span data-plugin="counterup">
                                            <?= isset($labTotal) ? (int)$labTotal : 0; ?>
                                        </span>
                                    </h2>
                                    <a href="<?= base_url(); ?>Page/lab_request">
                                        <p class="mb-0">Total Laboratory Requests</p>
                                    </a>
                                </div>
                                <i class="mdi mdi-flask-outline text-info bg-light"></i>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <div class="row">
                <div class="col-xl-6 col-lg-6">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="header-title mb-3">Health Cases by Classification</h4>
                            <div class="table-responsive">
                                <table class="table table-sm table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th>Classification</th>
                                            <th class="text-right">Count</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($illnessTally)): ?>
                                            <?php foreach ($illnessTally as $it): ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($it->category); ?></td>
                                                    <td class="text-right">
                                                        <span class="badge badge-info badge-pill"><?= (int)$it->total; ?></span>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="2" class="text-center text-muted">No data</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-6 col-lg-6">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="header-title mb-3">Diseases by Category</h4>
                            <?php if (!empty($caseCategoryTally)): ?>
                                <div class="text-muted mb-2">Click a category to view diseases.</div>
                            <?php endif; ?>
                            <div class="table-responsive">
                                <table class="table table-sm table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th>Category</th>
                                            <th class="text-right">Count</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($caseCategoryTally)): ?>
                                            <?php foreach ($caseCategoryTally as $it): ?>
                                                <?php
                                                    $category = (string) $it->category;
                                                    $categoryUrl = base_url('Page/health_category?category=' . rawurlencode($category));
                                                ?>
                                                <tr>
                                                    <td>
                                                        <a href="<?= $categoryUrl; ?>">
                                                            <?= htmlspecialchars($category); ?>
                                                        </a>
                                                    </td>
                                                    <td class="text-right">
                                                        <a href="<?= $categoryUrl; ?>">
                                                            <span class="badge badge-primary badge-pill"><?= (int)$it->total; ?></span>
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="2" class="text-center text-muted">No data</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
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
