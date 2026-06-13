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
                                    <h4 class="header-title mb-4">Applicant's Rating</h4><br />
                                       
                                    <table class="table table-striped mb-0">
                                        <tr>
                                            <th>CRITERIA</th>
                                            <th style="text-align:center">WEIGHT ALLOCATION</th>
                                            <th style="text-align:center">ACTUAL SCORE</th>
                                        </tr>
                                        <tr>
                                            <td>Education</td>
                                            <td style="text-align:center">10</td>
                                            <td style="text-align:center"><?php if(!$data) { echo "";}else{ echo ($data[0]->education != 0.00001) ? $data[0]->education : '';} ?></td>
                                           
                                        </tr>

                                        <tr>
                                            <td>Training</td>
                                            <td style="text-align:center">10</td>
                                            <td style="text-align:center"><?php if(!$data) { echo "";}else{ echo ($data[0]->training != 0.00001) ? $data[0]->training : '';} ?></td>
                                        </tr>

                                        <tr>
                                            <td>Experience</td>
                                            <td style="text-align:center">10</td>
                                            <td style="text-align:center"><?php if(!$data) { echo "";}else{ echo ($data[0]->experience != 0.00001) ? $data[0]->experience : '';} ?></td>
                                        </tr>
                                        <tr>
                                            <td>PBET/LET/LEPT Rating</td>
                                            <td style="text-align:center">10</td>
                                            <td style="text-align:center"><?php if(!$data) { echo "";}else{ echo ($data[0]->let_rating != 0.00001) ? $data[0]->let_rating : '';} ?></td>
                                        </tr>
                                        <tr>
                                            <td>PPST Classroom Observable Indicators (Demonstration Teaching using COT-RSP)</td>
                                            <td style="text-align:center">35</td>
                                            <td style="text-align:center"><?php if(!$data) { echo "";}else{ echo  ($data[0]->demo_rating != 0.00001) ? $data[0]->demo_rating : '';} ?></td>
                                        </tr>
                                        <tr>
                                            <td>PPST Non-Classroom Observable Indicators (Teacher Reflection)</td>
                                            <td style="text-align:center">25</td>
                                            <td style="text-align:center"><?php if(!$data) { echo "";}else{ echo  ($data[0]->tr_rating != 0.00001) ? $data[0]->tr_rating : '';} ?></td>
                                        </tr>
                                        <tr>
                                            <td>TOTAL POINTS</td>
                                            <td style="text-align:center">100</td>
                                            <td style="text-align:center"><strong><?php if(!$data) { echo "";}else{ echo number_format($data[0]->total_points, 2); } ?></strong></td>
                                        </tr>
                                 </table>

                                 <?php if($this->session->userdata('position')==='Admin'):?>
                                    <button type="button" class="btn btn-primary waves-effect waves-light" data-toggle="modal" data-target=".bs-example-modal-lg">Edit Rating</button>
                                     <?php elseif($this->session->userdata('position')==='reg'):?>

                                    <?php endif;?>

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
                                                        <h5 class="modal-title" id="myLargeModalLabel">Edit Rating</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                    </div>
                                                    <div class="modal-body">
                                                        
                                                    <form class="form-horizontal" method="post">
                                                        <div class="form-group row">
                                                            <label for="inputEmail3" class="col-md-3 col-form-label">Education</label>
                                                            <div class="col-md-9">
                                                                <input type="text" class="form-control" value="<?php if(!$data) { echo "";}else{ echo $data[0]->education; } ?>" name="education" >
                                                            </div>
                                                        </div>
                                                        <div class="form-group row">
                                                            <label for="inputEmail3" class="col-md-3 col-form-label">Training</label>
                                                            <div class="col-md-9">
                                                                <input type="text" class="form-control" name="training" value="<?php if(!$data) { echo "";}else{ echo $data[0]->training; } ?>" >
                                                            </div>
                                                        </div>
                                                        <div class="form-group row">
                                                            <label for="inputPassword5" class="col-md-3 col-form-label">Experience</label>
                                                            <div class="col-md-9">
                                                                <input type="text" class="form-control" name="experience" value="<?php if(!$data) { echo "";}else{ echo $data[0]->experience; } ?>" >
                                                            </div>
                                                        </div>
                                                        <div class="form-group row">
                                                            <label for="inputPassword5" class="col-md-3 col-form-label">PBET/LET/LEPT Rating</label>
                                                            <div class="col-md-9">
                                                                <input type="text" class="form-control" name="let_rating" value="<?php if(!$data) { echo "";}else{ echo $data[0]->let_rating; } ?>" >
                                                            </div>
                                                        </div>

                                                        <div class="form-group row">
                                                            <label for="inputPassword5" class="col-md-3 col-form-label">Demonstration Teaching using COT-RSP</label>
                                                            <div class="col-md-9">
                                                                <input type="text" class="form-control" name="demo_rating" value="<?php if(!$data) { echo "";}else{ echo $data[0]->demo_rating; } ?>">
                                                            </div>
                                                        </div>

                                                        <div class="form-group row">
                                                            <label for="inputPassword5" class="col-md-3 col-form-label">Teacher Reflection</label>
                                                            <div class="col-md-9">
                                                                <input type="text" class="form-control" name="tr_rating" value="<?php if(!$data) { echo "";}else{ echo $data[0]->tr_rating; } ?>">
                                                            </div>
                                                        </div>

                                                        <div class="form-group row">
                                                            <label for="inputPassword5" class="col-md-3 col-form-label">Total Rating</label>
                                                            <div class="col-md-9">
                                                                <input type="text" class="form-control" name="total_points" value="<?php if(!$data) { echo "";}else{ echo $data[0]->total_points; } ?>" >
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