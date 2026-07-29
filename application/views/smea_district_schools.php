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

$submissions = isset($submissions) ? $submissions : array();
$can_review  = isset($can_review) ? $can_review : false;
$back_url    = current_url();

// One table row per SMEA submission: a school submits once per budget code, so a
// school with two batches gets two independently reviewable rows. Schools with
// nothing submitted still get a row, with an empty submission.
$rows = array();
foreach ($data as $school) {
    $subs = isset($submissions[(string) $school->schoolID]) ? $submissions[(string) $school->schoolID] : array();

    if (empty($subs)) {
        $rows[] = array('school' => $school, 'smea' => null);
        continue;
    }

    foreach ($subs as $sub) {
        $rows[] = array('school' => $school, 'smea' => $sub);
    }
}

// status value -> [css modifier, icon, label, sort weight]
$states = array(
    'Submitted'      => array('submitted',  'mdi-check-decagram',   'Submitted',     1),
    'SDO Validated'  => array('validated',  'mdi-shield-check',     'SDO Validated', 3),
    'For Compliance' => array('compliance', 'mdi-alert-outline',    'For Compliance', 2),
);
?>

<?php // Loaded before the content, not after it: a <style> block below the table makes
      // the browser paint the raw markup first and restyle it a moment later. ?>
<?php include('includes/smea_district_styles.php'); ?>

            <!-- ============================================================== -->
            <!-- Start Page Content here -->
            <!-- ============================================================== -->

            <div class="content-page">
                <div class="content">

                    <div class="container-fluid">

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

                                    <?php if (empty($rows)) : ?>
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
                                    <div class="table-responsive sm-table-wrap">
                                        <!-- Skeleton shown until DataTables has taken the table over, so the
                                             raw unpaginated list never flashes on screen. -->
                                        <div class="sm-table-skeleton">
                                            <span></span><span></span><span></span><span></span><span></span>
                                        </div>
                                        <table id="datatable" class="table sm-table dt-responsive nowrap" style="width:100%;">
                                            <thead>
                                                <tr>
                                                    <th>School ID</th>
                                                    <th>School Name</th>
                                                    <th>Budget Code</th>
                                                    <th>Status</th>
                                                    <th>Submitted</th>
                                                    <th class="text-right">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($rows as $row) :
                                                    $school  = $row['school'];
                                                    $smea    = $row['smea'];
                                                    $name    = trim($school->schoolName) !== '' ? $school->schoolName : 'Unknown school';
                                                    $initial = strtoupper(substr(trim($name), 0, 1));

                                                    $status = $smea ? (trim((string) $smea->status) !== '' ? $smea->status : 'Submitted') : '';
                                                    $state  = isset($states[$status]) ? $states[$status] : array('pending', 'mdi-clock-outline', 'Not Submitted', 0);
                                                    ?>
                                                <tr>
                                                    <td><span class="sm-chip"><?= html_escape($school->schoolID); ?></span></td>
                                                    <td>
                                                        <div class="sm-school">
                                                            <span class="sm-avatar"><?= html_escape($initial); ?></span>
                                                            <span class="sm-school-text">
                                                                <span class="sm-school-name"><?= html_escape($name); ?></span>
                                                                <span class="sm-school-sub"><?= html_escape($school->district); ?></span>
                                                            </span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <?php if ($smea) : ?>
                                                            <span class="sm-chip"><?= html_escape($smea->b_code); ?></span>
                                                        <?php else : ?>
                                                            <span class="sm-muted">&mdash;</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td data-order="<?= $state[3]; ?>">
                                                        <span class="sm-status-pill sm-status-<?= $state[0]; ?>">
                                                            <i class="mdi <?= $state[1]; ?>"></i> <?= html_escape($state[2]); ?>
                                                        </span>
                                                        <?php if ($smea && !empty($smea->date_validated)) : ?>
                                                            <span class="sm-status-note">
                                                                <?= $status === 'For Compliance' ? 'Returned' : 'Validated'; ?>
                                                                <?= html_escape(date('M d, Y', strtotime($smea->date_validated))); ?>
                                                                <?php if (trim((string) $smea->validated_by) !== '') : ?>
                                                                    &middot; <?= html_escape($smea->validated_by); ?>
                                                                <?php endif; ?>
                                                            </span>
                                                        <?php endif; ?>
                                                        <?php if ($smea && $status === 'For Compliance' && trim((string) $smea->sdo_remarks) !== '') : ?>
                                                            <span class="sm-status-remark" title="<?= html_escape($smea->sdo_remarks); ?>">
                                                                &ldquo;<?= html_escape($smea->sdo_remarks); ?>&rdquo;
                                                            </span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td data-order="<?= $smea && !empty($smea->date_submit) ? strtotime($smea->date_submit) : 0; ?>">
                                                        <?php if ($smea && !empty($smea->date_submit)) : ?>
                                                            <span class="sm-date"><?= html_escape(date('M d, Y', strtotime($smea->date_submit))); ?></span>
                                                        <?php else : ?>
                                                            <span class="sm-muted">&mdash;</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td class="text-right">
                                                        <?php if (!$smea) : ?>
                                                            <span class="sm-muted">Nothing to review</span>
                                                        <?php else : ?>
                                                        <div class="sm-actions">
                                                            <a class="sm-btn" target="_blank" rel="noopener"
                                                               href="<?= base_url(); ?>Page/smea_review/<?= (int) $smea->id; ?>/1">
                                                                <i class="mdi mdi-file-document-outline"></i> View SMEA
                                                            </a>
                                                            <a class="sm-btn" target="_blank" rel="noopener"
                                                               href="<?= base_url(); ?>Page/smea_review_summary/<?= (int) $smea->id; ?>">
                                                                <i class="mdi mdi-chart-bar"></i> Summary
                                                            </a>

                                                            <?php if ($can_review && $status !== 'SDO Validated') : ?>
                                                                <form method="post" action="<?= base_url(); ?>Page/smea_validate" class="sm-inline-form"
                                                                      onsubmit="return confirm('Mark the SMEA of <?= html_escape(addslashes($name)); ?> (batch <?= html_escape($smea->b_code); ?>) as SDO VALIDATED?');">
                                                                    <input type="hidden" name="id" value="<?= (int) $smea->id; ?>">
                                                                    <input type="hidden" name="back" value="<?= html_escape($back_url); ?>">
                                                                    <button type="submit" class="sm-btn sm-btn-success">
                                                                        <i class="mdi mdi-check-bold"></i> Validate
                                                                    </button>
                                                                </form>
                                                            <?php endif; ?>

                                                            <?php if ($can_review && $status !== 'For Compliance') : ?>
                                                                <button type="button" class="sm-btn sm-btn-warning sm-compliance-open"
                                                                        data-id="<?= (int) $smea->id; ?>"
                                                                        data-school="<?= html_escape($name); ?>"
                                                                        data-bcode="<?= html_escape($smea->b_code); ?>">
                                                                    <i class="mdi mdi-alert-outline"></i> For Compliance
                                                                </button>
                                                            <?php endif; ?>
                                                        </div>
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

                <?php if ($can_review) : ?>
                <!-- Return a submission for compliance: the remark is what the school sees. -->
                <div id="smea-compliance" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="smeaComplianceLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content sm-modal">
                            <div class="modal-header">
                                <h5 class="modal-title" id="smeaComplianceLabel"><i class="mdi mdi-alert-outline"></i> Return for Compliance</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            </div>
                            <?= form_open('Page/smea_compliance'); ?>
                            <div class="modal-body">
                                <p class="sm-card-sub mb-2" id="smea-compliance-target"></p>
                                <div class="form-group mb-0">
                                    <label for="smea-compliance-remarks">Remarks / Comment <span class="text-danger">*</span></label>
                                    <textarea class="form-control" id="smea-compliance-remarks" name="sdo_remarks" rows="5" required
                                              placeholder="What has to be corrected or completed before this SMEA can be validated?"></textarea>
                                    <small class="sm-muted">The school sees this on its SMEA and can edit and submit the report again.</small>
                                </div>
                                <input type="hidden" name="id" id="smea-compliance-id" value="">
                                <input type="hidden" name="back" value="<?= html_escape($back_url); ?>">
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="sm-btn" data-dismiss="modal">Cancel</button>
                                <button type="submit" class="sm-btn sm-btn-warning">Return for Compliance</button>
                            </div>
                            </form>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

            <!-- ============================================================== -->
            <!-- End Page content -->
            <!-- ============================================================== -->

             <!-- Footer Start -->
             <?php include('includes/footer.php'); ?>
            <!-- end Footer -->

        </div>
        <!-- END wrapper -->

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
                        columnDefs: [{ orderable: false, targets: [5] }],
                        language: {
                            search: '',
                            searchPlaceholder: 'Search school name or ID…',
                            lengthMenu: '_MENU_ per page',
                            info: 'Showing _START_ to _END_ of _TOTAL_ submissions',
                            infoEmpty: 'No submissions',
                            zeroRecords: 'No matching school found'
                        }
                    });
                }

                // Table is styled and paginated — reveal it and drop the skeleton.
                $('.sm-table-wrap').addClass('is-ready');

                $(document).on('click', '.sm-compliance-open', function () {
                    var btn = $(this);
                    $('#smea-compliance-id').val(btn.data('id'));
                    $('#smea-compliance-target').text(btn.data('school') + ' — batch ' + btn.data('bcode'));
                    $('#smea-compliance-remarks').val('');
                    $('#smea-compliance').modal('show');
                });
            });
        </script>

    </body>
</html>
