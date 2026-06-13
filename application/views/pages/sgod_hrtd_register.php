

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

                                    <div class="row">
                                        <div class="col-12">
                                            <div class="card">
                                                <div class="card-body table-responsive">
                                                    <h4 class="header-title mb-4 text-center"><?= $data->description; ?><br /><span class="badge badge-success">Date: <?= $data->actual_date; ?></span></h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

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

                                         <div class="row">
                                            <div class="col-12">
                                                <div class="">
                                                        <?= validation_errors(); ?>

                                                        <?php
                                                        $attributes = array('class' => 'parsley-examples form-horizontal');
                                                        echo form_open('Hrtd/register_training/'.$this->uri->segment(3), $attributes);
                                                        ?>
                                                        <input type="hidden" value="<?= $this->session->username; ?>" name="IDNumber">
                                                        <input type="hidden" value="<?= $data->id; ?>" name="id">

                                                        <div class="form-group row">
                                                            <label class="col-lg-4 col-form-label">Fullname</label>
                                                            <div class="col-lg-3">
                                                              <input type="text" readonly class="form-control" name="fname" value="<?= ucfirst($staff->FirstName); ?>">
                                                            </div>
                                                            <div class="col-lg-2">
                                                              <input type="text" readonly class="form-control" name="mname" value="<?= ucfirst($staff->MiddleName); ?>">
                                                            </div>
                                                            <div class="col-lg-3">
                                                              <input type="text" readonly class="form-control" name="lname" value="<?= ucfirst($staff->LastName); ?>">
                                                            </div>
                                                        </div>

                                                        <div class="form-group row">
                                                            <label class="col-lg-4 col-form-label">passkey</label>
                                                            <div class="col-lg-8">
                                                               
                                                              <input type="text" class="form-control" name="passkey" value="" required>

                                                            </div>
                                                        </div>
                                                        

                                                        <div class="form-group row">
                                                            <label class="col-lg-4 col-form-label">School Personnel Position</label>
                                                            <div class="col-lg-8">
                                                               
                                                                <select name="position" class="form-control" required>
                                                                    <option value="" disabled selected>Select Position</option>
                                                                    <?php foreach($position as $row){?>
                                                                    <option value="<?= $row->id; ?>"><?= $row->pos_code; ?></option>
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
                                                        <button type="submit" class="btn btn-info waves-effect waves-light">Register</button>
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

               


                            

   

 