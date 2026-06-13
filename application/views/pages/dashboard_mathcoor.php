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
                        
                        <!-- <img class="img-fluid logo" src="<?= base_url(); ?>assets/images/hris/socmob.jpg" alt="MediSkwela" width="100%"> -->

                        <div class="page-title-right">
                            <ol class="breadcrumb p-0 m-0">
                                <li class="breadcrumb-item"><a href="#" data-toggle="modal" data-target="#myModal">Current Fiscal Year : <span class="badge badge-success"><?= $this->session->cur_fy; ?></span></a></li>
                            </ol>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                </div>
            </div>
            <!-- end page title -->



            <div class="row">
                            <div class="col-lg-12">
                                <div id="accordion" class="mb-3">
                                    <div id="accordion">
                                        <?php for ($q = 1; $q <= 4; $q++): ?>
                                            <?php 
                                                $headingId = "heading{$q}";
                                                $collapseId = "collapse{$q}";
                                                $isFirst = ($q === 1);
                                            ?>
                                            <div class="card mb-0">
                                                <div class="card-header" id="<?= $headingId; ?>">
                                                    <h6 class="m-0">
                                                        <a href="#<?= $collapseId; ?>" 
                                                        class="text-dark" 
                                                        data-toggle="collapse"
                                                        aria-expanded="<?= $isFirst ? 'true' : 'false'; ?>"
                                                        aria-controls="<?= $collapseId; ?>">
                                                            QUARTER <?= $q; ?>
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
                                                            <div class="card">
                                                                    <div class="card-body table-responsive">
                                                                        <?php $summary = $this->Coor_model->get_summary_by_level_subject($q); ?>
                                                                    <h4 class="header-title mb-4">Division-Level Consolidated Proficiency Report</h4>

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
                                                                                    <?php $sum_gl = $this->Coor_model->get_summary_by_grade_level($q,$i); ?>
                                                                                    <tr>
                                                                                        <th scope="row">Grade <?= $i; ?></th>
                                                                                        <td class="text-center"><span class="badge badge-info"><?= (!empty($sum_gl->average_mps) && is_numeric($sum_gl->average_mps)) ? number_format($sum_gl->average_mps, 0) : ''; ?></span></td>
                                                                                        <td class="text-center"><span class="badge badge-purple"><?= (!empty($sum_gl->total_abovemps) && is_numeric($sum_gl->total_abovemps)) ? number_format($sum_gl->total_abovemps, 0) : ''; ?></span></td>
                                                                                        <td class="text-center"><span class="badge badge-pink"><?= (!empty($sum_gl->total_learners) && is_numeric($sum_gl->total_learners)) ? number_format($sum_gl->total_learners, 0) : ''; ?></span></td>
                                                                                        <td class="text-center"><span class="badge badge-primary"><?= (!empty($sum_gl->total_total) && is_numeric($sum_gl->total_total)) ? number_format($sum_gl->total_total, 0) : ''; ?></span></td>
                                                                                        <td class="text-center"><?php if($sum_gl->total_learners != 0){?><span class="badge badge-info"><?= number_format($sum_gl->total_total/$sum_gl->total_learners, 2); ?></span><?php } ?></td>
                                                                                        <td class="text-center"><?php if($sum_gl->total_learners != 0){?><span class="badge badge-warning"><?= number_format(($sum_gl->total_abovemps/$sum_gl->total_learners)*100, 2); ?></span><?php } ?></td>
                                                                                    </tr>
                                                                                <?php endfor; ?>
                                                                                <tr>
                                                                                    <th class="text-right">TOTAL</th>
                                                                                    <td class="text-center"><span class="badge badge-dark"><?= (!empty($summary->average_mps) && is_numeric($summary->average_mps)) ? number_format($summary->average_mps, 2) : ''; ?></span></td>
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
                                                        </div>
                                                    <!-- end row -->
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endfor; ?>
                                    </div>
                                    
                                </div>

                            </div>
                            
                        </div>
                        <!-- end row -->


                        


                        
                        


            

           




        </div>
        <!-- end content -->

        <!-- sample modal content -->
                                        <div id="myModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-success">
                                                        <h5 class="modal-title text-white" id="myModalLabel">Change Fiscal Year</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                    </div>
                                                    <div class="modal-body">
                                                    <form action="<?= base_url('Pages/change_fy') ?>" method="post">
                                                        <div class="form-group row">
                                                            <div class="col-lg-12">
                                                                <select name="new_fy" class="form-control" onchange="this.form.submit()">
                                                                <option disabled selected>Change FY</option>
                                                                <?php for ($y = 2023; $y <= 2030; $y++) : ?>
                                                                    <option value="<?= $y ?>" <?= ($this->session->userdata('cur_fy') == $y) ? 'selected' : '' ?>>
                                                                        <?= $y ?>
                                                                    </option>
                                                                <?php endfor; ?>
                                                            </select>
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