<?php include('templates/head.php'); ?>
            <?php include('templates/header.php'); ?>

            

            <!-- ============================================================== -->
            <!-- Start Page Content here -->
            <!-- ============================================================== -->

            <div class="content-page">
                <div class="content">

                    <!-- Start Content-->
                    <div class="container-fluid">

                  

                    <?php if(!isset($_SESSION['sbm_fy'])){?>

                    <!-- start page title -->
                    <div class="row">
                            <div class="col-12">
                                <div class="page-title-box">
                                <div class="card">
                                    <div class="card-body">

                                    
                                                    <?= form_open('page/sbm_district_list'); ?>
                                                        <div class="form-group col-md-6">
                                                            <label>Fiscal Year</label>
                                                            <select class="form-control" name="fy" required>
                                                                <option></option>
                                                                <?php 
                                                                    for ($count = date('Y'); $count >= 2020; $count--) 
                                                                    echo '<option value="' . $count . '">' . $count . '</option>';
                                                                ?>
                                                                
                                                            </select>


                                                        </div>

                                                        <div class="form-group col-md-6">
                                                            <input type="submit" name="submit" class="btn btn-primary waves-effect waves-light" value="Submit" />
                                                        </div>
                                                    </form>
                                    </div>
                                </div>
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                        </div>
                        <!-- end page title -->
                        
                        <?php }else{ ?>

                        <!-- start page title -->
                        <div class="row">
                            <div class="col-12">
                                <div class="page-title-box">
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
                                        <h4 class="header-title mb-4">School List</h4><br />

                                        <table class="table mb-0">
                                            <!-- <table class="table mb-0"> -->
                                            <thead>
                                                <tr>
                                                    <th>No.</th>
                                                    <th>School Name </th>
                                                    <th class="text-center">Action Plan</th>
                                                    <th class="text-center">Self-Assessment</th>
                                                    <th class="text-center">TA Form</th>
                                            </thead>
                                            <tbody>
                                                <?php 
                                                    $c=1;
                                                    foreach($school as $row){
                                                    $sbm_submit=$this->Common->two_cond_row('sbm','school_id',$row->schoolID,'fy',$_SESSION['sbm_fy']);
                                                    $sbm_ta=$this->Common->two_cond_row('sbm_ta','school_id',$row->schoolID,'fy',$_SESSION['sbm_fy']);
                                                    $sbm_action=$this->Common->two_cond_row('sgod_action_plan','school_id',$row->schoolID,'fy',$_SESSION['sbm_fy']);
                                                ?>
                                               <tr>
                                                <td><?= $c++; ?></td>
                                                <td><?= $row->schoolName; ?></td>
                                                <td class="text-center">
                                                    <?php if(empty($sbm_action)){?>
                                                    <span class="text-danger">&#10008;</span> 
                                                    <?php }else{ ?>
                                                        <a target="_blank" href="<?= base_url(); ?>Page/sbm_action_plan_pview_district/<?= $row->schoolID; ?>" class="btn btn-success btn-sm">View</a>
                                                    <?php } ?>
                                                </td>
                                                <td class="text-center">
                                                    <?php if(empty($sbm_submit)){?>
                                                    <span class="text-danger">&#10008;</span> 
                                                    <?php }else{ ?>
                                                        <a target="_blank" href="<?= base_url(); ?>Page/checklist_district/<?= $row->schoolID; ?>" class="btn btn-success btn-sm">View</a>
                                                    <?php } ?>
                                                </td>
                                                <td class="text-center">
                                                    <?php if(empty($sbm_ta)){?>
                                                    <span class="text-danger">&#10008;</span> 
                                                    <?php }else{ ?>
                                                    
                                                        
                                                        <a target="_blank" href="<?= base_url(); ?>Page/tapr_form_district/<?= $row->schoolID; ?>" class="btn btn-success btn-sm">View</a>


                                                    <?php } ?>
                                                </td>
                                                <!-- <td>
                                                    <a href="<?= base_url(); ?>Page/tapr_form_district/<?= $row->schoolID; ?>" target="_blank" class="btn btn-sm btn-success">View</a>
                                                </td> -->
                                               </tr>
                                               <?php } ?>
                                            </tbody>

                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- end row -->

                        <?php } ?>

                    </div>
                    <!-- end container-fluid -->

                </div>
                <!-- end content -->

                



                                        





            <?php include('templates/footer.php'); ?>


            