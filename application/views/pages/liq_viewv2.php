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
                                    <!-- <a href="<?= base_url(); ?>Page/liq_view/<?= $this->uri->segment(3); ?>/<?= $this->uri->segment(4); ?>/<?= $this->uri->segment(5); ?>/<?= $this->uri->segment(6); ?>/<?= $this->uri->segment(7); ?>" class="btn btn-success">Add New</a> -->
                                <div class="clearfix"></div>
                                </div>
                            </div>
                        </div>
                        <!-- end page title -->

                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body table-responsive">
                                    <!-- <button type="button" style="float: right;" class="btn btn-success btn-rounded waves-effect width-md waves-light">
                                        <a href="<?= site_url('Page/printEmployeelistv3'); ?>" target="_blank">
                                            <strong style="color: white;"><i class="mdi mdi-printer"></i>Print Preview</strong>
                                        </a>
                                    </button> -->
                                        <!-- <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;"> -->
                                        
                                        
                                        <table id="datatable-buttons" class="table table-striped table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                            <tr>
                                                <th>Quantity</th>
                                                <th>Unit Of Measure</th>
                                                <th>Item Description</th>
                                                <th>Stock No.</th>
                                                <th>Estimated Unit Cost</th>
                                                <th>Estimated Cost </th>
                                                <th class="text-center">Manage</th>
                                            </tr>

                                            
                                            <tr>
                                                <td colspan="6" class="alignLeft2">I. MANDATORY BILLS</td>
                                                <td></td>
                                            </tr>
                                            <?php $tmb = 0;
                                                foreach($mb as $row){?>
                                                <tr>
                                                    <td><?= $row->qty; ?></td>
                                                    <td><?= $row->unit_mesure; ?></td>
                                                    <td><?= $row->item_des; ?></td>
                                                    <td></td>
                                                    <td><?= $row->cost; ?></td>
                                                    <td><?php $mb_ups = (double)$row->qty*(double)$row->cost; echo  number_format($mb_ups); ?></td>
                                                    <td class="text-center">
                                                        <a href="<?= base_url(); ?>Page/liq_update/<?= $row->id; ?>/<?= $this->uri->segment(7); ?>" class="btn sm <?php if(empty($row->acc_name)){echo 'btn-primary';}else{echo "btn-warning";}?>">Update</a>
                                                    </td>
                                                </tr>
                                                    <?php $tmb+=$mb_ups; ?>
                                                
                                            <?php  }  ?>


                                            <tr>
                                                <td colspan="6" class="alignLeft2">  II. MINOR REPAIR</td>
                                                <td></td>
                                            </tr>
                                            <?php 
                                                $tmr=0;
                                                foreach($mr as $row){?>
                                                <tr>
                                                    <td><?= $row->qty; ?></td>
                                                    <td><?= $row->unit_mesure; ?></td>
                                                    <td><?= $row->item_des; ?></td>
                                                    <td></td>
                                                    <td><?= $row->cost; ?></td>
                                                    <td><?php $mb_ups = (double)$row->qty*(double)$row->cost; echo  number_format($mb_ups); ?></td>
                                                    <td class="text-center">
                                                    <a href="<?= base_url(); ?>Page/liq_update/<?= $row->id; ?>/<?= $this->uri->segment(7); ?>" class="btn sm <?php if(empty($row->acc_name)){echo 'btn-primary';}else{echo "btn-warning";}?>">Update</a>
                                                    </td>
                                                </tr>
                                                <?php $tmr+=$mb_ups; ?>
                                                
                                            <?php  }  ?>

                                            <tr>
                                                <td colspan="6" class="alignLeft2"> III. TEACHING-LEARNING INSTRUCTION</td>
                                                <td></td>
                                            </tr>
                                            <?php 
                                                $ttli=0;
                                                foreach($tli as $row){?>
                                                <tr>
                                                    <td><?= $row->qty; ?></td>
                                                    <td><?= $row->unit_mesure; ?></td>
                                                    <td><?= $row->item_des; ?></td>
                                                    <td></td>
                                                    <td><?= $row->cost; ?></td>
                                                    <td><?php $mb_ups = (double)$row->qty*(double)$row->cost; echo  number_format($mb_ups); ?></td>
                                                    <td class="text-center">
                                                    <a href="<?= base_url(); ?>Page/liq_update/<?= $row->id; ?>/<?= $this->uri->segment(7); ?>" class="btn sm <?php if(empty($row->acc_name)){echo 'btn-primary';}else{echo "btn-warning";}?>">Update</a>
                                                    </td>
                                                </tr>
                                                <?php $ttli+=$mb_ups; ?>
                                                
                                            <?php  }  ?>
                                            <tr>
                                                <td colspan="6" class="alignLeft2">IV.TRAININGS/SEMINAR/TRAVEL</td>
                                                <td></td>
                                            </tr>
                                            <?php 
                                                $ttst=0;
                                                foreach($tst as $row){?>
                                                <tr>
                                                    <td><?= $row->qty; ?></td>
                                                    <td><?= $row->unit_mesure; ?></td>
                                                    <td><?= $row->item_des; ?></td>
                                                    <td></td>
                                                    <td><?= $row->cost; ?></td>
                                                    <td><?php $mb_ups = (double)$row->qty*(double)$row->cost; echo  number_format($mb_ups); ?></td>
                                                    <td class="text-center">
                                                    <a href="<?= base_url(); ?>Page/liq_update/<?= $row->id; ?>/<?= $this->uri->segment(7); ?>" class="btn sm <?php if(empty($row->acc_name)){echo 'btn-primary';}else{echo "btn-warning";}?>">Update</a>
                                                    </td>
                                                </tr>
                                                    <?php $ttst+=$mb_ups; ?>
                                                
                                            <?php  }  ?>

                                            <tr>
                                                <td>
                                                    <?= number_format($tmb+$tmr+$ttli+$ttst); ?>
                                                </td>
                                            </tr>
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

             
 