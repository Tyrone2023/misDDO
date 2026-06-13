            <?php include('templates/head.php'); ?> 
            <link href="<?= base_url(); ?>assets/libs/custombox/custombox.min.css" rel="stylesheet" type="text/css">
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
                                     <!-- <h4 class="header-title mb-4">My Application/s</h4> -->
                                                
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                        </div>
                        <!-- end page title -->

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

                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body table-responsive">
                                    <h4 class="header-title mb-4">Tracking</h4><br />
                                                                    
                                    <!-- <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                        <thead>
                                            <tr>
                                                <th>Date Submitted/Time</th>
												<th>Status</th>
												<th>Note</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                               <?php foreach($data as $row)
										  {
										  echo "<tr>";
										  echo "<td>".$row->dateSubmitted.' '.$row->timeSubmitted."</td>";
										  echo "<td>".$row->appStatus."</td>";
                                          echo "<td>".$row->note."</td>";
                                         
                                         echo "</tr>"; } ?>
                                        </tbody>

                                        </table> -->


                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="timeline timeline-left">
                                            <article class="timeline-item alt">
                                                <div class="text-left">
                                                    <div class="time-show first">
                                                        <a href="#" class="btn btn-primary w-lg">Today</a>
                                                    </div>
                                                </div>
                                            </article>
                                            <?php foreach($data as $row) { ?>

                                            <article class="timeline-item">
                                                <div class="timeline-desk">
                                                    <div class="panel">
                                                        <div class="timeline-box">
                                                            <span class="arrow"></span>
                                                            <span class="timeline-icon"><i class="mdi mdi-checkbox-blank-circle-outline"></i></span>
                                                            <h4 class=""><?php echo $row->dateSubmitted; ?></h4>
                                                            <p class="timeline-date text-muted"><small><?php echo $row->timeSubmitted; ?></small></p>
                                                            <p>Application Status:<strong> <?php echo $row->appStatus; ?></strong></p>
                                                            <p>Note: <?php echo $row->note; ?></p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </article>
                                            <?php } ?>
                                        </div>
                                        <!-- end timeline -->
                                    </div>
                                    <!-- end card-body -->
                                </div>
                                <!-- end card -->
                            </div>
                            <!-- end col -->
                        </div>
                        <!-- end row -->








                                    

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

       
       
 