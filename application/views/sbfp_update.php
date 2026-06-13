                    

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

                                    <h4>Update SBFP</h4>
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
                            <div class="col-12">
                                <div class="page-title-box">
                                <div class="card">
                                    <div class="card-body">

                                                    <?= form_open('Sbfp/sbfp_update/'); ?>

                                                    <input type="hidden" value="<?= $sbfp->semstudentid; ?>" name="id">

                                                  
                                                    
                                                    <div class="row">
                                                        <div class="col-lg-2">
                                                            <div class="form-group">
                                                                <label for="lastName">Weight</label>
                                                                <input type="text" class="form-control" name="weight" value="<?= $sbfp->weight; ?>">
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-2">
                                                            <div class="form-group">
                                                                <label for="lastName">height</label>
                                                                <input type="text" class="form-control" name="height" value="<?= $sbfp->height; ?>">
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-2">
                                                            <div class="form-group">
                                                                <label for="lastName">Age</label>
                                                                <input type="text" class="form-control" name="age" value="<?= $sbfp->age; ?>">
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-3">
                                                            <div class="form-group">
                                                                <label for="lastName">Nutritional Status</label>
                                                                <input type="text" class="form-control" name="nut_stat" value="<?= $sbfp->nut_stat; ?>">
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-3">
                                                            <div class="form-group">
                                                                <label for="lastName">Date</label>
                                                                <input type="date" class="form-control" name="EnrolledDate" value="<?= $sbfp->EnrolledDate; ?>">
                                                            </div>
                                                        </div>

                                                    </div>

                                        

                                                        
                                                        
   
                                                        <div class="form-group row">
                                                            <div class="col-lg-12">
                                                                <input type="submit" name="submit" class="btn btn-primary waves-effect waves-light" value="Submit" />
                                                            </div>
                                                        </div>
                                                    </form>
                                    </div>
                                </div>
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                        </div>

                      

                        

                    </div>
                    <!-- end container-fluid -->

                </div>
                <!-- end content -->   

             
 