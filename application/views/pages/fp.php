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
            .login-card .card-body {
                padding: 1.5rem 1.75rem 1.75rem;
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
            .reset-subtitle {
                color: #98a6ad;
                font-size: .85rem;
            }
            /* Password field with show/hide eye */
            .pw-wrap { position: relative; }
            .pw-wrap .toggle-eye {
                position: absolute;
                right: 10px;
                top: 50%;
                transform: translateY(-50%);
                z-index: 5;
                border: 0;
                background: transparent;
                color: #98a6ad;
                padding: 4px;
                cursor: pointer;
                font-size: 1.1rem;
                line-height: 1;
            }
            .pw-wrap .form-control { padding-right: 38px; }
            /* Toggle / back links */
            .toggle-link {
                display: inline-flex;
                align-items: center;
                gap: .3rem;
                font-weight: 700;
                color: #f1556c !important;
                transition: color .15s ease-in-out;
            }
            .toggle-link:hover { color: #e02c47 !important; text-decoration: none; }
            .back-link { color: #98a6ad !important; font-weight: 500; }
            .back-link:hover { color: #6c757d !important; text-decoration: none; }
        </style>
    </head>

    <body class="authentication-page">

        <div class="account-pages">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-8 col-lg-6 col-xl-5">
                        <div class="card login-card">
                            <div class="card-header p-3 bg-primary text-center">
                                <h4 class="text-white mb-0 mt-0"><i class="mdi mdi-lock-reset mr-1"></i> Forgot Password</h4>
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

                                <?= form_open('Pages/forgot_password', array('id' => 'resetPassword')); ?>

                                    <input type="hidden" name="reset_mode" id="reset_mode" value="manual">

                                    <p class="reset-subtitle mb-3" id="resetHintManual">
                                        Enter your <strong>Username / ID</strong> and a new password.
                                        You may also enter your registered email to help verify your identity.
                                    </p>
                                    <p class="reset-subtitle mb-3" id="resetHintEmail" style="display:none;">
                                        Enter your registered email and we will send a temporary password to it.
                                    </p>

                                    <!-- Manual reset fields (shown by default) -->
                                    <div id="manualResetFields">
                                        <div class="form-group mb-3">
                                            <label for="forgot_identifier">Username / ID</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="mdi mdi-account-outline"></i></span>
                                                </div>
                                                <input type="text" name="identifier" id="forgot_identifier" class="form-control" placeholder="Enter your username or ID" autocomplete="off" required>
                                            </div>
                                        </div>

                                        <div class="form-group mb-3">
                                            <label for="forgot_new_password">New Password</label>
                                            <div class="input-group pw-wrap">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="mdi mdi-lock-outline"></i></span>
                                                </div>
                                                <input type="password" name="new_password" id="forgot_new_password" class="form-control" placeholder="At least 8 characters" minlength="8" autocomplete="new-password" required>
                                                <button type="button" class="toggle-eye" data-target="forgot_new_password" aria-label="Show password"><i class="mdi mdi-eye-outline"></i></button>
                                            </div>
                                        </div>

                                        <div class="form-group mb-3">
                                            <label for="forgot_confirm_password">Confirm New Password</label>
                                            <div class="input-group pw-wrap">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="mdi mdi-lock-check-outline"></i></span>
                                                </div>
                                                <input type="password" name="confirm_password" id="forgot_confirm_password" class="form-control" placeholder="Re-enter your new password" minlength="8" autocomplete="new-password" required>
                                                <button type="button" class="toggle-eye" data-target="forgot_confirm_password" aria-label="Show password"><i class="mdi mdi-eye-outline"></i></button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Email: optional in manual mode, required in email mode -->
                                    <div class="form-group mb-3">
                                        <label for="forgot_email">Email <span class="text-muted" id="emailOptionalHint" style="text-transform:none;">(optional for those no email)</span></label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="mdi mdi-email-outline"></i></span>
                                            </div>
                                            <input type="email" name="email" id="forgot_email" class="form-control" placeholder="Enter your email" autocomplete="email">
                                        </div>
                                    </div>

                                    <button type="submit" id="forgotSubmitBtn" class="btn btn-block btn-primary btn-signin waves-effect waves-light">
                                        <i class="mdi mdi-lock-reset mr-1"></i> Update Password
                                    </button>

                                    <div class="text-center mt-3">
                                        <a href="#" id="toggleManualReset" class="toggle-link">
                                            <i class="mdi mdi-email-send-outline"></i> Send a temporary password by email instead
                                        </a>
                                    </div>

                                    <div class="text-center mt-2">
                                        <a href="<?= base_url(); ?>log_in" class="back-link"><i class="mdi mdi-arrow-left mr-1"></i>Back to Login</a>
                                    </div>

                                </form>

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

        <!-- Vendor js -->
        <script src="<?= base_url(); ?>assets/js/vendor.min.js"></script>

        <!-- App js -->
        <script src="<?= base_url(); ?>assets/js/app.min.js"></script>

        <script>
        (function () {
            var toggleLink       = document.getElementById('toggleManualReset');
            var manualFields     = document.getElementById('manualResetFields');
            var modeInput        = document.getElementById('reset_mode');
            var submitBtn        = document.getElementById('forgotSubmitBtn');
            var hintEmail        = document.getElementById('resetHintEmail');
            var hintManual       = document.getElementById('resetHintManual');
            var identifierInput  = document.getElementById('forgot_identifier');
            var newPasswordInput = document.getElementById('forgot_new_password');
            var confirmInput     = document.getElementById('forgot_confirm_password');
            var emailInput       = document.getElementById('forgot_email');
            var emailHint        = document.getElementById('emailOptionalHint');

            function setManualMode(isManual) {
                if (isManual) {
                    manualFields.style.display = 'block';
                    modeInput.value = 'manual';
                    submitBtn.innerHTML = '<i class="mdi mdi-lock-reset mr-1"></i> Update Password';
                    toggleLink.innerHTML = '<i class="mdi mdi-email-send-outline"></i> Send a temporary password by email instead';
                    hintManual.style.display = 'block';
                    hintEmail.style.display = 'none';
                    if (emailHint) emailHint.style.display = '';
                    emailInput.removeAttribute('required');
                    identifierInput.setAttribute('required', 'required');
                    newPasswordInput.setAttribute('required', 'required');
                    confirmInput.setAttribute('required', 'required');
                } else {
                    manualFields.style.display = 'none';
                    modeInput.value = 'email';
                    submitBtn.innerHTML = '<i class="mdi mdi-email-send-outline mr-1"></i> Request a New Password';
                    toggleLink.innerHTML = '<i class="mdi mdi-form-textbox-password"></i> Set a new password manually instead';
                    hintManual.style.display = 'none';
                    hintEmail.style.display = 'block';
                    if (emailHint) emailHint.style.display = 'none';
                    emailInput.setAttribute('required', 'required');
                    identifierInput.removeAttribute('required');
                    newPasswordInput.removeAttribute('required');
                    confirmInput.removeAttribute('required');
                }
            }

            if (toggleLink) {
                toggleLink.addEventListener('click', function (e) {
                    e.preventDefault();
                    setManualMode(modeInput.value !== 'manual');
                });
            }

            // Default: manual mode
            setManualMode(true);

            // Show / hide password eye toggles
            document.querySelectorAll('#resetPassword .toggle-eye').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    var input = document.getElementById(this.getAttribute('data-target'));
                    if (!input) return;
                    var icon = this.querySelector('i');
                    if (input.type === 'password') {
                        input.type = 'text';
                        if (icon) { icon.classList.remove('mdi-eye-outline'); icon.classList.add('mdi-eye-off-outline'); }
                    } else {
                        input.type = 'password';
                        if (icon) { icon.classList.remove('mdi-eye-off-outline'); icon.classList.add('mdi-eye-outline'); }
                    }
                });
            });
        })();
        </script>

    </body>

</html>
