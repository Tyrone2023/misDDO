
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
                                    <?php echo '<a href="<?= base_url(); ?><?= $link; ?>" class="btn btn-success waves-effect width-md waves-light"><?= $pn; ?></a>' ?>
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
                                                    <th>Item No.</th> 
                                                    <th>Item Position</th> 
                                                    <?php if($p==0){?><th>Fullname</th><?php } ?>
                                                    <th>SG</th>
                                                    <th>Step</th> 
                                                    <th>Manage</th>
                                                </tr>
                                            </thead>

                                            <tbody>
                                                <?php foreach($page as $row){ 
                                                    $itemNo = $row['itemNo'];
                                                    $item = $this->Page_model->get_single_row_by_id('hris_staff', 'itemNo', $itemNo);
                                                    ?>
                                                <tr>
                                                    <td><?= $row['itemNo']; ?></td> 
                                                    <td><?= $row['itemPosition']; ?></td> 
                                                    <?php if($p==0){?><td></td><?php } ?>
                                                    <td><?= $row['sg']; ?></td> 
                                                    <td><?= $row['step']; ?></td> 
                                                    <td>
                                                        <a href="plantilla_update/<?= $row['id']; ?>" class="text-success"><i class="mdi mdi-file-document-box-check-outline"></i>Edit</a>&nbsp;&nbsp;&nbsp;&nbsp;
                                                        <a href="plantilla_del/<?= $row['id']; ?>" class="text-danger"><i class="mdi mdi-file-document-box-check-outline"></i>Delete</a>
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

             
 