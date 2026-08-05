<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="utf-8" />
        <title>DOORS | Davao de Oro Online Recruitment System</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta content="Responsive bootstrap 4 admin template" name="description" />
        <meta content="Coderthemes" name="author" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <!-- App favicon -->
        <link rel="shortcut icon" href="<?= base_url(); ?>assets/images/hris.ico">

        <!-- Display weights for the campaign lockup -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500;600;700;800&display=swap" rel="stylesheet">

        <!-- App css -->
        <link href="<?= base_url(); ?>assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" id="bootstrap-stylesheet" />
        <link href="<?= base_url(); ?>assets/css/icons.min.css" rel="stylesheet" type="text/css" />
        <link href="<?= base_url(); ?>assets/css/app.min.css" rel="stylesheet" type="text/css" id="app-stylesheet" />

        <!-- Shared DOORS public theme (sign in / forgot password / registration) -->
        <link href="<?= base_url(); ?>assets/css/doors-public.css" rel="stylesheet" type="text/css" />
    </head>

    <body class="authentication-page">

        <div class="auth-shell">

            <!-- Top bar -->
            <header class="site-nav">
                <div class="container">
                    <div class="nav-inner">
                        <div class="nav-brand-group">
                            <img class="nav-seal" src="<?= base_url(); ?>resources/background/ke.png" alt="Department of Education">
                            <img class="nav-seal" src="<?= base_url(); ?>resources/background/deoro.jpg" alt="Division of Davao de Oro">
                            <span class="nav-divider"></span>
                            <a class="brand" href="<?= base_url(); ?>">
                                <img src="<?= base_url(); ?>assets/images/logo.png" alt="DOORS">
                            </a>
                        </div>
                        <nav class="nav-links">
                            <a class="nav-link-plain nav-hide-xs" href="<?= base_url('log_in'); ?>">
                                <i class="mdi mdi-home-outline mr-1"></i>Home
                            </a>
                            <?php if (isset($page) && $page->status == 0) { ?>
                            <a class="nav-link-plain nav-link-ghost" href="<?= base_url('Pages/new_applicant'); ?>">
                                <i class="mdi mdi-account-plus-outline mr-1"></i>Apply Now
                            </a>
                            <?php } ?>
                        </nav>
                    </div>
                </div>
            </header>

            <!-- Middle band: background image is confined here -->
            <main class="auth-main">

                <div class="auth-photo">
                    <img src="<?= base_url(); ?>resources/background/background.png" alt="">
                </div>

                <div class="auth-scroll">
                    <div class="container">
                        <div class="row align-items-center justify-content-center">

                            <!-- Hero: DOORS leads -->
                            <div class="col-lg-6 col-xl-6 hero-col">
                              
                                <div class="hero-brand">
                                    <img class="hero-logo" style="width: 420px; height: auto;" src="<?= base_url(); ?>assets/images/logo.png" alt="DOORS — Davao de Oro Online Recruitment System">
                                </div>
                                  <span class="hero-eyebrow">
                                    <img src="<?= base_url(); ?>resources/background/deoro.jpg" alt="">
                                     Schools Division of Davao de Oro
                                </span>
                                <div class="hero-campaign">
                                    <span class="hero-campaign-text" style="font-size: 2rem;">Make It Happen</span>
                                    <span class="hero-campaign-ddo" style="font-size: 2rem;">DDO</span>
                                </div>
                            </div>

                            <!-- Login card -->
                            <div class="col-md-9 col-lg-6 col-xl-5 offset-xl-1">
                                <div class="card login-card mb-0">
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

                                            <div class="form-group">
                                                <label for="Username">Username / Email</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="mdi mdi-account-outline"></i></span>
                                                    </div>
                                                    <input class="form-control" type="text" id="Username" name="username" placeholder="Enter your username or email" autocomplete="off">
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label for="typepass">Password</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="mdi mdi-lock-outline"></i></span>
                                                    </div>
                                                    <input class="form-control" type="password" required="" name="password" placeholder="Enter your password" autocomplete="off" id="typepass">
                                                    <div class="input-group-append">
                                                        <button class="btn btn-eye" type="button" id="togglePass" onclick="Toggle()" aria-label="Show password">
                                                            <i class="mdi mdi-eye-outline" id="togglePassIcon"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-meta">
                                                <span>
                                                    <i class="mdi mdi-shield-check text-success mr-1"></i>Secure sign in
                                                </span>
                                                <a href="<?= base_url(); ?>Pages/forgot_password" class="forgot-link">
                                                    <i class="mdi mdi-lock-reset"></i> Forgot Password?
                                                </a>
                                            </div>

                                            <div class="form-group mb-0">
                                                <button class="btn btn-block btn-signin waves-effect" type="submit" name="submit">
                                                    <i class="mdi mdi-login-variant mr-1"></i> Sign In
                                                </button>
                                            </div>

                                            <?php if (isset($page) && $page->status == 0) { ?>
                                            <div class="signup-box">
                                                <p class="signup-label">Don&rsquo;t have an account yet? Create one as:</p>
                                                <div class="button-list">
                                                    <a class="btn btn-outline-info" href="<?= base_url('Pages/new_applicant'); ?>">
                                                        <i class="mdi mdi-account-plus mr-1"></i> Applicant
                                                    </a>
                                                    <a class="btn btn-outline-purple" href="<?= base_url('private'); ?>">
                                                        <i class="mdi mdi-school mr-1"></i> Private School
                                                    </a>
                                                </div>
                                            </div>
                                            <?php } ?>

                                        <?= form_close(); ?>

                                    </div>
                                    <!-- end card-body -->
                                </div>
                                <!-- end card -->
                            </div>
                            <!-- end col -->

                        </div>
                        <!-- end row -->
                    </div>
                </div>

            </main>

            <!-- Footer -->
            <footer class="site-foot">
                <div class="container">
                    <div class="foot-inner">
                        <span><span class="foot-brand">DOORS</span> &mdash; Davao de Oro Online Recruitment System </span>
                        <span class="nav-hide-xs">Department of Education &bull; Region XI &bull; Schools Division of Davao de Oro</span>
                    </div>
                </div>
            </footer>

        </div>

        <!-- Vendor js -->
        <script src="<?= base_url(); ?>assets/js/vendor.min.js"></script>

        <!-- App js -->
        <script src="<?= base_url(); ?>assets/js/app.min.js"></script>
        <script>
        // Change the type of input to password or text
        function Toggle() {
            var temp = document.getElementById("typepass");
            var icon = document.getElementById("togglePassIcon");
            var btn  = document.getElementById("togglePass");

            if (temp.type === "password") {
                temp.type = "text";
                if (icon) { icon.className = "mdi mdi-eye-off-outline"; }
                if (btn)  { btn.setAttribute("aria-label", "Hide password"); }
            }
            else {
                temp.type = "password";
                if (icon) { icon.className = "mdi mdi-eye-outline"; }
                if (btn)  { btn.setAttribute("aria-label", "Show password"); }
            }
        }
    </script>

    </body>

</html>
