

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
                              
                                    <div class="clearfix"></div>
                                    
                                </div>
                            </div>
                        </div>
                        <!-- end page title -->

                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body table-responsive">
                                    <h4 class="header-title mb-4"><?= $title; ?></h4>
                                    <?php $staff = $this->Common->no_cond_select('hris_staff','FirstName,LastName,MiddleName,NameExtn,IDNumber'); ?>

                                    

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

                                         <div class="row">
                                            <div class="col-12">
                                                <div class="">
                                                        <?= validation_errors(); ?>

                                                        <?php
                                                        $attributes = array('class' => 'parsley-examples form-horizontal');
                                                        echo form_open('Hrtd/training_update', $attributes);
                                                        ?>
                                                        <div class="form-group row">
                                                            <label class="col-lg-4 col-form-label">Training Description</label>
                                                            <div class="col-lg-8">
                                                               
                                                              <textarea class="form-control" name="description" rows="3" id="example-textarea"><?= $data->description; ?></textarea>

                                                            </div>
                                                        </div>

                                                        <div class="form-group row">
                                                            <label class="col-lg-4 col-form-label">Target Date</label>
                                                            <div class="col-lg-8">
                                                               
                                                              <input type="date" class="form-control" name="target_date" value="<?= $data->target_date; ?>"> 

                                                            </div>
                                                        </div>

                                                        <div class="form-group row">
                                                            <label class="col-lg-4 col-form-label">Actual Date</label>
                                                            <div class="col-lg-8">
                                                               <input type="date" class="form-control" name="actual_date" value="<?= $data->actual_date; ?>">
                                                            </div>
                                                        </div>

                                                       <input type="hidden" name="id" value="<?= $data->id; ?>">

                                                        <div class="form-group row">
                                                            <label class="col-lg-4 col-form-label">Modality</label>
                                                            <div class="col-lg-8">
                                                               
                                                                <select name="modality" class="form-control" required>
                                                                    <option value="" disabled selected>Select Modality</option>
                                                                    <option <?php if($data->modality == 0){echo " selected ";} ?> value="0">Face to Face</option>
                                                                    <option <?php if($data->modality == 1){echo " selected ";} ?> value="1">Online</option>
                                                                    <option <?php if($data->modality == 2){echo " selected ";} ?> value="2">Blended</option>
                                                                </select>
                                                            </div>
                                                        </div>

                                                        <div class="form-group row">
                                                            <label class="col-lg-4 col-form-label">Venue</label>
                                                            <div class="col-lg-8">
                                                               
                                                              <textarea class="form-control" name="venue" rows="2" id="example-textarea"><?= $data->venue; ?></textarea>

                                                            </div>
                                                        </div>

                                                        <div class="form-group row">
                                                            <label class="col-lg-4 col-form-label">Fund Allocation</label>
                                                            <div class="col-lg-8">
                                                               <input value="<?= $data->funds; ?>" type="text" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1')" class="form-control" name="funds">
                                                            </div>
                                                        </div>

                                                        <div class="form-group row">
                                                            <label class="col-lg-4 col-form-label">Target Participant</label>
                                                            <div class="col-lg-8">
                                                               
                                                              <textarea class="form-control" name="participant" rows="2" id="example-textarea"><?= $data->participant; ?></textarea>

                                                            </div>
                                                        </div>
                                                        <div class="form-group row">
                                                            <label class="col-lg-4 col-form-label">No. of Participant</label>
                                                            <div class="col-lg-8">
                                                               
                                                              <input type="text" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1')" class="form-control" name="no_participant"> 

                                                            </div>
                                                        </div>

                                                        <div class="form-group row">
                                                            <label class="col-lg-4 col-form-label">Quater</label>
                                                            <div class="col-lg-8">
                                                               
                                                                <select name="quarter" class="form-control" required>
                                                                    <option value="" disabled selected>Select Quater</option>
                                                                    <option <?php if($data->quarter == 1){echo " selected ";} ?> value="1">Quater 1</option>
                                                                    <option <?php if($data->quarter == 2){echo " selected ";} ?> value="2">Quater 2</option>
                                                                    <option <?php if($data->quarter == 3){echo " selected ";} ?> value="3">Quater 3</option>
                                                                    <option <?php if($data->quarter == 4){echo " selected ";} ?> value="4">Quater 4</option> 
                                                                </select>
                                                            </div>
                                                        </div>

                                                        <div class="form-group row">
                                                            <label class="col-lg-4 col-form-label">PRC Accreditation No.</label>
                                                            <div class="col-lg-3">
                                                              <input type="text" class="form-control" name="prc" value="<?= $data->prc; ?>" required>                                                                 
                                                            </div>

                                                            <label class="col-lg-2 col-form-label">CPD Units</label>
                                                            <div class="col-lg-3">
                                                              <input type="text" value="<?= $data->cpd; ?>" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1')" class="form-control" name="cpd" required> 
                                                            </div>
                                                        </div>

                                                        <div class="form-group row">
                                                            <label class="col-lg-4 col-form-label">Program Owner</label>
                                                            <div class="col-lg-8">
                                                                <select name="po" class="form-control" data-toggle="select2" required>
                                                                    <option></option>
                                                                    <?php foreach($staff as $row){?>
                                                                    <option <?php if($row->IDNumber == $data->po){echo " selected ";} ?> value="<?= $row->IDNumber; ?>"><?= $row->FirstName; ?> <?= !empty($row->MiddleName) ? strtoupper(substr($row->MiddleName, 0, 1)) . '.' : ''; ?> <?= $row->LastName; ?></option>
                                                                    <?php } ?>
                                                                </select>
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


                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- end row -->

                    </div>
                    <!-- end container-fluid -->

                </div>
                <!-- end content -->

               


                            

   

 