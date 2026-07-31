<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title><?= isset($page_title) ? html_escape($page_title) . ' | ' : ''; ?>EduVision MIS</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description"
        content="Online recruitment and human resource information system &mdash; create an account, apply for vacant positions and track your application." />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="theme-color" content="#0f2557" />
    <link rel="shortcut icon" href="<?= base_url(); ?>assets/images/hris.ico">
    <link href="<?= base_url(); ?>assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" id="bootstrap-stylesheet" />
    <link href="<?= base_url(); ?>assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <link href="<?= base_url(); ?>assets/css/app.min.css" rel="stylesheet" type="text/css" id="app-stylesheet" />
    <link href="<?= base_url(); ?>assets/libs/custombox/custombox.min.css" rel="stylesheet" type="text/css">

    <style>
        /* Public topbar. .navbar-custom is position:fixed and 70px tall in the
           theme, and .content-page offsets itself by exactly that much — so the
           height stays 70px here and only the styling changes. */
        .public-topbar {
            background: linear-gradient(135deg, #0f2557 0%, #16336f 55%, #1b3d85 100%) !important;
            padding: 0 !important;
            box-shadow: 0 2px 10px rgba(15, 23, 42, .18) !important;
        }

        .public-topbar-inner {
            max-width: 1120px;
            height: 70px;
            margin: 0 auto;
            padding: 0 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
        }

        .public-brand {
            display: inline-flex;
            align-items: center;
            line-height: 0;
        }

        .public-brand img {
            height: 44px;
            width: auto;
            display: block;
        }

        .public-nav {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .public-nav a {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            text-decoration: none !important;
            white-space: nowrap;
            transition: background .15s, color .15s;
        }

        .public-nav-link {
            color: rgba(255, 255, 255, .85);
            font-size: 13px;
            font-weight: 600;
            letter-spacing: .3px;
            padding: 8px 14px;
            border-radius: 999px;
        }

        .public-nav-link:hover,
        .public-nav-link:focus {
            color: #fff;
            background: rgba(255, 255, 255, .12);
        }

        .public-nav-btn {
            color: #0f2557;
            background: #fff;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: .6px;
            text-transform: uppercase;
            padding: 9px 20px;
            border-radius: 999px;
            box-shadow: 0 2px 6px rgba(15, 23, 42, .2);
        }

        .public-nav-btn:hover,
        .public-nav-btn:focus {
            background: #dbe6ff;
            color: #0f2557;
        }

        @media (max-width: 575px) {
            .public-topbar-inner {
                padding: 0 12px;
            }

            .public-brand img {
                height: 30px;
            }

            .public-nav-link {
                display: none;
            }

            .public-nav-btn {
                padding: 8px 15px;
                font-size: 11px;
            }
        }
    </style>

</head>

<body>

    <!-- Begin page -->
    <div id="wrapper">


        <!-- Topbar Start -->
        <div class="navbar-custom public-topbar">
            <div class="public-topbar-inner">

                <a class="public-brand" href="<?= base_url(); ?>">
                    <img src="<?= base_url(); ?>assets/images/logo.png"
                        alt="DOORS &mdash; Davao De Oro Online Recruitment System">
                </a>

                <nav class="public-nav">
                    <!-- base_url() redirects to log_in, so a "Home" link here would
                         just duplicate Sign In — the logo already covers it. -->
                    <a class="public-nav-link" href="<?= base_url(); ?>new_applicant">Register</a>
                    <a class="public-nav-btn" href="<?= base_url(); ?>log_in">Sign In</a>
                </nav>

            </div>
        </div>
        <!-- end Topbar --> <!-- ========== Left Sidebar Start ========== -->
