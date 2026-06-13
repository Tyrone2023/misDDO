
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
                                <a href="<?= base_url().'Page/adjustment' ?>"><button type="button" class="btn btn-primary waves-effect waves-light" >Back</button></a>
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
                                        
                                        
                                        <?= form_open('page/adjustment_update'); ?>

                                            <input type="hidden" value="<?= $data->id; ?>" name="id">
                                            <div class="row">
                                                    
                                                    <div class="col-lg-12">
                                                        <div class="form-group">
                                                            <label > BEDP Pillar <span class="text-danger">*</span></label>
                                                            <select class="form-control" name="pillar" required>
                                                                <option value="" selected disabled>Please select</option>
                                                                <option <?php if($data->pillar == 1){echo "selected";} ?> value="1" >ACCESS</option>
                                                                <option <?php if($data->pillar == 2){echo "selected";} ?> value="2" >EQUITY</option>
                                                                <option <?php if($data->pillar == 3){echo "selected";} ?> value="3" >QUALITY</option>
                                                                <option <?php if($data->pillar == 4){echo "selected";} ?> value="4" >RESILIENCY AND WELL-BEING</option>
                                                                <option <?php if($data->pillar == 5){echo "selected";} ?> value="5" >ENABLING MECHANISM</option>
                                                                <option <?php if($data->pillar == 6){echo "selected";} ?> value="6" >RESILIENCY</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                            </div>



                                            <div class="row">
                                                    <div class="col-lg-12">
                                                        <div class="form-group">
                                                            <label >SCHOOL IMPROVEMENT PROJECT TITLE <span class="text-danger">*</span></label>
                                                            <input class="form-control" type="text" value="<?= $data->sip; ?>" name="sip" required>
                                                        </div>
                                                    </div>
                                            </div>

                                            <div class="row">
                                                    <div class="col-lg-12">
                                                        <div class="form-group">
                                                            <label >PERFORMANCE INDICATORS <span class="text-danger">*</span></label>
                                                            <input class="form-control" type="text" value="<?= $data->pi; ?>" name="pi" required>
                                                        </div>
                                                    </div>
                                            </div>

                                            

                                            <!-- <div class="row">
                                                <div class="col-lg-2">
                                                    <div class="form-group">
                                                        <label>1ST QUARTER</label>
                                                        <input class="form-control" type="text" name="q1">
                                                    </div>
                                                </div>
                                                <div class="col-lg-2">
                                                    <div class="form-group">
                                                        <label>2ND QUARTER</label>
                                                        <input class="form-control" type="text" name="q2">
                                                    </div>
                                                </div>
                                                <div class="col-lg-2">
                                                    <div class="form-group">
                                                        <label>3RD QUARTER</label>
                                                        <input class="form-control" type="text" name="q3">
                                                    </div>
                                                </div>
                                                <div class="col-lg-2">
                                                    <div class="form-group">
                                                        <label>4TH QUARTER</label>
                                                        <input class="form-control" type="text" name="q4">
                                                    </div>
                                                </div>
                                                <div class="col-lg-4">
                                                    <div class="form-group">
                                                        <label>TOTAL</label>
                                                        <input class="form-control" type="text" name="total" readonly>
                                                    </div>
                                                </div>
                                            </div> -->
                                            
                                            
                                            

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