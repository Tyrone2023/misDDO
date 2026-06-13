

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
                              
                                    <div class="clearfix"></div>
                                    
                                </div>
                            </div>
                        </div>
                        <!-- end page title -->

                        

                    

                <div class="row">
                    <div class="col-xl-12">
                        <div class="card-box">
                            <h4 class="header-title mb-4">District Level Proficiency</h4>

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

                                        <!-- Nav Tabs -->
                                        <ul class="nav nav-tabs">
                                            <?php for ($q = 1; $q <= 4; $q++): ?>
                                                <li class="nav-item">
                                                    <a href="#q<?= $q ?>" 
                                                    data-toggle="tab" 
                                                    aria-expanded="<?= $q == 1 ? 'true' : 'false' ?>" 
                                                    class="nav-link <?= $q == 1 ? 'active' : '' ?>">
                                                        <span class="d-block d-sm-none">
                                                            <i class="mdi mdi-<?= $q == 1 ? 'home-variant-outline' : ($q == 2 ? 'account-outline' : ($q == 3 ? 'email-outline' : 'settings-outline')) ?> font-18"></i>
                                                        </span>
                                                        <span class="d-none d-sm-block">Quarter <?= $q ?></span>
                                                    </a>
                                                </li>
                                            <?php endfor; ?>
                                        </ul>

                                        <!-- Tab Content -->
                                        <div class="tab-content">
                                            <?php for ($q = 1; $q <= 4; $q++): ?>
                                                <div class="tab-pane <?= $q == 1 ? 'show active' : '' ?>" id="q<?= $q ?>">

                                                <div class="row">
                                                    <div class="col-lg-12">
                                                        <div id="accordion" class="mb-3">
                                                            <div id="accordion">
                                                                <?php foreach ($district as $row): ?>
                                                                    <?php 
                                                                        $ic = $this->Common->three_cond_count_row('cid_coor_math_proficiency', 'district_id', $row->id,'quarter',$q,'year',$this->session->cur_sy)->num_rows();
                                                                        $headingId = "heading{$row->id}";
                                                                        $collapseId = "collapse{$row->id}";
                                                                        $isFirst = ($row->id == 1);
                                                                    ?>
                                                                    <div class="card mb-0">
                                                                        <div class="card-header" id="<?= $headingId; ?>">
                                                                            <h6 class="m-0">
                                                                                <a href="#<?= $collapseId; ?>" 
                                                                                class="text-dark" 
                                                                                data-toggle="collapse"
                                                                                aria-expanded="<?= $isFirst ? 'true' : 'false'; ?>"
                                                                                aria-controls="<?= $collapseId; ?>">
                                                                                    <b><?= $row->discription; ?></b> <?php if($ic != 0){?><span class="badge badge-success">View</span><?php } ?>
                                                                                </a>
                                                                            </h6>
                                                                        </div>

                                                                        <div id="<?= $collapseId; ?>" 
                                                                            class="collapse <?= $isFirst ? 'show' : ''; ?>" 
                                                                            aria-labelledby="<?= $headingId; ?>" 
                                                                            data-parent="#accordion">
                                                                            <div class="card-body">
                                                                            <div class="row">
                                                                                <div class="col-12">
                                                                                            <div class="card-body table-responsive">
                                                                                                <?php $summary = $this->Coor_model->get_summary_by_level_subject_by_district($row->id,$q); ?>

                                                                                                <table class="table table-bordered mb-0">
                                                                                                    
                                                                                                    <thead>
                                                                                                        <tr>
                                                                                                            <th>Grade Level</th>
                                                                                                            <th class="text-center">Average MPS</th>
                                                                                                            <th class="text-center">No. of Learners<br /> with 75% MPS<br /> and Above</th>
                                                                                                            <th class="text-center">Total No. of Learners</th>
                                                                                                            <th class="text-center">AVE MPS * No. of Learners</th>
                                                                                                            <th class="text-center">PROFICIENCY</th>
                                                                                                            <th class="text-center">PERCENTAGE OF LEARNERS<br /> WITH ATLEAST 75%</th>
                                                                                                        </tr>
                                                                                                    </thead>

                                                                                                    <tbody>
                                                                                                        <?php for ($i = 1; $i <= 12; $i++): ?>
                                                                                                            <?php $sum_gl = $this->Coor_model->get_summary_district_by_grade_level($row->id,$q,$i); ?>
                                                                                                            <tr>
                                                                                                                <th scope="row">Grade <?= $i; ?></th>
                                                                                                                <td class="text-center"><span class="badge badge-purple"><?= (!empty($sum_gl->average_mps) && is_numeric($sum_gl->average_mps)) ? number_format($sum_gl->average_mps, 0) : ''; ?></span></td>
                                                                                                                <td class="text-center"><span class="badge badge-purple"><?= (!empty($sum_gl->total_abovemps) && is_numeric($sum_gl->total_abovemps)) ? number_format($sum_gl->total_abovemps, 0) : ''; ?></span></td>
                                                                                                                <td class="text-center"><span class="badge badge-pink"><?= (!empty($sum_gl->total_learners) && is_numeric($sum_gl->total_learners)) ? number_format($sum_gl->total_learners, 0) : ''; ?></span></td>
                                                                                                                <td class="text-center"><span class="badge badge-primary"><?= (!empty($sum_gl->total_total) && is_numeric($sum_gl->total_total)) ? number_format($sum_gl->total_total, 0) : ''; ?></span></td>
                                                                                                                <td class="text-center"><?php if($sum_gl->total_learners != 0){?><span class="badge badge-info"><?= number_format($sum_gl->total_total/$sum_gl->total_learners, 2); ?></span><?php } ?></td>
                                                                                                                <td class="text-center"><?php if($sum_gl->total_learners != 0){?><span class="badge badge-warning"><?= number_format(($sum_gl->total_abovemps/$sum_gl->total_learners)*100, 2); ?></span><?php } ?></td>
                                                                                                            </tr>
                                                                                                        <?php endfor; ?>
                                                                                                        <tr>
                                                                                                            <th class="text-right"><a href="<?= base_url(); ?>Coor/math_coor_proficiency_schoollist_report/<?= $q; ?>/?district=<?= $row->discription; ?>">TOTAL</a></th>
                                                                                                            <td class="text-center"><span class="badge badge-dark"><?= (!empty($summary->average_mps) && is_numeric($summary->average_mps)) ? number_format($summary->average_mps, 0) : ''; ?></span></td>
                                                                                                            <td class="text-center"><span class="badge badge-dark"><?= (!empty($summary->total_abovemps) && is_numeric($summary->total_abovemps)) ? number_format($summary->total_abovemps, 0) : ''; ?></span></td>
                                                                                                            <td class="text-center"><span class="badge badge-dark"><?= (!empty($summary->total_learners) && is_numeric($summary->total_learners)) ? number_format($summary->total_learners, 0) : ''; ?></span></td>
                                                                                                            <td class="text-center"><span class="badge badge-dark"><?= (!empty($summary->total_total) && is_numeric($summary->total_total)) ? number_format($summary->total_total, 0) : ''; ?></span></td>
                                                                                                            <td class="text-center"><?php if($summary->total_learners != 0){?><span class="badge badge-dark"><?= number_format($summary->total_total/$summary->total_learners, 2); ?></span><?php } ?></td>
                                                                                                            <td class="text-center"><?php if($summary->total_learners != 0){?><span class="badge badge-dark"><?= number_format(($summary->total_abovemps/$summary->total_learners)*100, 2); ?></span><?php } ?></td>
                                                                                                        </tr>
                                                                                                    </tbody>
                                                                                                </table>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                            <!-- end row -->
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                <?php endforeach; ?>
                                                            </div>
                                                            
                                                        </div>

                                                    </div>
                                                    
                                                </div>
                                                <!-- end row -->

                                                    

                                    
                                        </div>
                                        <?php endfor; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- end col -->



                </div>
                    <!-- end container-fluid -->

                </div>
                <!-- end content -->


             

   

 