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
                                     <!-- <h4 class="header-title mb-4">My Application/s</h4> -->
                                                
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
                                    <h4 class="header-title mb-4">Application/s</h4><br />
                                       
                                        <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                        <thead>
                                            <tr>
                                                <th>Date Submitted</th>
												<th>Position</th>
                                                <th style="text-align:center">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                               <?php
										  $i=1;
										  foreach($data as $row)
										  {
										  echo "<tr>";
										  echo "<td>".$row->dateSubmitted."</td>";
										  echo "<td>".$row->jobTitle."</td>";
                                          ?>
                                        
                                        <?php 
                                            $applicant = $this->Common->one_cond_row('hris_applicant','empEmail',$row->empEmail); 
                                        ?>
                                                    
                                          <td style="text-align:center">
                                                    <a href="<?= base_url() ?>Pages/ma/<?= $applicant->id; ?>/<?= $row->jobID; ?>/<?= $row->pre_school; ?>" target="_blank"><i class=" mdi mdi-xbox-controller-view text-primary"></i>View Application</a>
                                                    <?php if($this->session->position == 'asds'){?>
                                                        <a target="_blank" href="<?= base_url(); ?>Pages/request_to_rr/<?= $applicant->id; ?>/<?= $row->jobID; ?>/<?= $row->appID?>" class="text-purple"><i class="mdi mdi-xbox-controller-view text-purple"></i>Request to Retain Rating</a>
                                                    <?php } ?>

                                                    <?php if($this->session->position == "Admin"){ ?>
                                                        <a class="text <?php echo ($track->status == 0) ? "text-info": ""; ?> " href="<?=base_url(); ?>Page/appTrack?jobID=<?php echo $row->jobID; ?>&id=<?php echo $row->empEmail; ?>"><i class=" mdi mdi-xbox-controller-view text-info"></i>Track</a>
                                                        <a class="text <?php echo ($track->status == 0) ? "text-info": ""; ?>"href="<?=base_url(); ?>Page/ratings?appID=<?php echo $row->appID; ?>"><i class=" mdi mdi-xbox-controller-view text-info"></i>Rating</a>
                                                       
                                                    <?php }else{ ?>
                                                    <?php if($track->status == 0){ ?>
                                                        <a class="text text-info" href="<?=base_url(); ?>Page/appTrack?jobID=<?php echo $row->jobID; ?>&id=<?php echo $row->empEmail; ?>"><i class="mdi mdi-xbox-controller-view text-info"></i>Track</a>
                                                    <?php } ?>
                                                    <?php if($rating->status == 0){ ?>
                                                        <a class="text text-info" href="<?=base_url(); ?>Page/ratings?appID=<?php echo $row->appID; ?>"><i class=" mdi mdi-xbox-controller-view text-info"></i>Rating</a>
                                                    <?php } ?>
                                                    <?php } ?>
                                                    <a href="<?=base_url(); ?>Pages/del_application/<?= $row->appID; ?>/<?= $row->jobID; ?>/?ee=<?= $row->empEmail; ?>"  onclick="return confirm('Are You Sure!!')"><i class=" mdi mdi-xbox-controller-view text-danger"></i>Delete</a>                    

                                                </td>

                                                    <?php echo "</tr>"; } ?>
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