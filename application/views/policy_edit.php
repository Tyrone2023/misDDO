
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
                                <a href="<?= base_url().'Page/policy' ?>"><button type="button" class="btn btn-primary waves-effect waves-light" >Back</button></a>
                                <!-- <h4 class="page-title" id="myLargeModalLabel">                            
                                        <button type="button" class="btn btn-primary waves-effect waves-light" data-toggle="modal" data-target=".bs-example-modal-lg">+ ADD NEW</button>
                                        <a href="acc" class="btn btn-info waves-effect waves-light" target="_blank">REPORTS</a>
                                    </h4> -->
                                    <!-- <div class="page-title-right">
                                        <ol class="breadcrumb p-0 m-0">
                                            <li class="breadcrumb-item">SGOD Management System v1.0</li>
                                        </ol>
                                    </div> -->
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                        </div>
                        <!-- end page title -->


                        
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="card">
                                    <div class="card-body">
                                        <h4 class="header-title mb-4"><?= $title; ?></h4>
                                        
                                        
                                        <?= form_open('page/policy_update'); ?>

                                            <input type="hidden" value="<?= $data->id; ?>" name="id">
                                            <div class="row">
                                                    <div class="col-lg-12">
                                                        <div class="form-group">
                                                            <label >Year<span class="text-danger">*</span></label>
                                                            <select class="form-control" name="fy" rquired>
                                                                <option value="" selected disabled>Please select</option>
                                                                <?php
                                                                    $startYear = date('Y') - 1;
                                                                    $endYear = $startYear + 6;
                                                                    for ($year = $startYear; $year <= $endYear; $year++) { ?>
                                                                        <option <?php if($data->fy == $year){echo "selected";} ?> value="<?= $year; ?>"><?= $year; ?></option>
                                                                    <?php } ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <!-- <div class="col-lg-6">
                                                        <div class="form-group">
                                                            <label > PPAs <span class="text-danger">*</span></label>
                                                            <select class="form-control" name="ppas" required>
                                                                <option value="" selected disabled>Please select</option>
                                                                <?php foreach($ppas as $row){?>
                                                                <option <?php if($data->ppas == $row->id){echo "selected";} ?> value="<?= $row->id; ?>" ><?= $row->description; ?></option>
                                                                <?php } ?>
                                                            </select>
                                                        </div>
                                                    </div> -->
                                            </div>


                                            <div class="row">
                                                    <div class="col-lg-12">
                                                        <div class="form-group">
                                                            <label >Operational Issue</label>
                                                            <textarea class="form-control" name="oi" rows="5" id="example-textarea" ><?= $data->oi; ?></textarea>
                                                        </div>
                                                    </div>
                                            </div>

                                            <div class="row">
                                                    <div class="col-lg-12">
                                                        <div class="form-group">
                                                            <label > Policy Issue </label>
                                                            <textarea class="form-control" name="pi" rows="2" id="example-textarea" ><?= $data->pi; ?></textarea>
                                                        </div>
                                                    </div>
                                            </div>

                                            <div class="row">
                                                    <div class="col-lg-12">
                                                        <div class="form-group">
                                                            <label > General Issue </label>
                                                            <textarea class="form-control" name="gi" rows="5" id="example-textarea" ><?= $data->gi; ?></textarea>
                                                        </div>
                                                    </div>
                                            </div>

                                            <div class="row">
                                                    <div class="col-lg-12">
                                                        <div class="form-group">
                                                            <label >Action taken or To be taken </label>
                                                            <textarea class="form-control" name="at" rows="5" id="example-textarea" ><?= $data->at; ?></textarea>
                                                        </div>
                                                    </div>
                                            </div>

                                            <div class="row">
                                                    <div class="col-lg-12">
                                                        <div class="form-group">
                                                            <label >Issues Needing Management Action or Decision </label>
                                                            <textarea class="form-control" name="issues" rows="5" id="example-textarea" ><?= $data->issues; ?></textarea>
                                                        </div>
                                                    </div>
                                            </div>

                                            
                                            

                                            <div class="form-group text-left mb-0">
                                               <input type="submit" name="submit" value="Submit" class="btn btn-primary waves-effect waves-light mr-1">
                                                
                                               
                                            </div>

                                        </form>

                                    </div>
                                </div>

                            </div>

                        </div>
                        <!--- end row -->

                    </div>
                    <!-- end container-fluid -->

                </div>
                <!-- end content -->

                

            <!-- ============================================================== -->
            <!-- End Page content -->
            <!-- ============================================================== -->

             <!-- Footer Start -->
             <?php include('includes/footer.php'); ?>
            <!-- end Footer -->

        </div>
        <!-- END wrapper -->


        <!-- Vendor js -->
        <script src="<?= base_url(); ?>assets/js/vendor.min.js"></script>

        <!-- App js -->
        <script src="<?= base_url(); ?>assets/js/app.min.js"></script>




   

         



    </body>
</html>