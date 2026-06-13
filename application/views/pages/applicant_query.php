
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

                        <?php if(!empty($data)): ?>
                        <div class="alert alert-info alert-dismissible fade show" role="alert">
                            <strong>Reminder:</strong> After completing your evaluation and reply, please click the <strong>Final</strong> button to hide this query from your list.
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <?php endif; ?>

                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body table-responsive">
                                    <h4 class="header-title mb-4">List of Applicants Query<br />
                                    </h4><br />
                                        <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                        <!-- <table id="datatable-buttons" class="table table-striped table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;"> -->
                                        <thead>
                                            <tr>
                                                <th>Fullname</th>
												<th>Rater's</th>
                                                <th>Applicant No.</th>
                                                <th>Date of Query</th>
                                                <th style="text-align:center">Action</th>
                                                <th style="text-align:center">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                                foreach($data as $row){ 
                                            ?>
                                            
                                            <tr>
                                                
                                                <td> <a href="<?= base_url(); ?>Pages/inquiry/<?= $row->applicant_id; ?>/<?= $row->job_id; ?>/<?= $row->pre_school; ?>/<?= $row->application_id; ?>/<?= $row->record_no; ?>"><?= $row->fullname; ?></a></td>
                                                <td><?= $row->rater_fullname; ?></td>
                                                <td><?= $row->record_no; ?></td>
                                                <td><?= $row->idate; ?></td>
                                                <td class="text-center">
                                                <?php if($row->position == 1){ ?>
                                                    <a class="btn btn-success btn-sm" target="_blank" href="<?= base_url(); ?>Pages/inquiry/<?= $row->applicant_id; ?>/<?= $row->job_id; ?>/<?= $row->pre_school; ?>/<?= $row->application_id; ?>/<?= $row->record_no; ?>"><i class="mdi mdi-notebook-multiple tooltips text-white"></i></a>
                                                <?php }else{ ?>
                                                    <a class="btn btn-success btn-sm" target="_blank" href="<?= base_url(); ?>Pages/inquiry_non/<?= $row->applicant_id; ?>/<?= $row->job_id; ?>/<?= $row->pre_school; ?>/<?= $row->application_id; ?>/<?= $row->record_no; ?>"><i class="mdi mdi-notebook-multiple tooltips text-white"></i></a>
                                                <?php } ?>
                                                </td>
                                                <td class="text-center"><a href="<?= base_url(); ?>Pages/aq/<?= $row->application_id; ?>" onclick="return confirm('Are you sure you want to finalize this query? This action will permanently hide it from your list.');" class="btn btn-purple btn-sm">Final</a></td>
                                            </tr>
                                            <?php }?>
                                        </tbody>

                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- end row -->

                        






                                                    </div>
                                                </div>
                                                <!-- /.modal-content -->
                                            </div>
                                            <!-- /.modal-dialog -->
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
            </script>

             
                                      
