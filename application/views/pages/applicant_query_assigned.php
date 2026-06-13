<!-- ============================================================== -->
            <!-- Start Page Content here -->
            <!-- ============================================================== -->

            <div class="content-page">
                <div class="content">

                    <!-- Start Content-->
                    <div class="container-fluid">

                        <!-- start page title -->
                        <div class="row">
                            <div class="col-12">
                                <div class="page-title-box">
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                        </div>
                        <!-- end page title -->

                        <?php if($this->session->flashdata('success')) : ?>
                            <?= '<div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>'
                                    .$this->session->flashdata('success').
                                '</div>';
                            ?>
                        <?php endif; ?>

                        <?php if($this->session->flashdata('danger')) : ?>
                            <?= '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>'
                                    .$this->session->flashdata('danger').
                                '</div>';
                            ?>
                        <?php endif;  ?>

                        <?php if(!empty($data)): ?>
                        <div class="alert alert-info alert-dismissible fade show" role="alert">
                            <strong>Reminder:</strong> After completing your evaluation and reply, please click the <strong>Final</strong> button to hide this query from your list.
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <?php endif; ?>

                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body table-responsive">
                                        <h4 class="header-title mb-4">My Assigned Applicant Queries</h4><br />
                                        <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                            <thead>
                                                <tr>
                                                    <th>Fullname</th>
                                                    <th>Applicant No.</th>
                                                    <th>Date of Query</th>
                                                    <th style="text-align:center">Action</th>
                                                    <th style="text-align:center">Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach($data as $row): ?>
                                                    <tr>
                                                        <td><?= $row->fullname; ?></td>
                                                        <td><?= $row->record_no; ?></td>
                                                        <td><?= $row->idate; ?></td>
                                                        <td class="text-center">
                                                        <?php if($row->position == 1){ ?>
                                                            <a class="btn btn-success btn-sm" target="_blank" href="<?= base_url(); ?>Pages/inquiry/<?= $row->applicant_id; ?>/<?= $row->job_id; ?>/<?= $row->pre_school; ?>/<?= $row->application_id; ?>/<?= $row->record_no; ?>"><i class="mdi mdi-notebook-multiple tooltips text-white"></i></a>
                                                        <?php }else{ ?>
                                                            <a class="btn btn-success btn-sm" target="_blank" href="<?= base_url(); ?>Pages/inquiry_non/<?= $row->applicant_id; ?>/<?= $row->job_id; ?>/<?= $row->pre_school; ?>/<?= $row->application_id; ?>/<?= $row->record_no; ?>"><i class="mdi mdi-notebook-multiple tooltips text-white"></i></a>
                                                        <?php } ?>
                                                        </td>
                                                        <td class="text-center">
                                                            <a href="<?= base_url(); ?>Pages/aq/<?= $row->application_id; ?>" onclick="return confirm('Are you sure you want to finalize this query? This action will permanently hide it from your list.');" class="btn btn-purple btn-sm">Final</a>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- end row -->

                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body table-responsive">
                                        <h4 class="header-title mb-4">Finalized Applicant Queries</h4><br />
                                        <table id="datatable2" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                            <thead>
                                                <tr>
                                                    <th>Fullname</th>
                                                    <th>Applicant No.</th>
                                                    <th>Date of Query</th>
                                                    <th style="text-align:center">Action</th>
                                                    <th style="text-align:center">Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach(($finalized_data ?? []) as $row): ?>
                                                    <tr>
                                                        <td><?= $row->fullname; ?></td>
                                                        <td><?= $row->record_no; ?></td>
                                                        <td><?= $row->idate; ?></td>
                                                        <td class="text-center">
                                                        <?php if($row->position == 1){ ?>
                                                            <a class="btn btn-success btn-sm" target="_blank" href="<?= base_url(); ?>Pages/inquiry/<?= $row->applicant_id; ?>/<?= $row->job_id; ?>/<?= $row->pre_school; ?>/<?= $row->application_id; ?>/<?= $row->record_no; ?>"><i class="mdi mdi-notebook-multiple tooltips text-white"></i></a>
                                                        <?php }else{ ?>
                                                            <a class="btn btn-success btn-sm" target="_blank" href="<?= base_url(); ?>Pages/inquiry_non/<?= $row->applicant_id; ?>/<?= $row->job_id; ?>/<?= $row->pre_school; ?>/<?= $row->application_id; ?>/<?= $row->record_no; ?>"><i class="mdi mdi-notebook-multiple tooltips text-white"></i></a>
                                                        <?php } ?>
                                                        </td>
                                                        <td class="text-center">
                                                            <span class="badge badge-success">Finalized</span>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- end row -->

                    </div>
                    <!-- end container-fluid -->

                </div>
                <!-- end content -->

                <!-- DataTables CSS fallback (loads only if your template's copy isn't already styling it) -->
                <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap4.min.css">
                <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap4.min.css">

                <script>
                (function () {
                    // Inject DataTables JS only if the plugin isn't already registered.
                    function injectScript(src, cb) {
                        var s = document.createElement('script');
                        s.src = src;
                        s.onload = cb;
                        s.onerror = function () { console.error('Failed to load: ' + src); };
                        document.head.appendChild(s);
                    }

                    function initTables() {
                        if (!window.jQuery || !jQuery.fn || !jQuery.fn.DataTable) {
                            console.error('DataTables still not available after load.');
                            return;
                        }

                        var commonOpts = {
                            paging: true,
                            searching: true,
                            ordering: true,
                            info: true,
                            lengthChange: true,
                            pageLength: 10,
                            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
                            responsive: true,
                            order: [[2, 'desc']], // sort by Date of Query desc by default
                            language: {
                                search: "Search:",
                                lengthMenu: "Show _MENU_ entries",
                                info: "Showing _START_ to _END_ of _TOTAL_ entries",
                                paginate: { previous: "Prev", next: "Next" },
                                emptyTable: "No records found"
                            }
                        };

                        ['#datatable', '#datatable2'].forEach(function (sel) {
                            var $t = jQuery(sel);
                            if ($t.length && !jQuery.fn.DataTable.isDataTable(sel)) {
                                $t.DataTable(commonOpts);
                            }
                        });
                    }

                    function boot() {
                        if (!window.jQuery) {
                            console.error('jQuery not found. DataTables cannot init.');
                            return;
                        }
                        if (jQuery.fn && jQuery.fn.DataTable) {
                            initTables();
                            return;
                        }
                        // Plugin missing — load from CDN as fallback (fixes "works locally, fails in prod")
                        injectScript('https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js', function () {
                            injectScript('https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap4.min.js', function () {
                                injectScript('https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js', initTables);
                            });
                        });
                    }

                    if (document.readyState === 'loading') {
                        document.addEventListener('DOMContentLoaded', boot);
                    } else {
                        boot();
                    }
                })();
                </script>