

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
                                    <a href="<?= base_url(); ?>Ps/private_report_add" class="btn btn-success waves-effect width-md waves-light">Add New</a>
                              
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
                                                    <th>Year</th>
                                                    <th>Quater</th>
                                                    <th>Percentage of the school leaver rate attributed to teenage pregnancy</th>
                                                    <th>Teacher's in Private Schools Subsidy</th>
                                                    <th>Number of Teachers Let Passers</th>
                                                    <th>Number of Teachers Not-Let Passers</th>
                                                    <th>No. of policies reviewed and implemented in all areas and levels of educational services delivery</th>
                                                    <th>Schools implementing full in-persons classes</th>
                                                    <th>Amount Allocation from PEAC</th>
                                                    <th>Amount Allocation for ESC</th>
                                                    <th>Amount Allocation for GASTPE</th>
                                                    <th>Schools implementing blended learning</th>
                                                    <th>Schools implementing full distance learning</th>
                                                    <th>File Attachment</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>

                                            <tbody>
                                                <?php foreach($data as $row){?>
                                                <tr>
                                                    <td><?= $row->year; ?></td>
                                                    <td>Quater <?= $row->quarter; ?></td>
                                                    <td>Male : <span class="badge badge-info"><?= $row->mpregnancy; ?></span> - Female : <span class="badge badge-success"><?= $row->fpregnancy; ?></span></td>
                                                    <td>Male : <span class="badge badge-info"><?= $row->msubsidy; ?></span>  - Female : <span class="badge badge-success"><?= $row->msubsidy; ?></span></td>
                                                    <td>Male : <span class="badge badge-info"><?= $row->mletpass; ?></span>  - Female : <span class="badge badge-success"><?= $row->mletpass; ?></span></td>
                                                    <td>Male : <span class="badge badge-info"><?= $row->mnotletpass; ?></span>  - Female : <span class="badge badge-success"><?= $row->mnotletpass; ?></span></td>
                                                    <td><span class="badge badge-purple"><?= $row->delivery; ?></span></td>
                                                    <td><span class="badge badge-purple"><?= $row->full_persons_classes; ?></span></td>
                                                    <td><span class="badge badge-purple"><?= $row->peac; ?></span></td>
                                                    <td><span class="badge badge-purple"><?= $row->esc; ?></span></td>
                                                    <td><span class="badge badge-purple"><?= $row->gastpe; ?></span></td>
                                                    <td><span class="badge badge-purple"><?= $row->blended_learning; ?></span></td>
                                                    <td><span class="badge badge-purple"><?= $row->distrance_learning; ?></span></td>
                                                    <td> &nbsp; &nbsp; <a target="_blank" href="<?= base_url(); ?>uploads/private/<?= $row->file; ?>" class="text-info"><i class="fas fa-file"></i></a></td>
                                                    <td>
                                                        <div class="button-list">
                                                            <a href="<?= base_url(); ?>Ps/private_report_update/<?= $row->id; ?>" data-id="<?= $row->id; ?>" class="btn btn-purple waves-effect waves-light open-AddBookDialog" data-toggle="modal" data-target="#myModal">File Update</a>
                                                            <a href="<?= base_url(); ?>Ps/private_report_update/<?= $row->id; ?>" class="btn btn-info waves-effect waves-light">Update</a>
                                                            <a onclick="return confirm('Are you sure?')" href="<?= base_url(); ?>Ps/report_delete/<?= $row->id; ?>" class="btn btn-sm btn-danger waves-effect waves-light">Delete</a>
                                                        </div>
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
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="myModalLabel">Update File</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                    </div>
                                                    <div class="modal-body">
                                                    <?php
                                                        $attributes = array('class' => 'parsley-examples form-horizontal');
                                                        echo form_open_multipart('Ps/private_update_file', $attributes);
                                                    ?>
                                                        <input type="hidden" id="id" name="ren" class="form-control">
                                                        <div class="form-group row">
                                                            <div class="col-lg-12">
                                                                <input type="file" name="file" class="form-control" placeholder="File">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="submit" class="btn btn-purple waves-effect waves-light">Save changes</button>
                                                    </div>
                                                </div>
                                                <!-- /.modal-content -->
                                            </div>
                                            <!-- /.modal-dialog -->
                                        </div>
                                        <!-- /.modal -->

    
   

 