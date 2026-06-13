

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
                                    

                                        <!-- <a data-toggle="modal" data-field="" data-id="" class="open-AddBookDialog btn btn-success" href="#addBookDialog">Upload AIP</a> -->
                                    </h4>
                                    <div class="page-title-right">
                                        <ol class="breadcrumb p-0 m-0">
                                            <?php if ($this->session->position == 'Admin'  || $this->session->position == "smme" || $this->session->position == "Accountant") { ?>
                                                <li class="breadcrumb-item"><a href="<?= base_url(); ?>Page/aip_sub">SUBMITTED AIP</a></li>
                                            <?php }else{ ?>
                                                <li class="breadcrumb-item"><a href="<?= base_url(); ?>Page/aip_action_list">SUBMITTED AIP</a></li>
                                            <?php } ?>
                                        </ol>
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                        </div>
                        <!-- end page title -->

                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="timeline" dir="ltr">
                                            <article class="timeline-item alt">
                                                <div class="text-right">
                                                    <div class="time-show first">
                                                        <a data-toggle="modal" data-field="" data-id="" class="open-AddBookDialog btn btn-primary w-lg" href="#comment">Comment/Remarks</a>
                                                    </div>
                                                </div>
                                            </article>

                                            <?php foreach($data as $row){ ?>
                                                <?php 
                                                $s = $this->SGODModel->one_cond_row('sgod_aip_submit', 'id',$row->submit_id);
                                                $user = $this->SGODModel->one_cond_row('users', 'username',$row->res);
                                                if($row->res != $s->res ){
                                                    $alt = "alt";
                                                }else{
                                                    $alt = 'none';
                                                }
                                                    ?>
                                         
                                            <article class="timeline-item <?= $alt; ?>">
                                                <div class="timeline-desk">
                                                    <div class="panel">
                                                        <div class="timeline-box ">
                                                            <span class="arrow-alt"></span>
                                                            <span class="timeline-icon"><i class="mdi mdi-checkbox-blank-circle-outline"></i></span>
                                                            <h4 class=""><?= $row->tdate; ?> <?php // $user->fname.' '.$user->mname.' '.$user->lname; ?></h4>
                                                            <p class="timeline-date text-muted"><small><?= $row->dtime; ?></small></p>
                                                            <p><?= $row->remarks; ?></p>

                                                            <?php
                                                             if($this->session->username == "sa" || $this->session->position == "smme" || $this->session->position == "Accountant"){
                                                             if($row->res == "sa" || $row->res == "smme" || $row->res == "Accountant" ){ ?>
                                                                <a class="text text-danger" href="<?= base_url(); ?>Page/aip_track_delete/<?= $row->id; ?>/<?= $this->uri->segment(3); ?>" onclick="return confirm('Are you sure?')">Delete</a>
                                                            <?php }} ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </article>
                                            <?php }  ?>
                                        
                                        </div>
                                        <!-- end timeline -->
                                    </div>
                                </div>
                                <!-- end card -->
                            </div>
                        </div>
                        <!-- end row -->


                        
                       

                    </div>
                    <!-- end container-fluid -->

                </div>
                <!-- end content -->

                <div id="comment" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="myModalLabel">Comments/Remarks</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                    </div>
                                                    <div class="modal-body">
                                                    <?= form_open('Page/remarks_aip'); ?>
                                                        <input type="hidden" name="id"  value="<?= $this->uri->segment(3); ?>">
                                                        <input type="hidden" name="school_id"  value="<?= $aip->school_id; ?>">
                                                        <div class="form-group">
                                                            <label>Comment/Remarks</label>
                                                            <textarea name="remarks" class="form-control"></textarea>
                                                        </div>

                                                           

                                                    </div>
                                                    <div class="modal-footer">
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
                });
    </script>
    


    </body>
</html>