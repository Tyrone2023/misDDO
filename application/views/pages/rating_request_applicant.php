
            <!-- ============================================================== -->
            <!-- Start Page Content here -->
            <!-- ============================================================== -->
            <?php 
                                                        $jobTypes = [
                                                            1 => '- Elementary',
                                                            2 => '- Secondary',
                                                            3 => '- Junior High School',
                                                            4 => '- Senior High School'

                                                        ];

                                                        // What the applicant asked to retain. r_type 2 means Demo & TR for
                                                        // teaching positions and Interview & Written Examination for the rest.
                                                        $retentionScopeLabel = function ($scope, $pType) {
                                                            if ((int) $scope === 1) {
                                                                return "<span class='badge badge-warning'>Retention of Ratings (All Scores)</span>";
                                                            }
                                                            if ((int) $scope !== 2) {
                                                                return '<span class="text-muted">&mdash;</span>';
                                                            }
                                                            return "<span class='badge badge-purple'>"
                                                                . (((int) $pType === 1)
                                                                    ? 'Retention of Demo and TR Ratings'
                                                                    : 'Retention of Interview and Written Examination Ratings')
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
                                    <h4 class="header-title mb-4">List of Rating Request<br />
                                        <?php if(isset($job)){?><span class="float-left badge badge-primary inline mt-2"><?= $job->jobTitle; ?></span><?php } ?>
                                    </h4><br />
                                        <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                        <!-- <table id="datatable-buttons" class="table table-striped table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;"> -->
                                        <thead>
                                            <tr>
                                                <th>Applicant No.</th>
                                                <th>Last Name</th>
												<th>Middle Name</th>
												<th>First Name</th>
                                                <th>Position Applied</th>
                                                <th>Date Submitted</th>
                                                <th>Request Type</th>
                                                <th>Request Status</th>
                                                <th>Retained</th>
                                                <th style="text-align:center">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach($data as $row){ 
                                            

                                            $b = $this->Common->one_cond_row('hris_applicant','id',$row->applicant_id);
                                            if(!empty($b)){
                                                $a = $this->Common->one_cond_row('hris_applicant','id',$row->applicant_id);
                                                $page = 'ma';
                                                $id_no = $a->id;
                                            }else{
                                                $a = $this->Common->one_cond_row('hris_staff','IDNumber',$row->applicant_id);
                                                $page = 'ma_staff';
                                                $id_no = $a->IDNumber;
                                            }
                                            $job = $this->Common->one_cond_row('hris_jobvacancy','jobID',$row->job_id);
                                            if(!empty($row->app_id)){
                                                $rating = $this->Common->one_cond_row('hris_applications_rating','appID', $row->app_id);
                                                $app = $this->Common->one_cond_row('hris_applications','appID', $row->app_id);
                                            }

                                           

                                            //if(isset($a)){ 
                                                ?>
                                                <tr>
                                                    <td><a target="_blank" href="<?= base_url(); ?>Pages/<?= $page; ?>/<?= $id_no; ?>/<?= $job->jobID; ?>/<?php  if(!empty($app->pre_school)){echo $app->pre_school;} ?>"><?php echo strtoupper(!empty($b) ? $a->record_no : $a->IDNumber); ?></a> </td>
                                                    <td><?= $a->LastName; ?> </td>
                                                    <td><?= $a->MiddleName; ?></td>
                                                    <td><?= $a->FirstName; ?></td>
                                                    <td><?= $job->jobTitle; ?> <?=  $jobTypes[$job->job_type] ?? ''; ?></td>    
                                                    <td><?= $row->rdate; ?> </td>
                                                    <?php $pType = (int)($row->p_type ?? 1); ?>
                                                    <td><?= $retentionScopeLabel($row->r_type, $pType); ?></td>
                                                    <td>
                                                        <?php
                                                            $requestStat = (int) $row->stat;
                                                            $denyReason  = trim((string)($row->deny_reason ?? ''));
                                                            if($requestStat === 0){
                                                                echo "<span class='badge badge-warning'>Under Review</span>";
                                                            }elseif($requestStat === 2){
                                                                echo "<span class='badge badge-danger'>Denied</span>";
                                                            }else{
                                                                echo "<span class='badge badge-success'>Confirmed</span>";
                                                            }
                                                        ?>
                                                        <?php if($requestStat === 2 && $denyReason !== ''){ ?>
                                                            <button type="button"
                                                                    class="btn btn-sm btn-outline-danger ml-1 rrDenyReasonBtn"
                                                                    data-toggle="modal"
                                                                    data-target="#rrDenyReasonModal"
                                                                    data-reason="<?= html_escape($denyReason); ?>"
                                                                    data-position="<?= html_escape($job->jobTitle . ' ' . ($jobTypes[$job->job_type] ?? '')); ?>"
                                                                    data-date="<?= html_escape((string)($row->adate ?? '')); ?>">
                                                                Reason
                                                            </button>
                                                        <?php } ?>
                                                    </td>
                                                    <td>
                                                        <?php
                                                            // Only meaningful once the request has been granted.
                                                            echo ($requestStat === 1)
                                                                ? $retentionScopeLabel($row->granted_scope ?? 0, $pType)
                                                                : '<span class="text-muted">&mdash;</span>';
                                                        ?>
                                                    </td>
                                                    <td>
                                                    <?php if($requestStat === 0){ ?>
                                                    <a onclick="return confirm('Are you sure?')" href="<?= base_url(); ?>Pages/rr_delete/<?= $row->id; ?>" class="btn btn-sm btn-danger">Delete</a>
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

                        <!-- Denial reason -->
                        <div class="modal fade" id="rrDenyReasonModal" tabindex="-1" role="dialog" aria-labelledby="rrDenyReasonModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content">
                                    <div class="modal-header bg-danger">
                                        <h5 class="modal-title text-white" id="rrDenyReasonModalLabel">
                                            <i class="mdi mdi-close-circle-outline mr-1"></i>Retention Request Denied
                                        </h5>
                                        <button type="button" class="close text-white" data-dismiss="modal" aria-hidden="true">&times;</button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <label class="font-weight-bold">Position Applied</label>
                                            <p id="rrDenyPosition" class="form-control-plaintext mb-2"></p>
                                        </div>
                                        <div class="form-group">
                                            <label class="font-weight-bold">Date Denied</label>
                                            <p id="rrDenyDate" class="form-control-plaintext mb-2"></p>
                                        </div>
                                        <div class="form-group mb-0">
                                            <label class="font-weight-bold">Reason</label>
                                            <div id="rrDenyReasonText" class="form-control bg-light" style="min-height:80px; white-space:pre-wrap;"></div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-light waves-effect" data-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
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

                $(document).on("click", ".rrDenyReasonBtn", function () {
                    var $btn = $(this);
                    $("#rrDenyPosition").text($btn.data("position") || "—");
                    $("#rrDenyDate").text($btn.data("date") || "—");
                    $("#rrDenyReasonText").text($btn.data("reason") || "No reason was recorded.");
                });
            </script>

             
                                      

