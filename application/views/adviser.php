

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
                                    <a href="#" data-toggle="modal" data-target="#myModal">
                                        <span class="badge badge-purple mb-3">Currently login: <b>SY <?= $this->session->cur_sy;?></span></b> 
                                    </a>
                                </li>
                            </ol>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                </div>
            </div>

            <!-- <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="clearfix">

                                        <form class="row g-3 align-items-center" action="<?= base_url(); ?>Sbfp/SectionAdviser">
                                            <div class="col-auto">
                                                <label for="sy" class="col-form-label fw-bold">SY</label>
                                            </div>
                                            <div class="col-auto">
                                                <input type="text" class="form-control" name="sy" placeholder="2025-2026">
                                            </div>
                                            <div class="col-auto">
                                                <button type="submit" class="btn btn-primary">Submit</button>

                                            </div>
                                        </form>
                            </div>            
                        </div>            
                    </div>            
                </div>
            </div> -->

            <!-- start row -->
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="clearfix">
                                

                                <div class="float-left">
                                    <h5 style="text-transform:uppercase">
                                        <strong>SECTIONS/ADVISERS</strong>
                                    </h5>
                                </div>

                                <div class="table-responsive">
                                    <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                        <thead>
                                            <tr>
                                                <th>Year Level</th>
                                                <th>Section</th>
                                                <th>Adviser</th>
                                            
                                                <th style="text-align:center">Manage</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($data as $row) { ?>
                                            <tr>
                                            <td><?= $row->YearLevel; ?></td>
                                                <td><?= $row->Section; ?></td>
                                                <td><?= $row->FirstName; ?> <?= $row->MiddleName; ?> <?= $row->LastName; ?></td>
                                                <td style="text-align:center">
                                                    <button type="button" class="btn btn-primary waves-effect waves-light btn-sm" onclick="editProgram('<?= $row->sectionID; ?>', '<?= $row->IDNumber; ?>')" data-toggle="modal" data-target="#editModal">
                                                        <i class="mdi mdi-pencil"></i>Edit
                                                    </button>

                                                    <a href="#" onclick="setDeleteUrl('<?= base_url('Sbfp/section_delete/' . $row->sectionID); ?>')" data-toggle="modal" data-target="#confirmationModal"  class="btn btn-danger waves-effect waves-light btn-sm">
                                                    <i class="ion ion-ios-alert"></i> Delete
                                                    </a>

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
<!-- Update Modal -->
<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Update Adviser</h5>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <div class="modal-body">
                <form method="post" action="<?php echo base_url('Sbfp/SectionAdviser'); ?>">
                    <input type="hidden" name="sectionID" id="sectionID">
                    <input type="hidden" id="SY" name="SY" class="form-control" value="<?php echo $this->session->userdata('sy'); ?>" required />
                    
                    <div class="col-md-12 mb-3">
                        <label for="Section">Section</label>
                        <input type="text" name="Section" id="Section" class="form-control" value="<?php echo $row->Section; ?>" required>
                    </div>

                    <div class="col-md-12 mb-3">
                        <label for="IDNumber">Adviser</label>
                        <select name="IDNumber" id="IDNumber" class="form-control select2" required>
                            <option value="">Select Adviser</option>
                            <?php foreach ($data as $level) { ?>
                                <option value="<?= $level->IDNumber; ?>"><?= $level->FirstName; ?> <?= $level->MiddleName; ?> <?= $level->LastName; ?></option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="modal-footer">
                        <input type="submit" name="update" value="Update Data" class="btn btn-primary waves-effect waves-light" />
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
 function editProgram(sectionID, IDNumber, section) {
    document.getElementById('sectionID').value = sectionID;
    document.getElementById('IDNumber').value = IDNumber;
}

</script>

          
                    <!-- end container-fluid -->



                        <!-- Confirmation Modal -->
<div class="modal fade" id="confirmationModal" tabindex="-1" role="dialog" aria-labelledby="confirmationModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmationModalLabel">Delete Confirmation</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="text-center">
                    <div class="circle-with-stroke d-inline-flex justify-content-center align-items-center">
                        <span class="h1 text-danger">!</span>
                    </div>
                    <p class="mt-3">Are you sure you want to delete this data?</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <a href="#" id="deleteButton" class="btn btn-danger" onclick="deleteData()">Delete</a>
            </div>
        </div>
    </div>
</div>

<style>
    .circle-with-stroke {
        width: 100px;
        height: 100px;
        border: 4px solid #dc3545;
        border-radius: 50%;
    }
</style>

<script>
    function setDeleteUrl(url) {
        document.getElementById('deleteButton').href = url;
    }

    function deleteData() {
        // This will now correctly delete the selected item
    }
</script>
<!-- Add Adviser Modal -->
<div class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myLargeModalLabel">Add New</h5>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <form method="post" action="<?= base_url('Sbfp/SectionAdviser'); ?>">
                    <input type="hidden" name="SY" class="form-control" value="<?php if(!empty($sy)){echo $sy;} ?>" readonly required />
                    

                    <div class="form-row align-items-center">
                        <div class="col-md-3 mb-3">
                            <label for="YearLevel">Year Level</label>
                            <select name="YearLevel" id="YearLevel" class="form-control select2">
                                <option value="0">Kindergarten</option>
                                <?php for ($i = 1; $i <= 12; $i++): ?>
                                    <option value="Grade <?= $i ?>">Grade <?= $i ?></option>
                                <?php endfor; ?>

                            </select>
                        </div>

                        <div class="col-md-3 mb-3">
                            <label for="Section">Section</label>
                            <input type="text" class="form-control" id="Section" name="Section" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="add_IDNumber">Adviser</label>
                            <select name="IDNumber" id="add_IDNumber" class="form-control" data-toggle="select2">
                                <option value="">Select Adviser</option>
                                <?php foreach ($staff as $level) { ?>
                                    <option 
                                        value="<?= $level->IDNumber; ?>" 
                                        data-fullname="<?= $level->FirstName; ?> <?= $level->MiddleName; ?> <?= $level->LastName; ?>" >
                                        <?= $level->FirstName; ?> <?= $level->MiddleName; ?> <?= $level->LastName; ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>

                            <input type="hidden" name="adviserName" id="add_adviserName" class="form-control" readonly>
              

                    <div class="modal-footer">
                        <input type="submit" name="save" value="Save Data" class="btn btn-primary waves-effect waves-light" />
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
$(document).ready(function() {
    $('#add_IDNumber').on('change', function () {
        var fullName = $(this).find('option:selected').data('fullname');
        $('#add_adviserName').val(fullName ? fullName : '');
    });
});
</script>



                                    <!-- /.modal-content -->
                                </div>
                                <!-- /.modal-dialog -->
                            </div>
                        </div>
                    </div>


        </div>
    </div>
</div>


<div id="myModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-purple">
                                                        <h5 class="modal-title text-white" id="myModalLabel">Change School Year</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                    </div>
                                                    <div class="modal-body">
                                                    <form action="<?= base_url('Pages/change_sy') ?>" method="post">
                                                        <div class="form-group row">
                                                            <div class="col-lg-12">
                                                                <select name="new_fy" class="form-control" onchange="this.form.submit()">
                                                                <option disabled selected>Change School Year</option>
                                                                <?php for ($y = 2023; $y <= 2030; $y++) : ?>
                                                                    <?php $sy = $y . '-' . ($y + 1); ?>
                                                                    <option value="<?= $sy ?>" <?= ($this->session->userdata('cur_fy') == $sy) ? 'selected' : '' ?>>
                                                                        <?= $sy ?>
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


