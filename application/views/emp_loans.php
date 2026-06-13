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
            <?php endif; ?>

            <style>
                .loan-card {
                    border: 0;
                    border-radius: 12px;
                    box-shadow: 0 6px 18px rgba(0, 0, 0, 0.06);
                    overflow: hidden;
                }

                .loan-header {
                    padding: 16px 20px;
                    background: #ffffff;
                    border-bottom: 1px solid #f1f1f1;
                }

                .loan-title {
                    margin: 0;
                    font-weight: 900;
                    font-size: 16px;
                    letter-spacing: .3px;
                    text-transform: uppercase;
                    color: #1f2937;
                }

                .loan-subtitle {
                    margin: 4px 0 0;
                    font-size: 12px;
                    color: #6c757d;
                }

                .loan-table th {
                    background: #f8fafc;
                    font-weight: 800;
                    white-space: nowrap;
                    border-bottom: 2px solid #e9ecef !important;
                }

                .loan-table td {
                    vertical-align: middle;
                }

                .money {
                    text-align: right !important;
                    font-variant-numeric: tabular-nums;
                    white-space: nowrap;
                }

                .text-center {
                    text-align: center !important;
                }

                .total-row td {
                    border-top: 2px solid #e9ecef !important;
                    font-weight: 900;
                    background: #fff;
                }

                .badge-status {
                    display: inline-block;
                    padding: .3rem .6rem;
                    border-radius: 999px;
                    font-size: 12px;
                    font-weight: 800;
                    border: 1px solid rgba(0,0,0,.08);
                }

                .badge-active {
                    background: #e8fff3;
                    color: #0f5132;
                }

                .badge-inactive {
                    background: #fff3cd;
                    color: #664d03;
                }

                .badge-closed {
                    background: #fde2e2;
                    color: #842029;
                }

                .empty-state {
                    padding: 16px;
                    border: 1px dashed #dee2e6;
                    border-radius: 10px;
                    background: #fafafa;
                    color: #6c757d;
                }
            </style>

            <div class="row">
                <div class="col-12">

                    <div class="card loan-card">
                        <div class="loan-header">
                            <h4 class="loan-title">Current Loans and Other Deductions</h4>
                            <div class="loan-subtitle">List of active deductions, effectivity dates, and balances</div>
                        </div>

                        <div class="card-body table-responsive">

                            <?php
                            $sumDeducted  = 0;
                            $sumPrincipal = 0;

                            // helper for safe output
                            function h($s)
                            {
                                return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
                            }

                            // helper badge (optional)
                            function status_badge($status)
                            {
                                $s = strtoupper(trim((string)$status));
                                $cls = 'badge-status badge-inactive';

                                if ($s === 'ACTIVE') $cls = 'badge-status badge-active';
                                else if ($s === 'CLOSED' || $s === 'TERMINATED') $cls = 'badge-status badge-closed';

                                return "<span class='{$cls}'>" . htmlspecialchars($status, ENT_QUOTES, 'UTF-8') . "</span>";
                            }
                            ?>

                            <table class="table table-striped table-bordered dt-responsive nowrap loan-table"
                                   style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                <thead>
                                    <tr>
                                        <th>PLI/GFI</th>
                                        <th class="text-center">Ded. Code</th>
                                        <th class="text-center">Effectivity</th>
                                        <th class="text-center">Termination</th>
                                        <th class="money">Deducted Amount</th>
                                        <th class="money">Principal Amount</th>
                                        <th class="text-center">Ded. Status</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <?php if (!empty($data)) : ?>
                                        <?php foreach ($data as $row) : ?>
                                            <?php
                                            $dedAmount = (float)$row->dedAmount;
                                            $principal = (float)$row->principalAmount;

                                            $sumDeducted  += $dedAmount;
                                            $sumPrincipal += $principal;
                                            ?>
                                            <tr>
                                                <td><?= h($row->dedAgency); ?></td>
                                                <td class="text-center"><?= h($row->dedCode); ?></td>
                                                <td class="text-center"><?= h($row->effectivity); ?></td>
                                                <td class="text-center"><?= h($row->termination); ?></td>
                                                <td class="money"><?= number_format($dedAmount, 2); ?></td>
                                                <td class="money"><?= number_format($principal, 2); ?></td>
                                                <td class="text-center"><?= status_badge($row->dedStatus); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else : ?>
                                        <tr>
                                            <td colspan="7" class="text-center text-muted" style="padding: 18px;">
                                                No current loans/deductions found.
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>

                                <tfoot>
                                    <tr class="total-row">
                                        <td colspan="4" class="text-right" style="font-weight:900;">TOTAL</td>
                                        <td class="money"><?= number_format($sumDeducted, 2); ?></td>
                                        <td class="money"><?= number_format($sumPrincipal, 2); ?></td>
                                        <td></td>
                                    </tr>
                                </tfoot>
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

    <?php include('templates/footer.php'); ?>


    <!--  Training Needs Modal (kept as-is) -->
    <div class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myLargeModalLabel">Training Needs</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">

                    <form class="form-horizontal" method="post">
                        <div class="form-group row">
                            <label for="inputEmail3" class="col-md-3 col-form-label">Training Need</label>
                            <div class="col-md-9">
                                <input type="text" class="form-control" name="trainingNeeds">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="inputPassword5" class="col-md-3 col-form-label">Justification</label>
                            <div class="col-md-9">
                                <textarea class="form-control" rows="5" name="justification"></textarea>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="inputEmail3" class="col-md-3 col-form-label">Category</label>
                            <div class="col-md-9">
                                <select class="form-control" name="trainingCat">
                                    <option>Acquisition Financial Management</option>
                                    <option>Budget Calculation</option>
                                    <option>Contact Management</option>
                                    <option>Financial Budget and Program Analysis</option>
                                    <option>Administrative Support</option>
                                    <option>Internal Resource Management</option>
                                    <option>Occupational Health and Safety Knowledge</option>
                                    <option>Process Management</option>
                                    <option>Ethics Knowledge</option>
                                    <option>Performance Management for Human Resource Professionals</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group mb-0 justify-content-end row">
                            <div class="col-md-9">
                                <input type="submit" name="submit" value="Submit" class="btn btn-info waves-effect waves-light">
                            </div>
                        </div>
                    </form>

                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <!-- /.modal -->

</div>
<!-- ============================================================== -->
<!-- End Page Content -->
<!-- ============================================================== -->
