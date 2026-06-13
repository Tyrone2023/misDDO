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
                                                <th>Coverage</th>
                                                <th>Date Uploaded</th>
                                                <th style='text-align:center'>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach($data as $row){?>
                                         <tr>
                                            <td><?= $row->coverage; ?></td>
                                            <td><?= $row->dateUploaded; ?> </td>
                                            <td class="text-center"><a target="_blank" href="<?= base_url(); ?>uploads/sip_files/<?= $row->fileAttachment; ?>" class="btn btn-primary waves-effect waves-light btn-sm">View Attachment</a></td>
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
       