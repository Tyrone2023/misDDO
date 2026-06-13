            <?php include('templates/head.php'); ?> 
            <link href="<?= base_url(); ?>assets/libs/custombox/custombox.min.css" rel="stylesheet" type="text/css">
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
                                        <!-- <button type="button" class="btn btn-primary waves-effect waves-light" data-toggle="modal" data-backdrop="static" data-keyboard="false" data-target=".bs-example-modal-lg">Add New</button> -->
                                                
                                    <div class="clearfix"></div>
                                    <br>
                                    <div class="row">
    <div class="col-12">
        <?php if (isset($showDropdown) && $showDropdown): ?>
            <div class="form-group">
                <form method="post" action="<?= base_url('Page/serviceRecord'); ?>">
                    <select id="departmentDropdown" name="departmentDropdown" data-toggle="select2" class="form-control" onchange="this.form.submit()">
                        <option value="">-- Select Department --</option>
                        <?php foreach ($dept as $department): ?>
                            <option value="<?= $department->Department; ?>" 
                                    <?= isset($_POST['departmentDropdown']) && $_POST['departmentDropdown'] === $department->Department ? 'selected' : ''; ?>>
                                <?= $department->Department; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </form>
            </div>
        <?php endif; ?>
    </div>
</div>
                                </div>
                            </div>
                        </div>
                        <!-- end page title -->

                        <?php if($this->session->flashdata('success')) : ?>

                            <?= '<div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>'
                                    .$this->session->flashdata('success'). 
                                '</div>'; 
                            ?>
                            <?php endif; ?>

                            <?php if($this->session->flashdata('danger')) : ?>
                            <?= '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>'
                                    .$this->session->flashdata('danger'). 
                                '</div>'; 
                            ?>
                            <?php endif;  ?>

                            <div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body table-responsive">
                <h4 class="header-title mb-4">SERVICE RECORD<br />
                    <span class="float-left badge badge-primary inline mt-2">
                        <?= isset($_POST['departmentDropdown']) && !empty($_POST['departmentDropdown']) ? $_POST['departmentDropdown'] : 'ALL'; ?>
                    </span>
                </h4><br />

                <!-- Check if data exists -->
                <?php if (empty($data)): ?>
                    <p>No service records found for the selected department or school.</p>
                <?php else: ?>
                    <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                        <thead>
                            <tr>
                                <th>Employee No.</th>
                                <th>Employee Name</th>
                                <th>Service</th>
                                <th>Designation</th>
                                <th>Status</th>
                                <!-- <th>Annual Salary</th>
                                <th>Office Entity</th>
                                <th>LV/ABS w/out Pay</th>
                                <th>Separation Cause/d</th> -->
                                <th>Manage</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($data as $row): ?>
                                <tr>
                                    <td><?= $row->IDNumber ?></td>
                                    <td><?= $row->FirstName.' '.$row->LastName ?></td>
                                    <td><?= $row->appointDate.' - '.$row->endDate ?></td>
                                    <td><?= $row->empPosition ?></td>
                                    <td><?= $row->empStatus ?></td>
                                    <!-- <td><?= $row->salary ?></td>
                                    <td><?= $row->empStation ?></td>
                                    <td><?= $row->lvwithoutpay ?></td>
                                    <td><?= $row->separation ?></td> -->
                                    <td>
                                        <!-- <a href="<?= base_url(); ?>Page/serviceRecordEdit?id=<?= $row->empID; ?>&f=<?= $row->FirstName; ?>&l=<?= $row->LastName; ?>" class="text-success">
                                            <i class="mdi mdi-file-document-box-check-outline"></i>Edit
                                        </a>&nbsp;&nbsp;&nbsp;&nbsp; -->

                                        <a href="<?= base_url(); ?>Page/serviceRecordview?IDNumber=<?= $row->IDNumber; ?>" class="text-success">
                                            <i class="mdi mdi-file-document-box-check-outline"></i>View Service Record
                                        </a>&nbsp;&nbsp;&nbsp;&nbsp;


                                        <!-- <a href="<?= base_url(); ?>Page/delete_serviceRecord?id=<?= $row->empID; ?>" onclick="return confirm('Are you sure you want to delete this record?')" class="text-danger">
                                            <i class="mdi mdi-file-document-box-check-outline"></i>Delete
                                        </a>&nbsp;&nbsp;&nbsp;&nbsp; -->
                                        <!-- <a href="<?= base_url(); ?>Page/delete_serviceRecordAll?id=<?= $row->IDNumber; ?>" onclick="return confirm('All service records for this employee will be deleted. Do you want to proceed?')" class="text-danger">
                                            <i class="mdi mdi-file-document-box-check-outline"></i>Delete All
                                        </a>&nbsp;&nbsp;&nbsp;&nbsp; -->
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
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
                                                        <h5 class="modal-title" id="myLargeModalLabel">Add New Service Record</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                    </div>
                                                    <div class="modal-body">
                                                    <?= form_open('Page/addSR'); ?>
                                                   

                                                    <div class="form-row">
                                                       <div class="form-group col-md-12">
                                                           <label for="inputEmail4" class="col-form-label">Employee</label>
                                                           <select class="form-control" required name="IDNumber" data-toggle="select2">
                                                                                                <option>Select</option>
                                                                                                <?php foreach($data1 as $row) { ?>
                                                                                                    <option value="<?= $row->IDNumber; ?>"><?= $row->IDNumber.' '.$row->LastName.', '.$row->FirstName.' '.$row->MiddleName; ?></option>
                                                                                                <?php } ?>
                                                                                            </select> 
                                                       </div>
                                                       
                                                   </div>


                                                   <div class="form-row">
                                                       <div class="form-group col-md-3">
                                                           <label for="inputEmail4" class="col-form-label">Designation</label>
                                                           <input type="text" required name="empPosition" class="form-control">
                                                       </div>
                                                       <div class="form-group col-md-3">
                                                           <label for="inputPassword4" class="col-form-label">Employment Status</label>
                                                           <input type="text" required name="empStatus" class="form-control">
                                                       </div>

                                                       <div class="form-group col-md-3">
                                                           <label for="inputEmail4" class="col-form-label">Annual Salary</label>
                                                           <input type="text" required name="salary" class="form-control">
                                                       </div>
                                                       <div class="form-group col-md-3">
                                                           <label for="inputPassword4" class="col-form-label">Office Entity</label>
                                                           <input type="text" required name="empStation" class="form-control">
                                                       </div>
                                                   </div>

                                                    <div class="form-row">
                                                       <div class="form-group col-md-3">
                                                           <label for="inputEmail4" class="col-form-label">From</label>
                                                           <input type="date" class="form-control" required name="appointDate">
                                                       </div>
                                                       <div class="form-group col-md-3">
                                                           <label for="inputPassword4" class="col-form-label">To</label>
                                                           <input type="date" class="form-control" name="endDate">
                                                       </div>

                                                       <div class="form-group col-md-3">
                                                           <label for="inputEmail4" class="col-form-label">LV/ABS w/out Pay</label>
                                                           <input type="text" name="lvwithoutpay" class="form-control">
                                                       </div>
                                                       <div class="form-group col-md-3">
                                                           <label for="inputPassword4" class="col-form-label">Separation Cause/d</label>
                                                           <input type="text" required name="separation" class="form-control">
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

<?php include('templates/footer.php'); ?>        
              