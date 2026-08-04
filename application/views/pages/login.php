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

        <style>
            :root {
                /* blue family — matches the app navbar / sidebar (#1a2942) */
                --navy: #1a2942;
                --navy-dark: #101a2b;
                --navy-light: #24365a;
                --teal: #3bc0c3;
                --gold: #e5a812;
                --gold-light: #f6cd52;
            }

            html, body {
                height: 100%;
            }

            /* Single-screen shell: the page itself never scrolls */
            body.authentication-page {
                height: 100vh;
                overflow: hidden;
                background: var(--navy);
                color: #fff;
            }

            .auth-shell {
                height: 100vh;
                display: flex;
                flex-direction: column;
            }

            /* ---------- Top bar (solid: the photo stops here) ---------- */
            .site-nav {
                flex: 0 0 auto;
                position: relative;
                z-index: 3;
                padding: .55rem 0;
                background: linear-gradient(180deg, #22345a 0%, var(--navy) 100%);
                border-bottom: 1px solid rgba(122, 160, 214, .3);
                box-shadow: 0 4px 20px rgba(8, 14, 24, .55);
            }
            .site-nav .nav-inner {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 1rem;
            }
            .nav-brand-group {
                display: flex;
                align-items: center;
                gap: .65rem;
                min-width: 0;
            }
            .nav-seal {
                height: 34px;
                width: 34px;
                object-fit: contain;
                border-radius: 50%;
                flex: 0 0 auto;
                filter: drop-shadow(0 2px 5px rgba(0, 0, 0, .5));
            }
            .nav-divider {
                width: 1px;
                height: 28px;
                flex: 0 0 auto;
                background: linear-gradient(180deg, rgba(122,160,214,0) 0%, rgba(122,160,214,.7) 50%, rgba(122,160,214,0) 100%);
                margin: 0 .2rem;
            }
            .site-nav .brand img {
                height: 38px;
                width: auto;
                max-width: 100%;
                display: block;
                filter: drop-shadow(0 2px 10px rgba(229, 168, 18, .3));
            }
            .site-nav .nav-links {
                display: flex;
                align-items: center;
                gap: .4rem;
            }
            .nav-link-plain {
                color: rgba(255, 255, 255, .85) !important;
                font-size: .8rem;
                font-weight: 600;
                letter-spacing: .2px;
                padding: .4rem .8rem;
                border-radius: .4rem;
                transition: all .15s ease-in-out;
                white-space: nowrap;
            }
            .nav-link-plain:hover {
                color: #fff !important;
                background: rgba(122, 160, 214, .22);
                text-decoration: none;
            }
            .nav-link-ghost {
                border: 1px solid rgba(246, 205, 82, .5);
                color: var(--gold-light) !important;
            }
            .nav-link-ghost:hover {
                background: rgba(229, 168, 18, .2) !important;
                color: #fff !important;
            }

            /* ---------- Middle band: the photo lives ONLY here ---------- */
            .auth-main {
                position: relative;
                flex: 1 1 auto;
                min-height: 0;
                overflow: hidden;
            }
            .auth-photo {
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                overflow: hidden;
                z-index: 0;
                background: var(--navy);
            }
            .auth-photo img {
                width: 100%;
                height: 100%;
                object-fit: cover;
                object-position: center center;
                display: block;
            }
            /* navy-tinted veil */
            .auth-photo::after {
                content: "";
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background:
                    radial-gradient(72% 66% at 26% 50%, rgba(12,20,35,.76) 0%, rgba(12,20,35,.44) 48%, rgba(12,20,35,.05) 78%),
                    linear-gradient(100deg, rgba(16,26,43,.90) 0%, rgba(26,41,66,.76) 38%, rgba(45,62,94,.56) 70%, rgba(62,80,114,.46) 100%),
                    linear-gradient(180deg, rgba(16,26,43,.58) 0%, rgba(16,26,43,.08) 26%, rgba(16,26,43,.08) 74%, rgba(16,26,43,.58) 100%);
            }
            .auth-scroll {
                position: relative;
                z-index: 2;
                height: 100%;
                overflow-y: auto;
                display: flex;
                padding: 1.25rem 0;
            }
            .auth-scroll > .container {
                margin-top: auto;
                margin-bottom: auto;
                width: 100%;
            }

            /* ---------- Hero: DOORS first, all three flush-left ---------- */
            .hero-col {
                position: relative;
                padding: .9rem 0 .9rem 1.6rem;
            }
            /* gold accent bar down the left of the brand block */
            .hero-col::before {
                content: "";
                position: absolute;
                left: 0;
                top: .3rem;
                bottom: .3rem;
                width: 4px;
                border-radius: 100px;
                background: linear-gradient(180deg,
                    rgba(246, 205, 82, 0) 0%,
                    #f6cd52 16%,
                    #e5a812 84%,
                    rgba(229, 168, 18, 0) 100%);
                box-shadow: 0 0 20px rgba(229, 168, 18, .55);
            }

            /* 1 — division chip */
            .hero-eyebrow {
                display: inline-flex;
                align-items: center;
                gap: .5rem;
                font-size: .69rem;
                font-weight: 700;
                line-height: 1;
                letter-spacing: 1.4px;
                text-transform: uppercase;
                color: #fff;
                background: rgba(36, 54, 90, .62);
                border: 1px solid rgba(122, 160, 214, .5);
                padding: .26rem .85rem .26rem .3rem;
                border-radius: 100px;
                margin-bottom: .75rem;
                backdrop-filter: blur(6px);
                -webkit-backdrop-filter: blur(6px);
            }
            .hero-eyebrow img {
                height: 22px;
                width: 22px;
                object-fit: contain;
                border-radius: 50%;
                flex: 0 0 auto;
            }

            /* 2 — DOORS wordmark, the primary brand on this page */
            .hero-brand {
                position: relative;
                display: block;
                z-index: 0;
                line-height: 0;
            }
            .hero-brand::before {
                content: "";
                position: absolute;
                left: 42%;
                top: 50%;
                width: 130%;
                height: 250%;
                transform: translate(-50%, -50%);
                background: radial-gradient(closest-side, rgba(229, 168, 18, .30) 0%, rgba(229, 168, 18, .09) 55%, rgba(229, 168, 18, 0) 100%);
                z-index: -1;
            }
            .hero-logo {
                --logo-h: 88px;
                display: block;
                height: var(--logo-h);
                width: auto;
                max-width: 100%;
                /* the PNG carries 3% transparent padding on its left edge — pull it
                   back so DOORS lines up with the chip and the campaign line */
                margin-left: calc(var(--logo-h) * -0.0872);
                filter: drop-shadow(0 8px 24px rgba(6, 12, 22, .7));
            }

            /* 3 — Make It Happen DDO */
            .hero-campaign {
                display: flex;
                align-items: baseline;
                flex-wrap: wrap;
                gap: .1rem .55rem;
                margin-top: .7rem;
                line-height: 1.05;
            }
            .hero-campaign-text {
                font-family: Montserrat, sans-serif;
                font-weight: 700;
                font-size: 1.12rem;
                line-height: 1.05;
                letter-spacing: 3.4px;
                text-transform: uppercase;
                color: rgba(255, 255, 255, .94);
                text-shadow: 0 2px 14px rgba(8, 14, 26, .95);
            }
            .hero-campaign-ddo {
                font-family: Montserrat, sans-serif;
                font-weight: 800;
                font-size: 1.72rem;
                line-height: 1;
                letter-spacing: 1.5px;
                color: var(--gold-light);
                background: linear-gradient(180deg, #fbe08a 0%, #f6cd52 45%, #e5a812 100%);
                -webkit-background-clip: text;
                background-clip: text;
                -webkit-text-fill-color: transparent;
                filter: drop-shadow(0 2px 10px rgba(229, 168, 18, .45));
            }

            /* ---------- Login card ---------- */
            .login-card {
                position: relative;
                border: 1px solid rgba(255, 255, 255, .55);
                border-radius: 1rem;
                overflow: hidden;
                background: rgba(255, 255, 255, .97);
                backdrop-filter: blur(22px) saturate(140%);
                -webkit-backdrop-filter: blur(22px) saturate(140%);
                /* layered navy drop shadow */
                box-shadow:
                    0 1px 3px rgba(16, 26, 43, .35),
                    0 8px 18px -6px rgba(16, 26, 43, .5),
                    0 22px 42px -12px rgba(16, 26, 43, .62),
                    0 46px 90px -22px rgba(8, 14, 26, .8);
            }
            .login-card::before {
                content: "";
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                height: 4px;
                background: linear-gradient(90deg, #e5a812 0%, #f6cd52 50%, #e5a812 100%);
            }
            .login-card .card-body {
                padding: 1.5rem 1.75rem 1.3rem;
            }
            .login-title {
                font-family: Montserrat, sans-serif;
                font-weight: 700;
                font-size: 1.32rem;
                line-height: 1.2;
                color: var(--navy);
                margin-bottom: .15rem;
            }
            .login-subtitle {
                color: #8b95a5;
                font-size: .82rem;
                line-height: 1.35;
                margin-bottom: 1.05rem;
            }
            .login-card .form-group {
                margin-bottom: .8rem;
            }
            .login-card .form-group > label {
                font-weight: 700;
                color: #6f7d90;
                font-size: .69rem;
                text-transform: uppercase;
                letter-spacing: .7px;
                margin-bottom: .3rem;
            }
            .login-card .input-group-text {
                background: #eef2f8;
                border: 1px solid #d6dfec;
                border-right: none;
                color: #8b98ab;
                width: 44px;
                padding: 0;
                justify-content: center;
                font-size: 1.15rem;
            }
            .login-card .form-control {
                border: 1px solid #d6dfec;
                border-left: none;
                background: #fbfcfe;
                height: calc(1.5em + 1rem + 2px);
                font-size: .89rem;
                color: var(--navy);
                box-shadow: none;
            }
            .login-card .input-group > .input-group-prepend > .input-group-text {
                border-top-left-radius: .45rem;
                border-bottom-left-radius: .45rem;
            }
            .login-card .input-group > .input-group-append > .btn {
                border-top-right-radius: .45rem;
                border-bottom-right-radius: .45rem;
            }
            .login-card .form-control:focus {
                box-shadow: none;
                border-color: #d6dfec;
                background: #fff;
            }
            .login-card .input-group:focus-within .input-group-text,
            .login-card .input-group:focus-within .form-control,
            .login-card .input-group:focus-within .btn-eye {
                border-color: var(--navy-light);
            }
            .login-card .input-group:focus-within .input-group-text {
                background: rgba(36, 54, 90, .1);
                color: var(--navy-light);
            }
            .btn-eye {
                background: #eef2f8;
                border: 1px solid #d6dfec;
                border-left: none;
                color: #8b98ab;
                width: 44px;
                padding: 0;
                font-size: 1.1rem;
                line-height: 1;
                box-shadow: none !important;
            }
            .btn-eye:hover,
            .btn-eye:focus {
                background: #e3eaf4;
                color: var(--navy);
            }
            .btn-signin {
                height: 44px;
                font-weight: 700;
                font-size: .89rem;
                letter-spacing: .4px;
                border: none;
                border-radius: .5rem;
                color: #241a00;
                background: linear-gradient(92deg, #f6cd52 0%, #e5a812 100%);
                box-shadow: 0 8px 18px -6px rgba(229, 168, 18, .65);
                transition: all .15s ease-in-out;
            }
            .btn-signin:hover,
            .btn-signin:focus {
                color: #241a00;
                background: linear-gradient(92deg, #ffd968 0%, #f0b41d 100%);
                box-shadow: 0 10px 24px -6px rgba(229, 168, 18, .8);
                transform: translateY(-1px);
            }
            .form-meta {
                display: flex;
                align-items: center;
                justify-content: space-between;
                flex-wrap: wrap;
                gap: .5rem;
                margin-bottom: .9rem;
                font-size: .77rem;
                line-height: 1.2;
                color: #8b95a5;
            }
            .forgot-link {
                display: inline-flex;
                align-items: center;
                gap: .3rem;
                font-weight: 700;
                font-size: .79rem;
                color: #f1556c !important;
            }
            .forgot-link:hover {
                color: #e02c47 !important;
                text-decoration: underline;
            }
            .signup-box {
                margin-top: 1rem;
                padding-top: .9rem;
                border-top: 1px solid #e6ecf4;
                text-align: center;
            }
            .signup-box .signup-label {
                font-weight: 600;
                font-size: .78rem;
                line-height: 1.3;
                color: #8b95a5;
                margin-bottom: .55rem;
            }
            .signup-box .btn {
                min-width: 140px;
                margin: .2rem;
                font-weight: 600;
                font-size: .81rem;
                border-radius: .45rem;
            }
            .login-card .alert {
                font-size: .83rem;
                line-height: 1.35;
                padding: .55rem .8rem;
                border-radius: .5rem;
            }
            .login-card .error {
                background: rgba(241, 85, 108, .09);
                border: 1px solid rgba(241, 85, 108, .35);
                color: #d03f3f;
                font-size: .8rem;
                font-weight: 600;
                line-height: 1.3;
                padding: .4rem .7rem;
                border-radius: .45rem;
                margin-bottom: .4rem;
            }

            /* ---------- Footer (solid: the photo stops here) ---------- */
            .site-foot {
                flex: 0 0 auto;
                position: relative;
                z-index: 3;
                padding: .5rem 0;
                font-size: .73rem;
                line-height: 1.35;
                color: rgba(255, 255, 255, .76);
                border-top: 1px solid rgba(122, 160, 214, .3);
                background: linear-gradient(0deg, #22345a 0%, var(--navy) 100%);
                box-shadow: 0 -4px 20px rgba(8, 14, 24, .55);
            }
            .site-foot .foot-inner {
                display: flex;
                align-items: center;
                justify-content: space-between;
                flex-wrap: wrap;
                gap: .25rem;
            }
            .site-foot .foot-brand {
                color: var(--gold-light);
                font-weight: 700;
                letter-spacing: 1px;
            }

            /* ---------- Responsive: width ---------- */
            @media (max-width: 1199.98px) {
                .hero-logo { --logo-h: 74px; }
                .hero-campaign-text { font-size: 1rem; letter-spacing: 2.8px; }
                .hero-campaign-ddo { font-size: 1.5rem; }
            }
            @media (max-width: 991.98px) {
                .auth-photo::after {
                    background:
                        linear-gradient(180deg, rgba(16,26,43,.86) 0%, rgba(28,44,70,.74) 45%, rgba(16,26,43,.9) 100%);
                }
                .hero-col {
                    text-align: center;
                    padding: 0 0 1.2rem;
                }
                .hero-col::before { display: none; }
                .hero-campaign { justify-content: center; }
                .hero-logo { --logo-h: 62px; margin-left: auto; margin-right: auto; }
            }
            @media (max-width: 575.98px) {
                .site-nav { padding: .45rem 0; }
                .nav-seal { height: 28px; width: 28px; }
                .nav-divider { height: 22px; }
                .site-nav .brand img { height: 29px; }
                .site-nav .nav-links .nav-hide-xs { display: none; }
                .auth-scroll { padding: .9rem 0; }
                .hero-eyebrow { font-size: .58rem; letter-spacing: .9px; margin-bottom: .6rem; }
                .hero-eyebrow img { height: 19px; width: 19px; }
                .hero-logo { --logo-h: 46px; }
                .hero-campaign { margin-top: .55rem; gap: .05rem .4rem; }
                .hero-campaign-text { font-size: .8rem; letter-spacing: 2px; }
                .hero-campaign-ddo { font-size: 1.1rem; }
                .hero-col { padding-bottom: 1rem; }
                .login-card .card-body { padding: 1.25rem 1.15rem 1.05rem; }
                .site-foot .foot-inner { justify-content: center; text-align: center; }
            }

            /* ---------- Responsive: height (stay on one screen) ---------- */
            @media (max-height: 800px) {
                .auth-scroll { padding: .85rem 0; }
                .hero-col { padding-top: .7rem; padding-bottom: .7rem; }
                .hero-logo { --logo-h: 70px; }
                .login-card .card-body { padding: 1.3rem 1.6rem 1.15rem; }
                .login-subtitle { margin-bottom: .9rem; }
                .form-meta { margin-bottom: .75rem; }
            }
            @media (max-height: 680px) {
                .site-nav { padding: .4rem 0; }
                .nav-seal { height: 28px; width: 28px; }
                .site-nav .brand img { height: 31px; }
                .auth-scroll { padding: .6rem 0; }
                .hero-col { padding-top: .45rem; padding-bottom: .45rem; }
                /* .hero-eyebrow { margin-bottom: .25rem; } */
                .hero-logo { --logo-h: 26px; }
                .hero-campaign { margin-top: .25rem; }
                .hero-campaign-text { font-size: .92rem; letter-spacing: 2.4px; }
                .hero-campaign-ddo { font-size: 1.3rem; }
                .login-card .card-body { padding: 1.05rem 1.4rem .95rem; }
                .login-title { font-size: 1.15rem; }
                .login-subtitle { font-size: .77rem; margin-bottom: .7rem; }
                .login-card .form-group { margin-bottom: .6rem; }
                .btn-signin { height: 40px; }
                .signup-box { margin-top: .75rem; padding-top: .65rem; }
                .site-foot { padding: .4rem 0; font-size: .69rem; }
            }
        </style>
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
                                                <!-- <a href="<?= base_url(); ?>Pages/forgot_password" class="forgot-link">
                                                    <i class="mdi mdi-lock-reset"></i> Forgot Password?
                                                </a> -->
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
                        <span><span class="foot-brand">DOORS</span> &mdash; Davao de Oro Online Recruitment System &copy; <?= date('Y'); ?></span>
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
