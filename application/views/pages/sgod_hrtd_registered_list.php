

            <!-- ============================================================== -->
            <!-- Start Page Content here -->
            <!-- ============================================================== -->
             <?php 
                $modality = [
                        1 => 'Face to Face',
                        2 => 'Online',
                        3 => 'Blended',
                    ];

             ?>

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

                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body table-responsive">

                                    <div class="row">
                                        <div class="col-12">
                                            <div class="card">
                                                <div class="card-body table-responsive">
                                                    <h4 class="header-title mb-4 text-center"><?= $training->description; ?><br /><span class="badge badge-success">Date: <?= $training->actual_date; ?></span></h4>
                                                    <p class="text-center"><?= $training->venue; ?><br />passkey: <?= $training->passkey; ?></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

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
                                                    <th>#</th>
                                                    <th>Fullname</th>
                                                    <th>Position</th>
                                                    <th>Email Address</th>
                                                    <th class="text-center">WAP <br />File</th>
                                                    <th class="text-center">MOV <br />File</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>

                                            <tbody>
                                                <?php $c=1; foreach($data as $row){
                                                    $pos = $this->Common->one_cond_row('sgod_hrtd_position','id',$row->position);
                                                    $staff = $this->Common->one_cond_row('hris_staff','IDNumber',$row->IDNumber);
                                                ?>
                                                <tr>
                                                    <td><?= $c++; ?></td>
                                                    <td><?= ucfirst($staff->FirstName); ?> <?= ucfirst($staff->MiddleName); ?> <?= ucfirst($staff->LastName); ?> <?= ucfirst($staff->NameExtn); ?></td>
                                                    <td><?= $pos->pos_code; ?></td>
                                                    <td><?= $staff->empEmail; ?></td>
                                                    <td class="text-center">
                                                        <?php if($row->wap){ ?>
                                                        <a target="_blank" href="<?= base_url(); ?>uploads/hrdfile/<?= $row->wap; ?>"><i class="far fa-file-pdf  text-purple"></i></a>
                                                        <?php } ?>
                                                    </td>
                                                    <td class="text-center">
                                                        <?php if($row->mov){ ?>
                                                        <a target="_blank" href="<?= base_url(); ?>uploads/hrdfile/<?= $row->mov; ?>"><i class="far fa-file-pdf  text-primary"></i></a>
                                                        <?php } ?>
                                                    </td>
                                                    <td class="text-center">
                                                        <?php if(empty($row->mov) || empty($row->wap)){ ?>
                                                        <a onclick="return confirm('Are you sure?')" class="text-danger tooltips" data-placement="top" data-toggle="tooltip" data-original-title="Delete" href="<?= base_url(); ?>Hrtd/participant_delete/<?= $row->id; ?>"><i class="far fa-trash-alt"></i></a>
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



                            

   

 