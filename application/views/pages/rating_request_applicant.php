
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
                                                    <td><?php if($row->r_type == 1){echo "<span class='badge badge-warning'>Retention of Ratings (All Criteria)";}else{echo "<span class='badge badge-purple'>Retention of Demo and TR Ratings";} ?></span></td>
                                                    <td><?php if($row->stat == 0){echo "<span class='badge badge-warning'>Under Review";}else{echo "<span class='badge badge-success'>Confirmed";} ?></td>
                                                    <td>
                                                    <?php if($row->stat == 0){ ?>
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

             
                                      

