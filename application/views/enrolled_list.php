            <?php include('templates/head.php'); ?>
            <?php include('templates/header.php'); ?>

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
                                        <button type="button" class="btn btn-primary waves-effect waves-light" data-toggle="modal" data-target=".bs-example-modal-lg">
                                            <i class="fas fa-user-plus mr-1"></i> Enroll New
                                        </button>
                                        <br /><br />
                                   
                                    <h4 class="page-title">List of Enrolled Students<br /><span class="badge badge-success">SY:</span>
                                    <a href="#" class="badge badge-primary" data-toggle="modal" data-target="#myModal"><?= !empty($csy->fy) ? $csy->fy : $csy; ?></a>
                                    </h4>

                                    

                                    <?php if($this->session->flashdata('success')) : ?>

                                        <?= '<div class="alert alert-success alert-dismissible fade show" role="alert">
                                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>'
                                                .$this->session->flashdata('success'). 
                                            '</div>'; 
                                        ?>
                                        <?php endif; ?>

                                        <?php if($this->session->flashdata('danger')) : ?>
                                        <?= '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>'
                                                .$this->session->flashdata('danger'). 
                                            '</div>'; 
                                        ?>
                                    <?php endif;  ?>
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                        </div>

                        <!-- end page title -->
                        <div class="row">
                            <div class="col-md-12">

                                <div class="card">
                                    <div class="card-body table-responsive">
                                        <!-- <h4 class="m-t-0 header-title mb-4">List of Enrolled Students </h4> -->
                                        
                                        
                                        <?php echo $this->session->flashdata('msg'); ?>
                                        <form method="get" action="<?= base_url('Masterlist/enrolledList'); ?>" class="mb-3">
    <input type="hidden" name="sy" value="<?= $this->input->get('sy'); ?>">

    <div class="form-row">
        <div class="col-md-3">
            <select name="yearlevel" class="form-control">
                <option value="">Select Year Level</option>
                <?php foreach ($yearlevels as $yl) { ?>
                    <option value="<?= $yl->YearLevel; ?>" <?= $this->input->get('yearlevel') == $yl->YearLevel ? 'selected' : ''; ?>>
                        <?= $yl->YearLevel; ?>
                    </option>
                <?php } ?>
            </select>
        </div>
        <div class="col-md-3">
            <select name="section" class="form-control">
                <option value="">Select Section</option>
                <?php foreach ($sections as $sec) { ?>
                    <option value="<?= $sec->Section; ?>" <?= $this->input->get('section') == $sec->Section ? 'selected' : ''; ?>>
                        <?= $sec->Section; ?>
                    </option>
                <?php } ?>
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary">Filter</button>
            <a href="<?= base_url('Masterlist/enrolledList?sy=' . $this->input->get('sy')); ?>" class="btn btn-secondary">Reset</a>
        </div>
    </div>
</form>


                                        <!-- <table id="datatable-buttons" class="table table-striped table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;"> -->
                                        <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                            <thead>
                                                <tr>
                                                    <th>Student Name</th>
                                                    <th>Student No.</th>
                                                    <!-- <th>Course</th> -->
                                                    <th>Year Level</th>
                                                    <th>Section</th>
                                                    <th style="text-align:center">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            <?php foreach ($data as $row) { ?>
                                                <tr>
                                                <td><?= $row->FirstName; ?> <?= $row->MiddleName; ?> <?= $row->LastName; ?></td>
                                                <td><?= $row->StudentNumber; ?></td>
                                                <td><?= $row->YearLevel; ?></td>
                                                <td><?= $row->Section; ?></td>
                                                <td>
                                                    <a href="<?= base_url(); ?>Sbfp/sbfp_enrollment_update/<?= $row->semstudentid;?>" class="btn btn-success btn-sm">Update</a>
                                                    <a href="<?= base_url(); ?>Sbfp/sbfp_enrollment_delete/<?= $row->semstudentid;?>/<?= $this->input->get('sy'); ?>" onclick="return confirm('Are you sure you want to delete this item?')" class="btn btn-danger btn-sm">Delete</a>
                                                </td>
                                                </tr>
                                                <?php } ?>
                                            </tbody>

                                        </table>

                                    </div>
                                </div>
                            </div>
                        </div>

                        <?php if(!empty($sy)){ ?>
                        <?php 
                            $balik_aral = $this->Common->column_count_three_cond('semesterstude','BalikAral','Yes','schoolID', $this->session->username,'SY', $sy);
                            $transferee = $this->Common->column_count_three_cond('semesterstude','Transferee','Yes','schoolID', $this->session->username,'SY', $sy);
                            $repeater = $this->Common->column_count_three_cond('semesterstude','Repeater','Yes','schoolID', $this->session->username,'SY', $sy);
                            $ip = $this->Common->column_count_three_cond('semesterstude','IP','Yes','schoolID', $this->session->username,'SY', $sy);
                            $FourPs = $this->Common->column_count_three_cond('semesterstude','FourPs','Yes','schoolID', $this->session->username,'SY', $sy);
                            $sr = $this->Common->count_students_by_year_and_sy('SY',$sy,'schoolID',$this->session->username);
                        ?>
                        <!-- end page title -->
                        <div class="row">
                            <div class="col-md-12">

                                <div class="card">
                                    <div class="card-body table-responsive">
                                        <h4 class="m-t-0 header-title mb-4">Summary</h4>
                                       
                                        <?php echo $this->session->flashdata('msg'); ?>
                                        <!-- <table id="datatable-buttons" class="table table-striped table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;"> -->
                                        <table class="table mb-0">                                            <thead>
                                                <tr>
                                                    <th></th>
                                                    <th>Counts</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                 <td>Transferees</td> 
                                                 <td><?= $transferee; ?></td>
                                                 <td><a href="<?= base_url(); ?>Sbfp/enrolledList_count_gl?sy=<?= $sy; ?>&col=Transferee&title=Transferees" class="btn btn-primary btn-sm">View</a></td>  
                                                </tr>
                                                <tr>
                                                 <td>Repeaters</td> 
                                                 <td><?= $repeater; ?></td>
                                                 <td><a href="<?= base_url(); ?>Sbfp/enrolledList_count_gl?sy=<?= $sy; ?>&col=Repeater&title=Repeaters" class="btn btn-primary btn-sm">View</a></td>  
                                                </tr>
                                                <tr>
                                                 <td>Balik-Aral</td> 
                                                 <td><?= $balik_aral; ?></td>
                                                 <td><a href="<?= base_url(); ?>Sbfp/enrolledList_count_gl?sy=<?= $sy; ?>&col=BalikAral&title=Balik-Aral" class="btn btn-primary btn-sm">View</a></td>  
                                                </tr>
                                                <tr>
                                                 <td>IPs</td> 
                                                 <td><?= $ip; ?></td>
                                                 <td><a href="<?= base_url(); ?>Sbfp/enrolledList_count_gl?sy=<?= $sy; ?>&col=IP&title=IPs" class="btn btn-primary btn-sm">View</a></td>  
                                                </tr>
                                                <tr>
                                                 <td>4Ps Beneficiaries</td> 
                                                 <td><?= $FourPs; ?></td>
                                                 <td><a href="<?= base_url(); ?>Sbfp/enrolledList_count_gl?sy=<?= $sy; ?>&col=FourPs&title=4Ps Beneficiaries" class="btn btn-primary btn-sm">View</a></td>  
                                                </tr>
                                            </tbody>

                                        </table>

                                    </div>
                                </div>
                            </div>
                        </div>
                        

                        <!-- end page title -->
                        <div class="row">
                            <div class="col-md-12">

                                <div class="card">
                                    <div class="card-body table-responsive">
                                        <h4 class="m-t-0 header-title mb-4">Enrollment Summary</h4>
                                       
                                        <?php echo $this->session->flashdata('msg'); ?>
                                        <!-- <table id="datatable-buttons" class="table table-striped table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;"> -->
                                        <table class="table mb-0">                                            <thead>
                                                <tr>
                                                    <th>Grade Level</th>
                                                    <th>Counts</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach($sr as $row){?>
                                                <tr>
                                                    <td><?= $row->YearLevel; ?></td>
                                                    <td><?= $row->total_students; ?></td>
                                                    <td><a href="<?= base_url(); ?>Sbfp/enrollment_summary?gl=<?= $row->YearLevel; ?>&sy=<?= $sy; ?>" class="btn btn-primary btn-sm">View</a></td>  
                                                </tr>
                                                <?php } ?>
                                            </tbody>

                                        </table>

                                    </div>
                                </div>
                            </div>
                        </div>

                        <?php } ?>




                        
                    </div>
                    <!-- end container-fluid -->

                </div>
                <!-- end content -->

                <?php include('templates/footer.php'); ?>

                <!--  Modal content for the above example -->
                <div class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" style="display: none;" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="myLargeModalLabel">Enrollment Form</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                            </div>
                            <div class="modal-body">


                                <?php $att = array('class' => 'parsley-examples'); ?>
                                <?= form_open('Sbfp/enroll', $att); ?>
                                <!-- general form elements -->
                                <div class="card-body">
                                    <input type="hidden" name="school_year" value="<?= $csy->fy; ?>">


                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="form-group">
                                                <label>Student Name</label>
                                                <select class="form-control" data-toggle="select2" name="StudentNumber" required>
                                                    <option>Select</option>
                                                    <?php foreach ($stud as $row) { 
                                                        $check = $this->Common->two_cond_count_row('semesterstude', 'StudentNumber', $row->StudentNumber, 'SY', $sy);
                                                        if($check->num_rows() == 0){
                                                    ?>
                                                        <option value="<?= $row->StudentNumber; ?>"><?= $row->LastName; ?>, <?= $row->FirstName; ?> <?= $row->MiddleName; ?> </option>
                                                    <?php } }  ?>
                                                </select>
                                            </div>
                                        </div>

                                    </div>

                                    <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>Department </label>
                                    <select name="Course" id="course" class="form-control" required>
                                        <option value="">Select Department</option>
                                        <?php
                                        foreach ($course as $row) {
                                            echo '<option value="' . $row->CourseDescription . '">' . $row->CourseDescription . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>Grade Level </label>
                                    <select name="YearLevel" id="yearlevel" class="form-control" required>
                                        <option value="">Select Grade Level</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>Section </label>
                                    <select name="Section" id="section" class="form-control" required>
                                        <option value="">Select Section</option>
                                    </select>
                                </div>
                            </div>

                          <div class="col-lg-6">
    <input type="hidden" name="IDNumber" id="AdviserID" class="form-control" readonly>

    <label>Adviser</label>
    <input type="text" name="Adviser" id="AdviserName" class="form-control" readonly>
</div>
</div>

                        
                                   



                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label>Track</label>
                                                <select name="Track" id="track" class="form-control">
                                                    <option value="">Select Track</option>
                                                    <?php foreach ($t as $row) { ?>
                                                        <option value="<?= $row->track; ?>"><?= $row->track; ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label>Strand</label>
                                                <select name="Qualification" id="strand" class="form-control">
                                                    <option value="">Select Strand</option>
                                                </select>
                                            </div>
                                        </div>

                                    </div>

                                    <div class="row">
                                            <div class="col-lg-12">
                                                <div class="form-group">
                                                    <label>Students Special Needs</label>
                                                    <select name="ssn" id="ssn" class="form-control" required>
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
                                                <label>Balik Aral?</label>
                                                <select name="BalikAral" id="yearlevel" class="form-control" required>
                                                    <!-- <option value="">Select</option> -->
                                                    <option value="No Data">No Data</option>
                                                    <option value="Yes">Yes</option>
                                                    <option value="No">No</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <label>Indigenous People Member?</label>
                                                <select name="IP" id="yearlevel" class="form-control" required>
                                                    <!-- <option value="">Select</option> -->
                                                    <option value="No Data">No Data</option>
                                                    <option value="Yes">Yes</option>
                                                    <option value="No">No</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <label>4Ps Benefeciary?</label>
                                                <select name="FourPs" id="yearlevel" class="form-control" required>
                                                    <!-- <option value="">Select</option> -->
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
                                                <label>Repeater?</label>
                                                <select name="Repeater" class="form-control" required>
                                                    <!-- <option value="">Select</option> -->
                                                    <option value="No Data">No Data</option>
                                                    <option value="Yes">Yes</option>
                                                    <option value="No">No</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <label>Transferee?</label>
                                                <select name="Transferee" class="form-control" required>
                                                    <!-- <option value="">Select</option> -->
                                                    <option value="No Data">No Data</option>
                                                    <option value="Yes">Yes</option>
                                                    <option value="No">No</option>
                                                </select>
                                            </div>
                                        </div>

                                        <!-- Status -->
                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <label>Status</label>
                                                <select name="StudeStatus" id="StudeStatus" class="form-control" required>
                                                    <option value="">Select</option>
                                                    <option>Old</option>
                                                    <option>New</option>
                                                </select>
                                            </div>
                                        </div>

                                        <input type="hidden" class="form-control" value="<?php echo $this->session->userdata('sy'); ?>" readonly name="SY" required />

                                    </div>

                                    <div class="row">
											<div class="col-lg-4">
												<div class="form-group">
													<label>Dewormed?</label>
													<select name="dewormStat" class="form-control" required>
														<option value="0">No Data</option>
														<option value="1">Yes</option>
														<option value="2">No</option>
													</select>
												</div>
											</div>
											<div class="col-lg-4">
												<div class="form-group">
													<label>Parent's consent for milk?</label>
													<select name="pc_for_milk" class="form-control" required>
														<option value="0">No Data</option>
														<option value="1">Yes</option>
														<option value="2">No</option>
													</select>
												</div>
											</div>

											<div class="col-lg-4">
												<div class="form-group">
													<label style="font-size:10px !important">Beneficiary of SBFP in Previous Years?</label>
													<select name="sbfp_ben_prevyear" class="form-control" required>
														<option value="0">No Data</option>
														<option value="1">Yes</option>
														<option value="2">No</option>
													</select>
												</div>
											</div>
										</div>

                                    <!-- <p style="color:green"><b>Note:  Leave the Semester empty for Elementary and Junior High School.  The SY is required and it depends on the options you chose from the login form.</b></p> -->
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
                    <!-- /.modal-content -->
                    </div>
                    <!-- /.modal-dialog -->
                </div>
                <!-- /.modal -->


                                        <!-- sample modal content -->
                                        <div id="myModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="myModalLabel">Change SY</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <form class="row g-3 align-items-center" action="<?= base_url(); ?>Masterlist/enrolledList">
                                                            <div class="col-lg-12">
                                                                <div class="form-group">
                                                                    <input type="text" value="" name="fy" class="form-control" placeholder="<?= date('Y')-1; ?> - <?= date('Y'); ?>">
                                                                </div>
                                                            </div>
                                                        
                                                    </div>
                                                    <div class="modal-footer">
                                                        <input type="submit" name="cur" class="btn btn-info" value="Submit"> </span>
                                                    </div>
                                                    </form>
                                                </div>
                                                <!-- /.modal-content -->
                                            </div>
                                            <!-- /.modal-dialog -->
                                        </div>
                                        <!-- /.modal -->

               
<script type="text/javascript">
$(document).ready(function() {

    $('#course').change(function() {
        var course = $(this).val();
        if (course != '') {
            $.ajax({
                url: "<?php echo base_url(); ?>page/fetch_yearlevel",
                method: "POST",
                data: { course: course },
                success: function(data) {
                    console.log("Yearlevel data:", data);
                    $('#yearlevel').html(data);
                    $('#section').html('<option value="">Select Section</option>');
                    $('#AdviserID').val('');
                    $('#AdviserName').val('');
                },
                error: function(xhr) {
                    console.error("Yearlevel fetch failed:", xhr.responseText);
                }
            });
        }
    });

    $('#yearlevel').change(function() {
        var yearlevel = $(this).val();
        if (yearlevel != '') {
            $.ajax({
                url: "<?php echo base_url(); ?>page/fetch_section",
                method: "POST",
                data: { yearlevel: yearlevel },
                success: function(data) {
                    console.log("Section data:", data);
                    $('#section').html(data);
                    $('#AdviserID').val('');
                    $('#AdviserName').val('');
                },
                error: function(xhr) {
                    console.error("Section fetch failed:", xhr.responseText);
                }
            });
        }
    });
$('#section').change(function () {
    var yearlevel = $('#yearlevel').val();
    var section = $(this).val();

    if (yearlevel !== '' && section !== '') {
        $.ajax({
            url: "<?php echo base_url(); ?>page/fetch_adviser",
            method: "POST",
            data: {
                yearlevel: yearlevel,
                section: section
            },
            dataType: "json",
            success: function (data) {
                console.log("Adviser Data:", data);
                $('#AdviserID').val(data.IDNumber);
                $('#AdviserName').val(data.adviserName);
            },
            error: function (xhr) {
                console.error("Adviser fetch failed:", xhr.responseText);
            }
        });
    } else {
        $('#AdviserID').val('');
        $('#AdviserName').val('');
    }
});

});
</script>
