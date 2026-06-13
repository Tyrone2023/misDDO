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
                                        
                                        <div class="page-title-right">
                                            <ol class="breadcrumb p-0 m-0">
                                                <li class="breadcrumb-item"><a href="<?= base_url(); ?>Page/jobVacancy">Job Vacancies</a></li>
                                                <li class="breadcrumb-item active">List of Applicants</li>
                                            </ol>
                                        </div>

                                    <div class="clearfix"></div>
                                </div>
                            </div>
                        </div>
                        <!-- end page title -->
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
                                

                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body table-responsive">
                                    <h4 class="header-title mb-4">List of Applicants<br /><span class="float-left badge badge-primary inline mt-2"><?php echo $_GET['jobTitle']; ?></span></h4><br />
                                        <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                        <!-- <table id="datatable-buttons" class="table table-striped table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;"> -->
                                        <thead>
                                            <tr>
                                                <th>Last Name</th>
												<th>First Name</th>
												<th>Middle Name</th>
                                                <th>Applicant No.</th>
                                                <th>Date Submitted</th>
                                                <th>Applicant Status</th>
                                                <th style="text-align:center">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                               <?php foreach($data as $row){ 
                                                //  $b = $this->Common->one_cond_row('hris_applicant','empEmail',$row->empEmail); 
                                                //  if(!empty($b)){
                                                //      $renren = $this->Common->one_cond_row('hris_applicant','empEmail',$row->empEmail);
                                                //      $record = $renren->record_no;
                                                //      $user='ma';
                                                //      $id=$renren->id;
                                                //  }else{
                                                //      $renren = $this->Common->one_cond_row('hris_staff','IDNumber',$row->empEmail);
                                                //      $record = $renren->IDNumber;
                                                //      $user='ma_staff';
                                                //      $id=$renren->IDNumber;
                                                //  }

                                                //  $rating = $this->Common->two_cond_row('hris_applications_rating','record_no',$id,'appID',$row->appID);
                                                //  $notify = $this->Common->four_cond_count_row('hris_applications_track', 'jobID',$row->jobID,'applicant_id',$row->applicant_id,'nstat',0,'res',$row->empEmail);

                                                //  if(!empty($rating)){
                                                //     $renrenguapo = $rating->demo_rating;
                                                //     $renrenguapoko = $rating->tr_rating;
                                                //     $ren = $this->Common->one_cond_row('hris_applicant','empEmail',$row->empEmail);
                                                //     if(!empty($ren)){
                                                //         $renren = $this->Common->one_cond_row('hris_applicant','empEmail',$row->empEmail);
                                                //         $record = $renren->record_no;
                                                //         $id=$renren->id;
                                                //         $user='ma';
                                                //     }else{
                                                //         $renren = $this->Common->one_cond_row('hris_staff','IDNumber',$row->empEmail);
                                                //         $record = $renren->IDNumber;
                                                //         $id=$renren->IDNumber;
                                                //         $user='ma_staff';
                                                //     }
                                                //}

                                               ?>

                                                <tr>
                                                    <td><?= strtoupper($row->LastName); ?></td>
                                                    <td><?= strtoupper($row->FirstName); ?></td>
                                                    <td><?= strtoupper($row->MiddleName); ?></td>
                                                    <td><?= $row->code; ?></td>
                                                    <td><?= $row->dateSubmitted; ?></td>
                                                    <td><span class="badge badge-<?php 
                                                        if($row->appStatus == 'Validated'){echo "info";
                                                        }elseif($row->appStatus == 'Application Submitted'){echo 'primary';
                                                        }elseif($row->appStatus == 'Endorsed for Rating'){echo 'warning';}
                                                        
                                                        ?> badge-pill"><?= $row->appStatus; ?></span>
                                                    </td>
                                          
                                            <?php  //$ca = $this->Common->two_cond_count_row('hris_rating_none', 'record_no', $record,'appID',date('Y')); ?>
                                            <td style="text-align:center">
                                                
                                               <a href="<?= base_url(); ?>pages/<?= $row->st; ?>/<?= $row->id; ?>/<?= $row->jobID; ?>/<?= $row->pre_school; ?>" target="_blank" class="btn btn-info tooltips" data-placement="top" data-toggle="tooltip" data-original-title="View Applicant Information"><i class="fas fa-file-alt"></i></a>&nbsp;&nbsp;
                                               <?php 
                                                $rate = $this->Common->two_cond_row('hris_applications_rating', 'appID', $row->appID,'record_no',$row->code); 
                                                if(!empty($rate)){
                                               ?>
                                               <a href="<?= base_url(); ?>Pages/ies/<?= $row->id;?>/<?= $row->appID; ?>/<?= $row->jobID; ?>" target="_blank" class="btn btn-success tooltips" data-placement="top" data-toggle="tooltip" data-original-title="View IES"><i class=" mdi mdi-xbox-controller-view"></i></button></a> &nbsp; 
                                               <?php } ?>

                                               <?php if($row->ren == 1){echo '<span class="text-danger">&#33;</span>';} ?>

                                               <?php if($row->stat == 0){ ?>
                                                    <a href="<?=base_url(); ?>Pages/close_applications/<?= $row->jobID; ?>/<?= $row->code; ?>/?ee=<?= $row->empEmail; ?>&jt=<?= $this->input->get('jobTitle'); ?>" class="text-primary tooltips" data-placement="top" data-toggle="tooltip" data-original-title="Lock Applications"><i class="mdi mdi-pencil-lock-outline btn btn-warning"></i></a>&nbsp;&nbsp;&nbsp;&nbsp;
                                                <?php }else{ ?>
                                                    <a href="<?=base_url(); ?>Pages/open_applications/<?= $row->jobID; ?>/<?= $row->code; ?>/?ee=<?= $row->empEmail; ?>&jt=<?= $this->input->get('jobTitle'); ?>" class="text-primary tooltips" data-placement="top" data-toggle="tooltip" data-original-title="Unlock Applications"><i class="mdi mdi-account-edit btn btn-primary"></i></a>&nbsp;&nbsp;&nbsp;&nbsp;
                                                <?php } ?>

                                                <?php if($this->session->username == 'asdsv2' || $this->session->username == 'Cyanne25'){?>
                                                <a class="btn btn-danger" onclick="return confirm('Are you sure?')" href="<?=base_url(); ?>Pages/ap_cancel_hr/<?= $row->appID; ?>"><i class="fas fa-times tooltips" data-placement="top" data-toggle="tooltip" data-original-title="Cancel Application"></i></a>&nbsp;&nbsp;
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


                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header bg-info py-3 text-white">
                                        <div class="card-widgets">
                                            <a href="javascript:;" data-toggle="reload"><i class="mdi mdi-refresh"></i></a>
                                            <a data-toggle="collapse" href="#cardCollpase3" role="button" aria-expanded="false" aria-controls="cardCollpase2"><i class="mdi mdi-minus"></i></a>
                                            <a href="#" data-toggle="remove"><i class="mdi mdi-close"></i></a>
                                        </div>
                                        <h5 class="card-title mb-0 text-white">SUMMARY</h5>

                                        
                                    </div>
                                    <div id="cardCollpase3" class="collapse show">
                                        <div class="card-body">
                                        <table class="table table-striped mb-0">
                                        <thead>
                                            <tr>
                                                <th>City/Municipality</th>
												<th style="text-align:center">Applicant Counts</th>
                                                <th style="text-align:center">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                               <?php
										  $i=1;
										  foreach($data1 as $row)
										  {
										  echo "<tr>";
										  echo "<td style='text-transform: uppercase'>".$row->perCity."</td>";
										  echo "<td style='text-align:center'>".$row->cityCounts."</td>";
										 
                                         
                                          ?>
                                            <td style="text-align:center"><a href="appPerMun?mun=<?php echo $row->perCity; ?>&jobID=<?php echo $row->jobID; ?>">
                                            
                                                    <i class=" mdi mdi-xbox-controller-view"></i>View List</a>
                                                     
                                        </td>
										  <?php echo "</tr>";	} ?>
                                        </tbody>

                                        </table>
                                        </div>
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

                <?php include('templates/footer.php'); ?>       

             
 <!-- Modal for  -->
<div id="appStatus" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="myModalLabel">Update Application Status</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                    </div>
                                                    <div class="modal-body">
                                                    <!-- <?= form_open('Pages/'); ?> -->
                                                <form method="post">
                                                    <div class="modal-body">
                                                        <div class="form-group col-md-12">
                                                            <label>Application Status</label>
                                                            <input type="text"  name="appStatus" class="form-control" value="" />
                                                        </div>

                                                        <div class="form-group col-md-12">
                                                            <label>Notes</label>
                                                            <textarea class="form-control" rows="3" name="note"></textarea>
                                                        </div>
                                                        
                                                        
                                                            <input type="hidden" name="jobID" id="id" value="">
                                                            <input type="hidden"  id="field" name="empEmail" class="form-control" value="" />
                                                        </div>    

                                                    </div>
                                                    <div class="modal-footer">
                                                        <input type="submit" name="submit" class="btn btn-primary waves-effect waves-light" value="Submit" />
                                                    </div>
                                                    </form>
                                                </div>
                                                <!-- /.modal-content -->
                                            </div>
                                            <!-- /.modal-dialog -->
                                        </div>

<script type="text/javascript">
                $(document).on("click", ".open-AddBookDialog", function () {
                    var myBookId = $(this).data('id');
                    $(".modal-body #id").val( myBookId );

                    var fieldId = $(this).data('field');
                    $(".modal-body #field").val( fieldId );
                });
            </script>

