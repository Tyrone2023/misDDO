<!-- ============================================================== -->
            <!-- Start Page Content here -->
            <!-- ============================================================== -->

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
                        11 => '- SHS Academic and Core Subjects',
                        12 => '- SHS Arts and Design Track',
                        13 => '- SHS Sports Track',
                        14 => '- SHS Technical-Vocational(TVL) Track',
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
                                    <!-- <h4 class="page-title">Basic Tables</h4> -->
                                    <div class="page-title-right">
                                        <ol class="breadcrumb p-0 m-0">
                                            <li class="breadcrumb-item"><a href="<?= base_url(); ?>Pages/ma/<?= $this->uri->segment(3); ?>/<?= $this->uri->segment(4); ?>/<?= $this->uri->segment(6); ?>">Application</a></li>
                                            <li class="breadcrumb-item active">Applicant Status</li>
                                        </ol>
                                    </div>
                                   
                                    <div class="clearfix"></div>
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
                                </div>
                            </div>
                        </div>
                        <!-- end page title -->

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-striped mb-0">
                                                <thead>
                                                    <tr class="bg-info text-center text-white">
                                                        <th colspan="2">APPLICATION DETAILS <?= $this->uri->segment(4); ?></th> 
                                                    </tr>
                                                </thead>
                                                <tbody>

                                                    <tr>
                                                        <th width="30%" class="text-right">Position Applied</th>
                                                        <td style="background: #9ddcf4; color:#222"><?= $job->jobTitle; ?> <?= $jobTypes[$job->job_type] ?? ''; ?></td>
                                                    </tr>
                                                    <tr>
                                                        <th class="text-right">District</th>
                                                        <td style="background: #9ddcf4; color:#222"><?= $app->district; ?></td>
                                                    </tr>
                                                    <tr>
                                                        <th class="text-right">Preferred School</th>
                                                        <?php $s = $this->Common->one_cond_row('schools', 'schoolID',$app->pre_school); ?>
                                                        <td style="background: #9ddcf4; color:#222"><?= $s->schoolName; ?></td>
                                                    </tr>
                                                    <tr>
                                                        <th class="text-right">Status</th>
                                                        <td style="background: #9ddcf4;"><span class="badge badge-warning"><?= $app->appStatus; ?></span></td>
                                                    </tr>
                                                    
                                                    
                                                    <tr>
                                                        <td colspan="2"></td>
                                                    </tr>

                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="timeline timeline-left">
                                            <article class="timeline-item alt">
                                                <div class="text-left">
                                                    <div class="time-show first">
                                                        <a href="#" class="btn btn-info w-lg" data-toggle="modal" data-target=".comment">Remarks/Comment</a>
                                                    </div>
                                                </div>
                                            </article>

                                            <?php foreach($data as $row){ ?>
                                            <article class="timeline-item">
                                                <div class="timeline-desk">
                                                    <div class="panel">
                                                        <div class="timeline-box">
                                                            <span class="arrow"></span>
                                                            <span class="timeline-icon <?php if($row->res != $this->session->username){echo "bg-info";} ?>"><i class="mdi mdi-checkbox-blank-circle-outline"></i></span>
                                                            <h4 class="<?php if($row->res != $this->session->username){echo "text-info";} ?>"><?= $row->dateSubmitted; ?></h4>
                                                            <p class="timeline-date text-muted"><small><?= $row->timeSubmitted; ?></small></p>
                                                            <p><?= $row->appStatus; ?><br /><span class="badge badge-info noti-icon-badge">Responsible : <?= $row->res; ?></span></p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </article>

                                            <?php } ?>
                                            

                                        </div>
                                        <!-- end timeline -->
                                    </div>
                                    <!-- end card-body -->
                                </div>
                                <!-- end card -->
                            </div>
                            <!-- end col -->
                        </div>
                        <!-- end row -->

                    </div>
                    <!-- end container-fluid -->

                </div>
                <!-- end content -->

                                        <!--  Modal content for the above example -->
                                        <div id="myModal" class="modal fade comment" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-info">
                                                        <h5 class="modal-title text-white" id="myLargeModalLabel">Remarks/Comment</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                    </div>
                                                    <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <div class="card bg-in">
                                                                <div class="card-body">

                                                                <form class="parsley-examples" action="<?= base_url(); ?>pages/comment/<?= $this->uri->segment(3); ?>/<?= $this->uri->segment(4); ?>/<?= $this->uri->segment(5); ?>/<?= $this->uri->segment(6); ?>" method="post">
                                                            
                                                                        
                                                                    <div class="form-group row">
                                                                        <label class="col-lg-12 col-form-label" for="example-textarea"></label>
                                                                        <div class="col-lg-12">
                                                                            <textarea class="form-control" rows="5" name="comment" id="example-textarea"></textarea>
                                                                        </div>
                                                                    </div>
                                                                    <input type="hidden" value="<?= $app->appID; ?>" name="app_id">
                                                                        
                              
                                                                    
                                                                        <div class="form-group text-right mb-0">
                                                                            <button onclick="return confirm('Are you sure?')" class="btn btn-info waves-effect waves-light mr-1" type="submit">
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


                                    
                                       

                                         


                                        



