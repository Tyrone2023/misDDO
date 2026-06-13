

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
                                                    <div class="row">
                                                        <div class="col-12">
                                                            <div class="">
                                                                    <?= validation_errors(); ?>
                                                                    <?php
                                                                    $attributes = array('class' => 'parsley-examples form-horizontal');
                                                                    echo form_open('Brigada/partners_update', $attributes);
                                                                    ?>

                                                                    

                                                                    <div class="form-group row">
                                                                        <label class="col-lg-4 col-form-label">Name of Partner</label>
                                                                        <div class="col-lg-8">
                                                                        
                                                                        <input type="text" value="<?= $data->name; ?>" name="name" class="form-control">

                                                                        </div>
                                                                    </div>

                                                                    <div class="form-group row">
                                                                        <label class="col-lg-4 col-form-label">Address</label>
                                                                        <div class="col-lg-8">
                                                                        
                                                                        <input type="text" value="<?= $data->address; ?>" name="address" class="form-control">

                                                                        </div>
                                                                    </div>

                                                                    <div class="form-group row">
                                                                        <label class="col-lg-4 col-form-label">Contact Person</label>
                                                                        <div class="col-lg-8">
                                                                        
                                                                        <input type="text" value="<?= $data->contact_person; ?>" name="contact_person" class="form-control">

                                                                        </div>
                                                                    </div>

                                                                    <div class="form-group row">
                                                                        <label class="col-lg-4 col-form-label">Contact Number</label>
                                                                        <div class="col-lg-8">
                                                                        
                                                                        <input type="text" value="<?= $data->contact; ?>" name="contact" class="form-control">

                                                                        </div>
                                                                    </div>

                                                                    <div class="form-group row">
                                                                        <label class="col-lg-4 col-form-label">General Partner Type </label>
                                                                        <div class="col-lg-8">
                                                                            <?php $arg = array('Private_Sector' => 'Private Sector', 
                                                                                            'Public_Sector' => 'Public Sector', 
                                                                                            'Civil_Society_Organizations' => 'Civil Society Organizations', 
                                                                                            'International' => 'International'); ?>
                                                                        
                                                                            <select name="general_type" class="form-control" required>
                                                                                <option value="" disabled selected>Select General Partner Type</option>
                                                                                <?php foreach($arg as $key => $row){?>
                                                                                <option <?php if($data->general_type == $key){echo "selected";}?> value="<?= $key; ?>"><?= $row; ?></option>
                                                                                <?php } ?>
                                                                            </select>
                                                                        </div>
                                                                    </div>

                                                                    <div class="form-group row">
                                                                        <label class="col-lg-4 col-form-label">Specific Partner Type </label>
                                                                        <div class="col-lg-8">
                                                                            <?php $arg = array('Government', 'INGO-International Non-Government Organizations', 'Others'); ?>
                                                                        
                                                                            <select name="specific_type" class="form-control" required>
                                                                                <option value="" disabled selected>Specific Partner Type</option>
                                                                                <?php foreach($arg as $row){?>
                                                                                <option <?php if($data->specific_type == $row){echo "selected";}?> value="<?= $row; ?>"><?= $row; ?></option>
                                                                                <?php } ?>
                                                                            </select>
                                                                        </div>
                                                                    </div>

                                                                    <input type="hidden" name="id" value="<?= $data->id; ?>">

                                                                    

                                                                
                                                            </div>
                                                        </div>

                                                    </div>
                                                    <!-- end row -->

                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="submit" class="btn btn-info waves-effect waves-light">Update</button>
                                                    </div>
                                                    </form>
                                    

                                    
                                </div>
                            </div>
                        </div>
                        <!-- end row -->

                    </div>
                    <!-- end container-fluid -->

                </div>
                <!-- end content -->


   

 