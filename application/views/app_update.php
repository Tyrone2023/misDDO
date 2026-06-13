

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
                                    <!-- <h4 class="page-title" id="myLargeModalLabel">                            
                                        <button type="button" class="btn btn-primary waves-effect waves-light" data-toggle="modal" data-target=".bs-example-modal-lg">+ ADD NEW</button>
                                        <a href="acc" class="btn btn-info waves-effect waves-light" target="_blank">REPORTS</a>
                                    </h4> -->
                                    <!-- <div class="page-title-right">
                                        <ol class="breadcrumb p-0 m-0">
                                            <li class="breadcrumb-item">SGOD Management System v1.0</li>
                                        </ol>
                                    </div> -->
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
                                        <?php $aip = $this->SGODModel->one_cond_row('sgod_aip', 'id', $data->aip_id); ?>

                                        <ul class="list-unstyled">
                                            <li><b>SCHOOL IMPROVEMENT PROJECT TITLE :</b> <?= $aip->sip_project; ?></li>
                                            <li><b>STRATEGY ACTIVITIES :</b> <?= $aip->strategy; ?></li>
                                            <li><b>CATEGORY :</b> <?= $aip->category; ?></li>
                                            <li><b>BUDGET PER ACTIVITY  :</b> <?= number_format($aip->budget); ?></li>
                                            <li><b>MATERIALS  :</b> <?= $aip->materials; ?></li>
                                        </ul>
                                        <hr />
                                        <h4 class="text-success">MATERIAL: <?= $data->materials; ?></h4>
                                        
                                        
                     

                        <?php $att = array('class' => 'parsley-examples','name' => 'abc',); ?>
                        <?= form_open('Page/app_edit', $att); ?>

                            <input type="hidden" name="id" value="<?= $data->id; ?>">
                            <input type="hidden" name="aip_id" value="<?= $data->aip_id; ?>">

                            <div class="row">
                              <div class="col-lg-3">
                                <div class="form-group">
                                  <label >Unit Measure</label>
                                  <input required type="text" class="form-control" name="unit_measure" value="<?= $data->unit_measure; ?>">
                                </div>
                              </div>

                              <div class="col-lg-3">
                                <div class="form-group">
                                  <label >Unit Price</label>
                                  <input  required type="text" class="form-control" value="<?= $data->unit_price; ?>" onkeypress="return isNumberKey(event)" name="unit_price">
                                </div>
                              </div>

                                
                              </div>
                            


                            <div class="row">
                              <div class="col-xl-12">
                                  <div class="card-box">

                                      <ul class="nav nav-tabs">
                                          <li class="nav-item">
                                              <a href="#home" data-toggle="tab" aria-expanded="true" class="nav-link active">
                                                  <span class="d-block d-sm-none"><i class="font-12">Q 1</i></span>
                                                  <span class="d-none d-sm-block">1st Quarter</span>
                                              </a>
                                          </li>
                                          <li class="nav-item">
                                              <a href="#profile" data-toggle="tab" aria-expanded="false" class="nav-link">
                                                  <span class="d-block d-sm-none"><i class="font-12">Q 2</i></span>
                                                  <span class="d-none d-sm-block">2nd Quarter</span>
                                              </a>
                                          </li>
                                          <li class="nav-item">
                                              <a href="#messages" data-toggle="tab" aria-expanded="false" class="nav-link">
                                                  <span class="d-block d-sm-none"><i class="font-12">Q 3</i></span>
                                                  <span class="d-none d-sm-block">3rd Quarter</span>
                                              </a>
                                          </li>
                                          <li class="nav-item">
                                              <a href="#settings" data-toggle="tab" aria-expanded="false" class="nav-link">
                                                  <span class="d-block d-sm-none"><i class="font-12">Q 4</i></span>
                                                  <span class="d-none d-sm-block">4th Quarter</span>
                                              </a>
                                          </li>
                                      </ul>
                                      <div class="tab-content">
                                          <div class="tab-pane show active" id="home">
                                            <div class="row">
                                                <div class="col-lg-4">
                                                  <div class="form-group">
                                                    <label >January</label>
                                                    <input type="text" class="form-control" id="PPRICE" onkeyup="multiply()" onkeypress="return isNumberKey(event)" value="<?php if(isset($data->jan)){echo $data->jan;}  ?>" name="jan" placeholder="Unit Price">
                                                  </div>
                                                  <div class="row">
                                                      <div class="col-lg-4">
                                                      <div class="form-group">
                                                        <input type="text" class="form-control" id="QTY"  onkeyup="multiply()" onkeypress="return isNumberKey(event)" value="<?= $data->qjan; ?>" name="qjan" placeholder="qty">
                                                      </div>
                                                      </div>
                                                      <div class="col-lg-8">
                                                      <div class="form-group">
                                                        <input type="text" class="form-control" id="TOTAL" readonly value="<?php if(isset($data->unit_price)){echo $data->qjan*$data->jan;}  ?>" name="tjan" placeholder="Total">
                                                      </div>
                                                        </div>
                                                  </div>

                                                  
                                                </div>


                                                <div class="col-lg-4">
                                                  <div class="form-group">
                                                  <label >February</label>
                                                    <input type="text" class="form-control" id="FEBPRICE" onkeyup="multiplyfeb()" onkeypress="return isNumberKey(event)" value="<?= $data->feb; ?>" name="feb" placeholder="Unit Price">
                                                  </div>
                                                  <div class="row">
                                                      <div class="col-lg-4">
                                                        <div class="form-group">
                                                          <input type="text" class="form-control" id="FEBQTY" onkeyup="multiplyfeb()" onkeypress="return isNumberKey(event)" value="<?= $data->qfeb; ?>" name="qfeb" placeholder="qty">
                                                        </div>
                                                      </div>
                                                      <div class="col-lg-8">
                                                        <div class="form-group">
                                                          <input type="text" class="form-control" id="FEBTOTAL" readonly value="<?php if(isset($data->unit_price)){echo $data->qfeb*$data->feb;}  ?>" name="tfeb" placeholder="Total">
                                                        </div>
                                                      </div>
                                                  </div>
                                                </div>

                                                <div class="col-lg-4">
                                                  <div class="form-group">
                                                  <label >March</label>
                                                    <input type="text" class="form-control" id="MARPRICE" onkeyup="multiplymar()" onkeypress="return isNumberKey(event)" value="<?= $data->mar; ?>" name="mar" placeholder="Unit Price">
                                                  </div>
                                                  <div class="row">
                                                      <div class="col-lg-4">
                                                      <div class="form-group">
                                                        <input type="text" class="form-control" id="MARQTY" onkeyup="multiplymar()" onkeypress="return isNumberKey(event)" value="<?= $data->qmar; ?>" name="qmar" placeholder="qty">
                                                      </div>
                                                      </div>
                                                      <div class="col-lg-8">
                                                      <div class="form-group">
                                                        <input type="text" class="form-control" id="MARTOTAL" readonly value="<?php if(isset($data->unit_price)){echo $data->qmar*$data->mar;}  ?>" name="tmar" placeholder="Total">
                                                      </div>
                                                        </div>
                                                  </div>

                                                  
                                                </div>
                                        </div> 
                                          </div>

                                          <div class="tab-pane" id="profile">
                                              <div class="row">
                                                  <div class="col-lg-4">
                                                    <div class="form-group">
                                                    <label >April</label>
                                                      <input type="text" class="form-control" id="APPRICE" onkeyup="multiplyapril()" onkeypress="return isNumberKey(event)" value="<?= $data->april; ?>" name="april" placeholder="Unit Price">
                                                    </div>
                                                    <div class="row">
                                                      <div class="col-lg-4">
                                                      <div class="form-group">
                                                        <input type="text" class="form-control" id="APQTY" onkeyup="multiplyapril()" onkeypress="return isNumberKey(event)" value="<?= $data->qapril; ?>" name="qapril" placeholder="qty">
                                                      </div>
                                                      </div>
                                                      <div class="col-lg-8">
                                                        <div class="form-group">
                                                          <input type="text" class="form-control" id="APTOTAL" readonly value="<?php if(isset($data->unit_price)){echo $data->qapril*$data->april;}  ?>" name="tapril" placeholder="Total">
                                                        </div>
                                                      </div>
                                                  </div>

                                                  
                                                  </div>

                                                  <div class="col-lg-4">
                                                    <div class="form-group">
                                                    <label >May</label>
                                                      <input type="text" class="form-control" id="MAYPRICE" onkeyup="multiplymay()" onkeypress="return isNumberKey(event)" value="<?= $data->may; ?>" name="may" placeholder="Unit Price">
                                                    </div>
                                                    <div class="row">
                                                      <div class="col-lg-4">
                                                      <div class="form-group">
                                                        <input type="text" class="form-control" id="MAYQTY" onkeyup="multiplymay()" onkeypress="return isNumberKey(event)" value="<?= $data->qmay; ?>" name="qmay" placeholder="qty">
                                                      </div>
                                                      </div>
                                                      <div class="col-lg-8">
                                                        <div class="form-group">
                                                          <input type="text" class="form-control" id="MAYTOTAL" readonly value="<?php if(isset($data->unit_price)){echo $data->qmay*$data->may;}  ?>" name="tmay" placeholder="Total">
                                                        </div>
                                                        </div>
                                                  </div>

                                                  
                                                  </div>

                                                  <div class="col-lg-4">
                                                    <div class="form-group">
                                                    <label >June</label>
                                                      <input type="text" class="form-control" id="JUNPRICE" onkeyup="multiplyjune()" onkeypress="return isNumberKey(event)" value="<?= $data->june; ?>" name="june" placeholder="Unit Price">
                                                    </div>
                                                    <div class="row">
                                                      <div class="col-lg-4">
                                                      <div class="form-group">
                                                        <input type="text" class="form-control" id="JUNQTY" onkeyup="multiplyjune()" onkeypress="return isNumberKey(event)" value="<?= $data->qjune; ?>" name="qjune" placeholder="qty">
                                                      </div>
                                                      </div>
                                                      <div class="col-lg-8">
                                                        <div class="form-group">
                                                          <input type="text" class="form-control" id="JUNTOTAL" readonly value="<?php if(isset($data->unit_price)){echo $data->qjune*$data->june;}  ?>" name="tapril" placeholder="Total">
                                                        </div>
                                                        </div>
                                                  </div>

                                                  
                                                  </div>

                                              </div>
                                              
                                          </div>
                                          <div class="tab-pane" id="messages">
                                              <div class="row">
                                                  <div class="col-lg-4">
                                                    <div class="form-group">
                                                    <label >July</label>
                                                      <input type="text" class="form-control" id="JULPRICE" onkeyup="multiplyjul()" onkeypress="return isNumberKey(event)" value="<?= $data->july; ?>" name="july" placeholder="Unit Price">
                                                    </div>
                                                    <div class="row">
                                                      <div class="col-lg-4">
                                                      <div class="form-group">
                                                        <input type="text" class="form-control" id="JULQTY" onkeyup="multiplyjul()" onkeypress="return isNumberKey(event)" value="<?= $data->qjuly; ?>" name="qjuly" placeholder="qty">
                                                      </div>
                                                      </div>
                                                      <div class="col-lg-8">
                                                        <div class="form-group">
                                                          <input type="text" class="form-control" id="JULTOTAL" readonly value="<?php if(isset($data->unit_price)){echo $data->qjuly*$data->july;}  ?>" name="tjuly" placeholder="Total">
                                                        </div>
                                                        </div>
                                                  </div>

                                                  
                                                  </div>

                                                  <div class="col-lg-4">
                                                    <div class="form-group">
                                                    <label>August</label>
                                                      <input type="text" class="form-control" id="AUGPRICE" onkeyup="multiplyaug()" onkeypress="return isNumberKey(event)" value="<?= $data->aug; ?>" name="aug" placeholder="Unit Price">
                                                    </div>
                                                    <div class="row">
                                                      <div class="col-lg-4">
                                                      <div class="form-group">
                                                        <input type="text" class="form-control" id="AUGQTY" onkeyup="multiplyaug()" onkeypress="return isNumberKey(event)" value="<?= $data->qaug; ?>" name="qaug" placeholder="qty">
                                                      </div>
                                                      </div>
                                                      <div class="col-lg-8">
                                                        <div class="form-group">
                                                          <input type="text" class="form-control" id="AUGTOTAL" readonly value="<?php if(isset($data->unit_price)){echo $data->qaug*$data->aug;}  ?>" name="taug" placeholder="Total">
                                                        </div>
                                                        </div>
                                                  </div>

                                                  
                                                  </div>

                                                  <div class="col-lg-4">
                                                    <div class="form-group">
                                                    <label >September</label>
                                                      <input type="text" class="form-control" id="SEPPRICE" onkeyup="multiplysep()" onkeypress="return isNumberKey(event)" value="<?= $data->sept; ?>" name="sept" placeholder="Unit Price">
                                                    </div>
                                                    <div class="row">
                                                      <div class="col-lg-4">
                                                      <div class="form-group">
                                                        <input type="text" class="form-control" id="SEPQTY" onkeyup="multiplysep()" onkeypress="return isNumberKey(event)" value="<?= $data->qsept; ?>" name="qsept" placeholder="qty">
                                                      </div>
                                                      </div>
                                                      <div class="col-lg-8">
                                                        <div class="form-group">
                                                          <input type="text" class="form-control" id="SEPTOTAL" readonly value="<?php if(isset($data->unit_price)){echo $data->qsept*$data->sept;}  ?>" name="tsept" placeholder="Total">
                                                        </div>
                                                        </div>
                                                  </div>

                                                  
                                                  </div>

                                              </div>   

                                          </div>
                                          <div class="tab-pane" id="settings">
                                              <div class="row">
                                                  <div class="col-lg-4">
                                                    <div class="form-group">
                                                    <label >October</label>
                                                      <input type="text" class="form-control" id='OCTPRICE' onkeyup="multiplyoct()" onkeypress="return isNumberKey(event)" value="<?= $data->oct; ?>" name="oct" placeholder="Unit Price">
                                                    </div>
                                                    <div class="row">
                                                      <div class="col-lg-4">
                                                      <div class="form-group">
                                                        <input type="text" class="form-control" id="OCTQTY" onkeyup="multiplyoct()" onkeypress="return isNumberKey(event)" value="<?= $data->qoct; ?>" name="qoct" placeholder="qty">
                                                      </div>
                                                      </div>
                                                      <div class="col-lg-8">
                                                        <div class="form-group">
                                                          <input type="text" class="form-control" id="OCTTOTAL" readonly value="<?php if(isset($data->unit_price)){echo $data->qoct*$data->oct;}  ?>" name="toct" placeholder="Total">
                                                        </div>
                                                        </div>
                                                  </div>

                                                  
                                                  </div>

                                                  <div class="col-lg-4">
                                                    <div class="form-group">
                                                    <label >November</label>
                                                      <input type="text" class="form-control" id="NOVPRICE" onkeyup="multiplynov()" onkeypress="return isNumberKey(event)" value="<?= $data->nov; ?>" name="nov" placeholder="Unit Price">
                                                    </div>
                                                    <div class="row">
                                                      <div class="col-lg-4">
                                                      <div class="form-group">
                                                        <input type="text" class="form-control" id="NOVQTY" onkeyup="multiplynov()" onkeypress="return isNumberKey(event)" value="<?= $data->qnov; ?>" name="qnov" placeholder="qty">
                                                      </div>
                                                      </div>
                                                      <div class="col-lg-8">
                                                        <div class="form-group">
                                                          <input type="text" class="form-control" id="NOVTOTAL" readonly value="<?php if(isset($data->unit_price)){echo $data->qnov*$data->nov;}  ?>" name="tnov" placeholder="Total">
                                                        </div>
                                                        </div>
                                                  </div>

                                                  
                                                  </div>

                                                  <div class="col-lg-4">
                                                    <div class="form-group">
                                                    <label >December</label>
                                                      <input type="text" class="form-control" id="DECPRICE" onkeyup="multiplydec()" onkeypress="return isNumberKey(event)" value="<?= $data->ddec; ?>" name="dec" placeholder="Unit Price">
                                                    </div>
                                                    <div class="row">
                                                      <div class="col-lg-4">
                                                      <div class="form-group">
                                                        <input type="text" class="form-control" id="DECQTY" onkeyup="multiplydec()" onkeypress="return isNumberKey(event)" value="<?= $data->qdec; ?>" name="qdec" placeholder="qty">
                                                      </div>
                                                      </div>
                                                      <div class="col-lg-8">
                                                        <div class="form-group">
                                                          <input type="text" class="form-control" id="DECTOTAL" readonly value="<?php if(isset($data->unit_price)){echo $data->qdec*$data->ddec;}  ?>" name="tdec" placeholder="Total">
                                                        </div>
                                                        </div>
                                                  </div>

                                                  
                                                  </div>

                                              </div>

                                             
                                          </div>
                                      </div>
                                  </div>
                                  <input type="submit" name="submit" value="Submit" class="btn btn-primary waves-effect waves-light mr-1">
                              </div>
                              <!-- end col -->
                                   
                             

                                                        </div>

                        </form>


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
           
            <!-- end Footer -->

        </div>
        <!-- END wrapper -->

        <!-- Vendor js -->
        <script src="<?= base_url(); ?>assets/js/vendor.min.js"></script>

        <!-- App js -->
        <script src="<?= base_url(); ?>assets/js/app.min.js"></script>
        

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