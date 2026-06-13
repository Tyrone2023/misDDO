<script type="text/javascript">
	function submitBday() {

		var Bdate = document.getElementById('bday').value;
		var Bday = +new Date(Bdate);
		Q4A = ~~((Date.now() - Bday) / (31557600000));
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

							<form class="parsley-examples" method="post" action="<?= base_url(); ?>Pages/personel_add">
								<div class="row">
									<h5>Personal Information</h5>
								</div>
								<input type="hidden" name="jobTitle" value=" " />
								<!-- <input type="hidden" name="perHouseNo" value="">
									<input type="hidden" name="perStreet" value="">
									<input type="hidden" name="perVillage" value="">
									<input type="hidden" name="perBarangay" value="">
									<input type="hidden" name="perCity" value="">
									<input type="hidden" name="perProvince" value="">
									<input type="hidden" name="perZipCode" value=""> -->
								<div class="row">
									<div class="col-lg-3">
										<div class="form-group">
											<label for="lastName">Employee No.</label>
											<input type="text"  class="form-control" name="IDNumber" required value="<?= $newIDNumber; ?>">
											<input type="hidden" value="<?= $ltd; ?>" name="ltd">
										</div>
									</div>
									<!-- <div class="col-lg-3">
												<div class="form-group">
													<label for="lastName">Employee No. <span class="ren_r">*</span></label>
													<input type="text" class="form-control" name="employeeNo" required value="<?= set_value('employeeNo'); ?>">
												</div>	
										</div> -->
									<div class="col-lg-3">
										<div class="form-group">
											<label for="lastName">Prefix </label>
											<select name="prefix" class="form-control">
												<option></option>
												<?php
												$array = array('Mr.', 'Ms.', 'Mrs.');
												foreach ($array as $ar) {
													echo '<option>' . $ar . '</option>';
												}
												?>
											</select>
										</div>
									</div>
								</div>

								<div class="row">
									<div class="col-lg-3">
										<div class="form-group">
											<label>First Name </label>
											<input type="text" class="form-control" name="FirstName" value="<?= set_value('FirstName'); ?>" required>
										</div>
									</div>
									<div class="col-lg-3">
										<div class="form-group">
											<label>Middle Name</label>
											<input type="text" class="form-control" name="MiddleName" value="<?= set_value('MiddleName'); ?>">
										</div>
									</div>
									<div class="col-lg-3">
										<div class="form-group">
											<label>Last Name</label>
											<input type="text" class="form-control" name="LastName" value="<?= set_value('LastName'); ?>" required>
										</div>
									</div>
									<div class="col-lg-3">
										<div class="form-group">
											<label for="lastName">Name Extn.</label>
											<input type="text" class="form-control" name="NameExtn" value="<?= set_value('NameExtn'); ?>">
										</div>
									</div>
								</div>

								<div class="row">

									<div class="col-lg-3">
										<div class="form-group">
											<label>Sex </label>
											<select name="Sex" class="form-control" required>
												<option></option>
												<?php
												$array = array('Female', 'Male');
												foreach ($array as $ar) {
													echo '<option>' . $ar . '</option>';
												}
												?>
											</select>
										</div>
									</div>
									<div class="col-lg-3">
										<div class="form-group">
											<label>Birth Date</label>
											<input type="date" name="BirthDate" class="form-control" id="bday" onchange="submitBday()" required>
										</div>
									</div>
									<div class="col-lg-1">
										<div class="form-group">
											<label>Age </label>
											<input type="text" name="age" id="resultBday" class="form-control" />
										</div>
									</div>
									<div class="col-lg-5">
										<div class="form-group">
											<label>Birth Place</label>
											<input type="text" class="form-control" name="BirthPlace">

										</div>
									</div>

								</div>

								<div class="row">
									<div class="col-lg-3">
										<div class="form-group">
											<label>Civil Status </label>
											<select name="MaritalStatus" class="form-control" required>
												<option></option>
												<?php
												$array = array('Single', 'Married', 'Separated', 'Divorced');
												foreach ($array as $ar) {
													echo '<option>' . $ar . '</option>';
												}
												?>
											</select>
										</div>
									</div>
									<div class="col-lg-3">
										<div class="form-group">
											<label>Height (in meter)</label>
											<input type="text" class="form-control" name="height">
										</div>
									</div>
									<div class="col-lg-3">
										<div class="form-group">
											<label>Weight (in kg)</label>
											<input type="text" class="form-control" name="weight">
										</div>
									</div>
									<div class="col-lg-3">
										<div class="form-group">
											<label>Blood Type</label>
											<input type="text" class="form-control" name="bloodType">
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-lg-3">
										<div class="form-group">
											<label>Telephone No.</label>
											<input type="text" class="form-control" name="empTelNo">
										</div>
									</div>
									<div class="col-lg-3">
										<div class="form-group">
											<label>Mobile No.</label>
											<input type="text" class="form-control" name="empMobile">
										</div>
									</div>
									<div class="col-lg-6">
										<div class="form-group">
											<label>E-mail <span class="ren_r">*</span></label>
											<input type="email" class="form-control" name="empEmail" required>
										</div>
									</div>
								</div>
								<!-- <div class="row">
										<div class="col-lg-6">
											<div class="form-group">
											<label>Facebook Link</label>
											 <input type="text" class="form-control" name="fb" >
											</div>
										</div>
										<div class="col-lg-6">
											<div class="form-group">
											<label>Skype</label>
											 <input type="text" class="form-control" name="skype" >
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
													echo '<option>' . $ar . '</option>';
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
													echo '<option>' . $ar . '</option>';
												}
												?>
											</select>

										</div>
									</div>
									<div class="col-lg-3">
										<div class="form-group">
											<label>Country</label>
											<input type="text" class="form-control" name="citizenshipCountry">
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
											<input type="text" class="form-control" name="empPosition" required>
										</div>
									</div>
									<div class="col-lg-2">
										<div class="form-group">
											<label>Item No.</label>
											<input type="text" class="form-control" name="itemNo" required>
										</div>
									</div>
									<div class="col-lg-2">
										<div class="form-group">
											<label>SG No.</label>
											<select class="form-control" name="sgNo">
												<option></option>
												<option>SG 1</option>
												<option>SG 2</option>
												<option>SG 3</option>
												<option>SG 4</option>
												<option>SG 5</option>
												<option>SG 6</option>
												<option>SG 7</option>
												<option>SG 8</option>
												<option>SG 9</option>
												<option>SG 10</option>
												<option>SG 11</option>
												<option>SG 12</option>
												<option>SG 13</option>
												<option>SG 14</option>
												<option>SG 15</option>
												<option>SG 16</option>
												<option>SG 17</option>
												<option>SG 18</option>
												<option>SG 19</option>
												<option>SG 20</option>
												<option>SG 21</option>
												<option>SG 22</option>
												<option>SG 23</option>
												<option>SG 24</option>
												<option>SG 25</option>
												<option>SG 26</option>
												<option>SG 27</option>
												<option>SG 28</option>
												<option>SG 29</option>
												<option>SG 30</option>
												<option>SG 31</option>
												<option>SG 32</option>
												<option>SG 33</option>
											</select>
										</div>
									</div>
									<div class="col-lg-1">
										<div class="form-group">
											<label>Step No.</label>
											<select class="form-control" name="stepNo">
												<option></option>
												<option>1</option>
												<option>2</option>
												<option>3</option>
												<option>4</option>
												<option>5</option>
												<option>6</option>
												<option>7</option>
												<option>8</option>
											</select>
										</div>
									</div>
									<div class="col-lg-2">
										<div class="form-group">
											<label>Auth. Annual Salary</label>
											<input type="text" class="form-control" name="authAnSalary">
										</div>
									</div>
									<div class="col-lg-2">
										<div class="form-group">
											<label>Actual Salary</label>
											<input type="text" class="form-control" name="actualSalary">
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-lg-6">
										<div class="form-group">
											<label>Department/School</label>
											<input type="text" class="form-control" name="Department">
										</div>
									</div>
									<div class="col-lg-3">
										<div class="form-group">
											<label>School ID (if assigned in school)</label>
											<input type="text" class="form-control" name="schoolID">
										</div>
									</div>
									<input type="hidden" class="form-control" value='112' readonly name="agencyCode">

									<div class="col-lg-3">
										<div class="form-group">
											<label>Station Code </label>
											<input type="text" class="form-control" name="staCode">
										</div>
									</div>
								</div>
								<div class="row">

									<div class="col-lg-4">
										<div class="form-group">
											<label>Date Hired</label>
											<input type="date" class="form-control" name="dateHired" required>
										</div>
									</div>
									<div class="col-lg-4">
										<div class="form-group">
											<label>Last Appointment</label>
											<input type="date" class="form-control" name="lastAppointmentDate" required>
										</div>
									</div>
									<div class="col-lg-4">
										<div class="form-group">
											<label>Expected Ret. Year</label>
											<input type="text" class="form-control" name="retYear">
										</div>
									</div>


								</div>
								<div class="row">
									<div class="col-lg-2">
										<div class="form-group">
											<label>UMID</label>
											<input type="text" required class="form-control" name="umid">
										</div>
									</div>
									<div class="col-lg-2">
										<div class="form-group">
											<label>PhilHeatlth No.</label>
											<input type="text" required class="form-control" name="philHealth">
										</div>
									</div>

									<div class="col-lg-2">
										<div class="form-group">
											<label>GSIS</label>
											<input type="text" required class="form-control" name="gsis">
										</div>
									</div>
									<div class="col-lg-2">
										<div class="form-group">
											<label>PAGIBIG</label>
											<input type="text" required class="form-control" name="pagibig">
										</div>
									</div>
									<div class="col-lg-2">
										<div class="form-group">
											<label>SSS No.</label>
											<input type="text" required class="form-control" name="sssNo">
										</div>
									</div>
									<div class="col-lg-2">
										<div class="form-group">
											<label>TIN</label>
											<input type="text" required class="form-control" name="tinNo">
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
												$array = array(
													'Elected', 'Co-Terminous', 'Permanent', 'Presidential', 'Temporary',
													'JO', 'COS', 'MOA', 'Casual/Contractual','Provisional'
												);
												foreach ($array as $ar) {
													echo '<option>' . $ar . '</option>';
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
													'No Eligibility', 'PBET', 'RA 4670', 'RA 6850', 'RA 1080',
													'PD 907'
												);
												foreach ($array as $ar) {
													echo '<option>' . $ar . '</option>';
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
													echo '<option>' . $ar . '</option>';
												}
												?>
											</select>
										</div>
									</div>
									<div class="col-lg-3">
										<div class="form-group">
											<label>Current Status</label>
											<select class="form-control" name="currentStatus" required>
												<option>Active</option>
												<!-- <?php
														$array = array('Active', 'Retired', 'Resigned', 'Terminated');
														foreach ($array as $ar) {
															echo '<option>' . $ar . '</option>';
														}
														?> -->
											</select>
										</div>
									</div>
								</div>

								<div class="row">
									<div class="col-lg-3">
										<div class="form-group">
											<label>Entitled to Leave Credits?</label>
											<select class="form-control" name="leaveCredits">
												<option></option>
												<?php
												$array = array('Yes', 'No');
												foreach ($array as $ar) {
													echo '<option>' . $ar . '</option>';
												}
												?>
											</select>
										</div>
									</div>
									<div class="col-lg-3">
										<div class="form-group">
											<label>Employee Group 1</label>
											<select class="form-control" required name="payGroup">
												<option></option>
												<?php
												$array = array('Casual', 'Elementary', 'Secondary', 'Provisionary');
												foreach ($array as $ar) {
													echo '<option>' . $ar . '</option>';
												}
												?>
											</select>
										</div>
									</div>

									<div class="col-lg-3">
										<div class="form-group">
											<label>Employee Group 2</label>
											<select class="form-control" required name="payCat">
												<option></option>
												<?php
												$array = array('Teaching', 'Non-Teaching');
												foreach ($array as $ar) {
													echo '<option>' . $ar . '</option>';
												}
												?>
											</select>
										</div>
									</div>

								</div>

								<!-- <div class="row">
											<div class="col-lg-3">
												<div class="form-group">
												<label>No. of Years as JO/COS</label>
												<input type="number" class="form-control" name="YearsAsJO" >
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
													echo '<option>' . $ar . '</option>';
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
													echo '<option>' . $ar . '</option>';
												}
												?>
											</select>
											</div>
										</div>

									</div> -->


								<div class="row">
									<h5>Contact Person in Case of Emergency</h5>
								</div>

								<div class="row">
									<div class="col-lg-3">
										<div class="form-group">
											<label>Contact Name</label>
											<input type="text" class="form-control" name="contactName">
										</div>
									</div>
									<div class="col-lg-3">
										<div class="form-group">
											<label>Relationship</label>
											<input type="text" class="form-control" name="contactRel">
										</div>
									</div>
									<div class="col-lg-6">
										<div class="form-group">
											<label>Email</label>
											<input type="email" class="form-control" name="contactEmail">
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-lg-3">
										<div class="form-group">
											<label>Contact No.</label>
											<input type="text" class="form-control" name="contactNo">
										</div>
									</div>
									<div class="col-lg-9">
										<div class="form-group">
											<label>Address</label>
											<input type="text" class="form-control" name="contactAddress">
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
											<input type="text" class="form-control" name="resHouseNo">
										</div>
									</div>
									<div class="col-lg-3">
										<div class="form-group">
											<label>Street</label>
											<input type="text" class="form-control" name="resStreet">
										</div>
									</div>
									<div class="col-lg-3">
										<div class="form-group">
											<label>Village</label>
											<input type="text" class="form-control" name="resVillage">
										</div>
									</div>
									<div class="col-lg-3">
										<div class="form-group">
											<label>Barangay</label>
											<input type="text" class="form-control" name="resBarangay">
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-lg-3">
										<div class="form-group">
											<label>Zip Code</label>
											<input type="text" class="form-control" name="resZipCode">
										</div>
									</div>
									<div class="col-lg-3">
										<div class="form-group">
											<label>City</label>
											<input type="text" class="form-control" name="resCity">
										</div>
									</div>
									<div class="col-lg-6">
										<div class="form-group">
											<label>Province</label>
											<input type="text" class="form-control" name="resProvince">
										</div>
									</div>
								</div>

								<div class="row">
									<div class="col-lg-12">
										<input type="submit" name="submit" class="btn btn-info" value="Save Profile">
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