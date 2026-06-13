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
                                    <h4 class="header-title mb-4">201 Files</h4>
                                        
                                            <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                                <thead>
                                                    <tr>
                                                        <th style="text-align: center;">Last Name</th>
                                                        <th style="text-align: center;">First Name</th>
                                                        <th style="text-align: center;">Middle Name</th>
                                                        <th style="text-align: center;">Employee No.</th>
                                                        <th style="text-align: center;">Position</th>
                                                        <th style="text-align: center;">Department</th>
                                                        <th style="text-align: center;">Action</th>
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
                                                
                                                ?>
                                                <td style="text-align:center;"> 
                                                    <a href="<?= base_url(); ?>page/hr_files_individual?id=<?php echo $row->IDNumber;?>"><button type="button" class="btn btn-primary btn-xs waves-effect waves-light"> <i class="fas fa-tv  mr-1"></i> <span>View</span> </button></a>
                                                    <a href="<?= base_url(); ?>page/upload201Files?id=<?php echo $data[0]->IDNumber; ?>"><button type="button" class="btn btn-secondary btn-xs waves-effect waves-light"><i class="fas fa-cloud-upload-alt mr-1"></i>Upload</button></a>
                                                                                                                                        
                                                </td>
                                                <?php
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

             
 