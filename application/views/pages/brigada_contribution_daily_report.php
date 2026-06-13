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
                        <?php //if($this->session->position == 'School'){?>
                        <div class="button-list">
                            <a href="<?= base_url(); ?>Brigada/contribution_report_new" class="btn btn-primary waves-effect waves-light openModalBtn">Add New</a>
                            <a href="#" data-toggle="modal" data-target="#myModal" class="btn btn-info waves-effect waves-light openModalBtn">Report</a>
                            <!-- NEW: DPDS (Excel-layout) report -->
                            <a href="#" data-toggle="modal" data-target="#dpdsModal" class="btn btn-success waves-effect waves-light openModalBtn">DPDS Report</a>
                        </div>
                        <?php //} ?>

                        <div class="page-title-right">
                            <ol class="breadcrumb p-0 m-0">
                                <li class="breadcrumb-item"><a href="#" data-toggle="modal" data-target="#renren">Current School Year : <span class="badge badge-success"><?= $this->session->cur_sy; ?></span></a></li>
                            </ol>
                        </div>

                        <div class="clearfix"></div>

                    </div>
                </div>
            </div>
            <!-- end page title -->

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body table-responsive">
                        <h4 class="header-title mb-4"><?= $title; ?></h4>

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

                             <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">

                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Date</th>
                                        <th>Partner</th>
                                        <th>Contribution Type</th>
                                        <th>Specific Contribution Type</th>
                                        <th>Quantity Contributed</th>
                                        <th>Unit of Contribution</th>
                                        <th>Amount</th>
                                        <th>No. of Beneficiary Learners</th>
                                        <th>No. of Beneficiary Personnel</th>
                                        <th>Form of Agreement </th>
                                        <th>Agreement Start Date</th>
                                        <th>Agreement End Date</th>
                                        <th>Project Category</th>
                                        <th>Project Name</th>
                                        <th>Status of Agreement / Project</th>
                                        <th>Initiated By</th>
                                        <th>Remarks</th>
                                        <th>SY</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <?php $c=1; foreach($data as $row){?>
                                    <tr>
                                        <td><?= $c++; ?></td>
                                        <th><?= $row->c_date; ?></th>
                                        <td><?= str_replace('_', ' ', $row->pname); ?></td>
                                        <td><?= str_replace('_', ' ', $row->cname); ?></td>
                                        <th><?= $row->spicific_contribution; ?></th>
                                        <th><?= $row->quantity_of_conftribution; ?></th>
                                        <th><?= $row->unit_of_contribution; ?></th>
                                        <th><?= $row->amount; ?></th>
                                        <th><?= $row->no_beneficiary_learnes; ?></th>
                                        <th><?= $row->no_beneficiary_personnel; ?></th>
                                        <th><?= $row->form_of_agreement; ?></th>
                                        <th><?= $row->agreement_started; ?></th>
                                        <th><?= $row->agreement_end; ?></th>
                                        <th><?= $row->project_category; ?></th>
                                        <th><?= $row->project_name; ?></th>
                                        <th><?= $row->status_agreement; ?></th>
                                        <th><?= $row->initiated_by; ?></th>
                                        <th><?= $row->remarks; ?></th>
                                        <th><?= $row->sy; ?></th>
                                        <td>
                                           <div class="btn-group-sm">
                                                <a href="<?= base_url(); ?>Brigada/contribution_report_edit/<?= $row->id; ?>" class="btn btn-success mr-1 tooltips" data-placement="top" data-toggle="tooltip" data-original-title="Edit">
                                                    <i class="fas fa-pencil-alt"></i>
                                                </a>
                                                <a onclick="return confirm('Are you sure?');" href="<?= base_url(); ?>Brigada/contribution_report_delete/<?= $row->id; ?>" class="btn btn-danger tooltips" data-placement="top" data-toggle="tooltip" data-original-title="Delete">
                                                    <i class="fas fa-times"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php } ?>
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

    <!-- sample modal content -->
    <div id="myModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-success">
                    <h5 class="modal-title text-white" id="myModalLabel">Select Date</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">
                <form action="<?= base_url('Brigada/brigada_daily') ?>" method="post">
                    <div class="form-group row">
                        <div class="col-lg-12">
                             <div class="row">
                                <div class="col-md-5">
                                    <label>Date From</label>
                                    <input type="date" name="date_from" class="form-control" required>
                                </div>
                                <div class="col-md-5">
                                    <label>Date To</label>
                                    <input type="date" name="date_to" class="form-control" required>
                                </div>
                                <div class="col-md-2" style="margin-top:32px;">
                                    <button type="submit" class="btn btn-success">
                                        Filter
                                    </button>
                                </div>
                            </div>
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


    <!-- ===== NEW: DPDS Report modal (select month, opens Excel-layout report in new tab) ===== -->
    <div id="dpdsModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="dpdsModalLabel" style="display: none;" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-success">
                    <h5 class="modal-title text-white" id="dpdsModalLabel">School Partnerships Data Sheet — Select Month</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">
                <!-- GET + target="_blank" => report opens in a new tab -->
                <form action="<?= base_url('Brigada/contribution_dpds_report') ?>" method="get" target="_blank">
                    <div class="form-group row">
                        <div class="col-lg-12">
                             <div class="row">
                                <div class="col-md-9">
                                    <label>Month</label>
                                    <input type="month" name="month" class="form-control" required>
                                </div>
                                <div class="col-md-3" style="margin-top:32px;">
                                    <button type="submit" class="btn btn-success">
                                        Generate
                                    </button>
                                </div>
                            </div>
                            <small class="text-muted">Shows all partner contributions for the selected month (current school year: <?= $this->session->cur_sy; ?>).</small>
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


    <!-- sample modal content -->
    <div id="renren" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-success">
                    <h5 class="modal-title text-white" id="myModalLabel">Change Fiscal Year</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">
                <form action="<?= base_url('Pages/change_sy') ?>" method="post">
                    <div class="form-group row">
                        <div class="col-lg-12">
                            <select name="new_fy" class="form-control" onchange="this.form.submit()">
                            <option disabled selected>Change School Year</option>
                            <?php
                                $currentYear = date('Y');
                                for ($y = $currentYear - 5; $y <= $currentYear + 4; $y++) :
                                    $sy = $y . '-' . ($y + 1);
                                ?>
                                    <option value="<?= $sy; ?>" <?= ($this->session->userdata('cur_fy') == $sy) ? 'selected' : ''; ?>>
                                        <?= $sy; ?>
                                    </option>
                            <?php endfor; ?>
                        </select>
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