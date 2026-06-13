
            <!-- ============================================================== -->
            <!-- Start Page Content here -->
            <!-- ============================================================== -->
            <?php $job = $this->Common->one_cond_row('hris_jobvacancy','jobID',$this->uri->segment(3));  ?>

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
                                    <h4 class="header-title mb-4"><?= $title; ?><br />
                                    <span class="badge badge-info"><?= $job->jobTitle; ?></span>
                                    </h4>
                                        <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                        <!-- <table id="datatable-buttons" class="table table-striped table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;"> -->
                                        <thead>
                                            <tr>
                                                <th>Major</th>
                                                <th>Count</th>
                                                <th style="text-align:center">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                                foreach($data as $row){
                                               $ad = $this->Page_model->rqa_cluster_jhs_count($this->uri->segment(3),$row->specialization);
                                                if($ad->num_rows() >= 1){
                                            ?>
                                            <tr>
                                                <td><?= $row->specialization; ?></td>
                                                <td><?= $ad->num_rows();?></td>
                                                <td class="text-center"><a href="<?= base_url(); ?>Pages/rqa_cluster_list_jhs/<?= $this->uri->segment(3); ?>/?s=<?= $row->specialization; ?>" class="text-success"><i class="mdi mdi-file-document-box-check-outline"></i>Printable View</a></td>
                                            </tr>
                                            <?php } } ?>
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

             


