
            <!-- ============================================================== -->
            <!-- Start Page Content here -->
            <!-- ============================================================== -->
                    <?php 
                        $emp_stat = $this->Common->one_cond('hris_hire_stat','stat',0); 
                        $nature_app = $this->Common->one_cond('hris_hire_stat','stat',1); 
                        $nature_sep = $this->Common->one_cond('hris_hire_stat','stat',2);
                        $district = $this->Common->no_cond('district'); 
                        $pt = $this->Common->one_cond('hris_hire_stat','stat',3);
                        $plantilla = $this->Common->no_cond_select('hris_plantilla','id,itemNo');
                        $staff = $this->Hiring_model->getStaffByPosition();
                        $vice = $this->Common->no_cond_select('hris_staff','FirstName,LastName,MiddleName,NameExtn,IDNumber');
                        $school = $this->Common->one_cond_select_ob('schools','schoolName, schoolID,schoolType','schoolType',0,'schoolName','ASC');
                    ?>

            <div class="content-page">
                <div class="content">

                    <!-- Start Content-->
                    <div class="container-fluid">

                        <!-- start page title -->
                        <div class="row">
                            <div class="col-12">
                                <div class="page-title-box">
                                <?php if($job->promotion == 1){?>
                                    <a class="btn sm btn-primary" target="_blank" href="<?= base_url(); ?>Pages/car_rqa_promotion/<?= $this->input->get('jobID'); ?>">RQA Printable View</a>
                                        <a class="btn sm btn-success" target="_blank" href="<?= base_url(); ?>Pages/car_rqa1_promotion/<?= $this->input->get('jobID'); ?>">RQA For Posting</a>
                                        <a class="btn sm btn-purple" target="_blank" href="<?= base_url(); ?>Pages/car_rqa1_promotion_region/<?= $this->input->get('jobID'); ?>">RQA For Region</a>
                                <?php }else{?>


                                <?php if($job->position == 1){ ?>
                                
                                        <a class="btn sm btn-primary" target="_blank" href="<?= base_url(); ?>Pages/car_rqa/<?= $this->input->get('jobID'); ?>">RQA Printable View</a>
                                        <a class="btn sm btn-success" target="_blank" href="<?= base_url(); ?>Pages/car_rqa1/<?= $this->input->get('jobID'); ?>">RQA For Posting</a>
                                        <a class="btn sm btn-info" href="<?= base_url(); ?>Page/rqa_municipality?jobID=<?= $this->input->get('jobID'); ?>&jobTitle=<?= $this->input->get('jobTitle'); ?>">Per Municipality</a>   
                                        <a class="btn sm btn-purple" target="_blank" href="<?= base_url(); ?>Pages/car/<?= $this->input->get('jobID'); ?>">CAR</a>
                                        <a class="btn sm btn-warning" target="_blank" href="<?= base_url(); ?>Pages/car_nlet/<?= $this->input->get('jobID'); ?>">Non LET Passers</a>
                                        <a class="btn sm btn-purple" target="_blank" href="<?= base_url(); ?>Pages/car_nlet_pos/<?= $this->input->get('jobID'); ?>">Non LET Passers posting</a>
                                        <?php if($job->job_type == 1){ ?>
                                                <a class="btn sm btn-primary" target="_blank" href="<?= base_url(); ?>Pages/rqa_cluster/<?= $this->input->get('jobID'); ?>">CAR - RQA V1</a>
                                            <a class="btn sm btn-success" target="_blank" href="<?= base_url(); ?>Pages/rqa_clusterv2/<?= $this->input->get('jobID'); ?>">CAR - RQA V2</a>
                                            
                                        <?php }elseif($job->job_type == 3 || $job->job_type == 2 || $job->job_type == 7 || $job->job_type == 8){ ?>
                                                <a class="btn sm btn-primary" target="_blank" href="<?= base_url(); ?>Pages/rqa_cluster_jhs/<?= $this->input->get('jobID'); ?>">CAR - RQA V1</a>
                                                <a class="btn sm btn-success" target="_blank" href="<?= base_url(); ?>Pages/rqa_cluster_jhsv2/<?= $this->input->get('jobID'); ?>">CAR - RQA V2</a>
                                        <?php }else{ ?>
                                                <a class="btn sm btn-primary" target="_blank" href="<?= base_url(); ?>Pages/rqa_cluster_shs/<?= $this->input->get('jobID'); ?>">CAR - RQA V1</a>
                                                <a class="btn sm btn-success" target="_blank" href="<?= base_url(); ?>Pages/rqa_cluster_shsv2/<?= $this->input->get('jobID'); ?>">CAR - RQA V2</a>
                                        <?php } ?>
                                                <a class="btn sm btn-warning" target="_blank" href="<?= base_url(); ?>Pages/car_rqa_complete/<?= $this->input->get('jobID'); ?>">CAR - RQA V3</a>
                                        <?php }elseif($job->position == 2){ ?>
                                                <a class="btn sm btn-primary" target="_blank" href="<?= base_url(); ?>Pages/car_rqa_administrative/<?= $this->input->get('jobID'); ?>">RQA Printable View</a>
                                                <a class="btn sm btn-success" target="_blank" href="<?= base_url(); ?>Pages/car_rqa_administrative_posting/<?= $this->input->get('jobID'); ?>">RQA For Posting</a>
                                                <a class="btn sm btn-primary" target="_blank" href="<?= base_url(); ?>Pages/car_rqa_administrative/<?= $this->input->get('jobID'); ?>/1">RQA Printable View w/ eSignature</a>
                                                <a class="btn sm btn-success" target="_blank" href="<?= base_url(); ?>Pages/car_rqa_administrative_posting/<?= $this->input->get('jobID'); ?>/1">RQA For Posting w/ eSignature</a>
                                                <a class="btn sm btn-purple" target="_blank" href="<?= base_url(); ?>Pages/car_rqa_administrative_region/<?= $this->input->get('jobID'); ?>">RQA For Region</a>

                                        <?php }elseif($job->position == 3){ ?>
                                                <a class="btn sm btn-primary" target="_blank" href="<?= base_url(); ?>Pages/car_rqa_related/<?= $this->input->get('jobID'); ?>">RQA Printable View</a>
                                                <a class="btn sm btn-success" target="_blank" href="<?= base_url(); ?>Pages/car_rqa_related_posting/<?= $this->input->get('jobID'); ?>">RQA For Posting</a>
                                                <a class="btn sm btn-primary" target="_blank" href="<?= base_url(); ?>Pages/car_rqa_related/<?= $this->input->get('jobID'); ?>/1">RQA Printable View w/ eSignature</a>
                                                <a class="btn sm btn-success" target="_blank" href="<?= base_url(); ?>Pages/car_rqa_related_posting/<?= $this->input->get('jobID'); ?>/1">RQA For Posting w/ eSignature</a>
                                        <?php }elseif($job->position == 4){ ?>
                                                <a class="btn sm btn-primary" target="_blank" href="<?= base_url(); ?>Pages/car_rqa_administrative/<?= $this->input->get('jobID'); ?>">RQA Printable View</a>
                                                <a class="btn sm btn-success" target="_blank" href="<?= base_url(); ?>Pages/car_rqa_administrative_posting/<?= $this->input->get('jobID'); ?>">RQA For Posting</a>
                                                <a class="btn sm btn-primary" target="_blank" href="<?= base_url(); ?>Pages/car_rqa_administrative/<?= $this->input->get('jobID'); ?>/1">RQA Printable View w/ eSignature</a>
                                                <a class="btn sm btn-success" target="_blank" href="<?= base_url(); ?>Pages/car_rqa_administrative_posting/<?= $this->input->get('jobID'); ?>/1">RQA For Posting w/ eSignature</a>
                                                <a class="btn sm btn-primary" target="_blank" href="<?= base_url(); ?>Pages/car_rqa_administrative_mun/<?= $this->input->get('jobID'); ?>/0">RQA Municipality</a>
                                                <a class="btn sm btn-success" target="_blank" href="<?= base_url(); ?>Pages/car_rqa_administrative_mun/<?= $this->input->get('jobID'); ?>/1">RQA Municipality For Posting w/ eSignature</a>
                                                

                                        <?php }else{ ?>
                                                <a class="btn sm btn-primary" target="_blank" href="<?= base_url(); ?>Pages/car_rqa_non/<?= $this->input->get('jobID'); ?>">RQA Printable View non-teaching</a>
                                                <a class="btn sm btn-success" target="_blank" href="<?= base_url(); ?>Pages/car_rqa1_none/<?= $this->input->get('jobID'); ?>">RQA For Posting</a>
                                                <a class="btn sm btn-primary" target="_blank" href="<?= base_url(); ?>Pages/car_rqa_non/<?= $this->input->get('jobID'); ?>/1">RQA Printable View non-teaching w/ eSignature</a>
                                                <a class="btn sm btn-success" target="_blank" href="<?= base_url(); ?>Pages/car_rqa1_none/<?= $this->input->get('jobID'); ?>/1">RQA For Posting w/ eSignature</a>
                                        <?php } ?>

                                        <?php } ?>
                            
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
                                    <h4 class="header-title mb-4">Registry of Qualified Applicants<br/><span class="float-left badge badge-primary inline mt-2"><?php echo $_GET['jobTitle']; ?></span></h4><br />
                                       
                                        <!-- <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;"> -->
                                        <table id="datatable-buttons" class="table table-striped table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                        
                                        <?php if($job->promotion == 1){?>
                                        <thead>
                                            <tr>
                                                <th>No.</th>
                                                <th>Fullname</th>
                                                <th>Applicant Code.</th>
												<th>Education</th>
                                                <th>Training</th>
                                                <th>Experience</th>
                                                <th>Performance</th>
                                                <th>COIs</th>
                                                <th>NCOIs</th>
                                                <th>Total</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                                $ren=1;
                                                $promotion = $this->Page_model->rqa_promotion($job->jobID); 
                                                foreach($promotion as $row){
                                            ?>
                                            <tr>
                                                <td><?= $ren++; ?></td>
                                                <td><?= strtoupper($row->FirstName).' '.strtoupper($row->MiddleName).' '.strtoupper($row->LastName).' '.strtoupper($row->NameExtn); ?></td>
                                                <td><?= $row->code; ?></td>
                                                <td><?= ($row->educ != 0.00001) ? $row->educ : ''; ?></td>
                                                <td><?= ($row->trainings != 0.00001) ? $row->trainings : ''; ?></td>
                                                <td><?= ($row->experience != 0.00001) ? $row->experience : ''; ?></td>
                                                <td><?= ($row->performance != 0.00001) ? $row->performance : ''; ?></td>
                                                <td><?= ($row->ppstco != 0.00001) ? $row->ppstco : ''; ?></td>
                                                <td><?= ($row->ppstpa != 0.00001) ? $row->ppstpa : ''; ?></td>
                                                <td><?= number_format($row->total_points, 2); ?></td>
                                                <td>
                                                    <?php if(!empty($hire)){ ?>
                                                    <a target="_blank" href="<?= base_url(); ?>personnel_profile/<?= $hire->IDNumber; ?>#docs" class="btn btn-info waves-effect waves-light">Profile</a>
                                                    <?php }else{ ?>
                                                    <button type="button" class="btn btn-primary waves-effect waves-light open-AddBookDialog" data-id="<?= $row->appID; ?>" data-job="<?= $job->jobID; ?>" data-item="<?= $row->st; ?>" data-appid="<?= $row->code; ?>" data-toggle="modal" data-target=".renren_guapo">Hire</button>
                                                    <?php } ?>
                                                </td>
                                            </tr>
                                            <?php } ?>

                                        </tbody>
                                        <?php }else{ ?>
                                        <?php if($job->position == 1){?>
                                            <thead>
                                            <tr>
                                                <th>No.</th>
                                                <th>Fullname</th>
                                                <th>Applicant<br /> Code.</th>
												<th>Education</th>
                                                <th>Training</th>
                                                <th>Experience</th>
                                                <th>LET Rating</th>
                                                <th>Demo</th>
                                                <th>Teacher<br /> Reflection</th>
                                                <th>Total</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                                $ren=1;
                                                $teaching = $this->Page_model->rqa($job->jobID); 
                                                foreach($teaching as $row){
                                                    $hire = $this->Common->one_cond_row('hris_hire','appID',$row->appID);
                                            ?>
                                            <tr>
                                                <td><?= $ren++; ?></td>
                                                <td>
                                                    <?= strtoupper($row->LastName) . ', ' . strtoupper($row->FirstName) . ' ' . strtoupper($row->MiddleName) . ' ' . strtoupper($row->NameExtn); ?>
                                                </td>                                                
                                                <td><?= $row->code; ?></td>
                                                <td><?= ($row->education != 0.00001) ? $row->education : ''; ?></td>
                                                <td><?= ($row->training != 0.00001) ? $row->training : ''; ?></td>
                                                <td><?= ($row->experience != 0.00001) ? $row->experience : ''; ?></td>
                                                <td><?= ($row->let_rating != 0.00001) ? $row->let_rating : ''; ?></td>
                                                <td><?= ($row->demo_rating != 0.00001) ? $row->demo_rating : ''; ?></td>
                                                <td><?= ($row->tr_rating != 0.00001) ? $row->tr_rating : ''; ?></td>
                                                <td><?= number_format($row->total_points, 2); ?></td>
                                                <td>
                                                    <?php if(!empty($hire)){ ?>
                                                    <a target="_blank" href="<?= base_url(); ?>personnel_profile/<?= $hire->IDNumber; ?>#docs" class="btn btn-info waves-effect waves-light">Profile</a>
                                                    <?php }else{ ?>
                                                    <button type="button" class="btn btn-primary waves-effect waves-light open-AddBookDialog" data-id="<?= $row->appID; ?>" data-job="<?= $job->jobID; ?>" data-item="<?= $row->st; ?>" data-appid="<?= $row->code; ?>" data-toggle="modal" data-target=".renren_guapo">Hire</button>
                                                    <?php } ?>
                                                </td>

                                            </tr>
                                            <?php } ?>

                                        </tbody>
                                            
                                        <?php }else{ ?>

                                            <thead>
                                            <tr>
                                                <th>No.</th>
                                                <th>Fullname</th>
                                                <th>Applicant<br /> Code.</th>
												<th>Education</th>
                                                <th>Training</th>
                                                <th>Experience</th>
                                                <th>Performance<br /> Rating</th>
                                                <th>Outstanding<br /> Accomplishments</th>
                                                <th>Application<br /> Of Education</th>
                                                <th>Application<br /> Of Learning<br /> & Development</th>
                                                <th>Interview</th>
                                                <th>Written</th>
                                                <th>Total</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                                $ren=1;
                                                $noneteaching = $this->Common->qualified_applicant_list_nt($job->jobID); 
                                                foreach($noneteaching as $row){
                                            ?>
                                            <tr>
                                                    <td><?= $ren++; ?></td>
                                                    <td><?= strtoupper($row->FirstName).' '.strtoupper($row->MiddleName).' '.strtoupper($row->LastName).' '.strtoupper($row->NameExtn); ?></td>
                                                    <td><?= $row->code; ?></td>
                                                    <td><?= ($row->educ != 0.00001) ? $row->educ : ''; ?></td>
                                                    <td><?= ($row->trainings != 0.00001) ? $row->trainings : ''; ?></td>
                                                    <td><?= ($row->experience != 0.00001) ? $row->experience : ''; ?></td>
                                                    <td><?= ($row->performance != 0.00001) ? $row->performance : ''; ?></td>
                                                    <td><?= ($row->oa != 0.00001) ? $row->oa : ''; ?></td>
                                                    <td><?= ($row->ae != 0.00001) ? $row->ae : ''; ?></td>
                                                    <td><?= ($row->ald != 0.00001) ? $row->ald : ''; ?></td>
                                                    <td><?= ($row->interview != 0.00001) ? $row->interview : ''; ?></td>
                                                    <td><?= ($row->written != 0.00001) ? $row->written : ''; ?></td>
                                                    <td><?php if(isset($row)){echo number_format($row->total_points, 2);} ?></td>
                                                    <td>
                                                    <?php if(!empty($hire)){ ?>
                                                    <a target="_blank" href="<?= base_url(); ?>personnel_profile/<?= $hire->IDNumber; ?>#docs" class="btn btn-info waves-effect waves-light">Profile</a>
                                                    <?php }else{ ?>
                                                    <button type="button" class="btn btn-primary waves-effect waves-light open-AddBookDialog" data-id="<?= $row->appID; ?>" data-job="<?= $job->jobID; ?>" data-item="<?= $row->st; ?>" data-appid="<?= $row->code; ?>" data-toggle="modal" data-target=".renren_guapo">Hire</button>
                                                    <?php } ?>
                                                </td>
                                                </tr>

                                            <?php } ?>

                                        </tbody>
                                    
                                        <?php }} ?>

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

                                        <!--  Modal content for the above example -->
                                        <div class="modal fade renren_guapo" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" style="display: none;" aria-hidden="true">
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="myLargeModalLabel">Add Information</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                    </div>
                                                    <div class="modal-body">
                                                    <?php 
                                                        $attributes = array('class' => 'parsley-examples');
                                                        echo form_open_multipart('Pages/hire_applicant', $attributes);
                                                    ?>
                                                    <input type="hidden" id="id" name="appID">
                                                    <input type="hidden" id="job" name="jobID">
                                                    <input type="hidden" id="item" name="item">
                                                    <input type="hidden" id="appid" name="IDNumber">

                                                        <div class="form-group row">
                                                            <label for="inputPassword3" class="col-md-3 col-form-label">District</label>
                                                            <div class="col-md-3">
                                                                <select class="form-control" name="d_id" id="position-select">
                                                                    <option value="0"></option>
                                                                    <?php foreach($district as $row){?>
                                                                    <option value="<?= $row->id; ?>"><?= $row->discription; ?></option>
                                                                    <?php } ?>
                                                                </select>
                                                            </div>

                                                            <label for="inputPassword3" class="col-md-1 col-form-label">School</label>
                                                            <div class="col-md-5">
                                                                <select class="form-control" name="school_id" id="position-select">
                                                                    <option></option>
                                                                    <?php foreach($school as $row){?>
                                                                    <option value="<?= $row->schoolID; ?>"><?= strtoupper($row->schoolName); ?></option>
                                                                    <?php } ?>
                                                                </select>
                                                            </div>
                                                        </div>

                                                        

                                                        <div class="form-group row">
                                                            <label for="inputPassword3" class="col-md-3 col-form-label">Position Title</label>
                                                            <div class="col-md-9">
                                                                <select class="form-control" name="pt" id="position-select" required>
                                                                    <option></option>
                                                                    <?php foreach($pt as $row){?>
                                                                    <option value="<?= $row->id; ?>"><?= $row->description; ?></option>
                                                                    <?php } ?>
                                                                </select>
                                                            </div>
                                                        </div>

                                                        <div class="form-group row">
                                                            <label for="inputPassword3" class="col-md-3 col-form-label">Employment Status</label>
                                                            <div class="col-md-9">
                                                                <select class="form-control" name="emp_stat" id="position-select" required>
                                                                    <option></option>
                                                                    <?php foreach($emp_stat as $row){?>
                                                                    <option value="<?= $row->id; ?>"><?= $row->description; ?></option>
                                                                    <?php } ?>
                                                                </select>
                                                            </div>
                                                        </div>

                                                        <div class="form-group row">
                                                            <label for="inputPassword3" class="col-md-3 col-form-label">Nature Of Appointment</label>
                                                            <div class="col-md-9">
                                                                <select class="form-control" name="appointment" id="position-select">
                                                                    <option></option>
                                                                    <?php foreach($nature_app as $row){?>
                                                                    <option value="<?= $row->id; ?>"><?= $row->description; ?></option>
                                                                    <?php } ?>
                                                                </select>
                                                            </div>
                                                        </div>

                                                        <div class="form-group row">
                                                            <label for="inputPassword3" class="col-md-3 col-form-label">Nature Of Separation</label>
                                                            <div class="col-md-4">
                                                                <select class="form-control" name="separation" id="position-select">
                                                                    <option></option>
                                                                    <?php foreach($nature_sep as $row){?>
                                                                    <option value="<?= $row->id; ?>"><?= $row->description; ?></option>
                                                                    <?php } ?>
                                                                </select>
                                                            </div>
                                                            <label for="inputPassword3" class="col-md-2 col-form-label">Published at</label>
                                                            <div class="col-md-3">
                                                                <select class="form-control" name="pub_at" required>
                                                                    <option></option>
                                                                    <option value="0">N/A</option>
                                                                    <option value="1">CSC JOB PORTAL</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="form-group row">
                                                            <label for="inputPassword3" class="col-md-3 col-form-label">Date Published</label>

                                                            <label for="inputPassword3" class="col-md-1 col-form-label">From</label>
                                                            <div class="col-md-3">
                                                                <input type="date" class="form-control" value="" name="date_pub_from" required>
                                                            </div>
                                                            <label for="inputPassword3" class="col-md-2 col-form-label">To</label>
                                                            <div class="col-md-3">
                                                                <input type="date" class="form-control" value="" name="date_pub_to" required>
                                                            </div>
                                                            
                                                        </div>

                                                        

                                                        <div class="form-group row">
                                                            <label for="inputPassword3" class="col-md-3 col-form-label">Started On</label>
                                                            <div class="col-md-3">
                                                                <input type="date" class="form-control" value="" name="started_on" required>
                                                            </div>
                                                            <label for="inputPassword3" class="col-md-3 col-form-label">Deliberation Held On</label>
                                                            <div class="col-md-3">
                                                                <input type="date" class="form-control" value="" name="del_held" required>
                                                            </div>
                                                            
                                                        </div>

                                                        <div class="form-group row">
                                                            <label for="inputPassword3" class="col-md-3 col-form-label"> Plantilla Item No.</label>
                                                            <div class="col-md-9">
                                                                <select class="form-control" data-toggle="select2" name="plantilla_id" required>
                                                                    <option></option>
                                                                    <?php foreach($plantilla as $row){?>
                                                                    <option value="<?= $row->id; ?>"><?= $row->itemNo; ?> </option>
                                                                    <?php } ?>
                                                                </select>
                                                            </div>
                                                        </div>

                                                        <div class="form-group row">
                                                            <label for="inputPassword3" class="col-md-6 col-form-label">Vice</label>
                                                            <div class="col-md-6">
                                                                <select class="form-control" data-toggle="select2" name="vice">
                                                                    <option value="">&nbsp;</option>
                                                                    <?php foreach($vice as $row){?>
                                                                    <option value="<?= $row->IDNumber; ?>"><?= $row->FirstName; ?> <?= !empty($row->MiddleName) ? strtoupper(substr($row->MiddleName, 0, 1)) . '.' : ''; ?> <?= $row->LastName; ?></option>
                                                                    <?php } ?>
                                                                    <option value="202601">2026 SHS NEW ITEM</option>
                                                                    <option value="202602">2026 JHS NEW ITEM</option>
                                                                    <option value="202603">2026 NEW ITEM</option>
                                                                </select>
                                                            </div>
                                                        </div>

                                                        <div class="form-group row">
                                                            <label for="inputPassword3" class="col-md-6 col-form-label">Schools Division Superintendent</label>
                                                            <div class="col-md-6">
                                                                <select class="form-control" data-toggle="select2" name="sds" required>
                                                                    <option></option>
                                                                    <?php foreach($staff as $row){?>
                                                                    <option value="<?= $row->IDNumber; ?>"><?= $row->FirstName; ?> <?= !empty($row->MiddleName) ? strtoupper(substr($row->MiddleName, 0, 1)) . '.' : ''; ?> <?= $row->LastName; ?></option>
                                                                    <?php } ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="form-group row">
                                                            <label for="inputPassword3" class="col-md-6 col-form-label">Assistant Schools Division Superintendent</label>
                                                            <div class="col-md-6">
                                                                <select class="form-control" data-toggle="select2" name="asds" required>
                                                                    <option></option>
                                                                    <?php foreach($staff as $row){?>
                                                                    <option value="<?= $row->IDNumber; ?>"><?= $row->FirstName; ?> <?= !empty($row->MiddleName) ? strtoupper(substr($row->MiddleName, 0, 1)) . '.' : ''; ?> <?= $row->LastName; ?></option>
                                                                    <?php } ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="form-group row">
                                                            <label for="inputPassword3" class="col-md-6 col-form-label">Administrative Officer  V</label>
                                                            <div class="col-md-6">
                                                                <select class="form-control" data-toggle="select2" name="aov" required>
                                                                    <option></option>
                                                                    <?php foreach($staff as $row){?>
                                                                    <option value="<?= $row->IDNumber; ?>"><?= $row->FirstName; ?> <?= !empty($row->MiddleName) ? strtoupper(substr($row->MiddleName, 0, 1)) . '.' : ''; ?> <?= $row->LastName; ?></option>
                                                                    <?php } ?>
                                                                </select>
                                                            </div>
                                                        </div>

                                                        <div class="form-group row">
                                                            <label for="inputPassword3" class="col-md-3 col-form-label">Salary Rate/SG/Step/Page</label>
                                                            <div class="col-md-3">
                                                                <input type="text" class="form-control" value="" name="salary" placeholder="Salary Rate" required>
                                                            </div>
                                                            <div class="col-md-2">
                                                                <select class="form-control" name="sg" required>
                                                                    <option disabled selected>SG</option>
                                                                    <?php for ($i = 1; $i <= 33; $i++): ?>
                                                                        <option value="<?= $i ?>"><?= $i ?></option>
                                                                    <?php endfor; ?>
                                                                </select>
                                                            </div>
                                                            <div class="col-md-2">
                                                                <select class="form-control" name="step" required>
                                                                    <option disabled selected>Step</option>
                                                                    <?php for ($i = 1; $i <= 8; $i++): ?>
                                                                        <option value="<?= $i ?>"><?= $i ?></option>
                                                                    <?php endfor; ?>
                                                                </select>
                                                            </div>
                                                            <div class="col-md-2">
                                                                <input type="text" class="form-control" value="" name="page" placeholder="Page No." required>
                                                            </div>
                                                        </div>

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

       
       
 