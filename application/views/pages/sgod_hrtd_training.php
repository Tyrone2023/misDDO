

            <!-- ============================================================== -->
            <!-- Start Page Content here -->
            <!-- ============================================================== -->
             <?php 
                $modality = [
                        1 => 'Face to Face',
                        2 => 'Online',
                        3 => 'Blended',
                    ];
                    $staff = $this->Common->no_cond_select('hris_staff','FirstName,LastName,MiddleName,NameExtn,IDNumber');

             ?>

            <div class="content-page">
                <div class="content">

                    <!-- Start Content-->
                    <div class="container-fluid">

                        <!-- start page title -->
                        <div class="row">
                            <div class="col-12">
                                <div class="page-title-box">
                                    <?php if($this->session->position == 'hrtd'){ ?>
                                    <a href="#" class="btn btn-primary waves-effect waves-light openModalBtn"data-toggle="modal" data-target="#myModal">Add New</a>
                                    <?php } ?>
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

                                         <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">

                                            <thead>
                                                <tr>
                                                    <th>Description</th>
                                                    <th>Target<br /> Date</th>
                                                    <th>Actual<br /> Date</th>
                                                    <th>MOdality</th>
                                                    <th>passkey</th>
                                                    <th>Funds<br /> Allocation</th>
                                                    <th>Target<br /> Participants</th>
                                                    <th>No. of<br /> Participants</th>
                                                    <th>Quater</th>
                                                    <?php if($this->session->position == 'hrtd'){ ?>
                                                    <th>Status</th>
                                                    <?php } ?>
                                                    <th>Registered<br /> Participants</th>
                                                    <th>Program Owner</th>
                                                    <th>PRC</th>
                                                    <th>CPD Units</th>
                                                    <?php if($this->session->position == 'hrtd'){ ?>
                                                    <th>Action</th>
                                                    <?php } ?>
                                                </tr>
                                            </thead>

                                            <tbody>
                                                <?php foreach($data as $row){
                                                    $count = $this->Common->one_cond('sgod_hrtd_training_registered','training_id',$row->id);
                                                ?>
                                                <tr>
                                                    <td><?= $this->Hrtd_model->autoBrEvery20Words($row->description,7);?></td>
                                                    <td><?= $row->target_date; ?></td>
                                                    <td><?= $row->actual_date; ?></td>
                                                    <td><?= $modality[$row->modality] ?? ''; ?></td>
                                                    <td><?= $row->passkey; ?></td>
                                                    <td><?= $row->funds; ?></td>
                                                    <td><?= $this->Hrtd_model->autoBrEvery20Words($row->participant,5);?></td>
                                                    <td><?= $row->no_participant;?></td>
                                                    <td class="text-center"><span class="badge badge-primary"><?= $row->quarter; ?></span></td>
                                                    <?php if($this->session->position == 'hrtd'){ ?>
                                                    <td>
                                                        <?php if($row->stat == 0){?>
                                                        <a class="btn btn-success btn-sm" href="<?= base_url(); ?>Hrtd/training_stat_update/1/<?= $row->id; ?>">Open</a>
                                                        <a class="btn btn-purple btn-sm" href="<?= base_url(); ?>Hrtd/training_stat_update/2/<?= $row->id; ?>">Archived</a>
                                                        <?php }else{ ?>
                                                            <?php if($row->stat == 1){?>
                                                                <a class="btn btn-success btn-sm" href="<?= base_url(); ?>Hrtd/training_stat_update/2/<?= $row->id; ?>">Open</a>
                                                            <?php }else{ ?>
                                                                <a class="btn btn-purple btn-sm" href="<?= base_url(); ?>Hrtd/training_stat_update/1/<?= $row->id; ?>">Archived</a>
                                                            <?php } ?>
                                                        <?php } ?>
                                                    </td>
                                                    <?php } ?>
                                                    <td class="text-center"><a class="btn btn-success btn-sm" href="<?= base_url(); ?>Hrtd/registered_in_training/<?= $row->id; ?>">View</a> <span class="badge badge-purple"><?= count($count); ?></span></td>
                                                    <?php $person = $this->Common->one_cond_row_select('hris_staff','FirstName,LastName,MiddleName,NameExtn,IDNumber','IDNumber',$row->po); ?>
                                                    <td><?= !empty($person) ? trim($person->FirstName . ' ' . (!empty($person->MiddleName) ? strtoupper(substr($person->MiddleName, 0, 1)) . '. ' : '') . $person->LastName) : ''; ?></td>
                                                    <td><?= $row->prc; ?></td>
                                                    <td><?= $row->cpd; ?></td>
                                                    <?php if($this->session->position == 'hrtd'){ ?>
                                                    <td>
                                                        <a class="text-success tooltips" data-placement="top" data-toggle="tooltip" data-original-title="Update" href="<?= base_url(); ?>Hrtd/training_update/<?= $row->id; ?>"><i class=" fas fa-edit"></i></a> &nbsp; &nbsp;
                                                        <a onclick="return confirm('Are you sure?')" class="text-danger tooltips" data-placement="top" data-toggle="tooltip" data-original-title="Delete" href="<?= base_url(); ?>Hrtd/training_delete/<?= $row->id; ?>"><i class="far fa-trash-alt"></i></a>
                                                    </td>
                                                    <?php } ?>
                                                </tr>
                                                <?php } ?>
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
                                        <div id="myModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content modal-lg">
                                                    <div class="modal-header bg-info">
                                                        <h5 class="modal-title" id="modalTitle">Add New Training</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                    </div>
                                                    <div class="modal-body">

                                                    <div class="row">
                                            <div class="col-12">
                                                <div class="">
                                                        <?= validation_errors(); ?>

                                                        <?php
                                                        $attributes = array('class' => 'parsley-examples form-horizontal');
                                                        echo form_open('Hrtd/hrtd_trainings_insert', $attributes);
                                                        ?>
                                                        <div class="form-group row">
                                                            <label class="col-lg-4 col-form-label">Training Description</label>
                                                            <div class="col-lg-8">
                                                               
                                                              <textarea class="form-control" name="description" rows="3" id="example-textarea"></textarea>

                                                            </div>
                                                        </div>

                                                        <div class="form-group row">
                                                            <label class="col-lg-4 col-form-label">Target Date</label>
                                                            <div class="col-lg-8">
                                                               
                                                              <input type="date" class="form-control" name="target_date"> 

                                                            </div>
                                                        </div>

                                                        <div class="form-group row">
                                                            <label class="col-lg-4 col-form-label">Venue</label>
                                                            <div class="col-lg-8">
                                                               
                                                              <textarea class="form-control" name="venue" rows="2" id="example-textarea"></textarea>

                                                            </div>
                                                        </div>

                                                        <div class="form-group row">
                                                            <label class="col-lg-4 col-form-label">Fund Allocation</label>
                                                            <div class="col-lg-3">
                                                               <input type="text" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1')" class="form-control" name="funds">
                                                            </div>

                                                            <label class="col-lg-3 col-form-label">No. of Participant</label>
                                                            <div class="col-lg-2">
                                                               
                                                              <input type="text" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1')" class="form-control" name="no_participant" required> 

                                                            </div>
                                                        </div>

                                                        <div class="form-group row">
                                                            <label class="col-lg-4 col-form-label">Target Participant</label>
                                                            <div class="col-lg-8">
                                                               
                                                              <textarea class="form-control" name="participant" rows="2" id="example-textarea"></textarea>

                                                            </div>
                                                        </div>

                                                        

                                                        <div class="form-group row">
                                                            <label class="col-lg-4 col-form-label">Quater</label>
                                                            <div class="col-lg-3">
                                                               
                                                                <select name="quarter" class="form-control" required>
                                                                    <option value="" disabled selected>Select Quater</option>
                                                                    <option value="1">Quater 1</option>
                                                                    <option value="2">Quater 2</option>
                                                                    <option value="3">Quater 3</option>
                                                                    <option value="4">Quater 4</option> 
                                                                </select>
                                                            </div>

                                                            <label class="col-lg-2 col-form-label">Modality</label>
                                                            <div class="col-lg-3">
                                                               
                                                                <select name="modality" class="form-control" required>
                                                                    <option value="" disabled selected>Select Modality</option>
                                                                    <option value="0">Face to Face</option>
                                                                    <option value="1">Online</option>
                                                                    <option value="2">Blended</option>
                                                                </select>
                                                            </div>
                                                        </div>

                                                        <div class="form-group row">
                                                            <label class="col-lg-4 col-form-label">PRC Accreditation No.</label>
                                                            <div class="col-lg-3">
                                                              <input type="text" class="form-control" name="prc" required>                                                                 
                                                            </div>

                                                            <label class="col-lg-2 col-form-label">CPD Units</label>
                                                            <div class="col-lg-3">
                                                              <input type="text" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1')" class="form-control" name="cpd" required> 
                                                            </div>
                                                        </div>

                                                        <div class="form-group row">
                                                            <label class="col-lg-4 col-form-label">Program Owner</label>
                                                            <div class="col-lg-8">
                                                                <select name="po" class="form-control" data-toggle="select2" required>
                                                                    <option></option>
                                                                    <?php foreach($staff as $row){?>
                                                                    <option value="<?= $row->IDNumber; ?>"><?= $row->FirstName; ?> <?= !empty($row->MiddleName) ? strtoupper(substr($row->MiddleName, 0, 1)) . '.' : ''; ?> <?= $row->LastName; ?></option>
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


                            

   

 