<script type="text/javascript">
	function submitBday() {

		var Bdate = document.getElementById('bday').value;
		var Bday = +new Date(Bdate);
		Q4A = ~~((Date.now() - Bday) / (31557600000));
		var theBday = document.getElementById('resultBday');
		theBday.value = Q4A;
	}
</script>
<style>
	.readonly {
		pointer-events: none;
	}
</style>
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
						<h4 class="page-title">
							<?= $title; ?>
						</h4>

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

							<?php $att = array('class' => 'parsley-examples'); ?>
							<?= form_open('Pages/employee_edit/' . $app->IDNumber, $att); ?>
							<div class="row">
								<h5>Personal Information</h5>
							</div>
							<div class="row">
								<!-- <div class="col-lg-3">
												<div class="form-group">
													<label for="lastName">Record No. <span class="ren_r">*</span></label>
													<input type="text" class="form-control" name="IDNumber" readonly required value="">
													
												</div>	
										</div>
										<div class="col-lg-3">
												<div class="form-group">
													<label for="lastName">Employee No. <span class="ren_r">*</span></label>
													<input type="text" class="form-control" name="employeeNo" readonly required value="">
												</div>	
										</div> -->
								<input type="hidden" class="form-control" name="id" readonly required
									value="<?= $app->IDNumber; ?>">
								<div class="col-lg-3">
									<div class="form-group">
										<label for="lastName">Prefix <span class="ren_r">*</span></label>
										<select name="prefix" class="form-control">
											<option></option>
											<?php
											$array = array('Mr.', 'Ms.', 'Mrs.');
											foreach ($array as $ar) {
												echo '<option';
												if ($ar == $app->prefix) {
													echo " selected ";
												}
												echo '>' . $ar . '</option>';
											}
											?>
										</select>
									</div>
								</div>

								<div class="col-lg-3">
									<div class="form-group">
										<label for="lastName">Employee Number <span class="ren_r">*</span></label>
										<input type="text" class="form-control" name="empNo" <?php if ($this->session->position == "user") {
																									echo "readonly";
																								} ?>
											value="<?= $app->IDNumber; ?>" required>
									</div>
								</div>

							</div>

							<div class="row">
								<div class="col-lg-3">
									<div class="form-group">
										<label>First Name <span class="ren_r">*</span></label>
										<input type="text" class="form-control" name="FirstName"
											value="<?= $app->FirstName; ?>" required>
									</div>
								</div>
								<div class="col-lg-3">
									<div class="form-group">
										<label>Middle Name</label>
										<input type="text" class="form-control" name="MiddleName"
											value="<?= $app->MiddleName; ?>">
									</div>
								</div>
								<div class="col-lg-3">
									<div class="form-group">
										<label>Last Name <span class="ren_r">*</span></label>
										<input type="text" class="form-control" name="LastName"
											value="<?= $app->LastName; ?>" required>
									</div>
								</div>
								<div class="col-lg-3">
									<div class="form-group">
										<label for="lastName">Name Extn.</label>
										<input type="text" class="form-control" name="NameExtn"
											value="<?= $app->NameExtn; ?>">
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
											$array = array('Female', 'Male');
											foreach ($array as $ar) {
												echo '<option ';
												if ($app->Sex == $ar) {
													echo " Selected ";
												}
												echo ' >' . $ar . '</option>';
											}
											?>
										</select>
									</div>
								</div>
								<div class="col-lg-3">
									<div class="form-group">
										<label>Birth Date <span class="ren_r">*</span></label>
										<input type="date" name="BirthDate" class="form-control" id="bday"
											onchange="submitBday()" required value="<?= $app->BirthDate; ?>">
									</div>
								</div>
								<div class="col-lg-1">
									<div class="form-group">
										<label>Age </label>
										<input type="text" readonly name="age" id="resultBday" class="form-control"
											value="<?= $app->age; ?>" />
									</div>
								</div>
								<div class="col-lg-5">
									<div class="form-group">
										<label>Birth Place</label>
										<input type="text" class="form-control" name="BirthPlace"
											value="<?= $app->BirthPlace; ?>">

									</div>
								</div>

							</div>

							<div class="row">
								<div class="col-lg-3">
									<div class="form-group">
										<label>Civil Status </label>
										<select name="MaritalStatus" class="form-control">
											<option></option>
											<?php
											$array = array('Single', 'Married', 'Divorced', 'Separated', 'Widowed');
											foreach ($array as $ar) {
												echo '<option';
												if ($ar == $app->MaritalStatus) {
													echo " selected ";
												}
												echo '>' . $ar . '</option>';
											}
											?>
										</select>
									</div>
								</div>
								<div class="col-lg-3">
									<div class="form-group">
										<label>Height (in meter)</label>
										<input type="text" class="form-control" name="height"
											value="<?= $app->height; ?>">
									</div>
								</div>
								<div class="col-lg-3">
									<div class="form-group">
										<label>Weight (in kg)</label>
										<input type="text" class="form-control" name="weight"
											value="<?= $app->weight; ?>">
									</div>
								</div>
								<div class="col-lg-3">
									<div class="form-group">
										<label>Blood Type</label>
										<input type="text" class="form-control" name="bloodType"
											value="<?= $app->bloodType; ?>">
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-lg-3">
									<div class="form-group">
										<label>Telephone No.</label>
										<input type="text" class="form-control" name="empTelNo"
											value="<?= $app->empTelNo; ?>">
									</div>
								</div>
								<div class="col-lg-3">
									<div class="form-group">
										<label>Mobile No.</label>
										<input type="text" class="form-control" name="empMobile"
											value="<?= $app->empMobile; ?>">
									</div>
								</div>
								<div class="col-lg-6">
									<div class="form-group">
										<label>E-mail </label>
										<input type="email" class="form-control" name="empEmail"
											value="<?= $app->empEmail; ?>">
									</div>
								</div>
							</div>
							<!-- <div class="row">
										<div class="col-lg-6">
											<div class="form-group">
											<label>Facebook Link</label>
											 <input type="text" class="form-control" name="fb" value="<?= $app->fb; ?>" >
											</div>
										</div>
										<div class="col-lg-6">
											<div class="form-group">
											<label>Skype</label>
											 <input type="text" class="form-control" name="skype" value="<?= $app->skype; ?>" >
											</div>
										</div>
									</div> -->
							<div class="row">
								<div class="col-lg-3">
									<div class="form-group">
										<label>Citizenship</label>
										<input type="text" class="form-control" name="citizenship" value="Filipino">
									</div>
								</div>
								<div class="col-lg-3">
									<div class="form-group">
										<label>Dual Citizen?</label>
										<select class="form-control" name="dualCitizenship">
											<option value="">Select</option>
											<?php
											$array = array('No Data', 'No', 'Yes');
											foreach ($array as $ar) {
												echo '<option';
												if ($ar == $app->dualCitizenship) {
													echo " selected ";
												}
												echo '>' . $ar . '</option>';
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
											foreach ($array as $ar) {
												echo '<option';
												if ($ar == $app->citizenshipType) {
													echo " selected ";
												}
												echo '>' . $ar . '</option>';
											}
											?>
										</select>

									</div>
								</div>
								<div class="col-lg-3">
									<div class="form-group">
										<label>Country</label>
										<input type="text" class="form-control" name="citizenshipCountry"
											value="<?= $app->citizenshipCountry; ?>">
									</div>
								</div>
							</div>

							<div class="row">
								<h5>Official Information</h5>
								<hr />
							</div>
							<div class="row">
								<div class="col-lg-3">
									<div class="form-group">
										<label>Current Position</label>
										<!-- <?php if($this->session->position == 'user'){ ?>
											<input type="text" readonly class="form-control" name="empPosition" value="<?= $app->empPosition; ?>">
										<?php }else{ ?>
										<select class="form-control" name="empPosition">
											<option value=""></option>
											<?php foreach($position as $row){ ?>
											<option <?php if(strtolower($row->title) ==  strtolower($app->empPosition)){echo ' selected ';}?> value="<?= strtoupper($row->title); ?>"><?= $row->title; ?></option>
											<?php } ?>
										</select>
										<?php } ?> -->

										<select class="form-control" name="empPosition">
											<option value=""></option>
											<?php foreach($position as $row){ ?>
											<option <?php if(strtolower($row->title) ==  strtolower($app->empPosition)){echo ' selected ';}?> value="<?= strtoupper($row->title); ?>"><?= $row->title; ?></option>
											<?php } ?>
										</select>

									</div>

								</div>
								<div class="col-lg-3">
									<div class="form-group">
										<label>Current Designation</label>
										
										<select class="form-control" name="jobTitle">
											<option value=""></option>
											<?php foreach($position as $row){ ?>
											<option 
												<?php if(strtolower($row->title) ==  strtolower($app->jobTitle)){echo ' selected ';}?>

											value="<?= strtoupper($row->title); ?>"><?= $row->title; ?></option>
											<?php } ?>
										</select>
									</div>
								</div>
								<div class="col-lg-1">
									<div class="form-group">
										<label>SG No.</label>
										<select class="form-control <?php if ($this->session->position == 'user') {
																		echo " readonly ";
																	} ?>" name="sgNo">
											<option></option>
											<!-- <?php
													for ($x = 1; $x <= 33; $x += 1) {
														echo "<option";
														if ($x == $app->sgNo) {
															echo " selected ";
														}
														echo ">SG $x</option>";
													}
													?>	 -->

											<?php
											$array = array('1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20', '21', '22', '23', '24', '25', '26', '27', '28', '29', '30', '31', '32', '33');
											foreach ($array as $ar) {
												echo '<option';
												if ($ar == $app->sgNo) {
													echo " selected ";
												}
												echo '>' . $ar . '</option>';
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
											for ($x = 1; $x <= 8; $x += 1) {
												echo "<option";
												if ($x == $app->stepNo) {
													echo " selected ";
												}
												echo ">$x</option>";
											}
											?>
										</select>
									</div>
								</div>
								<?php if ($this->session->position == 'asds' || $this->session->position == 'Human Resource Admin' || $this->session->position == 'HR Staff') { ?>
									<div class="col-lg-2">
										<div class="form-group">
											<label>Auth. Annual Salary</label>
											<input type="text" class="form-control" name="authAnSalary" value="<?= $app->authAnSalary; ?>">
										</div>
									</div>
									<div class="col-lg-2">
										<div class="form-group">
											<label>Actual Salary</label>
											<input type="text" class="form-control" name="actualSalary" value="<?= $app->actualSalary; ?>">
										</div>
									</div>
								<?php } else { ?>
									<input type="hidden" class="form-control" name="authAnSalary" value="<?= $app->authAnSalary; ?>">
									<input type="hidden" class="form-control" name="actualSalary" value="<?= $app->actualSalary; ?>">
								<?php } ?>
							</div>
							<div class="row">
								<div class="col-lg-3">
									<div class="form-group">
										<label>Item No.</label>
										<input type="text" class="form-control" name="itemNo"
											<?php if ($this->session->position == 'user') {
												echo " readonly ";
											} ?>
											value="<?= $app->itemNo; ?>">
									</div>
								</div>

								<div class="col-lg-4">
									<div class="form-group">
										<label>Department/School</label>
										<input type="text" class="form-control" name="Department"
											value="<?= $app->Department; ?>">
									</div>
								</div>
								<div class="col-lg-3">
									<div class="form-group">
										<label>School ID (if assigned in school)</label>
										<input type="text" class="form-control" name="schoolID"
											value="<?= $app->schoolID; ?>">
									</div>
								</div>
								<input type="hidden" class="form-control" value='112' readonly name="agencyCode"
									value="<?= $app->agencyCode; ?>">

								<div class="col-lg-2">
									<div class="form-group">
										<label>Station Code </label>
										<input type="text" class="form-control" name="staCode"
											value="<?= $app->staCode; ?>">
									</div>
								</div>
							</div>
							<div class="row">

								<div class="col-lg-4">
									<div class="form-group">
										<label>Date Hired</label>
										<input type="date" class="form-control" name="dateHired"
											<?php //if($this->session->position == 'user'){echo " readonly ";}
											?>
											value="<?= $app->dateHired; ?>">
									</div>
								</div>
								<div class="col-lg-4">
									<div class="form-group">
										<label>Last Appointment</label>
										<input type="date" class="form-control" name="lastAppointmentDate"
											<?php //if($this->session->position == 'user'){echo " readonly ";}
											?>
											value="<?= $app->lastAppointmentDate; ?>">
									</div>
								</div>
								<div class="col-lg-4">
									<div class="form-group">
										<label>Expected Ret. Year</label>
										<input type="text" class="form-control" name="retYear"
											value="<?= $app->retYear; ?>">
									</div>
								</div>


							</div>
							<div class="row">

								<div class="col-lg-6">
									<div class="form-group">
										<label>Direct Superior</label>
										<input type="text" class="form-control" name="directHead"
											value="<?= $app->directHead; ?>">
									</div>
								</div>
								<div class="col-lg-6">
									<div class="form-group">
										<label>Superior Position</label>
										<input type="text" class="form-control" name="directHeadPosition"
											value="<?= $app->directHeadPosition; ?>">
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-lg-2">
									<div class="form-group">
										<label>UMID</label>
										<input type="text" class="form-control" name="umid" value="<?= $app->umid; ?>">
									</div>
								</div>
								<div class="col-lg-2">
									<div class="form-group">
										<label>PhilHealth No.</label>
										<input type="text" class="form-control" name="philHealth"
											value="<?= $app->philHealth; ?>">
									</div>
								</div>

								<div class="col-lg-2">
									<div class="form-group">
										<label>GSIS</label>
										<input type="text" class="form-control" name="gsis" value="<?= $app->gsis; ?>">
									</div>
								</div>
								<div class="col-lg-2">
									<div class="form-group">
										<label>PAGIBIG</label>
										<input type="text" class="form-control" name="pagibig"
											value="<?= $app->pagibig; ?>">
									</div>
								</div>
								<div class="col-lg-2">
									<div class="form-group">
										<label>SSS No.</label>
										<input type="text" class="form-control" name="sssNo"
											value="<?= $app->sssNo; ?>">
									</div>
								</div>
								<div class="col-lg-2">
									<div class="form-group">
										<label>TIN</label>
										<input type="text" class="form-control" name="tinNo"
											value="<?= $app->tinNo; ?>">
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-lg-3">
									<div class="form-group">
										<label>LBP Account No.</label>
										<input type="text" class="form-control" name="bnk_acct"
											value="<?= $app->bnk_acct; ?>">
									</div>
								</div>

								<div class="col-lg-3">
									<div class="form-group">
										<label>Employment Status</label>
										<select class="form-control" name="empStatus">
											<option></option>
											<?php
											$array = array(
												'Elected',
												'Co-Terminous',
												'Permanent',
												'Presidential',
												'Temporary',
												'JO',
												'COS',
												'MOA',
												'Casual/Contractual',
												'Provisional'
											);
											foreach ($array as $ar) {
												echo '<option';
												if ($ar == $app->empStatus) {
													echo " selected ";
												}
												echo '>' . $ar . '</option>';
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
											$array = array(
												'No Eligibility',
												'PBET',
												'RA 4670',
												'RA 6850',
												'RA 1080',
												'LET',
												'PD 907',
												'PD 997',
												'Civil Service Professional',
												'Civil Service Sub-Professional',
												'Barangay Officials'
											);
											foreach ($array as $ar) {
												echo '<option';
												if ($ar == $app->csEligibility) {
													echo " selected ";
												}
												echo '>' . $ar . '</option>';
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
											$array = array('1st Level', '2nd Level', '3rd Level', 'No Eligibility');
											foreach ($array as $ar) {
												echo '<option';
												if ($ar == $app->csLevel) {
													echo " selected ";
												}
												echo '>' . $ar . '</option>';
											}
											?>
										</select>
									</div>
								</div>

							</div>

							<div class="row">

								<div class="col-lg-3">
									<div class="form-group">
										<label>Current Status</label>
										<select class="form-control" name="currentStatus">
											<option></option>
											<?php
											$array = array('Active', 'Retired', 'Resigned', 'Transferred', 'Deceased');
											foreach ($array as $ar) {
												echo '<option';
												if ($ar == $app->currentStatus) {
													echo " selected ";
												}
												echo '>' . $ar . '</option>';
											}
											?>
										</select>
									</div>
								</div>






								<?php if ($this->session->userdata('position') === 'Admin'): ?>
									<div class="col-lg-3">
										<div class="form-group">
											<label>Entitled to Leave Credits?</label>
											<select class="form-control" name="leaveCredits">
												<option></option>
												<?php
												$array = array('Yes', 'No');
												foreach ($array as $ar) {
													echo '<option';
													if ($ar == $app->leaveCredits) {
														echo " selected ";
													}
													echo '>' . $ar . '</option>';
												}
												?>
											</select>


										</div>
									</div>

								<?php endif; ?>







								<div class="col-lg-3">
									<div class="form-group">
										<label>Employee Group 1</label>
										<select class="form-control" name="payGroup">
											<option></option>
											<?php
											$array = array('Casual', 
															'Elementary', 
															'Secondary', 
															'Senior High School', 
															'Provisionary', 
															'Kindergarten',
															'Office Of The Schools Division Superintendent',
															'School Governance And Operations Division',
															'Curriculum Implementation Division',
															'Division of Davao Oriental-ALS'
															);
											foreach ($array as $ar) {
												echo '<option';
												if ($ar == $app->payGroup) {
													echo " selected ";
												}
												echo '>' . $ar . '</option>';
											}
											?>
										</select>
									</div>
								</div>

								<div class="col-lg-3">
									<div class="form-group">
										<label>Employee Group 2</label>
										<select class="form-control" name="payCat">
											<option></option>
											<?php
											$array = array('Teaching', 'Non-Teaching', 'Teaching Related');
											foreach ($array as $ar) {
												echo '<option';
												if ($ar == $app->payCat) {
													echo " selected ";
												}
												echo '>' . $ar . '</option>';
											}
											?>
										</select>
									</div>
								</div>
								<?php
								$allowed_roles = array('Human Resource Admin', 'HR Staff');
								if (in_array($this->session->userdata('position'), $allowed_roles)):
								?>
									<div class="col-lg-3">
										<div class="form-group">
											<label>With Monthly Leave Credits (VL and SL)?</label>
											<select class="form-control" name="leaveCredits">
												<option></option>
												<?php
												$array = array('Yes', 'No');
												foreach ($array as $ar) {
													echo '<option';
													if ($ar == $app->leaveCredits) {
														echo " selected ";
													}
													echo '>' . $ar . '</option>';
												}
												?>
											</select>
										</div>
									</div>
								<?php endif; ?>

							</div>


							<!-- <div class="row">
											<div class="col-lg-3">
												<div class="form-group">
												<label>No. of Years as JO/COS</label>
												<input type="number" class="form-control" name="YearsAsJO" value="<?= $app->YearsAsJO; ?>" >
												</div>
											</div>
										<div class="col-lg-3">
											<div class="form-group">
											<label>Nature of Work 1</label>
											<select class="form-control" name="workNature1">
												<option></option>
												<?php
												$array = array('Clerical Services', 'IT Services', 'Administrative', 'Watchman', 'Driver');
												foreach ($array as $ar) {
													echo '<option';
													if ($ar == $app->workNature1) {
														echo " selected ";
													}
													echo '>' . $ar . '</option>';
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
												$array = array('Clerical Services', 'IT Services', 'Administrative', 'Watchman', 'Driver');
												foreach ($array as $ar) {
													echo '<option';
													if ($ar == $app->workNature2) {
														echo " selected ";
													}
													echo '>' . $ar . '</option>';
												}
												?>
											</select>
											</div>
										</div>

									</div> -->
							<div class="row">
								<br />
								<h5>Contact Person in Case of Emergency</h5>
							</div>

							<div class="row">
								<div class="col-lg-3">
									<div class="form-group">
										<label>Contact Name</label>
										<input type="text" class="form-control" name="contactName"
											value="<?= $app->contactName; ?>">
									</div>
								</div>
								<div class="col-lg-3">
									<div class="form-group">
										<label>Relationship</label>
										<input type="text" class="form-control" name="contactRel"
											value="<?= $app->contactRel; ?>">
									</div>
								</div>
								<div class="col-lg-6">
									<div class="form-group">
										<label>Email</label>
										<input type="email" class="form-control" name="contactEmail"
											value="<?= $app->contactEmail; ?>">
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-lg-3">
									<div class="form-group">
										<label>Contact No.</label>
										<input type="text" class="form-control" name="contactNo"
											value="<?= $app->contactNo; ?>">
									</div>
								</div>
								<div class="col-lg-9">
									<div class="form-group">
										<label>Address</label>
										<input type="text" class="form-control" name="contactAddress"
											value="<?= $app->contactAddress; ?>">
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
										<input type="text" class="form-control" name="resHouseNo"
											value="<?= $app->resHouseNo; ?>">
									</div>
								</div>
								<div class="col-lg-3">
									<div class="form-group">
										<label>Street</label>
										<input type="text" class="form-control" name="resStreet"
											value="<?= $app->resStreet; ?>">
									</div>
								</div>
								<div class="col-lg-3">
									<div class="form-group">
										<label>Village</label>
										<input type="text" class="form-control" name="resVillage"
											value="<?= $app->resVillage; ?>">
									</div>
								</div>
								<div class="col-lg-3">
									<div class="form-group">
										<label>Barangay</label>
										<input type="text" class="form-control" name="resBarangay"
											value="<?= $app->resBarangay; ?>">
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-lg-3">
									<div class="form-group">
										<label>Zip Code</label>
										<input type="text" class="form-control" name="resZipCode"
											value="<?= $app->resZipCode; ?>">
									</div>
								</div>
								<div class="col-lg-3">
									<div class="form-group">
										<label>City/Municipality</label>
										<input type="text" class="form-control" name="resCity"
											value="<?= $app->resCity; ?>">
									</div>
								</div>
								<div class="col-lg-6">
									<div class="form-group">
										<label>Province</label>
										<input type="text" class="form-control" name="resProvince"
											value="<?= $app->resProvince; ?>">
									</div>
								</div>
							</div>

							<div class="row">
										
										<div class="col-lg-3">
												<div class="form-group">
													<label>Religion</label>
													 <input list="religion" id="browser" name="religion" class="form-control" required value="<?= $app->religion; ?>">
														<datalist id="religion">
															<?php foreach($re as $row){ ?>
																<option value="<?= $row->religion; ?>">
															<?php } ?>
														</datalist>	
													 
												</div>
										</div>
										<div class="col-lg-3">
												<div class="form-group">
													<label>Disability</label>
													 <select class="form-control" name="disability" required>
                                                        <option value="">Select</option>
                                                        <?php 
                                                                $array = array('No Data' => 0, 'No' => 1, 'Yes' => 2); 
                                                                foreach($array as $ar => $key){ ?>
																<option <?php if($app->disability == $key){echo ' selected ';} ?>  value="<?= $key; ?>"><?= $ar; ?></option>
                                                            <?php } ?>
													 </select>
													 
												</div>
										</div>
										<div class="col-lg-3">
											<div class="form-group">
											<label>Ethnic Group</label>
											<input list="ethnicity" id="browser" name="ethnicity" class="form-control" required value="<?= $app->religion; ?>">
											<datalist id="ethnicity">
												<?php foreach($et as $row){ ?>
													<option value="<?= $row->ethnicity; ?>">
												<?php } ?>
											</datalist>		 
											</div>
										</div>
									</div>

							<div class="row">
								<div class="col-lg-12">
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