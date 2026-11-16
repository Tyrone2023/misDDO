<!-- ============================================================== -->
<!-- Edit account: position, name, username (carried to linked records), password -->
<!-- ============================================================== -->
<?php
$linkedTotal = 0;
foreach ($linked as $l) {
    $linkedTotal += (int) $l['rows'];
}
?>
<div class="content-page">
    <div class="content">
        <div class="container-fluid">

            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <a href="<?= base_url(); ?>hrusers<?= $applicant ? '?tab=applicants' : ''; ?>" class="btn btn-light waves-effect"><i class="mdi mdi-arrow-left"></i> Back to Manage Users</a>
                        <div class="clearfix"></div>
                    </div>
                </div>
            </div>

            <?php if ($this->session->flashdata('success')) : ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <?= $this->session->flashdata('success'); ?>
                </div>
            <?php endif; ?>
            <?php if ($this->session->flashdata('danger')) : ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <?= $this->session->flashdata('danger'); ?>
                </div>
            <?php endif; ?>

            <div class="row">
                <div class="col-lg-7">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="header-title mb-1"><?= html_escape($title); ?></h4>
                            <p class="text-muted mb-3">
                                <?= html_escape(trim($acct->fname . ' ' . $acct->lname)); ?>
                                &middot; <?= html_escape($acct->position); ?>
                                <?php if ($is_self) { ?><span class="badge badge-info ml-1">Your account</span><?php } ?>
                            </p>

                            <form method="post" action="<?= base_url(); ?>hrusers/edit/<?= (int) $acct->id; ?>" autocomplete="off" id="acctForm">
                                <input type="hidden" name="token" value="<?= html_escape($token); ?>">

                                <h5 class="mt-0">Account</h5>
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="username" class="col-form-label">Username</label>
                                        <input type="text" id="username" name="username" required maxlength="45"
                                               value="<?= html_escape($acct->username); ?>"
                                               data-original="<?= html_escape($acct->username); ?>"
                                               <?= $acct->position === 'user' ? 'readonly' : ''; ?>
                                               class="form-control">
                                        <?php if ($acct->position === 'user') { ?>
                                            <small class="form-text text-muted">Employee usernames are their ID numbers and are changed from the personnel record.</small>
                                        <?php } ?>
                                    </div>

                                    <?php if (!$applicant) { ?>
                                    <div class="form-group col-md-6">
                                        <label for="acct_position" class="col-form-label">Position</label>
                                        <select id="acct_position" name="acct_position" class="form-control" required <?= $is_self ? 'disabled' : ''; ?>>
                                            <?php foreach ($positions as $p) { ?>
                                                <option value="<?= html_escape($p); ?>" <?= $p === $acct->position ? 'selected' : ''; ?>><?= html_escape($p); ?></option>
                                            <?php } ?>
                                        </select>
                                        <?php if ($is_self) { ?>
                                            <input type="hidden" name="acct_position" value="<?= html_escape($acct->position); ?>">
                                            <small class="form-text text-muted">You cannot change your own position.</small>
                                        <?php } ?>
                                    </div>
                                    <?php } ?>
                                </div>

                                <div id="renameNotice" class="alert alert-warning" style="display:none;">
                                    <b>Changing the username.</b>
                                    <?php if ($linkedTotal > 0) { ?>
                                        The <?= number_format($linkedTotal); ?> record(s) listed on the right that carry
                                        &ldquo;<?= html_escape($acct->username); ?>&rdquo; will be moved to the new username,
                                        so this person's history and tracking stay with the account.
                                    <?php } else { ?>
                                        No other records carry the old username.
                                    <?php } ?>
                                    The person must sign in with the new username from now on.
                                    <?php if ($code_ref) { ?>
                                        <br><b>Caution:</b> the system grants special access to the username
                                        &ldquo;<?= html_escape($acct->username); ?>&rdquo; by name. Renaming it removes that access.
                                    <?php } ?>
                                </div>

                                <div class="form-row">
                                    <div class="form-group col-md-4">
                                        <label for="fname" class="col-form-label">First name</label>
                                        <input type="text" id="fname" name="fname" maxlength="200" value="<?= html_escape($acct->fname); ?>" class="form-control">
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="mname" class="col-form-label">Middle name</label>
                                        <input type="text" id="mname" name="mname" maxlength="200" value="<?= html_escape($acct->mname); ?>" class="form-control">
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="lname" class="col-form-label">Last name</label>
                                        <input type="text" id="lname" name="lname" maxlength="200" value="<?= html_escape($acct->lname); ?>" class="form-control">
                                    </div>
                                </div>

                                <h5 class="mt-2">Change password</h5>
                                <p class="text-muted small mb-2">Leave both fields blank to keep the current password.</p>
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="password" class="col-form-label">New password</label>
                                        <input type="password" id="password" name="password" minlength="6" autocomplete="new-password" class="form-control">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="password_confirm" class="col-form-label">Confirm new password</label>
                                        <input type="password" id="password_confirm" name="password_confirm" minlength="6" autocomplete="new-password" class="form-control">
                                    </div>
                                </div>
                                <div class="custom-control custom-checkbox mb-3">
                                    <input type="checkbox" class="custom-control-input" id="showPw">
                                    <label class="custom-control-label" for="showPw">Show password</label>
                                </div>

                                <button type="submit" class="btn btn-primary">Save changes</button>
                                <a href="<?= base_url(); ?>hrusers<?= $applicant ? '?tab=applicants' : ''; ?>" class="btn btn-light ml-1">Cancel</a>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-lg-5">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="mt-0">Records linked to this username</h5>
                            <p class="text-muted small">
                                These records store &ldquo;<?= html_escape($acct->username); ?>&rdquo; as text and are
                                renamed with the account. Ratings, scores and the audit trail refer to the account
                                by its ID and follow it automatically.
                            </p>
                            <?php
                            $shown = 0;
                            foreach ($linked as $l) {
                                if ($l['rows'] === 0) continue;
                                $shown++;
                            }
                            ?>
                            <?php if ($shown === 0) { ?>
                                <p class="mb-0">No linked records.</p>
                            <?php } else { ?>
                                <table class="table table-sm mb-0">
                                    <tbody>
                                    <?php foreach ($linked as $l) { if ($l['rows'] === 0) continue; ?>
                                        <tr>
                                            <td><?= html_escape($l['label']); ?></td>
                                            <td class="text-right">
                                                <?php if ($l['rows'] === null) { ?>
                                                    <span class="text-muted" title="This username is numeric and this table also stores numeric IDs, so it is left unchanged.">not changed</span>
                                                <?php } else { ?>
                                                    <?= number_format($l['rows']); ?>
                                                <?php } ?>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                    </tbody>
                                </table>
                            <?php } ?>
                        </div>
                    </div>

                    <?php if (!empty($history)) { ?>
                    <div class="card">
                        <div class="card-body">
                            <h5 class="mt-0">Previous usernames</h5>
                            <table class="table table-sm mb-0">
                                <thead><tr><th>When</th><th>From</th><th>To</th><th>By</th></tr></thead>
                                <tbody>
                                <?php foreach ($history as $h) { ?>
                                    <tr>
                                        <td class="text-nowrap"><?= html_escape(date('M j, Y g:i A', strtotime($h->created_at))); ?></td>
                                        <td><?= html_escape($h->old_value); ?></td>
                                        <td><?= html_escape($h->new_value); ?></td>
                                        <td><?= html_escape($h->responsible ?: $h->username); ?></td>
                                    </tr>
                                <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <?php } ?>
                </div>
            </div>

        </div>
    </div>

<script>
(function () {
    var u = document.getElementById('username');
    var note = document.getElementById('renameNotice');
    var form = document.getElementById('acctForm');
    function sync() { note.style.display = (u.value.trim() !== u.getAttribute('data-original')) ? '' : 'none'; }
    u.addEventListener('input', sync);
    sync();

    document.getElementById('showPw').addEventListener('change', function () {
        var t = this.checked ? 'text' : 'password';
        document.getElementById('password').type = t;
        document.getElementById('password_confirm').type = t;
    });

    form.addEventListener('submit', function (e) {
        var p = document.getElementById('password').value, c = document.getElementById('password_confirm').value;
        if (p !== c) { e.preventDefault(); alert('The new password and its confirmation do not match.'); return; }
        var from = u.getAttribute('data-original'), to = u.value.trim();
        if (to !== from && !confirm('Change the username from "' + from + '" to "' + to + '"?\n\nLinked records will be moved to the new username.')) {
            e.preventDefault();
        }
    });
})();
</script>
