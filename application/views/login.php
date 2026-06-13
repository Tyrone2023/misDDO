<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="utf-8" />
        <title>Management Information System (MIS)</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta content="Responsive bootstrap 4 admin template" name="description" />
        <meta content="Coderthemes" name="author" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <!-- App favicon -->
        <link rel="shortcut icon" href="<?= base_url(); ?>assets/images/hris.ico">

        <!-- App css -->
        <link href="<?= base_url(); ?>assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" id="bootstrap-stylesheet" />
        <link href="<?= base_url(); ?>assets/css/icons.min.css" rel="stylesheet" type="text/css" />
        <link href="<?= base_url(); ?>assets/css/app.min.css" rel="stylesheet" type="text/css" id="app-stylesheet" />

    </head>

    <body class="authentication-page">

        <div class="account-pages my-5">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-8 col-lg-6 col-xl-5">
                        <div class="card mt-4">
                            <div class="card-header p-4 bg-primary">
                                <h4 class="text-white text-center mb-0 mt-0"><img  width="100%" src="<?= base_url(); ?>assets/images/logo.png" alt=""></h4>
                            </div>
                            <div class="card-body">
                            <?php if($this->session->flashdata('failed')) : ?>

                            <?= '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>'
                                    .$this->session->flashdata('failed'). 
                                '</div>'; 
                            ?>
                            <?php endif; ?> 

                            <?php if($this->session->flashdata('success')) : ?>

                                <?= '<div class="alert alert-success alert-dismissible fade show" role="alert">
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>'
                                        .$this->session->flashdata('success'). 
                                    '</div>'; 
                                ?>
                                <?php endif; ?>
                            
                                
                            <?= validation_errors(); ?>
                                <?= form_open('log_in') ?>

                                    <div class="form-group mb-3">
                                        <label for="emailaddress">Username / Email:</label>
                                        <input class="form-control" type="text" id="Username" name="username"  autocomplete="off" >
                                    </div>

                                    <div class="form-group mb-3">
                                        <label for="password">Password :</label>
                                        <input class="form-control" type="password" required="" name="password"  autocomplete="off" id="typepass" >
                                    </div>

                                    <div class="form-group mb-4">
                                        <div class="checkbox checkbox-success">
                                            <input id="remember" type="checkbox" onclick="Toggle()">
                                            <label for="remember">
                                                Show Password
                                            </label>
                                            <?php if($page->status == 0){ ?>
                                            <a href="new_applicant" class="text-muted float-right">Create an Account</a>
                                            <?php } ?>
                                            <a href="register" class="text-muted float-right">Create an Account/Register</a>
                                        </div>
                                    </div>

                                    <div class="form-group row text-center mt-4 mb-4">
                                        <div class="col-12">
                                            <button class="btn btn-md btn-block btn-primary waves-effect waves-light" type="submit" name="submit">Sign In</button>
                                        </div>
                                    </div>

                                    <div class="form-group row mb-0">
                                        <div class="col-sm-12 text-center">
                                            <p class="text-muted mb-0"><a href="<?= base_url(); ?>Pages/forgot_password" class="text-dark m-l-5"><b>Forgot Password?</b></a></p>
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
        <script>
        // Change the type of input to password or text
        function Toggle() {
            let temp = document.getElementById("typepass");
             
            if (temp.type === "password") {
                temp.type = "text";
            }
            else {
                temp.type = "password";
            }
        }
    </script>

    </body>

</html>