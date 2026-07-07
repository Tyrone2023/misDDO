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

        <style>
            body.authentication-page {
                min-height: 100vh;
                background: #ffffff;
            }
            .account-pages {
                min-height: 100vh;
                display: flex;
                align-items: center;
                padding: 1.25rem 0;
            }
            .login-card {
                border: none;
                border-radius: .75rem;
                overflow: hidden;
                box-shadow: 0 1rem 3rem rgba(0, 0, 0, .3);
            }
            .login-card .card-header {
                border-bottom: none;
            }
            .login-card .card-header img {
                width: auto !important;
                max-width: 100%;
                max-height: 120px;
            }
            .login-card .card-body {
                padding: 1.25rem 1.75rem 1.5rem;
            }
            .login-title {
                text-align: center;
                font-weight: 700;
                font-size: 1.25rem;
                color: #323a46;
                margin-bottom: .15rem;
            }
            .login-subtitle {
                text-align: center;
                color: #98a6ad;
                font-size: .85rem;
                margin-bottom: 1rem;
            }
            .login-card .form-group > label {
                font-weight: 600;
                color: #6c757d;
                font-size: .8rem;
                text-transform: uppercase;
                letter-spacing: .3px;
            }
            .login-card .input-group-text {
                background: #f7f7f9;
                border-right: none;
                color: #98a6ad;
                width: 46px;
                padding: 0;
                justify-content: center;
                font-size: 1.15rem;
            }
            .login-card .form-control {
                border-left: none;
                height: calc(1.5em + .9rem + 2px);
            }
            .login-card .form-control:focus {
                box-shadow: none;
                border-color: #ced4da;
            }
            .login-card .input-group:focus-within .input-group-text,
            .login-card .input-group:focus-within .form-control {
                border-color: #3688fc;
            }
            .btn-signin {
                height: 44px;
                font-weight: 600;
                letter-spacing: .5px;
                border-radius: .35rem;
            }
            /* Emphasized forgot-password link */
            .forgot-link {
                display: inline-flex;
                align-items: center;
                gap: .35rem;
                font-weight: 700;
                color: #f1556c !important;
                padding: .3rem .25rem;
                border-bottom: 2px solid transparent;
                transition: all .15s ease-in-out;
            }
            .forgot-link:hover {
                color: #e02c47 !important;
                border-bottom-color: #f1556c;
                text-decoration: none;
            }
            .forgot-link .mdi {
                font-size: 1.05rem;
            }
            .signup-box {
                margin-top: 1.1rem;
                padding-top: 1rem;
                border-top: 1px dashed #dee2e6;
                text-align: center;
            }
            .signup-box .signup-label {
                font-weight: 600;
                color: #323a46;
                margin-bottom: .65rem;
            }
            .signup-box .btn {
                min-width: 150px;
                margin: .25rem;
                font-weight: 600;
            }
        </style>
    </head>

    <body class="authentication-page">

        <div class="account-pages">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-8 col-lg-6 col-xl-5">
                        <div class="card login-card">
                            <div class="card-header p-3 bg-primary text-center">
                                <h4 class="text-white text-center mb-0 mt-0"><img width="100%" src="<?= base_url(); ?>assets/images/logo.png" alt=""></h4>
                            </div>
                            <div class="card-body">

                                <h4 class="login-title">Welcome Back</h4>
                                <p class="login-subtitle">Sign in to continue to your account.</p>

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

                                    <div class="form-group mb-2">
                                        <label for="Username">Username / Email</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="mdi mdi-account-outline"></i></span>
                                            </div>
                                            <input class="form-control" type="text" id="Username" name="username" placeholder="Enter your username or email" autocomplete="off">
                                        </div>
                                    </div>

                                    <div class="form-group mb-2">
                                        <label for="typepass">Password</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="mdi mdi-lock-outline"></i></span>
                                            </div>
                                            <input class="form-control" type="password" required="" name="password" placeholder="Enter your password" autocomplete="off" id="typepass">
                                        </div>
                                    </div>

                                    <div class="d-flex align-items-center justify-content-between mb-4">
                                        <div class="checkbox checkbox-success mb-0">
                                            <input id="remember" type="checkbox" onclick="Toggle()">
                                            <label for="remember">
                                                Show Password
                                            </label>
                                        </div>
                                        <a href="<?= base_url(); ?>Pages/forgot_password" class="forgot-link">
                                            <i class="mdi mdi-lock-reset"></i> Forgot Password?
                                        </a>
                                    </div>

                                    <div class="form-group mb-0">
                                        <button class="btn btn-block btn-primary btn-signin waves-effect waves-light" type="submit" name="submit">
                                            <i class="mdi mdi-login mr-1"></i> Sign In
                                        </button>
                                    </div>

                                    <?php if($page->status == 0){ ?>
                                    <div class="signup-box">
                                        <p class="signup-label">Create an Account as:</p>
                                        <div class="button-list">
                                            <a class="btn btn-info" href="<?= base_url('Pages/new_applicant'); ?>">
                                                <i class="mdi mdi-account-plus mr-1"></i> Applicant
                                            </a>
                                            <a class="btn btn-purple" href="<?= base_url('private'); ?>">
                                                <i class="mdi mdi-school mr-1"></i> Private School
                                            </a>
                                        </div>
                                    </div>
                                    <?php } ?>


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
