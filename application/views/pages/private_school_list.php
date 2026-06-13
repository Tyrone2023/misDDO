

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
                                    <a  href="#" data-toggle="modal" data-target="#general" class="btn-sm btn btn-purple mr-1">General Reports - Consolidated View</a>
                                    <a  href="#" data-toggle="modal" data-target="#other"  class="btn-sm btn btn-primary mr-1">Other Reports - Consolidated View</a>
                              
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
                                                    <th>NO.</th>
                                                    <th>School ID</th>
                                                    <th>School Name</th>
                                                    <th>Reports</th>
                                                </tr>
                                            </thead>

                                            <tbody>
                                                <?php $c=1; foreach($data as $row){?>
                                                <tr>
                                                    <td><?= $c++; ?></td>
                                                    <td><?= $row->schoolID; ?></td>
                                                    <td><?= strtoupper($row->schoolName); ?></td>
                                                    <td>
                                                        <a target="_blank" href="<?= base_url(); ?>Ps/private_list_admin/<?= $row->schoolID; ?>" class="btn-success mr-1 btn btn-sm" > General Reports</a>
                                                        <a target="_blank" href="<?= base_url(); ?>Ps/private_list_other_admin/<?= $row->schoolID; ?>" class="btn-primary mr-1 btn btn-sm">Other Reports</a>

                                                        
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
                                        <div id="general" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-primary">
                                                        <h5 class="modal-title" id="modalTitle">General Reports</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                    </div>
                                                    <div class="modal-body">

                                                    <div class="row">
                                            <div class="col-12">
                                                <div class="">
                                                        <?= validation_errors(); ?>

                                                        <?php
                                                        $attributes = array('class' => 'parsley-examples form-horizontal','target' => '_blank');
                                                        echo form_open('Ps/general_consolidated_report', $attributes);
                                                        ?>
                                                        <div class="form-group row">
                                                            <label class="col-lg-6 col-form-label">Year</label>
                                                            <div class="col-lg-6">
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
                                                            <label class="col-lg-6 col-form-label">Quarter</label>
                                                            <div class="col-lg-6">
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
                                                        

                                                </div>
                                            </div>

                                        </div>
                                        <!-- end row -->
                                                        
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="submit" class="btn btn-primary waves-effect waves-light">Filter</button>
                                                    </div>
                                                    </form>
                                                </div>
                                                <!-- /.modal-content -->
                                            </div>
                                            <!-- /.modal-dialog -->
                                        </div>
                                        <!-- /.modal -->


                                        <!-- sample modal content -->
                                        <div id="other" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-primary">
                                                        <h5 class="modal-title" id="modalTitle">Other Reports</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                    </div>
                                                    <div class="modal-body">

                                                    <div class="row">
                                            <div class="col-12">
                                                <div class="">
                                                        <?= validation_errors(); ?>

                                                        <?php
                                                        $attributes = array('class' => 'parsley-examples form-horizontal','target'=>'_blank');
                                                        echo form_open('Ps/other_consolidated_report', $attributes);
                                                        ?>
                                                        <div class="form-group row">
                                                            <label class="col-lg-6 col-form-label">Year</label>
                                                            <div class="col-lg-6">
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
                                                            <label class="col-lg-6 col-form-label">Quarter</label>
                                                            <div class="col-lg-6">
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
                                                            <label class="col-lg-6 col-form-label">Grade Level</label>
                                                            <div class="col-lg-6">
                                                                <select class="form-control" name="grade_level" required>
                                                                    <option value="" disabled selected>Select Grade Level</option>
                                                                    <option value="0">Kinder</option>
                                                                    <?php for($i = 1; $i <= 12; $i++): ?>
                                                                        <option value="<?= $i ?>">Grade <?= $i ?></option>
                                                                    <?php endfor; ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        

                                                        
                                                    
                                                </div>
                                            </div>

                                        </div>
                                        <!-- end row -->
                                                        
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="submit" class="btn btn-primary waves-effect waves-light">Filter</button>
                                                    </div>
                                                    </form>
                                                </div>
                                                <!-- /.modal-content -->
                                            </div>
                                            <!-- /.modal-dialog -->
                                        </div>
                                        <!-- /.modal -->



              

    
   

 