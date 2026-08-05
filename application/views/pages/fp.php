<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="utf-8" />
        <title>Forgot Password | DOORS &mdash; Davao de Oro Online Recruitment System</title>
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

        <style>
            /* Account-type picker: only this page needs it, so it stays here
               instead of in the shared theme. */
            .type-label {
                display: block;
                font-weight: 700;
                color: #6f7d90;
                font-size: .69rem;
                text-transform: uppercase;
                letter-spacing: .7px;
                margin-bottom: .35rem;
            }
            .type-option { margin-bottom: .5rem; }
            .type-option input {
                position: absolute;
                opacity: 0;
                width: 0;
                height: 0;
            }
            .type-option .type-box {
                display: flex;
                align-items: center;
                gap: .7rem;
                width: 100%;
                margin: 0;
                padding: .55rem .75rem;
                border: 1px solid var(--field-line);
                border-radius: .5rem;
                background: var(--field-bg);
                cursor: pointer;
                transition: all .15s ease-in-out;
            }
            .type-option .type-box:hover { border-color: var(--navy-light); }
            .type-option input:checked + .type-box {
                border-color: var(--gold);
                background: rgba(246, 205, 82, .12);
                box-shadow: 0 0 0 1px var(--gold);
            }
            .type-option input:focus-visible + .type-box {
                box-shadow: 0 0 0 3px rgba(229, 168, 18, .35);
            }
            .type-option .type-icon {
                flex: 0 0 34px;
                height: 34px;
                border-radius: 50%;
                background: #eef2f8;
                color: #8b98ab;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 1.15rem;
                transition: all .15s ease-in-out;
            }
            .type-option input:checked + .type-box .type-icon {
                color: #241a00;
                background: linear-gradient(92deg, #f6cd52 0%, #e5a812 100%);
                box-shadow: 0 4px 12px -4px rgba(229, 168, 18, .8);
            }
            .type-option .type-title {
                font-weight: 700;
                font-size: .86rem;
                line-height: 1.2;
                color: var(--navy);
            }
            .type-option .type-desc {
                font-size: .72rem;
                line-height: 1.3;
                color: var(--muted);
            }
            .type-option .type-check {
                margin-left: auto;
                color: var(--gold);
                opacity: 0;
                font-size: 1.15rem;
            }
            .type-option input:checked + .type-box .type-check { opacity: 1; }

            .security-note {
                display: flex;
                gap: .45rem;
                font-size: .73rem;
                line-height: 1.4;
                color: var(--muted);
                background: #f4f7fb;
                border: 1px solid var(--line);
                border-radius: .5rem;
                padding: .5rem .65rem;
                margin-top: .85rem;
            }
            .security-note i { color: #1abc9c; font-size: .9rem; line-height: 1.35; }

            .back-link {
                display: inline-flex;
                align-items: center;
                font-weight: 600;
                font-size: .78rem;
                color: var(--muted) !important;
            }
            .back-link:hover { color: var(--navy) !important; text-decoration: none; }

            @media (max-height: 680px) {
                .type-option .type-desc { display: none; }
                .type-option .type-box { padding: .45rem .7rem; }
                .security-note { display: none; }
            }
        </style>
    </head>

    <body class="authentication-page">

        <?php
            // Keeps the chosen account type selected when the form comes back
            // with an error.
            $selectedType = $this->session->flashdata('fp_account_type');
            $selectedType = ($selectedType === 'school') ? 'school' : 'applicant';
        ?>

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

                            <!-- Reset card -->
                            <div class="col-md-9 col-lg-6 col-xl-5 offset-xl-1">
                                <div class="card login-card mb-0">
                                    <div class="card-body">

                                        <h4 class="login-title">Forgot Password</h4>
                                        <p class="login-subtitle">
                                            For your security we only send a reset to the email registered with your
                                            account. Tell us what account you have and we will email a temporary password.
                                        </p>

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

                                        <?= form_open('Pages/forgot_password', array('id' => 'resetPassword')); ?>

                                            <!-- <span class="type-label">I am a</span> -->

                                            <div class="type-option">
                                                <input type="radio" name="account_type" id="type_applicant" value="applicant" <?= $selectedType === 'applicant' ? 'checked' : ''; ?> required>
                                                <label class="type-box" for="type_applicant">
                                                    <span class="type-icon"><i class="mdi mdi-account-tie-outline"></i></span>
                                                    <span>
                                                        <span class="type-title d-block">Applicant</span>
                                                        <span class="type-desc">I applied for a position online</span>
                                                    </span>
                                                    <i class="mdi mdi-check-circle type-check"></i>
                                                </label>
                                            </div>

                                            <div class="type-option mb-3">
                                                <input type="radio" name="account_type" id="type_school" value="school" <?= $selectedType === 'school' ? 'checked' : ''; ?>>
                                                <label class="type-box" for="type_school">
                                                    <span class="type-icon"><i class="mdi mdi-school-outline"></i></span>
                                                    <span>
                                                        <span class="type-title d-block">School</span>
                                                        <span class="type-desc">I sign in using our School ID</span>
                                                    </span>
                                                    <i class="mdi mdi-check-circle type-check"></i>
                                                </label>
                                            </div>

                                            <div class="form-group">
                                                <label for="forgot_email" id="emailLabel">Applicant Email</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="mdi mdi-email-outline"></i></span>
                                                    </div>
                                                    <input class="form-control" type="email" name="email" id="forgot_email" placeholder="Enter the email you registered with" autocomplete="email" required>
                                                </div>
                                                <small class="form-text text-muted" id="emailHelp">
                                                    Use the email address you used when you signed up as an applicant.
                                                </small>
                                                <small class="form-text text-muted" id="emailStatus" style="display:none;">
                                                </small>
                                            </div>

                                            <div class="form-group mb-0">
                                                <button class="btn btn-block btn-signin waves-effect" type="submit" id="forgotSubmitBtn">
                                                    <i class="mdi mdi-email-send-outline mr-1"></i> Send Temporary Password
                                                </button>
                                            </div>

                                            <div class="security-note">
                                                <i class="mdi mdi-shield-check"></i>
                                                <span>We will never ask for your password. If you did not request a reset, simply ignore the email.</span>
                                            </div>

                                            <div class="text-center mt-3">
                                                <a href="<?= base_url(); ?>log_in" class="back-link">
                                                    <i class="mdi mdi-arrow-left mr-1"></i>Back to Sign In
                                                </a>
                                            </div>

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
        (function () {
            var emailLabel = document.getElementById('emailLabel');
            var emailHelp  = document.getElementById('emailHelp');
            var emailInput = document.getElementById('forgot_email');

            var copy = {
                applicant: {
                    label: 'Applicant Email',
                    help: 'Use the email address you used when you signed up as an applicant.',
                    placeholder: 'Enter the email you registered with'
                },
                school: {
                    label: 'School Email',
                    help: 'Use the school email address registered in the system.',
                    placeholder: 'Enter your registered school email'
                }
            };

            function applyType(type) {
                var text = copy[type] || copy.applicant;
                emailLabel.textContent = text.label;
                emailHelp.textContent = text.help;
                emailInput.setAttribute('placeholder', text.placeholder);
            }

            document.querySelectorAll('input[name="account_type"]').forEach(function (radio) {
                radio.addEventListener('change', function () {
                    if (this.checked) {
                        applyType(this.value);
                    }
                });
                if (radio.checked) {
                    applyType(radio.value);
                }
            });

            // Email existence check before enabling the submit button
            var forgotSubmitBtn = document.getElementById('forgotSubmitBtn');
            var emailStatus = document.getElementById('emailStatus');
            var checkTimeout = null;

            function setStatus(message, ok) {
                emailStatus.style.display = 'block';
                emailStatus.textContent = message;
                emailStatus.style.color = ok ? '#0f9d58' : '#d03f3f';
            }

            function clearStatus() {
                emailStatus.style.display = 'none';
                emailStatus.textContent = '';
            }

            function checkEmail() {
                var type = document.querySelector('input[name="account_type"]:checked').value;
                var email = emailInput.value.trim();

                if (email === '' || !/^[^@\s]+@[^@\s]+\.[^@\s]+$/.test(email)) {
                    forgotSubmitBtn.disabled = true;
                    setStatus('Please enter a valid email address.', false);
                    return;
                }

                // debounce
                if (checkTimeout) {
                    clearTimeout(checkTimeout);
                }
                checkTimeout = setTimeout(function () {
                    forgotSubmitBtn.disabled = true;
                    setStatus('Checking email...', true);

                    var data = 'account_type=' + encodeURIComponent(type) + '&email=' + encodeURIComponent(email);

                    fetch('<?= base_url('Pages/ajax_fp_check_email'); ?>', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
                        },
                        body: data,
                        credentials: 'same-origin'
                    }).then(function (resp) {
                        return resp.json();
                    }).then(function (json) {
                        if (json && json.exists) {
                            setStatus('Email found. You may send the temporary password.', true);
                            forgotSubmitBtn.disabled = false;
                        } else {
                            setStatus(json && json.message ? json.message : 'No matching account found for that email.', false);
                            forgotSubmitBtn.disabled = true;
                        }
                    }).catch(function (err) {
                        setStatus('Could not verify email right now. Try again later.', false);
                        forgotSubmitBtn.disabled = true;
                    });
                }, 450);
            }

            emailInput.addEventListener('input', function () {
                clearStatus();
                forgotSubmitBtn.disabled = true;
                checkEmail();
            });

            document.querySelectorAll('input[name="account_type"]').forEach(function (r) {
                r.addEventListener('change', function () {
                    // when type changes, re-run the check for the current email
                    clearStatus();
                    forgotSubmitBtn.disabled = true;
                    if (emailInput.value.trim() !== '') {
                        checkEmail();
                    }
                });
            });
        })();
        </script>

    </body>

</html>
