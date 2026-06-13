<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="utf-8" />
        <title>Human Resource Management Information System</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta content="Responsive bootstrap 4 admin template" name="description" />
        <meta content="Coderthemes" name="author" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <!-- App favicon -->
        <link rel="shortcut icon" href="<?= base_url(); ?>assets/images/dts.ico">

        <!-- App css -->
        <link href="<?= base_url(); ?>assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" id="bootstrap-stylesheet" />
        <link href="<?= base_url(); ?>assets/css/icons.min.css" rel="stylesheet" type="text/css" />
        <link href="<?= base_url(); ?>assets/css/app.min.css" rel="stylesheet" type="text/css" id="app-stylesheet" />

		<script type="text/javascript">
			function submitBday() {
			
				var Bdate = document.getElementById('bday').value;
				var Bday = +new Date(Bdate);
				Q4A= ~~ ((Date.now() - Bday) / (31557600000));
				var theBday = document.getElementById('resultBday');
				theBday.value = Q4A;
			}
		</script>

    </head>

    <body class="authentication-page">

        <div class="account-pages my-5">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-8 col-lg-6 col-xl-5">
                        <div class="card mt-4">
                            <div class="card-header p-4 bg-primary">
                                <h4 class="text-white text-center mb-0 mt-0">Sign Up</h4>
                            </div>
                            <div class="card-body">
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
                                
                            <?= validation_errors(); ?>
                                <?= form_open('register') ?>
								
									<input type="hidden" name="record_no" value="<?= date('Y'); ?><?php $c = $per+1; echo $c;?>">
									<input type="hidden" name="number" value="<?php $c = $per+1; echo $c;?>">
									<input type="hidden" name="per_id" value="<?= $per_id;?>">

                                    <div class="form-group mb-3">
                                        <label for="emailaddress">Email Address :</label>
                                        <input required class="form-control" type="text" name="email"  autocomplete="off" >
                                    </div>

                                    <div class="form-group mb-3">
                                        <label>First Name</label>
                                        <input required class="form-control" type="text" required="" name="fname"  autocomplete="off" >
                                    </div>
									<div class="form-group mb-3">
                                        <label>Middle Name</label>
                                        <input required class="form-control" type="text" required="" name="mname"  autocomplete="off" >
                                    </div>
									<div class="form-group mb-3">
                                        <label>Last Name</label>
                                        <input required class="form-control" type="text" required="" name="lname"  autocomplete="off" >
                                    </div>
									<div class="form-group mb-3">
                                        <label>Birth Date</label>
                                        <input required class="form-control" type="date" required="" id="bday" onchange="submitBday()" name="bdate"  autocomplete="off" >
                                    </div>
									<input type="hidden" name="age" id="resultBday" class="form-control" />

                                    

                                    <div class="form-group row text-center mt-4 mb-4">
                                        <div class="col-12">
                                            <button class="btn btn-md btn-block btn-primary waves-effect waves-light" type="submit" name="submit">Register</button>
                                        </div>
                                    </div>

                                    
                                </form>

                            </div>
                            <!-- end card-body -->
                        </div>
                        <!-- end card -->

                        <!-- end row -->

                    </div>
                    <!-- end col -->
                </div>
                <!-- end row -->

            </div>
        </div>

        <!-- Vendor js -->
        <script src="<?= base_url(); ?>assets/js/vendor.min.js"></script>

        <!-- App js -->
        <script src="<?= base_url(); ?>assets/js/app.min.js"></script>

    </body>

</html>