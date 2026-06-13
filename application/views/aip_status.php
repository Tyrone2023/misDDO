
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
                                    <h4 class="page-title" id="myLargeModalLabel"> 
                                        
                                    </h4>
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

                                    

                                    

                                        <a data-toggle="modal" class="open-AddBookDialog btn btn-success w-lg" href="#sub">SUBMITTED</a>
                                        <a data-toggle="modal" class="open-AddBookDialog btn btn-primary w-lg" href="#notsub">NOT SUBMITTED</a>


                                        </div>  
                                    </div>
                                </div>

                            </div>

                        </div>
                        <!--- end row -->

                    </div>
                    <!-- end container-fluid -->

                </div>
                <!-- end content -->

                                        <div id="sub" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="myModalLabel"></h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                    </div>
                                                    <div class="modal-body">
                                                    <?= form_open('Page/aip_stat_sub'); ?>
                                                        <div class="form-group col-md-12">
                                                            <label ><?= $label; ?></label>
                                                            <select class="form-control" name="fy" required>
                                                                <option></option>
                                                            <?php 
                                                            $firstYear = (int)date('Y');
                                                            $lastYear = $firstYear + 5;
                                                            
                                                            for($i=$firstYear;$i<=$lastYear;$i++)
                                                            { 
                                                                echo '<option value='.$i.'>'.$i.'</option>';
                                                            }
                                                            ?>
                                                            </select>


                                                        </div>
   
                                                        <div class="form-group col-md-6">
                                                            <input type="submit" name="submit" class="btn btn-primary waves-effect waves-light" value="Submit" />
                                                        </div>
                                                    </form>
                                                </div>
                                                <!-- /.modal-content -->
                                            </div>
                                            <!-- /.modal-dialog -->
                                        </div>
            
                                        

                                        

                                        


            <!-- ============================================================== -->
            <!-- End Page content -->
            <!-- ============================================================== -->

             <!-- Footer Start -->
             <?php include('includes/footer.php'); ?>
            <!-- end Footer -->

        </div>
        <!-- END wrapper -->

        <div id="notsub" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="myModalLabel"></h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                    </div>
                                                    <div class="modal-body">
                                                    <?= form_open('Page/aip_stat_notsub'); ?>
                                                        <div class="form-group col-md-12">
                                                            <label ><?= $label; ?></label>
                                                            <select class="form-control" name="fy" required>
                                                                <option></option>
                                                            <?php 
                                                            $firstYear = (int)date('Y');
                                                            $lastYear = $firstYear + 5;
                                                            
                                                            for($i=$firstYear;$i<=$lastYear;$i++)
                                                            { 
                                                                echo '<option value='.$i.'>'.$i.'</option>';
                                                            }
                                                            ?>
                                                            </select>


                                                        </div>
   
                                                        <div class="form-group col-md-6">
                                                            <input type="submit" name="submit" class="btn btn-primary waves-effect waves-light" value="Submit" />
                                                        </div>
                                                    </form>
                                                </div>
                                                <!-- /.modal-content -->
                                            </div>
                                            <!-- /.modal-dialog -->
                                        </div>

        
    

        <!-- Vendor js -->
        <script src="<?= base_url(); ?>assets/js/vendor.min.js"></script>

        <!-- App js -->
        <script src="<?= base_url(); ?>assets/js/app.min.js"></script>


 


    </body>
</html>