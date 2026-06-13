                    

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

                                    <h4></h4>
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                        </div>
                        <!-- end page title -->


                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body table-responsive">
                                        <h4 class="m-t-0 header-title mb-4"><?= $title; ?></h4>

                                        <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">

                                            <thead>
                                         <tr>
                                                    <th>Student Name</th>
                                                    <th>Birth Day<br />(mm/dd/yyyy)</th>
                                                    <th>Weight<br />(Kg)</th>
                                                    <th>Height<br />(meters)</th>
                                                    <th>Sex</th>
                                                    <th>Grade <br/>Level</th>
                                                    <th>Height<br />(m2)</th>
                                                    <th>Age</th>
                                                    <th>Body Mass <br/>Index</th>
                                                    <th>Nutritional <br />Status</th>
                                                    <th>Section</th>
                                                    <th>Dewormed? <br />(yes or no)</th>
                                                    <th>Parent's consent <br/>for milk?<br/>(yes or no)</th>
                                                    <th>Participation<br/> in 4ps <br/>(yes or no)</th>
                                                    <th>Beneficiary of <br/>SBFP in Previous <br/>Years(yes or no)</th>
                                                    <th>Date of <br/>Weighing</th>
                                                    <th>Category as Primary <br/>Beneficiary (Wasted <br/>and Severely Wasted)</th>
                                                    <th>Category as Secondary<br/> Beneficiary (PARDO, <br/>Stunted/sevely Stunded/<br/>Indigent/Indigenous<br/>/less than 100/other kinder learners)</th>
                                                
                                                </tr>
                                             
                                            </thead>

                                            <tbody>
                                                <?php foreach($sbf as $row){
                                                    $stud = $this->Common->one_cond_row('studeprofile', 'StudentNumber', $row->StudentNumber);
                                                    ?>
                                                <tr>
                                                    <td><?= $stud->FirstName; ?> <?= $stud->MiddleName; ?> <?= $stud->LastName; ?></td>
                                                    <td><?= $row->bDate; ?></td>
                                                    <td><?= $row->weight; ?></td>
                                                    <td><?= $row->height; ?></td>
                                                    <td><?= $row->sex; ?></td>
                                                    <td><?= $row->grLevel; ?></td>
                                                    <td><?= $row->section; ?></td>
                                                    <td><?= $row->dewormStat; ?></td>
                                                    <td><?= $row->pc_for_milk; ?></td>
                                                    <td><?= $row->f4ps; ?></td>
                                                    <td><?= $row->sbfp_ben_prevyear; ?></td>
                                                    <td><?= $row->weighingDate; ?></td>
                                                    <td><?= $row->categoryPri; ?></td>
                                                    <td><?= $row->categorySec; ?></td>
                                                    <td><?= $row->age; ?></td>
                                                    <td><?= $row->height2; ?></td>
                                                    <td><?= $row->bmi; ?></td>
                                                    <td><?= $row->nut_stat; ?></td>
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

             
 