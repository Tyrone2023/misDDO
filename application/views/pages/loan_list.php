

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
                                                    <th>#</th>
                                                    <th>Employee No.</th>
                                                    <th>Fullname</th>
                                                    <th>Principal Amount</th>
                                                    <th>Deduction Amount</th>
                                                    <th>Effect Year From</th>
                                                    <th>Effect Year To</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>

                                            <tbody>
                                                <?php $c=1; foreach($data as $row){?>
                                                <tr>
                                                    <td><?= $c++; ?></td>
                                                    <td><?= $row->IDNumber; ?></td>
                                                    <td><?= $row->lName; ?>, <?= $row->fName; ?> <?= $row->mName; ?></td>
                                                    <td><?= number_format($row->principalAmount); ?></td>
                                                    <td><?= number_format($row->dedAmount); ?></td>
                                                    <td><?= $row->effectYearFrom; ?></td>
                                                    <td><?= $row->effectYearTo; ?></td>
                                                    <td class="text-center">
                                                        <a class="text-success tooltips" data-placement="top" data-toggle="tooltip" data-original-title="View Ledger" href="<?= base_url(); ?>Provident/loan_ledger/<?= $row->IDNumber; ?>/<?= $row->dedID; ?>"><i class="fas fa-file-invoice "></i></a> &nbsp; &nbsp;
                                                        <a class="text-primary tooltips" data-placement="top" data-toggle="tooltip" data-original-title="Whole Term Ledger " href="<?= base_url(); ?>Provident/loan_ledger_full_term/<?= $row->IDNumber; ?>/<?= $row->dedID; ?>"><i class="fas fa-file-invoice "></i></a> &nbsp; &nbsp;
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

                            

   

 