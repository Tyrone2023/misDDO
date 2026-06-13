

		<!-- ============================================================== -->
		<!-- Start Page Content here -->
		<!-- ============================================================== -->

		<?php $stud = $this->Common->one_cond_row('studeprofile', 'StudentNumber', $semstud->StudentNumber); ?>

		<div class="content-page">
			<div class="content">

				<!-- Start Content-->
				<div class="container-fluid">

					<!-- start page title -->
					<div class="row">

						<div class="col-md-12">
							<div class="page-title-box">
								
								
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

								<h4 class="page-title">ENROLLMENT FORM UPDATE <br /></h4>
								<div class="page-title-right">
									<ol class="breadcrumb p-0 m-0">
									</ol>
								</div>
								<div class="clearfix"></div>
							</div>
						</div>
					</div>

					<!-- end page title -->
					<div class="row">
						<div class="col-md-12">
							<div class="card card-info">


								<form role="form" method="post" enctype="multipart/form-data">
									<!-- general form elements -->
									<div class="card-body">
										<div class="row">
											<input type="hidden" name="id" value="<?= $this->uri->segment(3); ?>">
											<div class="col-lg-4">
												<div class="form-group">
													<label>Student No.</label>
													<input type="text" class="form-control" name="StudentNumber" value="<?= $semstud->StudentNumber; ?>" readonly required>
												</div>
											</div>
										</div>

										<div class="row">
											<div class="col-lg-4">
												<div class="form-group">
													<label>First Name</label>
													<input type="text" class="form-control" name="FName" value="<?= $stud->FirstName; ?>" readonly required>
												</div>
											</div>
											<div class="col-lg-4">
												<div class="form-group">
													<label>Middle Name</label>
													<input type="text" class="form-control" name="MName" value="<?= $stud->MiddleName; ?>" readonly required>
												</div>
											</div>
											<div class="col-lg-4">
												<div class="form-group">
													<label>Last Name</label>
													<input type="text" class="form-control" name="LName" value="<?= $stud->LastName; ?>" readonly required>
												</div>
											</div>
										</div>

										<div class="row">
											<div class="col-lg-4">
												<div class="form-group">
													<label>Department</label>
													<select name="Course" id="course" class="form-control" required>
														<option value=""></option>
														<?php foreach($dep as $row){?>
														<option value="<?= $row->CourseDescription; ?>" <?php if($row->CourseDescription == $semstud->Course){echo " selected ";} ?>><?= $row->CourseDescription; ?></option>
														<?php } ?>
													</select>
												</div>
											</div>
											<div class="col-lg-4">
												<div class="form-group">
													<label>Grade Level</label>
													<select name="YearLevel" id="yearlevel" class="form-control" required>
													<?php foreach($course as $row){?>
														<option value="<?= $row->Major; ?>" <?php if($row->Major == $semstud->YearLevel){echo " selected ";} ?> ><?= $row->Major; ?></option>
														<?php } ?>
													</select>
												</div>
											</div>
											<div class="col-lg-4">
												<div class="form-group">
													<label>Section </label>
													<select name="Section" id="section" class="form-control" required>
														<option value="<?= $semstud->Section; ?>"><?= $semstud->Section; ?></option>
													</select>
												</div>
											</div>
										</div>

										<div class="row">
											<div class="col-lg-4">
												<input type="hidden" name="IDNumber" id="AdviserID" class="form-control" readonly>
												<div class="form-group">
													<label>Adviser Name</label>
													<input type="text" name="Adviser" id="AdviserName" class="form-control" value="<?= $semstud->Adviser; ?>" readonly>
												</div>

											</div>

											<div class="col-lg-2">
												<div class="form-group">
													<label>Status</label>
													<select name="StudeStatus" id="yearlevel" class="form-control" required>
														<option <?php if($semstud->StudeStatus == 'Old'){echo " selected "; }?> >Old</option>
														<option <?php if($semstud->StudeStatus == 'New'){echo " selected "; }?>>New</option>
													</select>
												</div>
											</div>
											<div class="col-lg-3">
												<div class="form-group">
													<label>Track</label>

													<select name="Track" id="track" class="form-control">
														<option value="<?= $semstud->Track; ?>"><?= $semstud->Track; ?></option>
													</select>

												</div>
											</div>
											<div class="col-lg-3">
												<div class="form-group">
													<label>Strand</label>
													<select name="Qualification" id="strand" class="form-control" >
														<option value="<?= $semstud->Qualification; ?>"><?= $semstud->Qualification; ?></option>
													</select>
												</div>
											</div>
										</div>

										<div class="row">
											<div class="col-lg-4">
												<div class="form-group">
													<label>Balik Aral?</label>
													<select name="BalikAral" id="yearlevel" class="form-control" required>
														<option value="No Data" <?php if($semstud->BalikAral == 'No Data'){echo " selected "; }?> >No Data</option>
														<option value="Yes" <?php if($semstud->BalikAral == 'Yes'){echo " selected "; }?> >Yes</option>
														<option value="No" <?php if($semstud->BalikAral == 'No'){echo " selected "; }?> >No</option>
													</select>
												</div>
											</div>
											<div class="col-lg-4">
												<div class="form-group">
													<label>Indigenous People Member?</label>
													<select name="IP" id="yearlevel" class="form-control" required>
														<option value="No Data" <?php if($semstud->IP == 'No Data'){echo " selected "; }?> >No Data</option>
														<option value="Yes" <?php if($semstud->IP == 'Yes'){echo " selected "; }?> >Yes</option>
														<option value="No" <?php if($semstud->IP == 'No'){echo " selected "; }?> >No</option>
													</select>
												</div>
											</div>
											<div class="col-lg-4">
												<div class="form-group">
													<label>4Ps Benefeciary?</label>
													<select name="FourPs" id="yearlevel" class="form-control" required>
														<option value="No Data" <?php if($semstud->FourPs == 'No Data'){echo " selected "; }?> >No Data</option>
														<option value="Yes" <?php if($semstud->FourPs == 'Yes'){echo " selected "; }?> >Yes</option>
														<option value="No" <?php if($semstud->FourPs == 'No'){echo " selected "; }?> >No</option>
													</select>
												</div>
											</div>
										</div>

										<div class="row">
											<div class="col-lg-4">
												<div class="form-group">
													<label>Repeater?</label>
													<select name="Repeater" class="form-control" required>
														<option value="No Data" <?php if($semstud->BalikAral == 'No Data'){echo " selected "; }?> >No Data</option>
														<option value="Yes" <?php if($semstud->BalikAral == 'Yes'){echo " selected "; }?> >Yes</option>
														<option value="No" <?php if($semstud->BalikAral == 'No'){echo " selected "; }?> >No</option>
													</select>
												</div>
											</div>
											<div class="col-lg-4">
												<div class="form-group">
													<label>Transferee?</label>
													<select name="Transferee" class="form-control" required>
														<option value="No Data" <?php if($semstud->Transferee == 'No Data'){echo " selected "; }?> >No Data</option>
														<option value="Yes" <?php if($semstud->Transferee == 'Yes'){echo " selected "; }?> >Yes</option>
														<option value="No" <?php if($semstud->Transferee == 'No'){echo " selected "; }?> >No</option>
													</select>
												</div>
											</div>

											<div class="col-lg-4">
												<div class="form-group">
													<label>School Year</label>
													<input type="text" class="form-control" value="<?= $semstud->SY; ?>" readonly name="SY" required />
												</div>
											</div>
										</div>

										<div class="row">
											<div class="col-lg-4">
												<div class="form-group">
													<label>Dewormed?</label>
													<select name="dewormStat" class="form-control" required>
														<option value="0" <?php if($semstud->dewormStat == 0){echo " selected "; }?> >No Data</option>
														<option value="1" <?php if($semstud->dewormStat == 1){echo " selected "; }?> >Yes</option>
														<option value="2" <?php if($semstud->dewormStat == 2){echo " selected "; }?> >No</option>
													</select>
												</div>
											</div>
											<div class="col-lg-4">
												<div class="form-group">
													<label>Parent's consent for milk?</label>
													<select name="pc_for_milk" class="form-control" required>
														<option value="0" <?php if($semstud->pc_for_milk == 0){echo " selected "; }?> >No Data</option>
														<option value="1" <?php if($semstud->pc_for_milk == 1){echo " selected "; }?> >Yes</option>
														<option value="2" <?php if($semstud->pc_for_milk == 2){echo " selected "; }?> >No</option>
													</select>
												</div>
											</div>

											<div class="col-lg-4">
												<div class="form-group">
													<label>Beneficiary of SBFP in Previous Years?</label>
													<select name="sbfp_ben_prevyear" class="form-control" required>
														<option value="0" <?php if($semstud->sbfp_ben_prevyear == 0){echo " selected "; }?> >No Data</option>
														<option value="1" <?php if($semstud->sbfp_ben_prevyear == 1){echo " selected "; }?> >Yes</option>
														<option value="2" <?php if($semstud->sbfp_ben_prevyear == 2){echo " selected "; }?> >No</option>
													</select>
												</div>
											</div>
										</div>

										<!-- <p style="color:green"><b>Note:  Leave the Semester empty for Elementary and Junior High School.  The SY is required and it depends on the options you chose from the login form.</b></p> -->
										<div class="row">
											<div class="col-lg-12">
												<input type="submit" name="submit" class="btn btn-info" value="Update Enrollment"> </span>
											</div>
										</div>
									</div>
							</div><!-- /.box -->
						</div>
					</div>

					</form>

				</div>
			</div>
		</div>

		<!-- end container-fluid -->

	</div>
	<!-- end content -->



	<!-- Footer Start -->
	<?php include('includes/footer.php'); ?>
	<!-- end Footer -->

	</div>

	<!-- ============================================================== -->
	<!-- End Page content -->
	<!-- ============================================================== -->

	<script type="text/javascript">
// When Department dropdown changes
$('#department').change(function(){
    var department = $(this).val();
    if(department != ''){
        $.ajax({
            url: "<?= base_url('sbfp/fetch_yearlevel'); ?>",
            method: "POST",
            data: {course: department},
            success: function(data){
                $('#yearlevel').html(data);
                $('#section').html('<option value="">Select Year Level first</option>'); // Reset section too
            }
        });
    }
});


$('#yearlevel').change(function(){
    var yearlevel = $(this).val();
    if(yearlevel != ''){
        $.ajax({
            url: "<?= base_url('sbfp/fetch_section'); ?>",
            method: "POST",
            data: {yearlevel: yearlevel},
            success: function(data){
                $('#section').html(data);
            }
        });
    }


        $(document).ready(function() {
    $('#section').change(function() {
        var section = $('#section').val();
        var yearlevel = $('#yearlevel').val(); // Get the selected year level

        if (section !== '' && yearlevel !== '') { // Ensure both values are present
            // Fetch Adviser ID
            $.ajax({
                url: "<?php echo base_url(); ?>sbfp/fetch_adviser_id",
                method: "POST",
                data: { section: section, yearlevel: yearlevel }, // Send both params
                success: function(data) {
                    console.log("Adviser ID Response:", data); // Debug log
                    $('#AdviserID').val(data.trim()); // Populate Adviser ID
                }
            });

            // Fetch Adviser Name
            $.ajax({
                url: "<?php echo base_url(); ?>sbfp/fetch_adviser_name",
                method: "POST",
                data: { section: section, yearlevel: yearlevel }, // Send both params
                success: function(data) {
                    console.log("Adviser Name Response:", data); // Debug log
                    $('#AdviserName').val(data.trim()); // Populate Adviser Name
                }
            });
        } else {
            $('#AdviserID').val(''); // Clear Adviser ID field
            $('#AdviserName').val(''); // Clear Adviser Name field
        }
    });
});

        $('#track').change(function() {
            var track = $(this).val(); // Get the selected track value
            if (track != '') {
                // Clear the Strand dropdown
                $('#strand').html('<option value="">Select Strand</option>'); // Reset to default option

                $.ajax({
                    url: "<?php echo base_url(); ?>Settings/fetchStrand", // Make sure this URL is correct
                    method: "POST",
                    data: { track: track },
                    success: function(data) {
                        $('#strand').html(data); // Populate strands based on the response
                    },
                    error: function() {
                        // Handle any errors that occur during the AJAX request
                        $('#strand').html('<option value="">Error loading strands</option>');
                    }
                });
            } else {
                // Reset Strand dropdown if no Track is selected
                $('#strand').html('<option value="">Select Strand</option>');
            }
        });
    });
</script>
