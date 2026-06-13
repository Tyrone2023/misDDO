
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
                                    <h4 class="header-title mb-4">List of Applicants<br /><span class="float-left badge badge-primary inline mt-2"><?= $job->jobTitle; ?></span></h4><br />
                                        <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                        <!-- <table id="datatable-buttons" class="table table-striped table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;"> -->
                                        <thead>
                                            <tr>
                                                <th>First Name</th>
                                                <th>Last Name</th>
												<th>Middle Name</th>
                                                <th>Applicant No.</th>
                                                <th>Date Submitted</th>
                                                <th>Status</th>
                                                <th style="text-align:center">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach($data as $row){ $a = $this->Common->one_cond_row('hris_applicant','empEmail',$row->empEmail); ?>
                                                <tr>
                                                    <td><?= $a->FirstName; ?></td>
                                                    <td><?= $a->MiddleName; ?></td>
                                                    <td><?= $a->LastName; ?></td>
                                                    <td><?= strtoupper($a->record_no); ?></td>
                                                    <td><?= $row->dateSubmitted; ?></td>
                                                    <td><span class="badge badge-warning badge-pill" ><?= $row->appStatus; ?></span></td>
                                                    <td class="text-center">
                                                        <a href="<?= base_url(); ?>pages/ma/<?= $a->id; ?>/<?= $row->jobID; ?>/<?= $row->pre_school; ?>" target="_blank" class="text-success tooltips" data-placement="top" data-toggle="tooltip" data-original-title="View Applicant Information"><i class="fas fa-file-alt"></i></a>&nbsp; &nbsp;
                                                        <!-- <a href="<?= base_url(); ?>Pages/ies/<?= $a->id; ?>/<?= $row->appID; ?>/<?= $row->jobID; ?>" target="_blank" class="text-success tooltips" data-placement="top" data-toggle="tooltip" data-original-title="View IES"><i class=" mdi mdi-xbox-controller-view"></i></button></a> -->
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



