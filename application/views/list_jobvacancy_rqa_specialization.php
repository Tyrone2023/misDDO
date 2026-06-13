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
                                        <!-- <a class="btn sm btn-success" target="_blank" href="<?= base_url(); ?>Pages/car_rqa/<?= $this->input->get('jobID'); ?>">Printable View</a> -->
                                                
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
                                    <h4 class="header-title mb-4">Registry of Qualified Applicants<br/><span class="float-left badge badge-primary inline mt-2"><?php echo $_GET['spe']; ?></span></h4><br />
                                       
                                        <!-- <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;"> -->
                                        <table id="datatable-buttons" class="table table-striped table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                        <thead>
                                            <tr>
                                                <th>Applicant No.</th>
												<th>Application Code</th>
												<th>Education</th>
                                                <th>Training</th>
                                                <th>Experience</th>
                                                <th>LET Rating</th>
                                                <th>Demo</th>
                                                <th>TR</th>
                                                <th>Total</th>
                                                <!-- <th>Action</th> -->
                                            </tr>
                                        </thead>
                                        <tbody>
                                               <?php
										  foreach($data as $row)
										  {
										  echo "<tr>";
										  echo "<td>".$row->record_no."</td>";
										  echo "<td>".$row->appID."</td>";
										  echo "<td>".$row->education."</td>";
                                          echo "<td>".$row->training."</td>";
                                          echo "<td>".$row->experience."</td>";
                                          echo "<td>".$row->let_rating."</td>";
                                          echo "<td>".$row->demo_rating."</td>";
                                          echo "<td>".$row->tr_rating."</td>";
                                          echo "<td>".$row->total_points."</td>";
                                            ?>
                                          <!-- <td>
                                            <a href="<?= base_url(); ?>Pages/ies/<?= $row->id; ?>/<?= $row->appID; ?>/<?= $row->jobID; ?>" target="_blank" class="text-success"><i class="mdi mdi-file-document-box-check-outline"></i>View IES</a>&nbsp;&nbsp;&nbsp;&nbsp;
                                                      
                                           </td> -->

                                                    <?php echo "</tr>"; } ?>
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

       
       
 