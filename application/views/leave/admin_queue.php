<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">
            <div style="margin-left: 250px; padding-bottom: 90px;">

                <div class="page-title-box">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h4 class="page-title mb-0">Leave Processing Queue</h4>
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="javascript:void(0);">Leave</a></li>
                                <li class="breadcrumb-item active">My Queue</li>
                            </ol>
                        </div>
                    </div>
                </div>

                <?php if ($this->session->flashdata('success')): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?= html_escape((string)$this->session->flashdata('success')); ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                <?php endif; ?>

                <?php if ($this->session->flashdata('error')): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?= html_escape((string)$this->session->flashdata('error')); ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                <?php endif; ?>

                <?php
                    $logged_username = strtolower(trim((string)$this->session->userdata('username')));
                    $logged_position = isset($logged_position)
                        ? (string)$logged_position
                        : (string)$this->session->userdata('position');
                    $logged_position_lower = strtolower(trim($logged_position));
                ?>

                <div class="card">
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-7">
                                <h4 class="card-title mb-1">My Leave Queue</h4>
                                <p class="card-title-desc mb-0">
                                    Logged position:
                                    <strong><?= html_escape($logged_position); ?></strong>
                                </p>
                            </div>
                            <div class="col-md-5">
                                <form method="get" action="<?= base_url('leave_admin/my_queue'); ?>">
                                    <div class="input-group">
                                        <input type="text"
                                               name="q"
                                               value="<?= html_escape($q ?? ''); ?>"
                                               class="form-control"
                                               placeholder="Search name, ID, leave type, status...">
                                        <div class="input-group-append">
                                            <button type="submit" class="btn btn-primary">Search</button>
                                            <a href="<?= base_url('leave_admin/my_queue'); ?>" class="btn btn-secondary">Reset</a>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div class="mb-2">
                            <small class="text-muted">
                                Showing <?= ($total_rows > 0) ? ($offset + 1) : 0; ?>
                                to <?= min($offset + $limit, $total_rows); ?>
                                of <?= $total_rows; ?> records
                            </small>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-hover table-sm mb-0">
                                <thead class="thead-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Employee</th>
                                        <th>Department</th>
                                        <th>Leave Type</th>
                                        <th>From</th>
                                        <th>To</th>
                                        <th>Days</th>
                                        <th>Date Applied</th>
                                        <th>Status</th>
                                        <th>Attachment</th>
                                        <th style="width: 260px;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        $rows = array();
                                        if (!empty($applications)) {
                                            $rows = $applications;
                                        } elseif (!empty($queue)) {
                                            $rows = $queue;
                                        }

                                        if (!function_exists('queue_value')) {
                                            function queue_value($row, $key, $default = '')
                                            {
                                                if (is_array($row)) {
                                                    return isset($row[$key]) ? $row[$key] : $default;
                                                }

                                                if (is_object($row)) {
                                                    return isset($row->$key) ? $row->$key : $default;
                                                }

                                                return $default;
                                            }
                                        }
                                    ?>

                                    <?php if (!empty($rows)): ?>
                                        <?php foreach ($rows as $row): ?>
                                            <?php
                                                $id             = (int)queue_value($row, 'id', 0);
                                                $staff_idnumber = (string)queue_value($row, 'staff_idnumber', '');

                                                $first_name  = (string)queue_value($row, 'FirstName', queue_value($row, 'firstname', ''));
                                                $middle_name = (string)queue_value($row, 'MiddleName', queue_value($row, 'middlename', ''));
                                                $last_name   = (string)queue_value($row, 'LastName', queue_value($row, 'lastname', ''));

                                                $employee_name = trim(
                                                    $last_name .
                                                    (($last_name !== '' || $first_name !== '') ? ', ' : '') .
                                                    $first_name .
                                                    ($middle_name !== '' ? ' ' . $middle_name : '')
                                                );

                                                if ($employee_name === '') {
                                                    $employee_name = (string)queue_value($row, 'employee_name', $staff_idnumber);
                                                }

                                                $department       = (string)queue_value($row, 'Department', queue_value($row, 'department', ''));
                                                $leave_type_code  = (string)queue_value($row, 'leave_type_code', queue_value($row, 'leave_type', ''));
                                                $date_from        = (string)queue_value($row, 'date_from', '');
                                                $date_to          = (string)queue_value($row, 'date_to', '');
                                                $working_days     = (string)queue_value($row, 'working_days', '');
                                                $status           = strtoupper(trim((string)queue_value($row, 'status', '')));
                                                $created_at       = (string)queue_value($row, 'created_at', '');
                                                $attachment_path  = (string)queue_value($row, 'attachment_path', '');
                                                $attachment_count = (int)queue_value($row, 'attachment_count', 0);

                                                $certify_by   = strtolower(trim((string)queue_value($row, 'certify_by_position', '')));
                                                $recommend_by = strtolower(trim((string)queue_value($row, 'recommend_by_position', '')));
                                                $approve_by   = strtolower(trim((string)queue_value($row, 'approve_by_position', '')));

                                                $leave_type_upper = strtoupper(trim($leave_type_code));

                                                $badge_class = 'badge-info';
                                                if ($status === 'PENDING' || $status === 'DRAFT') {
                                                    $badge_class = 'badge-warning';
                                                } elseif ($status === 'CERTIFIED') {
                                                    $badge_class = 'badge-primary';
                                                } elseif ($status === 'RECOMMENDED') {
                                                    $badge_class = 'badge-info';
                                                } elseif ($status === 'APPROVED') {
                                                    $badge_class = 'badge-success';
                                                } elseif ($status === 'REJECTED' || $status === 'DENIED') {
                                                    $badge_class = 'badge-danger';
                                                }

                                                $can_certify   = false;
                                                $can_recommend = false;
                                                $can_approve   = false;

                                                if (
                                                    in_array($status, array('DRAFT', 'PENDING'), true) &&
                                                    (
                                                        ($logged_username === 'aov' && $certify_by === 'admin officer v') ||
                                                        ($logged_position_lower === 'admin officer v' && $certify_by === 'admin officer v') ||
                                                        (
                                                            in_array($logged_username, array('aoiv', 'hr'), true) &&
                                                            $certify_by !== 'admin officer v'
                                                        ) ||
                                                        (
                                                            in_array($logged_position_lower, array('human resource admin', 'admin officer iv'), true) &&
                                                            $certify_by !== 'admin officer v'
                                                        ) ||
                                                        ($certify_by !== '' && $certify_by === $logged_position_lower)
                                                    )
                                                ) {
                                                    $can_certify = true;
                                                }

                                                if (
                                                    $status === 'CERTIFIED' &&
                                                    (
                                                        ((is_numeric($logged_username) || $logged_position_lower === 'school') && $recommend_by === 'school head') ||
                                                        ($logged_position_lower === 'cid' && $recommend_by === 'cid chief') ||
                                                        ($logged_position_lower === 'sgod' && $recommend_by === 'sgod chief') ||
                                                        ($recommend_by !== '' && $recommend_by === $logged_position_lower)
                                                    )
                                                ) {
                                                    $can_recommend = true;
                                                }

                                                if (
                                                    $status === 'RECOMMENDED' &&
                                                    (
                                                        ($logged_position_lower === 'asds' && $approve_by === 'asds') ||
                                                        ($logged_position_lower === 'sds' && $approve_by === 'sds') ||
                                                        ($approve_by !== '' && $approve_by === $logged_position_lower)
                                                    )
                                                ) {
                                                    $can_approve = true;
                                                }
                                            ?>
                                            <tr>
                                                <td><?= $id; ?></td>
                                                <td>
                                                    <?= html_escape($employee_name); ?><br>
                                                    <small class="text-muted"><?= html_escape($staff_idnumber); ?></small>
                                                </td>
                                                <td><?= html_escape($department); ?></td>
                                                <td><?= html_escape($leave_type_code); ?></td>
                                                <td><?= html_escape($date_from); ?></td>
                                                <td><?= html_escape($date_to); ?></td>
                                                <td><?= html_escape($working_days); ?></td>
                                                <td><?= ($created_at !== '') ? date('Y-m-d', strtotime($created_at)) : '-'; ?></td>
                                                <td>
                                                    <span class="badge <?= $badge_class; ?>">
                                                        <?= html_escape($status); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <?php if ($attachment_path !== ''): ?>
                                                        <a href="<?= base_url($attachment_path); ?>" target="_blank" class="btn btn-sm btn-outline-info">
                                                            View
                                                        </a>
                                                    <?php elseif ($attachment_count > 0): ?>
                                                        <span class="badge badge-secondary"><?= $attachment_count; ?> file(s)</span>
                                                    <?php else: ?>
                                                        <span class="text-muted">-</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <a href="<?= base_url('leave_admin/view_application/' . $id); ?>" class="btn btn-sm btn-info mb-1">
                                                        View
                                                    </a>

                                                    <a href="<?= base_url('leave_admin/print_preview/' . $id); ?>" class="btn btn-sm btn-secondary mb-1" target="_blank">
                                                        Print
                                                    </a>

                                                    <?php if ($can_certify): ?>
                                                        <a href="<?= base_url('leave_admin/certify/' . $id); ?>"
                                                           class="btn btn-sm btn-success mb-1"
                                                           onclick="return confirm('Certify this leave application?');">
                                                            Certify
                                                        </a>
                                                    <?php endif; ?>

                                                    <?php if ($can_recommend): ?>
                                                        <a href="<?= base_url('leave_admin/recommend/' . $id); ?>"
                                                           class="btn btn-sm btn-warning mb-1"
                                                           onclick="return confirm('Recommend this leave application?');">
                                                            Recommend
                                                        </a>
                                                    <?php endif; ?>

                                                    <?php if ($can_approve): ?>
                                                        <a href="<?= base_url('leave_admin/approve/' . $id); ?>"
                                                           class="btn btn-sm btn-success mb-1"
                                                           onclick="return confirm('Approve this leave application?');">
                                                            Approve
                                                        </a>
                                                    <?php endif; ?>

                                                    <?php if (
                                                        $logged_position_lower === 'sds' &&
                                                        $status === 'APPROVED' &&
                                                        $leave_type_upper === 'FL'
                                                    ): ?>
                                                        <a href="<?= base_url('leave_admin/recall/' . $id); ?>"
                                                           class="btn btn-sm btn-danger mb-1"
                                                           onclick="return confirm('Recall this forced leave? Credits will be restored.');">
                                                            Recall
                                                        </a>
                                                    <?php endif; ?>

                                                    <?php if (!in_array($status, array('APPROVED', 'REJECTED', 'DENIED'), true)): ?>
                                                        <form method="post"
                                                              action="<?= base_url('leave_admin/reject/' . $id); ?>"
                                                              class="mt-1"
                                                              onsubmit="return confirm('Reject/Deny this leave application?');">
                                                            <div class="input-group input-group-sm">
                                                                <input type="text"
                                                                       name="rejection_reason"
                                                                       class="form-control"
                                                                       placeholder="Reason required"
                                                                       required>
                                                                <div class="input-group-append">
                                                                    <button type="submit" class="btn btn-danger">
                                                                        Reject
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="11" class="text-center text-muted">
                                                No pending leave applications in your queue.
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>

                        <?php if (!empty($pagination)): ?>
                            <div class="mt-3 d-flex justify-content-end">
                                <?= $pagination; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
