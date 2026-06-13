
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

                                        <?= form_open('plantilla_add'); ?>
									<div class="row">
                                  
										<div class="col-lg-7">
												<div class="form-group">
													<label for="lastName">Plantilla Group <span class="ren_r">*</span></label>
													<input type="text" class="form-control" list="pGroup" name="pGroup" required value="">
													<datalist id="pGroup">
														<?php foreach($pgroup as $row){ 
														echo '<option value="'. $row['pGroup'] .'">';
													  } ?>	
													</datalist>
												</div>	
										</div>
                                        
                                    </div>	
									<div class="row">
                                        <div class="col-lg-7">
												<div class="form-group">
													<label for="lastName">Item No.<span class="ren_r">*</span></label>
													<input type="text" class="form-control" list="itemNo" name="itemNo" required value="<?= set_value('itemNo'); ?>">
													<datalist id="itemNo">
														<?php foreach($itemNo as $row){ 
														echo '<option value="'. $row['itemNo'] .'">';
													  } ?>	
													</datalist>
												</div>	
										</div>
                                        
                                    </div>	

                                    <div class="row">
                                    <div class="col-lg-4">
												<div class="form-group">
													<label for="lastName">Item Position<span class="ren_r">*</span></label>
													<input type="text" class="form-control" list="itemPosition" name="itemPosition" required value="<?= set_value('itemPosition'); ?>">
													<datalist id="itemPosition">
														<?php foreach($itemPosition as $row){ 
														echo '<option value="'. $row['itemPosition'] .'">';
													  } ?>	
													</datalist>
												</div>	
										</div>
                                    <div class="col-lg-3">
											<div class="form-group">
											<label>Auth Annual Salary</label>
											 <input type="text" class="form-control" name="authAnnualSalary" value="" >
											</div>
										</div>
										
                                    </div>
									<div class="row">
									<div class="col-lg-4">
												<div class="form-group">
												<label>SG No.</label>
												<select class="form-control" name="sg">
													<option></option>
													<?php
														for ($x = 1; $x <= 33; $x+=1) {
															echo "<option value=".$x.">SG $x</option>";
														}
														?>													
												</select>
												</div>
											</div>
                                            <div class="col-lg-3">
												<div class="form-group">
												<label>Step No.</label>
												<select class="form-control" name="step">
													<option></option>
													<?php
														for ($x = 1; $x <= 8; $x+=1) {
														echo "<option value=".$x.">$x</option>";
														}
														?>											
												</select>
												</div>
											</div>
									</div>
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

                

               