

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
                                    <?php if($this->session->position == 'user'){?>
                                    <a href="#" class="btn btn-info btn-sm waves-effect waves-light"data-toggle="modal" data-target="#myModal">Report Entry</a>
                                    <?php } ?>
                                    <!-- <a href="#" class="btn btn-purple btn-sm waves-effect waves-light"data-toggle="modal" data-target="#ren">Number of Section</a> -->
                              
                                    <div class="clearfix"></div>
                                    
                                </div>
                            </div>
                        </div>
                        <!-- end page title -->

                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body table-responsive">
                                    <h4 class="header-title mb-4"><?= $title; ?></h4>

                                    

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

                                         <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">

                                            <thead>
                                                <tr>
                                                    <th>Quarter</th>
                                                    <th>Grade Level</th>
                                                    <th>Year</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>

                                            <tbody>
                                                
                                                <?php foreach($data as $row){?>
                                                <tr>
                                                    <td>Quarter <?= $row->quarter; ?></td>
                                                    <td>Grade <?= $row->grade_level; ?></td>
                                                    <td><?= $row->year; ?></td>
                                                    <td>
                                                        <?php if($this->session->position == 'user'){?>
                                                        <a class="btn btn-info btn-sm waves-effect waves-light" href="<?= base_url(); ?>Coor/coor_entry_details/<?= $row->quarter; ?>/<?= $row->year; ?>/<?= $row->grade_level; ?>/<?= $this->uri->segment(3); ?>">View Details</a>
                                                        <?php }else{ ?>
                                                        <a class="btn btn-info btn-sm waves-effect waves-light" href="<?= base_url(); ?>Coor/coor_entry_details_school/<?= $row->quarter; ?>/<?= $row->year; ?>/<?= $row->grade_level; ?>/<?= $this->uri->segment(3); ?>">View Details</a>
                                                        <?php } ?>
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

                <!-- sample modal content -->
                                        <div id="myModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-info">
                                                        <h5 class="modal-title" id="modalTitle">CLASS PROFICIENCY LEVEL</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                    </div>
                                                    <div class="modal-body">

                                                    <div class="row">
                                            <div class="col-12">
                                                <div class="">
                                                        <?= validation_errors(); ?>

                                                        <?php
                                                        $attributes = array('class' => 'parsley-examples form-horizontal');
                                                        echo form_open('Coor/level_proficiency', $attributes);
                                                        ?>
                                                        <input type="hidden" value="<?= $district->id; ?>" name="district_id">
                                                        <input type="hidden" value="<?= $this->uri->segment(3); ?>" name="subject">

                                                        <div class="form-group row">
                                                            <label class="col-lg-4 col-form-label">Year</label>
                                                            <div class="col-lg-8">
                                                                <?php
                                                                $currentYear = date("Y")+1; 
                                                                $earliestYear = $currentYear - 6; 
                                                                ?>
                                                                <select name="year" class="form-control" required>
                                                                <option value="" disabled selected>Select Year</option>
                                                                <?php
                                                                    for ($year = $currentYear; $year >= $earliestYear; $year--) {
                                                                        echo "<option value=\"$year\">$year</option>";
                                                                    }
                                                                ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="form-group row">
                                                            <label class="col-lg-4 col-form-label">Quarter</label>
                                                            <div class="col-lg-8">
                                                                <select class="form-control" name="quarter" required>
                                                                    <option value="" disabled selected>Select Quarter</option>
                                                                    <?php
                                                                        $quarters = ['First', 'Second', 'Third', 'Fourth'];
                                                                        foreach ($quarters as $index => $label) {
                                                                            $value = $index + 1;
                                                                            echo "<option value=\"$value\">$label Quarter</option>";
                                                                        }
                                                                    ?>
                                                                </select>
                                                            </div>
                                                        </div>


                                                        <div class="form-group row">
                                                            <label class="col-lg-4 col-form-label">Grade Level</label>
                                                            <div class="col-lg-8">
                                                               
                                                                <select name="grade_level" class="form-control">
                                                                    <option value="">Select Grade Level</option>
                                                                    <option value="0">Kindergarten</option>
                                                                    <?php for ($i = 1; $i <= 12; $i++): ?>
                                                                        <option value="<?= $i ?>">Grade <?= $i ?></option>
                                                                    <?php endfor; ?>
                                                                </select>

                                                            </div>
                                                        </div>


                                                        <div class="form-group row">
                                                            <label class="col-lg-4 col-form-label">Section</label>
                                                            <div class="col-lg-8">
                                                               
                                                                <select name="section" class="form-control" required>
                                                                    <option value="" disabled selected>Select Section</option>
                                                                    <?php foreach($section as $row){ ?>
                                                                        <option value="<?= $row->Section; ?>"><?= $row->Section; ?></option>
                                                                    <?php } ?>
                                                                </select>

                                                            </div>
                                                        </div>

                                                        <div class="form-group row">
                                                            <label class="col-lg-8 col-form-label">Average MPS</label>
                                                            <div class="col-lg-4">
                                                               <input type="text" name="mps" value="" class="form-control numbers-only">
                                                            </div>
                                                        </div>

                                                        <div class="form-group row">
                                                            <label class="col-lg-8 col-form-label">No. of Learners with 75% MPS and Above</label>
                                                            <div class="col-lg-4">
                                                               <input type="text" name="abovemps" value="" class="form-control numbers-only">
                                                            </div>
                                                        </div>

                                                        <div class="form-group row">
                                                            <label class="col-lg-8 col-form-label">Total No. of Learners</label>
                                                            <div class="col-lg-4">
                                                               <input type="text" name="learners" value="" class="form-control numbers-only">
                                                            </div>
                                                        </div>

                                                        <div class="form-group row">
                                                            <label class="col-lg-8 col-form-label">AVE MPS * No. of Learners</label>
                                                            <div class="col-lg-4">
                                                               <input type="text" name="total" value="" class="form-control numbers-only" readonly>
                                                            </div>
                                                        </div>

                                                       
                                                        <input type="hidden" value="<?= $this->uri->segment(3); ?>" name="id">
                                                        <input type="hidden" value="<?= $staff->schoolID; ?>" name="school_id">

                                                        
                                                    
                                                </div>
                                            </div>

                                        </div>
                                        <!-- end row -->
                                                        
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="submit" class="btn btn-info waves-effect waves-light">Save</button>
                                                    </div>
                                                    </form>
                                                </div>
                                                <!-- /.modal-content -->
                                            </div>
                                            <!-- /.modal-dialog -->
                                        </div>
                                        <!-- /.modal -->


                                        <div id="ren" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-purple">
                                                        <h5 class="modal-title text-white" id="modalTitle">ADD TOTAL NUMBER OF SECTIONS</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                    </div>
                                                    <div class="modal-body">

                                                    <div class="row">
                                                        <div class="col-12">
                                                            <div class="card">
                                                                <div class="card-body table-responsive">
                                                                    <cite class="text-danger">“Kindly review your entries carefully before saving. Once submitted, the information can no longer be modified.”</cite>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>



                                                    <div class="row">
                                                        <div class="col-12">
                                                            <div class="">
                                                                    <?= validation_errors(); ?>

                                                                    <?php
                                                                    $attributes = array('class' => 'parsley-examples form-horizontal');
                                                                    echo form_open('Coor/total_section', $attributes);
                                                                    ?>
                                                                    <input type="hidden" value="<?= $district->id; ?>" name="district_id">

                                                                    <div class="form-group row">
                                                                        <label class="col-lg-6 col-form-label">Year</label>
                                                                        <div class="col-lg-6">
                                                                            <?php
                                                                            $currentYear = date("Y")+1; 
                                                                            $earliestYear = $currentYear - 6; 
                                                                            ?>
                                                                            <select name="fy" class="form-control" required>
                                                                            <option value="" disabled selected>Select Year</option>
                                                                            <?php
                                                                                for ($year = $currentYear; $year >= $earliestYear; $year--) {
                                                                                    echo "<option value=\"$year\">$year</option>";
                                                                                }
                                                                            ?>
                                                                            </select>
                                                                        </div>
                                                                    </div>

                                                                    


                                                                    <div class="form-group row">
                                                                        <label class="col-lg-6 col-form-label">Total Number of Sections</label>
                                                                        <div class="col-lg-6">
                                                                        <input type="text" name="count" required value="" class="form-control numbers-only">
                                                                        </div>
                                                                    </div>

                                                                   
                                                                
                                                                    <input type="hidden" value="<?= $staff->schoolID; ?>" name="school_id">
                                                                    <input type="hidden" value="<?= $this->session->username; ?>" name="staff_id">

                                                                    
                                                                
                                                            </div>
                                                        </div>

                                                    </div>
                                                    <!-- end row -->
                                                        
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button onclick="return confirm('Are you Sure?')" type="submit" class="btn btn-purple waves-effect waves-light">Save</button>
                                                    </div>
                                                    </form>
                                                </div>
                                                <!-- /.modal-content -->
                                            </div>
                                            <!-- /.modal-dialog -->
                                        </div>
                                        <!-- /.modal -->



                                        <script>
                                            document.querySelectorAll('.numbers-only').forEach(function(input) {
                                                input.addEventListener('input', function () {
                                                    this.value = this.value.replace(/[^0-9]/g, '');
                                                });
                                            });
                                        </script>

                                        <script>
                                        document.addEventListener('DOMContentLoaded', function () {
                                            const mpsInput = document.querySelector('input[name="mps"]');
                                            const learnersInput = document.querySelector('input[name="learners"]');
                                            const totalInput = document.querySelector('input[name="total"]');

                                            function calculateTotal() {
                                                const mps = parseFloat(mpsInput.value) || 0;
                                                const learners = parseFloat(learnersInput.value) || 0;
                                                const total = Math.floor(mps * learners); // remove decimals
                                                totalInput.value = total;
                                            }

                                            // Trigger calculation on input
                                            mpsInput.addEventListener('input', calculateTotal);
                                            learnersInput.addEventListener('input', calculateTotal);
                                        });
                                        </script>
   

 