

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

                                    <?php if($this->session->position == 'Admin' || $this->session->position == "Accountant" || $this->session->position == "smme"){ ?>
                                        <a href="<?= base_url(); ?>Page/aip_sub_district" class="btn btn-info">BY DISTRICT</a>
                                        <a href="<?= base_url(); ?>Page/aip_sub" class="btn btn-success">SUBMITTED AIP</a>
                                        <a href="<?= base_url(); ?>Page/aip_approved" class="btn btn-primary">APPROVED AIP</a>
                                        <a href="<?= base_url(); ?>Page/aip_requested" class="btn btn-info">REQUESTED</a>
                                        <a href="<?= base_url(); ?>Page/aip_evaluate" class="btn btn-warning">EVALUATE</a>
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
                                        <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                                <thead>
                                                    <tr>
                                                        <th>Budget Code</th>
                                                        <th>SCHOOL NAME</th>
                                                        <th>GROUP</th>
                                                        <th>YEAR</th>
                                                        <th>MANAGE</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach($data as $row){ 
                                                        $school=$this->SGODModel->get_data_by_id('schools', 'schoolId',$row->school_id);
                                                        $sa=$this->SGODModel->two_cond_row('sgod_school_allocation', 'schoolId',$row->school_id,'alloc_batch',$row->b_code);
                                                        $com = $this->SGODModel->one_cond_count_rows('sgod_aip_track','submit_id',$row->id);
                                                        if($com->num_rows() <= 1){
                                                        ?>
                                                    <tr>
                                                        <td><?= $row->b_code; ?></td>
                                                        <td><?= $school->schoolName; ?></td>
                                                        <td><?= $sa->alloc_group; ?></td>
                                                        <td><?= $row->fy; ?></td>  
                                                        <td>
                                                        <?php if($this->session->position == "Admin" || $this->session->position == "Accountant" || $this->session->position == "smme"){ ?>
                                                            <a href="<?= base_url(); ?>Page/aip_track/<?= $row->id; ?>" class="btn btn-warning">View Status</a> &nbsp;
                                                            <?php if($row->status != 1){ ?>
                                                                <a href="<?= base_url(); ?>Page/view_sip_admin/<?= $row->school_id; ?>" class="btn btn-success" target="_blank">SIP</a> &nbsp;
                                                                <a href="<?= base_url(); ?>Page/aip_admin/<?= $row->school_id.'/'.$row->fy.'/'.$row->b_code.'/'.$row->id; ?>" class="btn btn-success" target="_blank">Evaluate AIP</a> &nbsp;
                                                                <a href="<?= base_url(); ?>Page/generate_sop_admin/<?= $row->school_id.'/'.$row->fy.'/'.$row->b_code.'/'.$row->id; ?>" class="btn btn-primary" target="_blank">Evaluate SOP</a> &nbsp;
                                                                
                                                                <?php $sap = $this->SGODModel->two_cond_row('sgod_app_percentage','b_code',$row->b_code,'fy',$row->fy); ?>
                                                                <?php if(!isset($sap->id)){?>
                                                                <a href="#" class="btn btn-info">Evaluate APP</a> &nbsp;
                                                                <?php }else{ ?>
                                                                    <a href="<?= base_url(); ?>Page/generate_app_admin/<?= $row->school_id.'/'.$row->fy.'/'.$row->b_code.'/'.$row->id; ?>" class="btn btn-info" target="_blank">Evaluate APP</a> &nbsp;
                                                                <?php } ?>
                                                                <a data-toggle="modal" data-field="<?= $row->fy; ?>" data-id="<?= $row->school_id; ?>" data-bcode="<?= $row->b_code; ?>" class="open-AddBookDialog btn btn-purple" href="#rca">Evaluate RCA</a>&nbsp;
                                                                <a href="<?= base_url(); ?>Page/approved_aip/<?= $row->id; ?>" class="btn btn-warning" onclick="return confirm('Are you sure?')">Approved</a>&nbsp;
                                                            <?php } else { ?> 
                                                                <?php $req = $this->SGODModel->two_cond_orderby('sgod_aip_request','fy', $row->fy, 'b_code', $row->b_code, 'id', 'DESC'); ?>

                                                                <?php if(!empty($req) && $req->stat == 0){?>
                                                                    <a data-toggle="modal" data-field="<?= $row->school_id; ?>" data-id="<?= $row->id; ?>" class="open-AddBookDialog btn btn-primary" href="#open">Open</a>
                                                                <?php } ?>

                                                            <?php } ?>
                                                            
                                                        <?php }else{ ?>
                                                            <a href="<?= base_url(); ?>Page/aip_track/<?= $row->id; ?>" class="btn btn-primary">View Status</a>&nbsp;
                                                            
                                                        <?php } ?>
                                                        </td> 
                                                    </tr>
                                                    <?php } } ?>
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

                <div id="open" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="myModalLabel">Open AIP</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                    </div>
                                                    <div class="modal-body">
                                                    <?= form_open('Page/open_aip'); ?>
                                                        <input type="hidden" name="id" id="id">
                                                        <input type="hidden" name="school_id" id="field">
                                                        <div class="form-group">
                                                            <label>Reason</label>
                                                            <textarea required name="remarks" id="" class="form-control"></textarea>
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


                                        <div id="rca" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="myModalLabel">FILTER ANNUAL IMPLEMENTATION PLAN </h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                    </div>
                                                    <div class="modal-body">
                                                    <?= form_open('Page/generate_rca_admin'); ?>
                                                    <input type="hidden" id="id" name="sid">
                                                    <input type="hidden" id="field" name="fy">
                                                    <input type="hidden" id="bcode" name="bcode">
                                                         

                                                        <div class="form-group">
                                                            <label>SELECT MONTH</label>
                                                            <select class="form-control" name="month" required>
                                                                <option></option>
                                                                <?php 
                                                                    $month = array('January' => 'jan', 'February' => 'feb', 'March' => 'mar', 'April'=> 'april', 'May' => 'may', 'June' => 'june', 'July' => 'july', 'August' => 'aug', 'September' => 'sept', 'October' => 'oct', 'November' => 'nov', 'December' => 'dec'); 
                                                                    foreach($month as $m => $val){
                                                                ?>
                                                                <option value="<?= $val; ?>"><?= $m; ?></option>
                                                                <?php } ?> 
                                                                
                                                            </select>
                                                        </div>

                                                        
                                                    <div class="modal-footer">
                                                        <input type="submit" name="aip" class="btn btn-primary waves-effect waves-light" value="Submit" />
                                                    </div>
                                                    </form>
                                                </div>
                                                <!-- /.modal-content -->
                                            </div>
                                            <!-- /.modal-dialog -->
                                        </div>
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

                    var bcode = $(this).data('bcode');
                    $(".modal-body #bcode").val( bcode );
                });
    </script>
    


    </body>
</html>