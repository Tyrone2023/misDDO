

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
                                   <a href="<?= base_url(); ?>Coor/coor_entry_view/<?= $this->uri->segment(6); ?>" class="btn btn-info waves-effect waves-light"><i class="fas fa-arrow-alt-circle-left "></i> Back Report Entry</a>
                                    <?php } ?>
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

                                         <table class="table table-bordered mb-0">

                                            <thead>
                                                <tr>
                                                    <th>Section</th>
                                                    <th>Average MPS</th>
                                                    <th class="text-center">No. of Learners<br /> with 75% MPS<br /> and Above</th>
                                                    <th>Total No. of Learners</th>
                                                    <th>AVE MPS * No. of Learners</th>
                                                    <?php if($this->session->position == 'user'){?>
                                                    <th>Action</th>
                                                    <?php } ?>
                                                </tr>
                                            </thead>

                                            <tbody>
                                                
                                                <?php 
                                                    $total_mps = 0;
                                                    $total_abovemps = 0;
                                                    $total_learners = 0;
                                                    $total_total = 0;
                                                    $count = 0; // Count number of rows

                                                    foreach($data as $row){
                                                        $total_mps += (float) $row->mps;
                                                        $total_abovemps += (int) $row->abovemps;
                                                        $total_learners += (int) $row->learners;
                                                        $total_total += (int) $row->total;
                                                        $count++;
                                                ?>
                                                    <tr>
                                                        <td><?= $row->section; ?></td>
                                                        <td class="text-center"><span class="badge badge-info"><?= $row->mps; ?></span></td>
                                                        <td class="text-center"><span class="badge badge-primary"><?= $row->abovemps; ?></span></td>
                                                        <td class="text-center"><span class="badge badge-purple"><?= $row->learners; ?></span></td>
                                                        <td class="text-center"><span class="badge badge-success"><?= $row->total; ?></span></td>
                                                        <?php if($this->session->position == 'user'){?>
                                                        <td class="text-center"><a onclick="return confirm('Are you sure?')" href="<?= base_url(); ?>Coor/cid_coor_delete_entry/<?= $row->id; ?>/<?= $this->uri->segment(3); ?>/<?= $this->uri->segment(4); ?>"><i class="fas fa-trash-alt text-danger"></i></a></td>
                                                        <?php } ?>
                                                    </tr>
                                                <?php } ?>

                                                <!-- TOTAL row -->
                                                <tr>
                                                    <th class="text-center text-dark">TOTAL</th>
                                                    <th class="text-center text-dark">
                                                        <?= ($count > 0) ? round($total_mps / $count, 2) : '0.00' ?>
                                                    </th>
                                                    <th class="text-center text-dark"><?= $total_abovemps; ?></th>
                                                    <th class="text-center text-dark"><?= $total_learners; ?></th>
                                                    <th class="text-center text-dark"><?= $total_total; ?></th>
                                                    <th></th>
                                                </tr>
                                                <?php if($total_learners != 0){?>
                                                <tr>
                                                    <td>PROFICIENCY</td>
                                                    <td><span class="badge badge-info"><?= number_format($total_total/$total_learners, 2); ?></span></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                </tr>
                                                <tr>
                                                    <td>PERCENTAGE OF LEARNERS WITH ATLEAST 75%</td>
                                                    <td><span class="badge badge-info"><?= number_format(($total_abovemps/$total_learners)*100, 2); ?></span></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
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

   

 