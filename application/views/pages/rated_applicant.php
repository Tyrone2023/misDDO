
            <!-- ============================================================== -->
            <!-- Start Page Content here -->
            <!-- ============================================================== -->
                                                     <?php
                                                        $data = $data ?? [];
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
                                    <h4 class="header-title mb-4"><?= htmlspecialchars($title ?? 'List of Applicants', ENT_QUOTES, 'UTF-8'); ?></h4><br />
                                        <table class="table table-bordered mb-0">
                                        <thead>
                                            <tr>
                                                <th>NO.</th>
                                                <th>FULLNAME</th>
                                                <th>APPLICANT CODE</th>
                                                <th>ADDRESS</th>
                                                <th>JOB TITLE</th>
                                                <th>EDUCATION</th>
                                                <th>TRAINING</th>
                                                <th>EXPERIENCE</th>
                                                <th>LET RATING</th>
                                                <th>DEMO RATING</th>
                                                <th>TR RATING</th>
                                                <th>STATUS</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (empty($data)) { ?>
                                                <tr>
                                                    <td colspan="12" class="text-center">No applicants found.</td>
                                                </tr>
                                            <?php } ?>
                                            <?php
                                                $nameCounts = [];
                                                foreach ($data as $r) {
                                                    $fullNameKey = strtoupper(trim($r->FirstName . ' ' . $r->MiddleName . ' ' . $r->LastName));
                                                    $nameCounts[$fullNameKey] = ($nameCounts[$fullNameKey] ?? 0) + 1;
                                                }
                                                ?>
                                                <?php $ivy = 1; foreach ($data as $row) { 
                                                    $fullName = strtoupper(trim($row->FirstName . ' ' . $row->MiddleName . ' ' . $row->LastName));
                                                    $isDuplicate = $nameCounts[$fullName] > 1;
                                                ?>
                                                <tr>
                                                    <td><?= $ivy++; ?></td>
                                                    <td>
                                                        <a style="<?= $isDuplicate ? 'color:#2433fe;' : '' ?>" href="<?= base_url(); ?>pages/<?= $row->st; ?>/<?= $row->id; ?>/<?= $row->jobID; ?>/<?= $row->pre_school; ?>/<?= $row->appID; ?>/<?= $row->code; ?>" target="_blank"><?= $fullName; ?></a>
                                                    </td>
                                                    <td><?= $row->code; ?></td>
                                                    <td><?= strtoupper($row->brgy.' '.$row->resCity); ?></td>
                                                    <td><?php 
                                                        $jobTypes = [
                                                            1 => 'Elementary',
                                                            2 => 'Secondary',
                                                            3 => 'Junior High School',
                                                            4 => 'Senior High School',
                                                            5 => 'kindergarten',
                                                            6 => 'IPED Elementary',
                                                            7 => 'IPED Secondary',
                                                            8 => 'IPED Junior High School',
                                                            9 => 'IPED Senior High School',
                                                            10 => 'SNED',                       
                                                        ];
                                                        echo $jobTypes[$row->job_type] ?? '';
                                                        ?>
                                                    </td>
                                                    <td style="<?= $isDuplicate ? 'color:#2433fe;' : '' ?>" class="text-center"><?= ((float)$row->education == 0.00001) ? '' : $row->education; ?></td>
                                                    <td style="<?= $isDuplicate ? 'color:#2433fe;' : '' ?>" class="text-center"><?= ((float)$row->training == 0.00001) ? '' : $row->training; ?></td>
                                                    <td style="<?= $isDuplicate ? 'color:#2433fe;' : '' ?>" class="text-center"><?= ((float)$row->experience == 0.00001) ? '' : $row->experience; ?></td>
                                                    <td style="<?= $isDuplicate ? 'color:#2433fe;' : '' ?>" class="text-center"><?= ((float)$row->let_rating == 0.00001) ? '' : $row->let_rating; ?></td>
                                                    <td style="<?= $isDuplicate ? 'color:#2433fe;' : '' ?>" class="text-center"><?= ((float)$row->demo_rating == 0.00001) ? '' : $row->demo_rating; ?></td>
                                                    <td style="<?= $isDuplicate ? 'color:#2433fe;' : '' ?>" class="text-center"><?= ((float)$row->tr_rating == 0.00001) ? '' : $row->tr_rating; ?></td>
                                                    <td class="<?= $row->appStatus == 'Rated' ? 'text-success fw-bold' : '' ?>">
                                                        <?= $row->appStatus; ?>
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


                        
                        






                        


                        


                    </div>
                    <!-- end container-fluid -->

                </div>
               

             
                                      
