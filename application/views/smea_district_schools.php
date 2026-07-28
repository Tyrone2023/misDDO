<?php
// rawurlencode, not urlencode: district names contain spaces and CodeIgniter's permitted_uri_chars
// allows %20 but rejects the "+" that urlencode emits.
$slug = rawurlencode($district);
$rate = $counts['all'] > 0 ? round(($counts['submitted'] / $counts['all']) * 100, 1) : 0;

$labels = array(
    'submitted' => 'Schools that submitted',
    'pending'   => 'Schools that have not submitted',
    'all'       => 'All schools in this district',
);
$blurb = isset($labels[$filter]) ? $labels[$filter] : $labels['submitted'];
?>

            <!-- ============================================================== -->
            <!-- Start Page Content here -->
            <!-- ============================================================== -->

            <div class="content-page">
                <div class="content">

                    <div class="container-fluid">

                        <!-- start page header -->
                        <div class="row">
                            <div class="col-12">
                                <div class="sm-hero">
                                    <div class="sm-hero-text">
                                        <span class="sm-hero-eyebrow"><i class="mdi mdi-file-document-check-outline"></i> Submitted SMEA</span>
                                        <h3 class="sm-hero-title"><i class="mdi mdi-school-outline"></i> <?= html_escape($district); ?></h3>
                                        <p class="sm-hero-sub">
                                            Fiscal Year <strong><?= html_escape($fy); ?></strong>
                                            <span class="sm-dotsep">&bull;</span>
                                            <?= html_escape($blurb); ?>
                                        </p>
                                    </div>
                                    <div class="sm-hero-stats">
                                        <div class="sm-stat">
                                            <span class="sm-stat-value"><?= number_format($counts['submitted']); ?></span>
                                            <span class="sm-stat-label">Submitted</span>
                                        </div>
                                        <div class="sm-stat">
                                            <span class="sm-stat-value"><?= number_format($counts['pending']); ?></span>
                                            <span class="sm-stat-label">Not Submitted</span>
                                        </div>
                                        <div class="sm-stat">
                                            <span class="sm-stat-value"><?= $rate; ?>%</span>
                                            <span class="sm-stat-label">Rate</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- end page header -->

                        <div class="row">
                            <div class="col-12">
                                <div class="sm-card">

                                    <div class="sm-card-head">
                                        <div>
                                            <h4 class="sm-card-title">School List</h4>
                                            <p class="sm-card-sub">
                                                <?= number_format(count($data)); ?>
                                                <?= count($data) === 1 ? 'school' : 'schools'; ?> listed
                                                out of <?= number_format($counts['all']); ?> in <?= html_escape($district); ?>.
                                            </p>
                                        </div>
                                        <div class="sm-card-actions">
                                            <a class="sm-btn" href="<?= base_url(); ?>Page/smea_admin">
                                                <i class="mdi mdi-arrow-left"></i> Back to Districts
                                            </a>
                                        </div>
                                    </div>

                                    <div class="sm-tabs">
                                        <a class="sm-tab <?= $filter === 'submitted' ? 'is-active' : ''; ?>"
                                           href="<?= base_url(); ?>Page/smea_admin_schools/<?= $slug; ?>/submitted">
                                            <i class="mdi mdi-check-circle-outline"></i> Submitted
                                            <span class="sm-tab-count"><?= number_format($counts['submitted']); ?></span>
                                        </a>
                                        <a class="sm-tab <?= $filter === 'pending' ? 'is-active' : ''; ?>"
                                           href="<?= base_url(); ?>Page/smea_admin_schools/<?= $slug; ?>/pending">
                                            <i class="mdi mdi-clock-outline"></i> Not Submitted
                                            <span class="sm-tab-count"><?= number_format($counts['pending']); ?></span>
                                        </a>
                                        <a class="sm-tab <?= $filter === 'all' ? 'is-active' : ''; ?>"
                                           href="<?= base_url(); ?>Page/smea_admin_schools/<?= $slug; ?>/all">
                                            <i class="mdi mdi-format-list-bulleted"></i> All Schools
                                            <span class="sm-tab-count"><?= number_format($counts['all']); ?></span>
                                        </a>
                                    </div>

                                    <?php if (empty($data)) : ?>
                                        <div class="sm-empty">
                                            <i class="mdi mdi-file-document-outline"></i>
                                            <h5>
                                                <?php if ($filter === 'pending') : ?>
                                                    Every school has submitted
                                                <?php elseif ($filter === 'submitted') : ?>
                                                    No submissions yet
                                                <?php else : ?>
                                                    No schools found
                                                <?php endif; ?>
                                            </h5>
                                            <p>
                                                <?php if ($filter === 'submitted') : ?>
                                                    No school in <?= html_escape($district); ?> has submitted a SMEA for FY <?= html_escape($fy); ?>.
                                                <?php elseif ($filter === 'pending') : ?>
                                                    All <?= number_format($counts['all']); ?> schools in <?= html_escape($district); ?> have submitted for FY <?= html_escape($fy); ?>.
                                                <?php else : ?>
                                                    No schools are recorded under <?= html_escape($district); ?>.
                                                <?php endif; ?>
                                            </p>
                                        </div>
                                    <?php else : ?>
                                    <div class="table-responsive">
                                        <table id="datatable" class="table sm-table dt-responsive nowrap" style="width:100%;">
                                            <thead>
                                                <tr>
                                                    <th>School ID</th>
                                                    <th>School Name</th>
                                                    <th>Status</th>
                                                    <th>Submissions</th>
                                                    <th>Budget Codes</th>
                                                    <th>Last Submitted</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($data as $row) :
                                                    $name       = trim($row->schoolName) !== '' ? $row->schoolName : 'Unknown school';
                                                    $initial    = strtoupper(substr(trim($name), 0, 1));
                                                    $subs       = (int) $row->submissions;
                                                    $has_sub    = $subs > 0;
                                                    $codes      = $has_sub && !empty($row->b_codes) ? explode(',', $row->b_codes) : array();
                                                    ?>
                                                <tr>
                                                    <td><span class="sm-chip"><?= html_escape($row->schoolID); ?></span></td>
                                                    <td>
                                                        <div class="sm-school">
                                                            <span class="sm-avatar"><?= html_escape($initial); ?></span>
                                                            <span class="sm-school-text">
                                                                <span class="sm-school-name"><?= html_escape($name); ?></span>
                                                                <span class="sm-school-sub"><?= html_escape($row->district); ?></span>
                                                            </span>
                                                        </div>
                                                    </td>
                                                    <td data-order="<?= $has_sub ? 1 : 0; ?>">
                                                        <?php if ($has_sub) : ?>
                                                            <span class="sm-status-pill sm-status-submitted"><i class="mdi mdi-check-decagram"></i> Submitted</span>
                                                        <?php else : ?>
                                                            <span class="sm-status-pill sm-status-pending"><i class="mdi mdi-clock-outline"></i> Not Submitted</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td><?= number_format($subs); ?></td>
                                                    <td>
                                                        <?php if (empty($codes)) : ?>
                                                            <span class="sm-muted">&mdash;</span>
                                                        <?php else : ?>
                                                            <span class="sm-chips">
                                                                <?php foreach ($codes as $code) : ?>
                                                                    <span class="sm-chip"><?= html_escape($code); ?></span>
                                                                <?php endforeach; ?>
                                                            </span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <?php if ($has_sub && !empty($row->last_submit)) : ?>
                                                            <span class="sm-date"><?= html_escape(date('M d, Y', strtotime($row->last_submit))); ?></span>
                                                        <?php else : ?>
                                                            <span class="sm-muted">&mdash;</span>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <?php endif; ?>

                                </div>
                            </div>
                        </div>
                        <!--- end row -->

                    </div>
                    <!-- end container-fluid -->

                </div>
                <!-- end content -->

            <!-- ============================================================== -->
            <!-- End Page content -->
            <!-- ============================================================== -->

             <!-- Footer Start -->
             <?php include('includes/footer.php'); ?>
            <!-- end Footer -->

        </div>
        <!-- END wrapper -->

        <?php include('includes/smea_district_styles.php'); ?>

        <!-- Vendor js -->
        <script src="<?= base_url(); ?>assets/js/vendor.min.js"></script>

        <!-- App js -->
        <script src="<?= base_url(); ?>assets/js/app.min.js"></script>

        <!-- Required datatable js -->
        <script src="<?= base_url(); ?>assets/libs/datatables/jquery.dataTables.min.js"></script>
        <script src="<?= base_url(); ?>assets/libs/datatables/dataTables.bootstrap4.min.js"></script>
        <script src="<?= base_url(); ?>assets/libs/datatables/dataTables.responsive.min.js"></script>
        <script src="<?= base_url(); ?>assets/libs/datatables/responsive.bootstrap4.min.js"></script>

        <script type="text/javascript">
            $(function () {
                // The shared datatables.init.js also targets #datatable, so guard against a
                // double initialisation.
                if (!$.fn.DataTable.isDataTable('#datatable')) {
                    $('#datatable').DataTable({
                        pageLength: 25,
                        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
                        order: [[1, 'asc']],
                        language: {
                            search: '',
                            searchPlaceholder: 'Search school name or ID…',
                            lengthMenu: '_MENU_ per page',
                            info: 'Showing _START_ to _END_ of _TOTAL_ schools',
                            infoEmpty: 'No schools',
                            zeroRecords: 'No matching school found'
                        }
                    });
                }
            });
        </script>

    </body>
</html>
