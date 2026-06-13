
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
                                    <?php if($this->session->position==='Human Resource Admin' || $this->session->position==='HR Staff' || $this->session->userdata('position')==='Super Admin' || $this->session->position==='asds'): ?>
                                        
                                        <?php if(isset($efr)){if($efr->num_rows() >= 1){ ?>
                                            <!-- <a onclick="return confirm('Are you sure?')" href="<?= base_url(); ?>Pages/efrv2" class="btn btn-info">Endorse for Rating</a> -->
                                        <?php }} ?>
                                    <?php endif; ?>
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
                                    <h4 class="header-title mb-4">List of Applicants<br />
                                        <?php if(isset($job)){?><span class="float-left badge badge-primary inline mt-2"><?= $job->jobTitle; ?></span><?php } ?>
                                    </h4><br />
                                        <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                        <!-- <table id="datatable-buttons" class="table table-striped table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;"> -->
                                        <thead>
                                            <tr>
                                                <th>Last Name</th>
												<th>Middle Name</th>
												<th>First Name</th>
                                                <th>Applicant No.</th>
                                                <th>Date Submitted</th>
                                                <th style="text-align:center">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach($data as $row){ 
                                           
                                            $b = $this->Common->one_cond_row('hris_applicant','empEmail',$row->empEmail);
                                            if(!empty($b)){
                                                $a = $this->Common->one_cond_row('hris_applicant','empEmail',$row->empEmail);
                                            }else{
                                                $a = $this->Common->one_cond_row('hris_staff','IDNumber',$row->empEmail);
                                            }
                                           
                                                
                                             
                                            //if(isset($a)){
                                                ?>
                                                <tr>
                                                    <td><?= $a->LastName; ?></td>
                                                    <td><?= $a->MiddleName; ?></td>
                                                    <td><?= $a->FirstName; ?></td>
                                                    <td><?php echo strtoupper(!empty($b) ? $a->record_no : $a->IDNumber); ?></td>
                                                    <td><?= $row->dateSubmitted; ?></td>
                                                    <td>
                                                        <div class="text-center">
                                                        <a target="_blank" href="<?= base_url(); ?>pages/<?php echo !empty($b) ? 'ma' : 'ma_staff'; ?>/<?php echo strtoupper(!empty($b) ? $a->id : $a->IDNumber); ?>/<?= $row->jobID; ?>/<?= $row->pre_school; ?>/<?= $row->appID; ?>/<?php if(!empty($b)){echo strtoupper($a->record_no);}else{echo strtoupper($a->IDNumber); }?>/" class="text-success tooltips" data-placement="top" data-toggle="tooltip" data-original-title="View Applicant Information"><i class="fas fa-file-alt"></i></a>&nbsp; &nbsp;
                                                  
                                                    <?php if($this->session->position == 'doceval' || $this->session->position == 'asds'){?>
                                                        <?php if(!empty($b)){$ren = $a->id;}else{$ren = $a->IDNumber; }?>
                                                       <?php 
                                                            $dq_res = $this->Common->three_cond_row('hris_app_dq', 'jobID',$row->jobID,'appID',$row->appID,'apID',$ren); 
                                                            if(isset($dq_res)){
                                                            $user = $this->Common->one_cond_row('users', 'id',$dq_res->res); 
                                                       ?>
                                                       <a href="#" data-toggle="modal" data-target=".dq<?= $dq_res->id; ?>" class="text-purple"><i class="fas fa-user-check tooltips" data-placement="top" data-toggle="tooltip" data-original-title="<?= $user->fname.' '.$user->lname; ?>"></i></a>
                                                       <?php } ?>
                                                       <?php if(isset($dq_res)){?>
                                                       <!--  Modal content for the above example -->
                                                            </div>
                                                        <div class="modal fade dq<?= $dq_res->id; ?>" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" style="display: none;" aria-hidden="true">
                                                        <div class="modal-dialog modal-lg">
                                                            <div class="modal-content">
                                                                <div class="modal-header bg-info">
                                                                    <h5 class="modal-title" id="myLargeModalLabel">Remarks</h5>
                                                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                                </div>
                                                                <div class="modal-body">
                                                                <div class="row">
                                                                    <div class="col-lg-12">
                                                                        <div class="card">
                                                                            <div class="card-body">
                                                                                <?php 
                                                                                    $job = $this->Common->one_cond_row('hris_jobvacancy','jobID',$dq_res->jobID);
                                                                                    $dq =  $this->Common->one_cond_row('hris_app_dq','appID',$dq_res->appID);
                                                                                    $ap =  $this->Common->one_cond_row('hris_applicant','id',$dq_res->apID);
                                                                                ?>

                                                                                <form class="parsley-examples" action="#" method="post">
                                                                                

                                                                                <?php if($job->job_type == 2){?>

                                                                                <div class="row">
                                                                                    <div class="col-lg-12">
                                                                                        <label class="col-form-label">Junior HS Specialization</label>
                                                                                        <input type="text" readonly class="form-control" name="bd"  value="<?= $ap->jhss; ?>" >
                                                                                        
                                                                                    </div>	

                                                                                    

                                                                                    
                                                                                </div><br />	
                                                                                
                                                                                <?php }elseif($job->job_type == 3){ ?>

                                                                                    <div class="row">
                                                                                    <div class="col-lg-12">
                                                                                        <label class="col-form-label">Senior HS Specialization</label>
                                                                                        <input type="text" readonly class="form-control" name="bd"  value="<?= $ap->shss; ?>" >
                                                                                    </div>	

                                                                                    

                                                                                    
                                                                                </div><br />


                                                                                <?php } ?>
                                                                                
                                                                                
                                                                                
                                                                                
                                                                                <div class="row">
                                                                                    <div class="col-lg-12">	
                                                                                            <h4 class="header-title">Mandatory documents presented <span class='text-danger'>*</span></h4>
                                                                                            <div class="form-group">
                                                                                            <div class="custom-control custom-checkbox">
                                                                                            <input type="checkbox" class="custom-control-input" id="customCheck1" <?php if($dq->li == 1){echo " Checked ";}?> name="li" disabled>
                                                                                            <label class="custom-control-label text-xs" for="customCheck1">Letter of Intent</label>
                                                                                        </div>
                                                                                        <div class="custom-control custom-checkbox">
                                                                                            <input type="checkbox" class="custom-control-input" id="customCheck2" <?php if($dq->da_pds == 1){echo " Checked ";}?> name="da_pds" disabled>
                                                                                            <label class="custom-control-label text-xs" for="customCheck2">Duly Accomplished PDS</label>
                                                                                        </div>
                                                                                        <div class="custom-control custom-checkbox">
                                                                                            <input type="checkbox" class="custom-control-input" id="customCheck3" <?php if($dq->prc == 1){echo " Checked ";}?> name="prc" disabled>
                                                                                            <label class="custom-control-label text-xs" for="customCheck3">Valid PRC License ( except for SHS which can be applied by non- licensed teachers)</label>
                                                                                        </div>
                                                                                        <div class="custom-control custom-checkbox">
                                                                                            <input type="checkbox" class="custom-control-input" id="customCheck4" <?php if($dq->trbd == 1){echo " Checked ";}?> name="trbd" disabled>
                                                                                            <label class="custom-control-label text-xs" for="customCheck4">Transcript of Records of the Baccalaureate Degree</label>
                                                                                        </div>
                                                                                        <div class="custom-control custom-checkbox">
                                                                                            <input type="checkbox" class="custom-control-input" id="customCheck5" <?php if($dq->omni == 1){echo " Checked ";}?> name="omni" disabled>
                                                                                            <label class="custom-control-label text-xs" for="customCheck5">Omnibus Sworn Statement</label>
                                                                                        </div>
                                                                                                

                                                                                            </div>	
                                                                                    </div>	
                                                                                    
                                                                                </div>

                                                                                <div class="row">
                                                                                    <div class="col-lg-12">	
                                                                                            <h4 class="header-title">Is the address in the MIS matched with the Voters Certificate/Barangay Certificate? <span class='text-danger'>*</span></h4>
                                                                                            <div class="form-group">
                                                                                            <div class="custom-control custom-radio">
                                                                                                <input type="radio" id="local1" name="local" <?php if($dq->local == 0){echo " Checked ";}?> required class="custom-control-input" value="0" disabled>
                                                                                                <label class="custom-control-label text-xs" for="local1">Yes</label>
                                                                                            </div>
                                                                                            <div class="custom-control custom-radio">
                                                                                                <input type="radio" id="local2" name="local" <?php if($dq->local == 1){echo " Checked ";}?> required class="custom-control-input" value="1" disabled>
                                                                                                <label class="custom-control-label text-xs" for="local2">No</label>
                                                                                            </div>         

                                                                                            </div>	
                                                                                    </div>	
                                                                                    
                                                                                </div>

                                                                                <div class="row">
                                                                                    <div class="col-lg-12">	
                                                                                            <h4 class="header-title">Remarks <span class='text-danger'>*</span></h4>
                                                                                            <div class="form-group">
                                                                                            <div class="custom-control custom-radio">
                                                                                            <input type="radio" id="customRadio1" required name="remarks" <?php if($dq->remarks == 1){echo " Checked ";}?> class="custom-control-input" value="1" disabled>
                                                                                            <label class="custom-control-label text-xs" for="customRadio1">Qualified</label>
                                                                                        </div>
                                                                                        <div class="custom-control custom-radio">
                                                                                            <input type="radio" id="customRadio2" required name="remarks" <?php if($dq->remarks == 2){echo " Checked ";}?> class="custom-control-input" value="2" disabled>
                                                                                            <label class="custom-control-label text-xs" for="customRadio2">Disqualified</label>
                                                                                        </div>
                                                                                                                                

                                                                                            </div>	
                                                                                    </div>	
                                                                                    
                                                                                </div>

                                                                                <?php if(isset($dq)){if($dq->remarks == 2){?>
                                                                                    
                                                                                <div class="row">
                                                                                    <div class="col-lg-12">
                                                                                            <div class="form-group">
                                                                                                <label>If disqualified, state the reasons here. </label>
                                                                                                <textarea class="form-control" rows="5" name="reason" id="example-textarea"><?= $dq->reason; ?></textarea>
                                                                                            </div>	
                                                                                    </div>	
                                                                                    
                                                                                </div>	
                                                                                <?php } } }?>

                                                                                
                                                                                    
                                                                                    

                                                                                    <div class="form-group text-right mb-0">
                                                                                    </div>

                                                                                </form>
                                                                            </div>
                                                                        </div>
                                                                        <!-- end card -->
                                                                    </div>
                                                                    <!-- end col -->

                                        
                                                                </div>
                                                                <!-- end row -->
                                                    </td>
                                                    <?php } ?>
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
                                                </div>
                                                <!-- /.modal-content -->
                                            </div>
                                            <!-- /.modal-dialog -->
                                        </div>
                                        <!-- /.modal -->


                        


                        


                    </div>
                    <!-- end container-fluid -->

                </div>
                <!-- end content -->

                <script>
                $(document).on("click", ".passingID", function () {
                    $(this).attr('data-id');
                $(".modal-body").val( ids );
                });
            </script>

             
                                      

