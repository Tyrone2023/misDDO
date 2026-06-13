<?php include('templates/head.php'); ?>  
<?php include('templates/header.php'); ?>  

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
						<div class="col-md-12">
							<div class="page-title-box">
								<!-- <h4 class="page-title">Student's Profile Form</h4> -->
								<div class="page-title-right">
									<ol class="breadcrumb p-0 m-0">
										<!-- <li class="breadcrumb-item"><a href="#">Currently login to <b>SY <?php echo $this->session->userdata('sy'); ?> <?php echo $this->session->userdata('semester'); ?></b></a></li> -->
									</ol>
								</div>
								<div class="clearfix"></div>
							</div>
						</div>
					</div>
					<div class="col-xl-12 col-sm-6 ">
						<!-- Portlet card -->
						<div class="card">
							<div class="card-header bg-info py-3 text-white">
								<div class="card-widgets">
									<a href="javascript:;" data-toggle="reload"><i class="mdi mdi-refresh"></i></a>
									<a data-toggle="collapse" href="#cardCollpase3" role="button" aria-expanded="false" aria-controls="cardCollpase2"><i class="mdi mdi-minus"></i></a>
									<!-- <a href="#" data-toggle="remove"><i class="mdi mdi-close"></i></a> -->
								</div>
								<h5 class="card-title mb-0 text-white">Student's Profile Form</h5>
							</div>
							<div id="cardCollpase3" class="collapse show">
								<div class="card-body">
									<!-- Flash Message Here -->
									<?php if ($this->session->flashdata('success')) : ?>

										<?= '<div class="alert alert-success alert-dismissible fade show" role="alert">
											<button type="button" class="close" data-dismiss="alert" aria-label="Close">
												<span aria-hidden="true">&times;</span>
											</button>'
											. $this->session->flashdata('success') .
											'</div>';
										?>
									<?php endif; ?>

									<?php if ($this->session->flashdata('danger')) : ?>
										<?= '<div class="alert alert-danger alert-dismissible fade show" role="alert">
												<button type="button" class="close" data-dismiss="alert" aria-label="Close">
													<span aria-hidden="true">&times;</span>
												</button>'
											. $this->session->flashdata('danger') .
											'</div>';
										?>
									<?php endif;  ?>
									<form role="form" method="post" enctype="multipart/form-data" class="parsley-examples">

										<div class="card-body">
											<div class="row">
												<h5>Personal Information</h5>
											</div>
											<div class="row">
												<div class="col-lg-3">
													<div class="form-group">
														<label for="lastName">Student No. <span style="color:red">*</span></label>
														<input type="text" class="form-control" name="StudentNumber" required>
													</div>
												</div>
												<div class="col-lg-3">
													<div class="form-group">
														<label for="lastName">LRN</label>
														<input type="text" class="form-control" name="LRN">
													</div>
												</div>

											</div>
											<div class="row">
												<div class="col-lg-3">
													<div class="form-group">
														<label>First Name <span style="color:red">*</span></label>
														<input type="text" class="form-control" name="FirstName" value="" required>
													</div>
												</div>
												<div class="col-lg-3">
													<div class="form-group">
														<label>Middle Name</label>
														<input type="text" class="form-control" name="MiddleName" value="">
													</div>
												</div>
												<div class="col-lg-3">
													<div class="form-group">
														<label>Last Name <span style="color:red">*</span></label>
														<input type="text" class="form-control" name="LastName" value="" required>
													</div>
												</div>
												<div class="col-lg-3">
													<div class="form-group">
														<label for="lastName">Name Extn.</label>
														<input type="text" class="form-control" name="nameExt">
													</div>
												</div>
											</div>

											<div class="row">
												<div class="col-lg-3">
													<div class="form-group">
														<label>Sex <span style="color:red">*</span></label>
														<select name="Sex" class="form-control" required>
															<option value="">Select</option>
															<option value="Female">Female</option>
															<option value="Male">Male</option>
														</select>
													</div>
												</div>
												<div class="col-lg-3">
													<div class="form-group">
														<label>Civil Status</label>
														<select name="CivilStatus" class="form-control">
															<option value="Single">Single</option>
															<option value="Married">Married</option>
														</select>
													</div>
												</div>
												<div class="col-lg-3">
													<div class="form-group">
														<label>Citizenship</label>
														<input type="text" class="form-control" name="Citizenship" value="Filipino">
													</div>
												</div>
												<div class="col-lg-3">
													<div class="form-group">
														<label>Blood Type</label>
														<input type="text" class="form-control" name="BloodType" value="">
													</div>
												</div>
											</div>
											<div class="row">



												<div class="col-lg-8">
													<div class="form-group">
														<label>Birth Place</label>
														<input type="text" class="form-control" name="BirthPlace" value="">
													</div>
												</div>

												<div class="col-lg-3">
													<div class="form-group">
														<label>Birth Date <span style="color:red">*</span></label>
														<input type="date" name="BirthDate" class="form-control" id="bday" onchange="submitBday()" required>
													</div>
												</div>
												<div class="col-lg-1">
													<div class="form-group">
														<label>Age</label>
														<input type="text" name="Age" id="resultBday" class="form-control" readonly />
													</div>
												</div>

											</div>


											<div class="row">
												<div class="col-lg-3">
													<div class="form-group">
														<label>Ethnicity</label>
														<select class="form-control select2" name="Ethnicity" required>
															<option disabled selected></option>
															<?php foreach ($ethnicity as $row) { ?>
																<option value="<?= $row->ethnicity; ?>"><?= $row->ethnicity; ?></option>
															<?php } ?>
														</select>
													</div>
												</div>

												<div class="col-lg-3">
													<div class="form-group">
														<label>Religion</label>
														<select class="form-control select2" name="Religion" required>
															<option disabled selected></option>
															<?php foreach ($religion as $row) { ?>
																<option value="<?= $row->religion; ?>"><?= $row->religion; ?></option>
															<?php } ?>
														</select>
													</div>
												</div>

												<div class="col-lg-6">
													<div class="form-group">
														<label>Last School Attended </label>
														<select class="form-control select2" name="Elementary">
															<option disabled selected></option>
															<?php foreach ($prevschool as $row) { ?>
																<option value="<?= $row->School; ?>"><?= $row->School; ?></option>
															<?php } ?>
														</select>
													</div>
												</div>

												<!-- <div class="col-lg-3">
													<div class="form-group">
														<label>Parent's Monthly Income</label>
														<select name="ParentsMonthly" class="form-control select2" id="salary" required>
															<option value="0001-5,000">1 - 5,000</option>
															<option value="5,001-10,000">5,001 - 10,000</option>
															<option value="10,001-15,000">10,001 - 15,000</option>
															<option value="15,001-20,000">15,001 - 20,000</option>
															<option value="20,001-25,000">20,001 - 25,000</option>
															<option value="25,001-30,000">25,001 - 30,000</option>
															<option value="30,001-35,000">30,001 - 35,000</option>
															<option value="35,001-40,000">35,001 - 40,000</option>
															<option value="40,001-45,000">40,001 - 45,000</option>
															<option value="45,001-50,000">45,001 - 50,000</option>
															<option value="50,001-10,0000">50,001 - 100,000</option>
															<option value="100,001-150,000">100,001 - 150,000</option>
														</select>
													</div>
												</div> -->
											</div>




											<div class="row">
												<h5>Contact Information</h5>
											</div>
											<div class="row">
												<div class="col-lg-3">
													<div class="form-group">
														<label>Telephone No.</label>
														<input type="text" class="form-control" name="TelNumber" value="">
													</div>
												</div>
												<div class="col-lg-3">
													<div class="form-group">
														<label>Mobile No.</label>
														<input type="text" class="form-control" name="MobileNumber" value="">
													</div>
												</div>
												<div class="col-lg-6">
													<div class="form-group">
														<label>E-mail </label>
														<input type="email" class="form-control" name="EmailAddress" value="" >
													</div>
												</div>
											</div>

											<div class="row">
												<div class="col-lg-3">
													<div class="form-group">
														<label for="province">Province</label>
														<select id="province" name="Province" class="form-control" required>
															<option value="">Select Province</option>
															<?php foreach ($provinces as $province): ?>
																<option value="<?php echo $province['id']; ?>"><?php echo $province['name']; ?></option>
															<?php endforeach; ?>
														</select>
													</div>
												</div>

												<div class="col-lg-3">
													<div class="form-group">
														<label for="city">City/Municipality</label>
														<select id="city" name="City" class="form-control" disabled required>
															<option value="">Select City/Municipality</option>
														</select>
													</div>
												</div>

												<div class="col-lg-3">
													<div class="form-group">
														<label for="barangay">Barangay</label>
														<select id="barangay" name="Brgy" class="form-control" disabled required>
															<option value="">Select Barangay</option>
														</select>
													</div>
												</div>

												<div class="col-lg-3">
													<div class="form-group">
														<label for="sitio">Sitio</label>
														<input type="text" id="sitio" class="form-control" name="Sitio" placeholder="Sitio">
													</div>
												</div>
											</div>









											<div class="row">
												<h5>Parents/Guardian Information</h5>
											</div>
											<div class="row">
												<div class="col-lg-4">
													<div class="form-group">
														<label>Guardian</label>
														<input type="text" class="form-control" name="Guardian" value="">
													</div>
												</div>
												<div class="col-lg-4">
													<div class="form-group">
														<label>Relationship to Guardian </label>
														<input type="text" class="form-control" name="GuardianRelationship" value="">
													</div>
												</div>
												<div class="col-lg-4">
													<div class="form-group">
														<label>Guardian Address </label>
														<input type="text" class="form-control" name="GuardianAddress" value="">
													</div>
												</div>

											</div>
											<div class="row">
												<div class="col-lg-4">
													<div class="form-group">
														<label>Guardian Contact No.</label>
														<input type="text" class="form-control" name="GuardianContact" value="">
													</div>
												</div>

												<div class="col-lg-4">
													<div class="form-group">
														<label>Guardian Tel. No.</label>
														<input type="text" class="form-control" name="GuardianTelNo" value="">
													</div>
												</div>

												<div class="col-lg-4">
													<div class="form-group">
														<label>Guardian Occupation</label>
														<input type="text" class="form-control" name="guardianOccupation" value="">
													</div>
												</div>
											</div>

											<div class="row">
												<div class="col-lg-4">
													<div class="form-group">
														<label>Father's Name</label>
														<input type="text" class="form-control" name="Father" value="">
													</div>
												</div>
												<div class="col-lg-4">
													<div class="form-group">
														<label>Father's Occupation</label>
														<input type="text" class="form-control" name="FOccupation" value="">
													</div>
												</div>
												<div class="col-lg-4">
													<div class="form-group">
														<label>Mobile No.</label>
														<input type="text" class="form-control" name="fContactNo" value="">
													</div>
												</div>


											</div>

											<div class="row">
												<div class="col-lg-4">
													<div class="form-group">
														<label>Mother</label>
														<input type="text" class="form-control" name="Mother" value="">
													</div>
												</div>
												<div class="col-lg-4">
													<div class="form-group">
														<label>Mother's Occupation</label>
														<input type="text" class="form-control" name="MOccupation" value="">
													</div>
												</div>
												<div class="col-lg-4">
													<div class="form-group">
														<label>Mobile No.</label>
														<input type="text" class="form-control" name="mContactNo" value="">
													</div>
												</div>

											</div>

											<div class="row">
												<div class="col-lg-12">
													<div class="form-group">
														<label>Additional Notes</label>
														<textarea class="form-control" rows="5" id="example-textarea" name="Notes"></textarea>
													</div>
												</div>
											</div>
											<?php if($this->session->position == 'School'){ ?>
												<input type="hidden" name="schoolID" value="<?= $this->session->userdata('username'); ?>" readonly>
												<input type="hidden" name="encode" value="<?= $this->session->userdata('username'); ?>" readonly>
											<?php }else{ ?>
												<input type="hidden" name="encode" value="<?= $this->session->userdata('username'); ?>" readonly>
												<input type="hidden" name="schoolID" value="<?= $staff->schoolID; ?>" readonly>
											<?php } ?>


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
					<!-- end card-->

				</div>
				<!-- end col -->



			</div>
		</div>

		<!-- end container-fluid -->

	</div>
	<!-- end content -->



	<!-- Footer Start -->
	<?php include('includes/footer.php'); ?>
	<!-- end Footer -->

	</div>
	<script src="<?= base_url(); ?>assets/js/jquery-3.6.0.min.js"></script>

	<script type="text/javascript">
		$(document).ready(function() {
			// Load provinces on page load
			$.ajax({
				url: '<?php echo site_url('Page/get_provinces'); ?>',
				type: 'GET',
				dataType: 'json',
				success: function(data) {
					$('#province').html('<option value="">Select Province</option>');
					$.each(data, function(index, province) {
						$('#province').append('<option value="' + province.id + '">' + province.name + '</option>');
					});
				}
			});

			// Load cities based on selected province
			$('#province').change(function() {
				var province = $(this).val();
				$('#city').prop('disabled', province == '');
				$('#barangay').prop('disabled', true).html('<option value="">Select Barangay</option>');

				if (province) {
					$.ajax({
						url: '<?php echo site_url('Page/get_cities'); ?>',
						type: 'POST',
						dataType: 'json',
						data: {
							province: province
						},
						success: function(data) {
							$('#city').html('<option value="">Select City/Municipality</option>');
							$.each(data, function(index, city) {
								$('#city').append('<option value="' + city.id + '">' + city.name + '</option>');
							});
						},
						error: function(xhr, status, error) {
							console.error("AJAX Error: ", status, error);
						}
					});
				} else {
					$('#city').html('<option value="">Select City/Municipality</option>');
				}
			});


			// Load barangays based on selected city
			$('#city').change(function() {
				var city = $(this).val();
				$('#barangay').prop('disabled', city == '');

				if (city) {
					$.ajax({
						url: '<?php echo site_url('Page/get_barangays'); ?>',
						type: 'POST',
						dataType: 'json',
						data: {
							city: city
						},
						success: function(data) {
							$('#barangay').html('<option value="">Select Barangay</option>');
							$.each(data, function(index, barangay) {
								$('#barangay').append('<option value="' + barangay.id + '">' + barangay.name + '</option>');
							});
						},
						error: function(xhr, status, error) {
							console.error("AJAX Error: ", status, error);
						}
					});
				} else {
					$('#barangay').html('<option value="">Select Barangay</option>');
				}
			});
		});
	</script>

	<!-- ============================================================== -->
	<!-- End Page content -->
	<!-- ============================================================== -->

	</div>
	<!-- END wrapper -->


	<!-- Vendor js -->
	<script src="<?= base_url(); ?>assets/js/vendor.min.js"></script>

	<!-- Plugin js-->
	<script src="<?= base_url(); ?>assets/libs/parsleyjs/parsley.min.js"></script>

	<!-- Validation init js-->
	<script src="<?= base_url(); ?>assets/js/pages/form-validation.init.js"></script>

	<!-- Select2 JS -->
	<script src="<?= base_url(); ?>assets/libs/select2/select2.min.js"></script>

	<!-- App js -->
	<script src="<?= base_url(); ?>assets/js/app.min.js"></script>

	<!-- Initialize Select2 -->
	<script>
		$(document).ready(function() {
			$('.select2').select2();
		});
	</script>





</body>

</html>