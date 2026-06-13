
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
                                    <h4 class="page-title">APPLICANT'S PROFILE</h4>
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                        </div>

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
                        <!-- end page title -->

                        <!-- Form row -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-body">

                                        <br />
                                        <?= validation_errors(); ?>
                                        <?php $att = array('class' => 'parsley-examples'); ?>
                                        <?= form_open('Pages/new_applicant', $att); ?>

                                        <input type="hidden" value="<?= date('Y');  ?>00<?= $count->number+1; ?>" name="record_no" class="form-control"> 
                                        <input type="hidden" value="<?= $count->number+1; ?>" name="number" class="form-control"> 
                                            <div class="form-row">
                                                <div class="form-group col-md-1">
                                                    <label for="inputname" class="col-form-label">Prefix</label>
                                                    <select name="prefix" required class="form-control">
                                                        <option></option>
                                                        <?php 
                                                            $array = array('Mr.', 'Ms.', 'Mrs.'); 
                                                            foreach($array as $ar){
                                                                echo '<option>'. $ar . '</option>';
                                                            }
                                                        ?>
													</select>
                                                </div>

                                                <div class="form-group col-md-4">
                                                    <label for="inputname" class="col-form-label">First Name</label>
                                                    <input type="text" required value="<?= set_value('fname'); ?>" name="fname" class="form-control">
                                                </div>
                                          
                                                <div class="form-group col-md-3">
                                                    <label for="inputname" class="col-form-label">Middle Name</label>
                                                    <input type="text" value="<?= set_value('mname'); ?>" name="mname" class="form-control">
                                                </div>
                                                <div class="form-group col-md-3">
                                                    <label for="inputname" class="col-form-label">Last Name</label>
                                                    <input type="text" required value="<?= set_value('lname'); ?>" name="lname" class="form-control">
                                                </div>
                                                <div class="form-group col-md-1">
                                                    <label for="inputname" class="col-form-label">EXTENSION</label>
                                                    <input type="text" value="<?= set_value('ext'); ?>" name="ext" class="form-control" placeholder="Jr, Sr">
                                                </div>
                                            </div>

                                            <div class="form-row">

                                                <div class="form-group col-md-5">
                                                    <label for="inputname" class="col-form-label">Email Address</label>
                                                    <input type="email" required value="<?= set_value('email'); ?>" name="email" class="form-control">
                                                </div>
                                          
                                                <div class="form-group col-md-3">
                                                    <label for="inputname" class="col-form-label">Contact Number</label>
                                                    <input type="text" required value="<?= set_value('contact'); ?>" name="contact" class="form-control">
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label for="inputname" class="col-form-label">Birth Date</label>
                                                    <input type="date" required value="<?= set_value('bd'); ?>" name="bd" class="form-control">
                                                </div>
                                            </div>


                                            <div class="form-row">
                                                <div class="form-group col-md-2">
                                                    <label for="inputname" class="col-form-label">Purok</label>
                                                    <input type="text" required value="<?= set_value('purok'); ?>" name="purok" class="form-control">
                                                </div>
                                          
                                                <div class="form-group col-md-3">
                                                    <label for="inputname" class="col-form-label">Barangay</label>
                                                    <input type="text" required value="<?= set_value('barangay'); ?>" name="barangay" class="form-control">
                                                </div>
                                                <div class="form-group col-md-3">
                                                    <label for="inputname" class="col-form-label">Municipality/City</label>
                                                    <input type="text" required value="<?= set_value('mun_city'); ?>" name="mun_city" class="form-control">
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label for="inputname" class="col-form-label">Province</label>
                                                    <input type="text" required value="<?= set_value('province'); ?>" name="province" class="form-control">
                                                </div>
                                            </div>

                                            <div class="form-row">
                                                <div class="form-group col-md-2">
                                                    <label for="lastName">Group <span class="ren_r">*</span></label>
                                                    <select required name="group" class="form-control">
                                                    <option></option>
                                                    <?php 
                                                        $array = array('Elementary', 'Junior High School', 'Senior High School'); 
                                                        foreach($array as $ar){
                                                        echo '<option value="'. $ar.'">'. $ar . '</option>';
                                                        }
                                                    ?>
                                                    </select>
                                                </div> 

                                                <div class="form-group col-md-3">
                                                    <label for="lastName">Specialization : <span style="font-style:italic; color:red; font-weight:300; font-size:10px"> "Note: required for JHS and SHS"</span></label>
                                                    <input type="text" value="<?= set_value('specialization'); ?>" name="specialization" class="form-control">
                                                    </select>
                                                </div>

                                                <div class="form-group col-md-3">
                                                    <label for="lastName">Track : <span style="font-style:italic; color:red; font-weight:300; font-size:10px"> "Note: required for Senior High School Only"</span></label>
                                                    <select name="track" class="form-control">
                                                    <option></option>
                                                    <?php 
                                                        $array = array('Academic Track', 'Technical Vocational Livelihood Track', 'Arts and Design', 'Sports'); 
                                                        foreach($array as $ar){
                                                        echo '<option>'. $ar . '</option>';
                                                        }
                                                    ?>
                                                    </select>
                                                </div>

                                                <div class="form-group col-md-4">
                                                    <label for="lastName">Preferred School</label>
                                                    <input type="text" value="<?= set_value('pre_school'); ?>" name="pre_school" class="form-control">
                                                </div>

                                            </div>

                                            <br />
                                            <button type="submit" value="submit" class="btn btn-primary"><?= $title; ?></button>
                                            
                                        </form>
                                        <br />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- end row -->


                    


                    <!-- end container-fluid -->

                </div>
                <!-- end content -->

                

                