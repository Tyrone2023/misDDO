
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
                                            echo form_open('Users/users_sub_update', $attributes);
                                        ?>

                                        <input type="hidden" name="id" value="<?= $user->id; ?>">

                                        <div class="form-row">
                                                <div class="form-group col-md-5">
                                                    <label for="inputUsername" class="col-form-label">Username</label>
                                                    <input type="text" required value="<?= $user->username; ?>" name="username" class="form-control">
                                                </div>
                                            
                                        </div>

                                        
                                      
                                        <div class="form-row">
                                        
                                                <div class="form-group col-md-3">
                                                    <label for="inputOfname" class="col-form-label">First Name</label>
                                                    <input type="text" value="<?= $user->fname; ?>" name="fname" class="form-control">
                                                </div>
                                                <div class="form-group col-md-3">
                                                    <label for="inputOfmname" class="col-form-label">Middle Name</label>
                                                    <input type="text" value="<?= $user->mname; ?>" name="mname" class="form-control">
                                                </div>
                                                <div class="form-group col-md-3">
                                                    <label for="inputOflname" class="col-form-label">Last Name</label>
                                                    <input type="text" value="<?= $user->lname; ?>" name="lname" class="form-control">
                                                </div>

                                                <div class="form-group col-md-2">
                                                    <label for="inputPosition" class="col-form-label">Position</label>
                                                        <select name="sp" class="form-control" required>
                                                            <option></option>
                                                            <?php foreach($sp as $row){?>
                                                            <option <?php 
                                                                if($row->id == $user->sp){echo "selected";}
                                                            ?> value="<?= $row->id; ?>"><?= $row->position; ?></option>
                                                            <?php } ?>
                                                        </select>

                                                
                                                </div>  
                                                
                                                
                                                <div class="form-group col-md-1">
                                                    <label for="inputOfgender" class="col-form-label">Sex</label>
                                                    <select id="inputState" name="sex" class="form-control" required>
                                                        <option value=""></option>
                                                    <?php 
                                                    $sex = array('Male' => 0,'Female' => 1);
                                                    foreach($sex as $s => $v){
                                                        echo "<option ";
                                                        if($v == $user->sex){echo "selected";}
                                                        echo " value='{$v}'>{$s}</option>";
                                                    }
                                                    ?>
                                                    </select>
                                                </div>
                                                
                                        </div>
                                        <div class="form-row">
                                                <div class="form-group col-md-12">
                                                    <label for="inputOfaddress" class="col-form-label">Address</label>
                                                    <input type="text" value="<?= $user->address; ?>" name="address" class="form-control">
                                                </div>
                                        </div>

                                        

                                        <button type="submit" value="submit" class="btn btn-primary">Submit</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <!-- end row -->

                    </div>
                    <!-- end container-fluid -->

                </div>
                <!-- end content -->

                

               