

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
                                    <!-- <a data-toggle="modal" class="open-AddBookDialog btn btn-primary" href="#g_aip">Generate SOP</a> -->
                                    <a class="btn btn-success waves-effect waves-light"  href="<?= base_url(); ?>Page/generate_sop"  target="_blank">Generate SOP</a>
                                    </h4>

                                    <div class="page-title-right">
                                        <ol class="breadcrumb p-0 m-0">
                                            <li class="breadcrumb-item"><a href="<?= base_url(); ?>Page/aip">AIP</a></li>
                                            <li class="breadcrumb-item"><a href="<?= base_url(); ?>Page/view_app" >APP</a></li>
                                            <li class="breadcrumb-item"><a href="<?= base_url(); ?>Page/smeav2" >SMEA</a></li>
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
                                                        <td>
                                                            <?php 
		                                                        $soppt = $this->SGODModel->two_cond_row('sgod_sop','aip_id',$row->id,'type','1');
                                                                if(!empty($soppt)){ ?>
                                                                    <a href="sop_edit/<?= $soppt->id; ?>" class="btn btn-warning">EDIT PT</a> 
                                                                <?php }else{ ?>
                                                                    <a data-toggle="modal" data-field="1" data-id="<?= $row->id; ?>" class="open-AddBookDialog btn btn-info" href="#sop">PT</a> 
                                                            <?php } ?>

                                                            <?php 
		                                                        $sopft = $this->SGODModel->two_cond_row('sgod_sop','aip_id',$row->id,'type','2');
                                                                if(!empty($sopft)){ ?>
                                                                    <a href="sop_edit/<?= $sopft->id; ?>" class="btn btn-warning">EDIT FT (MOOE)</a> 
                                                                <?php }else{ ?>
                                                                    <a data-toggle="modal" data-field="2" data-id="<?= $row->id; ?>" class="open-AddBookDialog btn btn-primary" href="#sop">FT (MOOE)</a> 
                                                            <?php } ?>

                                                            <?php 
		                                                        $sopfo = $this->SGODModel->two_cond_row('sgod_sop','aip_id',$row->id,'type','3');
                                                                if(!empty($sopfo)){ ?>
                                                                    <a href="sop_edit/<?= $sopfo->id; ?>" class="btn btn-warning">EDIT FT (OTHER SOURCES OF FUND)</a> 
                                                                <?php }else{ ?>
                                                                    <a data-toggle="modal" data-field="3" data-id="<?= $row->id; ?>" class="open-AddBookDialog btn btn-purple" href="#sop">FT (OTHER SOURCES OF FUND)</a>
                                                            <?php } ?>
                                                            
                                                            
                                                            
                                                        </td> 
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
                
                <!-- FINANCIAL TARGET (MOOE) -->
                <div id="sop" class="modal fade " tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="myModalLabel">TARGET</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                    </div>
                                                    <div class="modal-body">
                                                    <?= form_open('page/sop'); ?>
                                                         <div class="form-group">
                                                            <input type="hidden" name="aip_id" value="" id="id" />
                                                            <input type="hidden"  name="type" value="" id="field" />

                                                            <label>1ST QUARTER</label>
                                                            <input type="text" id="value1" onkeypress="return isNumberKey(event)" oninput="calculateTotal()" name="q1" value="" class="form-control amount" >
                                                        </div>
                                                        <div class="form-group">
                                                            <label>2ND QUARTER</label>
                                                            <input type="text"  id="value2" onkeypress="return isNumberKey(event)" oninput="calculateTotal()"  name="q2" class="form-control amount" />
                                                        </div>
                                                        <div class="form-group">
                                                            <label>3RD QUARTER</label>
                                                            <input type="text"  id="value3" onkeypress="return isNumberKey(event)" oninput="calculateTotal()"  name="q3" class="form-control amount" />
                                                        </div>
                                                        <div class="form-group">
                                                            <label>4TH QUARTER</label>
                                                            <input type="text"  id="value4" onkeypress="return isNumberKey(event)" oninput="calculateTotal()" name="q4" class="form-control amount" />
                                                            
                                                        </div>
                                                        <div class="form-group">
                                                            <label><a href="javascript:sumInputs()">TOTAL</a></label>
                                                            <input type="text"  name="total" id="total" class="form-control amount" />
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
                                    <!-- FINANCIAL TARGET (MOOE) END -->

                                    <div id="g_aip" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="myModalLabel">FILTER ANNUAL IMPLEMENTATION PLAN</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                    </div>
                                                    <div class="modal-body">
                                                    <?= form_open('page/generate_sop'); ?>
                                                         <div class="form-group">
                                                            <label>School ID</label>
                                                            <input type="text" name="school_id" readonly value="<?= $this->session->username; ?>" required class="form-control" >
                                                        </div>
                                                        <div class="form-group">
                                                            <label>YEAR</label>
                                                            <input type="text"  name="fy" class="form-control" />
                                                        </div>
                                                        <div class="form-group">
                                                            <label>BATCH CODE</label>
                                                            <select class="form-control" name="b_code" required>
                                                                <option></option>
                                                                <?php foreach($ssa as $row){
                                                                    echo "<option value='".$row->alloc_batch."'>".$row->alloc_batch." - ". $row->alloc_group." : PHP ".number_format($row->alloc_amount)."</option>";
                                                                   }
                                                                ?>
                                                                
                                                            </select>
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