

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
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body table-responsive">
                                    <h4 class="header-title mb-4"><?= $title; ?></h4>

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

                                        <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">

                                        <thead>
                                                <tr>
                                                    <th>Fullname</th>
                                                    <th>Record No.</th>
                                                    <th>Position</th>
                                                    <th>Department</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>

                                            <tbody>
                                                <?php foreach($registered as $row){ ?>
                                                <tr>
                                                <td><?= ucfirst($row['LastName']).', '.ucfirst($row['FirstName']).' '.ucfirst(substr($row['MiddleName'],0,1)); ?></td>
                                                    <td><?= $row['record_no']; ?></td>
                                                    <td><?= $row['empPosition']; ?></td>
                                                    <td><?= $row['Department']; ?></td>
                                                    <td>
                                                        <a href="registered_profile/<?= $row['record_no']; ?>" class="text-success"><i class="mdi mdi-file-document-box-check-outline"></i>View</a>&nbsp;&nbsp;&nbsp;&nbsp;
                                                        <a href="personel_del/" class="text-danger"><i class="mdi mdi-file-document-box-check-outline"></i>Delete</a>
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

             
 