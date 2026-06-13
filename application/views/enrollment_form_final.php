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
                                    <h4 class="page-title">Enrollment Form</h4>
									<?php echo $this->session->flashdata('msg'); ?>
                                   <div class="page-title-right">
                                        <ol class="breadcrumb p-0 m-0">
                                            <li class="breadcrumb-item"><a href="#"><span class="badge badge-purple mb-3">Currently login to <b>SY <?php echo $this->session->userdata('sy');?> <?php echo $this->session->userdata('semester');?></span></b></a></li>
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
										<div class="col-lg-4">
												<div class="form-group">
													<label>Student No.<span style="color:red">*</span></label>
													<input type="text" class="form-control" name="StudentNumber" value="<?php echo $_GET['id'];?>" readonly required >
													<input type="hidden" name="schoolID" value="<?php echo $this->session->userdata('username'); ?>" readonly>
													<input type="hidden" name="StudeStatus" value="Enrolled" readonly>
													</div>	
										</div>		
									</div>
									
									<div class="row">
										<div class="col-lg-4">
												<div class="form-group">
													<label>First Name <span style="color:red">*</span></label>
													<input type="text" class="form-control" name="FName" value="<?php echo $_GET['FName'];?>" readonly required >
												</div>	
										</div>	
										<div class="col-lg-4">
											<div class="form-group">
											<label>Middle Name <span style="color:red">*</span></label>
											 <input type="text" class="form-control" name="MName" value="<?php echo $_GET['MName'];?>" readonly required >
											</div>
										</div>  
										<div class="col-lg-4">
											<div class="form-group">
											<label>Last Name <span style="color:red">*</span></label>
											 <input type="text" class="form-control" name="LName" value="<?php echo $_GET['LName'];?>" readonly required >
                                        </div>
										</div>
									</div>	
									
									<div class="row">
										<div class="col-lg-4">
												<div class="form-group">
													<label>Department <span style="color:red">*</span></label>
														<select name="Course" id="course" class="form-control" required >
															 <option value="<?php echo $_GET['Course'];?>"><?php echo $_GET['Course'];?></option>
															 <?php
																foreach ($course as $row) {
																echo '<option value="' . $row->CourseDescription . '">' . $row->CourseDescription . '</option>';
																}
																?>
															</select>
														</div>
										</div>	
										<div class="col-lg-4">
												<div class="form-group">
													<label>Grade Level <span style="color:red">*</span></label>
													<select name="YearLevel" id="yearlevel" class="form-control" required>
													<option value="<?php echo $_GET['YearLevel'];?>"><?php echo $_GET['YearLevel'];?></option>
													</select>
												</div>
										</div>  
										<div class="col-lg-4">
												<div class="form-group">
													<label>Section </label>
													 <input type="text" class="form-control" name="Section" value=""  >
												</div>
										</div>
									</div>

									<div class="row">
										<div class="col-lg-4">
												<div class="form-group">
													<label>Status <span style="color:red">*</span></label>
														<select name="Status" id="yearlevel" class="form-control" required>
															<option value="">Select</option>
															<option>Old</option>
															<option>New</option>
														</select>
												</div>	
										</div>	
										<div class="col-lg-4">
												<div class="form-group">
													<label>Track</label>
													<select name="Track" id="Track" class="form-control select2">
                                <option value="">Select Track</option>
                                <?php foreach ($track as $level) { ?>
                                    <option value="<?= $level->track; ?>"><?=$level->track; ?></option>
                                <?php } ?>
                            </select>  
												
													 
												</div>
										</div>  
										<div class="col-lg-4">
												<div class="form-group">
													<label>Strand</label>
													<select name="Qualification" id="Qualification" class="form-control select2">
                                <option value="">Select Strand</option>
                                <?php foreach ($strand as $level) { ?>
                                    <option value="<?= $level->strand; ?>"><?=$level->strand; ?></option>
                                <?php } ?>
                            </select>  
												</div>
										</div>
									</div>

									<div class="row">
										<div class="col-lg-4">
												<div class="form-group">
													<label>Balik Aral? <span style="color:red">*</span></label>
														<select name="BalikAral" id="yearlevel" class="form-control" required>
															<option value="">Select</option>
															 <option value="No Data">No Data</option>
															<option value="Yes">Yes</option>
															<option value="No">No</option>
														</select>
												</div>	
										</div>	
										<div class="col-lg-4">
												<div class="form-group">
													<label>Indigenous People Member? <span style="color:red">*</span></label>
													<select name="IP" id="yearlevel" class="form-control" required>
														<option value="">Select</option>
														<option value="No Data">No Data</option>
														<option value="Yes">Yes</option>
													   <option value="No">No</option>
													</select>
												</div>
										</div>  
										<div class="col-lg-4">
												<div class="form-group">
													<label>4Ps Benefeciary? <span style="color:red">*</span></label>
													 <select name="FourPs" id="yearlevel" class="form-control" required>
														<option value="">Select</option>
														<option value="No Data">No Data</option>
														<option value="Yes">Yes</option>
													   <option value="No">No</option>
													</select>
												</div>
										</div>
									</div>									

									<div class="row">
										<div class="col-lg-4">
												<div class="form-group">
													<label>Repeater? <span style="color:red">*</span></label>
														<select name="Repeater" class="form-control" required>
															<option value="">Select</option>
															 <option value="No Data">No Data</option>
															<option value="Yes">Yes</option>
															<option value="No">No</option>
														</select>
												</div>	
										</div>	
										<div class="col-lg-4">
												<div class="form-group">
													<label>Transferee? <span style="color:red">*</span></label>
													<select name="Transferee" class="form-control" required>
														<option value="">Select</option>
														<option value="No Data">No Data</option>
														<option value="Yes">Yes</option>
													   <option value="No">No</option>
													</select>
												</div>
										</div>  
										
										<div class="col-lg-2">
												<div class="form-group">
													<label>Semester</label>
													<select name="Semester" class="form-control">
														<option value="<?php echo $_GET['sem'];?>"><?php echo $_GET['sem'];?></option>
														<option value=""></option>
														<option value="1st Sem.">1st Sem.</option>
														<option value="2nd Sem.">2nd Sem.</option>
														<option value="Summer">Summer</option>
													</select>
												</div>
										</div>	
										<div class="col-lg-2">
												<div class="form-group">
													<label>School Year <span style="color:red">*</span></label>
													<input type="text" class="form-control" value="<?php echo $this->session->userdata('sy');?>" name="SY" required />
												</div>
										</div>  
									</div>

										<p style="color:green"><b>Note:  Leave the Semester empty for Elementary and Junior High School.  The SY is required and it depends on the options you chose from the login form.</b></p>
									<div class="row">
										<div class="col-lg-12">
											<input type="submit" name="submit" class="btn btn-info" value="Process Enrollment"> </span>
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

        </div>
        <!-- END wrapper -->

        
       
        

	<script src="<?= base_url(); ?>assets/js/jquery-3.6.0.min.js"></script>
        
<script type="text/javascript">
    $(document).ready(function() {
        $('#course').change(function() {
            var course = $('#course').val();
            if (course != '') {
                $.ajax({
                    url: "<?php echo base_url(); ?>Page/fetch_yearlevel",
                    method: "POST",
                    data: {
                        course: course
                    },
                    success: function(data) {
                        $('#yearlevel').html(data);
                    }
                })
            }
        });
    });
    $(document).ready(function() {
        $('#track').change(function() {
            var track = $('#track').val();
            if (track != '') {
                $.ajax({
                    url: "<?php echo base_url(); ?>Page/fetchStrand",
                    method: "POST",
                    data: {
                        track: track
                    },
                    success: function(data) {
                        $('#strand').html(data);
                    }
                })
            }
        });
    });
</script>