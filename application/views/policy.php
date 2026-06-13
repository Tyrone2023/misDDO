

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
                                    <h4 class="page-title" id="myLargeModalLabel"> 

                                    <a href="<?= base_url().'Page/policy_new' ?>"><button type="button" class="btn btn-primary waves-effect waves-light" >Add Policy Issues & Updates</button></a>

                                    <div class="btn-group">
                                        <button type="button" class="btn btn-secondary dropdown-toggle waves-effect" data-toggle="dropdown" aria-expanded="false"> Qualitative Data <i class="mdi mdi-chevron-down"></i> </button>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item" href="<?= base_url(); ?>Page/innovations">Innovations</a>
                                            <a class="dropdown-item" href="<?= base_url(); ?>Page/quick_wins">Quick Wins and Best Practices</a>
                                            <a class="dropdown-item" href="<?= base_url(); ?>Page/policy">Policy Issues & Updates</a>
                                        </div>
                                    </div>



                                    
                                    </h4>

                                    <div class="page-title-right">
                                        <ol class="breadcrumb p-0 m-0">
                                            <li class="breadcrumb-item"><a href="<?= base_url(); ?>Page/aip">AIP</a></li>
                                            <li class="breadcrumb-item"><a href="<?= base_url(); ?>Page/view_app">APP</a></li>
                                            <li class="breadcrumb-item"><a href="<?= base_url(); ?>Page/smeav2">SMEA</a></li>
                                        </ol>
                                    </div>
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
                                                        <th>#</th>
                                                        <th>Year</th>
                                                        <th>Operational Issue</th>
                                                        <th>Policy Issue</th>
                                                        <th>General Issue</th>
                                                        <th> Action taken or To be taken </th>
                                                        <th> Issues Needing Management Action or Decision </th>
                                                        <th>MANAGE</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php $ivy=1; foreach($data as $row){ 
                                                        //$ppas = $this->SGODModel->one_cond_row('sgod_ppas','id',$row->ppas);
                                                        ?>
                                                    <tr>
                                                        <td><?= $ivy++; ?></td>
                                                        <td><?= $row->fy; ?></td>
                                                        <td><?= $row->oi; ?></td>
                                                        <td><?= $row->pi; ?></td>
                                                        <td><?= $row->gi; ?></td>
                                                        <td><?= $row->at; ?></td>
                                                        <td><?= $row->issues; ?></td>
                                                        <td>
                                                        <a class="btn btn-success sm" href="<?= base_url(); ?>Page/policy_update/<?= $row->id; ?>">EDIT</a>  &nbsp;
                                                        <a class="btn btn-danger sm" onclick="return confirm('Are you sure?')" href="<?= base_url(); ?>Page/policy_delete/<?= $row->id; ?>">DELETE</a>
                                                        </td>
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

                    </div>
                    <!-- end container-fluid -->

                </div>
                
                
                                    



                                    

                


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