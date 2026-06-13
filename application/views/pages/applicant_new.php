
        <!-- ============================================================== -->
        <!-- Start Page Content here -->
        <!-- ============================================================== -->

        

        <div class="content-page" style="margin-left:0">
            <div class="content">

                <!-- Start Content-->
                <div class="container-fluid">

                    <!-- start page title -->
                    <div class="row">
                        <div class="col-12">
                            <div class="page-title-box">
                                <h4 class="page-title">APPLICANT'S PROFILE</h4>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                    </div>
                    

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
                    <!-- end page title -->

                    <?= validation_errors(); ?>
                    <?php $att = array('class' => 'parsley-examples'); ?>
                    <?= form_open('Pages/new_applicant', array_merge($att, ['onsubmit' => 'validateCaptcha(event)'])); ?>



                    <div class="row">

                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-header">
                                    DECLARATION AND ATTESTATION:
                                </div>
                                <div class="card-body">
                                    <blockquote class="card-bodyquote">
                                        <p class="text-justify">DepEd <?php echo $data1[0]->division; ?> abides and strictly enforces the provisions of RA 10173 or the Data Privacy Act of 2012. By selecting the checkbox below and clicking "submit" you give your consent to the following: 1. Allow DepEd <?php echo $data1[0]->division; ?> to collect, process and keep your personal information for lawful purposes; 2. DepEd <?php echo $data1[0]->division; ?> cannot disclose your personal information to any third party. It can, however, share said information with its functional divisions, sections, units, and schools for employment purposes. You confirm that you are well-informed of the purposes of this effort and have agreed to the above-cited information.</p>
                                        <footer class="text-xs"> <input type="checkbox" required name="confirm" value="1"> &nbsp;<cite class="text-danger">confirm that I have read and understood the above information and agree to its every detail.</cite>
                                        </footer>
                                    </blockquote>
                                </div>
                            </div>
                        </div>


                    </div>
                    <!-- end row -->

                    <!-- Form row -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-body">

                                    <br />
                                    <input type="hidden" valu="" name="renren">
                                    <input type="hidden" valu="" name="ivykate">
                                    <input type="hidden" valu="" name="ivankyle">
                                    <input type="hidden" valu="" name="ic">

                                    <input type="hidden" value="<?= date('Y');  ?>00<?= $count->number + 1; ?>" name="record_no" class="form-control">
                                    <input type="hidden" value="<?= $count->number + 1; ?>" name="number" class="form-control">
                                    <div class="form-row">
                                        <div class="form-group col-md-1">
                                            <label for="inputname" class="col-form-label">Prefix</label>
                                            <select name="prefix" required class="form-control">
                                                <option></option>
                                                <?php
                                                $array = array('Mr.', 'Ms.', 'Mrs.');
                                                foreach ($array as $ar) {
                                                    echo '<option>' . $ar . '</option>';
                                                }
                                                ?>
                                            </select>
                                        </div>

                                        <div class="form-group col-md-4">
                                            <label for="inputname" class="col-form-label">First Name</label>
                                            <input type="text" required value="<?= set_value('fname'); ?>" name="fname" class="form-control">
                                        </div>

                                        <div class="form-group col-md-3">
                                            <label for="inputname" class="col-form-label">Middle Name</label>
                                            <input type="text" value="<?= set_value('mname'); ?>" name="mname" class="form-control">
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label for="inputname" class="col-form-label">Last Name</label>
                                            <input type="text" required value="<?= set_value('lname'); ?>" name="lname" class="form-control">
                                        </div>
                                        <div class="form-group col-md-1">
                                            <label for="inputname" class="col-form-label">EXTENSION</label>
                                            <input type="text" value="<?= set_value('ext'); ?>" name="ext" class="form-control" placeholder="Jr, Sr">
                                        </div>
                                    </div>

                                    <div class="form-row">

                                        <div class="form-group col-md-5">
                                            <label for="inputname" class="col-form-label">Email Address</label>
                                            <input type="email" required value="<?= set_value('email'); ?>" name="email" class="form-control">
                                        </div>

                                        <div class="form-group col-md-3">
                                            <label for="inputname" class="col-form-label">Contact Number</label>
                                            <input type="text" required value="<?= set_value('contact'); ?>" name="contact" class="form-control">
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label for="inputname" class="col-form-label">Birth Date</label>
                                            <input type="date" required id="bday" onchange="submitBday()" value="<?= set_value('bd'); ?>" name="bd" class="form-control">
                                        </div>
                                        <div class="form-group col-md-1">
                                            <label for="inputname" class="col-form-label">Age</label>
                                            <input type="text" readonly id="resultBday" value="<?= set_value('age'); ?>" name="age" class="form-control">
                                        </div>
                                    </div>



                                    <script src="<?= base_url(); ?>assets/js/jquery-3.6.0.min.js"></script>

                                    <script>
                                        $(document).ready(function() {
                                            // When the Province dropdown changes
                                            $('#Province').change(function() {
                                                var province = $(this).val();
                                                if (province != '') {
                                                    $.ajax({
                                                        url: "<?= base_url('Pages/get_cities_by_province'); ?>",
                                                        method: "POST",
                                                        data: {
                                                            province: province
                                                        },
                                                        success: function(data) {
                                                            $('#mun_city').html(data);
                                                            $('#barangay').html('<option value="">Select Barangay</option>'); // Reset barangay dropdown
                                                        }
                                                    });
                                                } else {
                                                    $('#mun_city').html('<option value="">Select City/Municipality</option>');
                                                    $('#barangay').html('<option value="">Select Barangay</option>');
                                                }
                                            });

                                            // When the City dropdown changes
                                            $('#mun_city').change(function() {
                                                var city = $(this).val();
                                                if (city != '') {
                                                    $.ajax({
                                                        url: "<?= base_url('Pages/get_barangays_by_city'); ?>",
                                                        method: "POST",
                                                        data: {
                                                            city: city
                                                        },
                                                        success: function(data) {
                                                            $('#barangay').html(data);
                                                        }
                                                    });
                                                } else {
                                                    $('#barangay').html('<option value="">Select Barangay</option>');
                                                }
                                            });
                                        });
                                    </script>


                                    <div class="row">
                                        <div class="col-lg-3">
                                            <div class="form-group">
                                                <label for="province">Province</label>
                                                <select id="Province" name="province" class="form-control" required>
                                                    <option value="">Select Province</option>
                                                    <?php foreach ($provinces as $row): ?>
                                                        <option value="<?= $row->Province; ?>"><?= $row->Province; ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-lg-3">
                                            <div class="form-group">
                                                <label for="city">City/Municipality</label>
                                                <select id="mun_city" name="mun_city" class="form-control" required>
                                                    <option value="">Select City/Municipality</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-lg-3">
                                            <div class="form-group">
                                                <label for="barangay">Barangay</label>
                                                <select id="barangay" name="barangay" class="form-control" required>
                                                    <option value="">Select Barangay</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-lg-3">
                                            <div class="form-group">
                                                <label for="sitio">Purok</label>
                                                <input type="text" id="sitio" class="form-control" name="purok" placeholder="Purok">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
										
										<div class="col-lg-3">
												<div class="form-group">
													<label>Religion</label>
													 <input list="religion" id="browser" name="religion" class="form-control" required value="">
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
																<option value="<?= $key; ?>"><?= $ar; ?></option>
                                                            <?php } ?>
													 </select>
													 
												</div>
										</div>
										<div class="col-lg-3">
											<div class="form-group">
											<label>Ethnic Group</label>
											<input list="ethnicity" id="browser" name="ethnicity" class="form-control" required value="">
											<datalist id="ethnicity">
												<?php foreach($et as $row){ ?>
													<option value="<?= $row->ethnicity; ?>">
												<?php } ?>
											</datalist>		 
											</div>
										</div>
									</div>



                                    <!-- <div class="form-row">

                                        <div class="form-group col-md-6">
                                            <label for="lastName">Specialization : <span style="font-style:italic; color:red; font-weight:300; font-size:10px"> "Note: required for JHS and SHS"</span></label>
                                            <input type="text" value="<?= set_value('specialization'); ?>" name="specialization" class="form-control">
                                            </select>
                                        </div>

                                        <div class="form-group col-md-6">
                                            <label for="lastName">Track : <span style="font-style:italic; color:red; font-weight:300; font-size:10px"> "Note: required for Senior High School Only"</span></label>
                                            <select name="track" class="form-control">
                                                <option></option>
                                                <?php
                                                $array = array('Academic Track', 'Technical Vocational Livelihood Track', 'Arts and Design', 'Sports');
                                                foreach ($array as $ar) {
                                                    echo '<option>' . $ar . '</option>';
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div> -->

                                    <div class="form-group">
                                        <div class="g-recaptcha" data-sitekey="<?= trim($mis_settings->site_key); ?>"></div>
                                    </div>


                                    

                                    <br />
                                    <button type="submit" value="submit" class="btn btn-primary"><?= $title; ?></button>

                                    </form>
                                    <br />
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- end row -->




                    <!-- end container-fluid -->

                </div>
                <!-- end content -->
                 <script src="https://www.google.com/recaptcha/api.js" async defer></script>
                 <script>
                    function validateCaptcha(event) {
                        var response = grecaptcha.getResponse();
                        if (response.length == 0) {
                            alert("Please verify that you are not a robot.");
                            event.preventDefault(); // prevent form submission
                            return false;
                        }
                    }
                </script>