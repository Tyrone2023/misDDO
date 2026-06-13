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
                            <div class="col-md-12">
                                <div class="page-title-box">
                                 


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
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                        </div>

                        

                        <!-- end page title -->
                        <div class="row">
                            <div class="col-md-12">

                                <div class="card">
                                    <div class="card-body table-responsive">
                                    <h4 class="m-t-0 header-title mb-4">List of <?= $this->input->get('title'); ?> Student</h4>

                                    <?php echo $this->session->flashdata('msg'); ?>
                                        <!-- <table id="datatable-buttons" class="table table-striped table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;"> -->
                                        <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                            <thead>
                                                <tr>
                                                    <th>Student Name</th>
                                                    <th>Student No.</th>
                                                    <th>Section</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php 
                                                    foreach($data as $row){ 
                                                    $stud = $this->Common->one_cond_row('studeprofile','StudentNumber',$row->StudentNumber);
                                                ?>
                                                <tr>
                                                    <td><?= $stud->FirstName.' '.$stud->MiddleName.' '.$stud->LastName; ?></td>
                                                    <td><?= $row->StudentNumber; ?></td>
                                                    <td><?= $row->Section; ?></td>
                                                </tr>
                                                <?php } ?>
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

                <?php include('templates/footer.php'); ?>

               

