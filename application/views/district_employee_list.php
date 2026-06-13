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
                                <a data-toggle="modal"  class="open-AddBookDialog btn btn-primary waves-effect waves-light" href="#add">Add New</a>
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                        </div>
                        <!-- end page title -->

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

                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body table-responsive">
                                    <h4 class="header-title mb-4">Employee List</h4>
                                        <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">

                                        <thead>
                                            <tr>
                                                <th>Fullname</th>
                                                <th>Employee No.</th>
                                                <th>Action</th>
                                                <th>Last Login</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                                foreach($staff as $row){
                                                    $log = $this->Common->one_cond_row_desc('mis_logs','username',$row->IDNumber,'transDate','DESC'); 
                                            ?>
                                              <tr>
                                                <td><?= strtoupper($row->FirstName.' '.$row->MiddleName.' '.$row->LastName); ?></td>
                                                <td><?= $row->IDNumber; ?></td>
                                                <td>
                                                    <a href="<?= base_url(); ?>Pages/pass_change_in_district/<?= $row->IDNumber; ?>/<?= $this->uri->segment(3); ?>" class="btn btn-sm btn-warning" onclick="return confirm('Are you sure?')">Reset Password</a>
                                                    <a href="<?= base_url(); ?>Page/remove_employee_on_list/<?= $row->IDNumber; ?>/<?= $this->uri->segment(3); ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Removed</a>
                                                </td>
                                                <td><?php if(!empty($log)){echo $log->transDate;} ?></td>
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
                <div id="add" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">

                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="myModalLabel">Add Employee</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                    </div>
                                                    
                                                    <div class="modal-body">
                                                    <?= form_open('Page/add_employee_on_list/'.$this->uri->segment(3)); ?>
                                                        <div class="form-group col-md-12">
                                                            <label>Employee Number</label>
                                                            <input type="text" required value="" name="IDNumber" class="form-control"> 
                                                        </div>
                                                        <input type="hidden" name="school_id" value="<?= $this->uri->segment(3); ?>">
                                                    
                                                        <div class="modal-footer">
                                                            <button type="submit" onclick="return confirm('Are you sure you want to add this employee to this school? Please review it first before clicking the OK button.')" name="submit" class="btn btn-primary waves-effect waves-light">Save</button>
                                                        </div>
                                                </form>
                                            </div>
                                                </div>
                                                <!-- /.modal-content -->
                                            </div>
                                            <!-- /.modal-dialog -->
                                        </div>
                                        <!-- /.modal -->

                <?php include('templates/footer.php'); ?>       

             
 