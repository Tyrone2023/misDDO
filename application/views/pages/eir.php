
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

                                        <?= form_open('Pages/ier_insert_info'); ?>
									<div class="row">
                                  
										<div class="col-lg-3">
												<div class="form-group">
													<label for="lastName">Civil Status</label>
													<select name="MaritalStatus" class="form-control" required>
                                                        <option></option>
                                                        <option <?php if($data->ms == 'Single'){echo " selected "; }?>>Single</option>
														<option <?php if($data->ms == 'Married'){echo " selected "; }?>>Married</option>
														<option <?php if($data->ms == 'Divorced'){echo " selected "; }?>>Divorced</option>
														<option <?php if($data->ms == 'Separated'){echo " selected "; }?>>Separated</option>
														<option <?php if($data->ms == 'Widowed'){echo " selected "; }?>>Widowed</option>
													</select>
												</div>	
										</div>

										<div class="col-lg-2">
												<div class="form-group">
													<label for="lastName">Disability</label>
													<select class="form-control" name="disability" required>
                                                        <option value="">Select</option>
                                                        <option  <?php if($data->disability == '1'){echo " selected "; }?> value="1">No</option>
                                                        <option  <?php if($data->disability == '2'){echo " selected "; }?> value="2">Yes</option>
                                                    </select>
												</div>	
										</div>

										<div class="col-lg-2">
												<div class="form-group">
													<label for="lastName">Ethnic Group</label>
													<input type="text" class="form-control" name="etchnic" required value="<?= $data->ethnicity; ?>">
													
												</div>	
										</div>
                                        
                                    </div>	

									<div class="row">
                                    <div class="col-lg-7">
												<div class="form-group">
													<label for="lastName">Religion</label>
													<input type="text" class="form-control"  name="religion" required value="<?= $data->religion; ?>">
													
												</div>	
										</div>
										
                                    </div>

									
									<?php if($this->uri->segment(4) === 'hris_staff'){?>
                                    <div class="row">
									

                                    <div class="col-lg-4">
												<div class="form-group">
													<label for="lastName">Email Address</label>
													<input type="text" class="form-control"  name="empEmail" required value="<?= $data->Email; ?>">
													
												</div>	
										</div>
                                    <div class="col-lg-3">
											<div class="form-group">
											<label>Contact No.</label>
											 <input type="text" class="form-control" name="empMobile" value="<?= $data->contact; ?>" >
											</div>
										</div>
										
                                    </div>

									<?php } ?>

									<div class="row">
									<div class="col-lg-7">
												<div class="form-group">
												<label>Education</label>
												<input type="text" class="form-control" name="bd" value="<?= $data->edu; ?>" >
												</div>
											</div>
                                            
									</div>

									<div class="row">
											<div class="col-lg-4">
												<div class="form-group">
												<label>Training</label>
												<input type="text" class="form-control" name="ttitle" value="" placeholder="Title">
												</div>
											</div>
											<div class="col-lg-3">
												<div class="form-group">
												<label>&nbsp;</label>
												<input type="text" class="form-control" name="thours" value="" placeholder="Hours">
												</div>
											</div>
                                            
									</div>

									<div class="row">
											<div class="col-lg-3">
												<div class="form-group">
												<label>Experience</label>
												<input type="text" class="form-control" name="edetails" value="" placeholder="Details">
												</div>
											</div>
											<div class="col-lg-2">
												<div class="form-group">
												<label>&nbsp;</label>
												<input type="text" class="form-control" name="eyears" value="" placeholder="Years">
												</div>
											</div>
											<div class="col-lg-2">
												<div class="form-group">
												<label>&nbsp;</label>
												<input type="text" class="form-control" name="eeligibility" value="" placeholder="Eligibility">
												</div>
											</div>
                                            
									</div>

									<input type="hidden" name="table" value="<?= $this->uri->segment('4'); ?>">
									<input type="hidden" name="code" value="<?= $this->uri->segment('5'); ?>">
									<input type="hidden" name="jobID" value="<?= $this->uri->segment('3'); ?>">



									<div class="row">
										<div class="col-lg-12">
											<input type="submit" name="submit" class="btn btn-info" value="Save">
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

                

               