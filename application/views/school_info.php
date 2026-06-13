            <?php include('templates/head.php'); ?>
            <link href="<?= base_url(); ?>assets/libs/custombox/custombox.min.css" rel="stylesheet" type="text/css">
            <?php include('templates/header.php'); ?>

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
                                        <h4 class="header-title mb-4">Edit School Info</h4>

                                        <form method="post">

                                            <div class="form-row">
                                            <input type="hidden" class="form-control" name="oldSchoolID" value="<?php echo $data[0]->schoolID; ?>">

                                                
                                                <div class="form-group col-md-4">
                                                    <label for="inputAddress" class="col-form-label" name="q1">School Name</label>
                                                    <input type="text" class="form-control" name="schoolName" value="<?php echo $data[0]->schoolName; ?>">
                                                </div>
                                                <div class="form-group col-md-2">
                                                    <label for="inputAddress" class="col-form-label">School ID</label>
                                                    <input type="text" class="form-control" name="schoolID" value="<?php echo $data[0]->schoolID; ?>">
                                                </div>
                                                <div class="form-group col-md-3">
                                                    <label for="inputAddress" class="col-form-label" name="q1">District</label>
                                                    <!-- <input type="text" class="form-control" name="district" value="<?php echo $data[0]->district; ?>"> -->
                                                    <select class="form-control" name="district">
                                                        <?php foreach($district as $row){ ?>
                                                            <option <?php if($data[0]->district == $row->discription){echo "selected";} ?> value="<?= $row->discription; ?>"><?= $row->discription; ?></option>
                                                            <?php } ?>
                                                    </select>
                                                </div>
                                                <div class="form-group col-md-3">
                                                    <label for="inputAddress" class="col-form-label" name="q1">School Type</label>
                                                    <?php 
                                                        $school_type = array('elementary' , 'IS', 'IS with SHS', 'JHS with SHS', 'JHS');
                                                    ?>
                                                                        <select class="form-control" name="st">
                                                                            <?php foreach($school_type as $row){ ?>
                                                                            <option <?php if($data[0]->course == $row){echo "selected";} ?> value="<?= $row; ?>"><?= $row; ?></option>
                                                                            <?php } ?>
                                                                        </select>
                                                </div>

                                            </div>

                                            <div class="form-row">
                                                <div class="form-group col-md-4">
                                                    <label for="inputEmail4" class="col-form-label">School Head</label>
                                                    <input type="text" class="form-control" name="adminFName" value="<?php echo $data[0]->adminFName; ?>" placeholder="First Name">
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label for="inputPassword4" class="col-form-label">_</label>
                                                    <input type="text" class="form-control" name="adminMName" value="<?php echo $data[0]->adminMName; ?>" placeholder="Middle Name">
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label for="inputPassword4" class="col-form-label">_</label>
                                                    <input type="text" class="form-control" name="adminLName" value="<?php echo $data[0]->adminLName; ?>" placeholder="Last Name">
                                                </div>
                                            </div>

                                            <div class="form-row">
                                                <div class="form-group col-md-3">
                                                    <label for="inputPassword4" class="col-form-label">School Head Designation</label>
                                                    <input type="text" class="form-control" name="adminDesignation" value="<?php echo $data[0]->adminDesignation; ?>">
                                                </div>
                                                <div class="form-group col-md-3">
                                                    <label for="inputEmail4" class="col-form-label">School Head E-mail</label>
                                                    <input type="text" class="form-control" name="adminEmail" value="<?php echo $data[0]->adminEmail; ?>">
                                                </div>
                                                <div class="form-group col-md-3">
                                                    <label for="inputEmail4" class="col-form-label">School E-mail</label>
                                                    <input type="text" class="form-control" name="schoolEmail" value="<?php echo $data[0]->schoolEmail; ?>">
                                                </div>

                                                <div class="form-group col-md-3">
                                                    <label for="inputPassword4" class="col-form-label">Contact Number/s</label>
                                                    <input type="text" class="form-control" name="adminMobile" value="<?php echo $data[0]->adminMobile; ?>">
                                                </div>


                                            </div>

                                            <div class="form-row">
                                                <div class="form-group col-md-4">
                                                    <label for="inputEmail4" class="col-form-label">Barangay</label>
                                                    <input type="text" class="form-control" name="brgy" value="<?php echo $data[0]->brgy; ?>">
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label for="inputPassword4" class="col-form-label">Municipality/City</label>
                                                    <input type="text" class="form-control" name="city" value="<?php echo $data[0]->city; ?>">
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label for="inputPassword4" class="col-form-label">Province</label>
                                                    <input type="text" class="form-control" name="province" value="<?php echo $data[0]->province; ?>">
                                                </div>
                                            </div>


                                            <div class="form-row">
                                            <div class="form-group col-md-2">
                                                    <label for="inputEmail4" class="col-form-label">Electricity</label>
                                                    <select class="form-control" name="electricity">
                                                        <option <?php if($data[0]->electricity == 0){echo " selected "; }?> value="0">Yes</option>
                                                        <option <?php if($data[0]->electricity == 1){echo " selected "; }?> value="1">No</option>
                                                    </select>
                                                </div>
                                                <div class="form-group col-md-2">
                                                    <label for="inputEmail4" class="col-form-label">Internet Connection</label>
                                                    <select class="form-control" name="internet">
                                                        <option <?php if($data[0]->internet == 0){echo " selected "; }?> value="0">Yes</option>
                                                        <option <?php if($data[0]->internet == 1){echo " selected "; }?> value="1">No</option>
                                                    </select>
                                                </div>
                                                <div class="form-group col-md-3">
                                                    <label for="inputPassword4" class="col-form-label">Internet Provider(If Yes)</label>
                                                    <input type="text" class="form-control" name="provider" value="<?php echo $data[0]->provider; ?>">
                                                </div>
                                                <div class="form-group col-md-2">
                                                    <label for="inputPassword4" class="col-form-label">Internet Speed(If Yes)</label>
                                                    <input type="text" class="form-control" name="mb" value="<?php echo $data[0]->mb; ?>" oninput="this.value = this.value.replace(/[^0-9]/g, '');">
                                                </div>
                                                <div class="form-group col-md-3">
                                                    <label for="inputPassword4" class="col-form-label">ICT Coordinator ID (Employee Number)</label>
                                                    <input type="text" class="form-control" name="coor" value="<?php echo $data[0]->coor; ?>" oninput="this.value = this.value.replace(/[^0-9]/g, '');">
                                                </div>
                                               
                                            </div>



                                           

                                           


                                            <input type="submit" name="submit" value="Update" class="btn btn-primary">
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- end row -->


                    </div>
                </div>
            </div>
            </div>
            <!-- end row -->

            </div>
            <!-- end container-fluid -->

            </div>
            <!-- end content -->

            <?php include('templates/footer.php'); ?>