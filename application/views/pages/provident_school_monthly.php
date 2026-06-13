

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
                                    <h4 class="page-title"><?= $title; ?></h4>
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                        </div>
                        <!-- end page title -->

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="card">
                                    <div class="card-body">
                                        <h4 class="header-title mb-4">Implementing Schools</h4>

                                        <div class="table-responsive">
                                            <table class="table table-hover table-bordered mb-0">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>School</th>
                                                        <th class="text-center">Loans Count</th>
                                                        <th class="text-center">Payment Count</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php $ivy=1; foreach($school as $row){
                                                        $loans = $this->Common->two_cond_count_row('provident_implementing','stat',0,'school_id',$row->school_id);
                                                        $loan_payment = $this->Common->two_cond_count_row('provident_implementing_payment','fy',$this->session->cur_fy,'school_id',$row->school_id,'month',date('m'));
                                                        ?>
                                                    <tr>
                                                        <th scope="row"><?= $ivy++; ?></th>
                                                        <td><a href="<?= base_url(); ?>Provident/implementing_school_paymentv2/<?= $this->session->cur_month; ?>/<?= $this->session->cur_fy; ?>/<?= $row->school_id; ?>"><?= $row->name; ?></a></td>
                                                        <td class="text-center"><span class="badge badge-success"><?= $loans->num_rows(); ?></td>
                                                        <td class="text-center">
                                                            <?php
                                                                $loansCount = $loans->num_rows();
                                                                $payCount   = $loan_payment->num_rows();

                                                                if ($loansCount != $payCount) {?>
                                                                    <span class="badge badge-danger"><?= $loan_payment->num_rows(); ?></span>
                                                                <?php }else{ ?> 
                                                                <span class="badge badge-info"><?= $loan_payment->num_rows(); ?></span>
                                                                <?php }?>
                                                        </td>
                                                    </tr>
                                                    <?php } ?>
                                                </tbody>
                                            </table>
                                        </div>

                                    </div>
                                </div>

                            </div>
                            
                        </div>
                        <!-- end row -->

                        

                        
                    


                    </div>
                    <!-- end container-fluid -->

                </div>
                <!-- end content -->

                

                