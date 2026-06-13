

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
                                    <!-- <a href="<?= base_url(); ?>Ps/private_report_add" class="btn btn-success waves-effect width-md waves-light">Add New</a> -->
                              
                                    <div class="clearfix"></div>
                                    
                                </div>
                            </div>
                        </div>
                        <!-- end page title -->

                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body table-responsive">
                                    <h4 class="header-title mb-4"><?= $question->question; ?></h4>

                                    

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

                                         <table class="table table-bordered mb-0">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Year</th>
                                                    <th>Quarter</th>
                                                    <th>Grade Level</th>
                                                    <th class="text-center">Number of Male</th>
                                                    <th class="text-center">Number of Female</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php $c=1; foreach($data as $row){ ?>
                                                <tr>
                                                    <td><?= $c++; ?></td>
                                                    <td><?= $row->year; ?></td>
                                                    <td>Quarter <?= $row->quarter; ?></td>
                                                    <td>
                                                        <?php if($row->grade_level == 0){?>
                                                            Kinder
                                                        <?php }else{ ?>
                                                        Grade <?= $row->grade_level; ?>
                                                        <?php } ?>
                                                    </td>
                                                    <td class="text-center"><span class="badge badge-info"><?= $row->nmale; ?></span></td>
                                                    <td class="text-center"><span class="badge badge-purple"><?= $row->nfemale; ?></span></td> 
                                                    <td>
                                                            <a onclick="return confirm('Are you sure?')" href="<?= base_url(); ?>Ps/report_other_delete/<?= $row->id; ?>" class="btn btn-danger waves-effect waves-light">Delete</a>
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


   

 