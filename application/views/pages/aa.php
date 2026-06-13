
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
                                <?php 
                                $position = $this->session->position;
                                if($position==='Human Resource Admin' || $position==='HR Staff' || $position==='Super Admin' || $position==='Super Admin' || $position==='Evaluator' || $position==='asds'): ?>
                                            <button type="button" class="btn btn-success waves-effect waves-light" data-toggle="modal" data-target=".aa">Search Applicant</button>
                                        <?php endif; ?>

                                    <div class="page-title-right">
                                        <ol class="breadcrumb p-0 m-0">
                                            <li class="breadcrumb-item"><a href="<?= base_url(); ?>Page/jobVacancy">Job Vacancy</a></li>
                                            <li class="breadcrumb-item active">List of Application</li>
                                        </ol>
                                    </div>
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

                        <?php $position = $this->session->position;
                            if($position==='Human Resource Admin' || $position==='HR Staff' || $position==='Super Admin' || $position==='asds'){ ?>
                                        
                                
                                
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body table-responsive">
                                        <?php if(!empty($applicant)){?>
                                        <?php if($this->session->eg != 3){?>
                                    <h3><?= $applicant->LastName.', '.$applicant->FirstName; ?> <?php if(!empty($applicant->MiddleName)){echo substr($applicant->MiddleName, 0, 1).'.';} ?></h3>
                                    <?php } ?>
                                    <h5><?= $applicant->record_no; ?></h5>
                                    <?php } ?>

                                    <?php if($applicant->stat == 1){?>
                                        <a class="btn btn-warning" href="<?= base_url(); ?>Pages/unlock_applicant?ee=<?= $applicant->empEmail; ?>" onclick="return confirm('Are you sure?')">Unlock Applicant</a><br /><br />
                                    <?php }else{ ?>
                                        <a class="btn btn-danger" href="<?= base_url(); ?>Pages/lock_applicant?ee=<?= $applicant->empEmail; ?>" onclick="return confirm('Are you sure?')">Lock Applicant</a><br /><br />
                                    <?php } ?> 

                                    <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                        <!-- <table id="datatable-buttons" class="table table-striped table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;"> -->
                                        <thead>
                                            <tr>
                                                <th>Job Title</th>
                                                <th>Year</th>
                                                <th>Education</th>
                                                <th>Trainings and Seminars</th>
                                                <th>Work Experience</th>
                                                <th>LET</th>
                                                <th>Demo</th>
                                                <th>Teacher's Reflection</th>
                                                <th style="text-align:center">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                                foreach($data as $row){ 
                                                    $a = $this->Common->one_cond_row('hris_applicant','record_no',$row->record_no); 
                                                    $application = $this->Common->one_cond_row('hris_applications','appID',$row->appID);
                                                    if(!empty($application)){
                                                    $job = $this->Common->one_cond_row('hris_jobvacancy','jobID',$application->jobID);
                                                    $rating = $this->Common->two_cond_row('hris_applications_rating','record_no',$a->record_no,'appID',$row->appID);
                                                    //$notify = $this->Common->four_cond_count_row('hris_applications_track', 'jobID',$row->jobID,'applicant_id',$row->applicant_id,'nstat',0,'res',$row->empEmail);
                                                    
                                            ?>
                                                <tr>
                                                    <td><?= $job->jobTitle; ?></td>
                                                    <td><?= $job->sy; ?></td>
                                                    <td class="text-center"><?php if($row->education != 0.00001){echo $row->education; } ?></td>
                                                    <td class="text-center"><?php if($row->training != 0.00001){echo $row->training; } ?></td>
                                                    <td class="text-center"><?php if($row->experience != 0.00001){echo $row->experience; } ?></td>
                                                    <td class="text-center"><?php if($row->let_rating != 0.00001){echo $row->let_rating; } ?></td>
                                                    <td class="text-center"><?php if($row->demo_rating != 0.00001){echo $row->demo_rating; } ?></td>
                                                    <td class="text-center"><?php if($row->tr_rating != 0.00001){echo $row->tr_rating; } ?></td>
                                                    
                                                    <td>
                                                    <?php $position = $this->session->position; if($position==='Super Admin' || $position==='asds'){ ?>
                                                    <a href="<?= base_url(); ?>pages/rate_applicant/<?= $a->id; ?>/<?= $application->jobID; ?>/<?= $application->pre_school; ?>" target="_blank" class="btn btn-pink tooltips" data-placement="top" data-toggle="tooltip" data-original-title="View Applicant Information"><i class="fas fa-file-alt"></i></a>&nbsp; &nbsp;
                                                    
                                                    <a href="#" class="open-AddBookDialog" data-toggle="modal" data-target=".demorating" data-id="<?= $row->appID; ?>" data-item="<?= $a->record_no; ?>" data-job="<?php if($rating->demo_rating != 0.00001){echo $rating->demo_rating; } ?>"><i class="mdi mdi-notebook-outline btn tooltips btn-primary" data-placement="top" data-toggle="tooltip" data-original-title="RATE DEMO"></i></a>

                                                    <a href="#" class="open-AddBookDialog" data-toggle="modal" data-target=".trrating" data-id="<?= $row->appID; ?>" data-item="<?= $a->record_no; ?>" data-job="<?php if($rating->tr_rating != 0.00001){echo $rating->tr_rating; } ?>"><i class="mdi mdi-notebook-outline btn tooltips btn-success" data-placement="top" data-toggle="tooltip" data-original-title="RATE TEACHER REFLECTION "></i></a>
                                                    
                                                    <?php }}  ?>
                                   

                                                    </td>
                                                </tr>
										    <?php	} ?>
                                        </tbody>

                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- end row -->

                        <?php }else{ ?>

                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body table-responsive">
                                        <?php if(!empty($applicant)){?>
                                        <?php if($this->session->eg != 3){?>
                                    <h3><?= $applicant->LastName.', '.$applicant->FirstName; ?> <?php if(!empty($applicant->MiddleName)){echo substr($applicant->MiddleName, 0, 1).'.';} ?></h3>
                                    <?php } ?>
                                    <h5><?= $applicant->record_no; ?></h5><br />
                                    <?php } ?>
                                        <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                        <!-- <table id="datatable-buttons" class="table table-striped table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;"> -->
                                        <thead>
                                            <tr>
                                                <th>Job Title</th>
                                                <th>Education</th>
                                                <th>Trainings and Seminars</th>
                                                <th>Work Experience</th>
                                                <th>LET</th>
                                                <th>Demo</th>
                                                <th>Teacher's Reflection</th>
                                                <th style="text-align:center">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                                foreach($data as $row){ 
                                                    $a = $this->Common->one_cond_row('hris_applicant','record_no',$row->record_no); 
                                                    $application = $this->Common->one_cond_row('hris_applications','appID',$row->appID);
                                                    $job = $this->Common->one_cond_row('hris_jobvacancy','jobID',$application->jobID);
                                                    $rating = $this->Common->two_cond_row('hris_applications_rating','record_no',$a->record_no,'appID',$row->appID);
                                                    //$notify = $this->Common->four_cond_count_row('hris_applications_track', 'jobID',$row->jobID,'applicant_id',$row->applicant_id,'nstat',0,'res',$row->empEmail);

                                            ?>
                                                <tr>
                                                    <td><?= $job->jobTitle; ?></td>
                                                    <td class="text-center"><?php if($row->eval_id1 == $this->session->id){if($row->education != 0.00001){echo $row->education; }}else{if($row->education != 0.00001){echo '<span class="badge badge-info">Rated</span>';}else{echo '<span class="badge badge-purple">Not Yet Rated</span>';}} ?></td>
                                                    <td class="text-center"><?php if($row->eval_id1 == $this->session->id){if($row->training != 0.00001){echo $row->training; }}else{if($row->training != 0.00001){echo '<span class="badge badge-info">Rated</span>';}else{echo '<span class="badge badge-purple">Not Yet Rated</span>';}} ?></td>
                                                    <td class="text-center"><?php if($row->eval_id1 == $this->session->id){if($row->experience != 0.00001){echo $row->experience; }}else{if($row->experience != 0.00001){echo '<span class="badge badge-info">Rated</span>';}else{echo '<span class="badge badge-purple">Not Yet Rated</span>';}} ?></td>
                                                    <td class="text-center"><?php if($row->eval_id1 == $this->session->id){if($row->let_rating != 0.00001){echo $row->let_rating; }}else{if($row->let_rating != 0.00001){echo '<span class="badge badge-info">Rated</span>';}else{echo '<span class="badge badge-purple">Not Yet Rated</span>';}} ?></td>
                                                    <td class="text-center"><?php if($row->eval_id2 == $this->session->id){if($row->demo_rating != 0.00001){echo $row->demo_rating; }}else{if($row->demo_rating != 0.00001){echo '<span class="badge badge-info">Rated</span>';}else{echo '<span class="badge badge-purple">Not Yet Rated</span>';}} ?></td>
                                                    <td class="text-center"><?php if($row->eval_id3 == $this->session->id){if($row->tr_rating != 0.00001){echo $row->tr_rating; }}else{if($row->tr_rating != 0.00001){echo '<span class="badge badge-info">Rated</span>';}else{echo '<span class="badge badge-purple">Not Yet Rated</span>';}} ?></td>
                                                    
                                                    <td>
                                                    
                                                    <?php if($this->session->eg == 1){ ?>
                                                                
                                                                <?php if(!empty($rating)){if($rating->eval_id1 == 0){ ?>
                                                                    <a href="<?= base_url(); ?>pages/rate_applicant/<?= $a->id; ?>/<?= $application->jobID; ?>/<?= $row->appID; ?>/<?= $a->record_no; ?>" class="btn <?php if($rating->eval_id1 == 0){echo "btn-info";}else{echo "btn-warning";} ?> tooltips" data-placement="top" data-toggle="tooltip" data-original-title="View Applicant Information"><i class="fas fa-file-alt"></i></a>&nbsp; &nbsp;
                                                                <?php }else{ ?>
                                                                    <?php if($this->session->id == $rating->eval_id1){?>
                                                                    <a href="<?= base_url(); ?>pages/rate_applicant/<?= $a->id; ?>/<?= $application->jobID; ?>/<?= $row->appID; ?>/<?= $a->record_no; ?>" class="btn btn-warning tooltips" data-placement="top" data-toggle="tooltip" data-original-title="View Applicant Information"><i class="fas fa-file-alt"></i></a>&nbsp; &nbsp;
                                                                    <?php } ?>
                                                                <?php }}else{ ?>
                                                                    <a href="<?= base_url(); ?>pages/rate_applicant/<?= $a->id; ?>/<?= $application->jobID; ?>/<?= $row->appID; ?>/<?= $a->record_no; ?>" class="btn btn-success tooltips" data-placement="top" data-toggle="tooltip" data-original-title="View Applicant Information"><i class="fas fa-file-alt"></i></a>&nbsp; &nbsp;
                                                                <?php } ?>
                                                            
                                                                <?php }elseif($this->session->eg == 2){ ?>
                                                                    <?php if(!empty($rating)){ ?>
                                                                        <?php if($rating->eval_id2 == 0){?>
                                                                            <a href="#" class="open-AddBookDialog" data-toggle="modal" data-target=".demorating" data-id="<?= $row->appID; ?>" data-item="<?= $a->record_no; ?>" data-job="<?php if($rating->demo_rating != 0.00001){echo $rating->demo_rating; } ?>"><i class="mdi mdi-notebook-outline btn btn-lg tooltips <?php if($rating->demo_rating != 0.00001){echo 'text-warning';}else{echo 'text-primary';} ?>" data-placement="top" data-toggle="tooltip" data-original-title="Rate"></i></a>
                                                                        <?php }else{ ?>
                                                                            <?php if($rating->eval_id2 == $this->session->id){ ?>
                                                                            <a href="#" class="open-AddBookDialog" data-toggle="modal" data-target=".demorating" data-id="<?= $row->appID; ?>" data-item="<?= $a->record_no; ?>" data-job="<?php if($rating->demo_rating != 0.00001){echo $rating->demo_rating; } ?>"><i class="mdi mdi-notebook-outline btn btn-lg tooltips <?php if($rating->demo_rating != 0.00001){echo 'text-warning';}else{echo 'text-primary';} ?>" data-placement="top" data-toggle="tooltip" data-original-title="Rate"></i></a>
                                                                        <?php } ?>
                                                                    <?php } ?>
                                                                
                                                                    <?php  }else{ ?>
                                                                    <a href="#" class="open-AddBookDialog" data-toggle="modal" data-target=".cdemorating" data-id="<?= $row->appID; ?>" data-item="<?= $a->record_no; ?>"><i class="mdi mdi-notebook-outline btn btn-lg tooltips text-info" data-placement="top" data-toggle="tooltip" data-original-title="Rate"></i></a>
                                                                <?php } ?>

                                                            <?php }elseif($this->session->eg == 3){ ?>
                                                                
                                                              <?php if(!empty($rating)){ ?>
                                                                    <?php if($rating->eval_id3 == 0){?>
                                                                        <a href="#" class="open-AddBookDialog" data-toggle="modal" data-target=".trrating" data-id="<?= $row->appID; ?>" data-item="<?= $a->record_no; ?>" data-job="<?php if($rating->tr_rating != 0.00001){echo $rating->tr_rating; } ?>"><i class="mdi mdi-notebook-outline btn btn-lg tooltips <?php if($rating->tr_rating != 0.00001){echo 'text-primary';}else{echo 'text-warning';} ?>" data-placement="top" data-toggle="tooltip" data-original-title="Rate"></i></a>
                                                                    <?php }else{ ?>
                                                                            <?php if($rating->eval_id3 == $this->session->id){ ?>
                                                                            <a href="#" class="open-AddBookDialog" data-toggle="modal" data-target=".trrating"  data-id="<?= $row->appID; ?>" data-item="<?= $a->record_no; ?>" data-job="<?php if($rating->tr_rating != 0.00001){echo $rating->tr_rating; } ?>"><i class="mdi mdi-notebook-outline btn btn-lg tooltips <?php if($rating->tr_rating != 0.00001){echo 'text-warning';}else{echo 'text-primary';} ?>" data-placement="top" data-toggle="tooltip" data-original-title="Rate"></i></a>
                                                                        <?php } ?>
                                                                    <?php } ?>
                                                              
                                                              <?php }else{ ?>
                                                                <a href="#" class="open-AddBookDialog" data-toggle="modal" data-target=".trcrating" data-id="<?= $row->appID; ?>" data-item="<?= $a->record_no; ?>"><i class="mdi mdi-notebook-outline btn btn-lg tooltips text-purple" data-placement="top" data-toggle="tooltip" data-original-title="Rate"></i></a>
                                                              <?php } ?>



                                                                <?php } ?>



                                                  

                                                        

                                                    </td>
                                                </tr>
										    <?php	} ?>
                                        </tbody>

                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- end row -->

                        <?php } ?>


                        


                    </div>
                    <!-- end container-fluid -->

                </div>
                <!-- end content -->

                
                <!--  Modal content for the above example -->
                <div id="myModal" class="modal fade demorating" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-primary">
                                                        <h5 class="modal-title text-white" id="myLargeModalLabel">DEMO RATING</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                    </div>
                                                    <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                        <p class="text-danger"><i>Note: Maximum allowed value is 35</i></p>
                                                            <div class="card">
                                                                <div class="card-body">

                                                                <form class="parsley-examples" action="<?= base_url(); ?>pages/update_dm_rate/<?= $a->id; ?>/<?= $job->jobID; ?>" method="post">
                                                                    <input type="hidden" name="app_id" id="id">
                                                                    <input type="hidden" name="record_no" id="item">
                                                                        
                                                                    <div class="row">
                                                                        <div class="col-lg-12">
                                                                                <div class="form-group">
                                                                                    <label>Rating</label>
                                                                                    <input type="text" id="job" class="form-control" name="demo_rating" >
                                                                                </div>	
                                                                        </div>	
                                                                        
                                                                    </div>

                              
                                                                    
                                                                        <div class="form-group text-right mb-0">
                                                                            <button class="btn btn-primary waves-effect waves-light mr-1" type="submit">
                                                                                Submit
                                                                            </button>
                                                                        </div>

                                                                    </form>
                                                                </div>
                                                            </div>
                                                            <!-- end card -->
                                                        </div>
                                                        <!-- end col -->

                            
                                                    </div>
                                                    <!-- end row -->


                                                    </div>
                                                </div>
                                                <!-- /.modal-content -->
                                            </div>
                                            <!-- /.modal-dialog -->
                                        </div>
                <!-- /.modal -->

                <!--  Modal content for the above example -->
                <div id="myModal" class="modal fade cdemorating" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-primary">
                                                        <h5 class="modal-title text-white" id="myLargeModalLabel">DEMO RATING</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                    </div>
                                                    <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <p class="text-danger"><i>Note: Maximum allowed value is 35</i></p>
                                                            <div class="card">
                                                                <div class="card-body">

                                                                <form class="parsley-examples" action="<?= base_url(); ?>pages/update_cdm_rate/<?= $a->id; ?>/<?= $job->jobID; ?>" method="post">
                                                                    <input type="hidden" name="app_id" id="id">
                                                                    <input type="hidden" name="record_no" id="item">
                                                                        
                                                                    <div class="row">
                                                                        <div class="col-lg-12">
                                                                                <div class="form-group">
                                                                                    <label>Rating</label>
                                                                                    <input onchange="limiter_demo(this);" type="text" id="job" class="form-control" name="demo_rating" >
                                                                                </div>	
                                                                        </div>	
                                                                        
                                                                    </div>

                              
                                                                    
                                                                        <div class="form-group text-right mb-0">
                                                                            <button class="btn btn-primary waves-effect waves-light mr-1" type="submit">
                                                                                Submit
                                                                            </button>
                                                                        </div>

                                                                    </form>
                                                                </div>
                                                            </div>
                                                            <!-- end card -->
                                                        </div>
                                                        <!-- end col -->

                            
                                                    </div>
                                                    <!-- end row -->


                                                    </div>
                                                </div>
                                                <!-- /.modal-content -->
                                            </div>
                                            <!-- /.modal-dialog -->
                                        </div>
                <!-- /.modal -->


                <!--  Modal content for the above example -->
                <div id="myModal" class="modal fade trrating" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-info">
                                                        <h5 class="modal-title text-white" id="myLargeModalLabel">TEACHER'S REFLECTION RATING</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                    </div>
                                                    <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <p class="text-danger"><i>Note: Maximum allowed value is 25</i></p>
                                                            <div class="card">
                                                                <div class="card-body">

                                                                <form class="parsley-examples" action="<?= base_url(); ?>pages/update_tr_rate/<?= $a->id; ?>/<?= $job->jobID; ?>" method="post">
                                                                    <input type="hidden" name="app_id" id="id">
                                                                    <input type="hidden" name="record_no" id="item">
                                                                        
                                                                    <div class="row">
                                                                        <div class="col-lg-12">
                                                                                <div class="form-group">
                                                                                    <label>Rating</label>
                                                                                    <input type="text" id="job" class="form-control" name="tr_rating" >
                                                                                </div>	
                                                                        </div>	
                                                                        
                                                                    </div>

                              
                                                                    
                                                                        <div class="form-group text-right mb-0">
                                                                            <button class="btn btn-info waves-effect waves-light mr-1" type="submit">
                                                                                Submit
                                                                            </button>
                                                                        </div>

                                                                    </form>
                                                                </div>
                                                            </div>
                                                            <!-- end card -->
                                                        </div>
                                                        <!-- end col -->

                            
                                                    </div>
                                                    <!-- end row -->


                                                    </div>
                                                </div>
                                                <!-- /.modal-content -->
                                            </div>
                                            <!-- /.modal-dialog -->
                                        </div>
                <!-- /.modal -->

                <!--  Modal content for the above example -->
                <div id="myModal" class="modal fade trcrating" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-danger">
                                                        <h5 class="modal-title text-white" id="myLargeModalLabel">TEACHER REFLECTION RATING</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                    </div>
                                                    <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <p class="text-danger"><i>Note: Maximum allowed value is 25</i></p>
                                                            <div class="card">
                                                                <div class="card-body">

                                                                <form class="parsley-examples" action="<?= base_url(); ?>pages/update_trc_rate/<?= $a->id; ?>/<?= $job->jobID; ?>" method="post">
                                                                    <input type="hidden" name="app_id" id="id">
                                                                    <input type="hidden" name="record_no" id="item">
                                                                        
                                                                    <div class="row">
                                                                        <div class="col-lg-12">
                                                                                <div class="form-group">
                                                                                    <label>Rating</label>
                                                                                    <input type="text" id="job" class="form-control" name="tr_rating" >
                                                                                </div>	
                                                                        </div>	
                                                                        
                                                                    </div>

                              
                                                                    
                                                                        <div class="form-group text-right mb-0">
                                                                            <button class="btn btn-danger waves-effect waves-light mr-1" type="submit">
                                                                                Submit
                                                                            </button>
                                                                        </div>

                                                                    </form>
                                                                </div>
                                                            </div>
                                                            <!-- end card -->
                                                        </div>
                                                        <!-- end col -->

                            
                                                    </div>
                                                    <!-- end row -->


                                                    </div>
                                                </div>
                                                <!-- /.modal-content -->
                                            </div>
                                            <!-- /.modal-dialog -->
                                        </div>
                <!-- /.modal -->

                <!--  Search Applicantion -->
                <div class="modal fade aa" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" style="display: none;" aria-hidden="true">
                                                    <div class="modal-dialog modal-lg">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="myLargeModalLabel">Search Applicant</h5>
                                                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                            </div>
                                                            <div class="modal-body">
                                                                
                                                            <?php 
                                                                $attributes = array('class' => 'parsley-examples');
                                                                echo form_open('Pages/applicant_applications', $attributes);
                                                            ?>
                                                              
                                                                <div class="form-group row">
                                                                    <label for="inputPassword5" class="col-md-3 col-form-label">Record No.</label>
                                                                    <div class="col-md-9">
                                                                        <input type="text" class="form-control" name="record_no" >
                                                                    </div>
                                                                </div>

                                                                <?php  $position = $this->session->position; if($position==='Evaluator'): ?>

                                                                <div class="form-group row">
                                                                    <label for="inputPassword5" class="col-md-3 col-form-label">Year</label>
                                                                    <div class="col-md-9">
                                                                    <select class="form-control" name="fy" required>
                                                                        <option></option>
                                                                    <?php 
                                                                    $firstYear = (int)date('Y');
                                                                    $lastYear = $firstYear + 5;
                                                                    
                                                                    for($i=$firstYear;$i<=$lastYear;$i++)
                                                                    { 
                                                                        echo '<option';
                                                                        if($i == date('Y')){echo " selected ";}
                                                                        echo ' value='.$i.'>'.$i.'</option>';
                                                                    }
                                                                    ?>
                                                                    </select>
                                                                    </div>
                                                                </div>

                                                                <?php endif; ?>
                                                                


                                                          
                                                            
                                                                <div class="form-group mb-0 justify-content-end row">
                                                                    <div class="col-md-9">
                                                                        <input type="submit" name="submit" value="Submit" class="btn btn-primary waves-effect waves-light">
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
                                                            <input type="text"  name="appStatus" class="form-control" value="" />
                                                        </div>

                                                        <div class="form-group col-md-12">
                                                            <label>Notes</label>
                                                            <textarea class="form-control" rows="3" name="note"></textarea>
                                                        </div>
                                                        
                                                        
                                                            <input type="hidden" name="jobID" id="id" value="">
                                                            <input type="hidden"  id="field" name="empEmail" class="form-control" value="" />
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
                <!-- /.modal -->
                

                

