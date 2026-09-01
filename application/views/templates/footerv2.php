
                <!-- Footer Start -->
                <footer class="footer">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-12">
                            DOORS | DEPED DAVAO DE ORO DIVISION
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
      
                <script>
                    /*
                     * Device fingerprint for the audit trail.
                     *
                     * The device id cookie is the primary identifier; this is the
                     * corroborating one, so a device whose cookie was cleared can
                     * still be recognised. Stable, low-entropy signals only - no
                     * canvas or font probing, nothing that identifies a person
                     * rather than a machine.
                     */
                    (function () {
                        try {
                            if (document.cookie.indexOf('mis_dfp=') !== -1) return;

                            var n = window.navigator || {};
                            var s = window.screen || {};
                            var tz = '';
                            try { tz = Intl.DateTimeFormat().resolvedOptions().timeZone || ''; } catch (e) {}

                            var seed = [
                                n.userAgent || '',
                                n.platform || '',
                                n.language || '',
                                (n.languages || []).join(','),
                                n.hardwareConcurrency || '',
                                n.deviceMemory || '',
                                n.maxTouchPoints || '',
                                s.width + 'x' + s.height + 'x' + (s.colorDepth || ''),
                                new Date().getTimezoneOffset(),
                                tz
                            ].join('|');

                            // FNV-1a, twice over, for a 16-char hex digest. A hash
                            // is enough: the server only ever compares it.
                            function fnv(str, offset) {
                                var h = offset;
                                for (var i = 0; i < str.length; i++) {
                                    h ^= str.charCodeAt(i);
                                    h = (h + ((h << 1) + (h << 4) + (h << 7) + (h << 8) + (h << 24))) >>> 0;
                                }
                                return ('00000000' + h.toString(16)).slice(-8);
                            }

                            var fp = fnv(seed, 2166136261) + fnv(seed.split('').reverse().join(''), 16777619);

                            document.cookie = 'mis_dfp=' + fp
                                + ';path=/;max-age=' + (10 * 365 * 24 * 3600)
                                + ';SameSite=Lax'
                                + (location.protocol === 'https:' ? ';Secure' : '');
                        } catch (e) {
                            /* A missing fingerprint costs one corroborating signal, nothing more. */
                        }
                    })();
                </script>

</body>
</html>
