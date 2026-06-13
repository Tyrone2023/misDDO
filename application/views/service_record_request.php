            <?php include('templates/head.php'); ?>
            <link href="<?= base_url(); ?>assets/libs/custombox/custombox.min.css" rel="stylesheet" type="text/css">
            <?php include('templates/header.php'); ?>

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

                        <?php if ($this->session->flashdata('success')) : ?>

                            <?= '<div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>'
                                . $this->session->flashdata('success') .
                                '</div>';
                            ?>
                        <?php endif; ?>

                        <?php if ($this->session->flashdata('danger')) : ?>
                            <?= '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>'
                                . $this->session->flashdata('danger') .
                                '</div>';
                            ?>
                        <?php endif;  ?>

                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body table-responsive">
                                        <h4 class="header-title mb-4">Service Record Request</h4>

                                        <form method="post">

                                            <div class="form-group">
                                                <label for="purpose" class="col-form-label">Purpose</label>
                                                <select name="purpose" class="form-control" required>
                                                    <option value="" disabled selected>Select Purpose</option>
                                                    <option value=" For Promotion"> For Promotion</option>
                                                    <option value="For GSIS">For GSIS</option>
                                                    <option value="for PAGIBIG">for PAGIBIG</option>
                                                    <option value="for PAGIBIG">for Other Purpose (specify in the message)</option>
                                                </select>
                                            </div>


                                            <div class="form-group">
                                                <label for="inputAddress" class="col-form-label" name="q5">Message</label>
                                                <textarea class="form-control" name="message" rows="4" cols="50"></textarea>
                                            </div>


                                            <input type="submit" name="submit" value="Submit" class="btn btn-primary">
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- end row -->


                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body table-responsive">
                                        <h4 class="header-title mb-4">Submitted Request</h4>

                                        <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                            <thead>
                                                <tr>
                                                    <th>Date Requested</th>
                                                    <th>Purpose</th>
                                                    <th>Message</th>
                                                    <th>Status</th>
                                                    <th style="text-align:center;">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($data as $row): ?>
                                                    <tr>
                                                        <td><?= $row->dateReq; ?></td>
                                                        <td><?= $row->purpose; ?></td>
                                                        <td><?= $row->message; ?></td>
                                                        <td><?= $row->reqStat; ?></td>
                                                        <td style="text-align:center;">
    <?php if ($row->reqStat === 'Processed'): ?>
        <?php if ($row->forPrint === '1'): ?>
            <a href="<?= base_url('Page/printServiceRecord?id=' . $row->IDNumber); ?>" class="btn btn-primary btn-sm" target="_blank">
                Print Service Record
            </a>
        <?php elseif ($row->forPrint === '0'): ?>
            <button class="btn btn-success btn-sm" data-toggle="modal" data-target="#approvedModal">
                Approved
            </button>
        <?php endif; ?>
    <?php elseif ($row->reqStat === 'Disapproved'): ?>
        <button class="btn btn-danger btn-sm" data-toggle="modal" data-target="#disapprovedModal">
            Disapproved
        </button>
    <?php elseif ($row->reqStat === 'Pending'): ?>
        <!-- Button to cancel the request -->
        <button class="btn btn-warning btn-sm" data-toggle="modal" data-target="#cancelModal">
            Cancel Request
        </button>
    <?php elseif ($row->reqStat === 'Canceled'): ?>
        <!-- If request is canceled, display Canceled text -->
        <button class="btn btn-secondary btn-sm" disabled>
            Canceled
        </button>
    <?php endif; ?>
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



<!-- Modal for Approved -->
<div class="modal fade" id="approvedModal" tabindex="-1" role="dialog" aria-labelledby="approvedModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="approvedModalLabel">Approval Status</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Approved but not available for printing.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal for Disapproved -->
<div class="modal fade" id="disapprovedModal" tabindex="-1" role="dialog" aria-labelledby="disapprovedModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="disapprovedModalLabel">Disapproval Reason</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <?= htmlspecialchars($row->disapprovalReason); ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal for confirming cancel request -->
<div class="modal fade" id="cancelModal" tabindex="-1" role="dialog" aria-labelledby="cancelModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cancelModalLabel">Cancel Request</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Are you sure you want to cancel this request?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
                <a href="<?= base_url('Page/cancelRequest?id=' . $row->IDNumber); ?>" class="btn btn-danger">
                    Yes, Cancel
                </a>
            </div>
        </div>
    </div>
</div>





                    </div>
                </div>
            </div>
            </div>
            <!-- end row -->

            </div>
            <!-- end container-fluid -->

            </div>
            <!-- end content -->

            <?php include('templates/footer.php'); ?>