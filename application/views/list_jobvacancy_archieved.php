            <?php include('templates/head.php'); ?> 
            <link href="<?= base_url(); ?>assets/libs/custombox/custombox.min.css" rel="stylesheet" type="text/css">
            <?php include('templates/header.php'); ?>    
            <?php 
            $jobTypes = [
                    1 => '- Elementary',
                    2 => '- Secondary',
                    3 => '- Junior High School',
                    4 => '- Senior High School',
                    5 => '- Kindergarten',
                    6 => '- IPED Elementary',
                    7 => '- IPED Secondary',
                    8 => '- IPED Junior High School',
                    9 => '- IPED Senior High School',
                    10 => '- SNED',
                ];
            ?>      

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
                                        <!-- <?php if($this->session->userdata('position')==='Admin'):?>
                                            <button type="button" class="btn btn-primary waves-effect waves-light" data-toggle="modal" data-target=".bs-example-modal-lg">Add New</button>
                                        <?php elseif($this->session->userdata('position')==='reg'):?>
                                                      
                                         <?php endif;?> -->
                                                
                                    <div class="clearfix"></div>
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
                                    <h4 class="header-title mb-4">Job Vacancies</h4><br />
                                       
                                        <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                        <thead>
                                            <tr>
                                                <th>Job Title</th>
												<th>Emp. Type</th>
                                                <th>Date Posted</th>
                                                <th>Status</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                               <?php
										  $i=1;
										  foreach($data as $row){ ?>
                                          <tr>
                                            <td><?= $row->jobTitle.' '.($jobTypes[$row->job_type] ?? ''); ?></td>
                                            <td><?= $row->empType; ?></td>
                                            <td><?= $row->datePosted; ?></td>
                                            <td><?= $row->jvStatus; ?></td>
                                          <td>
                                            <a href="<?=base_url(); ?>Page/viewApplicants?jobID=<?php echo $row->jobID; ?>&jobTitle=<?php echo $row->jobTitle; ?>" class="text-success tooltips" data-placement="top" data-toggle="tooltip" data-original-title="View Applicants"><i class="mdi mdi-file-document-box-check-outline"></i></a>&nbsp;&nbsp;&nbsp;&nbsp;
                                            <a href="<?=base_url(); ?>Pages/rqa_list/<?= $row->sy; ?>/?jobID=<?php echo $row->jobID; ?>&jobTitle=<?php echo $row->jobTitle; ?>" class="text-primary tooltips" data-placement="top" data-toggle="tooltip" data-original-title="RQA"><i class="mdi mdi-calculator"></i></a>&nbsp;&nbsp;&nbsp;&nbsp;
                                            <a onclick="return confirm('Are you sure you want to Open?')" href="<?=base_url(); ?>Pages/open_job/<?= $row->jobID; ?>" class="text-warning tooltips" data-placement="top" data-toggle="tooltip" data-original-title="Open"><i class="fas fa-lock"></i></a>&nbsp;&nbsp;&nbsp;&nbsp; 
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

                <?php include('templates/footer.php'); ?>  

       
       
 <!--  Add New Vacancies -->
 <div class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" style="display: none;" aria-hidden="true">
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="myLargeModalLabel">Job Vacancy Posting</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                    </div>
                                                    <div class="modal-body">
                                                        
                                                    <form class="form-horizontal" method="post">
                                                        <div class="form-group row">
                                                            <label for="inputEmail3" class="col-md-3 col-form-label">Position</label>
                                                            <div class="col-md-9">
                                                                <input type="text" class="form-control" name="jobTitle" >
                                                            </div>
                                                        </div>
                                                        <div class="form-group row">
                                                            <label for="inputEmail3" class="col-md-3 col-form-label">Item No. (if applicable)</label>
                                                            <div class="col-md-9">
                                                                <input type="text" class="form-control" name="itemNo" >
                                                            </div>
                                                        </div>
                                                        <div class="form-group row">
                                                            <label for="inputPassword3" class="col-md-3 col-form-label">Employment Type</label>
                                                            <div class="col-md-9">
                                                                <select class="form-control" name="empType">
                                                                    <option></option>
                                                                    <option>Permanent Position</option>
                                                                    <option>Job Order</option>
                                                                    <option>Contract of Service</option>

                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="form-group row">
                                                            <label for="inputPassword5" class="col-md-3 col-form-label">Department</label>
                                                            <div class="col-md-9">
                                                                <input type="text" class="form-control" name="department" >
                                                            </div>
                                                        </div>
                                                        <div class="form-group row">
                                                            <label for="inputPassword5" class="col-md-3 col-form-label">Job Description</label>
                                                            <div class="col-md-9">
                                                                <textarea class="form-control" rows="5" name="description"></textarea>
                                                            </div>
                                                        </div>

                                                        <div class="form-group row">
                                                            <label for="inputPassword5" class="col-md-3 col-form-label">No. of Vacancies</label>
                                                            <div class="col-md-9">
                                                                <input type="number" class="form-control" name="qty" >
                                                            </div>
                                                        </div>
                                                       
                                                        <div class="form-group mb-0 justify-content-end row">
                                                            <div class="col-md-9">
                                                                <input type="submit" name="submit" value="Submit" class="btn btn-info waves-effect waves-light">
                                                                <!-- <button type="submit" class="btn btn-info waves-effect waves-light">Submit</button> -->
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


<!--  Add New Vacancies -->
<div class="modal fade bs-example-modal-apply" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" style="display: none;" aria-hidden="true">
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="myLargeModalLabel">Submit an Application</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                    </div>
                                                    <div class="modal-body">
                                                        
                                                    <form class="form-horizontal" method="post">
                                                        <div class="form-group row">
                                                            <label for="inputEmail3" class="col-md-3 col-form-label">Position</label>
                                                            <div class="col-md-9">
                                                                <input type="text" class="form-control" name="jobTitle" >
                                                            </div>
                                                        </div>
                                                        <div class="form-group row">
                                                            <label for="inputEmail3" class="col-md-3 col-form-label">Item No. (if applicable)</label>
                                                            <div class="col-md-9">
                                                                <input type="text" class="form-control" name="itemNo" >
                                                            </div>
                                                        </div>
                                                        <div class="form-group row">
                                                            <label for="inputPassword3" class="col-md-3 col-form-label">Employment Type</label>
                                                            <div class="col-md-9">
                                                                <select class="form-control" name="empType">
                                                                    <option></option>
                                                                    <option>Permanent Position</option>
                                                                    <option>Job Order</option>
                                                                    <option>Contract of Service</option>

                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="form-group row">
                                                            <label for="inputPassword5" class="col-md-3 col-form-label">Department</label>
                                                            <div class="col-md-9">
                                                                <input type="text" class="form-control" name="department" >
                                                            </div>
                                                        </div>
                                                        <div class="form-group row">
                                                            <label for="inputPassword5" class="col-md-3 col-form-label">Job Description</label>
                                                            <div class="col-md-9">
                                                                <textarea class="form-control" rows="5" name="description"></textarea>
                                                            </div>
                                                        </div>

                                                        <div class="form-group row">
                                                            <label for="inputPassword5" class="col-md-3 col-form-label">No. of Vacancies</label>
                                                            <div class="col-md-9">
                                                                <input type="number" class="form-control" name="qty" >
                                                            </div>
                                                        </div>
                                                       
                                                        <div class="form-group mb-0 justify-content-end row">
                                                            <div class="col-md-9">
                                                                <input type="submit" name="submit" value="Submit" class="btn btn-info waves-effect waves-light">
                                                                <!-- <button type="submit" class="btn btn-info waves-effect waves-light">Submit</button> -->
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