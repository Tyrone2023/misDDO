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

                                    <?php if ($this->session->userdata('position') === 'Admin') : ?>

                                        <button type="button" class="btn btn-primary waves-effect waves-light" data-toggle="modal" data-backdrop="static" data-keyboard="false" data-target=".bs-example-modal-lg">Add New</button>
                                        <button type="button" class="btn btn-success waves-effect waves-light">Download the Announcement Template Here</button>

                                    <?php endif; ?>

                                    <div class="clearfix"></div>
                                </div>
                            </div>
                        </div>
                        <!-- end page title -->

                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body table-responsive">
                                        <h4 class="header-title mb-4">announcements</h4>

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
                                        <!-- <a href="monthlyLeaveCredits"><button type="button" class="btn btn-info">Generate Monthly Leave Credits</button></a> -->
                                        <br /> <br />
                                        <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">

                                            <thead>
                                                <tr>
                                                    <th>Title</th>
                                                    <th>Announcement</th>
                                                    <th>File</th>
                                                    <th>Status</th>
                                                    <th style='text-align:center'>Manage</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $i = 1;
                                                foreach ($data as $row) {
                                                    echo "<tr>";
                                                    echo "<td>" . $row->title . "</td>";
                                                    echo "<td>" . $row->announcement . "</td>";
                                                    echo "<td>" . $row->fileAttachment . "</td>";
                                                    echo "<td>" . $row->a_stat . "</td>";

                                                ?>

                                                    <td style='text-align:center'>
                                                        <?php if ($this->session->userdata('position') === 'Admin') : ?>
                                                            <!-- <a href="<?= base_url(); ?>Page/id=<?= $row->id; ?>" data-id="<?= $row->id; ?>" data-announcement="<?= $row->announcement; ?>" data-sltotal="<?= $row->a_stat; ?>" class="text-success open-AddBookDialog" data-toggle="modal"  data-target=".lcupdate"><i class="mdi mdi-file-document-box-check-outline"></i>Update</a>&nbsp;&nbsp;&nbsp;&nbsp; -->
                                                            <a href="<?= base_url(); ?>uploads/announcements/<?= $row->fileAttachment; ?>" target="_blank" class="text-info"><i class="mdi mdi-file-document-box-check-outline"></i>View File</a>&nbsp;&nbsp;&nbsp;&nbsp;
                                                            <a href="<?= base_url(); ?>Page/del_announcements/<?= $row->id; ?>" class="text-danger" onclick="return confirm('Are you sure you want to delete this item?')" ;><i class="mdi mdi-file-document-box-check-outline"> </i>Delete</a>

                                                        <?php endif; ?>
                                                    </td>

                                                <?php echo "</tr>";
                                                } ?>
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




                <div class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" style="display: none;" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="myLargeModalLabel">Announcement</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                            </div>
                            <div class="modal-body">
                                <?= form_open_multipart('Page/addAnnouncement'); ?>
                                <div class="form-row">
                                    <div class="form-group col-md-12">
                                        <label for="inputEmail4" class="col-form-label">Title</label>
                                        <input type="text" class="form-control" value="<?= set_value('title'); ?>" id="title" required name="title">
                                    </div>

                                </div>

                                <div class="form-row">
                                    <div class="form-group col-md-12">
                                        <label for="inputEmail4" class="col-form-label">Announcement Details</label>
                                        <textarea class="form-control" rows="5" name="announcement" required></textarea>
                                    </div>


                                </div>

                                <div class="form-row">

                                    <div class="form-group col-md-12">
                                        <label for="inputEmail4" class="col-form-label">File Attachment <span style="color:red">PNG or JPG only</span></label>
                                        <input type="file" class="form-control" required name="fileAttachment">
                                    </div>


                                </div>

                            </div>
                            <div class="modal-footer">
                                <button type="submit" name="submit" class="btn btn-primary waves-effect waves-light">Submit</button>
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




            <!-- Leave Credits Update -->
            <div class="modal fade lcupdate" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" style="display: none;" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="myLargeModalLabel">Announcement</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        </div>
                        <div class="modal-body">
                            <form method="post">

                                <div class="modal-body">

                                    <div class="form-row">
                                        <div class="form-group col-md-12">
                                            <label for="inputEmail4" class="col-form-label">Title</label>
                                            <input type="text" class="form-control" value="<?= set_value('title'); ?>" id="title" required name="title">
                                        </div>

                                    </div>

                                    <div class="form-row">
                                        <div class="form-group col-md-12">
                                            <label for="inputEmail4" class="col-form-label">Announcement</label>
                                            <input type="text" class="form-control" value="<?= set_value('announcement'); ?>" id="announcement" required name="announcement">
                                        </div>

                                    </div>

                                    <div class="form-row">
                                        <div class="form-group col-md-12">
                                            <label for="inputEmail4" class="col-form-label">File Attachment</label>
                                            <input type="text" class="form-control" value="<?= set_value('fileAttachment'); ?>" id="fileAttachment" required name="fileAttachment">
                                        </div>

                                    </div>

                                    <input type="hidden" name="ID" id="id" value="">
                                </div>

                        </div>
                        <div class="modal-footer">
                            <input type="submit" name="submit" value="Submit" class="btn btn-primary waves-effect waves-light">
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





            <?php include('templates/footer.php'); ?>
            <script type="text/javascript">
                $(document).on("click", ".open-AddBookDialog", function() {
                    var id = $(this).data('id');
                    $(".modal-body #id").val(id);

                    var announcement = $(this).data('announcement');
                    $(".modal-body #announcement").val(announcement);

                    var fileAttachment = $(this).data('fileAttachment');
                    $(".modal-body #fileAttachment").val(fileAttachment);

                    var coctotal = $(this).data('coctotal');
                    $(".modal-body #coctotal").val(coctotal);


                });
            </script>