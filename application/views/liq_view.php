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
                                        
                                        <a href="<?= base_url(); ?>Page/liquidation_insert/<?= $this->uri->segment(6); ?>/<?= $this->uri->segment(7); ?>/<?= $this->uri->segment(5); ?>" class="btn btn-success">Save</a>
                                        
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
                                            </tr>

                                            
                                            <tr>
                                                <td colspan="5" class="alignLeft2">I. MANDATORY BILLS</td>
                                                <td></td>
                                            </tr>
                                            <?php $tmb = 0;
                                                foreach($mb as $row){?>
                                                <?php 
                                                $get_app = $this->SGODModel->two_cond_rca('sgod_app', 'aip_id',$row->id,$mon);
                                                
                                                foreach($get_app as $row){?>
                                                <?php 
                                                    if($mon == 'jan'){
                                                        $mq = $row->qjan;
                                                        $m = $row->jan;
                                                    }elseif($mon == 'feb'){
                                                        $mq = $row->qfeb;
                                                        $m = $row->feb;
                                                    }elseif($mon == 'mar'){
                                                        $mq = $row->qmar;
                                                        $m = $row->mar;
                                                    }elseif($mon == 'april'){
                                                        $mq = $row->qapril;
                                                        $m = $row->april;
                                                    }elseif($mon == 'may'){
                                                        $mq = $row->qmay;
                                                        $m = $row->may;
                                                    }elseif($mon == 'june'){
                                                        $mq = $row->qjune;
                                                        $m = $row->june;
                                                    }elseif($mon == 'july'){
                                                        $mq = $row->qjuly;
                                                        $m = $row->july;
                                                    }elseif($mon == 'aug'){
                                                        $mq = $row->qaug;
                                                        $m = $row->aug;
                                                    }elseif($mon == 'sept'){
                                                        $mq = $row->qsept;
                                                        $m = $row->sept;
                                                    }elseif($mon == 'oct'){
                                                        $mq = $row->qoct;
                                                        $m = $row->oct;
                                                    }elseif($mon == 'nov'){
                                                        $mq = $row->qnov;
                                                        $m = $row->nov;
                                                    }else{
                                                        $mq = $row->qdec;
                                                        $m = $row->ddec;
                                                    }
                                                    
                                                    ?>

                                                <tr>
                                                    <td><?= $mq; ?></td>
                                                    <td><?= $row->unit_measure; ?></td>
                                                    <td><?= $row->materials; ?></td>
                                                    <td></td>
                                                    <td><?= number_format($m); ?></td>
                                                    <td><?php $mb_ups = (double)$m*(double)$mq; echo  number_format($mb_ups); ?></td>
                                                    
                                                </tr>

                                                <?php $tmb+=$mb_ups;?>
                                                <?php  } ?>
                                                
                                            <?php  }  ?>


                                            <tr>
                                                <td colspan="5" class="alignLeft2">  II. MINOR REPAIR</td>
                                                <td></td>
                                            </tr>
                                            <?php $tmr = 0;
                                                foreach($mr as $row){?>
                                                <?php 
                                                $get_app = $this->SGODModel->two_cond_rca('sgod_app', 'aip_id',$row->id,$mon);
                                                
                                                foreach($get_app as $row){?>
                                                <?php 
                                                    if($mon == 'jan'){
                                                        $mq = $row->qjan;
                                                        $m = $row->jan;
                                                    }elseif($mon == 'feb'){
                                                        $mq = $row->qfeb;
                                                        $m = $row->feb;
                                                    }elseif($mon == 'mar'){
                                                        $mq = $row->qmar;
                                                        $m = $row->mar;
                                                    }elseif($mon == 'april'){
                                                        $mq = $row->qapril;
                                                        $m = $row->april;
                                                    }elseif($mon == 'may'){
                                                        $mq = $row->qmay;
                                                        $m = $row->may;
                                                    }elseif($mon == 'june'){
                                                        $mq = $row->qjune;
                                                        $m = $row->june;
                                                    }elseif($mon == 'july'){
                                                        $mq = $row->qjuly;
                                                        $m = $row->july;
                                                    }elseif($mon == 'aug'){
                                                        $mq = $row->qaug;
                                                        $m = $row->aug;
                                                    }elseif($mon == 'sept'){
                                                        $mq = $row->qsept;
                                                        $m = $row->sept;
                                                    }elseif($mon == 'oct'){
                                                        $mq = $row->qoct;
                                                        $m = $row->oct;
                                                    }elseif($mon == 'nov'){
                                                        $mq = $row->qnov;
                                                        $m = $row->nov;
                                                    }else{
                                                        $mq = $row->qdec;
                                                        $m = $row->ddec;
                                                    }

                                                    
                                                    ?>
                                                <tr>
                                                    <td><?= $mq; ?></td>
                                                    <td><?= $row->unit_measure; ?></td>
                                                    <td><?= $row->materials; ?></td>
                                                    <td></td>
                                                    <td><?= number_format($m); ?></td>
                                                    <td><?php $mb_ups = (double)$m*(double)$mq; echo  number_format($mb_ups); ?></td>
                                                    
                                                </tr>

                                                <?php $tmr+=$mb_ups;?>
                                                <?php  } ?>
                                                
                                            <?php  }  ?>

                                            <tr>
                                                <td colspan="5" class="alignLeft2"> III. TEACHING-LEARNING INSTRUCTION</td>
                                                <td></td>
                                            </tr>
                                            <?php $ttli = 0;
                                                foreach($tli as $row){?>
                                                <?php 
                                                $get_app = $this->SGODModel->two_cond_rca('sgod_app', 'aip_id',$row->id,$mon);
                                                
                                                foreach($get_app as $row){?>
                                                <?php 
                                                    if($mon == 'jan'){
                                                        $mq = $row->qjan;
                                                        $m = $row->jan;
                                                    }elseif($mon == 'feb'){
                                                        $mq = $row->qfeb;
                                                        $m = $row->feb;
                                                    }elseif($mon == 'mar'){
                                                        $mq = $row->qmar;
                                                        $m = $row->mar;
                                                    }elseif($mon == 'april'){
                                                        $mq = $row->qapril;
                                                        $m = $row->april;
                                                    }elseif($mon == 'may'){
                                                        $mq = $row->qmay;
                                                        $m = $row->may;
                                                    }elseif($mon == 'june'){
                                                        $mq = $row->qjune;
                                                        $m = $row->june;
                                                    }elseif($mon == 'july'){
                                                        $mq = $row->qjuly;
                                                        $m = $row->july;
                                                    }elseif($mon == 'aug'){
                                                        $mq = $row->qaug;
                                                        $m = $row->aug;
                                                    }elseif($mon == 'sept'){
                                                        $mq = $row->qsept;
                                                        $m = $row->sept;
                                                    }elseif($mon == 'oct'){
                                                        $mq = $row->qoct;
                                                        $m = $row->oct;
                                                    }elseif($mon == 'nov'){
                                                        $mq = $row->qnov;
                                                        $m = $row->nov;
                                                    }else{
                                                        $mq = $row->qdec;
                                                        $m = $row->ddec;
                                                    }
                                                    
                                                    ?>
                                                <tr>
                                                    <td><?= $mq; ?></td>
                                                    <td><?= $row->unit_measure; ?></td>
                                                    <td><?= $row->materials; ?></td>
                                                    <td></td>
                                                    <td><?= number_format($m); ?></td>
                                                    <td><?php $mb_ups = (double)$m*(double)$mq; echo  number_format($mb_ups); ?></td>
                                                    
                                                </tr>
                                                <?php $ttli+=$mb_ups;?>
                                                <?php  } ?>
                                                
                                            <?php  }  ?>
                                            <tr>
                                                <td colspan="5" class="alignLeft2">IV.TRAININGS/SEMINAR/TRAVEL</td>
                                                <td></td>
                                            </tr>
                                            <?php $ttst = 0;
                                                foreach($tst as $row){?>
                                                <?php 
                                                $get_app = $this->SGODModel->two_cond_rca('sgod_app', 'aip_id',$row->id,$mon);
                                                
                                                foreach($get_app as $row){?>
                                                <?php 
                                                    if($mon == 'jan'){
                                                        $mq = $row->qjan;
                                                        $m = $row->jan;
                                                    }elseif($mon == 'feb'){
                                                        $mq = $row->qfeb;
                                                        $m = $row->feb;
                                                    }elseif($mon == 'mar'){
                                                        $mq = $row->qmar;
                                                        $m = $row->mar;
                                                    }elseif($mon == 'april'){
                                                        $mq = $row->qapril;
                                                        $m = $row->april;
                                                    }elseif($mon == 'may'){
                                                        $mq = $row->qmay;
                                                        $m = $row->may;
                                                    }elseif($mon == 'june'){
                                                        $mq = $row->qjune;
                                                        $m = $row->june;
                                                    }elseif($mon == 'july'){
                                                        $mq = $row->qjuly;
                                                        $m = $row->july;
                                                    }elseif($mon == 'aug'){
                                                        $mq = $row->qaug;
                                                        $m = $row->aug;
                                                    }elseif($mon == 'sept'){
                                                        $mq = $row->qsept;
                                                        $m = $row->sept;
                                                    }elseif($mon == 'oct'){
                                                        $mq = $row->qoct;
                                                        $m = $row->oct;
                                                    }elseif($mon == 'nov'){
                                                        $mq = $row->qnov;
                                                        $m = $row->nov;
                                                    }else{
                                                        $mq = $row->qdec;
                                                        $m = $row->ddec;
                                                    }
                                                    
                                                    ?>
                                                <tr>
                                                    <td><?= $mq; ?></td>
                                                    <td><?= $row->unit_measure; ?></td>
                                                    <td><?= $row->materials; ?></td>
                                                    <td></td>
                                                    <td><?= number_format($m); ?></td>
                                                    <td><?php $mb_ups = (double)$m*(double)$mq; echo  number_format($mb_ups); ?></td>
                                                    
                                                </tr>
                                                <?php $ttst+=$mb_ups;?>
                                                <?php  } ?>
                                                
                                            <?php  }  ?>

                                            <tr>
                                                <td colspan="5" class="alignLeft">TOTAL</td>
                                                <td><?php $omt = $tmb+$tmr+$ttli+$ttst;  echo number_format($omt); ?></td>
                                                <?php $_SESSION['omt'] = $omt; ?>
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

             
 