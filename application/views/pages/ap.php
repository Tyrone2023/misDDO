
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
                                                    <th>Item Position</th> 
                                                    <th>SG</th>
                                                    <th>Step</th> 
                                                    <th>Manage</th>
                                                </tr>
                                            </thead>

                                            <tbody>
                                                <?php foreach($page as $row){ 
                                                    ?>
                                                <tr>
                                                    <td><?= $row['itemPosition']; ?></td> 
                                                    <td><?= $row['sg']; ?></td> 
                                                    <td><?= $row['step']; ?><?= $row['view_status']; ?></td> 
                                                    <td>
                                                        <a data-toggle="modal" data-item="<?= $row['itemPosition']; ?>" class="open-AddBookDialog btn btn-primary waves-effect waves-light btn-sm" href="#addBookDialog">Apply</a>
                                                        <?php if($this->session->position == "Admin"){ ?>
                                                        <a href="<?= !$row['view_status'] ? 'view_status_view' : 'view_status_unview'?>/<?= $row['id']; ?>" class="text-<?= !$row['view_status'] ? 'danger' : 'success'?>">&nbsp; &nbsp; <?php 
                                                        echo !$row['view_status'] ? " <i class='fas fa-eye-slash'></i>" : " <i class='fas fa-eye'></i>";
                                                        ?></a>
                                                        <?php } ?>
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


                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body table-responsive">
                                    <h4 class="header-title mb-4">Counts Per available Item</h4>

                                    

                                        <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">

                                            <thead>
                                                <tr>
                                                    <th>Item Position</th> 
                                                    <th>Count</th>
                                                </tr>
                                            </thead>

                                            <tbody>
                                                <?php foreach($pos as $row){ 
                                                    ?>
                                                <tr>
                                                    <td><?= $row['position']; ?></td> 
                                                    <td><?= $row['count_ap']; ?></td> 
                                                   
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

                

             
 