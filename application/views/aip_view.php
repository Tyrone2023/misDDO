

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

                                       <?php 
                                        if(empty($aip_s)){
                                       ?>
                                                           
                                          <a href="<?= base_url().'Page/'.$b_link; ?>"><button type="button" class="btn btn-primary waves-effect waves-light" ><?= $b_label; ?></button></a>
                                        
                                          <?php }elseif(!empty($aip_s) && $aip_s->status != 1){ ?>

                                            <a href="<?= base_url().'Page/'.$b_link; ?>"><button type="button" class="btn btn-primary waves-effect waves-light" ><?= $b_label; ?></button></a>

                                        <?php } ?>

                                        <a class="btn btn-success waves-effect waves-light" href="<?= base_url(); ?>Page/generate_aip"  target="_blank">Generate AIP</a>
                                        <?php if(empty($submit_aip)){?>

                                            <?php if($alloc->alloc_type == 'SNED Fund'){?>
                                        <a class="btn btn-warning waves-effect waves-light" onclick="return confirm('Are you sure?')" href="<?= base_url(); ?>Page/submit_aip_sned">Submit for Evaluation</a>
                                            <?php }elseif($alloc->alloc_type == 'SBFP Fund'){ ?> 
                                        <a class="btn btn-warning waves-effect waves-light" onclick="return confirm('Are you sure?')" href="<?= base_url(); ?>Page/submit_aip_sbfp">Submit for Evaluation</a>
                                                <?php }else{ ?> 
                                        <a class="btn btn-warning waves-effect waves-light" onclick="return confirm('Are you sure?')" href="<?= base_url(); ?>Page/submit_aip">Submit for Evaluation</a>
                                                <?php } ?>

                                        <?php }else{ ?>
                                            <a class="btn btn-purple waves-effect waves-light" href="<?= base_url(); ?>Page/aip_action_list">Submitted AIP </a>
                                        <?php } ?>


                                        <?php  if(!empty($aip_s)){ ?>  
                                            <?php  if($aip_s->status == 1){ ?>    
                                        <?php  if(empty($aip_r)){ ?>        

                                            <a data-toggle="modal" data-field="<?= $aip_s->school_id; ?>" data-id="<?= $aip_s->id; ?>" class="open-AddBookDialog btn btn-warning" href="#open">Request for Unlock</a>

                                        <?php }else{ ?>
                                            <?php if($aip_r->stat == 1){?>
                                                <?php 
                                                    $req = $this->SGODModel->count_all_two_cond('sgod_aip_request','stat',1,'b_code',$_SESSION['aip']); 
                                                    if($req->num_rows() <= 2){ 
                                                ?>
                                            <a data-toggle="modal"  data-field="<?= $aip_s->school_id; ?>" data-id="<?= $aip_s->id; ?>" class="open-AddBookDialog btn btn-warning" href="#open">Request for Unlock</a>

                                        <?php }}}}} ?>

                                        <?php  if(isset($aip_s->status) && $aip_s->status == 1){ ?>
                                            <a target="_blank" href="<?= base_url(); ?>page/generate_ca/<?= $this->session->username; ?>/<?= $this->session->aip; ?>/<?= $aip_s->id; ?>"  class="btn btn-info">Certificate of Acceptance</a>
                                            
                                        <?php } ?>

                                        <a href="<?= base_url(); ?>Page/smeav2" class="btn btn-purple">SMEA</a>
                                    </h4>
                                    
                                    <div class="page-title-right">
                                        <ol class="breadcrumb p-0 m-0">
                                            <li class="breadcrumb-item"><a href="<?= base_url(); ?>Page/sop">SOP</a></li>
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
                                                        <th>SCHOOL IMPROVEMENT PROJECT TITLE</th>
                                                        <th>STRATEGY ACTIVITIES</th>
                                                        <th>PILLAR</th>
                                                        <th>DOMAIN</th>
                                                        <th>STRAND</th>
                                                        <th>PIA's</th>
                                                        <th>PROJECT OBJECTIVE</th>
                                                        <th>OUTPUT FOR THE YEAR</th>
                                                        <th>PERFORMANCE INDICATORS</th>
                                                        <th>MOVs</th>
                                                        <th>PERSON(S) RESPONSIBLE</th>
                                                        <th>SCHEDULE</th>
                                                        <th>VENUE</th>
                                                        <th>BUDGET PER ACTIVITY</th>
                                                        <th>BUDGET SOURCE</th>
                                                        <th>MATERIALS</th>
                                                        <th>BATCH CODE</th>
                                                        <th>FISCAL YEAR</th>
                                                        <th>MANAGE</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach($data as $row){ ?>
                                                    <tr>
                                                        <td><?= $row->sip_project; ?></td>
                                                        <td><?= $row->strategy; ?></td>
                                                        <td><?= $row->pillar; ?></td> 
                                                        <td><?= $row->domain; ?></td>
                                                        <td><?= $row->strand; ?></td> 
                                                        <td><?= $row->pia; ?></td> 
                                                        <td><?= $row->sip_pObjective; ?></td> 
                                                        <td><?= $row->sip_output; ?></td>
                                                        <td><?= $row->pi; ?></td>
                                                        <td><?= $row->movs; ?></td>
                                                        <td><?= $row->pr; ?></td>
                                                        <td><?= $row->schedule; ?></td>
                                                        <td><?= $row->venue; ?></td>
                                                        <td><?= $row->budget; ?></td>
                                                        <td><?= $row->budget_source; ?></td>
                                                        <td><?= $row->materials; ?></td>
                                                        <td><?= $row->b_code; ?></td>
                                                        <td><?= $row->fy; ?></td> 
                                                        <td>
                                                            <?php 
                                                              $check = $this->SGODModel->three_cond_row('sgod_aip_submit','school_id',$row->school_id,'fy',$row->fy,'b_code',$row->b_code);
                                                              if(!empty($check)){
                                                              if($check->status == 0){
                                                            ?>
                                                                <a class="btn btn-success sm" href="<?= base_url(); ?>Page/aip_edit/<?= $row->id; ?>">EDIT</a>  &nbsp;
                                                                <a class="btn btn-danger sm" onclick="return confirm('Are you sure?')" href="<?= base_url(); ?>Page/aip_delete/<?= $row->id; ?>">DELETE</a>
                                                            <?php }}else{ ?>
                                                                <a class="btn btn-success sm"  href="<?= base_url(); ?>Page/aip_edit/<?= $row->id; ?>">EDIT</a>  &nbsp;
                                                                <a class="btn btn-danger sm" onclick="return confirm('Are you sure?')" href="<?= base_url(); ?>Page/aip_delete/<?= $row->id; ?>">DELETE</a>
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

                    
                                        
                                        <div id="open" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="myModalLabel">Specify the reason for your request</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                    </div>
                                                    <div class="modal-body">
                                                    <?= form_open('Page/aip_request'); ?>
                                                        <input type="hidden" name="id" id="id">
                                                        <input type="hidden" name="school_id" id="field">
                                                        <div class="form-group">
                                                            <textarea name="remarks" required id="" class="form-control"></textarea>
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
     
     <SCRIPT language=Javascript>
        function isNumberKey(evt){
            var charCode = (evt.which) ? evt.which : evt.keyCode;
            if (charCode != 46 && charCode > 31 
              && (charCode < 48 || charCode > 57))
              return false;

            return true;
        }
      </SCRIPT>
      <script type="text/javascript">
                $(document).on("click", ".open-AddBookDialog", function () {
                    var myBookId = $(this).data('id');
                    $(".modal-body #id").val( myBookId );

                    var fieldId = $(this).data('field');
                    $(".modal-body #field").val( fieldId );

                    var sId = $(this).data('s_id');
                    $(".modal-body #s_id").val( sId );
                });
    </script>


    </body>
</html>