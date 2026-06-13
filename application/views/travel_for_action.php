
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
                        <a href="<?= site_url('travel/create') ?>" class="btn btn-primary">Add New Request</a>

                        <div class="clearfix"></div>
                    </div>
                </div>
            </div>
            <!-- end page title -->
             

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body table-responsive">
                            <h4 class="header-title mb-4"><?= $this->uri->segment(3) == 0 ? "Local" : "Outside the Division"; ?> Travel Requests</h4>


                            <table class="table mb-0">
                                <thead>
                                    <tr>
                                        <th>Purpose</th>
                                        <th>Destination</th>
                                        <th>Inclusive Date</th>
                                        <th>Status</th>
                                        <th>Date Encoded</th>
                                        <th>Attachment</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($data as $row){  ?>
                                     <tr>
                                        <td><?= $row->purpose; ?></td>
                                        <td><?= $row->destination; ?></td>
                                        <td><?= $row->inclusive_date ?> <?= $user->position; ?></td>
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

                                        <td>
                                            <div class="button-list">
                                                <?php if($row->file_url != ''){?>
                                                    <a href="<?= base_url(); ?>uploads/travel/<?= $row->file_url; ?>" target="_blank" class="btn btn-sm btn-purple"><i class="ion ion-md-attach" ></i> View</a>
                                                <?php } ?>
                                                </div>
                                        </td>

                                        <!-- <?php if ($user->position == 0){ ?>
                                        <td>
                                            <div class="button-list">
                                                <a href="<?= site_url('travel/travel_print_view/' . $row->id) ?>" class="btn btn-sm btn-success" target="_blank"><i class="fa fa-print"></i></a>
                                                <a onclick="return confirm('Are you sure?')" href="<?= site_url('travel/action_travel/' . $row->id) ?>/Reviewed" class="btn btn-sm btn-primary">Reviewed</a>
                                                <button type="button" class="btn btn-danger btn-sm waves-effect waves-light open-AddBookDialog" data-id="<?= $row->id; ?>" data-toggle="modal" data-target="#myModal">Reject</button>
                                            </div>
                                        </td>
                                        <?php } ?>

                                        <?php if($user->position == 1){ ?>
                                        <td>
                                            <div class="button-list">
                                                <a href="<?= site_url('travel/travel_print_view/' . $row->id) ?>" class="btn btn-sm btn-success" target="_blank"><i class="fa fa-print"></i></a>
                                                <a onclick="return confirm('Are you sure?')" href="<?= site_url('travel/action_travel/' . $row->id) ?>/Endorsed" class="btn btn-sm btn-primary">Endorsed</a>
                                                <button type="button" class="btn btn-danger btn-sm waves-effect waves-light open-AddBookDialog" data-id="<?= $row->id; ?>" data-toggle="modal" data-target="#myModal">Reject</button>
                                            </div>
                                        </td>
                                        
                                        <?php } ?>

                                        <?php if($user->position == 2){ ?>
                                        <td>
                                            <div class="button-list">
                                                <a href="<?= site_url('travel/travel_print_view/' . $row->id) ?>" class="btn btn-sm btn-success" target="_blank"><i class="fa fa-print"></i></a>
                                                <a onclick="return confirm('Are you sure?')" href="<?= site_url('travel/action_travel/' . $row->id) ?>/Approved" class="btn btn-sm btn-primary">Approved</a>
                                                <button type="button" class="btn btn-danger btn-sm waves-effect waves-light open-AddBookDialog" data-id="<?= $row->id; ?>" data-toggle="modal" data-target="#myModal">Reject</button>
                                            </div>
                                        </td>
                                        <?php } ?> -->

                                        <td>
                                            <div class="button-list">
                                            <a href="<?= site_url('travel/travel_print_view/' . $row->id) ?>" class="btn btn-sm btn-success" target="_blank"><i class="fa fa-print"></i></a>
                                            <?php if($this->uri->segment(3) == 0){
                                                if($user->position == 2){
                                                    if($row->status == 'Pending'){
                                                ?>
                                                    <a onclick="return confirm('Are you sure?')" href="<?= site_url('travel/action_travel/' . $row->id) ?>/Approved" class="btn btn-sm btn-primary">Approved</a>
                                                    <button type="button" class="btn btn-danger btn-sm waves-effect waves-light open-AddBookDialog" data-id="<?= $row->id; ?>" data-toggle="modal" data-target="#myModal">Reject</button>
                                                

                                            <?php } } }?>

                                            <?php if($this->uri->segment(3) == 1){
                                                if($user->position == 1){
                                                    if($row->status == 'Pending'){
                                                ?>
                                                <a onclick="return confirm('Are you sure?')" href="<?= site_url('travel/action_travel/' . $row->id) ?>/Endorsed" class="btn btn-sm btn-primary">Endorsed</a>
                                                <button type="button" class="btn btn-danger btn-sm waves-effect waves-light open-AddBookDialog" data-id="<?= $row->id; ?>" data-toggle="modal" data-target="#myModal">Reject</button>
                                                

                                            <?php } } }?>


                                            <?php if($this->uri->segment(3) == 0){
                                                if($user->position == 0){
                                                    if($row->status == 'Pending'){
                                                ?>
                                                    <a onclick="return confirm('Are you sure?')" href="<?= site_url('travel/action_travel/' . $row->id) ?>/Reviewed" class="btn btn-sm btn-primary">Reviewed</a>
                                                <button type="button" class="btn btn-danger btn-sm waves-effect waves-light open-AddBookDialog" data-id="<?= $row->id; ?>" data-toggle="modal" data-target="#myModal">Reject</button>
                                                

                                            <?php } } }?>

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

                                        <!--  modal content -->
                                        <div id="myModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-danger">
                                                        <h5 class="modal-title text-white" id="myModalLabel">Reject the travel authorization</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                    </div>
                                                    <div class="modal-body">
                                                    <?= form_open_multipart('Travel/action_travel_reject'); ?>
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

                                    