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

                        <div class="clearfix"></div>
                    </div>
                </div>
            </div>
            <!-- end page title -->

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body table-responsive">
                            <h4 class="header-title mb-4">Travel Requests</h4>


                            <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                <thead>
                                    <tr>
                                        <!-- <th>ID</th> -->
                                         <th>Fullname</th>
                                        <th>Purpose</th>
                                        <th>Destination</th>
                                        <th>Inclusive Dates</th>
                                        <th>Status</th>
                                        <th>Date Encoded</th>
                                        <th>Travel Type</th>
                                        <th>Attachment</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($requests as $row): ?>
                                        <?php $staff = $this->Common->one_cond_row_select('hris_staff','IDNumber,FirstName,MiddleName,LastName','IDNumber',$row->IDNumber); ?>
                                        <tr>
                                            <!-- <td><?= $row->id ?></td> -->
                                            <td><?= $staff->LastName.', '.$staff->FirstName; ?></td>
                                            <td><?= $this->TravelModel->add_br_every_words($row->purpose, 10); ?></td>
                                            <td><?= $row->destination ?></td>
                                            <td><?= $row->inclusive_date ?></td>
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
                                                   <?php if($row->ivy == 1){?>
                                                    <a onclick="return confirm('Are you sure?')" href="<?= site_url('travel/action_travel/' . $row->id) ?>/Approved" class="btn btn-sm btn-primary">Approve</a>
                                                   <?php }else{?>
                                                   <a onclick="return confirm('Are you sure?')" href="<?= site_url('travel/action_travel/' . $row->id) ?>/Recommended" class="btn btn-sm btn-primary">Recommend</a>
                                                   <?php } ?>

                                                <?php if($row->IDNumber == $this->session->username){?>
                                                    <a href="#" class="btn btn-sm btn-danger open-AddBookDialog" data-id="<?= $row->id; ?>" data-toggle="modal" data-target="#myModal"><i class="on ion-ios-trash"></i></a>
                                                <?php }else{ ?>
                                                    <button type="button" class="btn btn-danger btn-sm waves-effect waves-light open-AddBookDialog" data-id="<?= $row->id; ?>" data-toggle="modal" data-target="#myModal">Disapprove</button>
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

    <!--  modal content -->
                                        <div id="myModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-danger">
                                                        <h5 class="modal-title text-white" id="myModalLabel">Disapproved the travel authorization</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                    </div>
                                                    <div class="modal-body">
                                                    <?= form_open('Travel/action_travel_reject'); ?>
                                                        <input type="hidden" value="" name="travel_req_id" id="id">
                                                        <input type="hidden" value="Rejected" name="stat">


                                                        <div class="row">
                                                            <div class="col-lg-12">
                                                                <div class="form-group">
                                                                    <label>Remarks</label>
                                                                    <textarea name="remarks" class="form-control"></textarea>
                                                                </div>	
                                                            </div>	              
                                                        </div>

                                                        </div>
                                                        <div class="modal-footer">
                                                            <input type="submit" class="btn btn-danger waves-effect waves-light" value="Submit" />
                                                        </div>
                                                    </form>
                                                </div>
                                                <!-- /.modal-content -->
                                            </div>
                                            <!-- /.modal-dialog -->
                                        </div>
                                        <!-- /.modal -->


    <?php include('templates/footer.php'); ?>