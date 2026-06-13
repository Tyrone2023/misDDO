

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
                                    <a data-toggle="modal" class="open-AddBookDialog btn btn-primary" href="#sop">SMEA Report</a>
                                    <a target="_blank" class="btn btn-purple" href="<?= base_url(); ?>Page/smea_summary">SMEA Summary</a>

                                    <?php if($smea->num_rows() == 0){?>

                                        <a class="btn btn-warning waves-effect waves-light" onclick="return confirm('Are you sure?')"  href="<?= base_url(); ?>Page/submit_smea/<?= $this->session->username; ?>/<?= $_SESSION['aip']; ?>/<?= $_SESSION['fy']; ?>" >Submit SMEA</a>
                                    <?php } ?>


                                    <div class="btn-group">
                                        <button type="button" class="btn btn-secondary dropdown-toggle waves-effect" data-toggle="dropdown" aria-expanded="false"> Qualitative Data <i class="mdi mdi-chevron-down"></i> </button>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item" href="<?= base_url(); ?>Page/innovations">Innovations</a>
                                            <a class="dropdown-item" href="<?= base_url(); ?>Page/quick_wins">Quick Wins and Best Practices</a>
                                            <a class="dropdown-item" href="<?= base_url(); ?>Page/policy">Policy Issues & Updates</a>
                                        </div>
                                    </div>

                                    <a  class="btn btn-primary" href="<?= base_url(); ?>Page/adjustment">SOP Adjustment</a>


                                    
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
                                                        <!-- <th>SCHOOL IMPROVEMENT PROJECT TITLE</th> -->
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
                                                        <!-- <td>
                                                            <?php 
		                                                        $soppt = $this->SGODModel->two_cond_row('sgod_sop','aip_id',$row->id,'type','1');
                                                                if(!empty($soppt)){ ?>
                                                                    <a href="smea_edit/<?= $soppt->id; ?>" class="btn btn-warning">EDIT PT</a> 
                                                                <?php }else{ ?>
                                                                    <a data-toggle="modal" data-field="1" data-id="<?= $row->id; ?>" class="open-AddBookDialog btn btn-info" href="#sop">PT</a> 
                                                            <?php } ?>

                                                            <?php 
		                                                        $sopft = $this->SGODModel->two_cond_row('sgod_sop','aip_id',$row->id,'type','2');
                                                                if(!empty($sopft)){ ?>
                                                                    <a href="smea_edit/<?= $sopft->id; ?>" class="btn btn-warning">EDIT FT (MOOE)</a> 
                                                                <?php }else{ ?>
                                                                    <a data-toggle="modal" data-field="2" data-id="<?= $row->id; ?>" class="open-AddBookDialog btn btn-primary" href="#sop">FT (MOOE)</a> 
                                                            <?php } ?>

                                                            <?php 
		                                                        $sopfo = $this->SGODModel->two_cond_row('sgod_sop','aip_id',$row->id,'type','3');
                                                                if(!empty($sopfo)){ ?>
                                                                    <a href="smea_edit/<?= $sopfo->id; ?>" class="btn btn-warning">EDIT FT (OTHER SOURCES OF FUND)</a> 
                                                                <?php }else{ ?>
                                                                    <a data-toggle="modal" data-field="3" data-id="<?= $row->id; ?>" class="open-AddBookDialog btn btn-purple" href="#sop">FT (OTHER SOURCES OF FUND)</a>
                                                            <?php } ?>
                                                            
                                                            
                                                            
                                                        </td>  -->
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
                
                
                                    <!-- FINANCIAL TARGET (MOOE) END -->

                                    <div id="sop" class="modal fade " tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="myModalLabel">SMEA</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                    </div>
                                                    <div class="modal-body">
                                                    <?= form_open('page/generate_smea', ['target' => '_blank']); ?>
                                                        <div class="form-group col-md-12">
                                                            <label >Select Qaurter</label>
                                                            <select class="form-control" name="q" required>
                                                                <option></option>
                                                                <option value="1">Quarter 1</option>
                                                                <option value="2">Quarter 2</option>
                                                                <option value="3">Quarter 3</option>
                                                                <option value="4">Quarter 4</option>
                                                            </select>


                                                        </div>
                                                        
                                                    <div class="modal-footer">
                                                        <input type="submit" name="submit"  class="btn btn-primary waves-effect waves-light" value="Submit" />
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

        <script type="text/javascript">
                $(document).on("click", ".open-AddBookDialog", function () {
                    var myBookId = $(this).data('id');
                    $(".modal-body #id").val( myBookId );

                    var fieldId = $(this).data('field');
                    $(".modal-body #field").val( fieldId );
                });
            </script>


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

    <script>
        function calculateTotal() {
            var value1 = parseFloat(document.getElementById('value1').value) || 0;
            var value2 = parseFloat(document.getElementById('value2').value) || 0;
            var value3 = parseFloat(document.getElementById('value3').value) || 0;
            var value4 = parseFloat(document.getElementById('value4').value) || 0;
            var total = value1 + value2 + value3 + value4;
            
            document.getElementById('total').value = total;
        }
    </script>

<SCRIPT language=Javascript>
        function isNumberKey(evt){
            var charCode = (evt.which) ? evt.which : evt.keyCode;
            if (charCode != 46 && charCode > 31 
              && (charCode < 48 || charCode > 57))
              return false;

            return true;
        }
      </SCRIPT>


    


    </body>
</html>