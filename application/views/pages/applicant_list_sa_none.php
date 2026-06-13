
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
                                    <h4 class="header-title mb-4">List of Applicants<br />
                                    <span class="badge badge-info"><?= $job->jobTitle; ?></span>
                                    </h4>
                                        <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                        <!-- <table id="datatable-buttons" class="table table-striped table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;"> -->
                                        <thead>
                                            <tr>
                                                <th>District</th>
                                                <th>Count</th>
                                                <th style="text-align:center">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach($district as $row){  
                                                $app = $this->Common->four_cond_count_row('hris_applications', 'jobID', $this->uri->segment(3),'appStatus','Application Submitted','district',$row->discription,'dq',0);
                                                if($app->num_rows() >= 1){
                                                ?>
                                                <tr>
                                                    <td><?= $row->discription; ?></td>
                                                    <td><?= $app->num_rows(); ?></td>
                                                    <td class="text-center">
                                                    <!-- <?php if($this->session->position != 'doceval'){ ?>
                                                        <a onclick="return confirm('Are you sure?')" href="<?= base_url(); ?>Pages/efr_district/<?= $this->uri->segment(3); ?>/<?= $this->uri->segment(3); ?>/?district=<?= $row->discription; ?>" class="btn-warning btn">Endorse for Rating</a>
                                                    <?php } ?> -->
                                                        <a href="<?= base_url(); ?>Pages/validated_list/<?= $this->uri->segment(3); ?>/?district=<?= $row->discription; ?>" class="btn btn-info tooltips" data-placement="top" data-toggle="tooltip" data-original-title="View Applicants"><i class="mdi mdi-file-document-box-outline"></i>View Applicants</a>
                                                    
                                                    
                                                    </td>
                                                </tr>
										    <?php	} } ?>
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

             
 <!-- Modal for  -->
<div id="appStatus" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="myModalLabel">Update Application Status</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                    </div>
                                                    <div class="modal-body">
                                                    <!-- <?= form_open('Pages/'); ?> -->
                                                <form method="post">
                                                    <div class="modal-body">
                                                        <div class="form-group col-md-12">
                                                            <label>Application Status</label>
                                                            <input type="text"  name="appStatus" class="form-control" value="" />
                                                        </div>

                                                        <div class="form-group col-md-12">
                                                            <label>Notes</label>
                                                            <textarea class="form-control" rows="3" name="note"></textarea>
                                                        </div>
                                                        
                                                        
                                                            <input type="hidden" name="jobID" id="id" value="">
                                                            <input type="hidden"  id="field" name="empEmail" class="form-control" value="" />
                                                        </div>    

                                                    </div>
                                                    <div class="modal-footer">
                                                        <input type="submit" name="submit" class="btn btn-primary waves-effect waves-light" value="Submit" />
                                                    </div>
                                                    </form>
                                                </div>
                                                <!-- /.modal-content -->
                                            </div>
                                            <!-- /.modal-dialog -->
                                        </div>



