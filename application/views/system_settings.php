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
                                    <!-- <a href="<?= $link; ?>" class="btn btn-success waves-effect width-md waves-light"><?= $pn; ?></a> -->
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                        </div>
                        <!-- end page title -->

                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body table-responsive">
                                    <h4 class="header-title mb-4">System Settings</h4>

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

                                    <form method="post">
                                        <!-- <input type="submit" name="submit" class="btn btn-info" value="Company Settings"> -->
                                        <input type="submit" name="submit" class="btn btn-primary" value="Age Mass Updating">
                                        <input type="submit" name="retirement" class="btn btn-secondary" value="Calculate Retirement Year">
                                        <input type="submit" name="service" class="btn btn-success" value="Calculate Lenght of Service">
                                        <input type="submit" name="loyalty" class="btn btn-success" value="Loyalty">
                                        <input type="submit" name="salaryUpdating" class="btn btn-info" value="Mass Updating of Monthly Salary">
                                        <input type="submit" name="increment" class="btn btn-success" value="Step Increment">
                                        
                                    </form>
                                    
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

             
 