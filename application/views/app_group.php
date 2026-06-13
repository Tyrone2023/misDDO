
            <!-- ============================================================== -->
            <!-- Start Page Content here -->
            <!-- ============================================================== -->

            <div class="content-page">
                <div class="content">

                    <!-- Start Content-->
                    <div class="container-fluid">

                    <div class="row">
                            <div class="col-lg-12">
                                <div class="card">
                                    <div class="card-body">
                                        <h4 class="header-title mb-4"><?= $title; ?></h4>

                                        <ul class="list-unstyled">
                                            <li><b>SCHOOL IMPROVEMENT PROJECT TITLE :</b> <?= $aip->sip_project; ?></li>
                                            <li><b>STRATEGY ACTIVITIES :</b> <?= $aip->strategy; ?></li>
                                            <li><b>CATEGORY :</b> <?= $aip->category; ?></li>
                                            <li><b>BUDGET PER ACTIVITY  :</b> <?= number_format($aip->budget); ?></li>
                                            <li><b>MATERIALS  :</b> <?= $aip->materials; ?></li>
                                        </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                        <!-- start page title -->
                        <div class="row">
                            <div class="col-12">
                                <div class="page-title-box">
                                <?php $sap = $this->SGODModel->two_cond_row('sgod_app_percentage','b_code',$_SESSION['aip'],'fy',$_SESSION['fy']); ?>
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
                                    <?php 
                                        if(empty($aip_s)){
                                       ?>
                                          <a data-toggle="modal" data-field="" data-id="" class="open-AddBookDialog btn btn-primary" href="#addBookDialog">Add Material</a>
                                          <?php }elseif(!empty($aip_s) && $aip_s->status != 1){ ?>

                                            <a data-toggle="modal" data-field="" data-id="" class="open-AddBookDialog btn btn-primary" href="#addBookDialog">Add Material</a>

                                    <?php } ?>
                                    </h4>
                                    <div class="page-title-right">
                                        <ol class="breadcrumb p-0 m-0">
                                            <li class="breadcrumb-item"><a href="<?= base_url(); ?>Page/aip">ANNUAL IMPLEMENTATION PLAN</a></li>
                                            <li class="breadcrumb-item"><a href="<?= base_url(); ?>Page/sop">SCHOOL OPERATIONAL PLAN</a></li>
                                            <li class="breadcrumb-item"><a href="<?= base_url(); ?>Page/view_app">ANNUAL PROCUREMENT PLAN (APP)</a></li>
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

                                        <?php 
                                            
                                        ?>

                                        <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                                <thead>
                                                    <tr>
                                                        <th>MATERIALS</th>
                                                        <th>MANAGE </th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    
                                                    <?php foreach($data as $row){ ?>
                                                    <tr>
                                                        <td><?= $row->materials; ?>  </td>
                                                        <td>
                                                          <?php if($row->stat == 1){ ?>
                                                            <a class="btn btn-warning" href="<?= base_url(); ?>Page/gapp_edit/<?= $row->id; ?>">Update APP</a>
                                                        <?php  }else{ ?>
                                                          <a class="btn btn-primary" href="<?= base_url(); ?>Page/gapp_new/<?= $row->id; ?>">APP</a>
                                                        <?php  } ?>

                                                        <?php 
                                        if(empty($aip_s)){
                                       ?>
                                          <a class="btn btn-danger" onclick="return confirm('Are you sure?')" href="<?= base_url(); ?>Page/app_delete/<?= $row->id; ?>/<?= $aip->id; ?>">Delete</a>
                                          <?php }elseif(!empty($aip_s) && $aip_s->status != 1){ ?>

                                            <a class="btn btn-danger" onclick="return confirm('Are you sure?')" href="<?= base_url(); ?>Page/app_delete/<?= $row->id; ?>/<?= $aip->id; ?>">Delete</a>

                                    <?php } ?>

                                                        

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
                <!-- end content -->

               
                
                                        </div>
                                        </div>
                                        <!-- end content -->


                                        <div id="addBookDialog" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="myModalLabel">Add New Material</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <?= form_open('Page/group_app/'.$aip->id); ?>
                                                        <div class="form-group col-md-12">
                                                            <label >Material</label>
                                                            <input type="text"  name="material" class="form-control" />
                                                        </div>
                                                        
                                                        <div class="modal-body">
                                                            <input type="hidden" name="aip_id"  value="<?= $aip->id; ?>" />
                                                            <input type="hidden" name="school_id" value="<?= $aip->school_id; ?>" />
                                                            <input type="hidden" name="fy" value="<?= $aip->fy; ?>">
                                                            <input type="hidden" name="b_code" value="<?= $aip->b_code; ?>" />

                                                            <input type="hidden" name="aip_marterials" value="<?= $aip->materials; ?>"  class="form-control" />
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

     
       


    </body>
</html>