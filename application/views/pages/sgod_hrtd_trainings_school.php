

            <!-- ============================================================== -->
            <!-- Start Page Content here -->
            <!-- ============================================================== -->
             <?php 
                $modality = [
                        1 => 'Face to Face',
                        2 => 'Online',
                        3 => 'Blended',
                    ];

             ?>

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
                                                    <th class="text-center">WAP File</th>
                                                    <th class="text-center">MOV File</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>

                                            <tbody>
                                                <?php foreach($data as $row){
                                                    $check = $this->Common->two_cond_row('sgod_hrtd_training_registered','IDNumber',$this->session->username,'training_id',$row->id);
                                                ?>
                                                <tr>
                                                    <td><?= $this->Hrtd_model->autoBrEvery20Words($row->description,15);?></td>
                                                    <td class="text-center">
                                                        <?php if (!empty($check->wap)) : ?>
                                                            <a target="_blank" href="<?= base_url(); ?>uploads/hrdfile/<?= $check->wap; ?>"><i class="far fa-file-pdf text-purple"></i></a>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td class="text-center">
                                                        <?php if (!empty($check->mov)) : ?>
                                                            <a target="_blank" href="<?= base_url(); ?>uploads/hrdfile/<?= $check->mov; ?>"><i class="far fa-file-pdf text-primary"></i></a>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <?php if ($check): ?>
                                                            
                                                            <button type="button" class="btn btn-purple btn-sm waves-effect waves-light open-AddBookDialog" data-id="<?= $check->id; ?>" data-toggle="modal" data-target="#wap">WAP</button>
                                                            <button type="button" class="btn btn-primary btn-sm waves-effect waves-light open-AddBookDialog" data-id="<?= $check->id; ?>" data-toggle="modal" data-target="#mov">MOV</button>
                                                        <?php else: ?>
                                                            <a class="btn-sm btn btn-success" href="<?= base_url(); ?>Hrtd/register_training/<?= $row->id; ?>">Register</a>
                                                        <?php endif; ?>
                                                    </td>
                                                        
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


                <!-- Long Content Scroll Modal -->
                                        <div class="modal fade" id="wap" tabindex="-1" role="dialog"
                                        aria-labelledby="exampleModalScrollableTitle" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-scrollable" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-purple">
                                                        <h5 class="modal-title text-white" id="exampleModalScrollableTitle">WAP ATTACHMENT</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">

                                                    <div class="row">
                                            <div class="col-12">
                                                <div class="">
                                                        <?= validation_errors(); ?>

                                                        <?php
                                                        $attributes = array('class' => 'parsley-examples form-horizontal');
                                                        echo form_open_multipart('Hrtd/update_wap_file', $attributes);
                                                        ?>
                                                        <div class="form-group row">
                                                            <label class="col-lg-12 col-form-label"><span class="text-danger">The uploaded file must be in PDF format only, with a maximum size of 2 MB.</span></label>

                                                            <div class="col-lg-12">
                                                                <input type="file" name="file" value="" class="form-control">
                                                            </div>
                                                            <input type="hidden" value="" id="id" name="id">
                                                            <input type="hidden" value="<?= $this->session->username; ?>" name="IDNumber">
                                                        </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                        
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="submit" class="btn btn-purple">Submit</button>
                                                    </div>
                                                </form>
                                                </div>
                                            </div>
                                        </div>


                                        <!-- Long Content Scroll Modal -->
                                        <div class="modal fade" id="mov" tabindex="-1" role="dialog"
                                        aria-labelledby="exampleModalScrollableTitle" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-scrollable" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-primary">
                                                        <h5 class="modal-title" id="exampleModalScrollableTitle">MOV ATTACHMENT</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">

                                                    <div class="row">
                                            <div class="col-12">
                                                <div class="">
                                                        <?= validation_errors(); ?>

                                                        <?php
                                                        $attributes = array('class' => 'parsley-examples form-horizontal');
                                                        echo form_open_multipart('Hrtd/update_mov_file', $attributes);
                                                        ?>
                                                        <div class="form-group row">
                                                            <label class="col-lg-12 col-form-label"><span class="text-danger">The uploaded file must be in PDF format only, with a maximum size of 2 MB.</span></label>
                                                            <div class="col-lg-12">
                                                                <input type="file" name="file" value="" class="form-control">
                                                            </div>
                                                            <input type="hidden" value="" id="id" name="id">
                                                            <input type="hidden" value="<?= $this->session->username; ?>" name="IDNumber">
                                                        </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                        
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="submit" class="btn btn-primary">Submit</button>
                                                    </div>
                                                </form>
                                                </div>
                                            </div>
                                        </div>

                
                            

   

 