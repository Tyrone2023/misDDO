
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
                                                    <th>Item Position</th>
                                                    <th>Attachment</th>
                                                </tr>
                                            </thead>

                                            <tbody>
                                                <?php foreach($page as $row){ 
                                                    $app_id = $row['applicant_id'];
                                                    $app = $this->Page_model->get_single_table_by_id('hris_applicant','record_no',$app_id);
                                                    ?>
                                                <tr>
                                                    <td><a href="profile/<?= $app_id; ?>"><?php if($app !== null){
                                                    echo $app['LastName'].' '.$app['FirstName']; 
                                                    } ?></a></td> 
                                                    <td><?= $row['item_position']; ?></td> 
                                                    <td>
                                                        <a target="_blank" class="btn btn-primary waves-effect waves-light btn-sm" href="<?= $row['attach_link']; ?>">View</a>&nbsp;&nbsp;&nbsp;
                                                        <a href="ap_delete/<?= $row['id']; ?>" class="text-danger"><i class="mdi mdi-file-document-box-check-outline"></i>Delete</a>
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

             
 