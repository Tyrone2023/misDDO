
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
                                <h4 class="page-title">Sign Up</h4>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                    </div>
                    

                    <?php if ($this->session->flashdata('success')) : ?>

                        <?= '<div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>'
                            . $this->session->flashdata('success') .
                            '</div>';
                        ?>
                    <?php endif; ?>

                    <?php if ($this->session->flashdata('danger')) : ?>
                        <?= '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>'
                            . $this->session->flashdata('danger') .
                            '</div>';
                        ?>
                    <?php endif;  ?>
                    <!-- end page title -->

                    <?= validation_errors(); ?>
                    <?php $att = array('class' => 'parsley-examples'); ?>
                    <?= form_open('Ps/private', array_merge($att)); ?>




                    <!-- Form row -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-body">

                                    <br />
                                    <input type="hidden" valu="" name="renren">
                                    <input type="hidden" valu="" name="ivykate">
                                    <input type="hidden" valu="" name="ivankyle">
                                    <input type="hidden" valu="" name="ic">

                                    <div class="form-row">

                                                <div class="form-group col-md-4">
                                                    <label for="inputAddress" class="col-form-label" name="q1">School Name</label>
                                                    <input type="text" required class="form-control" name="schoolName" value="">
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label for="inputAddress" class="col-form-label">School ID</label>
                                                    <input type="text" required class="form-control" name="schoolID" value="">
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label for="inputAddress" class="col-form-label" name="q1">District</label>
                                                    <select class="form-control" required name="district">
                                                        <option disabled selected>Choose District</option>
                                                        <?php 
                                                        foreach($district as $row){
                                                        ?>
                                                        <option value="<?= $row->discription; ?>"><?= $row->discription; ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>

                                            </div>

                                            <div class="form-row">

                                                <div class="form-group col-md-4">
                                                    <label for="inputAddress" class="col-form-label" name="q1">Offers</label>
                                                    <select class="form-control" required name='course'>
                                                        <option disabled selected>Choose Offers</option>
                                                        <?php $district = array('Elementary','Integrated','Junior High School','Senior High School','Junior and Senior High School','Secondary','Complete');

                                                        foreach($district as $row){
                                                        ?>
                                                        <option value="<?= $row; ?>"><?= $row; ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                                <input type="hidden" value="Private" name="schoolType">
                                                <!-- <div class="form-group col-md-3">
                                                <label for="inputAddress" class="col-form-label" name="q1">School Type</label>
                                                    <select class="form-control" required name='schoolType'>
                                                        <option disabled selected></option>
                                                        <?php $district = array('Public','Private');

                                                        foreach($district as $row){
                                                        ?>
                                                        <option value="<?= $row; ?>"><?= $row; ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div> -->
                                                <div class="form-group col-md-4">
                                                    <label for="inputAddress" class="col-form-label" name="q1">Congressional District</label>
                                                    <select class="form-control" required name='congDist'>
                                                        <option disabled selected></option>
                                                        <?php $district = array(1,2,3,4);

                                                        foreach($district as $row){
                                                        ?>
                                                        <option value="<?= $row; ?>"><?= $row; ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>

                                                <div class="form-group col-md-4">
                                                    <label for="inputAddress" class="col-form-label">year Establish</label>
                                                    <input type="text" required class="form-control" name="yearEstab" value="">
                                                </div>

                                            </div>

                                            

                                            <div class="form-row">
                                                <div class="form-group col-md-4">
                                                    <label for="inputEmail4" class="col-form-label">School Head</label>
                                                    <input type="text" required class="form-control" name="adminFName" value="" placeholder="First Name">
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label for="inputPassword4" class="col-form-label">_</label>
                                                    <input type="text" required class="form-control" name="adminMName" value="" placeholder="Middle Name">
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label for="inputPassword4" class="col-form-label">_</label>
                                                    <input type="text" required class="form-control" name="adminLName" value="" placeholder="Last Name">
                                                </div>
                                            </div>

                                            <div class="form-row">
                                                <div class="form-group col-md-3">
                                                    <label for="inputPassword4" class="col-form-label">School Head Designation</label>
                                                    <input type="text" required class="form-control" name="adminDesignation" value="">
                                                </div>
                                                <div class="form-group col-md-3">
                                                    <label for="inputEmail4" class="col-form-label">School Head E-mail</label>
                                                    <input type="text" required class="form-control" name="adminEmail" value="">
                                                </div>
                                                <div class="form-group col-md-3">
                                                    <label for="inputEmail4" class="col-form-label">School E-mail</label>
                                                    <input type="text" required class="form-control" name="schoolEmail" value="">
                                                </div>

                                                <div class="form-group col-md-3">
                                                    <label for="inputPassword4" class="col-form-label">Contact Number/s</label>
                                                    <input type="text" required  class="form-control" name="adminMobile" value="">
                                                </div>


                                            </div>

                                            <div class="form-row">
                                                    <input type="hidden" required class="form-control" name="province" value="Davao Oriental">
                                                <div class="form-group col-md-6">
                                                    <label for="inputPassword6" class="col-form-label">Municipality/City</label>
                                                    <input type="text" required class="form-control" name="city" value="">
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <label for="inputEmail6" class="col-form-label">Barangay</label>
                                                    <input type="text" required class="form-control" name="brgy" value="">
                                                </div>
                                                
                                            </div>

                                    

                                    <div class="form-group">
                                        <div class="g-recaptcha" data-sitekey="<?= trim($mis_settings->site_key); ?>"></div>
                                    </div>


                                    

                                    <br />
                                    <button type="submit" value="submit" class="btn btn-primary">Register</button>

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
                 <script src="https://www.google.com/recaptcha/api.js" async defer></script>
                 <script>
                    function validateCaptcha(event) {
                        var response = grecaptcha.getResponse();
                        if (response.length == 0) {
                            alert("Please verify that you are not a robot.");
                            event.preventDefault(); // prevent form submission
                            return false;
                        }
                    }
                </script>