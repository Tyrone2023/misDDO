
            <!-- ============================================================== -->
            <!-- Start Page Content here -->
            <!-- ============================================================== -->
            <?php $job = $this->Common->one_cond_row('hris_jobvacancy','jobID',$this->uri->segment(3));  ?>

            <div class="content-page">
                <div class="content">

                    <!-- Start Content-->
                    <div class="container-fluid">

                        <!-- start page title -->
                        <div class="row">
                            <div class="col-12">
                                <div class="page-title-box">
                                    <div class="page-title-right">
                                        <ol class="breadcrumb p-0 m-0">
                                            <li class="breadcrumb-item"><a href="<?= base_url(); ?>Page/jobVacancy">Job Vacancy</a></li>
                                            <li class="breadcrumb-item active">List of Applicants</li>
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

                                


                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body table-responsive">
                                    <h4 class="header-title mb-4">List of Applicants<br /><span class="float-left badge badge-primary inline mt-2"><?= $job->jobTitle; ?></span></h4><br />
                                        <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                        <!-- <table id="datatable-buttons" class="table table-striped table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;"> -->
                                        <thead>
                                            <tr><?php if($this->session->eg != 3){ ?>
                                                <th>Fullname</th>
                                                <?php } ?>
                                                <th>Applicant No.</th>
                                                <th>Date Submitted</th>
                                                
                                                <?php //if($this->session->eg != 1){ ?>
                                                    <?php if($this->session->position == 'Evaluator' || $this->session->position == 'Admin' || $this->session->position == 'rater' ){ ?>
                                                    <th>Rating </th>
                                                <?php }  ?>
                                                <th>Status</th>
                                                <th style="text-align:center">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                                foreach($data as $row){
                                                    

                                                    // if($job->position == 1){
                                                    //     $b = $this->Common->one_cond_row('hris_applicant','empEmail',$row->empEmail); 
                                                    //     if(!empty($b)){
                                                    //         $a = $this->Common->one_cond_row('hris_applicant','empEmail',$row->empEmail);
                                                    //         $id = $a->record_no;
                                                    //         $a_id = $a->id;
                                                    //         $user='ma';
                                                    //     }else{
                                                    //         $a = $this->Common->one_cond_row('hris_staff','IDNumber',$row->empEmail);
                                                    //         $id = $a->IDNumber;
                                                    //         $a_id = $a->IDNumber;
                                                    //         $user='ma_staff';
                                                    //     }
                                                    //     $rating = $this->Common->two_cond_row('hris_applications_rating','record_no',$id,'appID',$row->appID);
                                                    //     $notify = $this->Common->four_cond_count_row('hris_applications_track', 'jobID',$row->jobID,'applicant_id',$row->applicant_id,'nstat',0,'res',$row->empEmail);

                                                    //     if(!empty($rating)){
                                                    //     $renrenguapo = $rating->demo_rating;
                                                    //     $renrenguapoko = $rating->tr_rating;
                                                    //     $records=$rating->record_no;
                                                    //     }
                                                    // }else{
                                                    //     $b = $this->Common->one_cond_row('hris_applicant','empEmail',$row->empEmail); 
                                                    // if(!empty($b)){
                                                    //     $a = $this->Common->one_cond_row('hris_applicant','empEmail',$row->empEmail);
                                                    //     if($job->position == 1)
                                                    //     $rating = $this->Common->two_cond_row('hris_rating_none','record_no',$a->record_no,'appID',$row->appID);
                                                    //     $notify = $this->Common->four_cond_count_row('hris_applications_track', 'jobID',$row->jobID,'applicant_id',$row->applicant_id,'nstat',0,'res',$row->empEmail);
                                                    //     if(!empty($rating)){
                                                    //     $renrenguapo = $rating->interview;
                                                    //     $renrenguapoko = $rating->written;
                                                    //     $records=$rating->record_no;
                                                    //     }
                                                    //     $id=$a->id;
                                                    //     $user='ma';
                                                    // }else{
                                                    //     $a = $this->Common->one_cond_row('hris_staff','IDNumber',$row->empEmail);
                                                    //     $rating = $this->Common->two_cond_row('hris_rating_none','record_no',$a->IDNumber,'appID',$row->appID);
                                                    //     $notify = $this->Common->four_cond_count_row('hris_applications_track', 'jobID',$row->jobID,'applicant_id',$row->applicant_id,'nstat',0,'res',$row->empEmail);
                                                    //     if(!empty($rating)){
                                                    //     $renrenguapo = $rating->interview;
                                                    //     $renrenguapoko = $rating->written;
                                                    //     $records=$rating->record_no;
                                                    //     }
                                                    //     $id=$a->IDNumber;
                                                    //     $user='ma_staff';
                                                    // }
                                                //}

                                                    
                                            ?>
                                                <tr><?php if($this->session->eg != 3){ ?>
                                                    <td><?= $row->LastName.', '.$row->FirstName; ?> <?php if(!empty($row->MiddleName)){echo substr($row->MiddleName, 0, 1).'.';} ?></td>
                                                    <?php } ?>
                                                    <td><?= $row->code; ?></td>
                                                    <td><?= $row->dateSubmitted; ?></td>
                                                    <?php if($this->session->position == 'Evaluator' || $this->session->position == 'Admin' || $this->session->position == 'rater'){  ?>
                                                    <td>
                                                            <?php if(!empty($rating)){
                                                                //if($this->session->eg != 1){
                                                            ?>
                                                            
                                                                    <?php if($this->session->eg == 2){ ?>
                                                                        <?php if($rating->eval_id2 == $this->session->id){ ?>

                                                                            <?php if($renrenguapo != 0.00001){echo $renrenguapo; } ?>

                                                                        <?php }elseif($renrenguapo != 0.00001){echo "<span class='badge badge-info'>Rated</span>";} ?>

                                                                    <?php }elseif($this->session->eg == 3){ ?>
                                                                        <?php if($rating->eval_id3 == $this->session->id){ ?>
                                                                            <?php if($renrenguapoko != 0.00001){echo $renrenguapoko; } ?>
                                                                        <?php }elseif($renrenguapoko != 0.00001){echo "<span class='badge badge-info'>Rated</span>";} ?>
                                                                    <?php } ?>
                                                                
                                                        
                                                    <?php }  ?>
                                                    </td>
                                                    <?php } ?>

                                                    <?php 
                                                        if(!empty($b)){
                                                            $page = 'ma';
                                                        }else{
                                                            $page = 'ma_staff';
                                                        }
                                                    ?>

                                                    <td><span class="badge badge-warning badge-pill" ><?= $row->appStatus; ?></span></td>
                                                    <td class="text-center">
                                                        <?php if($this->session->position != "Evaluator" && $this->session->position != "rater"){ //ivykate ic ?>
                                                            
                                                            <a  href="<?= base_url(); ?>pages/<?= $user; ?>/<?= $a_id; ?>/<?= $row->jobID; ?>/<?= $row->pre_school; ?>" class="btn btn-info tooltips" data-placement="top" data-toggle="tooltip" data-original-title="View Applicant Information"><i class="fas fa-file-alt"></i></a>&nbsp; &nbsp;
                                                            <?php if($notify->num_rows() >= 1){?>
                                                                <a href="<?= base_url(); ?>Pages/nofitychangestatadmin/<?= $row->applicant_id; ?>/<?= $row->jobID; ?>/<?= $row->pre_school; ?>/?empEmail=<?= $row->empEmail; ?>">
                                                                <span class="badge badge-pink rounded-circle noti-icon-badge tooltips" data-placement="top" data-toggle="tooltip" data-original-title="Notification"><?= $notify->num_rows(); ?></span></a>
                                                            <?php } ?>

                                                        <?php }else{ ?> 
                                                            <?php if($this->session->position == "rater"){ ?>
                                                                <a href="<?= base_url(); ?>pages/<?= $page; ?>/<?php if(!empty($b)){echo $a_id; }else{echo $a->IDNumber; } ?>/<?= $row->jobID; ?>/<?= $row->pre_school; ?>/<?= $row->appID; ?>/<?= $records; ?>" class="btn <?php if($rating->eval_id1 == 0){echo "btn-info";}else{echo "btn-warning";} ?> tooltips" data-placement="top" data-toggle="tooltip" data-original-title="View Applicant Information"><i class="fas fa-file-alt"></i></a>&nbsp; &nbsp;

                                                            <?php }else{ ?> 
                                                            <?php if($this->session->eg == 1){ ?>
                                                                
                                                                
                                                                <?php if(!empty($rating)){if($rating->eval_id1 == 0){ ?>
                                                                    
                                                                    <a href="<?= base_url(); ?>pages/<?= $page; ?>/<?php if(!empty($b)){echo $a_id; }else{echo $a->IDNumber; } ?>/<?= $row->jobID; ?>/<?= $row->pre_school; ?>/<?= $row->appID; ?>/<?= $records; ?>" class="btn <?php if($rating->eval_id1 == 0){echo "btn-info";}else{echo "btn-warning";} ?> tooltips" data-placement="top" data-toggle="tooltip" data-original-title="View Applicant Information"><i class="fas fa-file-alt"></i></a>&nbsp; &nbsp;
                                                                   
                                                                <?php }else{ ?>
                                                                    <?php if($this->session->id == $rating->eval_id1){?>
                                                                    <a href="<?= base_url(); ?>pages/<?= $page; ?>/<?php if(!empty($b)){echo $a_id; }else{echo $a->IDNumber; } ?>/<?= $row->jobID; ?>/<?= $row->pre_school; ?>/<?= $row->appID; ?>/<?= $records; ?>" class="btn btn-warning tooltips" data-placement="top" data-toggle="tooltip" data-original-title="View Applicant Information"><i class="fas fa-file-alt"></i></a>&nbsp; &nbsp;
                                                                    <?php } ?>
                                                                <?php }}else{ ?>
                                                                    <a href="<?= base_url(); ?>pages/<?= $row->st; ?>/<?= $row->id; ?>/<?= $row->jobID; ?>/<?= $row->pre_school; ?>/<?= $row->appID; ?>/<?= $row->code; ?>" class="btn btn-success tooltips" data-placement="top" data-toggle="tooltip" data-original-title="View Applicant Information"><i class="fas fa-file-alt"></i></a>&nbsp; &nbsp;
                                                                <?php } ?>
                                                            
                                                                <?php }elseif($this->session->eg == 2){ ?>

                                                                    
                                                                    
                                                                    <?php if(!empty($rating)){ ?>
                                                                        <?php if($rating->eval_id2 == 0){?>
                                                                            <a href="#" class="open-AddBookDialog" data-toggle="modal" data-target=".cdemorating" data-id="<?= $row->appID; ?>" data-item="<?php echo !empty($b) ? $a->record_no : $a_id; ?>" data-job="<?php if($renrenguapo != 0.00001){echo $renrenguapo; } ?>"><i class="mdi mdi-notebook-outline btn btn-lg tooltips <?php if($renrenguapo != 0.00001){echo 'text-warning';}else{echo 'text-primary';} ?>" data-placement="top" data-toggle="tooltip" data-original-title="Rate"></i></a>
                                                                        <?php }else{ ?>
                                                                            <?php if($rating->eval_id2 == $this->session->id){ ?>
                                                                            <a href="#" class="open-AddBookDialog" data-toggle="modal" data-target=".cdemorating" data-id="<?= $row->appID; ?>" data-item="<?php echo !empty($b) ? $a->record_no : $a_id; ?>" data-job="<?php if($renrenguapo != 0.00001){echo $renrenguapo; } ?>"><i class="mdi mdi-notebook-outline btn btn-lg tooltips <?php if($renrenguapo != 0.00001){echo 'text-warning';}else{echo 'text-primary';} ?>" data-placement="top" data-toggle="tooltip" data-original-title="Rate"></i></a>
                                                                        <?php } ?>
                                                                    <?php } ?>
                                                                
                                                                    <?php  }else{ ?>
                                                                        
                                                                    <a href="#" class="open-AddBookDialog" data-toggle="modal" data-target=".cdemorating" data-id="<?= $row->appID; ?>" data-item="<?php echo !empty($b) ? $a->record_no : $id; ?>"><i class="mdi mdi-notebook-outline btn btn-lg tooltips text-info" data-placement="top" data-toggle="tooltip" data-original-title="Rate"></i></a>
                                                                <?php } ?>

                                                            <?php }elseif($this->session->eg == 3){ ?>
                                                                
                                                              <?php if(!empty($rating)){ ?>
                                                                    <?php if($rating->eval_id3 == 0){?>
                                                                        <a href="#" class="open-AddBookDialog" data-toggle="modal" data-target=".trrating" data-id="<?= $row->appID; ?>" data-item="<?= $records ?>" data-job="<?php if($renrenguapoko != 0.00001){echo $renrenguapoko; } ?>"><i class="mdi mdi-notebook-outline btn btn-lg tooltips <?php if($renrenguapoko != .1){echo 'text-primary';}else{echo 'text-warning';} ?>" data-placement="top" data-toggle="tooltip" data-original-title="Rate"></i></a>
                                                                    <?php }else{ ?>
                                                                            <?php if($rating->eval_id3 == $this->session->id){ ?>
                                                                            <a href="#" class="open-AddBookDialog" data-toggle="modal" data-target=".trrating" data-id="<?= $row->appID; ?>" data-item="<?= $records ?>" data-job="<?php if($renrenguapoko != 0.00001){echo $renrenguapoko; } ?>"><i class="mdi mdi-notebook-outline btn btn-lg tooltips <?php if($renrenguapoko != .1){echo 'text-warning';}else{echo 'text-primary';} ?>" data-placement="top" data-toggle="tooltip" data-original-title="Rate"></i></a>
                                                                        <?php } ?>
                                                                    <?php } ?>
                                                              
                                                              <?php } }else{ ?>
                                                                <a href="#" class="open-AddBookDialog" data-toggle="modal" data-target=".trcrating" data-id="<?= $row->appID; ?>" data-item="<?= $records ?>"><i class="mdi mdi-notebook-outline btn btn-lg tooltips text-purple" data-placement="top" data-toggle="tooltip" data-original-title="Rate"></i></a>
                                                              <?php } ?>



                                                                <?php } ?>

                                                                <!-- <?php  if($row->appStatus == "Endorsed for Rating"){ if($rating->let_rating != 0.00001 && $rating->tr_rating != 0.00001) { ?><a onclick="return confirm('Are you sure?')" class="btn btn-purple btn-sm" href="<?= base_url(); ?>Pages/Ratednirenren/<?= $a_id; ?>/<?= $this->uri->segment(3); ?>/<?= $row->pre_school; ?>/<?= $row->appID; ?>/<?= $page; ?>">Rated</a><?php  } } ?> -->
                                                                

                                                            <?php } ?>
                                                            <?php if($this->uri->segment(4) == 0){?>
                                                            <?php if($this->session->position == 'School'){?>
                                                            <a  href="<?=base_url(); ?>Pages/del_application_school/<?= $row->appID; ?>/<?= $row->jobID; ?>/?ee=<?= $row->empEmail; ?>" class="btn btn-danger tooltips" data-placement="top" data-toggle="tooltip" data-original-title="Delete"><i class="fas fa-times"></i></a>
                                                            <?php } } ?>

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


                        


                    </div>
                    <!-- end container-fluid -->

                </div>
                <!-- end content -->

                <?php if($job->position == 1){?>

              
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

                                                                <form class="parsley-examples" action="<?= base_url(); ?>pages/update_dm_rate/<?= $id; ?>/<?= $this->uri->segment(3); ?>" method="post">
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

                                                                <form class="parsley-examples" action="<?= base_url(); ?>pages/update_dm_rate/<?= $id; ?>/<?= $this->uri->segment(3); ?>" method="post">
                                                                    <input type="hidden" name="app_id" id="id">
                                                                    <input type="hidden" name="record_no" id="item">
                                                                        
                                                                    <div class="row">
                                                                        <div class="col-lg-12">
                                                                                <div class="form-group">
                                                                                    <label>Rating</label>
                                                                                    <input  type="text" id="job" class="form-control" name="demo_rating" >
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

                                                                <form class="parsley-examples" action="<?= base_url(); ?>pages/update_tr_rate/<?= $id; ?>/<?= $this->uri->segment(3); ?>" method="post">
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

                                                                <form class="parsley-examples" action="<?= base_url(); ?>pages/update_trc_rate/<?= $id; ?>/<?= $this->uri->segment(3); ?>" method="post">
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
                 <?php }else{ ?>

                <!--  Modal content for the above example -->
                <div id="myModal" class="modal fade cdemorating" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-purple">
                                                        <h5 class="modal-title text-white" id="myLargeModalLabel">INTERVIEW</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                    </div>
                                                    <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <p class="text-danger"><i>Note: Maximum allowed value is 10</i></p>
                                                            <div class="card">
                                                                <div class="card-body">

                                                                <form class="parsley-examples" action="<?= base_url(); ?>pages/update_rate_none_eval2/<?= $this->uri->segment(3); ?>" method="post">
                                                                    
                                                                    <input type="hidden" name="id" value="<?= $this->uri->segment(3); ?>">
                                                                    <input type="hidden" name="app_id" id="id">
                                                                    <input type="hidden" name="record_no" id="item">
                                                                    <input type="hidden" name="page" value="<?= $this->uri->segment(2); ?>">
                                                                    <input type="hidden" name="col" value="interview">
                                                                    <input type="hidden" name="message" value="Interview">
                                                                    <input type="hidden" name="maxpoint" value="10">
                                                                        
                                                                    <div class="row">
                                                                        <div class="col-lg-12">
                                                                                <div class="form-group">
                                                                                    <label>Rating</label>
                                                                                    <input type="text"  class="form-control" name="interview"  value="" >
                                                                                </div>	
                                                                        </div>	
                                                                        
                                                                    </div>

                              
                                                                    
                                                                        <div class="form-group text-right mb-0">
                                                                            <button class="btn btn-purple waves-effect waves-light mr-1" type="submit">
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
                                                    <div class="modal-header bg-purple">
                                                        <h5 class="modal-title text-white" id="myLargeModalLabel">WRITTEN</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                    </div>
                                                    <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <p class="text-danger"><i>Note: Maximum allowed value is 10</i></p>
                                                            <div class="card">
                                                                <div class="card-body">

                                                                <form class="parsley-examples" action="<?= base_url(); ?>pages/update_rate_none_eval3/<?= $this->uri->segment(3); ?>" method="post">
                                                                    
                                                                    <input type="hidden" name="id" value="<?= $this->uri->segment(3); ?>">
                                                                    <input type="hidden" name="app_id" id="id">
                                                                    <input type="hidden" name="record_no" id="item">
                                                                    <input type="hidden" name="page" value="<?= $this->uri->segment(2); ?>">
                                                                    <input type="hidden" name="col" value="written">
                                                                    <input type="hidden" name="message" value="Written">
                                                                    <input type="hidden" name="maxpoint" value="10">
                                                                        
                                                                    <div class="row">
                                                                        <div class="col-lg-12">
                                                                                <div class="form-group">
                                                                                    <label>Rating</label>
                                                                                    <input type="text"  class="form-control" name="written"  value="" >
                                                                                </div>	
                                                                        </div>	
                                                                        
                                                                    </div>

                              
                                                                    
                                                                        <div class="form-group text-right mb-0">
                                                                            <button class="btn btn-purple waves-effect waves-light mr-1" type="submit">
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


                <?php } ?>
                

                

