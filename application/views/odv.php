

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
                                    <button type="button" class="btn btn-primary waves-effect waves-light" data-toggle="modal" data-target="#dv">Add New Entry</button>
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                        </div>
					 
                        <!-- end page title -->
						<div class="row">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-body table-responsive">
                                        <h4 class="m-t-0 header-title mb-4"><b>Document List</b></h4>
										
										<table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
										<thead>
                                            <tr>
											<th>Fullname</th>
                                            <th>Type</th>
                                            <th>Description</th>
                                            <th>Document No</th>
                                            <th>Date Released</th>
                                            <th class="text-center">QR Code</th>
											</tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach($data as $row){ ?>
                                            <tr>
                                                <td><?= $row->name; ?></td>
                                                <td><?= $row->doc_type; ?></td>
                                                <td><?= $row->doc_des; ?></td>
                                                <td><?= $row->doc_no; ?></td>
                                                <td><?= $row->rdate; ?></td>
                                                <td class="text-center"><a href="<?= base_url(); ?>Page/document_verifier_qr/<?= $row->id; ?>" target="_blank"><i class="mdi mdi-qrcode-scan tooltips text-info" data-placement="top" data-toggle="tooltip" data-original-title="View QR Code"></i></a></td>
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

                

                <!-- Footer Start -->
					<?php include('includes/footer.php'); ?>
                <!-- end Footer -->

            </div>

            <!-- ============================================================== -->
            <!-- End Page content -->
            <!-- ============================================================== -->

        </div>
        <!-- END wrapper -->

        <!--  Modal content for the above example -->
        <div class="modal fade" id="dv" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" style="display: none;" aria-hidden="true">
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="myLargeModalLabel">Document Verifier Entry Form</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                    </div>
                                                    <div class="modal-body">
                                                       
                                                    <div class="row">
                            

                                                        <div class="col-lg-12">
                                                            <div class="card">
                                                                <div class="card-body">

                                                                <?php $att = array('class' => 'parsley-examples'); ?>
                                                                <?= form_open('Page/document_verifier_add', $att); ?>
                                                                        <div class="form-group row">
                                                                            <label for="inputEmail3" class="col-md-4 col-form-label">Complete Name:<span class="text-danger">*</span></label>
                                                                            <div class="col-md-7">
                                                                                <input type="text" class="form-control" name="name"  placeholder="Complete Name">
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group row">
                                                                            <label for="hori-pass1" class="col-md-4 col-form-label">Document Type:<span class="text-danger">*</span></label>
                                                                            <div class="col-md-7">
                                                                                <select class="form-control" required name="doc_type">
                                                                                    <option></option>
                                                                                    <option value="1">Certificate of Completion</option>
                                                                                </select>
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group row">
                                                                            <label for="hori-pass1" class="col-md-4 col-form-label">Description:<span class="text-danger">*</span></label>
                                                                            <div class="col-md-7">
                                                                                <textarea class="form-control" rows="5" required id="example-textarea" name="doc_des"></textarea>
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group row">
                                                                            <label for="hori-pass1" class="col-md-4 col-form-label">Document No.:<span class="text-danger">*</span></label>
                                                                            <div class="col-md-7">
                                                                            <input type="text" class="form-control"  placeholder="Document No" name="doc_no" required>
                                                                            </div>
                                                                        </div>

                                                                        <div class="form-group row">
                                                                            <label for="hori-pass1" class="col-md-4 col-form-label">Date Released:<span class="text-danger">*</span></label>
                                                                            <div class="col-md-7">
                                                                            <input type="date" class="form-control" name="rdate"  placeholder="Date Released" required>
                                                                            </div>
                                                                        </div>
                                                                        
                                                                       
                                                                        <div class="form-group row mb-0">
                                                                            <div class="col-md-8 offset-md-4">
                                                                                <button type="submit" name="submit" class="btn btn-primary waves-effect waves-light mr-1">
                                                                                    Submit
                                                                                </button>
                                                                            </div>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                            <!-- end card -->
                                                        </div>
                                                        <!-- end col -->
                                                    </div>
                                                    <!-- end row -->


                                                    </div>
                                                </div>
                                                <!-- /.modal-content -->
                                            </div>
                                            <!-- /.modal-dialog -->
                                        </div>
                                        <!-- /.modal -->

        

        <!-- Vendor js -->
        <script src="<?= base_url(); ?>assets/js/vendor.min.js"></script>

        <script src="<?= base_url(); ?>assets/libs/moment/moment.min.js"></script>
        <script src="<?= base_url(); ?>assets/libs/jquery-scrollto/jquery.scrollTo.min.js"></script>
        <script src="<?= base_url(); ?>assets/libs/sweetalert2/sweetalert2.min.js"></script>

        <!-- Chat app -->
        <script src="<?= base_url(); ?>assets/js/pages/jquery.chat.js"></script>

        <!-- Todo app -->
        <script src="<?= base_url(); ?>assets/js/pages/jquery.todo.js"></script>

        <!--Morris Chart-->
        <script src="<?= base_url(); ?>assets/libs/morris-js/morris.min.js"></script>
        <script src="<?= base_url(); ?>assets/libs/raphael/raphael.min.js"></script>

        <!-- Sparkline charts -->
        <script src="<?= base_url(); ?>assets/libs/jquery-sparkline/jquery.sparkline.min.js"></script>

        <!-- Dashboard init JS -->
        <script src="<?= base_url(); ?>assets/js/pages/dashboard.init.js"></script>

        <!-- App js -->
        <script src="<?= base_url(); ?>assets/js/app.min.js"></script>

        <!-- Required datatable js -->
        <script src="<?= base_url(); ?>assets/libs/datatables/jquery.dataTables.min.js"></script>
        <script src="<?= base_url(); ?>assets/libs/datatables/dataTables.bootstrap4.min.js"></script>
        <!-- Buttons examples -->
        <script src="<?= base_url(); ?>assets/libs/datatables/dataTables.buttons.min.js"></script>
        <script src="<?= base_url(); ?>assets/libs/datatables/buttons.bootstrap4.min.js"></script>
        <script src="<?= base_url(); ?>assets/libs/jszip/jszip.min.js"></script>
        <script src="<?= base_url(); ?>assets/libs/pdfmake/pdfmake.min.js"></script>
        <script src="<?= base_url(); ?>assets/libs/pdfmake/vfs_fonts.js"></script>
        <script src="<?= base_url(); ?>assets/libs/datatables/buttons.html5.min.js"></script>
        <script src="<?= base_url(); ?>assets/libs/datatables/buttons.print.min.js"></script>
		<script src="<?= base_url(); ?>assets/libs/bootstrap-datepicker/bootstrap-datepicker.min.js"></script>
        <!-- Responsive examples -->
        <script src="<?= base_url(); ?>assets/libs/datatables/dataTables.responsive.min.js"></script>
        <script src="<?= base_url(); ?>assets/libs/datatables/responsive.bootstrap4.min.js"></script>

        <script src="<?= base_url(); ?>assets/libs/datatables/dataTables.keyTable.min.js"></script>
        <script src="<?= base_url(); ?>assets/libs/datatables/dataTables.select.min.js"></script>

        <!-- Datatables init -->
        <script src="<?= base_url(); ?>assets/js/pages/datatables.init.js"></script>
		
    </body>
</html>