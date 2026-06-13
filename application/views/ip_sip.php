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
                                <?php if($this->session->position != "Admin"){ ?>
                                    <button type="button" class="btn btn-primary waves-effect waves-light" data-toggle="modal" data-backdrop="static" data-keyboard="false" data-target=".bs-example-modal-lg">Add New</button>
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
                                    <h4 class="header-title mb-4">SCHOOL IMPROVEMENT PLAN (SIP)</h4><br />
                                    <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                        <thead>
                                            <tr>
                                                <th>Covered Year</th>
                                                <th>File</th>
                                                <th style='text-align:center'>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                               <?php
										  $i=1;
										  foreach($data as $row)
										  {
										  echo "<tr>";
										  echo "<td>".$row->coverage ."</td>";
										  echo "<td>".$row->fileAttachment."</td>";
                                          ?>
                                          <td style='text-align:center'>
                                                        <a href="<?= base_url(); ?>uploads/sip_files/<?= $row->fileAttachment; ?>" target="_blank" class="text-success"><i class="mdi mdi-file-document-box-check-outline"></i> View File </a>&nbsp;&nbsp;&nbsp;&nbsp;
                                                        <a href="<?= base_url(); ?>Pages/del_sip/<?= $row->id; ?>" onclick="return confirm('Are you sure?');" class="text-danger"><i class="mdi mdi-file-document-box-check-outline"></i> Delete </a>&nbsp;&nbsp;&nbsp;&nbsp;
                                          </td>

										  <?php echo "</tr>";
									  
															}
										   ?>
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

                <?php include('templates/footer.php'); ?>    
                
<!-- Modal for File Uploading -->
<div class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" style="display: none;" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="myLargeModalLabel">School Improvement Plan</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                            </div>
                            <div class="modal-body">
                                <?= form_open_multipart('Pages/sip'); ?>
                                    <div class="form-group row">
                                        <label for="inputEmail3" class="col-md-3 col-form-label">Year</label>
                                        <div class="col-md-9">
                                            <input type="text" class="form-control" name="coverage" placeholder="2023-2025">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="inputPassword5" class="col-md-3 col-form-label">File Attachment</label>
                                        <div class="col-md-9">
                                            <input type="file" class="form-control" name="attachment">
                                        </div>
                                    </div>

                                    <div class="form-group mb-0 justify-content-end row">
                                        <div class="col-md-9">
                                            <input type="submit" name="submit" value="Submit" class="btn btn-info waves-effect waves-light">
                                            <!-- <button type="submit" class="btn btn-info waves-effect waves-light">Submit</button> -->
                                        </div>
                                    </div>
                                </form>

                            </div>
                        </div>
                        <!-- /.modal-content -->
                    </div>
                    <!-- /.modal-dialog -->
                </div>
                <!-- /.modal -->

             
 