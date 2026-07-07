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
										  foreach($data as $row)
										  {
										  ?>
                                          <tr>
                                              <td class="align-middle"><?= htmlspecialchars($row->coverage); ?></td>
                                              <td class="align-middle">
                                                  <a href="<?= base_url(); ?>uploads/sip_files/<?= rawurlencode($row->fileAttachment); ?>" target="_blank" class="text-danger" title="<?= htmlspecialchars($row->fileAttachment); ?>">
                                                      <i class="mdi mdi-file-pdf-box"></i> <?= htmlspecialchars($row->fileAttachment); ?>
                                                  </a>
                                              </td>
                                              <td class="text-center align-middle text-nowrap">
                                                  <a href="<?= base_url(); ?>uploads/sip_files/<?= rawurlencode($row->fileAttachment); ?>" target="_blank" class="btn btn-sm btn-outline-success">
                                                      <i class="mdi mdi-eye-outline"></i> View
                                                  </a>
                                                  <a href="<?= base_url(); ?>Pages/del_sip/<?= $row->id; ?>" onclick="return confirm('Are you sure you want to delete this file?');" class="btn btn-sm btn-outline-danger">
                                                      <i class="mdi mdi-trash-can-outline"></i> Delete
                                                  </a>
                                              </td>
                                          </tr>
										  <?php
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
                                        <label for="inputEmail3" class="col-md-3 col-form-label">Covered Year <span class="text-danger">*</span></label>
                                        <div class="col-md-9">
                                            <input type="text" class="form-control" name="coverage" required>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="inputPassword5" class="col-md-3 col-form-label">File Attachment <span class="text-danger">*</span></label>
                                        <div class="col-md-9">
                                            <input type="file" class="form-control" name="attachment" accept="application/pdf,.pdf" required>
                                            <small class="form-text text-muted">Only PDF files are allowed.</small>
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

             
 