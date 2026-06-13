<?php include('templates/head.php'); ?>
            <?php include('templates/header.php'); ?>
            
<script src="<?= base_url(); ?>assets/libs/select2/select2.min.js"></script>
<script src="<?= base_url(); ?>assets/libs/select2/jquery.min.js"></script>
<link href="<?= base_url(); ?>assets/libs/select2/select2.min.css" rel="stylesheet" type="text/css" />
<style>
            .select2-container .select2-selection--single {
                height: calc(2.25rem + 2px) !important;
                /* Matches Bootstrap input height */
                border: 1px solid #ced4da !important;
                border-radius: .25rem !important;
                padding: .375rem .75rem !important;
            }

            .select2-container--default .select2-selection--single .select2-selection__rendered {
                line-height: 1.5 !important;
                color: #495057 !important;
            }

            .select2-container--default .select2-selection--single .select2-selection__arrow {
                height: calc(2.25rem + 2px) !important;
            }
        </style>



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

                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body table-responsive">
                                        <h4 class="header-title mb-4">Superior Setting</h4>

                                  
<?php if ($this->session->flashdata('error')): ?>
    <div class="alert alert-danger"><?= $this->session->flashdata('error'); ?></div>
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
                                        <button type="button" class="btn btn-primary waves-effect waves-light" data-toggle="modal" data-target=".bs-example-modal-lg">New Superior</button>
                                        <br /> <br />
                                        <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">

                                            <thead>
                                                <tr>
                                                    <th style='text-align:center'>Recommending Officer</th>
                                                    <th style='text-align:center'>Approver</th>
                                                    
                                                    <th style='text-align:center'>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                  
                                        <?php foreach ($data as $row) { ?>
                                            <tr>
                                                <td style="text-align:center"><?= $row->endorserName; ?></td>
                                                <td style="text-align:center"><?= $row->approverName; ?></td>
                                                <td style="text-align:center">
                                                    <button type="button" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#editModal<?= $row->supID; ?>">Edit</button>
                                                    <a href="<?= base_url('Page/deleteSuperior/' . $row->supID); ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this record?');">Delete</a>
                                                </td>
                                            </tr>
                                            
                                            <!-- Update Modal -->
                                            <div class="modal fade" id="editModal<?= $row->supID; ?>" tabindex="-1" role="dialog" aria-labelledby="editModalLabel<?= $row->supID; ?>" aria-hidden="true">
                                              <div class="modal-dialog">
                                                <form method="post" action="<?= base_url('Page/updateSuperior'); ?>">
                                                  <div class="modal-content">
                                                    <div class="modal-header">
                                                      <h5 class="modal-title">Edit Superior</h5>
                                                      <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                    </div>
                                                    <div class="modal-body">
                                                      <input type="hidden" name="supID" value="<?= $row->supID; ?>">
                                            
                                                      <div class="form-group">
                                                        <label>Recommending Officer</label>
                                                        <select name="endorser" id="endorserSelect<?= $row->supID; ?>" class="form-control select2" required>
                                                          <option value="">-- Select Endorser --</option>
                                                          <?php foreach ($data2 as $staff) { ?>
                                                            <option value="<?= $staff->IDNumber; ?>"
                                                              data-fullname="<?= $staff->FirstName . ' ' . $staff->MiddleName . ' ' . $staff->LastName; ?>"
                                                              <?= ($staff->IDNumber == $row->endorser) ? 'selected' : ''; ?>>
                                                              <?= $staff->FirstName . ' ' . $staff->MiddleName . ' ' . $staff->LastName; ?>
                                                            </option>
                                                          <?php } ?>
                                                        </select>
                                                        <input type="hidden" name="endorserName" id="endorserName<?= $row->supID; ?>" value="<?= $row->endorserName; ?>">
                                                      </div>
                                            
                                                      <div class="form-group">
                                                        <label>Approver</label>
                                                        <select name="approver" id="approverSelect<?= $row->supID; ?>" class="form-control select2" required>
                                                          <option value="">-- Select Approver --</option>
                                                          <?php foreach ($data3 as $staff) { ?>
                                                            <option value="<?= $staff->username; ?>"
                                                              data-fullname="<?= $staff->fname . ' ' . $staff->mname . ' ' . $staff->lname; ?>"
                                                              <?= ($staff->username == $row->approver) ? 'selected' : ''; ?>>
                                                              <?= $staff->fname . ' ' . $staff->mname . ' ' . $staff->lname; ?>
                                                            </option>
                                                          <?php } ?>
                                                        </select>
                                                        <input type="hidden" name="approverName" id="approverName<?= $row->supID; ?>" value="<?= $row->approverName; ?>">
                                                      </div>
                                            
                                                    </div>
                                                    <div class="modal-footer">
                                                      <button type="submit" class="btn btn-primary">Save Changes</button>
                                                      <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                                    </div>
                                                  </div>
                                                </form>
                                              </div>
                                            </div>
                                            
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

                <?php include('templates/footer.php'); ?>

                <!--  Modal content for the above example -->
                <div class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" style="display: none;" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="myLargeModalLabel">EMPLOYEE SETTING</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                            </div>
                            <div class="modal-body">
                                <!-- Form row -->
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="card">
                                            <div class="card-body">
                                            <form method="post">
    <div class="form-row">
        <div class="form-group col-md-12">
            <label class="col-form-label">Recommending Officer</label>
            <select name="endorser" id="endorserSelect" class="form-control select2" required>
                <option value="">Select Recommending Officer</option>
                <?php foreach ($data2 as $row) { ?>
                    <option value="<?php echo $row->IDNumber; ?>" 
                            data-fullname="<?php echo $row->FirstName . ' ' . $row->MiddleName . ' ' . $row->LastName; ?>">
                        <?php echo $row->FirstName; ?> <?php echo $row->MiddleName; ?> <?php echo $row->LastName; ?>
                    </option>
                <?php } ?>
            </select>
            <input type="hidden" name="endorserName" id="endorserName">
        </div>

        <div class="form-group col-md-12">
            <label class="col-form-label">Approver</label>
            <select name="approver" id="approverSelect" class="form-control select2" required>
                <option value="">Select Direct Superior</option>
                <?php foreach ($data3 as $row) { ?>
                    <option value="<?php echo $row->username; ?>" 
                            data-fullname="<?php echo $row->fname . ' ' . $row->mname . ' ' . $row->lname; ?>">
                        <?php echo $row->fname; ?> <?php echo $row->mname; ?> <?php echo $row->lname; ?>
                    </option>
                <?php } ?>
            </select>
            <input type="hidden" name="approverName" id="approverName">
        </div>
    </div>
    <input type="submit" name="submit" class="btn btn-primary" value="Submit">
</form>

</div>
                                        </div>

                                        <script>
    $(document).ready(function(){
        $('#endorserSelect').on('change', function(){
            var fullName = $(this).find(':selected').data('fullname');
            $('#endorserName').val(fullName);
        });

        $('#approverSelect').on('change', function(){
            var fullName = $(this).find(':selected').data('fullname');
            $('#approverName').val(fullName);
        });
    });

    <?php foreach ($data as $row) { ?>
$('#endorserSelect<?= $row->supID; ?>').on('change', function(){
    var fullName = $(this).find(':selected').data('fullname');
    $('#endorserName<?= $row->supID; ?>').val(fullName);
});

$('#approverSelect<?= $row->supID; ?>').on('change', function(){
    var fullName = $(this).find(':selected').data('fullname');
    $('#approverName<?= $row->supID; ?>').val(fullName);
});
<?php } ?>

</script>

                                        <script>
    $(document).ready(function() {
        $('.select2').select2();
    });
</script>
                                    </div>
                                </div>
                                <!-- end row -->
                            </div>
                        </div>
                        <!-- /.modal-content -->
                    </div>
                    <!-- /.modal-dialog -->
                </div>
                <!-- /.modal -->