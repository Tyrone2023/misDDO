<?php include('templates/head.php'); ?>
            <link href="<?= base_url(); ?>assets/libs/custombox/custombox.min.css" rel="stylesheet" type="text/css">
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
                            <div class="col-6">
                                <div class="card">
                                    <div class="card-body table-responsive">
                                        <h4 class="header-title mb-4">SCHOOL ALLOCATION</h4><br />

                                        

                                        <?= form_open('Page/fund_update'); ?>
                                                        <div class="form-group col-md-12">
                                                            <label>Fund Allocation</label>
                                                            <input type="text" required value="<?= $st->alloc_amount; ?>" id="item" name="alloc_amount" class="form-control"> 
                                                        </div>
                                                        <input type="hidden" name="id" id="id" value="<?= $st->id; ?>">

                                                        <div class="form-group col-md-12">
                                                            <label>Fascal Year</label>
                                                            <select class="form-control" name="fy" required>
                                                                <option></option>
                                                            <?php 
                                                            $firstYear = (int)date('Y');
                                                            $lastYear = $firstYear + 5;
                                                            for($i=$firstYear;$i<=$lastYear;$i++){ echo '<option';
                                                                if($st->alloc_year == $i){echo ' selected ';} 
                                                                echo ' value='.$i.'>'.$i.'</option>';}
                                                            ?>
                                                            </select>
                                                        </div>

                                                        
                                                        <div class="form-group col-md-12">
                                                            <label>Group</label>
                                                            <select class="form-control" name="group" required>
                                                                <option></option>
                                                                <?php 
                                                                $g = array('Senior High School','Junior High School','Elementary');
                                                                  foreach($g as $row){
                                                                ?>
                                                                <option <?php if($st->alloc_group == $row){echo ' selected ';}  ?> value="<?= $row; ?>"><?= $row; ?></option>
                                                                <?php } ?>
                                                            </select>
                                                        </div>


                                                        
                                                        <div class="form-group col-md-12">
                                                            <label>Allocation Type</label>
                                                            <select class="form-control" name="type" required>
                                                                <option></option>
                                                                <?php 
                                                                  foreach($bs as $row){
                                                                ?>
                                                                <option <?php if($st->alloc_type == $row->description){echo ' selected ';}  ?> value="<?= $row->description; ?>"><?= $row->description; ?></option>
                                                                <?php } ?>
                                                            </select>
                                                        </div>

                                                    
                                                        <div class="modal-footer">
                                                            <button type="submit" name="submit" class="btn btn-primary waves-effect waves-light">Update</button>
                                                        </div>
                                                </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- end row -->

                    </div>
                    <!-- end container-fluid -->

                </div>
                <!-- end content -->




                                                                            
                                       

            <?php include('templates/footer.php'); ?>