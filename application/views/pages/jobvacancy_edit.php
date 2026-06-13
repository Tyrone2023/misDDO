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
                                    <!-- <h4 class="page-title">Basic Tables</h4> -->
                                   
                                    <div class="clearfix"></div>
                                   
                                </div>
                            </div>
                        </div>
                        <!-- end page title -->
                        
                        

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="card">
                                    <div class="card-body">
                                        <h4 class="header-title mb-4">Application List</h4>

                                        <?php 
                                                                $attributes = array('class' => 'parsley-examples');
                                                                echo form_open_multipart('Pages/edit_vacancy', $attributes);
                                                            ?>
                                                            <div class="form-group row">
                                                            <label for="inputPassword3" class="col-md-3 col-form-label">Position</label>
                                                            <div class="col-md-9">
                                                                <select class="form-control" name="position" id="position-select">
                                                                    <option></option>
                                                                    <?php $gt = array('Teaching' => 1,'School Administration' => 2,'Related Teaching' => 3, 'Non-Teaching' => 4); ?>
                                                                    <?php foreach($gt as $row=>$key){?>
                                                                    <option value="<?= $key; ?>"><?= $row; ?></option>
                                                                    <?php } ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    
                                                        <div class="form-group row">
                                                            <label for="inputEmail3" class="col-md-3 col-form-label">Position Title</label>
                                                            <div class="col-md-9">
                                                                <select class="form-control" name="jobTitle" id="job-title-select">
                                                                    <option></option>
                                                                    <?php foreach($pos_title as $row){?>
                                                                    <option value="<?= $row->title; ?>" data-pos_id="<?= $row->pos_id; ?>" ><?= $row->title; ?></option>
                                                                    <?php } ?>

                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div id="teaching-group-type" style="display:none;">
                                                        <div class="form-group row">
                                                            
                                                            <label for="inputPassword3" class="col-md-3 col-form-label">Teaching Group Type</label>
                                                            <div class="col-md-9">
                                                                <select class="form-control" name="job_type">
                                                                    <option value="0"></option>
                                                                    <?php $gt = array('Elementary' => 1,'Junior High School' => 3,'Senior High School' => 4); ?>
                                                                    <?php foreach($gt as $row=>$key){?>
                                                                    <option value="<?= $key; ?>"><?= $row; ?></option>
                                                                    <?php } ?>

                                                                </select>
                                                            </div>
                                                            </div>
                                                        </div>

                                                        <div id="admin-group-type" style="display:none;">
                                                        <div class="form-group row">
                                                            
                                                            <label for="inputPassword3" class="col-md-3 col-form-label">Administration Group Type</label>
                                                            <div class="col-md-9">
                                                                <select class="form-control" name="job_type">
                                                                    <option value="0"></option>
                                                                    <?php $gt = array(
                                                                        'Elementary' => 1,
                                                                        'Secondary' => 2,
                                                                        'Junior High School'=>3,
                                                                        'Senior High School'=>4); 
                                                                    ?>
                                                                    <?php foreach($gt as $row=>$key){?>
                                                                    <option value="<?= $key; ?>"><?= $row; ?></option>
                                                                    <?php } ?>

                                                                </select>
                                                            </div>
                                                            </div>
                                                        </div>
                                                                
                                                                <div class="form-group row">
                                                                    <label for="inputPassword3" class="col-md-3 col-form-label">Employment Type</label>
                                                                    <div class="col-md-9">
                                                                        <select class="form-control" name="empType" required>
                                                                            <option></option>
                                                                            <?php  
                                                                                $jt = array('Permanent Position','Job Order','Contract of Service');
                                                                                foreach($jt as $row){
                                                                             ?>
                                                                            <option <?php if($row == $data->empType){echo 'selected';} ?> value="<?= $row; ?>" ><?= $row; ?></option>
                                                                            <?php } ?>

                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                

                                                                <div class="form-group row">
                                                                    <label for="inputPassword5" class="col-md-3 col-form-label">Year</label>
                                                                    <div class="col-md-9">
                                                                    <select class="form-control" name="sy" required>
                                                                        <option></option>
                                                                    <?php 
                                                                    $firstYear = (int)date('Y');
                                                                    $lastYear = $firstYear + 5;
                                                                    
                                                                    for($i=$firstYear;$i<=$lastYear;$i++){ ?>

                                                                        <option <?php if($i == $data->sy){echo 'selected';} ?> value='<?= $i; ?>'><?= $i; ?></option>';

                                                                    <?php } ?>
                                                                    </select>
                                                                    </div>
                                                                </div>

                                                                <div class="form-group row">
                                                                    <label for="inputPassword5" class="col-md-3 col-form-label">Office/Bureau/Service/Unit where the vacancy exists</label>
                                                                    <div class="col-lg-9">
                                                                        <input type="text" name="assign" class="form-control" value="<?= $row == $data->empType; ?>">
                                                                    </div>
                                                                </div>

                                                                <input type="hidden" name="jobID" value="<?= $data->jobID; ?>">
                                                            
                                                                <div class="form-group mb-0 justify-content-end row">
                                                                    <div class="col-md-9">
                                                                        <input type="submit" name="submit" value="Submit" class="btn btn-primary waves-effect waves-light">
                                                                        <!-- <button type="submit" class="btn btn-info waves-effect waves-light">Submit</button> -->
                                                                    </div>
                                                                </div>
                                                            </form>
                                        


                                    </div>
                                </div>

                            
                        </div>
                        <!--- end row -->


                    </div>
                    <!-- end container-fluid -->

                </div>
                <!-- end content -->

                <script>
                                        document.getElementById("position-select").addEventListener("change", function() {
                                            var selectedPosition = this.value;
                                            var jobSelect = document.getElementById("job-title-select");
                                            var teachingGroupType = document.getElementById("teaching-group-type");
                                            var admingroup = document.getElementById("admin-group-type");

                                            // Enable the job title dropdown
                                            jobSelect.disabled = false;
                                            
                                            // Show or hide the "Group Type for Teaching" dropdown based on selected position
                                            if (selectedPosition === "1") { // Teaching Positions
                                                teachingGroupType.style.display = "block"; // Show
                                            } else {
                                                teachingGroupType.style.display = "none"; // Hide
                                            }

                                            // Show or hide the "Administrative Group" dropdown based on selected position
                                            if (selectedPosition === "2") { // Teaching Positions
                                                admingroup.style.display = "block"; // Show
                                            } else {
                                                admingroup.style.display = "none"; // Hide
                                            }
                                            
                                            // Loop through all the job titles and hide/show based on the selected position
                                            var jobOptions = jobSelect.querySelectorAll("option");
                                            jobOptions.forEach(function(option) {
                                                if (option.value && option.getAttribute("data-pos_id") !== selectedPosition) {
                                                    option.style.display = "none"; // Hide options that don't match
                                                } else {
                                                    option.style.display = ""; // Show matching options
                                                }
                                            });
                                            
                                            // If no position is selected, disable the job title dropdown
                                            if (selectedPosition === "") {
                                                jobSelect.disabled = true;
                                                teachingGroupType.style.display = "none"; // Hide group type if no position is selected
                                            }
                                        });
                                    </script>
                                                 


                             


