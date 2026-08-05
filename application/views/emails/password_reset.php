<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Password reset notice.
 *
 * @var string $division       e.g. "DepEd Davao de Oro"
 * @var string $account_label  "Applicant" or "School"
 * @var string $recipient_name greeting name, already trimmed
 * @var string $username       the username to sign in with
 * @var string $password       temporary password, plain text
 * @var string $login_url      absolute URL of the sign-in page
 * @var string $requested_at   human readable request timestamp
 * @var string $support_email  where to report a request they did not make
 */
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Password Reset</title>
</head>
<body style="margin:0; padding:0; background:#eef2f7; font-family:'Segoe UI',Roboto,Arial,Helvetica,sans-serif; color:#1f2937;">

    <div style="display:none; max-height:0; overflow:hidden; opacity:0;">
        Your temporary password for <?= html_escape($division); ?> is inside. It works once, until you change it.
    </div>

    <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="background:#eef2f7;">
        <tr>
            <td align="center" style="padding:28px 12px;">

                <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="max-width:620px; background:#ffffff; border-radius:14px; overflow:hidden; box-shadow:0 8px 24px rgba(15,23,42,.10);">

                    <!-- Header -->
                    <tr>
                        <td style="background:#0d6efd; padding:24px 28px; color:#ffffff;">
                            <div style="font-size:19px; font-weight:700; letter-spacing:.2px;"><?= html_escape($division); ?></div>
                            <div style="font-size:13px; opacity:.92; margin-top:5px;">Password Reset &bull; <?= html_escape($account_label); ?> Account</div>
                        </td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td style="padding:28px;">

                            <p style="margin:0 0 14px; font-size:16px; font-weight:700;">Dear <?= html_escape($recipient_name); ?>,</p>

                            <p style="margin:0 0 18px; font-size:15px; line-height:1.65; color:#374151;">
                                We received a request to reset the password of your <strong><?= html_escape(strtolower($account_label)); ?> account</strong>
                                on <?= html_escape($requested_at); ?>. Your password has been replaced with the temporary one below.
                            </p>

                            <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="margin:0 0 20px; border:1px solid #e5e7eb; border-radius:12px; background:#f9fafb;">
                                <tr>
                                    <td style="padding:16px 18px; border-bottom:1px solid #e5e7eb;">
                                        <div style="font-size:12px; text-transform:uppercase; letter-spacing:.6px; color:#6b7280;">Username</div>
                                        <div style="font-size:17px; font-weight:700; color:#111827; margin-top:4px; word-break:break-all;"><?= html_escape($username); ?></div>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:16px 18px;">
                                        <div style="font-size:12px; text-transform:uppercase; letter-spacing:.6px; color:#6b7280;">Temporary Password</div>
                                        <div style="font-family:'Courier New',Courier,monospace; font-size:22px; font-weight:700; color:#dc2626; letter-spacing:1.5px; margin-top:4px;"><?= html_escape($password); ?></div>
                                    </td>
                                </tr>
                            </table>

                            <table role="presentation" cellpadding="0" cellspacing="0" border="0" style="margin:0 0 22px;">
                                <tr>
                                    <td style="background:#0d6efd; border-radius:8px;">
                                        <a href="<?= html_escape($login_url); ?>" style="display:inline-block; padding:13px 26px; font-size:15px; font-weight:700; color:#ffffff; text-decoration:none;">Sign in now</a>
                                    </td>
                                </tr>
                            </table>

                            <div style="border-left:4px solid #f59e0b; background:#fffbeb; padding:14px 16px; border-radius:0 8px 8px 0; margin:0 0 20px;">
                                <div style="font-size:14px; font-weight:700; color:#92400e; margin-bottom:6px;">Please do this right away</div>
                                <ul style="margin:0; padding-left:18px; font-size:14px; line-height:1.7; color:#78350f;">
                                    <li>Sign in with the temporary password, then change it from your profile.</li>
                                    <li>Use a password of at least 8 characters that you do not use anywhere else.</li>
                                    <li>Never share this password. Our staff will never ask you for it.</li>
                                </ul>
                            </div>

                            <p style="margin:0 0 6px; font-size:14px; line-height:1.65; color:#374151;">
                                <strong>Did not request this?</strong> Your old password no longer works, so please reset it again yourself using
                                the <em>Forgot Password</em> link, and report it to
                                <a href="mailto:<?= html_escape($support_email); ?>" style="color:#0d6efd;"><?= html_escape($support_email); ?></a>.
                            </p>

                            <p style="margin:24px 0 0; font-size:14px; line-height:1.6; color:#111827;">
                                Thank you,<br>
                                <strong><?= html_escape($division); ?></strong><br>
                                <span style="color:#6b7280;">Management Information System</span>
                            </p>

                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="padding:16px 28px 22px; background:#f9fafb; color:#6b7280; font-size:12px; line-height:1.6; border-top:1px solid #eef0f3;">
                            This message was generated automatically for <?= html_escape($account_label); ?> account holders. Please do not reply to this email.
                        </td>
                    </tr>

                </table>

            </td>
        </tr>
    </table>

</body>
</html>
