
            <!-- ============================================================== -->
            <!-- Start Page Content here -->
            <!-- ============================================================== -->

            <div class="content-page" style="margin-left:0">
                <div class="content">

                    <!-- Start Content-->
                    <div class="container-fluid">

                        <!-- start page title -->
                        <div class="row">
                            <div class="col-12">
                                <div class="page-title-box">
                                    <h4 class="page-title">EVALUATOR ACCOUNT</h4>
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Form row -->
                        <div class="row">
                            <div class="col-md-12">
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
                                <div class="card">
                                    <div class="card-body">
                                    
                                        <?= validation_errors(); ?>

                                        <?php 
                                                $attributes = array('class' => 'parsley-examples');
                                                echo form_open('Pages/user_add2', $attributes);
                                        ?>

                                        <div class="form-row">
                                                <div class="form-group col-md-6">
                                                    <label for="inputUsername" class="col-form-label">Username</label>
                                                    <input type="text" required value="<?= set_value('Username'); ?>" name="Username" class="form-control">
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <label for="inputUsername" class="col-form-label">Password</label>
                                                    <input type="password" required value="<?= set_value('Password'); ?>" name="Password" class="form-control">
                                                </div>
                                            
                                        </div>

                                        
                                      
                                        <div class="form-row">
                                        
                                                <div class="form-group col-md-4">
                                                    <label for="inputOfname" class="col-form-label">First Name</label>
                                                    <input type="text" value="<?= set_value('fname'); ?>" required name="fname" class="form-control">
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label for="inputOfmname" class="col-form-label">Middle Name</label>
                                                    <input type="text" value="<?= set_value('mname'); ?>" name="mname" class="form-control">
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label for="inputOflname" class="col-form-label">Last Name</label>
                                                    <input type="text" value="<?= set_value('lname'); ?>" required name="lname" class="form-control">
                                                </div>
                                        </div>        
                                        <div class="form-row">

                                                <div class="form-group col-md-4">
                                                    <label for="inputOfgender" class="col-form-label">Sex</label>
                                                    <select id="inputState" name="sex" class="form-control">
                                                    <?php 
                                                    $sex = array('Male','Female');
                                                    $c=0;
                                                    foreach($sex as $s){
                                                        echo "<option value='{$c}'>{$s}</option>";
                                                        $c++;
                                                    }
                                                    ?>
                                                    </select>
                                                </div>

                                                <div class="form-group col-md-8">
                                                    <label for="inputOfaddress" class="col-form-label">Address</label>
                                                    <input type="text" value="<?= set_value('address'); ?>" name="address" class="form-control">
                                                </div>

                                                
                                        </div>

                                        <div class="form-row">
                                        <div class="form-group col-md-4">
                                                    <label for="inputPosition" class="col-form-label">Position</label>
                                                        <select name="position" class="form-control">
                                                            <?php 
                                                                $position = array('Evaluator');
                                                                foreach($position as $row){
                                                            ?>
                                                            <option value="<?= $row; ?>"><?= $row; ?></option>
                                                            <?php } ?>
                                                            
                                                        </select>

                                                
                                                </div> 

                                                <div class="form-group col-md-4 box">
                                                    <label for="inputPosition" class="col-form-label">District</label>
                                                        <select name="user_id" class="form-control" required>
                                                            <option value=""></option>
                                                            <?php foreach($district as $row){ ?>
                                                            <option value="<?= $row->id; ?>"><?= $row->discription; ?></option>
                                                            <?php } ?>
                                                        </select>
                                                
                                                </div>  

                                                <div class="form-group col-md-4 box">
                                                    <label for="inputPosition" class="col-form-label">Evaluator Group</label>
                                                        <select name="eg" class="form-control" required>
                                                            <option value=""></option>
                                                            <?php 
                                                                $eg = array('Group 1' => 1,'Group 2' => 2,'Group 3' => 3);
                                                                foreach($eg as $row => $key){ 
                                                            ?>
                                                            <option <?php 
                                                                if($this->uri->segment(2) == $key){echo " selected ";}else{echo " disabled ";}
                                                            ?> value="<?= $key; ?>"><?= $row; ?></option>
                                                            <?php } ?>
                                                            
                                                        </select>
                                                
                                                </div>  
                                        </div>
                                        

                                        

                                        <button type="submit" value="submit" class="btn btn-primary">Submit</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <!-- end row -->


                    


                    <!-- end container-fluid -->

                </div>
                <!-- end content -->

                

                