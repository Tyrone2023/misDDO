

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
                                                    <th>Position</th>
                                                    <th>Manage</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                    foreach($users as $row){ 
                                                    $sp = $this->Common->one_cond_row('users_sp','id',$row->sp);
                                                    if($row->sp != 0){ 
                                                ?>
                                                <tr>
                                                    <td><?= ucfirst($row->fname).' '.ucfirst($row->mname).' '.ucfirst($row->lname); ?></td>
                                                    <td><?= $row->username; ?></td>
                                                    <td><?php if(isset($sp->position)){echo $sp->position; } ?></td>
                                                    <td>
                                                        
                                                        <a href="users_sub_update/<?= $row->id; ?>" class="btn btn-info"><i class=" fas fa-pencil-alt tooltips" data-placement="top" data-toggle="tooltip" data-original-title="Edit"></i></a> 
                                                        <a onclick="return confirm('Are you sure?')" href="<?= base_url(); ?>Users/sp_user_delete/<?= $row->id; ?>" class="tooltips btn-danger btn" data-placement="top" data-toggle="tooltip" data-original-title="Delete"><i class="fas fa-trash-alt "></i></a>
                                                        <a data-toggle="modal" data-id="<?= $row->id; ?>" data-job="<?= $row->sp; ?>" class="open-AddBookDialog btn btn-primary" href="#change_pass_admin"><i class="fas fa-user-alt" class="tooltips" data-placement="top" data-toggle="tooltip" data-original-title="Change Password"></i></a>
                                                    </td>
                                                </tr>
                                                <?php }} ?>
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


 