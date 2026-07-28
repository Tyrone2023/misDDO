<?php
$total_schools   = 0;
$total_submitted = 0;
foreach ($data as $r) {
    $total_schools   += (int) $r->total_schools;
    $total_submitted += (int) $r->submitted;
}
$total_pending = $total_schools - $total_submitted;
$overall_rate  = $total_schools > 0 ? round(($total_submitted / $total_schools) * 100, 1) : 0;
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
                                        <h3 class="sm-hero-title"><i class="mdi mdi-office-building-outline"></i> SMEA Submissions by District</h3>
                                        <p class="sm-hero-sub">
                                            Fiscal Year <strong><?= html_escape($fy); ?></strong>
                                            <span class="sm-dotsep">&bull;</span>
                                            Select a district to open its school list, then drill down into the schools that submitted.
                                        </p>
                                    </div>
                                    <div class="sm-hero-stats">
                                        <div class="sm-stat">
                                            <span class="sm-stat-value"><?= number_format(count($data)); ?></span>
                                            <span class="sm-stat-label">Districts</span>
                                        </div>
                                        <div class="sm-stat">
                                            <span class="sm-stat-value"><?= number_format($total_submitted); ?></span>
                                            <span class="sm-stat-label">Submitted</span>
                                        </div>
                                        <div class="sm-stat">
                                            <span class="sm-stat-value"><?= number_format($total_pending); ?></span>
                                            <span class="sm-stat-label">Not Submitted</span>
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
                                            <h4 class="sm-card-title">District List</h4>
                                            <p class="sm-card-sub">
                                                <?= number_format($total_submitted); ?> of <?= number_format($total_schools); ?>
                                                schools have submitted their SMEA (<?= $overall_rate; ?>%).
                                            </p>
                                        </div>
                                        <div class="sm-card-actions">
                                            <a class="sm-btn" href="<?= base_url(); ?>Page/smea_admin_list">
                                                <i class="mdi mdi-format-list-bulleted"></i> All Submissions
                                            </a>
                                            <a data-toggle="modal" class="sm-btn sm-btn-primary" href="#smea-report">
                                                <i class="mdi mdi-file-chart-outline"></i> SMEA Report
                                            </a>
                                        </div>
                                    </div>

                                    <?php if (empty($data)) : ?>
                                        <div class="sm-empty">
                                            <i class="mdi mdi-map-marker-off-outline"></i>
                                            <h5>No districts found</h5>
                                            <p>No schools are assigned to a district yet.</p>
                                        </div>
                                    <?php else : ?>
                                    <div class="table-responsive">
                                        <table id="datatable" class="table sm-table dt-responsive nowrap" style="width:100%;">
                                            <thead>
                                                <tr>
                                                    <th>No.</th>
                                                    <th>District</th>
                                                    <th>Total Schools</th>
                                                    <th>Submitted</th>
                                                    <th>Not Submitted</th>
                                                    <th class="text-right">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $count = 0;
                                                foreach ($data as $row) :
                                                    $count++;
                                                    $schools   = (int) $row->total_schools;
                                                    $submitted = (int) $row->submitted;
                                                    $pending   = (int) $row->not_submitted;
                                                    $rate      = $schools > 0 ? round(($submitted / $schools) * 100, 1) : 0;
                                                    $prate     = $schools > 0 ? round(($pending / $schools) * 100, 1) : 0;
                                                    // rawurlencode, not urlencode: district names contain spaces and CodeIgniter's
                                                    // permitted_uri_chars allows %20 but rejects the "+" that urlencode emits.
                                                    $slug      = rawurlencode($row->district);
                                                    $initial   = strtoupper(substr(trim($row->district), 0, 1));
                                                    ?>
                                                <tr>
                                                    <td><span class="sm-num"><?= $count; ?></span></td>
                                                    <td>
                                                        <div class="sm-district">
                                                            <span class="sm-avatar"><?= html_escape($initial); ?></span>
                                                            <span class="sm-district-text">
                                                                <span class="sm-district-name"><?= html_escape($row->district); ?></span>
                                                                <span class="sm-district-sub">Browse schools recorded under this district</span>
                                                            </span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span class="sm-chip"><?= number_format($schools); ?></span>
                                                    </td>
                                                    <td data-order="<?= $submitted; ?>">
                                                        <div class="sm-metric">
                                                            <span class="sm-metric-value sm-text-success"><?= number_format($submitted); ?></span>
                                                            <span class="sm-metric-label">Submitted</span>
                                                            <span class="sm-bar"><span class="sm-bar-fill sm-bar-success" style="width: <?= $rate; ?>%;"></span></span>
                                                            <span class="sm-metric-pct"><?= $rate; ?>%</span>
                                                        </div>
                                                    </td>
                                                    <td data-order="<?= $pending; ?>">
                                                        <div class="sm-metric">
                                                            <span class="sm-metric-value sm-text-danger"><?= number_format($pending); ?></span>
                                                            <span class="sm-metric-label">Not Submitted</span>
                                                            <span class="sm-bar"><span class="sm-bar-fill sm-bar-danger" style="width: <?= $prate; ?>%;"></span></span>
                                                            <span class="sm-metric-pct"><?= $prate; ?>%</span>
                                                        </div>
                                                    </td>
                                                    <td class="text-right">
                                                        <a class="sm-btn sm-btn-primary" href="<?= base_url(); ?>Page/smea_admin_schools/<?= $slug; ?>/submitted">
                                                            <i class="mdi mdi-school-outline"></i> View Schools
                                                        </a>
                                                    </td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                            <tfoot>
                                                <tr class="sm-total-row">
                                                    <td></td>
                                                    <td>TOTAL</td>
                                                    <td><?= number_format($total_schools); ?></td>
                                                    <td><?= number_format($total_submitted); ?></td>
                                                    <td><?= number_format($total_pending); ?></td>
                                                    <td class="text-right"><?= $overall_rate; ?>% submitted</td>
                                                </tr>
                                            </tfoot>
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

                <!-- SMEA report (per quarter) -->
                <div id="smea-report" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="smeaReportLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content sm-modal">
                            <div class="modal-header">
                                <h5 class="modal-title" id="smeaReportLabel"><i class="mdi mdi-file-chart-outline"></i> SMEA Report</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            </div>
                            <?= form_open('Page/smea_admin_generate', ['target' => '_blank']); ?>
                            <div class="modal-body">
                                <div class="form-group mb-0">
                                    <label for="smea-quarter">Select Quarter</label>
                                    <select class="form-control" id="smea-quarter" name="q" required>
                                        <option value="">Choose a quarter&hellip;</option>
                                        <option value="1">Quarter 1</option>
                                        <option value="2">Quarter 2</option>
                                        <option value="3">Quarter 3</option>
                                        <option value="4">Quarter 4</option>
                                    </select>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="sm-btn" data-dismiss="modal">Cancel</button>
                                <button type="submit" name="submit" class="sm-btn sm-btn-primary">Generate</button>
                            </div>
                            </form>
                        </div>
                    </div>
                </div>

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
                        columnDefs: [{ orderable: false, targets: [0, 5] }],
                        language: {
                            search: '',
                            searchPlaceholder: 'Search district…',
                            lengthMenu: '_MENU_ per page',
                            info: 'Showing _START_ to _END_ of _TOTAL_ districts',
                            infoEmpty: 'No districts',
                            zeroRecords: 'No matching district found'
                        }
                    });
                }
            });
        </script>

    </body>
</html>
