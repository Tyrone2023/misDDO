
<script type="text/javascript">
		function submitBday() {
		   
			var Bdate = document.getElementById('bday').value;
			var Bday = +new Date(Bdate);
			Q4A= ~~ ((Date.now() - Bday) / (31557600000));
			var theBday = document.getElementById('resultBday');
			theBday.value = Q4A;
		}

		</script>
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
                                    <h4 class="page-title"><?= $title; ?></h4>
                                 
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                        </div>
                        <!-- end page title -->

                        
                        <!-- Form row -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-body">
                                    
                                        <?= validation_errors(); ?>

                                        <?= form_open('personel_edit/'.$IDNumber); ?>
                                        <div class="row">
										<h5>Personal Information</h5>
									</div>
									<input type="hidden" name="jobTitle" value=" " />
									<input type="hidden" name="perHouseNo" value="">
									<input type="hidden" name="perStreet" value="">
									<input type="hidden" name="perVillage" value="">
									<input type="hidden" name="perBarangay" value="">
									<input type="hidden" name="perCity" value="">
									<input type="hidden" name="perProvince" value="">
									<input type="hidden" name="perZipCode" value="">
									<div class="row">
										<div class="col-lg-3">
												<div class="form-group">
													<label for="lastName">Record No. <span class="ren_r">*</span></label>
													<input type="text" class="form-control" name="IDNumber" readonly required value="<?= $IDNumber; ?>">
												</div>	
										</div>
										<div class="col-lg-3">
												<div class="form-group">
													<label for="lastName">Employee No. <span class="ren_r">*</span></label>
													<input type="text" class="form-control" name="employeeNo" readonly required value="">
												</div>	
										</div>
										<div class="col-lg-3">
												<div class="form-group">
													<label for="lastName">Prefix <span class="ren_r">*</span></label>
													<select name="prefix" class="form-control">
                                                        <option></option>
                                                        <?php 
                                                            $array = array('Mr.', 'Ms.', 'Mrs.'); 
                                                            foreach($array as $ar){
                                                                echo '<option';
																if($ar == $prefix){echo " selected ";}
																echo '>'. $ar . '</option>';
                                                            }
                                                        ?>
													</select>
												</div>	
										</div>											
									</div>
									
									<div class="row">
										<div class="col-lg-3">
												<div class="form-group">
													<label>First Name <span class="ren_r">*</span></label>
													<input type="text" class="form-control" name="FirstName" readonly value="<?= $fname; ?>" required >
												</div>	
										</div>	
										<div class="col-lg-3">
											<div class="form-group">
											<label>Middle Name</label>
											 <input type="text" class="form-control" name="MiddleName" readonly value="<?= $mname; ?>" >
											</div>
										</div>  
										<div class="col-lg-3">
											<div class="form-group">
											<label>Last Name <span class="ren_r">*</span></label>
											 <input type="text" class="form-control" name="LastName" readonly value="<?= $lname; ?>" required >
                                        </div>
										</div>
										<div class="col-lg-3">
												<div class="form-group">
													<label for="lastName">Name Extn.</label>
													<input type="text" class="form-control" name="NameExtn" value="<?= $NameExtn; ?>" >
												</div>	
										</div>	
									</div>	
									
									<div class="row">
										
										<div class="col-lg-3">
												<div class="form-group">
													<label>Sex <span class="ren_r">*</span></label>
													<select name="Sex" class="form-control" required>
                                                        <option></option>
                                                        <?php 
                                                            $array = array('F', 'M'); 
                                                            foreach($array as $ar){
                                                                echo '<option ';
																if($Sex == $ar){echo " Selected ";}
																echo ' >'. $ar . '</option>';
                                                            }
                                                        ?>
													</select>
													<?= $Sex; ?>
												</div>
										</div> 
										<div class="col-lg-3">
											<div class="form-group">
											<label>Birth Date <span class="ren_r">*</span></label>
											 <input type="date" name="BirthDate" class="form-control" id="bday" onchange="submitBday()" required value="<?= $BirthDate; ?>">
											</div>
										</div>  
										<div class="col-lg-1">
											<div class="form-group">
											<label>Age </label>
											 <input type="text" readonly name="age" id="resultBday" class="form-control" value="<?= $age; ?>"/>
											</div>
										</div> 
										<div class="col-lg-5">
											<div class="form-group">
												<label>Birth Place</label>
													 <input type="text" class="form-control" name="BirthPlace" value="<?= $BirthPlace; ?>" >
													 
											</div>
										</div>										
											
									</div>
									
									<div class="row">
									<div class="col-lg-3">
												<div class="form-group">
													<label>Civil Status <span class="ren_r">*</span></label>
													 <select name="MaritalStatus" class="form-control" required>
                                                        <option></option>
                                                        <?php 
                                                            $array = array('Single', 'Married'); 
                                                            foreach($array as $ar){
                                                                echo '<option';
																if($ar == $MaritalStatus){echo " selected ";}
																echo '>'. $ar . '</option>';
                                                            }
                                                        ?>
													</select>
												</div>
											</div>
											<div class="col-lg-3">
												<div class="form-group">
													<label>Height (in meter)</label>
													<input type="text" class="form-control" name="height" value="<?= $height; ?>" >
												</div>
											</div>
										<div class="col-lg-3">
											<div class="form-group">
											<label>Weight (in kg)</label>
											 <input type="text" class="form-control" name="weight" value="<?= $weight; ?>" >
											</div>
										</div>
										<div class="col-lg-3">
											<div class="form-group">
											<label>Blood Type</label>
											 <input type="text" class="form-control" name="bloodType" value="<?= $bloodType; ?>" >
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-lg-3">
											<div class="form-group">
											<label>Telephone No.</label>
											 <input type="text" class="form-control" name="empTelNo" value="<?= $empTelNo; ?>" >
											</div>
										</div>
										<div class="col-lg-3">
											<div class="form-group">
											<label>Mobile No.</label>
											 <input type="text" class="form-control" name="empMobile" value="<?= $empMobile; ?>" >
											</div>
										</div>
										<div class="col-lg-6">
											<div class="form-group">
											<label>E-mail <span class="ren_r">*</span></label>
											 <input type="email" class="form-control" name="empEmail" value="<?= $empEmail; ?>" required >
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-lg-6">
											<div class="form-group">
											<label>Facebook Link</label>
											 <input type="text" class="form-control" name="fb" value="<?= $fb; ?>" >
											</div>
										</div>
										<div class="col-lg-6">
											<div class="form-group">
											<label>Skype</label>
											 <input type="text" class="form-control" name="skype" value="<?= $skype; ?>" >
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-lg-3">
											<div class="form-group">
											<label>Citizenship</label>
											 <input type="text" class="form-control" name="citizenship" value="Filipino" >
											</div>
										</div>
										<div class="col-lg-3">
												<div class="form-group">
													<label>Dual Citizen?</label>
													 <select class="form-control" name="dualCitizenship">
                                                        <option value="">Select</option>
                                                        <?php 
                                                                $array = array('No Data', 'No', 'Yes'); 
                                                                foreach($array as $ar){
                                                                    echo '<option';
																	if($ar == $dualCitizenship){echo " selected ";}
																	echo '>'. $ar . '</option>';
                                                                }
                                                            ?>
													 </select>
													 
												</div>
										</div>
										<div class="col-lg-3">
												<div class="form-group">
													<label>Citizen Type</label>
													 <select class="form-control" name="citizenshipType">
														<option></option>
                                                        <?php 
                                                                $array = array('By Birth', 'By Naturalization'); 
                                                                foreach($array as $ar){
                                                                    echo '<option';
																	if($ar == $citizenshipType){echo " selected ";}
																	echo '>'. $ar . '</option>';
                                                                }
                                                            ?>
													 </select>
													 
												</div>
										</div>
										<div class="col-lg-3">
											<div class="form-group">
											<label>Country</label>
											 <input type="text" class="form-control" name="citizenshipCountry" value="<?= $citizenshipCountry; ?>" >
											</div>
										</div>
									</div>
									
									<div class="row">
										<h5>Official Information</h5>	<hr />
									</div>
									<div class="row">
										<div class="col-lg-3">
											<div class="form-group">
											<label>Current Position</label>
											 <input type="text" class="form-control" name="empPosition" value="<?= $empPosition; ?>" >
											</div>
										</div>
										<div class="col-lg-2">
											<div class="form-group">
											<label>Item No.</label>
											 <input type="text" class="form-control" name="currentItemNo" value="" >
											</div>
										</div>
										<div class="col-lg-2">
												<div class="form-group">
												<label>SG No.</label>
												<select class="form-control" name="sgNo">
													<option></option>
													<?php
														for ($x = 1; $x <= 33; $x+=1) {
														echo "<option";
														if($x == $sgNo){echo " selected ";}
														echo ">SG $x</option>";
														}
														?>													
												</select>
												</div>
											</div>
										<div class="col-lg-1">
												<div class="form-group">
												<label>Step No.</label>
												<select class="form-control" name="stepNo">
													<option></option>
													<?php
														for ($x = 1; $x <= 8; $x+=1) {
														echo "<option";
														if($x == $stepNo){echo " selected ";}
														echo ">$x</option>";
														}
														?>											
												</select>
												</div>
											</div>
										<div class="col-lg-2">
											<div class="form-group">
											<label>Auth. Annual Salary</label>
											 <input type="text" class="form-control" name="authAnSalary" value="<?= $authAnSalary; ?>" >
											</div>
										</div>
										<div class="col-lg-2">
											<div class="form-group">
											<label>Actual Salary</label>
											 <input type="text" class="form-control" name="actualSalary" value="<?= $actualSalary; ?>" >
											</div>
										</div>
									</div>

									<div class="row">
										<div class="col-lg-6">
											<div class="form-group">
											<label>Department/School</label>
											 <input type="text" class="form-control" name="Department" value="<?= $Department; ?>">
											</div>
										</div>
										<div class="col-lg-3">
											<div class="form-group">
											<label>School ID (if assigned in school)</label>
											 <input type="text" class="form-control" name="schoolID" value="<?= $schoolID; ?>" >
											</div>
										</div>
											 <input type="hidden" class="form-control" value='112' readonly name="agencyCode" value="<?= $agencyCode; ?>" >
											
										<div class="col-lg-3">
											<div class="form-group">
											<label>Station Code </label>
											 <input type="text" class="form-control" name="staCode" value="<?= $stationCode; ?>" >
											</div>
										</div>
									</div>

									<div class="row">
										
										<div class="col-lg-4">
											<div class="form-group">
											<label>Date Hired</label>
											 <input type="date" class="form-control" name="dateHired" value="<?= $dateHired; ?>" >
											</div>
										</div>
										<div class="col-lg-4">
											<div class="form-group">
											<label>Last Appointment</label>
											 <input type="date" class="form-control" name="lastAppointmentDate" value="<?= $lastAppointmentDate; ?>" >
											</div>
										</div>
										<div class="col-lg-4">
											<div class="form-group">
											<label>Expected Ret. Year</label>
											 <input type="text" class="form-control" name="retYear" value="<?= $retYear; ?>" >
											</div>
										</div>
										
										
									</div>
									<div class="row">
									<div class="col-lg-2">
											<div class="form-group">
											<label>UMID</label>
											 <input type="text" class="form-control" name="umid" value="<?= $umid; ?>" >
											</div>
										</div>
										<div class="col-lg-2">
											<div class="form-group">
											<label>PhilHeatlth No.</label>
											 <input type="text" class="form-control" name="philHealth" value="<?= $philHealth; ?>" >
											</div>
										</div>
										
										<div class="col-lg-2">
											<div class="form-group">
											<label>GSIS</label>
											 <input type="text" class="form-control" name="gsis" value="<?= $gsis; ?>" >
											</div>
										</div>
										<div class="col-lg-2">
											<div class="form-group">
											<label>PAGIBIG</label>
											 <input type="text" class="form-control" name="pagibig" value="<?= $pagibig; ?>" >
											</div>
										</div>
										<div class="col-lg-2">
											<div class="form-group">
											<label>SSS No.</label>
											 <input type="text" class="form-control" name="sssNo" value="<?= $sssNo; ?>" >
											</div>
										</div>
										<div class="col-lg-2">
											<div class="form-group">
											<label>TIN</label>
											 <input type="text" class="form-control" name="tinNo" value="<?= $tinNo; ?>" >
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-lg-3">
												<div class="form-group">
												<label>Employment Status</label>
												<select class="form-control" name="empStatus">
													<option></option>
													<?php 
                                                                $array = array('Elected', 'Co-Terminous','Permanent','Presidential','Temporary',
															'JO','COS','MOA','Casual/Contractual'); 
                                                                foreach($array as $ar){
                                                                    echo '<option';
																	if($ar == $empStatus){echo " selected ";}
																	echo '>'. $ar . '</option>';
                                                                }
                                                            ?>												
												</select>
												</div>
											</div>
										<div class="col-lg-3">
											<div class="form-group">
											<label>Civil Service Eligibility</label>
											<select class="form-control" name="csEligibility">
												<option></option>
												<?php 
                                                    $array = array('No Eligibility', 'PBET','RA 4670','RA 6850','RA 1080','LET',
															'PD 907'); 
                                                                foreach($array as $ar){
                                                                    echo '<option';
																	if($ar == $csEligibility){echo " selected ";}
																	echo '>'. $ar . '</option>';
                                                                }
                                                        ?>
											</select>
											</div>
										</div>
										<div class="col-lg-3">
											<div class="form-group">
											<label>CS Eligibility Level</label>
											<select class="form-control" name="csLevel">
												<option></option>
												<?php 
                                                    $array = array('1st Level', '2nd Level','3rd Level','No Eligibility'); 
                                                                foreach($array as $ar){
                                                                    echo '<option';
																	if($ar == $csLevel){echo " selected ";}
																	echo '>'. $ar . '</option>';
                                                                }
                                                        ?>
											</select>
											</div>
										</div>
										<div class="col-lg-3">
											<div class="form-group">
											<label>Current Status</label>
											<select class="form-control" name="currentStatus">
												<option></option>
												<?php 
                                                    $array = array('Active', 'Retired','Resigned','Terminated'); 
                                                                foreach($array as $ar){
                                                                    echo '<option';
																	if($ar == $currentStatus){echo " selected ";}
																	echo '>'. $ar . '</option>';
                                                                }
                                                        ?>
											</select>
											</div>
										</div>
									</div>
									<div class="row">
											<div class="col-lg-3">
												<div class="form-group">
												<label>No. of Years as JO/COS</label>
												<input type="number" class="form-control" name="YearsAsJO" value="<?= $YearsAsJO; ?>" >
												</div>
											</div>
										<div class="col-lg-3">
											<div class="form-group">
											<label>Nature of Work 1</label>
											<select class="form-control" name="workNature1">
												<option></option>
												<?php 
                                                    $array = array('Clerical Services', 'IT Services','Administrative','Watchman','Driver'); 
                                                                foreach($array as $ar){
                                                                    echo '<option';
																	if($ar == $workNature1){echo " selected ";}
																	echo '>'. $ar . '</option>';
                                                                }
                                                        ?>
											</select>
											</div>
										</div>

										<div class="col-lg-3">
											<div class="form-group">
											<label>Nature of Work 2</label>
											<select class="form-control" name="workNature2">
												<option></option>
												<?php 
                                                    $array = array('Clerical Services', 'IT Services','Administrative','Watchman','Driver'); 
                                                                foreach($array as $ar){
                                                                    echo '<option';
																	if($ar == $workNature2){echo " selected ";}
																	echo '>'. $ar . '</option>';
                                                                }
                                                        ?>
											</select>
											</div>
										</div>

									</div>
									<div class="row">
										<h5>Contact Person in Case of Emergency</h5>
									</div>
									
									<div class="row">
										<div class="col-lg-3">
											<div class="form-group">
											<label>Contact Name</label>
											 <input type="text" class="form-control" name="contactName" value="<?= $contactName; ?>" >
											</div>
										</div>
										<div class="col-lg-3">
											<div class="form-group">
												<label>Relationship</label>
												<input type="text" class="form-control" name="contactRel" value="<?= $contactRel; ?>" >
											</div>
										</div>
										<div class="col-lg-6">
											<div class="form-group">
												<label>Email</label>
												<input type="email" class="form-control" name="contactEmail" value="<?= $contactEmail; ?>" >
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-lg-3">
											<div class="form-group">
												<label>Contact No.</label>
												<input type="text" class="form-control" name="contactNo" value="<?= $contactNo; ?>" >
											</div>
										</div>
										<div class="col-lg-9">
											<div class="form-group">
												<label>Address</label>
												<input type="text" class="form-control" name="contactAddress" value="<?= $contactAddress; ?>" >
											</div>
										</div>
									</div>
									<div class="row">
										<h5>Personnel Address</h5>
									</div>
									<div class="row">
										<div class="col-lg-3">
											<div class="form-group">
												<label>House No.</label>
												<input type="text" class="form-control" name="resHouseNo" value="<?= $resHouseNo; ?>" >
											</div>
										</div>
										<div class="col-lg-3">
											<div class="form-group">
												<label>Street</label>
												<input type="text" class="form-control" name="resStreet" value="<?= $resStreet; ?>" >
											</div>
										</div>
										<div class="col-lg-3">
											<div class="form-group">
												<label>Village</label>
												<input type="text" class="form-control" name="resVillage" value="<?= $resVillage; ?>" >
											</div>
										</div>
										<div class="col-lg-3">
											<div class="form-group">
												<label>Barangay</label>
												<input type="text" class="form-control" name="resBarangay" value="<?= $resBarangay; ?>" >
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-lg-3">
											<div class="form-group">
												<label>Zip Code</label>
												<input type="text" class="form-control" name="resZipCode" value="<?= $resZipCode; ?>" >
											</div>
										</div>
										<div class="col-lg-3">
											<div class="form-group">
												<label>City</label>
												<input type="text" class="form-control" name="resCity" value="<?= $resCity; ?>" >
											</div>
										</div>
										<div class="col-lg-6">
											<div class="form-group">
												<label>Province</label>
												<input type="text" class="form-control" name="resProvince" value="<?= $resProvince; ?>" >
											</div>
										</div>
									</div>
									
									<div class="row">
										<div class="col-lg-12">
											<input type="hidden" name="id" value="<?= $IDNumber; ?>">
											<input type="submit" name="submit" class="btn btn-info" value="Update Profile">
										</div>
									</div>

                          </div><!-- /.box -->

					</div>
                                        






                                        </form>
                                    </div>
                                </div>
                            </div>
                        <!-- end row -->

                    </div>
                    <!-- end container-fluid -->

                </div>
                <!-- end content -->

                

               