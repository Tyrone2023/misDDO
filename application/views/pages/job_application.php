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
                                    <!-- <h4 class="page-title">Basic Tables</h4> -->
                                   
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
                                        <h4 class="header-title mb-4">Application List</h4>
                                        <div class="table-responsive">
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
                    15 => '- Elementary - SPIMS',
                    16 => '- Junior High School - SPIMS',
                    17 => '- DOST - (RA 7687)',
                    18 => '- DOST - (RA 10612)',
                    19 => '- (SST I)',
                    20 => '- FOR TESTING PURPOSES (DO NOT APPLY)'
                ];
                                        ?>

                                        <table class="table mb-0">
                                        <thead>
                                            <tr>
                                                <th>Position Applied</th>
                                                <th>District</th>
                                                <th>Year</th>
                                                <th>Preferred School</th>
                                                <th class="text-center">Manage</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                                 foreach($data as $row){ 
                                                 $jv = $this->Common->one_cond_row('hris_jobvacancy', 'jobID', $row->jobID);
                                                 $s = $this->Common->one_cond_row('schools', 'schoolID', $row->pre_school);
                                                 $notify = $this->Common->four_cond_count_row_one_not_equal('hris_applications_track', 'jobID',$row->jobID,'applicant_id',$row->applicant_id,'nstat',0,'res',$this->session->username);
                                            ?>
                                            <tr>
                                                <td><?= $jv->jobTitle; ?> <?= $jobTypes[$jv->job_type] ?? ''; ?></td>
                                                <td><?= $row->district; ?></td>
                                                <td><?= $jv->sy; ?></td>
                                                <td><?php  if(isset($s->schoolName)){echo $s->schoolName; } ?></td>

                                                <td class="text-center">
                                                <?php if($notify->num_rows() >= 1){?>
                                                    <a href="<?= base_url(); ?>Pages/nofitychangestat/<?= $this->uri->segment(3)?>/<?= $row->jobID; ?>/<?= $s->schoolID; ?>">
                                                    <span class="badge badge-pink rounded-circle noti-icon-badge tooltips" data-placement="top" data-toggle="tooltip" data-original-title="Notification"><?= $notify->num_rows(); ?></span></a>
                                                    <?php } ?>&nbsp; &nbsp;

                                                        <a  href="<?= base_url(); ?>Pages/<?php if($this->session->position == 'reg'){echo "ma";}else{echo "ma_staff";}?>/<?= $this->uri->segment(3)?>/<?= $row->jobID; ?>/<?= $s->schoolID; ?>">
                                                            <i class="fas fa-clipboard-list noti-icon btn btn-info"></i>
                                                        </a>
                                                    
                                                    
                                                    <?php 
                                                        if($this->session->position == 'reg' || $this->session->position == 'user'){ 
                                                            if($jv->a_stat == 0){
                                                                if($jv->position == 1){
                                                    ?>

                                                        <a href="#" data-toggle="modal" data-job="<?= $row->jobID; ?>" data-target=".applyedit" class="open-AddBookDialog"><i class="fas fa-pencil-alt btn btn-primary tooltips" data-placement="top" data-toggle="tooltip" data-original-title="Edit"></i></a>
                                                    <?php } ?>
                                                    <a onclick="return confirm('Are you sure?')" href="<?=base_url(); ?>Pages/ap_cancel/<?= $row->appID; ?>"><i class="fas fa-times btn btn-danger tooltips" data-placement="top" data-toggle="tooltip" data-original-title="Cancel My Application"></i></a>

                                                    <?php }} ?> 
                                                </td>
                                             
                                            </tr>
                                            <?php } ?>

                                        </tbody>
                                                 </table>
                                            
                                        </div>
                                    </div>
                                </div>

                            
                        </div>
                        <!--- end row -->


                    </div>
                    <!-- end container-fluid -->

                </div>
                <!-- end content -->


                


                             


