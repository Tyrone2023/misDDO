
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

                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body table-responsive">
                                        <h4 class="header-title mb-4">Add New School Info</h4>
                                        <?php $att = array('class' => 'parsley-examples'); ?>
                                        <?= form_open('Page/school_new',$att); ?>

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
                                                        // $district = array('Baganga North','Baganga South','Banaybanay','Boston','Caraga North','Caraga South',
                                                        // 'Cateel 1','cateel 2','Gov. Generoso North','Gov. Generoso South','Lupon East','Lupon West',
                                                        // 'Manay North','San Isidro North District','Tarragona');

                                                        foreach($district as $row){
                                                        ?>
                                                        <option value="<?= $row->discription; ?>"><?= $row->discription; ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>

                                            </div>

                                            <div class="form-row">

                                                <div class="form-group col-md-3">
                                                    <label for="inputAddress" class="col-form-label" name="q1">Offers</label>
                                                    <select class="form-control" required name='course'>
                                                        <option disabled selected>Choose Offers</option>
                                                        <?php $district = array('Elementary','Integrated','Junior High School','Secondary');

                                                        foreach($district as $row){
                                                        ?>
                                                        <option value="<?= $row; ?>"><?= $row; ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                                <div class="form-group col-md-3">
                                                <label for="inputAddress" class="col-form-label" name="q1">School Type</label>
                                                    <select class="form-control" required name='schoolType'>
                                                        <option disabled selected></option>
                                                        <?php $district = array('Public','Private');

                                                        foreach($district as $row){
                                                        ?>
                                                        <option value="<?= $row; ?>"><?= $row; ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                                <div class="form-group col-md-3">
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

                                                <div class="form-group col-md-3">
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
                                                <div class="form-group col-md-4">
                                                    <label for="inputEmail4" class="col-form-label">Barangay</label>
                                                    <input type="text" required class="form-control" name="brgy" value="">
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label for="inputPassword4" class="col-form-label">Municipality/City</label>
                                                    <input type="text" required class="form-control" name="city" value="">
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label for="inputPassword4" class="col-form-label">Province</label>
                                                    <input type="text" required class="form-control" name="province" value="">
                                                </div>
                                            </div>


                                            <input type="submit" name="submit" value="Add New" class="btn btn-primary">
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- end row -->



            </div>
            <!-- end container-fluid -->

            </div>
            <!-- end content -->
