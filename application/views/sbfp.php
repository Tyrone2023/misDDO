                    

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
                                <div class="page-title-box">
                                <div class="card">
                                    <div class="card-body">

                                                    <?= form_open('page/sbfp_form'); ?>

                                                    <?php if($this->session->position == 'SHNS'){?>

                                                        <div class="form-group row">
                                                            <div class="col-lg-4">
                                                                <label >School Name </label>
                                                                <select class="form-control" name="schoolID" data-toggle="select2" required>
                                                                    <option></option>
                                                                    <?php foreach($school as $row){?>
                                                                        <option value="<?= $row->schoolID; ?>"><?= $row->schoolName; ?></option>
                                                                    <?php }?>
                                                                    
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <?php } ?>
                                                    
                                                        <div class="form-group row">
                                                            <div class="col-lg-4">
                                                                <label >Fiscal Year</label>
                                                                <select class="form-control" name="sy" required>
                                                                    <option></option>
                                                                    <?php foreach($sy as $row){?>
                                                                        <option value="<?= $row->SY; ?>"><?= $row->SY; ?></option>
                                                                    <?php }?>
                                                                    
                                                                </select>
                                                            </div>
                                                            <div class="col-lg-4">
                                                                <label >Grade Level</label>
                                                                <select class="form-control" name="YearLevel" id="YearLevel" required>
                                                                    <option></option>
                                                                    <?php foreach($yl as $row){?>
                                                                        <option value="<?= $row->YearLevel; ?>"><?= $row->YearLevel; ?></option>
                                                                    <?php }?>
                                                                    
                                                                </select>
                                                            </div>
                                                            <div class="col-lg-4">
                                                                <label >Section</label>
                                                                <select class="form-control" name="Section" id="Section" required>
                                                                    <option></option>
                                                                    <?php foreach($section as $row){?>
                                                                        <option value="<?= $row->Section; ?>" data-grade="<?= $row->YearLevel; ?>"><?= $row->Section; ?></option>
                                                                    <?php }?>
                                                                    
                                                                </select>
                                                            </div>
                                                        </div>

                                        

                                                        
                                                        
   
                                                        <div class="form-group row">
                                                            <div class="col-lg-12">
                                                                <input type="submit" name="submit" class="btn btn-primary waves-effect waves-light" value="Submit" />
                                                            </div>
                                                        </div>
                                                    </form>
                                    </div>
                                </div>
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                        </div>
                                    



                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body table-responsive">
                                        <h4 class="m-t-0 header-title mb-4">Master List Beneficiaries for School-Based Feeding Program (SBFP)</h4>

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
                                                    <th>Manage</th>
                                                </tr>
                                             
                                            </thead>

                                            <tbody>
                                                <?php if(isset($_POST['submit'])){ ?>
                                                <?php foreach($sbf as $row){
                                                    $stud = $this->Common->one_cond_row('studeprofile', 'StudentNumber', $row->StudentNumber);
                                                    ?>
                                                <tr>
                                                    <td class="text-left"><?= $stud->LastName; ?><?= empty($stud->FirstName) ? '' : ', '.$stud->FirstName; ?><?= empty($stud->nameExt) ? '' : ', '.$stud->nameExt.', '; ?> <?= $stud->MiddleName; ?> </td>
                                                    <td><?= $stud->BirthDate; ?></td>
                                                    <td><?= $row->weight; ?></td>
                                                    <td><?= $row->height; ?></td>
                                                    <td><?= $stud->Sex; ?></td>
                                                    <td><?= $row->YearLevel; ?></td>
                                                    <td><?php $h = $row->height*$row->height; echo $h; ?></td>
                                                    <td><?= $row->age; ?></td>
                                                    <td><?php $result = ($h != 0) ? $row->weight / $h : "0";  ?> <?= $result; ?></td>
                                                    <td><?= $row->FourPs; ?></td>
                                                    <td><?= $row->sbfp_ben_prevyear; ?></td>
                                                    <td><?php if($row->dewormStat == 0){echo "Yes";}else{echo "No";} ?></td>
                                                    <td><?php if($row->pc_for_milk == 0){echo "Yes";}else{echo "No";} ?></td>
                                                    <td><?php if($row->sbfp_ben_prevyear == 0){echo "Yes";}else{echo "No";} ?></td>
                                                    <td><?php if($row->sbfp_ben_prevyear == 0){echo "Yes";}else{echo "No";} ?></td>
                                                    <td><?= $h; ?></td>
                                                    <td><?= $row->bmi; ?><?= $result; ?></td>
                                                    <td><?= $row->nut_stat; ?></td>
                                                    <td>
                                                        <a href="<?= base_url(); ?>Sbfp/sbfp_update/<?= $row->semstudentid; ?>" class="btn btn-sm btn-success">Edit</a>
                                                        <a href="<?= base_url(); ?>Sbfp/delete_sbfp/<?= $row->semstudentid; ?>" class="btn btn-sm btn-danger">Delete</a>
                                                    </td>
                                                </tr>
                                                <?php } } ?>
                                                
                                                
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


                <script>
                    const yearLevel = document.getElementById("YearLevel");
                    const section = document.getElementById("Section");

                    // Store all section options and remove them from the DOM except the default one
                    const defaultOption = section.querySelector('option[value=""]');
                    const sectionOptions = Array.from(section.querySelectorAll('option[data-grade]'));
                    sectionOptions.forEach(opt => opt.remove());

                    yearLevel.addEventListener("change", function () {
                        // Clear current section options except default
                        section.querySelectorAll('option[data-grade]').forEach(opt => opt.remove());

                        // Append only matching options
                        sectionOptions.forEach(opt => {
                            if (opt.dataset.grade === this.value) {
                                section.appendChild(opt);
                            }
                        });

                        // Reset the selection
                        section.value = "";
                    });
                </script>

             
 