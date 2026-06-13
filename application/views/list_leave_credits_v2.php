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

                                    <?php if ($this->session->userdata('position') === 'Admin'): ?>
                                        <a href="<?= base_url(); ?>Page/leaveCreditsUploading" class="btn btn-success waves-effect width-md waves-light">Upload Leave Credits Summary</a>
                                        <button type="button" class="btn btn-primary waves-effect waves-light" data-toggle="modal" data-backdrop="static" data-keyboard="false" data-target=".bs-example-modal-lg">Add New</button>

                                    <?php elseif ($this->session->userdata('position') === 'Staff'): ?>
                                        <a href="<?= base_url(); ?>Page/leaveCreditsUploading" class="btn btn-success waves-effect width-md waves-light">Upload Leave Credits Summary</a>
                                        <button type="button" class="btn btn-primary waves-effect waves-light" data-toggle="modal" data-backdrop="static" data-keyboard="false" data-target=".bs-example-modal-lg">Add New</button>
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
                                        <h4 class="header-title mb-4">Leave Credits Summary</h4>

                                        <?php if (!empty($success_message)): ?>
                                            <script>
                                                alert("<?= $success_message; ?>");
                                            </script>
                                        <?php endif; ?>

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
                                        <!-- <a href="CalleaveCreditsSummary"><button type="button" class="btn btn-info">Compute</button></a> -->

                                        <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">

                                            <thead>
                                                <tr>
                                                    <th>Last Name</th>
                                                    <th>First Name</th>
                                                    <th>Middle Name</th>
                                                    <th>Employee No.</th>
                                                    <th>Position</th>
                                                    <th style='text-align:center'>VL Total</th>
                                                    <th style='text-align:center'>SL Total</th>
                                                    <th style='text-align:center'>COC/Service Credits</th>
                                                    <th style='text-align:center'>Manage</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $i = 1;
                                                foreach ($data as $row) {
                                                    echo "<tr>";
                                                    echo "<td>" . $row->LastName . "</td>";
                                                    echo "<td>" . $row->FirstName . "</td>";
                                                    echo "<td>" . $row->MiddleName . "</td>";
                                                    echo "<td>" . $row->IDNumber . "</td>";
                                                    echo "<td>" . $row->empPosition . "</td>";
                                                    echo "<td style='text-align:center'>" . $row->vlTotal . "</td>";
                                                    echo "<td style='text-align:center'>" . $row->slTotal . "</td>";
                                                    echo "<td style='text-align:center'>" . $row->cocTotal . "</td>";

                                                ?>

                                                    <td style='text-align:center'>
                                                    <?php if (in_array($this->session->userdata('position'), ['Admin', 'Super Admin', 'HR Staff', 'Human Resource Admin'])): ?>
                                                            <a href="<?= base_url(); ?>Page/id=<?= $row->ID; ?>" data-id="<?= $row->ID; ?>" data-vltotal="<?= $row->vlTotal; ?>" data-sltotal="<?= $row->slTotal; ?>" data-coctotal="<?= $row->cocTotal; ?>" class="text-success open-AddBookDialog" data-toggle="modal" data-target=".lcupdate"><i class="mdi mdi-file-document-box-check-outline"></i>Update</a>&nbsp;&nbsp;&nbsp;&nbsp;
                                                            <a href="<?= base_url(); ?>Page/delete_lc?id=<?= $row->ID; ?>" class="text-danger" onclick="return confirm('Are you sure you want to delete this item?')" ;><i class="mdi mdi-file-document-box-check-outline"> </i>Delete</a>&nbsp;&nbsp;&nbsp;&nbsp;
                                                            <a href="<?= base_url(); ?>Page/leaveHistory?id=<?= $row->IDNumber; ?>" class="text-info"><i class="mdi mdi-file-document-box-check-outline"></i>Leave History</a>&nbsp;&nbsp;&nbsp;&nbsp;

                                                        <?php elseif ($this->session->userdata('position') === 'Staff'): ?>
                                                            <a href="<?= base_url(); ?>Page/id=<?= $row->ID; ?>" data-id="<?= $row->ID; ?>" data-vltotal="<?= $row->vlTotal; ?>" data-sltotal="<?= $row->slTotal; ?>" data-coctotal="<?= $row->cocTotal; ?>" class="text-success open-AddBookDialog" data-toggle="modal" data-target=".lcupdate"><i class="mdi mdi-file-document-box-check-outline"></i>Update</a>&nbsp;&nbsp;&nbsp;&nbsp;
                                                            <a href="<?= base_url(); ?>Page/delete_lc?id=<?= $row->ID; ?>" class="text-danger" onclick="return confirm('Are you sure you want to delete this item?')" ;><i class="mdi mdi-file-document-box-check-outline"> </i>Delete</a>&nbsp;&nbsp;&nbsp;&nbsp;
                                                            <a href="<?= base_url(); ?>Page/leaveHistory?id=<?= $row->IDNumber; ?>" class="text-info"><i class="mdi mdi-file-document-box-check-outline"></i>Leave History</a>&nbsp;&nbsp;&nbsp;&nbsp;

                                                        <?php elseif ($this->session->userdata('position') === 'School'): ?>

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
                                <h5 class="modal-title" id="myLargeModalLabel">Leave Credits</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                            </div>
                            <div class="modal-body">
                                <?= form_open('Page/addLC'); ?>


                                <div class="form-row">
                                    <div class="form-group col-md-12">
                                        <label for="inputEmail4" class="col-form-label">Employee</label>
                                        <select class="form-control" required name="IDNumber" data-toggle="select2">
                                            <option>Select</option>
                                            <?php foreach ($data1 as $row) { ?>

                                                <option value="<?= $row->IDNumber; ?>"><?= $row->IDNumber . ' ' . $row->LastName . ', ' . $row->FirstName . ' ' . $row->MiddleName; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>


                                </div>

                                <div class="form-row">

                                    <div class="form-group col-md-3">
                                        <label for="inputEmail4" class="col-form-label">As of</label>
                                        <input type="date" class="form-control" required name="asOf">
                                    </div>

                                    <div class="form-group col-md-3">
                                        <label for="inputEmail4" class="col-form-label">VL Total</label>
                                        <input type="text" class="form-control" value='0' required name="vlTotal">
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label for="inputPassword4" class="col-form-label">SL Total</label>
                                        <input type="text" class="form-control" value='0' name="slTotal">
                                    </div>

                                    <div class="form-group col-md-3">
                                        <label for="inputPassword4" class="col-form-label">COC/Service Record</label>
                                        <input type="text" class="form-control" value='0' name="cocTotal">
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
                            <h5 class="modal-title" id="myLargeModalLabel">Update Leave Credits</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        </div>
                        <div class="modal-body">
                        <form method="post" action="<?= base_url('Page/updateLeaveCredits'); ?>">


                                <div class="modal-body">


                                    <div class="form-row">
                                        <div class="form-group col-md-4">
                                            <label for="inputEmail4" class="col-form-label">VL Total</label>
                                            <input type="text" class="form-control" value="<?= set_value('vlTotal'); ?>" id="vltotal" required name="vlTotal">
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label for="inputPassword4" class="col-form-label">SL Total</label>
                                            <input type="text" class="form-control" value="<?= set_value('slTotal'); ?>" name="slTotal" id="sltotal">
                                        </div>

                                        <div class="form-group col-md-4">
                                            <label for="inputPassword4" class="col-form-label">COC/Service Record</label>
                                            <input type="text" class="form-control" value='0' id="coctotal" name="cocTotal">
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
    var vltotal = $(this).data('vltotal');
    var sltotal = $(this).data('sltotal');
    var coctotal = $(this).data('coctotal');

    console.log("Updating ID:", id); // Debugging line

    $(".modal-body #id").val(id);
    $(".modal-body #vltotal").val(vltotal);
    $(".modal-body #sltotal").val(sltotal);
    $(".modal-body #coctotal").val(coctotal);
});

            </script>