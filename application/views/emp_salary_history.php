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
                        <!-- <button type="button" class="btn btn-primary waves-effect waves-light" data-toggle="modal" data-backdrop="static" data-keyboard="false" data-target=".bs-example-modal-lg">Add New</button> -->
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

            <div class="row">
                <div class="col-12">

                    <style>
                        .salary-card {
                            border: 0;
                            border-radius: 12px;
                            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.06);
                        }

                        .salary-card .card-header {
                            background: #fff;
                            border-bottom: 1px solid #f1f1f1;
                            padding: 16px 20px;
                            border-top-left-radius: 12px;
                            border-top-right-radius: 12px;
                        }

                        .salary-title {
                            margin: 0;
                            font-weight: 800;
                            font-size: 18px;
                            color: #222;
                        }

                        .salary-subtitle {
                            margin: 2px 0 0;
                            font-size: 12px;
                            color: #6c757d;
                        }

                        .salary-table th {
                            background: #f8fafc;
                            border-bottom: 2px solid #e9ecef !important;
                            font-weight: 800;
                            white-space: nowrap;
                        }

                        .salary-table td {
                            vertical-align: middle;
                        }

                        .salary-table tfoot th {
                            background: #fff;
                            border-top: 2px solid #e9ecef !important;
                            font-weight: 900;
                        }

                        .money {
                            font-variant-numeric: tabular-nums;
                        }

                        .text-right {
                            text-align: right !important;
                        }

                        .text-center {
                            text-align: center !important;
                        }

                        .badge-soft {
                            display: inline-block;
                            padding: .35rem .6rem;
                            border-radius: 999px;
                            font-weight: 700;
                            font-size: 12px;
                            background: #eef2ff;
                            color: #334155;
                        }
                    </style>

                    <div class="card salary-card">
                        <div class="card-header d-flex align-items-center justify-content-between">
                            <div>
                                <h4 class="salary-title">My Salary History</h4>
                                <p class="salary-subtitle">Summary of salary records by month (Basic + PERA = Total)</p>
                            </div>

                            <!-- Optional: Add button if needed later -->
                            <!--
                            <button class="btn btn-primary btn-sm" data-toggle="modal" data-target=".bs-example-modal-lg">
                                <i class="mdi mdi-plus"></i> Add New
                            </button>
                            -->
                        </div>

                        <div class="card-body table-responsive">

                            <?php
                            $totalBasic = 0;
                            $totalPera  = 0;
                            $totalAll   = 0;
                            ?>

                            <table class="table table-striped table-bordered dt-responsive nowrap salary-table"
                                   style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                <thead>
                                    <tr>
                                        <th>Month/Year</th>
                                        <th>Position</th>
                                        <th class="text-center">SG/Step</th>
                                        <th class="text-right">Basic Salary</th>
                                        <th class="text-right">PERA</th>
                                        <th class="text-right">Total</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <?php if (!empty($data)) : ?>
                                        <?php foreach ($data as $row) : ?>
                                            <?php
                                            $basic = (float)$row->basicSalary;
                                            $pera  = (float)$row->pera;
                                            $total = $basic + $pera;

                                            $totalBasic += $basic;
                                            $totalPera  += $pera;
                                            $totalAll   += $total;
                                            ?>
                                            <tr>
                                                <td><?= htmlspecialchars($row->payMonth . ' ' . $row->payYear, ENT_QUOTES, 'UTF-8'); ?></td>
                                                <td><?= htmlspecialchars($row->empPosition, ENT_QUOTES, 'UTF-8'); ?></td>
                                                <td class="text-center">
                                                    <span class="badge-soft">
                                                        <?= htmlspecialchars($row->sg . '/ ' . $row->step, ENT_QUOTES, 'UTF-8'); ?>
                                                    </span>
                                                </td>
                                                <td class="text-right money"><?= number_format($basic, 2); ?></td>
                                                <td class="text-right money"><?= number_format($pera, 2); ?></td>
                                                <td class="text-right money" style="font-weight:800;"><?= number_format($total, 2); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else : ?>
                                        <tr>
                                            <td colspan="6" class="text-center text-muted" style="padding: 18px;">
                                                No salary history records found.
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>

                                <tfoot>
                                    <tr>
                                        <th colspan="3" class="text-right">Grand Total:</th>
                                        <th class="text-right money"><?= number_format($totalBasic, 2); ?></th>
                                        <th class="text-right money"><?= number_format($totalPera, 2); ?></th>
                                        <th class="text-right money"><?= number_format($totalAll, 2); ?></th>
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


    <!--  Add New Vacancies / Training Needs -->
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
