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
                            <div class="col-md-12">
                                <div class="page-title-box">
                                 


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
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                        </div>

                        

                        <!-- end page title -->
                        <div class="row">
                            <div class="col-md-12">

                                <div class="card">
                                    <div class="card-body table-responsive">
                                        <h4 class="m-t-0 header-title mb-4">Summary <?= $this->input->get('title'); ?></h4>
                                       
                                        <?php echo $this->session->flashdata('msg'); ?>
                                        <!-- <table id="datatable-buttons" class="table table-striped table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;"> -->
                                        <table class="table mb-0">                                            <thead>
                                                <tr>
                                                    <th>YearLevel</th>
                                                    <th>Counts</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php 
                                                    foreach($yl as $row){
                                                    $col = $this->Common->column_count_four_cond('semesterstude',$this->input->get('col'),'Yes','schoolID', $this->session->username,'SY', $this->input->get('sy'),'YearLevel',$row->YearLevel);
                                                    if($col != 0){
                                                ?>
                                                <tr>
                                                 <td><?= $row->YearLevel; ?></td> 
                                                 <td><?= $col; ?></td>
                                                 <td><a href="<?= base_url(); ?>Sbfp/enrolledList_of_names?col=<?= $this->input->get('col'); ?>&sy=<?= $this->input->get('sy'); ?>&yl=<?= $row->YearLevel; ?>&title=<?= $this->input->get('title'); ?>" class="btn btn-primary btn-sm">View</a></td>  
                                                </tr>
                                                <?php } } ?>
                                               
                                            </tbody>

                                        </table>

                                    </div>
                                </div>
                            </div>
                        </div>




                        
                    </div>
                    <!-- end container-fluid -->

                </div>
                <!-- end content -->

                <?php include('templates/footer.php'); ?>

               

