
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
                                <a href="<?= base_url().'Page/innovations' ?>"><button type="button" class="btn btn-primary waves-effect waves-light" >Back</button></a>
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
                                        
                                        
                                        <?= form_open('page/innovation_update'); ?>

                                            <input type="hidden" name="id" value="<?= $data->id; ?>">
                                            <div class="row">
                                                    <div class="col-lg-6">
                                                        <div class="form-group">
                                                            <label >Year<span class="text-danger">*</span></label>
                                                            <select class="form-control" name="fy" required>
                                                                <option value="" selected disabled>Please select</option>
                                                                <?php
                                                                    $startYear = date('Y') - 1;
                                                                    $endYear = $startYear + 6;
                                                                    for ($year = $startYear; $year <= $endYear; $year++) { ?>
                                                                        <option <?php if($year == $data->fy){echo "selected";} ?> value="<?= $year; ?>"><?= $year; ?></option>
                                                                    <?php } ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6">
                                                        <div class="form-group">
                                                            <label > BEDP Pillar <span class="text-danger">*</span></label>
                                                            <select class="form-control" name="bedp" required>
                                                                <option value="" selected disabled>Please select</option>
                                                                <option <?php if($data->bedp == 1){echo "selected";} ?> value="1" >ACCESS</option>
                                                                <option <?php if($data->bedp == 2){echo "selected";} ?> value="2" >EQUITY</option>
                                                                <option <?php if($data->bedp == 3){echo "selected";} ?> value="3" >QUALITY</option>
                                                                <option <?php if($data->bedp == 4){echo "selected";} ?> value="4" >RESILIENCY AND WELL-BEING</option>
                                                                <option <?php if($data->bedp == 5){echo "selected";} ?> value="5" >ENABLING MECHANISM</option>
                                                                <option <?php if($data->bedp == 6){echo "selected";} ?> value="6" >RESILIENCY</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                            </div>


                                            <div class="row">
                                                    <div class="col-lg-12">
                                                        <div class="form-group">
                                                            <label >Innovation <span class="text-danger">*</span></label>
                                                            <textarea class="form-control" name="innovation" rows="5" id="example-textarea"><?= $data->innovation; ?></textarea>
                                                        </div>
                                                    </div>
                                            </div>

                                            <div class="row">
                                                    <div class="col-lg-12">
                                                        <div class="form-group">
                                                            <label >Recipients <span class="text-danger">*</span></label>
                                                            <textarea class="form-control" name="recipient" rows="5" id="example-textarea"><?= $data->recipient; ?></textarea>
                                                        </div>
                                                    </div>
                                            </div>

                                            <div class="row">
                                                    <div class="col-lg-12">
                                                        <div class="form-group">
                                                            <label >Impact <span class="text-danger">*</span></label>
                                                            <textarea class="form-control" name="impact" rows="5" id="example-textarea"><?= $data->impact; ?></textarea>
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