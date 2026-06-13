

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
                                                <th>Coure Description</th>
                                                <th>Grade</th>
                                                <th style="text-align:center">Manage</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($data as $row) { ?>
                                            <tr>
                                                <td><?= $row->CourseDescription; ?></td>
                                                <td><?= $row->Major; ?></td>
                                                <td>
                                                    <button type="button" class="btn btn-primary waves-effect waves-light btn-sm" onclick="editProgram('<?= $row->courseid; ?>', '<?= $row->courseid; ?>')" data-toggle="modal" data-target="#editModal">
                                                        <i class="mdi mdi-pencil"></i>Edit
                                                    </button>
                                                    <a onclick="return confirm('Are you sure?')" href="<?= base_url(); ?>Sbfp/delete_gl/<?= $row->courseid; ?>" class="btn btn-sm btn-danger"><i class="ion ion-ios-alert"></i> Delete</a>
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
                <form method="post" action="<?php echo base_url('Sbfp/grade_level_add'); ?>">
                    <input type="hidden" name="sectionID" id="sectionID">
                    <div class="form-row align-items-center">
                        <div class="col-md-3 mb-3">
                            <label for="Section">Course Code</label>
                            <input type="text" class="form-control" id="CourseCode" name="CourseCode" required>
                        </div>

                        <div class="col-md-3 mb-3">
                            <label for="CourseDescription">Course Description</label>
                            <select name="CourseDescription" id="CourseDescription" class="form-control select2">
                               <option value="Preschool">Preschool</option>
                               <option value="Elementary">Elementary</option>
                               <option value="Junior High School">Junior High School</option> 
                               <option value="Senior High School">Senior High School</option>
                            </select>  
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="Section">Grade Level</label>
                            <input type="text" class="form-control" id="Major" name="Major" required>
                        </div>
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

<div class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myLargeModalLabel">Add New</h5>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <form method="post" action="<?= base_url('Sbfp/grade_level_add'); ?>">
                <input type="hidden" name="SY" class="form-control" value="<?php if(!empty($sy)){echo $sy;} ?>" readonly required />
                    
                    <div class="form-row align-items-center">
                        <div class="col-md-3 mb-3">
                            <label for="Section">Course Code</label>
                            <input type="text" class="form-control" id="CourseCode" name="CourseCode" required>
                        </div>

                        <div class="col-md-3 mb-3">
                            <label for="CourseDescription">Course Description</label>
                            <select name="CourseDescription" id="CourseDescription" class="form-control select2">
                               <option value="Preschool">Preschool</option>
                               <option value="Elementary">Elementary</option>
                               <option value="Junior High School">Junior High School</option> 
                               <option value="Senior High School">Senior High School</option>
                            </select>  
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="Section">Grade Level</label>
                            <input type="text" class="form-control" id="Major" name="Major" required>
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


        </div>
    </div>
</div>


