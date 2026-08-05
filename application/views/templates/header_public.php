<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title><?= isset($page_title) ? html_escape($page_title) . ' | ' : ''; ?>DOORS &mdash; Davao de Oro Online Recruitment System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description"
        content="Online recruitment and human resource information system &mdash; create an account, apply for vacant positions and track your application." />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="theme-color" content="#1a2942" />
    <!-- <link rel="shortcut icon" href="<?= base_url(); ?>assets/images/hris.ico"> -->

    <!-- Display weights for the campaign lockup -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500;600;700;800&display=swap" rel="stylesheet">

    <link href="<?= base_url(); ?>assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" id="bootstrap-stylesheet" />
    <link href="<?= base_url(); ?>assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <link href="<?= base_url(); ?>assets/css/app.min.css" rel="stylesheet" type="text/css" id="app-stylesheet" />
    <link href="<?= base_url(); ?>assets/libs/custombox/custombox.min.css" rel="stylesheet" type="text/css">

    <!-- Shared DOORS public theme (sign in / forgot password / registration) -->
    <link href="<?= base_url(); ?>assets/css/doors-public.css" rel="stylesheet" type="text/css" />

</head>

<body class="public-page">

    <!-- Begin page -->
    <div id="wrapper">


        <!-- Topbar Start -->
        <!-- .navbar-custom is position:fixed and 70px tall in the theme, and
             .content-page offsets itself by exactly that much — so the height
             stays 70px here and only the styling changes. The markup mirrors
             the sign-in page's top bar so the two read as one site. -->
        <div class="navbar-custom public-topbar">
            <div class="nav-inner d-flex align-items-center justify-content-between">

                <div class="nav-brand-group">
                    <img class="nav-seal" src="<?= base_url(); ?>resources/background/ke.png" alt="Department of Education">
                    <img class="nav-seal" src="<?= base_url(); ?>resources/background/deoro.jpg" alt="Division of Davao de Oro">
                    <span class="nav-divider"></span>
                    <a class="brand" href="<?= base_url(); ?>">
                        <img src="<?= base_url(); ?>assets/images/logo.png"
                            alt="DOORS &mdash; Davao de Oro Online Recruitment System">
                    </a>
                </div>

                <nav class="nav-links">
                    <a class="nav-link-plain nav-hide-xs" href="<?= base_url(); ?>new_applicant">
                        <i class="mdi mdi-account-plus-outline mr-1"></i>Register
                    </a>
                    <a class="nav-link-plain nav-link-ghost" href="<?= base_url(); ?>log_in">
                        <i class="mdi mdi-login-variant mr-1"></i>Sign In
                    </a>
                </nav>

            </div>
        </div>
        <!-- end Topbar --> <!-- ========== Left Sidebar Start ========== -->
