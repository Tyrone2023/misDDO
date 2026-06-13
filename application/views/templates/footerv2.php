
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
                    });
            </script>
      
</body>
</html>