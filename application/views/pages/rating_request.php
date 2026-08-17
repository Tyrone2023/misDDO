
            <!-- ============================================================== -->
            <!-- Start Page Content here -->
            <!-- ============================================================== -->
                                                     <?php 
                                                        $jobTypes = [
                                                            1 => '- Elementary',
                                                            2 => '- Secondary',
                                                            3 => '- Junior High School',
                                                            4 => '- Senior High School',
                                                            5 => '- kindergarten',
                                                            6 => '- IPED Elementary',
                                                            7 => '- IPED Secondary',
                                                            8 => '- IPED Junior High School',
                                                            9 => '- IPED Senior High School',
                                                            10 => '- SNED',

                                                        ];

                                                        // What the applicant asked to retain. r_type 2 means Demo & TR for
                                                        // teaching positions and Interview & Written Examination for the rest.
                                                        $retentionScopeLabel = function ($scope, $pType) {
                                                            if ((int) $scope === 1) {
                                                                return "<span class='badge badge-warning'>All scores</span>";
                                                            }
                                                            if ((int) $scope !== 2) {
                                                                return "<span class='badge badge-light'>Not recorded</span>";
                                                            }
                                                            return "<span class='badge badge-purple'>"
                                                                . (((int) $pType === 1) ? 'Demo &amp; TR only' : 'Interview &amp; Written only')
                                                                . "</span>";
                                                        };
                                                    ?>

            <div class="content-page">
                <div class="content">

                    <!-- Start Content-->
                    <div class="container-fluid">

                        <!-- start page title -->
                        <div class="row">
                            <div class="col-12">
                                <div class="page-title-box">
                                    
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
                                    <h4 class="header-title mb-4">List of Applicants</h4><br />
                                        <table id="datatable2" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                        <!-- <table id="datatable-buttons" class="table table-striped table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;"> -->
                                        <thead>
                                            <tr>
                                                <th>Applicant No.</th>
                                                <th>Last Name</th>
												<th>Middle Name</th>
												<th>First Name</th>
                                                <th>Position Applied</th>
                                                <th>Date Submitted</th>
                                                <th>Application Date</th>
                                                <th>Request Type</th>
                                                <th style="text-align:center">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach($data as $row){ 
                                            

                                            // $b = $this->Common->one_cond_row('hris_applicant','id',$row->applicant_id);
                                            // if(!empty($b)){
                                            //     $a = $this->Common->one_cond_row('hris_applicant','id',$row->applicant_id);
                                            //     $page = 'ma';
                                            //     $id_no = $a->id;
                                            // }else{
                                            //     $a = $this->Common->one_cond_row('hris_staff','IDNumber',$row->applicant_id);
                                            //     $page = 'ma_staff';
                                            //     $id_no = $a->IDNumber;
                                            // }
                                            // $job = $this->Common->one_cond_row('hris_jobvacancy','jobID',$row->job_id);
                                            // if(!empty($row->app_id)){
                                            //     $rating = $this->Common->one_cond_row('hris_applications_rating','appID', $row->app_id);
                                            //     $app = $this->Common->one_cond_row('hris_applications','appID', $row->app_id);
                                            // }

                                           

                                            //if(isset($a)){ 
                                                ?>
                                                <tr>
                                                    <td><a target="_blank" href="<?= base_url(); ?>Pages/<?= $row->st; ?>/<?= $row->id; ?>/<?= $row->job_id; ?>/<?= $row->pre_school; ?>"><?= strtoupper($row->code); ?></a></td>
                                                    <td><?= $row->LastName; ?></td>
                                                    <td><?= $row->MiddleName; ?></td>
                                                    <td><?= $row->FirstName; ?></td>
                                                    <td><?= $row->jobTitle; ?> <?=  $jobTypes[$row->job_type] ?? ''; ?></td>    
                                                    <td><?= $row->rdate; ?></td>
                                                    <td><?= $row->sy; ?></td>
                                                    <?php $pType = (int)($row->p_type ?? $row->job_position ?? 1); ?>
                                                    <td><?= $retentionScopeLabel($row->r_type, $pType); ?></td>
                                                    <td>
                                                    <?php if($pType == 1){?>
                                                    <a href="<?= base_url(); ?>Pages/request_rating_granted/<?= $row->rid; ?>/<?= $row->job_id; ?>/<?= $row->applicant_id; ?>/<?= $row->code; ?>/<?= $row->app_id; ?>/<?= $row->r_type; ?>" class="btn btn-sm btn-success">Retain</a>
                                                    <?php }else{ ?>
                                                    <a href="<?= base_url(); ?>Pages/request_rating_granted_none/<?= $row->rid; ?>/<?= $row->job_id; ?>/<?= $row->applicant_id; ?>/<?= $row->code; ?>/<?= $row->app_id; ?>/<?= $row->r_type; ?>" class="btn btn-sm btn-success">Retain</a>                                                    <?php } ?>
                                                    <a onclick="return confirm('Are you sure?')" href="<?= base_url(); ?>Pages/rr_delete/<?= $row->rid; ?>" class="btn btn-sm btn-danger">Delete</a>
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


                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body table-responsive">
                                    <h4 class="header-title mb-4">List of retained requests</h4><br />
                                        <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                        <!-- <table id="datatable-buttons" class="table table-striped table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;"> -->
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Applicant No.</th>
                                                <th>Last Name</th>
												<th>Middle Name</th>
												<th>First Name</th>
                                                <th>Position Applied</th>
                                                <th>Date Submitted</th>
                                                <!-- <th>Status</th> -->
                                                <th>Request Type</th>
                                                <th>Retained</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $c=1; foreach($granted as $row){ 
                                            //$jobs = $this->Common->one_cond_row('hris_jobvacancy','jobID',$row->job_id);
                                            // if($jobs->jvStatus == "Open"){
                                            

                                            // $b = $this->Common->one_cond_row('hris_applicant','id',$row->applicant_id);
                                            // if(!empty($b)){
                                            //     $a = $this->Common->one_cond_row('hris_applicant','id',$row->applicant_id);
                                            //     $page = 'ma';
                                            //     $id_no = $a->id;
                                            // }else{
                                            //     $a = $this->Common->one_cond_row('hris_staff','IDNumber',$row->applicant_id);
                                            //     $page = 'ma_staff';
                                            //     $id_no = $a->IDNumber;
                                            // }
                                            // $job = $this->Common->one_cond_row('hris_jobvacancy','jobID',$row->job_id);
                                            // if(!empty($row->app_id)){
                                            //     $rating = $this->Common->one_cond_row('hris_applications_rating','appID', $row->app_id);
                                            //     $ratings = $this->Common->one_cond('hris_applications_rating','appID', $row->app_id);
                                            //     $app = $this->Common->one_cond_row('hris_applications','appID', $row->app_id);
                                            // }
                                           

                                            //if(isset($a)){ 
                                                ?>
                                                <tr>
                                                    <td><?= $c++; ?></td>
                                                    <td><a target="_blank" href="<?= base_url(); ?>Pages/<?= $row->st; ?>/<?= $row->id; ?>/<?= $row->job_id; ?>/<?= $row->pre_school; ?>"><?= strtoupper($row->code); ?></a></td>
                                                    <td><?= $row->LastName; ?></td>
                                                    <td><?= $row->MiddleName; ?></td>
                                                    <td><?= $row->FirstName; ?></td>
                                                    <td><?= $row->jobTitle; ?> <?=  $jobTypes[$row->job_type] ?? ''; ?></td>    
                                                    <td><?= $row->rdate; ?></td>
                                                    <!-- <td><a href="<?= base_url(); ?>Pages/special_change_stat/<?= $row->r_type; ?>/<?= $app->appID; ?>">change</a> <?php if ($app->appStatus == 'Application Submitted'){echo $app->appStatus; } ?> </td> -->
                                                    <?php $pType = (int)($row->p_type ?? $row->job_position ?? 1); ?>
                                                    <td>
                                                        <?= $retentionScopeLabel($row->r_type, $pType); ?>
                                                        <!-- <a onclick="return confirm('Are you sure?')" href="<?= base_url(); ?>Pages/rr_delete/<?= $row->id; ?>" class="btn btn-sm btn-danger">Delete</a> -->
                                                    </td>
                                                    <td><?= $retentionScopeLabel($row->granted_scope ?? 0, $pType); ?></td>
                                                    
                                                </tr>
										    <?php	}  ?>
                                        </tbody>

                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- end row -->

                        






                        


                        


                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body table-responsive">
                                    <h4 class="header-title mb-4">List of denied requests</h4><br />
                                        <table id="datatable3" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Applicant No.</th>
                                                <th>Last Name</th>
                                                <th>Middle Name</th>
                                                <th>First Name</th>
                                                <th>Position Applied</th>
                                                <th>Date Submitted</th>
                                                <th>Date Denied</th>
                                                <th>Reason</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $d=1; foreach(($denied ?? []) as $row){ ?>
                                                <tr>
                                                    <td><?= $d++; ?></td>
                                                    <td><a target="_blank" href="<?= base_url(); ?>Pages/<?= $row->st; ?>/<?= $row->id; ?>/<?= $row->job_id; ?>/<?= $row->pre_school; ?>"><?= strtoupper($row->code); ?></a></td>
                                                    <td><?= $row->LastName; ?></td>
                                                    <td><?= $row->MiddleName; ?></td>
                                                    <td><?= $row->FirstName; ?></td>
                                                    <td><?= $row->jobTitle; ?> <?=  $jobTypes[$row->job_type] ?? ''; ?></td>
                                                    <td><?= $row->rdate; ?></td>
                                                    <td><?= $row->adate; ?></td>
                                                    <td style="white-space: normal; min-width: 260px;"><?= nl2br(html_escape((string) $row->deny_reason)); ?></td>
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

                <script>
                $(document).on("click", ".passingID", function () {
                    $(this).attr('data-id');
                $(".modal-body").val( ids );
                });
            </script>

             
                                      
