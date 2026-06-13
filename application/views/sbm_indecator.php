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


                        <!-- sample modal content -->
                        <div id="add" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">

                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="myModalLabel">Principle</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                    </div>
                                                    
                                                    <div class="modal-body">
                                                    <?= form_open('Page/sbm'); ?>

                                                        
                                                        
                                                        <div class="form-group col-md-12">
                                                            <label>Indicator</label>
                                                            <input type="text" required value="" name="indicator" class="form-control"> 
                                                        </div>

                                                        <div class="form-group col-md-12">
                                                            <label>Description</label>
                                                            <textarea class="form-control" name="description" rows="5" id="example-textarea"></textarea>
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
                                        <h4 class="header-title mb-4">PRINCIPLE</h4><br />

                                        <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                            <!-- <table class="table mb-0"> -->
                                            <thead>
                                                <tr>
                                                    <th>No.</th>
                                                    <th>Principle</th>
                                                    <th>Manage</th>
                                            </thead>
                                            <tbody>
                                                <?php 
                                                    $c=1;
                                                    foreach($sbm as $row){
                                                ?>
                                               <tr>
                                                <td><?= $c++; ?></td>
                                                <td><?= $row->indicator; ?></td>
                                                <td>
                                                    <a href="<?= base_url(); ?>Page/sbm_edit/<?= $row->id; ?>" class="btn btn-sm btn-success">Edit</a>
                                                    <a href="<?= base_url(); ?>Page/delete_sbm/<?= $row->id; ?>" onclick="return confirm('Are you sure?')" class="btn btn-sm btn-danger">Delete</a>
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

                



                                        





            <?php include('templates/footer.php'); ?>


            