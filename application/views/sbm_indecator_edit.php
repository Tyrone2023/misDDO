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
                                        <h4 class="header-title mb-4">PRINCIPLE</h4>

                                        <?= form_open('Page/sbm_edit'); ?>

                                                        
                                                        
                                                        <div class="form-group col-md-12">
                                                            <label>Indicator</label>
                                                            <input type="text" required value="<?= $sbm->indicator; ?>" name="indicator" class="form-control"> 
                                                        </div>

                                                        <input type="hidden" value="<?= $sbm->id; ?>" name="id">

                                                        <div class="form-group col-md-12">
                                                            <label>Description</label>
                                                            <textarea class="form-control" name="description" rows="5" id="example-textarea"><?= $sbm->description; ?></textarea>
                                                        </div>

                                                        
                                                    
                                                        <div class="modal-footer">
                                                            <input type="submit" value="Submit" name="submit" class="btn btn-primary waves-effect waves-light">
                                                        </div>
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


            