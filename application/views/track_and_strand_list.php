

            <!-- ============================================================== -->
            <!-- Start Page Content here -->
            <!-- ============================================================== -->

            <div class="content-page">
    <div class="content">

        <!-- Start Content-->
        <div class="container-fluid">

            <!-- start page title -->
            <div class="row">
                <div class="col-md-12">
                    <div class="page-title-box">
                        <h4 class="page-title">
                        <button type="button" class="btn btn-primary waves-effect waves-light" data-toggle="modal" data-target=".bs-example-modal-lg" style="float: right;">Add New</button>
                         
                        </h4>

                        <div class="page-title-right">
                            <ol class="breadcrumb p-0 m-0">
                                <li class="breadcrumb-item">
                                    <a href="#">
                                        <!-- <span class="badge badge-purple mb-3">Currently login to <b>SY <?php echo $this->session->userdata('sy');?> <?php echo $this->session->userdata('semester');?></span></b> -->
                                    </a>
                                </li>
                            </ol>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                </div>
            </div>

            

            <!-- start row -->
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="clearfix">
                                

                                <div class="float-left">
                                    <h5 style="text-transform:uppercase">
                                        <strong>GRADE LEVEL</strong>
                                    </h5>
                                </div>

                                <div class="table-responsive">
                                    <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                        <thead>
                                            <tr>
                                                <th>Track</th>
                                                <th>Strand</th>
                                                <th style="text-align:center">Manage</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($data as $row) { ?>
                                            <tr>
                                                <td><?= $row->track; ?></td>
                                                <td><?= $row->strand; ?></td>
                                                <td>
                                                    <button type="button" class="btn btn-sm btn-primary waves-effect waves-light open-AddBookDialog" data-id="<?= $row->trackID; ?>" data-job="<?= $row->strand; ?>" data-item="<?= $row->track; ?>" data-toggle="modal" data-target=".track_update"><i class="mdi mdi-pencil"></i>Edit</button>

                                                    <a onclick="return confirm('Are you sure?')" href="<?= base_url(); ?>Sbfp/delete_track_strand/<?= $row->trackID; ?>" class="btn btn-sm btn-danger"><i class="ion ion-ios-alert"></i> Delete</a>
                                                </td>
                                            </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <div class="modal fade track_update" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="myLargeModalLabel">Update</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                </div>
                                <div class="modal-body">
                                    <form method="post" action="<?= base_url(); ?>Sbfp/track_and_strand_list">
                                        <div class="form-row align-items-center">
                                            <input type="hidden" id='id' name="id">
                                        
                                            <div class="col-md-6 mb-3">
                                                <label for="track">Track</label>
                                                <input type="text" class="form-control" id="item" name="track" required>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="strand">Strand</label>
                                                <input type="text" class="form-control" id="job" name="strand" required>
                                            </div>
                                        </div>

                                        <div class="modal-footer">
                                            <input type="submit" name="update" value="Update Data" class="btn btn-primary waves-effect waves-light" />
                                        </div>
                                    </form>
                                </div>
                                <!-- /.modal-content -->
                            </div>
                            <!-- /.modal-dialog -->
                        </div>
                    </div>

                    <!-- /.modal-content -->
                </div>
                <!-- /.modal-dialog -->
            </div>
        </div>
    </div>

    <div class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="myLargeModalLabel">Add New</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                </div>
                                <div class="modal-body">
                                    <form method="post" action="<?= base_url(); ?>Sbfp/track_and_strand_list">
                                        <div class="form-row align-items-center">
                                            
                                            <div class="col-md-6 mb-3">
                                                <label for="track">Track</label>
                                                <input type="text" class="form-control" id="track" name="track" required>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="strand">Strand</label>
                                                <input type="text" class="form-control" id="strand" name="strand" required>
                                            </div>
                                        </div>

                                        <div class="modal-footer">
                                            <input type="submit" name="save" value="Save Data" class="btn btn-primary waves-effect waves-light" />
                                        </div>
                                    </form>
                                </div>
                                <!-- /.modal-content -->
                            </div>
                            <!-- /.modal-dialog -->
                        </div>
                    </div>

                    <!-- /.modal-content -->
                </div>
                <!-- /.modal-dialog -->
            </div>
        </div>
    </div>


