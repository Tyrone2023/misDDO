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
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body table-responsive">
                                        <h4 class="header-title mb-4">School List</h4><br />

                                        <table class="table mb-0">
                                            <!-- <table class="table mb-0"> -->
                                            <thead>
                                                <tr>
                                                    <th>No.</th>
                                                    <th>School Name </th>
                                                    <th class="text-center">Action Plan</th>
                                                    <th class="text-center">Self-Assessment</th>
                                                    <th class="text-center">TA Form</th>
                                            </thead>
                                            <tbody>
                                                <?php 
                                                    $c=1;
                                                    foreach($school as $row){
                                                ?>
                                               <tr>
                                                <td><?= $c++; ?></td>
                                                <td><?= $row->schoolName; ?></td>
                                                <td class="text-center">
                                                        <a target="_blank" href="sbm_action_plan_pview_district/<?= $row->schoolID; ?>" class="btn btn-success btn-sm">View</a>
                                                    
                                                </td>
                                                <td class="text-center">
                                                        <a target="_blank" href="checklist_district/<?= $row->schoolID; ?>" class="btn btn-success btn-sm">View</a>
                                              
                                                </td>
                                                <td class="text-center">
                                                        
                                                        <a target="_blank" href="tapr_form_district/<?= $row->schoolID; ?>" class="btn btn-success btn-sm">View</a>
                                                </td>
                                                <!-- <td>
                                                    <a href="<?= base_url(); ?>Page/tapr_form_district/<?= $row->schoolID; ?>" target="_blank" class="btn btn-sm btn-success">View</a>
                                                </td> -->
                                               </tr>
                                               <?php } ?>
                                            </tbody>

                                        </table>
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


            