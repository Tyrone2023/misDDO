<?php include('templates/head.php'); ?>

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

                                    <div class="page-title-right">

                                        <ol class="breadcrumb p-0 m-0">

                                            <li class="breadcrumb-item"><a href="#" data-toggle="modal" data-target="#myModal">Current School Year : <span class="badge badge-success"><?= $this->session->cur_sy; ?></span></a></li>

                                        </ol>

                                    </div>

                                    <h4 class="page-title">

                                        <i class="fas fa-chart-bar me-2"></i><?= $title; ?> - Administrative Dashboard

                                    </h4>

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





                        <!-- Statistics Dashboard -->

                        <?php

                        $total_fully_prepared = 0;

                        $total_partially_prepared = 0;

                        $total_not_prepared = 0;

                        $total_items = 0;

                        

                        foreach($data as $row){

                            $smcp_sub = $this->Common->one_cond('brigada_spc_items', 'spc_cat_id', $row->id);

                            foreach($smcp_sub as $srow){

                                $ivy = 1;

                                $c = $row->id.''.$ivy++;

                                $col = 'q'.$c;

                                for ($i = 1; $i <= 3; $i++) {

                                    $count = $this->Common->two_cond_count_row('brigada_spc_feedback', $col, $i,'sy',$this->session->cur_sy);

                                    if($i == 1) $total_fully_prepared += $count->num_rows();

                                    elseif($i == 2) $total_partially_prepared += $count->num_rows();

                                    elseif($i == 3) $total_not_prepared += $count->num_rows();

                                }

                                $total_items++;

                            }

                        }

                        

                        $total_responses = $total_fully_prepared + $total_partially_prepared + $total_not_prepared;

                        $fully_prepared_pct = $total_responses > 0 ? round(($total_fully_prepared / $total_responses) * 100, 1) : 0;

                        $partially_prepared_pct = $total_responses > 0 ? round(($total_partially_prepared / $total_responses) * 100, 1) : 0;

                        $not_prepared_pct = $total_responses > 0 ? round(($total_not_prepared / $total_responses) * 100, 1) : 0;

                        ?>





                        <div class="row">

                            <div class="col-xl-3 col-md-6">

                                <div class="card">

                                    <div class="card-body">

                                        <div class="d-flex align-items-center">

                                            <div class="flex-shrink-0 me-3">

                                                <div class="avatar-sm bg-success rounded">

                                                    <i class="fas fa-check-circle avatar-title text-white font-size-20"></i>

                                                </div>

                                            </div>

                                            <div class="flex-grow-1">

                                                <p class="text-muted mb-1">Fully Prepared</p>

                                                <h5 class="mb-0"><?= $total_fully_prepared; ?></h5>

                                                <div class="progress mt-2" style="height: 4px;">

                                                    <div class="progress-bar bg-success" role="progressbar" style="width: <?= $fully_prepared_pct; ?>%;" aria-valuenow="<?= $fully_prepared_pct; ?>" aria-valuemin="0" aria-valuemax="100"></div>

                                                </div>

                                                <small class="text-muted"><?= $fully_prepared_pct; ?>%</small>

                                            </div>

                                        </div>

                                    </div>

                                </div>

                            </div>

                            

                            <div class="col-xl-3 col-md-6">

                                <div class="card">

                                    <div class="card-body">

                                        <div class="d-flex align-items-center">

                                            <div class="flex-shrink-0 me-3">

                                                <div class="avatar-sm bg-warning rounded">

                                                    <i class="fas fa-exclamation-triangle avatar-title text-white font-size-20"></i>

                                                </div>

                                            </div>

                                            <div class="flex-grow-1">

                                                <p class="text-muted mb-1">Partially Prepared</p>

                                                <h5 class="mb-0"><?= $total_partially_prepared; ?></h5>

                                                <div class="progress mt-2" style="height: 4px;">

                                                    <div class="progress-bar bg-warning" role="progressbar" style="width: <?= $partially_prepared_pct; ?>%;" aria-valuenow="<?= $partially_prepared_pct; ?>" aria-valuemin="0" aria-valuemax="100"></div>

                                                </div>

                                                <small class="text-muted"><?= $partially_prepared_pct; ?>%</small>

                                            </div>

                                        </div>

                                    </div>

                                </div>

                            </div>

                            

                            <div class="col-xl-3 col-md-6">

                                <div class="card">

                                    <div class="card-body">

                                        <div class="d-flex align-items-center">

                                            <div class="flex-shrink-0 me-3">

                                                <div class="avatar-sm bg-danger rounded">

                                                    <i class="fas fa-times-circle avatar-title text-white font-size-20"></i>

                                                </div>

                                            </div>

                                            <div class="flex-grow-1">

                                                <p class="text-muted mb-1">Not Prepared</p>

                                                <h5 class="mb-0"><?= $total_not_prepared; ?></h5>

                                                <div class="progress mt-2" style="height: 4px;">

                                                    <div class="progress-bar bg-danger" role="progressbar" style="width: <?= $not_prepared_pct; ?>%;" aria-valuenow="<?= $not_prepared_pct; ?>" aria-valuemin="0" aria-valuemax="100"></div>

                                                </div>

                                                <small class="text-muted"><?= $not_prepared_pct; ?>%</small>

                                            </div>

                                        </div>

                                    </div>

                                </div>

                            </div>

                            

                            <div class="col-xl-3 col-md-6">

                                <div class="card">

                                    <div class="card-body">

                                        <div class="d-flex align-items-center">

                                            <div class="flex-shrink-0 me-3">

                                                <div class="avatar-sm bg-info rounded">

                                                    <i class="fas fa-chart-pie avatar-title text-white font-size-20"></i>

                                                </div>

                                            </div>

                                            <div class="flex-grow-1">

                                                <p class="text-muted mb-1">Total Responses</p>

                                                <h5 class="mb-0"><?= $total_responses; ?></h5>

                                                <div class="progress mt-2" style="height: 4px;">

                                                    <div class="progress-bar bg-info" role="progressbar" style="width: 100%;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>

                                                </div>

                                                <small class="text-muted"><?= $total_items; ?> items</small>

                                            </div>

                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>





                        <!-- Overall Progress Chart -->

                        <div class="row">

                            <div class="col-12">

                                <div class="card">

                                    <div class="card-header bg-primary text-white">

                                        <h5 class="card-title mb-0">

                                            <i class="fas fa-chart-bar me-2"></i>Overall Preparedness Overview

                                        </h5>

                                    </div>

                                    <div class="card-body">

                                        <div class="row">

                                            <div class="col-md-8">

                                                <div class="progress" style="height: 30px;">

                                                    <div class="progress-bar bg-success" role="progressbar" style="width: <?= $fully_prepared_pct; ?>%;" aria-valuenow="<?= $fully_prepared_pct; ?>" aria-valuemin="0" aria-valuemax="100">

                                                        <?= $fully_prepared_pct; ?>% Fully Prepared

                                                    </div>

                                                    <div class="progress-bar bg-warning" role="progressbar" style="width: <?= $partially_prepared_pct; ?>%;" aria-valuenow="<?= $partially_prepared_pct; ?>" aria-valuemin="0" aria-valuemax="100">

                                                        <?= $partially_prepared_pct; ?>% Partially

                                                    </div>

                                                    <div class="progress-bar bg-danger" role="progressbar" style="width: <?= $not_prepared_pct; ?>%;" aria-valuenow="<?= $not_prepared_pct; ?>" aria-valuemin="0" aria-valuemax="100">

                                                        <?= $not_prepared_pct; ?>% Not Prepared

                                                    </div>

                                                </div>

                                            </div>

                                            <div class="col-md-4">

                                                <div class="d-flex justify-content-around">

                                                    <div class="text-center">

                                                        <div class="badge bg-success p-2 mb-1">

                                                            <i class="fas fa-check-circle me-1"></i><?= $total_fully_prepared; ?>

                                                        </div>

                                                        <small class="d-block text-muted">Fully</small>

                                                    </div>

                                                    <div class="text-center">

                                                        <div class="badge bg-warning p-2 mb-1">

                                                            <i class="fas fa-exclamation-triangle me-1"></i><?= $total_partially_prepared; ?>

                                                        </div>

                                                        <small class="d-block text-muted">Partially</small>

                                                    </div>

                                                    <div class="text-center">

                                                        <div class="badge bg-danger p-2 mb-1">

                                                            <i class="fas fa-times-circle me-1"></i><?= $total_not_prepared; ?>

                                                        </div>

                                                        <small class="d-block text-muted">Not</small>

                                                    </div>

                                                </div>

                                            </div>

                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>





                        <?php $att = array('class' => 'parsley-examples'); ?>

                        <?= form_open('Brigada/brigada_spc', $att); ?>





                        <?php if (!empty($existing)) : ?>

                            <input type="hidden" name="feedback_id" value="<?= $existing[0]->id ?>">

                        <?php endif; ?>





                        <div class="row">

                            <div class="col-12">

                                <div class="card">

                                    <div class="card-header bg-dark text-white">

                                        <h5 class="card-title mb-0">

                                            <i class="fas fa-list-alt me-2"></i>Detailed Category Reports

                                        </h5>

                                    </div>

                                    <div class="card-body">





                        <div class="row">

                            <div class="col-lg-12">

                                <div id="accordion" class="custom-accordion">





                                <?php $count=1; foreach($data as $row){ 

                                    // Calculate category statistics

                                    $cat_fully = 0;

                                    $cat_partially = 0;

                                    $cat_not = 0;

                                    $smcp_sub = $this->Common->one_cond('brigada_spc_items', 'spc_cat_id', $row->id);

                                    foreach($smcp_sub as $srow){

                                        $ivy = 1;

                                        $c = $row->id.''.$ivy++;

                                        $col = 'q'.$c;

                                        for ($i = 1; $i <= 3; $i++) {

                                            $count_result = $this->Common->two_cond_count_row('brigada_spc_feedback', $col, $i,'sy',$this->session->cur_sy);

                                            if($i == 1) $cat_fully += $count_result->num_rows();

                                            elseif($i == 2) $cat_partially += $count_result->num_rows();

                                            elseif($i == 3) $cat_not += $count_result->num_rows();

                                        }

                                    }

                                    $cat_total = $cat_fully + $cat_partially + $cat_not;

                                    $cat_fully_pct = $cat_total > 0 ? round(($cat_fully / $cat_total) * 100, 1) : 0;

                                ?>

                                    <div class="accordion-item mb-3">

                                        <div class="accordion-header" id="heading<?= $row->id; ?>">

                                            <button class="accordion-button <?= $row->id != 1 ? 'collapsed' : ''; ?>" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?= $row->id; ?>" aria-expanded="<?= $row->id == 1 ? 'true' : 'false'; ?>" aria-controls="collapse<?= $row->id; ?>">

                                                <div class="d-flex w-100 justify-content-between align-items-center">

                                                    <span>

                                                        <i class="fas fa-folder me-2"></i><?= $count++.'. '. htmlspecialchars($row->name, ENT_QUOTES, 'UTF-8'); ?>

                                                    </span>

                                                    <div class="progress" style="width: 200px; height: 20px;">

                                                        <div class="progress-bar bg-success" style="width: <?= $cat_fully_pct; ?>%;" title="<?= $cat_fully_pct; ?>% Fully Prepared"></div>

                                                        <div class="progress-bar bg-warning" style="width: <?= $cat_total > 0 ? round(($cat_partially / $cat_total) * 100, 1) : 0; ?>%;" title="<?= $cat_total > 0 ? round(($cat_partially / $cat_total) * 100, 1) : 0; ?>% Partially"></div>

                                                        <div class="progress-bar bg-danger" style="width: <?= $cat_total > 0 ? round(($cat_not / $cat_total) * 100, 1) : 0; ?>%;" title="<?= $cat_total > 0 ? round(($cat_not / $cat_total) * 100, 1) : 0; ?>% Not Prepared"></div>

                                                    </div>

                                                    <span class="badge bg-primary ms-2"><?= $cat_total; ?> responses</span>

                                                </div>

                                            </button>

                                        </div>





                                        <div id="collapse<?= $row->id; ?>" class="accordion-collapse collapse <?= $row->id == 1 ? 'show' : ''; ?>" aria-labelledby="heading<?= $row->id; ?>" data-bs-parent="#accordion">

                                            

                                        <div class="accordion-body">

                                            <!-- Category Summary -->

                                            <div class="row mb-3">

                                                <div class="col-md-12">

                                                    <div class="d-flex justify-content-around text-center">

                                                        <div class="px-3">

                                                            <div class="badge bg-success p-2 mb-1">

                                                                <i class="fas fa-check-circle me-1"></i><?= $cat_fully; ?>

                                                            </div>

                                                            <small class="d-block text-muted">Fully Prepared</small>

                                                        </div>

                                                        <div class="px-3">

                                                            <div class="badge bg-warning p-2 mb-1">

                                                                <i class="fas fa-exclamation-triangle me-1"></i><?= $cat_partially; ?>

                                                            </div>

                                                            <small class="d-block text-muted">Partially Prepared</small>

                                                        </div>

                                                        <div class="px-3">

                                                            <div class="badge bg-danger p-2 mb-1">

                                                                <i class="fas fa-times-circle me-1"></i><?= $cat_not; ?>

                                                            </div>

                                                            <small class="d-block text-muted">Not Prepared</small>

                                                        </div>

                                                    </div>

                                                </div>

                                            </div>





                                            <!-- Detailed Table -->

                                            <div class="table-responsive">

                                                <table class="table table-striped table-hover">

                                                    <thead class="table-dark">

                                                        <tr>

                                                            <th>ITEM</th>

                                                            <th class="text-center">

                                                                <i class="fas fa-check-circle me-1"></i>Fully Prepared

                                                                <br><small>100% compliance</small>

                                                            </th>

                                                            <th class="text-center">

                                                                <i class="fas fa-exclamation-triangle me-1"></i>Partially Prepared

                                                                <br><small>Partial compliance</small>

                                                            </th>

                                                            <th class="text-center">

                                                                <i class="fas fa-times-circle me-1"></i>Not Prepared

                                                                <br><small>No compliance</small>

                                                            </th>

                                                            <th class="text-center">Actions</th>

                                                        </tr>

                                                    </thead>

                                                    <tbody>

                                                        <?php $ivy=1;  $r=0; foreach($smcp_sub as $srow){ 

                                                            $c = $row->id.''.$ivy++;

                                                            $feedback = !empty($existing) ? (array)$existing[0] : [];

                                                            $val = isset($feedback['q'.$c]) ? $feedback['q'.$c] : '';

                                                            $remarksVal = isset($feedback['r' . $c]) ? $feedback['r' . $c] : '';

                                                            $col = 'q'.$c;

                                                            for ($i = 1; $i <= 3; $i++) {

                                                                ${"fp$i"} = $this->Common->two_cond_count_row('brigada_spc_feedback', $col, $i,'sy',$this->session->cur_sy);

                                                            }

                                                            

                                                            $item_total = ${"fp1"}->num_rows() + ${"fp2"}->num_rows() + ${"fp3"}->num_rows();

                                                            $item_fully_pct = $item_total > 0 ? round((${"fp1"}->num_rows() / $item_total) * 100, 1) : 0;

                                                        ?>

                                                        <tr class="<?= (++$r%2 ? "" : "table-active"); ?>">

                                                            <td>

                                                                <strong><?= htmlspecialchars($srow->description, ENT_QUOTES, 'UTF-8'); ?></strong>

                                                                <div class="progress mt-1" style="height: 4px;">

                                                                    <div class="progress-bar bg-success" style="width: <?= $item_fully_pct; ?>%;"></div>

                                                                </div>

                                                            </td>

                                                            <td class="text-center">

                                                                <a href="<?= base_url(); ?>Brigada/spc_feedback/1/<?= $srow->id; ?>" 

                                                                   class="badge bg-success p-2" 

                                                                   title="View fully prepared schools">

                                                                    <i class="fas fa-eye me-1"></i><?= ${"fp1"}->num_rows(); ?>

                                                                </a>

                                                            </td>

                                                            <td class="text-center">

                                                                <a href="<?= base_url(); ?>Brigada/spc_feedback/2/<?= $srow->id; ?>" 

                                                                   class="badge bg-warning p-2" 

                                                                   title="View partially prepared schools">

                                                                    <i class="fas fa-eye me-1"></i><?= ${"fp2"}->num_rows(); ?>

                                                                </a>

                                                            </td>

                                                            <td class="text-center">

                                                                <a href="<?= base_url(); ?>Brigada/spc_feedback/3/<?= $srow->id; ?>" 

                                                                   class="badge bg-danger p-2" 

                                                                   title="View not prepared schools">

                                                                    <i class="fas fa-eye me-1"></i><?= ${"fp3"}->num_rows(); ?>

                                                                </a>

                                                            </td>

                                                            <td class="text-center">

                                                                <div class="btn-group" role="group">

                                                                    <button class="btn btn-sm btn-info" 

                                                                            onclick="showItemDetails('<?= $srow->id; ?>', '<?= htmlspecialchars($srow->description, ENT_QUOTES, 'UTF-8'); ?>')"

                                                                            title="View Details">

                                                                        <i class="fas fa-chart-line"></i>

                                                                    </button>

                                                                    <?php if(${"fp3"}->num_rows() > 0): ?>

                                                                    <button class="btn btn-sm btn-warning" 

                                                                            onclick="sendItemReminder('<?= htmlspecialchars($srow->description, ENT_QUOTES, 'UTF-8'); ?>')"

                                                                            title="Send Reminder">

                                                                        <i class="fas fa-bell"></i>

                                                                    </button>

                                                                    <?php endif; ?>

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

                                <?php } ?>





                                </div>

                            </div>

                        </div>

                        <!-- end row -->

                         <?php if($this->session->position == 'School'){?>

                        <div class="form-group text-left mb-0">

                            <input type="submit" name="submit" value="Submit" class="btn btn-primary waves-effect waves-light mr-1">

                        </div>

                        <?php } ?>





                                    </div>

                                </div>

                            </div>

                        </div>





                        </form>





                    </div>

                    <!-- end container-fluid -->





                </div>

                <!-- end content -->





                <!-- sample modal content -->

                                        <div id="myModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">

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



                







                <script>

                // Get all the checkboxes with the same name

                const checkboxes = document.querySelectorAll('input[name="option"]');



                checkboxes.forEach((checkbox) => {

                    checkbox.addEventListener('change', () => {

                        // If a checkbox is checked, uncheck others in the same group

                        checkboxes.forEach((otherCheckbox) => {

                            if (otherCheckbox !== checkbox) {

                                otherCheckbox.checked = false;

                            }

                        });

                    });

                });

            </script>                           











            <?php include('templates/footer.php'); ?>





            