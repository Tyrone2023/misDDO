

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
                                    <a href="<?= $link; ?>" class="btn btn-success waves-effect width-md waves-light"><?= $pn; ?></a>
                                    <div class="page-title-right">
                                        <ol class="breadcrumb p-0 m-0">
                                            <!-- <li class="breadcrumb-item"><a href="#">Download the School Accounts Template</a></li> -->
                                            <!-- <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                                            <li class="breadcrumb-item active">Dashboard 3</li> -->
                                        </ol>
                                    </div>
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
                                                    <th>Name</th>
                                                    <th>Username</th>
                                                    <th>Access Level</th>
                                                    <th>Manage</th>
                                                </tr>
                                            </thead>

                                            <tbody>
                                                <?php foreach($users as $row){?>
                                                <tr>
                                                    <td><?= ucfirst($row['fname']).' '.ucfirst(substr($row['mname'],0,1)).' '.ucfirst($row['lname']); ?></td>
                                                    <td><?= $row['username']; ?></td>
                                                    <td><?= $row['position']; ?></td>
                                                    <td>
                                                        <a href="user_edit/<?= $row['id']; ?>" class="text-success"><i class="mdi mdi-file-document-box-check-outline"></i> Edit </a>&nbsp;&nbsp;&nbsp;&nbsp;
                                                        <a href="user_delete/<?= $row['id']; ?>" class="text-danger"><i class="mdi mdi-file-document-box-check-outline"></i> Delete </a>&nbsp;&nbsp;&nbsp;&nbsp;
                                                        <a data-toggle="modal" data-id="<?= $row['id']; ?>" class="open-AddBookDialog text-info" href="#change_pass"><i class="fas fa-user-alt"></i><span> Change Password  </span></a>
                                                        <?php if($row['position'] == "User"){ ?><a href="<?= base_url(); ?>profile/<?= $row['user_id']; ?>" class="text-success"><i class="mdi mdi-file-document-box-check-outline"></i> View</a><?php } ?>
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

             
 