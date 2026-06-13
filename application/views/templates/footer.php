                                            
                                          
                                            <!-- sample modal content -->
                                            <div id="change_pass" class="modal fade open-AddBookDialog" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">

                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="myModalLabel">Change Password</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                    </div>
                                                    
                                                    <div class="modal-body">
                                                    <?= form_open(base_url().'Pages/pass_change'); ?>
                                                        <div class="form-group col-md-12">
                                                            <label >Password</label>
                                                            <input type="text" required value="<?= set_value('pass'); ?>" name="pass" class="form-control"> 
                                                        </div>
                                                        <input type="hidden" name="id"  value="<?= $this->session->id; ?>">
                                                    
                                                        <div class="modal-footer">
                                                            <button type="submit" name="submit" class="btn btn-primary waves-effect waves-light">Update</button>
                                                        </div>
                                                </form>
                                                </div>
                                                <!-- /.modal-content -->
                                            </div>
                                            <!-- /.modal-dialog -->
                                        </div>
                                        <!-- /.modal -->
                                        </div>


                                        <!-- sample modal content -->
                                        <div id="change_eval_pass" class="modal fade open-AddBookDialog" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">

                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="myModalLabel">Change Password</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                    </div>
                                                    
                                                    <div class="modal-body">
                                                    <?= form_open(base_url().'Pages/pass_change'); ?>
                                                        <div class="form-group col-md-12">
                                                            <label >Password</label>
                                                            <input type="text" required value="<?= set_value('pass'); ?>" name="pass" class="form-control"> 
                                                        </div>
                                                        <input type="hidden" name="id"  value="<?= $this->session->id; ?>">
                                                    
                                                        <div class="modal-footer">
                                                            <button type="submit" name="submit" class="btn btn-primary waves-effect waves-light">Update</button>
                                                        </div>
                                                </form>
                                                </div>
                                                <!-- /.modal-content -->
                                            </div>
                                            <!-- /.modal-dialog -->
                                        </div>
                                        <!-- /.modal -->
                                        </div>

                                        

                                        
                    <!-- Footer Start -->
                    <footer class="footer">
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-md-12">
                                MANAGEMENT INFORMATION SYSTEM | DEPED DAVAO ORIENTAL DIVISION
                                </div>
                            </div>
                        </div>
                    </footer>
                    <!-- end Footer -->

            </div>

            <!-- ============================================================== -->
            <!-- End Page content -->
            <!-- ============================================================== -->

        </div>
        <!-- END wrapper -->

         <!-- sample modal content -->
         <div id="change_pass_admin" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">

                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="myModalLabel">Change Password</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                    </div>
                                                    
                                                    <div class="modal-body">
                                                    <?= form_open(base_url().'Pages/pass_change'); ?>
                                                        <div class="form-group col-md-12">
                                                            <label >Password</label>
                                                            <input type="text" required value="<?= set_value('pass'); ?>" name="pass" class="form-control"> 
                                                        </div>
                                                        <input type="hidden" name="id" id="id" value="">
                                                        <input type="hidden" name="sp" id="job" value="">
                                                    
                                                        <div class="modal-footer">
                                                            <button type="submit" name="submit" class="btn btn-primary waves-effect waves-light">Update</button>
                                                        </div>
                                                </form>
                                                </div>
                                                <!-- /.modal-content -->
                                            </div>
                                            <!-- /.modal-dialog -->
                                        </div>
                                        <!-- /.modal -->
                                        </div>


        




<!-- Vendor js -->
<script src="<?= base_url(); ?>assets/js/vendor.min.js"></script>


<!-- Ricksaw js-->
<script src="<?= base_url(); ?>assets/libs/rickshaw/rickshaw.min.js"></script>

<!-- flot chart -->
<script src="<?= base_url(); ?>assets/libs/flot-charts/jquery.flot.js"></script>
<script src="<?= base_url(); ?>assets/libs/flot-charts/jquery.flot.tooltip.min.js"></script>
<script src="<?= base_url(); ?>assets/libs/flot-charts/jquery.flot.resize.js"></script>

<!-- Sparkline charts -->
<script src="<?= base_url(); ?>assets/libs/jquery-sparkline/jquery.sparkline.min.js"></script>

<!-- Dashboard init JS -->
<script src="<?= base_url(); ?>assets/js/pages/dashboard2.init.js"></script>

<!-- App js -->
<script src="<?= base_url(); ?>assets/js/app.min.js"></script>

<script>
    function toggleSidebar(e) {
        if (e) {
            e.preventDefault();
            if (typeof e.stopImmediatePropagation === 'function') {
                e.stopImmediatePropagation();
            }
            if (typeof e.stopPropagation === 'function') {
                e.stopPropagation();
            }
        }

        var body = document.body;
        var wrapper = document.getElementById('wrapper');
        body.classList.toggle('sidebar-enable');
        if (window.innerWidth >= 768) {
            body.classList.toggle('enlarged');
            if (wrapper) {
                wrapper.classList.toggle('enlarged');
            }
        } else {
            body.classList.remove('enlarged');
            if (wrapper) {
                wrapper.classList.remove('enlarged');
            }
        }

        return false;
    }
</script>

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

<!-- Responsive examples -->
<script src="<?= base_url(); ?>assets/libs/datatables/dataTables.responsive.min.js"></script>
<script src="<?= base_url(); ?>assets/libs/datatables/responsive.bootstrap4.min.js"></script>

<script src="<?= base_url(); ?>assets/libs/datatables/dataTables.keyTable.min.js"></script>
<script src="<?= base_url(); ?>assets/libs/datatables/dataTables.select.min.js"></script>



<!-- Datatables init -->
<script src="<?= base_url(); ?>assets/js/pages/datatables.init.js"></script>

<script src="<?= base_url(); ?>assets/libs/sweetalert2/sweetalert2.min.js"></script>

<!-- Sweet alert init js-->
<script src="<?= base_url(); ?>assets/js/pages/sweet-alerts.init.js"></script>

 <!-- Plugin js-->
 <script src="<?= base_url(); ?>assets/libs/parsleyjs/parsley.min.js"></script>
 <script src="<?= base_url(); ?>assets/libs/bootstrap-tagsinput/bootstrap-tagsinput.min.js"></script>
 <script src="<?= base_url(); ?>assets/libs/switchery/switchery.min.js"></script>

<script src="<?= base_url(); ?>assets/libs/select2/select2.min.js"></script>
<script src="<?= base_url(); ?>assets/libs/bootstrap-touchspin/jquery.bootstrap-touchspin.min.js"></script>
<script src="<?= base_url(); ?>assets/libs/jquery-mask-plugin/jquery.mask.min.js"></script>
<script src="<?= base_url(); ?>assets/libs/moment/moment.min.js"></script>
<script src="<?= base_url(); ?>assets/libs/bootstrap-timepicker/bootstrap-timepicker.min.js"></script>
<script src="<?= base_url(); ?>assets/libs/bootstrap-colorpicker/bootstrap-colorpicker.min.js"></script>
<script src="<?= base_url(); ?>assets/libs/bootstrap-datepicker/bootstrap-datepicker.min.js"></script>


<!-- Validation init js-->
<script src="<?= base_url(); ?>assets/js/pages/form-validation.init.js"></script>

<!-- Init js-->
<script src="<?= base_url(); ?>assets/js/pages/form-advanced.init.js"></script>


            <script type="text/javascript">
                $(document).on("click", ".open-AddBookDialog", function () {
                    var myBookId = $(this).data('id');
                    $(".modal-body #id").val( myBookId );

                    var itemid = $(this).data('item');
                    $(".modal-body #item").val( itemid );

                    var jobid = $(this).data('job');
                    $(".modal-body #job").val( jobid );

                    var appid = $(this).data('appid');
                    $(".modal-body #appid").val( appid );
                    

                    });
            </script>

                <script>
                    $(function () {
                        $("#district").hide();
                        $("#evgroup").hide();


                        $("#graph_select").change(function() {
                            var val = $(this).val();
                            if(val === "District" || val === "Evaluator") {
                                $("#district").show();
                            }
                            else{
                                $("#district").hide();
                            }
                            if(val === "Evaluator") {
                                $("#evgroup").show();
                            }
                            else{
                                $("#evgroup").hide();
                            }

                        });

                        });
                </script>
                
      
</body>
</html>
