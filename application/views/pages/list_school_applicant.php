
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
                                        
                                        <div class="page-title-right">
                                            <ol class="breadcrumb p-0 m-0">
                                                <li class="breadcrumb-item"><a href="<?= base_url(); ?>Page/jobVacancy">Job Vacancies</a></li>
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
                                    <h4 class="header-title mb-4">List of School<br /></h4><br />
                                        <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                        <thead>
                                            <tr>
                                                <th>School Name</th>
												<th>School ID</th>
                                                <th style="text-align:center">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                                foreach($data as $row){ 
                                                $school = $this->Common->one_cond_row('schools', 'schoolID',$row->pre_school);
                                                $acount = $this->Common->three_cond_count_row('hris_applications', 'jobID', $row->jobID,'pre_school',$school->schoolID,'appStatus','Application Submitted');

                                            ?>
                                            <tr>
                                                <td><?= $school->schoolName; ?></td>
                                                <td><?= $row->pre_school; ?></td>
                                                <td class="text-center">

                                                
                                                <a href="<?=base_url(); ?>Pages/district_applicant/<?= $row->jobID; ?>/0/<?= $row->pre_school; ?>" class="btn btn-success tooltips" data-placement="top" data-toggle="tooltip" data-original-title="Validate Applicant">
                                                <?php if($acount->num_rows() !=0){?>
                                                    <span class="badge badge-warning rounded-circle noti-icon-badge"><?= $acount->num_rows(); ?></span>
                                                    <?php } ?>
                                                <i class="mdi mdi-file-document-box-outline"></i></a>&nbsp;&nbsp;&nbsp;&nbsp;
                                                <a href="<?=base_url(); ?>Pages/district_applicant/<?= $row->jobID; ?>/1/<?= $row->pre_school; ?>" class="btn btn-purple tooltips" data-placement="top" data-toggle="tooltip" data-original-title="All Applicant"><i class="mdi mdi-file-document-box-outline"></i></a>&nbsp;&nbsp;&nbsp;&nbsp;

                                                

                                                </td>
                                            </tr>
                                            <?php } ?>
                                        </tbody>

                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- end row -->


                        
                        <!-- end row -->


                    </div>
                    <!-- end container-fluid -->

                </div>
                <!-- end content -->
