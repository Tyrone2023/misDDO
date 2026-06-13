
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
                                    <h4 class="page-title"><?= $title; ?></h4>
                                 
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                        </div>
                        <!-- end page title -->

                        
                        <!-- Form row -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-body">
                                    
                                        <?= validation_errors(); ?>

                                        <?php 
                                            $attributes = array('class' => 'parsley-examples');
                                            echo form_open('Users/users_sub_create', $attributes);
                                        ?>

                                        <input type="hidden" name="position" value="<?= $this->session->position; ?>">

                                        <div class="form-row">
                                                <div class="form-group col-md-5">
                                                    <label for="inputUsername" class="col-form-label">Username</label>
                                                    <input type="text" required value="<?= set_value('username'); ?>" name="username" class="form-control">
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label for="inputUsername" class="col-form-label">Password</label>
                                                    <input type="password" required value="<?= set_value('password'); ?>" name="password" class="form-control">
                                                </div>
                                                <?php if($this->session->position == "SHNS"){ ?>

                                                        <div class="form-group col-md-3">
                                                            <label for="inputPosition" class="col-form-label">District</label>
                                                                <select name="district" class="form-control" required>
                                                                    <option></option>
                                                                    <?php foreach($district as $row){?>
                                                                    <option value="<?= $row->id; ?>"><?= $row->discription; ?></option>
                                                                    <?php } ?>
                                                                </select>
                                                        </div> 
                                                <?php }else{ ?>
                                                    <input type="hidden" value="0" name="district" class="form-control">
                                                <?php } ?>
                                            
                                        </div>

                                        
                                      
                                        <div class="form-row">
                                        
                                                <div class="form-group col-md-3">
                                                    <label for="inputOfname" class="col-form-label">First Name</label>
                                                    <input type="text" value="<?= set_value('fname'); ?>" name="fname" class="form-control">
                                                </div>
                                                <div class="form-group col-md-3">
                                                    <label for="inputOfmname" class="col-form-label">Middle Name</label>
                                                    <input type="text" value="<?= set_value('mname'); ?>" name="mname" class="form-control">
                                                </div>
                                                <div class="form-group col-md-3">
                                                    <label for="inputOflname" class="col-form-label">Last Name</label>
                                                    <input type="text" value="<?= set_value('lname'); ?>" name="lname" class="form-control">
                                                </div>

                                                <?php if($this->session->position != "District"){ ?>

                                                <div class="form-group col-md-2">
                                                    <label for="inputPosition" class="col-form-label">Position</label>
                                                        <select name="sp" class="form-control" required>
                                                            <option></option>
                                                            <?php foreach($sp as $row){?>
                                                            <option value="<?= $row->id; ?>"><?= $row->position; ?></option>
                                                            <?php } ?>
                                                        </select>
                                                </div>  

                                                <?php }else{ ?>
                                                    <input type="hidden" value="0" name="sp" class="form-control">
                                                
                                                <?php } ?>
                                                
                                                
                                                <div class="form-group col-md-1">
                                                    <label for="inputOfgender" class="col-form-label">Sex</label>
                                                    <select id="inputState" name="sex" class="form-control" required>
                                                        <option value=""></option>
                                                    <?php 
                                                    $sex = array('Male' => 0,'Female' => 1);
                                                    foreach($sex as $s => $v){
                                                        echo "<option value='{$v}'>{$s}</option>";
                                                    }
                                                    ?>
                                                    </select>
                                                </div>
                                                
                                        </div>
                                        <div class="form-row">
                                                <div class="form-group col-md-12">
                                                    <label for="inputOfaddress" class="col-form-label">Address</label>
                                                    <input type="text" value="<?= set_value('address'); ?>" name="address" class="form-control">
                                                </div>
                                        </div>

                                        

                                        <button type="submit" value="submit" class="btn btn-primary">Create New User</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <!-- end row -->

                    </div>
                    <!-- end container-fluid -->

                </div>
                <!-- end content -->

                

               