
            <!-- ============================================================== -->
            <!-- Start Page Content here -->
            <!-- ============================================================== -->

            <div class="content-page">
                <div class="content">

                    <!-- Start Content-->
                    <div class="container-fluid">

                    <?php if(!isset($_SESSION['sbm_fy'])){?>

                    <!-- start page title -->
                    <div class="row">
                            <div class="col-12">
                                <div class="page-title-box">
                                <div class="card">
                                    <div class="card-body">

                                                    <?= form_open('page/sbm_action_plan'); ?>
                                                        <div class="form-group col-md-6">
                                                            <label >Fiscal Year</label>
                                                            <select class="form-control" name="fy" required>
                                                                <option></option>
                                                                <?php 
                                                                    for ($count = date('Y'); $count >= 2020; $count--) 
                                                                    echo '<option value="' . $count . '">' . $count . '</option>';
                                                                ?>
                                                                
                                                            </select>


                                                        </div>
   
                                                        <div class="form-group col-md-6">
                                                            <input type="submit" name="submit" class="btn btn-primary waves-effect waves-light" value="Submit" />
                                                        </div>
                                                    </form>
                                    </div>
                                </div>
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                        </div>
                        <!-- end page title -->
                        
                        <?php }else{ ?>


                        


                    

                        <!-- start page title -->
                        <div class="row">
                            <div class="col-12">
                                <div class="page-title-box">
                                    <h4 class="page-title" id="myLargeModalLabel">                            
                                        <a href="<?= base_url(); ?>Page/sbm_action_plan_new" class="btn btn-success">Add New</a>

                                        <a target="_blank" href="<?= base_url(); ?>Page/sbm_action_plan_pview" class="btn btn-primary"><i class="fas fa-print"></i> Print View</a>
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
                                        <h4 class="header-title mb-4"><?= $title; ?></h4>
                                        <div class="table-responsive">
                                        <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">

                                        <thead>
                                                <tr>
                                                    <th>Activity</th>
                                                    <th>Objectives</th>	
                                                    <th>Expected Outputs</th>	
                                                    <th>Methodology Strategy</th>	
                                                    <th>Time Frame</th>	
                                                    <th>Person Involved</th>	
                                                    <th>Budgetary Requirement</th>	
                                                    <th>Remarks</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>

                                            <tbody>
                                                <?php foreach($data as $row){?>
                                                <tr>
                                                    <td><?= $row->activity; ?></td>
                                                    <td><?= $row->objective; ?></td>
                                                    <td><?= $row->ex_output; ?></td>
                                                    <td><?= $row->metho_strategy; ?></td>
                                                    <td><?= $row->time_frame; ?></td>
                                                    <td><?= $row->person_involved; ?></td>
                                                    <td><?= $row->bud_req; ?></td>
                                                    <td><?= $row->remarks; ?></td>
                                                    <th>
                                                        <a href="<?= base_url(); ?>Page/sbm_action_plan_edit/<?= $row->id; ?>" class="btn btn-sm btn-warning">Update</a>
                                                        <a href="<?= base_url(); ?>Page/sbm_action_plan_del/<?= $row->id; ?>" class="btn btn-sm btn-danger">Delete</a>
                                                    </th>
                                                </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                        </div>  
                                    </div>
                                </div>

                            </div>

                        </div>
                        <!--- end row -->

                        <?php } ?>

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


        <!-- Required datatable js -->
        <script src="<?= base_url(); ?>assets/libs/datatables/jquery.dataTables.min.js"></script>
        <script src="<?= base_url(); ?>assets/libs/datatables/dataTables.bootstrap4.min.js"></script>
        <!-- Buttons examples -->
        <script src="<?= base_url(); ?>assets/libs/datatables/dataTables.buttons.min.js"></script>
        <script src="<?= base_url(); ?>assets/libs/datatables/buttons.bootstrap4.min.js"></script>
        <script src="<?= base_url(); ?>assets/libs/jszip/jszip.min.js"></script>
        <script src="<?= base_url(); ?>assets/libs/pdfmake/pdfmake.min.js"></script>
        <script src="<?= base_url(); ?>assets/libs/pdfmake/vfs_fonts.js"></script>
        <script src="<?= base_url(); ?>assets/libs/datatables/buttons.html5.min.js"></script>
        <script src="<?= base_url(); ?>assets/libs/datatables/buttons.print.min.js"></script>

        <!-- Responsive examples -->
        <script src="<?= base_url(); ?>assets/libs/datatables/dataTables.responsive.min.js"></script>
        <script src="<?= base_url(); ?>assets/libs/datatables/responsive.bootstrap4.min.js"></script>

        <script src="<?= base_url(); ?>assets/libs/datatables/dataTables.keyTable.min.js"></script>
        <script src="<?= base_url(); ?>assets/libs/datatables/dataTables.select.min.js"></script>

        <!-- Datatables init -->
        <script src="<?= base_url(); ?>assets/js/pages/datatables.init.js"></script>


     <script src="<?= base_url(); ?>assets/libs/custombox/custombox.min.js"></script>


    </body>
</html>