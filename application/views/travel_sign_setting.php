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
                            <div class="col-12">
                                <div class="page-title-box">
                                    <?php if($cur_staff->emp_type != 0){ ?>
                                    <a data-toggle="modal"  class="open-AddBookDialog btn btn-primary waves-effect waves-light" href="#add">Add New</a>
                                    <?php } ?>
                                    

                                    <a data-toggle="modal"  class="open-AddBookDialog btn btn-success waves-effect waves-light" href="#des">Configure</a>
                                    <button type="button" class="btn btn-primary waves-effect waves-light" data-toggle="modal" data-target="#myModal">e-Signature</button>

                                    
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                        </div>
                        <!-- end page title -->


                        <!-- sample modal content -->
                        <div id="add" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">

                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="myModalLabel">SIGNATORIES</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                    </div>
                                                    
                                                    <div class="modal-body">
                                                    <?= form_open('Travel/travel_sign_settings'); ?>

                                                        
                                                        
                                                        <div class="form-group col-md-12">
                                                            <label>Fullname</label>
                                                            <select class="form-control" data-toggle="select2" name="staff_id" required>
                                                                <option>Select</option>
                                                                <?php foreach($staff as $row){ ?>
                                                                    <option value="<?= $row->IDNumber; ?>"><?= $row->LastName.', '.$row->FirstName; ?> <?= ($char = mb_substr($row->MiddleName ?? '', 0, 1)) ? $char . '.' : ''; ?></option>
                                                                <?php } ?>
                                                            </select>
                                                        </div>

                                                        

                                                        <div class="form-group col-md-12">
                                                            <label>Position</label>
                                                            <select class="form-control" name="position" required>
                                                                    <option value="" disabled selected></option>
                                                                    <?php if($cur_staff->emp_type == 1){?>
                                                                        <option value="1">Recommending Authority - Outside the Divison</option>
                                                                        <option value="2">Approver</option>
                                                                    <?php }else{ ?>
                                                                    <option value="0">Immediate Supervisor or Department Head</option>
                                                                    <option value="1">Recommending Approval</option>
                                                                    <option value="2">Final Approving Authority</option>
                                                                    <?php } ?>
                                                            </select>
                                                        </div>
                                                        



                                                        <div class="form-group col-md-12">
                                                            <label>Type</label>
                                                            <select class="form-control" name="ttype" required>
                                                                    <option value="" disabled selected></option>
                                                                    <option value="0">Within the Division</option>
                                                                    <option value="1">Outside the Division</option>
                                                                    <!-- <option value="1">Foreign Travel</option>
                                                                    <option value="2">Personal Travel</option>
                                                                    <option value="3">Training-Related Travel</option> -->
                                                            </select>
                                                        </div>
                                                        <input type="hidden" value="<?= $cur_staff->emp_type; ?>" name="emp_type">

                                                        
                                                    
                                                        <div class="modal-footer">
                                                            <input type="submit" value="Submit" name="submit" class="btn btn-primary waves-effect waves-light">
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
                        <div id="des" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">

                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="myModalLabel">Configure</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                    </div>
                                                    
                                                    <div class="modal-body">
                                                    <?= form_open('Travel/update_emp_type'); ?>

                                             
                                                        <input type="hidden" value="<?= $this->session->username; ?>" name="user_id">
                                                        <div class="form-group col-md-12">
                                                            <label>Position</label>
                                                            <select class="form-control" name="emp_type" id="emp_type" required>
                                                                <option value="" disabled selected></option>
                                                                <option <?php if($cur_staff->emp_type == 1){echo "selected";} ?>  value="1">Teacher/NTP</option>
                                                                <option <?php if($cur_staff->emp_type == 2){echo "selected";} ?> value="2">School Head</option>
                                                                <option <?php if($cur_staff->emp_type == 3){echo "selected";} ?> value="3">Division Personnel</option>
                                                                <option <?php if($cur_staff->emp_type == 4){echo "selected";} ?> value="4">PSDS/EPS/EPS II/PDO I/Chief</option>
                                                            </select>
                                                        </div>

                                                        <div id="div_12" style="display:none;">
                                                            <div class="form-group col-md-12">
                                                                <label>School ID</label>
                                                                <input type="text" class="form-control" name="schoolID" value="<?= $cur_staff->schoolID; ?>" required>
                                                            </div>
                                                        </div>

                                                        <div id="div_34" style="display:none;">
                                                            <div class="form-group col-md-12">
                                                                <label>Section</label>
                                                                <select class="form-control" name="schoolID" required>
                                                                    <option value="" disabled selected></option>
                                                                    <option <?php if($cur_staff->schoolID == 0){echo "selected";} ?> value="0">Office of the Schools Division Superintendent</option>
                                                                    <option <?php if($cur_staff->schoolID == 1){echo "selected";} ?> value="1">School Governance and Operations Division</option>
                                                                    <option <?php if($cur_staff->schoolID == 2){echo "selected";} ?> value="2">Curriculum Implementation Division</option>
                                                                    <option <?php if($cur_staff->schoolID == 3){echo "selected";} ?> value="3">Titling</option>
                                                                </select>
                                                            </div>
                                                        </div>

                                                        

                                                        <div class="col-md-12">
                                                            <label for="departure_date">District</label>
                                                            <select class="form-control" name="d_id" required>
                                                                <option disabled selected></option>
                                                                <?php $district = $this->Common->no_cond('district'); foreach($district as $row){  ?>
                                                                <option <?php if($cur_staff->d_id == $row->id){echo "selected";} ?> <?= set_select('ttype', '0', isset($request) && $request->d_id == '0') ?> value="<?= $row->id; ?>"><?= $row->discription; ?></option>
                                                                <?php } ?>
                                                            </select>
                                                        </div>
                                                    
                                                        <div class="modal-footer">
                                                            <input type="submit" value="Submit" name="submit" class="btn btn-primary waves-effect waves-light">
                                                        </div>
                                                </form>
                                                </div>
                                                </div>
                                                <!-- /.modal-content -->
                                            </div>
                                            <!-- /.modal-dialog -->
                                        </div>
                                        <!-- /.modal -->

                                        <!--  modal content -->
                                        <div id="myModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="myModalLabel">Add/Update Electronic Signature</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                    </div>
                                                    <div class="modal-body">
                                                    <?= form_open_multipart('Travel/esig'); ?>
                                                        <input type="hidden" value="<?= $id; ?>" name="staff_id">


                                                        <div class="row">
                                                            <div class="col-lg-12">
                                                                <div class="form-group">
                                                                    <label><span class="text-danger">Note: Only PNG file format is allowed. The maximum file size is 2MB, and the image dimensions must be 150px wide and 70px high.</span></label>
                                                                    <input type="file"  class="form-control" name="userfile"  size="20" />
                                                                </div>	
                                                            </div>	              
                                                        </div>

                                                        </div>
                                                        <div class="modal-footer">
                                                            <input type="submit" class="btn btn-primary waves-effect waves-light" value="Save changes" />
                                                        </div>
                                                    </form>
                                                </div>
                                                <!-- /.modal-content -->
                                            </div>
                                            <!-- /.modal-dialog -->
                                        </div>
                                        <!-- /.modal -->

                                        

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

                        <?php 
                        $roles = [
                            0 => "Immediate Supervisor Or Department Head",
                            1 => "Recommending Approval",
                            2 => "Final Approving Authority"
                        ];

                        $travel = [
                            0 => "Within the Division",
                            1 => "Outside the Division"
                        ];
                        ?>
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body table-responsive">
                                        <h4 class="header-title mb-4">SIGNATORIES</h4><br />

                                        <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                            <!-- <table class="table mb-0"> -->
                                            <thead>
                                                <tr>
                                                    <th>No.</th>
                                                    <th>Fullname</th>
                                                    <th>Position</th>
                                                    <th>Travel Type</th>
                                                    <th>Action</th>
                                            </thead>
                                            <tbody>
                                                <?php 
                                                    $c=1;
                                                    foreach($data as $row){
                                                        $s = $this->Common->one_cond_row('hris_staff','IDNumber',$row->staff_id);
                                                ?>
                                               <tr>
                                                <td><?= $c++; ?></td>
                                                <td><?= $s->FirstName.' '.$s->LastName; ?></td>
                                                <td><?= $roles[$row->position]; ?></td>
                                                <td><?= $travel[$row->ttype]; ?></td>
                                                <td>
                                                    <a href="<?= base_url(); ?>Travel/delete_travel_setting/<?= $row->id; ?>" onclick="return confirm('Are you sure?')" class="btn btn-sm btn-danger">Delete</a>
                                                </td>
                                               </tr>
                                               <?php } ?>
                                            </tbody>

                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- end row -->

                    </div>
                    <!-- end container-fluid -->

                </div>
                <!-- end content -->

                <script>
document.addEventListener('DOMContentLoaded', function () {
    const empSelect = document.getElementById('emp_type');
    const div12 = document.getElementById('div_12');
    const div34 = document.getElementById('div_34');

    const schoolInput   = div12.querySelector('input[name="schoolID"]');   // text input
    const sectionSelect = div34.querySelector('select[name="schoolID"]');  // dropdown

    function toggleDivs() {
        const val = empSelect.value;

        // Reset both fields first
        div12.style.display = 'none';
        div34.style.display = 'none';

        schoolInput.required   = false;
        schoolInput.disabled   = true;

        sectionSelect.required = false;
        sectionSelect.disabled = true;

        if (val === '1' || val === '2') {
            // Teacher / School Head → School ID text field
            div12.style.display = 'block';
            schoolInput.disabled = false;
            schoolInput.required = true;
        } else if (val === '3' || val === '4') {
            // Division Personnel / PSDS… → Section dropdown
            div34.style.display = 'block';
            sectionSelect.disabled = false;
            sectionSelect.required = true;
        }
    }

    // Run once on page load to respect current value from PHP
    toggleDivs();

    // Run every time the selection changes
    empSelect.addEventListener('change', toggleDivs);
});
</script>

                



                                        





            <?php include('templates/footer.php'); ?>


            