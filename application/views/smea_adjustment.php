

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


                                    <a data-toggle="modal" class="open-AddBookDialog btn btn-success" href="#sop">SMEA Report</a>
                                    <a class="btn btn-primary" href="<?= base_url(); ?>Page/adjustment_new">New Adjustment</a>



                                    
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

                                      <?php 
                                      $val = array(
                                        "1" => "ACCESS",
                                        "2" => "EQUITY",
                                        "3" => "QUALITY",
                                        "4" => "RESILIENCY AND WELL-BEING",
                                        "5" => "ENABLING MECHANISM",
                                        "6" => "RESILIENCY",

                                      );
                                      
                                      ?>
                                  

                                        <h4 class="header-title mb-4"><?= $title; ?></h4>
                                        <div class="table-responsive">
                                        <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                                <thead>
                                                    <tr>
                                                        <th>SCHOOL IMPROVEMENT PROJECT TITLE</th>
                                                        <th>PILLAR</th>
                                                        <th>PERFORMANCE INDICATORS</th>
                                                        <th>TARGET</th>
                                                        <th>MANAGE</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach($data as $row){ ?>
                                                    <tr>
                                                            <td><?= $row->sip; ?></td>
                                                            <td><?= $val[$row->pillar]; ?></td> 
                                                            <td><?= $row->pi; ?></td>
                                                            <td>
                                                            <?php 
                                                            $pt = $this->Common->two_cond_row('sgod_sop_adjustment','aip_id',$row->id,'type','1');
                                                            $ft = $this->Common->two_cond_row('sgod_sop_adjustment','aip_id',$row->id,'type','2');
                                                            $fto = $this->Common->two_cond_row('sgod_sop_adjustment','aip_id',$row->id,'type','3');

                                                            if(empty($pt)){ 
                                                            ?>
                                                                <a href="<?= base_url(); ?>Page/smea_adjustment_new/1/<?= $row->id; ?>" class="btn btn-success btn-sm">PT</a> 
                                                            <?php }else{ ?>
                                                                <a href="<?= base_url(); ?>Page/smea_adjustment_update/1/<?= $pt->id; ?>" class="btn btn-warning btn-sm">EDIT PT</a> 
                                                            <?php } ?>

                                                            <?php if(empty($ft)){  ?>
                                                                <a href="<?= base_url(); ?>Page/smea_adjustment_new/2/<?= $row->id; ?>" class="btn btn-success btn-sm">FT (MOOE)</a> 
                                                            <?php }else{ ?>
                                                                <a href="<?= base_url(); ?>Page/smea_adjustment_update/2/<?= $ft->id; ?>" class="btn btn-warning btn-sm">EDIT FT (MOOE)</a> 
                                                            <?php } ?>

                                                            <?php if(empty($fto)){  ?>
                                                                    <a href="<?= base_url(); ?>Page/smea_adjustment_new/3/<?= $row->id; ?>" class="btn btn-success btn-sm">FT (OTHER SOURCES OF FUND)</a> 
                                                                <?php }else{ ?>
                                                                    <a href="<?= base_url(); ?>Page/smea_adjustment_update/3/<?= $fto->id; ?>" class="btn btn-warning btn-sm">EDIT FT (OTHER SOURCES OF FUND)</a>
                                                            <?php } ?>
                                                            </td>
                                                            <td>
                                                            <a href="<?= base_url(); ?>Page/adjustment_update/<?= $row->id; ?>" class="btn btn-purple btn-sm">Edit</a> 
                                                            <a href="<?= base_url(); ?>Page/adjustment_delete/<?= $row->id; ?>" class="btn btn-danger btn-sm">Delete</a> 
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
                
                
                                    <!-- FINANCIAL TARGET (MOOE) END -->

                                    <div id="sop" class="modal fade " tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="myModalLabel">SMEA</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                    </div>
                                                    <div class="modal-body">
                                                    <?= form_open('page/generate_smea#adjustment', ['target' => '_blank']); ?>
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