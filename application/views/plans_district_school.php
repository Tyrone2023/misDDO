<?php include('templates/head.php'); ?>  
            <?php include('templates/header.php'); ?>          

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
                                <?php if($this->session->position != "Admin"){ ?>
                                    <button type="button" class="btn btn-primary waves-effect waves-light" data-toggle="modal" data-backdrop="static" data-keyboard="false" data-target=".bs-example-modal-lg">Add New</button>
                                <?php } ?>    
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                        </div>
                        <!-- end page title -->

                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body table-responsive">
                                    <h4 class="header-title mb-4">SCHOOL IMPROVEMENT PLAN (SIP) - <?= strtoupper($school->schoolName); ?></h4><br />
                                    <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                        <thead>
                                            <tr>
                                                <th>remarks</th>
                                                <th>Date Submitted</th>
                                                <th>Manage</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach($data as $row){?>
                                         <tr>
                                            <td><?= $row->remarks; ?></td>
                                            <td><?= $row->date; ?></td>
                                            <td>
                                                                <a href="<?= base_url(); ?>Page/aip_admin/<?= $row->school_id.'/'.$row->fy.'/'.$row->b_code.'/'.$row->id; ?>" class="btn btn-success" target="_blank">View AIP</a> &nbsp;
                                                                <a href="<?= base_url(); ?>Page/generate_sop_admin/<?= $row->school_id.'/'.$row->fy.'/'.$row->b_code.'/'.$row->id; ?>" class="btn btn-primary" target="_blank">View SOP</a> &nbsp;
                                                                <a href="<?= base_url(); ?>Page/generate_app_admin/<?= $row->school_id.'/'.$row->fy.'/'.$row->b_code.'/'.$row->id; ?>" class="btn btn-info" target="_blank">View APP</a> &nbsp;
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
                <!-- end content -->

                <?php include('templates/footer.php'); ?>    
       