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
                                    <div class="card-body table-responsive">
                                    <h4 class="header-title mb-4">List of Personnel<br /><span class="float-left badge badge-primary inline mt-2">FOR LOYALTY THIS YEAR</span></h4><br />
                                        <!-- <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;"> -->
                                        <table id="datatable-buttons" class="table table-striped table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                        <thead>
                                            <tr>
                                                <th>Last Name</th>
												<th>First Name</th>
												<th>Middle Name</th>
                                                <th>Employee No.</th>
                                                <th>Position</th>
                                                <th>Department</th>
                                                <th style="text-align:center">Date Hired</th>
                                                <th style="text-align:center">Loyalty Type</th>
                                                <th style="text-align:center">Lenght of Service</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                                <?php
                                                  $i=1;
                                                  foreach($data as $row)
                                                  { ?>
                                                    <tr>
                                                        
                                                        <td><?= $row->LastName; ?></td>
                                                        <td><?= $row->FirstName; ?></td>
                                                        <td><?= $row->MiddleName; ?></td>
                                                        <td><?= $row->IDNumber; ?></td>
                                                        <td><?= $row->empPosition; ?></td>
                                                        <td><?= $row->Department; ?></td>
                                                        <td><?= $row->dateHired; ?></td>
                                                        <td><?= $row->loyaltyType; ?></td>
                                                        <td><?= $row->serviceLenght; ?></td>
                                                       
                                                    </tr>
                                                    <?php } ?>


                                               <?php
										//   $i=1;
										//   foreach($data as $row)
										//   {
										//   echo "<tr>";
										//   echo "<td>".$row->LastName."</td>";
										//   echo "<td>".$row->FirstName."</td>";
										//   echo "<td>".$row->MiddleName."</td>";
										//   echo "<td>".$row->IDNumber."</td>";
                                        //   echo "<td>".$row->empPosition."</td>";
                                        //   echo "<td>".$row->Department."</td>";
                                        //   echo "<td>".$row->dateHired."</td>";
                                        //   echo "<td style='text-align:center'>".$row->loyaltyType."</td>";
                                        //   echo "<td style='text-align:center'>".$row->serviceLenght."</td>";
										//   echo "</tr>";
									  
										// 					}
										   ?>
                                        </tbody>

                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- end row -->

                        <div class="row">
                            <div class="col-xl-12 col-sm-6 ">
                                <!-- Portlet card -->
                                <div class="card">
                                <div class="card-header bg-primary py-3 text-white">
                                        <div class="card-widgets">
                                            <a href="javascript:;" data-toggle="reload"><i class="mdi mdi-refresh"></i></a>
                                            <a data-toggle="collapse" href="#cardCollpase1" role="button" aria-expanded="false" aria-controls="cardCollpase2"><i class="mdi mdi-minus"></i></a>
                                            <a href="#" data-toggle="remove"><i class="mdi mdi-close"></i></a>
                                        </div>
                                        <h5 class="card-title mb-0 text-white">Summary Per Loyalty Type</h5>
                                    </div>
                                    <div id="cardCollpase1" class="collapse show">
                                        <div class="card-body">
                                                <table id="datatable-buttons" class="table table-striped table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                                <thead>
                                                    <tr>
                                                        <th>Type</th>
                                                        <th style="text-align:center">Counts</th>
                                                        <th style="text-align:center">Manage</th>
                                                    </tr>
                                                </thead>
                                                <tbody>

                                            <?php foreach($data1 as $row){ ?>
                                                <tr>
                                               
                                                    <td><?= $row->loyaltyType; ?></td>
                                                    <td style="text-align:center"><?= $row->counts; ?></td>
                                                    <td style="text-align:center"><a href="<?= base_url(); ?>Page/loyaltyList/<?= $row->loyaltyType; ?>"><button type="button" class="btn btn-success waves-effect waves-light btn-sm">View List</button></a></td>
                                                   
                                                </tr>
                                                <?php } ?>

                                                </tbody>

                                                </table>
                                        
                                        </div>
                                    </div>
                                </div>
                                <!-- end card-->
                            
                        </div>

                    </div>
                    <!-- end container-fluid -->

                </div>
                <!-- end content -->

                <?php include('templates/footer.php'); ?>       

             
 