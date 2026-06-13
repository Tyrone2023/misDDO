

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
                                    <a href="#" class="btn btn-primary waves-effect waves-light openModalBtn"data-toggle="modal" data-target="#myModal">Add Payment</a>
                                    
                              
                                    <div class="clearfix"></div>
                                    
                                </div>
                            </div>
                        </div>
                        <!-- end page title -->

                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body table-responsive">
                                        <div class="clearfix">
                                            <div class="float-left">
                                                <h4 class="text-left">
                                                    <?= $school->name; ?><br />
                                                </h4>
                                                <div class="col-md-6">
                                                    <span class="badge badge-primary"><?= $month; ?></span>
                                                    <span class="badge badge-purple"><?= $fy; ?></span>
                                                </div>

                                            </div>
                                        </div>
                                        <hr>
                                    <h4 class="header-title mb-4"><?= $title; ?></h4>

                                    

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

                                         <table class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">

                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Fullname</th>
                                                    <th>Month</th>
                                                    <th>Year</th>
                                                    <th>Amount</th>
                                                    <th>Remarks</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>

                                            <tbody>
                                                <?php $payment=0; $c=1; foreach($data as $row){
                                                    $emp = $this->Common->one_cond_row_select('hris_staff','FirstName,IDNumber,LastName','IDNumber',$row->IDNumber);
                                                ?>
                                                <tr>
                                                    <td><?= $c++; ?></td>
                                                    <td><?= $emp->FirstName; ?> <?= $emp->LastName; ?></td>
                                                    <td>
                                                        <?php 
                                                            $monthNum = (int) $row->month;
                                                            $monthWord = date("F", mktime(0, 0, 0, $monthNum, 1));
                                                            echo $monthWord ;
                                                        ?>
                                                    </td>
                                                    <td><?= $row->fy; ?></td>
                                                    <td><?= number_format($row->amount, 2); ?></td>
                                                    <th><?= $row->remarks; ?></th>
                                                    <td class="text-center">
                                                        <?php if($this->session->position == "provident"){?>
                                                            <?php if($row->stat == 0){?>
                                                            <a onclick="return confirm('Are you sure?')" class="text-success tooltips" data-placement="top" data-toggle="tooltip" data-original-title="Confirm" href="<?= base_url(); ?>Provident/payment_confirm/<?= $row->id; ?>"><i class="fas fa-brain"></i></a> &nbsp; &nbsp;
                                                            <?php } ?>
                                                            <a onclick="return confirm('Are you sure?')" class="text-danger tooltips" data-placement="top" data-toggle="tooltip" data-original-title="Delete" href="<?= base_url(); ?>Provident/provident_implementing_delete/<?= $row->id; ?>"><i class="far fa-trash-alt"></i></a> &nbsp; &nbsp;
                                                            <?php }else{ ?>
                                                            <?php if($row->stat == 0){?>
                                                            <a onclick="return confirm('Are you sure?')" class="text-danger tooltips" data-placement="top" data-toggle="tooltip" data-original-title="Delete" href="<?= base_url(); ?>Provident/provident_implementing_delete/<?= $row->id; ?>"><i class="far fa-trash-alt"></i></a> &nbsp; &nbsp;
                                                            <?php }else{ echo "Confirmed";} ?>
                                                        <?php } ?>
                                                        
                                                    </td>
                                                </tr>
                                                <?php $payment += $row->amount; } ?>
                                                <tr>
                                                    <td colspan="4">TOTAL</td>
                                                    <td><?= number_format($payment, 2); ?></td>
                                                    <td></td>
                                                    <td class="text-center">
                                                        <?php 
                                                            $check = $this->Common->three_cond_row('provident_add_info','month',$month,'fy',$fy,'school_id',$this->uri->segment(5)); 
                                                            if($this->session->position == 'provident'){
                                                            if($check){
                                                        ?>
                                                            <a target="_blank" href="<?= base_url(); ?>Provident/implementing_order_of_payment/<?= $this->uri->segment(5); ?>/<?= $payment; ?>/<?= $month; ?>/<?= $fy; ?>"><i class="fas fa-money-check-alt text-info"></i></a> &nbsp;
                                                            <a href="#" data-toggle="modal" data-target="#editinfo"><i class="fas fa-file-signature text-purple"></i></a>
                                                        <?php }else{ if(!empty($data)){ ?>
                                                            <a href="#" data-toggle="modal" data-target="#addinfo"><i class="fas fa-file-signature text-info"></i></a>
                                                        <?php } } } ?>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- end row -->

                    </div>
                    <!-- end container-fluid -->

                </div>
                <!-- end content -->

                <!-- sample modal content -->
                                        <div id="addinfo" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-info">
                                                        <h5 class="modal-title" id="modalTitle">Add Additional Info</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                    </div>
                                                    <div class="modal-body">

                                                    <div class="row">
                                                        <div class="col-12">
                                                            <div class="">
                                                                    <?= validation_errors(); ?>
                                                                    <?php
                                                                    $attributes = array('class' => 'parsley-examples form-horizontal');
                                                                    echo form_open('Provident/provident_info_add', $attributes);
                                                                    ?>


                                                                    <input type="hidden" name="school_id" value="<?= $school->school_id; ?>">
                                                                    <input type="hidden" name="month" value="<?= $month; ?>">
                                                                    <input type="hidden" name="fy" value="<?= $fy; ?>">

                                                                    <div class="form-group row">
                                                                        <label class="col-lg-4 col-form-label">Serial No.</label>
                                                                        <div class="col-lg-8">
                                                                        
                                                                        <input type="text" value=""  name="serial" class="form-control">

                                                                        </div>
                                                                    </div>

                                                                    <div class="form-group row">
                                                                        <label class="col-lg-4 col-form-label">Date</label>
                                                                        <div class="col-lg-8">
                                                                        
                                                                        <input type="date" value=""  name="cdate" class="form-control">

                                                                        </div>
                                                                    </div>


                                                                
                                                            </div>
                                                        </div>

                                                    </div>
                                                    <!-- end row -->
                                                        
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="submit" class="btn btn-info waves-effect waves-light">Save</button>
                                                    </div>
                                                    </form>
                                                </div>
                                                <!-- /.modal-content -->
                                            </div>
                                            <!-- /.modal-dialog -->
                                        </div>
                                        <!-- /.modal -->

                                        <!-- sample modal content -->
                                        <div id="myModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content modal-lg">
                                                    <div class="modal-header bg-info">
                                                        <h5 class="modal-title" id="modalTitle">Payment</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                    </div>
                                                    <div class="modal-body">

                                                    <div class="row">
                                                        <div class="col-12">
                                                            <div class="">
                                                                    <?= validation_errors(); ?>
                                                                    <?php
                                                                    $attributes = array('class' => 'parsley-examples form-horizontal');
                                                                    echo form_open('Provident/provident_imp_school_payment_insert', $attributes);
                                                                    ?>

                                                                    <div class="form-group row">
                                                                        <label class="col-lg-4 col-form-label">Fullname</label>
                                                                        <div class="col-lg-8">
                                                                        
                                                                            <select name="IDNumber" class="form-control" data-toggle="select2" required>
                                                                                <option value="" disabled selected>Select Employee</option>
                                                                                <?php foreach($staff as $row){?>
                                                                                <option value="<?= $row->IDNumber; ?>"><?= strtoupper($row->LastName); ?>, <?= strtoupper($row->FirstName); ?> <?= !empty($row->MiddleName) ? strtoupper(mb_substr($row->MiddleName, 0, 1)) . '.' : '' ?> </option>
                                                                                <?php } ?>
                                                                            </select>
                                                                        </div>
                                                                    </div>


                                                                    <input type="hidden" name="school_id" value="<?= $school->school_id; ?>">
                                                                    <input type="hidden" name="month" value="<?= $month; ?>">
                                                                    <input type="hidden" name="fy" value="<?= $fy; ?>">

                                                                    <div class="form-group row">
                                                                        <label class="col-lg-4 col-form-label">Amount</label>
                                                                        <div class="col-lg-8">
                                                                        
                                                                        <input type="text" value="" oninput="this.value=this.value.replace(/[^0-9.]/g,'').replace(/(\..*)\./g,'$1').replace(/^(\d+)(\.\d{0,2})?.*$/,'$1$2')" name="amount" class="form-control">

                                                                        </div>
                                                                    </div>



                                                                    <div class="form-group row">
                                                                        <label class="col-lg-4 col-form-label">Remarks</label>
                                                                        <div class="col-lg-8">
                                                                        <textarea class="form-control" name="remarks" rows="3" id="example-textarea"></textarea>

                                                                        </div>
                                                                    </div>


                                                                
                                                            </div>
                                                        </div>

                                                    </div>
                                                    <!-- end row -->
                                                        
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="submit" class="btn btn-info waves-effect waves-light">Save</button>
                                                    </div>
                                                    </form>
                                                </div>
                                                <!-- /.modal-content -->
                                            </div>
                                            <!-- /.modal-dialog -->
                                        </div>
                                        <!-- /.modal -->

                                        <?php if($check){?>

                                        <!-- sample modal content -->
                                        <div id="editinfo" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-info">
                                                        <h5 class="modal-title" id="modalTitle">Update Additional Info</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                    </div>
                                                    <div class="modal-body">

                                                    <div class="row">
                                                        <div class="col-12">
                                                            <div class="">
                                                                    <?= validation_errors(); ?>
                                                                    <?php
                                                                    $attributes = array('class' => 'parsley-examples form-horizontal');
                                                                    echo form_open('Provident/provident_info_update', $attributes);
                                                                    ?>

                                                                    <input type="hidden" name="id" value="<?= $check->id; ?>">


                                                                    <div class="form-group row">
                                                                        <label class="col-lg-4 col-form-label">Serial No.</label>
                                                                        <div class="col-lg-8">
                                                                        
                                                                        <input type="text" value="<?= $check->serial; ?>"  name="serial" class="form-control">

                                                                        </div>
                                                                    </div>

                                                                    <div class="form-group row">
                                                                        <label class="col-lg-4 col-form-label">Date</label>
                                                                        <div class="col-lg-8">
                                                                        
                                                                        <input type="date" value="<?= $check->cdate; ?>"  name="cdate" class="form-control">

                                                                        </div>
                                                                    </div>


                                                                
                                                            </div>
                                                        </div>

                                                    </div>
                                                    <!-- end row -->
                                                        
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="submit" class="btn btn-info waves-effect waves-light">Save</button>
                                                    </div>
                                                    </form>
                                                </div>
                                                <!-- /.modal-content -->
                                            </div>
                                            <!-- /.modal-dialog -->
                                        </div>
                                        <!-- /.modal -->
                                         <?php } ?>

                                        

                            

   

 