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
                                    <!-- <a href="<?= $link; ?>" class="btn btn-success waves-effect width-md waves-light"><?= $pn; ?></a> -->
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
                                        <h4 class="header-title mb-4">List of Registered Applicants<br /><span class="float-left badge badge-primary inline mt-2"></span></h4><br />
                                        <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                            <!-- <table id="datatable-buttons" class="table table-striped table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;"> -->
                                            <thead>
                                                <tr>
                                                    <!-- <th>Applicant No.</th> -->
                                                    <th>Applicant Name</th>
                                                    <th>Applicant No.</th>
                                                    <th>E-Mail</th>
                                                    <th>Contact</th>
                                                    <th>Address</th>
                                                    <th>Groups</th>
                                                    <th>Specialization</th>
                                                    <th>Track</th>
                                                    <th>Preferred School</th>
                                                    <th style="text-align:center">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($data as $row) { 
                                                     $check = $this->Common->three_cond_row('hris_staff','LastName',$row->LastName,'FirstName',$row->FirstName,'MiddleName',$row->MiddleName);
                                                     if(!empty($check)){
                                                    ?>
                                                    <tr>
                                                        <td><?= $row->LastName . ', ' . $row->FirstName . ' ' . $row->MiddleName; ?> </td>
                                                        <td><?= $row->record_no; ?></td>
                                                        <td><?= $row->empEmail; ?></td>
                                                        <td><?= $row->contactNo; ?></td>
                                                        <td><?= $row->perVillage . ' ' . $row->perBarangay . ', ' . $row->perCity . ', ' . $row->perProvince; ?></td>
                                                        <td><?= $row->groups; ?></td>
                                                        <td><?= $row->specialization; ?></td>
                                                        <td><?= $row->track; ?></td>
                                                        <td><?= $row->pre_school; ?></td>
                                                        <td>
                                                            <a href="<?= base_url(); ?>registered_profile/<?php echo $row->id; ?>" class="text-success"><i class="mdi mdi-file-document-box-check-outline"></i>View Profile</a>&nbsp;&nbsp;&nbsp;&nbsp;
                                                            <a href="<?= base_url(); ?>Page/myApplications?id=<?php echo $row->empEmail; ?>" class="text-info"><i class="mdi mdi-file-document-box-check-outline"></i>Applications</a>&nbsp;&nbsp;&nbsp;&nbsp;
                                                            <a href="<?= base_url(); ?>Page/hire?id=<?php echo $row->record_no; ?>&empEmail=<?php echo $row->empEmail; ?>&bd=<?php echo $row->BirthDate; ?>" class="text-primary"><i class="mdi mdi-file-document-box-check-outline"></i>Hire</a>&nbsp;&nbsp;&nbsp;&nbsp;
                                                            <a href="<?= base_url(); ?>Pages/profile_reg_delete/<?php echo $row->id; ?>" class="text-danger"><i class="mdi mdi-file-document-box-check-outline"></i>Delete</a>
                                                        </td>
                                                    </tr>
                                                <?php } }?>
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

                <?php include('templates/footer.php'); ?>


                <!-- Modal for  -->
                <div id="appStatus" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="myModalLabel">Update Application Status</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                            </div>
                            <div class="modal-body">
                                <!-- <?= form_open('Pages/'); ?> -->
                                <form method="post">
                                    <div class="modal-body">
                                        <div class="form-group col-md-12">
                                            <label>Application Status</label>
                                            <input type="text" name="appStatus" class="form-control" value="" />
                                        </div>

                                        <div class="form-group col-md-12">
                                            <label>Notes</label>
                                            <textarea class="form-control" rows="3" name="note"></textarea>
                                        </div>


                                        <input type="hidden" name="jobID" id="id" value="">
                                        <input type="hidden" id="field" name="empEmail" class="form-control" value="" />
                                    </div>

                            </div>
                            <div class="modal-footer">
                                <input type="submit" name="submit" class="btn btn-primary waves-effect waves-light" value="Submit" />
                            </div>
                            </form>
                        </div>
                        <!-- /.modal-content -->
                    </div>
                    <!-- /.modal-dialog -->
                </div>

                <script type="text/javascript">
                    $(document).on("click", ".open-AddBookDialog", function() {
                        var myBookId = $(this).data('id');
                        $(".modal-body #id").val(myBookId);

                        var fieldId = $(this).data('field');
                        $(".modal-body #field").val(fieldId);
                    });
                </script>