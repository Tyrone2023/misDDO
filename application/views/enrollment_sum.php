            <?php include('templates/head.php'); ?>
            <?php include('templates/header.php'); ?>
            <?php $sy =$this->input->get('sy'); ?>

            <!-- ============================================================== -->
            <!-- Start Page Content here -->
            <!-- ============================================================== -->

            <div class="content-page">
                <div class="content">

                    <!-- Start Content-->
                    <div class="container-fluid">

                    
                        <!-- start page title -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="page-title-box">
                                    
                                    <h4 class="page-title">List of Enrolled Students <?= $this->input->get('gl'); ?></h4>

                                    <div class="page-title-right">
                                        <ol class="breadcrumb p-0 m-0">
                                            <!-- <li class="breadcrumb-item"><a href="#"><span class="badge badge-purple mb-3">Currently login to <b>SY <?php echo $this->session->userdata('sy'); ?> <?php echo $this->session->userdata('semester'); ?></span></b></a></li> -->
                                        </ol>
                                    </div>

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
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                        </div>

                        <!-- end page title -->
                        <div class="row">
                            <div class="col-md-12">

                                <div class="card">
                                    <div class="card-body table-responsive">
                                        

                                        <!-- <table id="datatable-buttons" class="table table-striped table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;"> -->
                                            <table class="table table-sm mb-0">                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Student Name</th>
                                                    <th>Student No.</th>
                                                    <th>Section</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            <?php $c=1; foreach ($data as $row) { ?>
                                                <tr>
                                                    <td><?= $c++; ?></td>
                                                    <td class="text-left"><?= $row->LastName; ?>, <?= $row->FirstName; ?> <?= !empty($row->MiddleName) ? substr($row->MiddleName,0,1).'.' : ''; ?></td>
                                                    <td><?= $row->StudentNumber; ?></td>
                                                    <td><?= $row->Section; ?></td>
                                                </tr>
                                                <?php } ?>
                                            </tbody>

                                        </table>

                                    </div>
                                </div>
                            </div>
                        </div>

                        

                        <!-- end page title -->
                        <div class="row">
                            <div class="col-md-12">

                                <div class="card">
                                    <div class="card-body table-responsive">
                                        <h4 class="m-t-0 header-title mb-4">Age Profile</h4>
                                        <?php $sr = $this->Common->count_students_by_age('SY',$sy,'schoolID',$this->session->username,'YearLevel',$this->input->get('gl')); ?>
                                        <?php echo $this->session->flashdata('msg'); ?>
                                        <!-- <table id="datatable-buttons" class="table table-striped table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;"> -->
                                        <table class="table mb-0">                                            <thead>
                                                <tr>
                                                    <th>Age</th>
                                                    <th>Counts</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                               <?php foreach($sr as $row){?>
                                                <tr>
                                                    <td><?= $row->age; ?></td>
                                                    <td><?= $row->total_students; ?></td>
                                                    <td><a href="<?= base_url(); ?>Sbfp/enrollment_age_profile?gl=<?= $row->YearLevel; ?>&sy=<?= $sy; ?>&age=<?= $row->age; ?>" class="btn btn-primary btn-sm">View</a></td>  
                                                </tr>
                                                <?php } ?>
                                            </tbody>

                                        </table>

                                    </div>
                                </div>
                            </div>
                        </div>




                        
                    </div>
                    <!-- end container-fluid -->

                </div>
                <!-- end content -->

                <?php include('templates/footer.php'); ?>

