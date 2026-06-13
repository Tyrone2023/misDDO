

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
                                        <!-- <button type="button" class="btn btn-primary waves-effect waves-light" data-toggle="modal" data-target=".bs-example-modal-lg">+ ADD NEW</button> -->
                                    
                                        <!-- <a data-toggle="modal" data-field="" data-id="" class="open-AddBookDialog btn btn-success" href="#addBookDialog">Upload AIP</a> -->
                                    </h4>
                                    <?php if($this->session->position == 'School'){ ?>
                                        <div class="page-title-right">
                                            <ol class="breadcrumb p-0 m-0">
                                                <li class="breadcrumb-item"><a href="<?= base_url(); ?>Page/aip">ANNUAL IMPLEMENTATION PLAN</a></li>
                                                <li class="breadcrumb-item"><a href="<?= base_url(); ?>Page/sop">SCHOOL OPERATIONAL PLAN</a></li>
                                                <li class="breadcrumb-item"><a href="<?= base_url(); ?>Page/view_app">ANNUAL PROCUREMENT PLAN (APP)</a></li>
                                            </ol>
                                        </div>
                                    <?php } ?>

                                    <?php if($this->session->position != 'School'){ ?>
                                        <a href="<?= base_url(); ?>Page/aip_sub_district" class="btn btn-info">BY DISTRICT</a>
                                        <?php if($this->session->position != 'Accountant'){ ?>
                                        <a href="<?= base_url(); ?>Page/aip_sub" class="btn btn-success">SUBMITTED AIP</a>
                                        <a href="<?= base_url(); ?>Page/aip_approved" class="btn btn-primary">APPROVED AIP</a>
                                        <a href="<?= base_url(); ?>Page/aip_requested" class="btn btn-info">REQUESTED</a>
                                        <a href="<?= base_url(); ?>Page/aip_evaluate" class="btn btn-warning">EVALUATE</a>
                                        <?php } ?>
                                        
                                    <?php } ?>
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
                                            <table class="table mb-0">
                                                <thead>
                                                    <tr>
                                                        <th>NO.</th>
                                                        <th>DISTRICT</th>
                                                        <th>SUBMITTED</th>
                                                        <th>NOT SUBMITTED</th>
                                                        <th>TOTAL COUNTS</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php 
                                                    $count = 1;
                                                    $add = 0;
                                                    $notsub = 0;
                                                    foreach($data as $row){ ?>
                                                    <tr>
                                                        <td><?= $count++; ?></td>
                                                        <td><?= $row->district; ?></td>
                                                        <td>
                                                            <?php 
                                                                $ss = $this->SGODModel->one_cond('schools','district',$row->district);
                                                                $fys = $this->session->cur_fy;
                                                               
                                                                $sum = 0;
                                                                foreach($ss as $s){
                                                                    
                                                                    $sub = $this->SGODModel->two_cond_row('sgod_aip_submit', 'fy', $fys, 'school_id',$s->schoolID);
                                                                
                                                                    if(empty($sub)){
                                                                        $c = 0; 

                                                                    }else{
                                                                        $c = 1; 
                                                                    }

                                                                    $sum += $c;
                                                                }
                                                                

                                                            ?>
                                                            <a class="badge badge-info" href="<?= base_url(); ?>Page/aip_submitted/<?= $row->district; ?>"><?= $sum; ?></a>
                                                            
                                                        </td>
                                                        <td>
                                                        <?php $school = $this->SGODModel->count_all('schools','district',$row->district);  $s = $school->num_rows();?>
                                                            <a class="badge badge-danger" href="<?= base_url(); ?>Page/aip_nsubmitted/<?= $row->district; ?>"><?= $s-$sum; ?></a>
                                                            
                                                        </td>
                                                        <td>
                                                        <?php $school = $this->SGODModel->count_all('schools','district',$row->district);  $s = $school->num_rows();?>
                                                            <a class="badge badge-purple" href="<?= base_url(); ?>Page/aip_sub_dist_school/<?= $row->district; ?>"><?= $s; ?></a>
                                                            
                                                        </td>
                                                    </tr>
                                                    <?php 
                                                        $ns = $s-$sum;
                                                        $add += $sum; 
                                                        $notsub += $ns; 
                                                    ?>
                                                    <?php } ?>
                                                    <tr class="bg-dark text-white">
                                                        <td></td>
                                                        <td>TOTAL</td>
                                                        <td><a href="<?= base_url(); ?>Page/aip_stat_sub_summary/<?= $fys; ?>" class="text-white"><?= $add; ?></a></td>
                                                        <td><a href="<?= base_url(); ?>Page/aip_stat_notsub_summary/<?= $fys; ?>" class="text-white"><?= $notsub; ?></a></td>
                                                        <td><?= $notsub+$add; ?></td>
                                                    </tr>
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

     <script type="text/javascript">
                $(document).on("click", ".open-AddBookDialog", function () {
                    var myBookId = $(this).data('id');
                    $(".modal-body #id").val( myBookId );

                    var fieldId = $(this).data('field');
                    $(".modal-body #field").val( fieldId );

                    var bcode = $(this).data('bcode');
                    $(".modal-body #bcode").val( bcode );
                });
    </script>
    


    </body>
</html>