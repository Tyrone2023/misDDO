
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
                                    

                                    <?php if(!isset($sap->id)){?>
                                          <a class="btn btn-primary" href="#">Generate APP</a>
                                        <?php }else{ ?>
                                            <?php if($ft->fund_type == 0){?>
                                            <a class="btn btn-primary" href="<?= base_url(); ?>Page/generate_app"  target="_blank">Generate APP</a>

                                            <?php }else{ ?>
                                            <a class="btn btn-primary" href="<?= base_url(); ?>Page/generate_appv2"  target="_blank">Generate APP</a>
                                        <?php } ?>
                                        <?php } ?>   
                                        
                                        
                                        
                                        <a data-toggle="modal" class="open-AddBookDialog btn btn-warning" href="#appbp">APP Budget Percentage</a>
                                        <a class="btn btn-success" href="<?= base_url(); ?>Page/view_appv2">APP View V2</a>
                                    </h4>
                                    <div class="page-title-right">
                                        <ol class="breadcrumb p-0 m-0">
                                            <li class="breadcrumb-item"><a href="<?= base_url(); ?>Page/aip">AIP</a></li>
                                            <li class="breadcrumb-item"><a href="<?= base_url(); ?>Page/sop">SOP</a></li>
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
                                                        <th>SCHOOL IMPROVEMENT  PROJECT TITLE</th>
                                                        <th>MATERIALS</th>
                                                        <th>MANAGE</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    
                                                    <?php foreach($data as $row){ ?>
                                                    <tr>
                                                        <td>
                                                            <?php $aip=$this->SGODModel->one_cond_row('sgod_aip','id', $row->aip_id); ?>
                                                            <a href="<?= base_url(); ?>Page/group_app/<?= $aip->id; ?>"><?= $aip->sip_project; ?></a>
                                                        </td>
                                                        <td><?= $row->materials; ?>  </td>
                                                        <td>
                                                          <?php if($row->stat == 1){ ?>
                                                            <a class="btn btn-warning" href="app_edit/<?= $row->id; ?>">Update APP</a>
                                                        <?php  }else{ ?>
                                                          <a class="btn btn-primary" href="app_new/<?= $row->id; ?>">APP</a>
                                                        <?php  } ?>


                                                        
                                                        
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

                <div id="appbp" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="myModalLabel">APP BUDGET PERCENTAGE</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                    </div>
                                                    <div class="modal-body">

                                                    <?php $att = array('name' => 'renren'); ?>  
                                                    <?= form_open('page/app_percentage',$att); ?>
                                                         <div class="form-group">
                                                         <input type="hidden" name="id" readonly value="<?php if(!isset($sap->id)){echo 0;}else{echo $sap->id;} ?>" required class="form-control" >
                                                            <input type="hidden" name="school_id" readonly value="<?= $this->session->username; ?>" required class="form-control" >
                                                            <input type="hidden" name="b_code" readonly value="<?= $_SESSION['aip']; ?>" required class="form-control" >
                                                        </div>
                                                        <div class="form-group">
                                                            <label>MANDATORY BILLS <i>(in percent)</i></label>
                                                            <input type="text"  onkeyup="add()" value="<?php if(!isset($sap->id)){echo 20;}else{echo $sap->mb;} ?>" onkeypress="return isNumberKey(event)" name="mb" id="mb" class="form-control" />
                                                        </div>
                                                        <div class="form-group">
                                                            <label>MINOR REPAIR <i>(in percent)</i></label>
                                                            <input type="text"  onkeyup="add()" value="<?php if(!isset($sap->id)){echo 30;}else{echo $sap->mr;} ?>"  onkeypress="return isNumberKey(event)" name="mr" id="mr" class="form-control" />
                                                        </div>
                                                        <div class="form-group">
                                                            <label>TEACHING-LEARNING INSTRUCTION <i>(in percent)</i></label>
                                                            <input type="text"  onkeyup="add()" value="<?php if(!isset($sap->id)){echo 25;}else{echo $sap->tli;} ?>" onkeypress="return isNumberKey(event)" name="tli" id="tli" class="form-control" />
                                                        </div>
                                                        <div class="form-group">
                                                            <label>TRAININGS/SEMINAR/TRAVEL <i>(in percent)</i></label>
                                                            <input type="text"  onkeyup="add()" value="<?php if(!isset($sap->id)){echo 25;}else{echo $sap->tst;} ?>"  onkeypress="return isNumberKey(event)" name="tst" id="tst" class="form-control" />
                                                        </div>
                                                        <div class="form-group">
                                                            <label>TOTAL PERCENTAGE</label>
                                                            <input type="text" value="100" readonly name="total" id="pertotal" class="form-control" />
                                                        </div>

                                                        
                                                    <div class="modal-footer">
                                                        <input type="submit" id="persubmit" name="submit" class="btn btn-primary waves-effect waves-light" value="Submit" />
                                                    </div>
                                                    </form>
                                                </div>
                                                <!-- /.modal-content -->
                                            </div>
                                            <!-- /.modal-dialog -->
                                        </div>
                                        </div>

        

              


                
                                        </div>
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
                });

                function multiply(){
                  a=Number(document.abc.QTY.value);
                  b=Number(document.abc.PPRICE.value);
                  c=a*b;
                  document.abc.TOTAL.value=c;
                }
                
                function multiplyfeb(){
                  fa=Number(document.abc.FEBQTY.value);
                  fb=Number(document.abc.FEBPRICE.value);
                  fc=fa*fb;
                  document.abc.FEBTOTAL.value=fc;
                }

                function multiplymar(){
                  a=Number(document.abc.MARQTY.value);
                  b=Number(document.abc.MARPRICE.value);
                  c=a*b;
                  document.abc.MARTOTAL.value=c;
                }

                function multiplyapril(){
                  a=Number(document.abc.APQTY.value);
                  b=Number(document.abc.APPRICE.value);
                  c=a*b;
                  document.abc.APTOTAL.value=c;
                }

                function multiplymay(){
                  a=Number(document.abc.MAYQTY.value);
                  b=Number(document.abc.MAYPRICE.value);
                  c=a*b;
                  document.abc.MAYTOTAL.value=c;
                }

                function multiplyjune(){
                  a=Number(document.abc.JUNQTY.value);
                  b=Number(document.abc.JUNPRICE.value);
                  c=a*b;
                  document.abc.JUNTOTAL.value=c;
                }

                function multiplyjul(){
                  a=Number(document.abc.JULQTY.value);
                  b=Number(document.abc.JULPRICE.value);
                  c=a*b;
                  document.abc.JULTOTAL.value=c;
                }

                function multiplyaug(){
                  a=Number(document.abc.AUGQTY.value);
                  b=Number(document.abc.AUGPRICE.value);
                  c=a*b;
                  document.abc.AUGTOTAL.value=c;
                }

                function multiplysep(){
                  a=Number(document.abc.SEPQTY.value);
                  b=Number(document.abc.SEPPRICE.value);
                  c=a*b;
                  document.abc.SEPTOTAL.value=c;
                }

                function multiplyoct(){
                  a=Number(document.abc.OCTQTY.value);
                  b=Number(document.abc.OCTPRICE.value);
                  c=a*b;
                  document.abc.OCTTOTAL.value=c;
                }

                function multiplynov(){
                  a=Number(document.abc.NOVQTY.value);
                  b=Number(document.abc.NOVPRICE.value);
                  c=a*b;
                  document.abc.NOVTOTAL.value=c;
                }

                function multiplydec(){
                  a=Number(document.abc.DECQTY.value);
                  b=Number(document.abc.DECPRICE.value);
                  c=a*b;
                  document.abc.DECTOTAL.value=c;
                }

                function add(){
                  a=Number(document.renren.mb.value);
                  b=Number(document.renren.mr.value);
                  c=Number(document.renren.tli.value);
                  d=Number(document.renren.tst.value);
                  e=a+b+c+d;
                  document.renren.pertotal.value=e;
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