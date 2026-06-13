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
                .ded-card {
                    border: 0;
                    border-radius: 12px;
                    box-shadow: 0 6px 18px rgba(0, 0, 0, 0.06);
                    overflow: hidden;
                }

                .ded-header {
                    padding: 16px 20px;
                    background: #6f42c1; /* purple */
                    color: #fff;
                }

                .ded-title {
                    margin: 0;
                    font-weight: 900;
                    letter-spacing: .3px;
                    font-size: 16px;
                    text-transform: uppercase;
                }

                .ded-subtitle {
                    margin: 4px 0 0;
                    font-size: 12px;
                    opacity: .9;
                }

                .ded-table th {
                    background: #f8fafc;
                    font-weight: 800;
                    white-space: nowrap;
                }

                .ded-table td {
                    vertical-align: middle;
                }

                .money {
                    text-align: right !important;
                    font-variant-numeric: tabular-nums;
                    white-space: nowrap;
                }

                .label-col {
                    font-weight: 700;
                    color: #2b2f36;
                }

                .total-row td {
                    border-top: 2px solid #e9ecef !important;
                    font-weight: 900;
                    background: #fff;
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

                    <div class="card ded-card">
                        <div class="ded-header">
                            <h5 class="ded-title text-white">Mandatory Monthly Deductions</h5>
                            <div class="ded-subtitle ">Breakdown of standard deductions (GSIS, PhilHealth, Pag-IBIG, Tax)</div>
                        </div>

                        <div class="card-body">

                            <?php if (!empty($data) && isset($data[0])) : ?>
                                <?php
                                // Pull values safely
                                $gsis   = (float)$data[0]->dedGSIS;
                                $ph     = (float)$data[0]->dedPHealth;
                                $pag    = (float)$data[0]->dedPagibig;
                                $tax    = (float)$data[0]->dedTax;

                                $total  = $gsis + $ph + $pag + $tax;

                                // If you want peso sign, change to: "₱ " . number_format($value, 2)
                                ?>

                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered dt-responsive nowrap ded-table" style="width:100%;">
                                        <thead>
                                            <tr>
                                                <th>Deduction</th>
                                                <th class="money">Amount</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            <tr>
                                                <td class="label-col">GSIS</td>
                                                <td class="money"><?= number_format($gsis, 2); ?></td>
                                            </tr>
                                            <tr>
                                                <td class="label-col">PhilHealth</td>
                                                <td class="money"><?= number_format($ph, 2); ?></td>
                                            </tr>
                                            <tr>
                                                <td class="label-col">Pag-IBIG</td>
                                                <td class="money"><?= number_format($pag, 2); ?></td>
                                            </tr>
                                            <tr>
                                                <td class="label-col">Tax</td>
                                                <td class="money"><?= number_format($tax, 2); ?></td>
                                            </tr>
                                        </tbody>

                                        <tfoot>
                                            <tr class="total-row">
                                                <td class="label-col">TOTAL DEDUCTIONS</td>
                                                <td class="money"><?= number_format($total, 2); ?></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>

                            <?php else : ?>

                                <div class="empty-state text-center">
                                    <strong>No deduction data found.</strong><br>
                                    Please check if the payroll record is available for the selected period.
                                </div>

                            <?php endif; ?>

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

</div>
<!-- ============================================================== -->
<!-- End Page Content -->
<!-- ============================================================== -->
