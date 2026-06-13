

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
                                    <a href="#" class="btn btn-success waves-effect width-md waves-light"><?= $pn; ?></a>
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
                                                    <th>EMPLOYEE NAME</th>
                                                    <th>EMPLOYEE NO.</th>
                                                    <th>POSITION</th>
                                                    <th>DEPARTMENT</th>
                                                    <th>ACTION</th>
                                                </tr>
                                            </thead>

                                            <tbody>
                                                <?php foreach($personel as $row){ 
                                                    $user = $this->Page_model->get_limited_col_single_col('users','id','id',$row['IDNumber']);
                                                    ?>
                                                <tr>
                                                <td><?= ucfirst($row['LastName']).', '.ucfirst($row['FirstName']).' '.ucfirst(substr($row['MiddleName'],0,1)); ?></td>
                                                    <td><?= $row['IDNumber']; ?></td>
                                                    <td><?= $row['empPosition']; ?></td>
                                                    <td><?= $row['Department']; ?></td>
                                                    <td>
                                                    <a href="<?= base_url(); ?>personnel_profile/<?= $row['IDNumber']; ?>" class="text-success"><i class="mdi mdi-file-document-box-check-outline"></i>View
                                                    Details</a>&nbsp;&nbsp;&nbsp;&nbsp;

                                                <a data-toggle="modal" data-id="<?= $row['IDNumber']; ?>" class="open-AddBookDialog text-info" href="#change_pass"><i class="fas fa-user-alt"></i><span> Reset Password </span></a>
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

             
 