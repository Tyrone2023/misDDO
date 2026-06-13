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

                            <a href="#" class="waves-effect waves-light openModalBtn text-white btn-sm btn btn-info" data-toggle="modal" data-target="#myModal">Change Date</a><br />
                            <span class="badge badge-purple">Current Date : <?= $this->session->cur_month; ?>-<?= $this->session->cur_fy; ?></span>

                        <div class="clearfix"></div>
                    </div>
                </div>
            </div>
            <!-- end page title -->

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body table-responsive">

                            <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                <thead>
                                    <tr>
                                        <th>Fullname</th>
                                        <th>Position</th>
                                        <th>Inclusive Dates</th>
                                        <th>Purpose</th>
                                        <th>Destination</th>
                                        <th>Status</th>
                                        <th>Date Encoded</th>
                                        <th>Travel Type</th>
                                        <th>Attachment</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($requests as $row): ?>
                                        <tr>
                                            <td><?= strtoupper($row->LastName).', '.strtoupper($row->FirstName); ?></td>
                                            <td><?= $row->empPosition; ?></td>
                                            <td><?= $row->inclusive_date ?></td>
                                            <td><?= $this->TravelModel->add_br_every_words($row->purpose, 10); ?></td>
                                            <td><?= $row->destination ?></td>
                                            <td>
                                                <?php
                                                $status = $row->status;
                                                $class = '';
                                                switch ($status) {
                                                    case 'Pending':
                                                        $class = 'badge badge-warning'; // yellow/orange
                                                        break;
                                                    case 'Approved':
                                                        $class = 'badge badge-success'; // green
                                                        break;
                                                    case 'Returned':
                                                        $class = 'badge badge-danger'; // red
                                                        break;
                                                    default:
                                                        $class = 'badge badge-secondary'; // grey
                                                }
                                                ?>
                                                <span class="<?= $class ?>"><?= $status ?></span>
                                            </td>

                                            <td><?= $row->date_created ?></td>
                                            <td><?= $row->ttype == 0 ? '<span class="badge badge-purple">Within the Division</span>' : '<span class="badge badge-info">Outside Division</span>'; ?></td>
                                            <td>
                                                <div class="button-list">
                                                <?php if($row->file_url != ''){?>
                                                    <a href="<?= base_url(); ?>uploads/travel/<?= $row->file_url; ?>" target="_blank" class="btn btn-sm btn-purple"><i class="ion ion-md-attach" ></i> View</a>
                                                <?php } ?>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="button-list">

                                                <a href="<?= site_url('travel/travel_print_view/' . $row->id) ?>" class="btn btn-sm btn-success" target="_blank"><i class="fa fa-print"></i></a>
                                                
                                                
                                                <?php if($row->status == 'Endorsed'){ ?>
                                                   <a onclick="return confirm('Are you sure?')" href="<?= site_url('travel/action_travel/' . $row->id) ?>/Recommended" class="btn btn-sm btn-primary" target="_blank">Recommended</a>
                                               

                                                <?php if($row->IDNumber == $this->session->username){?>
                                                    <a href="#" class="btn btn-sm btn-danger open-AddBookDialog" data-id="<?= $row->id; ?>" data-toggle="modal" data-target="#myModal"><i class="on ion-ios-trash"></i></a>
                                                <?php }else{ ?>
                                                    <button type="button" class="btn btn-danger btn-sm waves-effect waves-light open-AddBookDialog" data-id="<?= $row->id; ?>" data-toggle="modal" data-target="#myModal">Reject</button>
                                                <?php } } ?>
                                               

                                            </div>
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


    <!-- sample modal content -->
                                        <div id="myModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content modal-lg">
                                                    <div class="modal-header bg-info">
                                                        <h5 class="modal-title" id="modalTitle">Select Date</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                    </div>
                                                    <div class="modal-body">

                                                    <div class="row">
                                                        <div class="col-12">
                                                            <div class="">
                                                                    <?= validation_errors(); ?>
                                                                    <?php
                                                                    $attributes = array('class' => 'parsley-examples form-horizontal');
                                                                    echo form_open('Provident/change_fy_and_m', $attributes);
                                                                    ?>

                                                                    


                                                                    <input type="hidden" class="form-control" value="0007" name="deduction_code">


                                                                    <div class="form-group row">
                                                                        <label class="col-lg-4 col-form-label">Month and Year</label>
                                                                        <div class="col-lg-4">
                                                                            <label class="col-lg-4 col-form-label"></label>
                                                                            <label class="col-lg-4 col-form-label">Month</label>
                                                                            <select name="month" class="form-control" required>
                                                                                <?php
                                                                                date_default_timezone_set('Asia/Manila');
                                                                                $months = [
                                                                                    '01' => 'January',  '02' => 'February', '03' => 'March',    '04' => 'April',
                                                                                    '05' => 'May',      '06' => 'June',     '07' => 'July',     '08' => 'August',
                                                                                    '09' => 'September','10' => 'October',  '11' => 'November', '12' => 'December'
                                                                                ];

                                                                                foreach ($months as $num => $name) {
                                                                                    echo "<option ";
                                                                                    if(date("m") == $num){echo " selected ";}
                                                                                    echo " value=\"$num\">$name</option>";
                                                                                }
                                                                                ?>
                                                                                </select>
                                                                        </div>
                                                                        <div class="col-lg-4">
                                                                            <label class="col-lg-4 col-form-label">Year</label>
                                                                                <?php
                                                                                $currentYear = date("Y");
                                                                                $fiscalStartYear = $currentYear - 10;
                                                                                $fiscalEndYear = $currentYear + 30;

                                                                                ?>
                                                                                <select name="fy" class="form-control" required>
                                                                                    <option value="" disabled selected>Select Fiscal Year</option>
                                                                                    <?php for ($year = $fiscalStartYear; $year <= $fiscalEndYear; $year++) { ?>
                                                                                    <option <?php if(date('Y') == $year){echo ' selected ';}?> value="<?= $year; ?>"><?= $year; ?></option>
                                                                                    <?php } ?>
                                                                                </select>
                                                                        </div>
                                                                    </div>


                                                                
                                                            </div>
                                                        </div>

                                                    </div>
                                                    <!-- end row -->
                                                        
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="submit" class="btn btn-info waves-effect waves-light">Submit</button>
                                                    </div>
                                                    </form>
                                                </div>
                                                <!-- /.modal-content -->
                                            </div>
                                            <!-- /.modal-dialog -->
                                        </div>
                                        <!-- /.modal -->

    <?php include('templates/footer.php'); ?>