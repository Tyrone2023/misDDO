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
                                <button type="button" class="btn btn-primary waves-effect waves-light" data-toggle="modal" data-backdrop="static" data-keyboard="false" data-target=".bs-example-modal-lg">Add New</button>
                                                
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
                                    <h4 class="header-title mb-4">COMPETENCY ASSESSMENT AND DEVELOPMENT PLAN</h4><br />
                                       
                                        <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                        <thead>
                                            <tr>
                                                <th>Target Competency</th>
												<th>Priority for IDP</th>
                                                <th>Development Activity</th>
                                                <th>Support Needed</th>
                                                <th>Trainer/Provider</th>
                                                <th>Schedule or Completion</th>
                                                <th style="text-align:center">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                               <?php
										  foreach($data as $row)
										  {
										  echo "<tr>";
										  echo "<td>".$row->targetCompetency."</td>";
										  echo "<td>".$row->priority."</td>";
                                          echo "<td>".$row->devActivity."</td>";
										  echo "<td>".$row->supportNeeded."</td>";
                                          echo "<td>".$row->trainingProvider."</td>";
										  echo "<td>".$row->sched."</td>";
                                          ?>
                                                                                    
                                          <td style="text-align:center">
                                            <a href="delete_id?id=<?php echo $row->id; ?>" class="text-danger"><i class="mdi mdi-file-document-box-check-outline"></i>Delete</a> 
                                                   
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

       
       
 <!--  Add New Vacancies -->
 <div class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" style="display: none;" aria-hidden="true">
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="myLargeModalLabel">Competency Assessment and Development Plan</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                    </div>
                                                    <div class="modal-body">
                                                        
                                                    <form class="form-horizontal" method="post">
                                                        <div class="form-group row">
                                                            <label for="inputEmail3" class="col-md-3 col-form-label">Target Competency</label>
                                                            <div class="col-md-9">
                                                                <input type="text" class="form-control" name="targetCompetency" >
                                                            </div>
                                                        </div>
                                                        <div class="form-group row">
                                                            <label for="inputEmail3" class="col-md-3 col-form-label">Priority</label>
                                                            <div class="col-md-9">
                                                                <input type="number" class="form-control" name="priority" >
                                                            </div>
                                                        </div>

                                                        <div class="form-group row">
                                                            <label for="inputEmail3" class="col-md-3 col-form-label">Development Activity</label>
                                                            <div class="col-md-9">
                                                                <input type="text" class="form-control" name="devActivity" >
                                                            </div>
                                                        </div>
                                                      
                                                        <div class="form-group row">
                                                            <label for="inputPassword5" class="col-md-3 col-form-label">Support Needed</label>
                                                            <div class="col-md-9">
                                                                <input type="text" class="form-control" name="supportNeeded" >
                                                            </div>
                                                        </div>

                                                        <div class="form-group row">
                                                            <label for="inputEmail3" class="col-md-3 col-form-label">Training Provider</label>
                                                            <div class="col-md-9">
                                                                <input type="text" class="form-control" name="trainingProvider" >
                                                            </div>
                                                        </div>

                                                        <div class="form-group row">
                                                            <label for="inputEmail3" class="col-md-3 col-form-label">Completion Schedule</label>
                                                            <div class="col-md-9">
                                                                <input type="text" class="form-control" name="sched" >
                                                            </div>
                                                        </div>

                                                        <div class="form-group mb-0 justify-content-end row">
                                                            <div class="col-md-9">
                                                                <input type="submit" name="submit" value="Submit" class="btn btn-info waves-effect waves-light">
                                                                <!-- <button type="submit" class="btn btn-info waves-effect waves-light">Submit</button> -->
                                                            </div>
                                                        </div>
                                                    </form>

                                                    </div>
                                                </div>
                                                <!-- /.modal-content -->
                                            </div>
                                            <!-- /.modal-dialog -->
                                        </div>
                                        <!-- /.modal -->


