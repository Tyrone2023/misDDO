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
                            <div class="col-12">
                                <div class="page-title-box">
                                    <!-- <a href="<?= $link; ?>" class="btn btn-success waves-effect width-md waves-light"><?= $pn; ?></a> -->
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                        </div>
                        <!-- end page title -->
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body">
                                        <!-- <h4 class="header-title mb-4">Options</h4> -->
                                        <div class="">
                                            <?= form_open('Page/retirement2'); ?>
                                               
                                                <div class="form-group row mb-0">
                                                    <!-- <label class="col-md-2 col-form-label">Year</label> -->
                                                    <div class="col-md-4">
                                                        <div class="input-group">
                                                            <input type="text" class="form-control" placeholder="Year" name="year" aria-label="Recipient's username">
                                                            <div class="input-group-append">
                                                                <input type="submit" name="submit" class="btn btn-info waves-effect waves-light" value="View">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <!-- end card -->
                            </div>
                            <!-- end col -->
                        </div>
                        <!-- end row -->

                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body table-responsive">
                                    <h4 class="header-title mb-4">List of Personnel <br /><span class="float-left badge badge-primary inline mt-2">Per Retirement Year</span></h4><br />
                     
                                        
                                            <!-- <div class="float-right">
                                                <h5><strong>Employee Masterlist</strong></h5>
                                            </div> -->
                                       

                                        <table class="table table-sm mb-0">
                                        
                                        <thead>
                                                    <tr>
                                                        <th>Last Name</th>
                                                        <th>First Name</th>
                                                        <th>Middle Name</th>
                                                        <th>Employee No.</th>
                                                        <th>Position</th>
                                                        <th>Department</th>
                                                        <th>Date Hired</th>
                                                        <th style="text-align:center">Retirement Year</th>
                                                        <th style="text-align:center">Length of Service <br />(in years)</th>
                                                    </tr>
                                                </thead>
                                    
                                        <tbody> 
                                               <?php
										  $i=1;
										  foreach($data as $row)
										  {
										  echo "<tr>";
										  echo "<td>".$row->LastName."</td>";
										  echo "<td>".$row->FirstName."</td>";
										  echo "<td>".$row->MiddleName."</td>";
										  echo "<td>".$row->IDNumber."</td>";
                                          echo "<td>".$row->empPosition."</td>";
                                          echo "<td>".$row->Department."</td>";
                                          echo "<td>".$row->origAppointmentDate."</td>";
                                          echo "<td style='text-align:center'>".$row->retYear."</td>";
                                          echo "<td style='text-align:center'>".$row->serviceLenght."</td>";
										  echo "</tr>";
									  
															}
										   ?>
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

             
 