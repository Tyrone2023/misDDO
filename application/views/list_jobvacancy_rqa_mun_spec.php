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
                                        <!-- <a class="btn sm btn-success" target="_blank" href="<?= base_url(); ?>Pages/car_rqa/<?= $this->input->get('jobID'); ?>">Printable View</a>
                                        <a class="btn sm btn-info" href="<?= base_url(); ?>Page/rqa_municipality/<?= $this->input->get('jobID'); ?>">Per Municipality</a>        -->
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
                                    <h4 class="header-title mb-4">Summary<br/><span class="float-left badge badge-primary inline mt-2">Per Municipality</span></h4><br />
                                       
                                    <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                        <thead>
                                            <tr>
                                                <th>Municipality</th>
												<th style="text-align: center">Counts</th>
                                                <th style="text-align: center">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                               <?php
										  foreach($data1 as $row)
										  {
										  echo "<tr>";
										  echo "<td>".$row->resCity."</td>";
										  echo "<td style='text-align: center'>".$row->resCounts."</td>";
                                            ?>
                                          <td style="text-align: center">
                                            <!-- <a href="<?= base_url(); ?>Page/rqa_municipality_list/?mun=<?= $row->resCity; ?>&jobID=<?= $row->jobID; ?>" class="text-success"><i class="mdi mdi-file-document-box-check-outline"></i>View List</a>&nbsp;&nbsp;&nbsp;&nbsp; -->
                                            <?php if($row->resCity == ""){?>
                                                <span class="text-primary"><i class="mdi mdi-file-document-box-check-outline"></i>Printable View</span>&nbsp;&nbsp;&nbsp;&nbsp;
                                            <?php }else{ ?>

                                            <a href="<?= base_url(); ?>Pages/rqa_municipality_spec?id=<?= $row->jobID; ?>&mun=<?= $row->resCity; ?>&spec=<?= $row->specialization; ?>" class="text-primary" target="_blank"><i class="mdi mdi-file-document-box-check-outline"></i>Printable View</a>&nbsp;&nbsp;&nbsp;&nbsp;
                                            
                                              <?php } ?>        
                                           </td>

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

       
       
 